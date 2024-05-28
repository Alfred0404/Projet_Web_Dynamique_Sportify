<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/parcourir.css">
    <title>Sportify - Parcourir</title>
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
            <li class="nav-item active"><a href="#">Tout parcourir</a></li>
            <li class="nav-item"><a href="recherche.php">Rechercher</a></li>
            <li class="nav-item"><a href="rendez_vous.php">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
        </ul>
    </div>
    <section class="first-section">
        <div class="section-content">
            <h1>Tout parcourir</h1>
        </div>
    </section>
    <section class="services">
        <h1>Nos services</h1>
        <form class="btns-activite" action="traitement.php" method="post">
            <button type="submit" name="button_parcourir" value="activite_sportive">
                <p>Activités sportives</p>
            </button>
            <button type="submit" name="button_parcourir" value="les_sports_de_competition">
                <p>Les Sports de Compétition</p>
            </button>
            <button type="submit" name="button_parcourir" value="salles_de_sport_omnes">
                <p>Salles de sport Omnes</p>
            </button>
        </form>
        <iframe name="hidden_iframe" style="display:none;"></iframe>
    </section>
    <footer>
        <p>© 2024 Sportify</p>
        <p>sportify@gmail.com</p>
        <p>01 38 67 18 52</p>
        <p>10 rue Sextius Michel - 75015 - Paris</p>
        <a href="#">Google Maps</a>
    </footer>
</body>

</html>