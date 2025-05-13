<?php
session_start();
require_once("Models/bookingRequestDataSet.php");

$view = new stdClass();
$view->pageTitle = "My Charger Booking Requests";

// Redirect if not logged in
//if (!isset($_SESSION['user'])) {
//    header("Location: Login.php");
//    exit();
//}

// Get the logged-in user's ID (Homeowner)
//$homeownerId = $_SESSION['user']->getUserId(); // Assuming the user object has getUserId()
$homeownerId = 5;

// Get status filter from GET parameter (if any)
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all'; // Options: '1', '2', '3', or 'all'

// Fetch bookings for homeowner’s charger points
$bookingSet = new bookingRequestDataSet();
$view->bookingRequests = $bookingSet->fetchBookingsForHomeowner($homeownerId, $statusFilter);

// Load the View
require_once("Views/HomeownerBookingRequest.phtml");
