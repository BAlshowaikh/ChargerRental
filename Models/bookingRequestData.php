<?php

class bookingRequestData
{
    private $id, $created, $start, $end, $pricePerKW, $chargerPointId, $userId, $statusId;

    public function __construct($dbRow)
    {
        $this->id = $dbRow['Booking_ID'];
        $this->created = $dbRow['Created_timestamp'];
        $this->start = $dbRow['Booking_start'];
        $this->end = $dbRow['Booking_end'];
        $this->pricePerKW = $dbRow['Price_per_kwatt'];
        $this->chargerPointId = $dbRow['Charger_point_ID'];
        $this->userId = $dbRow['User_ID'];
        $this->statusId = $dbRow['Booking_status_ID'];
    }

    // Getters for all properties
    public function getId() { return $this->id; }
    public function getCreated() { return $this->created; }
    public function getStart() { return $this->start; }
    public function getEnd() { return $this->end; }
    public function getPricePerKW() { return $this->pricePerKW; }
    public function getChargerPointId() { return $this->chargerPointId; }
    public function getUserId() { return $this->userId; }
    public function getStatusId() { return $this->statusId; }
}

