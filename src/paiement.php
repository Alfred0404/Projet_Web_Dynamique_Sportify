<?php
session_start();
// Inclure le fichier de connexion à la base de données
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs des champs de formulaire
    $type_carte = $_POST['type_carte'];
    $numero_carte = $_POST['numero_carte'];
    $nom_carte = $_POST['nom_carte'];
    $date_expiration = $_POST['date_expiration'];
    $code = $_POST['code'];
    $id_client = $_SESSION['user_id']; // Récupérer l'ID du client à partir de la session

    // Valeurs de la facture et de la date de paiement
    $facture = 50.00; // Valeur de la facture fixée à 50 euros
    $date_paiement = date('Y-m-d H:i:s'); // Date et heure actuelles

    // Requête SQL pour insérer les informations de la carte dans la table paiement
    $sql = "INSERT INTO paiement (type_carte, numero_carte, nom_carte, date_expiration, code, id_client, facture, date_paiement) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("ssssssds", $type_carte, $numero_carte, $nom_carte, $date_expiration, $code, $id_client, $facture, $date_paiement);
    $stmt->execute();
    $stmt->close();

    // Récupérer les informations du rendez-vous depuis la base de données
    $unique_id_client = $_SESSION['unique_id']; // ID du client récupéré de la session
    $unique_id_coach = $_SESSION['unique_id_coach']; // ID du coach récupéré de la session

    // Récupérer le dernier rendez-vous enregistré dans la base de données
    $sql_rdv = "SELECT jour_rdv, heure_rdv FROM prise_de_rendez_vous ORDER BY id_rdv DESC LIMIT 1";
    $stmt = $conn->prepare($sql_rdv);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Mettre à jour les valeurs de jour_rdv et heure_rdv
        $jour_rdv = $row['jour_rdv'];
        $heure_rdv = $row['heure_rdv'];
    } else {
        // Gérer le cas où aucun rendez-vous n'est trouvé
        $jour_rdv = "date inconnue";
        $heure_rdv = "heure inconnue";
    }

    // Construire le message avec les nouvelles valeurs de jour_rdv et heure_rdv
    $message = "Votre rendez-vous avec moi a bien été validé pour le $jour_rdv à $heure_rdv."; // Message automatique

    // Insérer le message dans la table messages
    $sql = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("iis", $unique_id_client, $unique_id_coach, $message);
    $stmt->execute();
    $stmt->close();


    // Insérer un message automatique dans la table messages

    // Redirection vers la page d'accueil
    header("Location: rendez_vous.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/paiement.css">
    <title>Paiement</title>
</head>

<body>
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
    </header>
    <div class="nav">
        <ul>
            <li class="nav-item"><a class="text-decoration-none" href="accueil.php">Accueil</a></li>
            <li class="nav-item"><a class="text-decoration-none" href="parcourir.php">Tout parcourir</a></li>
            <li class="nav-item"><a class="text-decoration-none" href="recherche.php">Rechercher</a></li>
            <li class="nav-item"><a class="text-decoration-none" href="rendez_vous.php">Rendez-vous</a></li>
            <li class="nav-item active"><a class="text-decoration-none" href="#">Votre compte</a></li>
            <li class="nav-item"><a href="users.php">Discussions</a></li>
            <li class="nav-item"><a class="text-decoration-none" href="logout.php">Déconnexion</a></li>
        </ul>
    </div>
    <section>
        <div class="paiement-container">
            <h1>Vos informations bancaires</h1>

            <!-- Formulaire pour saisir les informations de la carte -->
            <form method="post" class="paiement-form">
                <label for="type_carte">Type de carte :</label>
                <input type="text" id="type_carte" name="type_carte" required><br>

                <label for="numero_carte">Numéro de carte :</label>
                <input type="text" id="numero_carte" name="numero_carte" required><br>

                <label for="nom_carte">Nom sur la carte :</label>
                <input type="text" id="nom_carte" name="nom_carte" required><br>

                <label for="date_expiration">Date d'expiration :</label>
                <input type="month" id="date_expiration" name="date_expiration" required><br>

                <label for="code">Code de sécurité :</label>
                <input type="text" id="code" name="code" required><br>

                <button type="submit">Valider le paiement</button>
            </form>
        </div>
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