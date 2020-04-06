<?php
	
	$con = mysqli_connect("localhost", "show981111", "dyd1@tmdwlsfl", "show981111");

	$start = "2020-03-01 00:00";
	$end = "2020-04-25 11:59";

	$query = "SELECT canceledCourseDate, userID FROM DAYSCHEDULE order by UNIX_TIMESTAMP(canceledCourseDate) DESC";
	$res = mysqli_query($con, $query);
	while($row = mysqli_fetch_array($res) )
	{
		if(strtotime($row[0]) <= strtotime($end) && strtotime($row[0]) >= strtotime($start)  ){
			echo $row[0]." ".$row[1];
			$update = "UPDATE BOOKEDLIST SET status = 'canceled' WHERE userID = '$row[1]' AND startDate = '$row[0]' ";
			$updateq = mysqli_query($con,$update);
		}
		if(strtotime($row[0]) < strtotime($start))
		{
			break;
		}
	}



	echo $response;
	return $response;
	mysqli_close($con);

?>