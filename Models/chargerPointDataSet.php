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

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            // Update field names to match the data class column names
            $dataSet[] = new chargerPointData([
                'charger_point_id' => $row['charger_point_id'],
                'Name' => $row['Name'],
                'charger_point_description' => $row['charger_point_description'],
                'price_per_kwatt' => $row['price_per_kwatt'],
                'connector_type' => $row['connector_type'],
                'charger_image_url' => $row['charger_image_url'],
                'available_status_id' => $row['available_status_id'],
                'user_id' => $row['user_id'],
                'location_id' => $row['location_id']
            ]);
        }

        return $dataSet;
    }

    public function fetchChargerPointById($id)
    {
        $sqlQuery = 'SELECT * FROM Charger_point WHERE charger_point_id = :id';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(':id', $id);
        $statement->execute();

        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            // Update field names to match the data class column names
            return new chargerPointData([
                'charger_point_id' => $row['charger_point_id'],
                'Name' => $row['Name'],
                'charger_point_description' => $row['charger_point_description'],
                'price_per_kwatt' => $row['price_per_kwatt'],
                'connector_type' => $row['connector_type'],
                'charger_image_url' => $row['charger_image_url'],
                'available_status_id' => $row['available_status_id'],
                'user_id' => $row['user_id'],
                'location_id' => $row['location_id']
            ]);
        }

        return null;
    }

    public function fetchChargerPointsByUserId($chargerId)
    {
        $sqlQuery = '
        SELECT 
            cp.charger_point_id,
            cp.Name,
            cp.charger_point_description,
            cp.price_per_kwatt,
            cp.connector_type,
            cp.charger_image_url,
            cp.available_status_id,
            cp.user_id,
            cp.location_id,
            l.Latitude,
            l.Longitude
        FROM Charger_point cp
        JOIN Location l ON cp.location_id = l.location_id
        WHERE cp.charger_point_id = :chargerId
    ';

        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(':chargerId', $chargerId, PDO::PARAM_INT); // ✅ Corrected here
        $statement->execute();

        $dataSet = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            // You can return full array instead of objects if you need coordinates
            $dataSet[] = $row;

            // OR, if you're still using chargerPointData:
            // You can store coords separately in the result, or skip this.
            // $dataSet[] = new chargerPointData($row);
        }

        return $dataSet;
    }


    public function fetchAllDetailedChargerPoints($maxPrice = null, $availability = null)
    {
        $sql = "
    SELECT 
        cp.charger_point_id AS charger_point_id,
        cp.Name AS Name,
        cp.charger_point_description AS charger_point_description,
        cp.price_per_kwatt AS price_per_kwatt,
        cp.connector_type AS connector_type,
        cp.charger_image_url AS charger_image_url,
        cp.available_status_id AS available_status_id,
        cp.user_id AS user_id,
        cp.location_id AS location_id,
        l.Latitude AS Latitude,
        l.Longitude AS Longitude,
        a.Available_status AS Available_status
    FROM Charger_point cp
    JOIN Location l ON cp.location_id = l.location_id
    JOIN Available_status a ON cp.available_status_id = a.available_id
    WHERE 1=1
    ";

        $params = [];

        if ($maxPrice !== null && is_numeric($maxPrice)) {
            $sql .= " AND cp.price_per_kwatt <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        if (!empty($availability)) {
            $sql .= " AND a.Available_status = :availability";
            $params[':availability'] = $availability;
        }

        $statement = $this->_dbHandle->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function fetchDetailedChargerPointsPaginated($limit, $offset, $maxPrice = null, $availability = null)
    {
        $sql = "
        SELECT 
            cp.charger_point_id AS charger_point_id,
            cp.Name AS Name,
            cp.charger_point_description AS charger_point_description,
            cp.price_per_kwatt AS price_per_kwatt,
            cp.connector_type AS connector_type,
            cp.charger_image_url AS charger_image_url,
            cp.available_status_id AS available_status_id,
            cp.user_id AS user_id,
            cp.location_id AS location_id,
            l.Latitude AS Latitude,
            l.Longitude AS Longitude,
            a.Available_status AS Available_status
        FROM Charger_point cp
        JOIN Location l ON cp.location_id = l.location_id
        JOIN Available_status a ON cp.available_status_id = a.available_id
        WHERE 1=1
    ";

        $params = [];

        // Apply price filter if set
        if ($maxPrice !== null) {
            $sql .= " AND cp.price_per_kwatt <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        // Apply availability filter if set and not empty string
        if (!empty($availability)) {
            $sql .= " AND a.Available_status = :availability";
            $params[':availability'] = $availability;
        }

        // Add pagination
        $sql .= " LIMIT :limit OFFSET :offset";

        $statement = $this->_dbHandle->prepare($sql);

        // Bind filters first
        if (isset($params[':maxPrice'])) {
            $statement->bindValue(':maxPrice', $params[':maxPrice']);
        }
        if (isset($params[':availability'])) {
            $statement->bindValue(':availability', $params[':availability']);
        }

        // Bind limit and offset as integers
        $statement->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getTotalChargerCount($maxPrice = null, $availability = null)
    {
        $sql = "
        SELECT COUNT(*) as count
        FROM Charger_point cp
        JOIN Available_status a ON cp.available_status_id = a.available_id
        WHERE 1=1
    ";

        $params = [];

        if ($maxPrice !== null) {
            $sql .= " AND cp.price_per_kwatt <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        if (!empty($availability)) {
            $sql .= " AND a.Available_status = :availability";
            $params[':availability'] = $availability;
        }

        $statement = $this->_dbHandle->prepare($sql);

        if (isset($params[':maxPrice'])) {
            $statement->bindValue(':maxPrice', $params[':maxPrice']);
        }
        if (isset($params[':availability'])) {
            $statement->bindValue(':availability', $params[':availability']);
        }

        $statement->execute();

        return $statement->fetchColumn();
    }

    public function updateChargerPointByValues($id, $name, $desc, $price, $connector, $image, $status)
    {
        $sql = "UPDATE Charger_point 
            SET Name = :name, 
                charger_point_description = :description, 
                price_per_kwatt = :price, 
                connector_type = :connector, 
                charger_image_url = :image, 
                available_status_id = :status 
            WHERE charger_point_id = :id";

        $statement = $this->_dbHandle->prepare($sql);

        $statement->bindValue(':name', $name);
        $statement->bindValue(':description', $desc);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':connector', $connector);
        $statement->bindValue(':image', $image);
        $statement->bindValue(':status', $status);
        $statement->bindValue(':id', $id);

        return $statement->execute();
    }

    public function fetchDetailedChargerPointsPaginatedAdminView($limit, $offset)
    {
        $sql = "
        SELECT 
            cp.charger_point_id,
            cp.Name,
            cp.charger_point_description,
            cp.price_per_kwatt,
            cp.connector_type,
            cp.charger_image_url,
            cp.available_status_id,
            cp.user_id,
            cp.location_id,
            l.Latitude,
            l.Longitude,
            a.Available_status
        FROM Charger_point cp
        JOIN Location l ON cp.location_id = l.location_id
        JOIN Available_status a ON cp.available_status_id = a.available_id
        LIMIT :limit OFFSET :offset
    ";

        $statement = $this->_dbHandle->prepare($sql);
        $statement->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $statement->execute();

        $dataSet = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $dataSet[] = new chargerPointData($row);
        }

        return $dataSet;
    }


}

