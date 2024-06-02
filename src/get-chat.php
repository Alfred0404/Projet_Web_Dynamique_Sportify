<?php
session_start();
include "db_connection.php";

// si l'utilsiateur est connecté
if (isset($_SESSION['unique_id'])) {
    // on récupère l'id des utilisateurs (destinateur et destinataire)
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $output = "";

    // on récupère les messages entre les deux utilisateurs
    $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
    $query = mysqli_query($conn, $sql);

    // si on a des messages
    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            // si le message est sortant
            if ($row['outgoing_msg_id'] === $incoming_id) {
                $output .= '<div class="chat incoming">
                                <div class="details">
                                    <p>' . $row['msg'] . '</p>
                                </div>
                                </div>';
            }

            // si le message est entrant
            else {
                $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>' . $row['msg'] . '</p>
                                </div>
                                </div>';
            }
        }
    } else {
        $output .= '<div class="text">Aucun message.</div>';
    }
    echo $output;
} else {
    header("location: index.php");
}