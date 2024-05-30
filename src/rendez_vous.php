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
function validate($data) {
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

$sql = "SELECT rv.id_coach, rv.jour_rdv, rv.heure_rdv, rv.statut_rdv, c.nom_coach, c.prenom_coach, c.email_coach, c.specialite_coach, s.nom_salle
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
if ($is_admin) {
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
        <h1>Sportify</h1>
    </header>

    <div class="nav">
        <ul>
            <li class="nav-item"><a href="accueil.php">Accueil</a></li>
            <li class="nav-item"><a href="parcourir.php">Tout parcourir</a></li>
            <li class="nav-item"><a href="recherche.php">Rechercher</a></li>
            <li class="nav-item active"><a href="#">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
        </ul>
    </div>

    <section>
        <?php if ($is_admin): ?>
            <h1>Sélectionner un utilisateur</h1>
            <form method="POST" action="rendez_vous.php">
                <label for="id_client">Choisir un utilisateur:</label>
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
                <div class="rdv-details" data-coach-id="<?= $rdv['id_coach'] ?>" data-date-rdv="<?= $rdv['jour_rdv'] . ' ' . $rdv['heure_rdv'] ?>">
                    <h2>Rendez-vous avec <?= htmlspecialchars($rdv['nom_coach']) ?> <?= htmlspecialchars($rdv['prenom_coach']) ?></h2>
                    <p>Date et Heure: <?= htmlspecialchars($rdv['jour_rdv'] . ' ' . $rdv['heure_rdv']) ?></p>
                    <p>Salle: <?= htmlspecialchars($rdv['nom_salle']) ?></p>
                    <p>Email: <?= htmlspecialchars($rdv['email_coach']) ?></p>
                    <p>Spécialité: <?= htmlspecialchars($rdv['specialite_coach']) ?></p>
                    <form method="POST" action="rendez_vous.php" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');">
                        <input type="hidden" name="cancel_rdv" value="1">
                        <input type="hidden" name="id_coach" value="<?= $rdv['id_coach'] ?>">
                        <input type="hidden" name="date_rdv" value="<?= $rdv['jour_rdv'] . ' ' . $rdv['heure_rdv'] ?>">
                        <input type="hidden" name="id_client" value="<?= $id_client ?>">
                        <button type="submit">Annuler le RDV</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Vous n'avez aucun rendez-vous confirmé.</p>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <h1>Sélectionner un coach</h1>
            <form method="GET" action="disponibilites.php">
                <label for="id_coach">Choisir un coach:</label>
                <select id="id_coach" name="id_coach" required>
                    <?php foreach ($coachs as $coach): ?>
                        <option value="<?= $coach['id_coach'] ?>"><?= htmlspecialchars($coach['nom_coach']) . ' ' . htmlspecialchars($coach['prenom_coach']) ?></option>
                    <?php endforeach; ?>
                </select><br>
                <button type="submit">Voir les Disponibilités</button>
            </form>
        <?php elseif (!$is_admin && !$is_coach): ?>
            <h1>Prendre un Nouveau Rendez-vous</h1>
            <form action="disponibilites.php" method="GET">
                <input type="hidden" name="id_client" value="<?= $id_client ?>">
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
                <button type="submit">Voir les Disponibilités</button>
            </form>
        <?php elseif ($is_coach): ?>
            <h1>Consulter les Disponibilités</h1>
            <form action="disponibilites.php" method="GET">
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
                <button type="submit">Voir les Disponibilités</button>
            </form>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2024 Sportify</p>
    </footer>
</body>

</html>