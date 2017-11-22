<?php
session_start();

echo '<html><head>';
echo '<style>
form {margin-bottom:0;}
table {border-collapse: collapse;background-color:#F5DBED}
.recordtable {border-collapse: collapse;border:3px solid black;}
.fld {color:green;font-weight:bold;}
.toprow {color:blue;font-weight:bold;}
.note {color:red;font-weight:bold;}
.button {background-color:lightblue;color:purple;}
td {border:1px solid lightgray;}
</style>';

echo '</head>';
echo '<body>';

$tables=array('iqc_material','iqc_target','iqc_testing_event','examination');

echo '<form method=post style="margin-bottom:0;"><table><tr>';
foreach($tables as $value)
{
	echo '<td><button name=tablename value=\''.$value.'\'>'.$value.'</button></td>';
}
	echo '</form>';
echo '</tr></table>';

$GLOBALS['dbname']='qc';

if(isset($_SESSION['tablename']))
{
	$GLOBALS['tablename']=$_SESSION['tablename'];
}

if(isset($_POST['tablename']))
{
	$GLOBALS['tablename']=$_POST['tablename'];
	$_SESSION['tablename']=$_POST['tablename'];
}

$GLOBALS['db_user']='hinaben';
$GLOBALS['db_pass']='hinabeen';

$GLOBALS['role']='qc_root';

include 'edit_single_table.php';

echo '</body></html>';

//my_print_r($GLOBALS);

?>
