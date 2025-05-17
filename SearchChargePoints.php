<?php

session_start();

$view = new stdClass();
$view->styles = "Leaflet/leaflet.css";

require_once("Models/Database.php");
require_once("Models/chargerPointDataSet.php");

// Set default view mode
if (!isset($_SESSION['view'])) {
    $_SESSION['view'] = 'map';
}

// Handle AJAX request
if (isset($_GET['action']) && $_GET['action'] === 'getChargers') {
    header('Content-Type: application/json');

    $chargerDataSet = new chargerPointDataSet();

    try {
        $mode = $_GET['mode'] ?? 'list';
        $maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
        $availability = $_GET['availability'] ?? null;
        $sql = "SELECT * FROM charger_points WHERE 1=1";


        if ($mode === 'map') {
            $chargers = $chargerDataSet->fetchAllDetailedChargerPoints($maxPrice, $availability);
            echo json_encode(['chargers' => $chargers]);

        } elseif ($mode === 'list') {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 6;
            $offset = ($page - 1) * $limit;

            $chargers = $chargerDataSet->fetchDetailedChargerPointsPaginated($limit, $offset, $maxPrice, $availability);
            $totalCount = $chargerDataSet->getTotalChargerCount($maxPrice, $availability);
            $totalPages = ceil($totalCount / $limit);

            echo json_encode([
                'chargers' => $chargers,
                'totalPages' => $totalPages,
                'currentPage' => $page
            ]);
        } else {
            throw new Exception("Invalid mode.");
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to load data", "details" => $e->getMessage()]);
    }

    exit;
}

// Load normal page view
require_once("Views/SearchChargePoints.phtml");
