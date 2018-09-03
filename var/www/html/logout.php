<?php


$_SESSION = array();
setcookie( session_name(), '', time() - 3600, '/' );
session_destroy();

header('location:./login.php');
