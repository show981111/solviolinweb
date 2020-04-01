<?php
	
	require_once('bookingsystem.php');
	
	$userID = $_POST['userID'];
	//$userID = "test1";

	$test = new BookingSystem("");
	if(isset($userID))
	{
		$test->deleteStudent($userID);
	}
	
?>