<?php
session_start();
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}
// Vérifier le rôle de l'utilisateur
$is_admin = $_SESSION['role'] === 'admin';
$is_coach = $_SESSION['role'] === 'coach';
$is_client = $_SESSION['role'] === 'client';

if ($is_client) {
    // Récupérer l'ID du client à partir de la base de données en fonction du nom d'utilisateur
    $sql = "SELECT id_client FROM client WHERE nom_client = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("[get client id] Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("s", $_SESSION['user_name']); // Supposons que 'user_name' contienne le nom d'utilisateur
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $client_id_from_database = $row['id_client'];
    } else {
        echo "[get client id] Erreur : Aucun client trouvé avec ce nom d'utilisateur.";
    }
    $stmt->close();
}


// Fonction pour valider et sécuriser les entrées utilisateur
function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Mise à jour des informations de la carte bancaire du client
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_carte_bancaire'])) {
    if ($is_client) {
        // Récupérer l'ID du client connecté à partir de la session
        $id_client = $_SESSION['user_id'];

        // Valider et récupérer les données du formulaire
        $nom = validate($_POST['nom']);
        $prenom = validate($_POST['prenom']);
        $adresse_ligne_1 = validate($_POST['adresse_ligne_1']);
        $adresse_ligne_2 = validate($_POST['adresse_ligne_2']);
        $ville = validate($_POST['ville']);
        $code_postal = validate($_POST['code_postal']);
        $pays = validate($_POST['pays']);
        $carte_etudiant_client = validate($_POST['carte_etudiant_client']);

        // Requête SQL pour mettre à jour les informations de la carte bancaire dans la table client
        $sql = "UPDATE client SET nom_carte = ?, prenom_carte = ?, adresse_ligne_1_carte = ?, adresse_ligne_2_carte = ?, ville_carte = ?, code_postal_carte = ?, pays_carte = ?, carte_etudiant_client = ? WHERE id_client = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("[maj paiement] Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("ssssssssi", $nom, $prenom, $adresse_ligne_1, $adresse_ligne_2, $ville, $code_postal, $pays, $carte_etudiant_client, $id_client);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "[maj paiement] Informations de carte bancaire mises à jour avec succès.";
        } else {
            echo "[maj paiement] Erreur: " . $sql . "<br>" . $conn->error;
        }
        $stmt->close();
    } else {
        echo "[maj paiement] Vous n'avez pas les autorisations nécessaires pour mettre à jour ces informations.";
    }
}

// Sauvegarde du CV
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_cv'])) {
    if ($is_coach) {
        $firstName = validate($_POST['FirstName']);
        $lastName = validate($_POST['LastName']);
        $dateOfBirth = validate($_POST['DateOfBirth']);
        $email = validate($_POST['Email']);
        $phoneNumber = validate($_POST['PhoneNumber']);
        $street = validate($_POST['Street']);
        $city = validate($_POST['City']);
        $postalCode = validate($_POST['PostalCode']);
        $country = validate($_POST['Country']);
        $summary = validate($_POST['Summary']);
        $degreeTitle = validate($_POST['DegreeTitle']);
        $skills = validate($_POST['Skills']);
        $languageName = validate($_POST['LanguageName']);
        $proficiency = validate($_POST['Proficiency']);

        // Création du fichier XML
        $cvFileName = "cv_{$lastName}_{$firstName}.xml";
        $xmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <CoachCV>
            <PersonalInformation>
                <FirstName>{$firstName}</FirstName>
                <LastName>{$lastName}</LastName>
                <DateOfBirth>{$dateOfBirth}</DateOfBirth>
                <Email>{$email}</Email>
                <PhoneNumber>{$phoneNumber}</PhoneNumber>
                <Address>
                    <Street>{$street}</Street>
                    <City>{$city}</City>
                    <PostalCode>{$postalCode}</PostalCode>
                    <Country>{$country}</Country>
                </Address>
            </PersonalInformation>
            <ProfessionalSummary>
                <Summary>{$summary}</Summary>
            </ProfessionalSummary>
            <Education>
                <Degree>
                    <Title>{$degreeTitle}</Title>
                </Degree>
            </Education>
            <Skills>
                <Skill>{$skills}</Skill>
            </Skills>
            <Languages>
                <Language>
                    <Name>{$languageName}</Name>
                    <Proficiency>{$proficiency}</Proficiency>
                </Language>
            </Languages>
        </CoachCV>";

        // Vérifier si le dossier cvs existe, sinon le créer
        if (!file_exists('cvs')) {
            mkdir('cvs', 0777, true);
        }

        // Enregistrement du fichier XML
        file_put_contents("cvs/{$cvFileName}", $xmlContent);

        // Mise à jour du chemin du CV dans la base de données
        $sql = "UPDATE coach SET cv_coach=? WHERE email_coach=?";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("[save cv] Erreur de préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("ss", $cvFileName, $_SESSION['email']);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "[save cv] CV mis à jour avec succès.";
        }
    } else {
        echo "[save cv] Vous n'avez pas les autorisations nécessaires pour sauvegarder le CV.";
    }
}

// ? Mise à jour des informations du coach
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_coach'])) {
    if ($is_coach) {
        $bureau = validate($_POST['bureau']);
        $specialite = validate($_POST['specialite']);
        $photo = validate($_POST['photo']);
        $telephone = validate($_POST['telephone']);

        $sql = "UPDATE coach SET bureau_coach=?, specialite_coach=?, photo_coach=?, telephone_coach=? WHERE email_coach=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("[maj coach] Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $bureau, $specialite, $photo, $telephone, $_SESSION['email']);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "[maj coach] Informations mises à jour avec succès.";
        } else {
            echo "[maj coach] Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "[maj coach] Vous n'avez pas les autorisations nécessaires pour mettre à jour ces informations.";
    }
}

// ? Mise à jour des informations du client
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_client'])) {
    if ($is_client) {
        $date_naissance = validate($_POST['date_naissance']);
        $telephone = validate($_POST['telephone']);
        $profession = validate($_POST['profession']);

        $sql = "UPDATE client SET date_de_naissance=?, num_telephone=?, profession=? WHERE email_client=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("[maj client] Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("ssss", $date_naissance, $telephone, $profession, $_SESSION['email']);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "[maj client] Informations mises à jour avec succès.";
        } else {
            echo "[maj client] Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "[maj client] Vous n'avez pas les autorisations nécessaires pour mettre à jour ces informations.";
    }
}

// ? Récupérer les informations du coach
if ($is_coach) {
    $sql = "SELECT bureau_coach, specialite_coach, photo_coach, telephone_coach, cv_coach FROM coach WHERE email_coach=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("[coach infos] Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $coach_info = $result->fetch_assoc();
        $bureau_coach = $coach_info['bureau_coach'];
        $specialite_coach = $coach_info['specialite_coach'];
        $photo_coach = $coach_info['photo_coach'];
        $telephone_coach = $coach_info['telephone_coach'];
        $cv_coach = $coach_info['cv_coach'];
    }
}

// ? Récupérer les informations du client
if ($is_client) {
    $sql = "SELECT date_de_naissance, num_telephone, profession FROM client WHERE email_client=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("[client infos] Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $client_info = $result->fetch_assoc();
        $date_naissance = $client_info['date_de_naissance'];
        $telephone = $client_info['num_telephone'];
        $profession = $client_info['profession'];
    }
}

// ? Récupérer les informations de paiement du client
if ($is_client) {
    $sql = "SELECT id_paiement, facture, date_paiement, type_carte, numero_carte, nom_carte FROM paiement WHERE id_client=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("[paiement] Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $paiements = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $paiements[] = $row;
        }
    }
    $stmt->close();
}

// Inscription d'un nouveau coach
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_coach'])) {
    if ($is_admin) {
        $nom = validate($_POST['name']);
        $prenom = validate($_POST['prenom']);
        $sexe = validate($_POST['sexe']);
        $email = validate($_POST['email']);
        $password = validate($_POST['password']);

        // création de la requete SQL
        $sql = "INSERT INTO coach (nom_coach, prenom_coach, sexe_coach, email_coach, mdp_coach) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("[create coach] Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $nom, $prenom, $sexe, $email, $password);
        $stmt->execute();

        // vérification de la création du compte
        if ($stmt->affected_rows === 1) {
            echo "[create coach] Compte coach créé avec succès.";
            // Enregistrement dans la table users
            $unique_id = rand(time(), 100000000);
            $fname = "coach";
            $img = "image_coach/defaut.jpg";
            $status = "En ligne";

            $user_sql = "INSERT INTO users (unique_id, fname, lname, email, password, img, status) VALUES ('$unique_id', '$fname', '$nom', '$email', '$password', '$img', '$status')";
            // vérification de l'insertion
            if ($conn->query($user_sql) === TRUE) {
                echo "[create coach] Compte coach créé avec succès.";
                header("Location: compte.php");
                exit();
            } else {
                return "[create coach] Erreur lors de l'inscription dans la table users: " . $user_sql . "<br>" . $conn->error;
            }
        } else {
            echo "[create coach] Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "[create coach] Vous n'avez pas les autorisations nécessaires pour créer un compte coach.";
    }
}

// Inscription d'un nouvel administrateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_admin'])) {
    if ($is_admin) {
        $nom = validate($_POST['name']);
        $prenom = validate($_POST['prenom']);
        $sexe = validate($_POST['sexe']);
        $email = validate($_POST['email']);
        $password = validate($_POST['password']);

        // création de la requete SQL
        $sql = "INSERT INTO admin (nom_admin, prenom_admin, sexe_admin, email_admin, mdp_admin) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("[create admin] Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $nom, $prenom, $sexe, $email, $password);
        $stmt->execute();

        // vérification de la création du compte
        if ($stmt->affected_rows === 1) {
            echo "[create admin] Compte administrateur créé avec succès.";
            // Enregistrement dans la table users
            $unique_id = rand(time(), 100000000);
            $fname = "admin";
            $img = "image_admin/defaut.jpg";
            $status = "En ligne";

            $user_sql = "INSERT INTO users (unique_id, fname, lname, email, password, img, status) VALUES ('$unique_id', '$fname', '$nom', '$email', '$password', '$img', '$status')";
            // vérification de l'insertion
            if ($conn->query($user_sql) === TRUE) {
                echo "[create admin] Compte administrateur créé avec succès.";
                header("Location: compte.php");
                exit();
            } else {
                return "[create admin] Erreur lors de l'inscription dans la table users: " . $user_sql . "<br>" . $conn->error;
            }
        } else {
            echo "[create admin] Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "[create admin] Vous n'avez pas les autorisations nécessaires pour créer un compte administrateur.";
    }
}

// suppression d'un coach
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_coach'])) {
    if ($is_admin) {
        $id_coach = validate($_POST['id_coach']);

        $sql = "DELETE FROM coach WHERE id_coach=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("[delete coach] Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("i", $id_coach);
        $stmt->execute();

        // vérification de la suppression
        if ($stmt->affected_rows === 1) {
            echo "[delete coach] Compte coach supprimé avec succès.";
        } else {
            echo "[delete coach] Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "[delete coach] Vous n'avez pas les autorisations nécessaires pour supprimer un compte coach.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/compte.css">
    <title>Votre compte</title>
</head>

<body>
    <header>
        <h1 class="title">Sportify</h1>
        <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
    </header>
    <div class="nav">
        <ul>
            <li class="nav-item"><a class="text-decoration-none" href="accueil">Accueil</a></li>
            <li class="nav-item"><a class="text-decoration-none" href="parcourir.php">Tout parcourir</a></li>
            <li class="nav-item"><a class="text-decoration-none" href="recherche.php">Rechercher</a></li>
            <li class="nav-item"><a class="text-decoration-none" href="rendez_vous.php">Rendez-vous</a></li>
            <li class="nav-item active"><a class="text-decoration-none" href="#">Votre compte</a></li>
            <li class="nav-item"><a href="users.php">Discussions</a></li>
            <li class="nav-item"><a class="text-decoration-none" href="logout.php">Déconnexion</a></li>
        </ul>
    </div>
    <section>
        <div class="container client-infos">
            <h2>Informations du compte</h2>
            <p><strong>Nom :</strong> <?php echo $_SESSION['user_name']; ?></p>
            <p><strong>Prénom :</strong> <?php echo $_SESSION['prenom']; ?></p>
            <p><strong>Email :</strong> <?php echo $_SESSION['email']; ?></p>
            <p><strong>Rôle :</strong> <?php echo ucfirst($_SESSION['role']); ?></p>
            <?php if ($is_coach): ?>
                <p><strong>Bureau :</strong> <?php echo $bureau_coach ?? ''; ?></p>
                <p><strong>Spécialité :</strong> <?php echo $specialite_coach ?? ''; ?></p>
                <p><strong>Téléphone :</strong> <?php echo $telephone_coach ?? ''; ?></p>
                <p><img src="<?php echo $photo_coach ?? ''; ?>" alt="Photo du coach"></p>
                <div class="check-cv">
                    <button onclick="toggleDropdown('cv-view')">Voir le CV</button>
                </div>
                <div id="cv-view" class="cv-container container mt-5 w-100">
                    <?php
                    if (isset($cv_coach)) {
                        $cv_path = "cvs/" . htmlspecialchars($cv_coach);
                        if (file_exists($cv_path)) {
                            $cv_content = file_get_contents($cv_path);
                            $xml = simplexml_load_string($cv_content);

                            // Affichage du CV en HTML
                            echo '<div class="card mb-4">';
                            echo '<div class="card-body">';
                            echo '<h3 class="card-title mb-4">Informations Personnelles</h3>';
                            echo '<p><strong>Prénom:</strong> ' . htmlspecialchars($xml->PersonalInformation->FirstName) . '</p>';
                            echo '<p><strong>Nom:</strong> ' . htmlspecialchars($xml->PersonalInformation->LastName) . '</p>';
                            echo '<p><strong>Date de naissance:</strong> ' . htmlspecialchars($xml->PersonalInformation->DateOfBirth) . '</p>';
                            echo '<p><strong>Email:</strong> ' . htmlspecialchars($xml->PersonalInformation->Email) . '</p>';
                            echo '<p><strong>Téléphone:</strong> ' . htmlspecialchars($xml->PersonalInformation->PhoneNumber) . '</p>';
                            echo '<h4 class="mt-4">Adresse</h4>';
                            echo '<p><strong>Rue:</strong> ' . htmlspecialchars($xml->PersonalInformation->Address->Street) . '</p>';
                            echo '<p><strong>Ville:</strong> ' . htmlspecialchars($xml->PersonalInformation->Address->City) . '</p>';
                            echo '<p><strong>Code postal:</strong> ' . htmlspecialchars($xml->PersonalInformation->Address->PostalCode) . '</p>';
                            echo '<p><strong>Pays:</strong> ' . htmlspecialchars($xml->PersonalInformation->Address->Country) . '</p>';
                            echo '<h3 class="mt-4">Résumé Professionnel</h3>';
                            echo '<p>' . htmlspecialchars($xml->ProfessionalSummary->Summary) . '</p>';
                            echo '<h3 class="mt-4">Éducation</h3>';
                            echo '<p><strong>Diplôme:</strong> ' . htmlspecialchars($xml->Education->Degree->Title) . '</p>';
                            echo '<h3 class="mt-4">Compétences</h3>';
                            echo '<p>' . htmlspecialchars($xml->Skills->Skill) . '</p>';
                            echo '<h3 class="mt-4">Langues</h3>';
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
            <?php endif; ?>
            <?php if ($is_client): ?>
                <p><strong>Date de naissance :</strong> <?php echo $date_naissance ?? ''; ?></p>
                <p><strong>Numéro de téléphone :</strong> <?php echo $telephone ?? ''; ?></p>
                <p><strong>Profession :</strong> <?php echo $profession ?? ''; ?></p>
            <?php endif; ?>
            <form method="get" action="accueil.php">
                <button type="submit" class="btn-back">Retour à l'accueil</button>
            </form>
            <form method="post" action="logout.php">
                <button class="btn-logout" type="submit">Déconnexion</button>
            </form>
        </div>

        <?php if ($is_admin): ?>
            <div class="container create-coach">
                <h2>Créer un compte admin</h2>
                <form for="register_admin" method="post" action="compte.php">
                    <input type="hidden" name="register_admin" value="1">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required>
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" required>
                    <label for="sexe">Sexe :</label>
                    <input type="text" id="sexe" name="sexe" required>
                    <label for="email">Adresse Email :</label>
                    <input type="email" id="email" name="email" required>
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                    <button type="submit">Créer un compte admin</button>
                </form>

                <h2>Créer un compte coach</h2>
                <form for="register_coach" method="post" action="compte.php">
                    <input type="hidden" name="register_coach" value="1">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required>
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" required>
                    <label for="sexe">Sexe :</label>
                    <input type="text" id="sexe" name="sexe" required>
                    <label for="email">Adresse Email :</label>
                    <input type="email" id="email" name="email" required>
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                    <button type="submit">Créer un compte coach</button>
                </form>

                <h2>Supprimer un compte coach</h2>
                <form for="delete_coach" method="post" action="compte.php">
                    <input type="hidden" name="delete_coach" value="1">
                    <label for="id_coach">ID du coach :</label>
                    <?php
                    $sql = "SELECT id_coach, nom_coach, prenom_coach FROM coach";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        echo '<select id="id_coach" name="id_coach" required>';
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['id_coach'] . '">' . $row['nom_coach'] . ' ' . $row['prenom_coach'] . '</option>';
                        }
                        echo '</select>';
                    } else {
                        echo '<p>No coaches found.</p>';
                    }
                    ?>
                    <button type="submit">Supprimer le compte coach</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($is_coach): ?>
            <div class="container update">
                <h2>Mettre à jour les informations du coach</h2>
                <form method="post" action="compte.php">
                    <input type="hidden" name="update_coach" value="1">
                    <label for="bureau">Bureau :</label>
                    <input type="text" id="bureau" name="bureau" value="<?php echo $bureau_coach ?? ''; ?>" required>
                    <label for="specialite">Spécialité :</label>
                    <input type="text" id="specialite" name="specialite" value="<?php echo $specialite_coach ?? ''; ?>"
                        required>
                    <label for="photo">Photo (URL) :</label>
                    <input type="text" id="photo" name="photo" value="<?php echo $photo_coach ?? ''; ?>" required>
                    <label for="telephone">Téléphone :</label>
                    <input type="text" id="telephone" name="telephone" value="<?php echo $telephone_coach ?? ''; ?>"
                        required>
                    <button type="submit">Mettre à jour</button>
                </form>
            </div>

            <div class="container update">
                <h2>Remplir le CV</h2>
                <div class="fill-cv">
                    <button onclick="toggleDropdown('cv-form')">Remplir mon CV</button>
                </div>
                <div id="cv-form" class="cv-form-container">
                    <form method="post" action="compte.php">
                        <input type="hidden" name="save_cv" value="1">
                        <label for="FirstName">Prénom :</label>
                        <input type="text" id="FirstName" name="FirstName" required>
                        <label for="LastName">Nom :</label>
                        <input type="text" id="LastName" name="LastName" required>
                        <label for="DateOfBirth">Date de naissance :</label>
                        <input type="date" id="DateOfBirth" name="DateOfBirth" required>
                        <label for="Email">Email :</label>
                        <input type="email" id="Email" name="Email" required>
                        <label for="PhoneNumber">Numéro de téléphone :</label>
                        <input type="text" id="PhoneNumber" name="PhoneNumber" required>
                        <label for="Street">Rue :</label>
                        <input type="text" id="Street" name="Street" required>
                        <label for="City">Ville :</label>
                        <input type="text" id="City" name="City" required>
                        <label for="PostalCode">Code postal :</label>
                        <input type="text" id="PostalCode" name="PostalCode" required>
                        <label for="Country">Pays :</label>
                        <input type="text" id="Country" name="Country" required>
                        <label for="Summary">Résumé professionnel :</label>
                        <textarea id="Summary" name="Summary" rows="=10" required></textarea>
                        <label for="DegreeTitle">Diplôme :</label>
                        <input type="text" id="DegreeTitle" name="DegreeTitle" required>
                        <label for="Skills">Compétences :</label>
                        <textarea id="Skills" name="Skills" rows="=10" required></textarea>
                        <label for="LanguageName">Langue :</label>
                        <input type="text" id="LanguageName" name="LanguageName" required>
                        <label for="Proficiency">Niveau :</label>
                        <input type="text" id="Proficiency" name="Proficiency" required>
                        <button type="submit">Sauvegarder</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($is_client): ?>
            <div class="container update">
                <h2>Mettre à jour les informations du client</h2>
                <form method="post" action="compte.php">
                    <input type="hidden" name="update_client" value="1">
                    <label for="date_naissance">Date de naissance :</label>
                    <input type="date" id="date_naissance" name="date_naissance"
                        value="<?php echo $date_naissance ?? ''; ?>" required>
                    <label for="telephone">Numéro de téléphone :</label>
                    <input type="text" id="telephone" name="telephone" value="<?php echo $telephone ?? ''; ?>" required>
                    <label for="profession">Profession :</label>
                    <input type="text" id="profession" name="profession" value="<?php echo $profession ?? ''; ?>" required>
                    <button type="submit">Mettre à jour</button>
                </form>
            </div>
        <?php endif; ?>
        <?php if ($is_client): ?>
            <div class="container update">
                <h2>Modifier mes informations de carte bancaire</h2>
                <form method="post" action="compte.php">
                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" value="" required>

                    <label for="prenom">Prénom:</label>
                    <input type="text" id="prenom" name="prenom" value="" required>

                    <label for="adresse_ligne_1">Adresse ligne 1:</label>
                    <input type="text" id="adresse_ligne_1" name="adresse_ligne_1" value="" required>

                    <label for="adresse_ligne_2">Adresse ligne 2:</label>
                    <input type="text" id="adresse_ligne_2" name="adresse_ligne_2" value="">

                    <label for="ville">Ville:</label>
                    <input type="text" id="ville" name="ville" value="" required>

                    <label for="code_postal">Code postal:</label>
                    <input type="text" id="code_postal" name="code_postal" value="" required>

                    <label for="pays">Pays:</label>
                    <input type="text" id="pays" name="pays" value="" required>

                    <label for="carte_etudiant_client">Carte Étudiant:</label>
                    <input type="text" id="carte_etudiant_client" name="carte_etudiant_client" value="">

                    <input class="submit" type="submit" name="update_carte_bancaire" value="Mettre à jour">
                </form>
            </div>
            <div class="container factures">
                <h2>Mes Factures</h2>
                <table>
                    <tr>
                        <th>Facture</th>
                        <th>Date de Paiement</th>
                        <th>Type de Carte</th>
                        <th>Numéro de Carte</th>
                        <th>Nom sur la Carte</th>
                    </tr>
                    <?php foreach ($paiements as $paiement): ?>
                        <tr>
                            <td><?php echo $paiement['facture']; ?></td>
                            <td><?php echo $paiement['date_paiement']; ?></td>
                            <td><?php echo $paiement['type_carte']; ?></td>
                            <td><?php echo $paiement['numero_carte']; ?></td>
                            <td><?php echo $paiement['nom_carte']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
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
        function toggleDropdown(id) {
            var element = document.getElementById(id);
            if (element.style.display === "none") {
                element.style.display = "block";
            } else {
                element.style.display = "none";
            }
        }
    </script>
</body>

</html>