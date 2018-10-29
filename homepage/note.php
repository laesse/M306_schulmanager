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

function getConnection(){
	$servername = "localhost";
	$dbusername = "id7650771_phpuser";
	$password = "phpUser123#";
	$dbname = "id7650771_schulmanager";

	// return new mysqli connection
	return new mysqli($servername, $dbusername, $password, $dbname);
}


function showNote() {
	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>Note</title>
		<link href='note.css' rel='stylesheet'>
	</head>
	<body>

	<form action='?status=jumpToHome' method='post'>

		<button type='submit' class='btnHome'>Home</button>

	</form>

	<div class='container'>
	<h1>Add Notebook</h1>
	<p>Hier sind deine Notebooks.</p>
	<hr>
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

  			<div class='container notebook'>
			<h2 style='display:inline;'>".$name." </h2>
			<form style='display:inline;' action='?status=deleteNotebook' method='post'>
					<button type='submit' class='btnDeleteNotebook'>Delete</button>
					<input type='hidden' name='notebook' value='".$id_notebook."'>
			</form>

			</div>
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
  			<div class='container note'>
				  <p style='display:inline;'>".$title." </p>
					<form style='display:inline;' action='?status=deleteNote' method='post'>
						<button type='submit' class='btnDeleteNote'>Delete</button>
						<input type='hidden' name='note' value='".$id."'>
					</form>
					<form action='?status=checkSaveNote' method='post'>
				";
					if ($notetext == "Schreibe deine Notiz") {
						echo "<textarea name='txtNote' placeholder='".$notetext."' cols='128' rows='3'></textarea><br>";
					}else {
						echo "<textarea name='txtNote' placeholder='$notetext' cols='128' rows='3'>$notetext</textarea><br>";
					}
					echo "
						<button type='submit' class='btnSaveNote'>Save</button>
						<input type='hidden' name='note' value='".$id."'>
					</form>
				</div>
				";
			}

			$stmt1->close();
			$conn1->close();

		echo "
  				<div class='container note'>
				<form action='?status=checkAddNote' method='post'>
					<label for='addNote'>Add Note</label><br>
					<input type='text' name='addNote' required>
					<button type='submit' class='btnAddNote'>Add</button>
					<input type='hidden' name='notebook' value='".$id_notebook."'>
				</form>
				</div>
				<br><hr>
		";

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
		header("Location: note.php");
	}

}

function updateNote(){

	$pdo = new PDO('mysql:host=localhost;dbname=id7650771_schulmanager', 'id7650771_phpuser', 'phpUser123#');

	$statement = $pdo->prepare("UPDATE note SET notetext = ? WHERE id = ?");
	$statement->execute(array(htmlspecialchars(trim($_POST['txtNote'])), $_POST['note']));

	header("Location: note.php");
}

function checkAddNote() {
	$success = true;
	$note = htmlspecialchars(trim($_POST['addNote']));

	if (empty($note)) {
      $success = false;
  }

  /*
  Check if Note with the same title already in the notbook
	*/

	// get connection
	$conn = getConnection();
	
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
		header("Location: note.php");
	}

}

function insertNote(){

	// get connection
	$conn = getConnection();

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

  header("Location: note.php");
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
		header("Location: note.php");
	}

}

function insertNotebook() {

	// get connection
	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	// set parameters and execute
	$name = htmlspecialchars(trim($_POST['addNotebook']));

	// prepare and bind
	$stmt = $conn->prepare("INSERT INTO notebook (name, user_id_fk) VALUES (?, ?)");
	$stmt->bind_param("si", $name, $_SESSION["user"]);


	$stmt->execute();

	$stmt->close();
	$conn->close();


	header('Location: note.php');
}

function deleteNotebook(){

	// get connection
	$conn = getConnection();
	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
	}
	$notebook = htmlspecialchars(trim($_POST['notebook']));

	$delNote = $conn->prepare("DELETE FROM note WHERE notebook_id_fk=?");
  $delNote->bind_param("s", $notebook);

	if ($delNote->execute()) {
	}
	$delNote->close();

	$delNotebook = $conn->prepare("DELETE FROM notebook WHERE id=?");
	$delNotebook->bind_param("s", $notebook);

	if ($delNotebook->execute()) {
	}

	$delNotebook->close();
	$conn->close();

	header("Location: note.php");
}

function deleteNote(){


	// get connection
	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
	}

	$note = htmlspecialchars(trim($_POST['note']));
	// stmt to delete the note
	$delNote = $conn->prepare("DELETE FROM note WHERE id=?");
  $delNote->bind_param("s", $note);

	if ($delNote->execute()) {
	}

	$delNote->close();
	$conn->close();

	header('Location: note.php');
}

function jumpToHome() {

	header('Location: index.php');

}

?>
