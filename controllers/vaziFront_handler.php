<?php

spl_autoload_register();

$config = require_once('vaziconfig.php');
$user_info = array();
$file_name = $_SERVER['REQUEST_URI'];
$complete_post_auth = true;

$valid_requests_array = array(

	'/apis/displayallusers.php' =>  array('auth_required' => true, 'handle' => 'display_users', 'controller' => 'users'),
	'/apis/login.php' =>  array('auth_required' => true, 'handle' => 'login', 'controller' => 'users'),
	'/apis/index.php' =>  array('auth_required' => false, 'handle' => 'index', 'controller' => 'users'),
	'/apis/registration.php' =>  array('auth_required' => false, 'handle' => 'registration', 'controller' => 'users'),
	'/apis/getuserinfo.php' =>  array('auth_required' => true, 'handle' => 'user_info', 'controller' => 'users'),
	'/apis/getuserinfobyid.php' =>  array('auth_required' => false, 'handle' => 'user_info_byid', 'controller' => 'users'),

	'/apis/getforumpost.php' =>  array('auth_required' => false, 'handle' => 'get_forum_post', 'controller' => 'questions'),
	'/apis/getquestions.php' =>  array('auth_required' => false, 'handle' => 'get_questions', 'controller' => 'questions'),
	'/apis/ques.php' =>  array('auth_required' => true, 'handle' => 'post_question', 'controller' => 'questions'),
	'/apis/getanswer.php' =>  array('auth_required' => false, 'handle' => 'get_answer', 'controller' => 'comment'),
	'/apis/answ.php' =>  array('auth_required' => true, 'handle' => 'post_answer', 'controller' => 'comment'),
	'/apis/markcorrect.php' =>  array('auth_required' => true, 'handle' => 'mark_correct', 'controller' => 'comment'),
	'/apis/getforum.php' =>  array('auth_required' => false, 'handle' => 'get_forum', 'controller' => 'comment')


	/*
	//		Category Controllers		//
	
	'/apis/categoryHier.php' =>  array('auth_required' => false, 'handle' => 'get_category_hier', 'controller' => 'categorylist'),
	'/apis/categoryproducts.php' =>  array('auth_required' => false, 'handle' => 'get_category_products', 'controller' => 'categorylist'),
	'/apis/getpath.php' =>  array('auth_required' => false, 'handle' => 'get_breadcrumb', 'controller' => 'categorylist'),
	

	//		Product Controllers			//
	
	'/apis/productshome.php' =>  array('auth_required' => false, 'handle' => 'get_products_home', 'controller' => 'productlist'),
	'/apis/getproductdetail.php' =>  array('auth_required' => false, 'handle' => 'get_product_info', 'controller' => 'productlist'),

	
	//		CRM Controllers				//
	
	'/apis/isauthreq.php' =>  array('auth_required' => false, 'handle' => 'return_auth_req', 'controller' => 'crm'),
	'/apis/getuserinfo.php' =>  array('auth_required' => true, 'handle' => 'return_user_info', 'controller' => 'crm'),
	'/apis/getshippinginfo.php' =>  array('auth_required' => true, 'handle' => 'return_shipping_info', 'controller' => 'crm'),
	'/apis/logout.php' =>  array('auth_required' => false, 'handle' => 'clear_user_info', 'controller' => 'crm'),
	//'/apis/register.php' =>  array('auth_required' => false, 'handle' => 'register_user', 'controller' => 'crm'),
	'/apis/registertpl.php' =>  array('auth_required' => false, 'handle' => 'return_register_tpl', 'controller' => 'crm'),

	
	//		Cart Controllers			//

	'/apis/addtocart.php' =>  array('auth_required' => false, 'handle' => 'addtocart', 'controller' => 'cart'),
	'/apis/getcartstatus.php' =>  array('auth_required' => false, 'handle' => 'get_cart_status', 'controller' => 'cart'),
	'/apis/dropbag.php' =>  array('auth_required' => false, 'handle' => 'drop_bag', 'controller' => 'cart'),
	'/apis/submitorder.php' =>  array('auth_required' => false, 'handle' => 'submit_order', 'controller' => 'cart'),


	//		Search Controllers			//

	'/apis/searchresults.php' =>  array('auth_required' => false, 'handle' => 'return_all_products', 'controller' => 'search'),

	*/
);
	
try {
	
	if ($valid_requests_array[$file_name]['auth_required']) {
		$authorise = new vaziauth;
		if(!$authorise -> is_authenticated()) {
			$complete_post_auth = false;
			header('HTTP/1.1 401 Unauthorized');
		} else {
				$user_info = $authorise -> get_user_info(); 
			}
	}

	if ($complete_post_auth) {
		$controller = 'vazi' . $valid_requests_array[$file_name]['controller'];
		$handle = $valid_requests_array[$file_name]['handle'];
		$controller_obj = new $controller;
		call_user_func_array(array($controller_obj, $handle), array());
	}

} catch(Exception $e) {
			echo 'Caught Exception: ', $e->getMessage(), "\n";
		}
?>