<?php
    ob_start();
    session_start();
    require_once 'PHP/connexionBDD.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Load site-wide styles so elements like #page-title are styled -->
    <link rel="stylesheet" href="/GeoApp/CSS/style.css">
</head>

<body>
    <header>
        <?php if (isset($_SESSION['siret'])){
                include 'HTML/headerConnect.html';
            }
            else{
                include 'HTML/header.html';
            } 
        ?>
    </header>
    

    
    <h1 id="page-title">Bienvenue sur GeoApp !</h1>

</body>

</html>