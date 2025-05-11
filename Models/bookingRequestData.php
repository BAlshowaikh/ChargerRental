<?php

class bookingRequestData
{
    private $id, $created, $start, $end, $isPaid, $kw, $chargerPointId, $userId, $statusId;

    public function __construct($dbRow)
    {
        $this->id = $dbRow['Booking_ID'];
        $this->created = $dbRow['Created_timestamp'];
        $this->start = $dbRow['Booking_start'];
        $this->end = $dbRow['Booking_end'];
        $this->isPaid = $dbRow['Is_paid'];
        $this->kw = $dbRow['Amount_of_KW'];
        $this->chargerPointId = $dbRow['Charger_point_Charger_point_ID'];
        $this->userId = $dbRow['User_user_ID'];
        $this->statusId = $dbRow['Booking_Status_Booking_status_ID'];
    }

    public function getId() { return $this->id; }
    public function getCreated() { return $this->created; }
    public function getStart() { return $this->start; }
    public function getEnd() { return $this->end; }
    public function isPaid() { return $this->isPaid; }
    public function getKW() { return $this->kw; }
    public function getChargerPointId() { return $this->chargerPointId; }
    public function getUserId() { return $this->userId; }
    public function getStatusId() { return $this->statusId; }
}
