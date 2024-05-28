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
    $id_coach = $_POST['id_coach'];
    $date_rdv = $_POST['date_rdv'];

    $sql = "DELETE FROM prise_de_rendez_vous WHERE id_coach = ? AND id_client = ? AND date_rdv = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $id_coach, $_SESSION['user_id'], $date_rdv);

    if (mysqli_stmt_execute($stmt)) {
        echo "Rendez-vous annulé avec succès";
    } else {
        echo "Erreur lors de l'annulation du rendez-vous : " . mysqli_stmt_error($stmt);
    }
    exit();
}

// Récupérer les rendez-vous de l'utilisateur
$sql = "SELECT rv.id_coach, rv.date_rdv, rv.statut_rdv, c.nom_coach, c.prenom_coach, c.email_coach, c.specialite_coach 
        FROM prise_de_rendez_vous rv 
        JOIN coach c ON rv.id_coach = c.ID_coach 
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
                <div class="rdv-details" data-coach-id="<?= $rdv['id_coach'] ?>" data-date-rdv="<?= $rdv['date_rdv'] ?>">
                    <h2>Rendez-vous avec <?= htmlspecialchars($rdv['nom_coach']) ?> <?= htmlspecialchars($rdv['prenom_coach']) ?></h2>
                    <p>Date et Heure: <?= htmlspecialchars($rdv['date_rdv']) ?></p>
                    <p>Email: <?= htmlspecialchars($rdv['email_coach']) ?></p>
                    <p>Spécialité: <?= htmlspecialchars($rdv['specialite_coach']) ?></p>
                    <button class="cancel-button" data-coach-id="<?= $rdv['id_coach'] ?>" data-date-rdv="<?= $rdv['date_rdv'] ?>">Annuler le RDV</button>
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
                    echo '<option value="' . $coach['ID_coach'] . '">' . $coach['nom_coach'] . ' ' . $coach['prenom_coach'] . '</option>';
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
