<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointData.php';

session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 2 || $_SESSION['user_status'] !== 'Approve') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userID'];

$db = Database::getInstance()->getConnection();

// Fetch charger point data for this user
$stmt = $db->prepare("SELECT * FROM Charger_point WHERE user_ID = ?");
$stmt->execute([$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Only prepare the object if there's data
$chargePoint = $row ? new chargerPointData($row) : null;

// Load the view
require 'Views/HomeownerChargePoint.phtml';
