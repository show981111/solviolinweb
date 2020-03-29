
<?php
	
	//consoleTest("hi");
	require_once('bookingsystem.php');
	
	// $userID = "test1";
	// $userBranch = "교대";
	// $courseTeacher = "";
	// $fetchStart = "2020-03-01";
	// $fetchEnd = "2020-03-31";
	// $option = "all";
	//$option = $_POST['option'];
	$userID = $_POST['userID'];
	$userBranch = $_POST['userBranch'];
	$courseTeacher = $_POST['courseTeacher'];
	$fetchStart = $_POST['fetchStart'];
	$fetchEnd = $_POST['fetchEnd'];
	$option = $_POST['option'];


	
	$test = new BookingSystem($userBranch);
	//echo "good";
	//$test->getTimeForMonth("목","이채정","2020-04-02","30","admin");
	
	$test->getAllBookedList($userID,$userBranch,$courseTeacher, $fetchStart, $fetchEnd,$option );
	// getTimeForMonth($courseDay, $courseTeacher, $startDate, $userDuration)
	

?>