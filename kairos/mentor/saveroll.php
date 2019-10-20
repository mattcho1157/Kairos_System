<?php
session_start();
require('../globalfuncs.php');
checklogout();
connectDB();

if (!isset($_SESSION['usertype'])) {
	header('Location: http://localhost/fia2/index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Kairos | Roll</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../kairos.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700|Raleway&display=swap" rel="stylesheet">
	<link rel="icon" href="../img/logo.png">
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="../"><span><img src="../img/logo.png"></span>KAIROS</a>
		</div>

		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<li><a href="../info.php">INFO</a></li>
				<li><a href="studentprofiles.php">STUDENTS</a></li>
				<li><a href="retreats.php">RETREATS</a></li>
				<li><a href="roll.php" id="current-page">ROLL</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="page-content saveroll-content">
	<a href="roll.php" class="button">BACK</a>
	<?php
	//if new roll has been added
	if (isset($_POST['addroll'])) {
		$rid = 'null';
		$kid = $_POST['kid'];

		$kairos = $_POST['kairos'];
		$rollname = '';
		//students attending this retreat
		$retreatStudents = DB::query('select username, concat(fname, " ", lname) as name from users where username in (select username from students where kid = %s) order by lname', $kid);
		//all students are initially set as absent
		$absentStudents = array_column($retreatStudents, 'username');
	//if existing roll has been edited
	} else if (isset($_POST['editroll'])) {
		$rid = $_POST['rid'];
		$kid = $_POST['kid'];

		$kairos = DB::queryFirstRow('select name from kairos where kid = %s', $kid)['name'];
		$rollname = $_POST['rollname'];
		//students attending this retreat
		$retreatStudents = DB::query('select username, concat(fname, " ", lname) as name from users where username in (select username from students where kid = %s) order by lname', $kid);
		//students previously marked as absent
		$absentStudents = array_column(DB::query('select username from absences where rid = %s', $rid), 'username');
	} else {
		header('Location: roll.php');
		exit();
	}
	?>
	<form name="saverollform" method="post" action="roll.php">
		<h2><?php echo $kairos; ?> ROLL</h2>
		<input class="form-control rollname" type="text" name="rollname" placeholder="ROLL NAME" value="<?php echo $rollname; ?>" required>
		<h3>SELECT PRESENT STUDENTS:</h3>
		<div class="rollcheckbox">
			<ul> 
			<?php
			foreach ($retreatStudents as $student) {
				if (in_array($student['username'], $absentStudents)) {
					//if student is/was absent
					echo '
					<li><label><input type="checkbox" name="presentStudents[]" value="'.$student['username'].'"> &nbsp;'.$student['name'].'</label></li>';
				} else {
					echo '
					<li><label><input type="checkbox" name="presentStudents[]" value="'.$student['username'].'" checked> '.$student['name'].'</label></li>';
				}
			}
			?>
			</ul>
		</div>
		<input type="hidden" name="rid" value="<?php echo $rid; ?>">
		<input type="hidden" name="kid" value="<?php echo $kid; ?>">
		<input class="button" type="submit" name="submitroll" value="SAVE ROLL">
	</form>
</div>

</body>
</html>