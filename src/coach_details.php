<?php
// Vérifier si un identifiant de coach est passé en paramètre
if (isset($_GET['id'])) {
    // Récupérer l'identifiant du coach depuis l'URL
    $id_coach = intval($_GET['id']);

    // Connexion à la base de données
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
        </head>

        <body>
            <h1>Détails du Coach</h1>
            <p>Nom: <?php echo $nom_coach; ?></p>
            <p>Prénom: <?php echo $prenom_coach; ?></p>
            <p>Email: <?php echo $email_coach; ?></p>
            <p>Bureau: <?php echo $bureau_coach; ?></p>
            <p>CV: <?php echo $cv_coach; ?></p>
            <!-- Afficher le CV -->
            <?php //if(!empty($cv_coach)) { ?>
            <a href="<?php echo $cv_coach; ?>" target="_blank">CV</a>
            <?php //} ?>
            <!-- Afficher la photo -->
            <?php if (!empty($photo_coach)) { ?>
                <img src="<?php echo $photo_coach; ?>" alt="Photo du Coach">
            <?php } ?>
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
?>