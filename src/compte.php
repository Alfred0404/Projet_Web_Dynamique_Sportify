<?php
session_start();
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Récupérer l'ID du client à partir de la base de données en fonction du nom d'utilisateur
$sql = "SELECT id_client FROM client WHERE nom_client = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erreur de préparation de la requête : " . $conn->error);
}
$stmt->bind_param("s", $_SESSION['user_name']); // Supposons que 'user_name' contienne le nom d'utilisateur
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $client_id_from_database = $row['id_client'];
} else {
    echo "Erreur : Aucun client trouvé avec ce nom d'utilisateur.";
}
$stmt->close();

// Stocker l'ID du client dans la session
$_SESSION['user_id'] = $client_id_from_database;

// Vérifier le rôle de l'utilisateur
$is_admin = $_SESSION['role'] === 'admin';
$is_coach = $_SESSION['role'] === 'coach';
$is_client = $_SESSION['role'] === 'client';

// Fonction pour valider et sécuriser les entrées utilisateur
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Mise à jour des informations de la carte bancaire du client
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_carte_bancaire'])) {
    if ($is_client) {
        // Récupérer l'ID du client connecté à partir de la session
        $id_client = $_SESSION['user_id'];

        // Valider et récupérer les données du formulaire
        $nom = validate($_POST['nom']);
        $prenom = validate($_POST['prenom']);
        $adresse_ligne_1 = validate($_POST['adresse_ligne_1']);
        $adresse_ligne_2 = validate($_POST['adresse_ligne_2']);
        $ville = validate($_POST['ville']);
        $code_postal = validate($_POST['code_postal']);
        $pays = validate($_POST['pays']);
        $carte_etudiant_client = validate($_POST['carte_etudiant_client']);

        // Requête SQL pour mettre à jour les informations de la carte bancaire dans la table client
        $sql = "UPDATE client SET nom_carte = ?, prenom_carte = ?, adresse_ligne_1_carte = ?, adresse_ligne_2_carte = ?, ville_carte = ?, code_postal_carte = ?, pays_carte = ?, carte_etudiant_client = ? WHERE id_client = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("ssssssssi", $nom, $prenom, $adresse_ligne_1, $adresse_ligne_2, $ville, $code_postal, $pays, $carte_etudiant_client, $id_client);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "Informations de carte bancaire mises à jour avec succès.";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour mettre à jour ces informations.";
    }
}

// Mise à jour des informations du coach
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_coach'])) {
    if ($is_coach) {
        $bureau = validate($_POST['bureau']);
        $specialite = validate($_POST['specialite']);
        $photo = validate($_POST['photo']); // Vous pouvez adapter cette ligne pour gérer le téléchargement de fichiers
        $telephone = validate($_POST['telephone']);

        $sql = "UPDATE coach SET bureau_coach=?, specialite_coach=?, photo_coach=?, telephone_coach=? WHERE email_coach=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $bureau, $specialite, $photo, $telephone, $_SESSION['email']);
        $stmt->execute();
        
        if ($stmt->affected_rows === 1) {
            echo "Informations mises à jour avec succès.";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour mettre à jour ces informations.";
    }
}

// Mise à jour des informations du client
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_client'])) {
    if ($is_client) {
        $date_naissance = validate($_POST['date_naissance']);
        $telephone = validate($_POST['telephone']);
        $profession = validate($_POST['profession']);

        $sql = "UPDATE client SET date_de_naissance=?, num_telephone=?, profession=? WHERE email_client=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("ssss", $date_naissance, $telephone, $profession, $_SESSION['email']);
        $stmt->execute();
        
        if ($stmt->affected_rows === 1) {
            echo "Informations mises à jour avec succès.";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour mettre à jour ces informations.";
    }
}

// Récupérer les informations du coach
if ($is_coach) {
    $sql = "SELECT bureau_coach, specialite_coach, photo_coach, telephone_coach FROM coach WHERE email_coach=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $coach_info = $result->fetch_assoc();
        $bureau_coach = $coach_info['bureau_coach'];
        $specialite_coach = $coach_info['specialite_coach'];
        $photo_coach = $coach_info['photo_coach'];
        $telephone_coach = $coach_info['telephone_coach'];
    }
}

// Récupérer les informations du client
if ($is_client) {
    $sql = "SELECT date_de_naissance, num_telephone, profession FROM client WHERE email_client=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $client_info = $result->fetch_assoc();
        $date_naissance = $client_info['date_de_naissance'];
        $telephone = $client_info['num_telephone'];
        $profession = $client_info['profession'];
    }
}

// Récupérer les informations de paiement pour le client
if ($is_client) {
    $sql = "SELECT facture, date_paiement, type_carte, numero_carte, nom_carte FROM paiement WHERE id_client=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $paiements = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $paiements[] = $row;
        }
    }
    $stmt->close();
}

// Inscription d'un nouveau coach
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_coach'])) {
    if ($is_admin) {
        $nom = validate($_POST['name']);
        $prenom = validate($_POST['prenom']);
        $sexe = validate($_POST['sexe']);
        $email = validate($_POST['email']);
        $password = validate($_POST['password']);

        $sql = "INSERT INTO coach (nom_coach, prenom_coach, sexe_coach, email_coach, mdp_coach) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $nom, $prenom, $sexe, $email, $password);
        $stmt->execute();
        
        if ($stmt->affected_rows === 1) {
            echo "Compte coach créé avec succès.";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour créer un compte coach.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre compte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        h2 {
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Votre compte</h1>
        <?php if ($is_coach): ?>
            <h2>Modifier mes informations (Coach)</h2>
            <form method="post" action="">
                <label for="bureau">Bureau:</label>
                <input type="text" id="bureau" name="bureau" value="<?php echo $bureau_coach; ?>" required>

                <label for="specialite">Spécialité:</label>
                <input type="text" id="specialite" name="specialite" value="<?php echo $specialite_coach; ?>" required>

                <label for="photo">Photo URL:</label>
                <input type="text" id="photo" name="photo" value="<?php echo $photo_coach; ?>">

                <label for="telephone">Téléphone:</label>
                <input type="tel" id="telephone" name="telephone" value="<?php echo $telephone_coach; ?>" required>

                <input type="submit" name="update_coach" value="Mettre à jour">
            </form>
        <?php endif; ?>

        <?php if ($is_client): ?>
            <h2>Modifier mes informations (Client)</h2>
            <form method="post" action="">
                <label for="date_naissance">Date de Naissance:</label>
                <input type="date" id="date_naissance" name="date_naissance" value="<?php echo $date_naissance; ?>" required>

                <label for="telephone">Téléphone:</label>
                <input type="tel" id="telephone" name="telephone" value="<?php echo $telephone; ?>" required>

                <label for="profession">Profession:</label>
                <input type="text" id="profession" name="profession" value="<?php echo $profession; ?>" required>

                <input type="submit" name="update_client" value="Mettre à jour">
            </form>

            <h2>Modifier mes informations de carte bancaire</h2>
            <form method="post" action="">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="" required>

                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" value="" required>

                <label for="adresse_ligne_1">Adresse ligne 1:</label>
                <input type="text" id="adresse_ligne_1" name="adresse_ligne_1" value="" required>

                <label for="adresse_ligne_2">Adresse ligne 2:</label>
                <input type="text" id="adresse_ligne_2" name="adresse_ligne_2" value="">

                <label for="ville">Ville:</label>
                <input type="text" id="ville" name="ville" value="" required>

                <label for="code_postal">Code postal:</label>
                <input type="text" id="code_postal" name="code_postal" value="" required>

                <label for="pays">Pays:</label>
                <input type="text" id="pays" name="pays" value="" required>

                <label for="carte_etudiant_client">Carte Étudiant:</label>
                <input type="text" id="carte_etudiant_client" name="carte_etudiant_client" value="">

                <input type="submit" name="update_carte_bancaire" value="Mettre à jour">
            </form>

            <h2>Mes Factures</h2>
            <table>
                <tr>
                    <th>Facture</th>
                    <th>Date de Paiement</th>
                    <th>Type de Carte</th>
                    <th>Numéro de Carte</th>
                    <th>Nom sur la Carte</th>
                </tr>
                <?php foreach ($paiements as $paiement): ?>
                    <tr>
                        <td><?php echo $paiement['facture']; ?></td>
                        <td><?php echo $paiement['date_paiement']; ?></td>
                        <td><?php echo $paiement['type_carte']; ?></td>
                        <td><?php echo $paiement['numero_carte']; ?></td>
                        <td><?php echo $paiement['nom_carte']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <h2>Inscrire un nouveau coach</h2>
            <form method="post" action="">
                <label for="name">Nom:</label>
                <input type="text" id="name" name="name" required>

                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" required>

                <label for="sexe">Sexe:</label>
                <select id="sexe" name="sexe" required>
                    <option value="Homme">Homme</option>
                    <option value="Femme">Femme</option>
                    <option value="Autre">Autre</option>
                </select>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>

                <input type="submit" name="register_coach" value="Inscrire">
            </form>
        <?php endif; ?>
    </div>
</body>

</html>
