<?php
    if (SERVER["REQUEST_METHOD"] == "POST") {
        $siret = htmlspecialchars($_POST['siret']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        $rep = $bdd -> query("Select count(*) From users Where email = " + "or siret = " + $siret + ";");

        if ($rep[0] == 0) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $bdd -> prepare("Insert Into users (siret, email, password) Values (?, ?, ?);");
            $insert -> execute(array(
                'siret' => $siret,
                'email' => $email,
                'password' => $hashed_password
            ));
            header("Location: login.html");
        } else {
            echo "Email ou Siret déjà utilisé.";
        }
    }
?>