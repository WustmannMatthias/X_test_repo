<?php
	
	
	/**
	 * Classe Differe
	 */
	class Differe {
		
		private $_identifiant;
		private $_compte;
		private $_cycle;
		private $_dateEmprunt;
		private $_client;
		private $_montant;
		private $_montantRembourse;
		private $_dateFinalisation;
		
		
		function __construct($identifiant, $compte, $cycle, $dateEmprunt, $client, $montant, $montantRembourse, $dateFinalisation) {
			$this ->setIdentifiant($identifiant);
			$this ->setCycle($cycle);
			$this ->setDateEmprunt($dateEmprunt);
			$this ->setClient($client);
			$this ->setMontant($montant);
			$this ->setMontantRembourse($montantRembourse);
			$this ->setDateFinalisation($dateFinalisation);
			
			
			//echo "Objet Differe créé avec succès <br />";
		}
		
		
		/**
		 * Rembourse un certain montant d'un differe
		 */
		public function rembourser($montant, $dateRemboursement) {
			if($montant > $this ->calculerMontantRestantDu()) {
				trigger_error("Le montant du remboursement est supérieur au montant du par le client", E_USER_ERROR);
				return;
			}
			$this ->setMontantRembourse($this ->_montantRembourse + $montant);
			
			if ($this ->estRembourse()) {
				$this ->setDateFinalisation($dateRemboursement);
			}
			return true;
		}
		
		
		/**
		 * Renvoie true si le differe est totalement remboursé par le client, false sinon
		 */
		public function estRembourse() {
			return ($this ->_montant == $this ->_montantRembourse);
		}
		
		
		
		
		/**
		 * Calcule le montant restant d'un differe
		 */
		 public function calculerMontantRestantDu() {
		 	return ($this ->_montant - $this ->_montantRembourse);
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
		
		public function setDateEmprunt($dateEmprunt) {
			//Ajouter tests sur la validité de la date
			$this ->_dateEmprunt = $dateEmprunt;
		}
		public function getDateEmprunt() {
			return $this ->_dateEmprunt;
		}
		
		public function setClient($client) {
			//Ajouter tests sur l'existence du client
			$this ->_client = $client;
		}
		public function getClient() {
			return $this ->_client;
		}
		
		public function setMontant($montant) {
			if (is_nan($montant) || $montant < 0) {
				trigger_error("Le montant du differe doit être un nombre positif.", E_USER_WARNING);
				return;
			}
			$this ->_montant = $montant;
		}
		public function getMontant() {
			return $this ->_montant;
		}
		
		public function setMontantRembourse($montantRembourse) {
			//Prévoir des teste sur le montant rembourse
			$this ->_montantRembourse = $montantRembourse;
		}
		public function getMontantRembourse() {
			return $this ->_montantRembourse;
		}
		
		public function setDateFinalisation($dateFinalisation) {
			//Ajouter des tests sur le format de la date
			$this ->_dateFinalisation = $dateFinalisation;
		}
		public function getDateFinalisation() {
			return $this ->_dateFinalisation;
		}
		
		
		
		public function toString() {
			$sortie = "CHROME <br />";
			$sortie.= "ID : ".($this ->_identifiant)."<br />";
			$sortie.= "Compte : ".($this ->_compte)."<br />";
			$sortie.= "Cycle : ".($this ->_cycle)."<br />";
			$sortie.= "Date emprunt : ".($this ->_dateEmprunt)."<br />";
			$sortie.= "Client : ".($this ->_client)."<br />";
			$sortie.= "Montant : ".($this ->_montant)."<br />";
			
			return $sortie;
		}
	}
	
?>