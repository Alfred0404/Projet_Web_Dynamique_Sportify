<?php
session_start();
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Vérifier si l'utilisateur est un administrateur ou un coach
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
    $coach_info = $result->fetch_assoc();
    $bureau_coach = $coach_info['bureau_coach'];
    $specialite_coach = $coach_info['specialite_coach'];
    $photo_coach = $coach_info['photo_coach'];
    $telephone_coach = $coach_info['telephone_coach'];
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
    $client_info = $result->fetch_assoc();
    $date_naissance = $client_info['date_de_naissance'];
    $telephone = $client_info['num_telephone'];
    $profession = $client_info['profession'];
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
        .btn-back {
            background-color: #007bff;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
        img {
            width: 100px;
            height: auto;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Informations du compte</h2>
        <p><strong>Nom:</strong> <?php echo $_SESSION['user_name']; ?></p>
        <p><strong>Prénom:</strong> <?php echo $_SESSION['prenom']; ?></p>
        <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
        <p><strong>Rôle:</strong> <?php echo ucfirst($_SESSION['role']); ?></p>
        <?php if ($is_coach): ?>
        <p><strong>Bureau:</strong> <?php echo $bureau_coach; ?></p>
        <p><strong>Spécialité:</strong> <?php echo $specialite_coach; ?></p>
        <p><strong>Photo:</strong> <img src="<?php echo $photo_coach; ?>" alt="Photo du coach"></p>
        <p><strong>Téléphone:</strong> <?php echo $telephone_coach; ?></p>
        <?php endif; ?>
        <?php if ($is_client): ?>
        <p><strong>Date de naissance:</strong> <?php echo $date_naissance; ?></p>
        <p><strong>Numéro de téléphone:</strong> <?php echo $telephone; ?></p>
        <p><strong>Profession:</strong> <?php echo $profession; ?></p>
        <?php endif; ?>
        <form method="post" action="logout.php">
            <button type="submit">Déconnexion</button>
        </form>
        <form method="get" action="accueil.php">
            <button type="submit" class="btn-back">Retour à l'accueil</button>
        </form>
        </div>

        <?php if ($is_admin): ?>
        <div class="container">
        <h2>Créer un compte coach</h2>
        <form method="post" action="compte.php">
            <input type="hidden" name="create_coach" value="1">
            <label for="name">Nom :</label>
            <input type="text" id="name" name="name" required>
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
            <label for="sexe">Sexe :</label>
            <input type="text" id="sexe" name="sexe" required>
            <label for="email">Adresse Email :</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Créer un compte coach</button>
        </form>
        </div>
        <?php endif; ?>

        <?php if ($is_coach): ?>
        <div class="container">
        <h2>Mettre à jour les informations du coach</h2>
        <form method="post" action="compte.php">
            <input type="hidden" name="update_coach" value="1">
            <label for="bureau">Bureau :</label>
            <input type="text" id="bureau" name="bureau" value="<?php echo $bureau_coach; ?>" required>
            <label for="specialite">Spécialité :</label>
            <input type="text" id="specialite" name="specialite" value="<?php echo $specialite_coach; ?>" required>
            <label for="photo">Photo (URL) :</label>
            <input type="text" id="photo" name="photo" value="<?php echo $photo_coach; ?>" required>
            <label for="telephone">Téléphone :</label>
            <input type="text" id="telephone" name="telephone" value="<?php echo $telephone_coach; ?>" required>
            <button type="submit">Mettre à jour</button>
        </form>
<<<<<<< HEAD
        </div>
        <?php endif; ?>

        <?php if ($is_client): ?>
        <div class="container">
        <h2>Mettre à jour les informations du client</h2>
        <form method="post" action="compte.php">
            <input type="hidden" name="update_client" value="1">
            <label for="date_naissance">Date de naissance :</label>
            <input type="date" id="date_naissance" name="date_naissance" value="<?php echo $date_naissance; ?>" required>
            <label for="telephone">Numéro de téléphone :</label>
            <input type="text" id="telephone" name="telephone" value="<?php echo $telephone; ?>" required>
            <label for="profession">Profession :</label>
            <input type="text" id="profession" name="profession" value="<?php echo $profession; ?>" required>
            <button type="submit">Mettre à jour</button>
        </form>
        </div>
        <?php endif; ?>
=======
    </div>
    <?php endif; ?>

>>>>>>> b6f695585298a047198b1e68fffb4bb75cebd1f2
</body>

</html>
