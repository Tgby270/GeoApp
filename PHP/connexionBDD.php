<?php

    try{
        $bdd=new PDO  ('mysql:host=mysql-etu.unicaen.fr;dbname=geoapp_bd;charset=utf8', '', '');
    }
    catch(PDOException $e){
        echo "Erreur de connexion : " . $e->getMessage();
    }