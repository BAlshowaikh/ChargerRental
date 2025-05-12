<?php

session_start();

$view = new stdClass();

require_once("Models/Database.php");
require_once("Models/chargerPointDataSet.php");

// Set default view if not already stored in session
if (!isset($_SESSION['view'])) {
    $_SESSION['view'] = 'map';
}

// Handle AJAX request for charger point details
if (isset($_GET['action']) && $_GET['action'] === 'getChargers') {
    header('Content-Type: application/json');

    $chargerDataSet = new chargerPointDataSet();

    try {
        $chargers = $chargerDataSet->fetchAllDetailedChargerPoints();
        echo json_encode($chargers);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to load data", "details" => $e->getMessage()]);
    }
    exit;
}

// Otherwise, load the view as normal
require_once("Views/SearchChargePoints.phtml");
