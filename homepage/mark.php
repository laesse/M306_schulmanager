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

  $sql

  // prepare and bind
  $semestersFromAUser = $conn->prepare("SELECT id, COALESCE(CONCAT(semester_name,': ',DATE_FORMAT(semester_start,'%d.%m.%Y'),' - ',DATE_FORMAT(semester_end,'%d.%m.%Y')),semester_name) as semester_name FROM semester WHERE user_id_fk=?");
  $semestersFromAUser->bind_param("i",$_SESSION["user"]);

  if(!$semestersFromAUser->execute()){
    // TODO: echo Error
  }
  // bind result variable
  $semestersFromAUser->bind_result($id_semester, $semester_name);

  echo "<a href='#scroll-tab-0' class='mdl-layout__tab is-active'>Add Notebook</a>
            ";
  // fetch value
  while ($semestersFromAUser->fetch()) {

    echo "<a href='#scroll-tab-".$id_semester."' class='mdl-layout__tab'>".$semester_name."</a>
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
        </div>";

  echo "
        <main class='mdl-layout__content'>
          <div class='page-content'>

          </div>
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
