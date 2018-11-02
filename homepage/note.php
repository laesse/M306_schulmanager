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

	include 'connection.php';


function showNote() {

	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>Schulmanager: Note</title>
	";

	include 'head.php';

	echo "
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

	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	// prepare and bind
	$notebooksFromAUser = $conn->prepare("SELECT id, name FROM notebook WHERE user_id_fk=?");
	$notebooksFromAUser->bind_param("i",$_SESSION["user"]);

	if(!$notebooksFromAUser->execute()){
		// TODO: echo Error
	}
	// bind result variable
  $notebooksFromAUser->bind_result($id_notebook, $name);

	echo "<a href='#scroll-tab-0' class='mdl-layout__tab is-active'>Add Notebook</a>";
	// fetch value
	while ($notebooksFromAUser->fetch()) {

		echo "<a href='#scroll-tab-".$id_notebook."' class='mdl-layout__tab'>".$name."</a>";

	}

	$notebooksFromAUser->close();
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
	<section class='mdl-layout__tab-panel' id='scroll-tab-".$id_notebook."'>
      <div class='page-content'>

	  <div class='mdl-grid'>
			<div class='mdl-layout-spacer'></div>
    		<div class='mdl-cell mdl-cell--4-col'>
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

			$notesInNotebook->close();
			$conn2->close();
	echo "
			</div>
			<div class='mdl-layout-spacer'></div>
		</div>
	  </div>
    </section>
    ";

	}

	$notebooksFromAUser->close();
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
	$conn = getConnection();

  // Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	$updateNotetxt = $conn->prepare("UPDATE note SET notetext = ? WHERE id = ?");
	$updateNotetxt->bind_param("si",htmlspecialchars(trim($_POST['txtNote'])), $_POST['note_id']);

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
	$notebook = htmlspecialchars(trim($_POST['notebook_id']));

	$delNotebook = $conn->prepare("DELETE FROM notebook WHERE id=?");
	$delNotebook->bind_param("s", $notebook);

	if (!$delNotebook->execute()) {
		// TODO: echo Error
	}

	$delNotebook->close();
	$conn->close();

	showNote();
}


?>
