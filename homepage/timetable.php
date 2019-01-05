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
		<title>LeeSchoolassist: Timetable</title>
		<meta name='theme-color' content='pink'>
		<link rel='stylesheet' type='text/css' href='timetable.css'>
		<link rel='shortcut icon' href='favicon.png' type='image/x-icon'/>	
		<meta name='viewport' content='width=device-width, initial-scale=1.0' />
	</head>
	<body>
	
		<div class='divEdit'>
		
			<h1>ADD SUBJECT</h1><br>
			<p>Let's manager your week.</p><br>

			<form action='?status=checkAddSubject' method='post'>

				<h2>Subject</h2><br>
				<input type='text' name='name' value='".htmlspecialchars($_POST['name'])."'><br>
				<h2>Teacher</h2><br>
				<input type='text' name='teacherName' value='".htmlspecialchars($_POST['teacherName'])."'><br>
				<h2>Weekday</h2><br>
				<select name='dayOfWeek' id='addSubjectDayOfWeek'>
					<option></option>
					<option value='mo'>Monday</option>
					<option value='tu'>Tuesday</option>
					<option value='we'>Wednesday</option>
					<option value='th'>Thursday</option>
					<option value='fr'>Friday</option>
					<option value='sa'>Saturday</option>
					<option value='su'>Sunday</option>
				</select><br>
				<h2>Start</h2><br>
				<input type='time' name='startAt' id='addSubjectStartAt' value='".htmlspecialchars($_POST['startAt'])."'><br>
				<h2>End</h2><br>
				<input type='time' name='endAt' id='addSubjectEndAt' value='".htmlspecialchars($_POST['endAt'])."'><br>
				<button type='submit' class='btnLogin'>Add</button>

			</form>
		
		</div>
		<div class='divContent'>
		";
		
			showDay("mo", "<h2>MONDAY</h2><br>");	

			showDay("tu", "<br><h2>TUESDAY</h2><br>");	

			showDay("we", "<br><h2>WEDNESDAY</h2><br>");	

			showDay("th", "<br><h2>THURSDAY</h2><br>");	

			showDay("fr", "<br><h2>FRIDAY</h2><br>");	

			showDay("sa", "<br><h2>SATURDAY</h2><br>");	

			showDay("su", "<br><h2>SUNDAY</h2><br>");	

		echo "
		</div>	
		<div class='divNavigation'>
		
			<a href='index.php'><img src='img/homeOnWhite.svg'></a>
			<a href='note.php'><img src='img/noteOnWhite.svg'></a>
			<!--<a href='mark.php'><img src='img/markOnWhite.svg'></a>-->
			<a href='index.php?status=logout'><img src='img/logout.svg'></a>
			
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

function showDay($day_of_week, $output) {

	$div = false;
	$count = 0;
	
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
		
		if ($count == 0) {
			echo $output;
			$count = 99;
		}
		
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
		
		if ($div) {
			echo "<div class='divBlueElement'>";
			
				echo "<div class='left'>";
				echo substr($start_at, 0, 5);
				echo " - ";
				echo substr($end_at, 0, 5);
				echo " | ";
				echo $name;
				echo " - ";
				echo $teacher_name;
				
				echo "</div>";
				echo "<div class='right'>";
				echo "<form action='?status=deleteTimetable' method='post'>
						<button name='deleteButton' value='".$subject_id_fk."' class='btnRegister'>
							Delete
						</button>
					  </form>
					  </div>
				";
			
			echo "</div>";
			$div = false;
		
		} else {
			
			echo "<div class='divWhiteElement'>";
				
				echo "<div class='left'>";
				echo substr($start_at, 0, 5);
				echo " - ";
				echo substr($end_at, 0, 5);
				echo " | ";
				echo $name;
				echo " - ";
				echo $teacher_name;
				
				echo "</div>";
				echo "<div class='right'>";
				echo "<form action='?status=deleteTimetable' method='post'>
						<button name='deleteButton' value='".$subject_id_fk."' class='btnRegister'>
							Delete
						</button>
					  </form>
					  </div>
				";
			
			echo "</div>";			
			$div = true;
		}
		
	}
	
	$stmt->close();
	$conn->close();
	
}

function jumpToHome() {
	
	header('Location: index.php'); 
	
}

?>