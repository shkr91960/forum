<?php 
/*
	This class responds with the list of all the products.
*/

class vazicart {
	private $session;
	private $query;
	private $crm;
	private $emails;

	function __construct() {
		$this->session = new vazisession;
		$this->query = new vaziconnector;
		$this->crm = new vazicrm;
		$this->emails = new vaziemails;
	}

	private function create_order($info,$crmid) {
		$param = array(
			'Fields' => array('products'),
			'Tables' => array('cart'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'sessionid', 'op' => '=', 'val' => $this->session->get_session_id()),
				),
			),
		);
		$result = $this->query->select($param);
		if (count($result) == 0) {
			return false;
		} else {
			$products = json_decode($result[0]['products'],true);
			$address = $info->address . ', ' . $info->city . ', ' . $info->state . ', ' . $info->pincode;
			$phone = $info->phone;
			$timestamp = time();
			if (empty($products)) {
				return false;
			} else {
				$insert_param = array(
					'into' => "orders",
					'fields' => array("crmid","products","address","phone","timestamp"),
					'values' => array(array($crmid,json_encode($products),$address,$phone,$timestamp))
				);
				$this->query->insert($insert_param);
				$param = array(
					'Fields' => array('ordernumber'),
					'Tables' => array('orders'),
					'Logic' => array(
						'logical_op' => array('AND','AND'),
						'cond' => array(
							 array( 'col' => 'crmid', 'op' => '=', 'val' => $crmid),
							 array( 'col' => 'products', 'op' => '=', 'val' => json_encode($products)),
							 array( 'col' => 'timestamp', 'op' => '=', 'val' => $timestamp),
						),
					),
				);
				$this->result = $this->query->select($param);
				$ordernumber = $this->result[0]['ordernumber'];
				$this->emails->order_email($ordernumber,$crmid,$info,$products);
				$this->drop_bag();
				return $ordernumber;
			}
		}

	}

	public function submit_order() {
		$post_data = json_decode(file_get_contents("php://input"));
		$crmid = $this->crm->if_user_exists($post_data->Info->email);
		if (!$crmid) {
			$ordernumber = $this->create_order($post_data->Info,$this->crm->register_user($post_data->Info));
		} else {
			$ordernumber = $this->create_order($post_data->Info,$crmid);
		}
		if (!$ordernumber) {
			header('Content-type: application/json');
			echo '{ "status":"failure" }';
		} else {
			header('Content-type: application/json');
			echo '{ "ordernumber":' . $ordernumber . ', "status":"success" }';
		}
	}

	public function drop_bag() {
		$param = array(
			'Fields' => array('products'),
			'Tables' => array('cart'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'sessionid', 'op' => '=', 'val' => $this->session->get_session_id()),
				),
			),
		);
		$result = $this->query->select($param);
		if (count($result) != 0) {
			$param = array(
				'Update' => array('cart'),
				'Set' => array('products' => '{}', 'timestamp' => time()),
				'Where' => array(
						'logical_op' => array(),
						'cond' => array(
							array('col' => 'sessionid', 'op' => '=', 'val' => $this->session->get_session_id())
						)
					)
				);
			$this->query->update($param);
		}
	}

	public function get_cart_status() {
		$param = array(
			'Fields' => array('products'),
			'Tables' => array('cart'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'sessionid', 'op' => '=', 'val' => $this->session->get_session_id()),
				),
			),
		);
		$result = $this->query->select($param);
		header('Content-type: application/json');
		if (count($result) == 0) {
			echo '{"status": {}}';
		} else {
			echo '{"status": ' . $result[0]['products'] . '}';
		}
	}

	public function addtocart() {
		$post_data = json_decode(file_get_contents("php://input"));
		$param = array(
			'Fields' => array('products'),
			'Tables' => array('cart'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'sessionid', 'op' => '=', 'val' => $this->session->get_session_id()),
				),
			),
		);
		$result = $this->query->select($param);

		if (count($result) == 0) {
			$insert_param = array(
				'into' => "cart",
				'fields' => array("sessionid","products","timestamp"),
				'values' => array(array($this->session->get_session_id(),json_encode($post_data),time()))
			);
			foreach ($post_data as $key => $value) {
				if ($value->quantity > 0) {
					$this->query->insert($insert_param);
				}
			}
		} else {
			if (strlen($result[0]['products']) == 0) {
				$cart_products = $post_data;
			} else {
				$cart_products = json_decode($result[0]['products']);
				foreach ($post_data as $key => $value) {
					if (isset($cart_products->$key)) {
						if ($value->quantity <= 0) {
							unset($cart_products->$key);
						} else {
							$cart_products->$key->quantity = $value->quantity;
						}
					} else {
						if ($value->quantity > 0) {
							$cart_products->$key = $value;
						}
					}
				}
			}

			$param = array(
				'Update' => array('cart'),
				'Set' => array('products' => json_encode($cart_products), 'timestamp' => time()),
				'Where' => array(
						'logical_op' => array(),
						'cond' => array(
							array('col' => 'sessionid', 'op' => '=', 'val' => $this->session->get_session_id())
						)
					)
				);
			$this->query->update($param);	
		}
	}
}