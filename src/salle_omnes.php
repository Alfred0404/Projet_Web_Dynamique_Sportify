<?php
session_start();

include "db_connection.php";

$sql = "SELECT id_bulletin, titre_bulletin, contenu_bulletin FROM bulletin";
$result = $conn->query($sql);

$sql_activites = "SELECT id_activites, nom_activites, type_activites FROM activites";
$result_activites = $conn->query($sql_activites);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/salle_omnes.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="js/accueil.js"></script>
    <title>Sportify - Accueil</title>
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
            <li class="nav-item"><a href="rendez_vous.php">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
            <li class="nav-item"><a href="users.php">Discussions</a></li>
            <li class="nav-item"><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>
    <section class="first-section">
        <div class="section-content">
            <div class="presentation">
                <h1>Salle de sport omnes</h1>
                <p>Bienvenue à la salle de sport Omnes, votre destination ultime pour atteindre vos objectifs de
                    fitness et de bien-être ! Située au cœur de la ville, notre salle de sport moderne est équipée des
                    dernières innovations en matière d'équipements de musculation et de cardio-training. Que vous soyez
                    débutant ou athlète confirmé, notre équipe de coachs expérimentés est là pour vous accompagner et
                    vous motiver. Profitez également de nos cours collectifs variés, allant du yoga à la Zumba, dans une
                    ambiance conviviale et énergisante. Venez découvrir un espace dédié à votre forme et à votre santé,
                    et rejoignez une communauté dynamique et inspirante.</p>
                <h1 class="informations">Informations</h1>
                <div class="infos-salle">
                    <p><strong>adresse :</strong> 10 rue sextius michel</p>
                    <p><strong>Courriel :</strong> salle.omnes@edu.ece.fr</p>
                    <p><strong>Telephone :</strong> 01 37 48 12 98</p>
                    <p><strong>services :</strong> Cardio - Musculation - Gym - Boxe - Yoga</p>
                </div>
            </div>
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