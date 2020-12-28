<?php
// Klasse für den Benutzer, der sich einloggen kann und Beiträge ansehen kann
class Benutzer extends Beitrag{
  private $name;
  private $passwort;
  public $nutzerDatei;
  function __construct(){
    $this->nutzerDatei = "./src/data/nutzer.xml";

  }
  // Name ansehen bzw. festlegen
  public function getName(){
    return $this->name;
  }
  public function setName($name){
    $this->name = $name;
  }
  // Passwort ansehen bzw. festlegen
  public function getPasswort(){
    return $this->passwort;
  }
  public function setPasswort($passwort){
    $this->passwort = $passwort;
  }
  // Website-Inhalt aufbauen und Beiträge durchsuchen
  // File suchen und Fehlerausgabe,wenn die File nicht existiert
  public function sucheXMLDatei(){
    // fehlermeldung, wenn File nicht existiert
    if(!file_exists($this->dateixml)){
      throw new Exception("Die Datei " . $this->dateixml . " konnte nicht gefunden werden");
    } else {
      return simplexml_load_file($this->dateixml);
    }
  }
 //Testen, ob die Datei Nutzer existiert
  public function checkeNutzerDatei(){
    // fehlermeldung, wenn File nicht existiert
    if(!file_exists($this->nutzerDatei)){
      throw new Exception("Die Datei " . $this->nutzerDatei . " konnte nicht gefunden werden");
    } else {
      return simplexml_load_file($this->nutzerDatei);
    }
  }

  // Einloggen mit den Benutzerangaben
  public function einloggen(){
    // Testen, ob die Eingabefelder leer sind
    if(isset($_POST['name'])){
      // sind die Eingabefelder nicht leer, so werden alle html-charakter abgeschnitten, sodass kein HTML eingeschleußt wird
      $this->name = htmlspecialchars($_POST['name']);
    } else { $this->name = null; }
    // Abgekürzte Schreibweise: $benutzer = isset($_POST['benutzer'] ? htmlspecialchars($_POST['benutzer']) : null;)
    if(isset($_POST['passwort'])){
      $this->passwort = htmlspecialchars($_POST['passwort']);
    } else { $this->passwort = null;};
    // Testen, ob Nutzer-Datei existiert und Nutzerdatei laden
    $xml = $this->checkeNutzerDatei($this->nutzerDatei);
    $nutzer = $xml->Nutzer;
    // Eingegebenes Passwort und Name mit den Namen und Passwörtern aus der Nutzerdatei vergleichen
    for ($i = 0; $i < count($nutzer); $i++) {
      if($this->name == "" . $nutzer[$i]->Name && $this->passwort == "" . $nutzer[$i]->Passwort){
        //Ist der Nutzer Redakteur oder Administrator? -> Session für den Nutzer erstellen
        $_SESSION['name'] = "" . $this->name;
        $_SESSION['isAdmin'] = "" . $nutzer[$i]->IsAdministrator;
        $_SESSION['isRed'] = "" . $nutzer[$i]->IsRedakteur;
        $_SESSION['NutzerID'] = "" . $nutzer[$i]->ID;
        header('Location: ./index.php');
        break;
      }
    }
  }
  // Navigation und Tabellarische Beitragübersich laden
  public function ladeNavigation(){
    try{
      // Lade XML-Datei mit den Beiträgen
      $xml = $this->sucheXMLDatei($this->dateixml);
      $beitrag = $xml->Beitraege->Beitrag;
      $i = 0;
      // Navigationsliste öffnen
      echo "<ul>";
      echo "<li><a href='./index.php'>Home</a></li>";
      if(isset($_SESSION['isRed'])){
        if($_SESSION['isRed'] == "Ja"){
          echo "<li><a href='./beitragerstellen.php'>+ Neuer Beitrag</a></li>
                <li><a href='./bilderladen.php'>+ Bilder hochladen</a></li>";
        }
      }
      if(isset($_SESSION['isAdmin'])){
        if($_SESSION['isAdmin'] == "Ja"){
          echo "<li><a href='./NutzerTabelle.php'>+ Nutzerverwaltung</a></li>";
        }
      }
      // Lade die index.php, diese soll kopiert werden und für die Beiträge ergänzt werden
      $indexdatei = file_get_contents($this->indexpfad);
      foreach ($beitrag as $key) {
        if($key->BeitragId == "X"){continue;}
        else{
          // Dateinamen und Pfad für die Beiträge
          $beitragpfad = substr($this->dateiphp, 0, -4) . $i . ".php";
          $phpdatei = fopen($beitragpfad,"w");
          // IDs der Beiträge in der Property beitragId speichern
          $this->beitragId[] = " " . $key->BeitragId;
          $buttons = '';
          // Erstellen der Navigationsliste
          if(isset($_SESSION['isAdmin'])){
            if($_SESSION['isAdmin'] == "Ja"){
              echo "<li><a href='./beitragArtikel" . $i . ".php'>" . $key->Titel . "</a><ul><li>" . $key->BeitragId ."</li></ul></li>";
              $buttons = '<div class="haupt"><form action="./beitragArtikel' . $i . '.php" method="post">
                <button type="submit" name="loeschenbtn" value="' . $key->BeitragId . '">Beitrag löschen</button></div>';
            } else {
              echo "<li><a href='./beitragArtikel" . $i . ".php'>" . $key->Titel . "</a></li>";
            }
          } else {
            echo "<li><a href='./beitragArtikel" . $i . ".php'>" . $key->Titel . "</a></li>";
          }
          //ersetzen einiger Elemente aus der Index.php, welche in den Beiträgen nicht genutzt werden
          if(count($key->children()) == 5){
            $inhalt = str_replace('<?php include $haupt; ?>',"<h1>" . $key->Titel . "</h1>\n<div class='image'><img src=" . $key->Bildpfad . "></div>\n<p>" . $key->Text . "</p>",$indexdatei);
          }
          else {
              $inhalt = str_replace('<?php include $haupt; ?>',"<h1>" . $key->Titel . "</h1>\n<p>" . $key->Text . "</p>",$indexdatei);
          }
          $inhalt = str_replace('<div class="weitere"><?php include $weitere; ?></div>',$buttons ,$inhalt);
          // Beitragszähler um 1 erhöhen
          $i++;
          // Beitragsseiten erzeugen
          fwrite($phpdatei,$inhalt);
        }
      }
      // Navigationslste schließen
      echo "</ul>";
    }
    catch(Exception $e){
      echo $e->getMessage();
    }
  }
  // Lade die Beitragstabelle -> Es soll eine Tabelle mit den Listeneinträgen für die Beiträge erstellt werden
  // Es braucht jeweils die Überschrift, ein Bild, 20 Zeichen und ein ...lesen Link
  public function ladeBeitragstabelle(){
    try{
      //Daten aus der beitraege.xml laden
      $xml = $this->sucheXMLDatei($this->dateixml);
      $i = 0;
      $beitrag = $xml->Beitraege->Beitrag;
      $tabelle = "<?php echo \"<ul>";
      foreach ($beitrag as $key) {
        if($key->BeitragId == "X"){continue;}
        else{
        $beitragpfad = substr($this->dateiphp, 0, -4) . $i . ".php";
        $tabelle = $tabelle . "<li><h2>" . $key->Titel . "</h2><img src='" . $key->Bildpfad . "' alt='' width='100px'>". substr("<p>" . $key->Text, 3, 100) . "<a href='"
        . $beitragpfad . "'> ... Lesen</a></p></li>";
        $i++;
      }}
      $tabelle = $tabelle . "</ul>\";?>";
      // Öffne die Weitere Datei, um den Inhalt in die Datei zu schreiben
      $phpdatei = fopen($this->weitere,"w");
      fwrite($phpdatei,$tabelle);
    }
    catch(Exception $e){
      echo $e->getMessage();
    }
  }
  // Beiträge bearbeiten
  public function schreibeBeitrag(){}
  public function loescheBeitrag(){}
  public function ladeBilderHoch(){}
  public function ausloggen(){}
}
 ?>
