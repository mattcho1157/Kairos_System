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
	<title>Kairos | Retreats</title>

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
				<li><a href="retreats.php" id="current-page">RETREATS</a></li>
				<li><a href="roll.php">ROLL</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout"><span class="glyphicon glyphicon-user"></span> LOGOUT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner general-banner">
	<h1>Retreats</h1>
</div>

<div class="page-content">
	<h2>Retreat Summary</h2>
	<div class="table-responsive">
		<table class="table table-hover retreats-table">
			<thead>
				<tr>
					<th rowspan="2">RETREAT</th>
					<th rowspan="2">STARTING DATE</th>
					<th rowspan="1" colspan="2">STUDENTS</th>
					<th rowspan="1" colspan="2">MENTORS</th>
				</tr>
				<tr>
					<th>ENROLLED</th>
					<th>QUOTA</th>
					<th>ENROLLED</th>
					<th>QUOTA</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$currentYear = date('Y');
				//retrieve retreat data for current year
				$retreats = DB::query('select * from kairos where year(startdate) = %i order by startdate', $currentYear);

				//array for caching enrolled & quota numbers to be used for 'AVAILABLE POSITIONS' table
				$retreatPositions = array();
				foreach ($retreats as $retreat) {
					$name = $retreat['name'];
					$startdate = date_format(date_create($retreat['startdate']), 'j M Y');
					$studentsEnrolled = count(DB::query('select username from students where kid = %i', $retreat['kid']));
					$studentsQuota = $retreat['studentsquota'];
					$mentorsEnrolled = count(DB::query('select username from mentors where kid = %i', $retreat['kid']));
					$mentorsQuota = $retreat['mentorsquota'];
					echo '
					<tr>
						<td>'.$name.'</td>
						<td>'.$startdate.'</td>
						<td>'.$studentsEnrolled.'</td>
						<td>'.$studentsQuota.'</td>
						<td>'.$mentorsEnrolled.'</td>
						<td>'.$mentorsQuota.'</td>
					</tr>';

					//append enrolled & quota numbers into $retreatPositions array
					$retreatPositions[$name] = array('studentsQuota'=>$studentsQuota, 'studentsEnrolled'=>$studentsEnrolled, 'mentorQuota'=>$mentorsQuota, 'mentorEnrolled'=>$mentorsEnrolled);
				}
				?>
			</tbody>
		</table>
	</div>

	<h2>Available Positions</h2>
	<div class="table-responsive">
		<table class="table table-hover positions-table">
			<thead>
				<tr>
					<th>RETREATS</th>
					<th>STUDENTS</th>
					<th>MENTORS</th>
				</tr>
			</thead>
			<tbody>
				<?php
				//array for caching student/mentor positions where enrolled > quota
				$exceedingPositions = array();
				foreach ($retreatPositions as $retreat => $positions) {
					//calculating available positions using cache values from $retreatPositions array
					$studentPositions = $positions['studentsQuota'] - $positions['studentsEnrolled'];
					$mentorPositions = $positions['mentorQuota'] - $positions['mentorEnrolled'];
					echo '
					<tr>
						<td>'.$retreat.'</td>
						<td>'.$studentPositions.'</td>
						<td>'.$mentorPositions.'</td>
					</tr>';

					//append remaining position less than 0, append into $exceedingPositions array
					if ($studentPositions < 0) {
						array_push($exceedingPositions, $retreat.' Students');
					}
					if ($mentorPositions < 0) {
						array_push($exceedingPositions, $retreat.' Mentors');
					}
				}
				?>
			</tbody>
		</table>
	</div>

	<?php
	if (count($exceedingPositions) > 0) {
		echo '<p id="exceedingpositions">EXCEEDING QUOTA: '.join(", ",$exceedingPositions).'</p>';
	}
	?>
</div>

</body>
</html>