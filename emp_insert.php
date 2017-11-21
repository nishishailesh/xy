<?php
require('connection.php');
if(isset($_POST['submit']))
{
	$sql="INSERT INTO `staff_tbl` (`emp_name`, `mobile_no`) VALUES ('".$_POST['emp_name']."', '".$_POST['mobile_no']."')";

	if(mysqli_query($conn,$sql))
	{
		//echo 'insert sucessfully';
		//after sucessfully insert go to index page
		header('location:index.php');
	}
	else
	{
		echo 'not insert...';

	}
}
?>