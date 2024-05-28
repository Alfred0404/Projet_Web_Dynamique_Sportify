<?php
session_start();
$user_name = "root";
$password = "";
$database = "sportify"; // Utilisez la base de données correcte
$server = "127.0.0.1";
$port = 3306;

$conn = mysqli_connect($server, $user_name, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    $sql = "SELECT * FROM client WHERE email_client = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && $mdp === $user['mdp_client']) {
        $_SESSION['user_id'] = $user['id_client'];
        $_SESSION['user_name'] = $user['nom_client'];
        header("Location: accueil.php");
        exit();
    } else {
        echo "Email ou mot de passe incorrect.";
    }
} else {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connexion</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <h1>Connexion</h1>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>
            <label for="mdp">Mot de passe:</label>
            <input type="password" id="mdp" name="mdp" required><br>
            <button type="submit">Se connecter</button>
        </form>
    </body>
    </html>';
}
?>
