<?php
	/**
	§§ Créer un nouveau compte depuis la page de connexion
	
	 * Ce programme est appelé depuis la page ecran_connexion, et reçoit en paramètre un nom d'utilisateur et
	 * 	deux mots de passe. Après une série de tests sur la validité des données, ce programme
	 * 	crée une ligne dans la relation 'comptes' correspondant à un nouvel utilisateur.
	 */
	
	
	//Récupération de la session courante
	session_start();
	
	//Définition des chemins d'accès vers les dossiers include nécessaires à ce programme
	$pathInclude = "../data/";
	
	//inclusions de données relatives à la base de données et de fonctions php
	include $pathInclude."fonctions.inc.php";
	include $pathInclude."parametres_bd.inc.php";
	
	//Connexion au serveur mysqli
	$mysqli = mysqli_connect($host, $user, $pass);
	mysqli_select_db($mysqli, $base);
	
	
	/*
	 * VERIFICATION DES DONNEES DU FORMULAIRE
	 */
	if (isset($_POST['submit'])) {
		
		//Vérification que le login et les mots de passe aient été entrés
		if (isset($_POST['login']) && isset($_POST['mdp1']) && isset($_POST['mdp2'])) {
			 
			/*
			 * Donnée par donnée : cas ou les données contiendraient des caractères illégaux + Définition de
			 * de variables pour plus simplicité 
			 */
			if (mysqli_real_escape_string($mysqli, $_POST['login'])!= $_POST['login']) {
				$_SESSION['erreurInscription'] = "Caractères illégaux dans le login.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
			$login = $_POST['login'];
			
			if (mysqli_real_escape_string($mysqli, $_POST['mdp1'])!= $_POST['mdp1']) {
				$_SESSION['erreurInscription'] = "Caractères illégaux dans le mot de passe.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
			$motdepasse1 = $_POST['mdp1'];
			
			if (mysqli_real_escape_string($mysqli, $_POST['mdp2'])!= $_POST['mdp2']) {
				$_SESSION['erreurInscription'] = "Caractères illégaux dans le mot de passe.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
			$motdepasse2 = $_POST['mdp2'];
			
			
			//Cas ou le login est insuffisant
			if (strlen(trim($login)) < 5) {
				$_SESSION['erreurInscription'] = "Le login doit faire au moins 5 caractères.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
			
			//Cas où les mots de passe entrés sont différents
			if ($motdepasse1 != $motdepasse2) {
				$_SESSION['erreurInscription'] = "Les deux mots de passe ne correspondent pas.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
			else $motdepasse = $motdepasse1;
			
			if (strlen(trim($motdepasse)) < 5) {
				$_SESSION['erreurInscription'] = "Le mot de passe doit faire au moins 5 caractères.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
			else $motdepasse = md5($motdepasse);
			
			
			//Cas ou le login figure déjà dans la base de données
			$result = query($mysqli, "SELECT utilisateur
									FROM comptes 
									WHERE utilisateur = '$login'");
			if (mysqli_num_rows($result) != 0) {
				$_SESSION['erreurInscription'] = "Le nom d'utilisateur que vous avez choisi n'est pas disponible.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
			
			
			
			
			/**
			 * INSCRIPTION AUTORISEE
			 */
			$requeteAjout = "INSERT INTO comptes(utilisateur, mot_de_passe, quantite_possedee, argent_possede, dette) 
							 VALUE ('$login', '$motdepasse', 0, 0, 0); ";
			$ok = query($mysqli, $requeteAjout);
			
			
			if ($ok) {
				unset($_SESSION['erreurInscription']);
				$_SESSION['confirmationInscription'] = "Inscription effectuée.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
			
			
			
			
			
			else {
				$_SESSION['erreurInscription'] = "Echec lors de l'ajout dans la base de données.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
		}
		else { //if (!((isset($_POST['login']) && isset($_POST['mdp1']) && isset($_POST['mdp2']))))
			$_SESSION['erreurInscription'] = "Veuillez entrer un nom d'utilisateur et deux mots de passe.";
			mysqli_close($mysqli);
			header("Location: ../index.php");
			exit;
		}
	}
	

	
?>