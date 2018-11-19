<?php
	/**
	§§ Enregistrer un nouveau client
	 * Ce programme est appelé lors de la validation du formulaire du modal ajout_client.
	 * Son role est de récupérer les informations du formulaire et d'ajouter un nouveau client à la base de données.
	 */
	
	session_start();
	$IDcompte = $_SESSION['ID'];
	
	if (isset($_POST['nom']) && isset($_POST['date'])) {
		$nom = $_POST['nom'];
		$date = $_POST['date'];
		
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
		
		//Connexion au serveur MySQLi
		$mysqli = mysqli_connect($host, $user, $pass);
		mysqli_select_db($mysqli, $base);
		
		
		//Etape 0 : Test sur les entrées
		if (strlen($nom) < 3) {
			$_SESSION["messageErreurEnregistrementClient"] = "Veuillez fournir un nom d'au moins 3 caractères.";
			header("Location: ../index.php?clients");
			exit;
		}
		if ($nom != mysqli_real_escape_string($mysqli, $nom)) {
			$_SESSION["messageErreurEnregistrementClient"] = "Nom invalide.";
			header("Location: ../index.php?clients");
			exit;
		}
		
		
		//Etape 1 : Récupération du plus grand ID de client pour ce compte
		$ID;
		$resultID = query($mysqli, "SELECT max(ID) ID 
									FROM clients
									WHERE compte = '$IDcompte'; ");
		if (mysqli_num_rows($resultID) == 0) {
			$ID = 0;
		}
		else {
			$ID = mysqli_fetch_assoc($resultID)['ID'] + 1;
		}
		echo $ID;
		echo "<br />";
		
		//Etape 2 : Ajout du client dans la base de données
		$ok = query($mysqli, "INSERT INTO clients (ID, compte, nom, date_enregistrement)
								VALUE ('$ID', '$IDcompte', '$nom', '$date'); 
					");
		if ($ok) {
			$_SESSION["messageConfirmationEnregistrementClient"] = "Client enregistré avec succès.";
		}
		else {
			$_SESSION["messageErreurEnregistrementClient"] = "Erreur lors de l'enregistrement du client dans la base de données.";
		}
		mysqli_close($mysqli);
		header("Location: ../index.php?clients");
		exit;
		
	}
	else { //Cas ou le nom et la date ne sont pas envoyés
		$_SESSION["messageErreurEnregistrementClient"] = "Veuillez fournir un nom et une date d'enregistrement.";
		header("Location: ../index.php?clients");
		exit;
	}
	
?>