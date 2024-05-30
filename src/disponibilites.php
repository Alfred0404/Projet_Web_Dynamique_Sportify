<?php
session_start();
include "db_connection.php";

// Initialiser les messages d'erreur ou de succès
$message = "";

// Démarrer le tampon de sortie pour capturer les erreurs
ob_start();

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

$id_coach = $_GET['id_coach'];

// Fonction pour récupérer les créneaux horaires disponibles
function getAvailableSlots($conn, $id_coach)
{
    $sql = "SELECT j.nom_jour, d.heure_debut, d.heure_fin, d.id_disponibilite
            FROM disponibilite d
            JOIN jour j ON d.id_jour = j.id_jour
            WHERE d.id_coach = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_coach);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $slots = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $slots[$row['nom_jour']][] = [
            'heure_debut' => $row['heure_debut'],
            'heure_fin' => $row['heure_fin'],
            'id_disponibilite' => $row['id_disponibilite']
        ];
    }

    return $slots;
}

// Fonction pour récupérer les créneaux horaires réservés
function getBookedSlots($conn, $id_coach)
{
    $sql = "SELECT jour_rdv, heure_rdv
            FROM prise_de_rendez_vous
            WHERE id_coach = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_coach);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $bookedSlots = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dayOfWeek = $row['jour_rdv'];
        $time = $row['heure_rdv'];
        $bookedSlots[$dayOfWeek][] = $time;
    }

    return $bookedSlots;
}

// Récupération des données du coach
$sql = "SELECT * FROM coach WHERE id_coach = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_coach);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$coach = mysqli_fetch_assoc($result);

$availableSlots = getAvailableSlots($conn, $id_coach);
$bookedSlots = getBookedSlots($conn, $id_coach);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilités du Coach</title>
    <link rel="stylesheet" href="css/disponibilites.css">
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
            <li class="nav-item"><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>

    <section>
        <div class="section-content">
            <table>
                <thead>
                    <tr>
                        <th>Coach</th>
                        <th>Spécialité</th>
                        <th>Lundi</th>
                        <th>Mardi</th>
                        <th>Mercredi</th>
                        <th>Jeudi</th>
                        <th>Vendredi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $hours = [
                        '08:00' => '8h-9h',
                        '09:00' => '9h-10h',
                        '10:00' => '10h-11h',
                        '11:00' => '11h-12h',
                        '12:00' => '12h-13h',
                        '13:00' => '13h-14h',
                        '14:00' => '14h-15h',
                        '15:00' => '15h-16h',
                        '16:00' => '16h-17h'
                    ];
                    foreach ($hours as $time => $label):
                        echo '<tr>';
                        if ($time == '08:00') {
                            echo '<td rowspan="9">' . htmlspecialchars($coach['nom_coach']) . ' ' . htmlspecialchars($coach['prenom_coach']) . '</td>';
                            echo '<td rowspan="9">' . htmlspecialchars($coach['specialite_coach']) . '</td>';
                        }
                        foreach (['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'] as $jour):
                            $isBooked = isset($bookedSlots[$jour]) && in_array($time, $bookedSlots[$jour]);
                            $isLunchTime = $time == '12:00';
                            $class = $isBooked ? 'taken' : ($isLunchTime ? 'unavailable' : 'available');
                            $content = $isLunchTime ? 'Pause Déjeuner' : $label;
                            echo '<td class="' . $class . '" data-jour="' . $jour . '" data-heure="' . $time . '">' . $content . '</td>';
                        endforeach;
                        echo '</tr>';
                    endforeach;
                    ?>
                </tbody>
            </table>
            <?php if (!$is_coach && !$is_admin): ?>
                <div style="text-align: right; margin-top: 20px;">
                    <button id="reserve-button" disabled>Réserver</button>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        let selectedCell = null;

        document.querySelectorAll('.available').forEach(cell => {
            cell.addEventListener('click', function () {
                if (selectedCell) {
                    selectedCell.classList.remove('selected');
                }
                selectedCell = this;
                selectedCell.classList.add('selected');
                document.getElementById('reserve-button').disabled = false;
            });
        });

        document.getElementById('reserve-button').addEventListener('click', function () {
            if (selectedCell) {
                const jour = selectedCell.dataset.jour;
                const heure = selectedCell.dataset.heure;
                const confirmation = confirm(`Voulez-vous réserver le créneau ${jour} de ${heure} à ${parseInt(heure.split(':')[0]) + 1}h ?`);
                if (confirmation) {
                    const formData = new FormData();
                    formData.append('id_coach', '<?php echo htmlspecialchars($id_coach); ?>');
                    formData.append('jour_rdv', jour);
                    formData.append('heure_rdv', heure);

                    fetch('rendez_vous.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.text())
                        .then(data => {
                            if (data.includes("Rendez-vous pris avec succès!")) {
                                selectedCell.classList.remove('available');
                                selectedCell.classList.add('taken');
                                selectedCell.innerText = 'Réservé';
                                document.getElementById('reserve-button').disabled = true;
                                // Redirection vers la page des rendez-vous
                                window.location.href = 'rendez_vous.php';
                            } else {
                                alert(data);
                            }
                        })
                        .catch(error => console.error('Erreur:', error));
                }
            }
        });
    </script>
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