<?php
	
	require_once('bookingsystem.php');
	
	$closeBranch = $_POST['closeBranch'];
	$closeTeacher = $_POST['closeTeacher'];
	$closeStartDate = $_POST['closeStartDate'];
	$closeEndDate = $_POST['closeEndDate'];
	$isCancel = $_POST['isCancel'];

	
	$test = new BookingSystem($closeBranch);
	if(isset($closeBranch))
	{
		$test->close($closeBranch, $closeTeacher, $closeStartDate, $closeEndDate, $isCancel);
	}
	
	

?>