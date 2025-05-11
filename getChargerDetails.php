<?php
require_once("Models/chargerPointDataSet.php");

header('Content-Type: application/json');

if (isset($_POST['charger_id'])) {
    $chargerSet = new chargerPointDataSet();
    $charger = $chargerSet->fetchChargerPointById($_POST['charger_id']);

    echo json_encode([
        'name' => $charger->getName(),
        'description' => $charger->getDescription(),
        'connector_type' => $charger->getConnectorType(),
        'price_per_kw' => $charger->getPricePerKW(),
        'rating' => $charger->getRating()
    ]);
}
