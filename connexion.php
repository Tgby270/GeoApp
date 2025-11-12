<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $siret = htmlspecialchars($_POST['siret']);
        $password = htmlspecialchars($_POST['password']);

        $rep = $bdd -> query("Select password From users Where siret = '" . $siret . "';");

        if ($rep->rowCount() > 0) {
            $data = $rep->fetch();
            $hashed_password = $data['password'];

            if (password_verify($password, $hashed_password)) {
                // Connexion réussie
                header("Location: dashboard.html");
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Numéro de siret non trouvé.";
        }
    }
?>