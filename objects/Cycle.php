<?php
	
	namespace some\namespace;

	//use Some\Weird\Namespace;

	require_once "Vente.php";
	require_once "Differe.php";

	/**
	 * Classe Cycle
	 * 	Un cycle représente un achat.
	 * 
	 * C'est sur une instance de Cycle que l'utilisateur travaillera.
	 * 	Les objets Vente et Differe sont gérés via cet objet. Une vente, comme un differe, 
	 * 	se rapportent à un Cycle.
	 * 
	 */
	class Cycle {
		
		private $_identifiant;
		private $_compte;
		private $_dateAchat;
		private $_quantiteAchat;
		private $_tarif;
		private $_handicap;
		private $_commentaire;
		
		private $_ventesTab;
		private $_differesTab;
		private $_marchandiseRetiree;
		private $_handicapRembourse;
		
		
		
		/**
		 * Constructeur de l'objet Cycle
		 * @param $identifiant est un integer
		 * @param $compte est un integer, qui représente l'ID du Compte 
		 * @param $quantite est un nombre positif, exprimant une quantité en grammes
		 * @param $dateAchat est une chaine de caractère représentant une date au format JJ/MM/AAAA
		 * @param $tarif est un nombre positif, représentant un ratio prix/quantité
		 * @param $handicap est un nombre positif, représentant de déficite à l'instanciation du Cycle
		 * @param $commentaire est une chaine de caractères 
		 */
		public function __construct($identifiant, $compte, $dateAchat, $quantiteAchat, $tarif, $handicap, $commentaire) {
			$this ->setIdentifiant($identifiant);
			$this ->setCompte($compte);
			$this ->setDateAchat($dateAchat);
			$this ->setQuantiteAchat($quantiteAchat);
			$this ->setTarif($tarif);
			$this ->setHandicap($handicap);
			$this ->setCommentaire($commentaire);
			
			$this ->initialiseVentesTab();
			$this ->initialiseDifferesTab();
			$this ->initialiseMarchandiseRetiree();
			$this ->initialiseHandicapRembourse();
			
			//echo "Objet Cycle créé avec succès. <br />";
		}
		
		
		
		
		
		/**
		 * Renvoie le prix d'achat du cycle
		 */
		public function calculerPrix() {
			return $this ->_quantiteAchat * $this ->_tarif;
		}
		
		/**
		 * Renvoie le nombre de ventes effectuees
		 */
		public function compterVentesEffectuees() {
			return count($this ->_ventesTab);
		}
		/**
		 * Renvoie le nombre de ventes effectuees
		 */
		public function compterDifferes() {
			return count($this ->_differesTab);
		}
		
		/**
		 * Renvoie le nombre de differes en cours
		 */
		public function compterDifferesEnCours() {
			$acc = 0;
			foreach ($this ->_differesTab as $differe) {
				if (!($differe ->estRembourse())) {
					$acc ++;
				}
			}
			return $acc;
		}
		
		/**
		 * Renvoie la quantite restante du cycle
		 */
		public function calculerQuantiteRestante() {
			$quantiteRestante = $this ->_quantiteAchat;
			foreach ($this ->_ventesTab as $vente) {
				$quantiteRestante -= $vente ->getQuantite();
			}
			$quantiteRestante -= $this ->_marchandiseRetiree;
			return $quantiteRestante;
		}
		
		/**
		 * Renvoie la quantite vendue du cycle (somme de la quantité de chaque vente)
		 */
		public function calculerQuantiteVendue() {
			$acc = 0;
			foreach ($this ->_ventesTab as $vente) {
				$acc += $vente ->getQuantite();
			}
			return $acc;
		}
		
		/**
		 * A MODIFIER
		 * Renvoie la quantite manquante de marchandise (ventes + consommations + pertes)
		 */
		public function calculerQuantiteEcoulee() {
			return ($this ->calculerQuantiteVendue() + $this ->getMarchandiseRetiree());
		}
		
		/**
		 * Renvoie l'argent récupéré sur un cycle
		 */
		public function calculerArgentRecupere() {
			$argentRecupere = 0;
			foreach ($this ->_ventesTab as $vente) {
				$argentRecupere += $vente ->calculerPrix();
			}
			foreach ($this ->_differesTab as $differe) {
				if (!($differe ->estRembourse())) {
					$argentRecupere -= $differe ->calculerMontantRestantDu();
				}
			}
			return $argentRecupere;
		}
		
		/**
		 * Calcule la somme des differes EN COURS du cycle
		 */
		public function calculerArgentDu() {
			$argentDu = 0;
			foreach ($this ->_differesTab as $differe) {
				if (!($differe ->estRembourse())) {
					$argentDu += $differe ->calculerMontantRestantDu();
				}
			}
			return $argentDu;
		}
		
		/**
		 * Calcule l'argent total des ventes effectuées sur un cycle (differes compris)
		 */
		public function calculerArgentTotalVentes() {
			return ($this ->calculerArgentRecupere() + $this ->calculerArgentDu());
		}
		
		/**
		 * Calcule le tarif de vente moyen de la marchandise du cycle
		 */
		public function calculerTarifVenteMoyen() {
			if (count($this ->_ventesTab) == 0) {
		 		return ("?");
		 	}
			else {
				return ($this ->calculerArgentTotalVentes() / $this ->calculerQuantiteVendue());
			}
		}
		
		
		/**
		 * Renvoie le potentiel d'un cycle en se basant sur l'argent récupéré (donc sans compter les differes) 
		 */
		 public function calculerPotentielNet() {
		 	if (count($this ->_ventesTab) == 0) {
		 		$potentiel = "?";
		 	}
			else {
				$potentiel = ($this ->calculerArgentRecupere() / $this ->calculerQuantiteVendue()) * $this ->calculerQuantiteRestante();
			}
			return $potentiel;
		 }
		 
		/**
		 * Renvoie le potentiel d'un cycle en se basant sur le tarif moyen des ventes (donc en comptant les differes)
		 */
		 public function calculerPotentielBrut() {
		 	if (count($this ->_ventesTab) == 0) {
		 		$potentiel = "?";
		 	}
			else {
				$potentiel = ($this ->calculerTarifVenteMoyen() * $this ->calculerQuantiteRestante());
			}
			return $potentiel;
		 }
		
		/**
		 * Renvoie le montant restant à dépenser pour rembourser intégralement le handicap
		 */
		public function calculerHandicapRestant() {
			return ($this ->_handicap - $this ->_handicapRembourse);
		}
		
		/**
		 * Renvoie la valeur ajoutée d'un cycle (argent récupéré - argent dépensé à l'achat)
		 * SANS COMPTER LES CHROMES
		 */
		public function calculerValeurAjouteeNette() {
			return ($this ->calculerArgentRecupere() - $this ->calculerPrix());
		}
		
		/**
		 * Renvoie la valeur ajoutée d'un cycle (argent des ventes - argent dépensé à l'achat)
		 * EN COMPTANT LES CHROMES
		 */
		public function calculerValeurAjouteeBrute() {
			return ($this ->calculerArgentTotalVentes() - $this ->calculerPrix());
		}
		
		/**
		 * Renvoie la valeur ajoutée potentielle d'un cycle (argent des ventes - argent dépensé à l'achat)
		 * EN COMPTANT LES CHROMES ET LE HANDICAP
		 */
		public function calculerValeurAjouteePotentielle() {
			if ($this ->calculerPotentielBrut() == "?") {
				return "?";
			}
			$VApotentielle = $this ->calculerValeurAjouteeBrute();
			$VApotentielle += $this ->calculerPotentielBrut();
			return $VApotentielle;
		}
		
		/**
		 * Renvoie la somme possédée actuellement par l'utilisateur
		 */
		public function calculerSommeEnMain() {
			return ($this ->calculerArgentRecupere() - $this ->getHandicapRembourse()); 
		}
		
		
		
		/**
		 * Retourne vrai si un cycle est actif (stock non nul OU au moins un differe courant OU handicap non remboursé), 
		 *  faux sinon
		 */
		public function estActif() {
			$stock = $this ->calculerQuantiteRestante();
			$stockNul = ($stock <= 0);
			
			$handicap = $this ->calculerHandicapRestant();
			$handicapNul = ($handicap <= 0);
			
			$differes = $this ->compterDifferesEnCours();
			$aucunDiffere = ($differes <= 0);
			
			if ($stockNul && $handicapNul && $aucunDiffere) {
				return false;
			} else {
				return true;
			}
		}
		
		
		
		
		
		
		/**
		 * Méthode vendre($quantiteVente, $tarifVente, $client, $methodePayement, $date);
		 * Cette méthode : 
		 * 		- Déduit la quantité vendue du Cycle.
		 * 		- Crée un objet Vente associé au Cycle en question
		 * 		- Crée si besoin un objet Differe associé au cycle en question
		 * 		- Ajoute les objets Vente et Differe aux attributs _ventesTab et _differesTab
		 * 		- Renvoie le montant récupéré
		 * 
		 * @param $donnneesVente est de type array.
		 * Sa structure : 
		 * 		$donneesVente[0] = quantite vendue au format numerique
		 * 		$donneesVente[1] = tarif de vente au format numerique
		 * 		$donneesVente[2] = client (identifiant au format numérique)
		 * 		$donneesVente[3] = date de vente au format String
		 * 		$donneesVente[4] = methode de payement ("cash", "accompte", ou "differe")
		 * 		$donneesVente[5] = (optionnel) quantité dejà payée (si methode = "accompte")
		 * => array($quantiteVente, $tarifVente, $client, $date, $methodePayement [, $montantPaye])
		 */
		public function vendre($donneesVente) {
			$quantiteVente		= $donneesVente[0];
			$tarifVente			= $donneesVente[1];
			$client				= $donneesVente[2];
			$date				= $donneesVente[3];
			$methodePayement	= $donneesVente[4];
			if (count($donneesVente) == 6) { //Si la fonction est appelée avec 6 arguments, soit une methode accompte
				$montantPaye	= $donneesVente[5];
			}
			
			if ($quantiteVente > $this ->calculerQuantiteRestante()) {
				trigger_error("Quantité restante insuffisante pour effectuer la vente", E_USER_WARNING);
				return;
			}
			
			
			$montantRecupere;
			
			//Création d'une nouvelle Vente et d'un nouveau Differe si nécessaire, puis ajouts dans les attributs _ventesTab et _differesTab
			$vente = $this ->genereVente($client, $quantiteVente, $tarifVente, $methodePayement, $date);
			$montant = $vente ->calculerPrix();
			//Création d'un nouveau Differe si nécessaire
			if ($methodePayement == "Differe") {
				$differe = $this ->genereDiffere($client, $date, $montant);
				array_push($this ->_differesTab, $differe);
				$montantRecupere = 0;
			}
			elseif ($methodePayement == "Accompte") {
				$montantRestant = $montant - $montantPaye;
				$differe = $this ->genereDiffere($client, $date, $montantRestant);
				array_push($this ->_differesTab, $differe);
				$montantRecupere = $montantPaye;
			}
			elseif ($methodePayement == "Cash") { //Actualisation de la qte d'argent récupéré
				$montantRecupere = $montant;
			}
			else {
				echo "Methode de payement invalide. <br />";
				return;
			}
			array_push($this ->_ventesTab, $vente);
			
			//Renvoie le montant d'argent récupéré en cash à l'issue de la vente
			return $montantRecupere;
		}
		
		
		/**
		 * Methode genereVente();
		 * 	Cette méthode génère une Vente à partir des données de la methode 'vendre' et d'autres données
		 * 	à calculer (nom du cycle, ID de la vente)
		 */
		public function genereVente($client, $quantiteVente, $tarifVente, $methodePayement, $date) {
			$ID;
			if (count($this ->_ventesTab) > 0) {
				$derniereVente = $this ->_ventesTab[-1];
				$ID = $derniereVente ->getIdentifiant();
			}
			else {
				$ID = 1;
			}
			$cycle = $this ->_identifiant;
			$compte = $this ->_compte;
			$vente = new Vente($ID, $compte, $cycle, $client, $quantiteVente, $tarifVente, $methodePayement, $date);
			return $vente;
		}
		/**
		 * Methode genereDiffere();
		 * 	Cette méthode génère un Differe à partir des données de la methode 'vendre' et d'autres données
		 * 	à calculer (nom du cycle, ID du Differe)
		 */
		public function genereDiffere($client, $date, $montant) {
			$ID;
			if (count($this ->_differesTab) > 0) {
				$dernierDiffere = $this ->_differesTab[-1];
				$ID = $derniereDiffere ->getIdentifiant();
			}
			else {
				$ID = 1;
			}
			$cycle = $this ->_identifiant;
			$compte = $this ->_compte;
			$differe = new Differe($ID, $compte, $cycle, $date, $client, $montant, 0, 0);
			return $differe;
		}
		
		
		
		
		
		
		/**
		 * Fonction qui gère un retrait de marchandise sur le cycle
		 */
		public function retirerMarchandise($quantite) {
			if (is_nan($quantite) || $quantite < 0) {
				trigger_error("Impossible de retirer un nombre négatif de marchandise.", E_USER_WARNING);
				return;
			}
			$this ->setMarchandiseRetiree($this ->_marchandiseRetiree + $quantite);
			return true;
		}
		
		 
		/**
		 * Fonction qui gère un remboursement de handicap sur le cycle
		 */
		public function rembourserHandicap($montant) {
			if (is_nan($montant) || $montant < 0) {
				trigger_error("Impossible de rembourser plus que la somme due.", E_USER_WARNING);
				return;
			}
			$this ->setHandicapRembourse($this ->_handicapRembourse + $montant);
			return true;
		}
		
		
		
		
		/**
		 * Méthode appelée lors de l'enregistrement d'un remboursement de differe.
		 * 	Son role va etre d'appeler la fonction rembourser() de l'objet Cycle concerné
		 * 
		 * @param $differe est l'identifiant du differe à rembourser
		 * @param $montant est le montant du remboursement
		 * @param $dateRemboursement est la date du remboursement (format String)
		 */
		public function percevoirRemboursement($differe, $montant, $dateRemboursement) {			
			//Récupération de l'objet Differe à traiter depuis l'identifiant fourni
			$differeATraiter = false;
			foreach ($this ->_differesTab as $item) {
				if ($item ->getIdentifiant() == $differe) {
					$differeATraiter = $item;
					break;
				}
			}
			if(!$differeATraiter) {
				trigger_error("Le differe sélectionné pour le remboursement à effectuer n'existe pas.", E_USER_ERROR);
				return;
			}
			$ok = $differeATraiter ->rembourser($montant, $dateRemboursement);
			return $ok;
		}
		
		
		
		
		
		
		/*
		 * Méthodes pour initialiser les attributs _ventesTab, _differesTab, _handicapRembourse et _argentRecupere lors de la création
		 * 	de l'objet Cycle
		 */
		
		public function initialiseVentesTab() {
			$this ->_ventesTab = array();
		}
		public function initialiseDifferesTab() {
			$this ->_differesTab = array();
		}
		public function initialiseMarchandiseRetiree() {
			$this ->_marchandiseRetiree = 0;
		}
		public function initialiseHandicapRembourse() {
			$this ->_handicapRembourse = 0;
		}
		
		
		
		/**
		 * Methode renvoyant un tableau de tous les attributs du Cycle nécessaires dans la BDD
		 */
		public function toArray() {
			$attributs = array();
			array_push($attributs, $this ->_identifiant);
			array_push($attributs, $this ->_compte);
			array_push($attributs, $this ->_dateAchat);
			array_push($attributs, $this ->_quantiteAchat);
			array_push($attributs, $this ->_tarif);
			array_push($attributs, $this ->_handicap);
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
		
		public function setCompte($compte) {
			//Ajouter tests sur le format de l'ID du Compte
			$this ->_compte = $compte;
		}
		public function getCompte() {
			return $this ->_compte;
		}
		
		public function setQuantiteAchat($quantiteAchat) {
			if (is_nan($quantiteAchat) || $quantiteAchat < 0) {
				trigger_error("La quantité achetée doit être un nombre positif.", E_USER_WARNING);
				return;
			}
			$this ->_quantiteAchat = $quantiteAchat;
		}
		public function getQuantiteAchat() {
			return $this ->_quantiteAchat;
		}
		
		public function setDateAchat($dateAchat) {
			//Ajouter tests sur la validité de la date
			$this ->_dateAchat = $dateAchat;
		}
		public function getDateAchat() {
			return $this ->_dateAchat;
		}
		
		public function setTarif($tarif) {
			if (is_nan($tarif) || $tarif < 0) {
				trigger_error("La quantite vendue doit être un nombre décimal positif.", E_USER_WARNING);
				return;
			}
			$this ->_tarif = $tarif;
		}
		public function getTarif() {
			return $this ->_tarif;
		}
		
		public function setHandicap($handicap) {
			if (is_nan($handicap) || $handicap < 0) {
				trigger_error("Lhandicap doit être un nombre entier positif.", E_USER_WARNING);
				return;
			}
			$this ->_handicap = $handicap;
		}
		public function getHandicap() {
			return $this ->_handicap;
		}
		
		public function setCommentaire($commentaire) {
			//Ajouter tests sur le format du texte
			$this ->_commentaire = $commentaire;
		}
		public function getCommentaire() {
			return $this ->_commentaire;
		}
		
		public function setVentesTab($ventesTab) {
			if (!is_array($ventesTab)) {
				trigger_error("Type incompatibel : un tableau est attendu pour l'attribut \$_ventesTab.");
				return;
			}
			$this ->_ventesTab = $ventesTab;
		}
		public function getVentesTab() {
			return $this ->_ventesTab;
		}
		
		public function setDifferesTab($differesTab) {
			if (!is_array($differesTab)) {
				trigger_error("Type incompatibel : un tableau est attendu pour l'attribut \$_differesTab.");
				return;
			}
			$this ->_differesTab = $differesTab;
		}
		public function getDifferesTab() {
			return $this ->_differesTab;
		}
		
		public function setMarchandiseRetiree($marchandiseRetiree) {
			if (is_nan($marchandiseRetiree) || $marchandiseRetiree < 0) {
				trigger_error("La marchandise retiree doit être un nombre positif.", E_USER_WARNING);
				return;
			}
			$this ->_marchandiseRetiree = $marchandiseRetiree;
		}
		public function getMarchandiseRetiree() {
			return $this ->_marchandiseRetiree;
		}
		
		public function setHandicapRembourse($handicapRembourse) {
			if (is_nan($handicapRembourse) || $handicapRembourse < 0) {
				trigger_error("Le handicap rembourse doit être un nombre positif.", E_USER_WARNING);
				return;
			}
			$this ->_handicapRembourse = $handicapRembourse;
		}
		public function getHandicapRembourse() {
			return $this ->_handicapRembourse;
		}
		
		
		
		public function toString() {
			$sortie = "CYCLE <br />";
			$sortie.= "ID : ".($this ->_identifiant)."<br />";
			$sortie.= "Compte : ".($this ->_compte)."<br />";
			$sortie.= "Quantité achat : ".($this ->_quantiteAchat)."<br />";
			$sortie.= "Date achat : ".($this ->_dateAchat)."<br />";
			$sortie.= "Tarif : ".($this ->_tarif)."<br />";
			$sortie.= "Handicap : ".($this ->_handicap)."<br />";
			$sortie.= "Quantité restante : ".($this ->calculerQuantiteRestante())."<br />"; //N'est pas un attribut de la classe
			$sortie.= "Argent récupéré : ".($this ->calculerArgentRecupere())."<br />"; //N'est pas un attribut de la classe
			$sortie.= "Potentiel : ".($this ->calculerPotentielNet())."<br />"; //N'est pas un attribut de la classe
			$sortie.= "Commentaire : ".($this ->_commentaire)."<br />";
			$sortie.= (count($this ->_ventesTab))." ventes réalisées <br />";
			$sortie.= (count($this ->_differesTab))." differes sur ce cycle <br />"; 
			$sortie.= "Marchandise retirée : ".($this ->_marchandiseRetiree)."<br />";
			return $sortie;
		}
		
	}
	
	
	
?>