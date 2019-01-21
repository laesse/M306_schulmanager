<?php

// start session
session_start();

// hide errors
//error_reporting(0);
//ini_set('display_errors', 0);

if(!isset($_SESSION['user'])){
  header("Location: index.php");
}

switch(@$_GET['status'])
{
  case 'addSemester':
    addSemester();
  break;
  case 'delSemester':
    delSemester();
  break;
  case 'logout':
    logout();
  break;
  case 'addMark':
    addMark();
  break;
  case 'deleteMark':
    deleteMark();
  break;
  case 'editMark':
    updateMark();
  break;
  case 'viewMarks':
    viewMark();
    showMarks();
  break;
  default:
    showMarks();
  break;
}

function getConnection(){
  $ini = parse_ini_file('../config/db.ini');

  $servername = $ini["servername"];
  $dbname = $ini["db_name"];
  $dbusername = $ini["db_user"];
  $password = $ini["db_password"];
  // return new mysqli connection
  return new mysqli($servername, $dbusername, $password, $dbname);
}

function showMarks() {

	echo "<!DOCTYPE html>
  	<html>
  	<head>
		<title>LeeSchoolassist: Mark</title>
		<meta name='theme-color' content='pink'>
		<link rel='stylesheet' type='text/css' href='mark.css'>
		<link rel='shortcut icon' href='favicon.png' type='image/x-icon'/>
		<meta name='viewport' content='width=device-width, initial-scale=1.0' />
  	</head>
  	<body>

		<div class='divEdit'>

			<h1>ADD SEMESTER</h1><br>
			<p>Helps us to also remeber old marks.</p><br><br>

			<form action='?status=addSemester' method='post'>

				  <h2>Semestername</h2><br>
                  <input class='inputSemester' type='text' id='semesterName' name='semesterName'><br>
				   <h2>Start</h2><br>
                  <input class='inputSemester' type='text' pattern= '^\s*(3[01]|[12][0-9]|0?[1-9])\.(1[012]|0?[1-9])\.((?:19|20)\d{2})\s*$' id='dateFrom' name='dateFrom'><br>
                  <h2>End</h2><br>
				  <input class='inputSemester' type='text' pattern= '^\s*(3[01]|[12][0-9]|0?[1-9])\.(1[012]|0?[1-9])\.((?:19|20)\d{2})\s*$' id='dateTo' name='dateTo'><br>
                  <button class='btnLogin' type='submit'>Add</button><br><br><br>
            </form>

		";

		readSemester(@$_SESSION["semester"]);

		echo "
		</div>
		<div class='divContent'>
		";




    $conn2 = getConnection();
    if ($conn2->connect_error){
      die("Connection failed: ".$conn2->connect_error);
    }
    $max_mark_cnt = $conn2->prepare("SELECT  max(mrk.cnt) cnt
                                      FROM  (
                                       SELECT  m.subject_id_fk, COUNT(1) cnt
                                        FROM   mark m
                                        WHERE  m.semester_id_fk = ?
                                        GROUP  BY m.subject_id_fk
                                      )  mrk"
                                        );
    $max_mark_cnt->bind_param("i",$_SESSION["semester"]);
    if($max_mark_cnt->execute()){
      //TODO write error
    }
    $max_mark_cnt->bind_result($cnt);
    if($max_mark_cnt->fetch()){
      if($cnt > 0){
        for ($i = 1; $i <= intval($cnt); $i++) {

        }
      }else{
		  
		  if(isset($_SESSION['semester'])){
  		  	echo "<h2>NO MARKS ADDED YET.</h2>";
		  }
		  
        $cnt = 0;
      }
    }

    $max_mark_cnt->close();
    $conn2->close();

    $conn2 = getConnection();

    // weiter machen wenn semester Noten hat sonst nichts tun
    if ($cnt > 0){
      $marks = $conn2->prepare("SELECT  m.id
                                      , m.mark
                                      , sub.name
                                      , m.subject_id_fk
                                      , COALESCE(tt.test_date,m.added_at) AS MARK_DATE
                                      , mavg.avg_mark
                                  FROM  mark m
                                  JOIN  subject sub
                                    ON  (m.subject_id_fk  = sub.id)
                                  LEFT  OUTER JOIN  (
                                                      SELECT DISTINCT tt.id, tt.`test_date`
                                                          FROM test tt
                                                    ) AS tt
                                    ON  (m.test_id_fk     = tt.id)
                                  JOIN  (
                                          SELECT  avg(m.mark) as avg_mark
                                                , m.subject_id_fk
                                            FROM  mark m
                                           WHERE  m.semester_id_fk = ?
                                           GROUP  BY m.subject_id_fk
                                        ) AS mavg
                                    ON  (m.subject_id_fk  = mavg.subject_id_fk)
                                 WHERE  m.semester_id_fk = ?
                                 ORDER  BY m.subject_id_fk, MARK_DATE
                                           ");
		
      $marks->bind_param("ii", $_SESSION["semester"], $_SESSION["semester"]);
      
	  if($marks->execute()){
        //TODO write error
      }
      $marks->bind_result($mark_id, $mark, $subject_name, $subject_id, $mark_date, $avg_mark);

      // gruppenbruch für subject_id_fk
      $noch_daten_da = $marks->fetch(); // "vorlesen"
      while($noch_daten_da){
        $current_subject_id = $subject_id;
        echo "
        <h2>$subject_name</h2><br>";

		    $i = 0;
        $zw_avg_mark = $avg_mark;
        $zw_subject_id = $subject_id;

        //gruppenbruch Stufe Subject
		    $color = true;
        while ($noch_daten_da && $subject_id == $current_subject_id) {


			      if ($color) {

				          echo "
				  <div class='divWhiteElement'>
				  	<p>".$mark."</p>
				  ";

          $color = false;
         } else {

				  echo "
				  <div class='divBlueElement'>
				  	<p>".$mark."</p>
				  ";
	        $color = true;
         }
         echo "
         <form action='?status=deleteMark' method='post'>
         <input type='hidden' value='".$mark_id."' name='mark_id'/>
           <button name='btnDelete' class='btnRegister'>
             Delete
           </button>
         </form>
       </div>";

          $i += 1;
          $noch_daten_da = $marks->fetch(); // "nachlesen"
        }

        echo "
        <p>AVERAGE: ".$zw_avg_mark."</p><br><br>
		        ";
      }

  	  $marks->close();
    }



	if(isset($_SESSION['semester'])){
  	

  $conn2 = getConnection();
  if ($conn2->connect_error){
    die("Connection failed: ".$conn2->connect_error);
  }
  $subjectZeug = $conn2->prepare("SELECT id,name
                                   FROM subject
                                  WHERE user_id_fk = ?
                                  ORDER BY id DESC
                                ");
  $subjectZeug->bind_param("i",$_SESSION["user"]);
  if($subjectZeug->execute()){
    //TODO write error
  }
  $subjectZeug->bind_result($subject_id,$subject_name);
  
		  
	echo "
				<br><br>
				<form action='?status=addMark' method='post'>
				  	<h3>Subject</h3><br>
                    <select name='subject' id='subjects'>
                      <option value='0'>Please choose</option>";
  while($subjectZeug->fetch()){
    echo "
                      <option value='$subject_id'>$subject_name</option>";
  }
  $subjectZeug->close();
  $conn2->close();

  echo "
                    </select>
                <br>
				<h3>Mark</h3><br>
				<input pattern='-?[0-9]*(\.[0-9]+)?' type='text' id='newMark' name='mark' value='".htmlspecialchars(@$_POST['mark'])."'><br>
                <button class='btnSave' type='submit'>Add</button>
              </form> ";
	}
			echo "
            </div>

       	<div class='divNavigation'>
			<a href='index.php'><img src='img/homeOnWhite.svg'></a>
			<a href='note.php'><img src='img/noteOnWhite.svg'></a>
			<a href='timetable.php'><img src='img/timetableOnWhite.svg'></a>
			<a href='index.php?status=logout'><img src='img/logout.svg'></a>
		</div>

    </body>
  </html>
  ";


}


function viewMark() {
  $_SESSION["semester"] = @$_POST['semester_id'];
}

function readSemester($semester) {

	$conn = getConnection();
  	// Check connection
  	if ($conn->connect_error) {
		die("Connection failed: ".$conn->connect_error);
	}

  	// semesters from the current user for the Tab name
  	$semestersFromAUser = $conn->prepare(
    "SELECT
        id
      , semester_start
      , COALESCE(CONCAT(semester_name,': ',DATE_FORMAT(semester_start,'%d.%m.%Y'),' - ',DATE_FORMAT(semester_end,'%d.%m.%Y')),semester_name)
          AS semester_name
      , CASE
          WHEN semester_start < SYSDATE()
           AND semester_end  >= SYSDATE()
              THEN 1
              ELSE 0
        END
          AS is_current_semester
        FROM semester
        WHERE user_id_fk = ?
          AND definitiv = 0
        UNION ALL
		 SELECT
        id
      , semester_start
      , COALESCE(CONCAT(semester_name,': ',DATE_FORMAT(semester_start,'%d.%m.%Y'),' - ',DATE_FORMAT(semester_end,'%d.%m.%Y')),semester_name)
          AS semester_name
      , CASE
          WHEN semester_start < SYSDATE()
           AND semester_end  >= SYSDATE()
              THEN 1
              ELSE 0
        END
          AS is_current_semester
        FROM semester
        WHERE user_id_fk = ?
          AND definitiv = 1
		      AND semester_start = (SELECT MAX(semester_start) FROM semester WHERE definitiv = 1)
        ORDER BY semester_start DESC
        ");
  $user_id = @$_SESSION["user"];
	$semestersFromAUser->bind_param("ii", $user_id, $user_id);

  	if(!$semestersFromAUser->execute()){
		// TODO: echo Error
  	}

	// bind result variable
  	$semestersFromAUser->bind_result($id_semester,$semester_start, $semester_name, $is_current_semester);

  	// fetch value
  	while ($semestersFromAUser->fetch()) {

		echo "
		<form action='?status=viewMarks' method='post'>
			<input type='hidden' value='$id_semester' name='semester_id'/>
		";
      if(empty($semester)){
      	//activate current semester
      	if ($is_current_semester == 1){
    		echo "
    		<button class='activeSemester' type='submit'>".$semester_name."</button>
    		";
    		$_SESSION["semester"] = $id_semester;
      	} else {
    		echo "
    		<button class='semesters' type='submit'>".$semester_name."</button>
    		";
    	  }
    }else{
      //activate current semester

	if ($semester == $id_semester){
        echo "
        <button class='activeSemester' type='submit'>".$semester_name."</button>
        ";
        $_SESSION["semester"] = $id_semester;
      } else {
        echo "
        <button class='semesters' type='submit'>".$semester_name."</button>
        ";
      }
    }

		echo "

		</form><br>

		";

		/* TODO: MAYBE IN CONTENT
		<form action='?status=delSemester' method='post' style='display:inline-block'>
			<input type='hidden' value='$id_semester' name='semester_id'/>
			<button type='submit'>Delete</button>
		</form>
		*/
  	}

	$semestersFromAUser->close();
	$conn->close();

}




function addMark(){
    if(checkAddMark()){
        $conn = getConnection();
        $mark = htmlspecialchars(trim(@$_POST['mark']));

        if ($conn->connect_error){
          die("Connection failed: ".$conn->connect_error);
        }
        $insertMark = $conn->prepare("INSERT INTO mark(mark,test_id_fk,semester_id_fk,subject_id_fk)
                                                 VALUES(?,NULL,?,?)
                                      ");
        $insertMark->bind_param("dii",$mark,$_SESSION["semester"],$_POST["subject"]);
        if($insertMark->execute()){
          //TODO write error
        }
        $insertMark->close();
        $conn->close();
        unset($_POST['mark']);
        unset($_POST['semester_id']);
        unset($_POST['subject']);

        showMarks();
    }else{
        showMarks();
    }
}

function checkAddMark() {

	$success = true;
	$subject_id = htmlspecialchars(trim(@$_POST['subject']));
	$new_mark = htmlspecialchars(trim(@$_POST['mark']));

	/*
  check if parameters are empty
	*/
  if (empty($subject_id)) {
    $success = false;
  }
  if (empty($new_mark)) {
    $success = false;
  }
  //default value
  if ($subject_id == 0) {
    $success = false;
  }

  return $success;

}

function updateMark(){
  if(checkUpdateMark()){
      $conn = getConnection();
      $mark = htmlspecialchars(trim(@$_GET['new_mark']));

      if ($conn->connect_error){
        die("Connection failed: ".$conn->connect_error);
      }
      $updateMark = $conn->prepare("UPDATE  MARK
                                       SET  MARK = ?
                                           ,ADDED_AT = ADDED_AT
                                     WHERE  ID = ?
                                    ");
      $updateMark->bind_param("di",$mark,@$_GET["mark_id"]);
      if($updateMark->execute()){
        //TODO write error
      }
      $updateMark->close();
      $conn->close();

      header("Location: mark.php");
  }else{
      header("Location: mark.php");
  }
}
function checkUpdateMark(){

  	$success = true;
  	$mark_id = htmlspecialchars(trim(@$_GET['mark_id']));
  	$new_mark = htmlspecialchars(trim(@$_GET['new_mark']));
  	$user_id = htmlspecialchars(trim(@$_GET['user_id']));

  	/*
    check if parameters are empty
  	*/
    if (empty($mark_id)) {
      $success = false;
    }
    if (empty($new_mark)) {
      $success = false;
    }
    if (empty($user_id)) {
      $success = false;
    }


    $conn = getConnection();

    if ($conn->connect_error){
      die("Connection failed: ".$conn->connect_error);
    }
    $checkUserId = $conn->prepare("SELECT CONCAT(id,LOWER(username),'éêèáãà,3.14159,-1/12',id*id*3.14159,UPPER(password_hash)) user_id_soll
                                    FROM  user
                                   WHERE  id = ?
                                  ");
    $checkUserId->bind_param("i",$_SESSION["user"]);
    if($checkUserId->execute()){
      //TODO write error
    }
    $checkUserId->bind_result($user_id_soll);
    if($checkUserId->fetch()){
      //autencication
        if(strtoupper(hash("sha256",hash("sha512",$user_id_soll.hash("sha512","ifYouGetThisYouMustAreAVeryGoodHacker</>")))) != strtoupper($user_id)){
            $success = false;
            // wrong user
        }
    }

    $checkUserId->close();
    $conn->close();

    return $success;
}

function checkDeleteMark(){

  	$success = true;
  	$mark_id = htmlspecialchars(trim(@$_POST['mark_id']));

  	/*
    check if parameters are empty
  	*/
    if (empty($mark_id)) {
      $success = false;
    }
    return $success;
}

function deleteMark(){
  if(checkDeleteMark()){
      $conn = getConnection();
      $mark = htmlspecialchars(trim(@$_POST['mark_id']));

      if ($conn->connect_error){
        die("Connection failed: ".$conn->connect_error);
      }
      $deleteMark = $conn->prepare("DELETE FROM mark WHERE id = ?");
      $deleteMark->bind_param("i",$mark);
      if($deleteMark->execute()){
      }
      $deleteMark->close();
      $conn->close();
      showMarks();
  }else{
      showMarks();
  }
}

function addSemester(){

	//TODO KEINE DOPPELEINTRÄGE

  if(checkAddSemester()){
      $conn = getConnection();
      $semesterName = htmlspecialchars(trim(@$_POST['semesterName']));
      $dateFrom = htmlspecialchars(trim(@$_POST['dateFrom']));
      $dateTo = htmlspecialchars(trim(@$_POST['dateTo']));

      if ($conn->connect_error){
        die("Connection failed: ".$conn->connect_error);
      }
      $insertSem = $conn->prepare("INSERT INTO semester(semester_start,semester_end,semester_name,user_id_fk,definitiv)
                                               VALUES(STR_TO_DATE(?,'%d.%m.%Y'),STR_TO_DATE(?,'%d.%m.%Y'),?,?,0)
                                    ");
      $insertSem->bind_param("sssi",$dateFrom,$dateTo,$semesterName,$_SESSION['user']);
      if($insertSem->execute()){
        //TODO write error
      }
      $insertSem->close();
      $conn->close();
      unset($_POST['semesterName']);
      unset($_POST['dateFrom']);
      unset($_POST['dateTo']);

	  showMarks();
  }else{
    //TODO Fehlermeldung
	  showMarks();
  }
}

function checkAddSemester(){
      	$success = true;
      	$semesterName = htmlspecialchars(trim(@$_POST['semesterName']));
      	$dateFrom = htmlspecialchars(trim(@$_POST['dateFrom']));
      	$dateTo = htmlspecialchars(trim(@$_POST['dateTo']));

      	/*
        check if parameters are empty
      	*/
        if (empty($semesterName)) {
          $success = false;
        }
        if (empty($dateFrom)) {
          $success = false;
        }
        if (empty($dateTo)) {
          $success = false;
        }

        $dateFrom = convertToIso($dateFrom);
        $dateTo = convertToIso($dateTo);

        if($dateFrom >= $dateTo){
          $success = false;
        }
        // das alles funktioniert noch nicht so wie ich es will es ist aber auch nicht zwingend notwendeig desshab
        // lasse ich es jetzt einfach drin und wenn man das bedürfnis verspüren sollte hier noch was zu machen dann go for it.

        return $success;

}

function convertToIso($inStrDate){
  $day = substr($inStrDate,0,2);
  $month = substr($inStrDate,3,2);
  $year = substr($inStrDate,5,4);
  return $year.'-'.$month.'-'.$day;
}

function delSemester(){
  if(checkDelSemester()){
      $conn = getConnection();
      $semester_id = htmlspecialchars(trim(@$_POST['semester_id']));

      if ($conn->connect_error){
        die("Connection failed: ".$conn->connect_error);
      }
      $updateMark = $conn->prepare("DELETE FROM semester WHERE id = ?");
      $updateMark->bind_param("i",$semester_id);
      if($updateMark->execute()){
        //TODO write error
      }
      $updateMark->close();
      $conn->close();

      unset($_POST['semester_id']);
      header("Location: mark.php");
  }else{
      unset($_POST['semester_id']);
      header("Location: mark.php");
  }
}

function checkDelSemester(){

    	$success = true;
    	$semester_id = htmlspecialchars(trim(@$_POST['semester_id']));

    	/*
      check if parameters are empty
    	*/
      if (empty($semester_id)) {
        $success = false;
      }

      return $success;

}

function logout() {

  unset($_SESSION['user']);
  session_destroy();
  header('Location: index.php');

}



 ?>
