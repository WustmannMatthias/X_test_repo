<?php

	/**
	§§ Modifier les informations du compte
	§§ Retirer de l argent
	§§ Déposer de l argent
	
	 * Ce programme est appelé lors de la soumission des formulaires de modification des données de l'utilisateur 
	 * 	de la page mon_compte.
	 * 
	 */
	
	session_start();
	
	//Définition des chemins d'accès vers les dossiers
	$pathData = "../data/";
	
	//inclusions de données relatives à la base de données et de fonctions php
	include $pathData."fonctions.inc.php";
	include $pathData."parametres_bd.inc.php";
	
	//Connexion au serveur mysqli
	$mysqli = mysqli_connect($host, $user, $pass);
	mysqli_select_db($mysqli, $base);
	
	
	/**
	 * MODIFICATION D'INFORMATIONS
	 */
	if (isset($_GET['modifierInformations'])) {
		//Récupération des élements du formulaire
		$argentPossede = (isset($_POST['portefeuille'])) ? $_POST['portefeuille'] : 0;
		$dette = (isset($_POST['dette'])) ? $_POST['dette'] : 0;
		$quantitePossedee = (isset($_POST['stock'])) ? $_POST['stock'] : 0;
		$commentaire = (isset($_POST['commentaire'])) ? $_POST['commentaire'] : "";
		$dateModif = (isset($_POST['dateModif'])) ? $_POST['dateModif'] : date("Y-m-d", time());
		$motif = (isset($_POST['motif'])) ? $_POST['motif'] : "";
		$ID = $_SESSION['ID'];
		
		if ($dateModif == null) {
			$dateModif = date("Y-m-d", time());
		}
		
		//Tests sur les entrées
		if (is_nan($argentPossede) || $argentPossede < 0) {
			$_SESSION['messageErreurModification'] = "Montant du portefeuille invalide.";
			header("Location: ../index.php?mon_compte");
			exit;
		}
		if (is_nan($dette) || $dette < 0) {
			$_SESSION['messageErreurModification'] = "Montant de la dette invalide.";
			header("Location: ../index.php?mon_compte");
			exit;
		}
		if (is_nan($quantitePossedee) || $quantitePossedee < 0) {
			$_SESSION['messageErreurModification'] = "Montant du stock invalide.";
			header("Location: ../index.php?mon_compte");
			exit;
		}
		if ($motif != mysqli_real_escape_string($mysqli, $motif)) {
			$_SESSION["messageErreurModification"] = "Motif invalide.";
			header("Location: ../index.php?mon_compte");
			exit;
		}
		if ($commentaire != mysqli_real_escape_string($mysqli, $commentaire)) {
			$_SESSION["messageErreurModification"] = "Commentaire invalide.";
			header("Location: ../index.php?mon_compte");
			exit;
		}
		
		//Récupération des anciennes valeurs du compte
		$result = query($mysqli, "SELECT argent_possede, dette, quantite_possedee, commentaire 
								  FROM comptes WHERE ID = '$ID'; ");
		$n_uplet = mysqli_fetch_assoc($result);
		$argentPossedeOLD = $n_uplet['argent_possede'];
		$detteOLD = $n_uplet['dette'];
		$quantitePossedeeOLD = $n_uplet['quantite_possedee'];
		$commentaireOLD = $n_uplet['commentaire'];
		
		echo "$argentPossede, $argentPossedeOLD";
		//Comparaison avec les nouvelles valeurs pour savoir ce qui a été modifié
		// + Enregistrement de l'historique des modifications dans la base de données
		if ($argentPossede != $argentPossedeOLD) {
			$difference = $argentPossede - $argentPossedeOLD;
			query($mysqli, "INSERT INTO modifications_compte (compte, date, operation, attribut_modifie, valeur_modification, motif) 
							VALUE ('$ID', '$dateModif', 'Modification données', 'Portefeuille', '$difference', '$motif');
				 ");
		}
		if ($dette != $detteOLD) {
			$difference = $dette - $detteOLD;
			query($mysqli, "INSERT INTO modifications_compte (compte, date, operation, attribut_modifie, valeur_modification, motif) 
							VALUE ('$ID', '$dateModif', 'Modification données', 'Dette', '$difference', '$motif');
				 ");
		}
		if ($quantitePossedee != $quantitePossedeeOLD) {
			$difference = $quantitePossedee - $quantitePossedeeOLD;
			query($mysqli, "INSERT INTO modifications_compte (compte, date, operation, attribut_modifie, valeur_modification, motif) 
							VALUE ('$ID', '$dateModif', 'Modification données', 'Stock', '$difference', '$motif');
				 ");
		}
		
		if ($argentPossede == $argentPossedeOLD && $dette == $detteOLD && $quantitePossedee == $quantitePossedeeOLD && $commentaire == $commentaireOLD) {
			$_SESSION['messageErreurModification'] = "Aucune donnée à modifier.";
		}
		else {
			$ok = query($mysqli, "UPDATE comptes
									SET 
									argent_possede = '$argentPossede',
									quantite_possedee = '$quantitePossedee',
									dette = '$dette',
									commentaire = '$commentaire'
									WHERE ID = '$ID';
				 ");
			if ($ok) {
				$_SESSION['messageConfirmationModification'] = "Données actualisées avec succès.";
			}
			else {
				$_SESSION['messageErreurModification'] = "Erreur lors de l'ajout des nouvelles données dans la base de données.";
			}
			
		}
			
	}
	
	/**
	 * RETRAIT ARGENT
	 */
	elseif (isset($_GET['retraitArgent'])) {
		//Récupération des élements du formulaire
		$montant = (isset($_POST['montant'])) ? $_POST['montant'] : 0;
		$dateRetrait = (isset($_POST['dateRetrait'])) ? $_POST['dateRetrait'] : date("Y-m-d", time());
		$motif = (isset($_POST['motif'])) ? $_POST['motif'] : "";
		$ID = $_SESSION['ID'];
		
		if ($dateRetrait == null) {
			$dateRetrait = date("Y-m-d", time());
		}
		
		//Tests sur les entrées
		if ($motif != mysqli_real_escape_string($mysqli, $motif)) {
			$_SESSION["messageErreurModification"] = "Motif invalide.";
			header("Location: ../index.php?mon_compte");
			exit;
		}
		if (is_nan($montant) || $montant < 0) {
			$_SESSION["messageErreurModification"] = "Montant invalide.";
			header("Location: ../index.php?mon_compte");
			exit;
		}
		
		
		//récupération du portefeuille pour vérifier que le retrait est possible
		$result = query($mysqli, "SELECT argent_possede FROM comptes WHERE ID = '$ID'; ");
		$n_uplet = mysqli_fetch_assoc($result);
		$argentPossede = $n_uplet['argent_possede'];
		
		if ($argentPossede < $montant) {
			$_SESSION['messageErreurModification'] = "Fonds insuffisants pour effectuer un retrait de $montant€.";
		}
		elseif ($montant == 0) {
			$_SESSION['messageErreurModification'] = "Le montant saisi est nul.";
		}
		else {
			$solde = $argentPossede - $montant;
			$ok = query($mysqli, "INSERT INTO modifications_compte (compte, date, operation, attribut_modifie, valeur_modification, motif) 
							VALUE ('$ID', '$dateRetrait', 'Retrait', 'Portefeuille', '-$montant', '$motif'); 
						");	
			$ok2 = query($mysqli, "UPDATE comptes
							SET 
							argent_possede = '$solde'
							WHERE ID = '$ID';
				 ");
				 
			if ($ok && $ok2) {
				$_SESSION['messageConfirmationModification'] = "Retrait enregistré avec succès.";
			}
			else {
				$_SESSION['messageErreurModification'] = "Erreur lors de l'enregistrement du retrait dans la base de données.";
			}
		}
	}
	
	
	
	/**
	 * DEPOT ARGENT
	 */
	elseif (isset($_GET['depotArgent'])) {
		//Récupération des élements du formulaire
		$montant = (isset($_POST['montant'])) ? $_POST['montant'] : 0;
		$dateDepot = (isset($_POST['dateDepot'])) ? $_POST['dateDepot'] : date("Y-m-d", time());
		$motif = (isset($_POST['motif'])) ? $_POST['motif'] : "";
		$ID = $_SESSION['ID'];
		
		if ($dateDepot == null) {
			$dateDepot = date("Y-m-d", time());
		}
		
		//Tests sur les entrées
		if ($motif != mysqli_real_escape_string($mysqli, $motif)) {
			$_SESSION["messageErreurModification"] = "Motif invalide.";
			header("Location: ../index.php?mon_compte");
			exit;
		}
		if (is_nan($montant) || $montant < 0) {
			$_SESSION["messageErreurModification"] = "Montant invalide.";
			header("Location: ../index.php?mon_compte");
			exit;
		}
		
		//récupération du portefeuille pour calculer le nouveau solde
		$result = query($mysqli, "SELECT argent_possede FROM comptes WHERE ID = '$ID'; ");
		$n_uplet = mysqli_fetch_assoc($result);
		$argentPossede = $n_uplet['argent_possede'];
		
		if ($montant == 0) {
			$_SESSION['messageErreurModification'] = "Le montant saisi est nul.";
		}
		else {
			$solde = $argentPossede + $montant;
			$ok = query($mysqli, "INSERT INTO modifications_compte (compte, date, operation, attribut_modifie, valeur_modification, motif) 
							VALUE ('$ID', '$dateDepot', 'Dépot', 'Portefeuille', '$montant', '$motif'); 
						");	
			$ok2 = query($mysqli, "UPDATE comptes
							SET 
							argent_possede = '$solde'
							WHERE ID = '$ID';
				 ");
				 
			if ($ok && $ok2) {
				$_SESSION['messageConfirmationModification'] = "Dépot enregistré avec succès.";
			}
			else {
				$_SESSION['messageErreurModification'] = "Erreur lors de l'enregistrement du dépot dans la base de données.";
			}
		}
	}
	
	
	
	
	/**
	 * RETRAIT MARCHANDISE
	 */
	elseif (isset($_GET['retraitMarchandise'])) {
		//Récupération des élements du formulaire
		$montant = (isset($_POST['montant'])) ? $_POST['montant'] : 0;
		$dateRetrait = (isset($_POST['dateRetrait'])) ? $_POST['dateRetrait'] : date("Y-m-d", time());
		$motif = (isset($_POST['motif'])) ? $_POST['motif'] : "";
		$ID = $_SESSION['ID'];
		
		if ($dateRetrait == null) {
			$dateRetrait = date("Y-m-d", time());
		}
		
		//récupération du portefeuille pour vérifier que le retrait est possible
		$result = query($mysqli, "SELECT quantite_possedee FROM comptes WHERE ID = '$ID'; ");
		$n_uplet = mysqli_fetch_assoc($result);
		$quantitePossede = $n_uplet['quantite_possedee'];
		
		if ($quantitePossede < $montant) {
			$_SESSION['messageErreurModification'] = "Stock insuffisant pour effectuer un retrait de $montant g.";
		}
		elseif ($montant == 0) {
			$_SESSION['messageErreurModification'] = "Le montant saisi est nul.";
		}
		else {
			$solde = $quantitePossede - $montant;
			$ok = query($mysqli, "INSERT INTO modifications_compte (compte, date, operation, attribut_modifie, valeur_modification, motif) 
							VALUE ('$ID', '$dateRetrait', 'Retrait', 'Stock', '-$montant', '$motif'); 
						");	
			$ok2 = query($mysqli, "UPDATE comptes
							SET 
							quantite_possedee = '$solde'
							WHERE ID = '$ID';
				 ");
				 
			if ($ok && $ok2) {
				$_SESSION['messageConfirmationModification'] = "Retrait de marchandise enregistré avec succès.";
			}
			else {
				$_SESSION['messageErreurModification'] = "Erreur lors de l'enregistrement du retrait de marchandise dans la base de données.";
			}
		}
	}
		
		
		
		
		
	
	mysqli_close($mysqli);
	header("Location: ../index.php?mon_compte");
	exit;
	
?>