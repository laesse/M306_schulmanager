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
		<title>Home</title>
	";
	
	include 'head.php';
	
	echo "
	</head>
	<body>
		<!-- Uses a header that scrolls with the text, rather than staying locked at the top -->
		<div class='mdl-layout mdl-js-layout'>
  	";
	
	include 'navigation.php';
	
	echo "			
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