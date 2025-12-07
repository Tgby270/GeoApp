<?php
    require_once 'connexionBDD.php';
    require_once 'infrastructures.php';
    require_once 'Users.php';

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
                    floatval($row['coordonneeY']),
                    intval($row['id'])
                );
                array_push($Infs, $inf);
            }
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
            die('no data found');
        }

        else {
            $stmt = $bdd->prepare('SELECT * from Infrastructure where user_id = ?');
            $stmt->execute([$user_id]);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $inf = new Infrastructures(
                    $row['nom'],
                    $row['type'],
                    $row['adresse'],
                    floatval($row['coordonneeX']),
                    floatval($row['coordonneeY']),
                    intval($row['id'])
                );
                array_push($Infs, $inf);
            }
        }
        return $Infs;
    }
?>