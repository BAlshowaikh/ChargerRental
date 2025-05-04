<?php

class userData
{
    private $userId, $fName, $lName, $username, $password, $phNo, $registrationDate, $approved, $userRoleId;

    public function __construct($dbRow)
    {
        $this->userId = $dbRow['user_ID'];
        $this->fName = $dbRow['f_Name'];
        $this->lName = $dbRow['l_Name'];
        $this->username = $dbRow['username'];
        $this->password = $dbRow['password'];
        $this->phNo = $dbRow['Phone_no'];
        $this->registrationDate = $dbRow['Registration_date'];

        if ($dbRow['approved'])
        {
            $this->approved = "Yes";
        }
        else
        {
            $this->approved = "No";
        }

        $this->userRoleId = $dbRow['user_role_User_role_ID'];
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getFName()
    {
        return $this->fName;
    }

    public function getLName()
    {
        return $this->lName;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getPhNo()
    {
        return $this->phNo;
    }

    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    public function getApproved()
    {
        return $this->approved;
    }

    public function getUserRoleId()
    {
        return $this->userRoleId;
    }
}