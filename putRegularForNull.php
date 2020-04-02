<?php
	
	require_once('bookingsystem.php');
	
	$userID = $_POST['userID'];
	$courseTeacher = $_POST['courseTeacher'];
	$courseBranch = $_POST['courseBranch'];
	//$userID = "test1";

	$test = new BookingSystem("");
	if(isset($userID))
	{
		$test->putRegularForNull($userID, $courseTeacher, $courseBranch);
	}
	
?>