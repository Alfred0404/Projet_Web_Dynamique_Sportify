<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/recherche.css">
    <title>Sportify - Recherche</title>
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
            <li class="nav-item"><a href="#">Rechercher</a></li>
            <li class="nav-item"><a href="rendez_vous">Rendez-vous</a></li>
            <li class="nav-item"><a href="compte.php">Votre compte</a></li>
        </ul>
    </div>
    <section class="first-section">
        <div class="section-content">
            <h1>Rechercher</h1>
            <form action="traitement.php" class="search-bar">
                <input class="search-field" type="text" name="search" placeholder="Rechercher...">
                <button class="search-submit" type="submit">Rechercher</button>
            </form>
        </div>
    </section>
    <section class="resultats">
        <h1>Résultats</h1>
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