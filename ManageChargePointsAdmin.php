<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointData.php';

session_start();

// TEMPORARY: Fake Admin Session
$_SESSION['user_id'] = 1;
$_SESSION['user_role_id'] = 1; // 1 = Admin


$db = Database::getInstance()->getConnection();

//  Get all charger points
$stmt = $db->prepare("SELECT * FROM Charger_point");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$chargerPointObjs = [];
foreach ($rows as $row) {
    $chargerPointObjs[] = new chargerPointData($row);
}



require 'Views/ManageChargePointsAdmin.phtml';
