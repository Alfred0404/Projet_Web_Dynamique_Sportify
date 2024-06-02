<?php
session_start();

if(isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $username = $_SESSION['user_name'];
    header("Location: accueil.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}