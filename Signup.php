<?php

require ("Models/userDataSet.php");

$view = new stdClass();
$view->pageTitle = "Sign Up";
$view->styles = "css/LoginRegCSS.css";
session_start();

$user = new userDataSet();

if (isset($_SESSION["userID"]) && isset($_SESSION["user_status"]) && $_SESSION["user_status"] === "Approve")
{
    header("Location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['phno']))
{

    // Validate reCAPTCHA (This will be uncommented once we deploy in the server)
//    $captcha = $_POST['g-recaptcha-response'] ?? '';
//    $secretKey = "6LdJYD4rAAAAAIXJRUxroyKd0V0rB0FBQfxJAU_W";
//    $ip = $_SERVER['REMOTE_ADDR'];
//
//    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha&remoteip=$ip");
//    $responseData = json_decode($verifyResponse);
//
//    if (!$responseData->success) {
//        $view->loginError = "reCAPTCHA failed. Please verify you're not a robot.";
//        require_once('Views/Signup.phtml');
//        exit;
//    }

    // Proceed with signup logic
    $fname = htmlspecialchars($_POST['fname']);
    $lname = htmlspecialchars($_POST['lname']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $phno = htmlspecialchars($_POST['phno']);

    $regDate = date("Y-m-d");
    $regUser = $user->addUserData($fname, $lname, $email, $password, $phno, $regDate);
    $view->successReg = "Registered Successfully!";

}


require_once('Views/Signup.phtml');