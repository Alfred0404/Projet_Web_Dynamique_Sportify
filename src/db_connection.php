<?php
$user_name = "root";
$password = "";
$database = "sportify";
$server = "127.0.0.1";
$port = 3301;

$conn = mysqli_connect($server, $user_name, $password, $database, $port);


if (!$conn) {
    $port = 3306;
    $conn = mysqli_connect($server, $user_name, $password, $database, $port);

}

if (!mysqli_select_db($conn, $database)) {
    die("Database selection failed: " . mysqli_error($conn));
}
