<?php
session_start();
require_once("Models/bookingRequestDataSet.php");

$view = new stdClass();
$view->pageTitle = "My Charger Booking Requests";
$view->styles = ["/css/BookingRequestMange.css", "/css/BookingRequest.css", "/css/sharedLayout.css"];


// Auth (uncomment for real use)
 if (!isset($_SESSION['userID'])) {
     header("Location: Login.php");
     exit();
 }

$homeownerId = $_SESSION['userID'];


// Filters from GET
$statusFilter = $_GET['status'] ?? 'all'; // '1', '2', '3', or 'all'
$sortOrder = $_GET['sort'] ?? 'newest'; // 'newest' or 'oldest'

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pageSize = 6;
$offset = ($page - 1) * $pageSize;

$bookingSet = new bookingRequestDataSet();

// Fetch paginated and sorted bookings with filter
$view->bookingRequests = $bookingSet->fetchBookingsForHomeowner($homeownerId, $statusFilter, $sortOrder, $pageSize, $offset);

// Total for pagination
$totalBookings = $bookingSet->countBookingsForHomeowner($homeownerId, $statusFilter);
$view->totalPages = ceil($totalBookings / $pageSize);
$view->currentPage = $page;

// Pass filters to view
$view->statusFilter = $statusFilter;
$view->sortOrder = $sortOrder;

// View
require_once("Views/HomeownerBookingRequest.phtml");
