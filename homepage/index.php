<?php

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

// choose action
switch(@$_GET['status'])
{
	case 'logout':
       	logout();
	break;
	default:
		showHome();
	break;
}

// home
function showHome() {

	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>LeeSchoolassist: Home</title>
		<meta name='theme-color' content='#f1605b'>
		<link rel='stylesheet' type='text/css' href='index.css'>
		<link rel='shortcut icon' href='favicon.png' type='image/x-icon'/>	
		<meta name='viewport' content='width=device-width, initial-scale=1.0' />
	</head>
	<body>
	
		<section class='sectionHome'>
			
			<div class='divLanding'>
				";				
				if (!isset($_SESSION['user'])) {
	
					echo "
					<h1>WELCOME</h1><br>
					<h1>I AM LEE.</h1><br>
					<form action='user.php' method='post'>
						<button type='submit' class='btnLogin'>Login</button>
					</form>
					<form action='user.php?status=showRegistration' method='post'>
						<button type='submit' class='btnRegister'>Register</button>
					</form>		
					<br>
					<img src='img/down.svg'>
			</div>			
					";
	
				} else {
					echo "
					<h1>Hi</h1><br>
					<h1>".$_SESSION['username']."</h1><br>
					<p>How are you?</p>
					<br>
			</div>	
					
					<div class='divNavigation'>
						<a href='note.php'><img src='img/note.svg'></a>
						<a href='timetable.php'><img src='img/timetable.svg'></a>
						<a href='mark.php'><img src='img/mark.svg'></a>
						<a href='index.php?status=logout'><img src='img/logout.svg'></a>
					</div>				
					";
				}
	
				echo "
		
		</section>
		";
	
		if (!isset($_SESSION['user'])) {
	
		echo "
		<section class='sectionContent'>
			
			<div class='divTheme'>
				<h2>NOTE</h2>
				<p>Save all your thougths.</p>
			</div>
			<div class='divTheme'>
				<h2>TIMETABLE</h2>
				<p>Never forget a lesson again.</p>
			</div>
			<div class='divTheme'>
				<h2>MARK</h2>
				<p>Manage every result.</p>
			</div>

		</section>		
		";
	
		}
		
		echo "	
		</body>
	</html>
	";
	
}

// logout
function logout() {
	unset($_SESSION['user']);
	session_destroy();
	showHome();
}

?>