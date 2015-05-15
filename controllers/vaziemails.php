<?php 
/*
	This class is responsible for sending all the emails.
*/

class vaziemails {
	private $to;
	private $subject;
	private $headers;
	private $message;
	private $product_row_tpl;

	function __construct() {
		$this->to = $GLOBALS['config']['recipients'];
		$this->product_row_tpl = '<tr> <td valign="top" style="background-color: #ffffff;" bgcolor="#ffffff">--Serial Here--</td> <td valign="top" style="background-color: #ffffff;" bgcolor="#ffffff">--Product Name Here--</td> <td valign="top" style="background-color: #ffffff;" bgcolor="#ffffff">--Quantity Here--</td> <td valign="top" style="background-color: #ffffff;" bgcolor="#ffffff">--Unit Price Here--</td><td valign="top" style="background-color: #ffffff;" bgcolor="#ffffff">--Price Here--</td> </tr> ';
		$this->headers = 'From:  no_reply@happybatua.com' . "\r\n" . 'Content-type: text/html; charset=utf-8' . "\r\n";
	}

	private function cleanupEmail($email) {
		$email = htmlentities($email,ENT_COMPAT,'UTF-8');
		$email = preg_replace('=((<CR>|<LF>|0x0A/%0A|0x0D/%0D|\\n|\\r)\S).*=i', null, $email);
		return $email;
	}

	private function cleanupMessage($message) {
		$message = wordwrap($message, 70, "\r\n");
		return $message;
	}

	public function order_email($order_number,$crmid,$info,$products) {
		$email_tpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tpls' . DIRECTORY_SEPARATOR . 'email.html');

		$search = array('--Name Here--', '--Email Here--', '--Order Number Here--', '--Address Here--', '--Phone Here--');
		$replace_with = array(htmlentities($info->Name), htmlentities($info->email), htmlentities($order_number), htmlentities($info->address . ', ' . $info->city . ', ' . $info->state . ', ' . $info->pincode), htmlentities($info->phone));

		$email_tpl = str_replace($search, $replace_with, $email_tpl);

		$search = array('--Serial Here--', '--Product Name Here--', '--Quantity Here--','--Unit Price Here--', '--Price Here--');

		$insert_final = '';
		$serial = 1;
		$total = 0;

		foreach ($products as $key => $value) {
			$replace_with = array(htmlentities($serial++), htmlentities($value['displayName']), htmlentities($value['quantity']), htmlentities($value['price']), htmlentities($value['price']*$value['quantity']));
			$insert_final .= str_replace($search, $replace_with, $this->product_row_tpl);
			$total += $value['quantity']*$value['price'];
		}

		if ($total < 200 && $total > 0) {
			$total += 10;
		}

		$search = array('--Products Here--', '--Total Here--');
		$replace_with = array($insert_final,htmlentities($total));
		$email_tpl = str_replace($search, $replace_with, $email_tpl);

		$this->message = $this->cleanupMessage($email_tpl);
		$this->subject = 'New Order Submission - ' . $order_number;
		$sent = @mail($this->to, $this->subject, $this->message, $this->headers);

		/*if($sent) {
			echo '{"FormResponse": { "success": true}}';
		}
		else {
			echo '{"MusePHPFormResponse": { "success": false,"error": "Failed to send email"}}';
		}*/

	}
}

?>