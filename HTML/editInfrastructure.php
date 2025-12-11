<?php
session_start();
require_once '../PHP/connexionBDD.php';

// Check if user is admin
$isAdmin = isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == true;
$dashboardUrl = $isAdmin ? 'adminDashboard.php' : 'userDashboard.php';

// Get infrastructure ID
$inf_id = isset($_GET['inf_id']) ? intval($_GET['inf_id']) : 0;

if ($inf_id <= 0) {
    header('Location: ' . $dashboardUrl);
    exit;
}

// Fetch infrastructure data
$stmt = $bdd->prepare('SELECT * FROM Infrastructure WHERE id = ?');
$stmt->execute([$inf_id]);
$infrastructure = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$infrastructure) {
    header('Location: ' . $dashboardUrl);
    exit;
}

// Fetch existing options
$optionsStmt = $bdd->prepare('
    SELECT Option_infrastructure.option_nom 
    FROM infOption 
    INNER JOIN Option_infrastructure ON infOption.opt_code = Option_infrastructure.code 
    WHERE infOption.inf_id = ?
');
$optionsStmt->execute([$inf_id]);
$existingOptions = [];
while ($row = $optionsStmt->fetch(PDO::FETCH_ASSOC)) {
    $existingOptions[] = $row['option_nom'];
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
    
    $moteur = isset($_POST['moteur']) ? $_POST['moteur'] : null;
    $sensoriel = isset($_POST['sensoriel']) ? $_POST['sensoriel'] : null;
    $douche = isset($_POST['douches']) ? $_POST['douches'] : null;
    $sanitaires = isset($_POST['sanitaires']) ? $_POST['sanitaires'] : null;

    if (empty($nom) || empty($type) || empty($adresse) || empty($ville) || empty($coordonneeX) || empty($coordonneeY)) {
        $error = "Tous les champs sont requis.";
    } else {
        // Update infrastructure basic info
        $stmt = $bdd->prepare("UPDATE Infrastructure SET nom = ?, type = ?, adresse = ?, ville = ?, coordonneeX = ?, coordonneeY = ? WHERE id = ?");
        $stmt->execute([$nom, $type, $adresse, $ville, $coordonneeX, $coordonneeY, $inf_id]);
        
        // Delete existing options
        $deleteStmt = $bdd->prepare("DELETE FROM infOption WHERE inf_id = ?");
        $deleteStmt->execute([$inf_id]);
        
        // Insert new options
        $options = array($moteur ? 1 : 0, $sensoriel ? 1 : 0, $sanitaires ? 1 : 0, $douche ? 1 : 0);
        $optionNames = array('mobilite', 'sensoriel', 'sanitaires', 'douche');
        
        for ($i = 0; $i < count($options); $i++) {
            if ($options[$i] != null && $options[$i] != 0) {
                $optStmt = $bdd->prepare("INSERT INTO infOption (inf_id, opt_code) VALUES (?, ?)");
                $optcode = $bdd->prepare("SELECT code FROM Option_infrastructure WHERE option_nom = ?");
                $optcode->execute(array($optionNames[$i]));
                $codeRow = $optcode->fetch(PDO::FETCH_ASSOC);
                if ($codeRow) {
                    $optStmt->execute(array($inf_id, $codeRow['code']));
                }
            }
        }
        
        header('Location: ' . $dashboardUrl);
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

            <div class="options">
                <div class="options-section">
                    <p class="options-title">Accessibilité handicap :</p>
                    <div class="options-group">
                        <label for="moteur">
                            <input type="checkbox" id="moteur" name="moteur" <?php echo in_array('mobilite', $existingOptions) ? 'checked' : ''; ?>>
                            Moteur
                        </label>
                        <label for="sensoriel">
                            <input type="checkbox" id="sensoriel" name="sensoriel" <?php echo in_array('sensoriel', $existingOptions) ? 'checked' : ''; ?>>
                            Sensoriel
                        </label>
                    </div>
                </div>
                
                <div class="options-section">
                    <p class="options-title">Équipements :</p>
                    <div class="options-group">
                        <label for="sanitaires">
                            <input type="checkbox" id="sanitaires" name="sanitaires" <?php echo in_array('sanitaires', $existingOptions) ? 'checked' : ''; ?>>
                            Sanitaires
                        </label>
                        <label for="douches">
                            <input type="checkbox" id="douches" name="douches" <?php echo in_array('douche', $existingOptions) ? 'checked' : ''; ?>>
                            Douches
                        </label>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-submit">Enregistrer les modifications</button>
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <button type="button" onclick="window.location.href='adminDashboard.php'" class="btn-cancel">Annuler</button>
                <?php else: ?>
                <button type="button" onclick="window.location.href='userDashboard.php'" class="btn-cancel">Annuler</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>
</html>
