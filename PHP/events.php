<?php
    class Events {
        private $inf_id;
        private $libelle;
        private $date_debut;
        private $date_fin;
        private $heure_debut;
        private $heure_fin;
        private $uid;
        
        public function __construct($id, $libelle, $date_debut, $date_fin, $heure_debut, $heure_fin, $uid) {
            $this->inf_id = $id;
            $this->libelle = $libelle;
            $this->date_debut = $date_debut;
            $this->date_fin = $date_fin;
            $this->heure_debut = $heure_debut;
            $this->heure_fin = $heure_fin;
            $this->uid = $uid;
        }

        public function getId() {
            return $this->inf_id;
        }

        public function getLibelle() {
            return $this->libelle;
        }

        public function getDateDebut() {
            return $this->date_debut;
        }

        public function getDateFin() {
            return $this->date_fin;
        }

        public function getHeureDebut() {
            return $this->heure_debut;
        }

        public function getHeureFin() {
            return $this->heure_fin;
        }

        public function getUid() {
            return $this->uid;
        }

        public function getDuration() {
            // Format times properly (handle both "8" and "08:00" formats)
            $heure_debut = is_numeric($this->heure_debut) ? sprintf("%02d:00", $this->heure_debut) : $this->heure_debut;
            $heure_fin = is_numeric($this->heure_fin) ? sprintf("%02d:00", $this->heure_fin) : $this->heure_fin;
            
            $start = strtotime($this->date_debut . ' ' . $heure_debut);
            $end = strtotime($this->date_fin . ' ' . $heure_fin);
            return ($end - $start) / 60; // duration in minutes
        }
    }
?>