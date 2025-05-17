<?php

session_start();
require_once("Models/bookingRequestDataSet.php");
require_once("Models/chargerPointDataSet.php");

$view = new stdClass();
$view->pageTitle = "My Booking Requests";
$view->styles = ["/css/BookingRequest.css", "/css/BookingRequestMange.css", "/Leaflet/leaflet.css"];

// Login to be enabled later
 if (!isset($_SESSION['userID'])) {
     header("Location: Login.php");
     exit();
 }

$userId = $_SESSION['userID'];
// $userId = $_SESSION['user']->getUserId();
//$userId = 123;

// GET filters and pagination
$status = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pageSize = 6;
$offset = ($page - 1) * $pageSize;

$bookingSet = new bookingRequestDataSet();

// Get filtered, sorted, and paginated bookings
$view->bookings = $bookingSet->getBookingsWithFilters($userId, $status, $sort, $offset, $pageSize);

// Get total count for pagination
$view->totalBookings = $bookingSet->countBookingsWithFilters($userId, $status);
$view->currentPage = $page;
$view->totalPages = ceil($view->totalBookings / $pageSize);

// If user clicked on a charger
if (isset($_POST['charger_id'])) {
    $chargerSet = new chargerPointDataSet();
    $view->selectedCharger = $chargerSet->fetchChargerPointById($_POST['charger_id']);
}

require_once("Views/UserBookingRequest.phtml");
