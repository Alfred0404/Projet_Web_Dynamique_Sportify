<?php
session_start();
include "db_connection.php";

// Fonction pour valider et sécuriser les entrées utilisateur
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Connexion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_role'])) {
    $role = validate($_POST['login_role']);
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);

    $table = $role;
    $sql = "SELECT * FROM $table WHERE nom_$table = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($password == $row['mdp_' . $table]) {
            $_SESSION['user_id'] = $row['id_' . $table]; // Stocker l'ID de l'utilisateur dans la session
            $_SESSION['user_name'] = $row['nom_' . $table];
            $_SESSION['prenom'] = $row['prenom_' . $table];
            $_SESSION['email'] = $row['email_' . $table];
            $_SESSION['role'] = $role;
            header("Location: accueil.php");
            exit();
        } else {
            echo "Mot de passe incorrect.";
            exit();
        }
    } else {
        echo "Nom d'utilisateur incorrect.";
        exit();
    }
}