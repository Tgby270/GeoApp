<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $isloggedin = !empty($_SESSION['user_id']);
    $isAdmin = !empty($_SESSION['is_admin']);
?>
<!-- Use root-relative paths so this fragment works from any folder (e.g. /map/) -->
<link rel="stylesheet" href="/GeoApp/CSS/header.css">
<nav id="nav-bar">
        <div class="nav-left">
            <a href="/GeoApp/index.php">
            <img src="/GeoApp/ressource/logo.png" alt="Website Logo" id="header-logo"/>
            </a>
        </div>
        <div class="nav-center">
            <a href="/GeoApp/PHP/map.php" id="header-map">Map</a>
            <a href="/GeoApp/index.php" id="header-home-link">Menu</a>
            <a href="/GeoApp/about.html" id="header-about">À Propos</a>
        </div>

        <?php if (!$isloggedin && !$isAdmin) : ?>

        <div class="nav-right">
            <a href="/GeoApp/HTML/connexionPage.php" id="header-log-in">Se Connecter</a>
            <a href="/GeoApp/HTML/inscription.php" id="header-sign-up">S'inscrire</a>
        </div>

        <?php elseif ($isloggedin && !$isAdmin) : ?>
            <div class="nav-right">
            <a href="/GeoApp/HTML/userDashboard.php" id="header-log-in">Vos Infrastructures</a>
            <a href="/GeoApp/HTML/deconnection.php" id="header-sign-up">Se Deconnecter</a>
        </div>

        <?php elseif ($isloggedin && $isAdmin) : ?>
            <div class="nav-right">
            <a href="/GeoApp/HTML/adminDashboard.php" id="header-log-in">Admin Dashboard</a>
            <a href="/GeoApp/HTML/deconnection.php" id="header-sign-up">Se Deconnecter</a>
        </div>
        <?php endif; ?>
</nav>