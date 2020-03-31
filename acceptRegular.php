<?php
	
	//consoleTest("hi");
	require_once('bookingsystem.php');
	
	$pt_courseTeacher = $_POST['pt_courseTeacher'];
	$pt_courseBranch = $_POST['pt_courseBranch'];
	$pt_userID = $_POST['pt_userID'];
	$pt_startTime = $_POST['pt_startTime'];//15:00 ~ 16:00 in web -> select Start YYYY-MM-DD HH:mm
	$pt_startDateAndDow = $_POST['pt_startDateAndDow'];// 2020-02-06 월 in web -> userDuration
	$pt_reject = $_POST['reject'];

	// $pt_courseTeacher = "이채정";
	// $pt_courseBranch = "교대";
	// $pt_userID = "testt";
	// $pt_startTime = "15:00 ~ 16:00";//15:00 ~ 16:00
	// $pt_startDateAndDow = "2020-03-16 월";// 2020-02-06 월
	
	$startTime;
	$endTime;
	$dow;
	$startDate;
	if($pt_reject == "web")
	{
		$startDate = date("Y-m-d", strtotime($pt_startTime));
		$startTime = date("H:i", strtotime($pt_startTime));
		$dow = date('w',strtotime($pt_startTime));
		$tempTime = strtotime($startTime);
		if($pt_startDateAndDow == "30")
		{
			$endTime = date("H:i", strtotime("+30 minutes",$tempTime));
		}else if($pt_startDateAndDow == "45")
		{
			$endTime = date("H:i", strtotime("+45 minutes",$tempTime));
		}else if($pt_startDateAndDow == "60")
		{
			$endTime = date("H:i", strtotime("+60 minutes",$tempTime));
		}
	}else{
		if(strlen($pt_startTime) > 8)
		{
			$endTime = substr( $pt_startTime, 8 );
			$startTime = substr($pt_startTime, 0, 5);
		}else
		{
			echo "fail";
			return;
		}
		if(mb_strlen($pt_startDateAndDow,'utf-8') > 9)
		{
			$startDate = substr($pt_startDateAndDow, 0,10);
		}else{
			echo "fail";
			return;
		}
		//echo $pt_courseTeacher." ".$pt_courseBranch." ".$pt_userID. " ".$pt_startTime. " ".$pt_startDateAndDow ;
		$dow = date('w',strtotime($startDate));
	}
	
	$test = new BookingSystem($pt_courseBranch);
	if($pt_reject == "reject")
	{
		$test->deleteWaitList($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $pt_userID,$dow,"yes");
	}else
	{
		$test->acceptRegular($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $pt_userID, $dow);
	}
	

?>