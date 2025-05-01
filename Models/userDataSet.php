<?php

require ("Models/Database.php");
require ("Models/userData.php");

class userDataSet
{
    protected $_dbInstance, $_dbHandle;

    public function __construct()
    {
        $this->_dbInstance = \Models\Database::getInstance();
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
}