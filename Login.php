<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

require ("Models/userDataSet.php");

$view = new stdClass();			
$view->pageTitle = 'Login';

$user = new userDataSet();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"]) && isset($_POST["password"]))
{
    $email = $_POST["email"];
    $password = $_POST["password"];
    $userData = $user->userExists($email, $password);

    if ($userData)
    {
        session_start();
        $_SESSION["userID"] = $userData["user_ID"];
        $_SESSION["role"] = $userData["User_role_User_role_ID"];

        if ($userData["User_role_User_role_ID"] == 1 || $userData["User_role_User_role_ID"] == 2)
        {
            header("Location: ManageChargePoints.php");
            exit();
        }
        else if ($userData["User_role_User_role_ID"] == 3)
        {
            header("Location: BookChargePoints.php");
            exit();
        }
    }
    else
    {
        $view->errorMessage = "Invalid username or password";
    }
}

require_once('Views/Login.phtml');