<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();
require_once 'db_config.php'; // Connexion à la base de données

// Vérification de la session (seulement si l'utilisateur est connecté)
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

// Vérification si l'utilisateur est un admin
$is_admin = $_SESSION['is_admin'] ?? false;

// Connexion à la base de données
$conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_password");
if (!$conn) {
    die("Erreur de connexion à la base de données");
}

// Traitement du formulaire pour ajouter un utilisateur
if (isset($_POST['add_user'])) {
    $username = $_POST['new_username'];
    $password = $_POST['new_password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    if (isset($_POST['is_admin'])) {
        $is_admin = 't';
    } else {
        $is_admin = 'f';
    }

    // Ajouter l'utilisateur dans la base de données
    $query = "INSERT INTO users_db (username, password_hash, is_admin) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $query, [$username, $password_hash, $is_admin]);

    if ($result) {
        $result_message = "Utilisateur ajouté avec succès !";
    } else {
        $result_message = "Erreur lors de l'ajout de l'utilisateur.";
    }
}

// Traitement du formulaire pour supprimer un utilisateur
if (isset($_POST['delete_user'])) {
    $delete_username = $_POST['delete_username'];

    // Supprimer l'utilisateur de la base de données (évite la suppression de soi-même)
    if ($delete_username !== $_SESSION['username']) {
        $query = "DELETE FROM users_db WHERE username = $1";
        $result = pg_query_params($conn, $query, [$delete_username]);

        if ($result) {
            $result_message = "Utilisateur supprimé avec succès !";
        } else {
            $result_message = "Erreur lors de la suppression de l'utilisateur.";
        }
    } else {
        $result_message = "Vous ne pouvez pas vous supprimer vous-même.";
    }
}

// Traitement du formulaire pour changer son propre mot de passe
if (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    $query = "UPDATE users_db SET password_hash = $1 WHERE username = $2";
    $result = pg_query_params($conn, $query, [$new_password_hash, $_SESSION['username']]);

    if ($result) {
        $result_message = "Mot de passe mis à jour avec succès !";
    } else {
        $result_message = "Erreur lors de la mise à jour du mot de passe.";
    }
}

// Récupérer la liste des utilisateurs (pour les admins uniquement)
if ($is_admin) {
    $query = "SELECT username FROM users_db";
    $result = pg_query($conn, $query);
    $users = pg_fetch_all($result);
}

pg_close($conn);
require_once 'include/header.inc.php';
?>


<div class="admin-container">
    <?php if (isset($result_message)): ?>
        <!-- Message de résultat avec un bouton "OK" -->
        <div class="result-message">
            <h2><?php echo htmlspecialchars($result_message, ENT_QUOTES, 'UTF-8'); ?></h2>
            <form action="admin.php" method="GET">
                <button type="submit">OK</button>
            </form>
        </div>
    <?php else: ?>
        <!-- Page d'administration complète -->
        <h1>Page d'administration</h1>

        <!-- Changer le mot de passe -->
        <div class="form-container">
            <h2>Changer votre mot de passe</h2>
            <form action="admin.php" method="POST">
                <label for="new_password">Nouveau mot de passe :</label>
                <input type="password" name="new_password" required>
                <button type="submit" name="change_password">Changer le mot de passe</button>
            </form>
        </div>

        <?php if ($is_admin === 't'): ?>
            <!-- Ajouter un utilisateur -->
            <div class="form-container">
                <h2>Ajouter un utilisateur</h2>
                <form action="admin.php" method="POST">
                    <label for="new_username">Nom d'utilisateur :</label>
                    <input type="text" name="new_username" required>
                    <label for="new_password">Mot de passe :</label>
                    <input type="password" name="new_password" required>
                    <label for="is_admin">Utilisateur administrateur ?</label>
                    <input type="checkbox" name="is_admin">
                    <button type="submit" name="add_user">Ajouter l'utilisateur</button>
                </form>
            </div>

            <!-- Supprimer un utilisateur -->
            <div class="form-container">
                <h2>Supprimer un utilisateur</h2>
                <form action="admin.php" method="POST">
                    <label for="delete_username">Nom d'utilisateur à supprimer :</label>
                    <select name="delete_username">
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['username']; ?>"><?php echo $user['username']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="delete_user">Supprimer l'utilisateur</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>


<?php require_once 'include/footer.inc.php'; ?>
</body>
</html>
