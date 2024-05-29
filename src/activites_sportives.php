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
$sql = "SELECT id_activites, nom_activites FROM activites";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/activites_sportives.css">
    <script src="js/activites_sportives.js"></script>
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
        <form class="btns-activite" action="traitement.php" method="post">
            <button type="submit" name="button_parcourir" value="activite_sportive">
                <p>Activités sportives</p>
            </button>
            <button type="submit" name="button_parcourir" value="les_sports_de_competition">
                <p>Les Sports de Compétition</p>
            </button>
            <button type="submit" name="button_parcourir" value="salles_de_sport_omnes">
                <p>Salles de sport Omnes</p>
            </button>
        </form>

        <ul class="liste-activites">
            <?php
            if ($result->num_rows > 0) {
                // Afficher chaque activité
                while ($row = $result->fetch_assoc()) {
                    echo "<li class='card " . $row["nom_activites"] . "'><a href='?id=" . $row["id_activites"] . "'>" . ucfirst(str_replace("_", " ", $row["nom_activites"])) . "</a></li>";
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
        <a href="#">Google Maps</a>
    </footer>
</body>
<!--
</html>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Activités sportives</title>
</head>

<body>
    <h1>Activités sportives</h1>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            // Afficher chaque activité
            while ($row = $result->fetch_assoc()) {
                echo "<li><a href='?id=" . $row["id_activites"] . "'>" . ucfirst($row["nom_activites"]) . "</a></li>";
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

        // Requête pour trouver le coach responsable
        $sql_coach = "SELECT coach.id_coach, coach.nom_coach FROM coach
                      INNER JOIN activites ON coach.specialite_coach = activites.id_activites
                      WHERE activites.id_activites = $id_activite";
        $result_coach = $conn->query($sql_coach);

        if ($result_coach->num_rows > 0) {
            echo "<h2>Coach Responsable</h2>";
            while ($row_coach = $result_coach->fetch_assoc()) {
                echo "<li><a href='coach_details.php?id=" . $row_coach["id_coach"] . "'>" . $row_coach["nom_coach"] . "</a></li>";
            }
        } else {
            echo "<p>Aucun coach responsable trouvé pour cette activité.</p>";
        }
    }


    // Fermer la connexion
    $conn->close();
    ?>
</body>

</html> -->