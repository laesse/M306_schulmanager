<?php

// start session
session_start();

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

  default:
    showSemester();
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

function showSemester() {
  echo "
<!DOCTYPE html>
<html>
<head>
  <title>Schulmanager: Semester</title>
";

  include 'head.php';

  echo"
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
              <div class='mdl-cell mdl-cell--8-col'>
                <h3>Manage your Semesters</h3>";

                $conn = getConnection();
                // Check connection
                if ($conn->connect_error) {
                  die("Connection failed: ".$conn->connect_error);
                }

                // semesters from the current user
                $semestersFromAUser = $conn->prepare(
                  "SELECT
                      id
                    , semester_name
                    , DATE_FORMAT(semester_start,'%d.%m.%Y') semester_start
                    , semester_start sem_start
                    , DATE_FORMAT(COALESCE(semester_end,STR_TO_DATE('31.12.9999','%d.%m.%Y')),'%d.%m.%Y') semester_end
                    FROM semester
                      WHERE user_id_fk = ?
                    order by sem_start
                  ");
                $semestersFromAUser->bind_param("i",$_SESSION["user"]);

                if(!$semestersFromAUser->execute()){
                  // TODO: echo Error
                }
                // bind result variable
                $semestersFromAUser->bind_result($id_semester, $semester_name, $semester_start,$sem_start, $semester_end);

                // fetch value
                while ($semestersFromAUser->fetch()) {
                  echo "
                        <div>
                          <span>$semester_name </span>
                          <span>$semester_start</span>
                          <span>$semester_end</span>
                          <span>
                            <form action='?status=delSemester' method='post' style='display:inline-block'>
                              <input type='hidden' value='$id_semester' name='semester_id'/>
                              <button type='submit'>delete</button>
                            </form>
                          </span>
                        </div>";

                }

                $semestersFromAUser->close();
                $conn->close();
  echo"
                <form action='?status=addSemester' method='post'>
                  <div class='mdl-textfield mdl-js-textfield'>
                    <input class='mdl-textfield__input' type='text' id='semesterName' name='semesterName'>
                    <label class='mdl-textfield__label' for='dateFrom'>ex. \"Semester 1\"</label>
                    <span class='mdl-textfield__error'>This is not a date</span>
                  </div>
                  <div class='mdl-textfield mdl-js-textfield'>
                    <input class='mdl-textfield__input' type='text' pattern= '^\s*(3[01]|[12][0-9]|0?[1-9])\.(1[012]|0?[1-9])\.((?:19|20)\d{2})\s*$' id='dateFrom' name='dateFrom'>
                    <label class='mdl-textfield__label' for='dateFrom'>From ex. 01.08.2018</label>
                    <span class='mdl-textfield__error'>This is not a date</span>
                  </div>
                  <div class='mdl-textfield mdl-js-textfield'>
                    <input class='mdl-textfield__input' type='text' pattern= '^\s*(3[01]|[12][0-9]|0?[1-9])\.(1[012]|0?[1-9])\.((?:19|20)\d{2})\s*$' id='dateTo' name='dateTo'>
                    <label class='mdl-textfield__label' for='dateTo'>To ex. 31.01.2019</label>
                    <span class='mdl-textfield__error'>This is not a date</span>
                  </div>
                  <button class='mdl-button mdl-js-button mdl-button--raised' type='submit'>Add</button>
                </form>
              </div>
            <div class='mdl-layout-spacer'></div>
          </div>
        </div>
      </main>
    </div>
  </body>
</html>";

}


function addSemester(){
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

      showSemester();
  }else{
    //TODO Fehlermeldung
    showSemester();
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
/*
        $conn = getConnection();
        // Check connection
        if ($conn->connect_error) {
          die("Connection failed: ".$conn->connect_error);
        }

        // semesters from the current user
        $semestersFromAUser = $conn->prepare(
          "SELECT
              id
            , semester_name
            , semester_start sem_start
            , semester_end sem_end
            FROM semester
              WHERE user_id_fk = ?
            order by sem_start
          ");
        $semestersFromAUser->bind_param("i",$_SESSION["user"]);

        if(!$semestersFromAUser->execute()){
          // TODO: echo Error
        }
        // bind result variable
        $semestersFromAUser->bind_result($id_semester, $semester_name, $semester_start, $semester_end);

        while($semestersFromAUser->fetch()){
          if($semester_start < $dateFrom && $semester_end > $dateFrom){
            $success = false;
          }
          if($semester_start < $dateTo && $semester_end > $dateTo){
            $success = false;
          }
        }

        $semestersFromAUser->close();
        $conn->close();
        */
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
      header("Location: semester.php");
  }else{
      unset($_POST['semester_id']);
      header("Location: semester.php");
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


?>
