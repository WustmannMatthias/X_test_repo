<?php
	
	/**
	 * Classe Client
	 */
	class Client {
		
		private $_identifiant;
		private $_nom;
		private $_dette;
		
		public function __construct($identifiant, $nom, $dette) {
			$this ->setIdentifiant($identifiant);
			$this ->setNom($nom);
			$this ->setDette($dette);
			echo "Objet Client créé avec succès. <br />";
		}
		
		
		
		/*
		 * Accesseurs / Mutateurs
		 */
		
		public function setIdentifiant($identifiant) {
			//Ajouter tests sur le format de l'ID
			$this ->_identifiant = $identifiant;
		}
		public function getIdentifiant() {
			return $this ->_identifiant;
		}
		
		public function setNom($nom) {
			//Ajouter des tests sur le format du nom
			$this ->_nom = $nom;
		}
		public function getNom() {
			return $this ->_nom;
		}
		
		public function setDette($montant) {
			if (!is_int($montant) || $montant < 0) {
				trigger_error("Le montant doit être un entier positif.", E_USER_WARNING);
				return;
			}
			$this ->_dette = $montant;
		}
		public function getDette() {
			return $this ->_nom;
		}
		
		public function toString() {
			$sortie = "CLIENT <br />";
			$sortie.= "ID : ".($this ->_identifiant)."<br />";
			$sortie.= "Nom : ".($this ->_nom)."<br />";
			$sortie.= "Dette : ".($this ->_dette)."<br />";
			return $sortie;
		}
	}
	
?>