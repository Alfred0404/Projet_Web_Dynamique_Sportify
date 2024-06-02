<?php
session_start();
include "db_connection.php";

// Fonction pour valider et sécuriser les entrées utilisateur
function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Inscription des utilisateurs
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_role'])) {

    // Récupération des données du formulaire
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

    // vérification des doublons
    if ($result->num_rows > 0) {
        echo "Erreur: Un utilisateur avec le même nom existe déjà.";
    }

    // Pas de doublon
    if ($stmt->affected_rows === 1) {
        header("Location: index.php?success=Account created successfully");
        exit();
    } else {
        // Insertion des données dans la table correspondante
        $table = $role;
        $sql = "INSERT INTO $table (nom_$table, prenom_$table, sexe_$table, email_$table, mdp_$table) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $nom, $prenom, $sexe, $email, $password);
        $stmt->execute();

        // mettre les données dans la session et mettre le user dans la table users
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

// Connexion des utilisateurs
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_role'])) {
    // Récupération des données du formulaire
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
            // Connexion réussie, on met les données dans la session
            $_SESSION['user_id'] = $row['id_' . $table];
            $_SESSION['user_name'] = $row['nom_' . $table];
            $_SESSION['prenom'] = $row['prenom_' . $table];
            $_SESSION['email'] = $row['email_' . $table];
            $_SESSION['role'] = $role;
            $_SESSION['nom'] = $row['nom_' . $table];

            // Récupération du unique_id depuis la table users
            $user_sql = "SELECT unique_id FROM users WHERE fname = ? AND lname = ? AND password = ?";
            echo $user_sql;
            $user_stmt = $conn->prepare($user_sql);
            if ($user_stmt === false) {
                die("Erreur de préparation de la requête users : " . $conn->error);
            }
            echo $role . " " . $username . " " . $password;
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
                header("Location: index.php?message=Erreur: impossible de trouver l'utilisateur dans la table users.");
                exit();
            }
        } else {
            echo "Mot de passe incorrect.";
            header ("Location: index.php?message=Mot de passe incorrect.");
            exit();
        }
    } else {
        echo "Nom d'utilisateur incorrect.";
        header ("Location: index.php?message=Nom d'utilisateur incorrect.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>Connexion et Inscription</title>
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
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
    </header>
    <section>
        <div class="container connect">
            <h2>Connexion</h2>
            <form method="post" action="index.php">
                <label for="login_role">Sélectionnez votre rôle</label>
                <select id="login_role" name="login_role" required>
                    <option value="">--Choisir un rôle--</option>
                    <option value="admin">Administrateur</option>
                    <option value="coach">Coach</option>
                    <option value="client">Client</option>
                </select>
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Se connecter</button>
            </form>
        </div>
        <div class="container create-account">
            <h2>Créer un compte</h2>
            <form method="post" action="index.php">
                <label for="register_role">Sélectionnez votre rôle</label>
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
                    <button class="register" type="submit">S'inscrire</button>
                </div>
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