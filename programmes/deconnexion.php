
<?php
	/**
	§§ Se déconnecter du compte

	 * Ce programme est appelé depuis le bouton Déconnexion du aside 
	 * Son rôle est de déconnecter l'utilisateur et de le renvoyer vers la page d'accueil du site
	 * 	(en mode non connecté).
	 */
	
	
	
	//Récupération de la session courante
	session_start();
	
	//Vérification de l'accès légitime à cette page
	if (isset($_GET['deconnexion'])) {
		
		//destruction des variables de session qui maintiennent la connexion
		session_destroy();
		
		//Redirection
		header("Location: ../index.php");
		exit;
		
	}
	else { // if (!isset($_POST['deconnexion']))
		header("Location: ../index.php");
		exit;
	}
	
?>