<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

require ("Models/Database.php");
require ("Models/UserDataSet.php");

$view = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $addressRaw = $_POST["address"];
    $addressParts = array_map("trim", explode(",", $addressRaw));
    $addressParts = array_filter($addressParts); // Removes empty values

    $road = isset($addressParts[0]) ? $addressParts[0] : '';
    $road .= isset($addressParts[1]) ? ', ' . $addressParts[1] : '';
    $city = isset($addressParts[2]) ? $addressParts[2] : '';
    $country = isset($addressParts[3]) ? $addressParts[3] : '';

    $userID = $_SESSION["userID"];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['email'];
    $phoneNo = $_POST['phno'];

    $db = new UserdataSet();
    $db->updateUserInfo($userID, $fname, $lname, $username, $phoneNo);
    //$db->updateLocation($road, $city);

    header("Location: Profile.php?userID=$userID&updated=true");
}

require_once("Views/Profile.phtml");