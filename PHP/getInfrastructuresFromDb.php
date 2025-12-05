<?php
    require_once 'connexionBDD.php';
    require_once 'infrastructures.php';

    function getInfrastructuresFromDb() {
        global $bdd;
        $Infs = [];

        $stmt = $bdd->prepare('SELECT COUNT(*) from Infrastructure');
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count <= 0) {
            die('no data found');
        }

        else {
            $stmt = $bdd->prepare('SELECT * from Infrastructure');
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $inf = new Infrastructures(
                    $row['nom'],
                    $row['type'],
                    $row['adresse'],
                    floatval($row['coordonneeX']),
                    floatval($row['coordonneeY'])
                );
                array_push($Infs, $inf);
            }
        }
        return $Infs;
    }

    $infs = getInfrastructuresFromDb();
    
    
    foreach ($infs as $in){
        echo "<script>
            console.log('adding marker from db');
            createMarkersFromDb('{$in->nom}', {$in->coordonneeX}, {$in->coordonneeY}, '{$in->type}', '{$in->adresse}');
            console.log('marker added from db');
        </script>\n";
    }


?>