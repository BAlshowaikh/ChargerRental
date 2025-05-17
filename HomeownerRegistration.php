<?php

require ("Models/userDataSet.php");

$view = new stdClass();

session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $view->errors = [];

    // Check if file is uploaded correctly
    if (
        isset($_FILES['chargePointImage']) &&
        $_FILES['chargePointImage']['error'] === UPLOAD_ERR_OK
    ) {
        $fileTmpPath = $_FILES['chargePointImage']['tmp_name'];
        $fileSize = $_FILES['chargePointImage']['size'];
        $mimeType = mime_content_type($fileTmpPath);
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mimeType, $allowedTypes)) {
            $view->errors['chargePointImage'] = "Invalid image type. Only JPG and PNG are allowed.";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $view->errors['chargePointImage'] = "Image exceeds 2 MB limit.";
        }
    } else {
        $view->errors['chargePointImage'] = 'Please select an image to upload.';
    }

    if (empty($view->errors)) {
        $userDataSet = new userDataSet();

        $regDate    = date('Y-m-d');
        $userStatus = 'Reject';
        $latitude = $_POST['latitude'] ?? null;
        $longitude = $_POST['longitude'] ?? null;

        if (!$latitude || !$longitude) {
            $view->errors['general'] = "Could not capture location.";
        }

        try {
            // Step 1: Insert user & charger point with temporary image name
            $chargerId = $userDataSet->registerHomeOwner(
                $_POST['fname'],
                $_POST['lname'],
                $_POST['email'],
                $_POST['password'],
                $_POST['phno'],
                $regDate,
                $userStatus,
                $_POST["chargePointName"],
                '',
                $_POST['chargePoint'],
                floatval($_POST['price']),
                $_POST['availability'],
                $_POST['connectorType'],
                $_POST["road"],
                $_POST["city"],
                $_POST["home_number"],
                $_POST["zip_code"],
                $_POST["latitude"],
                $_POST["longitude"]
            );

            // Step 2: Build proper image name and save file
            $extension = $allowedTypes[$mimeType];
            $imageName = "charger{$chargerId}.{$extension}";
            $uploadDir = __DIR__ . "/images/ChargerPoints/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Remove existing files with same base name but different extensions
            foreach (["jpg", "png"] as $ext) {
                $old = $uploadDir . "charger{$chargerId}." . $ext;
                if (file_exists($old)) {
                    unlink($old);
                }
            }

            $targetPath = $uploadDir . $imageName;
            if (!move_uploaded_file($fileTmpPath, $targetPath)) {
                throw new RuntimeException("Failed to save uploaded image.");
            }

            // Step 3: Update DB with image name
            $userDataSet->updateChargerImage($chargerId, $imageName);

            $view->successReg = "Registration submitted! Awaiting admin approval.";
        } catch (Exception $e) {
            $view->errors['general'] = "Registration failed: " . $e->getMessage();
        }
    }
}


require_once('Views/HomeownerRegistration.phtml');