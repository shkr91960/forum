<?php
/*
	This class is responsible for the CRM user related Queries.
*/

class vazicrm {
	private $session;
	private $query;
	private $result;
	private $pass_hash;

	function __construct() {
		$this->session = new vazisession;
		$this->query = new vaziconnector;
		$this->pass_hash = new vazipasswordhash;
	}

	public function if_user_exists($email) {
		$param = array(
			'Fields' => array('*'),
			'Tables' => array('crm_users'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'email', 'op' => '=', 'val' => $email, ),
				),
			),
		);
		$this->result = $this->query -> select($param);
		if (count($this->result)==0) {
			return false;
		} else {
			return $this->result[0]['crmid'];
			//return true;
		}

	}

	public function clear_user_info() {
		$this->session->set_AuthRequired('true');
		$this->session->set_userInfo('','');
	}

	public function return_user_info() {
		header('Content-type: application/json');
		echo json_encode($this->session->get_userInfo());
	}

	public function return_auth_req() {
		header('Content-type: application/json');
		echo '{"authReq":"'.$this->session->get_AuthRequired().'"}';
	}

	public function register_user($info) {
		$insert_param = array(
			'into' => "crm_users",
			'fields' => array("Name","email","username","address","city","state","pincode","phone"),
			'values' => array(array($info->Name,$info->email,$info->email,$info->address,$info->city,$info->state,$info->pincode,$info->phone))
		);
		//header('Content-type: application/json');
		//echo json_encode($insert_param);
		$this->query->insert($insert_param);
		return $this->if_user_exists($info->email);
	}

	public function return_register_tpl() {
		header('Content-type: application/json');
		echo "'user': { 'Name':'', 'email':'', 'password': { one:'', two:'' }}";
	}

	public function return_shipping_info() {
		$user = $this->session->get_userInfo();
		$email =  $user[0]['username'];
		$param = array(
			'Fields' => array('Name','email','phone','address','city','state','pincode'),
			'Tables' => array('crm_users'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'email', 'op' => '=', 'val' => $email, ),
				),
			),
		);
		$this->result = $this->query -> select($param);
		header('Content-type: application/json');
		echo '{ "info": ' . json_encode($this->result[0]) . '}';
	} 

	public function no_action() {}
}
?>