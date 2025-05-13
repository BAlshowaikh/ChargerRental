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
        if (strpos($username, "@admin.com") !== false)
        {
            $userRoleID = 1;
        }
        elseif (strpos($username, "@home.com") !== false)
        {
            $userRoleID = 2;
        }
        else
        {
            $userRoleID = 3;
        }

        //Hash password
        $password = password_hash($password, PASSWORD_DEFAULT);
        $approved = "no";


        /*$locationQuery = "INSERT INTO Location (City, Road, Country) VALUES (:city, :road, :country)";

        $statement = $this->_dbHandle->prepare($locationQuery);
        $statement->bindParam(":road", $road);
        $statement->bindParam(':city', $city);
        $statement->bindParam(':country', $country);
        $statement->execute();

        $locationID = $this->_dbHandle->lastInsertId();*/


        $sqlQuery = "INSERT INTO User (f_Name, l_Name, username, password, Phone_no, Registration_date, Approved, User_role_User_role_ID) 
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
        //$stmt->bindParam(':locationID', $locationID, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function userExists($username, $password)
    {
        $sql = "SELECT * FROM User WHERE username = :username";
        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUserInfo($userID, $fname, $lname, $username, $phoneNo)
    {
        $sql = "UPDATE User 
                SET f_Name = :fname,
                    l_Name = :lname,
                    username = :username,
                    Phone_no = :phoneNo
                WHERE user_ID = :userID";

        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':phoneNo', $phoneNo);
        $stmt->bindParam(':userID', $userID);

        return $stmt->execute();
    }

    public function getUserByID($userID) {
        $sql = "SELECT * FROM User WHERE user_ID = :userID";

        $statement = $this->_dbHandle->prepare($sql); // ✅ Fixed typo here
        $statement->bindParam(':userID', $userID, PDO::PARAM_INT); // ✅ Bind the parameter
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC); // ✅ Return as associative array
    }

    public function updateLocation($road, $city, $locationID)
    {
        $sql = "UPDATE Location 
                SET city = :city, road = :road
                WHERE location_ID = :locationID";

        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':road', $road);
        $stmt->bindParam(':locationID', $locationID);
        return $stmt->execute();
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