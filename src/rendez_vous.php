<?php
session_start();
$user_name = "root";
$password = "";
$database = "sportify"; // Utilisez le nom correct de votre base de données
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

// Emploi du temps des coachs
$schedule = [
    'DUMAIS, Guy' => [
        'Monday' => ['14:00', '15:00', '16:00', '17:00'],
        'Wednesday' => ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'],
        'Friday' => ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00']
    ]
];

// Annuler un rendez-vous
if (isset($_POST['cancel_rdv'])) {
    $id_salle = $_POST['id_salle'];

    $sql = "DELETE FROM prise_de_rendez_vous WHERE id_salle = ? AND id_client = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id_salle, $_SESSION['user_id']);

    if (mysqli_stmt_execute($stmt)) {
        // Rediriger vers la même page après annulation pour actualiser la liste des rendez-vous
        header("Location: rendez_vous.php");
        exit();
    } else {
        echo "Erreur lors de l'annulation du rendez-vous : " . mysqli_stmt_error($stmt);
    }
}

// Ajouter un rendez-vous
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_rdv'])) {
    $id_coach = $_POST['id_coach'];
    $date_rdv_date = $_POST['date_rdv_date'];
    $date_rdv_time = $_POST['date_rdv_time'];
    $date_rdv = $date_rdv_date . ' ' . $date_rdv_time;

    // Vérifier la disponibilité
    $sql_check = "SELECT * FROM prise_de_rendez_vous WHERE id_coach = ? AND date_rdv = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "is", $id_coach, $date_rdv);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "Le coach n'est pas disponible à ce créneau horaire. Veuillez choisir un autre créneau.";
    } else {
        $sql = "INSERT INTO prise_de_rendez_vous (id_client, id_coach, date_rdv, statut_rdv) VALUES (?, ?, ?, 1)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $_SESSION['user_id'], $id_coach, $date_rdv);
        mysqli_stmt_execute($stmt);

        echo "Rendez-vous pris avec succès!";
        header("Location: rendez_vous.php");
        exit();
    }
}

// Récupérer les rendez-vous de l'utilisateur
$sql = "SELECT rv.id_salle, rv.date_rdv, rv.statut_rdv, c.nom_coach, c.prenom_coach, c.email_coach, c.specialite_coach 
        FROM prise_de_rendez_vous rv 
        JOIN coach c ON rv.id_coach = c.ID_coach 
        WHERE rv.id_client = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rendez_vous = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Récupérer les créneaux horaires disponibles
function getAvailableSlots($conn, $id_coach, $date, $schedule) {
    $dayOfWeek = date('l', strtotime($date)); // Nom du jour de la semaine (e.g., Monday)
    $slots = isset($schedule[$id_coach][$dayOfWeek]) ? $schedule[$id_coach][$dayOfWeek] : [];
    $availableSlots = [];

    foreach ($slots as $slot) {
        $datetime = "$date $slot:00";
        $sql = "SELECT * FROM prise_de_rendez_vous WHERE id_coach = ? AND date_rdv = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $id_coach, $datetime);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0) {
            $availableSlots[] = $slot;
        }
    }

    return $availableSlots;
}

if (isset($_GET['id_coach']) && isset($_GET['date'])) {
    $id_coach = $_GET['id_coach'];
    $date = $_GET['date'];
    $availableSlots = getAvailableSlots($conn, $id_coach, $date, $schedule);
    header('Content-Type: application/json');
    echo json_encode($availableSlots);
    exit();
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
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="parcourir.php">Tout parcourir</a></li>
            <li><a href="recherche.php">Rechercher</a></li>
            <li><a href="#">Rendez-vous</a></li>
            <li><a href="compte.php">Votre compte</a></li>
        </ul>
    </div>

    <section>
        <h1>Vos Rendez-vous Confirmés</h1>
        <?php if (count($rendez_vous) > 0): ?>
            <?php foreach ($rendez_vous as $rdv): ?>
                <div class="rdv-details" data-rdv-id="<?= $rdv['id_salle'] ?>">
                    <h2>Rendez-vous avec <?= htmlspecialchars($rdv['nom_coach']) ?> <?= htmlspecialchars($rdv['prenom_coach']) ?></h2>
                    <p>Date et Heure: <?= htmlspecialchars($rdv['date_rdv']) ?></p>
                    <p>Email: <?= htmlspecialchars($rdv['email_coach']) ?></p>
                    <p>Spécialité: <?= htmlspecialchars($rdv['specialite_coach']) ?></p>
                    <button class="cancel-button" data-rdv-id="<?= $rdv['id_salle'] ?>">Annuler le RDV</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Vous n'avez aucun rendez-vous confirmé.</p>
        <?php endif; ?>

        <h1>Prendre un Nouveau Rendez-vous</h1>
        <form action="rendez_vous.php" method="POST">
            <input type="hidden" name="add_rdv" value="1">
            <label for="id_coach">Choisir un coach:</label>
            <select id="id_coach" name="id_coach" required>
                <?php
                $sql = "SELECT * FROM coach";
                $result = mysqli_query($conn, $sql);

                while ($coach = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $coach['ID_coach'] . '">' . $coach['nom_coach'] . ' ' . $coach['prenom_coach'] . '</option>';
                }
                ?>
            </select><br>
            <label for="date_rdv">Date du rendez-vous:</label>
            <input type="date" id="date_rdv" name="date_rdv_date" required><br>
            <label for="time_rdv">Heure du rendez-vous:</label>
            <select id="time_rdv" name="date_rdv_time" required>
                <!-- Les créneaux horaires seront générés dynamiquement via JavaScript -->
            </select><br>
            <button type="submit">Prendre rendez-vous</button>
        </form>
    </section>

    <footer>
        <p>&copy; 2024 Sportify</p>
    </footer>

    <script>
        document.getElementById('id_coach').addEventListener('change', function () {
            const coachId = this.value;
            const dateInput = document.getElementById('date_rdv');
            const timeSelect = document.getElementById('time_rdv');

            dateInput.addEventListener('change', function () {
                const date = this.value;
                fetch(`rendez_vous.php?id_coach=${coachId}&date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        timeSelect.innerHTML = '';
                        data.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot;
                            option.textContent = slot;
                            timeSelect.appendChild(option);
                        });
                    });
            });
        });

        // Gestion de l'annulation du rendez-vous
        document.querySelectorAll('.cancel-button').forEach(button => {
            button.addEventListener('click', function () {
                const rdvId = this.dataset.rdvId;
                const confirmation = confirm("Êtes-vous sûr de vouloir annuler ce rendez-vous ?");
                if (confirmation) {
                    const formData = new FormData();
                    formData.append('cancel_rdv', '1');
                    formData.append('id_salle', rdvId);

                    fetch('rendez_vous.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Supprimer le rendez-vous de l'affichage
                        document.querySelector(`.rdv-details[data-rdv-id="${rdvId}"]`).remove();
                    })
                    .catch(error => console.error('Erreur:', error));
                }
            });
        });
    </script>
</body>

</html>
