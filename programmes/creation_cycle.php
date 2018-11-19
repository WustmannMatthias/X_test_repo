<?php
	/**
	§§ Démarrer un nouveau cycle

	 * Ce programme est appelé lors de la soumission du formulaire de création d'un nouveau cycle (depuis la page accueil ou depuis la page cycles)
	 *  Son role est de créer un nouveau cycle et de l'ajouter dans la base de données.
	 */
	
	session_start();
	
	
	
	//Vérification que TOUTES les données nécessaires au cycle aient été entrées
	if (isset($_POST['date']) && isset($_POST['quantite']) && isset($_POST['tarif']) && isset($_POST['payement'])) {
		$date = $_POST['date'];
		$quantite = $_POST['quantite'];
		$tarif = $_POST['tarif'];
		$methodePayement = $_POST['payement'];
		$commentaire = isset($_POST['commentaire']) ? $_POST['commentaire'] : "";
		
		if ($methodePayement == "Accompte") {
			if (isset($_POST['montantPaye'])) {
				$montantPaye = $_POST['montantPaye'];
			}
			else {// Si il manque l'info "montantPaye" pour un payement par accompte
				$_SESSION["messageErreurCreationCycle"] = "Montant de l'accompte versé non spécifié.";
				header("Location: ../index.php");
				exit;
			}
		}
		if ($date == null) {
			$date = date("Y-m-d", time());
		}
		

		
		/**
		 * SI TOUTES LES INFOS SONT LA : 
		 */
	
		//Définition des chemins d'accès vers les dossiers
		$pathData = "../data/";
		$functionsFile = "fonctions.inc.php";
		
		//inclusions de données relatives à la base de données et de fonctions php
		include $pathData.$functionsFile;
		include $pathData."parametres_bd.inc.php";
		
		//Connexion au serveur mysqli
		$mysqli = mysqli_connect($host, $user, $pass);
		mysqli_select_db($mysqli, $base);
		
		$IDcompte = $_SESSION['ID'];
		
		
		//Etape 0 : Test sur les entrées
		
		if (is_nan($quantite) || $quantite <= 0) {
			$_SESSION["messageErreurCreationCycle"] = "Quantité invalide.";
			header("Location: ../index.php");
			exit;
		}
		if (is_nan($tarif) || $tarif <= 0) {
			$_SESSION["messageErreurCreationCycle"] = "Tarif invalide.";
			header("Location: ../index.php");
			exit;
		}
		if ($commentaire != mysqli_real_escape_string($mysqli, $commentaire)) {
			$_SESSION["messageErreurCreationCycle"] = "Commentaire invalide.";
			header("Location: ../index.php");
			exit;
		}
		
		$resultPortefeuilleCompte = query($mysqli, "SELECT argent_possede FROM comptes WHERE ID = '$IDcompte'");
		$portefeuilleCompte = mysqli_fetch_assoc($resultPortefeuilleCompte)['argent_possede'];
		if ($methodePayement == "Cash" && ($tarif * $quantite) > $portefeuilleCompte) {
			$_SESSION["messageErreurCreationCycle"] = "Pas assez d'argent sur le compte.";
			header("Location: ../index.php");
			exit;
		}
		if (isset($montantPaye) && is_nan($montantPaye)) {
			$_SESSION["messageErreurCreationCycle"] = "Montant payé invalide.";
			header("Location: ../index.php");
			exit;
		}
		if (isset($montantPaye) && $montantPaye > $portefeuilleCompte) {
			$_SESSION["messageErreurCreationCycle"] = "Pas assez d'argent sur le compte.";
			header("Location: ../index.php");
			exit;
		}
		if (isset($montantPaye) && $montantPaye > ($tarif * $quantite)) {
			$_SESSION["messageErreurCreationCycle"] = "Montant payé supérieur au prix du cycle.";
			header("Location: ../index.php");
			exit;
		}
		
		//Etape 1 : Récupérer le plus grand ID du cycle du compte actif
		
		$ID;
		$resultID = query($mysqli, "SELECT max(ID) ID 
									FROM cycles 
									WHERE compte = '$IDcompte'; ");
		if (mysqli_num_rows($resultID) == 0) {
			$ID = 1;
		}
		else {
			$ID = mysqli_fetch_assoc($resultID)['ID'] + 1;
		}
		echo $ID;
		echo "<br />";
		
		//Etape 2 : instanciation d'un objet Compte correspondant au compte courant
		require_once "../objects/Compte.php";
		require_once "../objects/Cycle.php";
		$resultDonneesCompte = query($mysqli, "SELECT * FROM comptes WHERE ID = '$IDcompte'; ");
		$donnesCompte = mysqli_fetch_assoc($resultDonneesCompte);
		$compteCourant = new Compte($donnesCompte['ID'], $donnesCompte['utilisateur'], $donnesCompte['quantite_possedee'], 
									$donnesCompte['argent_possede'], $donnesCompte['dette'], $donnesCompte['commentaire']);
		
		
		//Etape 3 : appeler la fonction acheter() du Compte avec les données envoyées par l'utilisateur
		$donnesAchat = array($date, $quantite, $tarif, $methodePayement, $commentaire);
		if (isset($montantPaye)) {
			array_push($donnesAchat, $montantPaye);
		}
		
		$cycleCree = $compteCourant ->acheter($donnesAchat); //Actualisation des données du Compte + Création du nouveau cycle
		//Exceptionnellement, les données l'ID du cycle est modifié ici pour éviter de devoir récupérer tous les 
		// cycles du compte depuis la base données pour laisser le compte calculer l'ID tout seul
		$cycleCree ->setIdentifiant($ID);
		echo $cycleCree ->getIdentifiant();
		
		
		//Etape 4 : Création d'un nouveau cycle dans la bdd
		$cycleToString = implode("', '", $cycleCree ->toArray());
		$ok = query($mysqli, "INSERT INTO cycles (ID, compte, date_achat, quantite_achat, tarif, handicap, commentaire, actif)
							  VALUE ('$cycleToString', 'true'); 
					"); 
		if (!$ok) {
			$_SESSION["messageErreurCreationCycle"] = "Erreur lors de l'ajout du cycle dans la base de données";
			mysqli_close($mysqli);
			header("Location: ../index.php");
			exit;
		}
		
		
		//Etape 5 : Actualisation du des données du Compte dans la base de données
		$quantitePossedee = $compteCourant ->getQuantitePossedee();
		$argentPossede = $compteCourant ->getArgentPossede();
		$dette = $compteCourant ->getDette();
		$ok = query($mysqli, "UPDATE comptes 
							  SET quantite_possedee = '$quantitePossedee', argent_possede = '$argentPossede', dette = '$dette' 
							  WHERE ID = '$IDcompte';
					"); 			
		if (!$ok) {
			$_SESSION["messageErreurCreationCycle"] = "Erreur lors de l'actualisation du compte dans la base de données. Attention, le cycle a été créé. Veuillez contacter un administrateur.";
			mysqli_close($mysqli);
			header("Location: ../index.php");
			exit;
		}
		
		echo "<br /><br />";
		echo $compteCourant ->toString();
		echo "<br /><br />";
		echo $cycleCree ->toString();
		
		
		$_SESSION["messageConfirmationCreationCycle"] = "Cycle créé avec succès. Les données du compte ont été actualisées.";
		mysqli_close($mysqli);
		header("Location: ../index.php");
		exit;
		
		
		
		
		
	}
	else { //Si il manque des infos pour créer le cycle
		$_SESSION["messageErreurCreationCycle"] = "Création d'un nouveau cycle impossible : des informations sont manquantes.";
		header("Location: ../index.php");
		exit;
	}
	
	
?>