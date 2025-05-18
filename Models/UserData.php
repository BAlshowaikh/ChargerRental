<?php

class userData
{
    private $userId, $firstName, $lastName, $username, $password, $phoneNumber, $registrationTimestamp, $userStatus, $userRoleId, $userRoleName;

    public function __construct(array $dbRow)
    {
        $this->userId = $dbRow['user_id'];
        $this->firstName = $dbRow['first_name'];
        $this->lastName = $dbRow['last_name'];
        $this->username = $dbRow['username'];
        $this->password = $dbRow['password'];
        $this->phoneNumber = $dbRow['phone_number'];
        $this->registrationTimestamp = $dbRow['registration_timestamp'];
        $this->userStatus = $dbRow['user_status'];
        $this->userRoleId = $dbRow['user_role_id'];
        $this->userRoleName = $dbRow['user_role'];
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function getRegistrationTimestamp()
    {
        return $this->registrationTimestamp;
    }

    public function getUserStatus()
    {
        return $this->userStatus;
    }

    public function getUserRoleId()
    {
        return $this->userRoleId;
    }
    public function getUserRoleName()
    {
        return $this->userRoleName;
    }
}
