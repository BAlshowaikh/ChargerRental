<?php
session_start();
require_once("Models/bookingRequestDataSet.php");

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

$selectedAttribute = isset($_POST['attribute']) ? $_POST['attribute'] : '';
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : '';

$months = $bookingDataSet->getUniqueMonths();

$bookings = [];
$barData = [];

if (!empty($selectedAttribute)) {
    switch ($selectedAttribute) {
        case 'Created_timestamp':
        case 'Booking_start':
        case 'Booking_end':
            if (!empty($selectedMonth)) {
                $bookings = $bookingDataSet->getBookingsByMonth($selectedAttribute, $selectedMonth);
                $barData = array_count_values(array_column($bookings, $selectedAttribute));
            }
            break;
        case 'Price_per_kwatt':
            $bookings = $bookingDataSet->getRequestsCountByPrice();
            foreach ($bookings as $booking) {
                $barData[$booking['rounded_price']] = $booking['num_requests'];
            }
            break;
        case 'Charger_point_ID':
            $bookings = $bookingDataSet->getBookingsGroupedByChargerID();
            $barData = array_count_values(array_column($bookings, 'Charger_point_ID'));
            break;
        case 'User_ID':
            $bookings = $bookingDataSet->getBookingsGroupedByUserID();
            $barData = array_count_values(array_column($bookings, 'User_ID'));
            break;
        case 'Booking_status_ID':
            $bookings = $bookingDataSet->getBookingsGroupedByStatusID();
            $barData = array_count_values(array_column($bookings, 'Booking_status_ID'));
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