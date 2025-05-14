<?php
require_once("Models/bookingRequestDataSet.php");

// Set JSON header for AJAX
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $bookingId = (int)$_POST['booking_id'];
    $newStatus = (int)$_POST['status'];

    $bookingSet = new bookingRequestDataSet();
    $success = $bookingSet->updateBookingStatus($bookingId, $newStatus);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Booking status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update booking status.']);
    }
    exit;
}

// If request is invalid
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
exit;
