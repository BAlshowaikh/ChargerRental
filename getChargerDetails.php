<?php
require_once("Models/chargerPointDataSet.php");

header('Content-Type: application/json');

if (isset($_POST['charger_id'])) {
    $chargerId = (int) $_POST['charger_id'];

    $chargerSet = new chargerPointDataSet();

    // Temporarily assume the user ID is known (since this method works by user)
    // You should replace 123 with the actual logged-in user's ID if available
    //$userId = 123;

    $chargers = $chargerSet->fetchChargerPointsByUserId($chargerId);

    foreach ($chargers as $charger) {
        if ((int)$charger['charger_point_id'] === $chargerId) {
            echo json_encode([
                'name' => $charger['Name'],
                'description' => $charger['charger_point_description'],
                'connector_type' => $charger['connector_type'],
                'price_per_kw' => $charger['price_per_kwatt'],
                'lat' => $charger['Latitude'],
                'lng' => $charger['Longitude']
            ]);
            exit;
        }
    }

    echo json_encode(['error' => 'Charger not found for this user']);
}
