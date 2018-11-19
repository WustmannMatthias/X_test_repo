<?php
	/**
	§§ Enregistrer un retrait de marchandise

	 * Ce programme est appelé lors de la validation du formulaire enregister_retrait_marchandise.
	 * 	Son rôle est d'appliquer les modifications nécessaires au compte et au cycle, puis de renvoyer ces objets dans la base de données.
	 * 	Une ligne dans la table retraits_marchandise sera également ajoutée.
	 */
	
	
	session_start();
	
	if (isset($_POST['dateRetrait'])) echo "OKdate";
	if (isset($_POST['montant'])) echo "OKmontant";
	if (isset($_POST['motif'])) echo "OKmotif";
	if (isset($_POST['cycle'])) echo "OKcycle";
	//Vérification que TOUTES les données nécessaires au cycle aient été entrées
	if (isset($_POST['dateRetrait']) && isset($_POST['montant']) && isset($_POST['motif']) && isset($_POST['cycle'])) {
		$date = $_POST['dateRetrait'];
		$montant = $_POST['montant'];
		$motif = $_POST['motif'];
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
		
		
		//Etape 0 : Tests sur les entrées
		if (is_nan($montant) || $montant <= 0) {
			$_SESSION['messageErreurEnregistrementRetraitMarchandise'] = "Montant invalide.";
			exit;
		}
		if ($motif != mysqli_real_escape_string($mysqli, $motif)) {
			$_SESSION["messageErreurEnregistrementRetraitMarchandise"] = "Motif invalide.";
			exit;
		}
		
		$resultStockAchatCycle = query($mysqli, "SELECT quantite_achat FROM cycles WHERE compte = '$IDcompte' AND ID = '$cycle'");
		$stockAchatCycle = mysqli_fetch_assoc($resultStockAchatCycle)['quantite_achat'];
		$resultQteVendue = query($mysqli, "SELECT sum(quantite) qte FROM ventes WHERE compte = '$IDcompte' AND cycle = '$cycle'");
		$qteVendue = mysqli_fetch_assoc($resultQteVendue)['qte'];
		$resultQteRetiree = query($mysqli, "SELECT sum(quantite_retiree) qte FROM retraits_marchandise WHERE compte = '$IDcompte' AND cycle = '$cycle'");
		$qteRetiree = mysqli_fetch_assoc($resultQteRetiree)['qte'];
		if ($montant > ($stockAchatCycle - ($qteVendue + $qteRetiree))) {
			$_SESSION["messageErreurEnregistrementRetraitMarchandise"] = "Pas assez de marchandise pour effectuer un tel retrait.";
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
		
		
		//Etape 3 : Réalisation du retrait de marchandise
		$compteCourant ->retirerMarchandise($cycle, $montant);
		
		//Etape 4 : Update du compte dans la base de données
		$quantitePossedee = $compteCourant ->getQuantitePossedee();
		$ok = query($mysqli, "UPDATE comptes 
							  SET quantite_possedee = '$quantitePossedee' 
							  WHERE ID = '$IDcompte';
					"); 			
		if (!$ok) {
			$_SESSION["messageErreurEnregistrementRetraitMarchandise"] = "Erreur lors de l'actualisation du compte dans la base de données. Enregistrement du retrait de marchandise abandonné. ";
			mysqli_close($mysqli);
			exit;
		}
		else {
			//Etape 5 : Enregistrement du retrait de marchandise dans la base de données
			$ok = query($mysqli, "INSERT INTO retraits_marchandise (date, compte, cycle, quantite_retiree, motif)
									VALUE ('$date', '$IDcompte', '$cycle', '$montant', '$motif');
						");
			if ($ok) {
				$_SESSION['messageConfirmationEnregistrementRetraitMarchandise'] = "Retrait de marchandise enregistré.";
			}
			else {
				$_SESSION['messageErreurEnregistrementRetraitMarchandise'] = "Echec de l'enregistrement du retrait de marchandise. Attention, les données du compte ont été modifiées. Ajustez les données du compte ou contactez un administrateur.";
			}
			mysqli_close($mysqli);
			exit;
		}
		
	}
	else { //Si il manque des informations
		$_SESSION['messageErreurEnregistrementRetraitMarchandise'] = "Veuillez fournir tous les éléments nécessaires à l'enregistrement d'un retrait de marchandise.";
		exit;
	}
	
?>