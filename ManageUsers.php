<?php
session_start();
$view = new stdClass();
require_once("Models/userDataSet.php");


//Check if user is logged in
//$userId = $_SESSION['userID'] ?? null;
//
//if (!$userId) {
//    header("Location: login.php");
//    exit;
//}

$userId=1;
$userDataSet = new userDataSet();

// Pagination settings
$usersPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $usersPerPage;

// Handle search functionality
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedRole = isset($_GET['role']) ? $_GET['role'] : '';
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Fetch user roles for dropdown
$view->roles = $userDataSet->getUserRoles(); // Method to fetch roles
$view->users = $userDataSet->searchUsers($searchTerm, $selectedRole, $selectedStatus, $offset, $usersPerPage);
$totalUsers = $userDataSet->getTotalUsers($searchTerm, $selectedRole, $selectedStatus); // Total users for pagination
$totalPages = ceil($totalUsers / $usersPerPage);

// Assign these variables to the view
$view->currentPage = $currentPage;
$view->totalPages = $totalPages;
$view->searchTerm = $searchTerm;
$view->selectedRole = $selectedRole;
$view->selectedStatus = $selectedStatus; // Pass selected status to the view

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $userId = $_POST['userId'];

    switch ($action) {
        case 'approve':
            $userDataSet->approveUser($userId);
            break;
        case 'reject':
            $userDataSet->rejectUser($userId);
            break;
        case 'suspend':
            $userDataSet->suspendUser($userId);
            break;
        case 'unsuspend':
            $userDataSet->unsuspendUser($userId);
            break;
        case 'delete':
            $userDataSet->deleteUser($userId);
            break;
    }

    header("Location: ManageUsers.php?search=" . urlencode($searchTerm) . "&role=" . urlencode($selectedRole) . "&status=" . urlencode($selectedStatus));
    exit();
}

// Include the view file
require_once("Views/ManageUsers.phtml");