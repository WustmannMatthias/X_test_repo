<?php
	/**
	§§ Se connecter au programme
	
	 * Ce programme est appelé depuis la page ecran_connexion de l'application. Il reçoit en paramètre le nom 
	 *  d'utilisateur et le mot de passe entrés par l'utilisateur dans le formulaire de connexion.
	 * 
	 * Le rôle de ce programme est : 
	 * 		- De vérifier la légitimité de la tentative de connexion (informations correctes), et de créer une
	 * 			variable de session qui définira l'utilisateur comme étant connecté.
	 * 		- 
	 * 
	 */
	
	
	//Récupération de la session courante
	session_start();
	
	//Définition des chemins d'accès vers les dossiers
	$pathInclude = "../data/";
	
	//inclusions de données relatives à la base de données et de fonctions php
	include $pathInclude."fonctions.inc.php";
	include $pathInclude."parametres_bd.inc.php";
	
	
	
	//Connexion au serveur mysqli
	$mysqli = mysqli_connect($host, $user, $pass);
	mysqli_select_db($mysqli, $base);
	
	//Vérification des données du formulaire
	if (isset($_POST['submit'])) {
		if (isset($_POST['login']) && isset($_POST['mdp'])) {
			$login = mysqli_real_escape_string($mysqli, $_POST['login']); 
			$motdepasse = md5(mysqli_real_escape_string($mysqli, $_POST['mdp']));
			
			//Vérifiaction que les données entrées existent dans la base
			$requete = "SELECT ID
						FROM comptes
						WHERE utilisateur = '$login' AND mot_de_passe = '$motdepasse'; ";
			
			$resultatRequete = query($mysqli, $requete);
			if (mysqli_num_rows($resultatRequete) != null) {
				$ok = TRUE;
				$n_uplet = mysqli_fetch_assoc($resultatRequete);
				$ID = $n_uplet['ID'];
			}
			else {
				$ok = FALSE;
			}
			
			
			/*
			 * CONNEXION AUTORISEE
			 */
			if ($ok) {
				$_SESSION['connecte'] = "connecte";
				$_SESSION['utilisateur'] = $login; //A garder ?
				$_SESSION['ID'] = $ID; //A garder ?
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
			else { // Si les identifiants sont incorrects ou qu'une erreur est survenue lors de l'accès à la base de données
				$_SESSION['erreurConnexion'] = "Echec de la connexion.";
				mysqli_close($mysqli);
				header("Location: ../index.php");
				exit;
			}
		}
		else { // if (!isset($_POST['login']) && isset($_POST['mdp']))
			$_SESSION['erreurConnexion'] = "Veuillez entrer un nom d'utilisateur et un mot de passe.";
			mysqli_close($mysqli);
			header("Location: ../index.php");
			exit;
		}
	}
	else { // if (!isset($_POST['submit'])) {
		mysqli_close($mysqli);
		header("Location: ../index.php");
		exit;
	}
	
	
	
	
	
	
?>