<?php
require_once 'connexionBDD.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $type = htmlspecialchars($_POST['type']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $coordonneeX = htmlspecialchars($_POST['coordonneeX']);
    $coordonneeY = htmlspecialchars($_POST['coordonneeY']);
    $ville = htmlspecialchars($_POST['ville']);
    if(isset($_POST['moteur'])) $moteur = $_POST['moteur'];
    else $moteur = null;
    if(isset($_POST['sensoriel'])) $sensoriel = $_POST['sensoriel'];
    else $sensoriel = null;
    if(isset($_POST['douches'])) $douche = $_POST['douches'];
    else $douche = null;
    if(isset($_POST['sanitaires'])) $sanitaires = $_POST['sanitaires'];
    else $sanitaires = null;
    $uid = 0;

    if (isset($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
    }

    if (empty($nom) || empty($type) || empty($adresse) || empty($ville) || empty($coordonneeX) || empty($coordonneeY)) {
        die("Tous les champs sont requis.");
    }

    $check = $bdd->prepare("SELECT COUNT(*) from Infrastructure where nom = ? and adresse = ? and ville = ? and type = ? and coordonneeX = ? and coordonneeY = ?");
    $check->execute(array($nom, $adresse, $ville, $type, $coordonneeX, $coordonneeY));
    $data = $check->fetchColumn();
    if ($data > 0) {
        die("Cette infrastructure existe déjà.");
    } else {
        $rep = $bdd->prepare("INSERT INTO Infrastructure (nom, type, adresse, ville, coordonneeX, coordonneeY, user_id) VALUES (?,?,?,?,?,?,?)");
        $rep->execute(array($nom, $type, $adresse, $ville, $coordonneeX, $coordonneeY, $uid)); // Remplacez 1 par l'ID de l'utilisateur connecté si nécessaire

        $options = array($moteur ? 1 : 0, $sensoriel ? 1 : 0, $sanitaires ? 1 : 0, $douche ? 1 : 0);
        $optionNames = array('mobilite', 'sensoriel', 'sanitaires', 'douche');

        $infId = $bdd->lastInsertId();

        for ($i = 0; $i < count($options); $i++) {
            if ($options[$i] != null) {
                $optStmt = $bdd->prepare("INSERT INTO infOption (inf_id, opt_code) VALUES (?, ?)");
                $optcode = $bdd->prepare("SELECT code FROM Option_infrastructure WHERE option_nom = ?");
                $optcode->execute(array($optionNames[$i]));
                $codeRow = $optcode->fetch(PDO::FETCH_ASSOC);
                $optStmt->execute(array($infId, $codeRow['code']));
            }
        }

        header("Location: ../index.php");
        exit();
    }
}
?>