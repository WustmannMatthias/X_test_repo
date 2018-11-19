<?php
	/*
	§§ software installation
	§§ database creation
	§§ database initialisation
	*/
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Installation du logiciel X</title>
		<meta charset="UTF-8" />
	</head>
	<body>
		
		<?php
		    /**
			 * PROGRAMME D'INSTALLATION DE LA BASE DE DONNEES
			 */
			
			echo "<h1>Bienvenue chez X !</h1>";
			echo "<br /><br /><br />";
			
			$pathData = "../data/";
			require_once $pathData."parametres_bd.inc.php";
			require_once $pathData."fonctions.inc.php";
			
			
			//Connexion au serveur MySQLi
			$mysqli = mysqli_connect($host, $user, $pass, "") or die ("Problème de connexion au serveur mysqli : ".mysqli_connect_error());
			
			
			query($mysqli, "DROP DATABASE IF EXISTS $base");
			$ok = query($mysqli, "CREATE DATABASE $base");
			if ($ok) {
				echo "Base de données $base créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la base de données $base";
			}
			mysqli_select_db($mysqli, $base);
			
			echo "<br /><br /><br />";
			/*
			 * Relation 1 : comptes
			 */
			
			$ok = query($mysqli, "CREATE TABLE IF NOT EXISTS `comptes` (
								  `ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
								  `utilisateur` varchar(32) NOT NULL,
								  `mot_de_passe` varchar(32) NOT NULL,
								  `quantite_possedee` double NOT NULL,
								  `argent_possede` double NOT NULL,
								  `dette` double NOT NULL,
								  `commentaire` text NOT NULL
								) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						");
			if ($ok) {
				echo "Relation 'comptes' créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la relation 'comptes'";
			}
			echo "<br />";
			
			
			/*
			 * Relation 2 : cycles
			 */
			
			$ok = query($mysqli, "CREATE TABLE IF NOT EXISTS `cycles` (
								  `primary_key` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
								  `ID` int(11) NOT NULL, 
								  `compte` int(11) NOT NULL,
								  `date_achat` date NOT NULL,
								  `quantite_achat` double NOT NULL, 
								  `tarif` double NOT NULL, 
								  `handicap` double NOT NULL,
								  `commentaire` text NOT NULL,
								  `actif` varchar(5) NOT NULL
								  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						");
			if ($ok) {
				echo "Relation 'cycles' créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la relation 'cycles'";
			}
			echo "<br />";
			
			
			/*
			 * Relation 3 : ventes
			 */
			
			$ok = query($mysqli, "CREATE TABLE IF NOT EXISTS `ventes` (
								  `primary_key` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
								  `compte` int(11) NOT NULL,
								  `cycle` int(11) NOT NULL,
								  `client` int(11) NOT NULL,
								  `quantite` double NOT NULL, 
								  `tarif` double NOT NULL, 
								  `date` date NOT NULL,
								  `methode` varchar(32) NOT NULL,
								  `montant_paye` double
								  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						");
			if ($ok) {
				echo "Relation 'ventes' créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la relation 'ventes'";
			}
			echo "<br />";
			
			
			
			/*
			 * Relation 4 : differes
			 */
			
			$ok = query($mysqli, "CREATE TABLE IF NOT EXISTS `differes` (
								  `primary_key` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
								  `compte` int(11) NOT NULL,
								  `cycle` int(11) NOT NULL,
								  `date_emprunt` date NOT NULL,
								  `client` int(11) NOT NULL,
								  `montant` double NOT NULL, 
								  `montant_rembourse` double NOT NULL,
								  `date_finalisation` date NOT NULL
								  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						");
			if ($ok) {
				echo "Relation 'differes' créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la relation 'differes'";
			}
			echo "<br />";
			
			
			
			/*
			 * Relation 5 : clients
			 */
			
			$ok = query($mysqli, "CREATE TABLE IF NOT EXISTS `clients` (
								  `primary_key` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
								  `ID` int(11) NOT NULL, 
								  `compte` int(11) NOT NULL, 
								  `nom` varchar(32) NOT NULL,
								  `date_enregistrement` date NOT NULL
								  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						");
			if ($ok) {
				echo "Relation 'clients' créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la relation 'clients'";
			}
			echo "<br />";
			
			
			
			
			
			/*
			 * Relation 6 : modifications_compte
			 */
			
			$ok = query($mysqli, "CREATE TABLE IF NOT EXISTS `modifications_compte` (
								  `primary_key` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
								  `compte` int(11) NOT NULL, 
								  `operation` varchar(32),
								  `date` date NOT NULL,
								  `attribut_modifie` varchar(32) NOT NULL,
								  `valeur_modification` double NOT NULL,
								  `motif` text NOT NULL
								  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						");
			if ($ok) {
				echo "Relation 'modifications_compte' créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la relation 'modifications_compte'";
			}
			echo "<br />";
			
			
			
			/*
			 * Relation 7 : retraits_marchandise
			 */
			$ok = query($mysqli, "CREATE TABLE IF NOT EXISTS `retraits_marchandise` (
								  `primary_key` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
								  `date` date NOT NULL,
								  `compte` int(11) NOT NULL, 
								  `cycle` int(11) NOT NULL,
								  `quantite_retiree` double NOT NULL,
								  `motif` text NOT NULL
								  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						");
			if ($ok) {
				echo "Relation 'retraits_marchandise' créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la relation 'retraits_marchandise'";
			}
			echo "<br />";
			

			
			/*
			 * Relation 8 : remboursements_handicap
			 */
			$ok = query($mysqli, "CREATE TABLE IF NOT EXISTS `remboursements_handicap` (
								  `primary_key` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
								  `date` date NOT NULL,
								  `compte` int(11) NOT NULL, 
								  `cycle` int(11) NOT NULL,
								  `quantite_remboursee` double NOT NULL
								  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						");
			if ($ok) {
				echo "Relation 'remboursements_handicap' créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la relation 'remboursements_handicap'";
			}
			echo "<br />";
			


			/*
			 * Relation 8 : remboursements_differes
			 */
			$ok = query($mysqli, "CREATE TABLE IF NOT EXISTS `remboursements_differes` (
								  `primary_key` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
								  `date` date NOT NULL,
								  `compte` int(11) NOT NULL, 
								  `cycle` int(11) NOT NULL,
								  `client` varchar(32) NOT NULL,
								  `montant_rembourse` double NOT NULL
								  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						");
			if ($ok) {
				echo "Relation 'remboursements_differes' créee avec succès";
			}
			else {
				echo "Erreur lors de la création de la relation 'remboursements_differes'";
			}
			echo "<br />";
			
			
			mysqli_close($mysqli);
		?>
		
		
		
		
		
		<br /><br /><br />
		<h3 style="color: green; text-decoration: underline;">L'installation s'est déroulée correctement.</h3>
			
		<br /><br /><br /><br />
		<a href="../index.php" style="font-size: 1.25em; background-color: lightgreen; border-radius: 5px; padding: 10px; color: black;">
			Retour à la page de connexion
		</a>
		
		
	</body>
</html>

