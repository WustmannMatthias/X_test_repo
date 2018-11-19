<?php
	/**
	§§ Enregistrer un remboursement de différé

	 * Ce programme est appelé lors de la soumission du formulaire d'enregistrement d'une vente (depuis la page accueil ou depuis la page ventes)
	 *  Son role est de créer un nouvel objet vente et de l'ajouter dans la base de données. Les données du cycles et du compte seront bien sûr modifiées.
	 */
	
	session_start();
	
	//Vérification que TOUTES les données nécessaires au cycle aient été entrées
	if (isset($_POST['id']) && isset($_POST['montant']) && isset($_POST['cycle'])) {
		$cycle = $_POST['cycle'];
		$identifiant = $_POST['id'];
		$montant = $_POST['montant'];
		
		//provisoirement : 
		$date = date("Y-m-d", time());
		
		
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
		if (is_nan($montant) || $montant <= 0) {
			$_SESSION['messageErreurRemboursementDiffere'] = "Montant à rembourser invalide.";
			exit;
		}
		$resultDiffere = query($mysqli, "SELECT montant, montant_rembourse FROM differes WHERE primary_key = '$identifiant' AND compte = '$IDcompte'");
		$differeMontants = mysqli_fetch_assoc($resultDiffere);
		$resteARembourser = $differeMontants['montant'] - $differeMontants['montant_rembourse'];
		if ($montant > $resteARembourser) {
			$_SESSION['messageErreurRemboursementDiffere'] = "Montant à rembourser supérieur au montant du par le client.";
			exit;
		}
		
		

		//Etape 1 : instanciation d'un objet Compte correspondant au compte courant
		require_once "../objects/Compte.php";
		require_once "../objects/Cycle.php";
		require_once "../objects/Differe.php";
		
		$resultDonneesCompte = query($mysqli, "SELECT * FROM comptes WHERE ID = '$IDcompte'; ");
		$donnesCompte = mysqli_fetch_assoc($resultDonneesCompte);
		$compteCourant = new Compte($donnesCompte['ID'], $donnesCompte['utilisateur'], $donnesCompte['quantite_possedee'], 
									$donnesCompte['argent_possede'], $donnesCompte['dette'], $donnesCompte['commentaire']);
		echo "<br /><br /><br />";
		
		
		//Etape 2 : instanciation d'un objet Cycle correspondant au cycle sur lequel l'utilisateur travaille
		$resultDonneesCycle = query($mysqli, "SELECT * FROM cycles WHERE compte = '$IDcompte' AND ID = '$cycle'; ");
		$donneesCycle = mysqli_fetch_assoc($resultDonneesCycle);
		$cycleCourant = new Cycle($donneesCycle['ID'], $donneesCycle['compte'], $donneesCycle['date_achat'], $donneesCycle['quantite_achat'], 
								$donneesCycle['tarif'], $donneesCycle['handicap'], $donneesCycle['commentaire']);
		
		
		//Etape 3 : instanciation de l'objet Differe du cycle concerné, puis ajout à l'objet cycle sur lequel le remboursement doit être perçu
		$resultDonneesDiffere = query($mysqli, "SELECT * FROM differes WHERE primary_key = '$identifiant'
												AND compte = '$IDcompte' AND cycle = '$cycle'");
		$donneesDiffere = mysqli_fetch_assoc($resultDonneesDiffere);
		$differeCourant = new Differe($donneesDiffere['primary_key'], $donneesDiffere['compte'], $donneesDiffere['cycle'], $donneesDiffere['date_emprunt'], 
									$donneesDiffere['client'], $donneesDiffere['montant'], $donneesDiffere['montant_rembourse'], $donneesDiffere['date_finalisation']);
		$client = $donneesDiffere['client']; //Pour utiliser le nom du client dans la table 'remboursements_differes'

		
		//Etape 4 : Ajout du differe construit au cycle puis àjout du cycle construit (attributs du cycle + differe à traiter) à l'objet Compte
		$cycleCourant ->setDifferesTab(array($differeCourant));
		$compteCourant ->setCyclesTab(array($cycleCourant));
		unset($cycleCourant);
		unset($differeCourant);
		
		
		//Etape 5 : Réalisation du remboursement du differe
		$compteCourant ->percevoirRemboursement($cycle, $identifiant, $montant, $date);
		
		
		//Etape 6 : Récupération des objets Differe et Compte modifiés (aucune modification n'a été apportée au cycle)
		$differeCourant = $compteCourant ->getCyclesTab()[0] ->getDifferesTab()[0];
		//L'objet Compte a été modifié directement dans la variable $compteCourant, inutile de le recréer
		
		
		//Etape 7 : Actualisation du differe dans la bdd
		$montantRembourse = $differeCourant ->getMontantRembourse();
		$dateFinalisation = $differeCourant ->getDateFinalisation();
		
		$ok = query($mysqli, "UPDATE differes
							  SET 
							  montant_rembourse = '$montantRembourse', 
							  date_finalisation = '$dateFinalisation'
							  WHERE primary_key = '$identifiant';
					");
		if (!$ok) {
			$_SESSION["messageErreurRemboursementDiffere"] = "Erreur lors de l'actualisation du differe dans la base de données. Opération abandonnée.";
			mysqli_close($mysqli);
			exit;
		}
		
		
		//Etape 8 : actualisation du compte dans la bdd
		$portefeuille = $compteCourant ->getArgentPossede();
		$ok = query($mysqli, "UPDATE comptes
							  SET argent_possede = '$portefeuille'
							  WHERE ID = '$IDcompte';
							  ");
		if (!$ok) {
			$_SESSION["messageErreurRemboursementDiffere"] = "Erreur lors de l'actualisation du portefeuille du compte dans la base de données. Attention, le differe a été actualisé. Veuillez contacter un administrateur.";
			mysqli_close($mysqli);
			exit;
		}
		



		//Etape 9 : enregistrement d'un remboursement dans la table 'remboursements_differes' de la bdd
		$ok = query($mysqli, "INSERT INTO remboursements_differes (date, compte, cycle, client, montant_rembourse) 
							  VALUE ('$date', '$IDcompte', '$cycle', '$client', '$montant')
					");
		
		if (!$ok) {
			$_SESSION["messageErreurRemboursementDiffere"] = "Le remboursement a été enregistré. Attention, erreur lors de l'ajout du remboursement dans la table 'remboursements_differes'.";
			mysqli_close($mysqli);
			exit;
		}




		/**
		 * Si tout s'est bien passé
		 */
		$_SESSION["messageConfirmationRemboursementDiffere"] = "Le remboursement a bien été enregistré.";
		mysqli_close($mysqli);
		exit;
		
		
	}
	else { //Si il manque des paramètres
		$_SESSION['messageErreurRemboursementDiffere'] = "Veuillez fournir toutes les informations nécessaires à l'enregistrement du remboursement d'un differe";
	}
	
	
?>