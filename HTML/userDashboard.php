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
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
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

    <div id="edit-popup">
        <div class="popup-content">
            <span id="closePopup" class="closebtn">&times;</span>
            <h2>Éditer l'infrastructure</h2>
            <form id="editForm" method="post" action="userDashboard.php">
                <input type="hidden" name="inf_id" id="editInfId">
                <label for="editNom">Nom :</label>
                <input type="text" name="nom" id="editNom" required><br><br>

                <label for="editType">Type :</label>
                <input type="text" name="type" id="editType" required><br><br>

                <label for="editAdresse">Adresse :</label>
                <input type="text" name="adresse" id="editAdresse" required><br><br>

                <label for="editVille">Ville :</label>
                <input type="text" name="ville" id="editVille" required><br><br>

                <label for="editCoordonneeX">Longitude :</label>
                <input type="number" name="coordonneeX" id="editCoordonneeX" step="any" required><br><br>

                <label for="editCoordonneeY">Latitude :</label>
                <input type="number" name="coordonneeY" id="editCoordonneeY" step="any" required><br><br>

                <button type="submit" name="save_infrastructure">Enregistrer les modifications</button>
            </form>
        </div>
    </div>

    <button onclick="window.location.href='ajouterInfrastructure.php'" class="add-infrastructure-btn">Ajouter une Infrastructure</button>

    
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

    <script>
        // Edit popup functionality
        const editPopup = document.getElementById('edit-popup');
        const closePopupBtn = document.getElementById('closePopup');
        const editForm = document.getElementById('editForm');

        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const infrastructureItem = this.closest('.infrastructure-item');
                const infId = infrastructureItem.querySelector('input[name="inf_id"]').value;
                const nom = infrastructureItem.querySelector('h4').innerText;
                const type = infrastructureItem.querySelector('p:nth-of-type(1)').innerText.replace('Type: ', '');
                const adresse = infrastructureItem.querySelector('p:nth-of-type(2)').innerText.replace('Adresse: ', '');
                const ville = infrastructureItem.querySelector('p:nth-of-type(3)').innerText.replace('Ville: ', '');
                const coordonneesText = infrastructureItem.querySelector('p:nth-of-type(4)').innerText.replace('Coordonnées: (', '').replace(')', '');
                const [coordonneeX, coordonneeY] = coordonneesText.split(', ');

                document.getElementById('editInfId').value = infId;
                document.getElementById('editNom').value = nom;
                document.getElementById('editType').value = type;
                document.getElementById('editAdresse').value = adresse;
                document.getElementById('editVille').value = ville;
                document.getElementById('editCoordonneeX').value = coordonneeX;
                document.getElementById('editCoordonneeY').value = coordonneeY;

                editPopup.style.display = 'block';
            });
        });

        closePopupBtn.onclick = function () {
            editPopup.style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == editPopup) {
                editPopup.style.display = 'none';
            }
        }
    </script>
</body>

</html>