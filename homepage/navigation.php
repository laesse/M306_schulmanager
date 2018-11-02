<?php


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
		<a class='mdl-navigation__link' href='index.php?status=logout'>
			<!-- Contact Chip -->
			<span class='mdl-chip mdl-chip--contact'>
    			<span class='mdl-chip__contact mdl-color--teal mdl-color-text--white'>".$_SESSION['username'][0]."</span>
    			<span class='mdl-chip__text'>Logout</span>
			</span>
		</a>
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
		<a class='mdl-navigation__link' href='index.php?status=logout'>
			<!-- Contact Chip -->
			<span class='mdl-chip mdl-chip--contact'>
    			<span class='mdl-chip__contact mdl-color--teal mdl-color-text--white'>".$_SESSION['username'][0]."</span>
    			<span class='mdl-chip__text'>Logout</span>
			</span>
		</a>
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
