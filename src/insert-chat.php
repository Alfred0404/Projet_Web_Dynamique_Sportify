<?php
session_start();
include "db_connection.php";

// si l'utilisateur est connecté
if (isset($_SESSION['unique_id'])) {
    $outgoing_id = $_SESSION['unique_id'];
    echo "" . $outgoing_id;
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    echo "" . $incoming_id;
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    if (!empty($message)) {
        $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg)
                                        VALUES ({$incoming_id}, {$outgoing_id}, '{$message}')") or die();
    }
} else {
    header("location: index.php");
}