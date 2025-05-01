<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */


require ("Models/UserDataSet.php");

$view = new stdClass();
session_start();
/*session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /*$addressRaw = $_POST["address"];
    $addressParts = array_map("trim", explode(",", $addressRaw));
    $addressParts = array_filter($addressParts); // Removes empty values

    $road = isset($addressParts[0]) ? $addressParts[0] : '';
    $road .= isset($addressParts[1]) ? ', ' . $addressParts[1] : '';
    $city = isset($addressParts[2]) ? $addressParts[2] : '';
    $country = isset($addressParts[3]) ? $addressParts[3] : '';
    */
   /* $view->fullname = $_SESSION["fullname"];
    $view->username = $_SESSION["username"];

    $userID = $_SESSION["userID"];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['email'];
    $phoneNo = $_POST['phno'];

    $db = new UserdataSet();
    $db->updateUserInfo($userID, $fname, $lname, $username, $phoneNo);
    //$db->updateLocation($road, $city);

    header("Location: Profile.php?userID=$userID&updated=true");
}*/

$userID = isset($_SESSION["userID"]) ? $_SESSION["userID"] : null;

if ($userID) {
    $db = new userDataSet();

    // If form submitted, update info
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $phone = $_POST['phno'];

        $db->updateUserInfo($userID, $fname, $lname, $email, $phone);

        // Update session
        $_SESSION["fullname"] = $fname . ' ' . $lname;
        $_SESSION["username"] = $email;

        header("Location: Profile.php?updated=true");
        exit();
    }

    // Load user data from DB
    $user = $db->getUserByID($userID);

    if ($user) {
        $view->fullname = $user['f_Name'] . ' ' . $user['l_Name'];
        $view->f_Name = $user['f_Name'];
        $view->l_Name = $user['l_Name'];
        $view->username = $user['username'];
        $view->phone = $user['Phone_no'];
    }
}

require_once("Views/Profile.phtml");