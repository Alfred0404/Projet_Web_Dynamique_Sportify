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

if (!isset($_SESSION['user_id'])) {
    echo "Veuillez vous <a href='login.php'>connecter</a> pour prendre un rendez-vous.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_coach = $_POST['id_coach'];
    $date_rdv = $_POST['date_rdv'];

    $sql = "INSERT INTO prise_de_rendez_vous (id_client, id_coach, date_rdv, status) VALUES (?, ?, ?, 'confirmé')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $_SESSION['user_id'], $id_coach, $date_rdv);
    mysqli_stmt_execute($stmt);

    echo "Rendez-vous pris avec succès!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <!-- Contenu du header si nécessaire -->
    </header>

    <div class="nav">
        <ul>
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="parcourir.php">Tout parcourir</a></li>
            <li><a href="recherche.php">Rechercher</a></li>
            <li><a href="#">Rendez-vous</a></li>
            <li><a href="compte.php">Votre compte</a></li>
        </ul>
    </div>

    <section>
        <h1>Prendre un Rendez-vous</h1>
        <form action="rendez_vous.php" method="POST">
            <label for="id_coach">Choisir un coach:</label>
            <select id="id_coach" name="id_coach" required>
                <?php
                // Récupérer la liste des coachs
                $sql = "SELECT * FROM coach";
                $result = mysqli_query($conn, $sql);
                
                while ($coach = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $coach['id_coach'] . '">' . $coach['nom_coach'] . ' ' . $coach['prenom_coach'] . '</option>';
                }
                ?>
            </select><br>
            <label for="date_rdv">Date et heure du rendez-vous:</label>
            <input type="datetime-local" id="date_rdv" name="date_rdv" required><br>
            <button type="submit">Prendre rendez-vous</button>
        </form>
    </section>

    <footer>
        <!-- Contenu du footer si nécessaire -->
    </footer>
</body>

</html>
