<?php

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

switch($_GET['status'])
{
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
	case 'jumpToHome':
		jumpToHome();
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
		<link href='user.css' rel='stylesheet'>
	</head>
	<body>

	<form action='?status=jumpToHome' method='post'>

		<button type='submit' class='btnHome'>Home</button>
		
	</form>
	
	<form action='?status=checkLogin' method='post'>

	<div class='container'>

		<h1>Login</h1>
		<p>Schön bisch da.</p>
		<hr>

		<label for='username'>Benutzernamä</label><br>
		<input type='text' placeholder='Wie heissisch du?' name='username' value='".htmlspecialchars($_POST['username'])."' required><br><br>

		<label for='password'>Passwort</label><br>
		<input type='password' placeholder='No rasch s Passwort.' name='password' value='".htmlspecialchars($_POST['password'])."' required><br><br>

		<button type='submit' class='btnLogin'>Amälde</button>
		</div>

	<div class='container register'>
		<p>Du häsch no kein Account? Dänn chasch di da no <a href='?status=showRegistration'>registrierä</p>
	</div>

	</form>

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
		$dbusername = "root";
		$password = "";
		$dbname = "schulmanager";

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
		welcome();	
	}else {
		showLogin();
	}

}

function showRegistration() {
	
	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>Registeration</title>
		<link href='user.css' rel='stylesheet'>
		</head>
		<body>

	<form action='?status=checkRegistration' method='post'>
	
	<div class='container'>
		
		<h1>Registration</h1>
		<p>Findi mega, dass di bi eus ahmäldisch. Das isch de ersti Schritt zu dinere neue Schuelorganisation.</p>
		<hr>

		<label for='username'>Benutzernamä</label><br>
		<input type='text' placeholder='Wie heissisch denn du?' name='username' value='".htmlspecialchars($_POST['username'])."' required><br><br>

		<label for='email'>Mail</label><br>
		<input type='email' placeholder='Unter wellere Adresse chömmer di am eifachste kontaktiere?' name='email' value='".htmlspecialchars($_POST['email'])."' required><br><br>

		<label for='password'>Passwort</label><br>
		<input type='password' placeholder='Diin Sicherheitsschlüssel für die Siite' name='password' value='".htmlspecialchars($_POST['password'])."' required><br><br>

		<label for='password-repeat'>Repeat Password</label><br>
		<input type='password' placeholder='Sorry, muesches nomale ihgeh. Sicherheit isch wichtig.' name='password-repeat' value='".htmlspecialchars($_POST['password-repeat'])."' required><br><br>
	
		<button type='submit' class='btnRegister'>Registriärä</button>
		</div>

	<div class='container login'>
		<p>Du häsch doch scho en Account? Denn chasch di da <a href='?status=showLogin'>amälde</p>
	</div>

	</form>

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
	$dbusername = "root";
	$password = "";
	$dbname = "schulmanager";

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
	$dbusername = "root";
	$password = "";
	$dbname = "schulmanager";

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
	$dbusername = "root";
	$password = "";
	$dbname = "schulmanager";

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

function welcome() {
	
	header('Location: index.php'); 
	
}

function jumpToHome() {
	
	header('Location: index.php'); 
	
}

?>