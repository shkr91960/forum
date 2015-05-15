<?php 
/*
	This class responds with the list of products.
*/

class vaziproductlist {
	private $query;
	private $param;
	private $operation;
	private $result;
	private $post_data_processing_required;
	private $post_data_processing_method;
	private $json_encode_required;

	function __construct() {
		$this->query = new vaziconnector;
		$this->post_data_processing_required = false;
		$this->json_encode_required = false;
	}

	function __destruct() {
		$this->result = call_user_func_array(array($this->query, $this->operation), array($this->param));
		if ($this->post_data_processing_required) {
			call_user_func_array(array($this, $this->post_data_processing_method), array($this->param));
		}
		header('Content-type: application/json');
		if ($this->json_encode_required) {
			echo json_encode($this->result);
		} else {
			echo $this->result;
		}
		
		//echo "this is some text";
	}

	public function get_products_home() {
		$this->param = array(
			'Fields' => array('*'),
			'Tables' => array('products'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'tags', 'op' => '=', 'val' => "home", ),
				),
			),
		);

		$this->operation = 'select';
		$this->post_data_processing_required = true;
		$this->post_data_processing_method = 'get_products_home_pd';
		$this->json_encode_required = true;
	}

	public function get_products_home_pd() {
		$new = array();
		foreach ($this->result as $key => $value) {
			$new[$value['cat1']][] = $value;
		}
		$this->result = $new;
	}

	public function get_product_info() {
		$post_data = json_decode(file_get_contents("php://input"));
		//echo $post_data->code;

		$this->param = array(
			'Fields' => array('*'),
			'Tables' => array('products'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'productcode', 'op' => '=', 'val' => $post_data->productcode, ),
				),
			),
		);

		$this->operation = 'select';
		$this->post_data_processing_required = true;
		$this->post_data_processing_method = 'get_product_info_pd';
	}
	public function get_product_info_pd() {
		$this->result = '{ "product":' . json_encode($this->result) . '}';
	}
}
?>