<?php
	
	require_once('bookingsystem.php');
	
	$userID = $_POST['userID'];
	$startDate = $_POST['startDate'];
	
	//$userID = "test1";

	$test = new BookingSystem("");
	if(isset($userID))
	{
		$test->deleteCourse($userID, $startDate);
	}
	
?>