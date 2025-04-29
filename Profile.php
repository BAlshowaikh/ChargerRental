<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

require ("Models/Database.php");
require ("Models/UserDataSet.php");

$view = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $address = array_map("trim", explode(",", $_POST["address"]));
    $road = isset($address[0]) ? trim($address[0]) : '';
    if (isset($address[1])) {
        $road .= ", " . trim($address[1]);
    }
    $city = isset($address[2]) ? trim($address[2]) : '';

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