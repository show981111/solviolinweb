<?php
	
	$con = mysqli_connect("localhost", "show981111", "dyd1@tmdwlsfl", "show981111");

	$start = "2020-03-01 00:00";
	$end = "2020-04-25 11:59";
	$tempStart = date('Y-m-d H:i',strtotime($start)); 
	$tempEnd = date('Y-m-d H:i',strtotime($end)); 

	$getRegular = "SELECT courseTeacher,courseBranch,userID,startTime,endTime,dow,startDate FROM REGULARSCHEDULE WHERE courseBranch = '마곡' ";//가장 최근 정기예약 날짜 확인 
	$result1 = mysqli_query($con,$getRegular);                                                    //이미 연장된 학생은 뺼수잇도록 
	$addedStart= strtotime($start);
	//$dayofweek = date('w', strtotime($startDate));
	$regularStart;

	if(mysqli_num_rows ( $result1 ) <= 0){
		$response = "nothing";
	}

	while($row = mysqli_fetch_array($result1))
	{
		$addedStart= strtotime($start);
		$formatStartTime = date('H:i',strtotime($row[3]));
		$formatEndTime = date('H:i',strtotime($row[4]));
		//echo "//format Start ".$formatStartTime. " ". "formatEndTime ".$formatEndTime." dow".$row[5]." " .date('w', $addedStart) ." //";
		//$cand_StartDateTime = date('Y-m-d H:i',strtotime("$Formats $Formatst")); 
		
		if(strtotime($row[6]) > strtotime($start) )//if startDate is after termStart 
		{
			$addedStart = strtotime($row[6]);
		}else{
			while(date('w', $addedStart) != $row[5])//find the first Date that matches dow 다음학기 정기 첫날짜 찾기 위해서 
			{
				$addedStart = strtotime("+1 day",$addedStart);
				//echo " //addedStart ". date("Y-m-d",$addedStart);
			}
		}

		$regularStart = date('Y-m-d',$addedStart);
		$cand_StartDateTime = date('Y-m-d H:i',strtotime("$regularStart $formatStartTime")); 
		$cand_EndDateTime = date('Y-m-d H:i',strtotime("$regularStart $formatEndTime")); 
		//echo " //cand start ".$cand_StartDateTime. " cand End ".$cand_EndDateTime;

		$cand_endDate = date('Y-m-d',strtotime($cand_EndDateTime));

		while(strtotime($cand_endDate) <= strtotime($end) ) // +7씩 해나가면서 텀 끝날때까지 삽입 해준다! 
		{
			$insertquery = "INSERT INTO BOOKEDLIST (courseTeacher, courseBranch, startDate, endDate, userID, ownerID, status) VALUES ('$row[0]', '$row[1]', '$cand_StartDateTime', '$cand_EndDateTime', '$row[2]', '$row[2]', 'BOOKED'  ) ";
			$pushquery = mysqli_query($con,$insertquery);
			//$strStartDate = strtotime("+7 day",$strStartDate);
			$cand_StartDateTime = date('Y-m-d H:i',strtotime("+7 day", strtotime($cand_StartDateTime))); 
			$cand_EndDateTime = date('Y-m-d H:i',strtotime("+7 day", strtotime($cand_EndDateTime))); 
			$cand_endDate = date('Y-m-d',strtotime($cand_EndDateTime));
			//echo "////INSIDE while cand start ".$cand_StartDateTime. " cand End ".$cand_EndDateTime." END WHILE////";
		}


	}



	echo $response;
	return $response;
	mysqli_close($con);

?>