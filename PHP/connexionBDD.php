<?php

    function loadEnv($file){
        if (!file_exists($file)) {
            throw new Exception('Le fichier .env est introuvable.');
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!empty($key)) {
                putenv("$key=$value");
            }
        }
    }

    loadEnv(__DIR__ . '/../.env');

    try {
        $bdd = new PDO(
            'mysql:host=' . getenv('DB_SERVER') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8mb4',
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    }
?>