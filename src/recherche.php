<?php
session_start();
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Fonction pour valider et sécuriser les entrées utilisateur
function validate($data) {
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

    if (empty($search_query)) {
        $search_error = "Veuillez entrer un nom, une spécialité ou un type d'activité pour la recherche.";
    } else {
        // Rechercher uniquement dans la table coach
        $sql_coach = "
            SELECT 'coach' AS role, nom_coach AS nom, prenom_coach AS prenom, email_coach AS email, bureau_coach AS bureau, specialite_coach AS specialite, photo_coach AS photo, telephone_coach AS telephone
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
                SELECT 'activite' AS role, nom_activites AS nom, type_activites AS type
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
    <title>Recherche</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        input[type="text"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #5cb85c;
            border: none;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }

        .results {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .result-item {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        h2 {
            margin-bottom: 20px;
        }

        strong {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Formulaire de recherche -->
    <form method="post" action="recherche.php">
        <input type="text" name="search_query" placeholder="Entrez un nom, une spécialité ou un type d'activité" required>
        <button type="submit" name="search">Rechercher</button>
    </form>

    <!-- Affichage des résultats de recherche -->
    <?php if (!empty($search_result)): ?>
        <div class="results">
            <h2>Résultats de recherche</h2>
            <?php foreach ($search_result as $item): ?>
                <div class="result-item">
                    <?php if ($item['role'] === 'coach'): ?>
                        <p><strong>Rôle:</strong> Coach</p>
                        <p><strong>Nom:</strong> <?php echo htmlspecialchars($item['nom']); ?></p>
                        <p><strong>Prénom:</strong> <?php echo htmlspecialchars($item['prenom']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($item['email']); ?></p>
                        <p><strong>Bureau:</strong> <?php echo htmlspecialchars($item['bureau']); ?></p>
                        <p><strong>Spécialité:</strong> <?php echo htmlspecialchars($item['specialite']); ?></p>
                        <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($item['telephone']); ?></p>
                        <?php if (!empty($item['photo'])): ?>
                            <p><strong>Photo:</strong> <img src="<?php echo htmlspecialchars($item['photo']); ?>" alt="Photo du coach"></p>
                        <?php endif; ?>
                    <?php elseif ($item['role'] === 'activite'): ?>
                        <p><strong>Rôle:</strong> Activité</p>
                        <p><strong>Nom:</strong> <?php echo htmlspecialchars($item['nom']); ?></p>
                        <p><strong>Type d'activité:</strong> <?php echo htmlspecialchars($item['type']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (!empty($search_error)): ?>
        <p><?php echo $search_error; ?></p>
    <?php endif; ?>
</body>

</html>
