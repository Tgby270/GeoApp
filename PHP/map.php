<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../map/style.css">

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
</head>

<body>

    <header>
        <?php include __DIR__ . '/../HTML/header.html'; ?>
    </header>

    <div id="map1" style="height: 97vh; width: 100%;"></div>

    <script>
        var map = L.map('map1', { minZoom: 3 }).setView([47.1, 3], 6.3);
        loadmap(map);
    </script>

</body>
</html>