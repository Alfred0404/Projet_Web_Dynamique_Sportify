<?php
session_start();
if (isset($_SESSION['name'])) {
	$text = $_POST['text'];

	$text_message = "<div class='msgln'><span class='chat-time'>" . date("g:i A") . "</span> <b class='username'>" . $_SESSION['name'] . "</b> " . stripslashes(htmlspecialchars($text)) . "<br></div>";

	$myfile = fopen(__DIR__ . "/log.html", "a") or die("Impossible d'ouvrir le fichier " . __DIR__ . "/log.html");
	fwrite($myfile, $text_message);
	fclose($myfile);
}