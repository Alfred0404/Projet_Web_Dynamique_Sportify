<?php
session_start();
include "db_connection.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Récupérer l'ID du client à partir de la base de données en fonction du nom d'utilisateur
$sql = "SELECT id_client FROM client WHERE nom_client = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erreur de préparation de la requête : " . $conn->error);
}
$stmt->bind_param("s", $_SESSION['user_name']); // Supposons que 'user_name' contienne le nom d'utilisateur
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $client_id_from_database = $row['id_client'];
} else {
    echo "Erreur : Aucun client trouvé avec ce nom d'utilisateur.";
}
$stmt->close();

// Stocker l'ID du client dans la session
//$_SESSION['user_id'] = $client_id_from_database;

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

//   partie pilou
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

        // Requête SQL pour insérer les informations de la carte bancaire dans la table paiement_client
        $sql = "INSERT INTO paiement_client (id_client, nom, prenom, adresse_ligne_1, adresse_ligne_2, ville, code_postal, pays, carte_etudiant_client) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("issssssss", $id_client, $nom, $prenom, $adresse_ligne_1, $adresse_ligne_2, $ville, $code_postal, $pays, $carte_etudiant_client);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "Informations de carte bancaire mises à jour avec succès.";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour mettre à jour ces informations.";
    }
}
// fin partie pilou

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
            die("Erreur de préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("ss", $cvFileName, $_SESSION['email']);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "CV mis à jour avec succès.";
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour sauvegarder le CV.";
    }
}


// Mise à jour des informations du coach
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_coach'])) {
    if ($is_coach) {
        $bureau = validate($_POST['bureau']);
        $specialite = validate($_POST['specialite']);
        $photo = validate($_POST['photo']);
        $telephone = validate($_POST['telephone']);

        $sql = "UPDATE coach SET bureau_coach=?, specialite_coach=?, photo_coach=?, telephone_coach=? WHERE email_coach=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $bureau, $specialite, $photo, $telephone, $_SESSION['email']);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "Informations mises à jour avec succès.";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour mettre à jour ces informations.";
    }
}

// Mise à jour des informations du client
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_client'])) {
    if ($is_client) {
        $date_naissance = validate($_POST['date_naissance']);
        $telephone = validate($_POST['telephone']);
        $profession = validate($_POST['profession']);

        $sql = "UPDATE client SET date_de_naissance=?, num_telephone=?, profession=? WHERE email_client=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("ssss", $date_naissance, $telephone, $profession, $_SESSION['email']);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "Informations mises à jour avec succès.";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour mettre à jour ces informations.";
    }
}

// Récupérer les informations du coach
if ($is_coach) {
    $sql = "SELECT bureau_coach, specialite_coach, photo_coach, telephone_coach, cv_coach FROM coach WHERE email_coach=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
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

// Récupérer les informations du client
if ($is_client) {
    $sql = "SELECT date_de_naissance, num_telephone, profession FROM client WHERE email_client=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
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

// Inscription d'un nouveau coach
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_coach'])) {
    if ($is_admin) {
        $nom = validate($_POST['name']);
        $prenom = validate($_POST['prenom']);
        $sexe = validate($_POST['sexe']);
        $email = validate($_POST['email']);
        $password = validate($_POST['password']);

        $sql = "INSERT INTO coach (nom_coach, prenom_coach, sexe_coach, email_coach, mdp_coach) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $nom, $prenom, $sexe, $email, $password);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "Compte coach créé avec succès.";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour créer un compte coach.";
    }
}

// Inscription d'un nouveau admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_admin'])) {
    if ($is_admin) {
        $nom = validate($_POST['name']);
        $prenom = validate($_POST['prenom']);
        $sexe = validate($_POST['sexe']);
        $email = validate($_POST['email']);
        $password = validate($_POST['password']);

        $sql = "INSERT INTO admin (nom_admin, prenom_admin, sexe_admin, email_admin, mdp_admin) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sssss", $nom, $prenom, $sexe, $email, $password);
        $stmt->execute();
        
        if ($stmt->affected_rows === 1) {
            echo "Compte admin créé avec succès.";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour créer un compte admin.";
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
    <div class="container">
        <h2>Informations du compte</h2>
        <p><strong>Nom:</strong> <?php echo $_SESSION['user_name']; ?></p>
        <p><strong>Prénom:</strong> <?php echo $_SESSION['prenom']; ?></p>
        <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
        <p><strong>Rôle:</strong> <?php echo ucfirst($_SESSION['role']); ?></p>
        <?php if ($is_coach): ?>
            <p><strong>Bureau:</strong> <?php echo $bureau_coach ?? ''; ?></p>
            <p><strong>Spécialité:</strong> <?php echo $specialite_coach ?? ''; ?></p>
            <p><strong>Photo:</strong> <img src="<?php echo $photo_coach ?? ''; ?>" alt="Photo du coach"></p>
            <p><strong>Téléphone:</strong> <?php echo $telephone_coach ?? ''; ?></p>
        <?php endif; ?>
        <?php if ($is_client): ?>
            <p><strong>Date de naissance:</strong> <?php echo $date_naissance ?? ''; ?></p>
            <p><strong>Numéro de téléphone:</strong> <?php echo $telephone ?? ''; ?></p>
            <p><strong>Profession:</strong> <?php echo $profession ?? ''; ?></p>
        <?php endif; ?>
        <form method="post" action="logout.php">
            <button type="submit">Déconnexion</button>
        </form>
        <form method="get" action="accueil.php">
            <button type="submit" class="btn-back">Retour à l'accueil</button>
        </form>
    </div>

    <?php if ($is_admin): ?>
    <div class="container">
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

        <h2>Créer un compte administrateur</h2>
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
            <button type="submit">Créer un compte administrateur</button>
        </form>

        <!-- Section pour supprimer les comptes coachs -->
        <h2>Supprimer un compte coach</h2>
        <form method="post" action="compte.php">
            <?php
            $sql = "SELECT id_coach, nom_coach, prenom_coach FROM coach";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<input type='checkbox' name='coach_ids[]' value='" . $row['id_coach'] . "'>";
                    echo "<label>" . $row['nom_coach'] . " " . $row['prenom_coach'] . "</label><br>";
                }
            } else {
                echo "Aucun compte coach trouvé.";
            }
            ?>
            <button type="submit" name="delete_coaches">Supprimer les comptes sélectionnés</button>
        </form>

        <?php
        // Supprimer les coachs sélectionnés
        if (isset($_POST['delete_coaches'])) {
            if (!empty($_POST['coach_ids'])) {
                $deleted_coaches = implode(",", $_POST['coach_ids']);
                $delete_query = "DELETE FROM coach WHERE id_coach IN ($deleted_coaches)";
                if ($conn->query($delete_query) === TRUE) {
                    echo "Les coachs sélectionnés ont été supprimés avec succès.";
                } else {
                    echo "Erreur lors de la suppression des coachs: " . $conn->error;
                }
            } else {
                echo "Veuillez sélectionner au moins un coach à supprimer.";
            }
        }
        ?>
    </div>
    <?php endif; ?>

    <?php if ($is_coach): ?>
        <div class="container">
            <h2>Mettre à jour les informations du coach</h2>
            <form method="post" action="compte.php">
                <input type="hidden" name="update_coach" value="1">
                <label for="bureau">Bureau :</label>
                <input type="text" id="bureau" name="bureau" value="<?php echo $bureau_coach ?? ''; ?>" required>
                <label for="specialite">Spécialité :</label>
                <input type="text" id="specialite" name="specialite" value="<?php echo $specialite_coach ?? ''; ?>" required>
                <label for="photo">Photo (URL) :</label>
                <input type="text" id="photo" name="photo" value="<?php echo $photo_coach ?? ''; ?>" required>
                <label for="telephone">Téléphone :</label>
                <input type="text" id="telephone" name="telephone" value="<?php echo $telephone_coach ?? ''; ?>" required>
                <button type="submit">Mettre à jour</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($is_client): ?>
        <div class="container">
            <h2>Modifier les informations personnelles</h2>
            <form method="post" action="compte.php">
                <input type="hidden" name="update_client" value="1">
                <label for="date_naissance">Date de naissance :</label>
                <input type="date" id="date_naissance" name="date_naissance" value="<?php echo $date_naissance ?? ''; ?>" required>
                <label for="telephone">Numéro de téléphone :</label>
                <input type="text" id="telephone" name="telephone" value="<?php echo $telephone ?? ''; ?>" required>
                <label for="profession">Profession :</label>
                <input type="text" id="profession" name="profession" value="<?php echo $profession ?? ''; ?>" required>
                <button type="submit">Mettre à jour</button>
            </form>
        </div>

        <div class="container">
            <h2>Modifier les informations de la carte bancaire</h2>
            <form method="post" action="compte.php">
                <input type="hidden" name="update_carte_bancaire" value="1">

                <!-- Ajouter ici les champs pour les informations de la carte bancaire -->
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required>

                <label for="adresse_ligne_1">Adresse ligne 1 :</label>
                <input type="text" id="adresse_ligne_1" name="adresse_ligne_1" required>

                <label for="adresse_ligne_2">Adresse ligne 2 :</label>
                <input type="text" id="adresse_ligne_2" name="adresse_ligne_2">

                <label for="ville">Ville :</label>
                <input type="text" id="ville" name="ville" required>

                <label for="code_postal">Code postal :</label>
                <input type="text" id="code_postal" name="code_postal" required>

                <label for="pays">Pays :</label>
                <input type="text" id="pays" name="pays" required>

                <label for="carte_etudiant_client">Carte étudiante :</label>
                <input type="text" id="carte_etudiant_client" name="carte_etudiant_client">

                <!-- Ajouter d'autres champs si nécessaire -->

                <button type="submit">Mettre à jour</button>
            </form>
        </div>
    <?php endif; ?>

</body>

</html>

           
