<?php
// get the retreat & groupby parameters from URL (select input)
$retreat = $_REQUEST['retreat'];
$groupby = $_REQUEST['groupby'];
$currentYear = date('Y');
$listTable = '';

//retrieve 5 student names containing $name string in alphabetical order
require('../globalfuncs.php');
connectDB();

//query database for every student allocated to selected retreat grouped by house/pc
$studentGroups = DB::query('
	select distinct students.'.$groupby.', group_concat(concat(upper(users.lname), " ", users.fname) order by users.lname) as names
	from students
	join users
	on students.username = users.username and students.kid in (select kid from kairos where year(startdate) = '.$currentYear.' and name = "'.$retreat.'")
	group by students.'.$groupby.'
	order by students.'.$groupby);

//if at least one student has been allocated a retreat
if ($studentGroups) {
	//display results in table
	$listTable .= '<div class="table-responsive"><table class="table table-hover studentlist-table">';
	//iterate through each house/pc for table heading
	foreach ($studentGroups as $group) {
		$listTable .= '<tr><th>'.$group[$groupby].'</th>';
		$namesList = str_replace(',', ', &nbsp;&nbsp;', $group['names']);
		$listTable .= '<td>'.$namesList.'</td>';
		$listTable .= '</tr>';
	}
	$listTable .= '</table></div>';
} else {
	$listTable = '<h3>NO STUDENTS AVAILABLE</h3>';
}

//output 'No Suggestion' if no suggestion was found or output correct names 
echo $listTable;
?>