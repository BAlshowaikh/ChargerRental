<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['userID'])) {
    header("Location: Login.php");
    exit();
}

$view = new stdClass();
$view->pageTitle = 'Homepage';

// Determine navbar based on role
switch ($_SESSION['role']) {
    case 1:
        $view->navbarPartial = 'AdminNavbar.phtml';
        break;
    case 2:
        $view->navbarPartial = 'HomeownerNavBar.phtml';
        break;
    case 3:
        $view->navbarPartial = 'CustomerNavBar.phtml';
        break;
    default:
        $view->navbarPartial = 'defaultNavbar.phtml';
}

require_once('Views/index.phtml');
