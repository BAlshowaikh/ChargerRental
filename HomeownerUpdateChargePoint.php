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
    $chargerId      = intval($_POST['charger_id']);
    $name           = $_POST['name'];
    $description    = $_POST['description'];
    $cost           = $_POST['cost_per_kwh'];
    $connectorType  = $_POST['connector_type'];

    $db = Database::getInstance()->getConnection();

    // Fetch current charger point
    $stmt = $db->prepare("SELECT * FROM Charger_point WHERE charger_point_id = ? AND user_id = ?");
    $stmt->execute([$chargerId, $userId]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current) {
        header("Location: HomeownerChargePoint.php");
        exit;
    }

    // Use current image as default
    $imageUrl = $current['charger_image_url'];
    $imageChanged = false;

    // Handle image upload
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

    // Check if anything changed
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
        $updateStmt->execute([$name, $description, $cost, $connectorType, $imageUrl, $chargerId, $userId]);

        header("Location: HomeownerChargePoint.php?status=updated");
        exit;
    } else {
        header("Location: HomeownerChargePoint.php?status=nochange");
        exit;
    }
}
?>
