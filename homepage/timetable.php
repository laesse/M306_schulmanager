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

function showTimetable() {
	
	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<title>Timetable</title>
	";
	
	include 'head.php';
	
	echo "
	</head>
	<body>
	
	<form action='?status=jumpToHome' method='post'>

		<button type='submit' class='btnHome'>Home</button>
		
	</form>
	
	IDEE: READ ALLER SUBJECT SORTED NACH TIME IN SEPARATEN DAY TABELLEN NEBENEINANDER
	
	ADD SUBJECT
	<div class='container addSubject'>
	<form action='?status=checkAddSubject' method='post'>
		
		<label for='addSubjectName'>Name</label><br>
		<input type='text' name='name' id='addSubjectName' required><br>
		<label for='addSubjectTeacherName'>TeacherName</label><br>
		<input type='text' name='teacherName' id='addSubjectTeacherName' required><br>
		
		<br>
		
		<label for='addSubjectDayOfWeek'>DayOfWeek</label><br>
		<input type='text' name='dayOfWeek' id='addSubjectDayOfWeek' required><br>
		
		<label for='addSubjectStartAt'>StartAt</label><br>
		<input type='text' name='startAt' id='addSubjectStartAt' required><br>
		
		<label for='addSubjectEndAt'>EndAt</label><br>
		<input type='text' name='endAt' id='addSubjectEndAt' required><br>
		
		<br>
		
		<button type='submit' class='btnAddSubject'>Add</button>
		
	</form>
	</div>
	
	</body>
	</html>
	";
	
}

function checkAddSubject(){
	//TODO: ADDS (TIMETABLE: day_of_week,start_at,end_at,teacher_name MIT USER_ID_FK UND SUBJECT_ID_FK) UND SUBJECT (NAME MIT USER_ID_FK)
	echo "check";
	
	$success = true;
	$name = htmlspecialchars(trim($_POST['name']));
	//TODO REST
	
	if (empty($name)) {
        $success = false; 
    }
	
	//TODO: DB - GIBTS DAS SCHON?
	
	if ($success) {
		insertSubject();
	}else {
		showTimetable();
	}
	
}

function insertSubject(){
	//TODO: ADDS (TIMETABLE: day_of_week,start_at,end_at,teacher_name MIT USER_ID_FK UND SUBJECT_ID_FK) UND SUBJECT (NAME MIT USER_ID_FK)
	echo "insert";
	showTimetable();
}

function jumpToHome() {
	
	header('Location: index.php'); 
	
}

?>