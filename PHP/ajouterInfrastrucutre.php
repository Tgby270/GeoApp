<?php
require_once 'connexionBDD.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $type = htmlspecialchars($_POST['type']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $coordonnéeX = htmlspecialchars($_POST['coordonnéeX']);
    $coordonnéeY = htmlspecialchars($_POST['coordonnéeY']);


    if (empty($nom) || empty($type) || empty($adresse) || empty($coordonnéeX) || empty($coordonnéeY)) {
        die("Tous les champs sont requis.");
    }

    $check = $db->prepare("SELECT COUNT(*) from Infrastructure where nom = ? and adresse = ? and type = ? and coordonnéeX = ? and coordonnéeY = ?");
    $check->execute(array($nom, $adresse, $type, $coordonnéeX, $coordonnéeY));
    $data = $check->fetch();
    if ($data > 0) {
        die("Cette infrastructure existe déjà.");
    } else {
        $rep = $bdd->prepare("INSERT INTO Infrastructure (nom, type, adresse, coordonnéeX, coordonnéeY) VALUES (?,?,?,?,?)");
        $rep->execute(array($nom, $type, $adresse, $coordonnéeX, $coordonnéeY));

        header("Location: index.html");
    }
}
?>