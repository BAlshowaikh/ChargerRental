<?php
require_once 'Models/chargerPointDataSet.php';
session_start();

// Check user authentication
if (!isset($_SESSION['userID']) || $_SESSION['role'] != 2 || $_SESSION['user_status'] !== 'Approve') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Get and validate charger ID
    $chargerId = isset($_POST['charger_id']) ? intval($_POST['charger_id']) : 0;
    if ($chargerId <= 0) $errors[] = "Invalid charger ID.";

    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $cost = $_POST['cost_per_kwh'] ?? '';
    $connectorType = htmlspecialchars(trim($_POST['connector_type'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Validate fields
    if ($name === '' || strlen($name) > 100) $errors[] = "Name is required and must be under 100 characters.";
    if (strlen($description) > 500) $errors[] = "Description must be under 500 characters.";
    if (!is_numeric($cost) || $cost < 0) $errors[] = "Cost must be a non-negative number.";
    if ($connectorType === '' || strlen($connectorType) > 50) $errors[] = "Connector type is required and must be under 50 characters.";

    // Show any errors
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit;
    }

    // Fetch current data
    $chargerDataSet = new chargerPointDataSet();
    $current = $chargerDataSet->getChargerPointByIdAndUser($chargerId, $userId);

    if (!$current) {
        header("Location: HomeownerChargePoint.php");
        exit;
    }

    // Image handling
    $imageUrl = $current->getImageUrl();
    $imageChanged = false;

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileSize    = $_FILES['image_file']['size'];

        // Use safer fallback if mime_content_type is not available
        $mimeType = function_exists('mime_content_type')
            ? mime_content_type($fileTmpPath)
            : $_FILES['image_file']['type'];

        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mimeType, $allowedTypes)) {
            die("Invalid image type.");
        }

        if ($fileSize > 2 * 1024 * 1024) {
            die("Image is too large.");
        }

        $extension = $allowedTypes[$mimeType];
        $imageFilename = "charger{$chargerId}." . $extension;

        $uploadDir = __DIR__ . '/images/ChargerPoints/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach (['jpg', 'png'] as $ext) {
            $oldFile = $uploadDir . "charger{$chargerId}." . $ext;
            if (file_exists($oldFile)) unlink($oldFile);
        }

        if (move_uploaded_file($fileTmpPath, $uploadDir . $imageFilename)) {
            $imageUrl = $imageFilename;
            $imageChanged = true;
        }
    }

    // Check for actual changes
    $somethingChanged = (
        trim($name) !== trim($current->getName()) ||
        trim($description) !== trim($current->getDescription()) ||
        (float)$cost != (float)$current->getPricePerKW() ||
        strcasecmp($connectorType, $current->getConnectorType()) !== 0 ||
        $imageChanged
    );

    if ($somethingChanged) {
        $chargerDataSet->updateChargerPoint($chargerId, $userId, $name, $description, $cost, $connectorType, $imageUrl);
        header("Location: HomeownerChargePoint.php?status=updated");
    } else {
        header("Location: HomeownerChargePoint.php?status=nochange");
    }

    exit;
}
?>
