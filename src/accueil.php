<!-- 
<?php
$user_name = "root";
$password = "";
$database = "test1";
$server = "127.0.0.1";
$port = 3306;

$conn = mysqli_connect($server, $user_name, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "<p>Connection to the Server opened successfully</p>";

// Optionnel : sélection de la base de données
if (!mysqli_select_db($conn, $database)) {
    die("Database selection failed: " . mysqli_error($conn));
}
// echo "Database selected successfully";

// Requête SQL pour sélectionner toutes les lignes de la table utilisateurs
// $sql = "SELECT id, titre, auteur FROM livres";
// $result = mysqli_query($conn, $sql);

// if (mysqli_num_rows($result) > 0) {
//     // Affichage des résultats
//     while ($row = mysqli_fetch_assoc($result)) {
//         echo "ID: " . $row["id"] . " - titre: " . $row["titre"] . " - auteur: " . $row["auteur"] . "<br>";
//     }
// } else {
//     echo "0 résultats";
// }
?> -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/accueil.css">
    <title>Sportify - Accueil</title>
</head>
<body>
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
    </header>
    <div class="nav">
        <ul>
            <li class="nav-item active"><a href="#">Accueil</a></li>
            <li class="nav-item"><a href="parcourir.php">Tout parcourir</a></li>
            <li class="nav-item"><a href="recherche.php">Rechercher</a></li>
            <li class="nav-item"><a href="rendez_vous.php">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
            <li class="nav-item"><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>
    <section class="first-section">

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
        <p>10 rue Sextius Michel - 75015 - Paris</p>
        <a href="#">Google Maps</a>
    </footer>
</body>
</html>

