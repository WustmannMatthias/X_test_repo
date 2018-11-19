<?php
	
	require_once "Cycle.php";
				
	/**
	 * Classe Compte
	 * 	Un Compte représente l'utilisateur du logiciel
	 */
	class Compte {
		
		private $_identifiant;
		private $_utilisateur;
		private $_quantitePossedee;
		private $_argentPossede;
		private $_dette;
		private $_commentaire;
		
		private $_cyclesTab;
		
		
		/**
		 * Constructeur de l'objet Compte
		 * @param $identifiant est un integer
		 * @param $utilisateur est une chaine de caractères
		 * @param $quantitePossede est un nombre positif
		 * @param $argentPossede est un nombre positif
		 * @param $dette est un nombre positif
		 */
		function __construct($identifiant, $utilisateur, $quantitePossedee, $argentPossede, $dette, $commentaire) {
			$this ->setIdentifiant($identifiant);
			$this ->setUtilisateur($utilisateur);
			$this ->setQuantitePossedee($quantitePossedee);
			$this ->setArgentPossede($argentPossede);
			$this ->setDette($dette);
			$this ->setCommentaire($commentaire);
			
			$this ->initialiseCyclesTab();
			
			//echo "Objet Compte créé avec succès. <br />";
		}
		
		
		
		/**
		 * Methode acheter();
		 * 	Cette méthode est appelée lors d'un achat, soit lors du début d'un nouveau Cycle.
		 * 	Cette méthode : 
		 * 			- Crée un nouvel objet Cycle
		 * 			- En fonction de la méthode de payement, l' _argentPossede et la _dette sont actualisés
		 * 			- Actualise la _quantitePossedee en fonction de l'achat
		 * 			- Le nouveau Cycle est ajouté à _cyclesTab.
		 * 	
		 * @param $donneesAchat est de type array.
		 * 	Sa structure : 
		 * 		$donneesAchat[0] = date d'achat au format String
		 * 		$donneesAchat[1] = quantite achetee au format numérique
		 * 		$donneesAchat[2] = tarif d'achat au format numérique
		 * 		$donneesAchat[3] = methode de payement ("cash", "accompte", ou "differe")
		 * 		$donneesAchat[4] = commentaire au format String (= "") si aucun commentaire
		 * 		$donneesAchat[5] = (optionnel) quantité dejà payée (si methode = "accompte")
		 * 	=> array($date, $quantiteAchat, $tarif, $methodePayement, $commentaire [, $montantPaye]);
		 */
		public function acheter($donneesAchat) {
			$date				= $donneesAchat[0];
			$quantiteAchat		= $donneesAchat[1];
			$tarif				= $donneesAchat[2];
			$methodePayement	= $donneesAchat[3];
			$commentaire		= $donneesAchat[4];
			if (count($donneesAchat) == 6) { //Si la fonction est appelée avec 6 arguments, soit une methode accompte
				$montantPaye	= $donneesAchat[5];
			}
			
			//Génération d'un cycle avec un handicap nul
			$cycle = $this ->genereCycle($date, $quantiteAchat, $tarif, $commentaire);
			$montant = $cycle ->calculerPrix();
			
			//Actualisation de l'argent possédé (et de la dette si nécessaire)
			if ($methodePayement == "Differe") {
				$this ->setDette($this ->_dette + $montant);
				$cycle ->setHandicap($montant);
			}
			elseif ($methodePayement == "Accompte") {
				$montantRestant = $montant - $montantPaye; //Qte restante à payer
				$argentRestant = $this ->_argentPossede - $montantPaye; //Qte restante sur le compte après l'achat
				if ($argentRestant < 0) {
					trigger_error("Achat impossible, pas assez d'argent sur le compte.", E_USER_WARNING);
					return;
				}
				$this ->setArgentPossede($argentRestant);
				$this ->setDette($this ->_dette + $montantRestant);
				$cycle ->setHandicap($montantRestant);
			}
			elseif ($methodePayement == "Cash") {
				$argentRestant = $this ->_argentPossede - $montant;
				if ($argentRestant < 0) {
					trigger_error("Achat impossible, pas assez d'argent sur le compte.", E_USER_WARNING);
					return;
				}
				$this ->setArgentPossede($argentRestant);
			}
			else {
				echo "Methode de payement invalide. <br />";
				return;
			}
			
			//Augmenter la quantite possédée en fonction de la quantité achetée
			$this -> setQuantitePossedee($this ->_quantitePossedee + $quantiteAchat);
			
			array_push($this ->_cyclesTab, $cycle); //Ajout du cycle 
			return $cycle;
		}
		
		
		/**
		 * Methode genereCycle();
		 * 	Cette méthode génère un Cycle à partir des données de la methode 'acheter' et d'autres données
		 * 	à calculer (nom du Compte, ID de la vente)
		 */
		public function genereCycle($date, $quantiteAchat, $tarif, $commentaire) {
			$ID;
			if (count($this ->_cyclesTab) > 0) {
				$dernierCycle = $this ->_cyclesTab[-1];
				$ID = $dernierCycle ->getIdentifiant();
			}
			else {
				$ID = 1;
			}
			$compte = $this ->_identifiant;
			
			//Le cycle est créé avec un handicap de 0, puis est actualisé par la suite en fonction de la methode de payement
			$cycle = new Cycle($ID, $compte, $date, $quantiteAchat, $tarif, 0, $commentaire);
			return $cycle;
		}
		
		
		
		
		
		
		/**
		 * Methode vendre();
		 * 	Cette méthode sélectionne le cycle dans _cyclesTab auquel se rapporte la Vente à enregistrer, 
		 * 	puis appelle la méthode vendre() du Cycle en question, qui va créer les objets Vente et Differe et 
		 * 	actualiser le Cycle.
		 * 	
		 * 	Le montant en cash est retourné, et l'attribut _argentPossede du compte est actualisé en conséquence.
		 * 
		 * @param $donnneesVente est de type array.
		 * Sa structure : 
		 * 		$donneesVente[0] = cycle (identifiant au format numérique)
		 * 		$donneesVente[1] = quantite vendue au format numerique
		 * 		$donneesVente[2] = tarif de vente au format numerique
		 * 		$donneesVente[3] = client (identifiant au format numérique)
		 * 		$donneesVente[4] = date de vente au format String
		 * 		$donneesVente[5] = methode de payement ("cash", "accompte", ou "differe")
		 * 		$donneesVente[6] = (optionnel) quantité dejà payée (si methode = "accompte")
		 * => array($cycle, $quantiteVente, $tarifVente, $client, $date, $methodePayement [, $montantPaye]) 
		 */
		public function vendre($donneesVente) {
			$cycle				= $donneesVente[0];
			$quantiteVente		= $donneesVente[1];
			$tarifVente			= $donneesVente[2];
			$client				= $donneesVente[3];
			$date				= $donneesVente[4];
			$methodePayement	= $donneesVente[5];
			if (count($donneesVente) == 7) { //Si la fonction est appelée avec 6 arguments, soit une methode accompte
				$montantPaye	= $donneesVente[6];
			}
			
			if ($methodePayement == "Accompte" && !isset($montantPaye)) {
				trigger_error("Erreur lors de la création de l'accompte : le montant déjà payé n'est pas spécifié.", E_USER_ERROR);
				return;
			}
			
			//Récupération de l'objet Cycle à traiter depuis l'identifiant fourni
			$cycleATraiter = false;
			foreach ($this ->_cyclesTab as $item) {
				if ($item ->getIdentifiant() == $cycle) {
					$cycleATraiter = $item;
					break;
				}
			}
			if(!$cycleATraiter) {
				trigger_error("Le cycle sélectionné pour la vente à effectuer n'existe pas.", E_USER_ERROR);
				return;
			}
			
			//Génération d'un tableau de paramètres pour appeler la methode vendre() du Cycle à traiter
			$params = array($quantiteVente, $tarifVente, $client, $date, $methodePayement);
			if (isset($montantPaye)) {
				array_push($params, $montantPaye);
			}
			//Cette ligne execute la methode vendre() du Cycle (création de la Vente, etc) et récupère le montant encaissé en cash
			$montantRecupere = $cycleATraiter ->vendre($params);
			//Actualisation de la qte d'argent possédée et du la quantite de marchandise possedee
			$this ->setArgentPossede($this ->_argentPossede + $montantRecupere);
			$this ->setQuantitePossedee($this ->_quantitePossedee - $quantiteVente);
		}
		
		
		
		
		
		
		/**
		 * Fonction appelée lors d'un retrait de marchandise sur un cycle (consommations par exemple)
		 * Son role va être d'appeler la function retraitMarchandise() du cycle en question, et d'actualiser les
		 * 	données du compte
		 * 
		 * @param $cycle est l'identifiant du cycle sur lequel la marchandise est à retirer
		 * @param $quantite est la quantite de marchandise à déduire
		 */
		public function retirerMarchandise($cycle, $quantite) {
			if (is_nan($quantite) || $quantite < 0) {
				trigger_error("Impossible de retirer un nombre négatif de marchandise.", E_USER_WARNING);
				return;
			}
			
			//Récupération de l'objet Cycle à traiter depuis l'identifiant fourni
			$cycleATraiter = false;
			foreach ($this ->_cyclesTab as $item) {
				if ($item ->getIdentifiant() == $cycle) {
					$cycleATraiter = $item;
					break;
				}
			}
			if(!$cycleATraiter) {
				trigger_error("Le cycle sélectionné pour la vente à effectuer n'existe pas.", E_USER_ERROR);
				return;
			}
			$ok = $cycleATraiter ->retirerMarchandise($quantite);
			if ($ok) {
				$this ->setQuantitePossedee($this ->_quantitePossedee - $quantite);
			}
		}
		
		
		/**
		 * Méthode appelée lors d'un remboursement de handicap sur un cycle
		 * Son role va être d'appeler la function rembourserHandicap() du cycle en question, et d'actualiser les
		 * 	données du compte
		 * 
		 * @param $cycle est l'identifiant du cycle sur lequel la marchandise est à retirer
		 * @param $montant est le montant à rembourser
		 */
		public function rembourserHandicap($cycle, $montant) {
			if (is_nan($montant) || $montant < 0) {
				trigger_error("Impossible de rembourser un montant negatif.", E_USER_WARNING);
				return;
			}
			
			//Récupération de l'objet Cycle à traiter depuis l'identifiant fourni
			$cycleATraiter = false;
			foreach ($this ->_cyclesTab as $item) {
				if ($item ->getIdentifiant() == $cycle) {
					$cycleATraiter = $item;
					break;
				}
			}
			if(!$cycleATraiter) {
				trigger_error("Le cycle sélectionné pour la vente à effectuer n'existe pas.", E_USER_ERROR);
				return;
			}
			$ok = $cycleATraiter ->rembourserHandicap($montant);
			if ($ok) {
				$this ->setArgentPossede($this ->_argentPossede - $montant);
				$this ->setDette($this ->_dette - $montant);
			}
		}
		
		
		
		
		
		/**
		 * Méthode appelée lors de l'enregistrement d'un remboursement de differe.
		 * 	Son role va etre d'appeler la fonction percevoirRemboursement() de l'objet Cycle concerné, et d'actualiser
		 * 	les données du compte.
		 * 
		 * @param $cycle est l'identifiant du cycle sur lequel la marchandise est à retirer
		 * @param $differe est l'identifiant du differe à rembourser
		 * @param $montant est le montant du remboursement
		 * @param $dateRemboursement est la date du remboursement (format String)
		 */
		public function percevoirRemboursement($cycle, $differe, $montant, $dateRemboursement) {
			if (is_nan($montant) || $montant < 0) {
				trigger_error("Impossible de percevoir un remboursement d'un montant negatif.", E_USER_WARNING);
				return;
			}
			
			//Récupération de l'objet Cycle à traiter depuis l'identifiant fourni
			$cycleATraiter = false;
			foreach ($this ->_cyclesTab as $item) {
				if ($item ->getIdentifiant() == $cycle) {
					$cycleATraiter = $item;
					break;
				}
			}
			if(!$cycleATraiter) {
				trigger_error("Le cycle sélectionné pour la vente à effectuer n'existe pas.", E_USER_ERROR);
				return;
			}
			$ok = $cycleATraiter ->percevoirRemboursement($differe, $montant, $dateRemboursement);
			if ($ok) {
				$this ->setArgentPossede($this ->_argentPossede + $montant);
			}
		}
		
		
		
		
		
		
		
		
		/*
		 * Méthodes pour initialiser les attributs _cyclesTab lors de la création
		 * 	de l'objet Compte
		 */
		
		public function initialiseCyclesTab() {
			$this ->_cyclesTab = array();
		}
		
		
		/**
		 * Methode renvoyant un tableau de tous les attributs du Cycle nécessaires dans la BDD
		 */
		public function toArray() {
			$attributs = array();
			array_push($attributs, $this ->_identifiant);
			array_push($attributs, $this ->_utilisateur);
			array_push($attributs, $this ->_quantitePossedee);
			array_push($attributs, $this ->_argentPossede);
			array_push($attributs, $this ->_dette);
			array_push($attributs, $this ->_commentaire);
			return $attributs;
		}
		
		
		
		
		/*
		 * Accesseurs et Mutateurs
		 */
		
		public function setIdentifiant($identifiant) {
			//Ajouter tests sur le format de l'ID
			$this ->_identifiant = $identifiant;
		}
		public function getIdentifiant() {
			return $this ->_identifiant;
		}
		
		public function setUtilisateur($utilisateur) {
			//Ajouter tests sur le format de l'utilisateur
			$this ->_utilisateur = $utilisateur;
		}
		public function getUtilisateur() {
			return $this ->_utilisateur;
		}
		
		public function setQuantitePossedee($quantitePossedee) {
			if (is_nan($quantitePossedee) || $quantitePossedee < 0) {
				trigger_error("La quantite possedée doit être un nombre positif.", E_USER_WARNING);
				return;
			}
			$this ->_quantitePossedee = $quantitePossedee;
		}
		public function getQuantitePossedee() {
			return $this ->_quantitePossedee;
		}
		
		public function setArgentPossede($argentPossede) {
			if (is_nan($argentPossede) || $argentPossede < 0) {
				trigger_error("L'argent possedé doit être un nombre positif.", E_USER_WARNING);
				return;
			}
			$this ->_argentPossede = $argentPossede;
		}
		public function getArgentPossede() {
			return $this ->_argentPossede;
		}
		
		public function setDette($dette) {
			if (is_nan($dette) || $dette < 0) {
				trigger_error("La dette doit être un nombre positif.", E_USER_WARNING);
				return;
			}
			$this ->_dette = $dette;
		}
		public function getDette() {
			return $this ->_dette;
		}
		
		public function setCommentaire($commentaire) {
			//Ajouter tests sur le format du texte
			$this ->_commentaire = $commentaire;
		}
		public function getCommentaire() {
			return $this ->_commentaire;
		}
		
		public function setCyclesTab($cyclesTab) {
			if (!is_array($cyclesTab)) {
				trigger_error("Type incompatibel : un tableau est attendu pour l'attribut \$_cyclesTab.");
				return;
			}
			$this ->_cyclesTab = $cyclesTab;
		}
		public function getCyclesTab() {
			return $this ->_cyclesTab;
		}
		
		
		public function toString() {
			$sortie = "COMPTE <br />";
			$sortie.= "ID : ".($this ->_identifiant)."<br />";
			$sortie.= "Utilisateur : ".($this ->_utilisateur)."<br />";
			$sortie.= "Quantité possédée : ".($this ->_quantitePossedee)."<br />";
			$sortie.= "Argent possédé : ".($this ->_argentPossede)."<br />";
			$sortie.= "Dette : ".($this ->_dette)."<br />";
			$sortie.= "Commentaire : ".($this ->_commentaire)."<br />";
			$sortie.= (count($this ->_cyclesTab))." cycles en cours <br />"; 
			return $sortie;
		}
		
	}
	
	
?>