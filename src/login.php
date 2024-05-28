<?php
session_start();
include "db_connection.php";

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous que vous avez bien inclus le fichier de connexion à la base de données
    include "db_connection.php";

    // Vérifiez si les données du formulaire ont été soumises via POST
    if(isset($_POST['nom_admin'], $_POST['mdp_admin'])) {
        // Récupérez les données du formulaire et nettoyez-les
        $nom_admin = validate($_POST['nom_admin']);
        $mdp_admin = validate($_POST['mdp_admin']);

        // Vérifiez si les champs requis ne sont pas vides
        if (empty($nom_admin) || empty($mdp_admin)) {
            echo "Nom d'utilisateur et mot de passe sont requis.";
            exit();
        } else {
            // Préparez et exécutez la requête SQL pour vérifier les informations d'identification de l'utilisateur
            $sql = "SELECT * FROM admin WHERE nom_admin = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Erreur de préparation de la requête : " . $conn->error);
            }
            $stmt->bind_param("s", $nom_admin);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                if ($mdp_admin == $row['mdp_admin']) {
                    // Nom d'utilisateur et mot de passe corrects, rediriger vers la page de connexion réussie
                    $_SESSION['nom_admin'] = $row['nom_admin'];
                    header("Location: home.php");
                    exit(); // Assurez-vous qu'aucun contenu HTML n'est envoyé après cette redirection
                } else {
                    echo "Mot de passe incorrect.";
                    exit();
                }
            } else {
                echo "Nom d'utilisateur incorrect.";
                exit();
            }
        }
    } else {
        echo "Nom d'utilisateur et mot de passe sont requis.";
        exit();
    }
}
?>
