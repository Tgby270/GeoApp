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
            <input type="text" id="nom" name="nom" required><br><br>

            <label for="type">Type d'infrastructure : </label>
            <input type="text" id="type" name="type" required><br><br>

            <label for="adresse">Adresse : </label>
            <input type="text" id="adresse" name="adresse" required><br><br>

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

           <input type="submit" value="Ajouter Infrastructure">
        </form>
        <?php include '../PHP/ajouterInfrastrucutre.php'; ?>
    </div>
</body>
</html>