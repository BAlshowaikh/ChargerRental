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

    public function createBooking($start, $end, $kw, $cpId, $userId, $statusId, $pricePerKWh)
    {
        $query = "INSERT INTO Booking_request (
                    Created_timestamp,
                    Booking_start,
                    Booking_end,
                    Is_paid,
                    Amount_of_KW,
                    Price_per_KWh,
                    Charger_point_Charger_point_ID,
                    User_user_ID,
                    Booking_Status_Booking_status_ID)
                  VALUES (
                    NOW(),
                    :start,
                    :end,
                    0,
                    :kw,
                    :price,
                    :cpId,
                    :userId,
                    :statusId)";

        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':start', $start);
        $statement->bindParam(':end', $end);
        $statement->bindParam(':kw', $kw);
        $statement->bindParam(':price', $pricePerKWh);
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

    public function getBookingsWithFilters($userId, $status, $sort, $offset, $limit) {
        $params = [$userId];

        $query = "
        SELECT 
            br.*,
            bs.Booking_status,
            cp.Price_per_kWatt AS Price_per_KWh
        FROM Booking_request br
        JOIN Booking_Status bs ON br.Booking_Status_Booking_status_ID = bs.Booking_status_ID
        JOIN Charger_point cp ON br.Charger_point_Charger_point_ID = cp.Charger_point_ID
        WHERE br.User_user_ID = ?
    ";

        if (!empty($status)) {
            $query .= " AND bs.Booking_status = ?";
            $params[] = $status;
        }

        $order = ($sort === 'oldest') ? "ASC" : "DESC";
        $query .= " ORDER BY br.Booking_start $order LIMIT $limit OFFSET $offset";

        $statement = $this->_dbHandle->prepare($query);
        $statement->execute($params);

        return $statement->fetchAll();
    }


    public function countBookingsWithFilters($userId, $status) {
        $params = [$userId];

        $query = "
        SELECT COUNT(*) 
        FROM Booking_request br
        JOIN Booking_Status bs ON br.Booking_Status_Booking_status_ID = bs.Booking_status_ID
        WHERE br.User_user_ID = ?
    ";

        if (!empty($status)) {
            $query .= " AND bs.Booking_status = ?";
            $params[] = $status;
        }

        $statement = $this->_dbHandle->prepare($query);
        $statement->execute($params);
        return $statement->fetchColumn();
    }


    public function getBookingsWithStatusByUserId($userId)
    {
        $query = "SELECT br.*, bs.Booking_status 
              FROM Booking_request br
              INNER JOIN Booking_Status bs
              ON br.Booking_Status_Booking_status_ID = bs.Booking_status_ID
              WHERE br.User_user_ID = :userId
              ORDER BY br.Booking_start DESC";

        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':userId', $userId);
        $statement->execute();

        return $statement->fetchAll();
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
