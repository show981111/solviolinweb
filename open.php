<?php
	
	require_once('bookingsystem.php');
	
	$openTeacher = $_POST['openTeacher'];
	$openBranch = $_POST['openBranch'];
	$openStartDate = $_POST['openStartDate'];
	$openEndDate = $_POST['openEndDate'];
	// $openTeacher = "이채정";
	// $openBranch = "교대";
	// $openStartDate = "2020-03-20 14:00";
	// $openEndDate = "2020-03-20 19:00";
	
	
	$test = new BookingSystem($openBranch);
	if(isset($openTeacher))
	{
		$test->open($openStartDate, $openEndDate, $openTeacher, $openBranch);
	}
	
	

?>