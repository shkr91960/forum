<?php
/*
	This class is responsible for users related methods.
*/

class vaziusers {
	private $result;
	private $session;

	function __construct() {
		$this->query = new vaziconnector;
		$this->session = new vazisession;
	}

	public function user_info_byid() {
		$id = $_POST['userid'];

		$param = array(
			'Fields' => array('name','userid'),
			'Tables' => array('user'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'userid', 'op' => '=', 'val' => $id, ),
				),
			),
		);

		$this->result = $this->query -> select($param);

		header('Content-type: application/json');
		echo json_encode($this->result);
	}

	public function user_info() {
		$loggedUser = $this->session->get_userInfo();

		$param = array(
			'Fields' => array('username','name','userid'),
			'Tables' => array('user'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'username', 'op' => '=', 'val' => $loggedUser[0]["username"], ),
				),
			),
		);

		$this->result = $this->query -> select($param);
		header('Content-type: application/json');
		echo json_encode($this->result);
	}

	public function display_users() {
		$param = array(
			'Fields' => array('*'),
			'Tables' => array('user'),
			/*'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'email', 'op' => '=', 'val' => $email, ),
				),
			),*/
		);
	$this->result = $this->query -> select($param);

		echo "<html><head></head><body>";

		echo "<table><tr><th>Name</th><th>Email</th><th>User ID</th><th>Points</th></tr>";

		echo "<tbody>"; 

		foreach ($this->result as $value) {
		    echo "<tr><td>" . $value['name'] . "</td><td>" . $value['username'] . "</td><td>" . $value['userid'] . "</td><td>" . $value['points'] . "</td></tr>";
		}

		echo "</table>";

		echo "</body></html>";
	}

	public function login() {
		echo "Login successful";
	}

	public function index() {
		$ask = $_POST['ans'];
		echo "$ask";
	}

    public function registration() {
		$first=$_POST['first'];
		$last=$_POST['last'];
		$emailid=$_POST['emailid'];
		$passwrd=$_POST['passwrd'];
		$cpasswrd=$_POST['cpasswrd'];

		if ($passwrd == $cpasswrd && $passwrd != '' && $first != '' && $emailid != '') {
			$param = array(
				'Fields' => array('name'),
				'Tables' => array('user'),
				'Logic' => array(
					'logical_op' => array(),
					'cond' => array(
						 array( 'col' => 'username', 'op' => '=', 'val' => $emailid, )
					),
				),
			);
			$this->result = $this->query -> select($param);

			if (count($this->result)) {
				echo "Email already exists, please continue to log in.";
			} else {
				$password_hash = new vazipasswordhash;

				$insert_param = array(
					'into' => "user",
					'fields' => array("name","username","password"),
					'values' => array(array($first . " " . $last, $emailid, $password_hash->create_hash($passwrd)))
				);
			
				$this->query->insert($insert_param);
				
				echo "<html>
				<head>
					<title>Registration successful</title>
					<meta http-equiv='refresh' content='3; /' />
				</head>
				<body>
					Registration Successful!! Redirecting to the home page...
				</body>
				</html>";
			}
			
		} else {
			echo "Error with the form submission, please go back and correct this.";
		}
	}	

}