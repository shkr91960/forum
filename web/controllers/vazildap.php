<?php
/*
	This class is responsible for connecting to the directory and authenticating the user
*/

class vazildap {
		private $ldap;
		private $bind;
		private $ldap_host;
		private $ldap_dn;

		
		function __construct() {
				$this->ldap_host = $GLOBALS['config']['ldap_host'];
				$this->ldap_dn = $GLOBALS['config']['ldap_dn'];
				$this->employee_number = '';
			}
			
		protected function is_ldap_connected() {
				if($this->ldap = ldap_connect($this->ldap_host)) {
						return true;
					} else {
							throw new Exception('Not able to connect to the ldap host - ldap.php');
						}
			}
			
		protected function is_ldap_bound($u,$p) {
			//echo 'ldap auth occurred <br/>';
			return true; //Comment out this line for the ldap authentication to occur.
				if($this->is_ldap_connected()) {
						//ldap_set_option($this->ldap, LDAP_OPT_DEBUG_LEVEL, 7);
						ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
						if($this->bind = ldap_bind($this->ldap,'CN='.$u.','.$this->ldap_dn,$p)) {
								$result = ldap_search($this->ldap,$this->ldap_dn,'(SAMAccountName=' . $u . ')',array("employeenumber"));
								if($result) {
									$entries = ldap_get_entries($this->ldap,$result);
									//var_dump($entries);
									if(isset($entries[0]['employeenumber'])) {
											$this->employee_number = $entries[0]['employeenumber'][0];
										}
									}
								return true;
							} else {
									return false;
								}
					}
			}
	}
?>