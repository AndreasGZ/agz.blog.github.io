<?php
session_start();
require_once './src/php/klassen/abstrKlasseBeitrag.php';
require_once './src/php/klassen/klasseBenutzer.php';
require_once './src/php/klassen/interfaceEinloggen.php';
require_once './src/php/klassen/klasseAdministrator.php';
require_once './src/php/klassen/klasseRedakteur.php';

// Objekte erstellen
$objBenutzer = new Benutzer();
$haupt = $objBenutzer->haupt;
$login = $objBenutzer->login;
$logout = $objBenutzer->logout;
$weitere = $objBenutzer->weitere;
$objBenutzer->einloggen();
$objBenutzer->ladeBeitragstabelle();
if(isset($_SESSION['isRed'])){
	if($_SESSION['isRed'] == "Ja"){
	$objRedakteur = new Redakteur();
	$objRedakteur->ausloggen();
	if(isset($_POST['BeitragErstellen'])){
		$objRedakteur->schreibeBeitrag();
		}
}}
?>

<!DOCTYPE html>
<html lang="de">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Blog</title>
		<!-- Wie kann ich sicherstellen, dass das Stylesheet am Ende eingebunden wird -->
		<link rel="stylesheet" type="text/css" href="./main.css">
    <!-- <script src="javascript.js"></script> -->
	</head>

	<body>
    <header>
      <div class="kopf">
        <h1>Willkommen auf meinem Blog</h1>
      </div>
    </header>

    <aside>
      <div class="login"><?php
			if(isset($_SESSION['isAdmin']) && isset($_SESSION['isRed'])){
			if($_SESSION['isAdmin'] == "Ja" || $_SESSION['isReda'] == "Ja"){
				include $logout;
			}} else {
				include $login;
			}
			?></div>
      <nav class="navigation"><?php
			$objBenutzer->ladeNavigation();
			?></nav>
    </aside>

    <main>
      <div class="haupt">
				<form action="./beitragerstellen.php" method="post">
					<input type="text" name="titel" placeholder="Beitragstitel">
					<textarea name="inhalt"></textarea>
					<button type="submit" name="BeitragErstellen" value="senden">Absenden</button>
					<button type="reset">Reset</button>
				</form>
			</div>
    </main>

    <footer>
      <div class="fuss">
        <ul>
          <li>&copy; Blog</li>
          <li>Impressum</li>
          <li>Datenschutzhinweis</li>
        </ul>
      </div>
    </footer>

	</body>
</html>
