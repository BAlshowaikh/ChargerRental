<?php
session_start();
require_once("Models/bookingRequestDataSet.php");

// Check if user is logged in
//$userId = $_SESSION['userID'] ?? null;
//
//if (!$userId) {
//    header("Location: login.php");
//    exit;
//}

$userId = 3;
$bookingDataSet = new BookingRequestDataSet();

$attributes = [
    'Created_timestamp',
    'Booking_start',
    'Booking_end',
    'Price_per_kwatt',
    'Charger_point_ID',
    'User_ID',
    'Booking_status_ID'
];

$selectedAttribute = $_POST['attribute'] ?? '';
$selectedMonth = $_POST['month'] ?? '';

$months = $bookingDataSet->getUniqueMonths($userId);

$bookings = [];
$barData = [];

if (!empty($selectedAttribute)) {
    switch ($selectedAttribute) {
        case 'Created_timestamp':
        case 'Booking_start':
        case 'Booking_end':
            if (!empty($selectedMonth)) {
                $bookings = $bookingDataSet->getBookingsByMonth($selectedAttribute, $selectedMonth, $userId);
                $barData = array_count_values(array_column($bookings, $selectedAttribute));
            }
            break;
        case 'Price_per_kwatt':
            $bookings = $bookingDataSet->getRequestsCountByPrice($userId);
            foreach ($bookings as $booking) {
                $barData[$booking['rounded_price']] = $booking['num_requests'];
            }
            break;
        case 'Charger_point_ID':
            $bookings = $bookingDataSet->getBookingsGroupedByChargerID($userId);
            $barData = array_count_values(array_column($bookings, 'Charger_point_ID'));
            break;
        case 'User_ID':
            $bookings = $bookingDataSet->getBookingsGroupedByUserID($userId);
            $barData = array_count_values(array_column($bookings, 'User_ID'));
            break;
        case 'Booking_status_ID':
            $bookings = $bookingDataSet->getBookingsGroupedByStatusID($userId);
            // Now using status_name instead of Booking_status_ID
            $barData = [];
            foreach ($bookings as $booking) {
                $barData[$booking['Booking_status']] = $booking['num_requests'];
            }
            break;
    }
}

$view = new stdClass();
$view->attributes = $attributes;
$view->selectedAttribute = $selectedAttribute;
$view->months = $months;
$view->selectedMonth = $selectedMonth;
$view->barData = $barData;

require_once("Views/SystemReport.phtml");