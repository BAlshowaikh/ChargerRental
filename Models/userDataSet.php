<?php

require ("Models/Database.php");
require ("Models/userData.php");

class userDataSet
{
    protected $_dbInstance, $_dbHandle;

    public function __construct()
    {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getConnection();
    }

    public function addUserData($userID, $fname, $lname, $username, $password, $phoneNo, $regDate, $approved, $userRoleID)
    {
        if (strpos($username, "@admin.com") !== false)
        {
            $userRoleID = "1";
        }
        elseif (strpos($username, "@home.com") !== false)
        {
            $userRoleID = "2";
        }
        else
        {
            $userRoleID = "3";
        }

        //Hash password
        $password = password_hash($password, PASSWORD_DEFAULT);

        $sqlQuery = "INSERT INTO User (user_ID, f_Name, l_Name, username, password, Phone_no, Registration_date, Approved, User_role_User_role_ID) 
                     VALUES (:userID, :fname, :lname, :username, :password, :phoneNo, :regDate, :approved, :userRoleID)";

        $stmt = $this->_dbHandle->prepare($sqlQuery);

        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':phoneNo', $phoneNo);
        $stmt->bindParam(':regDate', $regDate);
        $stmt->bindParam(':approved', $approved);
        $stmt->bindParam(':userRoleID', $userRoleID);

        $stmt->execute();
    }

    public function updateUserInfo($userID, $fname, $lname, $username, $phoneNo)
    {
        $sql = "UPDATE User 
                SET f_Name = :fname,
                    l_Name = :lname,
                    username = :username,
                    Phone_no = :phoneNo,
                WHERE user_ID = :userID";

        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':phoneNo', $phoneNo);
        $stmt->bindParam(':userID', $userID);

        return $stmt->execute();
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