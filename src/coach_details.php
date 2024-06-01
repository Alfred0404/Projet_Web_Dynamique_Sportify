<?php
session_start();

if (!isset($_SESSION['role']) || !isset($_SESSION['nom'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];

if (isset($_GET['logout'])) {
    $id_coach = $_GET['id'];
    // Enregistrer le contenu de la discussion dans la base de données
    saveChatToDatabase($id_coach, $id_client);
    session_destroy();
    sleep(1);
    header("Location: coach_details.php?id=" . $id_coach); // Rediriger l'utilisateur avec l'identifiant du coach
    exit();
}

// Vérifier si un identifiant de coach est passé en paramètre
if (isset($_GET['id'])) {
    // Récupérer l'identifiant du coach depuis l'URL
    $id_coach = intval($_GET['id']);

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Sportify";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connexion échouée: " . $conn->connect_error);
    }

    // Récupérer les informations du coach
    $sql = "SELECT nom_coach, prenom_coach, email_coach, cv_coach, bureau_coach, photo_coach FROM coach WHERE id_coach = $id_coach";
    $result = $conn->query($sql);

    // Vérifier si le coach existe
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nom_coach = $row["nom_coach"];
        $prenom_coach = $row["prenom_coach"];
        $email_coach = $row["email_coach"];
        $cv_coach = $row["cv_coach"];
        $bureau_coach = $row["bureau_coach"];
        $photo_coach = $row["photo_coach"];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du Coach</title>
    <link rel="stylesheet" href="../src/css/coach_details.css" />
</head>
<body>
    <h1>Détails du Coach</h1>
    <p>Nom: <?php echo $nom_coach; ?></p>
    <p>Prénom: <?php echo $prenom_coach; ?></p>
    <p>Email: <?php echo $email_coach; ?></p>
    <p>Bureau: <?php echo $bureau_coach; ?></p>
    <p>CV: <?php echo $cv_coach; ?></p>

    <!-- Afficher la photo -->
    <?php if (!empty($photo_coach)) { ?>
    <img src="<?php echo $photo_coach; ?>" alt="Photo du Coach">
    <?php } ?>

    <?php
    // Vérifier les conditions d'accès à la chatroom
    if ($role != 'client') {
        echo "<p>Vous n'avez pas accès à cette chatroom.</p>";
    }
?>
</body>
</html>

<?php
    } else {
        echo "Aucun coach trouvé avec cet identifiant.";
    }

    // Fermer la connexion
    $conn->close();
} else {
    echo "Identifiant de coach non spécifié.";
}

// Fonction pour enregistrer le contenu de la discussion dans la base de données
function saveChatToDatabase($id_coach, $id_client) {
    // Votre code de connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Sportify";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connexion échouée: " . $conn->connect_error);
    }

    // Récupérer le contenu de la discussion
    $log_file = "log.html";
    if (file_exists($log_file)) {
        $chat_content = file_get_contents($log_file);

        // Enregistrer le contenu de la discussion dans la base de données
        $stmt = $conn->prepare("INSERT INTO chat_conversations (id_client, id_coach, chat_file) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_client, $id_coach, $chat_content);
        $stmt->execute();
        $stmt->close();
    }

    // Fermer la connexion
    $conn->close();
}
?>
