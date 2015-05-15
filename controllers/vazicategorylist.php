<?php 
/*
	This class responds with the list of all the products.
*/

class vazicategorylist {
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
	}

	public function get_category_hier() {
		$this->param = array(
			'Fields' => array('*'),
			'Tables' => array('categories'),
			'Add' => '',
		);
		$this->operation = 'select';
		$this->post_data_processing_required = true;
		$this->json_encode_required = true;
		$this->post_data_processing_method = 'get_category_hier_pd';
	}

	private function get_category_hier_pd() {
		$new = array();
		foreach ($this->result as $key => $value) {
			$new[$value['cat1']][$value['cat2']][] = array('code' => $value['combcode'], 'name' => $value['cat3'], );
		}
		$this->result = $new;
	}

	public function get_category_products() {
		$post_data = json_decode(file_get_contents("php://input"));

		//echo $post_data->categorycode;
		$cat1 = $post_data->categorycode;
		$cat3 = substr($cat1, strrpos($cat1, "-")+1);
		$cat1 = substr_replace($cat1, '', strrpos($cat1, "-"));
		$cat2 = substr($cat1, strrpos($cat1, "-")+1);
		$cat1 = substr_replace($cat1, '', strrpos($cat1, "-"));

		$this->param = array(
			'Fields' => array('*'),
			'Tables' => array('products'),
			'Logic' => array(
				'logical_op' => array('AND','AND'),
				'cond' => array(
					 array( 'col' => 'cat1code', 'op' => '=', 'val' => $cat1, ),
					 array( 'col' => 'cat2code', 'op' => '=', 'val' => $cat2, ),
					 array( 'col' => 'cat3code', 'op' => '=', 'val' => $cat3, ),
				),
			),

		);
		$this->operation = 'select';
		$this->post_data_processing_required = true;
		$this->post_data_processing_method = 'get_category_products_pd';
	}

	private function get_category_products_pd() {
		$this->result = '{ "products":' . json_encode($this->result) . '}';
	}

	public function get_breadcrumb() {
		$post_data = json_decode(file_get_contents("php://input"));
		//var_dump($post_data);
		$this->param = array(
			'Fields' => array('*'),
			'Tables' => array('categories'),
			'Logic' => array(
				'logical_op' => array(),
				'cond' => array(
					 array( 'col' => 'combcode', 'op' => '=', 'val' => $post_data->code, ),
				),
			),
		);
		$this->operation = 'select';
		$this->post_data_processing_required = true;
		$this->json_encode_required = false;
		$this->post_data_processing_method = 'get_breadcrumb_pd';
	}

	public function get_breadcrumb_pd() {
		$this->result = '{ "brd":' . json_encode($this->result) . '}';
	}
}

?>