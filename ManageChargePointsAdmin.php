<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointData.php';

session_start();

// Simulate admin session only if not already set
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role_id'] = 1; // 1 = Admin
}

// ✅ Check admin permission (optional but recommended)
if ($_SESSION['user_role_id'] !== 1) {
    header("Location: unauthorized.php");
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    // ✅ Get all charger points
    $stmt = $db->prepare("SELECT * FROM Charger_point");
    $stmt->execute();

    $chargerPointObjs = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $chargerPointObjs[] = new chargerPointData($row);
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

require 'Views/ManageChargePointsAdmin.phtml';
