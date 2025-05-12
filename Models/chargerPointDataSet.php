<?php

require_once("Models/Database.php");
require_once("Models/chargerPointData.php");

class chargerPointDataSet
{
    protected $_dbInstance, $_dbHandle;

    public function __construct()
    {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getConnection();
    }

    public function fetchAllChargerPoints()
    {
        $sqlQuery = 'SELECT * FROM Charger_point';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();

        $dataSet = [];

        while ($row = $statement->fetch()) {
            $dataSet[] = new chargerPointData($row);
        }

        return $dataSet;
    }

    public function fetchChargerPointById($id)
    {
        $sqlQuery = 'SELECT * FROM Charger_point WHERE Charger_point_ID = :id';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(':id', $id);
        $statement->execute();

        if ($row = $statement->fetch()) {
            return new chargerPointData($row);
        }

        return null;
    }

    public function fetchChargerPointsByUserId($userId)
    {
        $sqlQuery = 'SELECT * FROM Charger_point WHERE User_user_ID = :userId';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(':userId', $userId);
        $statement->execute();

        $dataSet = [];

        while ($row = $statement->fetch()) {
            $dataSet[] = new chargerPointData($row);
        }

        return $dataSet;
    }

    // Function to retrieve the chrager points details

    public function fetchAllDetailedChargerPoints()
    {
        $sql = "
        SELECT 
          cp.Name,
          cp.Charger_point_ID,
          cp.Charger_point_description,
          cp.Price_per_kWatt,
          cp.Connector_type,
          cp.Rating,
          cp.Charger_image_url,
          l.Latitude, 
          l.Longitude,
          a.Available_status AS Availability_status
        FROM Charger_point cp
        JOIN Location l ON cp.Location_Location_ID = l.Location_ID
        JOIN Available_status a ON cp.Available_status_Available_ID = a.Available_ID
    ";

        $statement = $this->_dbHandle->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Code for pagination
    public function fetchDetailedChargerPointsPaginated($limit, $offset)
    {
        $sql = "
        SELECT 
            cp.Name,
            cp.Charger_point_ID,
            cp.Charger_point_description,
            cp.Price_per_kWatt,
            cp.Connector_type,
            cp.Rating,
            cp.Charger_image_url,
            l.Latitude, 
            l.Longitude,
            a.Available_status AS Availability_status
        FROM Charger_point cp
        JOIN Location l ON cp.Location_Location_ID = l.Location_ID
        JOIN Available_status a ON cp.Available_status_Available_ID = a.Available_ID
        LIMIT :limit OFFSET :offset
    ";

        $statement = $this->_dbHandle->prepare($sql);
        $statement->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalChargerCount()
    {
        $sql = "SELECT COUNT(*) as total FROM Charger_point";
        $statement = $this->_dbHandle->prepare($sql);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function fetchFilteredChargerPoints($filters, $limit = null, $offset = null) {
        $sql = "
        SELECT 
            cp.Name,
            cp.Charger_point_ID,
            cp.Charger_point_description,
            cp.Price_per_kWatt,
            cp.Connector_type,
            cp.Rating,
            cp.Charger_image_url,
            l.Latitude, 
            l.Longitude,
            a.Available_status AS Availability_status
        FROM Charger_point cp
        JOIN Location l ON cp.Location_Location_ID = l.Location_ID
        JOIN Available_status a ON cp.Available_status_Available_ID = a.Available_ID
        WHERE 1=1
    ";

        $params = [];

        if (isset($filters['max_price'])) {
            $sql .= " AND cp.Price_per_kWatt <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }

        if (!empty($filters['availability'])) {
            $sql .= " AND a.Available_status = :availability";
            $params[':availability'] = $filters['availability'];
        }

        if (isset($limit) && isset($offset)) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $statement = $this->_dbHandle->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }

        if (isset($limit) && isset($offset)) {
            $statement->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $statement->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getFilteredChargerCount($filters) {
        $sql = "
        SELECT COUNT(*) as total
        FROM Charger_point cp
        JOIN Available_status a ON cp.Available_status_Available_ID = a.Available_ID
        WHERE 1=1
    ";

        $params = [];

        if (isset($filters['max_price'])) {
            $sql .= " AND cp.Price_per_kWatt <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }

        if (!empty($filters['availability'])) {
            $sql .= " AND a.Available_status = :availability";
            $params[':availability'] = $filters['availability'];
        }

        $statement = $this->_dbHandle->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }

        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC)['total'];
    }

}
