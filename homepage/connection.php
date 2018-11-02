<?php
function getConnection(){

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

$servername = "localhost";
$dbusername = "id7650771_phpuser";
$password = "phpUser123#";
$dbname = "id7650771_schulmanager";

// return new mysqli connection
return new mysqli($servername, $dbusername, $password, $dbname);

}
?>
