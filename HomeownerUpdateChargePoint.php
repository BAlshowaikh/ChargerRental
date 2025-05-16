<?php
require_once 'Models/Database.php';

session_start();
$userId = $_SESSION['userID'] ?? null;

if (!$userId) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chargerId = $_POST['charger_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $cost = $_POST['cost_per_kwh'];
    $connectorType = $_POST['connector_type'];
    $imageUrl = $_POST['image_url'];

    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("UPDATE Charger_point 
                          SET Name = ?, 
                              Charger_point_description = ?, 
                              Price_per_kWatt = ?, 
                              Connector_type = ?, 
                              Charger_image_url = ?
                          WHERE Charger_point_ID = ? AND user_ID = ?");

    $stmt->execute([$name, $description, $cost, $connectorType, $imageUrl, $chargerId, $userId]);

    header("Location: HomeownerChargePoint.php");
    exit;
}

?>
