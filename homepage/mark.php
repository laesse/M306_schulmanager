<?php

// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

switch($_GET['status'])
{
  case 'logout':
         logout();
  break;
  default:
    showMarks();
  break;
}

function getConnection(){
  $ini = parse_ini_file('../config/db.ini');

  $servername = $ini["servername"];
  $dbusername = $ini["db_name"];
  $password = $ini["db_user"];
  $dbname = $ini["db_password"];
  // return new mysqli connection
  return new mysqli($servername, $dbusername, $password, $dbname);
}

function showMarks() {
  echo "
  <!DOCTYPE html>
  <html>
  <head>
    <title>Schulmanager: Mark</title>
  ";

  include 'head.php';

  echo "
  </head>
  <body>
    <!-- Uses a header that scrolls with the text, rather than staying locked at the top -->
    <div class='mdl-layout mdl-js-layout'>
    ";

    echo "
    <header class='mdl-layout__header mdl-layout__header--scroll'>
      <div class='mdl-layout__header-row'>
        <!-- Title -->
        <span class='mdl-layout-title'>Schulmanager</span>
          <!-- Add spacer, to align navigation to the right -->
          <div class='mdl-layout-spacer'></div>
            <!-- Navigation -->
            <nav class='mdl-navigation'>";
    if (isset($_SESSION['user'])) {
      echo "
              <a class='mdl-navigation__link' href='index.php'>Home</a>
              <a class='mdl-navigation__link' href='note.php'>Note</a>
              <a class='mdl-navigation__link' href='timetable.php'>Timetable</a>
              <a class='mdl-navigation__link' href='index.php?status=logout'>
                <!-- Contact Chip -->
                <span class='mdl-chip mdl-chip--contact'>
                  <span class='mdl-chip__contact mdl-color--teal mdl-color-text--white'>".$_SESSION['username'][0]."</span>
                  <span class='mdl-chip__text'>Logout</span>
                </span>
              </a>";
    } else {
        echo "
              <a class='mdl-navigation__link' href='index.php'>Home</a>
              <a class='mdl-navigation__link' href='user.php'>Login</a>";
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
        FROM SEMESTER
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
        FROM SEMESTER
        WHERE user_id_fk = ?
          AND definitiv = 1
		      AND semester_start = (SELECT MAX(semester_start) FROM semester WHERE definitiv = 1)
        ORDER BY semester_start
        ");
  $semestersFromAUser->bind_param("ii",$_SESSION["user"], $_SESSION["user"]);

  if(!$semestersFromAUser->execute()){
    // TODO: echo Error
  }
  // bind result variable
  $semestersFromAUser->bind_result($id_semester,$semester_start, $semester_name, $is_current_semester);

  // fetch value
  while ($semestersFromAUser->fetch()) {
    echo "<a href='#scroll-tab-".$id_semester."' class='mdl-layout__tab ";
    //activate current semester
    if ($is_current_semester == 1){
      echo "is-active";
    }
    echo "'>".$semester_name."</a>
            ";

  }

  $semestersFromAUser->close();
  $conn->close();
  echo "</div>
        </header>
        <div class='mdl-layout__drawer'>
          <span class='mdl-layout-title'>Schulmanager</span>
          <nav class='mdl-navigation'>";
  if (isset($_SESSION['user'])) {
    echo "
            <a class='mdl-navigation__link' href='index.php'>Home</a>
            <a class='mdl-navigation__link' href='note.php'>Note</a>
            <a class='mdl-navigation__link' href='timetable.php'>Timetable</a>
            <a class='mdl-navigation__link' href='index.php?status=logout'>
            <!-- Contact Chip -->
              <span class='mdl-chip mdl-chip--contact'>
                <span class='mdl-chip__contact mdl-color--teal mdl-color-text--white'>".$_SESSION['username'][0]."</span>
                <span class='mdl-chip__text'>Logout</span>
              </span>
            </a>";
  } else {
      echo "
             <a class='mdl-navigation__link' href='index.php'>Home</a>
             <a class='mdl-navigation__link' href='user.php'>Login</a>";
  }

  echo "
          </nav>
        </div>
        <main class='mdl-layout__content'>";

  $conn = getConnection();

  if ($conn->connect_error){
      die("Connection failed: ".$conn->connect_error);
  }


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
          AS is_current_semester -- is current SYSDATE in the range of the semester
      , definitiv
        FROM SEMESTER
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
      , definitiv
        FROM SEMESTER
        WHERE user_id_fk = ?
          AND definitiv = 1
		      AND semester_start = (SELECT MAX(semester_start) FROM semester WHERE definitiv = 1) -- Last semester with defininitv = 1
        ORDER BY semester_start
        ");
  $semestersFromAUser->bind_param("ii",$_SESSION["user"], $_SESSION["user"]);

  if($semestersFromAUser->execute()){
    //TODO write error
  }

  $semestersFromAUser->bind_result($id_semester,$semester_start, $semester_name, $is_current_semester, $definitiv);

  while($semestersFromAUser->fetch()){
    echo "
          <section class='mdl-layout__tab-panel ";
          if ($is_current_semester == 1){
            echo "is-active";
          }
    echo"' id='scroll-tab-$id_semester'>
            <div class='page-content'>
              <div class='mdl-layout-spacer'></div>
                <div class='mdl-cell mdl-cell--6-col'>
                  <h3>Your Marks in Semester: $semester_name</h3>
                  <table class='mdl-data-table mdl-js-data-table'>
                    <thead>
                      <tr>
                        <th class='mdl-data-table__cell--non-numeric'>Subject</th>";



            $conn2 = getConnection();

            if ($conn2->connect_error){
                die("Connection failed: ".$conn2->connect_error);
            }
            $max_mark_cnt = $conn2->prepare("SELECT  max(m.cnt) cnt
                                        FROM  (
                                                SELECT  m.subject_id_fk, COUNT(1) cnt
                                                  FROM  mark m
                                                 WHERE  m.semester_id_fk = ?
                                                 GROUP  BY m.subject_id_fk
                                              ) m"
                                            );
            $max_mark_cnt->bind_param("i", $id_semester);
            if($max_mark_cnt->execute()){
              //TODO write error
            }
            $max_mark_cnt->bind_result($cnt);
            if($max_mark_cnt->fetch()){
                for ($i = 1; $i <= intval($cnt); $i++) {
                    echo "
                        <th>Mark $i</th>";
                }
            }
            echo "      <th>Avarage Mark</th>
                      </tr>
                    </thead>
                    <tbody>";
            $max_mark_cnt->close();

            $marks = $conn2->prepare("SELECT  m.mark
                                            , sub.name
                                            , m.subject_id_fk
                                            , COALESCE(tt.date,m.added_at) AS MARK_DATE
                                            , mavg.avg_mark
                                        FROM  mark m
                                        JOIN  subject sub
                                          ON  (m.subject_id_fk  = sub.id)
                                        LEFT  OUTER JOIN  (
                                                            SELECT DISTINCT tt.test_id_fk, tt.`Date`
                                                              FROM test_time tt
                                                          ) AS tt
                                          ON  (m.test_id_fk     = tt.test_id_fk)
                                        JOIN  (
                                                SELECT  avg(m.mark) as avg_mark
                                                      , m.subject_id_fk
                                                  FROM  mark m
                                                 WHERE  m.semester_id_fk = ?
                                                 GROUP  BY m.subject_id_fk
                                              ) AS mavg
                                          ON  (m.subject_id_fk  = mavg.subject_id_fk)
                                       WHERE  m.semester_id_fk = ?
                                         ");
            $marks->bind_param("ii", $id_semester, $id_semester);
            if($marks->execute()){
              //TODO write error
            }
            $marks->bind_result($mark, $subject_name, $subject_id, $mark_date, $avg_mark);

            // gruppenbruch für subject_id_fk
            $noch_daten_da = $marks->fetch(); // "vorlesen"
            while($noch_daten_da){
              $current_subject_id = $subject_id;
              echo "<tr>
                      <td class='mdl-data-table__cell--non-numeric'>$subject_name</td>";
              $i = 0;
              $zw_avg_mark = $avg_mark;
              while ($noch_daten_da && $subject_id == $current_subject_id) {
                echo"
                      <td>$mark</td>";
                $i += 1;
                $noch_daten_da = $marks->fetch(); // "nachlesen"
              }
              while ($i < intval($cnt)){
                echo"
                      <td></td>";
                $i += 1;
              }
              echo "  <td>$zw_avg_mark</td>
                    </tr>";
            }
            echo "
                  </tbody>
                </table>";
            $marks->close();
            $conn2->close();
    echo"
              </div>
              <div class='mdl-layout-spacer'></div>
            </div>
          </section>";
  }
  $semestersFromAUser->close();
  $conn->close();


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

function logout() {

  unset($_SESSION['user']);
  session_destroy();
  header('Location: index.php');

}



 ?>
