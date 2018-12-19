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

function showHome() {

	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>LeeSchoolassist: Home</title>
		<link rel='stylesheet' type='text/css' href='index.css'>
		<link rel='shortcut icon' href='favicon.png' type='image/x-icon'/>	
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	</head>
	<body>
	
		<div class='header'>
		
			<div class='menuIcon'>
				<img src='img/menu.svg'>
			</div>
			<p>Lee Schoolassist</p>
		
		</div>
		
		<div class='content'>
		
		
			<div class='contentRow'>
			
				<div class='rowContent' id='left'>
					<h1>I am Lee</h1>
					<p>Welcome to my website.<br>
					I would like to manage your school stuffe with you.<br>
					Are you interessted to try it out?</p>
	
					";				
					if (!isset($_SESSION['user'])) {
	
						echo "
						<form action='user.php' method='post'>
							<button type='submit' id='mainButton'>Login</button>
						</form>
						<form action='user.php?status=showRegistration' method='post'>
							<button type='submit' id='registerButton'>Register</button>
						</form>				
						";
	
					} else {
						
						echo "
						<form action='index.php?status=logout' method='post'>
							<button type='submit' id='mainButton'>Logout</button>
						</form>
						";
							
					}
					echo "
	
					</p>
				</div>
				<div class='rowPicture' id='right'>
					<br>
				</div>
			
			</div>
			<div class ='contentRow1'>
				
				<div class='rowPicture' id='left'>
					<br>
				</div>
				<div class='rowContent' id='right'>
				
					<h1>What can I do?</h1>
					<p>At the moment I can help you with the following themes.<br>
					Is there something for you?</p>
					
					<div class='themes'>
						<div class='theme'>
						";
						
						if (!isset($_SESSION['user'])) {
							
							echo "
							<h2>Timetable </h2>		
							<p>Never forgett a lesson again.</p>
							";
							
						} else {
							
							echo "
							<form action='timetable.php' method='post'>
								<button type='submit' id='mainButton'>Timetable</button>
							</form>
							";
							
						}
	
						echo "
						</div>
						<hr>
						<div class='theme'>
						";
						
						if (!isset($_SESSION['user'])) {
							
							echo "
							<h2>Mark </h2>
							<p>See your marks at all time.</p>
							";
							
						} else {
							
							echo "
							<form action='mark.php' method='post'>
								<button type='submit' id='mainButton'>Mark</button>
							</form>
							";
							
						}
	
						echo "							
						</div>
						<hr>
						<div class='theme'>
						";
	
						if (!isset($_SESSION['user'])) {
							
							echo "
							<h2>Note </h2>
							<p>Manage all your information.</p>
							";
							
						} else {
							
							echo "
							<form action='note.php' method='post'>
								<button type='submit' id='mainButton'>Note</button>
							</form>
							";
							
						}
	
						echo "
						</div>
					</div>
				
				</div>
			
			</div>
		
		</div>
		
		<div class='footer'>
		
			<h2 class='footerTitle'>Lee Schoolassist</h2>
			<br>
			<h2 class='footerRights'>2018 all rights reserved</h2>
		
		</div>
		
		</body>
	</html>
	";
	
}

function test() {
	echo "iess";	
}

function logout() {
	
	unset($_SESSION['user']);
	session_destroy();
	showHome();
	
}

?>