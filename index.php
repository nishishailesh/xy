<?php
//include connection file
require('connection.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
</head>
<body bgcolor="#FFCCFF">
<!-- staff insert form -->	
<h1><center>Registration</center></h1>
<table align="center"  border=" 1" cellspacing="2"  >
<form method="post" action="emp_insert.php">
<tr><td>	Name:</td>
	<td>
	<input type="text" name="emp_name" placeholder="Enter employee name"></td></tr>
<tr><td>	Mobile No:</td>
	<td>	<input type="text" name="mobile_no" placeholder="Enter mobile number"></td></tr>
<tr><td></td>
	<td><input type="submit" name="submit" value="Save"></td></td>
</table>	
</form>

<?php
//staff table data view
$view_sql="select * from `staff_tbl`";
$view_data=mysqli_query($conn,$view_sql);
//now we arrang data in tabuler form
?>
<table align="center" cellspacing="3"  border=" 1px solid black" cellpadding="3" >
<tr><th>Emp ID</th><th>Emp Name</th><th>Contact No</th></tr>
<?php
while ($data=mysqli_fetch_array($view_data)) {
?>
<tr>
	<td><?php echo $data['emp_id']; ?></td><td><?php echo $data['emp_name']; ?></td><td><?php echo $data['mobile_no']; ?></td>
</tr>

<?php
}
?>
</table>
<h1><center>Leave Detail</center></h1>
<!-- leave form -->
<table align="center"  border=" 1" cellspacing="2"  >
<form method="post" action="leave_insert.php">

	<?php
	//get staff name for select box
	$view_emp="select * from `staff_tbl`";
	$view_empdata=mysqli_query($conn,$view_sql);
	?>
<tr>
	<td>	Name:</td>
	<td><select name="emp_id">
		<?php
		while ($emp_data=mysqli_fetch_array($view_empdata)) {
		?>
			<option value="<?php echo $emp_data['emp_id']; ?>"><?php echo $emp_data['emp_name']; ?></option>
		<?php
		}
		?>
	</select></td></tr>
	
	<tr><td>From Date:</td>
	<td><input type="date" name="from_date" placeholder="from date"></td></tr>
	
	<tr><td>To Date:
	<td><input type="date" name="to_date" placeholder="to date"></td></tr>

	<tr><td>Reason:</td>
	<td><textarea name="reason">Reason</textarea></td></tr>
	
	<?php
	//get staff name for select box
	$view_emp1="select * from `staff_tbl`";
	$view_empdata1=mysqli_query($conn,$view_sql);
	?>
	<tr><td>Assign To:</td>
	<td><select name="assign_to">
	<?php
		while ($emp_data=mysqli_fetch_array($view_empdata1)) {
		?>
			<option value="<?php echo $emp_data['emp_id']; ?>"><?php echo $emp_data['emp_name']; ?></option>
		<?php
		}
		?>
		</select></td></tr>
	
	<tr><td><td><input type="submit" name="leave" value="save"></td></tr>

</form>
	</table>
<?php
	//view leave data
	//$view_leave="select * from `leave_tbl`";
	$view_leave="select a1.emp_id,b1.emp_name, a1.from_date, a1.to_date, a1.reason, a1.assign_to from leave_tbl as a1, staff_tbl as b1 where a1.emp_id = b1.emp_id ";
	$view_leavedata=mysqli_query($conn,$view_leave);
?>
<table align="center" cellspacing="3"  border="1" >
<tr><th>Emp ID</th><th>Emp Name</th><th>From Date</th><th>To Date</th><th>Reason</th><th>Assign To</th></tr>
<?php
while ($data_leave=mysqli_fetch_array($view_leavedata)) {
?>
<tr>
	<td><?php echo $data_leave['emp_id']; ?></td><td><?php echo $data_leave['emp_name']; ?></td><td><?php echo $data_leave['from_date']; ?></td><td><?php echo $data_leave['to_date']; ?></td><td><?php echo $data_leave['reason']; ?></td><td><?php echo $data_leave['assign_to']; ?></td>
</tr>

<?php
}
?>
</table>

</body>
</html>