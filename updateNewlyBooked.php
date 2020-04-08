<?php
	
	$con = mysqli_connect("localhost", "show981111", "dyd1@tmdwlsfl", "show981111");

	$start = "2020-03-01 00:00";
	$end = "2020-04-25 11:59";

	$query = "SELECT courseTeacher, userBranch, newlyBookedDate, endDate, userID, canceledCourseDate FROM DAYSCHEDULE order by UNIX_TIMESTAMP(newlyBookedDate) DESC";
	$res = mysqli_query($con, $query);
	while($row = mysqli_fetch_array($res) )
	{
		if(strtotime($row[2]) <= strtotime($end) && strtotime($row[2]) >= strtotime($start)  ){
			echo $row[0]." ".$row[2];
			if(substr($row[2],0,1) == "2" )
			{
				if($row[5] != null && substr($row[5],0,1) == "2"){
					$update =  "INSERT INTO BOOKEDLIST (courseTeacher, courseBranch, startDate, endDate, userID, status, changeFrom) VALUES ('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', 'BOOKED', '$row[5]'  ) ";
					$updateq = mysqli_query($con,$update);
				}else{
					$update =  "INSERT INTO BOOKEDLIST (courseTeacher, courseBranch, startDate, endDate, userID, status) VALUES ('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', 'BOOKED'  ) ";
					$updateq = mysqli_query($con,$update);
				}
			}
			
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