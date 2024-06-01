<?php
session_start();
if (isset($_GET['logout'])) {
    // Message de sortie simple
    $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>" . $_SESSION['name'] . "</b> a quitté la session de chat.</span><br></div>";

    $myfile = fopen(__DIR__ . "/log.html", "a") or die("Impossible d'ouvrir le fichier!" . __DIR__ . "/log.html");
    fwrite($myfile, $logout_message);
    fclose($myfile);
    session_destroy();
    sleep(1);

    if (isset($_GET['id'])) {
        header("Location: coach_details.php?id=" . $_GET['id']); // Rediriger l'utilisateur avec l'identifiant du coach
    } else {
        header("Location: coach_details.php");
    }
    exit();
}

if (isset($_POST['enter'])) {
    if ($_POST['name'] != "") {
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
    } else {
        echo '<span class="error">Veuillez saisir votre nom</span>';
    }
}

function loginForm($id_coach)
{
    echo
        '<div id="loginform">
    <p>Veuillez saisir votre nom pour continuer!</p>
    <form action="coach_details.php?id=' . $id_coach . '" method="post">
    <label for="name">Nom: </label>
    <input type="text" name="name" id="name" />
    <input type="submit" name="enter" id="enter" value="Soumettre" />
    </form>
    </div>';
}

// Vérifier si un identifiant de coach est passé en paramètre
if (isset($_GET['id'])) {
    // Récupérer l'identifiant du coach depuis l'URL
    $id_coach = intval($_GET['id']);

    // Connexion à la base de données
    $user_name = "root";
    $password = "";
    $database = "sportify";
    $server = "127.0.0.1";
    $port = 3301;

    $conn = mysqli_connect($server, $user_name, $password, $database, $port);

    if (!$conn) {
        echo "le port 3301 ne marche pas";
        $port = 3306;
        $conn = mysqli_connect($server, $user_name, $password, $database, $port);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    // Récupérer les informations du coach
    $sql = "SELECT nom_coach, prenom_coach, email_coach, cv_coach, bureau_coach, photo_coach FROM coach WHERE id_coach = $id_coach";
    $result = $conn->query($sql);


    // Vérifier si le coach existe
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nom_coach = $row["nom_coach"];
        $prenom_coach = $row["prenom_coach"];
        $email_coach = $row["email_coach"];
        $cv_coach = $row["cv_coach"];
        $bureau_coach = $row["bureau_coach"];
        $photo_coach = $row["photo_coach"];
        ?>

        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="../src/css/coach_details.css">
            <script src="js/activites_sportives.js"></script>
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
                    <li class="nav-item"><a href="logout.php">Déconnexion</a></li>
                </ul>
            </div>
            <section class="first-section">
                <div class="section-content">
                    <div class="coach-card">
                        <h1><?php echo ucfirst($nom_coach) . " " . ucfirst($prenom_coach); ?></h1>
                        <div class="container">
                            <?php if (!empty($photo_coach)) { ?>
                                <img class="photo-coach" src="<?php echo $photo_coach; ?>" alt="Photo du Coach">
                            <?php } ?>
                            <div class="infos-coach">
                                <p>Coach</p>
                                <p>Id : <?php echo $id_coach; ?></p>
                                <p>Bureau : <?php echo $bureau_coach; ?></p>
                                <p>Téléphone : (numéro de téléphone)</p>
                                <p>Email : <?php echo $email_coach; ?></p>
                            </div>
                        </div>

                        <form class="btns-coach" action="disponibilites.php" method="get">
                            <button type="submit" name="button_coach" value="rendez-vous">
                                <!-- send $id_coach -->
                                <input type="hidden" name="id_coach" value="<?php echo $id_coach; ?>">
                                <p>Prendre rendez-vous</p>
                            </button>
                            <button type="submit" name="button_coach" value="contacter">
                                <p>Contacter le coach</p>
                            </button>
                        </form>

                        <div class="cv-section">
                            <button onclick="toggleCV()">Voir le CV</button>
                            <div id="cv-content" class="cv-content">
                                <?php
                                if (isset($cv_coach)) {
                                    $cv_path = "cvs/" . htmlspecialchars($cv_coach);
                                    if (file_exists($cv_path)) {
                                        $cv_content = file_get_contents($cv_path);
                                        $xml = simplexml_load_string($cv_content);

                                        // Affichage du CV en HTML
                                        echo '<div class="card ">';
                                        echo '<div class="card-body">';
                                        echo '<h2 class="card-title">Informations Personnelles</h2>';
                                        echo '<p><strong>Prénom:</strong> ' . htmlspecialchars($xml->PersonalInformation->FirstName) . '</p>';
                                        echo '<p><strong>Nom:</strong> ' . htmlspecialchars($xml->PersonalInformation->LastName) . '</p>';
                                        echo '<p><strong>Date de naissance:</strong> ' . htmlspecialchars($xml->PersonalInformation->DateOfBirth) . '</p>';
                                        echo '<p><strong>Email:</strong> <a id="mail-link" href="mailto:' . htmlspecialchars($xml->PersonalInformation->Email) . '">' . htmlspecialchars($xml->PersonalInformation->Email) . '</a></p>';
                                        echo '<p><strong>Téléphone:</strong> ' . htmlspecialchars($xml->PersonalInformation->PhoneNumber) . '</p>';
                                        echo '<h2>Adresse</h2>';
                                        echo '<p><strong>Rue:</strong> ' . htmlspecialchars($xml->PersonalInformation->Address->Street) . '</p>';
                                        echo '<p><strong>Ville:</strong> ' . htmlspecialchars($xml->PersonalInformation->Address->City) . '</p>';
                                        echo '<p><strong>Code postal:</strong> ' . htmlspecialchars($xml->PersonalInformation->Address->PostalCode) . '</p>';
                                        echo '<p><strong>Pays:</strong> ' . htmlspecialchars($xml->PersonalInformation->Address->Country) . '</p>';
                                        echo '<h2>Résumé Professionnel</h2>';
                                        echo '<p>' . htmlspecialchars($xml->ProfessionalSummary->Summary) . '</p>';
                                        echo '<h2>Éducation</h2>';
                                        echo '<p><strong>Diplôme:</strong> ' . htmlspecialchars($xml->Education->Degree->Title) . '</p>';
                                        echo '<h2>Compétences</h2>';
                                        echo '<p>' . htmlspecialchars($xml->Skills->Skill) . '</p>';
                                        echo '<h2>Langues</h2>';
                                        foreach ($xml->Languages->Language as $language) {
                                            echo '<div class="mb-2">';
                                            echo '<p><strong>Langue:</strong> ' . htmlspecialchars($language->Name) . '</p>';
                                            echo '<p><strong>Niveau:</strong> ' . htmlspecialchars($language->Proficiency) . '</p>';
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        echo '</div>';
                                    } else {
                                        echo '<div class="alert alert-warning" role="alert">Le CV n\'a pas été trouvé.</div>';
                                    }
                                }
                                ?>
                            </div>
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
            <script>
                function toggleCV() {
                    var element = document.getElementById("cv-content");
                    if (element.style.display === "none" || element.style.display === "") {
                        element.style.display = "block";
                    } else {
                        element.style.display = "none";
                    }
                }
            </script>
        </body>

        </html>

        <?php
    } else {
        echo "Aucun coach trouvé avec cet identifiant.";
    }

    // Fermer la connexion
    $conn->close();
} else {
    echo "Identifiant de coach non spécifié.";
}
?>