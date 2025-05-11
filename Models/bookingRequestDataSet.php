<?php

require_once("Models/Database.php");
require_once("Models/bookingRequestData.php");

class bookingRequestDataSet
{
    protected $_dbInstance, $_dbHandle;

    public function __construct()
    {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getConnection();
    }

    public function createBooking($start, $end, $kw, $cpId, $userId, $statusId)
    {
        $query = "INSERT INTO Booking_request (
                    Created_timestamp, Booking_start, Booking_end, Is_paid, Amount_of_KW,
                    Charger_point_Charger_point_ID, User_user_ID, Booking_Status_Booking_status_ID)
                  VALUES (NOW(), :start, :end, 0, :kw, :cpId, :userId, :statusId)";

        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':start', $start);
        $statement->bindParam(':end', $end);
        $statement->bindParam(':kw', $kw);
        $statement->bindParam(':cpId', $cpId);
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':statusId', $statusId);

        $statement->execute();
    }

    public function getBookingsByUserId($userId)
    {
        $query = "SELECT * FROM Booking_request WHERE User_user_ID = :userId ORDER BY Booking_start DESC";
        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':userId', $userId);
        $statement->execute();

        $results = [];

        while ($row = $statement->fetch()) {
            $results[] = new bookingRequestData($row);
        }

        return $results;
    }

    public function getBookingsForChargerPoint($cpId)
    {
        $query = "SELECT * FROM Booking_request WHERE Charger_point_Charger_point_ID = :cpId ORDER BY Booking_start DESC";
        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':cpId', $cpId);
        $statement->execute();

        $results = [];

        while ($row = $statement->fetch()) {
            $results[] = new bookingRequestData($row);
        }

        return $results;
    }

    public function updateBookingStatus($bookingId, $newStatusId)
    {
        $query = "UPDATE Booking_request SET Booking_Status_Booking_status_ID = :statusId WHERE Booking_ID = :id";
        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':statusId', $newStatusId);
        $statement->bindParam(':id', $bookingId);
        $statement->execute();
    }
}
