<?php

require("Models/userDataSet.php");

$view = new stdClass();
$view->pageTitle = 'Login';
$view->styles = 'css/LoginRegCSS.css';

session_start();

$user = new userDataSet();

if (isset($_SESSION["userID"]) && isset($_SESSION["user_status"]) && $_SESSION["user_status"] === "Approve") {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"]) && isset($_POST["password"])) {

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
//        require_once('Views/Login.phtml');
//        exit;
//    }

    // Proceed with login logic
    $email = $_POST["email"];
    $password = $_POST["password"];
    $userData = $user->userExists($email, $password);

    if ($userData) {
        if ($userData["user_status"] !== "Approve") {
            if($userData["user_status"] === "Reject") {
                $view->loginError = "Your account is rejected.";
            }
            else {
                $view->loginError = "Your account is not approved yet. Please wait for admin approval.";
            }
        }
        else {
            $_SESSION["userID"] = $userData["user_id"];
            $_SESSION["role"] = $userData["user_role_id"];
            $_SESSION["user_status"] = $userData["user_status"];
            $_SESSION["fullname"] = $userData["first_name"] . " " . $userData["last_name"];
            $_SESSION["username"] = $userData["username"];

            header("Location: index.php?login=1");
            exit();
        }
    } else {
        $view->loginError = "Invalid username or password";
    }
}

require_once('Views/Login.phtml');
