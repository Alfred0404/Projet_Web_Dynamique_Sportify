<?php
session_start();
include "db_connection.php";

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function registerUser($conn, $table, $nom, $prenom, $sexe, $email, $motdepasse) {
    $sql = "INSERT INTO $table (nom_$table, prenom_$table, sexe_$table, email_$table, mdp_$table) VALUES ('$nom', '$prenom', '$sexe', '$email', '$motdepasse')";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?message=Inscription réussie. Vous pouvez maintenant vous connecter.");
        exit();
    } else {
        return "Erreur: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_role'])) {
    $role = $_POST['register_role'];
    $nom = validate($_POST['name']);
    $prenom = validate($_POST['prenom']);
    $sexe = validate($_POST['sexe']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    if ($role == 'admin') {
        $register_message = registerUser($conn, 'admin', $nom, $prenom, $sexe, $email, $password);
    } elseif ($role == 'coach') {
        $register_message = registerUser($conn, 'coach', $nom, $prenom, $sexe, $email, $password);
    } elseif ($role == 'client') {
        $register_message = registerUser($conn, 'client', $nom, $prenom, $sexe, $email, $password);
    }
}
?>
