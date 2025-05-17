<?php

require ("Models/userDataSet.php");

$view = new stdClass();
$view->pageTitle = "Sign Up";
$view->styles = "/css/LoginRegCSS.css";
session_start();

$user = new userDataSet();

if (isset($_SESSION["userID"]) && isset($_SESSION["user_status"]) && $_SESSION["user_status"] === "Approve")
{
    header("Location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['phno']))
{
    $_SESSION["user_status"] = $user["user_status"];
    /*$addressRaw = $_POST["address"];
    $addressParts = array_map("trim", explode(",", $addressRaw));
    $addressParts = array_filter($addressParts); // Removes empty values

    $road = isset($addressParts[0]) ? $addressParts[0] : '';
    $road .= isset($addressParts[1]) ? ', ' . $addressParts[1] : '';
    $city = isset($addressParts[2]) ? $addressParts[2] : '';
    $country = isset($addressParts[3]) ? $addressParts[3] : '';*/

    $fname = htmlspecialchars($_POST['fname']);
    $lname = htmlspecialchars($_POST['lname']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $phno = htmlspecialchars($_POST['phno']);

    /*$errors = [];

    if (strlen($fname) < 3 || !preg_match("/^[a-zA-Z\s]+$/", $fname)) {
        $errors['fname'] = "First name must be at least 3 characters and contain only letters.";
    }

    if (strlen($lname) < 3 || !preg_match("/^[a-zA-Z\s]+$/", $lname)) {
        $errors['lname'] = "Last name must be at least 3 characters and contain only letters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    if (!preg_match("/^\d{8,}$/", $phno)) {
        $errors['phno'] = "Phone number must be at least 8 digits.";
    }

    if (empty($road) || empty($city) || empty($country)) {
        $errors['address'] = "Incomplete address. Please provide road, city, and country.";
    }

    $view->errors = $errors;

    if (empty($errors))
    {*/
        $regDate = date("Y-m-d");
        $regUser = $user->addUserData($fname, $lname, $email, $password, $phno, $regDate);
        $view->successReg = "Registered Successfully!";
    //}
}


require_once('Views/Signup.phtml');