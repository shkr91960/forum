<?php
/*
	This class is responsible for the http-authentication of the user.
*/

class vaziauth {
		private $username;
		private $password;
		private $session;
		private $pass_hash;
		public $user_info;

		function __construct() {
			$this->session = new vazisession;
			$this->pass_hash = new vazipasswordhash;
		}
			
		private function request_authentication () {
				header('WWW-Authenticate: Basic realm="CRM Portal"');
    			header('HTTP/1.1 401 Unauthorized');
			}
			
		private function is_token_sent() {
				if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
						return true;
					} else {
							return false;
						}
			}
			
		public function get_user_info() {
				return $this->user_info;
			}

		public function ldap_auth_method() {
			$this->username = $_SERVER['PHP_AUTH_USER'];
			$this->password = $_SERVER['PHP_AUTH_PW'];
			$ldap = new vazildap;
			if ($ldap->is_ldap_bound($this->username,$this->password)) {
				$query = new vaziconnector;
				$tables = array('crm_users');
				foreach($tables as $tbl_name) {
						$param = array(
								'Fields' => array('name','ldap'),
								'Tables' => array($tbl_name),
								'Logic' => array(
									'logical_op' => array(),
									'cond' => array(
										 array( 'col' => 'ldap', 'op' => '=', 'val' => $this->username, ),
									),
								),
							);
						$result = $query -> select($param);
						if(count($result) != 0) {
								$this->user_info = $result;
								break;
							}
					}
				if(count($this->user_info) != 0) {
						//var_dump($this->user_info); echo 'Line number 58 <br>';
						$this->session->set_userInfo($this->user_info[0]['name'],$this->user_info[0]['ldap']);
						$this->session->set_AuthRequired('false');
					} else {
							$insert_param = array(
								'into' => "crm_users",
								'fields' => array("Name","ldap","Progress"),
								'values' => array(array($ldap->displayname,$this->username,"1"))
							);
							$query->insert($insert_param);
						}
				return true;
					
			} else {
				return false;
			}
		}

		public function mysql_auth_method() {
			$this->username = $_SERVER['PHP_AUTH_USER'];
			$this->password = $_SERVER['PHP_AUTH_PW'];
			$query = new vaziconnector;
			$tables = array('user');
			foreach($tables as $tbl_name) {
					$param = array(
							'Fields' => array('name','username','password'),
							'Tables' => array($tbl_name),
							'Logic' => array(
								'logical_op' => array(),
								'cond' => array(
									 array( 'col' => 'username', 'op' => '=', 'val' => $this->username, ),
									 //array( 'col' => 'password', 'op' => '=', 'val' => $this->password, ),
								),
							),
						);
					$result = $query -> select($param);
					if(count($result) != 0) {
							$this->user_info = $result;
							break;
						}
				}
			if(count($this->user_info)!=0) {
					//var_dump($this->user_info); echo 'Line number 100 <br>';
					if($this->pass_hash->validate_password($this->password,$this->user_info[0]['password'])) { 
						$this->session->set_userInfo($this->user_info[0]['name'],$this->user_info[0]['username']);
						$this->session->set_AuthRequired('false');
						return true;
					} else {
						return false;
					}
				} else {
						return false;
					}
		}
		
		public function is_authenticated() {
			if ($this->session->get_AuthRequired() == 'true') {
				if($this->is_token_sent()) {
					if ($GLOBALS['config']['ldap_auth']) {
						return $this->ldap_auth_method();
					} else {
						return $this->mysql_auth_method();
					}			
				} else {
					//$this->request_authentication();
					return false;
				}
			} else {
					$this->user_info = $this->session->get_userInfo();
					//var_dump($this->user_info); echo 'Line number 123 <br>';
					return true;
				}
		}
	}

?>