<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = htmlspecialchars($_POST['nom']);
        $type = htmlspecialchars($_POST['type']);
        $adresse = htmlspecialchars($_POST['adresse']);
        $coordonnéeX = htmlspecialchars($_POST['coordonnéeX']);
        $coordonnéeY = htmlspecialchars($_POST['coordonnéeY']);


        $rep = $bdd->prepare("Insert into infrastructure (nom, type, adresse, coordonnéeX, coordonnéeY) Values (?, ?, ?, ?, ?);");
        $rep->execute(array('nom' => $nom,
                            'type' => $type,
                            'adresse' => $adresse,
                            'coordonnéeX' => $coordonnéeX,
                            'coordonnéeY' => $coordonnéeY));
        
        header("Location: index.html");
    }
?>