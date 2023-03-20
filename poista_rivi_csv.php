<?php
/* php://input: This is a read-only stream that allows us to read 
    raw data from the request body. It returns all the raw data after 
the HTTP headers of the request, regardless of the content type.
    file_get_contents() function: This function in PHP is used to read 
    a file into a string.
json_decode() function: This function takes a JSON string and 
    converts it into a PHP variable that may be an array or an object.
 */
$input=json_decode(file_get_contents("php://input"),false);
//poimitaan tiedot opjektista
$poistettava_rivi = $input->poistettava_rivi;
$tiedoston_nimi = "tiedot.csv";
// Avataan csv-tiedosto lukutilassa ja kirjoitustilassa (r+).
if (($tiedosto = fopen($tiedoston_nimi, "r")) !== FALSE) {
    // Määritetään csv-tiedoston erotinmerkki
    $erotinmerkki = ",";

    // Alustetaan taulukko, johon tallennetaan objektit
    $objektit = array();
    
    $rivi_nro = 0;
    // Käydään tiedosto läpi rivi kerrallaan
    while (($rivi = fgetcsv($tiedosto, 0, $erotinmerkki)) !== false) {
        $rivi_nro = $rivi_nro + 1;
        // Tarkistetaan, onko rivi poistettava
        if ($rivi_nro !== $poistettava_rivi) {
            // Muodostetaan objekti riviä vastaavista arvoista
            $objekti = array(
                "nimi" => $rivi[0],
                "ika" => $rivi[1],
            );

            // Lisätään objekti taulukkoon
            array_push($objektit, $objekti);
        } else {
            $poistettu = array(
                "nimi" => $rivi[0],
                "ika" => $rivi[1],
            );
        }
    }
    // Suljetaan tiedosto
    fclose($tiedosto);
} else {
    //jos tiedoston avaaminen ei onnistunut
    http_response_code(500);
    echo json_encode(array('virhe' => 'Tiedoston lukeminen epäonnistui'));
    exit;
}
// avataan tiedosto kirjoitusta varten
if (($tiedosto = fopen($tiedoston_nimi, "w")) !== FALSE) {

    // Käydään taulukko läpi ja kirjoitetaan uudet tiedot csv-tiedostoon
    foreach ($objektit as $objekti) {
        $rivi = array($objekti["nimi"], $objekti["ika"]);
        fputcsv($tiedosto, $rivi, $erotinmerkki);
    }

    // Suljetaan tiedosto
    fclose($tiedosto);
    
    // jos mitään ei poistettu eli rivi ei ollut
    if (empty($poistettu)) {
        http_response_code(404);
        echo json_encode(array('virhe' => 'Tietoa ei löytynyt'));
    } else {
        // Palautetaan taulukko objekteista JSON-muodossa
        http_response_code(200);
        echo json_encode($poistettu);
    }
    
} else {
    //jos tiedoston avaaminen ei onnistunut
    http_response_code(500);
    echo json_encode(array('virhe' => 'Tiedoston kirjoitus epäonnistui'));
}
?>