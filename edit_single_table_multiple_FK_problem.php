<?php



if(
	!isset($GLOBALS['dbname']) 	||
	!isset($GLOBALS['tablename']) ||
	!isset($GLOBALS['db_user']) ||
	!isset($GLOBALS['db_pass'])	||
	!isset($GLOBALS['role'])
	)
	{
		echo 'globals not set';
		//exit(0);
	}
	
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

//////////////////
/////Main code////
//////////////////

////////set globals///////////

//$GLOBALS['dbname']='user';
//$GLOBALS['role']='user_admin';
//$GLOBALS['tablename']='user';

//$GLOBALS['db_user']='hinaben';
//$GLOBALS['db_pass']='hinabeen';

////////verify db user and set link///////////
$link=mysqli_connect('127.0.0.1',$GLOBALS['db_user'],$GLOBALS['db_pass']);
if(!$link)
{
	echo 'error1:'.mysqli_error($link); exit(0);
}
else
{
	mysqli_query($link,'set role \''.$GLOBALS['role'].'\'');
}
	
$GLOBALS['link']=$link;
verify_db_user();

//verify_ap_user('1','password');


//////////notes on permission//////////
/*
 * show
 * new+edit+delete
 * 
 * row level: user, group
 * 
 * create role user_admin
 * 
 * grant all on user.user to 'user_admin' 
 * grant select on user.role to 'user_admin' 
 * 
 * grant user_admin to 'hinaben' 
 * */
 //////////////////////////////////




/////////////////run code////////////////

//////global menu//////////
echo '<h3>'.$dbname.'/'.$tablename.'</h3><form method=post>
			<input type=submit  class=button name=action value=search>
			<input type=submit  class=button name=action value=add>
	</form>';

////take action////////////
if(isset($_POST['action']))
{
	$_POST['action']();
}

/////////////general functions/////////////////////

function add()
{
	echo '<form method=post><table class="pure-table pure-table-bordered pure-table-striped">';
	echo '<thead>';
	echo '<tr><th colspan=2  class=toprow>'.$GLOBALS['tablename'].' (Insert)</th></tr>';
	echo '</thead>';
	echo '<tbody>';
	$fld=get_key();
	$option=prepare_option_from_fk();
	//my_print_r($option);
	foreach($fld as $k=>$v)
	{
		if($v['Extra']=='auto_increment')
		{
			echo '<tr><td>'.$v['Field'].'</td><td>Server generated</td></tr>';
		}
		elseif( isset($option[$v['Field']]))
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>';
			mk_select_from_array_return_key($v['Field'],$option[$v['Field']],'','');
			echo '</td></tr>';
		}
		elseif(substr($v['Type'],0,7)=='varchar')
		{
			$varchar_len=substr($v['Type'],8,-1);
			if($varchar_len>30)
			{
				$cols=min($varchar_len,50);
				$rows=min(round($varchar_len/$cols,0),5);
				echo '<tr><td class=fld>'.$v['Field'].'</td><td>
								<textarea 	maxlength=\''.$varchar_len.'\'
											title=\'maximum '.$varchar_len.' letters\'
											cols=\''.$cols.'\' 
											rows=\''.$rows.'\' 
											name=\''.$v['Field'].'\'></textarea></td></tr>';
			}
			else
			{
				//	pattern="[A-Za-z]{3}" title="Three letter country code"  
				echo '<tr><td class=fld>'.$v['Field'].'</td><td><input 
									maxlength=\''.$varchar_len.'\'
									title=\'maximum '.$varchar_len.' letters\'
									type=text name=\''.$v['Field'].'\'></td></tr>';				
			}
		}
		elseif($v['Type']=='datetime')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>';	
			read_datetime($v['Field'],$v['Field'],bindec("00111111"),'');
			echo '</td></tr>';				
		}	
		elseif($v['Type']=='date')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>';	
			read_datetime($v['Field'],$v['Field'],bindec("00111000"),'');
			
			echo '</td></tr>';			
		}
		elseif($v['Type']=='time')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>';	
			read_datetime($v['Field'],$v['Field'],bindec("000000111"),'');
			echo '</td></tr>';				
		}		
		elseif(substr($v['Type'],0,3)=='int')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td><input type=number name=\''.$v['Field'].'\'></td></tr>';				
		}	
		elseif(substr($v['Type'],0,6)=='bigint')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td><input  class=button type=number name=\''.$v['Field'].'\'></td></tr>';				
		}	
		elseif($v['Type']=='float')
		{

//title shown like <pre>. so no unnecessary space
			echo '<tr><td class=fld>'.$v['Field'].'</td><td><input 
													type=text 
pattern="[0-9]*.[0-9]*" 
title="{correct->2.3, 2.0, 0.3, .3,3.} 
{incorrect-> {2xd , y2}"
													
													name=\''.$v['Field'].'\'>
										</td></tr>';				
		}	
		else
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>
			<input type=text name=\''.$v['Field'].'\'></td></tr>';				
		}
	}
	echo '<tr><td  class=note>Action</td><td><input  class=button type=submit name=action value=insert></td></tr>';
	echo '</tbody></table></form>';
}



function search()
{
	echo '<form method=post><table>';
	echo '<thead>';
	echo '<tr><th colspan=2  class=toprow>'.$GLOBALS['tablename'].' (Search)</th></tr>';
	echo '</thead>';
	echo '<tbody>';
	$fld=get_key();
	$option=prepare_option_from_fk();
	//my_print_r($option);
	foreach($fld as $k=>$v)
	{
		if( isset($option[$v['Field']]))
		{
			echo '<tr><td class=fld><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td>';
			mk_select_from_array_return_key($v['Field'],$option[$v['Field']],'','');
			echo '</td></tr>';
		}
		elseif(substr($v['Type'],0,7)=='varchar')
		{
			$varchar_len=substr($v['Type'],8,-1);
			if($varchar_len>30)
			{
				$cols=min($varchar_len,50);
				$rows=min(round($varchar_len/$cols,0),5);
				echo '<tr><td class=fld><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td>
								<textarea 	maxlength=\''.$varchar_len.'\'
											title=\'maximum '.$varchar_len.' letters\'
											cols=\''.$cols.'\' 
											rows=\''.$rows.'\' 
											name=\''.$v['Field'].'\'></textarea></td></tr>';
			}
			else
			{
				//	pattern="[A-Za-z]{3}" title="Three letter country code"  
				echo '<tr><td class=fld><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td><input 
									maxlength=\''.$varchar_len.'\'
									title=\'maximum '.$varchar_len.' letters\'
									type=text name=\''.$v['Field'].'\'></td></tr>';				
			}
		}
		/*
		elseif($v['Type']=='datetime')
		{
			echo '<tr><td class=fld><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td>';	
			read_datetime($v['Field'],$v['Field'],bindec("00111111"),'');
			echo '</td></tr>';				
		}	
		elseif($v['Type']=='date')
		{
			echo '<tr><td class=fld><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td>';	
			read_datetime($v['Field'],$v['Field'],bindec("00111000"),'');
			
			echo '</td></tr>';			
		}
		elseif($v['Type']=='time')
		{
			echo '<tr><td class=fld><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td>';	
			read_datetime($v['Field'],$v['Field'],bindec("000000111"),'');
			echo '</td></tr>';				
		}
		*/
		
		elseif(substr($v['Type'],0,3)=='int')
		{
			echo '<tr><td><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td><input type=number name=\''.$v['Field'].'\'></td></tr>';				
		}	
		elseif(substr($v['Type'],0,6)=='bigint')
		{
			echo '<tr><td class=fld><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td><input type=number name=\''.$v['Field'].'\'></td></tr>';				
		}	
		elseif($v['Type']=='float')
		{

//title shown like <pre>. so no unnecessary space
			echo '<tr><td class=fld><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td><input 
													type=text 
pattern="[0-9]*.[0-9]*" 
title="{correct->2.3, 2.0, 0.3, .3,3.} 
{incorrect-> {2xd , y2}"
													
													name=\''.$v['Field'].'\'>
										</td></tr>';				
		}	
		else
		{
			echo '<tr><td class=fld><input type=checkbox name=\'cb_'.$v['Field'].'\' >'.$v['Field'].'</td><td>
			<input type=text name=\''.$v['Field'].'\'></td></tr>';				
		}
	}
	echo '<tr><td class=note>Action --></td><td><input class=button type=submit name=action 
								value=show_search_result></td></tr>';
	echo '</tbody></table></form>';
}


function insert()
{
	//my_print_r($_POST);
	$fld=get_key();
	
	$sql='insert into `'.$GLOBALS['tablename'].'` ';
	$sql_fld='(';
	$sql_val='values(';
	
	foreach($fld as $k=>$v)
	{	
		if($v['Extra']=='auto_increment')
		{
			//DO NOTHING
		}
		
		elseif($v['Type']=='datetime' )
		{
			$dt=	$_POST[$v['Field'].'_year'].'-'.
					$_POST[$v['Field'].'_month'].'-'.
					$_POST[$v['Field'].'_day'].' '.
					$_POST[$v['Field'].'_hour'].':'.
					$_POST[$v['Field'].'_min'].':'.
					$_POST[$v['Field'].'_sec'];
		}
		elseif($v['Type']=='date')
		{
			$dt=	$_POST[$v['Field'].'_year'].'-'.
					$_POST[$v['Field'].'_month'].'-'.
					$_POST[$v['Field'].'_day'];
		}
		elseif($v['Type']=='time')
		{
			$dt=	$_POST[$v['Field'].'_hour'].':'.
					$_POST[$v['Field'].'_min'].':'.
					$_POST[$v['Field'].'_sec'];
		}		
		else
		{
			$dt=$_POST[$v['Field']];
		}
		
		
			$sql_fld=$sql_fld.'`'.$v['Field'].'`, ';
			$sql_val=$sql_val.'\''.$dt.'\', ';
	}
	$sql_fld=substr($sql_fld,0,-2);
	$sql_fld=$sql_fld.')  ';

	$sql_val=substr($sql_val,0,-2);
	$sql_val=$sql_val.')';	
	
	$sql=$sql.$sql_fld.$sql_val;
	//echo '<h3>'.$sql.'</h3>';
	$result=run_query($sql);
	if($result==false)
	{
		echo '<h3 style="color:red;">No record inserted</h3>';
	}
	else
	{
		echo '<h3 style="color:green;">'.$result.' record inserted</h3>';
	}
}

function show_search_result()
{
	//my_print_r($_POST);	
	$fld=get_key();
	
	$sql='select * from `'.$GLOBALS['tablename'].'` where ';
	$sql_where=' ';
	
	foreach($fld as $k=>$v)
	{	
		if(isset($_POST['cb_'.$v['Field']]))
		{
			//if($v['Type']=='datetime')
			//{
				//$value=	$_POST[$v['Field'].'_year'].'-'.
						//$_POST[$v['Field'].'_month'].'-'.
						//$_POST[$v['Field'].'_day'].' '.
						//$_POST[$v['Field'].'_hour'].':'.
						//$_POST[$v['Field'].'_min'].':'.
						//$_POST[$v['Field'].'_sec'];
				
			//}
			//elseif($v['Type']=='date')
			//{
				//$value=	$_POST[$v['Field'].'_year'].'-'.
						//$_POST[$v['Field'].'_month'].'-'.
						//$_POST[$v['Field'].'_day'];

				
			//}
			//elseif($v['Type']=='time')
			//{
				//$value=	$_POST[$v['Field'].'_hour'].':'.
						//$_POST[$v['Field'].'_min'].':'.
						//$_POST[$v['Field'].'_sec'];
				
			//}
			//else
			//{
				$value=$_POST[$v['Field']];
			//}
			$sql_where=$sql_where.' `'.$v['Field'].'` like \'%'.$value.'%\' and ';
		}
	}
	
	
	$sql_where=substr($sql_where,0,-4);
	
	$sql=$sql.$sql_where;
	//my_print_r($sql);
	$result=run_query($sql);
	echo'<h3>Records of '.$GLOBALS['tablename'].' '.$sql_where.'</h3>';
	while($data=get_single_result($result))
	{
		show($data);
	}
}

function show_sql($sql)
{
	//my_print_r($_POST);
	$result=run_query($sql);
	while($data=get_single_result($result))
	{
		show($data);
	}	
}


function in_subarray($a,$k,$v)
{
		foreach($a as $sa)
		{
			if(isset($sa[$k]))
			{
				if($sa[$k]==$v)
				{
					return $sa;
				}
			}
		}
		return false;
}


function edit()
{
	//my_print_r($_POST);
	$sql=mk_select_sql_from_pk();
	$result=run_query($sql);
	$data=get_single_result($result);
	echo '<form method=post><table class=recordtable>';
	echo '<tbody>';
	$fld=get_key();
	$pk_array=get_primary_key($GLOBALS['tablename']);
	//my_print_r($pk_array);
	
	$option=prepare_option_from_fk();
	//my_print_r($option);
	foreach($fld as $k=>$v)
	{
		///////If PRI, create POST
		if(in_subarray($pk_array,'Field',$v['Field']))
		{
			echo '<input type=hidden name=\'__'.$v['Field'].'\' value=\''.$data[$v['Field']].'\'>';
		}

		//////If autoincriment just display , if it is primary, it will be passed as POST
		if($v['Extra']=='auto_increment')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>'.$data[$v['Field']].'</td></tr>';
		}
		
		/////if foreign key, prepare dropdown
		elseif( isset($option[$v['Field']]))
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>';
			mk_select_from_array_return_key($v['Field'],$option[$v['Field']],'',$data[$v['Field']]);
			echo '</td></tr>';
		}
		
		//////otherthings
		elseif(substr($v['Type'],0,7)=='varchar')
		{
			$varchar_len=substr($v['Type'],8,-1);
			if($varchar_len>30)
			{
				$cols=min($varchar_len,50);
				$rows=min(round($varchar_len/$cols,0),5);
				echo '<tr><td class=fld>'.$v['Field'].'</td><td>
								<textarea 	maxlength=\''.$varchar_len.'\'
											title=\'maximum '.$varchar_len.' letters\'
											cols=\''.$cols.'\' 
											rows=\''.$rows.'\' 
											name=\''.$v['Field'].'\'>'.$data[$v['Field']].'</textarea></td></tr>';
			}
			else
			{
				echo '<tr><td class=fld>'.$v['Field'].'</td><td><input 
									maxlength=\''.$varchar_len.'\'
									title=\'maximum '.$varchar_len.' letters\'
									type=text name=\''.$v['Field'].'\' value=\''.$data[$v['Field']].'\'></td></tr>';	
			}
		}
		elseif($v['Type']=='datetime')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>';	
			read_datetime($v['Field'],$v['Field'],bindec("00111111"),$data[$v['Field']]);
			echo '</td></tr>';				
		}	
		elseif($v['Type']=='date')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>';	
			read_datetime($v['Field'],$v['Field'],bindec("00111000"),$data[$v['Field']]);
			
			echo '</td></tr>';			
		}
		elseif($v['Type']=='time')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>';	
			read_datetime($v['Field'],$v['Field'],bindec("000000111"),$data[$v['Field']]);
			echo '</td></tr>';				
		}		
		elseif(substr($v['Type'],0,3)=='int')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td><input type=number name=\''.$v['Field'].'\' 
						value=\''.$data[$v['Field']].'\' ></td></tr>';				
		}	
		elseif(substr($v['Type'],0,6)=='bigint')
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td><input type=number name=\''.$v['Field'].'\'
						value=\''.$data[$v['Field']].'\'  ></td></tr>';				
		}	
		elseif($v['Type']=='float')
		{

//title shown like <pre>. so no unnecessary space
			echo '<tr><td class=fld>'.$v['Field'].'</td><td><input 
													type=text 
pattern="[0-9]*.[0-9]*" 
title="{correct->2.3, 2.0, 0.3, .3,3.} 
{incorrect-> {2xd , y2}"
													
													name=\''.$v['Field'].'\'
													value=\''.$data[$v['Field']].'\' >
										</td></tr>';				
		}	
		else
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>
			<input type=text name=\''.$v['Field'].'\'
			value=\''.$data[$v['Field']].'\' ></td></tr>';				
		}
	}
	echo '<tr><td class=note>Action --></td><td><input  class=button type=submit name=action value=save>';
	echo '<input class=button type=submit name=action value=delete></td></tr>';
	echo '</tbody></table></form>';	
	
}


function show($data)
{
	//my_print_r($data);
	echo '<form method=post><table class=recordtable>';
	echo '<tbody>';
	$fld=get_key();
	$pk_array=get_primary_key($GLOBALS['tablename']);
	$fk=get_foreign_key();
	my_print_r($fk);

	foreach($fld as $k=>$v)
	{
		////if primary key, prepare POST
		if(in_subarray($pk_array,'Field',$v['Field']))
		{
			echo '<input type=hidden name=\''.$v['Field'].'\' value=\''.$data[$v['Field']].'\'>';
		}

		////IF FOREIGN key, get idea from parent table
		////if field is mono-foreign key ?
		////if field is muti foreign key ?
		if($found=in_subarray($fk,'COLUMN_NAME',$v['Field']))
		{
				$sql='select * from `'.$found['REFERENCED_TABLE_NAME'].'` where 
							`'.$found['REFERENCED_COLUMN_NAME'].'`=\''.$data[$v['Field']].'\'';
							//echo $sql;
				
				////can be multiple
				////Which one?
				$result_fk=run_query($sql);
				while($fk_data=get_single_result($result_fk))
				{
					if($fk_data[$v['Field']]==$data[$v['Field']])
					{
						//print_r($fk_data);
						$dv='';
						foreach($fk_data as $vv)
						{
							$dv=$dv.'|'.$vv;
						}
						echo '<tr><td class=fld>'.$v['Field'].'</td><td>'.$dv.'</td></tr>';
					}
				}
		}

		else
		{
			echo '<tr><td class=fld>'.$v['Field'].'</td><td>
			'.$data[$v['Field']].'</td></tr>';				
		}
	}
	echo '<tr><td class=note>Action --></td><td><input  class=button type=submit name=action value=edit>';
	echo '<input class=button type=submit name=action value=delete></td></tr>';
	echo '</tbody></table></form>';	
	
}

function mk_select_sql_from_pk()
{
	$sql_pwhere=' where ';
	
	$pk_array=get_primary_key($GLOBALS['tablename']);
	
	foreach($pk_array as $pk)
	{
		$sql_pwhere=$sql_pwhere.'`'.$pk['Field'].'`='.'\''.$_POST[$pk['Field']].'\' and ';
	}
	$sql_pwhere=substr($sql_pwhere,0,-4);
	
	$psql='select * from `'.$GLOBALS['tablename'].'`'.$sql_pwhere;
//echo $psql;
	
	return $psql;
}

function mk_delete_sql_from_pk()
{
	$sql_pwhere=' where ';
	
	$pk_array=get_primary_key($GLOBALS['tablename']);
	
	foreach($pk_array as $pk)
	{
		$sql_pwhere=$sql_pwhere.'`'.$pk['Field'].'`='.'\''.$_POST[$pk['Field']].'\' and ';
	}
	$sql_pwhere=substr($sql_pwhere,0,-4);
	
	$psql='delete from `'.$GLOBALS['tablename'].'`'.$sql_pwhere;
//echo $psql;
	
	return $psql;
}

function save()
{
	//my_print_r($_POST);
	$fld=get_key();
	
	$sql='update `'.$GLOBALS['tablename'].'` ';
	$sql_set=' set ';
	$sql_where=' where ';
	
	$sql_pwhere=' where ';
	
	$pk_array=get_primary_key($GLOBALS['tablename']);
	
	foreach($pk_array as $pk)
	{
		$sql_where=$sql_where.'`'.$pk['Field'].'`='.'\''.$_POST['__'.$pk['Field']].'\' and ';
	}
	$sql_where=substr($sql_where,0,-4);


	foreach($fld as $k=>$v)
	{
	
		if($v['Type']=='datetime' )
		{
			$dt=	$_POST[$v['Field'].'_year'].'-'.
					$_POST[$v['Field'].'_month'].'-'.
					$_POST[$v['Field'].'_day'].' '.
					$_POST[$v['Field'].'_hour'].':'.
					$_POST[$v['Field'].'_min'].':'.
					$_POST[$v['Field'].'_sec'];
		}
		elseif($v['Type']=='date')
		{
			$dt=	$_POST[$v['Field'].'_year'].'-'.
					$_POST[$v['Field'].'_month'].'-'.
					$_POST[$v['Field'].'_day'];
		}
		elseif($v['Type']=='time')
		{
			$dt=	$_POST[$v['Field'].'_hour'].':'.
					$_POST[$v['Field'].'_min'].':'.
					$_POST[$v['Field'].'_sec'];
		}		
		else
		{
			$dt=$_POST[$v['Field']];
		}
		
			$sql_set=$sql_set.'`'.$v['Field'].'`=\''.$dt.'\' , ';
			
			
		if(in_subarray($pk_array,'Field',$v['Field']))
		{
			$sql_pwhere=$sql_pwhere.'`'.$v['Field'].'`='.'\''.$dt.'\' and ';
		}
	}
	$sql_set=substr($sql_set,0,-2);
	$sql_pwhere=substr($sql_pwhere,0,-4);

	$sql=$sql.$sql_set.$sql_where;
	
	//echo '<h3>'.$sql.'</h3>';
	$result=run_query($sql);
	if($result==false)
	{
		echo '<h3 style="color:red;">No record updated</h3>';
	}
	else
	{
		echo '<h3 style="color:green;">'.$result.' record updated</h3>';
	}
	
	$psql='select * from `'.$GLOBALS['tablename'].'`'.$sql_pwhere;
	//echo $psql;
	
	show_sql($psql);
}

function delete()
{
	//my_print_r($_POST);
	$sql=mk_delete_sql_from_pk();
	
	$result=run_query($sql);
	if($result==false)
	{
		echo '<h3 style="color:red;">No record deleted</h3>';
	}
	else
	{
		echo '<h3 style="color:green;">'.$result.' record deleted</h3>';
	}

}
function my_print_r($a)
{
	echo '<pre>';
	print_r($a);
	echo '</pre>';
}

function get_key()
{
	$sql='desc `'.$GLOBALS['tablename'].'`';
	//echo $sql;
	$result=run_query($sql);
	$ret=array();
	while($data=get_single_result($result))
	{
		$ret[]=$data;
	}
	return $ret;
}

function get_primary_key($tablename)
{
	$sql='desc `'.$tablename.'`';
	//echo $sql;
	$result=run_query($sql);
	$ret=array();
	while($data=get_single_result($result))
	{
		//print_r($data);echo '<br>';
		if($data['Key']=='PRI')
		{
			$ret[]=$data;
		}
	}
	//print_r($ret);
	return $ret;
}

function get_foreign_key()
{
	$sql='select * from KEY_COLUMN_USAGE 
				where 
					constraint_schema=\''.$GLOBALS['dbname'].'\' and 
					table_name=\''.$GLOBALS['tablename'].'\' and
					REFERENCED_COLUMN_NAME is not null';
	//echo $sql;
	$result=run_query_is($sql);
	$ret=array();
	while($data=get_single_result($result))
	{
		$ret[]=$data;
	}
	return $ret;
}

function prepare_option_from_fk()
{
	$fk_array=get_foreign_key();
	//my_print_r($fk_array);
	$option=array();
	foreach($fk_array as $fk)
	{
		//echo '<h1>'.substr($fk['CONSTRAINT_NAME'],-4).'</h1>';
		if(substr($fk['CONSTRAINT_NAME'],-4)!='text')
		{
			//[CONSTRAINT_NAME]
			//echo $fk['COLUMN_NAME'];
			//echo $fk['REFERENCED_TABLE_NAME'];
			//echo $fk['REFERENCED_COLUMN_NAME'];
			$sql='select * , `'.$fk['REFERENCED_COLUMN_NAME'].'` 
					from `'.$fk['REFERENCED_TABLE_NAME'].'` group by  `'.$fk['REFERENCED_COLUMN_NAME'].'`';
			//echo $sql;
			$result=run_query($sql);
			while($ar=get_single_result($result))
			{
				$dv='';
				foreach($ar as $v)
				{
					$dv=$dv.'|'.$v;
				}
				$option[$fk['COLUMN_NAME']][$ar[$fk['REFERENCED_COLUMN_NAME']]]=$dv;			
			}
			//my_print_r($option);
		}
	}
	return $option;
}


function mk_select_from_array_return_key($name, $select_array,$disabled,$default)
{
	//print_r($select_array);
		//echo $default.'<<<<';
		
		echo '<select  '.$disabled.' name=\''.$name.'\'>';
		foreach($select_array as $key=>$value)
		{
			if($key==$default)
			{
				echo '<option  selected value=\''.$key.'\' > '.$key.'*'.$value.' </option>';
			}
			else
			{
				echo '<option  value=\''.$key.'\' > '.$key.'*'.$value.' </option>';
			}
		}
		echo '</select>';	
		return TRUE;
}


////////////user verification functions/////////////////


function verify_ap_user($u,$p)
{
	$sql='select * from user where id=\''.$u.'\'';
	$result=run_query($sql);
	if($result===FALSE){echo '???';exit(0);}
	$result_array=get_single_result($result);
	if(md5($p)==$result_array['password'])
	{
		return true;
	}
	else
	{
		echo 'wrong ap passord';exit(0);
	}
}




/////////////database functions//////////////////////
function run_query($sql)
{
	//$link=mysqli_connect('127.0.0.1',$GLOBALS['db_user'],$GLOBALS['db_pass']);
	$link=$GLOBALS['link'];
	if(!$link)
	{
		echo 'error1:'.mysqli_error($link); return false;
	}
	else
	{
		//mysqli_query($link,'set role \''.$GLOBALS['role'].'\'');
		$db_success=mysqli_select_db($link,$GLOBALS['dbname']);
	}
	
	if(!$db_success)
	{
		echo 'error2:'.mysqli_error($link); return false;
	}
	else
	{
		//$x=run_query('select current_role');
		//my_print_r(get_single_result($x));
		$result=mysqli_query($link,$sql);
	}
	
	if(!$result)
	{
		echo 'error3:'.mysqli_error($link); return false;
	}
	else
	{
		return $result;
	}	
}

function get_single_result($result)
{
		return mysqli_fetch_assoc($result);
}

function run_query_is($sql)
{
	//$link=mysqli_connect('127.0.0.1',$GLOBALS['db_user'],$GLOBALS['db_pass']);
	$link=$GLOBALS['link'];
	if(!$link)
	{
		echo 'error1:'.mysqli_error($link); return false;
	}
	else
	{
		//mysqli_query($link,'set role \''.$GLOBALS['role'].'\'');
		$db_success=mysqli_select_db($link,'information_schema');
	}
	
	if(!$db_success)
	{
		echo 'error2:'.mysqli_error($link); return false;
	}
	else
	{
		$result=mysqli_query($link,$sql);
	}
	
	if(!$result)
	{
		echo 'error3:'.mysqli_error($link); return false;
	}
	else
	{
		return $result;
	}	
}

function verify_db_user()
{
	$link=mysqli_connect('127.0.0.1',$GLOBALS['db_user'],$GLOBALS['db_pass']);
	if(!$link)
	{
		echo 'error1:'.mysqli_error($link); return false;
	}
	else
	{
		mysqli_query($link,'set role \''.$GLOBALS['role'].'\'');
	}
		
	$GLOBALS['link']=$link;

	return true;
}

function read_number($name,$id,$from,$to,$default)
{
	echo '<select title="'.$name.'" name=\''.$name.'\' id=\''.$id.'\'>';
	for($i=$from;$i<=$to;$i++)
	{
		if($i==$default)
		{
			echo '<option selected>'.$default.'</option>';
		}
		else
		{
			echo '<option >'.$i.'</option>';			
		}
	}
	echo '</select>';
	
}

function read_datetime($name,$id,$include,$default='')
{
	//64=year,32=month,16=day,8=hr,4=min,2=sec
	if($default=='')
	{
		$date=date_parse(date('Y-M-d h:r:s'));
	}
	else
	{
		$date=date_parse($default);		
	}
	//my_print_r($date);
	echo '<table><tr>';
	if(($include&32)==32)
	{
		echo '
				<td><input size=3  title=\''.$name.'_year\' min=1 max=9999
							type=number style="width:5em" placeholder=YYYY name=\''.$name.'_year\' id=\''.$id.'_year\' 
							value=\''.$date['year'].'\'></td>';
	}

	if(($include&16)==16)
	{						
		echo '		<td>';read_number($name.'_month',$id.'_month',1,12,$date['month']);echo '</td>';
	}	
	if(($include&8)==8)
	{	
		echo '		<td>';read_number($name.'_day',$id.'_day',1,31,$date['day']);echo '</td>';
	}
	if(($include&4)==4)
	{	
		echo '		<td>';read_number($name.'_hour',$id.'_hour',1,24,$date['hour']);echo '</td>';
	}
	if(($include&2)==2)
	{	
		echo '		<td>';read_number($name.'_min',$id.'_min',1,60,$date['minute']);echo '</td>';
	}
	if(($include&1)==1)
	{	
		echo '		<td>';read_number($name.'_sec',$id.'_sec',1,60,$date['second']);echo '</td>';
	}
	echo '		</tr></table>';
}

?>

