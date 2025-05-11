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
}
