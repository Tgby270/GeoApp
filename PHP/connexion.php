<?php
    session_start();
    require_once 'connexionBDD.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $siret = htmlspecialchars($_POST['siret']);
        $password = htmlspecialchars($_POST['password']);

        $rep = $bdd -> query("Select password From Users Where siret = '" . $siret . "';");

        if ($rep->rowCount() > 0) {
            $data = $rep->fetch();
            $hashed_password = $data['password'];

            if (password_verify($password, $hashed_password)) {
                $_SESSION['siret'] = $siret;
                $id = $bdd -> prepare("Select user_id From Users Where siret = ?");
                $id -> execute(array($siret));
                $_SESSION['user_id'] = $id->fetchColumn();

                $admin = $bdd -> prepare('SELECT user_id from Admins where user_id = ?');
                $admin -> execute(array($_SESSION['user_id']));
                $_SESSION['is_admin'] = $admin->rowCount() > 0;

                header("Location: ../index.php");
            } else {
                $_SESSION['error'] = "Mot de passe incorrect";
                header("Location: ../HTML/connexionPage.php"); 
                exit();
            }
        } else {
            $_SESSION['error'] = "Numéro de siret non trouvé";
            header("Location: ../HTML/connexionPage.php"); 
            exit();
            
        }
    }
?>