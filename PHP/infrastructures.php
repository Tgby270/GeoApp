<?php
    class Infrastructures{
        public String $nom;
        public String $type;
        public String $adresse;

        public String $ville;

        public float $coordonneeX;
        public float $coordonneeY;
        public int $inf_id;


        public function __construct(String $nom, String $type, String $adresse, String $ville, float $coordonneeX, float $coordonneeY, int $inf_id){
            $this->nom = $nom;
            $this->type = $type;
            $this->adresse = $adresse;
            $this->ville = $ville;
            $this->coordonneeX = $coordonneeX;
            $this->coordonneeY = $coordonneeY;
            $this->inf_id = $inf_id;
        }

        public function getNom(){
            return $this->nom;
        }

        public function getVille(){
            return $this->ville;
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

        public function getInfId(){
            return $this->inf_id;
        }
        
    }
?>