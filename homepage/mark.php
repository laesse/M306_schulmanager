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

  // prepare and bind
  $semestersFromAUser = $conn->prepare("SELECT
        id
      , semester_start
      , semester_end
      , COALESCE(CONCAT(semester_name,': ',DATE_FORMAT(semester_start,'%d.%m.%Y'),' - ',DATE_FORMAT(semester_end,'%d.%m.%Y')),semester_name)
          AS semester_name
      , CASE
          WHEN semester_start < SYSDATE() AND SYSDATE() <= semester_end THEN 1
          ELSE 0
        END AS is_current_semester
        FROM SEMESTER
        WHERE user_id_fk=?");
  $semestersFromAUser->bind_param("i",$_SESSION["user"]);

  if(!$semestersFromAUser->execute()){
    // TODO: echo Error
  }
  // bind result variable
  $semestersFromAUser->bind_result($id_semester,$semester_start,$semester_end, $semester_name, $is_current_semester);

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

  $semestersFromAUser = $conn->prepare("SELECT
      id
    , semester_name
    , semester_start
    , semester_end
    , CASE
        WHEN semester_start < SYSDATE() AND SYSDATE() <= semester_end THEN 1
        ELSE 0
      END AS is_current_semester
    FROM semester
    WHERE user_id_fk = ?");
  $semestersFromAUser->bind_param("i",$_SESSION["user"]);

  if($semestersFromAUser->execute()){
    //TODO write error
  }

  $semestersFromAUser->bind_result($id_semester, $semester_name, $semester_start, $semester_end, $is_current_semester);

  while($semestersFromAUser->fetch()){
    echo "
          <section class='mdl-layout__tab-panel ";
          if ($is_current_semester == 1){
            echo "is-active";
          }
    echo"' id='scroll-tab-$id_semester'>
            <div class='page-content'>";



    echo"
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
