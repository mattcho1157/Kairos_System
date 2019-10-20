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
	<title>Kairos | Students Profiles</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../kairos.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700|Raleway&display=swap" rel="stylesheet">
	<link rel="icon" href="../img/logo.png">

	<script>
		function showProfile(name) {
			//if input text is empty
			if (name.length == 0) { 
				//clear suggestions & exit function
				document.getElementById('namesearch').innerHTML = '';
				return;
			} else {
				//create XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();
				//function called whenever readyState of request changes
				xmlhttp.onreadystatechange = function() {
					//if server response is ready
					if (this.readyState == 4 && this.status == 200) {
						//readyState = 4 -> request finished and response is ready
						//status = 200 -> returns "OK" for request status
						document.getElementById('namesearch').innerHTML = this.responseText;
					}
				}
				//send request to .php on the server - parameter name is added
				xmlhttp.open('GET', 'namesuggestions.php?name=' + name, true);
				xmlhttp.send();
			}
		}
	</script>
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
				<li><a href="studentprofiles.php" id="current-page">STUDENTS</a></li>
				<li><a href="retreats.php">RETREATS</a></li>
				<li><a href="roll.php">ROLL</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner general-banner">
	<h1>Students</h1>
</div>

<div class="students-tab">
	<a href="studentprofiles.php" id="current-tab">PROFILES</a>
	<a href="studentlist.php">STUDENT LIST</a>
</div>

<?php
if (isset($_POST['savenotes'])) {
	//saving student notes
	$notes = trim(stripslashes(htmlspecialchars($_POST['notes'])));
	alert('info', 'STUDENT NOTES HAVE BEEN SAVED');

	DB::update('students', array(
		'notes' => $notes
	), 'username = %s', $_POST['notesusername']);

}
?>

<div class="page-content">
	<h2>Search Student Profile</h2>
	<form>
		<input type="text" class="form-control searchbar" placeholder="FULL NAME" onkeyup="showProfile(this.value)">
		<div id="namesearch"></div>
	</form>

	<?php
	if (isset($_GET['username'])) {

		//retrieving student profile data from users table
		$usersProfile = DB::queryFirstRow('select username, fname, lname from users where username = %s', $_GET['username']);
		//checking if username is valid
		if (isset($usersProfile)) {
			//retrieving student profile data from students table
			$studentsProfile = DB::queryFirstRow('select house, pc, notes, kid from students where username = %s', $_GET['username']);
			
			echo '
			<table class="studentprofile-table">
				<tr>
					<th>NAME</th>
					<td>'.$usersProfile['fname'].' '.$usersProfile['lname'].'</td>
				</tr>
				<tr>
					<th>S-NUMBER</th>
					<td>'.$usersProfile['username'].'</td>
				</tr>
				<tr>
					<th>HOUSE & PC</th>
					<td>'.$studentsProfile['house'].' '.$studentsProfile['pc'].'</td>
				</tr>
				<tr>
					<th>RETREAT</th>';

			//if student has been allocated a retreat
			if (isset($studentsProfile['kid'])) {
				//retrieving student's retreat name, date from students table
				$retreatData = DB::queryFirstRow('select name, date_format(startdate, "%Y") as year from kairos where kid = '.$studentsProfile['kid']);
				echo '<td>'.$retreatData['year'].' '.$retreatData['name'].'</td>';
			} else {
				echo '<td>Not Allocated</td>';
			}
			echo '
				</tr>
			</table>

			<form name="notesform" method="post" action="studentprofiles.php" class="textarea-form">
				<h3>NOTES</h3>
				<textarea class="form-control" name="notes" maxlength="500">'
				//display notes if it exists, otherwise display empty string
				.(isset($studentsProfile['notes']) ? $studentsProfile['notes'] : '').
				'</textarea>
				<input type="hidden" name="notesusername" value="'.$usersProfile['username'].'">
				<input class="button" type="submit" name="savenotes" value="SAVE NOTES">
			</form>';
		}
	}
	?>
</div>

</body>
</html>