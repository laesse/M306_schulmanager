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
		<title>Schulmanager: Home</title>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
		<link rel='stylesheet' href='https://code.getmdl.io/1.3.0/material.indigo-pink.min.css'>
		<script defer src='https://code.getmdl.io/1.3.0/material.min.js'></script>
	</head>
	<body>
		<!-- Uses a header that scrolls with the text, rather than staying locked at the top -->
		<div class='mdl-layout mdl-js-layout'>
  			<header class='mdl-layout__header mdl-layout__header--scroll'>
    			<div class='mdl-layout__header-row'>
      				<!-- Title -->
      				<span class='mdl-layout-title'>Schulmanager</span>
      				<!-- Add spacer, to align navigation to the right -->
      				<div class='mdl-layout-spacer'></div>
      					<!-- Navigation -->
      					<nav class='mdl-navigation'>
	";
	
	
	if (isset($_SESSION['user'])) {
		echo "
        <a class='mdl-navigation__link' href='note.php'>Note</a>
		<a class='mdl-navigation__link' href='timetable.php'>Timetable</a>
		<a class='mdl-navigation__link' href='index.php?status=logout'>Logout</a>	
		";
	} else {
   		echo "
		<a class='mdl-navigation__link' href='user.php'>Login</a>
		";
	}      
		
	echo "
	  					</nav>
    				</div>
  				</header>
				<div class='mdl-layout__drawer'>
    				<span class='mdl-layout-title'>Schulmanager</span>
    				<nav class='mdl-navigation'>
    ";
	
	if (isset($_SESSION['user'])) {
		echo "
        <a class='mdl-navigation__link' href='note.php'>Note</a>
		<a class='mdl-navigation__link' href='timetable.php'>Timetable</a>
		<a class='mdl-navigation__link' href='index.php?status=logout'>Logout</a>	
		";
	} else {
   		echo "
		<a class='mdl-navigation__link' href='user.php'>Login</a>
		";
	} 
	
	echo "
					</nav>
  				</div>
  				<main class='mdl-layout__content'>
    				<div class='page-content'>	
						
						<div class='mdl-grid'>
							<div class='mdl-layout-spacer'></div>
    						<div class='mdl-cell mdl-cell--4-col'>
							
	<h3>Welcome</h3>
	<p>Start your schoolmanagement today.</p>
							
							</div>
    						<div class='mdl-layout-spacer'></div>
						</div>
													
					</div>
  				</main>
			<footer class='mdl-mini-footer'>
  				<div class='mdl-mini-footer__left-section'>
    				<div class='mdl-logo'>TODO</div>
    				<ul class='mdl-mini-footer__link-list'>
      					<li><a href=''>Help</a></li>
      					<li><a href=''>Privacy & Terms</a></li>
    				</ul>
  				</div>
			</footer>
			</div>
		</body>
	</html>
	";
	
}

function logout() {
	
	unset($_SESSION['user']);
	session_destroy();
	showHome();
	
}

?>