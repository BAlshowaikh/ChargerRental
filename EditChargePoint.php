<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointData.php';

session_start();

if (!isset($_GET['id'])) {
    echo "No charger point ID provided.";
    exit;
}

$id = $_GET['id'];
$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("SELECT * FROM Charger_point WHERE Charger_point_ID = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "Charger point not found.";
    exit;
}

$charger = new chargerPointData($row);

require 'Views/EditChargePointsAdmin.phtml';
?>
