<?php
/*
	This class is responsible for creating the session of the user.
*/

class vazisession {
		private $user;
		private $name;
		private $AuthRequired;
		private $session_id;
		
		function __construct() {
				@session_start();
				$this->session_id = session_id();
				if (!isset($_SESSION['AuthRequired']) || $_SESSION['AuthRequired'] == 'true') {
					$this->AuthRequired = 'true';
				} else {
					$this->user = $_SESSION['userName'];
					$this->name = $_SESSION['Name'];
					$this->AuthRequired = 'false';
				}
			}
			
		function __destruct() {
				session_write_close();
			}
			
		public function set_userInfo($n,$u) {
				$this->user = $u; $this->name = $n;
				$_SESSION['userName'] = $u; $_SESSION['Name'] = $n; 
			}
			
		public function get_userInfo() {
				return array( array('username' => $this->user, 'name' => $this->name) );
			}
			
		public function set_AuthRequired($b) {
				$this->AuthRequired = $b;
				$_SESSION['AuthRequired'] = $b;
			}
			
		public function get_AuthRequired() {
				return $this->AuthRequired;
			}

		public function get_session_id() {
			return $this->session_id;
		}
	}
?>