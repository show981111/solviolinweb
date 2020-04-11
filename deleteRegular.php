<?php
	
	require_once('bookingsystem.php');
	
	$userID = $_POST['userID'];
	$cancelBranch = $_POST['cancelBranch'];
	$cancelTeacher = $_POST['cancelTeacher'];
	$startDate = $_POST['startDate'];
	//$userID = "test1";
	$startTime;
	if(strlen($startDate) > 11)
	{
		$startTime = substr( $startDate, 11 );
		$dow = date('w',strtotime($startDate));
		$test = new BookingSystem("");
		if(isset($userID))
		{
			$test->deleteRegular($userID,$cancelBranch, $cancelTeacher, $startDate,$startTime,$dow);
		}
	}else{
		echo "checkStartDate";
	}
	
?>