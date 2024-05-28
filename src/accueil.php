<?php
session_start();
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/accueil.css">
    <title>Accueil</title>
</head>

<body>
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
        <p>Bienvenue, <?php echo htmlspecialchars($username); ?>!</p>
        <p>Vous êtes connecté en tant que <?php echo htmlspecialchars($role); ?>.</p>
        <a href="logout.php">Déconnexion</a>
    </header>

    <div class="nav">
        <ul>
            <li class="nav-item"><a href="#">Accueil</a></li>
            <li class="nav-item"><a href="parcourir.php">Tout parcourir</a></li>
            <li class="nav-item"><a href="recherche.php">Rechercher</a></li>
            <li class="nav-item"><a href="rendez_vous.php">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
        </ul>
    </div>
    <section class="">
        <div class="section-content">
            <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
            <h1>Sportify</h1>
            <p>La solution de mise en relation avec des professionnels du sport</p>
        </div>
    </section>
    <section class="bulletin">
        <h1>Bulletin sportif de la semaine</h1>
    </section>
    <footer>
        <p>© 2024 Sportify</p>
        <p>sportify@gmail.com</p>
        <p>01 38 67 18 52</p>
        <p>12 rue de la bite - 75015 - Paris</p>
        <a href="#">Google Maps</a>
    </footer>
</body>

</html>
