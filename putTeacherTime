<?php
	
	require_once('bookingsystem.php');
	
	$teacherBranch = $_POST['teacherBranch'];
    $teacherName = $_POST['teacherName'];
    $timeStart = $_POST['timeStart'];
    $timeEnd = $_POST['timeEnd'];
    $courseDay = $_POST['courseDay'];
    $option = $_POST['option'];

	// $teacherBranch = "교대";
 //    $teacherName = "이채정";
 //    $timeStart = "13:00";
 //    $timeEnd = "15:00";
 //    $courseDay = "월";

	$test = new BookingSystem($teacherBranch);
	if(isset($teacherBranch))
	{
		if($option == "register")
		{
			$test->putTeacherTime($teacherName, $teacherBranch, $timeStart, $timeEnd, $courseDay);
		}else if($option == "delete")
		{
			$test->deleteTeacherTime($teacherName, $teacherBranch, $timeStart, $timeEnd, $courseDay);
		}
		
	}
	
?>