<?php

require_once '../Models/Database.php';

// Get PDO connection using your singleton Database class
$db = Database::getInstance();
$pdo = $db->getConnection();

// Check if connection succeeded
if (!$pdo) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$sql = "
    SELECT 
      cp.Name,
      cp.Charger_point_ID,
      cp.Charger_point_description,
      cp.Price_per_kWatt,
      cp.Connector_type,
      cp.Rating,
      l.Latitude, 
      l.Longitude,
      a.Available_status AS Availability_status
    FROM Charger_point cp
    JOIN Location l ON cp.Location_Location_ID = l.Location_ID
    JOIN Available_status a ON cp.Available_status_Available_ID = a.Available_ID

";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $chargers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($chargers);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
