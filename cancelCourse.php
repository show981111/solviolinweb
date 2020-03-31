<?php
	
	//consoleTest("hi");
	require_once('bookingsystem.php');
	
	$userID = $_POST['userID'];
	$cancelTeacher = $_POST['cancelTeacher'];
	$cancelBranch = $_POST['cancelBranch'];
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];
	$userName = $_POST['userName'];
	// echo $userID;
	// echo $cancelTeacher;
	// echo $cancelBranch;
	// echo $startDate;
	// echo $endDate;
	
	//$userBranch = "교대";

	$test = new BookingSystem($cancelBranch);
	//echo "good";vv
	// $test->cancelCourse("test1","김은솔","교대", "2020-03-26 20:00","2020-03-26 08:30");
	if(isset($userID))
	{
		$test->cancelCourse($userID,$cancelTeacher, $cancelBranch, $startDate, $endDate, $userName);
	}
	// getTimeForMonth($courseDay, $courseTeacher, $startDate, $userDuration)
	

?>