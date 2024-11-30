<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php'); // Redirection vers l'écran de connexion
    exit();
}

function get_stat($query) {
    global $conn;
    $result = pg_query($conn, $query);
    if ($result) {
        $row = pg_fetch_assoc($result);
        return $row['count'] ?? $row['avg'] ?? $row['total'] ?? 0;
    }
    return 0;
}
require_once 'db_config.php';
require_once 'include/header.inc.php';
?>


    <h1>Statistiques du Parc</h1>

    <?php
    

    // -----------------------------------
    // Section des statistiques des employés
    // -----------------------------------

    $total_employes = get_stat("SELECT COUNT(*) FROM employees");

    // Statistique : Répartition des employés par poste
    $employes_par_poste_query = pg_query($conn, "SELECT e_poste, COUNT(*) FROM employees GROUP BY e_poste");
    $employes_par_poste = [];
    while ($row = pg_fetch_assoc($employes_par_poste_query)) {
        $employes_par_poste[$row['e_poste']] = $row['count'];
    }

    // Statistique : Âge moyen des employés
    $age_moyen_employes = round(get_stat("SELECT AVG(e_age) FROM employees"));

    // Statistique : Salaire moyen des employés
    $salaire_moyen_employes = round(get_stat("SELECT AVG(e_salaire) FROM employees"), 2);

    // Statistique : Répartition des employés par âge
    $repartition_age_query = pg_query($conn, "
        SELECT 
            CASE 
                WHEN e_age < 25 THEN 'Moins de 25'
                WHEN e_age BETWEEN 25 AND 40 THEN '25-40'
                WHEN e_age BETWEEN 41 AND 60 THEN '41-60'
                ELSE 'Plus de 60' 
            END AS groupe_age, COUNT(*) 
        FROM employees 
        GROUP BY groupe_age
    ");
    $repartition_age = [];
    while ($row = pg_fetch_assoc($repartition_age_query)) {
        $repartition_age[$row['groupe_age']] = $row['count'];
    }

    // Statistique : Répartition des employés par salaire
    $repartition_salaire_query = pg_query($conn, "
        SELECT 
            CASE 
                WHEN e_salaire < 2000 THEN 'Moins de 2000€'
                WHEN e_salaire BETWEEN 2000 AND 4000 THEN '2000€ - 4000€'
                WHEN e_salaire BETWEEN 4000 AND 6000 THEN '4000€ - 6000€'
                ELSE 'Plus de 6000€' 
            END AS groupe_salaire, COUNT(*) 
        FROM employees 
        GROUP BY groupe_salaire
    ");
    $repartition_salaire = [];
    while ($row = pg_fetch_assoc($repartition_salaire_query)) {
        $repartition_salaire[$row['groupe_salaire']] = $row['count'];
    }

    // Statistique : Employé le plus jeune
    $plus_jeune_query = pg_query($conn, "SELECT e_nom, e_prenom, e_age FROM employees ORDER BY e_age ASC LIMIT 1");
    $plus_jeune = pg_fetch_assoc($plus_jeune_query);

    // Statistique : Employé le plus âgé
    $plus_age_query = pg_query($conn, "SELECT e_nom, e_prenom, e_age FROM employees ORDER BY e_age DESC LIMIT 1");
    $plus_age = pg_fetch_assoc($plus_age_query);

    // Affichage de la section employés
    echo '<section>';
    echo '<h2>Statistiques des Employés</h2>';
    echo '<div class="stats-container">';

    // Carte pour le total des employés
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Total Employés</h3>';
    echo '<p class="stat-value">' . $total_employes . '</p>';
    echo '</div>';

    // Carte pour les employés par poste
    foreach ($employes_par_poste as $poste => $count) {
        echo '<div class="stat-card">';
        echo '<h3 class="stat-title">Employés - ' . $poste . '</h3>';
        echo '<p class="stat-value">' . $count . '</p>';
        echo '</div>';
    }

    // Carte pour l'âge moyen des employés
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Âge moyen des employés</h3>';
    echo '<p class="stat-value">' . $age_moyen_employes . ' ans</p>';
    echo '</div>';

    // Carte pour le salaire moyen des employés
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Salaire moyen des employés</h3>';
    echo '<p class="stat-value">' . $salaire_moyen_employes . ' €</p>';
    echo '</div>';

    // Carte pour la répartition des employés par âge
    foreach ($repartition_age as $groupe => $count) {
        echo '<div class="stat-card">';
        echo '<h3 class="stat-title">Employés - Groupe d\'âge ' . $groupe . '</h3>';
        echo '<p class="stat-value">' . $count . '</p>';
        echo '</div>';
    }

    // Carte pour la répartition des employés par salaire
    foreach ($repartition_salaire as $groupe => $count) {
        echo '<div class="stat-card">';
        echo '<h3 class="stat-title">Employés - Groupe de salaire ' . $groupe . '</h3>';
        echo '<p class="stat-value">' . $count . '</p>';
        echo '</div>';
    }

    // Carte pour l'employé le plus jeune
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Employé le plus jeune</h3>';
    echo '<p class="stat-value">' . $plus_jeune['e_nom'] . ' ' . $plus_jeune['e_prenom'] . ' (' . $plus_jeune['e_age'] . ' ans)</p>';
    echo '</div>';

    // Carte pour l'employé le plus âgé
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Employé le plus âgé</h3>';
    echo '<p class="stat-value">' . $plus_age['e_nom'] . ' ' . $plus_age['e_prenom'] . ' (' . $plus_age['e_age'] . ' ans)</p>';
    echo '</div>';

    echo '</div>'; // Fermeture du conteneur de cartes
    echo '</section>';

    // ----------------------------------------- 
    // Section des statistiques des installations
    // -----------------------------------------

    // Statistique : Répartition des installations par type
    $installations_par_type_query = pg_query($conn, "SELECT SUBSTRING(instal_id, 1, 6) AS type_instal, COUNT(*) FROM installations GROUP BY type_instal");
    $installations_par_type = [];
    while ($row = pg_fetch_assoc($installations_par_type_query)) {
        $installations_par_type[$row['type_instal']] = $row['count'];
    }

    // Statistique : Nombre d'installations ouvertes
    $installations_ouvertes = get_stat("SELECT COUNT(*) FROM installations WHERE ouvert = TRUE");

    // Statistique : Nombre d'installations fermées
    $installations_fermees = get_stat("SELECT COUNT(*) FROM installations WHERE ouvert = FALSE");

    // Statistique : Nombre d'installations avec Fast Pass activé
    $installations_fast_pass = get_stat("SELECT COUNT(*) FROM installations WHERE instal_fast_pass = TRUE");

    // Statistique : Nombre d'installations avec zone d'attente spécifiée
    $installations_avec_attente = get_stat("SELECT COUNT(*) FROM installations WHERE instal_waiting = 1");  // Remplacer TRUE par 1

    // Statistique : Surface totale de toutes les installations
    $surface_totale_installations = get_stat("SELECT SUM(instal_surface) FROM installations");

    // Statistique : Installation avec la plus grande surface
    $installation_max_surface_query = pg_query($conn, "SELECT instal_nom, instal_surface FROM installations ORDER BY instal_surface DESC LIMIT 1");
    $installation_max_surface = pg_fetch_assoc($installation_max_surface_query);

    // Statistique : Installation avec la plus petite surface
    $installation_min_surface_query = pg_query($conn, "SELECT instal_nom, instal_surface FROM installations ORDER BY instal_surface ASC LIMIT 1");
    $installation_min_surface = pg_fetch_assoc($installation_min_surface_query);

    // Statistique : Nombre d'installations par zone (area_id)
    $installations_par_zone_query = pg_query($conn, "SELECT area_id, COUNT(*) FROM installations GROUP BY area_id");
    $installations_par_zone = [];
    while ($row = pg_fetch_assoc($installations_par_zone_query)) {
        $installations_par_zone[$row['area_id']] = $row['count'];
    }

    // Statistique : Nombre d'installations par centre (centrale_id)
    $installations_par_central_query = pg_query($conn, "SELECT centrale_id, COUNT(*) FROM installations GROUP BY centrale_id");
    $installations_par_central = [];
    while ($row = pg_fetch_assoc($installations_par_central_query)) {
        $installations_par_central[$row['centrale_id']] = $row['count'];
    }

    // Statistique : Nombre d'installations ayant une description
    $installations_avec_description = get_stat("SELECT COUNT(*) FROM installations WHERE instal_description IS NOT NULL AND instal_description != ''");

    // Affichage de la section installations
    echo '<section>';
    echo '<h2>Statistiques des Installations</h2>';
    echo '<div class="stats-container">';

    // Carte pour les installations par type
    foreach ($installations_par_type as $type => $count) {
        echo '<div class="stat-card">';
        echo '<h3 class="stat-title">Installations - ' . $type . '</h3>';
        echo '<p class="stat-value">' . $count . '</p>';
        echo '</div>';
    }

    // Carte pour le nombre d'installations ouvertes
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Installations Ouvertes</h3>';
    echo '<p class="stat-value">' . $installations_ouvertes . '</p>';
    echo '</div>';

    // Carte pour le nombre d'installations fermées
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Installations Fermées</h3>';
    echo '<p class="stat-value">' . $installations_fermees . '</p>';
    echo '</div>';

    // Carte pour le nombre d'installations avec Fast Pass activé
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Installations avec Fast Pass</h3>';
    echo '<p class="stat-value">' . $installations_fast_pass . '</p>';
    echo '</div>';

    // Carte pour le nombre d'installations avec zone d'attente
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Installations avec Zone d\'Attente</h3>';
    echo '<p class="stat-value">' . $installations_avec_attente . '</p>';
    echo '</div>';

    // Carte pour la surface totale des installations
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Surface Totale des Installations</h3>';
    echo '<p class="stat-value">' . $surface_totale_installations . ' m²</p>';
    echo '</div>';

    // Carte pour l'installation avec la plus grande surface
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Installation avec la plus grande surface</h3>';
    echo '<p class="stat-value">' . $installation_max_surface['instal_nom'] . ' (' . $installation_max_surface['instal_surface'] . ' m²)</p>';
    echo '</div>';

    // Carte pour l'installation avec la plus petite surface
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Installation avec la plus petite surface</h3>';
    echo '<p class="stat-value">' . $installation_min_surface['instal_nom'] . ' (' . $installation_min_surface['instal_surface'] . ' m²)</p>';
    echo '</div>';

    // Carte pour le nombre d'installations par zone
    foreach ($installations_par_zone as $zone => $count) {
        echo '<div class="stat-card">';
        echo '<h3 class="stat-title">Installations - Zone ' . $zone . '</h3>';
        echo '<p class="stat-value">' . $count . '</p>';
        echo '</div>';
    }

    // Carte pour le nombre d'installations par centre
    foreach ($installations_par_central as $central => $count) {
        echo '<div class="stat-card">';
        echo '<h3 class="stat-title">Installations - Centre ' . $central . '</h3>';
        echo '<p class="stat-value">' . $count . '</p>';
        echo '</div>';
    }

    // Carte pour le nombre d'installations avec description
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Installations avec Description</h3>';
    echo '<p class="stat-value">' . $installations_avec_description . '</p>';
    echo '</div>';

    echo '</div>'; // Fermeture du conteneur de cartes
    echo '</section>';

    // ----------------------------------- 
// Section des statistiques des passages
// -----------------------------------

// 1. Total des passages
$total_passages = get_stat("SELECT COUNT(*) FROM passages");

// 2. Passages par capteur
$passages_par_capteur_query = pg_query($conn, "SELECT capteur_id, COUNT(*) FROM passages GROUP BY capteur_id");
$passages_par_capteur = [];
while ($row = pg_fetch_assoc($passages_par_capteur_query)) {
    $passages_par_capteur[$row['capteur_id']] = $row['count'];
}

// 3. Passages par date (par jour)
$passages_par_date_query = pg_query($conn, "SELECT DATE(passage_date) AS date, COUNT(*) FROM passages GROUP BY DATE(passage_date)");
$passages_par_date = [];
while ($row = pg_fetch_assoc($passages_par_date_query)) {
    $passages_par_date[$row['date']] = $row['count'];
}

// 4. Durée moyenne des passages
$avg_duree_passages = get_stat("SELECT AVG(passage_duree) FROM passages");

// 5. Passages par type de capteur
$passages_par_type_capteur_query = pg_query($conn, "SELECT capteur_type, COUNT(*) 
                                                   FROM passages 
                                                   JOIN capteurs ON passages.capteur_id = capteurs.capteur_id 
                                                   GROUP BY capteur_type");
$passages_par_type_capteur = [];
while ($row = pg_fetch_assoc($passages_par_type_capteur_query)) {
    $passages_par_type_capteur[$row['capteur_type']] = $row['count'];
}

// 6. Passages par installation
$passages_par_instal_query = pg_query($conn, "SELECT capteur_instalation, COUNT(*) 
                                              FROM passages 
                                              JOIN capteurs ON passages.capteur_id = capteurs.capteur_id 
                                              GROUP BY capteur_instalation");
$passages_par_instal = [];
while ($row = pg_fetch_assoc($passages_par_instal_query)) {
    $passages_par_instal[$row['capteur_instalation']] = $row['count'];
}

// 7. Passages par fournisseur (en fonction des capteurs associés)
$passages_par_fournisseur_query = pg_query($conn, "SELECT fournisseur_id, COUNT(*) 
                                                  FROM passages 
                                                  JOIN capteurs ON passages.capteur_id = capteurs.capteur_id 
                                                  GROUP BY fournisseur_id");
$passages_par_fournisseur = [];
while ($row = pg_fetch_assoc($passages_par_fournisseur_query)) {
    $passages_par_fournisseur[$row['fournisseur_id']] = $row['count'];
}

// 8. Passages par capteurs hors service
$passages_par_hs_query = pg_query($conn, "SELECT capteurs.capteur_id, COUNT(*) 
                                          FROM passages 
                                          JOIN capteurs ON passages.capteur_id = capteurs.capteur_id 
                                          WHERE capteur_hs = TRUE 
                                          GROUP BY capteurs.capteur_id");
$passages_par_hs = [];
while ($row = pg_fetch_assoc($passages_par_hs_query)) {
    $passages_par_hs[$row['capteur_id']] = $row['count'];
}

// 9. Passages par centrale
$passages_par_centrale_query = pg_query($conn, "SELECT centrale_id, COUNT(*) 
                                               FROM passages 
                                               JOIN capteurs ON passages.capteur_id = capteurs.capteur_id 
                                               GROUP BY centrale_id");
$passages_par_centrale = [];
while ($row = pg_fetch_assoc($passages_par_centrale_query)) {
    $passages_par_centrale[$row['centrale_id']] = $row['count'];
}

// Affichage de la section passages
echo '<section>';
echo '<h2>Statistiques des Passages</h2>';
echo '<div class="stats-container">';

// 1. Carte pour le total des passages
echo '<div class="stat-card">';
echo '<h3 class="stat-title">Total Passages</h3>';
echo '<p class="stat-value">' . $total_passages . '</p>';
echo '</div>';

// 2. Carte pour les passages par capteur
foreach ($passages_par_capteur as $capteur_id => $count) {
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Passages - Capteur ' . $capteur_id . '</h3>';
    echo '<p class="stat-value">' . $count . '</p>';
    echo '</div>';
}

// 3. Carte pour les passages par date
foreach ($passages_par_date as $date => $count) {
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Passages - Date ' . $date . '</h3>';
    echo '<p class="stat-value">' . $count . '</p>';
    echo '</div>';
}

// 4. Carte pour la durée moyenne des passages
echo '<div class="stat-card">';
echo '<h3 class="stat-title">Durée moyenne des passages</h3>';
echo '<p class="stat-value">' . round($avg_duree_passages, 2) . ' secondes</p>';
echo '</div>';

// 5. Carte pour les passages par type de capteur
foreach ($passages_par_type_capteur as $type => $count) {
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Passages - Type Capteur ' . $type . '</h3>';
    echo '<p class="stat-value">' . $count . '</p>';
    echo '</div>';
}

// 6. Carte pour les passages par installation
foreach ($passages_par_instal as $instal => $count) {
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Passages - Installation ' . $instal . '</h3>';
    echo '<p class="stat-value">' . $count . '</p>';
    echo '</div>';
}

// 7. Carte pour les passages par fournisseur
foreach ($passages_par_fournisseur as $fournisseur_id => $count) {
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Passages - Fournisseur ' . $fournisseur_id . '</h3>';
    echo '<p class="stat-value">' . $count . '</p>';
    echo '</div>';
}

// 8. Carte pour les passages par capteurs hors service
foreach ($passages_par_hs as $capteur_id => $count) {
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Passages - Capteur HS ' . $capteur_id . '</h3>';
    echo '<p class="stat-value">' . $count . '</p>';
    echo '</div>';
}

// 9. Carte pour les passages par centrale
foreach ($passages_par_centrale as $centrale_id => $count) {
    echo '<div class="stat-card">';
    echo '<h3 class="stat-title">Passages - Centrale ' . $centrale_id . '</h3>';
    echo '<p class="stat-value">' . $count . '</p>';
    echo '</div>';
}

echo '</div>'; // Fermeture du conteneur de cartes
echo '</section>';

    // Fermeture de la connexion à la base de données
    pg_close($conn);
    
    require_once 'include/footer.inc.php';
    ?>
    </body>
</html>
