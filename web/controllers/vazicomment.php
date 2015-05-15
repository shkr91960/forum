<?php

class vazicomment{
	private $result;
	private $session;

	function __construct() {
		$this->query = new vaziconnector;
		$this->session = new vazisession;
	}

	public function mark_correct() {

		$correct = $_POST['correct'];
		$comment_id = $_POST['commentId'];

		$correct = $correct?0:1;

		$param = array(
			'Update' => array('comments'),
			'Set' => array('correct' => $correct),
			'Where' => array(
					'logical_op' => array(),
					'cond' => array(
						array('col' => 'commentId', 'op' => '=', 'val' => $comment_id)
					)
				)
			);
		$this->query->update($param);
	}

	public function get_forum() {
		$qid = $_POST['comment_id'];
		
		$param = array(
			'Fields' => array('*'),
			'Tables' => array('comments'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'commentId', 'op' => '=', 'val' => $cid, ),
				),
			),
		);
		$this->result = $this->query -> select($param);

		header('Content-type: application/json');
		echo json_encode($this->result);
	}
	public function get_answer() {

		$qid = $_POST['qid'];
	  
		$param = array(
			'Fields' => array('*'),
			'Tables' => array('comments'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'questionId', 'op' => '=', 'val' => $qid, ),
				),
			),
		);
		$this->result = $this->query -> select($param);

		foreach ($this->result as $key => $value) {
			$param = array(
				'Fields' => array('name'),
				'Tables' => array('user'),
				'Logic' => array(
					'logical_op' => array(),
					'cond' => array(
						 array( 'col' => 'userid', 'op' => '=', 'val' => $value['userid'] , ),
					),
				),
			);

			$result = $this->query->select($param);

			//$this->result->name = new StdClass;
			$this->result[$key]['name'] = $result[0]['name'];
			$this->result[$key]['comment'] = html_entity_decode($this->result[$key]['comment']);

			//echo json_encode($result[0]['name']);
			
		}

		header('Content-type: application/json');
		echo json_encode($this->result);
	}
	public function post_answer(){
		$answ = htmlentities($_POST['comment']);
		$qid = htmlentities($_POST['qid']);

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
					'into' => "comments",
					'fields' => array("comment","questionId","userid"),
					'values' => array(array($answ,$qid,$result[0]['userid']))
				);
			
		$this->query->insert($insert_param);
		echo "success";
	}
}