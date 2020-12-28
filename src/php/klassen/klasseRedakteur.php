<?php
// Klasse für den Redakteur, welcher Teil der Benutzer ist und Beiträge erstellen kann
class Redakteur extends Benutzer{
  public $isRedakteur;
  // Testen, ob der Nutzer Redakteur ist
  public function getisRedakteur(){
    if(isset($_SESSION['isRed'])){
    return $this->isRedakteur;
  }
  }
  // Beitrag erstellen
  public function schreibeBeitrag(){
    try{
      $xml = $this->sucheXMLDatei();
      $beitrag = $xml->Beitraege->Beitrag;
      if(isset($_POST['titel']) && isset($_POST['inhalt'])){
        $ID_Beitrag = 'beitragArtikel' . count($beitrag);
        $datum = time();
        // Kindelemente hinzufügen
        $xmlChildB = $xml->Beitraege[0]->addChild('Beitrag');
        $xmlneuerBeitrag = $xmlChildB->addChild('BeitragId',$ID_Beitrag);
        $xmlneuerBeitrag = $xmlChildB->addChild('Titel',$_POST['titel']);
        $xmlneuerBeitrag = $xmlChildB->addChild('Text',$_POST['inhalt']);
        $xmlneuerBeitrag = $xmlChildB->addChild('Datum',strftime("%d.%m.%Y",$datum) . "\n");
        // Beitrag speichern
        file_put_contents($this->dateixml, $xml->asXML());
      }
    }
    catch(Exception $e){
      echo $e->getMessage();
    }
  }
  public function ladeIDs(){
    $xml = $this->sucheXMLDatei();
    $beitrag = $xml->Beitraege->Beitrag;
    foreach ($beitrag as $key) {
      if($key->BeitragId != "X"){
        echo "<option>ID: " . $key->BeitragId . ", Titel: " . $key->Titel . "</option>";
      }
    }
  }
  // Bilder hochladen
  public function ladeBilderHoch(){
    if(isset($_FILES["bild"])){
      // Pfad in den das Bild kommt
      $pfad = $this->Bildpfad;
      $target = $pfad . basename($_FILES["bild"]["name"]);
      $uploadOk = 1;
      $imageFileType = strtolower(pathinfo($target,PATHINFO_EXTENSION));
      // Testen, ob es ein echtes Bild ist
      if(isset($_POST["BildBtn"])) {
        $check = getimagesize($_FILES["bild"]["tmp_name"]);
        if($check !== false) {
          echo "Datei ist ein Bild - " . $check["mime"] . ".";
          $uploadOk = 1;
        } else {
          echo "Datei ist kein Bild.";
          $uploadOk = 0;
        }
      }
      //Testen, ob die Datei schon existiert
      if (file_exists($target)) {
        echo "Sorry, das Bild existiert bereits.";
        $uploadOk = 0;
      }
      // UploadOk prüfen
      if ($uploadOk == 0) {
        echo "Sorry, Datei wurde nicht hochgeladen.";
        // Wenn alles ok ist, dann hochladen versuchen
      } else {
        if (move_uploaded_file($_FILES["bild"]["tmp_name"], $target)) {
          echo "Die Datei ". htmlspecialchars( basename( $_FILES["bild"]["name"])). " wurde hochgeladen.";
          $txtfile = fopen("./src/data/bildpfade.txt","a");
          fwrite($txtfile, "\n" . $target);
          fclose($txtfile);
          $xml = $this->sucheXMLDatei();
          $beitrag = $xml->Beitraege[0]->Beitrag;
          $i = count($beitrag);
          // echo $_POST['beitragID'];
          $schneide = strpos($_POST['beitragID'], ",");
          if($_POST['beitragID'] == "Nur hochladen"){
            echo "Bild wird nur hochgeladen.";
          }else{
            $_POST['beitragID'] = substr($_POST['beitragID'],4,$schneide-4);
          }
          for($n = 0; $n < $i; $n++) {
            if($_POST['beitragID'] == "" . $xml->Beitraege[0]->Beitrag[$n]->BeitragId){
              //Überprüft, ob das Element bereits 5 Kinder hat (Id,Titel,Text,Datum und Bildpfad)
              if(count($xml->Beitraege[0]->Beitrag[$n]->children()) < 5){
                  $xml->Beitraege[0]->Beitrag[$n]->addChild('Bildpfad', $target);
                  echo "Beitrag wird eingepflegt.";
              } else {
                echo "Dieser Beitrag hat bereits ein Bild. Zum Bearbeiten des Bildes bitte an Administrator wenden.";
              }
              }
            }
           file_put_contents($this->dateixml,$xml->asXML());
        } else {
          echo "Sorry, es gab ein Problem mit dem Upload.";
        }
      }

    }
  }
  // Ausloggen
  public function ausloggen(){
    // Testen, ob die Eingabefelder leer sind
    if(isset($_POST['buttonOut'])){
      $_SESSION = array();
      header("location: ./index.php");
    }
  }
}
 ?>
