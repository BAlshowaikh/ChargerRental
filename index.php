<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['userID'])) {
    header("Location: Login.php");
    exit();
}

$view = new stdClass();
$view->pageTitle = 'Homepage';

require_once('Views/index.phtml');
