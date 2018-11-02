<?php
function getConnection(){

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

$servername = "localhost";
$dbusername = "u144372704_yrew";
$password = "phpUser123#";
$dbname = "u144372704_yrew";

// return new mysqli connection
return new mysqli($servername, $dbusername, $password, $dbname);

}
?>
