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
                    Price_per_kwatt,
                    Charger_point_ID,
                    User_ID,
                    Booking_status_ID
                  ) VALUES (
                    NOW(),
                    :start,
                    :end,
                    :price,
                    :cpId,
                    :userId,
                    :statusId
                  )";

        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':start', $start);
        $statement->bindParam(':end', $end);
        $statement->bindParam(':price', $pricePerKWh);
        $statement->bindParam(':cpId', $cpId);
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':statusId', $statusId);

        $statement->execute();
    }

    public function getBookingsByUserId($userId)
    {
        $query = "SELECT * FROM Booking_request WHERE User_ID = :userId ORDER BY Booking_start DESC";
        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':userId', $userId);
        $statement->execute();

        $results = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new bookingRequestData($row);
        }

        return $results;
    }

    public function getBookingsWithFilters($userId, $status, $sort, $offset, $limit)
    {
        $params = [$userId];

        $query = "
        SELECT 
            br.*,
            bs.Booking_status
        FROM Booking_request br
        JOIN Booking_Status bs ON br.Booking_status_ID = bs.Booking_status_ID
        WHERE br.User_ID = ?
        ";

        if (!empty($status)) {
            $query .= " AND bs.Booking_status = ?";
            $params[] = $status;
        }

        $order = ($sort === 'oldest') ? "ASC" : "DESC";
        $query .= " ORDER BY br.Booking_start $order LIMIT $limit OFFSET $offset";

        $statement = $this->_dbHandle->prepare($query);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countBookingsWithFilters($userId, $status)
    {
        $params = [$userId];

        $query = "
        SELECT COUNT(*) 
        FROM Booking_request br
        JOIN Booking_Status bs ON br.Booking_status_ID = bs.Booking_status_ID
        WHERE br.User_ID = ?
        ";

        if (!empty($status)) {
            $query .= " AND bs.Booking_status = ?";
            $params[] = $status;
        }

        $statement = $this->_dbHandle->prepare($query);
        $statement->execute($params);
        return $statement->fetchColumn();
    }

    public function fetchBookingsForHomeowner($homeownerId, $status = 'all')
    {
        $query = "SELECT br.*, cp.Name AS charger_name, bs.Booking_status
              FROM Booking_request br
              INNER JOIN Charger_point cp ON br.Charger_point_ID = cp.charger_point_id
              INNER JOIN Booking_Status bs ON br.Booking_status_ID = bs.Booking_status_id
              WHERE cp.user_id = :homeownerId";

        if ($status !== 'all') {
            $query .= " AND br.Booking_status_ID = :status";
        }

        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':homeownerId', $homeownerId);

        if ($status !== 'all') {
            $statement->bindParam(':status', $status);
        }

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingsWithStatusByUserId($userId)
    {
        $query = "SELECT br.*, bs.Booking_status 
                  FROM Booking_request br
                  INNER JOIN Booking_Status bs ON br.Booking_status_ID = bs.Booking_status_ID
                  WHERE br.User_ID = :userId
                  ORDER BY br.Booking_start DESC";

        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':userId', $userId);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingsForChargerPoint($cpId)
    {
        $query = "SELECT * FROM Booking_request WHERE Charger_point_ID = :cpId ORDER BY Booking_start DESC";
        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':cpId', $cpId);
        $statement->execute();

        $results = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new bookingRequestData($row);
        }

        return $results;
    }

    public function updateBookingStatus($bookingId, $newStatusId)
    {
        $query = "UPDATE Booking_request SET Booking_status_ID = :statusId WHERE Booking_ID = :id";
        $statement = $this->_dbHandle->prepare($query);
        $statement->bindParam(':statusId', $newStatusId);
        $statement->bindParam(':id', $bookingId);
        $statement->execute();
    }
}
