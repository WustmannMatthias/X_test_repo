<?php
/*
	§§ Affichage de la page d accueil du site
	§§ Menu Aside
	§§ Modal cycle
	§§ Modal vente
	§§ Modal retrait de marchandise
	§§ Modal remboursement de handicap
	§§ Statistiques du cycle courant
*/
?>

<!DOCTYPE html>
<html>
	<head>
		<title>X Accueil</title>
		
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Ubuntu:400" />
		
		<link href="style/bootstrap.css" rel="stylesheet" />
		<link href="style/bootstrap-theme.css" rel="stylesheet" />
		<link href="style/bootsperso.css" rel="stylesheet" />
		
		<script type="application/javascript" src="scripts/jquery-3.2.1.js"></script>
		<script type="application/javascript" src="scripts/bootstrap.js"></script>
		
		<link type="text/css" rel="stylesheet" href="style/stylesheet.css" />
		<script type="application/javascript" src="scripts/gestion_interactions.js"></script>
		<script type="application/javascript" src="scripts/gestion_affichage.js"></script>
	</head>
	<body>
		<div class="container-fluid">
			<?php
				/**
				 * fonction qui retourne la liste des clients au format array : 
				 * ID -> nom
				 * ID -> nom
				 */
				function getClientsTab() {
					global $mysqli;
					$IDcompte = $_SESSION['ID'];
					$clientsTab = array();
					$resultClients = query($mysqli, "SELECT ID, nom FROM clients WHERE compte = '$IDcompte';");
					while ($n_uplet = mysqli_fetch_assoc($resultClients)) {
						$clientsTab[$n_uplet['ID']] = $n_uplet['nom'];
					}
					return $clientsTab;
				}
				
				
				
				
				
				$pathInclude 	= "frames/";
				$pathProgrammes = "programmes/";
				$pathObjects 	= "objects/";
				
				require_once $pathObjects."Compte.php";
				require_once $pathObjects."Cycle.php";
				require_once $pathObjects."Vente.php";
				require_once $pathObjects."Differe.php";
				require_once $pathObjects."Client.php";
				
				session_start();
				
				//Début du corps de la page
				if (!isset($_SESSION['connecte'])) {
					require_once $pathInclude."ecran_connexion.php";
				}
				else { //Pour un utilisateur connecté
					
					//Définition des chemins d'accès vers les dossiers
					$pathData = "data/";
					
					//inclusions de données relatives à la base de données et de fonctions php
					include $pathData."fonctions.inc.php";
					include $pathData."parametres_bd.inc.php";
					
					//Connexion au serveur mysqli
					$mysqli = mysqli_connect($host, $user, $pass);
					mysqli_select_db($mysqli, $base);
					
			?>
			
			<div class="row">
				<?php include $pathInclude."navbar.php"; ?>
			</div>
			
			<div class="row">
				<?php include $pathInclude."aside.php"; ?>
				
				<div id="partie_ecran_variable">
					<?php
						if (isset($_GET['accueil']) || $_SERVER['QUERY_STRING'] == "") {
							include $pathInclude."main_accueil.php"; 
						}
						elseif (isset($_GET['mon_compte'])) {
							include $pathInclude."mon_compte.php";
						}
						elseif (isset($_GET['cycles'])) {
							include $pathInclude."cycles.php";
						}
						elseif (isset($_GET['cycle']) && isset($_GET['primary_key'])) {
							include $pathInclude."detail_cycle.php";
						}
						elseif (isset($_GET['ventes'])) {
							include $pathInclude."ventes.php";
						}
						elseif (isset($_GET['differes'])) {
							include $pathInclude."differes.php";
						}
						elseif (isset($_GET['remboursements_differes'])) {
							include $pathInclude."remboursements_differes.php";
						}
						elseif (isset($_GET['clients'])) {
							include $pathInclude."clients.php";
						}
						elseif (isset($_GET['interface_admin'])) {
							include $pathInclude."interface_admin.php";
						}
					?>
				</div>
			</div>
		
			<?php
				}
			?>
		</div>
		
	</body>
</html>



