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
	<title>Kairos | Enrol</title>

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
				<li><a href="enrol.php" id="current-page">ENROL</a></li>
				<li><a href="permission.php">PERMISSION</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner general-banner">
	<h1>Retreat Enrolment</h1> 
</div>

<?php
//retrieving user's preferences data
$userPrefData = DB::query('select kid from preferences where username = %s order by prefnum', $_SESSION['username']);
//preferences form submitted & user has not already submitted preferences
if (isset($_POST['submitpref']) && !$userPrefData) {
	$pref1 = $_POST['pref1'];
	$pref2 = $_POST['pref2'];
	if ($pref1 == $pref2) {
		//if user selected two identical retreats
		alert('danger', 'PLEASE SELECT TWO DIFFERENT RETREATS');
	} else {
		//retrieving kid from retreats table using current year & retreat name
		$currentYear = date('Y');
		$pref1kid = DB::queryFirstRow('select kid from kairos where year(startdate) = %i and name = %s', $currentYear, $pref1)['kid'];
		$pref2kid = DB::queryFirstRow('select kid from kairos where year(startdate) = %i and name = %s', $currentYear, $pref2)['kid'];

		DB::insert('preferences', array(
			'username' => $_SESSION['username'],
			'prefnum' => 1,
			'kid' => $pref1kid
		));

		DB::insert('preferences', array(
			'username' => $_SESSION['username'],
			'prefnum' => 2,
			'kid' => $pref2kid
		));

		alert('info', 'PLEASE DOWNLOAD THE PARENTAL PERMISSION FORM');
	}
}


//for checking if user has submitted the preference-resubmission-request form
$userResubmitpref = DB::queryFirstRow('select resubmitpref from students where username = %s', $_SESSION['username'])['resubmitpref'];
//resubmission-request form submitted & user has not already requested
if (isset($_POST['submitrequest']) && !$userResubmitpref) {
	//student's provided reason for resubmission request
	$reason = $_POST['resubmitreason'];

	//send admin an email (my email address used for testing)
	require('../sendmail.php');
	sendResubmitprefEmail($_SESSION['username'], $reason);

	alert('info', 'YOUR REQUEST HAS BEEN SENT');

	//update students' resubmitpref status to true
	DB::update('students', array(
		'resubmitpref' => 1,
	), 'username = %s', $_SESSION['username']);
}
?>


<div class="page-content">
<?php
//retrieving user's preferences data
$userPrefData = DB::query('select kid from preferences where username = %s order by prefnum', $_SESSION['username']);

if (!$userPrefData) {
	//student has not already submitted preferences form
	echo '
	<form name="prefform" method="post" action="enrol.php" class="enrol-form">
		<h2>Preference Selection</h2>';

	$currentYear = date('Y');
	//retrieve date of retreats that will occur this year
	$dates = DB::query('select date_format(startdate, "%e %b %Y") as start_date from kairos where year(startdate) = '.$currentYear.' order by startdate');
	//if 4 retreats for this year has not been added to table, display no dates
	$dates = (count($dates) != 4 ? ['-', '-', '-', '-'] : [$dates[0]['start_date'], $dates[1]['start_date'], $dates[2]['start_date'], $dates[3]['start_date']]);

	echo '
		<div class="form-group form-select-group">
			<label for="pref1">1<sup>st</sup> Preference</label>
			<select name="pref1" class="form-control" id="pref1">
				<option value="K1" selected>K1 ('.$dates[0].')</option>
				<option value="K2">K2 ('.$dates[1].')</option>
				<option value="K3">K3 ('.$dates[2].')</option>
				<option value="K4">K4 ('.$dates[3].')</option>
			</select>
		</div>

		<div class="form-group form-select-group">
			<label for="pref2">2<sup>nd</sup> Preference</label>
			<select name="pref2" class="form-control" id="pref2">
				<option value="K1">K1 ('.$dates[0].')</option>
				<option value="K2" selected>K2 ('.$dates[1].')</option>
				<option value="K3">K3 ('.$dates[2].')</option>
				<option value="K4">K4 ('.$dates[3].')</option>
			</select>
		</div>
		<input class="button" type="submit" name="submitpref" value="SUBMIT">
	</form>';
} else {
	//student has already submitted preferences form

	//retrieving name & date of retreats using kid
	$pref1kairos = DB::queryFirstRow('select name, date_format(startdate, "%e %b %Y") as startdate from kairos where kid = '.$userPrefData[0]['kid']);
	$pref2kairos = DB::queryFirstRow('select name, date_format(startdate, "%e %b %Y") as startdate from kairos where kid = '.$userPrefData[1]['kid']);

	//displaying student's preferences
	echo '
	<h2>Your Selected Retreats</h2>
	<h3>1<sup>st</sup> Preference: '.$pref1kairos['name'].' ('.$pref1kairos['startdate'].')<h3>
	<h3>2<sup>nd</sup> Preference: '.$pref2kairos['name'].' ('.$pref2kairos['startdate'].')<h3>

	<h2>Request For Resubmission</h2>';

	//checking if user has already requested for a resubmission
	$resubmitpref = DB::queryFirstRow('select resubmitpref from students where username = %s', $_SESSION['username'])['resubmitpref'];
	if ($resubmitpref) {
		//user has already requested for a resubmission
		echo '
		<p>You have made a request for a resubmission. You will shortly be notified of our approval.</p>';
	} else {
		//user has not already requested for a resubmission
		//form for resubmission request
		echo '
		<form name="prefform" method="post" action="enrol.php" class="textarea-form">
			<p>Please state the reason why you would like to change your retreat preferences. You will shortly be notified of our approval.</p>
			<textarea class="form-control" name="resubmitreason" maxlength="500"></textarea>
			<input class="button" type="submit" name="submitrequest" value="REQUEST">
		</form>';

	}
}
?>
</div>

</body>
</html>