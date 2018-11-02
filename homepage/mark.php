<?php

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

switch($_GET['status'])
{
	case 'logout':
       	logout();
	break;
	default:
		showHome();
	break;
}

function logout() {

	unset($_SESSION['user']);
	session_destroy();
	header('Location: index.php');

}



 ?>
