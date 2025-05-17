<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */


require ("Models/UserDataSet.php");

$view = new stdClass();
$view->styles = '/css/ProfileCSS.css';
session_start();


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
        $view->fullname = $user['first_name'] . ' ' . $user['last_name'];
        $view->f_Name = $user['first_name'];
        $view->l_Name = $user['last_name'];
        $view->username = $user['username'];
        $view->phone = $user['phone_number'];
    }
}

require_once("Views/Profile.phtml");