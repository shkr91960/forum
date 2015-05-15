<?php

class vaziquestions{
	private $result;
	private $session;

	function __construct() {
		$this->query = new vaziconnector;
		$this->session = new vazisession;
	}

	public function get_forum_post() {
		$qid = $_POST['question_id'];
		
		$param = array(
			'Fields' => array('*'),
			'Tables' => array('question'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'qid', 'op' => '=', 'val' => $qid, ),
				),
			),
		);
		$this->result = $this->query -> select($param);

		$param = array(
			'Fields' => array('name'),
			'Tables' => array('user'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'userid', 'op' => '=', 'val' => $this->result[0]['userid'], ),
				),
			),
		);

		$result = $this->query->select($param);

		$this->result[0]['name'] = $result[0]['name'];
		$this->result[0]['question'] = html_entity_decode($this->result[0]['question']);

		header('Content-type: application/json');
		echo json_encode($this->result);
	}
	public function get_questions($value='') {
	  
		$param = array(
			'Fields' => array('*'),
			'Tables' => array('question'),
		);
		$this->result = $this->query -> select($param);

		header('Content-type: application/json');
		echo json_encode($this->result);
	}
	public function post_question(){
		$title = htmlentities($_POST['title']);
		$ques = htmlentities($_POST['ta']);

		$user = $this->session->get_userInfo();

		$param = array(
			'Fields' => array('userid'),
			'Tables' => array('user'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'username', 'op' => '=', 'val' => $user[0]['username'], ),
				),
			),
		);

		$result = $this->query->select($param);

		$insert_param = array(
					'into' => "question",
					'fields' => array("title","question","userid"),
					'values' => array(array($title,$ques,$result[0]['userid']))
				);
			
		$this->query->insert($insert_param);
		echo "success";
	}
}