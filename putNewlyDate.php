<?php
	
	require_once('bookingsystem.php');
	
	$courseTeacher = $_POST['courseTeacher'];
	$courseBranch = $_POST['courseBranch'];
	$userID = $_POST['userID'];
	$startDate = $_POST['startDate'];
	$canceledDate = $_POST['canceledDate'];
	$userDuration = $_POST['userDuration'];
	$userName = $_POST['userName'];

	// $courseTeacher ="김은솔";
	// $courseBranch = "교대";
	// $userID = "test1";
	// $startDate = "2020-03-29 13:30";
	// $canceledDate = "2020-03-09 16:30";
	// $userDuration = "30";
	// $userName = "test1";

	// $userID = "test1";
	// $extendTeacher = "김은솔";
	// $extendBranch = "교대";
	// $extendStartDate = "2020-03-19 14:30";
	// $extendEndDate =  "2020-03-19 15:00";
	
	$test = new BookingSystem($courseBranch);
	if(isset($userName))
	{
		$test->putNewlyDate($courseTeacher,$courseBranch, $userID, $startDate, $canceledDate, $userDuration, $userName);
	}
	
	

?>