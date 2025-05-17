<?php

session_start();

$view = new stdClass();

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

        if ($mode === 'map') {
            // Return all charger points (for map)
            $chargers = $chargerDataSet->fetchAllDetailedChargerPoints();
            echo json_encode(['chargers' => $chargers]);

        } elseif ($mode === 'list') {
            // Return paginated results (for list view)
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 6;
            $offset = ($page - 1) * $limit;

            $chargers = $chargerDataSet->fetchDetailedChargerPointsPaginated($limit, $offset);
            $totalCount = $chargerDataSet->getTotalChargerCount();
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
