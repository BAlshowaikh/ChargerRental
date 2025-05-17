<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointDataSet.php';

session_start();
$chargerSet = new chargerPointDataSet();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Validate ID
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) $errors[] = "Invalid charger ID.";

    // Sanitize and validate inputs
    $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $desc = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $price = $_POST['price'] ?? '';
    $connector = htmlspecialchars(trim($_POST['connector'] ?? ''), ENT_QUOTES, 'UTF-8');
    $status = $_POST['status'] ?? '';

    if ($name === '' || strlen($name) > 100) $errors[] = "Name is required and must be under 100 characters.";
    if (strlen($desc) > 500) $errors[] = "Description must be under 500 characters.";
    if (!is_numeric($price) || $price < 0) $errors[] = "Price must be a non-negative number.";
    if ($connector === '' || strlen($connector) > 50) $errors[] = "Connector type is required and must be under 50 characters.";
    if (!in_array($status, ['0', '1'])) $errors[] = "Invalid status selected.";

    // Stop and show errors if any
    if (!empty($errors)) {
        foreach ($errors as $e) echo "<p style='color:red;'>$e</p>";
        exit;
    }

    // Fetch current charger point
    $existing = $chargerSet->fetchChargerPointById($id);
    if (!$existing) die("Charger not found.");

    // Set image to current by default
    $image = $existing->getImageUrl();
    $imageChanged = false;

    // Image upload handling
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imageFile']['tmp_name'];
        $fileSize = $_FILES['imageFile']['size'];
        $mimeType = mime_content_type($fileTmpPath);
        $uploadDir = 'images/ChargerPoints/';
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mimeType, $allowedTypes)) die("Invalid image type. Only JPG and PNG are allowed.");
        if ($fileSize > 2 * 1024 * 1024) die("Image is too large. Max size is 2MB.");

        $extension = $allowedTypes[$mimeType];
        $image = "charger{$id}." . $extension;

        // Delete old images
        foreach (['jpg', 'png'] as $ext) {
            $old = $uploadDir . "charger{$id}." . $ext;
            if (file_exists($old)) unlink($old);
        }

        // Save new image
        if (move_uploaded_file($fileTmpPath, $uploadDir . $image)) {
            $imageChanged = true;
        }
    }

    // Change detection
    $somethingChanged = (
        trim($name) !== trim($existing->getName()) ||
        trim($desc) !== trim($existing->getDescription()) ||
        (float)$price != (float)$existing->getPricePerKW() ||
        strcasecmp($connector, $existing->getConnectorType()) !== 0 ||
        (int)$status !== (int)$existing->getAvailableStatusId() ||
        $imageChanged
    );

    if ($somethingChanged) {
        $chargerSet->updateChargerPointByValues($id, $name, $desc, $price, $connector, $image, $status);
        header("Location: ManageChargePointsAdmin.php?status=updated");
    } else {
        header("Location: ManageChargePointsAdmin.php?status=nochange");
    }
    exit;
}

// If GET: load the form with charger data
if (!isset($_GET['id'])) die("No charger point ID provided.");

$id = intval($_GET['id']);
$charger = $chargerSet->fetchChargerPointById($id);

if (!$charger) die("Charger not found.");

require 'Views/EditChargePointsAdmin.phtml';
