<?php
require_once("Models/bookingRequestDataSet.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $bookingId = $_POST['booking_id'];
    $newStatus = $_POST['status'];

    $bookingSet = new bookingRequestDataSet();
    $bookingSet->updateBookingStatus($bookingId, $newStatus);
}

header("Location: HomeownerBookingRequest.php");
exit();
