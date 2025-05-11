<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointData.php';

session_start();

// ✅ Ensure user is logged in
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$userId) {
    header("Location: login.php");
    exit;
}

$db = Database::getInstance()->getConnection();

// ✅ Check if the user already has a charger point
$stmt = $db->prepare("SELECT * FROM Charger_point WHERE User_user_ID = ?");
$stmt->execute([$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Create chargerPointData instance if data exists
$chargePoint = $row ? new chargerPointData($row) : null;

// ✅ Handle new charger point submission ONLY if user doesn't have one
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $chargePoint === null) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['cost_per_kwh'];
    $connector = $_POST['connector_type'];
    $available = isset($_POST['is_available']) ? 1 : 0;
    $locationId = 1;
    $rating = 5; // default or static
    $imagePath = '';

    // ✅ Handle file upload if provided
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    // ✅ Insert charger point into DB
    $insertStmt = $db->prepare("
        INSERT INTO Charger_point 
        (Name, Charger_point_description, Price_per_kWatt, Connector_type, Rating, Charger_image_url, Available_status_Available_ID, User_user_ID, Location_Location_ID)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insertStmt->execute([
        $name, $description, $price, $connector, $rating,
        $imagePath, $available, $userId, $locationId
    ]);

    // ✅ Redirect to prevent re-posting on refresh
    header("Location: ManageChargePoints.php");
    exit;
}

// ✅ Render the view and provide $chargePoint to the template
include 'Views/ManageChargePoints.phtml';
