<?php
session_start();

// Vérifier si l'utilisateur est connecté
if(isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $username = $_SESSION['user_name'];
    header("Location: accueil.php");
    exit();
}
// Si l'utilisateur n'est pas connecté, le rediriger vers la page de connexion
else {
    header("Location: index.php");
    exit();
}