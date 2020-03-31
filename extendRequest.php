<?php
	
	require_once('bookingsystem.php');
	
	$userID = $_POST['userID'];
	$extendTeacher = $_POST['extendTeacher'];
	$extendBranch = $_POST['extendBranch'];
	$extendStartDate = $_POST['extendStartDate'];
	$extendEndDate = $_POST['extendEndDate'];

	// $userID = "test1";
	// $extendTeacher = "김은솔";
	// $extendBranch = "교대";
	// $extendStartDate = "2020-03-19 14:30";
	// $extendEndDate =  "2020-03-19 15:00";
	
	$test = new BookingSystem($userBranch);
	if(isset($extendEndDate))
	{
		$test->extendRequest($userID, $extendTeacher,$extendBranch,$extendStartDate, $extendEndDate );
	}

?>