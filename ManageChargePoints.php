<?php
require_once 'Models/Database.php';
require_once 'models/ChargerPointData.php';

session_start();

// Temporarily simulate login (for testing only)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Use actual user ID from your DB
}

$userId = $_SESSION['user_id']; // not hardcoded
$model = new ChargerPointData($userId);


// Connect to DB
$db = Database::getInstance();
$pdo = $db->getConnection();

$model = new ChargerPointData($userId);

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user already has a charger point
    $existing = $model->get();
    if ($existing) {
        echo "<script>alert('You already have a charger point.');</script>";
        header("Location: ManageChargePoints.php");
        exit;
    }

    // Gather form input
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $connectorType = $_POST['connector_type'];
    $rating = $_POST['rating'];
    $availableId = $_POST['available_status'];
    $locationId = $_POST['location_id'];

    // Handle image upload
    $imageUrl = '';
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir);
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imageUrl = $targetPath;
        }
    }

    // Insert into DB
    $stmt = $pdo->prepare("
        INSERT INTO Charger_point 
        (Name, Charger_point_description, Price_per_kWatt, Connector_type, Rating, Charger_image_url, Available_status_Available_ID, User_user_ID, Location_Location_ID)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $success = $stmt->execute([
        $name,
        $description,
        $price,
        $connectorType,
        $rating,
        $imageUrl,
        $availableId,
        $userId,
        $locationId
    ]);

    if (!$success) {
        echo "<pre>"; print_r($stmt->errorInfo()); echo "</pre>";
    }

    header("Location: ManageChargePoints.php");
    exit;
}

// Fetch updated charger point (after insert or for view)
$chargePoint = $model->get();
include 'Views/ManageChargePoints.phtml';
