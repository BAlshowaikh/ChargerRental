<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointData.php';

session_start();

// TEMPORARY - manually fake login session
$_SESSION['userID'] = 4; // Replace with your test user ID


//Check if user is logged in
$userId = $_SESSION['userID'] ?? null;

if (!$userId) {
    header("Location: login.php");
    exit;
}

$db = Database::getInstance()->getConnection();

// Fetch charger point data for this user
$stmt = $db->prepare("SELECT * FROM Charger_point WHERE user_ID = ?");
$stmt->execute([$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$chargePoint = $row ? new chargerPointData($row) : null;

// Load the view
require 'Views/ManageChargePoints.phtml';
