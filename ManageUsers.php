<?php
session_start();
$view = new stdClass();
require_once("Models/userDataSet.php");

$userDataSet = new userDataSet();
$view->users = $userDataSet->getAllUsers(); // Fetch all users from the database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $userId = $_POST['userId'];

    switch ($action) {
        case 'approve':
            $userDataSet->approveUser($userId);
            break;
        case 'suspend':
            $userDataSet->suspendUser($userId);
            break;
        case 'delete':
            $userDataSet->deleteUser($userId);
            break;
    }

    // Redirect to the same page
    header("Location: ManageUsers.php");
    exit();
}

require_once("Views/ManageUsers.phtml");