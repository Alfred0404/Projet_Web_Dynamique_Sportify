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

$id_coach = $_GET['id_coach'];

// Fonction pour récupérer les créneaux horaires disponibles
function getAvailableSlots($conn, $id_coach) {
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
function getBookedSlots($conn, $id_coach) {
    $sql = "SELECT date_rdv
            FROM prise_de_rendez_vous 
            WHERE id_coach = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_coach);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $bookedSlots = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $date = new DateTime($row['date_rdv']);
        $dayOfWeek = $date->format('l');
        $time = $date->format('H:i');
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
        <h1>Sportify - Disponibilités</h1>
    </header>

    <div class="nav">
        <ul>
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="parcourir.php">Tout parcourir</a></li>
            <li><a href="recherche.php">Rechercher</a></li>
            <li><a href="rendez_vous.php">Rendez-vous</a></li>
            <li><a href="compte.php">Votre compte</a></li>
        </ul>
    </div>

    <section>
        <h1>Disponibilités</h1>
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
                    '08:00' => '8h-9h', '09:00' => '9h-10h', '10:00' => '10h-11h', '11:00' => '11h-12h',
                    '12:00' => '12h-13h', '13:00' => '13h-14h', '14:00' => '14h-15h', '15:00' => '15h-16h', '16:00' => '16h-17h'
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
        <div style="text-align: right; margin-top: 20px;">
            <button id="reserve-button" disabled>Réserver</button>
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
                    formData.append('id_coach', <?= $id_coach ?>);
                    formData.append('date_rdv', `${new Date().toISOString().split('T')[0]} ${heure}:00`);

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
                        } else {
                            alert(data);
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
                }
            }
        });
    </script>
</body>

</html>
