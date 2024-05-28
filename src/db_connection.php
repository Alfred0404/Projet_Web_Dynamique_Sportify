<?php
$user_name = "root";
$password = "";
$database = "sportify";
$server = "127.0.0.1";
$port = 3306;

$conn = mysqli_connect($server, $user_name, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "<p>Connection to the Server opened successfully</p>";

// Optionnel : sélection de la base de données
if (!mysqli_select_db($conn, $database)) {
    die("Database selection failed: " . mysqli_error($conn));
}
?>
