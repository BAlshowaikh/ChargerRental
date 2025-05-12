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

    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("UPDATE Charger_point 
                          SET Name = ?, Charger_point_description = ?, Price_per_kWatt = ?, Connector_type = ?
                          WHERE Charger_point_ID = ? AND User_user_ID = ?");
    $stmt->execute([$name, $description, $cost, $connectorType, $chargerId, $userId]);

    header("Location: ManageChargePoints.php");
    exit;
}
?>
