<?php
require('connection.php');
if(isset($_POST['leave']))
{
	$sql="INSERT INTO `leave_tbl` (`emp_id`, `from_date`, `to_date`, `reason`, `assign_to`) VALUES ('".$_POST['emp_id']."', '".$_POST['from_date']."', '".$_POST['to_date']."', '".$_POST['reason']."', '".$_POST['assign_to']."')";
	
	if(mysqli_query($conn,$sql))
	{
		//echo 'insert sucessfully';
		//after sucessfully insert go to index page
		header('location:index.php');
	}
	else
	{
		echo 'not insert.';
	}
}
?>

