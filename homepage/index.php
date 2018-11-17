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
	";
	
	include 'head.php';
	
	echo "
	</head>
	<body>
		<!-- Uses a header that scrolls with the text, rather than staying locked at the top -->
		<div class='mdl-layout mdl-js-layout mdl-layout--fixed-header'>
			<header class='mdl-layout__header mdl-layout__header--scroll'>
				<br><br><br><br><br><br><br><br><br><br><br>
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
        <a class='mdl-navigation__link' href='index.php'>Home</a>
        <a class='mdl-navigation__link' href='note.php'>Note</a>
		<a class='mdl-navigation__link' href='timetable.php'>Timetable</a>
		<a class='mdl-navigation__link' href='index.php?status=logout'>
			<!-- Contact Chip -->
			<span class='mdl-chip mdl-chip--contact'>
    			<span class='mdl-chip__contact mdl-color--teal mdl-color-text--white'>".$_SESSION['username'][0]."</span>
    			<span class='mdl-chip__text'>Logout</span>
			</span>
		</a>
		";
	} else {
   		echo "
        <a class='mdl-navigation__link' href='index.php'>Home</a>
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
        <a class='mdl-navigation__link' href='index.php'>Home</a>
        <a class='mdl-navigation__link' href='note.php'>Note</a>
		<a class='mdl-navigation__link' href='timetable.php'>Timetable</a>
		<a class='mdl-navigation__link' href='index.php?status=logout'>
			<!-- Contact Chip -->
			<span class='mdl-chip mdl-chip--contact'>
    			<span class='mdl-chip__contact mdl-color--teal mdl-color-text--white'>".$_SESSION['username'][0]."</span>
    			<span class='mdl-chip__text'>Logout</span>
			</span>
		</a>
		";
	} else {
   		echo "
        <a class='mdl-navigation__link' href='index.php'>Home</a>
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
							
	";
	
	
	if (!isset($_SESSION['user'])) {
	
	echo "
							<form action='user.php' method='post'>
								<button class='mdl-button mdl-js-button mdl-button--raised mdl-button--colored' type='submit'>Login</button>
							</form>
							<form action='user.php?status=showRegistration' method='post'>
								<button class='mdl-button mdl-js-button mdl-button--raised' type='submit'>Register</button>
							</form>
							
							<br>
								
								
	";
	
	}
	
	
	echo "
								</div>
								<div class='mdl-layout-spacer'></div>
  							</div>
	
							<br>
							
							<!-- Square card -->
							<style>
							.demo-card-square.mdl-card {
  								width: 320px;
  								height: 320px;
							}
							.demo-card-square > .mdl-card__title {
								color: #fff;
								background-color: #3f51b5;
								/*
								background:
								url('../assets/demos/dog.png') bottom right 15% no-repeat #46B6AC;
								*/
							}
							</style>
							
							<div class='mdl-grid'>
    						<div class='mdl-layout-spacer'></div>
							
  								<div class='mdl-cell mdl-cell--3-col'>
									
									<div class='demo-card-square mdl-card mdl-shadow--2dp'>
										<div class='mdl-card__title mdl-card--expand'>
											<h2 class='mdl-card__title-text'>Note</h2>
										</div>
  										<div class='mdl-card__supporting-text'>
											Save your thoughts.
  										</div>
									</div>
								
								</div>
  								<div class='mdl-cell mdl-cell--3-col'>
								
									<div class='demo-card-square mdl-card mdl-shadow--2dp'>
										<div class='mdl-card__title mdl-card--expand'>
    										<h2 class='mdl-card__title-text'>Timetable</h2>
  										</div>
  										<div class='mdl-card__supporting-text'>
											Never forgett a lesson again.
  										</div>
									</div>
						
							</div>
    						<div class='mdl-layout-spacer'></div>
						</div>
						
						<br><br><br><br><br><br>
													
					</div>
  				</main>
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