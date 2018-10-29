<?php

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

switch($_GET['status'])
{
	case 'checkSaveNote':
       	checkSaveNote();
	break;
	case 'checkAddNote':
       	checkAddNote();
	break;
	case 'checkAddNotebook':
       	checkAddNotebook();
	break;
	case 'deleteNotebook':
       	deleteNotebook();
	break;
	case 'deleteNote':
       	deleteNote();
	break;
	case 'jumpToHome':
		jumpToHome();
		break;
	default:
		showNote();
	break;
}

function showNote() {
	
	
	
	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>Note</title>
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
	} 
	
	echo "
					</nav>
  				</div>
  				<main class='mdl-layout__content'>
    				<div class='page-content'>
					
					
					
						<div class='mdl-grid'>
							<div class='mdl-layout-spacer'></div>
    						<div class='mdl-cell mdl-cell--4-col'>
							
							
	<h3>Notebooks</h3>
	<p>These are your Notebooks.</p>
	";				
		
	$servername = "localhost";
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
	$stmt = $conn->prepare("SELECT id, name FROM notebook WHERE user_id_fk=?");
	$stmt->bind_param("i",$_SESSION["user"]);

	$stmt->execute();

	// bind result variable
    $stmt->bind_result($id, $name);

	// fetch value
	while ($stmt->fetch()) {

		$id_notebook = $id;
	
	echo "
	
	<!-- Square card -->
	<style>
	.demo-card-square.mdl-card {
		width: 320px;
		height: 320px;
	}
	.demo-card-square > .mdl-card__title {
  		color: #fff;
  		background: url('../assets/demos/dog.png') bottom right 15% no-repeat #46B6AC;
	}
	</style>

	<div class='demo-card-square mdl-card mdl-shadow--2dp'>
  		<div class='mdl-card__title mdl-card--expand'>
			<h2 class='mdl-card__title-text'>".$name."</h2>
  		</div>
  		<div class='mdl-card__supporting-text'>
    		DELETE?
  		</div>
  		<div class='mdl-card__actions mdl-card--border'>
	";
		
    		$servername1 = "localhost";
			$dbusername1 = "id7650771_phpuser";
			$password1 = "phpUser123#";
			$dbname1 = "id7650771_schulmanager";

			// Create connection
			$conn1 = new mysqli($servername1, $dbusername1, $password1, $dbname1);

			// Check connection
			if ($conn1->connect_error) {
				die("Connection failed: ".$conn->connect_error);
			}

			// prepare and bind
			$stmt1 = $conn1->prepare("SELECT id, title, notetext FROM note WHERE notebook_id_fk=?");
			$stmt1->bind_param("i",$id_notebook);

			$stmt1->execute();

			// bind result variable
    		$stmt1->bind_result($id, $title, $notetext);

			// fetch value
			while ($stmt1->fetch()) {
			
	echo "
			<button id='show-dialog' type='button' class='mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect'>Show Note</button>
  			<dialog class='mdl-dialog'>
    			<h4 class='mdl-dialog__title'>Allow data collection?</h4>
    			<div class='mdl-dialog__content'>
      				
					<form action='?status=checkSaveNote' method='post'>
						<textarea name='txtNote'>".$notetext."</textarea><br>
						<button type='submit' class='btnSaveNote'>Save</button>
					</form>
					
    			</div>
    			<div class='mdl-dialog__actions'>
      				<button type='button' class='mdl-button'>Agree</button>
      				<button type='button' class='mdl-button close'>Disagree</button>
    			</div>
  			</dialog>
  			<script>
    			var dialog = document.querySelector('dialog');
    			var showDialogButton = document.querySelector('#show-dialog');
    			if (! dialog.showModal) {
					dialogPolyfill.registerDialog(dialog);
    			}
    			showDialogButton.addEventListener('click', function() {
      				dialog.showModal();
    			});
    			dialog.querySelector('.close').addEventListener('click', function() {
					dialog.close();
    			});
  			</script>
			
			
  		</div>
	</div>
	<br>
	";
			}

			$stmt1->close();
			$conn1->close();
	}

	$stmt->close();
	$conn->close();

	echo"
	</div>
	<div class='container addNotebook'>
	<form action='?status=checkAddNotebook' method='post'>
		<label for='addNotebook'>Add Notebook</label><br>
		<input type='text' name='addNotebook' id='addNotebook' required>
		<button type='submit' class='btnAddNotebook'>Add</button>
	</form>
	</div>

	</body>
	</html>
	";
	
	echo "
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

function checkSaveNote() {

	$success = true;
	$notetxt = htmlspecialchars(trim($_POST['txtNote']));

	if (empty($notetxt)) {
        $success = false;
    }

	if ($success) {
		updateNote();
	}else {
		showNote();
	}

}

function updateNote(){

	$pdo = new PDO('mysql:host=localhost;dbname=id7650771_schulmanager', 'id7650771_phpuser', 'phpUser123#');

	$statement = $pdo->prepare("UPDATE note SET notetext = ? WHERE id = ?");
	$statement->execute(array(htmlspecialchars(trim($_POST['txtNote'])), $_POST['note']));

	showNote();
}

function checkAddNote() {

	$success = true;
	$note = htmlspecialchars(trim($_POST['addNote']));

	if (empty($note)) {
        $success = false;
    }

	// Check Note already used
	$servername = "localhost";
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
	$stmt = $conn->prepare("SELECT id FROM note WHERE title=? AND notebook_id_fk=?");
	$stmt->bind_param("si", $note, $notebook_id_fk);

	$notebook_id_fk = htmlspecialchars(trim($_POST['notebook']));

	$stmt->execute();

	// bind result variable
   	$stmt->bind_result($id);

	// fetch value
	if ($stmt->fetch()) {
		$success = false;
	}

	$stmt->close();
	$conn->close();

	if ($success) {
		insertNote();
	}else {
		showNote();
	}

}

function insertNote(){

	$servername = "localhost";
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
	$stmt = $conn->prepare("INSERT INTO note (title, notetext, notebook_id_fk) VALUES (?, ?, ?)");
	$stmt->bind_param("ssi", htmlspecialchars(trim($_POST['addNote'])), $notetext, htmlspecialchars(trim($_POST['notebook'])));

	// set parameters and execute
	$notetext = "Schreibe deine Notiz";

	$stmt->execute();

	$stmt->close();
	$conn->close();

	showNote();
}

function checkAddNotebook() {

	$success = true;
	$notebook = htmlspecialchars(trim($_POST['addNotebook']));

	if (empty($notebook)) {
        $success = false;
    }

	// Check Username already used
	$servername = "localhost";
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
	$stmt = $conn->prepare("SELECT id FROM notebook WHERE user_id_fk=? AND name=?");
	$stmt->bind_param("is",$_SESSION["user"],$notebook);

	$stmt->execute();

	// bind result variable
   	$stmt->bind_result($id);

	// fetch value
	if ($stmt->fetch()) {
		$success = false;
	}

	$stmt->close();
	$conn->close();

	if ($success) {
		insertNotebook();
	}else {
		showNote();
	}

}

function insertNotebook() {

	$servername = "localhost";
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
	$stmt = $conn->prepare("INSERT INTO notebook (name, user_id_fk) VALUES (?, ?)");
	$stmt->bind_param("si", $name, $_SESSION["user"]);

	// set parameters and execute
	$name = htmlspecialchars(trim($_POST['addNotebook']));

	$stmt->execute();

	$stmt->close();
	$conn->close();

	showNote();

}

function deleteNotebook(){

	$servername = "localhost";
	$username 	= "id7650771_phpuser";
	$password 	= "phpUser123#";
	$dbname 	= "id7650771_schulmanager";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	// sql to delete a record
	$sql = "DELETE FROM note WHERE notebook_id_fk=".htmlspecialchars(trim($_POST['notebook']));

	if (mysqli_query($conn, $sql)) {
	}

	// sql to delete a record
	$sql = "DELETE FROM notebook WHERE id=".htmlspecialchars(trim($_POST['notebook']));

	if (mysqli_query($conn, $sql)) {
	}

	mysqli_close($conn);

	showNote();
}

function deleteNote(){

	$servername = "localhost";
	$username 	= "id7650771_phpuser";
	$password 	= "phpUser123#";
	$dbname 	= "id7650771_schulmanager";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	// sql to delete a record
	$sql = "DELETE FROM note WHERE id=".htmlspecialchars(trim($_POST['note']));

	if (mysqli_query($conn, $sql)) {
	}

	mysqli_close($conn);

	showNote();
}

function jumpToHome() {

	header('Location: index.php');

}

?>
