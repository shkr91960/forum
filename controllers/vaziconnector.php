<?php

class vaziconnector {
		private $con;
		private $table_info;
		
		private function refresh_table_info() {
				$this->table_info = require('vazitableinfo.php');
			}
		
		public function get_table_names() {
				$table_names = array();
				foreach($this->table_info as $val) {
						array_push($table_names, $val['table_name']);
					}
				return $table_names;
			}
		
		function __construct() {
				$this->refresh_table_info();
				if(!$this->con = call_user_func_array('mysqli_connect',$GLOBALS['config']['db'])) {
						throw new Exception('Not able to connect to mysql server on connector.php.');
					}
			}
			
		function __destruct() {
				$this->con->close();
			}
			
		private function wrap_quotes($str) {
				if($str != '*') {
					$tmp = explode(".",$str);
					$s = '';
					foreach($tmp as $v) {
							$s .= '`'.$v.'`.';
						}
					$s = substr($s,0,-1);
					return $s;
				} else {
						return $str;
					}
				
			}
			
		private function ver_select($param) {
				if(isset($param)) {
						$p_keys_m = array("Fields","Tables");
						foreach($p_keys_m as $val) {
								if(!array_key_exists($val,$param)) {
										throw new Exception("Required key $val does not exists in the parameters. - connector.php.");
									}
							}
						if(array_key_exists('Logic',$param)) {
								$l_keys_m = array('logical_op','cond');
								$c_keys_m = array('col','op','val');
								foreach($l_keys_m as $val) {
										if(!array_key_exists($val,$param['Logic'])) {
												throw new Exception("Required key $val does not exists in the parameters. - connector.php.");
											}
									}
								foreach($param['Logic']['cond'] as $val) {
										foreach($c_keys_m as $v) {
												if(!array_key_exists($v,$val)) {
														throw new Exception("Required key $v does exists in the parameters. - connector.php.");
													}
											}
									}
								if(count($param['Logic']['logical_op'])+1 != count($param['Logic']['cond'])) {
										throw new Exception("Number of Conditions and Logic Operators are not correct. - connector.php.");
									}
							}
					} else {
							throw new Exception("Parameters for the select are not set - failed on connector.php.");
						}
			}

		public function select($param) {
				$this->ver_select($param);
				if( is_array($param['Fields']) && is_array($param['Tables']) ) {
						$sql = "Select " . implode(', ',array_map(array($this,"wrap_quotes"),$param['Fields'])) . " FROM " . implode(', ',array_map(array($this,"wrap_quotes"),$param['Tables']));
						$b_req = false;
						$log_str = ' ';
						if(array_key_exists('Logic',$param)) {
								$b_req = true;
								$log_str .= "WHERE `" . $param['Logic']['cond'][0]['col'] . "` " . $param['Logic']['cond'][0]['op'] . " ?";
								foreach($param['Logic']['logical_op'] as $key => $log) {
										$log_str .= " " . $log . " `" . $param['Logic']['cond'][$key+1]['col'] . "` " . $param['Logic']['cond'][$key+1]['op'] . " ?";
									}
							}
						$sql .= $log_str;
						if(isset($param["Add"])) {
								$sql .= $param["Add"];
							}
						if(!$stmt = $this->con->prepare($sql)) {
								throw new Exception("Prepare failed for query $sql - " . $this->con->error . " - connector.php.");
							}
						if($b_req) {
								$b_var[0] = '';
								foreach($param['Logic']['cond'] as $key => $val) {
										foreach($this->table_info as $t) {
												foreach($param['Tables'] as $v) {
														if(strtolower($v) == strtolower($t['table_name'])) {
																$keys = array_keys($t['col_info']);
																foreach($keys as $k => $value) {
																		if(strtolower($value) == strtolower($val['col'])) {
																				$b_var[0] .= $t['col_info'][$value];
																			}
																	}
															}
													}
											}
										$b_var[$key+1] = &$param['Logic']['cond'][$key]['val'];
									}
								if(!call_user_func_array(array($stmt,'bind_param'),$b_var)) {
										throw new Exception("Parameter Bind failed " . $stmt->error . " - connector.php.");
									}
							}
						if(!$stmt->execute()) {
								throw new Exception("Execute failed " . $stmt->error . " - connector.php.");
							}
						$result = $stmt->get_result();
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						return $rows;
					} else {
							throw new Exception("Array expected for both Fields and Tables - connector.php.");
						}
			}
			
		private function ver_create($param) {
				if(array_key_exists("TableNames",$param)) {
						$table_count = count($param["TableNames"]);
					} else {
							throw new Exception("Required key TableNames not found in the parameters - connector.php.");
						}
				if(array_key_exists("Fields",$param)) {
						$field_set_count = count($param["Fields"]);
					} else {
							throw new Exception("Required key Fields not found in the parameters - connector.php.");
						}
				if($table_count == $field_set_count) {
						foreach($param["Fields"] as $value) {
								if(array_key_exists("Names",$value)) {
										$names_count = count($value["Names"]);
									} else {
											throw new Exception("Required Key Names not found in the parameters - connector.php.");
										}
								if(array_key_exists("Types",$value)) {
										$types_count = count($value["Types"]);
									} else {
											throw new Exception("Required Key Types not found in the parameters - connector.php.");
										}
								if(array_key_exists("Lengths",$value)) {
										$len_count = count($value["Lengths"]);
									} else {
											throw new Exception("Required Key Lenghts not found in the parameters - connector.php.");
										}
								if(array_key_exists("NotNull",$value)) {
										$not_nulls_count = count($value["NotNull"]);
									} else {
											throw new Exception("Required Key NotNull not found in the parameters - connector.php.");
										}
								if(array_key_exists("Defaults",$value)) {
										$defaults_count = count($value["Defaults"]);
									} else {
											throw new Exception("Required Key Defaults not found in the parameters - connector.php.");
										}
								if( $names_count==$types_count && $names_count==$len_count ) {
										if($not_nulls_count != 0) {
												if($names_count!=$not_nulls_count) {
														throw new Exception("Not Null Counts does not matches Names count - connector.php.");
													}
											}
										if($defaults_count != 0) {
												if($names_count!=$defaults_count) {
														throw new Exception("Defaults Counts does not matches Names count - connector.php.");
													}
											}
									}
								if(array_key_exists("Primary",$value)) {
										$t = array_flip($value["Names"]);
										if(!array_key_exists($value["Primary"],$t)) {
												throw new Exception("Primary key does not exist in Names - connector.php.");
											}
									}
								if(array_key_exists("Unique",$value)) {
										$t = array_flip($value["Names"]);
										if(!array_key_exists($value["Unique"],$t)) {
												throw new Exception("Unique key does not exist in Names - connector.php.");
											}
									}
								if(array_key_exists("Key",$value)) {
										$t = array_flip($value["Names"]);
										if(!array_key_exists($value["Key"],$t)) {
												throw new Exception("Index key does not exist in Names - connector.php.");
											}
									}	
							}
					} else {
							throw new Exception("TableNames count does not match the Fields set count - connector.php.");
						}
			}

		public function create($param) {
				$this->ver_create($param);
				foreach($param["TableNames"] as $key => $table_name) {
						$sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (";
						foreach($param["Fields"][$key]["Names"] as $k => $name) {
								$s = " `" . $name . "` ";
								$s .= $param["Fields"][$key]["Types"][$k];
								$s .= "(" . $param["Fields"][$key]["Lengths"][$k] . ")";
								if( $param["Fields"][$key]["NotNull"][$k] ) {
										$s .= " NOT NULL";
									}
								if( isset($param["Fields"][$key]["Defaults"][$k]) && $param["Fields"][$key]["Defaults"][$k]!='' ) {
										if( $param["Fields"][$key]["Defaults"][$k]=='NULL' ) {
												$s .= " DEFAULT NULL";
											} else {
													$s .= " DEFAULT '" . $param["Fields"][$key]["Defaults"][$k] . "'";
												}
									}
								$sql .= $s . ',';
							}
						if(array_key_exists("Primary",$param["Fields"][$key])) {
								$sql .= " PRIMARY KEY (`" . $param["Fields"][$key]["Primary"] . "`),";
							}
						if(array_key_exists("Unique",$param["Fields"][$key])) {
								$sql .= " UNIQUE KEY `" . $param["Fields"][$key]["Unique"] . "` (`" . $param["Fields"][$key]["Unique"] . "`),";
							}
						if(array_key_exists("Key",$param["Fields"][$key])) {
								$sql .= " KEY `" . $param["Fields"][$key]["Key"] . "` (`" . $param["Fields"][$key]["Key"] . "`),";
							}
						$sql = substr($sql,0,-1);					
						$sql .= " )";
						if(array_key_exists("StorageEngine",$param["Fields"][$key])) {
								$sql .= " ENGINE=" . $param["Fields"][$key]["StorageEngine"];
							}
						if(array_key_exists("DefaultCharset",$param["Fields"][$key])) {
								$sql .= " DEFAULT CHARSET=" . $param["Fields"][$key]["DefaultCharset"];
							}
						if (!$this->con->query($sql)) {
							throw new Exception("Table creation failed: (" . $this->con->errno . ") " . $this->con->error);
						}
						$this->refresh_table_info();
					}
			}
			
		private function ver_update($param) {
				if(array_key_exists("Update",$param) && array_key_exists("Set",$param) && array_key_exists("Where",$param) ) {
						$l_keys_m = array('logical_op','cond');
						$c_keys_m = array('col','op','val');
						foreach($l_keys_m as $val) {
								if(!array_key_exists($val,$param['Where'])) {
										throw new Exception("Required key $val does not exists in the parameters. - connector.php.");
									}
							}
						foreach($param['Where']['cond'] as $val) {
								foreach($c_keys_m as $v) {
										if(!array_key_exists($v,$val)) {
												throw new Exception("Required key $v does exists in the parameters. - connector.php.");
											}
									}
							}
						if(count($param['Where']['logical_op'])+1 != count($param['Where']['cond'])) {
								throw new Exception("Number of Conditions and Logic Operators are not correct. - connector.php.");
							}
						if(!is_array($param['Update'])) {
								throw new Exception("Array expected for the key 'Update' - connector.php.");
							}
						if(!is_array($param['Set'])) {
								throw new Exception("Array expected for the key 'Set' - connector.php.");
							}
					} else {
							throw new Exception("Required Keys does not exists in update parameters - connector.php.");
						}
			}
			
		public function update($param) {
				$this->ver_update($param);
				$bind_for = array();
				$sql = "UPDATE " . implode(', ',array_map(array($this,"wrap_quotes"),$param['Update'])) . " SET";
				foreach($param['Set'] as $key => $value) {
						$sql .= " `" . $key . "` = ?,";
						$bind_for[] = array($key,$value);
					}
				$sql = substr($sql,0,-1);
				$log_str = ' WHERE';
				$i = 0;
				do {
					if($i != 0) {
							$log_str .= " " . $param['Where']['logical_op'][$i-1];
						}
					$log_str .= " `" . $param['Where']['cond'][$i]['col'] . "` " . $param['Where']['cond'][$i]['op'] . " ?";
					$bind_for[] = array($param['Where']['cond'][$i]['col'],$param['Where']['cond'][$i]['val']);
					$i++;
				} while($i<=count($param['Where']['logical_op']));
				$sql .= $log_str;
				$b_var[0] = '';
				foreach($bind_for as $key => $val) {
						foreach($this->table_info as $t) {
								foreach($param['Update'] as $v) {
										if(strtolower($v) == strtolower($t['table_name'])) {
												$keys = array_keys($t['col_info']);
												foreach($keys as $k => $value) {
														if(strtolower($value) == strtolower($val[0])) {
																$b_var[0] .= $t['col_info'][$value];
															}
													}
											}
									}
							}
						$b_var[] = &$bind_for[$key][1];
					}				
				if(!$stmt = $this->con->prepare($sql)) {
						throw new Exception("Prepare failed for query $sql - " . $this->con->error . " - connector.php.");
					}
				if(!call_user_func_array(array($stmt,'bind_param'),$b_var)) {
						throw new Exception("Parameter Bind failed " . $stmt->error . " - connector.php.");
					}
				if(!$stmt->execute()) {
						throw new Exception("Execute failed " . $stmt->error . " - connector.php.");
					}
				return $stmt->affected_rows;
			}
			
		private function ver_insert($param) {
				if(!isset($param["into"])) {
						throw new Exception("Table name not set in the parameters for create - connector.php.");
					}
				if(!isset($param["fields"])) {
						throw new Exception("Fields not set in the parameters for create - connector.php.");
					}
				if(!is_array($param["fields"])) {
						throw new Exception("Array expected for the field values in the parameters for create - connector.php.");
					}
			}
			
		public function insert($param) {
				$this->ver_insert($param);
				$sql = "INSERT INTO `" . $param["into"] . "` ( " . implode(', ',array_map(array($this,"wrap_quotes"),$param['fields'])) . " ) VALUES ";
				$f_types = '';
				foreach($param["fields"] as $val) {
						foreach($this->table_info as $t) {
								if(strtolower($param["into"]) == strtolower($t['table_name'])) {
										$keys = array_keys($t['col_info']);
										foreach($keys as $k => $value) {
												if(strtolower($value) == strtolower($val)) {
														$f_types .= $t['col_info'][$value];
													}
											}
									}
							}
					}
				$b_var[0] = '';
				foreach($param["values"] as $key => $value) {
					$sql .= " (";
					$b_var[0] .= $f_types;
						foreach($value as $k => $val) {
								$sql .= "?,";
								$b_var[] = &$param["values"][$key][$k];
							}
					$sql = substr($sql,0,-1);
					$sql .= "),";
					}
				$sql = substr($sql,0,-1);
				if(!$stmt = $this->con->prepare($sql)) {
						throw new Exception("Prepare failed for query $sql - " . $this->con->error . " - connector.php.");
					}
				if(!call_user_func_array(array($stmt,'bind_param'),$b_var)) {
						throw new Exception("Parameter Bind failed " . $stmt->error . " - connector.php.");
					}
				if(!$stmt->execute()) {
						throw new Exception("Execute failed " . $stmt->error . " - connector.php.");
					}
			}
		
		private function ver_inner_join($param) {
				//var_dump($param); echo '<br/>';
				if(array_key_exists("select",$param) && array_key_exists("from",$param) && array_key_exists("inner_join",$param) && array_key_exists("on",$param) && array_key_exists("Where",$param) ) {
						$l_keys_m = array('logical_op','cond');
						$c_keys_m = array('col','op','val');
						foreach($l_keys_m as $val) {
								if(!array_key_exists($val,$param['Where'])) {
										throw new Exception("Required key $val does not exists in the parameters. - connector.php.");
									}
							}
						foreach($param['Where']['cond'] as $val) {
								foreach($c_keys_m as $v) {
										if(!array_key_exists($v,$val)) {
												throw new Exception("Required key $v does exists in the parameters. - connector.php.");
											}
									}
							}
						if(count($param['Where']['logical_op'])+1 != count($param['Where']['cond'])) {
								throw new Exception("Number of Conditions and Logic Operators are not correct. - connector.php.");
							}
						if(!is_array($param['select'])) {
								throw new Exception("Array expected for the key 'Update' - connector.php.");
							}
						if(!is_array($param['from'])) {
								throw new Exception("Array expected for the key 'Set' - connector.php.");
							}
						if(!is_array($param['inner_join'])) {
								throw new Exception("Array expected for the key 'Set' - connector.php.");
							}
						if(!is_array($param['on'])) {
								throw new Exception("Array expected for the key 'Set' - connector.php.");
							}
					} else {
							throw new Exception("Required Keys does not exists in update parameters - connector.php.");
						}
			}
			
//$sql = "SELECT * FROM `employeesz1` INNER JOIN `q3m2y2013` ON `employeesz1`.`EMP ID` = `q3m2y2013`.`EMP ID` WHERE `q3m2y2013`.`Date` = '3' AND `q3m2y2013`.`Shift ID` = '8' AND `employeesz1`.`Zone` = '8'";
/*
$param = array (
						'select' => array('*'),
						'from' => array('employeesz1'),
						'inner_join' => array($table),
						'on' => array('employeesz1.EmpID',$table.'.EmpID'),
						'Where' => array(
								'logical_op' => array('AND','AND'),
								'cond' => array(
									array('col' => $table.'.Date', 'op' => '=', 'val' => $date),
									array('col' => $table.'.ShiftID', 'op' => '=', 'val' => '1'),
									array('col' => 'employeesz1.Zone', 'op' => '=', 'val' => '4')
								)
							)
					);
*/
			
		public function inner_join($param) {
				$this->ver_inner_join($param);
				$sql = "Select " . implode(', ',array_map(array($this,"wrap_quotes"),$param['select'])) . " FROM " . implode(', ',array_map(array($this,"wrap_quotes"),$param['from'])) . " INNER JOIN " . implode(', ',array_map(array($this,"wrap_quotes"),$param['inner_join'])) . " ON " . implode(' = ',array_map(array($this,"wrap_quotes"),$param['on']));
				$log_str = ' WHERE';
				$i = 0;
				do {
					if($i != 0) {
							$log_str .= " " . $param['Where']['logical_op'][$i-1];
						}
					$log_str .= " " . $this->wrap_quotes($param['Where']['cond'][$i]['col']) . $param['Where']['cond'][$i]['op'] . " ?";
					$bind_for[] = array($param['Where']['cond'][$i]['col'],$param['Where']['cond'][$i]['val']);
					$i++;
				} while($i<=count($param['Where']['logical_op']));
				$sql .= $log_str;
				//echo $sql;
				//var_dump($bind_for);
				$b_var[0] = '';
				foreach($bind_for as $key => $val) {
						$temp = explode(".",$val[0]);
						$tbl = $temp[0];
						$fld = $temp[1];
						foreach($this->table_info as $t) {
								if(strtolower($tbl) == strtolower($t['table_name'])) {
										$keys = array_keys($t['col_info']);
										foreach($keys as $k => $value) {
												if(strtolower($value) == strtolower($fld)) {
														$b_var[0] .= $t['col_info'][$value];
													}
											}
									}
							}
						$b_var[] = &$bind_for[$key][1];
					}
				if(!$stmt = $this->con->prepare($sql)) {
						throw new Exception("Prepare failed for query $sql - " . $this->con->error . " - connector.php.");
					}
				if(!call_user_func_array(array($stmt,'bind_param'),$b_var)) {
						throw new Exception("Parameter Bind failed " . $stmt->error . " - connector.php.");
					}
				if(!$stmt->execute()) {
						throw new Exception("Execute failed " . $stmt->error . " - connector.php.");
					}
				$result = $stmt->get_result();
				$rows = $result->fetch_all(MYSQLI_ASSOC);
				//var_dump($rows);
				return $rows;
				//var_dump($b_var);
			}
	}
?>