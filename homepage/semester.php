<?php

// start session
session_start();

if(!isset($_SESSION['user'])){
  header("Location: index.php");
}

switch(@$_GET['status'])
{
  case 'ding':
    ding();
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
                  echo "<p>$semester_name \t $semester_start \t $semester_end
                  </p>";

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
                    <label class='mdl-textfield__label' for='dateFrom'>From ex. 01.06.2018</label>
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





?>
