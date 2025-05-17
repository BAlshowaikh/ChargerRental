<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointDataSet.php';

session_start();
$chargerSet = new chargerPointDataSet();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = intval($_POST['id']);
    $name      = $_POST['name'];
    $desc      = $_POST['description'];
    $price     = $_POST['price'];
    $connector = $_POST['connector'];
    $status    = $_POST['status'];

    // Fetch current data
    $existing = $chargerSet->fetchChargerPointById($id);
    if (!$existing) {
        die("Charger not found.");
    }

    // Default to current image
    $image = $existing->getImageUrl();
    $imageChanged = false;

    // Handle image upload
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imageFile']['tmp_name'];
        $fileSize    = $_FILES['imageFile']['size'];
        $mimeType    = mime_content_type($fileTmpPath);
        $uploadDir   = 'images/ChargerPoints/';
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mimeType, $allowedTypes)) {
            die("Invalid image type. Only JPG and PNG are allowed.");
        }

        if ($fileSize > 2 * 1024 * 1024) {
            die("Image is too large. Max size is 2MB.");
        }

        $extension = $allowedTypes[$mimeType];
        $image = "charger{$id}." . $extension;

        // Delete existing image
        foreach (['jpg', 'png'] as $ext) {
            $old = $uploadDir . "charger{$id}." . $ext;
            if (file_exists($old)) unlink($old);
        }

        if (move_uploaded_file($fileTmpPath, $uploadDir . $image)) {
            $imageChanged = true;
        }
    }

    // Check for changes
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

} else {
    if (!isset($_GET['id'])) {
        die("No charger point ID provided.");
    }

    $id = $_GET['id'];
    $charger = $chargerSet->fetchChargerPointById($id);

    if (!$charger) {
        die("Charger not found.");
    }

    require 'Views/EditChargePointsAdmin.phtml';
}
