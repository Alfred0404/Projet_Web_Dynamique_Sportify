<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <form method="POST" action="login.php">
        <label for="nom_admin">Nom d'utilisateur :</label>
        <input type="text" id="nom_admin" name="nom_admin" required>

        <label for="mdp_admin">Mot de passe :</label>
        <input type="password" id="mdp_admin" name="mdp_admin" required>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
