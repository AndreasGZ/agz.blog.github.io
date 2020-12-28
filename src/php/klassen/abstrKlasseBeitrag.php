<?php
require_once './src/php/klassen/interfaceEinloggen.php';
// Abstrakte Klasse für die Beiträge
abstract class Beitrag implements Einloggen{
  public $haupt = './src/php/home.php';
  public $login = './src/php/login.php';
  public $logout = './src/php/logout.php';
  public $weitere = './src/php/weitere.php';
  protected $dateixml = './src/data/beitraege.xml';
  protected $dateiphp = './beitragArtikel.php';
  public $indexpfad = './index.php';
  public $Bildpfad = "./src/img/";
  protected $beitragId;
  // Website-Inhalt aufbauen und Beiträge durchsuchen
  // File suchen und Fehlerausgabe,wenn die File nicht existiert
  abstract function sucheXMLDatei();
  abstract function ladeNavigation();
  abstract function ladeBeitragstabelle();
  // Beiträge bearbeiten
  abstract function schreibeBeitrag();
  abstract function loescheBeitrag();
  abstract function ladeBilderHoch();
}
?>
