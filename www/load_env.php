<?php
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("Le fichier .env n'existe pas.");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Ignore les commentaires
        }

        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);

            // Supprime les guillemets si présents
            $value = trim($value, '"\'');

            // Définit la variable dans $_ENV
            $_ENV[$key] = $value;
        }
    }
}
