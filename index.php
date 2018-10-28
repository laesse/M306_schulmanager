<?php

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

switch($_GET['status'])
{
	case 'jumpToUser':
       	jumpToUser();   
	break; 
	case 'jumpToNote':
       	jumpToNote();   
	break;
	case 'jumpToTimetable':
       	jumpToTimetable();   
	break; 
	case 'logout':
       	logout();   
	break; 
	default:
		showHome();
	break;
}



function showHome() {
	
	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>Home</title>
		<link href='index.css' rel='stylesheet'>
	</head>
	<body>
	";
	
	if (isset($_SESSION['user'])) {
		echo "
		<form action='?status=jumpToNote' method='post'>

		<div class='container'>
		<button type='submit' class='btnNote'>Notizä</button>
		</div>

		</form>
		
		<form action='?status=jumpToTimetable' method='post'>

		<div class='container'>
		<button type='submit' class='btnTimetable'>Timetable</button>
		</div>

		</form>
		
		<form action='?status=logout' method='post'>

		<div class='container'>
		<button type='submit' class='btnLogout'>Abmälde</button>
		</div>

		</form>
		";
	} else {
   		echo "
		<form action='?status=jumpToUser' method='post'>

		<div class='container'>
		<button type='submit' class='btnUser'>Amälde</button>
		</div>

		</form>
		";
	}
		
    echo "		
	</body>
	</html>
	";
	
}



function jumpToUser() {
	
	header('Location: user.php'); 
	
}

function jumpToNote() {
	
	header('Location: note.php'); 
	
}

function jumpToTimetable() {
	
	header('Location: timetable.php'); 
	
}

function logout() {
	
	unset($_SESSION['user']);
	session_destroy();
	showHome();
	
}

?>