<?php
require_once 'load_env.php';

try {
    loadEnv(__DIR__ . './../.env');
} catch (Exception $e) {
    die($e->getMessage());
}

// Utilisation des variables
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_name = $_ENV['DB_NAME'] ?? '';
$db_user = $_ENV['DB_USER'] ?? '';
$db_password = $_ENV['DB_PASSWORD'] ?? '';

// Connexion à la base de données
$conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_password");

if (!$conn) {
    die("Erreur de connexion à la base de données");
}
