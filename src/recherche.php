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
        $search_error = "Veuillez entrer un nom ou une spécialité pour la recherche.";
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
            // Rechercher dans la table activites
            $sql_activite = "
                SELECT 'activite' AS role, nom_activites AS nom, type_activites AS type
                FROM activites
                WHERE nom_activites LIKE ?";
            
            $stmt_activite = $conn->prepare($sql_activite);
            if ($stmt_activite === false) {
                die("Erreur de préparation de la requête : " . $conn->error);
            }
            $like_query_activite = "%" . $search_query . "%";
            $stmt_activite->bind_param("s", $like_query_activite);
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            width: 300px;
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
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
        .result-item {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }
        .result-item img {
            width: 100px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Recherche Coach</h2>
        <form method="post" action="recherche.php">
            <label for="search_query">Nom, Spécialité ou Sport :</label>
            <input type="text" id="search_query" name="search_query" required>
            <button type="submit" name="search">Rechercher</button>
        </form>
        <?php if (!empty($search_error)): ?>
            <p style="color: red;"><?php echo $search_error; ?></p>
        <?php endif; ?>
    </div>
    
    <?php if ($search_result): ?>
        <div class="container">
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
    <?php endif; ?>

</body>
</html>
