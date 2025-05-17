<?php
session_start();

// DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect if not logged in
if (!isset($_SESSION['userID']) || !isset($_SESSION['role'])) {
    header("Location: Login.php");
    exit();
}

$view = new stdClass();
$view->pageTitle = 'Homepage';

$showLoginPopup = false;
$message = "";

if (isset($_GET['login'])) {
    $userID = $_SESSION['userID'];
    $role = $_SESSION['role'];
    $showLoginPopup = true;

    require_once 'Models/Database.php';
    $db = Database::getInstance()->getConnection();

    if ($role == 1) {
        // Admin
        $stmt = $db->query("SELECT COUNT(*) FROM User WHERE user_status = 'Pending'");
        $count = $stmt->fetchColumn();
        $message = "You have $count pending user registration requests. Head to the Admin Panel to review them.";

    } elseif ($role == 2) {
        // Homeowner
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM Booking_request br
            JOIN Charger_point cp ON br.Charger_point_ID = cp.Charger_point_ID
            WHERE cp.User_ID = ? AND br.Booking_status_ID = (
                SELECT Booking_status_ID FROM Booking_Status WHERE Booking_status = 'Pending' LIMIT 1
            )
        ");
        $stmt->execute([$userID]);
        $count = $stmt->fetchColumn();
        $message = "You have $count pending booking requests for your charging stations. Review them in your dashboard.";

    } elseif ($role == 3) {
        // Customer/User
        $stmt = $db->prepare("
            SELECT bs.Booking_status 
            FROM Booking_request br
            JOIN Booking_Status bs ON br.Booking_status_ID = bs.Booking_status_ID
            WHERE br.User_ID = ?
            ORDER BY br.Booking_ID DESC LIMIT 1
        ");
        $stmt->execute([$userID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && in_array(strtolower($row['Booking_status']), ['approved', 'declined'])) {
            $message = "Good news! Your latest booking request was {$row['Booking_status']}.";
        } else {
            $message = "You’re all set! No updates on your bookings yet.";
        }
    }
}

$view->showLoginPopup = $showLoginPopup;
$view->popupMessage = $message;

require_once('Views/index.phtml');
