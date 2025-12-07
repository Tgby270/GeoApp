<?php
require_once 'connexionBDD.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $type = htmlspecialchars($_POST['type']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $coordonneeX = htmlspecialchars($_POST['coordonneeX']);
    $coordonneeY = htmlspecialchars($_POST['coordonneeY']);
    $uid = 0;

    if(isset($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
    }

    if (empty($nom) || empty($type) || empty($adresse) || empty($coordonneeX) || empty($coordonneeY)) {
        die("Tous les champs sont requis.");
    }

    $check = $db->prepare("SELECT COUNT(*) from Infrastructure where nom = ? and adresse = ? and type = ? and coordonneeX = ? and coordonneeY = ?");
    $check->execute(array($nom, $adresse, $type, $coordonneeX, $coordonneeY));
    $data = $check->fetchColumn();
    if ($data > 0) {
        die("Cette infrastructure existe déjà.");
    } else {
        $rep = $bdd->prepare("INSERT INTO Infrastructure (nom, type, adresse, coordonneeX, coordonneeY, user_id) VALUES (?,?,?,?,?,?)");
        $rep->execute(array($nom, $type, $adresse, $coordonneeX, $coordonneeY, $uid)); // Remplacez 1 par l'ID de l'utilisateur connecté si nécessaire

        header("Location: index.html");
    }
}
?>