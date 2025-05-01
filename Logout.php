<?php

session_start();

unset($_SESSION['username']);
unset($_SESSION['fullname']);
unset($_SESSION['userID']);
unset($_SESSION['role']);

header("Location: Login.php");