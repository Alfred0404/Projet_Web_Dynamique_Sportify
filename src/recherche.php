<?php
session_start();
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Fonction pour valider et sécuriser les entrées utilisateur
function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialiser les variables pour les résultats de recherche
$search_result = null;
$search_error = "";

// Effectuer une recherche lorsque le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_query = validate($_POST['search_query']);
    $search_query = strtolower(str_replace(" ", "_", $search_query));

    if (empty($search_query)) {
        $search_error = "Veuillez entrer un nom, une spécialité ou un type d'activité pour la recherche.";
    } else {
        // Rechercher uniquement dans la table coach
        $sql_coach = "
            SELECT 'coach' AS role, id_coach, nom_coach AS nom, prenom_coach AS prenom, email_coach AS email, bureau_coach AS bureau, specialite_coach AS specialite, photo_coach AS photo, telephone_coach AS telephone
            FROM coach
            WHERE nom_coach LIKE ? OR specialite_coach LIKE ?";

        $stmt_coach = $conn->prepare($sql_coach);
        if ($stmt_coach === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $like_query_coach = "%" . $search_query . "%";
        $stmt_coach->bind_param("ss", $like_query_coach, $like_query_coach);
        $stmt_coach->execute();
        $result_coach = $stmt_coach->get_result();
        if ($result_coach->num_rows > 0) {
            $search_result = $result_coach->fetch_all(MYSQLI_ASSOC);
        } else {
            // Si la recherche dans la table coach ne donne aucun résultat,
            // rechercher dans la table activites
            $sql_activite = "
                SELECT 'activite' AS role, id_activites AS id, nom_activites AS nom, type_activites AS type
                FROM activites
                WHERE nom_activites LIKE ? OR type_activites = ?";

            $stmt_activite = $conn->prepare($sql_activite);
            if ($stmt_activite === false) {
                die("Erreur de préparation de la requête : " . $conn->error);
            }
            $like_query_activite = "%" . $search_query . "%";
            $stmt_activite->bind_param("ss", $like_query_activite, $search_query);
            $stmt_activite->execute();
            $result_activite = $stmt_activite->get_result();
            if ($result_activite->num_rows > 0) {
                $search_result = $result_activite->fetch_all(MYSQLI_ASSOC);
            } else {
                $search_error = "Aucun résultat trouvé pour : " . htmlspecialchars($search_query);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/recherche.js"></script>
    <link rel="stylesheet" href="css/recherche.css">
    <title>Recherche</title>
</head>

<body>
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
    </header>

    <div class="nav">
        <ul>
            <li class="nav-item"><a href="accueil.php">Accueil</a></li>
            <li class="nav-item"><a href="parcourir.php">Tout parcourir</a></li>
            <li class="nav-item active"><a href="#">Rechercher</a></li>
            <li class="nav-item"><a href="rendez_vous.php">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
            <li class="nav-item"><a href="users.php">Discussions</a></li>
            <li class="nav-item"><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>
    <section>
        <!-- Formulaire de recherche -->
        <form method="post" action="recherche.php" class="search">
            <input class="search-field" type="text" name="search_query"
                placeholder="Entrez un nom, une spécialité ou un type d'activité" required>
            <button class="search-submit" type="submit" name="search">Rechercher</button>
        </form>

        <!-- Affichage des résultats de recherche -->
        <?php if (!empty($search_result)): ?>
            <div class="results">
                <?php foreach ($search_result as $item): ?>
                    <div class="result-item">
                        <?php if ($item['role'] === 'coach'): ?>
                            <div class="infos-coach">
                                <p><strong>Rôle :</strong> Coach</p>
                                <p><strong>Nom :</strong> <?php echo htmlspecialchars($item['nom']); ?></p>
                                <p><strong>Prénom :</strong> <?php echo htmlspecialchars($item['prenom']); ?></p>
                                <p><strong>Email :</strong> <?php echo htmlspecialchars($item['email']); ?></p>
                                <p><strong>Bureau :</strong> <?php echo htmlspecialchars($item['bureau']); ?></p>
                                <p><strong>Spécialité :</strong> <?php echo htmlspecialchars($item['specialite']); ?></p>
                                <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($item['telephone']); ?></p>
                            </div>
                            <div class="photo-coach">
                                <?php if (!empty($item['photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['photo']); ?>" alt="Photo du coach">
                                <?php endif; ?>
                            </div>
                            <div class="coach-link">
                                <a href="coach_details.php?id=<?php echo $item['id_coach']; ?>">Voir le profil</a>
                            </div>
                        <?php elseif ($item['role'] === 'activite'): ?>
                            <ul class="liste-activites">
                                <?php
                                echo "<li class='card " . $item["nom"] . " " . $item["type"] . "'><a href='?id=" . $item["id"] . "'>" . ucfirst(str_replace("_", " ", $item["nom"])) . "</a></li>";
                                ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($search_error)): ?>
            <p><?php echo $search_error; ?></p>
        <?php endif; ?>
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
                echo "<h1>Coachs Responsables</h1>";
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

</html>