<?php
session_start();
session_destroy(); // Détruit la session
header("Location: login.php");
exit();
?>