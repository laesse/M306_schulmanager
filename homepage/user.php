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

function getConnection(){
	$ini = parse_ini_file('../config/db.ini');

	$servername = $ini["servername"];
	$dbusername = $ini["db_user"];
	$password = $ini["db_password"];
	$dbname = $ini["db_name"];

	// return new mysqli connection
	return new mysqli($servername, $dbusername, $password, $dbname);
}

function showLogin() {

	echo "
	<!DOCTYPE html>
	<html>
	<head>		
		<title>LeeSchoolassist: Login</title>
		<meta name='theme-color' content='pink'>
		<link rel='stylesheet' type='text/css' href='user.css'>
		<link rel='shortcut icon' href='favicon.png' type='image/x-icon'/>	
		<meta name='viewport' content='width=device-width, initial-scale=1.0' />
	</head>
	<body>
	
		<div class='divText'>
			
			<h1>LOGIN</h1>
			<p>Welcome back! Login to manage your work.</p>
			<p>Don't have an account yet?</p> 
			<form action='user.php?status=showRegistration' method='post'>
				<button type='submit' class='btnRegister'>Register here</button>
			</form>
			
		</div>
		<div class='divContent'>
		
			<form action='?status=checkLogin' method='post'>
				<h2>Username</h2><br>
    			<input type='text' name='username' value='".htmlspecialchars(@$_POST['username'])."'><br><br>
				<h2>Password</h2><br>
				<input type='password' name='password' value='".htmlspecialchars(@$_POST['password'])."'><br><br>
				<button type='submit' class='btnLogin'>Login</button>
			</form>
		
		</div>
		
		<div class='divNavigation'>
			<a href='index.php'><img src='img/home.svg'></a>
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

		// Create connection
		$conn = getConnection();

		// Check connection
		if ($conn->connect_errno) {
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
				if ( strtoupper(hash("sha256", $password_input)) == strtoupper($password)){
				$success = true;
				$_SESSION["user"] = $id;
				$_SESSION["username"] = $username;
        $_SESSION["password_hash"] = strtoupper($password);
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
		<title>LeeSchoolassist: Register</title>
		<meta name='theme-color' content='pink'>
		<link rel='stylesheet' type='text/css' href='user.css'>
		<link rel='shortcut icon' href='favicon.png' type='image/x-icon'/>	
		<meta name='viewport' content='width=device-width, initial-scale=1.0' />
	</head>
	<body>
		
		<div class='divText'>
		
			<h1>REGISTER</h1>
			<p>Welcome! Register to manage your work.</p>
			<p>Already have an account?</p>
			<form action='user.php?status=showLogin' method='post'>
				<button type='submit' class='btnRegister'>Login here</button>
			</form>
			
		</div>
		<div class='divContent'>
		
			<form action='?status=checkRegistration' method='post'>
  				<h2>Username</h2><br>
				<input type='text' name='username' value='".htmlspecialchars(@$_POST['username'])."'><br><br>
  				<h2>Mail</h2><br>
				<input type='email' name='email' value='".htmlspecialchars(@$_POST['email'])."'><br><br>
  				<h2>Password</h2><br>
				<input type='password' name='password' value='".htmlspecialchars(@$_POST['password'])."'><br><br>
  				<h2>Repeat Password</h2><br>
				<input type='password' name='password-repeat' value='".htmlspecialchars(@$_POST['password-repeat'])."'><br><br>
				<button type='submit' class='btnLogin'>Register</button>
			</form>
			
		</div>
		
		<div class='divNavigation'>
			<a href='index.php'><img src='img/home.svg'></a>
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


	// Create connection
	$conn = getConnection();

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


	// Create connection
	$conn = getConnection();

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


	// Create connection
	$conn = getConnection();

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
	$password_hash =hash('sha256', htmlspecialchars(trim($_POST['password'])));

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
