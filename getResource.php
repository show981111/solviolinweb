<?php
	
	$userBranch = $_POST['userBranch'];
	$con = mysqli_connect("localhost", "show981111", "dyd1@tmdwlsfl", "show981111");
	$result = mysqli_query($con, "SELECT Teacher, color FROM TEACHERLIST WHERE Branch = '$userBranch';");
	$response = array();

	while($row = mysqli_fetch_array($result)){
		$teacher = strtolower(str_replace(' ', '', $row[0]));
		$result1 = mysqli_query($con, "SELECT startTime, endTime, courseDay FROM COURSETIMELINE WHERE courseBranch = '$userBranch' AND courseTeacher = '$row[0]';");
		$response1 = array();
		
		while($row1 = mysqli_fetch_array($result1))
		{
			if($row1[2] == "월")
			{
				$date = "1";
			}
			if($row1[2] == "화")
			{
				$date = "2";
			}
			if($row1[2] == "수")
			{
				$date = "3";
			}
			if($row1[2] == "목")
			{
				$date = "4";
			}
			if($row1[2] == "금")
			{
				$date = "5";
			}
			if($row1[2] == "토")
			{
				$date = "6";
			}
			if($row1[2] == "일")
			{
				$date = "0";
			}
			array_push($response1, array("start" => $row1[0], "end" => $row1[1], "dow" => "[".$date."]"));	
			
		}
		
		array_push($response, array("title"=>$teacher, "id" => $teacher, "eventColor" => $row[1], "businessHours" => $response1));
	}
	
		

	
	echo json_encode($response,JSON_UNESCAPED_UNICODE);
	
	mysqli_close($con);




?>