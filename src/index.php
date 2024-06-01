<?php
session_start();
include "db_connection.php";

// Fonction pour valider et sécuriser les entrées utilisateur
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Inscription
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_role'])) {

    $role = validate($_POST['register_role']);
    $nom = validate($_POST['name']);
    $prenom = validate($_POST['prenom']);
    $sexe = validate($_POST['sexe']);
    $email = validate($_POST['email']); 
    $password = validate($_POST['password']);

    // Vérification des doublons
    $check_sql = "SELECT * FROM admin WHERE nom_admin = ?";
    $stmt = $conn->prepare($check_sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête de vérification des doublons : " . $conn->error);
    }
    $stmt->bind_param("s", $nom);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Un doublon a été trouvé
        echo "Erreur: Un utilisateur avec le même nom existe déjà.";
    } else {
        // Pas de doublon, on peut insérer
        $table = $role;
        $sql = "INSERT INTO $table (nom_$table, prenom_$table, sexe_$table, email_$table, mdp_$table) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $nom, $prenom, $sexe, $email, $password);
        $stmt->execute();
            
        if ($stmt->affected_rows === 1) {
            $_SESSION['role'] = $role;
            $_SESSION['nom'] = $nom;

            // Enregistrement dans la table users
            $unique_id = rand(time(), 100000000);
            $_SESSION['unique_id'] = $unique_id;
            $fname = $table;
            $img = "image_coach/defaut.jpg";
            $status = "En ligne";
            
            $user_sql = "INSERT INTO users (unique_id, fname, lname, email, password, img, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $user_stmt = $conn->prepare($user_sql);
            if ($user_stmt === false) {
                die("Erreur de préparation de la requête user : " . $conn->error);
            }
            $user_stmt->bind_param("issssss", $unique_id, $fname, $nom, $email, $password, $img, $status);
            $user_stmt->execute();

            if ($user_stmt->affected_rows === 1) {
                header("Location: index.php?message=Inscription réussie. Vous pouvez maintenant vous connecter.");
                exit();
            } else {
                echo "Erreur lors de l'inscription dans la table users: " . $user_sql . "<br>" . $conn->error;
            }
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    }
}

    

// Connexion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_role'])) {
    $role = validate($_POST['login_role']);
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);

    $table = $role;
    $sql = "SELECT * FROM $table WHERE nom_$table = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($password == $row['mdp_' . $table]) {
            $_SESSION['user_name'] = $row['nom_' . $table];
            $_SESSION['prenom'] = $row['prenom_' . $table];
            $_SESSION['email'] = $row['email_' . $table];
            $_SESSION['role'] = $role;
            $_SESSION['nom'] = $row['nom_' . $table];

            // Récupération du unique_id depuis la table users
            $user_sql = "SELECT unique_id FROM users WHERE fname = ? AND lname = ? AND password = ?";
            $user_stmt = $conn->prepare($user_sql);
            if ($user_stmt === false) {
                die("Erreur de préparation de la requête users : " . $conn->error);
            }
            $user_stmt->bind_param("sss", $role, $username, $password);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();

            if ($user_result->num_rows === 1) {
                $user_row = $user_result->fetch_assoc();
                $_SESSION['unique_id'] = $user_row['unique_id'];
                header("Location: accueil.php");
                exit();
            } else {
                echo "Erreur: impossible de trouver l'utilisateur dans la table users.";
                exit();
            }
        } else {
            echo "Mot de passe incorrect.";
            exit();
        }
    } else {
        echo "Nom d'utilisateur incorrect.";
        exit();
    }
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion et Inscription</title>
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
    </style>
    <script>
        function showRegistrationFields() {
            const role = document.getElementById('register_role').value;
            const fields = document.getElementById('registration_fields');
            if (role) {
                fields.style.display = 'block';
            } else {
                fields.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>
        <form method="post" action="index.php">
            <label for="login_role">Sélectionnez votre rôle :</label>
            <select id="login_role" name="login_role" required>
                <option value="">--Choisir un rôle--</option>
                <option value="admin">Administrateur</option>
                <option value="coach">Coach</option>
                <option value="client">Client</option>
            </select>
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
    <div class="container">
        <h2>Créer un compte</h2>
        <form method="post" action="index.php">
            <label for="register_role">Sélectionnez votre rôle :</label>
            <select id="register_role" name="register_role" onchange="showRegistrationFields()" required>
                <option value="">--Choisir un rôle--</option>
                <option value="admin">Administrateur</option>
                <option value="client">Client</option>
            </select>
            <div id="registration_fields" style="display: none;">
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom">
                <label for="sexe">Sexe :</label>
                <input type="text" id="sexe" name="sexe">
                <label for="email">Adresse Email :</label>
                <input type="email" id="email" name="email">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password">
                <button type="submit">S'inscrire</button>
            </div>
        </form>
    </div>
</body>
</html>
