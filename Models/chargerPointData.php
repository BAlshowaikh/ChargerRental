<?php
class ChargerPointData {
    private $homeownerId;

    public function __construct($homeownerId) {
        $this->homeownerId = $homeownerId;

        if (!isset($_SESSION['charge_points'])) {
            $_SESSION['charge_points'] = [];
        }
    }

    public function get() {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM Charger_point WHERE User_user_ID = ?");
        $stmt->execute([$this->homeownerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function save($data) {
        $_SESSION['charge_points'][$this->homeownerId] = $data;
    }
}
