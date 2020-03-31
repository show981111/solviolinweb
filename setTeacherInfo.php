<?php
	
	require_once('bookingsystem.php');
	
	$teacherBranch = $_POST['teacherBranch'];
    $teacherName = $_POST['teacherName'];
    $color = $_POST['color'];
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
			$test->putTeacherInfo($teacherName, $teacherBranch, $color);
		}else if($option == "delete")
		{
			$test->deleteTeacherInfo($teacherName, $teacherBranch);
		}
		
	}
	
?>