<?php
$user_name = "root";
$password = "";
$database = "sportify";
$server = "127.0.0.1";
$port = 3301;

$conn = mysqli_connect($server, $user_name, $password, $database, $port);

if (!$conn) {
    echo "le port 3301 ne marche pas";
    $port = 3306;
    $conn = mysqli_connect($server, $user_name, $password, $database, $port);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
}

// Récupérer les activités
$sql = "SELECT id_activites, nom_activites, type_activites FROM activites";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/parcourir.css">
    <script src="js/parcourir.js"></script>
    <title>Sportify - Parcourir</title>
</head>

<body>
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
    </header>

    </div>
    <div class="nav">
        <ul>
            <li class="nav-item"><a href="accueil.php">Accueil</a></li>
            <li class="nav-item active"><a href="#">Tout parcourir</a></li>
            <li class="nav-item"><a href="recherche.php">Rechercher</a></li>
            <li class="nav-item"><a href="rendez_vous.php">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
        </ul>
    </div>
    <section class="first-section">
        <div class="section-content">
            <h1>Tout parcourir</h1>
        </div>
    </section>
    <section class="services">
        <h1>Nos services</h1>
        <div class="btns-activite">
            <button class="bouton-activite" id="activite_sportive" onclick="sort_activities()">
                <p>Activités sportives</p>
            </button>
            <button class="bouton-activite" id="sport_de_competition" onclick="sort_activities()">
                <p>Les Sports de Compétition</p>
            </button>
            <button class="bouton-activite" id="salle_de_sport_omnes" onclick="sort_activities()">
                <p>Salles de sport Omnes</p>
            </button>
        </div>

        <ul class="liste-activites">
            <?php
            if ($result->num_rows > 0) {
                // Afficher chaque activité
                while ($row = $result->fetch_assoc()) {
                    echo "<li class='card " . $row["nom_activites"] . " " . $row["type_activites"] . "'><a href='?id=" . $row["id_activites"] . "'>" . ucfirst(str_replace("_", " ", $row["nom_activites"])) . "</a></li>";
                }
            } else {
                echo "Aucune activité trouvée.";
            }
            ?>
        </ul>
        <?php
        // Afficher le coach responsable si une activité est sélectionnée
        if (isset($_GET['id'])) {
            $id_activite = intval($_GET['id']);
            // echo "activite selectionnee : ".$id_activite;

            // Requête pour trouver le coach responsable
            $sql_coach = "SELECT coach.id_coach, coach.nom_coach FROM coach
                      INNER JOIN activites ON coach.specialite_coach = activites.id_activites
                      WHERE activites.id_activites = $id_activite";
            $result_coach = $conn->query($sql_coach);

            if ($result_coach->num_rows > 0) {
                echo "<h1>Coach Responsable</h1>";
                echo '<ul class="liste-coachs">';
                while ($row_coach = $result_coach->fetch_assoc()) {
                    echo "<li class='coach'><a href='coach_details.php?id=" . $row_coach["id_coach"] . "'>" . ucfirst($row_coach["nom_coach"]) . "</a></li>";

                }
                echo "</ul>";
            } else {
                echo "<p>Aucun coach responsable trouvé pour cette activité.</p>";
            }
        }

        $conn->close();
        ?>
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