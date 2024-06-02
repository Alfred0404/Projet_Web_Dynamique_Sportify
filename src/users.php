<?php
session_start();
include "db_connection.php";

// si l'utilisateur n'est pas connecté
if (!isset($_SESSION['unique_id'])) {
  header("location: index.php");
}

$outgoing_id = $_SESSION['unique_id'];
$sql = "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id} ORDER BY user_id DESC";
$query = mysqli_query($conn, $sql);
$output = "";

// si aucun utilisateur n'est disponible pour discuter
if (mysqli_num_rows($query) == 0) {
  $output .= "No users are available to chat";
}

// si des utilisateurs sont disponibles pour discuter
elseif (mysqli_num_rows($query) > 0) {
  while ($row = mysqli_fetch_assoc($query)) {
    // on récupère le dernier message entre l'utilisateur connecté et l'utilisateur avec qui il discute
    $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']}
                OR outgoing_msg_id = {$row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id}
                OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";

    $query2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($query2);

    // afficher le dernier message s'il existe
    (mysqli_num_rows($query2) > 0) ? $result = $row2['msg'] : $result = "Aucun message";

    // si le message est supérieur à 28 caractères, on le tronque
    (strlen($result) > 28) ? $msg = substr($result, 0, 28) . '...' : $msg = $result;

    if (isset($row2['outgoing_msg_id'])) {
      ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "You: " : $you = "";
    }
    else {
      $you = "";
    }

    // afficher si l'utilisateur est en ligne ou non
    ($row['status'] == "Offline now") ? $offline = "offline" : $offline = "";

    // cacher le message "You: " si l'utilisateur connecté est l'utilisateur avec qui on discute
    ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

    // afficher les utilisateurs disponibles pour discuter
    $output .= '<a class = "user_info" href="chat.php?user_id=' . $row['unique_id'] . '">
                  <div class="content">
                  <img src="php/images/' . $row['img'] . '" alt="">
                  <div class="details">
                      <span>' . $row['fname'] . " " . $row['lname'] . '</span>
                      <p>' . $you . $msg . '</p>
                  </div>
                  </div>
                  <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
                  </a>';
  }

}


?>

<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Sportify - Discussions</title>
</head>

<body>
  <header class="header">
    <h1 class="title">Sportify</h1>
    <img src="../assets/logo_Sportify.png" alt="logo" id="logo">
  </header>
  <div class="nav">
    <ul>
      <li class="nav-item"><a class="text-decoration-none" href="accueil">Accueil</a></li>
      <li class="nav-item"><a class="text-decoration-none" href="parcourir.php">Tout parcourir</a></li>
      <li class="nav-item"><a class="text-decoration-none" href="recherche.php">Rechercher</a></li>
      <li class="nav-item"><a class="text-decoration-none" href="rendez_vous.php">Rendez-vous</a></li>
      <li class="nav-item"><a class="text-decoration-none" href="compte.php">Votre compte</a></li>
      <li class="nav-item active"><a href="#">Discussions</a></li>
      <li class="nav-item"><a class="text-decoration-none" href="logout.php">Déconnexion</a></li>
    </ul>
  </div>
  <section class="container">
    <div class="wrapper">
      <section class="users">
        <header>
          <div class="content">
            <?php
            // récupérer les informations de l'utilisateur connecté
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
            if (mysqli_num_rows($sql) > 0) {
              $row = mysqli_fetch_assoc($sql);
            }
            ?>
            <img src="image_coach/defaut.jpg" alt="">
            <div class="details">
              <!-- afficher les informations de l'utilisateur connecté -->
              <span><?php echo $row['fname'] . " " . $row['lname'] ?></span>
              <p><?php echo $row['status']; ?></p>
            </div>
          </div>
        </header>
        <?php echo $output; ?>
        <div class="users-list">
        </div>
      </section>
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
  <script src="js/users.js"></script>

</body>

</html>