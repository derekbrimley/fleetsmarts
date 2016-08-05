<?php		

	
class Home extends MY_Controller 
{

	function index()
	{
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//phpinfo();
	
		$data['tab'] = "Home";
		$data['title'] = "Home";
		$this->load->view('home_view',$data);
	}
	
	function logout()
	{
		$this->session->sess_destroy();
		redirect(base_url("index.php/home"));
	}
	
	
	function authenticate()
	{
		$current_user = $this->session->userdata('username');
		
		if ($current_user == "")
		{
			redirect(base_url("/index.php/login"));
		}
	}
	
	function training_video()
	{
		$data = null;
		$this->load->view('test_training_video',$data);
	}
	
	function fix_bill_accounts()
	{
		$where["category"] = "Bill";
		$bill_accounts = db_select_accounts($where);
		
		foreach ($bill_accounts as $account)
		{
			$where = null;
			$where["account_type"] = "Business";
			$where["category"] = "Vendor";
			$where["vendor_id"] = $account["vendor_id"];
			$vendor_account = db_select_account($where);
			
			$update_account = null;
			$update_account["vendor_account_id"] = $vendor_account["id"];
			
			$where = null;
			$where["id"] = $account["id"];
			db_update_account($update_account,$where);
		}
	
		echo "All bill accounts have been fixed!";
	}
	
	function tester()
	{
		//$po_id = $_POST["po_id"];
		$po_id = 2;
		
		//GET PO
		$where = null;
		$where["id"] = $po_id;
		$po = db_select_purchase_order($where);
		
		//GET APPROVAL PERSON
		$where = null;
		$where["id"] = $po["approved_by_id"];
		$person = db_select_person($where);
	
	
		$to = 'covax13@gmail.com';

		$random = get_random_string(5);
		$subject = 'PO Request '.$po["id"]." - $".number_format($po["expense_amount"],2)." for ".$po["category"]." ".$random;
		$data['po'] = $po;
		$message = $this->load->view('emails/po_request_email',$data, TRUE);
		
		$headers = "From: fleetsmarts@fleetsmarts.net\r\n";
		//$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
		//$headers .= "CC: susan@example.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		//mail("covax13@gmail.com","FleetSmarts Now Does Email!","You better believe it! Guess who just figured out how to send emails from FleetSmarts!!","From: fleetsmarts@fleetsmarts.net");
		mail($to, $subject, $message, $headers);
		
		echo 'success';
	}
	
	function view_message_log()
	{
		$file = 'custom_message_log.txt';
		echo "Custom Message Log<br><br>";
		echo str_replace("\r\n","<br>",file_get_contents($file));
	}
	
	function check_for_new_notifications()
	{
		$user_id = $_POST["user_id"];
		
		//CHECK FOR NEW NOTIFICATIONS
		$where = null;
		$where = " user_id = $user_id AND displayed_datetime IS NULL";
		$notification = db_select_notification($where,"generated_datetime");
		
		$data['user_id'] = $user_id;
		$data['notification'] = $notification;
		$this->load->view('main_menu/notification',$data);
	}
	
}
?>