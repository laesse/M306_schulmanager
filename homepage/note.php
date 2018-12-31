<?php

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

switch($_GET['status'])
{
	case 'viewNotebook':
		viewNotebook();
	break;
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

function getConnection(){
	$ini = parse_ini_file('../config/db.ini');

	$servername = $ini["servername"];
	$dbusername = $ini["db_user"];
	$password = $ini["db_password"];
	$dbname = $ini["db_name"];

	// return new mysqli connection
	return new mysqli($servername, $dbusername, $password, $dbname);
}


function showNote() {

	echo "
	<!DOCTYPE html>
	<html>
	<head>		
		<title>LeeSchoolassist: Note</title>
		<meta name='theme-color' content='pink'>
		<link rel='stylesheet' type='text/css' href='note.css'>
		<link rel='shortcut icon' href='favicon.png' type='image/x-icon'/>	
		<meta name='viewport' content='width=device-width, initial-scale=1.0' />
	</head>
	<body>
	
		<div class='divEdit'>
			
			<h1>ADD NOTEBOOK</h1>
			<p>Save all your thoughts.</p><br>
			<form action='?status=checkAddNotebook' method='post'>
				<input type='text' name='addNotebook'><br>
  				<button type='submit' class='btnLogin'>ADD</button>
			</form><br><br><br>
			";
			readNotebooks();
			echo "
		
		</div>
  		<div class='divContent'>
		";
	
		if (isset($_SESSION["noteText"])) {
			
			echo "
			<form action='?status=checkSaveNote' method='post'>
				<textarea type='text' name='txtNote'>".@$_SESSION["noteText"]."</textarea>
				<button class='btnLogin' type='submit'>Save</button>
    		</form>
			<form action='?status=deleteNotebook' method='post'>
				<button class='btnRegister' type='submit'>Delete</button>
			</form>
			";
			
		}
			
		echo "	
		</div>
		<div class='divNavigation'>
			<a href='index.php'><img src='img/homeOnWhite.svg'></a>
			<a href='timetable.php'><img src='img/timetableOnWhite.svg'></a>
			<!--<a href='mark.php'><img src='img/markOnWhite.svg'></a>-->
			<a href='index.php?status=logout'><img src='img/logout.svg'></a>
		</div>
  
</div>
</body>
</html>
	";


}

function readNotebooks() {
	// Create connection
	$conn1 = getConnection();

	// Check connection
	if ($conn1->connect_error) {
		die("Connection failed: ".$conn1->connect_error);
	}

  $notebooksFromAUser = $conn1->prepare("SELECT id, name FROM notebook WHERE user_id_fk=?");
	$notebooksFromAUser->bind_param("i",$_SESSION["user"]);

	if(!$notebooksFromAUser->execute()){
		// TODO: echo Error
	}
	// bind result variable
  $notebooksFromAUser->bind_result($id_notebook, $name);

	// fetch value
	while ($notebooksFromAUser->fetch()) {
  
		echo "
		<form action='?status=viewNotebook' method='post'>
		";

    	$conn2 = getConnection();
		//get the notes out of the notebook
		$notesInNotebook = $conn2->prepare("SELECT id, notetext FROM note WHERE notebook_id_fk=?");
		$notesInNotebook->bind_param("i",$id_notebook);
		if (!$notesInNotebook->execute()){
			// TODO: echo Error
		}

		// bind result variable
		$notesInNotebook->bind_result($id, $notetext);

		// fetch value
		if ($notesInNotebook->fetch()) {
				echo "
				<input type='hidden' name='noteText' value='".$notetext."'>
				<input type='hidden' name='saveNote' value='".$id."'>
				<input type='hidden' name='deleteNotebook' value='".$id_notebook."'>
				";
			}

		$notesInNotebook->close();
		$conn2->close();
		
		echo "
    		<button class='notebooks' type='submit'>".$name."</button>
		</form><br>
		";

	}

	$notebooksFromAUser->close();
	$conn1->close();
}



function viewNotebook(){
	
	$_SESSION["noteText"] = htmlspecialchars(trim(@$_POST['noteText']));
	$_SESSION["saveNote"] = htmlspecialchars(trim(@$_POST['saveNote']));
	$_SESSION["deleteNotebook"] = htmlspecialchars(trim(@$_POST['deleteNotebook']));
	showNote();
	
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
	
	$conn = getConnection();
	$txtNote = htmlspecialchars(trim($_POST['txtNote']));
	$_SESSION["noteText"] = $txtNote;
  	
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	$updateNotetxt = $conn->prepare("UPDATE note SET notetext = ? WHERE id = ?");
	$updateNotetxt->bind_param("si", $txtNote, $_SESSION['saveNote']);

	if(!$updateNotetxt->execute()){
		// TODO: echo Error
	}


	$updateNotetxt->close();
	$conn->close();
	showNote();
	
}


function insertNote(){
	$notebook = htmlspecialchars(trim($_POST['addNotebook']));

  $conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}


	// prepare and bind
	$stmt = $conn->prepare("SELECT id FROM notebook WHERE name=?");
	$stmt->bind_param("s",$notebook);

	if(!$stmt->execute()){
		// TODO: echo Error
	}

	// bind result variable
  $stmt->bind_result($id);

	// fetch value
	if ($stmt->fetch()) {
		// Create connection
		$conn1 = getConnection();

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

	/*
  Check if User already have a notebook with the same title
	*/
	if (empty($notebook)) {
    $success = false;
  }

	// get connection
	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	// if cnt is 1 there is alerady a notebook with the same name for this user
	$stmt = $conn->prepare("SELECT count(*) cnt FROM notebook WHERE user_id_fk=? AND name=?");
	$stmt->bind_param("is",$_SESSION["user"],$notebook);

	if(!$stmt->execute()){
	  // TODO: echo Error
	}

  $stmt->bind_result($cnt);

	if ($stmt->fetch()) {
		if($cnt == 1){
			$success = false;
		}
	}

	$stmt->close();
	$conn->close();

	if ($success) {
		insertNotebook();
		insertNote();
		showNote();
	}else {
		// TODO: echo Error
		showNote();
	}

}

function insertNotebook() {
	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	// set parameters and execute
	$name = htmlspecialchars(trim($_POST['addNotebook']));

	// prepare and bind
	$insertIntoNotebook = $conn->prepare("INSERT INTO notebook (name, user_id_fk) VALUES (?, ?)");
	$insertIntoNotebook->bind_param("si", $name, $_SESSION["user"]);


	if(!$insertIntoNotebook->execute()){
		// TODO: echo Error
	}

	$insertIntoNotebook->close();
	$conn->close();
}

function deleteNotebook(){
	
	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);

	}
	
	$delNotebook = $conn->prepare("DELETE FROM note WHERE notebook_id_fk=?");
	$delNotebook->bind_param("i", $_SESSION['deleteNotebook']);

	if (!$delNotebook->execute()) {
		//ECHO FAIL
	}

	$delNotebook->close();
	$conn->close();

	
		
	$conn1 = getConnection();

	// Check connection
	if ($conn1->connect_error) {
    	die("Connection failed: " . $conn1->connect_error);

	}
	
	$delNotebook = $conn1->prepare("DELETE FROM notebook WHERE id=?");
	$delNotebook->bind_param("i", $_SESSION['deleteNotebook']);

	if (!$delNotebook->execute()) {
		//ECHO FAIL
	}

	$delNotebook->close();
	$conn1->close();

	unset($_SESSION['noteText']);
	unset($_SESSION['saveNote']);
	unset($_SESSION['deleteNotebook']);
	showNote();
}


?>
