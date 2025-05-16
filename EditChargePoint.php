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

    $image = "charger{$id}.jpg";

    // Handle image upload
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imageFile']['tmp_name'];
        $fileSize = $_FILES['imageFile']['size'];
        $mimeType = mime_content_type($fileTmpPath);
        $uploadDir = 'images/ChargerPoints/';
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mimeType, $allowedTypes)) {
            die("Invalid image type. Only JPG and PNG are allowed.");
        }

        if ($fileSize > 2 * 1024 * 1024) {
            die("Image is too large. Max size is 2MB.");
        }

        $extension = $allowedTypes[$mimeType];
        $image = "charger{$id}." . $extension;

        // Delete any existing version of the image
        foreach (['jpg', 'png'] as $ext) {
            $old = $uploadDir . "charger{$id}." . $ext;
            if (file_exists($old)) unlink($old);
        }

        move_uploaded_file($fileTmpPath, $uploadDir . $image);
    }

    //  Use dataset to update (pass raw values)
    $chargerSet->updateChargerPointByValues($id, $name, $desc, $price, $connector, $image, $status);

    header("Location: ManageChargePointsAdmin.php");
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
