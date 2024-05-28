<?php
session_start();

if(isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $username = $_SESSION['user_name'];
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>HOME</title>
    </head>
    <body>
        <h1>Bienvenue, <?php echo htmlspecialchars($username); ?>!</h1>
        <h2>Vous êtes connecté en tant que <?php echo htmlspecialchars($role); ?>.</h2>
        <a href="logout.php">Déconnexion</a>
    </body>
    </html>

    <?php
} else {
    header("Location: index.php");
    exit();
}
?>
