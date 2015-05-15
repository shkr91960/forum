<?php
/*
	This class is responsible for returning the search results.
*/

class vazisearch {
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

		public function return_all_products() {
			$this->param = array(
				'Fields' => array('product','cat3','cat3code','brand','Keywords','productcode','imagename','weight','unit','mrp','hbprice'),
				'Tables' => array('products'),
				//'Add' => 'LIMIT 0 , 100'
			);

			$this->operation = 'select';
			$this->post_data_processing_required = true;
			$this->post_data_processing_method = 'return_all_products_pd';
			//$this->json_encode_required = true;
		}

		public function return_all_products_pd() {
			$temp = array();
			foreach ($this->result as $key => $value) {
				$value['incart'] = 0;
				$temp[$value['productcode']] = $value;
			}
			//$this->result = $temp;
			$this->result = '{ "results":' . json_encode($temp) . '}';
		}

	}
?>