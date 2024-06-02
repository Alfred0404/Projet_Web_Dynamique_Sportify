<?php
session_start();
include "db_connection.php";
if (!isset($_SESSION['unique_id'])) {
  header("location: index.php");
}
?>
<?php include_once "header.php"; ?>
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
      <li class="nav-item"><a href="accueil.php">Accueil</a></li>
      <li class="nav-item"><a href="parcourir.php">Tout parcourir</a></li>
      <li class="nav-item"><a href="recherche.php">Rechercher</a></li>
      <li class="nav-item"><a href="rendez_vous.php">Rendez-vous</a></li>
      <li class="nav-item"><a href="compte.php">Votre compte</a></li>
      <li class="nav-item active"><a href="users.php">Discussions</a></li>
      <li class="nav-item"><a href="logout.php">Déconnexion</a></li>
    </ul>
  </div>
  <section class="container">
    <div class="wrapper">
      <section class="chat-area">
        <header>
          <?php
          $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
          $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
          if (mysqli_num_rows($sql) > 0) {
            $row = mysqli_fetch_assoc($sql);
          } else {
            header("location: users.php");
          }
          ?>
          <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
          <img src="image_coach/defaut.jpg">
          <div class="details">
            <span><?php echo $row['fname'] . " " . $row['lname'] ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </header>
        <div class="chat-box">

        </div>
        <form action="#" class="typing-area">
          <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
          <input type="text" name="message" class="input-field" placeholder="Entrez votre message ici..."
            autocomplete="off">
          <button><i class="fab fa-telegram-plane"></i></button>
        </form>
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
  <script src="js/chat.js"></script>
</body>

</html>