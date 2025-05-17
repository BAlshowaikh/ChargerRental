<?php

$view = new stdClass();
$view->pageTitle = 'Homepage';
session_start();
require_once('Views/index.phtml');
