<?php

require ("Models/userDataSet.php");

$view = new stdClass();			
$view->pageTitle = 'Login';
$view->styles = '/css/LoginRegCSS.css';

session_start();

$user = new userDataSet();

if (isset($_SESSION["userID"]) && isset($_SESSION["user_status"]) && $_SESSION["user_status"] === "Approve")
{
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"]) && isset($_POST["password"]))
{
    $email = $_POST["email"];
    $password = $_POST["password"];
    $userData = $user->userExists($email, $password);

    if ($userData) {
        // Check approval status
        if ($userData["user_status"] !== "Approve") {
            $view->loginError = "Your account is not approved yet. Please wait for admin approval.";
        } else {
            $_SESSION["userID"] = $userData["user_id"];
            $_SESSION["role"] = $userData["user_role_id"];
            $_SESSION["user_status"] = $userData["user_status"];

            $_SESSION["fullname"] = $userData["first_name"] . " " . $userData["last_name"];
            $_SESSION["username"] = $userData["username"];

            if ($userData["user_role_id"] == 1 || $userData["user_role_id"] == 2) {
                header("Location: index.php");
                exit();
            } else if ($userData["user_role_id"] == 3) {
                header("Location: index.php");
                exit();
            }
        }
    }
    else
    {
        $view->loginError = "Invalid username or password";
    }


}


require_once('Views/Login.phtml');