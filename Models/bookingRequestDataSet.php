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

    public function getBookingsByMonth($attribute, $month) {
        $query = "SELECT * FROM Booking_request WHERE MONTH($attribute) = :month";
        $stmt = $this->_dbHandle->prepare($query);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingsGroupedByChargerID() {
        $stmt = $this->_dbHandle->query("SELECT Charger_point_ID, COUNT(*) AS num_requests, AVG(Price_per_kwatt) AS avg_price FROM Booking_request GROUP BY Charger_point_ID");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingsGroupedByUserID() {
        $stmt = $this->_dbHandle->query("SELECT User_ID, COUNT(*) AS num_requests FROM Booking_request GROUP BY User_ID");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingsGroupedByStatusID() {
        $stmt = $this->_dbHandle->query("SELECT Booking_status_ID, COUNT(*) AS num_requests FROM Booking_request GROUP BY Booking_status_ID");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUniqueMonths() {
        $stmt = $this->_dbHandle->query("SELECT DISTINCT MONTH(Created_timestamp) AS month FROM Booking_request ORDER BY month");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getRequestsCountByPrice() {
        $stmt = $this->_dbHandle->query("
        SELECT ROUND(Price_per_kwatt, 2) AS rounded_price, COUNT(*) AS num_requests 
        FROM Booking_request 
        GROUP BY rounded_price
    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}