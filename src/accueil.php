<?php
session_start();

include "db_connection.php";

$sql = "SELECT id_bulletin, titre_bulletin, contenu_bulletin FROM bulletin";
$result = $conn->query($sql);
?>

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
            <li class="nav-item"><a href="users.php">WhatsApp</a></li>
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
        <ul class="liste-bulletin">
            <?php
            if ($result->num_rows > 0) {
                // Afficher chaque bulletin
                while ($row = $result->fetch_assoc()) {
                    echo "<li class='card'><h2 class='titre-bulletin'>" . $row["titre_bulletin"] . "</h2><p>" . $row["contenu_bulletin"] . "</p></li>";
                }
            } else {
                echo "Aucun bulletin trouvée.";
            }
            ?>
        </ul>
    </section>
    <footer>
        <p>© 2024 Sportify</p>
        <p>sportify@gmail.com</p>
        <p>01 38 67 18 52</p>
        <p>10 rue Sextius Michel - 75015 - Paris</p>
        <a class="lien-gmaps"
            href="https://www.google.fr/maps/place/10+Rue+Sextius+Michel,+75015+Paris/@48.8511413,2.2860178,17z/data=!3m1!4b1!4m6!3m5!1s0x47e67151e3c16d05:0x1e3446766ada1337!8m2!3d48.8511378!4d2.2885927!16s%2Fg%2F11jy_4vh_c?entry=ttu">Google
            Maps</a>
    </footer>
</body>

</html>