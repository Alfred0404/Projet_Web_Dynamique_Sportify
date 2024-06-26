<?php
session_start();

include "db_connection.php";
// echo "<p>Connection to the Server opened successfully</p>";

// Optionnel : sélection de la base de données
if (!mysqli_select_db($conn, $database)) {
    die("Database selection failed: " . mysqli_error($conn));
}
// echo "Database selected successfully";

// Requête SQL pour sélectionner toutes les lignes de la table `utilisateurs`
$sql = "SELECT id_client, nom_client, prenom_client, sexe_client, date_de_naissance, mdp_client, email_client, num_telephone, profession FROM client";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Affichage des résultats
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row["id_client"] . "<br>" . "nom: " . $row["nom_client"] . "<br>" . "prenom: " . "<br>" . $row["prenom_client"] . "<br>" . "date naissance: " . "<br>" . $row["date_de_naissance"] . "<br>";
    }
} else {
    echo "0 résultats";
}