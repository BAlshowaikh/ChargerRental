<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointData.php';

session_start();
$_SESSION['userID'] = 4; // TEMP LOGIN

$userId = $_SESSION['userID'] ?? null;

if (!$userId) {
    header("Location: login.php");
    exit;
}

$db = Database::getInstance()->getConnection();
$chargePoint = chargerPointData::getByUserId($db, $userId);

require 'Views/HomeownerChargePoint.phtml';
