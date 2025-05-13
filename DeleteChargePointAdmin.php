<?php
require_once 'Models/Database.php';

session_start();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ManageChargePointsAdmin.php");
    exit;
}

// Check if charger ID is provided
if (!isset($_POST['id'])) {
    echo "No charger ID provided.";
    exit;
}

$id = $_POST['id'];
$db = Database::getInstance()->getConnection();

try {
    $stmt = $db->prepare("DELETE FROM Charger_point WHERE Charger_point_ID = ?");
    $stmt->execute([$id]);

    // Redirect after successful deletion
    header("Location: ManageChargePointsAdmin.php");
    exit;
} catch (PDOException $e) {
    echo "Error deleting charger point: " . $e->getMessage();
}
