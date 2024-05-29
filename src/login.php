<?php
session_start();
include "db_connection.php";

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_role'])) {
    $role = $_POST['login_role'];
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);

    if ($role == 'admin') {
        $table = 'admin';
    } elseif ($role == 'coach') {
        $table = 'coach';
    } elseif ($role == 'client') {
        $table = 'client';
    }

    $sql = "SELECT * FROM $table WHERE nom_$table='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password == $row['mdp_' . $table]) {
            $_SESSION['role'] = $role;
            $_SESSION['user_name'] = $username;
            header("Location: home.php");
            exit();
        } else {
            $login_message = "Mot de passe incorrect.";
            header("Location: index.php?message=$login_message");
            exit();
        }
    } else {
        $login_message = "Aucun compte trouvé avec ce nom d'utilisateur.";
        header("Location: index.php?message=$login_message");
        exit();
    }
}
?>
