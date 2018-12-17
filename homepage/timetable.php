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
	case 'deleteTimetable':
		deleteTimetable();
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
		
		<br><br>
		
		<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
			<select class='mdl-textfield__input' name='dayOfWeek' id='addSubjectDayOfWeek'>
				<option></option>
				<option value='mo'>Monday</option>
    			<option value='tu'>Tuesday</option>
    			<option value='we'>Wednesday</option>
    			<option value='th'>Thursday</option>
    			<option value='fr'>Friday</option>
    			<option value='sa'>Saturday</option>
    			<option value='su'>Sunday</option>
  			</select>	
			<label class='mdl-textfield__label' for='addSubjectDayOfWeek'>DayOfWeek</label>
		</div>
		
		<div class='mdl-grid'>
  			<div class='mdl-cell mdl-cell--3-col'>
				<label for='addSubjectStartAt'>StartAt</label><br>
				<input type='time' name='startAt' id='addSubjectStartAt' value='".htmlspecialchars($_POST['startAt'])."'>
			</div>
  			<div class='mdl-cell mdl-cell--3-col'>
				<label for='addSubjectEndAt'>EndAt</label><br>
				<input type='time' name='endAt' id='addSubjectEndAt' value='".htmlspecialchars($_POST['endAt'])."'>
			</div>
			<div class='mdl-cell mdl-cell--3-col'>
				<button class='mdl-button mdl-js-button mdl-button--raised' type='submit'>Add</button>
			</div>
  		</div>
		
		<br>
		
	</form>

	
	
	

							<!-- Simple list -->
						<style>
							.demo-list-item {
							width: 300px;
						}
						</style>

						<ul class='demo-list-item mdl-list'>
						
						
  							<li class='mdl-list__item' style='background-color: #03DAC6;'>
    							<span class='mdl-list__item-primary-content'>
      								Monday
    							</span>
  							</li>
      					";
	
						showDay("mo");	
						
						echo "
							<li class='mdl-list__item' style='background-color: #03DAC6;'>
    							<span class='mdl-list__item-primary-content'>
      								Tuesday
    							</span>
  							</li>
      					";
	
						showDay("tu");	
						
						echo "
							<li class='mdl-list__item' style='background-color: #03DAC6;'>
    							<span class='mdl-list__item-primary-content'>
      								Wednesday
    							</span>
  							</li>
      					";
	
						showDay("we");	
						
						echo "
							<li class='mdl-list__item' style='background-color: #03DAC6;'>
    							<span class='mdl-list__item-primary-content'>
      								Thursday
    							</span>
  							</li>
      					";
	
						showDay("th");	
						
						echo "
							<li class='mdl-list__item' style='background-color: #03DAC6;'>
    							<span class='mdl-list__item-primary-content'>
      								Friday
    							</span>
  							</li>
      					";
	
						showDay("fr");	
						
						echo "
							<li class='mdl-list__item' style='background-color: #03DAC6;'>
    							<span class='mdl-list__item-primary-content'>
      								Saturday
    							</span>
  							</li>
      					";
	
						showDay("sa");	
						
						echo "
							<li class='mdl-list__item' style='background-color: #03DAC6;'>
    							<span class='mdl-list__item-primary-content'>
      								Sunday
    							</span>
  							</li>
      					";
	
						showDay("su");	
						
						echo "
						</ul>
						
						
						
						


							

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
		$stmt = $conn->prepare("SELECT id FROM timetable WHERE user_id_fk=? AND day_of_week=? AND ((? BETWEEN start_at AND end_at) OR (? BETWEEN start_at AND end_at) OR (start_at BETWEEN ? AND ?) OR (end_at BETWEEN ? AND ?) OR start_at=? OR start_at=? OR end_at=? OR end_at=?)");
		$stmt->bind_param("isssssssssss", $_SESSION["user"], $dayOfWeek, $startAt, $endAt, $startAt, $endAt, $startAt, $endAt, $startAt, $endAt, $startAt, $endAt);
		
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

function deleteTimetable(){
		
	$conn = getConnection();

	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);

	}
	
	$subjectfk = htmlspecialchars(trim($_POST['deleteButton']));
	
	$delTimetable = $conn->prepare("DELETE FROM timetable WHERE user_id_fk=? AND subject_id_fk=?");
	$delTimetable->bind_param("ii", $_SESSION["user"], $subjectfk);

	if (!$delTimetable->execute()) {
		//ECHO FAIL
	}

	$delTimetable->close();
	$conn->close();
	
	showTimetable();
	
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
		echo "   until   ";
		echo substr($end_at, 0, 5);
		echo "<form action='?status=deleteTimetable' method='post'>
				<!-- Accent-colored flat button -->
			  	<button name='deleteButton' value='".$subject_id_fk."' class='mdl-button mdl-js-button mdl-button--accent'>
  					Delete
			  	</button>
			  </form>
		";
		echo "<hr>";
	}
	
	echo "<br>";
	
	$stmt->close();
	$conn->close();
	
}

function jumpToHome() {
	
	header('Location: index.php'); 
	
}

?>