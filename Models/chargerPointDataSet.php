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
}
