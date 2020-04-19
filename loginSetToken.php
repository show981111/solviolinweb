<?php
	
	//consoleTest("hi");
	require_once('bookingsystem.php');

	
	$userID = $_POST['userID'];
	$userPassword = $_POST['userPassword'];
	$userToken = $_POST['token'];
	// $userID = "test1";
	// $option = "cancelAll";
	// $userID = "test1";
	// $option = "changeRes";
	
	$test = new BookingSystem("");
	
	if(isset($userID) && isset($userPassword))
	{
		$test->login($userID, $userPassword, $userToken);
	}
	
	

?>