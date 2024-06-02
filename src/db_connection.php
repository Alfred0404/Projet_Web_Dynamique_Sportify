<?php
$user_name = "root";
$password = "";
$database = "sportify";
$server = "127.0.0.1";
$port = 3306;

// on essaie de se connecter à la base de données avec le port 3301 (fonctionne sur le pc d'Alfred)
$conn = mysqli_connect($server, $user_name, $password, $database, $port);

// si la connexion échoue, on essaie de se connecter à la base de données avec le port 3306 (fonctionne sur le pc des autres membres du groupe)
if (!$conn) {
    $port = 3306;
    $conn = mysqli_connect($server, $user_name, $password, $database, $port);
}

// si la connexion échoue, on affiche un message d'erreur
if (!mysqli_select_db($conn, $database)) {
    die("Database selection failed: " . mysqli_error($conn));
}
