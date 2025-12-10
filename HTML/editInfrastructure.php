<?php
session_start();
require_once '../PHP/connexionBDD.php';

// Get infrastructure ID
$inf_id = isset($_GET['inf_id']) ? intval($_GET['inf_id']) : 0;

if ($inf_id <= 0) {
    header('Location: adminDashboard.php');
    exit;
}

// Fetch infrastructure data
$stmt = $bdd->prepare('SELECT * FROM Infrastructure WHERE id = ?');
$stmt->execute([$inf_id]);
$infrastructure = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$infrastructure) {
    header('Location: adminDashboard.php');
    exit;
}

// Process form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $type = htmlspecialchars($_POST['type']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $ville = htmlspecialchars($_POST['ville']);
    $coordonneeX = htmlspecialchars($_POST['coordonneeX']);
    $coordonneeY = htmlspecialchars($_POST['coordonneeY']);

    if (empty($nom) || empty($type) || empty($adresse) || empty($ville) || empty($coordonneeX) || empty($coordonneeY)) {
        $error = "Tous les champs sont requis.";
    } else {
        $stmt = $bdd->prepare("UPDATE Infrastructure SET nom = ?, type = ?, adresse = ?, ville = ?, coordonneeX = ?, coordonneeY = ? WHERE id = ?");
        $stmt->execute([$nom, $type, $adresse, $ville, $coordonneeX, $coordonneeY, $inf_id]);
        
        header('Location: adminDashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/GeoApp/CSS/editInfrastructure.css">
    <title>Éditer Infrastructure</title>
</head>
<body>
    <header>
        <?php include './header.php'; ?>
    </header>
    <div class="form-container">
        <form action="" method="post">
            <?php if(!empty($error)): ?>
                <div style="color: #d32f2f; padding: 12px; margin-bottom: 15px; background: #ffe6e6; border-radius: 8px; border: 1px solid #d32f2f;">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <h2>Éditer l'infrastructure</h2>
            <label for="nom">Nom de l'infrastructure : </label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($infrastructure['nom']); ?>" required><br><br>

            <label for="type">Type d'infrastructure : </label>
            <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($infrastructure['type']); ?>" required><br><br>

            <label for="adresse">Adresse : </label>
            <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($infrastructure['adresse']); ?>" required><br><br>

            <label for="ville">Ville : </label>
            <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($infrastructure['ville']); ?>" required><br><br>

            <div class="coordinates-row">
                <div class="coord-group">
                    <label for="coordonneeX">Longitude : </label>
                    <input type="number" id="coordonneeX" name="coordonneeX" step="any" value="<?php echo htmlspecialchars($infrastructure['coordonneeX']); ?>" required>
                </div>
                <div class="coord-group">
                    <label for="coordonneeY">Latitude : </label>
                    <input type="number" id="coordonneeY" name="coordonneeY" step="any" value="<?php echo htmlspecialchars($infrastructure['coordonneeY']); ?>" required>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-submit">Enregistrer les modifications</button>
                <button type="button" onclick="window.location.href='adminDashboard.php'" class="btn-cancel">Annuler</button>
            </div>
        </form>
    </div>
</body>
</html>
