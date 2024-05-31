<?php
session_start();
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Vérifier le rôle de l'utilisateur
$is_admin = $_SESSION['role'] === 'admin';
$is_coach = $_SESSION['role'] === 'coach';
$is_client = $_SESSION['role'] === 'client';

// Fonction pour valider et sécuriser les entrées utilisateur
function validate($data)
{
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
            // echo "Informations mises à jour avec succès.";
        } else {
            // echo "Erreur: " . $sql . "<br>" . $conn->error;
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
    <link rel="stylesheet" href="css/compte.css">
    <title>Votre compte</title>
</head>

<body>
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
    </header>
    <div class="nav">
        <ul>
            <li class="nav-item"><a href="accueil">Accueil</a></li>
            <li class="nav-item"><a href="parcourir.php">Tout parcourir</a></li>
            <li class="nav-item"><a href="recherche.php">Rechercher</a></li>
            <li class="nav-item"><a href="rendez_vous.php">Rendez-vous</a></li>
            <li class="nav-item active"><a href="#">Votre compte</a></li>
            <li class="nav-item"><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>
    <section>
        <div class="container client-infos">
            <h2>Informations du compte</h2>
            <p><strong>Nom :</strong> <?php echo $_SESSION['user_name']; ?></p>
            <p><strong>Prénom :</strong> <?php echo $_SESSION['prenom']; ?></p>
            <p><strong>Email :</strong> <?php echo $_SESSION['email']; ?></p>
            <p><strong>Rôle :</strong> <?php echo ucfirst($_SESSION['role']); ?></p>
            <?php if ($is_coach): ?>
                <p><strong>Bureau :</strong> <?php echo $bureau_coach ?? ''; ?></p>
                <p><strong>Spécialité :</strong> <?php echo $specialite_coach ?? ''; ?></p>
                <p><strong>Photo :</strong> <img src="<?php echo $photo_coach ?? ''; ?>" alt="Photo du coach"></p>
                <p><strong>Téléphone :</strong> <?php echo $telephone_coach ?? ''; ?></p>
            <?php endif; ?>
            <?php if ($is_client): ?>
                <p><strong>Date de naissance :</strong> <?php echo $date_naissance ?? ''; ?></p>
                <p><strong>Numéro de téléphone :</strong> <?php echo $telephone ?? ''; ?></p>
                <p><strong>Profession :</strong> <?php echo $profession ?? ''; ?></p>
            <?php endif; ?>
            <form method="post" action="logout.php">
                <button class="btn-logout" type="submit">Déconnexion</button>
            </form>
            <form method="get" action="accueil.php">
                <button type="submit" class="btn-back">Retour à l'accueil</button>
            </form>
        </div>

        <?php if ($is_admin): ?>
            <div class="container create-coach">
                <h2>Créer un compte coach</h2>
                <form for="register_coach" method="post" action="compte.php">
                    <input type="hidden" name="register_coach" value="1">
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
            <div class="container update">
                <h2>Mettre à jour les informations du coach</h2>
                <form method="post" action="compte.php">
                    <input type="hidden" name="update_coach" value="1">
                    <label for="bureau">Bureau :</label>
                    <input type="text" id="bureau" name="bureau" value="<?php echo $bureau_coach ?? ''; ?>" required>
                    <label for="specialite">Spécialité :</label>
                    <input type="text" id="specialite" name="specialite" value="<?php echo $specialite_coach ?? ''; ?>"
                        required>
                    <label for="photo">Photo (URL) :</label>
                    <input type="text" id="photo" name="photo" value="<?php echo $photo_coach ?? ''; ?>" required>
                    <label for="telephone">Téléphone :</label>
                    <input type="text" id="telephone" name="telephone" value="<?php echo $telephone_coach ?? ''; ?>"
                        required>
                    <button type="submit">Mettre à jour</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($is_client): ?>
            <div class="container update">
                <h2>Mettre à jour les informations du client</h2>
                <form method="post" action="compte.php">
                    <input type="hidden" name="update_client" value="1">
                    <label for="date_naissance">Date de naissance :</label>
                    <input type="date" id="date_naissance" name="date_naissance"
                        value="<?php echo $date_naissance ?? ''; ?>" required>
                    <label for="telephone">Numéro de téléphone :</label>
                    <input type="text" id="telephone" name="telephone" value="<?php echo $telephone ?? ''; ?>" required>
                    <label for="profession">Profession :</label>
                    <input type="text" id="profession" name="profession" value="<?php echo $profession ?? ''; ?>" required>
                    <button type="submit">Mettre à jour</button>
                </form>
            </div>
        <?php endif; ?>
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