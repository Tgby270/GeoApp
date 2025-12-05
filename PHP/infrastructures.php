<?php
    class Infrastructures{
        public String $nom;
        public String $type;
        public String $adresse;
        public float $coordonneeX;
        public float $coordonneeY;


        public function __construct(String $nom, String $type, String $adresse, float $coordonneeX, float $coordonneeY){
            $this->nom = $nom;
            $this->type = $type;
            $this->adresse = $adresse;
            $this->coordonneeX = $coordonneeX;
            $this->coordonneeY = $coordonneeY;
        }

        public function getNom(){
            return $this->nom;
        }

        public function getType(){
            return $this->type;
        }

        public function getAdresse(){
            return $this->adresse;
        }

        public function getCoordonneeX(){
            return $this->coordonneeX;
        }

        public function getCoordonneeY(){
            return $this->coordonneeY;
        }   
    }
?>