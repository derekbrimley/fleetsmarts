<?php		


	
class Expenses extends MY_Controller 
{

	function index()
	{
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//GET CASH ACCOUNT LIST FOR SELECTED COMPANY
		$where = null;
		//$where = "account_group = 'TAB' OR account_group = 'Comdata' OR account_group = 'Ultimate' OR account_group = 'Spark CC' OR account_group = 'SmartPay' OR account_group = 'Venture CC' OR category = 'Invoice Allocations' ";
		//$where["category"] = "Cash";
		$where = ' category = "Cash" AND parent_account_id IS NOT NULL ';
		$source_accounts = db_select_accounts($where,"account_name");
		
		//CREATE DROPDOWN LIST OF ALLOWED CASH ACCOUNTS
		$source_accounts_options = array();
		foreach($source_accounts as $account)
		{
			$source_accounts_option['account_id'] = $account["id"];
			$source_accounts_option['account_name'] = $account["account_name"];
			
			$source_accounts_options[] = $source_accounts_option;
		}
		
		//CREATE DROPDOWN LIST OF CASH ACCOUNTS
		$cash_account_options = array();
		$cash_account_options["Select"] = "Select";
		foreach($source_accounts as $account)
		{
			$cash_account_options[$account["id"]] = $account["account_name"];
		}
		
		
		$data['cash_account_options'] = $cash_account_options;
		$data['source_accounts_options'] = $source_accounts_options;
		$data['title'] = "Transactions";
		$data['tab'] = 'Transactions';
		if(user_has_permission("view transactions tab"))
		{
			$this->load->view('expenses_view',$data);
		}
		else
		{
			redirect(base_url("index.php/home"));
		}
	}
	
	function load_unallocated_filter_div()
	{
		//GET COMPANIES WHO HAVE SPARK CC
		$where = null;
		$where = " spark_cc_number IS NOT NULL ";
		$spark_holder_companies = db_select_companys($where);
	
		//CREATE DROPDOWN FOR ISSUER
		$issuer_sidebar_options = array();
		$issuer_sidebar_options["All"] = "All";
		foreach($spark_holder_companies as $issier)
		{
			$title = $issier["company_side_bar_name"];
			$issuer_sidebar_options[$issier['id']] = $title;
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
		
		//GET CASH ACCOUNT LIST FOR SELECTED COMPANY
		$where = null;
		//$where = "account_group = 'TAB' OR account_group = 'Comdata' OR account_group = 'Ultimate' OR account_group = 'Spark CC' OR account_group = 'SmartPay' OR account_group = 'Venture CC' OR category = 'Invoice Allocations' ";
		//$where["category"] = "Cash";
		$where = ' category = "Cash" AND parent_account_id IS NOT NULL ';
		$source_accounts = db_select_accounts($where,"account_name");
		
		//CREATE DROPDOWN LIST OF ALLOWED CASH ACCOUNTS
		$source_accounts_options = array();
		foreach($source_accounts as $account)
		{
				if($account["category"] == "Invoice Allocations")
				{
					//GET ACCOUN COMPANY
					$where = null;
					$where["id"] = $account["company_id"];
					$account_company = db_select_company($where);
				
					$source_accounts_option['account_name'] = "Invoices - ".$account_company["company_side_bar_name"];
				}
				else
				{
					$source_accounts_option['account_name'] = $account["account_name"];
				}
			
				$source_accounts_option['account_id'] = $account["id"];
				
				$source_accounts_options[] = $source_accounts_option;
			}
		
		$category_sidebar_options = array();
		$category_sidebar_options["All"] = "All";
		//GET OPTIONS FOR EXPENSE CATEGORY FILTER
		foreach(get_distinct("category","expense") as $category)
		{
		
			$title = $category;
			if(empty($title))
			{
				$title = "Unassigned";
			}
		
			$category_sidebar_options[$title] = $title;
			//echo $category;
		}
		
		
		$data['category_sidebar_options'] = $category_sidebar_options;
		$data['issuer_sidebar_options'] = $issuer_sidebar_options;
		$data['source_accounts_options'] = $source_accounts_options;
		$data['bill_owner_sidebar_options'] = $bill_owner_sidebar_options;
		$this->load->view('expenses/unallocated_filter_div',$data);
	}
	
	function load_report()
	{
		//GET EXPENSE TYPE
		$expense_type = $_POST["expense_type_dropdown"];
		$issuer = $_POST["issuer_sidebar_dropdown"];
		$bill_owner = $_POST["bill_owner_sidebar_dropdown"];
		$category = $_POST["category_dropdown"];
		$after_date = $_POST["after_date_filter"];
		$before_date = $_POST["before_date_filter"];
		$is_locked = $_POST["locked_dropdown"];
		$is_recorded = $_POST["recorded_dropdown"];
		$person_id = $this->session->userdata('person_id');
		
		$where = null;
		$where['related_business_id'] = $person_id;
		$relationship = db_select_business_relationship($where);
		$relationship_business_id = $relationship['business_id'];

		if((user_has_permission("view all transactions"))||user_has_permission("view all transactions for assigned business")&&!empty($relationship))
		{
			
			//echo $issuer;
			
			//GET COMPANIES WHO HAVE SPARK CC
			$where = null;
			$where = " spark_cc_number IS NOT NULL ";
			$spark_holder_companies = db_select_companys($where);
		
			//CREATE DROPDOWN FOR ISSUER
			$issuer_sidebar_options = array();
			$issuer_sidebar_options["All"] = "All";
			foreach($spark_holder_companies as $exp_issuer)
			{
				$title = $exp_issuer["company_side_bar_name"];
				$issuer_sidebar_options[$exp_issuer['id']] = $title;
			}
		
			//GET EXPENSE OWNERS(COMPANIES THAT BELONG TO FLEETMANAGERS OR ARE BUSINESS COMPANIES)
			$invoice_owner_where = " type = 'Business' AND company_status = 'Active'";
			$bill_owners = db_select_companys($invoice_owner_where,"company_side_bar_name");
			
			//GET OPTIONS FOR BILL OWNER SIDEBAR DROPDOWN LIST
			$bill_owner_sidebar_options = array();
			$bill_owner_sidebar_options[""] = "";
			foreach($bill_owners as $owner)
			{
				$title = $owner["company_side_bar_name"];
				$bill_owner_sidebar_options[$owner['id']] = $title;
			}
			//$bill_owner_sidebar_options["Revenue"] = "Revenue";
			//$bill_owner_sidebar_options["Invoice Payment"] = "Invoice Payment";
			//$bill_owner_sidebar_options["Transfer"] = "Transfer";
		
			//GET CASH ACCOUNT LIST FOR SELECTED COMPANY
			$where = null;
			//$where = "account_group = 'TAB' OR account_group = 'Comdata' OR account_group = 'Ultimate' OR account_group = 'Spark CC' OR account_group = 'SmartPay' OR account_group = 'Venture CC' OR category = 'Invoice Allocations' ";
			$where = ' category = "Cash" AND parent_account_id IS NOT NULL ';
			$source_accounts = db_select_accounts($where,"account_name");
			
			//CREATE DROPDOWN LIST OF ALLOWED CASH ACCOUNTS
			$source_accounts_options = array();
			foreach($source_accounts as $account)
			{
				$source_accounts_option['account_name'] = $account["account_name"];
				$source_accounts_option['account_id'] = $account["id"];
				
				$source_accounts_options[] = $source_accounts_option;
			}
			
			//BUILD SQL QUERY TO GRAB ALL THE EXPENSES
			$where = null;
			if($expense_type != "All")
			{
				$where = " AND expense_type = '".$expense_type."' ";
			}
			//$where = " AND expense_type = 'Expense' ";
			//echo "<br>".$issuer;
			//SET WHERE FOR ISSUER (company_id)
			if($issuer != "All")
			{
				if($issuer == "Unassigned")
				{
					$where = $where." AND issuer_id IS NULL ";
				}
				else
				{
					$where = $where." AND issuer_id = '".$issuer."'";
				}
			}
			
			//PERMISSIONS
			if(user_has_permission("view all transactions")==false && user_has_permission("view all transactions for assigned business"))
			{
				$where = $where." AND company_id = ".$relationship_business_id;
			}
			
			//SET WHERE FOR OWNER (company_id)
			if($bill_owner != "All")
			{
				if($bill_owner == "Unassigned")
				{
					$where = $where." AND company_id IS NULL ";
				}
				else if($bill_owner == "Revenue")
				{
					$where = $where." AND company_id IS NULL AND owner_type = 'Revenue' ";
				}
				else if($bill_owner == "Invoice Payment")
				{
					$where = $where." AND company_id IS NULL AND owner_type = 'Invoice Payment' ";
				}
				else if($bill_owner == "Transfer")
				{
					$where = $where." AND company_id IS NULL AND owner_type = 'Transfer' ";
				}
				else
				{
					$where = $where." AND company_id = ".$bill_owner;
				}
			}
			
			//SET WHERE FOR CATEGORY
			if($category != "All")
			{
				if($category == "Unassigned")
				{
					$where = $where." AND category IS NULL ";
				}
				else
				{
					$where = $where." AND category = '".$category."' ";
				}
			}
			
			//SET WHERE FOR DROP START DATE
			if(!empty($after_date))
			{
				$after_date = date("Y-m-d G:i:s",strtotime($after_date));
				$where = $where." AND expense_datetime >= '".$after_date."' ";
			}
			else
			{
				$default_after_date = "01/18/2014";
				$default_after_date = date("Y-m-d G:i:s",strtotime($default_after_date));
				$where = $where." AND expense_datetime >= '".$default_after_date."' ";
			}
			
			//SET WHERE FOR DROP END DATE
			if(!empty($before_date))
			{
				$before_date = date("Y-m-d G:i:s",strtotime($before_date)+24*60*60);
				$where = $where." AND expense_datetime < '".$before_date."' ";
			}
			
			//SET WHERE FOR LOCKED
			if($is_locked != "All")
			{
				if($is_locked == 'Locked')
				{
					$where = $where." AND locked_datetime IS NOT NULL ";
				}
				else
				{
					$where = $where." AND locked_datetime IS NULL ";
				}
			}
			
			//SET WHERE FOR RECORDED
			if($is_recorded != "All")
			{
				if($is_recorded == 'Recorded')
				{
					$where = $where." AND recorded_datetime IS NOT NULL ";
				}
				else
				{
					$where = $where." AND recorded_datetime IS NULL ";
				}
			}
			
			$source_where = "";
			$none_checked = true;
			foreach($source_accounts_options as $source)
			{
				//echo $source["account_name"]." - ".$_POST["get_".$source["account_id"]]."<br>";
				//echo $source["account_name"]." - ".$source["account_id"]."<br>";
				if(array_key_exists("get_".$source["account_id"],$_POST))
				{
					if($_POST["get_".$source["account_id"]] == "true")
					{
						$none_checked = false;
						$source_where = $source_where." OR expense_account_id = ".$source["account_id"];
					}
				}
			}
			$source_where = " AND (".substr($source_where,4).")";
			
			if($none_checked)
			{
				$where = $where." AND 1 = 2 ";
			}
			else
			{
				$where = $where.$source_where;
			}
			$where = substr($where,4);
			
			
			//echo $where;
			$expenses = db_select_expenses($where,"expense_datetime DESC");
			
			$data['issuer_sidebar_options'] = $issuer_sidebar_options;
			$data['bill_owner_sidebar_options'] = $bill_owner_sidebar_options;
			$data['expenses'] = $expenses;
			$this->load->view('expenses/unallocated_expense_report',$data);
		}
		else
		{
			echo "<div id='main_content_header'>
			<span style='font-weight:bold;'>Transactions</span>
			<div style='float:right; width:25px;'>
				<img id='filter_loading_icon' name='filter_loading_icon' src='/images/loading.gif' style='float:right; height:20px; padding-top:5px; display:none;' />
				<img id='refresh_logs' name='refresh_logs' src='/images/refresh.png' title='Refresh Log' style='cursor:pointer; float:right; height:20px; padding-top:5px;' onclick='load_report()' />
			</div>
			</div><br><br><div style='margin-left:15px;color:red'>You don't have permission to view this report.</div>";
		}
		
	}
	
	function load_expense_row($expense_id)
	{
		//GET COMPANIES WHO HAVE SPARK CC
		$where = null;
		$where = " spark_cc_number IS NOT NULL ";
		$spark_holder_companies = db_select_companys($where);
	
		//CREATE DROPDOWN FOR ISSUER
		$issuer_sidebar_options = array();
		$issuer_sidebar_options["All"] = "All";
		foreach($spark_holder_companies as $exp_issuer)
		{
			$title = $exp_issuer["company_side_bar_name"];
			$issuer_sidebar_options[$exp_issuer['id']] = $title;
		}
	
		//GET EXPENSE OWNERS(COMPANIES THAT BELONG TO FLEETMANAGERS OR ARE BUSINESS COMPANIES)
		$invoice_owner_where = " type = 'Business' AND company_status = 'Active'";
		$bill_owners = db_select_companys($invoice_owner_where,"company_side_bar_name");
		
		//GET OPTIONS FOR BILL OWNER SIDEBAR DROPDOWN LIST
		$bill_owner_sidebar_options = array();
		$bill_owner_sidebar_options[""] = "";
		foreach($bill_owners as $owner)
		{
			$title = $owner["company_side_bar_name"];
			$bill_owner_sidebar_options[$owner['id']] = $title;
		}
		//$bill_owner_sidebar_options["Revenue"] = "Revenue";
		//$bill_owner_sidebar_options["Invoice Payment"] = "Invoice Payment";
		//$bill_owner_sidebar_options["Transfer"] = "Transfer";
		
		//GET NEWLY SAVED EXPENSE
		$where = null;
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		$data['expense'] = $expense;
		//$data['category_options'] = $category_options;
		$data['issuer_sidebar_options'] = $issuer_sidebar_options;
		$data['bill_owner_sidebar_options'] = $bill_owner_sidebar_options;
		$this->load->view('expenses/expense_row',$data);
	}
	
	function save_expense()
	{
		date_default_timezone_set('America/Denver');
		
		$user_id = $this->session->userdata('user_id');
		$expense_id =  $_POST["row"];
		$issuer_id = $_POST["issuer_dropdown_".$expense_id];
		$company_id = $_POST["owner_dropdown_".$expense_id];
		$category = $_POST["category_dropdown_".$expense_id];
		$is_recorded = $_POST["recorded_".$expense_id];
		$is_locked = $_POST["locked_".$expense_id];
		$note_text = $_POST["lock_notes_".$expense_id];
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $expense_id;
		$this_expense = db_select_expense($where);
		
		//CREATE LOCK NOTE
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		$date_text = date("m/d/y H:i");
		$full_note = $date_text." - ".$initials." | ".$note_text."\n\n";
		
		//SET EXPENSE TO UPDATE
		$expense = null;
		$expense["category"] = $category;
		
		//ONLY USERS WITH PERMISSION CAN CHANGE THE ISSUER
		if(user_has_permission("lock non-owned expenses") && empty($this_expense["issuer_id"]))
		{
			$expense["issuer_id"] = $issuer_id;
		}
		if(user_has_permission("allow user to change owner on transaction"))
		{
			$expense["company_id"] = $company_id;
		}
		/**
		//IF PERMISSIONS EXIST - UPDATE OWNER AND IS_RECORDED
		//if(user_has_permission("edit expenses"))
		//{
			//ASSIGN OWNER TO EXPENSE
			if(is_numeric($company_id))
			{
				$expense["company_id"] = $company_id;
				$expense["owner_type"] = "Company";
			}
			else
			{
				$expense["company_id"] = null;
				$expense["owner_type"] = $company_id; //which is actually a string
			}
			
			//IF IS RECORDED IS CHECKED
			if($is_recorded == 'recorded')
			{
				$expense["recorded_datetime"] = date("Y-m-d H:i:s");
			}
			else
			{
				$expense["recorded_datetime"] = NULL;
			}
		//}
		**/
		
		//GET USER
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		
		// error_log("company_id = ".$company_id);
		//print_to_log("company_id = ".$company_id);
		//AS LONG AS OWNER HAS BEEN ASSIGNED AND CATEGORY AS BEEN ASSIGNED
		if(!empty($company_id) && !empty($category))
		{
			//GET OWNER COMPANY
			$where = null;
			$where["id"] = $company_id;
			$owner_company = db_select_company($where);

			//print_to_log("test inside if");
			if($user["person_id"] == $owner_company["person_id"])
			{
				//IF EXPENSE IS LOCKED
				if($is_locked == 'yes')
				{
					$expense["locked_datetime"] = date("Y-m-d H:i:s");
					$expense["expense_notes"] = "Locked: ".$full_note.$this_expense["expense_notes"];
				}
				else
				{
					$expense["locked_datetime"] = null;;
				}
			}
			else
			{
				//IF USER HAS PERMISSION TO LOCKED NON-OWNED EXPENSES
				if(user_has_permission("lock non-owned expenses"))
				{
					//print_to_log($is_locked);
					//IF EXPENSE IS LOCKED
					if($is_locked == 'yes')
					{
						//print_to_log("test inside is_locked");
						$expense["locked_datetime"] = date("Y-m-d H:i:s");
						$expense["expense_notes"] = "Locked: ".$full_note.$this_expense["expense_notes"];
					}
					else
					{
						$expense["locked_datetime"] = null;
					}
				}
			}
		}
		
		
		//UPDATE EXPENSE IF NOT LOCKED
		if(empty($this_expense["locked_datetime"]))
		{
			// echo $category;
			$where = null;
			$where["id"] = $expense_id;
			//print_to_log("expense_id = ".$expense_id);
			db_update_expense($expense,$where);
		}
		
		/**
		//TEMPORARILY ENABLE CHANGES WHILE LOCKED
		$where = null;
		$where["id"] = $expense_id;
		//print_to_log("expense_id = ".$expense_id);
		db_update_expense($expense,$where);
		**/
		
		//echo $expense["recorded_datetime"] ;
		
		$this->load_expense_row($expense_id);
	}
	
	//LOAD INITIAL FORM TO MATCH PO TO EXPENSE
	function match_po()
	{
		$expense_id = $_POST["id"];
		
		//GET EXPENSE
		$where = null;
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		//GET PURCHASE ORDERS
		$where = null;
		//$where = " expense_id IS NULL ";
		$where = " account_id = ".$expense["expense_account_id"]." AND expense_id IS NULL ";
		$purchase_orders = db_select_purchase_orders($where,"expense_datetime DESC");
		
		$po_options = array();
		$po_options["Select"] = "Select";
		foreach ($purchase_orders as $po)
		{
			$date = date('m/d/y',strtotime($po["expense_datetime"]));
			$title = $date." PO".$po["id"]." $".number_format($po["expense_amount"],2);
			$po_options[$po['id']] = $title;
		}
		
		$data['expense'] = $expense;
		$data['po_options'] = $po_options;
		$this->load->view('expenses/lock_expense_div',$data);
	}
	
	function po_match_selected()
	{
		$po_action = $_POST["lock_action"];
		$expense_id = $_POST["expense_id"];
		$po_id = $_POST["po_match_id"];
		
		//GET EXPENSE
		$where = null;
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		//GET PO
		$where = null;
		$where["id"] = $po_id;
		$po = db_select_purchase_order($where);
		
		if($po_action == "Match PO")
		{
			$data['po_action'] = $po_action;
			$data['expense'] = $expense;
			$data['po'] = $po;
			$this->load->view('expenses/lock_expense_form_from_po',$data);
		}
		else if($po_action == "Skip PO" || $po_action == "Create PO")
		{
			$data['po_action'] = $po_action;
			$data['expense'] = $expense;
			$data['po'] = $po;
			$this->load->view('expenses/lock_expense_form',$data);
		}
		
	}
	
	function perform_po_action()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');

		$expense_id = $_POST["lock_expense_id"];
		$po_action = $_POST["po_action"];
		$po_id = $_POST["po_id"];
		
		//GET EXPENSE
		$where = null;
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		if($po_action == "Match PO")
		{
			//GET PO
			$where = null;
			$where["id"] = $po_id;
			$po = db_select_purchase_order($where);
			
			
			//UPDATE EXPENSE WITH PO INFO
			$update_expense = null;
			$update_expense["company_id"] = $po["owner_id"];
			$update_expense["category"] = $po["category"];
			
			//UPDATE EXPENSE NOTES 
			$text = "Expense matched to PO".$po["id"];
			$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
			$date_text = date("m/d/y H:i");
			
			$full_note = $date_text." - ".$initials." | ".$text."\n\n";
			
			$update_expense["expense_notes"] = $full_note.$expense["expense_notes"];
			
			$where = null;
			$where["id"] = $expense_id;
			db_update_expense($update_expense,$where);
			
			//UPDATE PO WITH MATCHED EXPENSE ID
			$update_po = null;
			$update_po["expense_id"] = $expense_id;
			
			$where = null;
			$where["id"] = $po_id;
			db_update_purchase_order($update_po, $where);
			
			//LOCK EXPENSE
			$this->lock_expense($expense_id);
			
		}
		else if($po_action == "Create PO")
		{
			//GET EXPENSE ISSUER PERSON
			$where = null;
			$where["id"] = $expense["issuer_id"];
			$issuer_company = db_select_company($where);
			
			$where = null;
			$where["id"] = $issuer_company["person_id"];
			$issuer_person = db_select_person($where);
			
			//CREATE NEW PO
			//MAKE ISSUER NAME TEXT
			$recorder_name = $this->session->userdata('f_name')." ".$this->session->userdata('l_name');
			
			//INSERT NEW PO
			$insert_po["issuer_id"] = $issuer_person["id"];
			$insert_po["issued_datetime"] = date("Y-m-d H:i:s");
			$insert_po["expense_datetime"] = date("Y-m-d H:i:s");
			$insert_po["po_log"] = date("m/d/y H:i")." | PO created by ".$recorder_name." from transaction | ".$expense["description"];
			$insert_po["expense_amount"] = $expense["expense_amount"];
			$insert_po["owner_id"] = $expense["company_id"];
			$insert_po["category"] = $expense["category"];
			$insert_po["account_id"] = $expense["expense_account_id"];
			db_insert_purchase_order($insert_po);
			
			//GET NEW PO
			$where = null;
			$where["issuer_id"] = $insert_po["issuer_id"];
			$where["issued_datetime"] = $insert_po["issued_datetime"];
			$new_po = db_select_purchase_order($where);
			
			
			//UPDATE EXPENSE NOTES 
			$text = "PO".$new_po["id"]." generated and approval requested";
			$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
			$date_text = date("m/d/y H:i");
			
			$full_note = $date_text." - ".$initials." | ".$text."\n\n";
			
			$update_expense["expense_notes"] = $full_note.$expense["expense_notes"];
			
			$where = null;
			$where["id"] = $expense_id;
			db_update_expense($update_expense,$where);
			
		}
		else if($po_action == "Skip PO")
		{
			//LOCK EXPENSE AS IS
			$this->lock_expense($expense_id);
		}
		
		//echo 'hello';
		$this->load_expense_row($expense_id);
	}
	
	//CALLED BY OTHER FUNCTIONS
	function lock_expense($expense_id)
	{
		//GET USER
		$user_id = $this->session->userdata('user_id');
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
			
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		//GET OWNER COMPANY
		$where = null;
		$where["id"] = $expense["company_id"];
		$owner_company = db_select_company($where);
			
		//CREATE LOCK NOTE
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		$date_text = date("m/d/y H:i");
		$full_note = $date_text." - ".$initials." | Expense Locked\n\n";
		
		$update_expense = null;
		
		//AS LONG AS OWNER HAS BEEN ASSIGNED AND CATEGORY AS BEEN ASSIGNED
		if(!empty($expense['company_id']) && !empty($expense["category"]))
		{
			//print_to_log("test inside if");
			if($user["person_id"] == $owner_company["person_id"])
			{
				$update_expense["locked_datetime"] = date("Y-m-d H:i:s");
				$update_expense["expense_notes"] = "Locked: ".$full_note.$expense["expense_notes"];
			}
			else
			{
				//GET PERMISSION
				$where = null;
				$where["id"] = $owner_company["expenses_permission_id"];
				$permission = db_select_permission($where);
			
				//IF USER HAS PERMISSION TO LOCKED NON-OWNED EXPENSES
				if(user_has_permission("lock non-owned expenses") || user_has_permission($permission["permission_name"]))
				{
					//print_to_log($is_locked);
					//IF EXPENSE IS LOCKED
					//print_to_log("test inside is_locked");
					$update_expense["locked_datetime"] = date("Y-m-d H:i:s");
					$update_expense["expense_notes"] = $full_note.$expense["expense_notes"];
				}
			}
		}
		
		//UPDATE EXPENSE IF NOT LOCKED
		$where = null;
		$where["id"] = $expense_id;
		//print_to_log("expense_id = ".$expense_id);
		db_update_expense($update_expense,$where);
	}
	
	//UNLOCK EXPENSE
	function unlock_expense()
	{
		$expense_id = $_POST["id"];
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		//RESET EXPENSE LOCKED DATETIME TO NULL
		$expense_update = null;
		$expense_update["locked_datetime"] = null;
		
		//CREATE NOTE FOR UNLOCK
		$text = "Expense unlocked";
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		date_default_timezone_set('America/Denver');
		$date_text = date("m/d/y H:i");
		$full_note = $date_text." - ".$initials." | ".$text."\n\n";
		$expense_update["expense_notes"] = $full_note.$expense["expense_notes"];
		
		//UPDATE EXPENSE IF USER HAS PERMISSION TO UNLOCK EXPENSE
		if(user_has_permission("lock non-owned expenses"))
		{
			$where = null;
			$where["id"] = $expense_id;
			db_update_expense($expense_update,$where);
		}
		
		$this->load_expense_row($expense_id);
		
	}
	
	//CHANGE EXPENSE TYPE -- THIS IS NOT LONGER USED
	function change_expense_type()
	{
		$expense_id = $_POST["id"];
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		if($expense["expense_type"] == "Expense")
		{
			$expense_type = "Transfer";
		}
		else if($expense["expense_type"] == "Transfer")
		{
			$expense_type = "Revenue";
		}
		else if($expense["expense_type"] == "Revenue")
		{
			$expense_type = "Expense";
		}
		
		
		//RESET EXPENSE LOCKED DATETIME TO NULL
		$expense_update = null;
		$expense_update["expense_type"] = $expense_type;
		
		//CREATE NOTE FOR CHANGE
		$text = "Changed to ".$expense_type;
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		date_default_timezone_set('America/Denver');
		$date_text = date("m/d/y H:i");
		$full_note = $date_text." - ".$initials." | ".$text."\n\n";
		//$expense_update["expense_notes"] = $full_note.$expense["expense_notes"];
		
		//UPDATE EXPENSE IF USER HAS PERMISSION TO UNLOCK EXPENSE
		if(user_has_permission("lock non-owned expenses"))
		{
			$where = null;
			$where["id"] = $expense_id;
			db_update_expense($expense_update,$where);
		}
		
		$this->load_expense_row($expense_id);
		
	}
	
	//GET NOTES
	function get_notes($expense_id)
	{
		$where = null;
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		$data['expense'] = $expense;
		$this->load->view('expenses/expense_notes_div',$data);
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
		$expense = db_select_expense($where);
		
		$update_expense["expense_notes"] = $full_note.$expense["expense_notes"];
		db_update_expense($update_expense,$where);
		
		$this->get_notes($expense_id);
		
		//echo $update_load["settlement_notes"];
	}
	
	//SPLIT UP EXPENSE INTO MULTIPLE EXPENSES
	function split_expense()
	{
		$expense_id = $_POST["split_expense_id"];
		
		//GET EXPENSE TO SPLIT
		$where = null;
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		//PERFORM SPLIT IF EXPENSE IS NOT LOCKED
		if(empty($expense["locked_datetime"]))
		{
		
			
			for($i=1;$i<=5;$i++)
			{
				//CREATE NOTES
				$text = $_POST["allocation_notes_$i"];
				$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
				date_default_timezone_set('America/Denver');
				$date_text = date("m/d/y H:i");
				
				$full_note = $date_text." - ".$initials." | Expense Split | ".$text."\n\n";
				
				//AS LONG AS THE AMOUNT IS NOT 0 AND ITS NOT EMPTY
				if(!($_POST["allocation_amount_$i"] == 0 || empty($_POST["allocation_amount_$i"])))
				{
					//CREATE UNALLOCATED EXPENSE
					$new_expense = null;
					$new_expense["expense_type"] = $expense["expense_type"];
					$new_expense["expense_account_id"] = $expense["expense_account_id"];
					$new_expense["issuer_id"] = $expense["issuer_id"];
					$new_expense["owner_type"] = $expense["owner_type"];
					$new_expense["company_id"] = $expense["company_id"];
					$new_expense["expense_datetime"] = $expense["expense_datetime"];
					$new_expense["category"] = $expense["category"];
					$new_expense["debit_credit"] = $expense["debit_credit"];
					$new_expense["expense_amount"] = $_POST["allocation_amount_$i"];
					$new_expense["description"] = $expense["description"]." split from original amount of $".$expense["expense_amount"];
					$new_expense["link"] = $expense["link"];
					$new_expense["expense_notes"] = $full_note.$expense["expense_notes"];
					$new_expense["guid"] = $expense["guid"];
					$new_expense["report_guid"] = $expense["report_guid"];
					
					db_insert_expense($new_expense);
				}
			}
			
			//DELETE ORIGINAL EXPENSE
			$where = null;
			$where["id"] = $expense["id"];
			db_delete_expense($where);
		}
	}

	//LOAD EXPENSE ALLOCATION DIALOG
	function load_expense_allocation_dialog()
	{
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $_POST["expense_id"];
		$expense = db_select_expense($where);
		
		//echo $expense["id"];
		
		$where = null;
		$where['expense_id'] = $expense['id'];
		$pos = db_select_purchase_orders($where);
		
		$data['pos'] = $pos;
		$data['expense'] = $expense;
		$this->load->view('expenses/expense_allocation_dialog',$data);
		
	}
	
	//ALLOCATION DIALOG -- TRANSACTION TYPE SELECTED
	function transaction_type_selected()
	{
		$transaction_type = $_POST["transaction_type_dropdown"];
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $_POST["allocated_expense_id"];
		$expense = db_select_expense($where);
		
		if($transaction_type == "Business Expense")
		{
			//GET EXPENSE ACCOUNTS FOR BUSINESS
			$where = null;
			$where = ' company_id ='.$expense["company_id"].' AND category = "'.$expense["category"].'" AND account_class = "Expense" and parent_account_id IS NOT NULL ';
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
			$data['expense'] = $expense;
			$this->load->view('expenses/business_expense_form',$data);
		}
		else if($transaction_type == "Fuel Purchase")
		{
			$data['expense'] = $expense;
			$this->load->view('expenses/fuel_purchase_form',$data);
		}
		else if($transaction_type == "Member Expense")
		{
			//GET COOP COMPANY
			$where = null;
			$where["category"] = "Coop";
			$coop_company = db_select_company($where);
			
			//GET MEMBERS
			$where = null;
			$where["business_id"] = $coop_company["id"];
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
				asort($member_options);
			}
			
			//GET OPTIONS FOR DRIVER SELECTION
			
			
			
			
			
			$data['member_options'] = $member_options;
			$data['expense'] = $expense;
			$this->load->view('expenses/me_form',$data);
			//$this->load->view('expenses/me_type_form',$data);
		}
		else if($transaction_type == "Invoice Paid")//Bill Paid
		{
			//GET LIABILITY ACCOUNTS FOR BUSINESS
			$where = null;
			//$where["company_id"] = $expense["company_id"];
			//$where["account_class"] = "Liability";
			//$where = ' company_id = '.$expense["company_id"].' AND account_class = "Liability" AND relationship_id IS NOT NULL ';
			$where = ' company_id = '.$expense["company_id"].' AND account_class = "Liability" AND parent_account_id IS NULL ';
			$accounts = db_select_accounts($where,"account_name");
			
			$payable_options = array();
			$payable_options["Select"] = "Select";
			if(!empty($accounts))
			{
				foreach($accounts as $account)
				{
					$title = $account["account_name"];
					$payable_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			
			$data['payable_options'] = $payable_options;
			$data['expense'] = $expense;
			$this->load->view('expenses/invoice_paid_account_list',$data);
		}
		else if($transaction_type == "Invoice Payment Received")
		{
			//GET ASSET ACCOUNTS FOR BUSINESS
			$where = null;
			//$where["company_id"] = $expense["company_id"];
			//$where["account_class"] = "Liability";
			//$where = ' company_id = '.$expense["company_id"].' AND account_class = "Asset" AND relationship_id IS NOT NULL AND parent_account_id IS NOT NULL ';
			$where = ' company_id = '.$expense["company_id"].' AND account_class = "Asset" AND parent_account_id IS NULL ';
			$accounts = db_select_accounts($where,"account_name");
			
			$receivable_options = array();
			$receivable_options["Select"] = "Select";
			if(!empty($accounts))
			{
				foreach($accounts as $account)
				{
					$title = $account["account_name"];
					$receivable_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			
			$data['receivable_options'] = $receivable_options;
			$data['expense'] = $expense;
			$this->load->view('expenses/invoice_payment_received_account_list',$data);
		}
		else if($transaction_type == "Load Payment Received")
		{
			//GET BILLED UNDER OPTIONS
			$where = null;
			$where["type"] = "Carrier";
			$where["company_status"] = "Active";
			$carriers = db_select_companys($where,"company_side_bar_name");
			
			$billed_under_dropdown_options = array();
			$billed_under_dropdown_options["Select"] = "Select";
			foreach($carriers as $company)
			{
				$billed_under_dropdown_options[$company["id"]] = $company["company_side_bar_name"];
			}
			
			//GET COOP COMPANY
			$where = null;
			$where["category"] = "Coop";
			$coop_company = db_select_company($where);
			
			//GET CHARGEBACK EXPENSE ACCOUNTS
			//$where = null;
			//$where["account_type"] = "Business";
			//$where["company_id"] = $dispatch_company["id"];
			//$where["account_class"] = "Expense";
			//$where = 'account_type = "Business" AND company_id = '.$dispatch_company["id"].' AND account_class = "Expense" AND parent_account_id IS NOT NULL ';
			//$chargeback_accounts = db_select_accounts($where);
			
			//GET COOP EXPENSE ACCOUNTS
			$where = null;
			$where = 'company_id = '.$coop_company["id"].' AND account_class = "Expense" AND parent_account_id IS NOT NULL ';
			$chargeback_exp_accounts = db_select_accounts($where,"account_name");
			
			$chargeback_exp_options = array();
			$chargeback_exp_options["Select"] = "Select";
			foreach($chargeback_exp_accounts as $account)
			{
				$chargeback_exp_options[$account["id"]] = $account["account_name"];
			}
			
			//GET COOP REV ACCOUNTS
			$where = null;
			$where = 'company_id = '.$coop_company["id"].' AND account_class = "Revenue" AND parent_account_id IS NOT NULL ';
			$chargeback_rev_accounts = db_select_accounts($where,"account_name");
			
			$chargeback_rev_options = array();
			$chargeback_rev_options["Select"] = "Select";
			foreach($chargeback_rev_accounts as $account)
			{
				$chargeback_rev_options[$account["id"]] = $account["account_name"];
			}
			
			//GET COOP REV ACCOUNTS
			$where = null;
			$where = 'company_id = '.$coop_company["id"].' AND account_class = "Asset" AND parent_account_id IS NOT NULL ';
			$chargeback_ar_accounts = db_select_accounts($where,"account_name");
			
			$chargeback_ar_options = array();
			$chargeback_ar_options["Select"] = "Select";
			foreach($chargeback_ar_accounts as $account)
			{
				$chargeback_ar_options[$account["id"]] = $account["account_name"];
			}
			
			$data['chargeback_ar_options'] = $chargeback_ar_options;
			$data['chargeback_rev_options'] = $chargeback_rev_options;
			$data['chargeback_exp_options'] = $chargeback_exp_options;
			$data['billed_under_dropdown_options'] = $billed_under_dropdown_options;
			$data['expense'] = $expense;
			$this->load->view('expenses/load_payment_received_form',$data);
		}
		else if($transaction_type == "Cash to Cash Transfer")
		{
			if($expense["debit_credit"] == "Credit")
			{
				$corresponding_debit_credit = "Debit";	
			}
			else
			{
				$corresponding_debit_credit = "Credit";	
			}
			
			
			
			//GET LOCKED AND UNALLOCATED EXPENSES THAT ARE CATEGORY CASH TO CASH
			$where = null;
			$where = ' company_id = '.$expense["company_id"].' AND debit_credit = "'.$corresponding_debit_credit.'" AND category = "Cash to Cash" AND locked_datetime IS NOT NULL AND recorded_datetime IS NULL AND id <> '.$expense["id"];
			$unallocated_cash_to_cash_expenses = db_select_expenses($where,"expense_datetime");
			
			$c2c_options = array();
			$c2c_options["Select"] = "Select";
			$c2c_options["No Match"] = "No Match";
			if(!empty($unallocated_cash_to_cash_expenses))
			{
				foreach($unallocated_cash_to_cash_expenses as $c2c_expense)
				{
					//GET EXPENSE ACCOUNT
					$where = null;
					$where["id"] = $c2c_expense["expense_account_id"];
					$expense_account = db_select_account($where);
					
					if($c2c_expense["debit_credit"] == "Credit")
					{
						$expense_text = number_format($c2c_expense["expense_amount"],2);	
					}
					else
					{
						$expense_text = "(".number_format($c2c_expense["expense_amount"],2).")";	
					}
					
					
					$title = date('m/d/y',strtotime($c2c_expense["expense_datetime"]))." ".$expense_account["account_name"]." ".$expense_text;
					$c2c_options[$c2c_expense["id"]] = $title;
					//echo $option;
				}
			}
			
			//GET OPTIONS FOR CORRESPONDING ACCOUNT DROP DOWN
			$where = null;
			$where = ' category = "Cash" AND parent_account_id IS NOT NULL ';
			$cash_accounts = db_select_accounts($where,"account_name");
			
			$cash_accounts_options = array();
			$cash_accounts_options["Select"] = "Select";
			foreach($cash_accounts as $account)
			{
				$title = $account["account_name"];
				$cash_accounts_options[$account['id']] = $title;
			}
			
			
			$data['cash_accounts_options'] = $cash_accounts_options;
			$data['c2c_options'] = $c2c_options;
			$data['expense'] = $expense;
			$this->load->view('expenses/cash_to_cash_form',$data);
		}
		else if($transaction_type == "Ticket Expense")
		{
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
			$data['expense'] = $expense;
			$this->load->view('expenses/ticket_expense_form',$data);
		}
		
	}
	
	function me_type_selected()
	{
		$me_type = $_POST["me_type"];
		$expense_id = $_POST["allocated_expense_id"];
		
		//GET EXPENSE
		$where = null;
		$where["id"] = $expense_id;
		$expense = db_select_expense($where);
		
		if($me_type == "BA - Non-Standard")
		{
			//GET COOP LIABILITY MEMBER ACCOUNTS
			$where = null;
			$where = ' company_id = '.$expense["company_id"].' AND account_class = "Liability" AND account_type = "Member" AND category = "Settlements Payable"';
			$payable_accounts = db_select_accounts($where);
			
			$payable_options = array();
			$payable_options["Select"] = "Select";
			if(!empty($payable_accounts))
			{
				foreach($payable_accounts as $account)
				{
					$title = $account["account_name"];
					$payable_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			$data['payable_options'] = $payable_options;
			$data['me_type'] = $me_type;
			$data['expense'] = $expense;
			$this->load->view('expenses/me_ba_ns_form',$data);
		}
		else if($me_type == "BA - Standard")
		{
			//GET COOP ASSET HOLDING ACCOUNTS
			$where = null;
			$where = ' company_id = '.$expense["company_id"].' AND account_class = "Asset" AND account_type = "Holding" ';
			$holding_accounts = db_select_accounts($where);
			
			$holding_options = array();
			$holding_options["Select"] = "Select";
			if(!empty($holding_accounts))
			{
				foreach($holding_accounts as $account)
				{
					$title = $account["account_name"];
					$holding_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			$data['holding_options'] = $holding_options;
			$data['me_type'] = $me_type;
			$data['expense'] = $expense;
			$this->load->view('expenses/me_ba_s_form',$data);
		}
		else if($me_type == "Personal Advance")
		{
			//GET COOP LIABILITY MEMBER ACCOUNTS
			$where = null;
			$where = ' company_id = '.$expense["company_id"].' AND account_class = "Liability" AND account_type = "Member" AND category = "Settlements Payable"';
			$payable_accounts = db_select_accounts($where);
			
			$payable_options = array();
			$payable_options["Select"] = "Select";
			if(!empty($payable_accounts))
			{
				foreach($payable_accounts as $account)
				{
					$title = $account["account_name"];
					$payable_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			//GET COOP REVENUE ACCOUNTS
			$where = null;
			$where = ' company_id = '.$expense["company_id"].' AND account_class = "Revenue" AND parent_account_id IS NOT NULL ';
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
			
			$data['rev_options'] = $rev_options;
			$data['payable_options'] = $payable_options;
			$data['me_type'] = $me_type;
			$data['expense'] = $expense;
			$this->load->view('expenses/me_pa_form',$data);
		}
		
		
		
		
	}
	
	function payable_account_selected()
	{
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $_POST["allocated_expense_id"];
		$expense = db_select_expense($where);
		
		//GET LIST OF ALL SUB ACCOUNT FOR PARENT ACCOUNT ID
		$where = null;
		$where["parent_account_id"] = $_POST["payable_account_id"];
		$sub_accounts = db_select_accounts($where);
		
		
		//GET LIST OF INVOICES THAT HAVE NOT BEEN CLOSED
		$where = null;
		$where = ' business_id = '.$expense["company_id"].' AND closed_datetime IS NULL AND (credit_account_id = '.$_POST["payable_account_id"];
		foreach($sub_accounts as $sub_account)
		{
			$where = $where." OR credit_account_id = ".$sub_account["id"];
		}
		$where = $where.")";
		$invoices = db_select_invoices($where,"invoice_datetime DESC");
		
		$invoice_options = array();
		$invoice_options["Select"] = "Select";
		if(!empty($invoices))
		{
			foreach($invoices as $invoice)
			{
				$title = date('m/d/y',strtotime($invoice["invoice_datetime"]))." ".$invoice["invoice_number"]." ".$invoice["invoice_category"]." $".number_format($invoice["invoice_amount"],2);
				$invoice_options[$invoice["id"]] = $title;
				//echo $option;
			}
		}
		$data['payable_account_id'] = $_POST["payable_account_id"];
		$data['invoices'] = $invoices;
		$data['expense'] = $expense;
		$data['invoice_options'] = $invoice_options;
		$this->load->view('expenses/invoice_paid_form',$data);
		
	}
	
	function receivable_account_selected()
	{
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $_POST["allocated_expense_id"];
		$expense = db_select_expense($where);
		
		// //GET LIST OF INVOICES THAT HAVE NOT BEEN CLOSED
		// $where = null;
		// $where = ' business_id = '.$expense["company_id"].' AND closed_datetime IS NULL AND debit_account_id = '.$_POST["receivable_account_id"];
		// $invoices = db_select_invoices($where,"invoice_datetime DESC");
		
		//GET LIST OF ALL SUB ACCOUNT FOR PARENT ACCOUNT ID
		$where = null;
		$where["parent_account_id"] = $_POST["receivable_account_id"];
		$sub_accounts = db_select_accounts($where);
		
		//GET LIST OF INVOICES THAT HAVE NOT BEEN CLOSED
		$where = null;
		$where = ' business_id = '.$expense["company_id"].' AND closed_datetime IS NULL AND (debit_account_id = '.$_POST["receivable_account_id"];
		foreach($sub_accounts as $sub_account)
		{
			$where = $where." OR debit_account_id = ".$sub_account["id"];
		}
		$where = $where.")";
		$invoices = db_select_invoices($where,"invoice_datetime DESC");
		
		$invoice_options = array();
		$invoice_options["Select"] = "Select";
		if(!empty($invoices))
		{
			foreach($invoices as $invoice)
			{
				$title = date('m/d/y',strtotime($invoice["invoice_datetime"]))." ".$invoice["invoice_number"]." ".$invoice["invoice_category"]." $".number_format($invoice["invoice_amount"],2);
				$invoice_options[$invoice["id"]] = $title;
				//echo $option;
			}
		}
		
		$data['receivable_account_id'] = $_POST["receivable_account_id"];
		$data['invoices'] = $invoices;
		$data['expense'] = $expense;
		$data['invoice_options'] = $invoice_options;
		$this->load->view('expenses/invoice_payment_received_form',$data);
	}
	
	function get_funded_loads()
	{
		$billing_method = $_POST["billing_method_dropdown"];
		$billed_under = $_POST["billed_under_dropdown"];
		
		//GET UNFUNDED INVOICES
		$where = null;
		$where = " amount_funded IS NOT NULL AND funded_datetime IS NULL AND billing_method = '$billing_method' AND billed_under = $billed_under";
		//$where = " billing_status_number > 3 AND funded_datetime IS NULL AND billing_method = '$billing_method' AND billed_under = $billed_under";
		$funded_invoices = db_select_loads($where);
		
		$data['funded_invoices'] = $funded_invoices;
		$this->load->view('expenses/funded_loads_div',$data);
	}
	
	//SAVE BUSINESS EXPENSE ALLOCATION
	function record_business_expense()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $_POST["allocated_expense_id"];
		$expense = db_select_expense($where);
		
		//CREDIT CASH ACCOUNT, DEBIT EXPENSE ACCOUNT
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		//$transaction = null;
		$transaction["category"] = "Business Expense";
		$transaction["description"] = $expense["description"];
		
		$entries = array();
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $expense["expense_account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $expense["expense_amount"];
		$credit_entry["entry_description"] = $expense["description"];
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $_POST["expense_account_id"];
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $expense["expense_amount"];
		$debit_entry["entry_description"] = $expense["description"];;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries);
		
		//UPDATE EXPENSE WITH ALLOCATED DATETIME
		$update_expense = null;
		$update_expense["recorded_datetime"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $expense["id"];
		db_update_expense($update_expense,$where);
		
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);
		
	}
	
	function record_fuel_purchase()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET POST DATA
		$allocated_expense_id = $_POST["allocated_expense_id"];
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $allocated_expense_id;
		$expense = db_select_expense($where);
		
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		//GET A/R FROM MEMBERS ON FUEL PAYMENTS ACCOUNT (GENERIC HOLDING)
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Fuel Payments";
		$ar_from_members_on_fuel = db_select_default_account($where);
		
		//CREDIT CASH ACCOUNT, DEBIT EXPENSE ACCOUNT
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		//$transaction = null;
		$transaction["category"] = "BA - Standard";
		$transaction["description"] = $expense["description"];
		
		$entries = array();
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $expense["expense_account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $expense["expense_amount"];
		$credit_entry["entry_description"] = "Fuel Purchase | ".$expense["description"];
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $ar_from_members_on_fuel["account_id"];
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $expense["expense_amount"];
		$debit_entry["entry_description"] = "Fuel Purchase | ".$expense["description"];
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries);
		
		//UPDATE EXPENSE WITH ALLOCATED DATETIME
		$update_expense = null;
		$update_expense["recorded_datetime"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $expense["id"];
		db_update_expense($update_expense,$where);
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);
	}
	
	function record_invoice_paid()//BILL PAID
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//echo $_POST["allocated_expense_id"];

		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $_POST["allocated_expense_id"];
		$expense = db_select_expense($where);
		
		//GET LIST OF ALL SUB ACCOUNT FOR PARENT ACCOUNT ID
		$where = null;
		$where["parent_account_id"] = $_POST["payable_account_id"];
		$sub_accounts = db_select_accounts($where);
		
		
		//GET LIST OF INVOICES THAT HAVE NOT BEEN CLOSED
		$where = null;
		$where = ' business_id = '.$expense["company_id"].' AND closed_datetime IS NULL AND (credit_account_id = '.$_POST["payable_account_id"];
		foreach($sub_accounts as $sub_account)
		{
			$where = $where." OR credit_account_id = ".$sub_account["id"];
		}
		$where = $where.")";
		$invoices = db_select_invoices($where,"invoice_datetime DESC");
		
		if(!empty($invoices))
		{
			//CREDIT CASH ACCOUNT, DEBIT A/P ACCOUNT
			//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
			//$transaction = null;
			$transaction["category"] = "Bill Paid";
			$transaction["description"] = $expense["description"];
			
			$entries = array();
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $expense["expense_account_id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = round($expense["expense_amount"],2);
			$credit_entry["entry_description"] = $expense["description"];
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			$invoice_payment_arrays = array();
			foreach($invoices as $invoice)
			{
				$invoice_id = $invoice["id"];
				
				if(isset($_POST["paid_bill_checkbox_$invoice_id"]))
				{	
					
					
					$pay_amount = round($_POST["paid_bill_amount_$invoice_id"],2);
					
					//CREATE DEBIT ENTRY
					$debit_entry = null;
					$debit_entry["account_id"] = $invoice["credit_account_id"];
					$debit_entry["recorder_id"] = $recorder_id;
					$debit_entry["recorded_datetime"] = $recorded_datetime;
					$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
					$debit_entry["debit_credit"] = "Debit";
					$debit_entry["entry_amount"] = $pay_amount;
					$debit_entry["entry_description"] = $expense["description"];;
					//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
					
					$entries[] = $debit_entry;
					
					$invoice_payment_array = null;
					$invoice_payment_array["invoice_id"] = $invoice["id"];
					$invoice_payment_array["debit_account_entry"] = $debit_entry;
					$invoice_payment_arrays[] = $invoice_payment_array;
					
				}
			}

			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			create_transaction_and_entries($transaction,$entries);
			
			//CREATE INVOICE PAYMENTS
			foreach($invoice_payment_arrays as $invoice_payment_array)
			{
				//GET PAYMENT ACCOUNT ENTRY TO ATTACH TO INVOICE
				$where = null;
				$where = $invoice_payment_array["debit_account_entry"];
				$debit_account_entry = db_select_account_entry($where);

				//ATTACH ACCOUNT ENTRY TO INVOICE AS INVOICE_PAYMENT
				$insert_invoice_payment = null;
				$insert_invoice_payment["invoice_id"] = $invoice_payment_array["invoice_id"];
				$insert_invoice_payment["account_entry_id"] = $debit_account_entry["id"];
				db_insert_invoice_payment($insert_invoice_payment);
				
				//UPDATE INVOICE STATUS (INSERT CLOSED_DATETIME IF BALANCE IS 0)
				update_invoice_status($invoice_payment_array["invoice_id"]);
			}

			//UPDATE EXPENSE WITH ALLOCATED DATETIME
			$update_expense = null;
			$update_expense["recorded_datetime"] = $recorded_datetime;
			
			$where = null;
			$where["id"] = $expense["id"];
			db_update_expense($update_expense,$where);
		}
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);

		//echo 'hello';
	}
	
	function record_invoice_payment_received()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $_POST["allocated_expense_id"];
		$expense = db_select_expense($where);
		
		//GET LIST OF ALL SUB ACCOUNT FOR PARENT ACCOUNT ID
		$where = null;
		$where["parent_account_id"] = $_POST["receivable_account_id"];
		$sub_accounts = db_select_accounts($where);
		
		//GET LIST OF INVOICES THAT HAVE NOT BEEN CLOSED
		$where = null;
		$where = ' business_id = '.$expense["company_id"].' AND closed_datetime IS NULL AND (credit_account_id = '.$_POST["receivable_account_id"];
		foreach($sub_accounts as $sub_account)
		{
			$where = $where." OR debit_account_id = ".$sub_account["id"];
		}
		$where = $where.")";
		$invoices = db_select_invoices($where,"invoice_datetime DESC");
		
		if(!empty($invoices))
		{
			//DEBIT CASH ACCOUNT, CREDIT A/P ACCOUNT
			//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
			//$transaction = null;
			$transaction["category"] = "Invoice Payment Received";
			$transaction["description"] = $expense["description"];
			
			$entries = array();
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $expense["expense_account_id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $expense["expense_amount"];
			$debit_entry["entry_description"] = $expense["description"];
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			$invoice_payment_arrays = array();
			foreach($invoices as $invoice)
			{
				$invoice_id = $invoice["id"];
				
				if(isset($_POST["paid_bill_checkbox_$invoice_id"]))
				{	
					
					
					$pay_amount = round($_POST["paid_bill_amount_$invoice_id"],2);
			
					//CREATE CREDIT ENTRY
					$credit_entry = null;
					$credit_entry["account_id"] = $invoice["debit_account_id"];
					$credit_entry["recorder_id"] = $recorder_id;
					$credit_entry["recorded_datetime"] = $recorded_datetime;
					$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
					$credit_entry["debit_credit"] = "Credit";
					$credit_entry["entry_amount"] = $pay_amount;
					$credit_entry["entry_description"] = $expense["description"];;
					//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
					
					$entries[] = $credit_entry;
					
					$invoice_payment_array = null;
					$invoice_payment_array["invoice_id"] = $invoice["id"];
					$invoice_payment_array["account_entry"] = $credit_entry;
					$invoice_payment_arrays[] = $invoice_payment_array;
				}
			}
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			create_transaction_and_entries($transaction,$entries);
			
			//CREATE INVOICE PAYMENTS
			foreach($invoice_payment_arrays as $invoice_payment_array)
			{
				//GET PAYMENT ACCOUNT ENTRY TO ATTACH TO INVOICE
				$where = null;
				$where = $invoice_payment_array["account_entry"];
				$account_entry = db_select_account_entry($where);

				//ATTACH ACCOUNT ENTRY TO INVOICE AS INVOICE_PAYMENT
				$insert_invoice_payment = null;
				$insert_invoice_payment["invoice_id"] = $invoice_payment_array["invoice_id"];
				$insert_invoice_payment["account_entry_id"] = $account_entry["id"];
				db_insert_invoice_payment($insert_invoice_payment);
				
				//UPDATE INVOICE STATUS (INSERT CLOSED_DATETIME IF BALANCE IS 0)
				update_invoice_status($invoice_payment_array["invoice_id"]);
			}
			
			//UPDATE EXPENSE WITH ALLOCATED DATETIME
			$update_expense = null;
			$update_expense["recorded_datetime"] = $recorded_datetime;
			
			$where = null;
			$where["id"] = $expense["id"];
			db_update_expense($update_expense,$where);
		}
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);
		//echo 'hello';
	}
	
	function record_cash_to_cash()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $_POST["allocated_expense_id"];
		$this_expense = db_select_expense($where);
		
		if($_POST["matching_expense"] == "No Match")
		{
			$flag = true;
			
			//GET CORRESPONDING ACCOUNT
			$where = null;
			$where["id"] = $_POST["corresponding_account"];
			$corresponding_account = db_select_account($where);
			
			//CREATE MATCHED EXPENSE FOR TRANSACTION PURPPOSES
			$matched_expense["expense_account_id"] = $corresponding_account["id"];
			$matched_expense["description"] = "Generated from the transaction tab with out a match | ".$this_expense["description"];
		}
		else
		{
			$flag = false;
			
			//GET MATCHED EXPENSE
			$where = null;
			$where["id"] = $_POST["matching_expense"];
			$matched_expense = db_select_expense($where);
		}
		
		if($this_expense["debit_credit"] == "Credit")
		{
			$debit_account = $this_expense["expense_account_id"];
			$credit_account = $matched_expense["expense_account_id"];
			$dr_desc = $this_expense["description"];
			$cr_desc = $matched_expense["description"];
		}
		else
		{
			$debit_account = $matched_expense["expense_account_id"];
			$credit_account = $this_expense["expense_account_id"];
			$dr_desc = $matched_expense["description"];
			$cr_desc = $this_expense["description"];
		}
		
		$description = "Cash to Cash | CREDIT: $cr_desc DEBIT: $dr_desc";
		
		//DEBIT CASH ACCOUNT, CREDIT CASH ACCOUNT
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		//$transaction = null;
		$transaction["category"] = "Cash to Cash";
		$transaction["description"] = $description;
		
		$entries = array();
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $debit_account;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($this_expense["expense_datetime"]));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $this_expense["expense_amount"];
		$debit_entry["entry_description"] = $dr_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $credit_account;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($this_expense["expense_datetime"]));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $this_expense["expense_amount"];
		$credit_entry["entry_description"] = $cr_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries,$flag);
		
		//UPDATE BOTH EXPENSES WITH ALLOCATED DATETIME
		$update_expense = null;
		$update_expense["recorded_datetime"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $this_expense["id"];
		db_update_expense($update_expense,$where);
		
		if($_POST["matching_expense"] != "No Match")
		{
			$where = null;
			$where["id"] = $matched_expense["id"];
			db_update_expense($update_expense,$where);
		}
		
		//RELOAD THE EXPENSE ROW
		//$this->load_expense_row($expense["id"]);
		echo 'success'; //LOAD REPORT IS CALLED IN JS UPON SUCCESS
	}
	
	function record_ba_ns_expense() //NO LONGER USED - USE RECORD_ME_EXPENSE INSTEAD
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET POST DATA
		$allocated_expense_id = $_POST["allocated_expense_id"];
		$member_payable_account_id = $_POST["member_payable_account_id"];
		$receipt_required = $_POST["receipt_required"];
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $allocated_expense_id;
		$expense = db_select_expense($where);
		
		//CREDIT CASH ACCOUNT, DEBIT EXPENSE ACCOUNT
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		//$transaction = null;
		$transaction["category"] = "BA - Non-Standard";
		$transaction["description"] = $expense["description"];
		
		$entries = array();
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $expense["expense_account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $expense["expense_amount"];
		$credit_entry["entry_description"] = $expense["description"];
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $member_payable_account_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $expense["expense_amount"];
		$debit_entry["entry_description"] = $expense["description"];
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries);
		
		//UPDATE EXPENSE WITH ALLOCATED DATETIME
		$update_expense = null;
		$update_expense["recorded_datetime"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $expense["id"];
		db_update_expense($update_expense,$where);
		
		//GET CLIENT
		//GET PAYABLE ACCOUNT
		$where = null;
		$where["id"] = $member_payable_account_id;
		$payable_account = db_select_account($where);
		
		//GET BUSINESS RELATIONSHIP
		$where = null;
		$where["id"] = $payable_account["relationship_id"];
		$business_relationship = db_select_business_relationship($where);
		
		//GET CLIENT
		$where = null;
		$where["company_id"] = $business_relationship["related_business_id"];
		$client = db_select_client($where);
		
		if($receipt_required == "Yes")
		{
			//CREATE DESCRIPTION FOR SOURCE ACCOUNT ENTRY
			$ce_description = "Business Advance | ".$expense["description"];
			
			$is_reimburseable = "Yes";
		}
		else
		{
			//CREATE DESCRIPTION FOR SOURCE ACCOUNT ENTRY
			$ce_description = "Member Expense | ".$expense["description"];
			
			$is_reimburseable = "No";
		}
		
		
		//CREATE CLIENT EXPENSE
		$client_expense = null;
		$client_expense["expense_id"] = $expense["id"];
		$client_expense["client_id"] = $client["id"];
		$client_expense["owner_id"] =  $expense["company_id"];
		$client_expense["expense_datetime"] = $expense["expense_datetime"];
		$client_expense["category"] = $expense["category"];
		$client_expense["expense_amount"] = round($expense["expense_amount"],2);
		$client_expense["description"] = $ce_description;
		$client_expense["is_reimbursable"] = $is_reimburseable;
		//$client_expense["link"] = $entry_link;
		
		db_insert_client_expense($client_expense);
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);
	}
	
	function record_ba_s_expense() //NO LONGER USED - USE RECORD_ME_EXPENSE AND RECORD_FUEL_PURCHASE INSTEAD
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET POST DATA
		$allocated_expense_id = $_POST["allocated_expense_id"];
		$holding_account_id = $_POST["holding_account_id"];
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $allocated_expense_id;
		$expense = db_select_expense($where);
		
		//CREDIT CASH ACCOUNT, DEBIT EXPENSE ACCOUNT
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		//$transaction = null;
		$transaction["category"] = "BA - Standard";
		$transaction["description"] = $expense["description"];
		
		$entries = array();
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $expense["expense_account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $expense["expense_amount"];
		$credit_entry["entry_description"] = $expense["description"];
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $holding_account_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $expense["expense_amount"];
		$debit_entry["entry_description"] = $expense["description"];
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries);
		
		//UPDATE EXPENSE WITH ALLOCATED DATETIME
		$update_expense = null;
		$update_expense["recorded_datetime"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $expense["id"];
		db_update_expense($update_expense,$where);
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);
	}
	
	function record_personal_advance() //NO LONGER USED - USE RECORD_ME_EXPENSE INSTEAD
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET POST DATA
		$allocated_expense_id = $_POST["allocated_expense_id"];
		$member_payable_account_id = $_POST["member_payable_account_id"];
		$rev_account_id = $_POST["rev_account_id"];
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $allocated_expense_id;
		$expense = db_select_expense($where);
		
		
		
		//CREDIT CASH ACCOUNT, DEBIT MEMBER PAYABLE ACCOUNT
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		//$transaction = null;
		$transaction["category"] = "PA Fee";
		$transaction["description"] = $expense["description"];
		
		$entries = array();
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $expense["expense_account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $expense["expense_amount"];
		$credit_entry["entry_description"] = $expense["description"];
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $member_payable_account_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $expense["expense_amount"];
		$debit_entry["entry_description"] = $expense["description"];
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries);

		
		
		//CREDIT REVENUE ACCOUNT, DEBIT MEMBER PAYABLE ACCOUNT
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		//$transaction = null;
		$transaction["category"] = "BA - Non-Standard";
		$transaction["description"] = $expense["description"];
		
		$fee_amount = round($expense["expense_amount"] * .1,2);
		
		$entries = array();
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $rev_account_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $fee_amount;
		$credit_entry["entry_description"] = "PA Fee | ".$expense["description"];
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $member_payable_account_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $fee_amount;
		$debit_entry["entry_description"] = "PA Fee | ".$expense["description"];
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries);
		
		
		
		//UPDATE EXPENSE WITH ALLOCATED DATETIME
		$update_expense = null;
		$update_expense["recorded_datetime"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $expense["id"];
		db_update_expense($update_expense,$where);



		//GET CLIENT
		//GET PAYABLE ACCOUNT
		$where = null;
		$where["id"] = $member_payable_account_id;
		$payable_account = db_select_account($where);
		
		//GET BUSINESS RELATIONSHIP
		$where = null;
		$where["id"] = $payable_account["relationship_id"];
		$business_relationship = db_select_business_relationship($where);
		
		//GET CLIENT
		$where = null;
		$where["company_id"] = $business_relationship["related_business_id"];
		$client = db_select_client($where);
		
		//CREATE DESCRIPTION FOR SOURCE ACCOUNT ENTRY
		$ce_description = "Personal Advance | ".$expense["description"];
		$is_reimburseable = "No";
		
		//CREATE CLIENT EXPENSE
		$client_expense = null;
		$client_expense["expense_id"] = $expense["id"];
		$client_expense["client_id"] = $client["id"];
		$client_expense["owner_id"] =  $expense["company_id"];
		$client_expense["expense_datetime"] = $expense["expense_datetime"];
		$client_expense["category"] = $expense["category"];
		$client_expense["expense_amount"] = round($expense["expense_amount"] + 1.5 + ($expense["expense_amount"]*.1),2);
		$client_expense["description"] = $ce_description;
		$client_expense["is_reimbursable"] = $is_reimburseable;
		//$client_expense["link"] = $entry_link;
		
		db_insert_client_expense($client_expense);
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);
	}
	
	function record_me_expense()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET POST DATA
		$member_relationship_id = $_POST["member_relationship_id"];
		$payment_method = $_POST["payment_method"];
		$allocated_expense_id = $_POST["allocated_expense_id"];
		
		//$member_payable_account_id = $_POST["member_payable_account_id"];
		//$rev_account_id = $_POST["rev_account_id"];
		
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		//GET RELATIONSHIP BETWEEN COOP AND MEMBER
		$where = null;
		$where["id"] = $member_relationship_id;
		$coop_member_relationship = db_select_business_relationship($where);
		
		//GET MEMBER COMPANY
		$where = null;
		$where["id"] = $coop_member_relationship["related_business_id"];
		$member_company = db_select_company($where);
		
		//GET CLIENT
		$where = null;
		$where["company_id"] = $member_company["id"];
		$client = db_select_client($where);
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $allocated_expense_id;
		$expense = db_select_expense($where);
		
		//GET A/P TO MEMBER ON SETTLEMENT DEFAULT ACCOUNT
		$where = null;
		$where["company_id"] = $member_company["id"];
		$where["category"] = "Coop A/P to Member on Settlements";
		$ap_to_member_on_settlement_default_account = db_select_default_account($where);
		
		if($payment_method == "Personal Advance")
		{
			//GET COOP PA FEE REVENUE DEFAULT ACCOUNT
			$where = null;
			$where["company_id"] = $coop_company["id"];
			$where["category"] = "Revenue on PA Fees";
			$pa_fee_rev_default_account = db_select_default_account($where);
			
			//CREDIT CASH ACCOUNT, DEBIT MEMBER PAYABLE ACCOUNT
			//CREATE TRANSACTION
			$transaction = null;
			$transaction["category"] = "Personal Advance";
			$transaction["description"] = $expense["description"];
			
			$entries = array();
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $expense["expense_account_id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $expense["expense_amount"];
			$credit_entry["entry_description"] = $expense["description"];
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $ap_to_member_on_settlement_default_account["account_id"];//A/P TO MEMBER ON SETTLEMENT ACCOUNT
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $expense["expense_amount"];
			$debit_entry["entry_description"] = $expense["description"];
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			
			//CREDIT REVENUE ACCOUNT, DEBIT MEMBER PAYABLE ACCOUNT
			
			//GET PA FEE AMOUNT FROM SETTINGS
			$where = null;
			$where["setting_name"] = "PA Fee";
			$pa_fee_setting = db_select_setting($where);
			
			$pa_fee_percentage = $pa_fee_setting["setting_value"];
			
			//CALCULATE FEE AS $1.50 PLUS PERCENTAGE FEE
			$fee_amount = round(1.50 + ($expense["expense_amount"] * $pa_fee_percentage),2);
			
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $pa_fee_rev_default_account["account_id"];//COOP PA FEE REVENUE ACCOUNT
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $fee_amount;
			$credit_entry["entry_description"] = "PA Fee | ".$expense["description"];
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $ap_to_member_on_settlement_default_account["account_id"];//A/P TO MEMBER ON SETTLEMENT ACCOUNT
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $fee_amount;
			$debit_entry["entry_description"] = "PA Fee | ".$expense["description"];
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			$new_transaction = create_transaction_and_entries($transaction,$entries);
			
			
			//UPDATE EXPENSE WITH ALLOCATED DATETIME
			$update_expense = null;
			$update_expense["recorded_datetime"] = $recorded_datetime;
			
			$where = null;
			$where["id"] = $expense["id"];
			db_update_expense($update_expense,$where);
			
			//CREATE DESCRIPTION FOR SOURCE ACCOUNT ENTRY
			$ce_description = "Personal Advance | ".$expense["description"];
			$is_reimburseable = "No";
			
			//CREATE CLIENT EXPENSE
			$client_expense = null;
			$client_expense["expense_id"] = $expense["id"];
			$client_expense["client_id"] = $client["id"];
			$client_expense["owner_id"] =  $expense["company_id"];
			$client_expense["expense_datetime"] = $expense["expense_datetime"];
			$client_expense["category"] = $expense["category"];
			$client_expense["expense_amount"] = round($expense["expense_amount"] + $fee_amount,2);
			$client_expense["description"] = $ce_description;
			$client_expense["is_reimbursable"] = $is_reimburseable;
			$client_expense["transaction_id"] = $new_transaction["id"];
			//$client_expense["link"] = $entry_link;
			
			db_insert_client_expense($client_expense);
		}
		else if($payment_method == "FleetProtect")
		{
			//CREATE TRANSACTION
			$transaction = null;
			$transaction["category"] = "FleetProtect Expense";
			$transaction["description"] = $expense["description"];
			
			$entries = array();
			
			//GET MEMBER FP ACCOUNT
			$where = null;
			$where["company_id"] = $member_company["id"];
			$where["category"] = "Coop A/R on FleetProtect";
			$ar_from_member_fp_default_account = db_select_default_account($where);
			
			//CREDIT CASH ACCOUNT [FULL AMOUNT], DEBIT A/R FROM MEMBER ON FP
			
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $expense["expense_account_id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $expense["expense_amount"];
			$credit_entry["entry_description"] = $expense["description"];
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $ar_from_member_fp_default_account["account_id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $expense["expense_amount"];
			$debit_entry["entry_description"] = $expense["description"];
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//DETERMINE FP DEDUCTIBLE AMOUNT
			$where = null;
			$where["setting_name"] = "FleetProtect Deductible";
			$fp_deductible_setting = db_select_setting($where);
			
			$deductible_amount = $fp_deductible_setting["setting_value"];
			
			if($expense["expense_amount"] < $deductible_amount)
			{
				$deductible_amount = $expense["expense_amount"];
			}
			
			//CREDIT A/R FROM MEMBER ON FP [DEDUCTIBLE AMOUNT], DEBIT A/P TO MEMBER ON SETTLEMENT [DEDUCTIBLE AMOUNT]
			
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $ar_from_member_fp_default_account["account_id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $deductible_amount;
			$credit_entry["entry_description"] = "FP Deductible on Member Expense | $".number_format($expense["expense_amount"],2)." - ".$expense["description"];
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $ap_to_member_on_settlement_default_account["account_id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $deductible_amount;
			$debit_entry["entry_description"] = "FP Deductible on Member Expense | $".number_format($expense["expense_amount"],2)." - ".$expense["description"];
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			$new_transaction = create_transaction_and_entries($transaction,$entries);
			
			
			//CREATE CLIENT EXPENSE [DEDUCTIBLE AMOUNT]
			
			//INSERT CLIENT EXPENSE
			$client_expense = null;
			//$client_expense["expense_id"] = $expense["id"];
			$client_expense["client_id"] = $client["id"];
			$client_expense["owner_id"] =  $coop_company["id"];
			$client_expense["expense_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$client_expense["category"] = $expense["category"];
			$client_expense["expense_amount"] = $deductible_amount;
			$client_expense["description"] = "FP Deductible on Member Expense | $".number_format($expense["expense_amount"],2)." - ".$expense["description"];
			$client_expense["is_reimbursable"] = "No";
			$client_expense["transaction_id"] = $new_transaction["id"];
			//$client_expense["file_guid"] = $contract_secure_file["file_guid"];
			
			db_insert_client_expense($client_expense);
		}
		else if($payment_method == "Next Settlement")
		{
			//CREDIT CASH ACOUNT [FULL AMOUNT], DEBIT A/P TO MEMBER ON SETTLEMENT
			
			//CREATE TRANSACTION
			$transaction = null;
			$transaction["category"] = "FleetProtect Expense";
			$transaction["description"] = $expense["description"];
			
			$entries = array();
			
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $expense["expense_account_id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $expense["expense_amount"];
			$credit_entry["entry_description"] = $expense["description"];
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $ap_to_member_on_settlement_default_account["account_id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $expense["expense_amount"];
			$debit_entry["entry_description"] = $expense["description"];
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			$new_transaction = create_transaction_and_entries($transaction,$entries);
			
			//CREATE CLIENT EXPENSE [FULL AMOUNT]
			
			//INSERT CLIENT EXPENSE
			$client_expense = null;
			//$client_expense["expense_id"] = $expense["id"];
			$client_expense["client_id"] = $client["id"];
			$client_expense["owner_id"] =  $coop_company["id"];
			$client_expense["expense_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$client_expense["category"] = $expense["category"];
			$client_expense["expense_amount"] = $expense["expense_amount"];
			$client_expense["description"] = "Member Expense | ".$expense["description"];
			$client_expense["is_reimbursable"] = "No";
			$client_expense["transaction_id"] = $new_transaction["id"];
			//$client_expense["file_guid"] = $contract_secure_file["file_guid"];
			
			db_insert_client_expense($client_expense);
		}
		else if($payment_method == "Receipt Required")
		{
			//CREDIT CASH ACOUNT [FULL AMOUNT], DEBIT A/P TO MEMBER ON SETTLEMENT
			
			//CREATE TRANSACTION
			$transaction = null;
			$transaction["category"] = "FleetProtect Expense";
			$transaction["description"] = $expense["description"];
			
			$entries = array();
			
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $expense["expense_account_id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $expense["expense_amount"];
			$credit_entry["entry_description"] = $expense["description"];
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $ap_to_member_on_settlement_default_account["account_id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $expense["expense_amount"];
			$debit_entry["entry_description"] = $expense["description"];
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			$new_transaction = create_transaction_and_entries($transaction,$entries);
			
			//CREATE CLIENT EXPENSE [FULL AMOUNT]
			
			//INSERT CLIENT EXPENSE
			$client_expense = null;
			//$client_expense["expense_id"] = $expense["id"];
			$client_expense["client_id"] = $client["id"];
			$client_expense["owner_id"] =  $coop_company["id"];
			$client_expense["expense_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
			$client_expense["category"] = $expense["category"];
			$client_expense["expense_amount"] = $expense["expense_amount"];
			$client_expense["description"] = "Member Expense | ".$expense["description"];
			$client_expense["is_reimbursable"] = "Yes";
			$client_expense["transaction_id"] = $new_transaction["id"];
			//$client_expense["file_guid"] = $contract_secure_file["file_guid"];
			
			db_insert_client_expense($client_expense);
		}
		
		//UPDATE EXPENSE WITH ALLOCATED DATETIME
		$update_expense = null;
		$update_expense["recorded_datetime"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $expense["id"];
		db_update_expense($update_expense,$where);
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);
	}
	
	function record_load_payment_received()//FREIGHT PAYMENT RECEIVED
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET POST DATA
		$allocated_expense_id = $_POST["allocated_expense_id"];
		$billing_method = $_POST["billing_method_dropdown"];
		$billed_under = $_POST["billed_under_dropdown"]; //client_id
		$gross_pay = $_POST["funded_amount"];
		$total_deduction_amount = $_POST["total_deduction_amount"];
		$total_reimbursement_amount = $_POST["total_reimbursement_amount"];
		
		//error_log("text about error: | LINE ".__LINE__." ".__FILE__);
		
		//GET EXPENSE
		$where = null;
		$where["id"] = $allocated_expense_id;
		$expense = db_select_expense($where);

		$load_datetime = $expense["expense_datetime"];
		
		$cash_account_id = $expense["expense_account_id"];
		
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		//GET DEFAULT FACTORING EXPENSE ACCOUNT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "Factoring Expense";
		$factoring_exp_default_acc = db_select_default_account($where);
		
		//GET DISPATCH COMPANY
		$where = null;
		$where["category"] = "Dispatch";
		$dispatch_company = db_select_company($where);
		
		//GET RELATIONSHIP BETWEEN DISPATCH AND COOP
		$where = null;
		$where["business_id"] = $coop_company["id"];
		$where["related_business_id"] = $dispatch_company["id"];
		$where["relationship"] = "Customer";
		$coop_dispatch_relationship = db_select_business_relationship($where);
		
		//GET UNFUNDED INVOICES - WHERE NEEDS TO MATCH WITH THE LOADS PULLED IN THE get_funded_loads() FUNCTION. BASED ON MISSING FUNDED DATETIME AND EXISTING AMOUNT FUNDED FROM BILLING TAB
		$where = null;
		$where = " amount_funded IS NOT NULL AND funded_datetime IS NULL AND billing_method = '$billing_method' AND billed_under = $billed_under";
		//$where = " billing_status_number > 3 AND funded_datetime IS NULL AND billing_method = '$billing_method' AND billed_under = $billed_under";
		$funded_invoices = db_select_loads($where);
		
		//error_log("text about error: | LINE ".__LINE__." ".__FILE__);
		
		$entries = array();
		
		//FOREACH INVOICE
		foreach ($funded_invoices as $load)
		{
			//error_log("text about error: | LINE ".__LINE__." ".__FILE__);
			
			$invoice_id = $load["id"];
			//echo $invoice_id;
			
			//MARK LOAD AS FUNDED
			if(isset($_POST["invoice_checkbox_$invoice_id"]))
			{
				//error_log("text about error: | LINE ".__LINE__." ".__FILE__);
				
				//echo " is checked";
				
				//GET LOAD
				$where = null;
				$where["id"] = $invoice_id;
				$load = db_select_load($where);
				
				//UPDATE FUNDED LOADS WITH FUNDED DATE AND UPDATED FUNDED AMOUNT
				$funded_datetime = date("y-m-d h:i:s",strtotime($load_datetime));
				
				$where = null;
				$where["id"] =$load["id"];
				$update["amount_funded"] = round($_POST["invoice_amount_$invoice_id"],2);
				$update["funded_datetime"] = $funded_datetime;
				
				db_update_load($update,$where);
				
				//echo "<br>";
				
				//GET BROKER (customer)
				$where = null;
				$where["id"] = $load["broker_id"];
				$broker_customer = db_select_customer($where);
				
				//GET RELATIONSHIP BETWEEN COOP AND BROKER
				$where = null;
				$where["business_id"] = $coop_company["id"];
				$where["relationship"] = "member customer";
				$where["related_business_id"] = $broker_customer["company_id"];
				$coop_broker_relationship = db_select_business_relationship($where);
				
				//GET BROKER A/R ACCOUNT
				$where = null;
				$where["company_id"] = $broker_customer["company_id"];
				$where["category"] = "Coop A/R on Loads Hauled";
				$broker_default_ar_account = db_select_default_account($where);
				
				//echo $broker_ar_account["account_name"];
				
				//CREDIT A/R FROM BROKER ACCOUNT (ORIGINAL LOAD AMOUNT)
				$credit_entry = null;
				$credit_entry["account_id"] = $broker_default_ar_account["account_id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $recorded_datetime;
				$credit_entry["entry_datetime"] = $funded_datetime;
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = round($load["amount_funded"]+$load["financing_cost"],2);
				$credit_entry["entry_description"] = "Payment received for load ".$load["customer_load_number"];
				
				$entries[] = $credit_entry;
				
				if(round($load["amount_short_paid"],2) != 0)
				{
					//error_log("text about error: | LINE ".__LINE__." ".__FILE__);
					
					//CREDIT A/R FROM BROKER ACCOUNT (ORIGINAL LOAD AMOUNT)
					$credit_entry = null;
					$credit_entry["account_id"] = $broker_default_ar_account["account_id"];
					$credit_entry["recorder_id"] = $recorder_id;
					$credit_entry["recorded_datetime"] = $recorded_datetime;
					$credit_entry["entry_datetime"] = $funded_datetime;
					$credit_entry["debit_credit"] = "Credit";
					$credit_entry["entry_amount"] = round($load["amount_short_paid"],2);
					$credit_entry["entry_description"] = "Amount short paid for load ".$load["customer_load_number"];
					
					$entries[] = $credit_entry;

					//GET GENERIC A/P HOLDING ACCOUNT
					$where = null;
					$where["company_id"] = $coop_company["id"];
					$where["category"] = "A/P to Members on Settlements";
					$coop_default_settlement_ap_account = db_select_default_account($where);
					
					//DEBIT A/P TO MEMBERS (HOLDING) (SHORT PAY AMOUNT)
					$debit_entry = null;
					$debit_entry["account_id"] = $coop_default_settlement_ap_account["account_id"];
					$debit_entry["recorder_id"] = $recorder_id;
					$debit_entry["recorded_datetime"] = $recorded_datetime;
					$debit_entry["entry_datetime"] = $funded_datetime;
					$debit_entry["debit_credit"] = "Debit";
					$debit_entry["entry_amount"] = round($load["amount_short_paid"],2);
					$debit_entry["entry_description"] = "Amount short paid for load ".$load["customer_load_number"];
					
					$entries[] = $debit_entry;
				}
				
				if(round($load["financing_cost"],2) != 0)
				{
					//error_log("text about error: | LINE ".__LINE__." ".__FILE__);
					
					//DEBIT FINANCING EXP ACCOUNT (FINANCING COST AMOUNT)
					$debit_entry = null;
					$debit_entry["account_id"] = $factoring_exp_default_acc["account_id"];
					$debit_entry["recorder_id"] = $recorder_id;
					$debit_entry["recorded_datetime"] = $recorded_datetime;
					$debit_entry["entry_datetime"] = $funded_datetime;
					$debit_entry["debit_credit"] = "Debit";
					$debit_entry["entry_amount"] = round($load["financing_cost"],2);
					$debit_entry["entry_description"] = "Financing cost for load ".$load["customer_load_number"];
					
					$entries[] = $debit_entry;
				}
				
				
			}
		}
			
		//GET DEFAULT CHARGEBACK/DEDUCTOIN EXP ACCOUNT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "Funding Chargeback Expense";
		$chargeback_exp_default_acc = db_select_default_account($where);
		
		//GET DEFAULT CHARGEBACK GUARANTEE REVENUE ACCOUNT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "Funding Chargeback Guarantee Revenue";
		$chargeback_guarantee_rev_default_acc = db_select_default_account($where);
		
		//GET DEFAULT CHARGEBACK GUARANTEE A/R ACCOUNT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "Funding Chargeback Guarantee A/R";
		$chargeback_guarantee_ar_default_acc = db_select_default_account($where);
			
		//error_log("chargeback_exp_default_acc ".$chargeback_exp_default_acc["account_id"]." | LINE ".__LINE__." ".__FILE__);
		//error_log("chargeback_guarantee_rev_default_acc ".$chargeback_guarantee_rev_default_acc["account_id"]." | LINE ".__LINE__." ".__FILE__);
		//error_log("chargeback_guarantee_ar_default_acc ".$chargeback_guarantee_ar_default_acc["account_id"]." | LINE ".__LINE__." ".__FILE__);
		
		//FOREACH CHARGBACK/DEDUCTION
		 for($i = 1; $i <= 10; $i++)
		 {
			 //error_log("text about error: | LINE ".__LINE__." ".__FILE__);
			 
			 if(!empty($_POST["d_amount_$i"]))
			 {
				// error_log("text about error: | LINE ".__LINE__." ".__FILE__);
				 
				//DEBIT CHARGEBACK EXPENSE ACCOUNT (DEDUCTION AMOUNT)
				$debit_entry = null;
				//$debit_entry["account_id"] = $_POST["d_exp_account_$i"];
				$debit_entry["account_id"] = $chargeback_exp_default_acc["account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $recorded_datetime;
				$debit_entry["entry_datetime"] = $funded_datetime;
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = round($_POST["d_amount_$i"],2);
				$debit_entry["entry_description"] = "Chargeback/Deduction Exp | ".$_POST["d_notes_$i"];
				
				$entries[] = $debit_entry;
				
				//print_r($debit_entry);
				
				//CREATE COOP INVOICE TO ARROWHEAD
				$insert_invoice = null;
				$insert_invoice['business_id'] = $coop_company["id"];
				$insert_invoice['relationship_id'] = $coop_dispatch_relationship["id"];
				//$insert_invoice['debit_account_id'] = $_POST["d_ar_account_$i"];//should be A/R account
				$insert_invoice['debit_account_id'] = $chargeback_guarantee_ar_default_acc["account_id"];//should be A/R account
				//$insert_invoice['credit_account_id'] = $_POST["d_rev_account_$i"];
				$insert_invoice['credit_account_id'] = $chargeback_guarantee_rev_default_acc["account_id"];
				$insert_invoice['invoice_type'] = "Revenue Generated";
				$insert_invoice['invoice_description'] = "Funding Guarantee coverage | ".$_POST["d_notes_$i"];
				$insert_invoice['invoice_category'] = "Funding Guarantee";
				$insert_invoice['invoice_datetime'] = $recorded_datetime;
				$insert_invoice['invoice_amount'] = round($_POST["d_amount_$i"],2);
				$insert_invoice['invoice_created_datetime'] = $recorded_datetime;
				
				db_insert_invoice($insert_invoice);
				
				$newly_created_invoice = db_select_invoice($insert_invoice);
			
				$update_invoice = null;
				$update_invoice["invoice_number"] = "UMC - AH".$newly_created_invoice["id"];
				
				$where = null;
				$where["id"] = $newly_created_invoice["id"];
				db_update_invoice($update_invoice,$where);
				//$updated_invoice = db_select_invoice($where);
				
				
				//CREDIT REVENUE ACCOUNT
				$credit_entry = null;
				//$credit_entry["account_id"] = $_POST["d_rev_account_$i"];
				$credit_entry["account_id"] = $chargeback_guarantee_rev_default_acc["account_id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $recorded_datetime;
				$credit_entry["entry_datetime"] = $funded_datetime;
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = round($_POST["d_amount_$i"],2);
				$credit_entry["entry_description"] = "Revenue from Funding Guarantee | ".$_POST["d_notes_$i"];
				
				$entries[] = $credit_entry;
				
				
				//DEBIT A/R ON CHARGEBACK GUARANTEE
				$debit_entry = null;
				//$debit_entry["account_id"] = $_POST["d_ar_account_$i"];//should be A/R account
				$debit_entry["account_id"] = $chargeback_guarantee_ar_default_acc["account_id"];//should be A/R account
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $recorded_datetime;
				$debit_entry["entry_datetime"] = $funded_datetime;
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = round($_POST["d_amount_$i"],2);
				$debit_entry["entry_description"] = "A/R on Funding Guarantee | ".$_POST["d_notes_$i"];
				
				$entries[] = $debit_entry;
				
				
				
				//CREATE BILL HOLDING FOR ARROWHEAD
				$bill_holder = null;
				$bill_holder["invoice_id"] = $newly_created_invoice["id"];
				$bill_holder["company_id"] = $dispatch_company["id"];
				$bill_holder["from_company_id"] = $coop_company["id"];
				$bill_holder["created_datetime"] = $recorded_datetime;
				$bill_holder["bill_datetime"] = $funded_datetime;
				$bill_holder["description"] = "Bill from Coop for Funding Guarantee | ".$_POST["d_notes_$i"];
				$bill_holder["amount"] = round($_POST["d_amount_$i"],2);
				
				db_insert_bill_holder($bill_holder);
				
				//error_log("text about error: | LINE ".__LINE__." ".__FILE__);
			 }
		 }//end foreach chargeback
			
			
			
			
		//FOREACH REIMBURSEMENT	
		 for($i = 1; $i <= 10; $i++)
		 {
			 //error_log("text about error: | LINE ".__LINE__." ".__FILE__);
			 
			 if(!empty($_POST["r_amount_$i"]))
			 {
				 //error_log("text about error: | LINE ".__LINE__." ".__FILE__);
				 
				//CREDIT CHARGEBACK EXPENSE ACCOUNT (REIMBURSEMENT AMOUNT)
				$credit_entry = null;
				//$credit_entry["account_id"] = $_POST["r_exp_account_$i"];
				$credit_entry["account_id"] = $chargeback_exp_default_acc["account_id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $recorded_datetime;
				$credit_entry["entry_datetime"] = $funded_datetime;
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = round($_POST["r_amount_$i"],2);
				$credit_entry["entry_description"] = "Funding reimbursement | ".$_POST["r_notes_$i"];
				
				$entries[] = $credit_entry;
				
				//print_r($debit_entry);
				
				//CREATE COOP INVOICE TO ARROWHEAD
				$insert_invoice = null;
				$insert_invoice['business_id'] = $coop_company["id"];
				$insert_invoice['relationship_id'] = $coop_dispatch_relationship["id"];
				//$insert_invoice['debit_account_id'] = $_POST["r_ar_account_$i"];//should be A/R account
				$insert_invoice['debit_account_id'] = $chargeback_guarantee_ar_default_acc["account_id"];//should be A/R account
				//$insert_invoice['credit_account_id'] = $_POST["r_rev_account_$i"];
				$insert_invoice['credit_account_id'] = $chargeback_guarantee_rev_default_acc["account_id"];
				$insert_invoice['invoice_type'] = "Revenue Generated";
				$insert_invoice['invoice_description'] = "Funding reimbursement | ".$_POST["r_notes_$i"];
				$insert_invoice['invoice_category'] = "Funding Reimbursement";
				$insert_invoice['invoice_datetime'] = $recorded_datetime;
				$insert_invoice['invoice_amount'] = round($_POST["r_amount_$i"]*-1,2);
				$insert_invoice['invoice_created_datetime'] = $recorded_datetime;
				
				db_insert_invoice($insert_invoice);
				
				$newly_created_invoice = db_select_invoice($insert_invoice);
			
				$update_invoice = null;
				$update_invoice["invoice_number"] = "UMC - AH".$newly_created_invoice["id"];
				
				$where = null;
				$where["id"] = $newly_created_invoice["id"];
				db_update_invoice($update_invoice,$where);
				//$updated_invoice = db_select_invoice($where);
				
				
				//DEBIT REVENUE ACCOUNT
				$debit_entry = null;
				//$debit_entry["account_id"] = $_POST["r_rev_account_$i"];
				$debit_entry["account_id"] = $chargeback_guarantee_rev_default_acc["account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $recorded_datetime;
				$debit_entry["entry_datetime"] = $funded_datetime;
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = round($_POST["r_amount_$i"],2);
				$debit_entry["entry_description"] = "Credit issued for funding reimbursement | ".$_POST["r_notes_$i"];
				
				$entries[] = $debit_entry;
				
				
				//DEBIT A/R ON CHARGEBACK GUARANTEE
				$credit_entry = null;
				//$credit_entry["account_id"] = $_POST["r_ar_account_$i"];//should be A/R account
				$credit_entry["account_id"] = $chargeback_guarantee_ar_default_acc["account_id"];//should be A/R account
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $recorded_datetime;
				$credit_entry["entry_datetime"] = $funded_datetime;
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = round($_POST["r_amount_$i"],2);
				$credit_entry["entry_description"] = "A/R on Funding Guarantee | ".$_POST["r_notes_$i"];
				
				$entries[] = $credit_entry;
				
				
				
				//CREATE BILL HOLDING FOR ARROWHEAD
				$bill_holder = null;
				$bill_holder["invoice_id"] = $newly_created_invoice["id"];
				$bill_holder["company_id"] = $dispatch_company["id"];
				$bill_holder["from_company_id"] = $coop_company["id"];
				$bill_holder["created_datetime"] = $recorded_datetime;
				$bill_holder["bill_datetime"] = $funded_datetime;
				$bill_holder["description"] = "Credit from Coop for funding reimbursement| ".$_POST["r_notes_$i"];
				$bill_holder["amount"] = round($_POST["r_amount_$i"]*-1,2);
				
				db_insert_bill_holder($bill_holder);
			 }
		 }//end foreach reimbursement
		
			
		//SUBMIT THE WHOLE TRANSACTION
		
		//DEBIT CASH ACCOUNT (FULL TRANSACTION AMOUNT)
		$debit_entry = null;
		$debit_entry["account_id"] = $cash_account_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = $funded_datetime;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = round($expense["expense_amount"],2);
		$debit_entry["entry_description"] = "Freight invoices funded";
		
		$entries[] = $debit_entry;
		
		//print_r($entries);
		
		$transaction = null;
		$transaction["category"] = "Freight Funded";
		$transaction["description"] = "Freight Invoices Funded";
		
		//print_r($transaction);
		//echo "<br>";
		//print_r($entries);

		create_transaction_and_entries($transaction,$entries);
		
		//error_log("text about error: | LINE ".__LINE__." ".__FILE__);
		
		//UPDATE EXPENSE WITH ALLOCATED DATETIME
		$update_expense = null;
		$update_expense["recorded_datetime"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $expense["id"];
		db_update_expense($update_expense,$where);
		
		//echo "<br>success!!";
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);
		
		//error_log("END OF FUNCTION | LINE ".__LINE__." ".__FILE__);
	}
	
	function record_ticket_expense()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET THIS EXPENSE
		$where = null;
		$where["id"] = $_POST["allocated_expense_id"];
		$expense = db_select_expense($where);
		
		//GET TICKET
		$where = null;
		$where["id"] = $_POST["ticket_id"];
		$ticket = db_select_ticket($where);
		
		
		//CREDIT CASH ACCOUNT, DEBIT TICKET LIABILTY ACCOUNT
		
		//$transaction = null;
		$transaction["category"] = "Business Expense";
		$transaction["description"] = $expense["description"];
		
		$entries = array();
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $expense["expense_account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $expense["expense_amount"];
		$credit_entry["entry_description"] = $expense["description"];
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $ticket["balance_sheet_account_id"];
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($expense["expense_datetime"]));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $expense["expense_amount"];
		$debit_entry["entry_description"] = $expense["description"];;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries);
		
		//UPDATE EXPENSE WITH ALLOCATED DATETIME
		$update_expense = null;
		$update_expense["recorded_datetime"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $expense["id"];
		db_update_expense($update_expense,$where);
		
		
		//GET PAYMENT ACCOUNT ENTRY TO ATTACH TO INVOICE
		$where = null;
		$where = $debit_entry;
		$ticket_account_entry = db_select_account_entry($where);
		
		//CREATE NEW TICKET PAYMENT
		$ticket_payment = null;
		$ticket_payment["ticket_id"] = $ticket["id"];
		$ticket_payment["account_entry_id"] = $ticket_account_entry["id"];
		db_insert_ticket_payment($ticket_payment);
		
		
		//RELOAD THE EXPENSE ROW
		$this->load_expense_row($expense["id"]);
		
	}
	
	//UPLOAD CSV FILE ... REDIRECTS TO PROPER REPORT
	function do_upload($report)
	{
		
		//RESTRICT ACCESS ACCORDING TO PERMISSIONS
		if(!user_has_permission("create business account entries"))
		{
			//ALLOW RECORD FUNDING W/ OUT BUSINESS ACCOUNT PERMISSION
			if($entry_type != "Record Funding")
			{
				//EVERYTHING ELSE IS REDIRECTED
				redirect(base_url("index.php/accounts/index/Client/All/no_permission1/All"));
			}
		}
		
		$config = null;
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv';
		$config['max_size']	= '1000';

		$this->load->library('upload', $config);

		//IF ERRORS
		if ( ! $this->upload->do_upload())
		{
			echo $this->upload->display_errors();
		}
		else //SUCCESS
		{
			if($report == 'comdata')
			{
				$file = $this->upload->data();
				$file_name = $file["file_name"];
				$sp_account_id = $_POST["comdata_account_dropdown"];
				$this->comdata_report($file_name,$sp_account_id);
			}
			else if($report == 'sp_cash_load')
			{
				$file = $this->upload->data();
				$file_name = $file["file_name"];
				$sp_account_id = $_POST["smartpay_account_dropdown"];
				$this->smartpay_cash_load_report($file_name,$sp_account_id);
			}
			else if($report == 'spark_cc')
			{
				$file = $this->upload->data();
				$file_name = $file["file_name"];
				$this->spark_cc_report($file_name);
			}
			else if($report == 'money_code')
			{
				$file = $this->upload->data();
				$file_name = $file["file_name"];
				$sp_account_id = $_POST["mc_smartpay_account_dropdown"];
				$this->money_code_report($file_name,$sp_account_id);
			}
			else if($report == 'tab')
			{
				$file = $this->upload->data();
				$file_name = $file["file_name"];
				$sp_account_id = $_POST["tab_account_dropdown"];
				$this->tab_report($file_name,$sp_account_id);
			}
			else if($report == 'venture_cc')
			{
				$file = $this->upload->data();
				$file_name = $file["file_name"];
				$this->venture_cc_report($file_name);
			}
			
		}
	}
	
	//COMDATA REPORT CONFIRMATION
	function comdata_report($file_name,$sp_account_id)
	{
		$notes = "";
		$entries = array();
		$csv_doc = fopen("./uploads/$file_name", "r");
		$row_number = 1;
		//FOREACH ROW
		while (($row = fgetcsv($csv_doc)) !== false) 
		{
			//echo "<br>";
			//echo $row_number;
			if($row_number > 1)
			{
				$column = 1;
				//FOR EACH CELL
				foreach ($row as $cell) 
				{
					if($column == 1)
					{
						$date = htmlspecialchars($cell);
					}
					else if($column == 2)
					{
						$time = htmlspecialchars($cell);
					}
					else if($column == 3)
					{
						$type = trim(htmlspecialchars($cell));
					}
					elseif($column == 4)
					{
						$location = htmlspecialchars($cell);
					}
					elseif($column == 5)
					{
						$city = htmlspecialchars($cell);
					}
					elseif($column == 6)
					{
						$state = htmlspecialchars($cell);
					}
					else if($column == 9)
					{
						$entry_amount = round(htmlspecialchars($cell),2);
					}
					else if($column == 10)
					{
						$fee = round(htmlspecialchars($cell),2);
					}
					
					$column++;
				}//END ROW
				
				if($type == "FP TOTAL")
				{
					$description = "$type at $location in $city, $state";
				}
				else
				{
					$description = $type;
				}
					
				$entry_datetime = date("Y-m-d H:i:s",strtotime($date." ".$time));
				
				$report_is_empty = false;
				//IF REPORT IS EMPTY
				if(empty($entry_datetime))
				{
					$report_is_empty = true;
					break;
				}
				
				//DETERMINE DEBIT OR CREDIT
				//DETERMINE DEFAULTS FOR EXPENSE, TRANSFER, OR REVENUE
				$expense_type = "Expense";
				if($entry_amount > 0)
				{
					$debit_credit = "Credit";
				}
				else
				{
					$debit_credit = "Debit";
					$entry_amount = $entry_amount * -1;
				}
				
				//GET GARRETT AS ISSUER
				$where = null;
				$where["person_id"] = 1;
				$where["category"] = "Office Staff";
				$garrett_company = db_select_company($where);
				
				$entry["issuer_id"] = $garrett_company["id"];
				$entry["expense_type"] = $expense_type;
				$entry["debit_credit"] = $debit_credit;
				$entry["entry_amount"] = $entry_amount;
				$entry["account_id"] = $sp_account_id;
				$entry["entry_datetime"] = $entry_datetime;
				$entry["entry_description"] = trim("Comdata Transaction | ".$description);
				$entry["report_guid"] = null;
				
				//ADD ENTRY TO THE ARRAY
				if(!empty($entry["entry_amount"]) && ($type == "FP TOTAL" || $type == "Check" || $type == "Load"))
				{
					$entries[] = $entry;
					//db_insert_account_entry($entry);
				}
				
			}//END ROW
			
			$row_number++;
		}
		fclose($csv_doc);
		
		$where = null;
		$where["id"] = $sp_account_id;
		$account = db_select_account($where);
		
		//IF REPORT IS NOT EMPTY
		if(!$report_is_empty)
		{
			$data['entry_type'] = "Comdata Entry";
			$data['entries'] = $entries;
			$data['file_name'] = $file_name;
			$data['account'] = $account;
			$data['report_name'] = "Comdata Transaction Report";
			$data['sp_account_id'] = $sp_account_id;
			$this->load->view('expenses/accounts_transaction_table',$data);
		}
		else
		{
			$data['report_name'] = "Transaction Report";
			$data['account'] = $account;
			$this->load->view('expenses/empty_report_view',$data);
		}
	}
	
	
	//GENERATE SMARTPAY CASH LOAD REPORT CONFIRMATION
	function smartpay_cash_load_report($file_name,$sp_account_id)
	{
		//echo $file_name."<br><br>";
		$notes = "";
		$entries = array();
		$csv_doc = fopen("./uploads/$file_name", "r");
		$row_number = 1;
		//FOREACH ROW
		while (($row = fgetcsv($csv_doc)) !== false) 
		{
			//echo $row_number;
			//echo "<br>";
			if($row_number > 1)
			{
				$column = 1;
				//FOR EACH CELL
				foreach ($row as $cell) 
				{
					if($column == 1)
					{
						$card_holder_name = htmlspecialchars($cell);
					}
					elseif($column == 3)
					{
						$notes = htmlspecialchars($cell);
					}
					elseif($column == 4)
					{
						$card_number = htmlspecialchars($cell);
					}
					elseif($column == 7)
					{
						$entry["entry_datetime"] = date("Y-m-d H:i:s",strtotime(htmlspecialchars($cell)));
						$time_of_day = date("n/j H:i:s",strtotime(htmlspecialchars($cell)));
					}
					elseif($column == 8)
					{
						$entry["entry_amount"] = htmlspecialchars($cell);
					}
					elseif($column == 9)
					{
						$user_login = htmlspecialchars($cell);
					}
					
					
					$column++;
				}//END CELL
				
				$report_is_empty = false;
				//IF REPORT IS EMPTY
				if(empty($card_number))
				{
					$report_is_empty = true;
					break;
				}
				
				//GET EFS USER'S PERSON_ID
				$where = null;
				$where["login"] = $user_login;
				$corporate_login = db_select_corporate_login($where);
				
				if(!empty($corporate_login))
				{
					//GET STAFF/FM WITH THIS PERSON_ID
					$where = null;
					$where = ' person_id = '.$corporate_login["person_id"].' AND (category = "Office Staff" OR category = "Fleet Manager")';
					$company = db_select_company($where);
					
					$issuer_id = $company["id"];
				}
				else
				{
					$issuer_id = null;
				}
				
				
				$entry["issuer_id"] = $issuer_id;
				$entry["expense_type"] = "Expense";
				$entry["debit_credit"] = "Debit";
				$entry["account_id"] = $sp_account_id;
				$entry["entry_description"] = trim("Cash Load | ".$time_of_day." | $card_holder_name | $notes");
				$entry["report_guid"] = null;
				
				//ADD ENTRY TO THE ARRAY
				if(!empty($entry["entry_amount"]))
				{
					$entries[] = $entry;
					//db_insert_account_entry($entry);
				}
				
			}//END ROW
			
			$row_number++;
		}
		fclose($csv_doc);
		
		$where = null;
		$where["id"] = $sp_account_id;
		$account = db_select_account($where);
		
		//IF REPORT IS NOT EMPTY
		if(!$report_is_empty)
		{
			$data['entry_type'] = "SmartPay Expense";
			$data['entries'] = $entries;
			$data['file_name'] = $file_name;
			$data['account'] = $account;
			$data['report_name'] = "SmartPay Cash Load Report";
			$data['sp_account_id'] = $sp_account_id;
			$this->load->view('expenses/accounts_transaction_table',$data);
		}
		else
		{
			$data['report_name'] = "SmartPay Cash Load Report";
			$data['account'] = $account;
			$this->load->view('expenses/empty_report_view',$data);
		}
	}
	
	//GENERATE MONEY CODE REPORT CONFIRMATION
	function money_code_report($file_name,$sp_account_id)
	{
		$notes = "";
		$entries = array();
		$csv_doc = fopen("./uploads/$file_name", "r");
		$row_number = 1;
		//FOREACH ROW
		while (($row = fgetcsv($csv_doc)) !== false) 
		{
			//echo $row_number;
			//echo "<br>";
			if($row_number > 1)
			{
				$column = 1;
				//FOR EACH CELL
				foreach ($row as $cell) 
				{
					if($column == 1)
					{
						$ref_number = htmlspecialchars($cell);
					}
					else if($column == 9)
					{
						$issused_by = htmlspecialchars($cell);
					}
					elseif($column == 8)
					{
						$issused_to = htmlspecialchars($cell);
					}
					elseif($column == 11)
					{
						$entry["entry_datetime"] = date("Y-m-d H:i:s",strtotime(htmlspecialchars($cell)));
					}
					elseif($column == 6)
					{
						$fee = round(htmlspecialchars($cell),2);
					}
					elseif($column == 3)
					{
						$check_amount = round(htmlspecialchars($cell),2);
						$code_amount = round(htmlspecialchars($cell),2);
					}
					elseif($column == 13)
					{
						$notes = htmlspecialchars($cell);
					}
					elseif($column == 2)
					{
						$check_number = htmlspecialchars($cell);
					}
					
					$column++;
				}//END CELL
				
				$report_is_empty = false;
				//IF REPORT IS EMPTY
				if(empty($ref_number))
				{
					$report_is_empty = true;
					break;
				}
				
				$issuer_company_id = null;
				//echo $issused_by;
				$where = null;
				$where["system_name"] = "EFS OO";
				$where["login"] = $issused_by;
				$corporate_login = db_select_corporate_login($where);
				
				if(!empty($corporate_login))
				{
					$where = null;
					$where = ' person_id = '.$corporate_login["person_id"].' AND (category = "Office Staff" OR category = "Fleet Manager")';
					$issuer_company = db_select_company($where);
					
					if(!empty($issuer_company))
					{
						$issuer_company_id = $issuer_company["id"];
					}
				}
				
				
				
				$entry["issuer_id"] = $issuer_company_id;//company_id
				$entry["expense_type"] = "Expense";
				$entry["debit_credit"] = "Debit";
				$entry["entry_amount"] = $check_amount + $fee;
				$entry["account_id"] = $sp_account_id;
				$fee = number_format($fee,2);
				$entry["entry_description"] = trim("Money Code $issused_by $$code_amount | $ref_number $check_number | $issused_to | $notes + $$fee fee");
				$entry["report_guid"] = null;
				
				//ADD ENTRY TO THE ARRAY
				if(!empty($entry["entry_amount"]))
				{
					$entries[] = $entry;
					//db_insert_account_entry($entry);
				}
				
			}//END ROW
			
			$row_number++;
		}
		fclose($csv_doc);
		
		$where = null;
		$where["id"] = $sp_account_id;
		$account = db_select_account($where);
		
		//IF REPORT IS NOT EMPTY
		if(!$report_is_empty)
		{
			$data['entry_type'] = "SmartPay Expense";
			$data['entries'] = $entries;
			$data['file_name'] = $file_name;
			$data['account'] = $account;
			$data['report_name'] = "Money Code Use Report";
			$data['sp_account_id'] = $sp_account_id;
			$this->load->view('expenses/accounts_transaction_table',$data);
		}
		else
		{
			$data['report_name'] = "Money Code Use Report";
			$data['account'] = $account;
			$this->load->view('expenses/empty_report_view',$data);
		}
	}
	
	//GENERATE SPARK CC REPORT CONFIRMATION
	function spark_cc_report($file_name)
	{
		$notes = "";
		$entries = array();
		$csv_doc = fopen("./uploads/$file_name", "r");
		$row_number = 1;
		//FOREACH ROW
		while (($row = fgetcsv($csv_doc)) !== false) 
		{
			//echo $row_number;
			//echo "<br>";
			if($row_number > 1)
			{
				$column = 1;
				//FOR EACH CELL
				foreach ($row as $cell) 
				{
					if($column == 1)
					{
						$entry["entry_datetime"] = date("Y-m-d H:i:s",strtotime(htmlspecialchars($cell)));
					}
					elseif($column == 2)
					{
						$card_number = htmlspecialchars($cell);
					}
					elseif($column == 3)
					{
						$notes = trim(htmlspecialchars($cell));
					}
					elseif($column == 4)
					{
						$debit = round(htmlspecialchars($cell),2);
					}
					elseif($column == 5)
					{
						$credit = round(htmlspecialchars($cell),2);
					}
					
					
					$column++;
				}//END CELL
				
				//DETERMINE ACCOUNT ID
				$where = null;
				$where["id"] = $_POST["account_dropdown"];
				$account = db_select_account($where);
				
				if(!empty($account))
				{
					$entry["account_id"] = $account["id"]; //THIS IS THE SPARK ALLOCATION ACCOUNT
				}
				
				//DETERMINE DEBIT OR CREDIT
				//DETERMINE DEFAULTS FOR EXPENSE, TRANSFER, OR REVENUE
				$expense_type = "Expense";
				$entry_amount = null;
				if(!empty($credit))
				{
					$debit_credit = "Credit";
					$entry_amount = $credit;
				}
				else if(!empty($debit))
				{
					$debit_credit = "Debit";
					$entry_amount = $debit;
				}
				
				//ADD ENTRY TO THE ARRAY
				if(!empty($entry_amount))
				{
					//DETERMINE ISSUER
					//GET CORPORATE CARD
					$where = null;
					$where["last_four"] = $card_number;
					$where["account_id"] = $account["id"];
					$card = db_select_corporate_card($where);
					error_log($card_number." | LINE ".__LINE__." ".__FILE__);
					if(!empty($card))
					{
						//GET STAFF/FM WITH THIS PERSON_ID
						$where = null;
						$where = ' person_id = '.$card["person_id"].' AND (category = "Office Staff" OR category = "Fleet Manager")';
						$company = db_select_company($where);
						
						$issuer_id = $company["id"];
					}
					else
					{
						$issuer_id = null;
					}
					
					$entry["issuer_id"] = $issuer_id;
					$entry["expense_type"] = $expense_type;
					$entry["debit_credit"] = $debit_credit;
					$entry["entry_amount"] = $entry_amount;
					$entry["entry_description"] = "Spark CC | $card_number | $notes";
					$entry["report_guid"] = null;
				
					$entries[] = $entry;
					$entry = null;
					//db_insert_account_entry($entry);
				}
				
			}//END ROW
			
			$row_number++;
		}
		fclose($csv_doc);
		
		//$account["account_name"] = "Spark CC Accounts";
		
		$data['entry_type'] = "Spark CC Expense";
		$data['account'] = $account;
		$data['entries'] = $entries;
		$data['file_name'] = $file_name;
		$data['report_name'] = "Spark CC Report";
		$this->load->view('expenses/accounts_transaction_table',$data);
	}
	
	//GENERATE TAB BANK REPORT CONFIRMATION
	function tab_report($file_name,$sp_account_id)
	{
		
		$where = null;
		$where["id"] = $sp_account_id;
		$account = db_select_account($where);
		
		$notes = "";
		$entries = array();
		$csv_doc = fopen("./uploads/$file_name", "r");
		$row_number = 1;
		//FOREACH ROW
		while (($row = fgetcsv($csv_doc)) !== false) 
		{
			//echo $row_number;
			//echo "<br>";
			if($row_number > 1)
			{
				$column = 1;
				//FOR EACH CELL
				foreach ($row as $cell) 
				{
					if($column == 1)
					{
						$entry_datetime = date("Y-m-d H:i:s",strtotime(htmlspecialchars($cell)));
					}
					else if($column == 4)
					{
						$entry_amount = round(htmlspecialchars($cell),2);
					}
					elseif($column == 3)
					{
						$description = htmlspecialchars($cell);
					}
					
					$column++;
				}//END CELL
				
				$report_is_empty = false;
				//IF REPORT IS EMPTY
				if(empty($entry_datetime))
				{
					$report_is_empty = true;
					break;
				}
				
				//DETERMINE DEBIT OR CREDIT
				//DETERMINE DEFAULTS FOR EXPENSE, TRANSFER, OR REVENUE
				$expense_type = "Expense";
				if($entry_amount > 0)
				{
					$debit_credit = "Credit";
				}
				else
				{
					$debit_credit = "Debit";
					$entry_amount = $entry_amount * -1;
				}
				
				//NEED TO DEFAULT TO GARRETT AS ISSUER - NEED TO FIGURE OUT HOW TO DESIGNATE A DEFAULT USER
				//GET GARRETT AS ISSUER
				$where = null;
				$where["person_id"] = 1;
				$where["category"] = "Office Staff";
				$garrett_company = db_select_company($where);
				
				$entry["issuer_id"] = $garrett_company["id"];
				$entry["expense_type"] = $expense_type;
				$entry["debit_credit"] = $debit_credit;
				$entry["entry_amount"] = $entry_amount;
				$entry["account_id"] = $sp_account_id;
				$entry["entry_datetime"] = $entry_datetime;
				$entry["entry_description"] = trim($account["account_name"]." | ".$description);
				$entry["report_guid"] = null;
				
				//ADD ENTRY TO THE ARRAY
				if(!empty($entry["entry_amount"]))
				{
					$entries[] = $entry;
					//db_insert_account_entry($entry);
				}
				
			}//END ROW
			
			$row_number++;
		}
		fclose($csv_doc);
		
		
		//IF REPORT IS NOT EMPTY
		if(!$report_is_empty)
		{
			$data['entry_type'] = "TAB Entry";
			$data['entries'] = $entries;
			$data['file_name'] = $file_name;
			$data['account'] = $account;
			$data['report_name'] = "Transaction Report";
			$data['sp_account_id'] = $sp_account_id;
			$this->load->view('expenses/accounts_transaction_table',$data);
		}
		else
		{
			$data['report_name'] = "TAB Transaction Report";
			$data['account'] = $account;
			$this->load->view('expenses/empty_report_view',$data);
		}
	}
	
	//GENERATE VENTURE CC REPORT CONFIRMATION
	function venture_cc_report($file_name)
	{
		$notes = "";
		$entries = array();
		$csv_doc = fopen("./uploads/$file_name", "r");
		$row_number = 1;
		//FOREACH ROW
		while (($row = fgetcsv($csv_doc)) !== false) 
		{
			//echo $row_number;
			//echo "<br>";
			if($row_number > 1)
			{
				$column = 1;
				//FOR EACH CELL
				foreach ($row as $cell) 
				{
					if($column == 1)
					{
						$entry["entry_datetime"] = date("Y-m-d H:i:s",strtotime(htmlspecialchars($cell)));
					}
					elseif($column == 2)
					{
						$card_number = htmlspecialchars($cell);
					}
					elseif($column == 3)
					{
						$notes = trim(htmlspecialchars($cell));
					}
					elseif($column == 4)
					{
						$debit = round(htmlspecialchars($cell),2);
					}
					elseif($column == 5)
					{
						$credit = round(htmlspecialchars($cell),2);
					}
					
					
					$column++;
				}//END CELL
				
				//DETERMINE ACCOUNT ID
				$where = null;
				$where["id"] = $_POST["account_dropdown"];
				$account = db_select_account($where);
				
				if(!empty($account))
				{
					$entry["account_id"] = $account["id"]; //THIS IS THE SPARK ALLOCATION ACCOUNT
				}
				
				//DETERMINE DEBIT OR CREDIT
				$expense_type = "Expense";
				if(!empty($credit))
				{
					$debit_credit = "Credit";
					$entry_amount = $credit;
				}
				else if(!empty($debit))
				{
					$debit_credit = "Debit";
					$entry_amount = $debit;
				}
				
				//ADD ENTRY TO THE ARRAY
				if(!empty($entry_amount))
				{
					//DETERMINE ISSUER
					//GET CORPORATE CARD
					$where = null;
					$where["last_four"] = $card_number;
					$where["account_id"] = $account["id"];
					$card = db_select_corporate_card($where);
					// error_log($card_number." | LINE ".__LINE__." ".__FILE__);
					if(!empty($card))
					{
						//GET STAFF/FM WITH THIS PERSON_ID
						$where = null;
						$where = ' person_id = '.$card["person_id"].' AND (category = "Office Staff" OR category = "Fleet Manager")';
						$company = db_select_company($where);
						
						$issuer_id = $company["id"];
					}
					else
					{
						$issuer_id = null;
					}
					
					$entry["issuer_id"] = $issuer_id;
					$entry["expense_type"] = $expense_type;
					$entry["debit_credit"] = $debit_credit;
					$entry["entry_amount"] = $entry_amount;
					$entry["entry_description"] = "Venture CC | $card_number | $notes";
					$entry["report_guid"] = null;
				
					$entries[] = $entry;
					$entry = null;
					//db_insert_account_entry($entry);
				}
				
			}//END ROW
			
			$row_number++;
		}
		fclose($csv_doc);
		
		//$account["account_name"] = $venture_account["account_name"];
		
		$data['entry_type'] = "Venture CC Expense";
		$data['account'] = $account;
		$data['entries'] = $entries;
		$data['file_name'] = $file_name;
		$data['report_name'] = "Venture CC Report";
		$this->load->view('expenses/accounts_transaction_table',$data);
	}
	
	
	//ADD TRANSACTIONS TO DB -- THIS IS FOR ALL THE DIFFERENT REPORTS
	function add_smartpay_transactions()
	{
		$report_guid = get_random_string(10);
		
		$number_of_trans = $_POST["number_of_trans"];
		
		$recorder_id = $this->session->userdata('person_id');
		date_default_timezone_set('America/Denver');
		$entry_datetime = date("n/d H:i");
		$account_id = "All";
		
		//GET ALL TRANSACTIONS
		for($i = 1; $i <= $number_of_trans; $i++)
		{
			//INSERT THE ACOUNT ENTRY
			//db_insert_account_entry($entry);
		
			//GET ACCOUNT
			$where = null;
			$where["id"] = $_POST["account_id_$i"];
			$account = db_select_account($where);
			
			//CREATE UNALLOCATED EXPENSE
			$expense = null;
			$expense["expense_type"] = $_POST["expense_type_$i"];
			$expense["expense_account_id"] = $_POST["account_id_$i"];
			$expense["company_id"] = $account["company_id"];
			$expense["issuer_id"] = $_POST["expense_issuer_$i"]; //PULL FROM THE ISSUER ID IN THE ACCOUNTS TRANS TABLE - HOW TO HANDLE NULL?
			$expense["expense_datetime"] = $_POST["entry_datetime_$i"];
			$expense["debit_credit"] = $_POST["entry_debit_credit_$i"];
			$expense["expense_amount"] = $_POST["entry_amount_$i"];
			$expense["description"] = $_POST["entry_description_$i"]." | uploaded $entry_datetime";
			$expense["link"] = $_POST["entry_link_$i"];
			$expense["guid"] = $_POST["entry_guid_$i"];
			$expense["report_guid"] = $report_guid;
			
			db_insert_expense($expense);
		}
			
		$company_id = "none";
		
		redirect(base_url("index.php/expenses"));
	}
	
	
	
	
	
	
	
	
	
	
	
	//AJAX LOAD PO ATTACHMENT DIALOG DIV
	function load_po_file_upload()
	{
		$po_id = $_POST["po_id"];
		
		$data = null;
		$data["po_id"] = $po_id;
		$this->load->view('expenses/po_attachment_div',$data);
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
	
	
	
	
	
}
