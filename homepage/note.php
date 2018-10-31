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
	case 'checkAddNotebook':
       	checkAddNotebook();
	break;
	case 'deleteNotebook':
       	deleteNotebook();
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
	
		
	
	
	<!-- Simple header with scrollable tabs. -->
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
					<!-- Tabs -->
    				<div class='mdl-layout__tab-bar mdl-js-ripple-effect'>
	";
	
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
	$stmt = $conn->prepare("SELECT id, name FROM notebook WHERE user_id_fk=?");
	$stmt->bind_param("i",$_SESSION["user"]);

	$stmt->execute();

	// bind result variable
    $stmt->bind_result($id, $name);

	echo "<a href='#scroll-tab-0' class='mdl-layout__tab is-active'>Add Notebook</a>";
	// fetch value
	while ($stmt->fetch()) {
	
		echo "<a href='#scroll-tab-".$id."' class='mdl-layout__tab'>".$name."</a>";
		
	}

	$stmt->close();
	$conn->close();
	
	echo "
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
  
    <section class='mdl-layout__tab-panel is-active' id='scroll-tab-0'>
      <div class='page-content'>
	  
	  	<div class='mdl-grid'>
			<div class='mdl-layout-spacer'></div>
    		<div class='mdl-cell mdl-cell--4-col'>
	  	
		<h3>Add Notebook</h3>
	
		<form action='?status=checkAddNotebook' method='post'>
	
			<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
    			<input class='mdl-textfield__input' type='text' id='sample3' name='addNotebook' >
    			<label class='mdl-textfield__label' for='sample3'>Name</label>
  			</div>
			<!-- FAB button with ripple -->
			<button class='mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect' type='submit'>
  				<i class='material-icons'>add</i>
			</button>		
		</form>
		
			</div>
			<div class='mdl-layout-spacer'></div>
		</div>
			
	  </div>
    </section>
    ";
	
	$servername1 = "localhost";
	/*
	$dbusername1 = "root";
	$password1 = "";
	$dbname1 = "schulmanager";
	*/
	$dbusername1 = "id7650771_phpuser";
	$password1 = "phpUser123#";
	$dbname1 = "id7650771_schulmanager";
	
		
	// Create connection
	$conn1 = new mysqli($servername1, $dbusername1, $password1, $dbname1);

	// Check connection
	if ($conn1->connect_error) {
		die("Connection failed: ".$conn1->connect_error);
	}

	// prepare and bind
	$stmt1 = $conn1->prepare("SELECT id FROM notebook WHERE user_id_fk=?");
	$stmt1->bind_param("i",$_SESSION["user"]);

	$stmt1->execute();

	// bind result variable
    $stmt1->bind_result($id);
	
	// fetch value
	while ($stmt1->fetch()) {

		$id_notebook = $id;
		
	echo "
	<section class='mdl-layout__tab-panel' id='scroll-tab-".$id."'>
      <div class='page-content'>
	  
	  <div class='mdl-grid'>
			<div class='mdl-layout-spacer'></div>
    		<div class='mdl-cell mdl-cell--4-col'>
	"; 	
		
		
		
		
		
    		$servername2 = "localhost";
			/*
			$dbusername2 = "root";
			$password2 = "";
			$dbname2 = "schulmanager";
			*/
			$dbusername2 = "id7650771_phpuser";
			$password2 = "phpUser123#";
			$dbname2 = "id7650771_schulmanager";
			
			
			// Create connection
			$conn2 = new mysqli($servername2, $dbusername2, $password2, $dbname2);

			// Check connection
			if ($conn2->connect_error) {
				die("Connection failed: ".$conn2->connect_error);
			}

			// prepare and bind
			$stmt2 = $conn2->prepare("SELECT id, notetext FROM note WHERE notebook_id_fk=?");
			$stmt2->bind_param("i",$id_notebook);

			$stmt2->execute();

			// bind result variable
    		$stmt2->bind_result($id, $notetext);

			// fetch value
			if ($stmt2->fetch()) {
				
				echo "
				<br>
				<form action='?status=checkSaveNote' method='post'>
				<div class='mdl-textfield mdl-js-textfield'>
					<textarea class='mdl-textfield__input' type='text' rows= '18' id='sample5' name='txtNote'>".$notetext."</textarea>
    				<label class='mdl-textfield__label' for='sample5'>Your Note...</label>
				</div>
				<br>
				<input type='hidden' name='note' value='".$id."'>
				<button class='mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect' type='submit'>Save</button>
    			</form>
				<br>
				<form action='?status=deleteNotebook' method='post'>
				<input type='hidden' name='deleteNotebook' value='".$id_notebook."'>
				<button class='mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent' type='submit'>Delete</button>
				</form>
				<br><br>
				";
			}

			$stmt2->close();
			$conn2->close();
	echo "
			</div>
			<div class='mdl-layout-spacer'></div>
		</div>
	  </div>
    </section>
    ";
		
	}

	$stmt1->close();
	$conn1->close();
	
	
	echo "
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

	//$pdo = new PDO('mysql:host=localhost;dbname=schulmanager', 'root', '');
	$pdo = new PDO('mysql:host=localhost;dbname=id7650771_schulmanager', 'id7650771_phpuser', 'phpUser123#');

	$statement = $pdo->prepare("UPDATE note SET notetext = ? WHERE id = ?");
	$statement->execute(array(htmlspecialchars(trim($_POST['txtNote'])), $_POST['note']));

	showNote();
}

function insertNote(){

	$notebook = htmlspecialchars(trim($_POST['addNotebook']));
	
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
	$stmt = $conn->prepare("SELECT id FROM notebook WHERE name=?");
	$stmt->bind_param("s",$notebook);

	$stmt->execute();

	// bind result variable
   	$stmt->bind_result($id);

	// fetch value
	if ($stmt->fetch()) {
		
		$servername1 = "localhost";
		/*
		$dbusername1 = "root";
		$password1 = "";
		$dbname1 = "schulmanager";
		*/
		$dbusername1 = "id7650771_phpuser";
		$password1 = "phpUser123#";
		$dbname1 = "id7650771_schulmanager";
		
	
		// Create connection
		$conn1 = new mysqli($servername1, $dbusername1, $password1, $dbname1);

		// Check connection
		if ($conn1->connect_error) {
			die("Connection failed: ".$conn1->connect_error);
		}

		// prepare and bind
		$stmt1 = $conn1->prepare("INSERT INTO note (title, notetext, notebook_id_fk) VALUES (?, ?, ?)");
		$stmt1->bind_param("ssi", $notetitle, $notetext, $id);

		// set parameters and execute
		$notetitle = "generatedNote";
		$notetext = "Write your Note";

		$stmt1->execute();

		$stmt1->close();
		$conn1->close();
		
	}

	$stmt->close();
	$conn->close();
	
}

function checkAddNotebook() {

	$success = true;
	$notebook = htmlspecialchars(trim($_POST['addNotebook']));

	if (empty($notebook)) {
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
		insertNote();
		showNote();
	}else {
		showNote();
	}

}

function insertNotebook() {

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
	$stmt = $conn->prepare("INSERT INTO notebook (name, user_id_fk) VALUES (?, ?)");
	$stmt->bind_param("si", $name, $_SESSION["user"]);

	// set parameters and execute
	$name = htmlspecialchars(trim($_POST['addNotebook']));

	$stmt->execute();

	$stmt->close();
	$conn->close();

}

function deleteNotebook(){

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
	$conn = mysqli_connect($servername, $dbusername, $password, $dbname);
	// Check connection
	if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	// sql to delete a record
	$sql = "DELETE FROM note WHERE notebook_id_fk=".htmlspecialchars(trim($_POST['deleteNotebook']));

	if (mysqli_query($conn, $sql)) {
	}

	// sql to delete a record
	$sql = "DELETE FROM notebook WHERE id=".htmlspecialchars(trim($_POST['deleteNotebook']));

	if (mysqli_query($conn, $sql)) {
	}

	mysqli_close($conn);

	deleteNote();
	showNote();
}

function deleteNote(){

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
	$conn = mysqli_connect($servername, $dbusername, $password, $dbname);
	// Check connection
	if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	// sql to delete a record
	$sql = "DELETE FROM note WHERE notebook_id_fk=".htmlspecialchars(trim($_POST['deleteNotebook']));

	if (mysqli_query($conn, $sql)) {
	}

	mysqli_close($conn);
	
}

?>
