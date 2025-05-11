<?php
session_start();
require_once("Models/bookingRequestDataSet.php");
require_once("Models/chargerPointDataSet.php");

$view = new stdClass();
$view->pageTitle = "Book a Charger Point";

// Redirect to login if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: Login.php");
    exit();
}

// Get the charger point ID from query string
if (!isset($_GET['id'])) {
    echo "Invalid access: Charger Point ID is required.";
    exit();
}

$cpId = $_GET['id'];
$chargerPointSet = new chargerPointDataSet();
$view->chargerPoint = $chargerPointSet->fetchChargerPointById($cpId);

if (!$view->chargerPoint) {
    echo "Charger point not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $userId = $_SESSION['user']->getUserId();
    $start = $_POST['start'];
    $end = $_POST['end'];
    $kw = $_POST['kw'];

    // Use status ID 1 for 'Pending'
    $statusId = 1;

    $bookingSet = new bookingRequestDataSet();
    $bookingSet->createBooking($start, $end, $kw, $cpId, $userId, $statusId);

    $view->successMessage = "Booking submitted successfully!";
}

require_once("Views/BookChargePoints.phtml");
