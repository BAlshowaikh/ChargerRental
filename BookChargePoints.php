<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once("Models/bookingRequestDataSet.php");
require_once("Models/chargerPointDataSet.php");

$view = new stdClass();
$view->pageTitle = "Book a Charger Point";

//Redirect to login if not logged in
if (!isset($_SESSION['userID'])) {
    header("Location: Login.php");
    exit();
}

// Get the charger point ID from query string
if (!isset($_GET['id'])) {
    echo "Invalid access: Charger Point ID is required.";
    exit();
}

$cpId = $_GET['id'];
//$cpId =3;
$chargerPointSet = new chargerPointDataSet();
$view->chargerPoint = $chargerPointSet->fetchChargerPointById($cpId);

if (!$view->chargerPoint) {
    echo "Charger point not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $userId = $_SESSION['userID'];
    //$userId = 123;
    $chargerPointId = $_GET['id']; // ensure it's coming from the URL
    //$chargerPointId =3;

    $start = new DateTime($_POST['start']);
    $durationHours = (int) $_POST['duration'];

    // Clone and add duration to get end time
    $end = clone $start;
    $end->modify("+{$durationHours} hours");

    // Optional: fetch current price from charger point object
    $pricePerKWh = $view->chargerPoint->getPricePerKW();

    // Assume 1 kWh per hour of usage (or you can use other logic)
    $totalKW = $durationHours;
    $statusId = 1; // Pending

    $bookingSet = new bookingRequestDataSet();
    $bookingSet->createBooking(
        $start->format('Y-m-d H:i:s'),
        $end->format('Y-m-d H:i:s'),
        $totalKW, // still insert if needed by DB
        $chargerPointId,
        $userId,
        $statusId,
        $pricePerKWh
    );

    $start = new DateTime($_POST['start']);
    $durationHours = (int) $_POST['duration'];
    $pricePerKWh = $view->chargerPoint->getPricePerKW();
    $totalCost = $durationHours * $pricePerKWh;

    $view->successMessage = "Booking submitted successfully!";
    $view->bookingSummary = [
        'startTime' => $start->format("Y-m-d H:i"),
        'startTimestamp' => $start->getTimestamp(), // ← Add this
        'duration' => $durationHours,
        'pricePerKWh' => $pricePerKWh,
        'totalCost' => $totalCost
    ];
}
require_once("Views/BookChargePoints.phtml");
