<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/GeoApp/CSS/ajoutInfrastructure.css">
    <title>Ajouter Infrastructure</title>
</head>
<body>
    <header>
        <?php include './header.php'; ?>
    </header>
    <div class="form-container">
        <form action="../PHP/ajouterInfrastrucutre.php" method="post">
            <label for="nom">Nom de l'infrastructure : </label>
            <input type="text" id="nom" name="nom" required>

            <label for="type">Type d'infrastructure : </label>
            <input type="text" id="type" name="type" required>

            <label for="adresse">Adresse : </label>
            <input type="text" id="adresse" name="adresse" required>

            <label for = "ville">Ville : </label>
            <input type="text" id="ville" name="ville" required>

            <div class="coordinates-row">
                <div class="coord-group">
                    <label for="coordonneeX">Longitude : </label>
                    <input type="number" id="coordonneeX" name="coordonneeX" step = "any" required>
                </div>
                <div class="coord-group">
                    <label for="coordonneeY">Latitude : </label>
                    <input type="number" id="coordonneeY" name="coordonneeY" step = "any"required>
                </div>
            </div>

            <div class="options">
                <div class="options-section">
                    <p class="options-title">Accessibilité handicap :</p>
                    <div class="options-group">
                        <label for="moteur">
                            <input type="checkbox" id="moteur" name="moteur">
                            Moteur
                        </label>
                        <label for="sensoriel">
                            <input type="checkbox" id="sensoriel" name="sensoriel">
                            Sensoriel
                        </label>
                    </div>
                </div>
                
                <div class="options-section">
                    <p class="options-title">Équipements :</p>
                    <div class="options-group">
                        <label for="sanitaires">
                            <input type="checkbox" id="sanitaires" name="sanitaires">
                            Sanitaires
                        </label>
                        <label for="douches">
                            <input type="checkbox" id="douches" name="douches">
                            Douches
                        </label>
                    </div>
                </div>
            </div>

           <input type="submit" value="Ajouter Infrastructure">
        </form>
    </div>
</body>
</html>