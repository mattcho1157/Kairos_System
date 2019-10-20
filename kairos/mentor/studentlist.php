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
	<title>Kairos | Students List</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../kairos.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700|Raleway&display=swap" rel="stylesheet">
	<link rel="icon" href="../img/logo.png">

	<script>
	$(document).ready(function(){
		//called if select inputs are changed
		$('#notes-retreat, #notes-groupby').change(function(){
			//create XMLHttpRequest object
			var xmlhttp = new XMLHttpRequest();
			//function called whenever readyState of request changes
			xmlhttp.onreadystatechange = function() {
				//if server response is ready
				if (this.readyState == 4 && this.status == 200) {
					//readyState = 4 -> request finished and response is ready
					//status = 200 -> returns "OK" for request status
					document.getElementById('listTable').innerHTML = this.responseText;
				}
			}
			//send request to .php on the server - parameters retreat & groupby inputs are added
			xmlhttp.open('GET', 'listtable.php?retreat=' + $('#notes-retreat').val() + '&groupby=' + $('#notes-groupby').val(), true);
			xmlhttp.send();
		}).change();
	});
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
	<a href="studentprofiles.php">PROFILES</a>
	<a href="studentlist.php" id="current-tab">STUDENT LIST</a>
</div>

<div class="page-content">
	<form>
		<table class="studentlistoptions-table">
			<tr>
				<th>RETREAT: </th>
				<td>
					<select class="form-control" name="notes-retreat" id="notes-retreat">
						<option value="K1" selected>K1</option>
						<option value="K2">K2</option>
						<option value="K3">K3</option>
						<option value="K4">K4</option>
					</select>
				</td>
				<th id="groupby-heading">GROUP BY: </th>
				<td>
					<select class="form-control" name="notes-groupby" id="notes-groupby">
						<option value="house" selected>House</option>
						<option value="pc">PC Group</option>
					</select>
				</td>
			</tr>
		</table>
	</form>
	<div id="listTable"></div>
</div>

</body>
</html>