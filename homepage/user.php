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
	case 'checkLogin':
       	checkLogin();
	break;
	case 'showLogin':
       	showLogin();
	break;
	case 'checkRegistration':
       	checkRegistration();
	break;
	case 'showRegistration':
		showRegistration();
	break;
	default:
		showLogin();
	break;
}

function showLogin() {
	
	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>Login</title>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
		<link rel='stylesheet' href='https://code.getmdl.io/1.3.0/material.indigo-red.min.css'>
		<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' type='text/css'>
		<script defer src='https://code.getmdl.io/1.3.0/material.min.js'></script>
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
							
							
	<h3>Login</h3>
	<p>Welcome back! Login to manage your work.</p>
	<p>Don't have an account yet? <a href='?status=showRegistration'>Register here</a></p>
					
	<!-- Textfield with Floating Label -->
	<form action='?status=checkLogin' method='post'>
  		<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
    		<input class='mdl-textfield__input' type='text' id='sample3' name='username' value='".htmlspecialchars($_POST['username'])."'>
    		<label class='mdl-textfield__label' for='sample3'>Username</label>
  		</div>
		<br>
		<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
    		<input class='mdl-textfield__input' type='password' id='sample3' name='password' value='".htmlspecialchars($_POST['password'])."'>
    		<label class='mdl-textfield__label' for='sample3'>Password</label>
  		</div>
		<br>
		<!-- TODO: SNACKBAR / Accent-colored raised button with ripple -->
		<button class='mdl-button mdl-js-button mdl-button--raised' type='submit'>Login</button>
	</form>			
					
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

function checkLogin() {

	
	$success = true;

	$username = htmlspecialchars(trim($_POST['username']));
	$password_input = htmlspecialchars(trim($_POST['password']));

	if (empty($username)) {
		unset($_POST['username']);
        $success = false;
    }
	if (empty($password_input)) {
		unset($_POST['password']);
        $success = false;
    }

	if ($success){
		$success = false;

		
		//IF FOUND $success = true;
		$servername = "localhost";
		/*
		$dbusername = "root";
		$password = "";
		$dbname = "schulmanager";
		*/
		$dbusername = "id7650771_phpuser";
		$password = "phpUser123#";
		$dbname = "id7650771_schulmanager";
		

		// Create connection
		$conn = new mysqli($servername, $dbusername, $password, $dbname);

		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: ".$conn->connect_error);
		}

		// prepare and bind
		$stmt = $conn->prepare("SELECT id, password_hash FROM user WHERE username=?");
		$stmt->bind_param("s",$username);

		$stmt->execute();

		// bind result variable
    	$stmt->bind_result($id, $password);

		// fetch value
		if ($stmt->fetch()) {
			if ($password_input == $password){
				$success = true;
				$_SESSION["user"] = $id;
			}else {
				unset($_POST['password']);
			}
		} else {
			unset($_POST['username']);
			unset($_POST['password']);
        	$success = false;
		}

		$stmt->close();
		$conn->close();
	}

	if ($success) {
		header('Location: index.php');
	}else {
		showLogin();
	}

}

function showRegistration() {

	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>Register</title>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
		<link rel='stylesheet' href='https://code.getmdl.io/1.3.0/material.indigo-red.min.css'>
		<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' type='text/css'>
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
        <a class='mdl-navigation__link' href='index.php'>Home</a>
        <a class='mdl-navigation__link' href='note.php'>Note</a>
		<a class='mdl-navigation__link' href='timetable.php'>Timetable</a>
		<a class='mdl-navigation__link' href='index.php?status=logout'>Logout</a>	
		";
	} else {
		echo "
		<a class='mdl-navigation__link' href='index.php'>Home</a>
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
		<a class='mdl-navigation__link' href='index.php?status=logout'>Logout</a>	
		";
	} else {
		echo "
		<a class='mdl-navigation__link' href='index.php'>Home</a>
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
							
							
	<h3>Register</h3>
	<p>Welcome! Register to manage your work.</p>
	<p>Already have an account? <a href='?status=showLogin'>Login here</a></p>
					
	<!-- Textfield with Floating Label -->
	<form action='?status=checkRegistration' method='post'>
  		<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
    		<input class='mdl-textfield__input' type='text' id='sample3' name='username' value='".htmlspecialchars($_POST['username'])."'>
    		<label class='mdl-textfield__label' for='sample3'>Username</label>
  		</div>
		<br>
		<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
    		<input class='mdl-textfield__input' type='email' id='sample3' name='email' value='".htmlspecialchars($_POST['email'])."'>
    		<label class='mdl-textfield__label' for='sample3'>Mail</label>
  		</div>
		<br>
		<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
    		<input class='mdl-textfield__input' type='password' id='sample3' name='password' value='".htmlspecialchars($_POST['password'])."'>
    		<label class='mdl-textfield__label' for='sample3'>Password</label>
  		</div>
		<br>
		<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
    		<input class='mdl-textfield__input' type='password' id='sample3' name='password-repeat' value='".htmlspecialchars($_POST['password-repeat'])."'>
    		<label class='mdl-textfield__label' for='sample3'>Repeate Password</label>
  		</div>
		<br>
		<!-- TODO: SNACKBAR / Accent-colored raised button with ripple -->
		<button class='mdl-button mdl-js-button mdl-button--raised' type='submit'>Register</button>
	</form>			
					
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

function checkRegistration() {

	$success = true;

	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$password = htmlspecialchars(trim($_POST['password']));
	$password_repeat = htmlspecialchars(trim($_POST['password-repeat']));


	if (empty($username)) {
		unset($_POST['username']);
        $success = false;
    }
	if (empty($email)) {
		unset($_POST['email']);
        $success = false;
    }
	if (empty($password)) {
		unset($_POST['password']);
        $success = false;
    }
	if (empty($password_repeat)) {
		unset($_POST['password-repeat']);
        $success = false;
    }


	if ($password_repeat != $password) {
		unset($_POST['password']);
		unset($_POST['password-repeat']);
        $success = false;
    }


	// Check Username already used
	$servername = "localhost";
	/*
	$dbusername = "root";
	$password = "";
	$dbname = "schulmanager";
	*/
	$dbusername = "id7650771_phpuser";
	$password = "phpUser123#";
	$dbname = "id7650771_schulmanager";
	

	// Create connection
	$conn = new mysqli($servername, $dbusername, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	// prepare and bind
	$stmt = $conn->prepare("SELECT id FROM user WHERE username=?");
	$stmt->bind_param("s",$username);

	$stmt->execute();

	// bind result variable
   	$stmt->bind_result($id);

	// fetch value
	if ($stmt->fetch()) {
		$success = false;
		unset($_POST['username']);
	}

	$stmt->close();
	$conn->close();

	// Check email already used
	$servername = "localhost";
	/*
	$dbusername = "root";
	$password = "";
	$dbname = "schulmanager";
	*/
	$dbusername = "id7650771_phpuser";
	$password = "phpUser123#";
	$dbname = "id7650771_schulmanager";
	

	// Create connection
	$conn = new mysqli($servername, $dbusername, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	// prepare and bind
	$stmt = $conn->prepare("SELECT id FROM user WHERE email=?");
	$stmt->bind_param("s",$email);

	$stmt->execute();

	// bind result variable
    $stmt->bind_result($id);

	// fetch value
	if ($stmt->fetch()) {
		$success = false;
		unset($_POST['email']);
	}

	$stmt->close();
	$conn->close();



	if ($success) {
		insertRegistration();
	}else {
		showRegistration();
	}

}


function insertRegistration() {

	$servername = "localhost";
	/*
	$dbusername = "root";
	$password = "";
	$dbname = "schulmanager";
	*/
	$dbusername = "id7650771_phpuser";
	$password = "phpUser123#";
	$dbname = "id7650771_schulmanager";
	

	// Create connection
	$conn = new mysqli($servername, $dbusername, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	// prepare and bind
	$stmt = $conn->prepare("INSERT INTO user (username, email, password_hash) VALUES (?, ?, ?)");
	$stmt->bind_param("sss", $username, $email, $password_hash);

	// set parameters and execute
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$password_hash = htmlspecialchars(trim($_POST['password']));

	$stmt->execute();

	$stmt->close();
	$conn->close();

	showLogin();

}

function logout() {
	
	unset($_SESSION['user']);
	session_destroy();
	header('Location: index.php');
	
}
?>
