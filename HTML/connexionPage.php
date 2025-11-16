<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/GeoApp/CSS/inscription.css">
    <title>Connexion</title>
</head>
<body>
    <header>
        <?php include './header.html'; ?>
    </header>
    <div class="form-container">
        <form action="/GEOAPP/PHP/connexion.php" method="post">
            <label for="siret">Siret : </label>
            <input type="integer" id="siret" name="siret" required><br><br>

            <label for="password">Mot de passe : </label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Se connecter">
            <br>
            <a href="/GeoApp/HTML/inscription.php">Pas de compte ? Inscrivez-vous ici.</a>
        </form>
    </div>
</body>
</html>