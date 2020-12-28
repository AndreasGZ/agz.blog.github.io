<?php
// Klasse für den Administrator, welcher Teil der Benutzer ist und diese verwaltet und Beiträge bearbeitet
class Administrator extends Benutzer{
  public $isAdministrator;
  // Testen, ob der eingeloggte Nutzer ein Admin ist
  public function getisAdministrator(){
    if(isset($_SESSION['isAdmin'])){
    return $this->isAdministrator;
    }
  }
  // Nutzer aus der XML-Datei löschen
  public function loescheNutzer(){
    if(isset($_POST['loescheNutzer']))
     {
      //Nutzer aus nutzer.xml löschen
      $xml = $this->checkeNutzerDatei($this->nutzerDatei);
      $nutzer = $xml->Nutzer;
      $i = count($nutzer);
      for ($n = 0; $n < $i; $n++) {
        $nutzer = "". $xml->Nutzer[$n]->ID;
        if( $nutzer == $_POST['loescheNutzer'] && $nutzer != $_SESSION['NutzerID']){
          $message =  "<br>Lösche " . $nutzer;
          // Hier werden alle Elemente auf X gesetzt, da ich beim null setzen Probleme bekam
          $xml->Nutzer[$n] = null;
          $xml->Nutzer[$n]->addChild("ID","X");
          $xml->Nutzer[$n]->addChild("Name","X");
          $xml->Nutzer[$n]->addChild("Passwort","X");
          $xml->Nutzer[$n]->addChild("IsAdministrator","X");
          $xml->Nutzer[$n]->addChild("IsRedakteur","X\n");
          print_r($xml->Nutzer[$n]->children());
        }
        else {
          $message = "<br>Nutzer " . $nutzer . " konnte nicht gelöscht werden.";
        }
        echo $message;
      }
      // Nutzer speichern
      file_put_contents($this->nutzerDatei, $xml->asXML());
     }
  }
  // Neuen Nutzer anlegen
  public function neuerNutzer(){
    if(isset($_POST['name']) && isset($_POST['passwort']) && isset($_POST['is'])){
      if($_POST['name'] != "" && $_POST['passwort'] != ""){
        $xml = $this->checkeNutzerDatei($this->nutzerDatei);
        $i = count($xml->Nutzer);
          // Kindelemente hinzufügen
          $xmlChildN = $xml->addChild('Nutzer');
          $xmlneuerNutzer = $xmlChildN->addChild('ID',$i);
          $xmlneuerNutzer = $xmlChildN->addChild('Name',$_POST['name']);
          $xmlneuerNutzer = $xmlChildN->addChild('Passwort',$_POST['passwort']);
          if($_POST['is'] == "Admin"){
            $xmlneuerNutzer = $xmlChildN->addChild('IsAdministrator', 'Ja');
            $xmlneuerNutzer = $xmlChildN->addChild('IsRedakteur', 'Nein');
          } else {
            $xmlneuerNutzer = $xmlChildN->addChild('IsAdministrator', 'Nein');
            $xmlneuerNutzer = $xmlChildN->addChild('IsRedakteur', "Ja\n");
          }
          // Nutzer speichern
          file_put_contents($this->nutzerDatei, $xml->asXML());
      }
      else {
        echo "Bitte füllen Sie alle Felder für den Benutzer aus";
      }
    }
  }
  // Alle gespeicherten Nutzer anzeigen
  public function zeigeNutzer(){
    // Testen, ob Nutzer-Datei existiert und Nutzerdatei laden
    $xml = $this->checkeNutzerDatei($this->nutzerDatei);
    $nutzer = $xml->Nutzer;
    echo '<tr><th>Name</th><th>Passwort</th><th>Administrator</th><th>Redakteur</th><th>Löschen</th></tr>';
    // Eingegebenes Passwort und Name mit den Namen und Passwörtern aus der Nutzerdatei vergleichen
    foreach ($nutzer as $key) {
      if($key->ID == "X"){
        continue;
      }
      else{
      echo '<tr><td>' . $key->Name . '</td><td>' . $key->Passwort . "</td><td>" . $key->IsAdministrator . "</td><td>" . $key->IsRedakteur . "</td><td><button name='loescheNutzer' type='submit' value='" . $key->ID . "'>X</button></td></tr>";
      }
    }
  }
  // Beiträge löschen
  public function loescheBeitrag(){
    if(isset($_POST["loeschenbtn"]))
    {
      try{
        $xml = $this->sucheXMLDatei();
        $beitrag = $xml->Beitraege[0]->Beitrag;
        $i = count($beitrag);
        for ($n = 0; $n < $i; $n++) {
          if($beitrag[$n]->BeitragId == $_POST['loeschenbtn']){
            $xml->Beitraege[0]->Beitrag[$n] = null ;
            $xmlloesche = $xml->Beitraege[0]->Beitrag[$n];
            $xmlloesche->addChild('BeitragId','X');
            $xmlloesche->addChild('Titel','X');
            $xmlloesche->addChild('Text','X');
            $xmlloesche->addChild('Datum',"X\n");
          }
        }
          header("Location: ./index.php");
          file_put_contents($this->dateixml, $xml->asXML());
      }
      catch(Exception $e){
        echo $e->getMessage();
      }
    }

  }
  // Nutzer ausloggen
  public function ausloggen(){
    // Testen, ob die Eingabefelder leer sind
    if(isset($_POST['buttonOut'])){
      $_SESSION = array();
      header("location: ./index.php");
    }
  }
}
 ?>
