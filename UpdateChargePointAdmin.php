<?php
//require_once 'Models/Database.php';
//
//if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//    echo "Invalid request.";
//    exit;
//}
//
//$id          = $_POST['id'];
//$name        = $_POST['name'];
//$desc        = $_POST['description'];
//$price       = $_POST['price'];
//$connector   = $_POST['connector'];
//$image       = "charger{$id}.jpg";
//$status      = $_POST['status'];
//
//$db = Database::getInstance()->getConnection();
//$stmt = $db->prepare("UPDATE Charger_point SET Name=?, Charger_point_description=?, Price_per_kWatt=?, Connector_type=?, Charger_image_url=?, Available_status_ID=? WHERE Charger_point_ID=?");
//$stmt->execute([$name, $desc, $price, $connector, $image, $status, $id]);
//
//// Redirect back to admin page
//header("Location: ManageChargePointsAdmin.php");
//exit;
