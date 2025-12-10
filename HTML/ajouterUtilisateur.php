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
        <form action="../PHP/ajouterUtilisateur.php" method="post">
            <label for="nom">Numéro de Siret : </label>
            <input type="text" id="nom" name="nom" required><br><br>

            <label for="type">Adresse Email : </label>
            <input type="text" id="type" name="type" required><br><br>

            <label for="adresse">Mot de Passe :</label>
            <input type="text" id="adresse" name="adresse" required><br><br>

           <input type="submit" value="Ajouter Utilisateur">
        </form>
        <?php include '../PHP/ajouterUtilisateur.php'; ?>
    </div>
</body>
</html>