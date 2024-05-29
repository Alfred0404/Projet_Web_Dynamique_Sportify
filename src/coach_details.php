<?php
session_start();

if (!isset($_SESSION['role']) || !isset($_SESSION['nom'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];


if (isset($_GET['logout'])) {
    $id_coach = $_GET['id'];
    // Message de sortie simple
    $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>" . $_SESSION['name'] . "</b> a quitté la session de chat.</span><br></div>";

    $log_file = __DIR__ . "/log_$id_coach.html";
    $myfile = fopen($log_file, "a") or die("Impossible d'ouvrir le fichier!");
    fwrite($myfile, $logout_message);
    fclose($myfile);
    session_destroy();
    sleep(1);
    header("Location: coach_details.php?id=" . $id_coach); // Rediriger l'utilisateur avec l'identifiant du coach
    exit();
}

if (isset($_POST['enter'])) {
    if ($_POST['name'] != "") {
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
    } else {
        echo '<span class="error">Veuillez saisir votre nom</span>';
    }
}

function loginForm($id_coach) {
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
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Sportify";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connexion échouée: " . $conn->connect_error);
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
    <title>Détails du Coach</title>
    <link rel="stylesheet" href="../src/css/coach_details.css" />
</head>
<body>
    <h1>Détails du Coach</h1>
    <p>Nom: <?php echo $nom_coach; ?></p>
    <p>Prénom: <?php echo $prenom_coach; ?></p>
    <p>Email: <?php echo $email_coach; ?></p>
    <p>Bureau: <?php echo $bureau_coach; ?></p>
    <p>CV: <?php echo $cv_coach; ?></p>

    <!-- Afficher la photo -->
    <?php if (!empty($photo_coach)) { ?>
    <img src="<?php echo $photo_coach; ?>" alt="Photo du Coach">
    <?php } ?>

    <?php
    // Vérifier les conditions d'accès à la chatroom
    if ($role == 'client') {
        // Chatroom
        if (!isset($_SESSION['name'])) {
            loginForm($id_coach);
        } else {
            $log_file = "log_$id_coach.html";
    ?>
            <div id="wrapper">
                <div id="menu">
                    <p class="welcome">Bienvenue, <b><?php echo $_SESSION['name']; ?></b></p>
                    <p class="logout"><a id="exit" href="#">Quitter la conversation</a></p>
                </div>
                <div id="chatbox">
                <?php
                if (file_exists($log_file) && filesize($log_file) > 0) {
                    $contents = file_get_contents($log_file);
                    echo $contents;
                }
                ?>
                </div>

                <form name="message" action="">
                    <input name="usermsg" type="text" id="usermsg" />
                    <input name="submitmsg" type="submit" id="submitmsg" value="Envoyer" />
                </form>
            </div>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script type="text/javascript">
            // jQuery Document
            $(document).ready(function () {
                $("#submitmsg").click(function () {
                    var clientmsg = $("#usermsg").val();
                    $.post("post.php?id=<?php echo $id_coach; ?>", { text: clientmsg });
                    $("#usermsg").val("");
                    return false;
                });

                function loadLog() {
                    var oldscrollHeight = $("#chatbox").prop("scrollHeight") - 20;
                    $.ajax({
                        url: "<?php echo $log_file; ?>",
                        cache: false,
                        success: function (html) {
                            $("#chatbox").html(html);

                            var newscrollHeight = $("#chatbox").prop("scrollHeight") - 20;
                            if (newscrollHeight > oldscrollHeight) {
                                $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal');
                            }
                        }
                    });
                }

                setInterval(loadLog, 2500);
                    $("#exit").click(function () {
                    var exit = confirm("Êtes-vous sûr de vouloir quitter la conversation?");
                    if (exit == true) {
                        window.location = "coach_details.php?logout=true&id=<?php echo $id_coach; ?>";
                    }
                });
            });
            </script>
        <?php
                }
    } else {
        echo "<p>Vous n'avez pas accès à cette chatroom.</p>";
    }
?>
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
