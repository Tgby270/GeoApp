<?php
    session_start();
    require_once 'connexionBDD.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $siret = htmlspecialchars($_POST['siret']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $password2 = htmlspecialchars($_POST['password2']);

        $query = "SELECT COUNT(*) FROM Users WHERE email = :email OR siret = :siret";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':siret', $siret, PDO::PARAM_INT);
        $stmt->execute();
        $existingUser = $stmt->fetchColumn();

        if ($existingUser == 0) {
            if ($password != $password2) {
                $_SESSION['error'] =  "Les mots de passe ne correspondent pas.";
                header("Location: ../HTML/inscription.php"); 
                exit();
            }
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $bdd -> prepare("Insert Into Users (siret, email, password) Values (:siret, :email, :password);");
            if ($insert -> execute(array(
                'siret' => $siret,
                'email' => $email,
                'password' => $hashed_password
            ))){
                header("Location: ../index.php");
                exit();
            }                
            else {
                $_SESSION['error'] = "Erreur lors de l'enregistrement de l'utilisateur.";
                header("Location: ../HTML/inscription.php"); 
                exit();
            }
        }
        else {
            $_SESSION['error'] = "Email ou Siret déjà utilisé.";
            header("Location: ../HTML/inscription.php"); 
            exit();
        }
    }
?>