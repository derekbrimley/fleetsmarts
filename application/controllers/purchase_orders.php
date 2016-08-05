<?php		



class Purchase_orders extends MY_Controller 
{

	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		$data['title'] = "POs";
		$data['tab'] = 'POs';
		$this->load->view('purchase_orders_view',$data);
	}
	
	function load_po_filter()
	{
		//GET COMPANIES WHO HAVE SPARK CC
		$where = null;
		$where = " role = 'Office Staff' OR role = 'Fleet Manager' ";
		$issuers = db_select_people($where,'f_name');
		//CREATE DROPDOWN FOR ISSUER
		$issuer_sidebar_options = array();
		$issuer_sidebar_options["All"] = "All";
		foreach($issuers as $issuer)
		{
			$title = $issuer["full_name"];
			$issuer_sidebar_options[$issuer['id']] = $title;
		}
			
		//GET EXPENSE OWNERS(COMPANIES THAT BELONG TO FLEETMANAGERS OR ARE BUSINESS COMPANIES)
		$invoice_owner_where = " type = 'Business' AND company_status = 'Active'";
		$bill_owners = db_select_companys($invoice_owner_where,"company_side_bar_name");
		
		//GET OPTIONS FOR BILL OWNER SIDEBAR DROPDOWN LIST
		$bill_owner_sidebar_options = array();
		$bill_owner_sidebar_options["All"] = "All";
		foreach ($bill_owners as $bill_owner)
		{
			$title = $bill_owner["company_side_bar_name"];
			$bill_owner_sidebar_options[$bill_owner['id']] = $title;
		}
		$bill_owner_sidebar_options["Revenue"] = "Revenue";
		$bill_owner_sidebar_options["Invoice Payment"] = "Invoice Payment";
		$bill_owner_sidebar_options["Transfer"] = "Transfer";
		$bill_owner_sidebar_options["Unassigned"] = "Unassigned";
		
		
		//GET CATEGORY OPTIONS FOR PO FILTER
		$category_sidebar_options = array();
		$category_sidebar_options["All"] = "All";
		foreach(get_distinct("category","purchase_order") as $category)
		{
		
			$title = $category;
			if(empty($title))
			{
				$title = "Unassigned";
			}
		
			$category_sidebar_options[$title] = $title;
			//echo $category;
		}
		
		//GET APPROVER OPTIONS
		$approver_sidebar_options = array();
		$approver_sidebar_options["All"] = "All";
		//GET OPTIONS FOR PO FILTER
		foreach(get_distinct("approved_by_id","purchase_order") as $approved_by_id)
		{
			//GET APPROVED BY PERSON
			$where = null;
			$where["id"] = $approved_by_id;
			$person = db_select_person($where);
		
			if(!empty($person))
			{
				$title = $person["full_name"];
				$approver_sidebar_options[$approved_by_id] = $title;
			}
		
		}
		asort($approver_sidebar_options);
		$approver_sidebar_options["Unassigned"] = "Unassigned";
		
		
		
		//GET ACCOUNT OPTIONS
		$account_sidebar_options = array();
		$account_sidebar_options["All"] = "All";
		//GET OPTIONS FOR PO FILTER
		foreach(get_distinct("account_id","purchase_order") as $account_id)
		{
			//GET APPROVED BY PERSON
			$where = null;
			$where["id"] = $account_id;
			$account = db_select_account($where);
		
			if(!empty($account))
			{
				$title = $account["account_name"];
				$account_sidebar_options[$account_id] = $title;
			}
		
		}
		asort($account_sidebar_options);
		$account_sidebar_options["Unassigned"] = "Unassigned";
		
		
		
		$data['account_sidebar_options'] = $account_sidebar_options;
		$data['approver_sidebar_options'] = $approver_sidebar_options;
		$data['category_sidebar_options'] = $category_sidebar_options;
		$data['issuer_sidebar_options'] = $issuer_sidebar_options;
		$data['bill_owner_sidebar_options'] = $bill_owner_sidebar_options;
		$this->load->view('purchase_orders/po_filter_div',$data);
		
	}
	
	function load_po_report()
	{
		//GET FILTER PARAMETERS
		$issuer_id = $_POST["issuer_sidebar_dropdown"];
		$bill_owner_id = $_POST["bill_owner_sidebar_dropdown"];
		$category = $_POST["category_dropdown"];
		$after_date = $_POST["after_date_filter"];
		$before_date = $_POST["before_date_filter"];
		$account_id = $_POST["account_dropdown"];
		$approved_by_id = $_POST["approver_dropdown"];
		$status = $_POST["status_dropdown"];
		
		//SET WHERE FOR PURCHASE ORDERS
		$where = " 1 = 1";
		
		//SET WHERE FOR ISSUER (person_id)
		if($issuer_id != "All")
		{
			$where = $where." AND issuer_id = '".$issuer_id."'";
		}
		
		//SET WHERE FOR OWNER (company_id)
		if($bill_owner_id != "All")
		{
			$where = $where." AND owner_id = ".$bill_owner_id;
		}
		
		//SET WHERE FOR CATEGORY
		if($category != "All")
		{
			$where = $where." AND `purchase_order`.category = '".$category."' ";
		}
		
		//SET WHERE FOR DROP START DATE
		if(!empty($after_date))
		{
			$after_date = date("Y-m-d G:i:s",strtotime($after_date));
			$where = $where." AND `purchase_order`.expense_datetime >= '".$after_date."' ";
		}
		
		//SET WHERE FOR DROP END DATE
		if(!empty($before_date))
		{
			$before_date = date("Y-m-d G:i:s",strtotime($before_date)+24*60*60);
			$where = $where." AND `purchase_order`.expense_datetime < '".$before_date."' ";
		}
		
		//SET WHERE FOR ACCOUNT
		if($account_id != "All")
		{
			$where = $where." AND `purchase_order`.account_id = '".$account_id."'";
		}
		
		//SET WHERE FOR ACCOUNT
		if($approved_by_id != "All")
		{
			if($approved_by_id == "Unassigned")
			{
				$where = $where." AND `purchase_order`.approved_by_id IS NULL ";
			}
			else
			{
				$where = $where." AND `purchase_order`.approved_by_id = '".$approved_by_id."'";
			}
			
		}
		
		//SET WHERE FOR ACCOUNT
		if($status != "All")
		{
			if($status == "Approved")
			{
				$where = $where." AND `purchase_order`.approved_datetime IS NOT NULL ";
			}
			else
			{
				$where = $where." AND `purchase_order`.approved_datetime IS NULL ";
			}
			
		}
		
		//SEARCH
		if(!empty($_POST["search_term"]))
		{
			$search = mysql_real_escape_string($_POST["search_term"]);
			$where = "     `purchase_order`.id = '$search' ";//NEEDED TO ADD 4 BLANKS FOR SUBSTR
		}
		
		//echo $where;
		
		//GET PURCHSE ORDERS
		$purchase_orders = db_select_purchase_orders($where,"id DESC");
		
		$data['purchase_orders'] = $purchase_orders;
		$this->load->view('purchase_orders/po_report_div',$data);
	}
	
	function load_po_view()
	{
		$this_person_id = $this->session->userdata('person_id');
	
		$po_id = $_POST["po_id"];
		
		//GET PURCHASE ORDER
		$where = null;
		$where["id"] = $po_id;
		$po = db_select_purchase_order($where);
		
		//CHECK TO SEE IF PO IS COMPLETE
		$po_status_text = "";
		if	(
				empty($po["expense_datetime"]) ||
				empty($po["expense_amount"]) ||
				empty($po["owner_id"]) ||
				empty($po["category"]) ||
				empty($po["account_id"]) ||
				empty($po["approved_by_id"]) ||
				empty($po["po_notes"])
			)
		{
			$po_status_text = "Incomplete";
			$po_is_complete = false;
		}
		else
		{
			$po_is_complete = true;
		}
		
		//CREATE DROPDOWN LIST OF ALLOWED CASH ACCOUNTS
		$where = null;
		//$where = "account_group = 'TAB' OR account_group = 'Spark CC' OR account_group = 'SmartPay' OR account_group = 'Venture CC' ";
		//$where["category"] = "Cash";
		$where = ' category = "Cash" AND parent_account_id IS NOT NULL ';
		$source_accounts = db_select_accounts($where,"account_name");
		$source_accounts_options = array();
		$source_accounts_options["Select"] = "Select";
		foreach($source_accounts as $account)
		{
			$title = $account["account_name"];
			$source_accounts_options[$account['id']] = $title;
		}
		
		$approved_by_options = null;
		$approved_by_options["Select"] = "Select";
		
		$permission_company = null;
		if(!empty($po["owner_id"]))
		{
			//GET PO COMPANY CATEGORY NAME 
			$where = null;
			$where['id'] = $po["owner_id"];
			$company = db_select_company($where);
			
			//GET THE PERMISSION ASSOCIATED WITH THIS EXPENSE CATEGORY
			$where = null;
			$where["permission_name"] = "approve and lock ".$company['category']." expenses";
			$permission = db_select_permission($where);
			
			//GET USERS WITH THIS PERMISSION
			$where = null;
			$where["permission_id"] = $permission["id"];
			$user_permissions = db_select_user_permissions($where);
			
			
			
			foreach($user_permissions as $user_permission)
			{
				//GET USER
				$where = null;
				$where["id"] = $user_permission["user_id"];
				$user = db_select_user($where);
				
				//GET PERSON
				$where = null;
				$where["id"] = $user["person_id"];
				$person = db_select_person($where);
				
				$title = $person["full_name"];
				$approved_by_options[$person['id']] = $title;
			}
		}
		
		//GET ATTACHMENTS
		$where = null;
		$where["type"] = "purchase_order";
		$where["attached_to_id"] = $po["id"];
		$attachments = db_select_attachments($where);
		
		//GET CATEGORIES FOR THIS BUSINESS
		
		//GET ALL ACTIVE TRUCKS
		$where = null;
		$where["dropdown_status"] = "Show";
		$trucks = db_select_trucks($where,"truck_number");
		$truck_dropdown_options = array();
		$truck_dropdown_options["Select"] = "Select Truck";
		foreach($trucks as $truck)
		{
			$truck_dropdown_options[$truck["id"]] = $truck["truck_number"];
		}
		
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		//$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$clients_dropdown_options = array();
		$clients_dropdown_options["Select"] = "Select Driver";
		foreach($dd_all_clients as $client)
		{
			$clients_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		
		$data['po_id'] = $po_id;
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['attachments'] = $attachments;
		$data['po_is_complete'] = $po_is_complete;
		$data['po_status_text'] = $po_status_text;
		$data['this_person_id'] = $this_person_id;
		$data['approved_by_options'] = $approved_by_options;
		$data['source_accounts_options'] = $source_accounts_options;
		$data['company_categories'] = get_company_categories();
		$data['po'] = $po;
		$this->load->view('purchase_orders/po_view_div',$data);
	}
	
	function save_po()
	{
		date_default_timezone_set('America/Denver');
		
		$po_id = $_POST["po_id"];
		$po_date = $_POST["po_date"];
		$expense_amount = $_POST["expense_amount"];
		$account_id = $_POST["account_dropdown"];
		$owner_id = $_POST["owner_id"];
		$category = $_POST["po_category"];
		$po_notes = $_POST["po_notes"];
		$approved_by_id = $_POST["approved_by_dropdown"];
		$po_load_number = $_POST["po_load_number"];
		$po_client_id = $_POST["po_client_id"];
		$po_truck_id = $_POST["po_truck_id"];
		$po_gps = $_POST["po_gps"];
		
		
		//GET PO
		$where = null;
		$where["id"]  = $po_id;
		$po = db_select_purchase_order($where);
		
		//VALIDATE
		if($account_id != 'Select')
		{
			$update_po["account_id"] = $account_id;
		}
		
		if($approved_by_id == 'Select')
		{
			$update_po["approved_by_id"] = NULL;
		}
		else
		{
			$update_po["approved_by_id"] = $approved_by_id;
		}
		
		//SAVE DRIVER
		if($po_client_id != 'Select')
		{
			$update_po["client_id"] = $po_client_id;
		}
		else
		{
			$update_po["client_id"] = NULL;
		}
		
		//SAVE TRUCK
		if($po_truck_id != 'Select')
		{
			$update_po["po_truck_id"] = $po_truck_id;
		}
		else
		{
			$update_po["po_truck_id"] = NULL;
		}
		
		//SAVE LOCATION
		if(!empty($po_gps))
		{
			$update_po["po_gps"] = $po_gps;
		}
		else
		{
			$update_po["po_gps"] = NULL;
		}
		
		//SAVE LOAD
		$update_po["load_id"] = NULL;
		if(!empty($po_load_number))
		{
			$where = null;
			$where["customer_load_number"] = $po_load_number;
			$load = db_select_load($where);
			
			if(!empty($load))
			{
				$update_po["load_id"] = $load["id"];
			}
		}
		
		//IF OWNER CHANGES, RESET OWNER
		if($owner_id != $po["owner_id"])
		{
			$update_po["approved_by_id"] = NULL;
		}
		
		//IF DRIVER CHANGED AND CATEGORY WAS PA
		if($po_client_id != $po["client_id"] && ($category == "PA" || $category == "ME - Fuel"))
		{
			$update_po["category"] = null;
		}
		else
		{
			$update_po["category"] = $category;
		}
		
		//UPDATE PO
		$update_po["expense_datetime"] = date("Y-m-d H:i:s",strtotime($po_date));
		$update_po["expense_amount"] = round($expense_amount,2);
		$update_po["owner_id"] = $owner_id;
		$update_po["po_notes"] = $po_notes;
		
		//UPDATE AS LONG AS PO HASN'T BEEN APPROVED YET
		if(empty($po["approved_datetime"]))
		{
			$where = null;
			$where["id"] = $po_id;
			db_update_purchase_order($update_po,$where);
		}
		
		//IF CATEGORY CHANGED TO ME - LUMPER
		if($category != $po["category"] && $category == "ME - Lumper")
		{
			//echo "changed ";
			if(!empty($load))
			{
				$load_id = $load["id"];

				$geocode = reverse_geocode($po_gps);

				//GET NEWLY UPDATED PO
				$where = null;
				$where["id"]  = $po_id;
				$updated_po = db_select_purchase_order($where);
				
				//GET CLIENT
				$where = null;
				$where["id"] = $updated_po["client_id"];
				$client = db_select_client($where);
				
				$update_load = null;
				$update_load["has_lumper"] = "Yes";
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				//INSERT NEW BILLING NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "Lumper PO# ".$updated_po["id"]." created for ".$client["client_nickname"]." for $".number_format($updated_po["expense_amount"]);
				db_insert_note($insert_note);
				
				
				//SEND LUMPER RECEIPT EMAIL
				$email_data = null;
				$email_data["location"] = $geocode["city"].", ".$geocode["state"];
				$message = $this->load->view('emails/lumper_receipt_email',$email_data, TRUE);
				//$to = 'covax13@gmail.com';
				$to = $client["company"]["person"]["email"];
				$subject = 'Lumper Receipt Request for Load '.$load["customer_load_number"];
				//$message = "test";
				$headers = "From: paperwork.dispatch@gmail.com\r\n";
				//$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
				$headers .= "CC: paperwork.dispatch@gmail.com\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				//mail("covax13@gmail.com","FleetSmarts Now Does Email!","You better believe it! Guess who just figured out how to send emails from FleetSmarts!!","From: fleetsmarts@fleetsmarts.net");
				mail($to, $subject, $message, $headers);
			}
		}
		
		//IF CATEGORY CHANGED TO PA
		if($category != $po["category"] && ($category == "PA" || $category == "ME - Fuel"))
		{
			$load_id = $load["id"];
			//GET NEWLY UPDATED PO
			$where = null;
			$where["id"]  = $po_id;
			$updated_po = db_select_purchase_order($where);
			
			//GET CLIENT
			$where = null;
			$where["id"] = $updated_po["client_id"];
			$client = db_select_client($where);
				
			$hold_report = get_hold_report($updated_po["client_id"]);
			
			// $hold_report["hold_status"] = $hold_status;
			// $hold_report["loads_missing_dc"] = $loads_missing_dc;
			// $hold_report["loads_missing_hc"] = $loads_missing_hc;
			// $hold_report["client_expenses"] = $client_expenses;
			
			$update_po = null;
			$update_po["po_log"] = $updated_po["po_log"]."\n".date("m/d/y H:i")." | ".$client["client_nickname"]." has ".count($hold_report["loads_missing_dc"])." missing digital bols, ".count($hold_report["loads_missing_hc"])." missing orignial bols, ".count($hold_report["client_expenses"])." missing receipts";
			
			$where = null;
			$where["id"] = $updated_po["id"];
			db_update_purchase_order($update_po,$where);
		}
		
	
		
		echo 'Success';
		
	}
	
	function email_po()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		
		//$this->load->library('email');
		
		$po_id = $_POST["po_id"];
		
		//GET PO
		$where = null;
		$where["id"] = $po_id;
		$po = db_select_purchase_order($where);
		
		//GET APPROVAL PERSON
		$where = null;
		$where["id"] = $po["approved_by_id"];
		$person = db_select_person($where);
	
		//$to = 'covax13@gmail.com';
		$to = $person["email"];

		$random = get_random_string(5);
		//$subject = 'PO Request '.$po["id"]." - $".number_format($po["expense_amount"],2)." for ".$po["category"];
		$subject = 'PO Request '.$po["id"];
		$data['po'] = $po;
		$message = $this->load->view('emails/po_request_email',$data, TRUE);
		
		//$headers = "From: fleetsmarts@fleetsmarts.net\r\n";
		//$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
		//$headers .= "CC: susan@example.com\r\n";
		//$headers .= "MIME-Version: 1.0\r\n";
		//$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		//mail("covax13@gmail.com","FleetSmarts Now Does Email!","You better believe it! Guess who just figured out how to send emails from FleetSmarts!!","From: fleetsmarts@fleetsmarts.net");
		//mail($to, $from, $subject, $message);
		

		$this->email->from("fleetsmarts@fleetsmarts.net","Dispatch");
		$this->email->to($to);
		$this->email->cc('fleetsmarts@integratedlogicsticssolutions.co');
		$this->email->subject($subject);
		$this->email->message($message);
		$this->email->send();
		//echo $this->email->print_debugger();
		
		
		//MAKE ISSUER NAME TEXT
		$issuer_name = $this->session->userdata('f_name')." ".$this->session->userdata('l_name');
		
		//UPDATE PURCHASE ORDER WITH EMAIL DATETIME
		$update_po["email_datetime"] = date("Y-m-d H:i:s");
		$update_po["po_log"] = $po["po_log"]."\n".date("m/d/y H:i")." | PO Request sent to ".$to." by ".$issuer_name;
		$where = null;
		$where["id"] = $po_id;
		db_update_purchase_order($update_po,$where);
		
		echo 'success';
	}
	
	function delete_po()
	{
		$this_person_id = $this->session->userdata('person_id');
		
		$po_id = $_POST["po_id"];
		
		//GET PO
		$where = null;
		$where["id"]  = $po_id;
		$po = db_select_purchase_order($where);
		
		if($this_person_id == $po["issuer_id"] || $this_person_id == $po["approved_by_id"])
		{
			$where = null;
			$where["id"] = $po["id"];
			db_delete_purchase_order($where);
		}
	}
	
	function create_new_po()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		
		//MAKE ISSUER NAME TEXT
		$issuer_name = $this->session->userdata('f_name')." ".$this->session->userdata('l_name');
		
		//INSERT NEW PO
		$po["issuer_id"] = $this->session->userdata('person_id');
		$po["issued_datetime"] = date("Y-m-d H:i:s");
		$po["expense_datetime"] = date("Y-m-d H:i:s");
		$po["po_log"] = date("m/d/y H:i")." | PO created by ".$issuer_name;
		db_insert_purchase_order($po);
		
		//GET NEW PO
		$where = null;
		$where["issuer_id"] = $po["issuer_id"];
		$where["issued_datetime"] = $po["issued_datetime"];
		$new_po = db_select_purchase_order($where);
		
		echo $new_po["id"];
	}
	
	function quick_po_approval($po_id)
	{
		$person_id = $this->session->userdata('person_id');
		
		//GET PO
		$where = null;
		$where["id"] = $po_id;
		$po = db_select_purchase_order($where);

		//IF USER HAS PERMISSION TO VIEW PO
		if(($person_id == $po["issuer_id"] || $person_id == $po["approved_by_id"]) || (user_has_permission('view all purchase orders for assigned business') && user_is_assigned_to_business($po["owner_id"])) || user_has_permission('view all purchase orders'))
		{
			if(empty($po["approved_datetime"]))
			{
				//GET ATTACHMENTS
				$where = null;
				$where["type"] = "purchase_order";
				$where["attached_to_id"] = $po["id"];
				$attachments = db_select_attachments($where);
				
				$data['attachments'] = $attachments;
				$data['po'] = $po;
				$this->load->view('purchase_orders/quick_po_approval_view',$data);
			}
			else
			{
				$data['po'] = $po;
				$this->load->view('purchase_orders/po_email_feedback_view',$data);
			}
			
			
		}
	}
	
	function approve_po($po_id)
	{
		date_default_timezone_set('America/Denver');
		
		//GET PO
		$where = null;
		$where["id"] = $po_id;
		$po = db_select_purchase_order($where);
		
		$this_person_id = $this->session->userdata('person_id');
		
		//VALIDATE PROPER APPROVER
		if($po["approved_by_id"] == $this_person_id)
		{
			//MAKE ISSUER NAME TEXT
			$issuer_name = $this->session->userdata('f_name')." ".$this->session->userdata('l_name');
		
			//UPDATE PO WITH APPROVED BY DATETIME
			$update_po["approved_datetime"] = date("Y-m-d H:i:s");
			$update_po["approved_by_id"] = $this_person_id;
			$update_po["po_log"] = $po["po_log"]."\n".date("m/d/y H:i")." | PO approved by ".$issuer_name;
			
			$where = null;
			$where["id"] = $po["id"];
			db_update_purchase_order($update_po,$where);
			
			//GET PO
			$where = null;
			$where["id"] = $po_id;
			$po = db_select_purchase_order($where);
			
			$data['po'] = $po;
			$this->load->view('purchase_orders/po_email_feedback_view',$data);
		}
		else
		{
			echo "It appears that you aren't the one that is suppose to be approving this PO";
		}
	}
	
	function approve_po_from_list($po_id)
	{
		date_default_timezone_set('America/Denver');
		
		//GET PO
		$where = null;
		$where["id"] = $po_id;
		$po = db_select_purchase_order($where);
		
		$this_person_id = $this->session->userdata('person_id');
		
		//VALIDATE PROPER APPROVER
		if($po["approved_by_id"] == $this_person_id)
		{
			//MAKE ISSUER NAME TEXT
			$issuer_name = $this->session->userdata('f_name')." ".$this->session->userdata('l_name');
		
			//UPDATE PO WITH APPROVED BY DATETIME
			$update_po["approved_datetime"] = date("Y-m-d H:i:s");
			$update_po["approved_by_id"] = $this_person_id;
			$update_po["po_log"] = $po["po_log"]."\n".date("m/d/y H:i")." | PO approved by ".$issuer_name;
			
			$where = null;
			$where["id"] = $po["id"];
			db_update_purchase_order($update_po,$where);
			
			//GET PO
			$where = null;
			$where["id"] = $po_id;
			$po = db_select_purchase_order($where);
			
			
			
			$data['po'] = $po;
			$this->load->view('purchase_orders/po_row',$data);
		}
		else
		{
			echo "It appears that you aren't the one that is suppose to be approving this PO";
		}
	}
	
	function deny_po($po_id)
	{
		date_default_timezone_set('America/Denver');
		
		//GET PO
		$where = null;
		$where["id"] = $po_id;
		$po = db_select_purchase_order($where);
		
		$this_person_id = $this->session->userdata('person_id');
		
		//VALIDATE PROPER APPROVER
		if($po["approved_by_id"] == $this_person_id)
		{
			//MAKE ISSUER NAME TEXT
			$issuer_name = $this->session->userdata('f_name')." ".$this->session->userdata('l_name');
		
			//UPDATE PO WITH APPROVED BY DATETIME
			$update_po["email_datetime"] = null;
			$update_po["po_log"] = $po["po_log"]."\n".date("m/d/y H:i")." | PO Request denied by ".$issuer_name;
			
			$where = null;
			$where["id"] = $po["id"];
			db_update_purchase_order($update_po,$where);
			
			//GET PO
			$where = null;
			$where["id"] = $po_id;
			$po = db_select_purchase_order($where);
			
			$data['po'] = $po;
			$this->load->view('purchase_orders/po_email_feedback_view',$data);
		}
		else
		{
			echo "It appears that you aren't the one that is suppose to be approving this PO";
		}
	}
	
	//AJAX LOAD PO ATTACHMENT DIALOG DIV
	function load_po_file_upload()
	{
		$po_id = $_POST["po_id"];
		
		$data = null;
		$data["po_id"] = $po_id;
		$this->load->view('purchase_orders/po_attachment_div',$data);
	}
	
	//UPLOAD PO ATTACHMENT
	function upload_po_attachment()
	{
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
		$post_name = 'attachment_file';
		$file = $_FILES[$post_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		//$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$title = $_POST["attachment_name"];
		$category = "PO Attachment";
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$contract_secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		//CREATE ATTACHMENT IN DB
		$attachment = null;
		$attachment["type"] = "purchase_order";
		$attachment["attached_to_id"] = $_POST["po_id"];
		$attachment["file_guid"] = $contract_secure_file["file_guid"];
		$attachment["attachment_name"] = $_POST["attachment_name"];

		db_insert_attachment($attachment);
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		load_upload_success_view();
	}
	
	function open_lumper_dialog()
	{
		$po_id = $_POST["po_id"];
		
		//GET ALL ACTIVE TRUCKS
		$where = null;
		$where["dropdown_status"] = "Show";
		$trucks = db_select_trucks($where,"truck_number");
		$truck_dropdown_options = array();
		$truck_dropdown_options["Select"] = "Select";
		foreach($trucks as $truck)
		{
			$truck_dropdown_options[$truck["id"]] = $truck["truck_number"];
		}
		
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		//$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$clients_dropdown_options = array();
		$clients_dropdown_options["Select"] = "Select";
		foreach($dd_all_clients as $client)
		{
			$clients_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		
		$data['po_id'] = $po_id;
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$this->load->view('purchase_orders/lumper_dialog',$data);
	}
	
	function validate_lumper_load_number()
	{
		date_default_timezone_set('America/Denver');
		
		$load_number = $_POST["load_number"];
		$po_id = $_POST["po_id"];
		
		//CHECK IF LOAD EXISTS
		$where = null;
		$where["customer_load_number"] = $load_number;
		$load = db_select_load($where);
		
		if(!empty($load))
		{
			echo "<script>$('#lumper_dialog').dialog('close')</script>";
			
			//UPDATE PO
			$update_po = null;
			$update_po["load_id"] = $load["id"];
			
			$where = null;
			$where["id"] = $po_id;
			db_update_purchase_order($update_po,$where);
			
			//UPDATE LOAD
			$update_load = null;
			$update_load["has_lumper"] = "Yes";
			$where = null;
			$where["id"] = $load["id"];
			db_update_load($update_load,$where);
			
			//INSERT NEW NOTE
			$insert_note = null;
			$insert_note["note_type"] = "load_billing";
			$insert_note["note_for_id"] = $load["id"];
			$insert_note["note_datetime"] = date("Y-m-d H:i");
			$insert_note["user_id"] = $this->session->userdata('user_id');
			$insert_note["note_text"] = "PO ".$po_id." generated for LUMPER on this load";
			db_insert_note($insert_note);
		}
		else
		{
			echo "<script>alert('Invalid Load Number!')</script>";
		}
		
	}
	
	
}
