<?php
session_start();
require "../PHP/getInfrastructuresFromDb.php";

$isAdmin = isset($_SESSION["is_admin"]);
$isloggedin = isset($_SESSION["user_id"]);


$userId = $_SESSION['user_id'];
$infrastructures = getUserInfrastructures($userId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_infrastructure'], $_POST['inf_id'])) {
            deleteInfrastructure(intval($_POST['inf_id']));
            header('Location: userDashboard.php');
            exit;
        }

        if (isset($_POST['edit_infrastructure'], $_POST['inf_id'])) {
            header('Location: editInfrastructure.php?inf_id=' . intval($_POST['inf_id']));
            exit;
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <title>Dashboard</title>
</head>

<body>

    <header>
        <?php include './header.php'; ?>
    </header>

       <div class="dashboard-container">
        <h1>Vos Infrastructures</h1>

        <?php

        echo "<button type=\"button\" class=\"collapsible\">";
        echo "<h3>Infrastructures (" . count($infrastructures) . ")</h3>";
        echo "</button>";
        echo "<div class=\"content\">";
        echo "<div class=\"infrastructure-list\">";
        
        foreach ($infrastructures as $infrastructure) {
            echo "<div class=\"infrastructure-item\">";
            echo "<h4>" . htmlspecialchars($infrastructure->getNom()) . "</h4>";
            echo "<p><strong>Type:</strong> " . htmlspecialchars($infrastructure->getType()) . "</p>";
            echo "<p><strong>Adresse:</strong> " . htmlspecialchars($infrastructure->getAdresse()) . "</p>";
            echo "<p><strong>Ville:</strong> " . htmlspecialchars($infrastructure->getVille()) . "</p>";
            echo "<p><strong>Coordonnées:</strong> (" . htmlspecialchars($infrastructure->getCoordonneeX()) . ", " . htmlspecialchars($infrastructure->getCoordonneeY()) . ")</p>";
            echo "<div class=\"button-group\">";
            echo "<form method=\"post\" action=\"userDashboard.php\">";
            echo "<input type=\"hidden\" name=\"inf_id\" value=\"" . htmlspecialchars($infrastructure->getInfId()) . "\">";
            echo "<button type=\"submit\" name=\"edit_infrastructure\" class=\"btn-edit\">Éditer</button>";
            echo "</form>";
            echo "<form method=\"post\" action=\"userDashboard.php\">";
            echo "<input type=\"hidden\" name=\"inf_id\" value=\"" . htmlspecialchars($infrastructure->getInfId()) . "\">";
            echo "<button type=\"submit\" name=\"delete_infrastructure\" class=\"btn-delete\">Supprimer</button>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
        }
        
        echo "</div>";
        echo "</div>";
        ?>
        </div>

        <div id = "add">
            <button onclick="window.location.href='ajouterInfrastructure.php'" class="add-infrastructure-btn">Ajouter une Infrastructure</button>
        </div>

    <script>
        const STORAGE_KEY = 'dashboardCollapsibleState';
        const coll = document.getElementsByClassName('collapsible');
        const state = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');

        for (let i = 0; i < coll.length; i++) {
            const content = coll[i].nextElementSibling;

            if (state[i]) {
                coll[i].classList.add('active');
                content.style.display = 'block';
            }

            coll[i].addEventListener('click', function () {
                const isOpen = content.style.display === 'block';
                this.classList.toggle('active');
                content.style.display = isOpen ? 'none' : 'block';
                state[i] = !isOpen;
                localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
            });
        }
    </script>
</body>

</html>