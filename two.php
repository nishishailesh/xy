<?php
session_start();
include 'common_table_function.php';

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

$link=get_link('hinaben','hinabeen','qc_root');
search($link,'qc','iqc_testing_event');
if(isset($_POST['action']))
{
	if($_POST['action']=='show_search_result')
	{
		//my_print_r($_POST);
		$sql=prepare_search_where_from_array($link,'qc','iqc_testing_event',$_POST,' order by time_of_analysis');
		$qc_target=array();
		//echo $sql.'<br>';
		$result=run_query($link,'qc',$sql);
		echo '<table>';
		while($ar=get_single_result($result))
		{
			if($got_it=in_subarray($qc_target,'iqc_target_id',$ar['iqc_target_id']))
			{
				$mean=$got_it['mean'];
				$sd=$got_it['sd'];				
			}
			else
			{
				$target_result=run_query($link,'qc','select * from iqc_target where id=\''.$ar['iqc_target_id'].'\'');
				$qcur=get_single_result($target_result);
				$qc_target[]=$qcur;
				$mean=$qcur['mean'];
				$sd=$qcur['sd'];					
			}			
			echo '<tr>';
			echo '<td>'.$ar['id'].'</td><td>'.$ar['iqc_target_id'].'</td><td>'.$ar['result'].'</td>
						<td>'.$mean.'</td><td>'.$sd.'</td>';			
			echo '</tr>';
		}
	}
}
echo '</body></html>';


?>
