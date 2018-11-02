<?php
// start session
session_start();

// hide errors
error_reporting(0);
ini_set('display_errors', 0);

echo "
<header class='mdl-layout__header mdl-layout__header--scroll'>
    			<div class='mdl-layout__header-row'>
      				<!-- Title -->
      				<span class='mdl-layout-title'>Schulmanager</span>
      				<!-- Add spacer, to align navigation to the right -->
      				<div class='mdl-layout-spacer'></div>
      					<!-- Navigation -->
      					<nav class='mdl-navigation'>
	";
	
	
	if (isset($_SESSION['user'])) {
		echo "
        <a class='mdl-navigation__link' href='index.php'>Home</a>
        <a class='mdl-navigation__link' href='note.php'>Note</a>
		<a class='mdl-navigation__link' href='timetable.php'>Timetable</a>
		<a class='mdl-navigation__link' href='index.php?status=logout'>Logout</a>	
		";
	} else {
   		echo "
        <a class='mdl-navigation__link' href='index.php'>Home</a>
		<a class='mdl-navigation__link' href='user.php'>Login</a>
		";
	}      
		
	echo "
	  					</nav>
    				</div>
  				</header>
				<div class='mdl-layout__drawer'>
    				<span class='mdl-layout-title'>Schulmanager</span>
    				<nav class='mdl-navigation'>
    ";
	
	if (isset($_SESSION['user'])) {
		echo "
        <a class='mdl-navigation__link' href='index.php'>Home</a>
        <a class='mdl-navigation__link' href='note.php'>Note</a>
		<a class='mdl-navigation__link' href='timetable.php'>Timetable</a>
		<a class='mdl-navigation__link' href='index.php?status=logout'>Logout</a>	
		";
	} else {
   		echo "
        <a class='mdl-navigation__link' href='index.php'>Home</a>
		<a class='mdl-navigation__link' href='user.php'>Login</a>
		";
	} 
	
	echo "
					</nav>
  				</div>
";
?>