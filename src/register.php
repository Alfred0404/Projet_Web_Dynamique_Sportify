<?php
session_start();
include "db_connection.php";

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function enregistrer_user($conn, $table, $nom, $prenom, $sexe, $email, $motdepasse) {
    $sql = "INSERT INTO $table (nom_$table, prenom_$table, sexe_$table, email_$table, mdp_$table) VALUES ('$nom', '$prenom', '$sexe', '$email', '$motdepasse')";
    if ($conn->query($sql) === TRUE) {
        // Enregistrement dans la table users
        $unique_id = rand(time(), 100000000);
        $fname = $table;
        $img = "image_coach/defaut.jpg";
        $status = "En ligne";

        $user_sql = "INSERT INTO users (unique_id, fname, lname, email, password, img, status) VALUES ('$unique_id', '$fname', '$nom', '$email', '$motdepasse', '$img', '$status')";
        if ($conn->query($user_sql) === TRUE) {
            header("Location: index.php?message=Inscription réussie. Vous pouvez maintenant vous connecter.");
            exit();
        } else {
            return "Erreur lors de l'inscription dans la table users: " . $user_sql . "<br>" . $conn->error;
        }
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
        $register_message = enregistrer_user($conn, 'admin', $nom, $prenom, $sexe, $email, $password);
    } elseif ($role == 'coach') {
        $register_message = enregistrer_user($conn, 'coach', $nom, $prenom, $sexe, $email, $password);
    } elseif ($role == 'client') {
        $register_message = enregistrer_user($conn, 'client', $nom, $prenom, $sexe, $email, $password);
    }
}