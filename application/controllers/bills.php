<?php		

class Bills extends MY_Controller 
{
	//INDEX
	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//GET BUSINESS USERS
		$where = null;
		$where["company_status"] = "Active";
		$where["type"] = "Business";
			
		$business_users = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN LIST OF BUSINESS USERS
		$business_users_options = array();
		$business_users_options["All"] = "All";
		foreach($business_users as $company)
		{
			$title = $company["company_side_bar_name"];
			$business_users_options[$company["id"]] = $title;
		}
		
		//GET OPEN TICKETS
		$where = null;
		$where["completion_date"] = NULL;
		$open_tickets = db_select_tickets($where);
		
		$ticket_options = array();
		$ticket_options["Select"] = "Select";
		if(!empty($open_tickets))
		{
			foreach($open_tickets as $ticket)
			{
				$title = "Ticket# ".$ticket["id"];
				$ticket_options[$ticket["id"]] = $title;
				//echo $option;
			}
		}
		
		$data['ticket_options'] = $ticket_options;
		$data['business_users_options'] = $business_users_options;
		$data['tab'] = 'Bills';
		$data['title'] = "Bills";
		$this->load->view('bills_view',$data);
	}// end index()
	
	function load_report()
	{
		//GET ALL FILTER PARAMETERS
		$business_user_id = $_POST["business_user"];
		$invoice_type = $_POST["invoice_type"];
		$relationship_id = $_POST["relationship_id"];
		$relationship_account_id = $_POST["relationship_account_id"];
		$after_date = $_POST["after_date_filter"];
		$before_date = $_POST["before_date_filter"];
		$person_id = $this->session->userdata('person_id');
		$status = $_POST['bill_status'];
		
		//SET WHERE FOR INVOICES
		$where = ' (invoice_type = "Expense Incurred" OR invoice_type = "Deposit Payable")';
		
		//SET WHERE FOR BUSINESS USER
		if($business_user_id != "All")
		{
			$where = $where." AND business_id = ".$business_user_id;
		}
		
		//SET WHERE FOR BILL STATUS
		if($status == "Open")
		{
			$where = $where." AND closed_datetime IS NULL";
		}
		if($status == "Closed")
		{
			$where = $where." AND closed_datetime IS NOT NULL";
		}
		
		//SET WHERE FOR INVOICE TYPE
		if($invoice_type != "All")
		{
			$where = $where." AND invoice_type = '".$invoice_type."'";
		}
		
		//SET WHERE FOR VENDOR
		if($relationship_id != "All")
		{
			$where = $where." AND relationship_id = '".$relationship_id."'";
		}
		
		//SET WHERE FOR ACCOUNT ID
		if($relationship_account_id != "All")
		{
			$where = $where." AND credit_account_id = ".$relationship_account_id;
		}
		
		//SET WHERE FOR DROP START DATE
		if(!empty($after_date))
		{
			$after_date = date("Y-m-d G:i:s",strtotime($after_date));
			$where = $where." AND invoice_datetime >= '".$after_date."' ";
		}
		
		//SET WHERE FOR DROP END DATE
		if(!empty($before_date))
		{
			$before_date = date("Y-m-d G:i:s",strtotime($before_date)+24*60*60);
			$where = $where." AND invoice_datetime < '".$before_date."' ";
		}
		
		//echo $where;
		
		$invoices = db_select_invoices($where,"invoice_datetime DESC");
		
		$data['relationship_id'] = $relationship_id;
		$data['invoices'] = $invoices;
		$this->load->view('bills/bills_report',$data);
	}
	
	function load_payment_view()
	{
		//GET ALL FILTER PARAMETERS
		$business_user_id = $_POST["business_user"];
		$invoice_type = $_POST["invoice_type"];
		$relationship_id = $_POST["relationship_id"];
		$relationship_account_id = $_POST["relationship_account_id"];
		$after_date = $_POST["after_date_filter"];
		$before_date = $_POST["before_date_filter"];
		
		//echo $relationship_account_id;
		
		//SET WHERE FOR INVOICES
		$where = ' (invoice_type = "Expense Incurred" OR invoice_type = "Deposit Payable")';
		
		//SET WHERE FOR BUSINESS USER
		if($business_user_id != "All")
		{
			$where = $where." AND business_id = ".$business_user_id;
		}
		
		//SET WHERE FOR INVOICE TYPE
		if($invoice_type != "All")
		{
			$where = $where." AND invoice_type = '".$invoice_type."'";
		}
		
		//SET WHERE FOR VENDOR
		if($relationship_id != "All")
		{
			$where = $where." AND relationship_id = '".$relationship_id."'";
		}
		
		//SET WHERE FOR ACCOUNT ID
		if($relationship_account_id != "All")
		{
			$where = $where." AND credit_account_id = ".$relationship_account_id;
		}
		
		//SET WHERE FOR DROP START DATE
		if(!empty($after_date))
		{
			$after_date = date("Y-m-d G:i:s",strtotime($after_date));
			$where = $where." AND invoice_datetime >= '".$after_date."' ";
		}
		
		//SET WHERE FOR DROP END DATE
		if(!empty($before_date))
		{
			$before_date = date("Y-m-d G:i:s",strtotime($before_date)+24*60*60);
			$where = $where." AND invoice_datetime < '".$before_date."' ";
		}
		
		//echo $where;
		
		$invoices = db_select_invoices($where,"invoice_datetime DESC");
		
		//GET VENDOR
		$where = null;
		$where["id"] = $relationship_id;
		$relationship = db_select_business_relationship($where);
		
		date_default_timezone_set('America/Denver');
		$payment_date = date("Y-m-d G:i:s");
		
		$data['payment_date'] = $payment_date;
		$data['vendor'] = $relationship["related_business"];
		$data['invoices'] = $invoices;
		$this->load->view('bills/bill_payment_view',$data);
	}
	
	//PAYEE SELECTED ON FILTER LEFT BAR
	function business_user_selected()
	{
		$business_user_id = $_POST["business_user"];
		
		//GET COMPANY... TO PREVENT SQL INJECTION
		$where = null;
		$where["id"] = $business_user_id;
		$business_user_company = db_select_company($where);
		
		//GET lIST OF VENDORS RELATIONSHIPS
		$where = null;
		//$where["business_id"] = $business_user_id;
		//$where["relationship"] = "Vendor";
		$where = ' business_id = '.$business_user_company["id"].' AND (relationship = "Vendor" OR relationship = "Staff") ';
		$business_relationships = db_select_business_relationships($where);
	
		$relationship_options = array();
		$relationship_options["All"] = "All";
		if(!empty($business_relationships))
		{
			foreach($business_relationships as $relationship)
			{
				$title = $relationship["related_business"]["company_name"];
				$relationship_options[$relationship["id"]] = $title;
				//echo $option;
			}
		}
		
		$data['relationship_options'] = $relationship_options;
		$this->load->view('bills/vendor_filter_dropdown_options',$data);
	}
	
	//VENDOR SELECTED ON FILTER LEFT BAR
	function relationship_selected()
	{
		$business_user_id = $_POST["business_user"];
		$relationship_id = $_POST["relationship_id"];
		$invoice_type = $_POST["invoice_type"];
		
		if($relationship_id != "All")
		{
			$where = null;
			$where = 'company_id = '.$business_user_id.' AND account_class = "Liability" ';
			$accounts = db_select_accounts($where);
		}
		else
		{
			$accounts = null;
		}
		
		$account_options = array();
		$account_options["All"] = "All";
		if(!empty($accounts))
		{
			foreach($accounts as $account)
			{
				$title = $account["account_name"];
				$account_options[$account["id"]] = $title;
				//echo $option;
			}
		}
		
		$data['account_options'] = $account_options;
		$this->load->view('bills/bills_account_filter_options',$data);
	}
	
	//LOADS MEMBER_SELECTION_TABLE AFTER BUSINESS USER IS SELECTED IN NEW BILL DIALOG
	function new_invoice_business_user_selected()
	{
		$company_id = $_POST["business_user_id"];
		$person_id = $this->session->userdata('person_id');
		
		//GET COMPANY OF PERSON
		$where = null;
		//$where["person_id"] = $person_id;
		$where = " (category = 'Office Staff' OR category = 'Fleet Manager') AND person_id = ".$person_id;
		$user_company = db_select_company($where);
		
		// $where = null;
		// $where['business_id'] = $company_id;
		// $where['related_business_id'] = $user_company["id"];
		// $user_business_relationship = db_select_business_relationship($where);
		
		//echo $company_id;
		//echo $company["id"];
		
		if((user_has_permission("create and accept incoming bills for assigned business")&&(user_is_assigned_to_business($company_id)))||(user_has_permission("create and accept incoming bills for non-assigned business")))
		{
		
			//GET BUSINESS USER
			$where = null;
			$where["id"] = $company_id;
			$business_user = db_select_company($where);
			
			if($business_user["category"] == "Coop")
			{
				//GET MEMBERS
				$where = null;
				$where["business_id"] = $company_id;
				$where["relationship"] = "Member";
				$related_members = db_select_business_relationships($where);
				
				//CREATE DROPDOWN LIST
				$member_options = array();
				$member_options["Select"] = "Select";
				foreach($related_members as $relationship)
				{
					//GET RELATED COMPANY
					$member = $relationship["related_business"];
					
					$title = $member["company_side_bar_name"];
					$member_options[$relationship["id"]] = $title;
				}
				
				$script = '$("#member_or_business_row").show();';
			}
			else
			{
				$member_options = array();
				$member_options["Select"] = "Select";
				
				$script = '$("#bill_type_row").show();';
			}
			
			$data['script'] = $script;
			$data['member_options'] = $member_options;
			$this->load->view('bills/bills_member_selection_table',$data);
		}
		else
		{
			echo "<br><br><div style='color:red;'>You don't have permission to perform this action.</div>";
		}
	}
	
	//LOADS OPTIONS FOR VENDOR IN NEW BILL DIALOG
	function load_customer_vendor_selection_div()
	{
		$member_or_business = $_POST["member_or_business"];
		$member_id = $_POST["member_id"];
		$business_user_id = $_POST["business_user_id"];
		$bill_type = $_POST["bill_type"];
		$new_bill_ticket = $_POST["new_bill_ticket"];
		$payment_method = $_POST["payment_method"];
		$bill_holder_id = $_POST["bill_holder_id"];
		
		$customer_vendor = "Vendor";
		
		//GET LIST OF VENDORS
		$where = null;
		//$where["business_id"] = $business_user_id;
		//$where["relationship"] = "vendor";
		$where = ' business_id = '.$business_user_id.' AND (relationship = "Vendor" OR relationship = "Staff") ';
		$relationships = db_select_business_relationships($where,"related_company_name");
		
		$relationship_options = array();
		$relationship_options["Select"] = "Select";
		if(!empty($relationships))
		{
			foreach($relationships as $relationship)
			{
				$title = $relationship["related_business"]["company_name"];
				$relationship_options[$relationship["id"]] = $title;
				//echo $option;
			}
		}
		
		$pre_selected_vendor = "Select";
		if(!empty($bill_holder_id))
		{
			//GET BILL HOLDER
			$where = null;
			$where["id"] = $bill_holder_id;
			$bill_holder = db_select_bill_holder($where);
			
			if(!empty($bill_holder))
			{
				//GET RELATIONSHIP
				
				//GET FROM COMPANY
				$where = null;
				$where["id"] = $bill_holder["company_id"];
				$payer_company = db_select_company($where);
				
				//GET FROM COMPANY
				$where = null;
				$where["id"] = $bill_holder["from_company_id"];
				$from_company = db_select_company($where);
				
				//GET RELATIONSHIP
				$where = null;
				$where["relationship"] = "Vendor";
				$where["business_id"] = $payer_company["id"];
				$where["related_business_id"] = $from_company["id"];
				$relationship = db_select_business_relationship($where);
				
				$pre_selected_vendor = $relationship["id"];
			}
		}
		
		
		$data['bill_holder_id'] = $bill_holder_id;
		$data['pre_selected_vendor'] = $pre_selected_vendor;
		$data['member_or_business'] = $member_or_business;
		$data['member_id'] = $member_id;
		$data['customer_vendor'] = $customer_vendor;
		$data['bill_type'] = $bill_type;
		$data['new_bill_ticket'] = $new_bill_ticket;
		$data['payment_method'] = $payment_method;
		$data['business_user_id'] = $business_user_id;
		$data['relationship_options'] = $relationship_options;
		$this->load->view('bills/new_bill_relationship_selection_form',$data);
	}
	
	//LOADS NEW INVOICE FORM AFTER CUSTOMER OR VENDOR IS SELECTED
	function customer_vendor_selected()
	{
		$bill_holder_id = $_POST["relationship_selected_bill_holder_id"];
		$member_or_business = $_POST["relationship_selected_business_or_member"];
		$member_relationship_id = $_POST["relationship_selected_member_id"];
		$business_user_id = $_POST["relationship_selected_business_user_id"];
		$bill_type = $_POST["relationship_selected_bill_type"];
		$new_bill_ticket = $_POST["relationship_selected_new_bill_ticket"];
		$payment_method = $_POST["relationship_selected_payment_method"];
		$relationship_id = $_POST["relationship_selected_relationship_id"];
		
		
		if($member_or_business == "Member")
		{
			$balance_sheet_account_label = "A/P to Vendor Account";
			
			//GET VENDOR LIABILITY ACCOUNTS
			$where = null;
			$where["relationship_id"] = $member_relationship_id;
			$where["account_class"] = "Liability";
			$where["company_id"] = $business_user_id;
			$accounts = db_select_accounts($where);
			
			$balance_sheet_account_options = array();
			$balance_sheet_account_options["Select"] = "Select";
			if(!empty($accounts))
			{
				foreach($accounts as $account)
				{
					$title = $account["account_name"];
					$balance_sheet_account_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			$income_statement_account_label = "A/R from Member Account";
			//GET VENDOR EXPENSE ACCOUNTS
			$where = null;
			$where["relationship_id"] = $member_relationship_id;
			$where["account_class"] = "Asset";
			$where["company_id"] = $business_user_id;
			$accounts = db_select_accounts($where);
			
			$income_statement_account_options = array();
			$income_statement_account_options["Select"] = "Select";
			if(!empty($accounts))
			{
				foreach($accounts as $account)
				{
					$title = $account["account_name"];
					$income_statement_account_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			
			$deposit_account_label = $income_statement_account_label;
			$deposit_account_options = $income_statement_account_options;
		}
		else //IF BUSINESS
		{
			$balance_sheet_account_label = "Payable Account";
			
			//GET VENDOR LIABILITY ACCOUNTS
			$where = null;
			$where["relationship_id"] = $relationship_id;
			$where["account_class"] = "Liability";
			$accounts = db_select_accounts($where);
			
			$balance_sheet_account_options = array();
			$balance_sheet_account_options["Select"] = "Select";
			if(!empty($accounts))
			{
				foreach($accounts as $account)
				{
					$title = $account["account_name"];
					$balance_sheet_account_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			$income_statement_account_label = "Expense Account";
			//GET VENDOR EXPENSE ACCOUNTS
			$where = null;
			//$where["relationship_id"] = $relationship_id;
			$where["company_id"] = $business_user_id;
			$where["account_class"] = "Expense";
			$accounts = db_select_accounts($where,"account_name");
			
			$income_statement_account_options = array();
			$income_statement_account_options["Select"] = "Select";
			if(!empty($accounts))
			{
				foreach($accounts as $account)
				{
					$title = $account["account_name"];
					$income_statement_account_options[$account["id"]] = $title;
					//echo $option;
				}	
			}
			
			
			$deposit_account_label = "Security Deposit Account";
			//GET VENDOR DEPOSIT ACCOUNT (ASSET)
			$where = null;
			$where["relationship_id"] = $relationship_id;
			$where["account_class"] = "Asset";
			$accounts = db_select_accounts($where);
			
			$deposit_account_options = array();
			$deposit_account_options["Select"] = "Select";
			if(!empty($accounts))
			{
				foreach($accounts as $account)
				{
					$title = $account["account_name"];
					$deposit_account_options[$account["id"]] = $title;
					//echo $option;
				}
			}
		}
		
		//GET BILL HOLDER IF BILL HOLDER EXISTS
		$bill_holder = null;
		if(!empty($bill_holder_id))
		{
			//GET BILL HOLDER
			$where = null;
			$where["id"] = $bill_holder_id;
			$bill_holder = db_select_bill_holder($where);
		}
		
		
		
		$data['bill_holder'] = $bill_holder;
		$data['relationship_id'] = $relationship_id;
		$data['deposit_account_label'] = $deposit_account_label;
		$data['income_statement_account_label'] = $income_statement_account_label;
		$data['balance_sheet_account_label'] = $balance_sheet_account_label;
		$data['balance_sheet_account_options'] = $balance_sheet_account_options;
		$data['income_statement_account_options'] = $income_statement_account_options;
		$data['deposit_account_options'] = $deposit_account_options;
		$data['member_or_business'] = $member_or_business;
		$data['bill_type'] = $bill_type;
		$data['new_bill_ticket'] = $new_bill_ticket;
		$data['payment_method'] = $payment_method;
		$data['business_user_id'] = $business_user_id;
		$this->load->view('bills/new_bill_form',$data);
	}
	
	//CREATE AND INSERT INVOICE INTO DB FROM NEW INVOICE FORM
	function insert_new_invoice()
	{
		
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorder_id = $this->session->userdata('user_id');
		
		$business_user_id = $_POST["new_invoice_business_user_id"];
		$relationship_id = $_POST["new_invoice_relationship_id"];
		$business_or_member = $_POST["new_invoice_member_or_business"];
		$bill_type = $_POST["new_invoice_bill_type"];
		$ticket_id = $_POST["new_invoice_new_bill_ticket"];
		$payment_method = $_POST["new_invoice_payment_method"];
		$balance_sheet_id = $_POST["balance_sheet_id"];
		$income_statement_id = $_POST["income_statement_id"];
		$deposit_account_id = $_POST["deposit_account_id"];
		$invoice_date = $_POST["new_invoice_date"];
		$invoice_amount = $_POST["new_invoice_amount"];
		$invoice_number = $_POST["new_invoice_number"];
		$invoice_desc = $_POST["new_invoice_desc"];
		$bill_holder_id = $_POST["new_invoice_bill_holder_id"];
		$bill_holder_file_guid = $_POST["bill_holder_file_guid"];
		
		$invoice_created_datetime = date("Y-m-d G:i:s");
		
		//GET BUSINESS USER
		$where = null;
		$where["id"] = $business_user_id;
		$business_user = db_select_company($where);
		
		if($business_user["category"] != "Coop")
		{
			$business_or_member = "Business";
		}
		
		if($business_or_member == "Business")
		{
			$transaction = null;
			
			if(!empty($bill_holder_id))
			{
				//CLOSE BILL HOLDER
				$update_bill_holder = null;
				$update_bill_holder["closed_datetime"] = $invoice_created_datetime;
				
				$where = null;
				$where["id"] = $bill_holder_id;
				db_update_bill_holder($update_bill_holder,$where);
			}
			
			//DETERMINE DEBIT AND CREDIT ACCOUNTS
			$debit_account_id = null;
			$credit_account_id = null;
			$db_invoice_type = null;
			
			if($bill_type == "Business Expense")
			{
				$debit_account_id = $income_statement_id;
				$credit_account_id = $balance_sheet_id;
				$db_invoice_type = "Expense Incurred";
			}
			else if($bill_type == "Deposit Requested")
			{
				$debit_account_id = $deposit_account_id;
				$credit_account_id = $balance_sheet_id;
				$db_invoice_type = "Deposit Payable";
			}
			else if($bill_type == "Ticket Expense")
			{
				//GET LEASING COMPANY
				$where = null;
				$where["category"] = "Leasing";
				$leasing_company = db_select_company($where);
				
				//GET DEFAULT DAMAGE REPAIR LIABILITY ACCOUNT
				$where = null;
				$where["company_id"] = $leasing_company["id"];
				$where["category"] = "Damage Repair Liability";
				$default_damage_liability_account = db_select_default_account($where);
				
				
				$debit_account_id = $default_damage_liability_account["account_id"];
				$credit_account_id = $balance_sheet_id;
				$db_invoice_type = "Expense Incurred";
				
				//REASSIGN THE TICKET'S LIABILITY ACCOUNT TO VENDOR A/P
				//hold off on this for now... need to think this through a bit more
			}
			
			
			
			//GET ACCOUNT FOR CATEGORY
			$where = null;
			$where["id"] = $debit_account_id;
			$category_account = db_select_account($where);
			
			
			//INSERT INVOICE INTO DB
			$insert_invoice = null;
			$insert_invoice['business_id'] = $business_user_id;
			$insert_invoice['relationship_id'] = $relationship_id;
			$insert_invoice['debit_account_id'] = $debit_account_id;
			$insert_invoice['credit_account_id'] = $credit_account_id;
			$insert_invoice["invoice_number"] = $invoice_number;
			$insert_invoice['invoice_type'] = $db_invoice_type;
			$insert_invoice['invoice_description'] = $invoice_desc;
			$insert_invoice['invoice_category'] = $category_account["category"];
			$insert_invoice['invoice_datetime'] = date("Y-m-d G:i:s",strtotime($invoice_date));
			$insert_invoice['invoice_amount'] = $invoice_amount;
			$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
			
			db_insert_invoice($insert_invoice);
			
			//GET NEWLY CREATED INVOICE
			$where = null;
			$newly_created_invoice = db_select_invoice($insert_invoice);
			
			if(empty($bill_holder_file_guid))
			{
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
				$post_name = 'invoice_file';
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = "Invoice ".$invoice_number;
				$category = "Invoice";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = 'All';
				$driver_permission = 'None';
				$contract_secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
				
				$bill_file_guid = $contract_secure_file["file_guid"];
				
			}
			else
			{
				$bill_file_guid = $bill_holder_file_guid;
			}
			
			//UPDATE INVOICE
			$update_invoice = null;
			$update_invoice["file_guid"] = $bill_file_guid;
			
			$where = null;
			$where["id"] = $newly_created_invoice["id"];
			db_update_invoice($update_invoice,$where);
			
			//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
			//$transaction = null;
			$transaction["category"] = "New Bill";
			$transaction["description"] = $db_invoice_type." - ".$invoice_number;
			
			$entries = array();
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $credit_account_id;
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $invoice_created_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $invoice_amount;
			$credit_entry["entry_description"] = "Bill Received from Vendor | ".$invoice_desc;
			$credit_entry["file_guid"] = $bill_file_guid;
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $debit_account_id;
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $invoice_created_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $invoice_amount;
			$debit_entry["entry_description"] = "Bill Received from Vendor | ".$invoice_desc;
			$debit_entry["file_guid"] = $bill_file_guid;
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			create_transaction_and_entries($transaction,$entries);
			
			if($bill_type == "Ticket Expense")
			{
				//GET PAYMENT ACCOUNT ENTRY TO ATTACH TO INVOICE
				$where = null;
				$where = $debit_entry;
				$ticket_account_entry = db_select_account_entry($where);
				
				//CREATE NEW TICKET PAYMENT
				$ticket_payment = null;
				$ticket_payment["ticket_id"] = $ticket_id;
				$ticket_payment["account_entry_id"] = $ticket_account_entry["id"];
				db_insert_ticket_payment($ticket_payment);
			}
				
			
			//error_log("got to the end of insert_new_invoice()| LINE ".__LINE__." ".__FILE__);
			
		}
		else if($business_or_member == "Member")
		{
			//GET BILL HOLDER
			$bill_holder = null;
			if(!empty($bill_holder_id))
			{
				//GET BILL HOLDER
				$where = null;
				$where["id"] = $bill_holder_id;
				$bill_holder = db_select_bill_holder($where);
				
				//GET ORIGNAL INVOICE FOR BILL HOLDER
				$where = null;
				$where["id"] = $bill_holder["invoice_id"];
				$orginal_invoice = db_select_invoice($where);
			}
			
			//CREATE INVOICE (BILL) [FULL AMOUNT]
			//INSERT INVOICE INTO DB
			$insert_invoice = null;
			$insert_invoice['business_id'] = $business_user_id;
			$insert_invoice['relationship_id'] = $relationship_id;
			//$insert_invoice['debit_account_id'] = $debit_account_id;
			$insert_invoice['credit_account_id'] = $balance_sheet_id;
			$insert_invoice['invoice_type'] = "Expense Incurred";
			$insert_invoice['invoice_description'] = "Member bill - ".$invoice_desc;
			$insert_invoice['invoice_category'] = $orginal_invoice["invoice_category"];
			$insert_invoice['invoice_datetime'] = date("Y-m-d G:i:s",strtotime($invoice_date));
			$insert_invoice['invoice_amount'] = $invoice_amount;
			$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
			
			db_insert_invoice($insert_invoice);
			
			//GET NEWLY CREATED INVOICE
			$where = null;
			$newly_created_invoice = db_select_invoice($insert_invoice);
			

			//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
			$post_name = 'invoice_file';
			$file = $_FILES[$post_name];
			$name = str_replace(' ','_',$file["name"]);
			$type = $file["type"];
			//$title = pathinfo($file["name"], PATHINFO_FILENAME);
			$title = "Invoice ".$invoice_number;
			$category = "Invoice";
			$local_path = $file["tmp_name"];
			$server_path = '/edocuments/';
			$office_permission = 'All';
			$driver_permission = 'None';
			$contract_secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
			
			
			
			
			
			
			//CREATE PROPER ACCOUNT ENTRIES
			$transaction = null;
			$transaction["category"] = "Member Bill";
			$transaction["description"] = "Member Bill - ".$invoice_number;
				
			$entries = array();
			
			if($payment_method == "FleetProtect")
			{

				//TRANSACTION: DEBIT [FULL AMOUNT] A/R ON FP (DRIVER SPECIFIC), CREDIT [FULL AMOUNT] A/P TO VENDOR
				
				//GET VENDOR ACCOUNT
				$where = null;
				$where["id"] = $balance_sheet_id;
				$ap_to_vendor_account = db_select_account($where);
				
				//GET BUSINESS RELATIONSHIP ASSOCIATED WITH A/P TO VENDOR ACCOUNT
				$where = null;
				$where["id"] = $ap_to_vendor_account["relationship_id"];
				$vendor_acc_relationship = db_select_business_relationship($where);
				
				//GET MEMBER COMPANY
				$where = null;
				$where["id"] = $vendor_acc_relationship["related_business_id"];
				//$member_company = db_select_company($where);
				
				//GET DEFAULT A/R ON FP ACCOUNT FOR MEMBER
				$where = null;
				$where["company_id"] = $vendor_acc_relationship["related_business_id"];
				$where["category"] = "Coop A/R on FleetProtect";
				$ar_from_member_on_fp_default_account = db_select_default_account($where);
				
				$debit_account_id = $ar_from_member_on_fp_default_account["account_id"];
				
				//CREATE CREDIT ENTRY
				$credit_entry = null;
				$credit_entry["account_id"] = $ap_to_vendor_account["id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $invoice_created_datetime;
				$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = $invoice_amount;
				$credit_entry["entry_description"] = "Member Bill | ".$invoice_desc;
				//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $credit_entry;
				
				//CREATE DEBIT ENTRY
				$debit_entry = null;
				$debit_entry["account_id"] = $ar_from_member_on_fp_default_account["account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $invoice_created_datetime;
				$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = $invoice_amount;
				$debit_entry["entry_description"] = "Member Bill | ".$invoice_desc;
				//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $debit_entry;
				
				
				//TRANSACTION: DEBIT [DEDUCTIBLE AMOUNT] A/P TO MEMBER (DRIVER SPECIFIC), CREDIT [DEDUCTIBLE AMOUNT] A/R ON FP (DRIVER SPECIFIC)
				
				//GET DEDUCTIBLE AMOUNT
				$where = null;
				$where["setting_name"] = "FleetProtect Deductible";
				$fp_deductible_setting = db_select_setting($where);
				
				$deductible_amount = $fp_deductible_setting["setting_value"];
				
				if($invoice_amount < $deductible_amount)
				{
					$deductible_amount = $invoice_amount;
				}
				
				//GET A/P TO MEMBER (DRIVER SPECIFIC) ACCOUNT
				$where = null;
				$where["company_id"] = $vendor_acc_relationship["related_business_id"];
				$where["category"] = "Coop A/P to Member on Settlements";
				$ap_to_member_on_settlements_default_account = db_select_default_account($where);
				
				//CREATE CREDIT ENTRY
				$credit_entry = null;
				$credit_entry["account_id"] = $ar_from_member_on_fp_default_account["account_id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $invoice_created_datetime;
				$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = $deductible_amount;
				$credit_entry["entry_description"] = "FP Deductible on Member Bill | $".number_format($invoice_amount,2)." - ".$invoice_desc;
				//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $credit_entry;
				
				//CREATE DEBIT ENTRY
				$debit_entry = null;
				$debit_entry["account_id"] = $ap_to_member_on_settlements_default_account["account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $invoice_created_datetime;
				$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = $deductible_amount;
				$debit_entry["entry_description"] = "FP Deductible on Member Bill | $".number_format($invoice_amount,2)." - ".$invoice_desc;
				//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $debit_entry;
				
				//CREATE CLIENT_EXPENSE [DEDUCTIBLE AMOUNT]
				
				//GET CLIENT
				$where = null;
				$where["company_id"] = $vendor_acc_relationship["related_business_id"];
				$client = db_select_client($where);
				
				//GET COOP COMPANY
				$where = null;
				$where["category"] = "Coop";
				$coop_company = db_select_company($where);
				
				//INSERT CLIENT EXPENSE
				$client_expense = null;
				//$client_expense["expense_id"] = $expense["id"];
				$client_expense["client_id"] = $client["id"];
				$client_expense["owner_id"] =  $coop_company["id"];
				$client_expense["expense_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$client_expense["category"] = $orginal_invoice["invoice_category"];
				$client_expense["expense_amount"] = $deductible_amount;
				$client_expense["description"] = "FP Deductible on Member Bill | $".number_format($invoice_amount,2)." - ".$invoice_desc;
				$client_expense["is_reimbursable"] = "No";
				//$client_expense["file_guid"] = $contract_secure_file["file_guid"];
				
				db_insert_client_expense($client_expense);
				
			}
			else if($payment_method == "Next Settlement")
			{
				//TRANSACTION: DEBIT [FULL AMOUNT] A/P TO MEMBER (DRIVER SPECIFIC), CREDIT [FULL AMOUNT] A/P TO VENDOR
				
				//GET VENDOR ACCOUNT
				$where = null;
				$where["id"] = $balance_sheet_id;
				$ap_to_vendor_account = db_select_account($where);
				
				//GET BUSINESS RELATIONSHIP ASSOCIATED WITH A/P TO VENDOR ACCOUNT
				$where = null;
				$where["id"] = $ap_to_vendor_account["relationship_id"];
				$vendor_acc_relationship = db_select_business_relationship($where);
				
				//GET MEMBER COMPANY
				$where = null;
				$where["id"] = $vendor_acc_relationship["related_business_id"];
				//$member_company = db_select_company($where);
				
				//GET A/P TO MEMBER (DRIVER SPECIFIC) ACCOUNT
				$where = null;
				$where["company_id"] = $vendor_acc_relationship["related_business_id"];
				$where["category"] = "Coop A/P to Member on Settlements";
				$ap_to_member_on_settlements_default_account = db_select_default_account($where);
				
				$debit_account_id = $ap_to_member_on_settlements_default_account["account_id"];
				
				//CREATE CREDIT ENTRY
				$credit_entry = null;
				$credit_entry["account_id"] = $ap_to_vendor_account["id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $invoice_created_datetime;
				$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = $invoice_amount;
				$credit_entry["entry_description"] = "Member Bill | ".$invoice_desc;
				//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $credit_entry;
				
				//CREATE DEBIT ENTRY
				$debit_entry = null;
				$debit_entry["account_id"] = $ap_to_member_on_settlements_default_account["account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $invoice_created_datetime;
				$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = $invoice_amount;
				$debit_entry["entry_description"] = "Member Bill | ".$invoice_desc;
				//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $debit_entry;
				
				
				//CREATE CLIENT_EXPENSE [FULL AMOUNT]
				
				//GET CLIENT
				$where = null;
				$where["company_id"] = $vendor_acc_relationship["related_business_id"];
				$client = db_select_client($where);
				
				//GET COOP COMPANY
				$where = null;
				$where["category"] = "Coop";
				$coop_company = db_select_company($where);
				
				//INSERT CLIENT EXPENSE
				$client_expense = null;
				//$client_expense["expense_id"] = $expense["id"];
				$client_expense["client_id"] = $client["id"];
				$client_expense["owner_id"] =  $coop_company["id"];
				$client_expense["expense_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$client_expense["category"] = $orginal_invoice["invoice_category"];
				$client_expense["expense_amount"] = $invoice_amount;
				$client_expense["description"] = "Member Bill | $".number_format($invoice_amount,2)." - ".$invoice_desc;
				$client_expense["is_reimbursable"] = "No";
				//$client_expense["file_guid"] = $contract_secure_file["file_guid"];
				
				db_insert_client_expense($client_expense);
			}
			else if($payment_method == "Dispatch Expense")
			{
				//TRANSACTION: DEBIT [FULL AMOUNT] A/P TO MEMBERS (PARENT HOLDING), CREDIT [FULL AMOUNT] A/P TO ARROWHEAD
				
				//GET A/P TO ARROWHEAD ACCOUNT
				$where = null;
				$where["id"] = $balance_sheet_id;
				$ap_to_vendor_account = db_select_account($where);
				
				//GET BUSINESS RELATIONSHIP ASSOCIATED WITH A/P TO VENDOR ACCOUNT
				$where = null;
				$where["id"] = $ap_to_vendor_account["relationship_id"];
				$vendor_acc_relationship = db_select_business_relationship($where);
				
				//GET MEMBER COMPANY
				$where = null;
				$where["id"] = $vendor_acc_relationship["related_business_id"];
				//$member_company = db_select_company($where);
				
				//GET A/P TO MEMBERS (PARENT HOLDING) ACCOUNT
				$where = null;
				$where["company_id"] = $vendor_acc_relationship["business_id"];
				$where["category"] = "A/P to Members on Settlements";
				$ap_to_members_on_settlements_default_account = db_select_default_account($where);
				
				$debit_account_id = $ap_to_members_on_settlements_default_account["account_id"];
				
				//CREATE CREDIT ENTRY
				$credit_entry = null;
				$credit_entry["account_id"] = $ap_to_vendor_account["id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $invoice_created_datetime;
				$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = $invoice_amount;
				$credit_entry["entry_description"] = "Member Bill | ".$invoice_desc;
				//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $credit_entry;
				
				//CREATE DEBIT ENTRY
				$debit_entry = null;
				$debit_entry["account_id"] = $ap_to_members_on_settlements_default_account["account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $invoice_created_datetime;
				$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = $invoice_amount;
				$debit_entry["entry_description"] = "Member Bill | ".$invoice_desc;
				//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $debit_entry;
				
			}
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			create_transaction_and_entries($transaction,$entries);
			
			//UPDATE INVOICE
			$update_invoice = null;
			$update_invoice["debit_account_id"] = $debit_account_id;
			$update_invoice["invoice_number"] = $invoice_number;
			$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
			$where = null;
			$where["id"] = $newly_created_invoice["id"];
			db_update_invoice($update_invoice,$where);
			
			//CLOSE OUT BILL HOLDER
			if(!empty($bill_holder_id))
			{
				//CLOSE BILL HOLDER
				$update_bill_holder = null;
				$update_bill_holder["closed_datetime"] = $invoice_created_datetime;
				
				$where = null;
				$where["id"] = $bill_holder_id;
				db_update_bill_holder($update_bill_holder,$where);
			}
		}
		
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		load_upload_success_view();
	}
	
	function load_invoice_details()
	{
		//GET INVOICE
		$where = null;
		$where["id"] = $_POST["invoice_id"];
		$invoice = db_select_invoice($where);
		
		//GET INVOICE PAYMENTS
		$where = null;
		$where["invoice_id"] = $invoice["id"];
		$invoice_payments = db_select_invoice_payments($where);
		
		$data['invoice_payments'] = $invoice_payments;
		$data['invoice'] = $invoice;
		$this->load->view('bills/bill_details',$data);
	}
	
	function load_new_bill_details()
	{
		//GET BILL HOLDER
		$where = null;
		$where["id"] = $_POST["bill_holder_id"];
		$bill_holder = db_select_bill_holder($where);
		
		//GET INVOICE
		$where = null;
		$where["id"] = $bill_holder["invoice_id"];
		$invoice = db_select_invoice($where);
		
		$data['bill_holder'] = $bill_holder;
		$data['invoice'] = $invoice;
		$this->load->view('bills/bill_holder_details',$data);
	}
	
	//GET INVOICE NOTES
	function get_invoice_notes($invoice_id)
	{
		$where = null;
		$where["id"] = $invoice_id;
		$invoice = db_select_invoice($where);
		
		$data['invoice'] = $invoice;
		$this->load->view('bills/bills_invoice_notes_div',$data);
	}//end get_invoice_notes
	
	//SAVE NOTE
	function save_note()
	{
		$invoice_id = $_POST["invoice_id"];
		
		$text = $_POST["new_note"];
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		date_default_timezone_set('America/Denver');
		$date_text = date("m/d/y");
		
		$full_note = $date_text." - ".$initials." | ".$text."\n\n";
		
		$where["id"] = $invoice_id;
		$invoice = db_select_invoice($where);
		
		$update_invoice["invoice_notes"] = $full_note.$invoice["invoice_notes"];
		db_update_invoice($update_invoice,$where);
		
		$this->get_invoice_notes($invoice_id);
		
		//echo $update_load["settlement_notes"];
	}
	
	function refresh_row()
	{
		$row = $_POST["invoice_id"];
		//echo $row;
		
		//GET INVOICE
		$where = null;
		$where["id"] = $row;
		$invoice = db_select_invoice($where);
		//echo $row;
		
		$data['relationship_id'] = $_POST["relationship_id"];
		$data['invoice'] = $invoice;
		$data['row'] = $row;
		$this->load->view('bills/bill_row',$data);
		
	}
	
	function load_new_bills_report()
	{
		$payer_id = $_POST["payer_id"];
		$sent_from_id = $_POST["sent_from_id"];
		$after_date = $_POST["new_bill_after_date_filter"];
		$before_date = $_POST["new_bill_before_date_filter"];
		$person_id = $this->session->userdata('person_id');
		
		//SET WHERE FOR INVOICES
		$where = " closed_datetime IS NULL ";
		
		
		//SET WHERE FOR PAYER
		if($payer_id != "All")
		{
			$where = $where." AND company_id = ".$payer_id;
		}
		
		//SET WHERE FOR PAYER
		if($sent_from_id != "All")
		{
			$where = $where." AND from_company_id = ".$sent_from_id;
		}
		
		//SET WHERE FOR DROP START DATE
		if(!empty($after_date))
		{
			$after_date = date("Y-m-d G:i:s",strtotime($after_date));
			$where = $where." AND bill_datetime >= '".$after_date."' ";
		}
		
		//SET WHERE FOR DROP END DATE
		if(!empty($before_date))
		{
			$before_date = date("Y-m-d G:i:s",strtotime($before_date)+24*60*60);
			$where = $where." AND bill_datetime < '".$before_date."' ";
		}
		
		//GET ALL OPEN BILL HOLDERS
		$bill_holders = db_select_bill_holders($where,"created_datetime DESC");
		
		$data['bill_holders'] = $bill_holders;
		$this->load->view('bills/new_bills_report',$data);
	}
	
	function load_new_bill_dialog()
	{
		$bill_holder_id = $_POST["bill_holder_id"];
		
		echo $bill_holder_id;
		
		//$data['bill_holders'] = $bill_holders;
		//$this->load->view('bills/new_bills_report',$data);
	}
	
	function generate_invoice()
	{
		$relationship_id = $_POST["gi_invoice_relationship_id"];
		$invoice_date = $_POST["gi_invoice_date"];
		$invoice_amount = $_POST["gi_invoice_amount"];
		$invoice_number = $_POST["gi_invoice_number"];
		$invoice_desc = $_POST["gi_invoice_desc"];
		
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $relationship_id;
		$relationship = db_select_business_relationship($where);
		
		//GET RELATED BUSINESS
		$where = null;
		$where["id"] = $relationship["related_business_id"];
		$business_company = db_select_company($where);
		
		//GET BUSINESS USER
		$where = null;
		$where["id"] = $relationship["business_id"];
		$related_company = db_select_company($where);
		
		$data['business_company'] = $business_company;
		$data['related_company'] = $related_company;
		$data['invoice_date'] = $invoice_date;
		$data['invoice_amount'] = $invoice_amount;
		$data['invoice_number'] = $invoice_number;
		$data['invoice_desc'] = $invoice_desc;
		$data['title'] = "Invoice Generator";
		$this->load->view('invoices/generated_invoice',$data);
		
	}
	
}//	END INVOICES CLASS
?>