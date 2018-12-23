<?php

// start session
session_start();

if(!isset($_SESSION['user'])){
  header("Location: index.php");
}

switch(@$_GET['status'])
{
  case 'logout':
    logout();
  break;
  case 'addMark':
    addMark();
  break;
  case 'editMarks':
    showMarks('edit');
  break;
  case 'deleteMarks':
    showMarks('del');
  break;
  case 'delMark':
    deleteMark();
  break;
  case 'editMark':
    updateMark();
  break;
  default:
    showMarks('show');
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

function showMarks($parm) {
  echo "<!DOCTYPE html>
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
              <a class='mdl-navigation__link' href='semester.php'>Semester</a>
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
            <a class='mdl-navigation__link' href='semester.php'>Semester</a>
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
          AS semester_name_fake
      , CASE
          WHEN semester_start < SYSDATE()
           AND semester_end  >= SYSDATE()
              THEN 1
              ELSE 0
        END
          AS is_current_semester -- is current SYSDATE in the range of the semester
      , definitiv
      , semester_name
        FROM semester
        WHERE user_id_fk = ?
          AND definitiv = 0
        UNION ALL
		 SELECT
        id
      , semester_start
      , COALESCE(CONCAT(semester_name,': ',DATE_FORMAT(semester_start,'%d.%m.%Y'),' - ',DATE_FORMAT(semester_end,'%d.%m.%Y')),semester_name)
          AS semester_name_fake
      , CASE
          WHEN semester_start < SYSDATE()
           AND semester_end  >= SYSDATE()
              THEN 1
              ELSE 0
        END
          AS is_current_semester
      , definitiv
      , semester_name
        FROM semester
        WHERE user_id_fk = ?
          AND definitiv = 1
		      AND semester_start = (SELECT MAX(semester_start) FROM semester WHERE definitiv = 1) -- Last semester with defininitv = 1
        ORDER BY semester_start
        ");
  $semestersFromAUser->bind_param("ii",$_SESSION["user"], $_SESSION["user"]);

  if($semestersFromAUser->execute()){
    //TODO write error
  }

  $semestersFromAUser->bind_result($id_semester,$semester_start, $semester_name, $is_current_semester, $definitiv,$semester_name_orig);

  while($semestersFromAUser->fetch()){
    echo "
          <section class='mdl-layout__tab-panel ";
    if ($is_current_semester == 1){
      echo "is-active";
    }
    echo"' id='scroll-tab-$id_semester'>
            <div class='page-content'>
            <div class='mdl-grid'>
              <div class='mdl-cell mdl-cell--3-col'></div>
                <div class='mdl-cell mdl-cell--8-col'>";



    $conn2 = getConnection();
    if ($conn2->connect_error){
      die("Connection failed: ".$conn2->connect_error);
    }
    $max_mark_cnt = $conn2->prepare("SELECT  max(m.cnt) cnt
                                      FROM  (
                                       SELECT  m.subject_id_fk, COUNT(1) cnt
                                        FROM   mark m
                                        WHERE  m.semester_id_fk = ?
                                        GROUP  BY m.subject_id_fk
                                            )  m"
                                        );
    $max_mark_cnt->bind_param("i", $id_semester);
    if($max_mark_cnt->execute()){
      //TODO write error
    }
    $max_mark_cnt->bind_result($cnt);
    if($max_mark_cnt->fetch()){
      if($cnt > 0){
        echo "
                  <h3>Your Marks in Semester: \"$semester_name_orig\"</h3>
                  <table class='mdl-data-table mdl-js-data-table'>
                    <thead>
                      <tr>
                        <th class='mdl-data-table__cell--non-numeric'>Subject</th>";

        for ($i = 1; $i <= intval($cnt); $i++) {
          if ($parm == 'del' || $parm == 'edit'){
            echo "
                          <th class='mdl-data-table__cell--non-numeric'>Mark $i</th>";
          }else{
            echo "
                          <th>Mark $i</th>";
          }

        }
        echo "
                        <th class='mdl-data-table__cell--non-numeric'>Avarage Mark</th>
                      </tr>
                    </thead>
                    <tbody>";
      }else{
        echo "
                  <h4>no marks in this semester yet...</h4>";
        $cnt = 0;
      }
    }

    $max_mark_cnt->close();

    // weiter machen wenn semester Noten hat sonst nichts tun
    if ($cnt > 0){
      $marks = $conn2->prepare("SELECT  m.id
                                      , m.mark
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
                                 ORDER  BY m.subject_id_fk, MARK_DATE
                                           ");
      $marks->bind_param("ii", $id_semester, $id_semester);
      if($marks->execute()){
        //TODO write error
      }
      $marks->bind_result($mark_id, $mark, $subject_name, $subject_id, $mark_date, $avg_mark);

      // gruppenbruch für subject_id_fk
      $noch_daten_da = $marks->fetch(); // "vorlesen"
      while($noch_daten_da){
        $current_subject_id = $subject_id;
        echo "
                      <tr>
                        <td class='mdl-data-table__cell--non-numeric'>$subject_name</td>";
        $i = 0;
        $zw_avg_mark = $avg_mark;
        $zw_subject_id = $subject_id;
        //gruppenbruch Stufe Subject
        while ($noch_daten_da && $subject_id == $current_subject_id) {
          if ($parm == 'del'){

            echo"
                      <td class='mdl-data-table__cell--non-numeric'>
                      <span class='mdl-chip mdl-chip--contact mdl-chip--deletable'>
                        <span class='mdl-chip__text'>$mark</span>
                        <a href='?status=delMark&mark_id=$mark_id&user_id=".hash("sha256", hash("sha512",$_SESSION["user"].strtolower($_SESSION["username"]).'éêèáãà,3.14159,-1/12'.($_SESSION["user"]*$_SESSION["user"]*3.14159).$_SESSION["password_hash"].hash("sha512","ifYouGetThisYouMustAreAVeryGoodHacker</>")))."' class='mdl-chip__action'><i class='material-icons'>cancel</i></a>
                      </span>
                      </td>";
          }else if($parm == 'edit'){
            echo"
                      <td class='mdl-data-table__cell--non-numeric'>
                        <form action='?status=editMark' method='post'>
                          <input type='hidden' name='mark_id' value='$mark_id'>

                          <input class='mdl-textfield__input' pattern='-?[0-9]*(\.[0-9]+)?' type='text' name='mark' id='newMark$mark_id' value='$mark'>



                          <button type='button' onclick='var newMark = document.getElementById(\"newMark$mark_id\").value;
                          window.location.href = \"?status=editMark&mark_id=$mark_id&new_mark=\"+newMark+\"&user_id="
                          .hash("sha256", hash("sha512",$_SESSION["user"].strtolower($_SESSION["username"]).'éêèáãà,3.14159,-1/12'.($_SESSION["user"]*$_SESSION["user"]*3.14159).$_SESSION["password_hash"].hash("sha512","ifYouGetThisYouMustAreAVeryGoodHacker</>")))
                          ."\" ;' type='submit' class='mdl-button mdl-js-button mdl-button--icon'>
                            <i class='material-icons'>done</i>
                          </button>
                        </form>
                      </td>";
          }else{
            echo"
                      <td>$mark</td>";

          }
          $i += 1;
          $noch_daten_da = $marks->fetch(); // "nachlesen"
        }
        while ($i < intval($cnt)){
          // Restliche Tabellenfelder ausgeben für Fächer die weniger Noten haben als welches mit den meisten noten im Semester
          echo"
                      <td></td>";
          $i += 1;
        }
        echo "
                      <td>$zw_avg_mark</td>

                    </tr>";
      }
      echo "
                  </tbody>
                </table>";
      $marks->close();
    }

    $conn2->close();
    echo"

              </div>
              <div class='mdl-cell mdl-cell--2-col'></div>
            </div>
            <div class='mdl-grid'>
              <div class='mdl-cell mdl-cell--4-col'></div>
              <div class='mdl-cell mdl-cell--2-col'>";




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
                <form action='?status=addMark' method='post'>
                  <div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
                    <select class='mdl-textfield__input' name='subject' id='subjects'>
                      <option value='0'>Please choose</option>";
  while($subjectZeug->fetch()){
    echo "
                      <option value='$subject_id'>$subject_name</option>";
  }
  $subjectZeug->close();
  $conn2->close();

  echo "
                    </select>
                  <label class='mdl-textfield__label' for='subjects'>Subject</label>
                </div>
                <br>
                <div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>
                  <input class='mdl-textfield__input' pattern='-?[0-9]*(\.[0-9]+)?' type='text' id='sample3' name='mark' value='".htmlspecialchars(@$_POST['mark'])."'>
                  <label class='mdl-textfield__label' for='sample3'>Mark</label>
                  <span class='mdl-textfield__error'>Input is not a number!</span>
                </div>
                <input type='hidden' name='semester_id' value='$id_semester'>
                <br>
                <button class='mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored' type='submit'>
                  <i class='material-icons'>add</i>
                </button>
              </form>

            </div>
            <div class='mdl-cell mdl-cell--4-col'>";
    if ($parm == 'del' || $parm == 'edit'){
      echo"
              <form action='mark.php' method='post'>
                <button class='mdl-button mdl-js-button mdl-button--icon' type='submit'>
                  <i class='material-icons'>exit_to_app</i>
                </button>
              </form>";
    }else{
      echo"
              <form action='?status=editMarks' method='post'>
                <button class='mdl-button mdl-js-button mdl-button--icon' type='submit'>
                  <i class='material-icons'>edit</i>
                </button>
              </form>

              <br>

              <form action='?status=deleteMarks' method='post'>
                <button class='mdl-button mdl-js-button mdl-button--icon' type='submit'>
                  <i class='material-icons'>delete</i>
                </button>
              </form>";
    }
    echo"
            </div>
          </div>
        </div>
        </section>";
  }
  $semestersFromAUser->close();
  $conn->close();
  echo"
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
        $insertMark->bind_param("dii",$mark,$_POST["semester_id"],$_POST["subject"]);
        if($insertMark->execute()){
          //TODO write error
        }
        $insertMark->close();
        $conn->close();
        unset($_POST['mark']);
        unset($_POST['semester_id']);
        unset($_POST['subject']);

        showMarks('show');
    }else{
        showMarks('show');
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
  	$mark_id = htmlspecialchars(trim(@$_GET['mark_id']));
  	$user_id = htmlspecialchars(trim(@$_GET['user_id']));

  	/*
    check if parameters are empty
  	*/
    if (empty($mark_id)) {
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
        }
    }

    $checkUserId->close();
    $conn->close();


    return $success;
}

function deleteMark(){
  if(checkDeleteMark()){
      $conn = getConnection();
      $mark = htmlspecialchars(trim(@$_POST['mark']));

      if ($conn->connect_error){
        die("Connection failed: ".$conn->connect_error);
      }
      $updateMark = $conn->prepare("DELETE FROM mark WHERE id = ?");
      $updateMark->bind_param("i",$_GET["mark_id"]);
      if($updateMark->execute()){
        //TODO write error
      }
      $updateMark->close();
      $conn->close();

      unset($_GET['status']);
      unset($_GET['mark_id']);
      unset($_GET['user_id']);
      header("Location: mark.php");
  }else{
      unset($_GET['status']);
      unset($_GET['mark_id']);
      unset($_GET['user_id']);
      header("Location: mark.php");
  }
}

function logout() {

  unset($_SESSION['user']);
  session_destroy();
  header('Location: index.php');

}



 ?>
