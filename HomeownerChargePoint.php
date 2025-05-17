<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointDataSet.php';

session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 2 || $_SESSION['user_status'] !== 'Approve') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userID'];

// Fetch charger point data for this user
$chargerSet = new chargerPointDataSet();
$chargePoint = $chargerSet->getChargerPointByUserId($userId);

// Load the view
require 'Views/HomeownerChargePoint.phtml';
