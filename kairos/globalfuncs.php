<?php
//connect to database
function connectDB() {
	//import the meekroDB class library
	require_once 'meekrodb.2.3.class.php';

	//database connection details
	DB::$user = 'root';
	DB::$password = '';
	DB::$dbName ='fia2';
}

//alert message pop-up
function alert($type, $msg) {
	echo '
	<div class="alert alert-'.$type.' alert-dismissible fade in">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.
		$msg
	.'</div>';
}

//listen for appended param in url and logout
function checklogout() {
	if (isset($_GET['logout'])) {
		session_destroy();
		session_start();
		$_SESSION['logoutSuccessful'] = true;
		header('Location: http://localhost/fia2/index.php');
		exit();
	}
}

//checking usertype to display corresponding navbar links
function navbarlinks() {
	if (isset($_SESSION['usertype'])) {
		if ($_SESSION['usertype'] == 's') {
			//student navbar links
			echo '
			<li><a href="student/enrol.php">ENROL</a></li>
			<li><a href="student/permission.php">PERMISSION</a></li>';
		} elseif ($_SESSION['usertype'] == 'm') {
			//mentor navbar links
			echo '
			<li><a href="mentor/studentprofiles.php">STUDENTS</a></li>
			<li><a href="mentor/retreats.php">RETREATS</a></li>
			<li><a href="mentor/roll.php">ROLL</a></li>';
		} elseif ($_SESSION['usertype'] == 'a') {
			//admin navbar links
			echo '
			<li><a href="admin/admin.php">ADMIN</a></li>';
		}
		//logout link - append logout param to url
		echo '
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
		</ul>';
	} else {
		//anonymous user
		echo '
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li><a href="login.php"><span class="glyphicon glyphicon-user"></span> LOGIN</a></li>
		</ul>';
	}
}


?>