<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointDataSet.php';

session_start();

// TEMPORARY - manually fake login session
//if (!isset($_SESSION['userID'])) {
//    $_SESSION['userID'] = 1;
//    $_SESSION['role'] = 1; // 1 = Admin
//}

// Check admin permission (optional)
if ($_SESSION['role'] !== 1) {
    header("Location: unauthorized.php");
    exit;
}

// Instantiate the dataset model
$chargerDataSet = new chargerPointDataSet();

// Pagination setup
$chargersPerPage = 6;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $chargersPerPage;

// Fetch paginated data from model
$chargerPointObjs = $chargerDataSet->fetchDetailedChargerPointsPaginatedAdminView($chargersPerPage, $offset);
$totalChargers = $chargerDataSet->getTotalChargerCount();
$totalPages = ceil($totalChargers / $chargersPerPage);

// Pass pagination values to the view
$view = new stdClass();
$view->currentPage = $currentPage;
$view->totalPages = $totalPages;

require 'Views/ManageChargePointsAdmin.phtml';
