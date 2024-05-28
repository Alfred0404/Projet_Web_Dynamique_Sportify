<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre compte</title>
</head>
<body>
    <header></header>
    <div class="nav">
        <ul>
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="parcourir.php">Tout parcourir</a></li>
            <li><a href="recherche.php">Rechercher</a></li>
            <li><a href="rendez_vous.php">Rendez-vous</a></li>
            <li><a href="#">Votre compte</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>
    <section>
        <h2>Informations du compte</h2>
        <p>Nom : <?php echo $_SESSION['user_name']; ?></p>
        <p>Prénom : <?php echo $_SESSION['prenom']; ?></p>
        <p>Email : <?php echo $_SESSION['email']; ?></p>
        <p>Rôle : <?php echo $_SESSION['role']; ?></p>
    </section>
    <footer></footer>
</body>
</html>
