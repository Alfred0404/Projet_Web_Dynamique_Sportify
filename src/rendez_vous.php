<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/rendez_vous.css">
    <title>Sportify - Rendez-vous</title>
</head>

<body>
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
    </header>

    </div>
    <div class="nav">
        <ul>
            <li class="nav-item"><a href="accueil.php">Accueil</a></li>
            <li class="nav-item"><a href="parcourir.php">Tout parcourir</a></li>
            <li class="nav-item"><a href="recherche.php">Rechercher</a></li>
            <li class="nav-item active"><a href="#">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
        </ul>
    </div>
    <section class="first-section">
        <div class="section-content">
            <h1>Rendez-vous</h1>
            <p>(mettre ici le form pour prendre rdv avec le bon coach deja selctionné)</p>
        </div>
    </section>
    <section class="rdvs">
        <h1>Vos rendez-vous</h1>
    </section>
    <footer>
        <p>© 2024 Sportify</p>
        <p>sportify@gmail.com</p>
        <p>01 38 67 18 52</p>
        <p>10 rue Sextius Michel - 75015 - Paris</p>
        <a class="lien-gmaps" href="https://www.google.fr/maps/place/10+Rue+Sextius+Michel,+75015+Paris/@48.8511413,2.2860178,17z/data=!3m1!4b1!4m6!3m5!1s0x47e67151e3c16d05:0x1e3446766ada1337!8m2!3d48.8511378!4d2.2885927!16s%2Fg%2F11jy_4vh_c?entry=ttu">Google Maps</a>
    </footer>
</body>

</html>

<?php
if (isset($_POST['button_coach'])) {
    $button_parcourir = $_POST['button_coach'];
    // Effectuer les actions nécessaires avec le texte du bouton
    echo "Le texte du bouton cliqué est : " . htmlspecialchars($button_coach);
} else {
    echo "Aucun texte de bouton reçu.";
}
?>