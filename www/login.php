<?php
session_start();
require_once 'db_config.php'; // Fichier contenant les infos de connexion DB

// Vérification du formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Établir une connexion sécurisée à la base de données
    $conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_password");

    if (!$conn) {
        die("Erreur de connexion à la base de données");
    }

    // Requête pour récupérer l'utilisateur
    $query = "SELECT * FROM users_db WHERE username = $1";
    $result = pg_query_params($conn, $query, [$username]);

    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        
        // Vérification du mot de passe haché
        if (password_verify($password, $user['password_hash'])) {
            // Connexion réussie
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin']; // Ajout de la vérification du rôle d'admin
            header('Location: stats.php'); // Redirection vers la page des statistiques
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect";
        }
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect";
    }

    pg_close($conn);
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./styles/styles.css"/>
    <title>Connexion</title>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <h2>Connexion</h2>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-btn">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
