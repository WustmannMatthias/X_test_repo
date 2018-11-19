<?php
	/*
	§§ execute les requetes sql admin
	
	 * Ce programme est appelé de manière asynchrone depuis le script de gestion de l'interface
	 * 	administrateur gestion_interface.js .
	 * Il reçoit en paramètre une requête SQL, et la traite, et construit à partir du résultat 
	 * 	un tableau html, écrit en sortie.
	 * La sortie de ce programme sera directement chargée dans la zone prévue à cet effet de 
	 * 	l'interface. 
	 */
	 
	 
	 //Récupération de la requete et de la base dans laquelle elle doit être effectuée
	 if (isset($_GET['requete']) && isset($_GET['base'])) {
		$requete = trim($_GET['requete']);
		$baseDemande = trim($_GET['base']);
		
		if ($requete == "") {
			exit;
		}
		
		//Connexion au serveur mysqli et sélection de la base demandée
		$pathInclude = "../data/";
		include $pathInclude."fonctions.inc.php";
		include $pathInclude."parametres_bd.inc.php";
		
		$mysqli = mysqli_connect($host, $user, $pass);
		mysqli_select_db($mysqli, $baseDemande);
		
		
		
		
		
		//Préparation de la sortie HTML
		//Traitement requete
		$resultatRequete = query($mysqli, $requete);
		if (mysqli_num_rows($resultatRequete) == 0) {
			echo "<span class='erreur'>Aucun résultat trouvé dans la base de données</span>";
			exit;
		}
		
		//Préparation de l'entête du tableau de sortie
		$sortieHTML = "<table cellspacing='0'>";
		$sortieHTML .= "<thead><tr>";
		$ligneSortie = mysqli_fetch_assoc($resultatRequete);
		foreach ($ligneSortie as $attribut => $valeur) {
			$sortieHTML .= "<th>$attribut</th>";
		}
		$sortieHTML .= "</tr></thead>";
		
		//Préparation du corps du tableau de sortie
		$sortieHTML .= "<tbody>";
		mysqli_data_seek($resultatRequete, 0); //Repositionnement au premier n-uplet
		while ($n_uplet = mysqli_fetch_assoc($resultatRequete)) {
			$sortieHTML .= "<tr>";
			foreach ($n_uplet as $valeur) {
				$sortieHTML .= "<td>$valeur</td>";
			}
		}
		
		//Sortie du programme
		echo $sortieHTML;
		exit;
		
		
		
		
		

	}
?>