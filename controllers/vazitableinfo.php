<?php
//$config = require_once('vaziConfig.php');

if(!$con = call_user_func_array('mysqli_connect',$GLOBALS['config']['db'])) {
		throw new Exception("Not able to connect to the mysql server. Please check the config.php and make sure db has correct values.");
	}
	
if(!$stmt = $con -> prepare('SHOW TABLES')) {
		throw new Exception("Not able to prepare the query 'Show Tables' on tableinfo.php.");
	} else {
			$stmt->execute();
			$result = $stmt->get_result();
			$tables = $result->fetch_all(MYSQLI_ASSOC);
		}
		
if(isset($tables)) {
		if(!defined("INDEX")) {
			define("INDEX",'Tables_in_' . $GLOBALS['config']['db']['dataBase']);
			}
		$table_des = array();
		$pattern = '/\(\d*\)/';
		foreach($tables as $tbl) {
				if(!$stmt = $con -> prepare("DESCRIBE " . $tbl[INDEX])) {
						//var_dump($tbl[INDEX]);
						throw new Exception("Not able to prepare the query 'Describe' on tableinfo.php.");
					} else {
							$stmt->execute();
							$result = $stmt->get_result();
							$table_fields = $result->fetch_all(MYSQLI_ASSOC);
							$tbl_des = array();
							foreach($table_fields as $field) {
									array_push($tbl_des,array('Field' => $field['Field'], 'Type' => preg_replace($pattern,'',$field['Type'])));
								}
							$table_des[$tbl[INDEX]] = $tbl_des;
						}
			}
	} else {
			throw new Exception("Variable \$tables not set in tableinfo.php.");
		}
	
$type_reference = array(
	'int' => 'i','text' => 's','varchar' => 's','tinyint' => 'i','float' => 'i',
);

if(isset($table_des)) {
		$table_info = array();
		foreach($table_des as $key => $value) {
				$tablename = $key;
				$n_cols = count($value);
				$col_info = array();
				foreach($value as $v) {
						$col_info[$v['Field']] = $type_reference[$v['Type']];
					}
				array_push($table_info,array( 'table_name' => $tablename,'n_cols' => $n_cols,'col_info' => $col_info, ));
			}
	} else {
			throw new Exception("Variable \$table_des not set in tableinfo.php.");
		}
	
$con -> close();
//var_dump($table_info); echo '<br/>';
return $table_info;
?>