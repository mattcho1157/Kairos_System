<?php
session_start();
require('globalfuncs.php');
checklogout();

$_SESSION['prevPage'] = 'info.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Kairos | Info</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="kairos.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700|Raleway&display=swap" rel="stylesheet">
	<link rel="icon" href="img/logo.png">
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
			<a class="navbar-brand" href="./"><span><img src="img/logo.png"></span>KAIROS</a>
		</div>

		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<li><a href="info.php" id="current-page">INFO</a></li>
				<?php navbarlinks(); ?>
		</div>
	</div>
</nav>

<div class="banner" id="info-banner">
	<h1>What Is Kairos?</h1> 
</div>

<div class="info-content">
	<h2>About Kairos</h2>
	<p>Students listen to a team of staff who share their story of the good times and challenging times in their lives, reflect on their relationship with God and how we are called to make a difference in our world. The Kairos retreat is the last event in which Year 12s are invited to participate at the GTOEC at Maroon Dam. The students are challenged to clear all distractions and worries about life back in Brisbane and be fully present to each aspect of the retreat. Through talks from leaders, self reflection and small group discussions, we aim to support personal growth by:</p><br>
	<ul>
		<li>Knowledge of self and others</li>
		<li>Humility in shared challenges and sacred stories and</li>
		<li>Wisdom in God’s Love and applying their new insight to the rest of their lives.</li>
	</ul><br>
	<p>Kairos is an opportunity to take a breather in the midst of the busyness of Year 12. It is a place where young men can ask honest questions and seek honest answers in an environment where others are permitted to do the same, with an understanding that nobody has it all figured out.</p><br>
	<p>The 'success' of the Kairos Retreat depends mainly on the willingness of the participants to engage in the activities both individually and collectively. We present the 'carpe diem' (seize the day) challenge to them in the very beginning of the retreat. When the students take up this challenge, it is very special to be a part of the retreat as a leader and that was definitely the case for this year’s participants.</p><br>
</div>

<div class="info-quote">
	<p><i>"Love one another. As I have loved you, so you must love one another."</i><br><br>John: 13 34-35</p>
</div>

<div class="info-content container">
	<?php
	connectDB();
	$current_year = date('Y');
	//retrieve date of retreats that will occur this year
	$dates = DB::query('select date_format(startdate, "%e %b %Y") as start_date from kairos where year(startdate) = '.$current_year.' order by startdate');
	//if 4 retreats for this year has not been added to table, display no dates
	$dates = (count($dates) != 4 ? ['_', '_', '_', '_'] : [$dates[0]['start_date'], $dates[1]['start_date'], $dates[2]['start_date'], $dates[3]['start_date']]);
	?>
	<h2>Starting Dates</h2>
	<div class="dates row">
		<div class="col-xs-6 col-sm-3">
			<h3 class="info-kairos-label">K1</h3>
			<p><?php echo $dates[0]; ?></p>
		</div>
		<div class="col-xs-6 col-sm-3">
			<h3 class="info-kairos-label">K2</h3>
			<p><?php echo $dates[1]; ?></p>
		</div>
		<div class="col-xs-6 col-sm-3">
			<h3 class="info-kairos-label">K3</h3>
			<p><?php echo $dates[2]; ?></p>
		</div>
		<div class="col-xs-6 col-sm-3">
			<h3 class="info-kairos-label">K4</h3>
			<p><?php echo $dates[3]; ?></p>
		</div>
	</div>
</div>

<?php
//alert message pop-up for account log in / sign up
if (isset($_SESSION['loginSuccessful'])) {
	alert('info', 'LOG IN SUCCESSFUL'.$_SESSION['fname']);
	unset($_SESSION['loginSuccessful']);

} elseif (isset($_SESSION['signupSuccessful'])) {
	alert('info', 'SIGN UP SUCCESSFUL - PLEASE ACTIVATE YOUR ACCOUNT VIA EMAIL');
	unset($_SESSION['signupSuccessful']);
}
?>

</body>
</html>