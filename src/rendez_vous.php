<?php
session_start();
include "db_connection.php";

// Initialiser les messages d'erreur ou de succès
$message = "";

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

// Annuler un rendez-vous
if (isset($_POST['cancel_rdv'])) {
    $id_coach = $_POST['id_coach'] ?? null;
    $date_rdv = $_POST['date_rdv'] ?? null;
    $id_client = $_POST['id_client'] ?? $_SESSION['user_id'];

    if ($id_coach === null || $date_rdv === null) {
        echo "Paramètres manquants pour l'annulation du rendez-vous.";
        exit();
    }

    list($jour_rdv, $heure_rdv) = explode(' ', $date_rdv, 2);

    // Suppression du rendez-vous
    $sql = "DELETE FROM prise_de_rendez_vous WHERE id_coach = ? AND id_client = ? AND jour_rdv = ? AND heure_rdv = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiss", $id_coach, $id_client, $jour_rdv, $heure_rdv);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: rendez_vous.php");
        exit();
    } else {
        echo "Erreur lors de l'annulation du rendez-vous : " . mysqli_stmt_error($stmt);
    }
}

// Ajouter un rendez-vous
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['cancel_rdv']) && !$is_admin && !$is_coach) {
    $id_coach = $_POST['id_coach'] ?? null;
    $jour_rdv = $_POST['jour_rdv'] ?? null;
    $heure_rdv = $_POST['heure_rdv'] ?? null;
    $id_client = $_POST['id_client'] ?? $_SESSION['user_id'];
    echo "" . $id_coach;
    if ($id_coach === null || $jour_rdv === null || $heure_rdv === null) {
        echo "Paramètres manquants pour la prise du rendez-vous.";
        exit();
    }

    // Vérifier la disponibilité
    $sql_check = "SELECT * FROM prise_de_rendez_vous WHERE id_coach = ? AND jour_rdv = ? AND heure_rdv = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "iss", $id_coach, $jour_rdv, $heure_rdv);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "Le créneau est déjà réservé.";
    } else {
        // Attribution d'une salle aléatoire (exemple avec 5 salles)
        $id_salle = rand(1, 5);

        $sql = "INSERT INTO prise_de_rendez_vous (id_client, id_coach, id_salle, jour_rdv, heure_rdv, statut_rdv) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiiss", $id_client, $id_coach, $id_salle, $jour_rdv, $heure_rdv);

        if (mysqli_stmt_execute($stmt)) {
            echo "Rendez-vous pris avec succès!";
        } else {
            echo "Erreur lors de la prise du rendez-vous : " . mysqli_stmt_error($stmt);
        }
    }
    exit();
}

// Récupérer les rendez-vous de l'utilisateur sélectionné ou connecté
$id_client = ($is_admin && isset($_POST['id_client'])) ? validate($_POST['id_client']) : $_SESSION['user_id'];
$id_coach = $_SESSION['role'] === 'coach' ? $_SESSION['user_id'] : null;

// Récupérer les rendez-vous de l'utilisateur
$sql = "SELECT rv.id_coach, rv.jour_rdv, rv.heure_rdv, rv.statut_rdv, c.photo_coach, c.nom_coach, c.prenom_coach, c.email_coach, c.specialite_coach, s.nom_salle
        FROM prise_de_rendez_vous rv
        JOIN coach c ON rv.id_coach = c.id_coach
        JOIN salle s ON rv.id_salle = s.id_salle
        WHERE rv.id_client = ? OR rv.id_coach = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id_client, $id_coach);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rendez_vous = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Récupérer la liste des utilisateurs pour le formulaire de sélection si l'utilisateur est un admin
if ($is_admin) {
    $sql_users = "SELECT id_client, nom_client, prenom_client FROM client";
    $result_users = mysqli_query($conn, $sql_users);
    $users = mysqli_fetch_all($result_users, MYSQLI_ASSOC);
}

// Récupérer la liste des coachs pour le formulaire de sélection si l'utilisateur est un admin
if ($is_client || $is_admin) {
    $sql_coachs = "SELECT id_coach, nom_coach, prenom_coach FROM coach";
    $result_coachs = mysqli_query($conn, $sql_coachs);
    $coachs = mysqli_fetch_all($result_coachs, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous</title>
    <link rel="stylesheet" href="css/rendez_vous.css">
</head>

<body>
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
    </header>

    <div class="nav">
        <ul>
            <li class="nav-item"><a href="accueil.php">Accueil</a></li>
            <li class="nav-item"><a href="parcourir.php">Tout parcourir</a></li>
            <li class="nav-item"><a href="recherche.php">Rechercher</a></li>
            <li class="nav-item active"><a href="#">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
            <li class="nav-item"><a href="users.php">Discussions</a></li>
            <li class="nav-item"><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>

    <section>
        <?php if ($is_admin): ?>
            <!-- selection de l'utilisateur dont on veut afficher les rendez-vous -->
            <h1>Sélectionner un utilisateur</h1>
            <form class="select-user-form" method="POST" action="rendez_vous.php">
                <label for="id_client">Choisir un utilisateur :</label>
                <select id="id_client" name="id_client" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id_client'] ?>" <?= $user['id_client'] == $id_client ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['nom_client']) . ' ' . htmlspecialchars($user['prenom_client']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Voir les Rendez-vous</button>
            </form>
        <?php endif; ?>

        <h1>Rendez-vous client confirmés</h1>
        <?php if (count($rendez_vous) > 0): ?>
            <?php foreach ($rendez_vous as $rdv): ?>
                <div class="rdv-details" data-coach-id="<?= $rdv['id_coach'] ?>"
                    data-date-rdv="<?= $rdv['jour_rdv'] . ' ' . $rdv['heure_rdv'] ?>">
                    <?php if ($is_coach): ?>
                        <h2>Rendez-vous</h2>
                    <?php else: ?>
                        <h2>Rendez-vous avec <?= htmlspecialchars($rdv['nom_coach']) ?>
                            <?= htmlspecialchars($rdv['prenom_coach']) ?>
                        </h2>
                    <?php endif; ?>
                    <div class="infos">
                        <div class="infos-rdv">
                            <!-- afficher les infos du rendez-vous -->
                            <p>Date et Heure: <?= htmlspecialchars($rdv['jour_rdv'] . ' ' . $rdv['heure_rdv']) ?></p>
                            <p>Salle: <?= htmlspecialchars($rdv['nom_salle']) ?></p>
                            <p>Email: <?= htmlspecialchars($rdv['email_coach']) ?></p>
                            <?php
                            // Récupérer le nom de la spécialité du coach
                            $sql = "SELECT nom_activites FROM activites WHERE id_activites = ?";
                            $stmt = mysqli_prepare($conn, $sql);
                            mysqli_stmt_bind_param($stmt, "i", $rdv['specialite_coach']);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $activite = mysqli_fetch_assoc($result);
                            echo "<p>Spécialité : " . htmlspecialchars($activite['nom_activites']) . "</p>";
                            ?>
                        </div>
                        <div class="img-coach">
                            <img src=<?= $rdv['photo_coach'] ?> alt="photo-coach">
                        </div>
                    </div>
                    <!-- form d'annulation d'un rendez-vous -->
                    <form method="POST" action="rendez_vous.php" style="display: inline;"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');">
                        <input type="hidden" name="cancel_rdv" value="1">
                        <input type="hidden" name="id_coach" value="<?= $rdv['id_coach'] ?>">
                        <input type="hidden" name="date_rdv" value="<?= $rdv['jour_rdv'] . ' ' . $rdv['heure_rdv'] ?>">
                        <input type="hidden" name="id_client" value="<?= $id_client ?>">
                        <?php if (!$is_coach): ?>
                            <button class="cancel-rdv" type="submit">Annuler le RDV</button>
                        <?php endif ?>

                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Vous n'avez aucun rendez-vous confirmé.</p>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <h1>Sélectionner un coach</h1>
            <form method="GET" action="disponibilites.php" class="select-user-form">
                <label for="id_coach">Choisir un coach :</label>
                <select id="id_coach" name="id_coach" required>
                    <?php foreach ($coachs as $coach): ?>
                        <option value="<?= $coach['id_coach'] ?>">
                            <?= htmlspecialchars($coach['nom_coach']) . ' ' . htmlspecialchars($coach['prenom_coach']) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>
                <button type="submit">Voir les disponibilités</button>
            </form>
        <?php elseif (!$is_admin && !$is_coach): ?>
            <h1>Prendre un nouveau rendez-vous</h1>
            <form action="disponibilites.php" method="GET" class="select-rdv-form">
                <input type="hidden" name="id_client" value="<?= $id_client ?>">
                <label for="id_coach">Choisir un coach :</label>
                <select id="id_coach" name="id_coach" required>
                    <?php
                    $sql = "SELECT * FROM coach";
                    $result = mysqli_query($conn, $sql);

                    while ($coach = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $coach['id_coach'] . '">' . htmlspecialchars($coach['nom_coach']) . ' ' . htmlspecialchars($coach['prenom_coach']) . '</option>';
                    }
                    ?>
                </select><br>
                <button type="submit">Voir les disponibilités</button>
            </form>
        <?php elseif ($is_coach): ?>
            <h1>Consulter les disponibilités</h1>
            <form action="disponibilites.php" method="GET" class="select-rdv-form">
                <label for="id_coach">Choisir un coach:</label>
                <select id="id_coach" name="id_coach" required>
                    <?php
                    $sql = "SELECT * FROM coach";
                    $result = mysqli_query($conn, $sql);

                    while ($coach = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $coach['id_coach'] . '">' . htmlspecialchars($coach['nom_coach']) . ' ' . htmlspecialchars($coach['prenom_coach']) . '</option>';
                    }
                    ?>
                </select><br>
                <button type="submit">Voir les disponibilités</button>
            </form>
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