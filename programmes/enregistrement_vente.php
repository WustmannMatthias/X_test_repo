<?php
	/**
	§§ Enregistrer une vente
	 * Ce programme est appelé lors de la soumission du formulaire d'enregistrement d'une vente (depuis la page accueil ou depuis la page ventes)
	 *  Son role est de créer un nouvel objet vente et de l'ajouter dans la base de données. Les données du cycles et du compte seront bien sûr modifiées.
	 */
	
	session_start();
	
	//Vérification que TOUTES les données nécessaires au cycle aient été entrées
	if (isset($_POST['dateVente']) && isset($_POST['quantiteVendue']) && isset($_POST['tarif']) && isset($_POST['methodePayement'])
		 && isset($_POST['montantPaye']) && isset($_POST['cycle']) && isset($_POST['client'])) {
		$date = $_POST['dateVente'];
		$quantite = $_POST['quantiteVendue'];
		$tarif = $_POST['tarif'];
		$methodePayement = $_POST['methodePayement'];
		$montantPaye = $_POST['montantPaye'];
		$cycle = $_POST['cycle'];
		$client = $_POST['client'];
		
		if ($methodePayement == "Accompte") {
			if (!$montantPaye) {
				echo "Montant de l'accompte non spécifié";
				$_SESSION["messageErreurEnregistrementVente"] = "Montant de l'accompte versé non spécifié.";
				exit;
			}
		}
		if ($date == null || $date == "") {
			$date = date("Y-m-d", time());
		}
	
		/**
		 * SI TOUTES LES INFOS SONT LA : 
		 */
	
		//Définition des chemins d'accès vers les dossiers
		$pathData = "../data/";
		
		//inclusions de données relatives à la base de données et de fonctions php
		include $pathData."fonctions.inc.php";
		include $pathData."parametres_bd.inc.php";
		
		//Connexion au serveur mysqli
		$mysqli = mysqli_connect($host, $user, $pass);
		mysqli_select_db($mysqli, $base);
		
		$IDcompte = $_SESSION['ID'];
		
		
		
		//Etape 0 : Tests sur les entrées
		if (is_nan($quantite) || $quantite <= 0) {
			$_SESSION['messageErreurEnregistrementVente'] = "Quantité invalide.";
			exit;
		}
		$resultStockAchatCycle = query($mysqli, "SELECT quantite_achat FROM cycles WHERE compte = '$IDcompte' AND ID = '$cycle'");
		$stockAchatCycle = mysqli_fetch_assoc($resultStockAchatCycle)['quantite_achat'];
		$resultQteVendue = query($mysqli, "SELECT sum(quantite) qte FROM ventes WHERE compte = '$IDcompte' AND cycle = '$cycle'");
		$qteVendue = mysqli_fetch_assoc($resultQteVendue)['qte'];
		$resultQteRetiree = query($mysqli, "SELECT sum(quantite_retiree) qte FROM retraits_marchandise WHERE compte = '$IDcompte' AND cycle = '$cycle'");
		$qteRetiree = mysqli_fetch_assoc($resultQteRetiree)['qte'];
		if ($quantite > ($stockAchatCycle - ($qteVendue + $qteRetiree))) {
			$_SESSION["messageErreurEnregistrementVente"] = "Pas assez de marchandise pour effectuer une telle vente.";
			exit;
		}
		if (is_nan($tarif) || $tarif <= 0) {
			$_SESSION["messageErreurEnregistrementVente"] = "Tarif invalide.";
			exit;
		}
		if (isset($montantPaye) && is_nan($montantPaye)) {
			$_SESSION["messageErreurEnregistrementVente"] = "Montant payé invalide.";
			exit;
		}
		if (isset($montantPaye) && $montantPaye > ($tarif * $quantite)) {
			$_SESSION["messageErreurEnregistrementVente"] = "Montant payé supérieur au prix de la vente.";
			exit;
		}
		
		
		
		//Etape 1 : instanciation d'un objet Compte correspondant au compte courant
		require_once "../objects/Compte.php";
		require_once "../objects/Cycle.php";
		
		$resultDonneesCompte = query($mysqli, "SELECT * FROM comptes WHERE ID = '$IDcompte'; ");
		$donnesCompte = mysqli_fetch_assoc($resultDonneesCompte);
		$compteCourant = new Compte($donnesCompte['ID'], $donnesCompte['utilisateur'], $donnesCompte['quantite_possedee'], 
									$donnesCompte['argent_possede'], $donnesCompte['dette'], $donnesCompte['commentaire']);
		echo $compteCourant ->toString();
		echo "<br /><br /><br />";
		
		
		//Etape 2 : instanciation d'un objet Cycle correspondant au cycle sur lequel l'utilisateur travaille, puis ajout de ce cycle aux attributs du compte
		$resultDonneesCycle = query($mysqli, "SELECT * FROM cycles WHERE compte = '$IDcompte' AND ID = '$cycle'; ");
		$donneesCycle = mysqli_fetch_assoc($resultDonneesCycle);
		$cycleCourant = new Cycle($donneesCycle['ID'], $donneesCycle['compte'], $donneesCycle['date_achat'], $donneesCycle['quantite_achat'], 
								$donneesCycle['tarif'], $donneesCycle['handicap'], $donneesCycle['commentaire']);
		$compteCourant ->setCyclesTab(array($cycleCourant));
		unset($cycleCourant);
		
		
		//Etape 3 : Réalisation de la vente. Les attributs de l'objet compteCourant sont actualisés
		$donneesVente = array($cycle, $quantite, $tarif, $client, $date, $methodePayement);
		if ($methodePayement == "Accompte") {
			array_push($donneesVente, $montantPaye);
		}
		$compteCourant ->vendre($donneesVente);
		
		
		//Etape 4 : Récupération des objets Cycles, Vente, et (éventuellement Differe) qui ont été créés.
		$cycleCourant = $compteCourant ->getCyclesTab()[0];
		$vente = $cycleCourant ->getVentesTab()[0];
		if ($cycleCourant ->compterDifferes() != 0) {
			$differe = $cycleCourant ->getDifferesTab()[0];
		}
		else {
			$differe = false;
		}
		
		/*
		 * Etape 5 : Actualisation de toutes ces données dans la BDD
		 */
		//Differe (à insérer dans la bdd)
		if ($differe) {
			$cycleDiffere = $differe ->getCycle();
			$dateEmprunt = $differe ->getDateEmprunt();
			$client = $differe ->getClient();
			$montant = $differe ->getMontant();
			$montantRembourse = $differe ->getMontantRembourse();
			$ok = query($mysqli, "INSERT INTO differes (compte, cycle, date_emprunt, client, montant, montant_rembourse, date_finalisation)
									VALUE ('$IDcompte', '$cycleDiffere', '$dateEmprunt', '$client', '$montant', '$montantRembourse', '0'); ");
			if (!$ok) {
				$_SESSION["messageErreurEnregistrementVente"] = "Erreur lors de l'ajout de l'objet Differe dans la base de données. La vente est annulée.";
				mysqli_close($mysqli);
				exit;
			}
		}
		
		//Vente (à insérer dans la bdd)
		$cycleVente = $vente ->getCycle();
		$client = $vente ->getClient();
		$quantite = $vente ->getquantite();
		$tarif = $vente ->getTarif();
		$payement = $vente ->getPayement();
		$date = $vente ->getDate();
		if ($methodePayement == "Differe") {$montantPaye = 0;}
		elseif ($methodePayement == "Cash") {$montantPaye = $vente ->calculerPrix();}
		
		$ok = query($mysqli, "INSERT INTO ventes (compte, cycle, client, quantite, tarif, date, methode, montant_paye)
							VALUE ('$IDcompte', '$cycleVente', '$client', '$quantite', '$tarif', '$date', '$payement', '$montantPaye'); ");
		if (!$ok) {
			$_SESSION["messageErreurEnregistrementVente"] = "Erreur lors de l'ajout de l'objet Differe dans la base de données. La vente est annulée. 
						Attention, un differe a peut être été ajouté à la base de données. Veuillez contacter un administrateur.";
			mysqli_close($mysqli);
			exit;
		}
		
		
		//Cycle : rien a updater
		
		//Compte (à updater dans la bdd)
		$quantitePossedee = $compteCourant ->getQuantitePossedee();
		$argentPossede = $compteCourant ->getArgentPossede();
		$dette = $compteCourant ->getDette();
		$ok = query($mysqli, "UPDATE comptes 
							  SET quantite_possedee = '$quantitePossedee', argent_possede = '$argentPossede', dette = '$dette' 
							  WHERE ID = '$IDcompte';
					"); 			
		if (!$ok) {
			$_SESSION["messageErreurEnregistrementVente"] = "Erreur lors de l'actualisation du compte dans la base de données. Attention, le cycle a été créé. Veuillez contacter un administrateur.";
			mysqli_close($mysqli);
			exit;
		}
		
		
		
		/**
		 * Si tout s'est bien passé
		 */
		$_SESSION["messageConfirmationEnregistrementVente"] = "La vente a bien été enregistrée.";
		mysqli_close($mysqli);
		exit;
		
	
	}
	else {
		$_SESSION['messageErreurEnregistrementVente'] = "Veuillez fournir tous les éléments nécessaires à l'enregistrement d'une vente.";
		exit;
	}
	
?>