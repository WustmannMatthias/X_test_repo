<?php
	
	/**
	 * Classe Vente
	 */
	class Vente {
		
		private $_identifiant;
		private $_compte;
		private $_cycle;
		private $_client;
		private $_quantite;
		private $_tarif;
		private $_payement;
		private $_date;
		
		
		
		/**
		 * Constructeur de l'objet Vente
		 */
		public function __construct($identifiant, $compte, $cycle, $client, $quantite, $tarif, $payement, $date) {
			$this ->setIdentifiant($identifiant);
			$this ->setCompte($compte);
			$this ->setCycle($cycle);
			$this ->setClient($client);
			$this ->setQuantite($quantite);
			$this ->setTarif($tarif);
			$this ->setPayement($payement);
			$this ->setDate($date);
			
			//echo "Objet Vente créé avec succès. <br />";
		}
		
		
		
		public function calculerPrix() {
			return $this ->_quantite * $this ->_tarif;
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
		
		public function setCycle($cycle) {
			//Ajouter tests sur l'existence du cycle
			$this ->_cycle = $cycle;
		}
		public function getCycle() {
			return $this ->_cycle;
		}
		
		public function setClient($client) {
			//Ajouter tests sur l'existence du client
			$this ->_client = $client;
		}
		public function getClient() {
			return $this ->_client;
		}
		
		public function setQuantite($quantite) { //quantite exprimée en g
			if (is_nan($quantite) || $quantite < 0) {
				trigger_error("La quantite vendue doit être un nombre positif.", E_USER_WARNING);
				return;
			}
			$this ->_quantite = $quantite;
		}
		public function getQuantite() {
			return $this ->_quantite;
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
		
		public function setPayement($payement) {
			$methodesPayement = array('Cash', 'Differe', 'Accompte');
			if (!in_array($payement, $methodesPayement)) {
				trigger_error("La méthode de payement entrée est invalide", E_USER_WARNING);
				return;
			}
			$this ->_payement = $payement;
		}
		public function getPayement() {
			return $this ->_payement;
		}
		
		public function setDate($date) {
			//Ajouter tests sur la validité de la date
			$this ->_date = $date;
		}
		public function getDate() {
			return $this ->_date;
		}
		
		
		public function toString() {
			$sortie = "VENTE <br />";
			$sortie.= "ID : ".($this ->_identifiant)."<br />";
			$sortie.= "Compte : ".($this ->_compte)."<br />";
			$sortie.= "Cycle : ".($this ->_cycle)."<br />";
			$sortie.= "Client : ".($this ->_client)."<br />";
			$sortie.= "Quantité : ".($this ->_quantite)."<br />";
			$sortie.= "Tarif : ".($this ->_tarif)."<br />";
			$sortie.= "Payement : ".($this ->_payement)."<br />";
			$sortie.= "Date : ".($this ->_date)."<br />";
			return $sortie;
		}
		
	}
	
?>