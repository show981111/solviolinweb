<?php
	
	//consoleTest("hi");
	require_once('bookingsystem.php');

	
	$branch = $_POST['branch'];
	//$branch = "교대";
	// $userID = "test1";
	// $option = "cancelAll";
	// $userID = "test1";
	// $option = "changeRes";
	
	$test = new BookingSystem("");
	
	if(isset($branch))
	{
		//echo " gogogo ";
		$test->calculateIncome($branch);
	}
	
	

?>