<?php
session_start();
require "../PHP/getInfrastructuresFromDb.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../map/mapStyle.css">

    <!--Style Required for the map-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!--Style Required to show clusters of markers-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <!--Script Required for the map-->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../map/apiCall.js"></script>

    <!--Script Required to show clusters of markers-->
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script src="../map/filtersSidebar.js" defer></script>
</head>

<body>

    <header>
        <?php include("../HTML/header.php"); ?>
    </header>

    <!-- removed inline style, CSS will control size -->
    <div id="map1"></div>

    <button id="openFilters">
        Open Filters
    </button>

    <div id="filters_modal">
        <div class="modal-content">
            <span id="closeFilters" class="closebtn">&times;</span>
            <h2>FILTREZ VOS RECHERCHES</h2>

            <div id="filtersContainer">
                <form id="filtersForm">
                    <label for="locationSearch">Ville :</label>
                    <input type="text" name="locationSearch" id="locationSearch">

                    <label for="rayon">Rayon (km):</label>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        <input type="range" name="Rayon" id="rayon" min="0" max="100" value="0"
                            style="flex: 1; margin: 0;">
                        <span id="rayonValue">Désactivé</span>
                    </div>

                    <label for="equipmentSearch">Type d'infrastructure :</label>
                    <button type="button" id="toggleCheckboxes" class="toggle-checkbox-btn">
                        <span class="checkbox-icon">▼</span> Afficher les types
                    </button>

                    <div id="equipmentSelectionArea" class="equipment-selection-area" style="display: none;">
                        <input type="text" id="equipmentSearch" placeholder="Rechercher un type d'équipement...">

                        <div id="equipmentGrid" class="equipment-grid">
                            <div class="loading-text">Chargement...</div>
                        </div>

                        <div id="selectedCount" class="selected-count">0 sélectionné(s)</div>
                    </div>

                    <label>Options d'équipement :</label>
                    <button type="button" id="toggleAmenities" class="toggle-checkbox-btn">
                        <span class="checkbox-icon">▼</span> Afficher les options
                    </button>

                    <div id="amenitiesSelectionArea" class="equipment-selection-area" style="display: none;">
                        <div class="equipment-grid">
                            <div class="equipment-card" onclick="toggleAmenityCheckbox('sanitairesCheckbox')">
                                <input type="checkbox" id="sanitairesCheckbox" name="sanitaires" value="sanitaires"
                                    onclick="event.stopPropagation();">
                                <span>🚻 Possède des Sanitaires</span>
                            </div>

                            <div class="equipment-card" onclick="toggleAmenityCheckbox('doucheCheckbox')">
                                <input type="checkbox" id="doucheCheckbox" name="douche" value="douche"
                                    onclick="event.stopPropagation();">
                                <span>🚿 Possède des Douches</span>
                            </div>
                        </div>
                    </div>

                    <label>Options d'accessibilité :</label>
                    <button type="button" id="toggleAccessibility" class="toggle-checkbox-btn">
                        <span class="checkbox-icon">▼</span> Afficher les options d'accessibilité
                    </button>

                    <div id="accessibilitySelectionArea" class="equipment-selection-area" style="display: none;">
                        <div class="equipment-grid">
                            <div class="equipment-card" onclick="toggleAmenityCheckbox('handiMCheckbox')">
                                <input type="checkbox" id="handiMCheckbox" name="handiM" value="handiM"
                                    onclick="event.stopPropagation();">
                                <span>Accessible aux personnes en situation de handicap moteur</span>
                            </div>

                            <div class="equipment-card" onclick="toggleAmenityCheckbox('handiSCheckbox')">
                                <input type="checkbox" id="handiSCheckbox" name="handiS" value="handiS"
                                    onclick="event.stopPropagation();">
                                <span>Accessible aux personnes en situation de handicap sensoriel</span>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="apply-filters-btn" id="applyFiltersBtn"
                            style="flex: 1; margin-top: 0;">Appliquer les filtres</button>
                        <button type="button" class="apply-filters-btn" id="resetFiltersBtn"
                            style="flex: 1; margin-top: 0;">Réinitialiser</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        var map = L.map('map1', { minZoom: 3 }).setView([47.1, 3], 6.3);
        loadmap(map);
        showEquipmentTypes();
        //getUserLocation();

        // Toggle amenity checkboxes when clicking on the card (same as equipment types)
        function toggleAmenityCheckbox(checkboxId) {
            const checkbox = document.getElementById(checkboxId);
            checkbox.checked = !checkbox.checked;
            const card = checkbox.closest('.equipment-card');
            const value = checkbox.value;

            if (checkbox.checked) {
                card.classList.add('selected');
                selectedOptions.add(value);
            } else {
                card.classList.remove('selected');
                selectedOptions.delete(value);
            }
        }

        // Toggle amenities section
        document.getElementById('toggleAmenities').addEventListener('click', function (e) {
            e.preventDefault();
            const amenitiesArea = document.getElementById('amenitiesSelectionArea');
            amenitiesArea.style.display = amenitiesArea.style.display === 'none' ? 'block' : 'none';
            this.classList.toggle('active');
        });

        // Toggle accessibility section
        document.getElementById('toggleAccessibility').addEventListener('click', function (e) {
            e.preventDefault();
            const accessibilityArea = document.getElementById('accessibilitySelectionArea');
            accessibilityArea.style.display = accessibilityArea.style.display === 'none' ? 'block' : 'none';
            this.classList.toggle('active');
        });

        // Setup filter form submission
        document.getElementById('filtersForm').addEventListener('submit', function (e) {
            e.preventDefault();
            applyFilters();
            // Close modal after applying filters
            const modal = document.getElementById('filters_modal');
            if (modal) {
                modal.classList.remove('show');
            }
        });
    </script>

    <?php
    // Check if filters are submitted via POST
    if(isset($_POST['locationSearch']) || isset($_POST['equipmentTypes'])) {
        // Build filters array
        $filters = [];
        
        // Add location filter
        if (!empty($_POST['locationSearch'])) {
            $filters['ville'] = $_POST['locationSearch'];
        }
        
        // Add equipment types filter
        if (!empty($_POST['equipmentTypes']) && is_array($_POST['equipmentTypes'])) {
            $filters['types'] = $_POST['equipmentTypes'];
        }
        
        // Get filtered infrastructures
        $infs = getInfrastructuresFromDbFiltered($filters);
    } else {
        // No filters set, get all infrastructures
        $infs = getInfrastructuresFromDb();
    }

    foreach ($infs as $in) {

        $options = getInfOptions($in->inf_id);

        echo "<script>
                console.log('adding marker from db');
                createMarkersFromDb('{$in->nom}', {$in->coordonneeX}, {$in->coordonneeY}, '{$in->type}', '{$in->adresse}', '{$in->ville}'," . json_encode($options) . ",'{$in->inf_id}');
                console.log('marker added from db');
            </script>\n";
    }
    ?>


</body>

</html>