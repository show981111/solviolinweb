<?php
	
	$con = mysqli_connect("localhost", "show981111", "dyd1@tmdwlsfl", "show981111");

	$start = "2020-03-29 00:00";
	$end = "2020-04-25 11:59";

	$query = "SELECT courseTeacher, courseBranch, userID, courseTime, endTime , dow, startDate  FROM SCHEDULE ";
	$res = mysqli_query($con, $query);
	while($row = mysqli_fetch_array($res) )
	{
		$insert = "INSERT INTO REGULARSCHEDULE(courseTeacher, courseBranch, userID, startTime, endTime , dow, startDate) VALUES  ('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]' )  ";
		$queryInsert = mysqli_query($con,$insert);
	}



	echo $response;
	return $response;
	mysqli_close($con);

?>