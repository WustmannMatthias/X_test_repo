<?php
	/**
	§§ Enregistrer un remboursement de handicap
	 * Ce programme est appelé lors de la validation du formulaire enregistrement_remboursement_handicap.
	 * 	Son rôle est d'appliquer les modifications nécessaires au compte et au cycle, puis de renvoyer ces objets dans la base de données.
	 * 	Une ligne dans la table remboursements_handicaps sera également ajoutée.
	 */
	
	
	session_start();
	
	//Vérification que TOUTES les données nécessaires au cycle aient été entrées
	if (isset($_POST['dateRemboursement']) && isset($_POST['montant']) && isset($_POST['cycle'])) {
		$date = $_POST['dateRemboursement'];
		$montant = $_POST['montant'];
		$cycle = $_POST['cycle'];
		
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
		
		
		//Etape 0 : Test sur les entrées
		if (is_nan($montant) || $montant <= 0) {
			$_SESSION['messageErreurRemboursementHandicap'] = "Montant invalide.";
			exit;
		}
		
		$resultPortefeuilleCompte = query($mysqli, "SELECT argent_possede FROM comptes WHERE ID = '$IDcompte'");
		$portefeuilleCompte = mysqli_fetch_assoc($resultPortefeuilleCompte)['argent_possede'];
		if ($montant > $portefeuilleCompte) {
			$_SESSION["messageErreurRemboursementHandicap"] = "Pas assez d'argent sur le compte.";
			exit;
		}
		
		$resultHandicapCycle = query($mysqli, "SELECT handicap FROM cycles WHERE compte = '$IDcompte' AND ID = '$cycle'");
		$handicapCycle = mysqli_fetch_assoc($resultHandicapCycle)['handicap'];
		$resultDejaRembourse = query($mysqli, "SELECT sum(quantite_remboursee) montant FROM remboursements_handicap WHERE compte = '$IDcompte' AND cycle = '$cycle' ");
		$dejaRembourse = mysqli_fetch_assoc($resultDejaRembourse)['montant'];
		if ($montant > ($handicapCycle - $dejaRembourse)) {
			$_SESSION["messageErreurRemboursementHandicap"] = "Le montant entré est supérieur au montant du.";
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
		
		
		//Etape 3 : Réalisation du remboursement
		$compteCourant ->rembourserHandicap($cycle, $montant);
		
		//Etape 4 : Update du compte dans la base de données
		$argentPossede = $compteCourant ->getArgentPossede();
		$dette = $compteCourant ->getDette();
		$ok = query($mysqli, "UPDATE comptes 
							  SET 
							  argent_possede = '$argentPossede',
							  dette = '$dette' 
							  WHERE ID = '$IDcompte';
					"); 			
		if (!$ok) {
			$_SESSION["messageErreurRemboursementHandicap"] = "Erreur lors de l'actualisation du compte dans la base de données. Enregistrement du remboursement abandonné. ";
			mysqli_close($mysqli);
			exit;
		}
		else {
			//Etape 5 : Enregistrement du remboursement dans la base de données
			$ok = query($mysqli, "INSERT INTO remboursements_handicap (date, compte, cycle, quantite_remboursee)
									VALUE ('$date', '$IDcompte', '$cycle', '$montant');
						");
			if ($ok) {
				$_SESSION['messageConfirmationRemboursementHandicap'] = "Remboursement enregistré.";
			}
			else {
				$_SESSION['messageErreurRemboursementHandicap'] = "Echec de l'enregistrement du remboursement. Attention, les données du compte ont été modifiées. Ajustez les données du compte ou contactez un administrateur.";
			}
			mysqli_close($mysqli);
			exit;
		}
		
	}
	else { //Si il manque des informations
		$_SESSION['messageErreurRemboursementHandicap'] = "Veuillez fournir tous les éléments nécessaires à l'enregistrement d'un remboursement de handicap.";
		exit;
	}
	
?>