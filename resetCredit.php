<?php
	
	$con = mysqli_connect("localhost", "show981111", "dyd1@tmdwlsfl", "show981111");

	$query = "UPDATE USER SET userCredit = 2 WHERE status <> 'false' ";
	$insert = mysqli_query($con,$query);
	if(mysqli_affected_rows($con) > 0)
	{
		echo "success";
	}else{
		echo "fail";
	}
	
	mysqli_close($con);

?>