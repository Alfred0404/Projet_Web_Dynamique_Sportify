<?php
// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sportify";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer les activités de type 'activite'
$sql = "SELECT id_activites, nom_activites FROM activites WHERE type = 'sport de competition'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sport de compétition</title>
</head>
<body>
    <h1>Sport de compétition</h1>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            // Afficher chaque activité
            while($row = $result->fetch_assoc()) {
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
            while($row_coach = $result_coach->fetch_assoc()) {
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
</html>
