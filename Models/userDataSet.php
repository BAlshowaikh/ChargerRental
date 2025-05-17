<?php

require ("Models/Database.php");
require ("Models/userData.php");

class userDataSet
{
    protected $_dbInstance, $_dbHandle;

    public function __construct()
    {
        $this->_dbInstance = \Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getConnection();
    }

    public function addUserData($fname, $lname, $username, $password, $phoneNo, $regDate)//, $road, $city, $country)
    {
        if (strpos($username, "@homeowner.com") !== false)
        {
            $userRoleID = 2;
        }
        elseif (strpos($username, "@customer.com") !== false)
        {
            $userRoleID = 3;
        }

        //Hash password
        $password = password_hash($password, PASSWORD_DEFAULT);
        $approved = "Pending";


        $sqlQuery = "INSERT INTO User (first_name, last_name, username, password, phone_number, registration_timestamp, user_status, user_role_id) 
                     VALUES (:fname, :lname, :username, :password, :phoneNo, :regDate, :approved, :userRoleID)";

        $stmt = $this->_dbHandle->prepare($sqlQuery);

        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':phoneNo', $phoneNo);
        $stmt->bindParam(':regDate', $regDate);
        $stmt->bindParam(':approved', $approved);
        $stmt->bindParam(':userRoleID', $userRoleID, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function userExists($username, $password)
    {
        $sql = "SELECT * FROM User WHERE username = :username";
        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function updateUserInfo($userID, $fname, $lname, $username, $phoneNo)
    {
        $sql = "UPDATE User 
                SET first_name = :fname,
                    last_name = :lname,
                    username = :username,
                    phone_number = :phoneNo
                WHERE user_id = :userID";

        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':phoneNo', $phoneNo);
        $stmt->bindParam(':userID', $userID);

        return $stmt->execute();
    }

    public function getUserByID($userID) {
        $sql = "SELECT * FROM User WHERE user_id = :userID";

        $statement = $this->_dbHandle->prepare($sql); // ✅ Fixed typo here
        $statement->bindParam(':userID', $userID, PDO::PARAM_INT); // ✅ Bind the parameter
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC); // ✅ Return as associative array
    }


    public function registerHomeOwner(
        $firstName,
        $lastName,
        $username,
        $plainPassword,
        $phoneNo,
        $regDate,
        $userStatus,
        $chargeName,
        $chargePointImage,       // blank on call
        $chargePointDescription,
        $pricePerKW,
        $availability,
        $connectorType,
        $road,
        $city,
        $homeNumber,
        $zipCode,
        $latitude,
        $longitude
    ): int {
        if (strpos($username, '@admin.com') !== false) {
            throw new \InvalidArgumentException('Admin users cannot self-register.');
        } elseif (strpos($username, '@homeowner.com') !== false) {
            $userRoleId = 2;
            $userStatus = 'Pending';
        } else {
            $userRoleId = 3;
            $userStatus = 'Pending';
        }
        $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);


        // Insert Location
        $sqlLocation = "
        INSERT INTO Location (Road, City, Home_number, ZIP_Code, Latitude, Longitude)
        VALUES (:road, :city, :home_number, :zip_code, :latitude, :longitude)
    ";
        $stmtLocation = $this->_dbHandle->prepare($sqlLocation);
        $stmtLocation->bindParam(':road', $road);
        $stmtLocation->bindParam(':city', $city);
        $stmtLocation->bindParam(':home_number', $homeNumber);
        $stmtLocation->bindParam(':zip_code', $zipCode);
        $stmtLocation->bindParam(':latitude', $latitude);
        $stmtLocation->bindParam(':longitude', $longitude);
        $stmtLocation->execute();
        $locationId = (int)$this->_dbHandle->lastInsertId();

        // User
        $sqlU = "
INSERT INTO User
  (first_name, last_name, username, password,
   phone_number, registration_timestamp,
   user_status, user_role_id)
VALUES
  (:firstName,    :lastName,    :username,  :hashed,
   :phoneNo,    :regDate,  :userStatus, :userRoleId)
";
        $stmt = $this->_dbHandle->prepare($sqlU);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':hashed', $hashed);
        $stmt->bindParam(':phoneNo', $phoneNo);
        $stmt->bindParam(':regDate', $regDate);
        $stmt->bindParam(':userStatus', $userStatus);
        $stmt->bindParam(':userRoleId', $userRoleId);
        $stmt->execute();

        $newUserId = (int)$this->_dbHandle->lastInsertId();

        // Charger_point (blank image_url)
        $sqlCharger = "
        INSERT INTO Charger_point (
            Name,
            charger_image_url,
            charger_point_description,
            price_per_kwatt,
            available_status_id,
            connector_type,
            user_id,
            location_id
        ) VALUES (
            :chargeName,
            :chargePointImage,
            :chargePointDescription,
            :pricePerKW,
            :availability,
            :connectorType,
            :userID,
            :locationID
        )";

        $stmt2 = $this->_dbHandle->prepare($sqlCharger);
        $stmt2->bindValue(':chargeName', $chargeName);
        $stmt2->bindValue(':chargePointImage', $chargePointImage); // e.g. empty string
        $stmt2->bindParam(':chargePointDescription', $chargePointDescription);
        $stmt2->bindParam(':pricePerKW', $pricePerKW);
        $stmt2->bindParam(':availability', $availability);
        $stmt2->bindParam(':connectorType', $connectorType);
        $stmt2->bindParam(':userID', $newUserId);
        $stmt2->bindParam(':locationID', $locationId);
        $stmt2->execute();

        // Return charger_point_id for image upload and update
        return (int)$this->_dbHandle->lastInsertId();
    }

    public function updateChargerImage(int $chargerId, string $imageName): void
    {
        $stmt = $this->_dbHandle->prepare(
            "UPDATE Charger_point
           SET charger_image_url = :img
         WHERE charger_point_id = :id"
        );
        $stmt->execute([
            ':img' => $imageName,
            ':id'  => $chargerId,
        ]);
    }

    public function getAllUsers($offset, $limit) {
        $sql = "SELECT u.*, ur.user_role 
            FROM User u 
            JOIN User_role ur ON u.user_role_id = ur.User_role_ID
            LIMIT :offset, :limit";
        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new userData($row);
        }

        return $users;
    }

    public function getTotalUsers($searchTerm, $selectedRole, $selectedStatus) {
        $query = "SELECT COUNT(*) FROM User WHERE (first_name LIKE :search OR last_name LIKE :search OR user_id = :userId)";

        if ($selectedRole) {
            $query .= " AND user_role_id = :role"; // Adjust role filtering
        }

        if ($selectedStatus) {
            $query .= " AND user_status = :status"; // Filter by user status
        }

        $stmt = $this->_dbHandle->prepare($query);
        $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
        $stmt->bindValue(':userId', $searchTerm, PDO::PARAM_INT);

        if ($selectedRole) {
            $stmt->bindValue(':role', $selectedRole, PDO::PARAM_INT);
        }

        if ($selectedStatus) {
            $stmt->bindValue(':status', $selectedStatus, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function approveUser($userId) {
        $sql = "UPDATE User SET user_status = 'Approve' WHERE user_id = :userId";
        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function rejectUser($userId) {
        $sql = "UPDATE User SET user_status = 'Reject' WHERE user_id = :userId";
        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function suspendUser($userId) {
        $sql = "UPDATE User SET user_status = 'Suspend' WHERE user_id = :userId";
        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function unsuspendUser($userId) {
        $sql = "UPDATE User SET user_status = 'Approve' WHERE user_id = :userId";
        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteUser($userId) {
        $sql = "DELETE FROM User WHERE user_id = :userId";
        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function searchUsers($searchTerm, $selectedRole, $selectedStatus, $offset, $limit) {
        $query = "SELECT u.*, ur.user_role 
              FROM User u 
              JOIN User_role ur ON u.user_role_id = ur.User_role_ID 
              WHERE (first_name LIKE :search OR last_name LIKE :search OR u.user_id = :userId)";

        if ($selectedRole) {
            $query .= " AND u.user_role_id = :role"; // Adjust role filtering
        }

        if ($selectedStatus) {
            $query .= " AND u.user_status = :status"; // Filter by user status
        }

        $query .= " LIMIT :offset, :limit";
        $stmt = $this->_dbHandle->prepare($query);
        $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
        $stmt->bindValue(':userId', $searchTerm, PDO::PARAM_INT);

        if ($selectedRole) {
            $stmt->bindValue(':role', $selectedRole, PDO::PARAM_INT);
        }

        if ($selectedStatus) {
            $stmt->bindValue(':status', $selectedStatus, PDO::PARAM_STR);
        }

        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new userData($row); // Create instances of userData
        }

        return $users;
    }

    public function getUserRoles() {
        $query = "SELECT * FROM User_role";
        $stmt = $this->_dbHandle->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}