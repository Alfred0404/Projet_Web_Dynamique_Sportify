<?php
session_start();
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Vérifier si l'utilisateur est un administrateur
$is_admin = $_SESSION['role'] === 'admin';

// Fonction pour valider et sécuriser les entrées utilisateur
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Création d'un compte coach
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_coach'])) {
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
    </style>
</head>

<body>
    <div class="container">
        <h2>Informations du compte</h2>
        <p><strong>Nom:</strong> <?php echo $_SESSION['user_name']; ?></p>
        <p><strong>Prénom:</strong> <?php echo $_SESSION['prenom']; ?></p>
        <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
        <p><strong>Rôle:</strong> <?php echo ucfirst($_SESSION['role']); ?></p>
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
</body>

</html>
