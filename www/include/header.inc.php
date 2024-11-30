<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" href="images/logo.svg" type="image/svg+xml">
    <title>Pressure Plate</title>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css"/>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="./images/logo.svg" alt="Pressure Plate Logo">
            </div>
            <div class="nav-links">
                <a href="stats.php">STATS</a>
                <a href="admin.php">ADMIN</a>
            </div>
            <form action="logout.php" method="post" class="logout-form">
                <button type="submit" class="logout-button">Déconnexion</button>
            </form>
        </nav>
    </header>