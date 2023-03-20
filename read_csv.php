<?php
// Avataan csv-tiedosto lukutilassa
if (($tiedosto = fopen("tiedot.csv", "r")) !== FALSE) {
    // Määritetään csv-tiedoston erotinmerkki
    $erotinmerkki = ",";

    // Alustetaan taulukko, johon tallennetaan objektit
    $objektit = array();

    // Käydään tiedosto läpi rivi kerrallaan
    while (($rivi = fgetcsv($tiedosto, 0, $erotinmerkki)) !== false) {

    // Muodostetaan objekti riviä vastaavista arvoista
    $objekti = array(
        "nimi" => $rivi[0],
        "ika" => $rivi[1],
    );

    // Lisätään objekti taulukkoon
    array_push($objektit, $objekti);
    }

    // Suljetaan tiedosto
    fclose($tiedosto);

    // Palautetaan taulukko objekteista JSON-muodossa
    http_response_code(200);
    echo json_encode($objektit);
} else {
    //jos tiedoston avaaminen ei onnistunut
    http_response_code(500);
    echo json_encode(array('virhe' => 'Tiedoston lukeminen epäonnistui'));
}

?>