<?php
// class ChargerPointData {
//     private $homeownerId;

//     public function __construct($homeownerId) {
//         $this->homeownerId = $homeownerId;

//         if (!isset($_SESSION['charge_points'])) {
//             $_SESSION['charge_points'] = [];
//         }
//     }

//     public function get() {
//         $db = Database::getInstance()->getConnection();

//         $stmt = $db->prepare("SELECT * FROM Charger_point WHERE User_user_ID = ?");
//         $stmt->execute([$this->homeownerId]);
//         return $stmt->fetch(PDO::FETCH_ASSOC);
//     }


//     public function save($data) {
//         $_SESSION['charge_points'][$this->homeownerId] = $data;
//     }

class chargerPointData
{
    private $id, $name, $description, $pricePerKW, $connectorType, $rating, $imageUrl, $availableStatusId, $userId, $locationId;

    public function __construct($dbRow)
    {
        $this->id = $dbRow['Charger_point_ID'];
        $this->name = $dbRow['Name'];
        $this->description = $dbRow['Charger_point_description'];
        $this->pricePerKW = $dbRow['Price_per_kWatt'];
        $this->connectorType = $dbRow['Connector_type'];
        $this->rating = $dbRow['Rating'];
        $this->imageUrl = $dbRow['Charger_image_url'];
        $this->availableStatusId = $dbRow['Available_status_Available_ID'];
        $this->userId = $dbRow['User_user_ID'];
        $this->locationId = $dbRow['Location_Location_ID'];
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function getPricePerKW() { return $this->pricePerKW; }
    public function getConnectorType() { return $this->connectorType; }
    public function getRating() { return $this->rating; }
    public function getImageUrl() { return $this->imageUrl; }
    public function getAvailableStatusId() { return $this->availableStatusId; }
    public function getUserId() { return $this->userId; }
    public function getLocationId() { return $this->locationId; }

}
