<?php
require_once 'Models/Database.php';
session_start();

// Check user authentication
if (!isset($_SESSION['userID']) || $_SESSION['role'] != 2 || $_SESSION['user_status'] !== 'Approve') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Sanitize & validate charger ID
    $chargerId = isset($_POST['charger_id']) ? intval($_POST['charger_id']) : 0;
    if ($chargerId <= 0) $errors[] = "Invalid charger ID.";

    // Sanitize other inputs
    $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $cost = $_POST['cost_per_kwh'] ?? '';
    $connectorType = htmlspecialchars(trim($_POST['connector_type'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Field validations
    if ($name === '' || strlen($name) > 100) $errors[] = "Name is required and must be under 100 characters.";
    if (strlen($description) > 500) $errors[] = "Description must be under 500 characters.";
    if (!is_numeric($cost) || $cost < 0) $errors[] = "Cost must be a non-negative number.";
    if ($connectorType === '' || strlen($connectorType) > 50) $errors[] = "Connector type is required and must be under 50 characters.";

    // Show errors if any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit;
    }

    $db = Database::getInstance()->getConnection();

    // Fetch current charger point
    $stmt = $db->prepare("SELECT * FROM Charger_point WHERE charger_point_id = ? AND user_id = ?");
    $stmt->execute([$chargerId, $userId]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current) {
        header("Location: HomeownerChargePoint.php");
        exit;
    }

    // Default to existing image
    $imageUrl = $current['charger_image_url'];
    $imageChanged = false;

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileSize    = $_FILES['image_file']['size'];
        $mimeType    = mime_content_type($fileTmpPath);
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mimeType, $allowedTypes)) {
            die("Invalid image type.");
        }

        if ($fileSize > 2 * 1024 * 1024) {
            die("Image is too large.");
        }

        $extension = $allowedTypes[$mimeType];
        $imageFilename = "charger{$chargerId}." . $extension;

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/images/ChargerPoints/';
        foreach (['jpg', 'png'] as $ext) {
            $oldFile = $uploadDir . "charger{$chargerId}." . $ext;
            if (file_exists($oldFile)) unlink($oldFile);
        }

        if (move_uploaded_file($fileTmpPath, $uploadDir . $imageFilename)) {
            $imageUrl = $imageFilename;
            $imageChanged = true;
        }
    }

    // Detect changes
    $somethingChanged = (
        trim($name) !== trim($current['Name']) ||
        trim($description) !== trim($current['charger_point_description']) ||
        (float)$cost != (float)$current['price_per_kwatt'] ||
        strcasecmp($connectorType, $current['connector_type']) !== 0 ||
        $imageChanged
    );

    if ($somethingChanged) {
        $updateStmt = $db->prepare("
            UPDATE Charger_point 
            SET Name = ?, 
                charger_point_description = ?, 
                price_per_kwatt = ?, 
                connector_type = ?, 
                charger_image_url = ?
            WHERE charger_point_id = ? AND user_id = ?
        ");
        $updateStmt->execute([
            $name,
            $description,
            $cost,
            $connectorType,
            $imageUrl,
            $chargerId,
            $userId
        ]);

        header("Location: HomeownerChargePoint.php?status=updated");
        exit;
    } else {
        header("Location: HomeownerChargePoint.php?status=nochange");
        exit;
    }
}
?>
