<?php
    session_start();
?>
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
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']); 
            ?>
        </div>
    <?php endif; ?>
    <div class="form-container">
        <form action="../PHP/connexion.php" method="post">
            <label for="siret">Siret : </label>
            <input type="integer" id="siret" name="siret" required><br><br>

            <label for="password">Mot de passe : </label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Se connecter">
            <br>
            <a href="./inscription.php">Pas de compte ? Inscrivez-vous ici.</a>
        </form>
    </div>
</body>
</html>