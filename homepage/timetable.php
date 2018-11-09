<?php

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

switch($_GET['status'])
{
	case 'checkAddSubject':
		checkAddSubject();
	break;
	case 'jumpToHome':
		jumpToHome();
	break;
	default:
		showTimetable();
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

function showTimetable() {
	
	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>Schulmanager: Timetable</title>
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
	
	
	
	
	
	<h3>Add Subject</h3>
	<p>Manager your week here.</p>
	
	<form action='?status=checkAddSubject' method='post'>
		
		<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
    		<input class='mdl-textfield__input' type='text' id='sample3' name='name' value='".htmlspecialchars($_POST['name'])."'>
    		<label class='mdl-textfield__label' for='sample3'>SubjectName</label>
  		</div>
		<br>
		<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
    		<input class='mdl-textfield__input' type='text' id='sample3' name='teacherName' value='".htmlspecialchars($_POST['teacherName'])."'>
    		<label class='mdl-textfield__label' for='sample3'>TeacherName</label>
  		</div>
		
		<div class='mdl-grid'>
  			<div class='mdl-cell mdl-cell--3-col'>				
				<label for='addSubjectDayOfWeek'>DayOfWeek</label><br>
				<select name='dayOfWeek' id='addSubjectDayOfWeek'>
					<option></option>
					<option value='mo'>Monday</option>
    				<option value='tu'>Tuesday</option>
    				<option value='we'>Wednesday</option>
    				<option value='th'>Thursday</option>
    				<option value='fr'>Friday</option>
    				<option value='sa'>Saturday</option>
    				<option value='su'>Sunday</option>
  				</select>				
			</div>
  			<div class='mdl-cell mdl-cell--3-col'>
				<label for='addSubjectStartAt'>StartAt</label><br>
				<input type='time' name='startAt' id='addSubjectStartAt' value='".htmlspecialchars($_POST['startAt'])."'>
			</div>
  			<div class='mdl-cell mdl-cell--3-col'>
				<label for='addSubjectEndAt'>EndAt</label><br>
				<input type='time' name='endAt' id='addSubjectEndAt' value='".htmlspecialchars($_POST['endAt'])."'>
			</div>
  		</div>
		
		<button class='mdl-button mdl-js-button mdl-button--raised' type='submit'>Add</button>
		
		<br><br><br>
		
	</form>
	
	
	


	<div class='mdl-tabs mdl-js-tabs mdl-js-ripple-effect'>
  <div class='mdl-tabs__tab-bar'>
      <a href='#monday-panel' class='mdl-tabs__tab is-active'>Monday</a>
      <a href='#tuesday-panel' class='mdl-tabs__tab'>Tuesday</a>
      <a href='#wednesday-panel' class='mdl-tabs__tab'>Wednesday</a>
      <a href='#thursday-panel' class='mdl-tabs__tab'>Thursday</a>
      <a href='#friday-panel' class='mdl-tabs__tab'>Friday</a>
      <a href='#saturday-panel' class='mdl-tabs__tab'>Saturday</a>
      <a href='#sunday-panel' class='mdl-tabs__tab'>Sunday</a>
  </div>
	
  <div class='mdl-tabs__panel is-active' id='monday-panel'>
  	";
	showDay("mo");	
	echo "
  </div>
  <div class='mdl-tabs__panel' id='tuesday-panel'>
  	";
	showDay("tu");
	echo " 
  </div>
  <div class='mdl-tabs__panel' id='wednesday-panel'>
  	";
	showDay("we");
	echo "
  </div>
  <div class='mdl-tabs__panel' id='thursday-panel'>
    ";
	showDay("th");
	echo "
  </div>
  <div class='mdl-tabs__panel' id='friday-panel'>
    ";
	showDay("fr");
	echo "
  </div>
  <div class='mdl-tabs__panel' id='saturday-panel'>
    ";
	showDay("sa");
	echo "
  </div>
  <div class='mdl-tabs__panel' id='sunday-panel'>
    ";
	showDay("su");
	echo "
  </div>
</div>	



							</div>
    						<div class='mdl-layout-spacer'></div>
						</div>



					</div>
  				</main>
			</div>
	
	</body>
	</html>
	";
	
}

function checkAddSubject(){
	
	$success = true;
	
	$name = htmlspecialchars(trim($_POST['name']));
	
	$teacherName = htmlspecialchars(trim($_POST['teacherName']));
	$dayOfWeek = htmlspecialchars(trim($_POST['dayOfWeek']));
	$startAt = htmlspecialchars(trim($_POST['startAt']));
	$endAt = htmlspecialchars(trim($_POST['endAt']));
			
	if (empty($name)) {
        $success = false; 
    }
	
	if (empty($teacherName)) {
        $success = false; 
    }
	
	if (empty($dayOfWeek)) {
        $success = false; 
    }
	
	if (empty($startAt)) {
        $success = false; 
    }
	
	if (empty($endAt)) {
        $success = false;
    }
	
	if ($startAt > $endAt) {
        $success = false;
	}
	
	if ($success) {
		
		// Create connection
		$conn = getConnection();

		// Check connection
		if ($conn->connect_errno) {
			die("Connection failed: ".$conn->connect_error);
		}

		// prepare and bind
		$stmt = $conn->prepare("SELECT id FROM timetable WHERE day_of_week=? AND ((? BETWEEN start_at AND end_at) OR (? BETWEEN start_at AND end_at) OR (start_at BETWEEN ? AND ?) OR (end_at BETWEEN ? AND ?) OR start_at=? OR start_at=? OR end_at=? OR end_at=?)");
		$stmt->bind_param("sssssssssss", $dayOfWeek, $startAt, $endAt, $startAt, $endAt, $startAt, $endAt, $startAt, $endAt, $startAt, $endAt);
		
		$stmt->execute();

		// bind result variable
    	$stmt->bind_result($id);

		// fetch value
		if ($stmt->fetch()) {
			unset($_POST['dayOfWeek']);
			unset($_POST['startAt']);
			unset($_POST['endAt']);
		} else {
			//SUBJECT BEREITS DA?
			checkSubjectExists();
		}

		$stmt->close();
		$conn->close();
		
	}
	
	showTimetable();
		
}

function checkSubjectExists() {

		
		$subjectName = htmlspecialchars(trim($_POST['name']));
	
		// Create connection
		$conn = getConnection();

		// Check connection
		if ($conn->connect_errno) {
			die("Connection failed: ".$conn->connect_error);
		}

		// prepare and bind
		$stmt = $conn->prepare("SELECT id FROM subject WHERE name=?");
		$stmt->bind_param("s", $subjectName);
		
		$stmt->execute();

		// bind result variable
    	$stmt->bind_result($id);

		// fetch value
		if ($stmt->fetch()) {
			insertTimetable();
		} else {
			insertSubject();
		}

		$stmt->close();
		$conn->close();
}

function insertSubject(){
	
	// Create connection
	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	// prepare and bind
	$stmt = $conn->prepare("INSERT INTO subject (name, user_id_fk) VALUES (?, ?)");
	$stmt->bind_param("si", $subjectName, $_SESSION["user"]);

	// set parameters and execute
	$subjectName = htmlspecialchars(trim($_POST['name']));

	$stmt->execute();

	$stmt->close();
	$conn->close();
	
	insertTimetable();
}
	
function insertTimetable() {
	
	// Create connection
	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}
	
	// prepare and bind
	$stmt = $conn->prepare("INSERT INTO timetable (user_id_fk, subject_id_fk, day_of_week, start_at, end_at, teacher_name) VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("iissss", $_SESSION["user"], $subjectId , $dayOfWeek, $startAt, $endAt, $teacherName);

	// set parameters and execute
	$subjectId = getSubjectId();
	$dayOfWeek = htmlspecialchars(trim($_POST['dayOfWeek']));
	$startAt = htmlspecialchars(trim($_POST['startAt'])).':00';
	$endAt = htmlspecialchars(trim($_POST['endAt'])).':00';
	$teacherName = htmlspecialchars(trim($_POST['teacherName']));
	
	$stmt->execute();

	$stmt->close();
	$conn->close();
	
	unset($_POST['name']);
	unset($_POST['teacherName']);
	unset($_POST['dayOfWeek']);
	unset($_POST['startAt']);
	unset($_POST['endAt']);
	
}

function getSubjectId() {
	
	$subjectName = htmlspecialchars(trim($_POST['name']));
	
	// Create connection
	$conn = getConnection();

	// Check connection
	if ($conn->connect_errno) {
		die("Connection failed: ".$conn->connect_error);
	}

	// prepare and bind
	$stmt = $conn->prepare("SELECT id FROM subject WHERE name=?");
	$stmt->bind_param("s",$subjectName);

	$stmt->execute();

	// bind result variable
    $stmt->bind_result($id);

	// fetch value
	$stmt->fetch();

	$stmt->close();
	$conn->close();
	
	return $id;
	
}

function showDay($day_of_week) {
	
	// Create connection
	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

	// prepare and bind
	$stmt = $conn->prepare("SELECT subject_id_fk, teacher_name, start_at, end_at FROM timetable WHERE user_id_fk=? AND day_of_week=? ORDER BY start_at ASC");
	$stmt->bind_param("is",$_SESSION["user"], $day_of_week);

	$stmt->execute();

	// bind result variable
    $stmt->bind_result($subject_id_fk, $teacher_name, $start_at, $end_at);

	// fetch value
	while ($stmt->fetch()) {
		
		// Create connection
		$conn1 = getConnection();

		// Check connection
		if ($conn1->connect_error) {
			die("Connection failed: ".$conn1->connect_error);
		}

		// prepare and bind
		$stmt1 = $conn1->prepare("SELECT name FROM subject WHERE id=?");
		$stmt1->bind_param("i",$subject_id_fk);

		$stmt1->execute();

		// bind result variable
    	$stmt1->bind_result($name);
		
		$stmt1->fetch();
		
		$stmt1->close();
		$conn1->close();
		
		echo "<br>";
		echo $name;
		echo " - ";
		echo $teacher_name;
		echo "<br>";
		echo substr($start_at, 0, 5);
		echo "<br>";
		echo substr($end_at, 0, 5);
		echo "<hr>";
	}
	
	$stmt->close();
	$conn->close();
	
}

function jumpToHome() {
	
	header('Location: index.php'); 
	
}

?>