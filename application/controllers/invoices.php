<?php		

class Invoices extends MY_Controller 
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
		
		$data['business_users_options'] = $business_users_options;
		$data['tab'] = 'Invoices';
		$data['title'] = "Invoices";
		if(user_has_permission("view invoice tab"))
		{
			$this->load->view('invoices_view',$data);
		}
		else
		{
			redirect(base_url("index.php/home"));
		}
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
		$status = $_POST['invoice_status'];
		
		$where = null;
		$where['related_business_id'] = $person_id;
		$relationship = db_select_business_relationship($where);
		$relationship_business_id = $relationship['business_id'];
		
		//SET WHERE FOR INVOICES
		$where = ' (invoice_type = "Revenue Generated" OR invoice_type = "Deposit Receivable")';
		
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
		
		// echo $where;
		
		$invoices = db_select_invoices($where,"invoice_datetime DESC");
		
		$data['relationship_id'] = $relationship_id;
		$data['invoices'] = $invoices;
		$this->load->view('invoices/invoices_report',$data);
	}
	
	function business_user_selected()
	{
		$business_user_id = $_POST["business_user"];
		$invoice_type = $_POST["invoice_type"];
		
		if($invoice_type == "Revenue Generated" || $invoice_type == "Deposit Receivable")
		{
			//GET lIST OF CUSTOMERS
			$where = null;
			$where["business_id"] = $business_user_id;
			$where["relationship"] = "customer";
			$business_relationships = db_select_business_relationships($where);
		
			$cust_vend = "Customer";
		}
		else if($invoice_type == "Expense Incurred" || $invoice_type == "Deposit Payable")
		{
			//GET lIST OF VENDORS
			$where = null;
			$where["business_id"] = $business_user_id;
			$where["relationship"] = "vendor";
			$business_relationships = db_select_business_relationships($where);
		
			$cust_vend = "Vendor";
		}
		else
		{
			// //GET lIST OF CUSTOMERS/VENDORS
			// $where = null;
			// $where["business_id"] = $business_user_id;
			// $business_relationships = db_select_business_relationships($where);
		
			// $cust_vend = "Customer/Vendor";
			
			//GET lIST OF CUSTOMERS
			$where = null;
			$where["business_id"] = $business_user_id;
			$where["relationship"] = "customer";
			$business_relationships = db_select_business_relationships($where);
		
			$cust_vend = "Customer";
		}
		
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
		
		$data['cust_vend'] = $cust_vend;
		$data['relationship_options'] = $relationship_options;
		$this->load->view('invoices/relationship_filter_dropdown_options',$data);
	}
	
	//LOADS MEMBER_SELECTION_TABLE AFTER BUSINESS USER IS SELECTED
	function new_invoice_business_user_selected()
	{
		$company_id = $_POST["business_user_id"];
		$person_id = $this->session->userdata('person_id');
		
		//GET COMPANY OF PERSON
		$where = null;
		$where = " (category = 'Office Staff' OR category = 'Fleet Manager') AND person_id = ".$person_id;
		$user_company = db_select_company($where);
		
		$where = null;
		$where['business_id'] = $company_id;
		$where['related_business_id'] = $user_company["id"];
		$user_business_relationship = db_select_business_relationship($where);
		
		if((user_has_permission("create invoice for assigned business")&&!empty($user_business_relationship))||(user_has_permission("create invoice for non-assigned business")))
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
				
				$script = '$("#invoice_type_row").show();';
			}
			
			$data['script'] = $script;
			$data['member_options'] = $member_options;
			$this->load->view('invoices/member_selection_table',$data);
		}
		else
		{
			echo "<br><br><div style='color:red'>You don't have permission to create a new invoice.</div>";
		}
	}

	function payment_selection_form()
	{
		$business_user_id = $_POST["business_user_id"];
		$relationship_id = $_POST["member_id"];
		$payment_method = $_POST["payment_method"];
		$new_invoice_type = $_POST["new_member_invoice_type"];
		
		
		if($new_invoice_type == "Revenue Generated")
		{
			$income_statement_account_label = "Revenue Account";
			
			//GET CUSTOMER REVENUE ACCOUNTS
			$where = null;
			$where = ' company_id ='.$business_user_id.' AND account_class = "Revenue" AND parent_account_id IS NOT NULL ';
			$accounts = db_select_accounts($where);
			
			$income_statement_account_options = array();
			$income_statement_account_options["Select"] = "Select";
			foreach($accounts as $account)
			{
				$title = $account["account_name"];
				$income_statement_account_options[$account["id"]] = $title;
				//echo $option;
			}
		}
		else if($new_invoice_type == "Request Deposit")
		{
			$income_statement_account_label = "Deposit Account";
			
			//GET CUSTOMER REVENUE ACCOUNTS
			$where = null;
			$where = ' company_id ='.$business_user_id.' AND account_class = "Asset" AND parent_account_id IS NOT NULL ';
			$accounts = db_select_accounts($where);
			
			$income_statement_account_options = array();
			$income_statement_account_options["Select"] = "Select";
			foreach($accounts as $account)
			{
				$title = $account["account_name"];
				$income_statement_account_options[$account["id"]] = $title;
				//echo $option;
			}
		}
		
		
		$data['payment_method'] = $payment_method;
		$data['relationship_id'] = $relationship_id;
		$data['income_statement_account_label'] = $income_statement_account_label;
		$data['income_statement_account_options'] = $income_statement_account_options;
		$data['new_invoice_type'] = $new_invoice_type;
		$data['business_user_id'] = $business_user_id;
		$this->load->view('invoices/new_member_invoice_form',$data);
	}
	
	function load_customer_vendor_selection_div()
	{
		$member_or_business = $_POST["member_or_business"];
		$member_id = $_POST["member_id"];
		$business_user_id = $_POST["business_user_id"];
		$new_invoice_type = $_POST["new_invoice_type"];
		
		$customer_vendor = "Customer";
		
		//GET LIST OF CUSTOMERS
		$where = null;
		$where["business_id"] = $business_user_id;
		$where["relationship"] = "customer";
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
		
		$data['member_or_business'] = $member_or_business;
		$data['member_id'] = $member_id;
		$data['customer_vendor'] = $customer_vendor;
		$data['new_invoice_type'] = $new_invoice_type;
		$data['business_user_id'] = $business_user_id;
		$data['relationship_options'] = $relationship_options;
		$this->load->view('invoices/new_relationship_selection_form',$data);
	}
	
	//LOADS NEW INVOICE FORM AFTER CUSTOMER OR VENDOR IS SELECTED
	function customer_vendor_selected()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		
		$member_or_business = $_POST["relationship_selected_business_or_member"];
		$member_relationship_id = $_POST["relationship_selected_member_id"];
		$business_user_id = $_POST["relationship_selected_business_user_id"];
		$new_invoice_type = $_POST["relationship_selected_new_invoice_type"];
		$relationship_id = $_POST["relationship_selected_relationship_id"];
		
		
		if($member_or_business == "Member")
		{
			
			$balance_sheet_account_label = "Receivable Account";
			//GET CUSTOMER ASSET ACCOUNTS
			$where = null;
			$where["relationship_id"] = $relationship_id;
			$where["account_class"] = "Asset";
			$accounts = db_select_accounts($where);
			
			$balance_sheet_account_options = array();
			$balance_sheet_account_options["Select"] = "Select";
			foreach($accounts as $account)
			{
				$title = $account["account_name"];
				$balance_sheet_account_options[$account["id"]] = $title;
				//echo $option;
			}
			
			$income_statement_account_label = "Revenue Account";
			//GET CUSTOMER REVENUE ACCOUNTS
			$where = null;
			$where["relationship_id"] = $relationship_id;
			$where["account_class"] = "Revenue";
			$accounts = db_select_accounts($where);
			
			$income_statement_account_options = array();
			$income_statement_account_options["Select"] = "Select";
			foreach($accounts as $account)
			{
				$title = $account["account_name"];
				$income_statement_account_options[$account["id"]] = $title;
				//echo $option;
			}
			
			$deposit_account_label = "Security Deposit Account";
			//GET CUSTOMER DEPOSIT ACCOUNT (LIABILITY)
			$where = null;
			$where["relationship_id"] = $relationship_id;
			$where["account_class"] = "Liability";
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
		else //IF BUSINESS
		{
			$balance_sheet_account_label = "Receivable Account";
			//GET CUSTOMER ASSET ACCOUNTS
			$where = null;
			$where["relationship_id"] = $relationship_id;
			$where["account_class"] = "Asset";
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
			
			$income_statement_account_label = "Revenue Account";
			//GET CUSTOMER REVENUE ACCOUNTS
			$where = null;
			$where["relationship_id"] = $relationship_id;
			$where["account_class"] = "Revenue";
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
			
			$deposit_account_label = "Security Deposit Account";
			//GET CUSTOMER DEPOSIT ACCOUNT (LIABILITY)
			$where = null;
			$where["relationship_id"] = $relationship_id;
			$where["account_class"] = "Liability";
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
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $relationship_id;
		$relationship = db_select_business_relationship($where);
		
		//GET BUSINESS USER COMPANY
		$where = null;
		$where["id"] = $relationship["business_id"];
		$business_user = db_select_company($where);
		
		//GET INVOICE CODE FROM BUSINESS NAME
		$name_array = explode(" ",$business_user["company_name"]);
		$business_user_code_long = "";
		foreach($name_array as $w)
		{
			$business_user_code_long .= $w[0];
		}
		$business_user_code = substr($business_user_code_long,0,3);
		
		//GET VENDOR/CUSTOMER
		$where = null;
		$where["id"] = $relationship["related_business_id"];
		$related_business = db_select_company($where);
	
		//GET INVOICE CODE FROM RELATED-BUSINESS NAME
		$name_array = explode(" ",$related_business["company_name"]);
		$related_company_code_long = "";
		foreach($name_array as $w)
		{
			$related_company_code_long .= $w[0];
		}
		$related_company_code = substr($related_company_code_long,0,3);
		
		
		//GET UNIQUE INVOICE NUMBER USING CURRENT DATETIME
		//$unique_num = dechex(time() - 1441245956);
		$unique_num = strtoupper(dechex(time() - 1409709956));//ALL UPPERCASE HEX CODE FOR TIME INTEGER FROM A CERTAIN TIME IN 2014
		$invoice_number = $business_user_code.":".$related_company_code.":".$unique_num;
		
		
		$data['invoice_number'] = $invoice_number;
		$data['relationship_id'] = $relationship_id;
		$data['deposit_account_label'] = $deposit_account_label;
		$data['income_statement_account_label'] = $income_statement_account_label;
		$data['balance_sheet_account_label'] = $balance_sheet_account_label;
		$data['balance_sheet_account_options'] = $balance_sheet_account_options;
		$data['income_statement_account_options'] = $income_statement_account_options;
		$data['deposit_account_options'] = $deposit_account_options;
		$data['new_invoice_type'] = $new_invoice_type;
		$data['business_user_id'] = $business_user_id;
		$this->load->view('invoices/new_invoice_form',$data);
	}
	
	function relationship_selected()
	{
		$business_user_id = $_POST["business_user"];
		$relationship_id = $_POST["relationship_id"];
		$invoice_type = $_POST["invoice_type"];
		
		if($business_user_id!="All")
		{
			if($relationship_id == "All")
			{
				if($invoice_type == "Revenue Generated" || $invoice_type == "Deposit Receivable")
				{
					$where = 'company_id = '.$business_user_id.' AND (account_type = "Vendor" OR account_type = "Customer") AND account_class = "Asset"';
				}
				else if($invoice_type == "Expense Incurred" || $invoice_type == "Deposit Payable")
				{
					$where = 'company_id = '.$business_user_id.' AND (account_type = "Vendor" OR account_type = "Customer") AND account_class = "Liability"';
				}
				else
				{
					$where = 'company_id = '.$business_user_id.' AND (account_type = "Vendor" OR account_type = "Customer") AND (account_class = "Asset" || account_class = "Liability")';
				}
			
				$accounts = db_select_accounts($where);
			}
			else
			{
				//GET ACCOUNTS FOR THIS RELATIONSHIP
				$where = 'relationship_id = '.$relationship_id.' AND (account_class = "Asset" OR account_class = "Liability")';
				$accounts = db_select_accounts($where);
			}
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
		$this->load->view('invoices/account_filter_options',$data);
	}
	
	//CREATE AND INSERT INVOICE INTO DB FROM NEW INVOICE FORM
	function insert_new_invoice()
	{
		
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$invoice_created_datetime = date("Y-m-d G:i:s");
		$recorder_id = $this->session->userdata('user_id');
		
		//GET GENERIC DATA ABOUT THE INVOICE
		$invoice_date = $_POST["new_invoice_date"];
		$invoice_number = $_POST["new_invoice_number"];
		$invoice_amount = round($_POST["new_invoice_amount"],2);
		$invoice_desc = $_POST["new_invoice_desc"];
		
		$transaction = null;
		
		//DETERMINE RELATIONSHIP TYPE
		$relationship_id = $_POST["new_invoice_relationship_id"];
		$where = null;
		$where["id"] = $relationship_id;
		$business_relationship = db_select_business_relationship($where);
		
		if($business_relationship["relationship"] == "Member")
		{
			$payment_method = $_POST["new_invoice_payment_method"];
			$income_statement_id = $_POST["income_statement_id"];
			
			
			//GET DEFAULT ACCOUNT A/P TO MEMBER ON SETTLEMENT
			$where = null;
			$where["company_id"] = $business_relationship["related_business_id"];
			$where["category"] = "Coop A/P to Member on Settlements";
			$ap_to_member_on_settlement_default_account = db_select_default_account($where);
			//echo $ap_to_member_on_settlement_default_account["account_id"]." ";
			
			
			//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
			$post_name = 'invoice_file';
			$file = $_FILES[$post_name];
			$name = str_replace(' ','_',$file["name"]);
			$type = $file["type"];
			//$title = pathinfo($file["name"], PATHINFO_FILENAME);
			$title = "Client Expense Invoiced".$invoice_created_datetime;
			$category = "Invoice";
			$local_path = $file["tmp_name"];
			$server_path = '/edocuments/';
			$office_permission = 'All';
			$driver_permission = 'None';
			$contract_secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
			
			
			if($payment_method == "Next Settlement")
			{
				
				//GET REVENUE ACCOUNT
				$where = null;
				$where["id"] = $_POST["income_statement_id"];
				$rev_account = db_select_account($where);
				
				$transaction["category"] = "Member Expense Invoiced";
				$transaction["description"] = $invoice_desc;
				
				$entries = array();
				
				//CREDIT COOP INCOME_STATEMENT_ACCOUNT (FULL AMOUNT)
				$credit_entry = null;
				$credit_entry["account_id"] = $_POST["income_statement_id"];//REVENUE ACCOUNT
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $invoice_created_datetime;
				$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = $invoice_amount;
				$credit_entry["entry_description"] = "Member Expense | ".$invoice_desc;
				$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $credit_entry;
				
				//DEBIT A/P TO MEMBER ON SETTLEMENTS (DEDUCTABLE AMOUNT)
				$debit_entry = null;
				$debit_entry["account_id"] = $ap_to_member_on_settlement_default_account["account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $invoice_created_datetime;
				$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = $invoice_amount;
				$debit_entry["entry_description"] = "Member Expense | ".$rev_account["category"]." - ".$invoice_desc;
				$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $debit_entry;
				
				//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
				create_transaction_and_entries($transaction,$entries);
				
				//CREATE CLIENT_EXPENSE FOR MEMBER (DEDUCTABLE AMOUNT)
				
				//GET CLIENT
				$where = null;
				$where["company_id"] = $business_relationship["related_business_id"];
				$client = db_select_client($where);
				
				//GET COOP COMPANY
				$where = null;
				$where["category"] = "Coop";
				$coop_company = db_select_company($where);
				
				$client_expense = null;
				//$client_expense["expense_id"] = $expense["id"];
				$client_expense["client_id"] = $client["id"];
				$client_expense["owner_id"] =  $coop_company["id"];
				$client_expense["expense_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$client_expense["category"] = $rev_account["category"];
				$client_expense["expense_amount"] = $invoice_amount;
				$client_expense["description"] = "Member Expense | ".$rev_account["category"]." - ".$invoice_desc;
				$client_expense["is_reimbursable"] = "No";
				//$client_expense["file_guid"] = $contract_secure_file["file_guid"];
				
				db_insert_client_expense($client_expense);
				
				
				echo "success!";
				
				
			}
			else if($payment_method == "FleetProtect")
			{
				//GET DEFAULT ACCOUNT A/R FROM MEMBER ON FLEET PROTECT
				$where = null;
				$where["company_id"] = $business_relationship["related_business_id"];
				$where["category"] = "Coop A/R on FleetProtect";
				$ar_from_member_on_fp_default_account = db_select_default_account($where);
				//echo $ar_from_member_on_fp_default_account["account_id"]." ";
				
				//DETERMINE DEDUCTABLE AMOUNT
				$where = null;
				$where["setting_name"] = "FleetProtect Deductible";
				$deductible_setting = db_select_setting($where);
				
				$deductible_amount = $deductible_setting["setting_value"];
				
				if($invoice_amount < $deductible_amount)
				{
					$deductible_amount = $invoice_amount;
				}
				
				
				$transaction["category"] = "Member Expense Invoiced";
				$transaction["description"] = $invoice_desc;
				
				$entries = array();
				
				//CREDIT COOP INCOME_STATEMENT_ACCOUNT (FULL AMOUNT)
				$credit_entry = null;
				$credit_entry["account_id"] = $_POST["income_statement_id"];//REVENUE ACCOUNT
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $invoice_created_datetime;
				$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = $invoice_amount;
				$credit_entry["entry_description"] = $invoice_desc;
				$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $credit_entry;
				
				//DEBIT A/R FROM MEMBER ON FP ACCOUNT (FULL AMOUNT)
				$debit_entry = null;
				$debit_entry["account_id"] = $ar_from_member_on_fp_default_account["account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $invoice_created_datetime;
				$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = $invoice_amount;
				$debit_entry["entry_description"] = $invoice_desc;
				$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $debit_entry;
				
				//CREDIT A/R FROM MEMBER ON FP ACCOUNT (DEDUCTABLE AMOUNT)
				$credit_entry = null;
				$credit_entry["account_id"] = $ar_from_member_on_fp_default_account["account_id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $invoice_created_datetime;
				$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = $deductible_amount;
				$credit_entry["entry_description"] = "FP Deductible on Member Bill | $".number_format($invoice_amount,2)." - ".$invoice_desc;
				$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $credit_entry;
				
				//DEBIT A/P TO MEMBER ON SETTLEMENTS (DEDUCTABLE AMOUNT)
				$debit_entry = null;
				$debit_entry["account_id"] = $ap_to_member_on_settlement_default_account["account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $invoice_created_datetime;
				$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = $deductible_amount;
				$debit_entry["entry_description"] = "FP Deductible on Member Bill | $".number_format($invoice_amount,2)." - ".$invoice_desc;
				$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $debit_entry;
				
				//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
				create_transaction_and_entries($transaction,$entries);
				
				//CREATE CLIENT_EXPENSE FOR MEMBER (DEDUCTABLE AMOUNT)
				
				//GET CLIENT
				$where = null;
				$where["company_id"] = $business_relationship["related_business_id"];
				$client = db_select_client($where);
				
				//GET COOP COMPANY
				$where = null;
				$where["category"] = "Coop";
				$coop_company = db_select_company($where);
				
				//GET REVENUE ACCOUNT
				$where = null;
				$where["id"] = $_POST["income_statement_id"];
				$rev_account = db_select_account($where);
				
				$client_expense = null;
				//$client_expense["expense_id"] = $expense["id"];
				$client_expense["client_id"] = $client["id"];
				$client_expense["owner_id"] =  $coop_company["id"];
				$client_expense["expense_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$client_expense["category"] = $rev_account["category"];
				$client_expense["expense_amount"] = $deductible_amount;
				$client_expense["description"] = "FP Deductible on Member Bill | $".number_format($invoice_amount,2)." - ".$invoice_desc;
				$client_expense["is_reimbursable"] = "No";
				//$client_expense["file_guid"] = $contract_secure_file["file_guid"];
				
				db_insert_client_expense($client_expense);
				
				
				//echo "success!";
			}
		}
		else
		{
		
			$business_user_id = $_POST["new_invoice_business_user_id"];
			$invoice_type = $_POST["new_invoice_invoice_type"];
			$balance_sheet_id = $_POST["balance_sheet_id"];
			$income_statement_id = $_POST["income_statement_id"];
			$deposit_account_id = $_POST["deposit_account_id"];
			
			
			//$invoice_number = $_POST["new_invoice_number"];
			
			
			//DETERMINE DEBIT AND CREDIT ACCOUNTS
			$debit_account_id = null;
			$credit_account_id = null;
			$db_invoice_type = null;
				
			if($invoice_type == "Revenue Generated")
			{
				$debit_account_id = $balance_sheet_id;
				$credit_account_id = $income_statement_id;
				$db_invoice_type = "Revenue Generated";
			}
			else if($invoice_type == "Request Deposit")
			{
				$debit_account_id = $balance_sheet_id;
				$credit_account_id = $deposit_account_id;
				$db_invoice_type = "Deposit Receivable";
			}
			
			//GET ACCOUNT FOR CATEGORY
			$where = null;
			$where["id"] = $credit_account_id;
			$category_account = db_select_account($where);
				
			
			
			//INSERT INVOICE INTO DB
			$insert_invoice = null;
			$insert_invoice['business_id'] = $business_user_id;
			$insert_invoice['relationship_id'] = $relationship_id;
			$insert_invoice['debit_account_id'] = $debit_account_id;
			$insert_invoice['credit_account_id'] = $credit_account_id;
			$insert_invoice['invoice_type'] = $db_invoice_type;
			$insert_invoice['invoice_description'] = $invoice_desc;
			$insert_invoice['invoice_category'] = $category_account["category"];
			$insert_invoice['invoice_datetime'] = date("Y-m-d G:i:s",strtotime($invoice_date));
			$insert_invoice['invoice_amount'] = $invoice_amount;
			$insert_invoice['invoice_number'] = $invoice_number;
			$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
			
			db_insert_invoice($insert_invoice);
			
			//GET NEWLY CREATED INVOICE
			$where = null;
			$newly_created_invoice = db_select_invoice($insert_invoice);
			
			//GET RELATIONSHIP
			$where = null;
			$where["id"] = $category_account["relationship_id"];
			$relationship = db_select_business_relationship($where);
			
			//GET BUSINESS USER COMPANY
			$where = null;
			$where["id"] = $relationship["business_id"];
			$business_user = db_select_company($where);
			
			// //GET INVOICE CODE FROM BUSINESS NAME
			// $name_array = explode(" ",$business_user["company_name"]);
			// $business_user_code_long = "";
			// foreach($name_array as $w)
			// {
				// $business_user_code_long .= $w[0];
			// }
			// $business_user_code = substr($business_user_code_long,0,3);
			
			// //GET VENDOR/CUSTOMER
			// $where = null;
			// $where["id"] = $relationship["related_business_id"];
			// $related_business = db_select_company($where);
		
			// //GET INVOICE CODE FROM RELATED-BUSINESS NAME
			// $name_array = explode(" ",$related_business["company_name"]);
			// $related_company_code_long = "";
			// foreach($name_array as $w)
			// {
				// $related_company_code_long .= $w[0];
			// }
			// $related_company_code = substr($related_company_code_long,0,3);
			
			// $invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
			

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
			
			//UPDATE INVOICE
			$update_invoice = null;
			$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
			
			
			//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
			//$transaction = null;
			$transaction["category"] = "New Invoice";
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
			$credit_entry["entry_description"] = $invoice_desc;
			$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $debit_account_id;
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $invoice_created_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $invoice_amount;
			$debit_entry["entry_description"] = $invoice_desc;
			$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			create_transaction_and_entries($transaction,$entries);
			
			
			$where = null;
			$where["id"] = $newly_created_invoice["id"];
			db_update_invoice($update_invoice,$where);
			
			//GET PAYEE BUSINESS COMPANY
			$where = null;
			$where["id"] = $relationship["related_business_id"];
			$payer_company = db_select_company($where);
			
			//IF PAYER IS A BUSINESS USER, CREATE BILL HOLDER FOR PAYER
			if($payer_company["type"] == "Business")
			{
				
				//CREATE BILL HOLDING FOR ARROWHEAD
				$bill_holder = null;
				$bill_holder["invoice_id"] = $newly_created_invoice["id"];
				$bill_holder["company_id"] = $payer_company["id"];//payer
				$bill_holder["from_company_id"] = $business_user_id;//payee
				$bill_holder["created_datetime"] = $invoice_created_datetime;
				$bill_holder["bill_datetime"] = date("Y-m-d G:i:s",strtotime($invoice_date));
				$bill_holder["description"] = "Bill from ".$business_user["company_side_bar_name"]." Invoice ".$invoice_number." | ".$invoice_desc;
				$bill_holder["amount"] = $invoice_amount;
				$bill_holder["file_guid"] = $contract_secure_file["file_guid"];
				
				db_insert_bill_holder($bill_holder);
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
		$this->load->view('invoices/invoice_details',$data);
	}
	
	//GET INVOICE NOTES
	function get_invoice_notes($invoice_id)
	{
		$where = null;
		$where["id"] = $invoice_id;
		$invoice = db_select_invoice($where);
		
		$data['invoice'] = $invoice;
		$this->load->view('invoices/invoice_notes_div',$data);
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
		$this->load->view('invoices/invoice_row',$data);
		
	}
	
	function generate_invoice()
	{
		$business_user_id = $_POST["gi_business_user_id"];
		$relationship_id = $_POST["gi_invoice_relationship_id"];
		$invoice_date = $_POST["gi_invoice_date"];
		$invoice_amount = $_POST["gi_invoice_amount"];
		$invoice_number = $_POST["gi_invoice_number"];
		$invoice_desc = $_POST["gi_invoice_desc"];
		
		//GET BUSINESS USER
		$where = null;
		$where["id"] = $business_user_id;
		$business_company = db_select_company($where);
		
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $relationship_id;
		$relationship = db_select_business_relationship($where);
		
		//GET RELATED BUSINESS
		$where = null;
		$where["id"] = $relationship["related_business_id"];
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