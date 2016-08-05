<?php		


	
class Receipts extends MY_Controller 
{

	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		$data['title'] = "Receipts";
		$data['tab'] = 'Receipts';
		$this->load->view('receipts_view',$data);
	}
	
	
	//LOAD LEFT BAR FILTER DIV
	function load_filter_div()
	{
		
		$category_sidebar_options = array();
		$category_sidebar_options["All"] = "All";
		//GET OPTIONS FOR EXPENSE CATEGORY FILTER
		foreach(get_distinct("category","client_expense") as $category)
		{
			$title = $category;
			$category_sidebar_options[$title] = $title;
			//echo $category;
		}
		
		//GET EXPENSE OWNERS(COMPANIES THAT BELONG TO FLEETMANAGERS OR ARE BUSINESS COMPANIES)
		$invoice_owner_where = " type = 'Fleet Manager' OR type = 'Business'";
		$bill_owners = db_select_companys($invoice_owner_where,"company_side_bar_name");
		
		//GET OPTIONS FOR BILL OWNER SIDEBAR DROPDOWN LIST
		$owner_sidebar_options = array();
		$owner_sidebar_options["All"] = "All";
		foreach ($bill_owners as $bill_owner)
		{
			$title = $bill_owner["company_side_bar_name"];
			$owner_sidebar_options[$bill_owner['id']] = $title;
		}
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		//$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$client_sidebar_options = array();
		$client_sidebar_options["All"] = "All";
		foreach($dd_all_clients as $client)
		{
			$client_sidebar_options[$client["id"]] = $client["client_nickname"];
		}
		
		$data['category_sidebar_options'] = $category_sidebar_options;
		$data['client_sidebar_options'] = $client_sidebar_options;
		$data['owner_sidebar_options'] = $owner_sidebar_options;
		$this->load->view('receipts/receipts_filter_div',$data);
	}
	
	//LOAD REPORT
	function load_report()
	{
		//GET FILTER INPUTS
		$owner_id = $_POST["owner_sidebar_dropdown"];
		$client_id = $_POST["client_sidebar_dropdown"];
		$category = $_POST["category_dropdown"];
		$after_date = $_POST["after_date_filter"];
		$before_date = $_POST["before_date_filter"];
		$get_outstanding = $_POST["get_outstanding"];
		$get_settled = $_POST["get_settled"];
		
		
		//BULD SQL QUERY FOR CLIENT_EXPENSE LIST
		$where = " is_reimbursable = 'Yes' ";
		
		//SET WHERE FOR OWNER
		if($owner_id != "All")
		{
			$where = $where." AND owner_id = '".$owner_id."'";
		}
		
		//SET WHERE FOR CLIENT
		if($client_id != "All")
		{
			$where = $where." AND client_id = '".$client_id."'";
		}
		
		//SET WHERE FOR CATEGORY
		if($category != "All")
		{
			$where = $where." AND category = '".$category."'";
		}
		
		//SET WHERE FOR DROP START DATE
		if(!empty($after_date))
		{
			$after_date = date("Y-m-d G:i:s",strtotime($after_date));
			$where = $where." AND expense_datetime >= '".$after_date."' ";
		}
		
		//SET WHERE FOR DROP END DATE
		if(!empty($before_date))
		{
			$before_date = date("Y-m-d G:i:s",strtotime($before_date)+24*60*60);
			$where = $where." AND expense_datetime < '".$before_date."' ";
		}
		
		
		//SET WHERE FOR STATUS
		$status_where = "";
		$none_checked = true;
		
		//SET WHERE FOR OUTSTANDING STATUS
		if($get_outstanding == "true")
		{
			$none_checked = false;
			$status_where = $status_where." AND (receipt_datetime IS NULL AND paid_datetime IS NULL) ";
		}
		
		//SET WHERE FOR OUTSTANDING STATUS
		if($get_settled == "true")
		{
			$none_checked = false;
			$status_where = $status_where." AND (receipt_datetime IS NOT NULL OR paid_datetime IS NOT NULL)";
		}
		
		
		$status_where = " AND (".substr($status_where,4).")";
		
		if($get_outstanding == "true" && $get_settled == "true")
		{
			$status_where = "";
		}
			
		if($none_checked)
		{
			$where = $where." AND false ";
		}
		else
		{
			$where = $where.$status_where;
		}
		
		//echo $where;
		
		$client_expenses = db_select_client_expenses($where,"expense_datetime DESC");
		
		
		$data['client_expenses'] = $client_expenses;
		$this->load->view('receipts/receipts_report',$data);
	}
	
	//GET NOTES
	function get_notes($expense_id)
	{
		$where = null;
		$where["id"] = $expense_id;
		$expense = db_select_client_expense($where);
		
		$data['expense'] = $expense;
		$this->load->view('receipts/receipt_notes_div',$data);
	}//end get_notes
	
	//SAVE NOTE
	function save_note()
	{
		$expense_id = $_POST["expense_id"];
		
		$text = $_POST["new_note"];
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		date_default_timezone_set('America/Denver');
		$date_text = date("m/d/y H:i");
		
		$full_note = $date_text." - ".$initials." | ".$text."\n\n";
		
		$where["id"] = $expense_id;
		$expense = db_select_client_expense($where);
		
		$update_expense["receipt_notes"] = $full_note.$expense["receipt_notes"];
		db_update_client_expense($update_expense,$where);
		
		$this->get_notes($expense_id);
		
		//echo $update_load["settlement_notes"];
	}
	
	//LOAD UPLOAD RECEIPT DIV
	function load_upload_receipt_div()
	{
		$client_expense_id = $_POST["client_expense_id"];
		
		//GET CLIENT EXPENSE
		$where = null;
		$where["id"] = $client_expense_id;
		$client_expense = db_select_client_expense($where);
		
		//JUST IN CASE SOMEONE ELSE IS WORKING ON THE SAME RECEIPT, OR IF RECEIPT ROW DOESN'T REFLECT UPLOAD
		if(empty($client_expense["paid_datetime"]))
		{
			
			//GET CLIENT
			$where = null;
			$where["id"] = $client_expense["client_id"];
			$client = db_select_client($where);
			
			
			//GET LOADS
			$where = null;
			$where = 'amount_funded IS NULL AND status = "Dropped" AND billing_status <> "Closed" ';
			$loads = db_select_loads($where);
			
			//CREATE LOAD OPTIONS
			$load_options = array();
			$load_options["Select"] = "Select";
			foreach($loads as $load)
			{
				$title = $load['customer_load_number']." ".$load["broker"]["customer_name"];
				$load_options[$load["id"]] = $title;
				//echo $option;
			}
			$load_options["Already Billed"] = "Already Billed";
			
			//GET BUSINESS USERS
			$where = null;
			$where["company_status"] = "Active";
			$where["type"] = "Business";
				
			$business_users = db_select_companys($where,"company_side_bar_name");
			
			//CREATE DROPDOWN LIST OF BUSINESS USERS
			$business_users_options = array();
			$business_users_options["Select"] = "Select";
			foreach($business_users as $company)
			{
				$title = $company["company_side_bar_name"];
				$business_users_options[$company["id"]] = $title;
			}
			
			//GET COOP-DRIVER RELATIONSHIP
			$where = null;
			$where["business_id"] = $client_expense["owner_id"];
			$where["related_business_id"] = $client["company_id"];
			$coop_member_relationship = db_select_business_relationship($where);
			
			//GET FLEETPROTECT ACCOUNT
			$where = null;
			$where["category"] = "FleetProtect";
			$where["relationship_id"] = $coop_member_relationship["id"];
			$fleetprotect_accounts = db_select_accounts($where);
			
			//CREATE FLEETPROTECT ACCOUNTS OPTIONS
			$fp_accounts_options = array();
			$fp_accounts_options["Select"] = "Select";
			foreach($fleetprotect_accounts as $account)
			{
				$title = $account['account_name'];
				$fp_accounts_options[$account["id"]] = $title;
				//echo $option;
			}
			
			//GET OWNER REVENUE ACCOUNTS
			$where = null;
			$where = ' company_id = '.$client_expense["owner_id"].' AND account_class = "Revenue" AND parent_account_id IS NOT NULL ';
			$rev_accounts = db_select_accounts($where);
			
			$rev_options = array();
			$rev_options["Select"] = "Select";
			if(!empty($rev_accounts))
			{
				foreach($rev_accounts as $account)
				{
					$title = $account["account_name"];
					$rev_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			$data['client_expense'] = $client_expense;
			$data['rev_options'] = $rev_options;
			$data['business_users_options'] = $business_users_options;
			$data['fp_accounts_options'] = $fp_accounts_options;
			$data['load_options'] = $load_options;
			$data['client_expense_id'] = $client_expense_id;
			$this->load->view('receipts/upload_receipt_div',$data);
		}
		else
		{
			echo "This receipt has already been uploaded. Refresh the page to see the most current list.";
		}
	}
	
	//UPLOAD RECEIPT
	function upload_receipt()
	{
		$recorder_id = $this->session->userdata('person_id');
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d H:i:s");
	
		//GET INPUTS
		$client_expense_id = $_POST["client_expense_id"];
		$who_pays = $_POST["who_pays"];
		$business_user_id = $_POST["business_user_id"];
		$expense_account_id = $_POST["expense_account_id"];
		$load_id = $_POST["load_id"];
		$fp_account_id = $_POST["fp_account_id"]; //WE CAN GET THIS ACCOUNT THROUGHT HE DEFAULT ACCOUNTS -- CHANGE IN FUTURE
		$rev_account_id = $_POST["rev_account_id"];
		$receipt_amount = round($_POST["receipt_amount"],2);
		$more_receipts = $_POST["more_receipts"];
		
		if($who_pays == "Lost Receipt" || $who_pays == "Driver")
		{
			$receipt_amount = 0;
			$more_receipts = 'false';
		}

		
		//GET CLIENT EXPENSE
		$where = null;
		$where["id"] = $client_expense_id;
		$client_expense = db_select_client_expense($where);

		//GET CLIENT
		$where = null;
		$where["id"] = $client_expense["client_id"];
		$this_client = db_select_client($where);
		
		//CREATE NEW NOTE TO ADD TO NOTES
		$text = "Receipt uploaded for $".number_format($receipt_amount,2)." assigned to ".$who_pays;
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		$date_text = date("m/d/y H:i");
		
		$full_note = $date_text." - ".$initials." | ".$text."\n\n";
		
		//SETTLE BUSINESS ADVANCE
		$update_ce = null;
		$update_ce["receipt_amount"] = number_format($receipt_amount,2);
		$update_ce["receipt_datetime"] = $recorded_datetime;
		$update_ce["paid_datetime"] = $recorded_datetime;
		//$update_ce["file_guid"] = $secure_file["file_guid"];
		$update_ce["receipt_notes"] = $full_note.$client_expense["receipt_notes"];
		
		$where = null;
		$where["id"] = $client_expense["id"];
		db_update_client_expense($update_ce,$where);
		
		//GET RELATIONSHIP
		$where = null;
		$where["business_id"] = $client_expense["owner_id"];
		$where["related_business_id"] = $this_client["company_id"];
		$where["relationship"] = "Member";
		$relationship = db_select_business_relationship($where);
		
		//echo "business_id ".$client_expense["owner_id"];
		//echo "<br>client company id ".$this_client["company_id"];
		
		//GET A/P TO MEMBER ON SETTLEMENT DEFAULT ACCOUNT
		$where = null;
		$where["company_id"] = $this_client["company_id"];
		$where["category"] = "Coop A/P to Member on Settlements";
		$ap_to_member_on_settlement_default_account = db_select_default_account($where);
		
		//GET DRIVER PAYABLE ACCOUNT
		$where = null;
		//$where = ' category = "Settlements Payable" AND account_type = "Member" AND parent_account_id IS NOT NULL AND relationship_id = '.$relationship["id"];
		$where["id"] = $ap_to_member_on_settlement_default_account["account_id"];
		$driver_payable_account = db_select_account($where);
		
		
		
		/**
		//DEPENDING ON WHO PAYS - HANDLE THE EXPENSE
		if($who_pays == "Client")
		{
			//CREATE NON-REIMBURSABLE CLIENT EXPENSE
			$driver_expense = null;
			$driver_expense["expense_id"] = $client_expense["expense_id"];
			$driver_expense["settlement_id"] = $client_expense["settlement_id"];
			$driver_expense["client_id"] = $client_expense["client_id"];
			$driver_expense["owner_id"] =  $client_expense["owner_id"];
			$driver_expense["expense_datetime"] = $client_expense["expense_datetime"];
			$driver_expense["category"] = $client_expense["category"];
			$driver_expense["expense_amount"] = $receipt_amount;
			$driver_expense["description"] = $client_expense["description"];
			$driver_expense["is_reimbursable"] = "No";
			db_insert_client_expense($driver_expense);
		}
		else if($who_pays == "Driver Damage")
		{
			$entry_amount = $receipt_amount;
		
			$client_deductible_limit = 100;
			
			$avoidable = $_POST["avoidable_dropdown"];
			
			//GET CLIENT'S DAMAGE ACCOUNT
			$where = null;
			$where["company_id"] = $this_client["company"]["id"];
			$where["category"] = "Client Damage";
			$damage_account = db_select_account($where);

			if($_POST["estimate_dropdown"] == "No Estimate")
			{
				if($avoidable == "Avoidable")
				{
					//CALCULATE DEDUCTABLE
					if($entry_amount > $client_deductible_limit)
					{
						$deductible_amount = $client_deductible_limit;
						
						$damage_amount = round($entry_amount - $deductible_amount,2);	
						
						//DEBIT DRIVER'S DAMAGE ACCOUNT
						$debit_damage = null;
						$debit_damage["account_id"] = $damage_account["id"];
						$debit_damage["recorder_id"] = $recorder_id;
						$debit_damage["entry_datetime"] = $entry_datetime;
						$debit_damage["entry_type"] = "Damage Payment";
						$debit_damage["debit_credit"] = "Debit";
						$debit_damage["entry_amount"] = round($damage_amount,2);
						$debit_damage["entry_description"] = "Damage Payment | $$entry_amount LESS $$deductible_amount deductible | $entry_description";
						$debit_damage["entry_link"] = $entry_link;
				
						db_insert_account_entry($debit_damage);
						
						//SET CE AMOUNT
						$ce_amount = $client_deductible_limit;
						
						//SET CLIENT EXPENSE DESCRIPTION FOR DEDUCTABLE
						$ce_description = "Damage Payment | $$deductible_amount deductible on $$entry_amount damage | $entry_description";
					}
					else
					{
						//SET CE AMOUNT
						$ce_amount = $entry_amount;
						
						//SET CLIENT EXPENSE DESCRIPTION FULL DAMAGE
						$ce_description = "Damage Payment | $entry_description";
					}
				
					//CREATE CLIENT EXPENSE FOR DAMAGE
					$damage_expense = null;
					$damage_expense["expense_id"] = $client_expense["expense_id"];
					$damage_expense["client_id"] = $client_expense["client_id"];
					$damage_expense["owner_id"] =  $client_expense["owner_id"];
					$damage_expense["expense_datetime"] = $client_expense["expense_datetime"];
					$damage_expense["category"] = $client_expense["category"];
					$damage_expense["expense_amount"] = round($ce_amount,2);
					$damage_expense["description"] = $ce_description;
					$damage_expense["is_reimbursable"] = "No";
					$damage_expense["link"] = $entry_link;
					
					db_insert_client_expense($damage_expense);
				}
				else
				{
					//DEBIT DRIVER'S DAMAGE ACCOUNT
					$debit_damage = null;
					$debit_damage["account_id"] = $damage_account["id"];
					$debit_damage["recorder_id"] = $recorder_id;
					$debit_damage["entry_datetime"] = $entry_datetime;
					$debit_damage["entry_type"] = "Damage Payment";
					$debit_damage["debit_credit"] = "Debit";
					$debit_damage["entry_amount"] = round($entry_amount,2);
					$debit_damage["entry_description"] = "Damage Payment | Unavoidable | $entry_description";
					$debit_damage["entry_link"] = $entry_link;
			
					db_insert_account_entry($debit_damage);
				}
			}
			else //if there is an estimate
			{
				//TODO: 
					//TAKE INTO ACCOUNT THE DEDUCTABLE PAID AT THE TIME OF THE DAMAGE ESTIMATE
				
				//GET ESTIMATE
				$where = null;
				$where["id"] = $_POST["estimate_dropdown"];
				$estimate_entry = db_select_account_entry($where);
				
				//DEBIT DRIVER'S DAMAGE ACCOUNT
				$debit_damage["account_id"] = $damage_account["id"];
				$debit_damage["recorder_id"] = $recorder_id;
				$debit_damage["entry_datetime"] = $entry_datetime;
				$debit_damage["entry_type"] = "Damage Payment";
				$debit_damage["debit_credit"] = "Debit";
				$debit_damage["entry_amount"] = $entry_amount;
				$debit_damage["entry_description"] = "Damage Payment | $entry_description";
				$debit_damage["entry_link"] = $entry_link;
		
				db_insert_account_entry($debit_damage);
				
				
				//CREDIT DRIVER'S DAMAGE ACCOUNT TO REIMBURSE FOR THE ESTIMATE ENTRY
				$adjusting_entry["account_id"] = $damage_account["id"];
				$adjusting_entry["recorder_id"] = $recorder_id;
				$adjusting_entry["entry_datetime"] = $entry_datetime;
				$adjusting_entry["entry_type"] = "Damage Adjustment";
				$adjusting_entry["debit_credit"] = "Credit";
				$adjusting_entry["entry_amount"] = round($estimate_entry["entry_amount"],2);
				$adjusting_entry["entry_description"] = "Adjusting Entry | Cancel out damage estimate from ".date("m/d/y H:i",strtotime($estimate_entry["entry_datetime"]));
				$adjusting_entry["entry_link"] = $entry_link;
		
				db_insert_account_entry($adjusting_entry);
				
				if($more_receipts == 'false')
				{
					$update_entry["is_approved"] = "Yes";
				}
				
				//UPDATE THE ESTIMATE ENTRY TO NOTE THE ADJUSTING ENTRY
				$update_entry["entry_description"] = "ADJUSTED ".date("m/d/y H:i",strtotime($entry_datetime))." | ".$estimate_entry["entry_description"];
				$where = null;
				$where["id"] = $estimate_entry["id"];
				db_update_account_entry($update_entry,$where);
			}
		}
		else if($who_pays == "Driver Equipment")
		{
			//GET DRIVER EQUIPMENT ACCOUNT
			$where = null;
			$where["company_id"] = $this_client["company"]["id"];
			$where["category"] = "Driver Equipment";
			$de_account = db_select_account($where);
			
			//DEBIT DRIVER EQUIPMENT ACCOUNT
			$debit_de["account_id"] = $de_account["id"];
			$debit_de["recorder_id"] = $recorder_id;
			$debit_de["entry_datetime"] = $entry_datetime;
			$debit_de["entry_type"] = "Driver Equipment Purchase";
			$debit_de["debit_credit"] = "Debit";
			$debit_de["entry_amount"] = round($receipt_amount,2);
			$debit_de["entry_description"] = "Driver Equipment | $entry_description";
			$debit_de["entry_link"] = $entry_link;
	
			db_insert_account_entry($debit_de);
			
			//CREATE DRIVER EQUIPMENT (FUTURE FEATURE)
		}
		else if($who_pays == "Lost Receipt")
		{
			$more_receipts = 'false';
			$receipt_amount = 0;
		}
		**/
		
		
		
		
		
		//CALCULATE REMAINING BALANCE ON CLIENT EXPENSE
		$ce_balance = $client_expense["expense_amount"] - $receipt_amount;
		
		//IF THERE ARE MORE RECEIPTS
		if($more_receipts == 'true')
		{
		
			//CREATE A NEW CLIENT EXPENSE FOR THE REMAINING BALANCE ON THE ADVANCE
			$balance_ce = null;
			$balance_ce["expense_id"] = $client_expense["expense_id"];
			$balance_ce["settlement_id"] = $client_expense["settlement_id"];
			$balance_ce["client_id"] = $client_expense["client_id"];
			$balance_ce["owner_id"] =  $client_expense["owner_id"];
			$balance_ce["expense_datetime"] = $client_expense["expense_datetime"];
			$balance_ce["category"] = $client_expense["category"];
			$balance_ce["expense_amount"] = round($ce_balance,2);
			$balance_ce["description"] = $client_expense["description"];
			$balance_ce["is_reimbursable"] = "Yes";
			$balance_ce["receipt_notes"] = $full_note.$client_expense["receipt_notes"];
			db_insert_client_expense($balance_ce);
			
		}
		else
		{
			//GET PERSONAL ADVANCE FEE FROM SETTINGS
			$where = null;
			$where["setting_name"] = "PA Fee";
			$pa_fee_setting = db_select_setting($where);
			$pa_fee = $pa_fee_setting["setting_value"];
			
			
			if($ce_balance > 0.01)
			{
				//CALCULATE ADVANCE FEE
				$advance_fee = round($ce_balance*$pa_fee,2);
			
				//CREATE A CLIENT EXPENSE FOR A PERSONAL ADVANCE FOR THE REMAINING BALANCE OF THE BA
				$balance_ce = null;
				$balance_ce["expense_id"] = $client_expense["expense_id"];
				$balance_ce["settlement_id"] = $client_expense["settlement_id"];
				$balance_ce["client_id"] = $client_expense["client_id"];
				$balance_ce["owner_id"] =  $client_expense["owner_id"];
				$balance_ce["expense_datetime"] = $client_expense["expense_datetime"];
				$balance_ce["category"] = $client_expense["category"];
				$balance_ce["expense_amount"] = round($ce_balance + $advance_fee,2);
				$balance_ce["description"] = "Balance on Business Advance | $".number_format($ce_balance,2)." balance +  $".number_format($advance_fee,2)." advance fee |".$client_expense["description"];
				$balance_ce["is_reimbursable"] = "No";
				db_insert_client_expense($balance_ce);
			}
		}
		
		
		$update_ce = null;
		if($who_pays == "Lost Receipt" || $who_pays == "Driver")
		{
		}
		else
		{
			if(!empty($_FILES['receipt_file']))
			{
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
				$post_name = 'receipt_file';
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = "Receipt ".$client_expense_id;
				$category = "Receipt";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = 'All';
				$driver_permission = 'None';
				$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
			
			
				
				//UPDATE CLIENT EXPENSE WITH FILE GUID 
				$update_ce = null;
				$update_ce["file_guid"] = $secure_file["file_guid"];
				
				$where = null;
				$where["id"] = $client_expense["id"];
				db_update_client_expense($update_ce,$where);
			}
		}
		
		
		
		
		//DEPENDING ON WHO PAYS - ENTER THE PROPER ACCOUNT ENTRIES
		if($who_pays == "Business User")
		{
			//CREATE TRANSACTION
			$transaction["category"] = "Business Expense";
			$transaction["description"] = $client_expense["description"];
			
			$entries = array();
			
			//CREDIT DRIVER PAYABLE ACCOUNT - RECEIPT AMOUNT
			$credit_entry = null;
			$credit_entry["account_id"] = $driver_payable_account["id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $receipt_amount;
			$credit_entry["entry_description"] = $client_expense["description"];
			$credit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//DEBIT OWNER COMPANY EXPENSE ACCOUNT - RECEIPT AMOUNT
			$debit_entry = null;
			$debit_entry["account_id"] = $_POST["expense_account_id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $receipt_amount;
			$debit_entry["entry_description"] = $client_expense["description"];;
			$debit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			$new_transaction = create_transaction_and_entries($transaction,$entries);
			
			//UPDATE CLIENT EXPENSE WITH FILE GUID (previously set) AND TRANSACTION ID
			$update_ce = null;
			$update_ce["transaction_id"] = $new_transaction["id"];
			
			$where = null;
			$where["id"] = $client_expense["id"];
			db_update_client_expense($update_ce,$where);
		}
		else if($who_pays == "Broker")
		{
			//GET LOAD
			$where = null;
			$where["id"] = $load_id;
			$load = db_select_load($where);
			
			//GET CUSTOMER
			$where = null;
			$where["id"] = $load["broker_id"];
			$customer = db_select_customer($where);
			
			//GET CUSTOMER COMPANY
			$where = null;
			$where["id"] = $customer["company_id"];
			$broker_company = db_select_company($where);
			
			//GET BROKER RELATIONSHIP WITH COOP
			$where = null;
			$where["business_id"] = $client_expense["owner_id"];
			$where["related_business_id"] = $broker_company["id"];
			$where["relationship"] = "Member Customer";
			$broker_coop_relationship = db_select_business_relationship($where);
			
			if(!empty($broker_coop_relationship))
			{
				//GET BROKER'S A/R ACOUNT WITH THE COOP
				$where = null;
				$where = ' account_type = "Holding" AND account_class = "Asset" AND relationship_id = '.$broker_coop_relationship["id"];
				$broker_ar_account = db_select_account($where);
			}
			
			if(!empty($broker_ar_account))
			{
				//CREATE TRANSACTION
				$transaction["category"] = "Broker Expense";
				$transaction["description"] = $client_expense["description"];
				
				$entries = array();
				
				//CREDIT DRIVER PAYABLE ACCOUNT - RECEIPT AMOUNT
				$credit_entry = null;
				$credit_entry["account_id"] = $driver_payable_account["id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $recorded_datetime;
				$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = $receipt_amount;
				$credit_entry["entry_description"] = "Receipt Turned In | ".$client_expense["description"];
				$credit_entry["file_guid"] = $secure_file["file_guid"];
				
				$entries[] = $credit_entry;
				
				//DEBIT A/R FROM BROKER ACCOUNT - RECEIPT AMOUNT
				$debit_entry = null;
				$debit_entry["account_id"] = $broker_ar_account["id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $recorded_datetime;
				$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = $receipt_amount;
				$debit_entry["entry_description"] = "Broker Expense | ".$client_expense["description"];;
				$debit_entry["file_guid"] = $secure_file["file_guid"];
				
				$entries[] = $debit_entry;
				
				//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
				$new_transaction = create_transaction_and_entries($transaction,$entries);
				
				
				//CREATE LOAD EXPENSE - RECEIPT AMOUNT
				$load_expense = null;
				$load_expense["load_id"] = $load["id"];
				$load_expense["expense_amount"] = $receipt_amount;
				$load_expense["explanation"] = "Reimbursable Load Expense";
				$load_expense["is_billable"] = "Yes";
				$load_expense["receipt_datetime"] = $recorded_datetime;
				$load_expense["file_guid"] = $secure_file["file_guid"];
				
				db_insert_load_expense($load_expense);
			
				
				//UPDATE CLIENT EXPENSE AND TRANSACTION ID
				$update_ce = null;
				$update_ce["transaction_id"] = $new_transaction["id"];
				
				$where = null;
				$where["id"] = $client_expense["id"];
				db_update_client_expense($update_ce,$where);
			}
			
		}
		else if($who_pays == "Driver")
		{
			//CREATE TRANSACTION
			$transaction["category"] = "Business Expense";
			$transaction["description"] = $client_expense["description"];
			
			$entries = array();
			
			//CREDIT DRIVER PAYABLE ACCOUNT - BA AMOUNT
			$credit_entry = null;
			$credit_entry["account_id"] = $driver_payable_account["id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $client_expense["expense_amount"];
			$credit_entry["entry_description"] = "Receipt Turned In | ".$client_expense["description"];
			//$credit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			
			//DEBIT DRIVER PAYABLE ACCOUNT - BA AMOUNT
			$debit_entry = null;
			$debit_entry["account_id"] = $driver_payable_account["id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $client_expense["expense_amount"];
			$debit_entry["entry_description"] = "Driver Expense | ".$client_expense["description"];
			//$debit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			$new_transaction = create_transaction_and_entries($transaction,$entries);
			
			//UPDATE CLIENT EXPENSE WITH FILE GUID (previously set) AND TRANSACTION ID
			$update_ce = null;
			$update_ce["transaction_id"] = $new_transaction["id"];
			
			$where = null;
			$where["id"] = $client_expense["id"];
			db_update_client_expense($update_ce,$where);
			
			//CREATE CLIENT EXPENSE - BA AMOUNT
			$driver_expense = null;
			$driver_expense["expense_id"] = $client_expense["expense_id"];
			$driver_expense["settlement_id"] = $client_expense["settlement_id"];
			$driver_expense["client_id"] = $client_expense["client_id"];
			$driver_expense["owner_id"] =  $client_expense["owner_id"];
			$driver_expense["expense_datetime"] = $client_expense["expense_datetime"];
			$driver_expense["category"] = $client_expense["category"];
			$driver_expense["expense_amount"] = round($client_expense["expense_amount"],2);
			$driver_expense["description"] = "Driver Expense | ".$client_expense["description"];
			$driver_expense["is_reimbursable"] = "No";
			$driver_expense["receipt_notes"] = $full_note.$client_expense["receipt_notes"];
			db_insert_client_expense($driver_expense);
			
		}
		else if($who_pays == "FleetProtect")
		{
			//CREATE TRANSACTION
			$transaction["category"] = "Business Expense";
			$transaction["description"] = $client_expense["description"];
			
			$entries = array();
			
			//CREDIT DRIVER PAYABLE ACCOUNT - RECEIPT AMOUNT
			$credit_entry = null;
			$credit_entry["account_id"] = $driver_payable_account["id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $receipt_amount;
			$credit_entry["entry_description"] = "Receipt Turned In | ".$client_expense["description"];
			$credit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//DEBIT A/R ON FLEETPROTECT LOAN - RECEIPT AMOUNT
			$debit_entry = null;
			$debit_entry["account_id"] = $fp_account_id;
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $receipt_amount;
			$debit_entry["entry_description"] = "Loan to Driver | ".$client_expense["description"];;
			$debit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CALCULATE DOWN PAYMENT AMOUNT FROM SETTINGS
			$where = null;
			$where["setting_name"] = "FleetProtect Deductible";
			$down_payment_setting = db_select_setting($where);
			$max_down = $down_payment_setting["setting_value"];

			$down_payment = min($max_down,$receipt_amount);
			
			
			//CREDIT A/R FLEETPROTECT ACCOUNT - FLEETPROTECT DOWN PAYMENT
			$credit_entry = null;
			$credit_entry["account_id"] = $fp_account_id;
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $down_payment;
			$credit_entry["entry_description"] = "FleetProtect Deductible | ".$client_expense["description"];
			$credit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//DEBIT DRIVER PAYABLE ACCOUNT - FLEETPROTECT DOWN PAYMENT
			$debit_entry = null;
			$debit_entry["account_id"] = $driver_payable_account["id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $down_payment;
			$debit_entry["entry_description"] = "FleetProtect Deductible | ".$client_expense["description"];;
			$debit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			$new_transaction = create_transaction_and_entries($transaction,$entries);
			
			//CREATE CLIENT EXPENSE - FLEETPROTECT DOWN PAYMENT
			$driver_expense = null;
			$driver_expense["expense_id"] = $client_expense["expense_id"];
			$driver_expense["settlement_id"] = $client_expense["settlement_id"];
			$driver_expense["client_id"] = $client_expense["client_id"];
			$driver_expense["owner_id"] =  $client_expense["owner_id"];
			$driver_expense["expense_datetime"] = $client_expense["expense_datetime"];
			$driver_expense["category"] = $client_expense["category"];
			$driver_expense["expense_amount"] = round($down_payment,2);
			$driver_expense["description"] = "FleetProtect Deductible | ".$client_expense["description"];
			$driver_expense["is_reimbursable"] = "No";
			$driver_expense["transaction_id"] = $new_transaction["id"];
			$driver_expense["file_guid"] = $secure_file["file_guid"];
			$driver_expense["receipt_notes"] = $full_note.$client_expense["receipt_notes"];
			db_insert_client_expense($driver_expense);
			
			
		}
		else if($who_pays == "Lost Receipt")
		{
			
		
			//CREATE TRANSACTION
			$transaction["category"] = "Business Expense";
			$transaction["description"] = $client_expense["description"];
			
			$entries = array();
			
			//CREDIT DRIVER PAYABLE ACCOUNT - BA AMOUNT
			$credit_entry = null;
			$credit_entry["account_id"] = $driver_payable_account["id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $client_expense["expense_amount"];
			$credit_entry["entry_description"] = "Receipt Lost | ".$client_expense["description"];
			//$credit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//DEBIT DRIVER PAYABLE ACCOUNT - BA AMOUNT
			$debit_entry = null;
			$debit_entry["account_id"] = $driver_payable_account["id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $client_expense["expense_amount"];
			$debit_entry["entry_description"] = "Driver Expense - No Receipt | ".$client_expense["description"];
			//$debit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//GET PERSONAL ADVANCE FEE FROM SETTINGS
			$where = null;
			$where["setting_name"] = "PA Fee";
			$pa_fee_setting = db_select_setting($where);
			$pa_fee = $pa_fee_setting["setting_value"];
			
			//CALCULATE ADVANCE FEE
			$advance_fee = round($client_expense["expense_amount"]*$pa_fee,2);
			
			//CREDIT REVENUE ACCOUNT  - PA FEE
			$credit_entry = null;
			$credit_entry["account_id"] = $rev_account_id;
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $advance_fee;
			$credit_entry["entry_description"] = "Advance Fee | ".$client_expense["description"];
			//$credit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//DEBIT DRIVER PAYABLE ACCOUNT - PA FEE
			$debit_entry = null;
			$debit_entry["account_id"] = $driver_payable_account["id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($client_expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $advance_fee;
			$debit_entry["entry_description"] = "Missing Receipt Fee | ".$client_expense["description"];
			//$debit_entry["file_guid"] = $secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			$new_transaction = create_transaction_and_entries($transaction,$entries);
			
			//CREATE CLIENT EXPENSE - BA AMOUNT + PA FEE
			$no_receipt_ce = null;
			$no_receipt_ce["expense_id"] = $client_expense["expense_id"];
			$no_receipt_ce["settlement_id"] = $client_expense["settlement_id"];
			$no_receipt_ce["client_id"] = $client_expense["client_id"];
			$no_receipt_ce["owner_id"] =  $client_expense["owner_id"];
			$no_receipt_ce["expense_datetime"] = $client_expense["expense_datetime"];
			$no_receipt_ce["category"] = $client_expense["category"];
			$no_receipt_ce["expense_amount"] = round($client_expense["expense_amount"] + $advance_fee,2);
			$no_receipt_ce["description"] = "Business Advance - No Receipt | $".number_format($client_expense["expense_amount"],2)." BA +  $".number_format($advance_fee,2)." advance fee |".$client_expense["description"];
			$no_receipt_ce["transaction_id"] = $new_transaction["id"];
			//$no_receipt_ce["file_guid"] = $secure_file["file_guid"];
			$no_receipt_ce["is_reimbursable"] = "No";
			db_insert_client_expense($no_receipt_ce);
			
		}
		
		
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		load_upload_success_view();
	}
	
	function refresh_row($client_expense_id)
	{
		//GET CLIENT EXPENSE
		$where = null;
		$where["id"] = $client_expense_id;
		$client_expense = db_select_client_expense($where);
		
		$data['client_expense'] = $client_expense;
		$this->load->view('receipts/receipt_row',$data);
		//echo $client_expense["id"];
	}
	
	function business_user_selected()
	{
		$business_user_id = $_POST["business_user_id"];
		
		//GET EXPENSE ACCOUNTS FOR BUSINESS
		$where = null;
		$where = ' company_id ='.$business_user_id.' AND account_class = "Expense" and parent_account_id IS NOT NULL ';
		$expense_accounts = db_select_accounts($where);
		
		$expense_options = array();
		$expense_options["Select"] = "Select";
		if(!empty($expense_accounts))
		{
			foreach($expense_accounts as $account)
			{
				$title = $account["account_name"];
				$expense_options[$account["id"]] = $title;
				//echo $option;
			}
		}
		
		$data['expense_options'] = $expense_options;
		$this->load->view('receipts/expense_account_options',$data);
		
	}
	
	function upload_receipt_file()
	{
		$ce_id =  $_POST["client_expense_id"];
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
		$post_name = 'row_receipt_file';
		$file = $_FILES[$post_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		//$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$title = "Receipt ".$ce_id;
		$category = "Receipt";//TRUCK ATTACHEMENT OR TRAILER ATTACHMENT
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
	
		//UPDATE CLIENT EXPENSE
		$update = null;
		$update["file_guid"] = $secure_file["file_guid"];
		$where = null;
		$where["id"] = $ce_id;
		db_update_client_expense($update,$where);
	}
	
	
	
	
	
	
	
	
	
	
}