<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/GeoApp/CSS/inscription.css">
    <title>Inscription</title>
</head>
<body>
    <header>
        <?php include './header.php'; ?>
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
        <form action="../PHP/inscription.php" method="post">
            <label for="siret">Numero de Siret : </label>
            <input type="integer" id="siret" name="siret" required><br><br>

            <label for="email">Email : </label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required><br><br>

            <label for="password2">Vérification mot de passe :</label>
            <input type="password" id="password2" name="password2" required><br><br>

            <input type="submit" value="S'inscrire">
            <br>
            <a href="./connexionPage.php">Déjà un compte ? Connectez-vous ici.</a>
        </form>
    </div>
</body>
</html>