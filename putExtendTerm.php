<?php
	
	require_once('bookingsystem.php');
	
	$termStart = $_POST['termStart'];
	$termEnd = $_POST['termEnd'];
	$userBranch = "";

	// $termStart = "2020-06-01";
	// $termEnd = "2020-06-29";

	$dateTime = DateTime::createFromFormat('Y-m-d', $termStart);
	$dateTimes = DateTime::createFromFormat('Y-m-d', $termEnd);
	$errors = DateTime::getLastErrors();
	if (!$dateTime || !$dateTimes || !empty($errors['warning_count'])) {
	    echo "notMatched";
	}else
	{
		$test = new BookingSystem($userBranch);
		$test->putExtendTerm($termStart, $termEnd);
	}
	
?>