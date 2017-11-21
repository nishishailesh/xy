<?php
error_reporting('e_all');
//write database connection code here
$conn=mysqli_connect("localhost","root","root","xyz");

if(!$conn)
{
	echo 'not';
}
?>
