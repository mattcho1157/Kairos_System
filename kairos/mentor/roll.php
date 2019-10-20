<?php
session_start();
require('../globalfuncs.php');
checklogout();
connectDB();
date_default_timezone_set('Australia/Brisbane');


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

<div class="banner general-banner">
	<h1>Roll</h1>
</div>

<?php
if (isset($_POST['submitroll'])) {
	$rid = $_POST['rid'];
	$rollname = $_POST['rollname'];
	$kid = $_POST['kid'];
	$recordDate = date('Y-m-d H:i:s');

	//list of present students from saved roll
	$presentStudents = isset($_POST['presentStudents']) ? $_POST['presentStudents'] : [];
	$retreatStudents = array_column(DB::query('select username from students where kid = %s', $kid), 'username');
	//list of absent students from saved roll
	$absentStudents = array_diff($retreatStudents, $presentStudents);
	if ($rid == 'null') {
		//new roll was created
		//insert new roll into rolls table
		DB::insert('rolls', array(
			'kid' => $kid,
			'name' => $rollname,
			'recorddate' => $recordDate
		));
		//newly inserted roll's rid
		$newrid = DB::queryFirstRow('select last_insert_id()')['last_insert_id()'];

		foreach ($absentStudents as $student) {
			//insert absent students into absences table
			DB::insert('absences', array(
				'username' => $student,
				'rid' => $newrid
			));
		}

	} else {
		//existing roll was edited
		//update roll in rolls table
		DB::update('rolls', array(
			'name' => $rollname,
		), 'rid = %s', $rid);

		//delete all absent students fro this roll from absences
		DB::delete('absences', 'rid = %s', $rid);

		foreach ($absentStudents as $student) {
			//insert absent students into absences table
			DB::insert('absences', array(
				'username' => $student,
				'rid' => $rid
			));
		}
	}
	alert('info', 'ROLL HAS BEEN SAVED');

}

?>

<div class="page-content">
	<form name="addrollform" method="post" action="saveroll.php">
		<?php
		$currentDate = date('Y-m-d');
		//getting the four retreats for current year
		$kairos = DB::query('select date(startdate) as start_date, kid from kairos where year(startdate) = %i order by startdate', date('Y'));
		
		//adding a period of 3 days onto each start-date to calculate end-dates
		$k1enddate = date_add(new DateTime($kairos[0]['start_date']), new DateInterval('P3D'))->format('Y-m-d');
		$k2enddate = date_add(new DateTime($kairos[1]['start_date']), new DateInterval('P3D'))->format('Y-m-d');
		$k3enddate = date_add(new DateTime($kairos[2]['start_date']), new DateInterval('P3D'))->format('Y-m-d');
		$k4enddate = date_add(new DateTime($kairos[3]['start_date']), new DateInterval('P3D'))->format('Y-m-d');

		//automatically setting the kairos for new roll depending on current date
		if ($kairos[0]['start_date'] <= $currentDate && $currentDate <= $k1enddate) {
			$kairosIndex = 0;
		} elseif ($kairos[1]['start_date'] <= $currentDate && $currentDate <= $k2enddate) {
			$kairosIndex = 1;
		} elseif ($kairos[2]['start_date'] <= $currentDate && $currentDate <= $k3enddate) {
			$kairosIndex = 2;
		} elseif ($kairos[3]['start_date'] <= $currentDate && $currentDate <= $k4enddate) {
			$kairosIndex = 3;	
		} else {
			//current date does not lie within date ranges of the four retreats
			echo '<h3>You can add new rolls only during a retreat.</h3>';
		}
		//if current time is during a retreat, allow mentor to create a new roll with corresponding POST variables
		if (isset($kairosIndex)) {
			echo '
			<input type="hidden" name="kid" value="'.$kairos[$kairosIndex]['kid'].'">
			<input type="hidden" name="kairos" value="K'.($kairosIndex + 1).'">
			<input class="button" type="submit" name="addroll" value="ADD NEW ROLL">';
		}
		?>
		
	</form>
	<h2>Completed Rolls (<?php echo date('Y'); ?>)</h2>
	<?php
	//retrieve all existing rolls taken in the current year
	$rolls = DB::query('select * from rolls where year(recorddate) = %i order by recorddate desc', date('Y'));
	//if rolls exist
	if ($rolls) {
		echo '
		<div class="table-responsive">
			<table class="table table-hover rollsummary">
				<thead>
					<tr>
						<th>RETREAT</th>
						<th>NAME</th>
						<th>DATE</th>
						<th></th>
					</tr>
				</thead>
				<tbody>';
		//iterating through each roll to display data in table
		foreach ($rolls as $roll) {
			$retreat = DB::queryFirstRow('select name from kairos where kid = %s', $roll['kid'])['name'];
			$name = $roll['name'];
			$date = date_format(date_create($roll['recorddate']), 'j M - g:i A');
			echo '
					<tr>
						<td>'.$retreat.'</td>
						<td>'.$name.'</td>
						<td>'.$date.'</td>
						<td>
							<form name="editrollform" method="post" action="saveroll.php">
								<input type="hidden" name="rid" value="'.$roll['rid'].'">
								<input type="hidden" name="rollname" value="'.$roll['name'].'">
								<input type="hidden" name="kid" value="'.$roll['kid'].'">
								<input class="button" type="submit" name="editroll" value="EDIT">
							</form>
						</td>
					</tr>';
		}
		echo '
				</tbody>
			</table>
		</div>';
	} else {
		echo '<h3>NONE</h3>';
	}

	?>

</div>

</body>
</html>