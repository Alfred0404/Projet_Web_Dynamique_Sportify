<?php
session_start();
$user_name = "root";
$password = "";
$database = "sportify";
$server = "127.0.0.1";
$port = 3306;

$conn = mysqli_connect($server, $user_name, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    echo "Veuillez vous <a href='login.php'>connecter</a> pour voir vos rendez-vous.";
    exit();
}

// Annuler un rendez-vous
if (isset($_POST['cancel_rdv'])) {
    $id_coach = $_POST['id_coach'] ?? null;
    $date_rdv = $_POST['date_rdv'] ?? null;

    if ($id_coach === null || $date_rdv === null) {
        echo "Paramètres manquants pour l'annulation du rendez-vous.";
        exit();
    }

    list($jour_rdv, $heure_rdv) = explode(' ', $date_rdv, 2);

    // Suppression du rendez-vous
    $sql = "DELETE FROM prise_de_rendez_vous WHERE id_coach = ? AND id_client = ? AND jour_rdv = ? AND heure_rdv = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiss", $id_coach, $_SESSION['user_id'], $jour_rdv, $heure_rdv);

    if (mysqli_stmt_execute($stmt)) {
        echo "Rendez-vous annulé avec succès";
    } else {
        echo "Erreur lors de l'annulation du rendez-vous : " . mysqli_stmt_error($stmt);
    }
    exit();
}

// Ajouter un rendez-vous
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['cancel_rdv'])) {
    $id_coach = $_POST['id_coach'];
    $date_rdv = $_POST['date_rdv_date'] . ' ' . $_POST['date_rdv_time'];

    // Vérifier la disponibilité
    $sql_check = "SELECT * FROM prise_de_rendez_vous WHERE id_coach = ? AND heure_rdv = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "is", $id_coach, $date_rdv);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "Le créneau est déjà réservé.";
    } else {
        // Attribution d'une salle aléatoire (exemple avec 5 salles)
        $id_salle = rand(1, 5);

        $sql = "INSERT INTO prise_de_rendez_vous (id_client, id_coach, id_salle, jour_rdv, heure_rdv, statut_rdv) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiiss", $_SESSION['user_id'], $id_coach, $id_salle, $_POST['jour_rdv'], $_POST['heure_rdv']);

        if (mysqli_stmt_execute($stmt)) {
            echo "Rendez-vous pris avec succès!";
        } else {
            echo "Erreur lors de la prise du rendez-vous : " . mysqli_stmt_error($stmt);
        }
    }
    exit();
}

// Récupérer les rendez-vous de l'utilisateur
$sql = "SELECT rv.id_coach, rv.jour_rdv, rv.heure_rdv, rv.statut_rdv, c.nom_coach, c.prenom_coach, c.email_coach, c.specialite_coach, s.nom_salle 
        FROM prise_de_rendez_vous rv 
        JOIN coach c ON rv.id_coach = c.ID_coach 
        JOIN salle s ON rv.id_salle = s.id_salle 
        WHERE rv.id_client = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rendez_vous = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
        <h1>Vos Rendez-vous Confirmés</h1>
        <?php if (count($rendez_vous) > 0): ?>
            <?php foreach ($rendez_vous as $rdv): ?>
                <div class="rdv-details" data-coach-id="<?= $rdv['id_coach'] ?>" data-date-rdv="<?= $rdv['jour_rdv'] . ' ' . $rdv['heure_rdv'] ?>">
                    <h2>Rendez-vous avec <?= htmlspecialchars($rdv['nom_coach']) ?> <?= htmlspecialchars($rdv['prenom_coach']) ?></h2>
                    <p>Date et Heure: <?= htmlspecialchars($rdv['jour_rdv'] . ' ' . $rdv['heure_rdv']) ?></p>
                    <p>Salle: <?= htmlspecialchars($rdv['nom_salle']) ?></p>
                    <p>Email: <?= htmlspecialchars($rdv['email_coach']) ?></p>
                    <p>Spécialité: <?= htmlspecialchars($rdv['specialite_coach']) ?></p>
                    <button class="cancel-button" data-coach-id="<?= $rdv['id_coach'] ?>" data-date-rdv="<?= $rdv['jour_rdv'] . ' ' . $rdv['heure_rdv'] ?>">Annuler le RDV</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Vous n'avez aucun rendez-vous confirmé.</p>
        <?php endif; ?>

        <h1>Prendre un Nouveau Rendez-vous</h1>
        <form action="disponibilites.php" method="GET">
            <label for="id_coach">Choisir un coach:</label>
            <select id="id_coach" name="id_coach" required>
                <?php
                $sql = "SELECT * FROM coach";
                $result = mysqli_query($conn, $sql);

                while ($coach = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $coach['ID_coach'] . '">' . htmlspecialchars($coach['nom_coach']) . ' ' . htmlspecialchars($coach['prenom_coach']) . '</option>';
                }
                ?>
            </select><br>
            <button type="submit">Voir les Disponibilités</button>
        </form>
    </section>

    <footer>
        <p>&copy; 2024 Sportify</p>
    </footer>

    <script>
        // Gestion de l'annulation du rendez-vous
        document.querySelectorAll('.cancel-button').forEach(button => {
            button.addEventListener('click', function () {
                const coachId = this.dataset.coachId;
                const dateRdv = this.dataset.dateRdv;
                const confirmation = confirm("Êtes-vous sûr de vouloir annuler ce rendez-vous ?");
                if (confirmation) {
                    const formData = new FormData();
                    formData.append('cancel_rdv', '1');
                    formData.append('id_coach', coachId);
                    formData.append('date_rdv', dateRdv);

                    fetch('rendez_vous.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Vérifier si la suppression a été effectuée avec succès
                        if (data.includes('Rendez-vous annulé avec succès')) {
                            // Supprimer le rendez-vous de l'affichage
                            document.querySelector(`.rdv-details[data-coach-id="${coachId}"][data-date-rdv="${dateRdv}"]`).remove();
                        } else {
                            alert('Erreur lors de l\'annulation du rendez-vous : ' + data);
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
                }
            });
        });
    </script>
</body>

</html>
