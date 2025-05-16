<?php
require_once 'Models/Database.php';
require_once 'Models/chargerPointData.php';

session_start();
$db = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission (update)
    $id        = intval($_POST['id']);
    $name      = $_POST['name'];
    $desc      = $_POST['description'];
    $price     = $_POST['price'];
    $connector = $_POST['connector'];
    $status    = $_POST['status'];

    // Default image file name
    $image = "charger{$id}.jpg";

    // Handle image upload if a new one is submitted
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imageFile']['tmp_name'];
        $fileSize = $_FILES['imageFile']['size'];
        $mimeType = mime_content_type($fileTmpPath);
        $uploadDir = 'images/ChargerPoints/';
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mimeType, $allowedTypes)) {
            die("Invalid image type.");
        }

        if ($fileSize > 2 * 1024 * 1024) {
            die("Image is too large.");
        }

        $extension = $allowedTypes[$mimeType];
        $image = "charger{$id}." . $extension;

        // Delete old formats
        foreach (['jpg', 'png'] as $ext) {
            $old = $uploadDir . "charger{$id}." . $ext;
            if (file_exists($old)) unlink($old);
        }

        move_uploaded_file($fileTmpPath, $uploadDir . $image);
    }

    // Update DB
    $stmt = $db->prepare("UPDATE Charger_point SET Name=?, Charger_point_description=?, Price_per_kWatt=?, Connector_type=?, Charger_image_url=?, Available_status_ID=? WHERE Charger_point_ID=?");
    $stmt->execute([$name, $desc, $price, $connector, $image, $status, $id]);

    // Redirect
    header("Location: ManageChargePointsAdmin.php");
    exit;

} else {
    // Handle GET: show form
    if (!isset($_GET['id'])) {
        die("No charger point ID provided.");
    }

    $id = $_GET['id'];
    $stmt = $db->prepare("SELECT * FROM Charger_point WHERE Charger_point_ID = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Charger not found.");
    }

    $charger = new chargerPointData($row);

    // Render form view
    require 'Views/EditChargePointsAdmin.phtml';
}
?>
