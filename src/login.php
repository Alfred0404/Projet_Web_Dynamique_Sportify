<?php
// Détails de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sportify";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Fonction pour enregistrer les données dans la base de données
function registerUser($conn, $table, $nom, $prenom, $sexe, $email, $motdepasse) {
    $hashed_password = password_hash($motdepasse, PASSWORD_BCRYPT);
    $sql = "INSERT INTO $table (nom_$table, prenom_$table, sexe_$table, email_$table, mdp_$table) VALUES ('$nom', '$prenom', '$sexe', '$email', '$hashed_password')";
    if ($conn->query($sql) === TRUE) {
        // Rediriger vers la page de bienvenue avec le rôle
        header("Location: index.php?role=$table&action=welcome");
        exit();
    } else {
        return "Erreur: " . $sql . "<br>" . $conn->error;
    }
}



// Traiter l'inscription
$register_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_role'])) {
    $role = $_POST['register_role'];
    $nom = $_POST['name'];
    $prenom = $_POST['prenom'];
    $sexe = $_POST['sexe'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    if ($role == 'admin') {
        $register_message = registerUser($conn, 'admin', $nom, $prenom, $sexe, $email, $password);
    } elseif ($role == 'coach') {
        $register_message = registerUser($conn, 'coach', $nom, $prenom, $sexe, $email, $password);
    } elseif ($role == 'client') {
        $register_message = registerUser($conn, 'client', $nom, $prenom, $sexe, $email, $password);
    }
}

// Traiter la connexion
$login_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_role'])) {
    $role = $_POST['login_role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($role == 'admin') {
        $table = 'admin';
    } elseif ($role == 'coach') {
        $table = 'coach';
    } elseif ($role == 'client') {
        $table = 'client';
    }

    $sql = "SELECT * FROM $table WHERE nom_$table='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['mdp_' . $table])) {
            // Rediriger vers la page de bienvenue
            header("Location: index.php?role=$table&action=welcome");
            exit();
        } else {
            $login_message = "Mot de passe incorrect.";
        }
    } else {
        $login_message = "Aucun compte trouvé avec ce nom d'utilisateur.";
    }
}

// Définir le rôle pour la page de bienvenue
$role_name = '';
if (isset($_GET['role'])) {
    if ($_GET['role'] == 'admin') {
        $role_name = 'Administrateur';
    } elseif ($_GET['role'] == 'coach') {
        $role_name = 'Coach';
    } elseif ($_GET['role'] == 'client') {
        $role_name = 'Client';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Connexion et Création de Compte</title>
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
            margin: 10px;
            width: 300px;
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
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <?php if (isset($_GET['action']) && $_GET['action'] == 'welcome' && $role_name): ?>
        <div class="container">
            <h1>Bienvenue, <?php echo htmlspecialchars($role_name); ?> !</h1>
            <p>Votre connexion a été réussie.</p>
        </div>
    <?php else: ?>
        <div class="container">
            <h2>Connexion</h2>
            <?php if ($login_message): ?>
                <p><?php echo $login_message; ?></p>
            <?php endif; ?>
            <label for="role">Sélectionnez votre rôle :</label>
            <select id="login-role" name="role" onchange="showLoginForm()">
                <option value="">--Choisir un rôle--</option>
                <option value="admin">Administrateur</option>
                <option value="coach">Coach</option>
                <option value="client">Client</option>
            </select>

            <!-- Formulaire de connexion pour les administrateurs -->
            <form id="login-admin-form" class="hidden" method="post">
                <input type="hidden" name="login_role" value="admin">
                <label for="admin-username">Nom d'utilisateur (Admin) :</label>
                <input type="text" id="admin-username" name="username" required>
                <label for="admin-password">Mot de passe :</label>
                <input type="password" id="admin-password" name="password" required>
                <button type="submit">Se connecter</button>
            </form>

            <!-- Formulaire de connexion pour les coachs -->
            <form id="login-coach-form" class="hidden" method="post">
                <input type="hidden" name="login_role" value="coach">
                <label for="coach-username">Nom d'utilisateur (Coach) :</label>
                <input type="text" id="coach-username" name="username" required>
                <label for="coach-password">Mot de passe :</label>
                <input type="password" id="coach-password" name="password" required>
                <button type="submit">Se connecter</button>
            </form>

            <!-- Formulaire de connexion pour les clients -->
            <form id="login-client-form" class="hidden" method="post">
                <input type="hidden" name="login_role" value="client">
                <label for="client-username">Nom d'utilisateur (Client) :</label>
                <input type="text" id="client-username" name="username" required>
                <label for="client-password">Mot de passe :</label>
                <input type="password" id="client-password" name="password" required>
                <button type="submit">Se connecter</button>
            </form>
        </div>

        <div class="container">
            <h2>Créer un compte</h2>
            <?php if ($register_message): ?>
                <p><?php echo $register_message; ?></p>
            <?php endif; ?>
            <label for="register-role">Sélectionnez votre rôle :</label>
            <select id="register-role" name="register_role" onchange="showRegisterForm()">
                <option value="">--Choisir un rôle--</option>
                <option value="admin">Administrateur</option>
                <option value="coach">Coach</option>
                <option value="client">Client</option>
            </select>

            <!-- Formulaire d'inscription -->
            <form id="register-form" class="hidden" method="post">
                <input type="hidden" id="register-role-hidden" name="register_role">
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
                <button type="submit">S'inscrire</button>
            </form>
        </div>
    <?php endif; ?>

    <script>
        function showLoginForm() {
            var selectedRole = document.getElementById("login-role").value;
            var adminForm = document.getElementById("login-admin-form");
            var coachForm = document.getElementById("login-coach-form");
            var clientForm = document.getElementById("login-client-form");

            if (selectedRole === "admin") {
                adminForm.classList.remove("hidden");
                coachForm.classList.add("hidden");
                clientForm.classList.add("hidden");
            } else if (selectedRole === "coach") {
                adminForm.classList.add("hidden");
                coachForm.classList.remove("hidden");
                clientForm.classList.add("hidden");
            } else if (selectedRole === "client") {
                adminForm.classList.add("hidden");
                coachForm.classList.add("hidden");
                clientForm.classList.remove("hidden");
            } else {
                adminForm.classList.add("hidden");
                coachForm.classList.add("hidden");
                clientForm.classList.add("hidden");
            }
        }

        function showRegisterForm() {
            var selectedRole = document.getElementById("register-role").value;
            var registerForm = document.getElementById("register-form");
            var registerRoleHidden = document.getElementById("register-role-hidden");

            registerRoleHidden.value = selectedRole;
            registerForm.classList.remove("hidden");
        }
    </script>
</body>
</html>
