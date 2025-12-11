<?php
    require_once 'connexionBDD.php';
    require_once 'infrastructures.php';
    require_once 'Users.php';
    require_once 'events.php';

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
                    $row['ville'],
                    floatval($row['coordonneeX']),
                    floatval($row['coordonneeY']),
                    intval($row['id'])
                );
                array_push($Infs, $inf);
            }
        }
        return $Infs;
    }

    function getInfrastructuresFromDbFiltered($filters = []) {
        global $bdd;
        $Infs = [];
        
        // Build the SQL query with filters
        $sql = 'SELECT * FROM Infrastructure WHERE 1=1';
        $params = [];
        
        // Filter by type if provided
        if (!empty($filters['type'])) {
            $sql .= ' AND type = ?';
            $params[] = $filters['type'];
        }
        
        // Filter by ville/address if provided
        if (!empty($filters['ville'])) {
            $sql .= ' AND (ville LIKE ? OR adresse LIKE ?)';
            $searchTerm = '%' . $filters['ville'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Filter by multiple types if provided as array
        if (!empty($filters['types']) && is_array($filters['types'])) {
            $placeholders = str_repeat('?,', count($filters['types']) - 1) . '?';
            $sql .= ' AND type IN (' . $placeholders . ')';
            $params = array_merge($params, $filters['types']);
        }
        
        $stmt = $bdd->prepare($sql);
        $stmt->execute($params);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $inf = new Infrastructures(
                $row['nom'],
                $row['type'],
                $row['adresse'],
                $row['ville'],
                floatval($row['coordonneeX']),
                floatval($row['coordonneeY']),
                intval($row['id'])
            );
            array_push($Infs, $inf);
        }
        
        return $Infs;
    }

    function getUsersFromDb() {
        global $bdd;
        $Users = [];

        $stmt = $bdd->prepare('SELECT COUNT(*) from Users where user_id > 0');
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count <= 0) {
            die('no data found');
        }

        else {
            $stmt = $bdd->prepare('SELECT user_id, Siret, Email from Users where user_id > 0');
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = new Users(
                    $row['user_id'],
                    $row['Siret'],
                    $row['Email'],
                );
                array_push($Users, $user);
            }
        }
        return $Users;
    }

    function deleteInfrastructure($inf_id) {
        global $bdd;

        $stmt = $bdd->prepare('DELETE FROM Infrastructure WHERE id = ?');
        $stmt->execute([$inf_id]);
    }

    function deleteUser($user_id) {
        global $bdd;

        $stmt = $bdd->prepare('UPDATE Infrastructure set user_id = 0 where user_id = ?');
        $stmt->execute([$user_id]);

        $stmt = $bdd->prepare('UPDATE Calendrier set user_id = 0 where user_id = ?');
        $stmt->execute([$user_id]);

        $stmt = $bdd->prepare('DELETE FROM Users WHERE user_id = ? AND user_id != 0');
        $stmt->execute([$user_id]);
    }

    function getUserInfrastructures($user_id) {
        global $bdd;
        $Infs = [];

        $stmt = $bdd->prepare('SELECT COUNT(*) from Infrastructure where user_id = ?');
        $stmt->execute([$user_id]);
        $count = $stmt->fetchColumn();

        if ($count <= 0) {
            return $Infs;
        }

        else {
            $stmt = $bdd->prepare('SELECT * from Infrastructure where user_id = ?');
            $stmt->execute([$user_id]);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $inf = new Infrastructures(
                    $row['nom'],
                    $row['type'],
                    $row['adresse'],
                    $row['ville'],
                    floatval($row['coordonneeX']),
                    floatval($row['coordonneeY']),
                    intval($row['id'])
                );
                array_push($Infs, $inf);
            }
        }
        return $Infs;
    }

    function getCalendarEventsThisWeekDB($inf_id){
        global $bdd;
        $events = [];

        $firstday = date('Y-m-d', strtotime("this week"));
        $lastday = date('Y-m-d', strtotime("this week +6 days"));

        $stmt = $bdd->prepare('SELECT * from Calendrier where inf_id = ? and cal_date_debut >= ? and cal_date_fin <= ?');
        $stmt->execute([$inf_id, $firstday, $lastday]);

        

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $event = new Events(
                $row['inf_id'],
                $row['cal_libelle'],
                $row['cal_date_debut'],
                $row['cal_date_fin'],
                $row['cal_horaire_debut'],
                $row['cal_horaire_fin'],
                $row['user_id']
            );
            array_push($events, $event);
        }
        return $events;
    }

        function getCalendarEventsThisWeekAPI($inf_id){
        global $bdd;
        $events = [];

        $firstday = date('Y-m-d', strtotime("this week"));
        $lastday = date('Y-m-d', strtotime("this week +6 days"));

        $stmt = $bdd->prepare('SELECT * from CalendrierAPI where inf_id = ? and cal_date_debut >= ? and cal_date_fin <= ?');
        $stmt->execute([$inf_id, $firstday, $lastday]);

        

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $event = new Events(
                $row['inf_id'],
                $row['cal_libelle'],
                $row['cal_date_debut'],
                $row['cal_date_fin'],
                $row['cal_horaire_debut'],
                $row['cal_horaire_fin'],
                $row['user_id']
            );
            array_push($events, $event);
        }
        return $events;
    }

    function insertCalendarEvent($inf_id, $libelle, $date_debut, $date_fin, $heure_debut, $heure_fin, $uid) {
        global $bdd;

        if($heure_fin > 17) {
            return "Les événements ne peuvent pas se terminer après 17h.";
        }

        $stmt = $bdd->prepare("SELECT * from Calendrier where cal_date_debut=? AND cal_horaire_debut = ? AND cal_horaire_fin = ?");
        $stmt->execute([$date_debut, $heure_debut, $heure_fin]);
        $existingEvent = $stmt->fetch(PDO::FETCH_ASSOC);

        if($existingEvent) {
            return "Un événement existe déjà à cette date et heure.";
        }

        if(is_numeric($inf_id)){
            $stmt = $bdd->prepare('INSERT INTO Calendrier (inf_id, cal_libelle, cal_date_debut, cal_date_fin, cal_horaire_debut, cal_horaire_fin, user_id) VALUES (?,?,?,?,?,?,?)');
        }
        else{
            $stmt = $bdd->prepare('INSERT INTO CalendrierAPI (inf_id, cal_libelle, cal_date_debut, cal_date_fin, cal_horaire_debut, cal_horaire_fin, user_id) VALUES (?,?,?,?,?,?,?)');
        }
        try {
            $stmt->execute([$inf_id, $libelle, $date_debut, $date_fin, $heure_debut, $heure_fin, $uid]);
            return true;
        } catch (Exception $e) {
            return 'Error inserting event: ' . $e->getMessage();
        }
    }

    function getInfOptions($inf_id) {
        global $bdd;
        $options = [
            'sanitaires' => 'false',
            'douche' => 'false',
            'handiM' => 'false',
            'handiS' => 'false'
        ];

        $stmt = $bdd->prepare('
            SELECT Option_infrastructure.option_nom 
            FROM infOption 
            JOIN Option_infrastructure ON infOption.opt_code = Option_infrastructure.code 
            WHERE infOption.inf_id = ?
        ');
        $stmt->execute([$inf_id]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $optionName = $row['option_nom'];
            if ($optionName === 'sanitaires') {
                $options['sanitaires'] = 'true';
            } elseif ($optionName === 'douche') {
                $options['douche'] = 'true';
            } elseif ($optionName === 'mobilite') {
                $options['handiM'] = 'true';
            } elseif ($optionName === 'sensoriel') {
                $options['handiS'] = 'true';
            }
        }

        return $options;
    }
?>