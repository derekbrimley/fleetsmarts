<?php		
class Accounts extends MY_Controller 
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
		$business_users_options["Select"] = "Select";
		foreach($business_users as $company)
		{
			$title = $company["company_side_bar_name"];
			$business_users_options[$company["id"]] = $title;
		}
		
		//GET ACCOUNT TYPES
		$account_type_options = array();
		$account_type_options["All"] = "All";
		foreach(get_distinct("account_type","account") as $option)
		{
			$title = $option;
			$account_type_options[$title] = $title;
			//echo $option;
		}
		
		//GET ACCOUNT CLASSES
		$account_class_options = array();
		$account_class_options["All"] = "All";
		foreach(get_distinct("account_class","account") as $option)
		{
			$title = $option;
			$account_class_options[$title] = $title;
			//echo $option;
		}
		
		//GET ACCOUNT CATEGORIES
		$account_category_options = array();
		$account_category_options["All"] = "All";
		foreach(get_distinct("category","account") as $option)
		{
			$title = $option;
			$account_category_options[$title] = $title;
			//echo $option;
		}
		
		$data['account_category_options'] = $account_category_options;
		$data['account_class_options'] = $account_class_options;
		$data['account_type_options'] = $account_type_options;
		$data['business_users_options'] = $business_users_options;
		$data['title'] = "Accounts";
		$data['tab'] = 'Accounts';
		$this->load->view('accounts_view',$data);
	
	}// end index
	
	function load_report()
	{
		//GET DATA FROM FILTER FORM
		$business_user_id = $_POST["business_user"];//company_id
		$account_type = $_POST["account_type"];
		$account_class = $_POST["account_class"];
		$account_category = $_POST["account_category"];
		$sub_account_display = $_POST["sub_account_display"];
		
		//GET BUSINESS USER COMPANY
		$where = null;
		$where["id"] = $business_user_id;
		$business_user = db_select_company($where);
		
		//GET ACCOUNTS
		$where = null;
		$where["company_id"] = $business_user_id;
		
		if($sub_account_display == "Hide")
		{
			$where["parent_account_id"] = NULL;
		}
		
		if($account_type != "All")
		{
			$where["account_type"] = $account_type;
		}
		
		if($account_class != "All")
		{
			$where["account_class"] = $account_class;
		}
		
		if($account_category != "All")
		{
			$where["category"] = $account_category;
		}
		
		$accounts = db_select_accounts($where,"account_class, account_type, account_name");
		
		$data['business_user'] = $business_user;
		$data['accounts'] = $accounts;
		if(user_has_permission("view business accounts"))
		{
			$this->load->view('accounts/accounts_list',$data);
		}
		else
		{
			echo "You don't have permissions to view this content.";
		}
	}
	
	function load_new_account_form()
	{
		$business_user_id = $_POST["business_user_id"];
		$account_with = $_POST["account_with"];
		$person_id = $this->session->userdata('person_id');
		
		$where = null;
		$where['business_id'] = $business_user_id;
		$where['related_business_id'] = $person_id;
		$company = db_select_business_relationship($where);
		
		if((user_has_permission("create new accounts for assigned business")&&!empty($company))||(user_has_permission("create new accounts for non-assigned business")))
		{
			//echo $business_user_id;
			
			//GET ASSET ACCOUNTS
			$where = null;
			$where["company_id"] = $business_user_id;
			$where["account_class"] = "Asset";
			$where["parent_account_id"] = NULL;
			//$where = " company_id = '$business_user_id' AND account_class = 'Asset' AND parent_account_id IS NULL ";
			$asset_accounts = db_select_accounts($where);
			
			$asset_account_options = array();
			$asset_account_options["Select"] = "Select";
			$asset_account_options["No Parent"] = "No Parent";
			if(!empty($asset_accounts))
			{
				foreach($asset_accounts as $account)
				{
					$title = $account["account_name"];
					$asset_account_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			//GET LIABILITY ACCOUNTS
			$where = null;
			$where["company_id"] = $business_user_id;
			$where["account_class"] = "Liability";
			$where["parent_account_id"] = NULL;
			$liability_accounts = db_select_accounts($where);
			
			$liability_account_options = array();
			$liability_account_options["Select"] = "Select";
			$liability_account_options["No Parent"] = "No Parent";
			if(!empty($liability_accounts))
			{
				foreach($liability_accounts as $account)
				{
					$title = $account["account_name"];
					$liability_account_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			//GET REVENUE ACCOUNTS
			$where = null;
			$where["company_id"] = $business_user_id;
			$where["account_class"] = "Revenue";
			$where["parent_account_id"] = NULL;
			$revenue_accounts = db_select_accounts($where);
			
			$revenue_account_options = array();
			$revenue_account_options["Select"] = "Select";
			$revenue_account_options["No Parent"] = "No Parent";
			if(!empty($revenue_accounts))
			{
				foreach($revenue_accounts as $account)
				{
					$title = $account["account_name"];
					$revenue_account_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			//GET EXPENSE ACCOUNTS
			$where = null;
			$where["company_id"] = $business_user_id;
			$where["account_class"] = "Expense";
			$where["parent_account_id"] = NULL;
			$expense_accounts = db_select_accounts($where);
			
			$expense_account_options = array();
			$expense_account_options["Select"] = "Select";
			$expense_account_options["No Parent"] = "No Parent";
			if(!empty($expense_accounts))
			{
				foreach($expense_accounts as $account)
				{
					$title = $account["account_name"];
					$expense_account_options[$account["id"]] = $title;
					//echo $option;
				}
			}
			
			$relationship_options = null;
			if($account_with == "Customer")
			{
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
			}
			else if($account_with == "Vendor")
			{
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
			}
			else if($account_with == "Member")
			{
				//GET LIST OF MEMBERS
				$where = null;
				$where["business_id"] = $business_user_id;
				$where["relationship"] = "Member";
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
			}
			
			//GET ASSET OPTIONS FOR CATEGORY
			$where = null;
			//$where["company_id"] = $business_user_id;
			//$where["account_class"] = "Asset";
			//$where["parent_account_id"] = NULL;
			$where = ' company_id ='.$business_user_id.' AND account_class = "Asset"';
			$asset_account_categories = get_distinct("category","account",$where,"category");
			
			$asset_category_options = array();
			$asset_category_options["Select"] = "Select";
			$asset_category_options["New Category"] = "New Category";
			foreach($asset_account_categories as $category)
			{
				$title = $category;
				$asset_category_options[$category] = $title;
				//echo $option;
			}
			
			//GET LIABILITY OPTIONS FOR CATEGORY
			$where = null;
			//$where["company_id"] = $business_user_id;
			//$where["account_class"] = "Liability";
			//$where["parent_account_id"] = NULL;
			$where = ' company_id ='.$business_user_id.' AND account_class = "Liability"';
			$liability_account_categories = get_distinct("category","account",$where,"category");
			
			$liability_category_options = array();
			$liability_category_options["Select"] = "Select";
			$liability_category_options["New Category"] = "New Category";
			foreach($liability_account_categories as $category)
			{
				$title = $category;
				$liability_category_options[$category] = $title;
				//echo $option;
			}
			
			//GET REVENUE OPTIONS FOR CATEGORY
			$where = null;
			//$where["company_id"] = $business_user_id;
			//$where["account_class"] = "Revenue";
			//$where["parent_account_id"] = NULL;
			$where = ' company_id ='.$business_user_id.' AND account_class = "Revenue"';
			$revenue_account_categories = get_distinct("category","account",$where,"category");
			
			$revenue_category_options = array();
			$revenue_category_options["Select"] = "Select";
			$revenue_category_options["New Category"] = "New Category";
			foreach($revenue_account_categories as $category)
			{
				$title = $category;
				$revenue_category_options[$category] = $title;
				//echo $option;
			}
			
			//GET EXPENSE OPTIONS FOR CATEGORY
			$where = null;
			//$where["company_id"] = $business_user_id;
			//$where["account_class"] = "Expense";
			//$where["parent_account_id"] = NULL;
			$where = ' company_id ='.$business_user_id.' AND account_class = "Expense" ';
			$expense_account_categories = get_distinct("category","account",$where,"category");
			
			$expense_category_options = array();
			$expense_category_options["Select"] = "Select";
			$expense_category_options["New Category"] = "New Category";
			foreach($expense_account_categories as $category)
			{
				$title = $category;
				$expense_category_options[$category] = $title;
				//echo $option;
			}
			
			
			
			$data['asset_category_options'] = $asset_category_options;
			$data['liability_category_options'] = $liability_category_options;
			$data['revenue_category_options'] = $revenue_category_options;
			$data['expense_category_options'] = $expense_category_options;
			$data['business_user_id'] = $business_user_id;
			$data['account_with'] = $account_with;
			$data['relationship_options'] = $relationship_options;
			$data['asset_account_options'] = $asset_account_options;
			$data['liability_account_options'] = $liability_account_options;
			$data['revenue_account_options'] = $revenue_account_options;
			$data['expense_account_options'] = $expense_account_options;
			
			$this->load->view('accounts/new_account_form',$data);
		}
		else
		{
			echo "<br><br><div style='color:red'>You don't have permission to create a new account for this business user.</div>";
		}
		
		
		
	}
	
	function customer_selected_for_new_account()
	{
		//GET ACCOUNTS RECEIVABLES ACCOUNTS
		$where = null;
		$where["company_id"] = $business_user_id;
		$where["category"] = "Accounts Receivable";
		$ar_accounts = db_select_accounts($where);
		
		$ar_account_options = array();
		$ar_account_options["Select"] = "Select";
		$ar_account_options["No Parent"] = "No Parent";
		foreach($ar_accounts as $account)
		{
			$title = $account["account_name"];
			$ar_account_options[$account["id"]] = $title;
			//echo $option;
		}
		
		//GET EXPENSE ACCOUNTS
		$where = null;
		$where["company_id"] = $business_user_id;
		$where["account_class"] = "Revenue";
		$revenue_accounts = db_select_accounts($where);
		
		$revenue_account_options = array();
		$revenue_account_options["Select"] = "Select";
		$revenue_account_options["No Parent"] = "No Parent";
		foreach($revenue_accounts as $account)
		{
			$title = $account["account_name"];
			$revenue_account_options[$account["id"]] = $title;
			//echo $option;
		}
		
		$data['revenue_account_options'] = $revenue_account_options;
		$data['ar_account_options'] = $ar_account_options;
		$this->load->view('accounts/new_customer_account_form',$data);
	}
	
	function create_new_account()
	{
		
		$company_id = $_POST["business_user_company_id"];
		$account_type = $_POST["account_type"];
		$relationship_id = $_POST["relationship_id"];
		$account_class = $_POST["account_class"];
		
		$parent_asset_account = $_POST["parent_asset_account"];
		$parent_liability_account = $_POST["parent_liability_account"];
		$parent_revenue_account = $_POST["parent_revenue_account"];
		$parent_expense_account = $_POST["parent_expense_account"];
		
		$account_category = $_POST["account_category"];
		$account_name = $_POST["account_name"];
		
		if($account_type != "Business" || $account_type != "Holding")
		{
				$new_account["relationship_id"] = $relationship_id;
		}
		
		if($account_class == "Asset")
		{
			if($parent_asset_account != "No Parent")
			{
				//GET PARENT ACCOUNT
				$where = null;
				$where["id"] = $parent_asset_account;
				$parent_account = db_select_account($where);
				
				$new_account["parent_account_id"] = $parent_account["id"];
			}
			
			if($_POST["asset_category"] == "New Category")
			{
				$category = $_POST["account_category"];
			}
			else
			{
				$category = $_POST["asset_category"];
			}
		}
		else if($account_class == "Liability")
		{
			if($parent_liability_account != "No Parent")
			{
				//GET PARENT ACCOUNT
				$where = null;
				$where["id"] = $parent_liability_account;
				$parent_account = db_select_account($where);
				
				$new_account["parent_account_id"] = $parent_account["id"];
			}
			
			if($_POST["liability_category"] == "New Category")
			{
				$category = $_POST["account_category"];
			}
			else
			{
				$category = $_POST["liability_category"];
			}
		}
		else if($account_class == "Revenue")
		{
			if($parent_revenue_account == "No Parent")
			{
				$category = $account_category;
			}
			else
			{
				//GET PARENT ACCOUNT
				$where = null;
				$where["id"] = $parent_revenue_account;
				$parent_account = db_select_account($where);
				
				$new_account["parent_account_id"] = $parent_account["id"];
			}
			
			if($_POST["revenue_category"] == "New Category")
			{
				$category = $_POST["account_category"];
			}
			else
			{
				$category = $_POST["revenue_category"];
			}
		}
		else if($account_class == "Expense")
		{
			if($parent_expense_account != "No Parent")
			{
				//GET PARENT ACCOUNT
				$where = null;
				$where["id"] = $parent_expense_account;
				$parent_account = db_select_account($where);
				
				$new_account["parent_account_id"] = $parent_account["id"];
			}
			
			if($_POST["expense_category"] == "New Category")
			{
				$category = $_POST["account_category"];
			}
			else
			{
				$category = $_POST["expense_category"];
			}
		}
		
		
		$new_account["company_id"] = $company_id;
		$new_account["account_type"] = $account_type;
		$new_account["account_class"] = $account_class;
		$new_account["category"] = $category;
		$new_account["account_status"] = "Open";
		$new_account["account_name"] = $account_name;
		db_insert_account($new_account);
		
		// if($account_class == "Expense")
		// {
			// if($_POST["expense_category"] == "New Category")
			// {
				// //GET NEWLY CREATED ACCOUNT
				// $where = null;
				// $where = $new_account;
				// $newly_created_account = db_select_account($where);
				
				// //CREATE NEW PERMISSION
				// $permission = null;
				// $permission["permission_name"] = "approve expenses with category: ".$newly_created_account["category"];
				// $permission["category"] = "POs";
				// $permission["secondary_category"] = "PO Approval";
				// $permission["permission_description"] = "allows user to approve a PO with a category of ".$newly_created_account["account_name"];
				// db_insert_permission($permission);
			
			// }
		// }
		
		
		echo "success";
	}
	
	function load_sub_accounts()
	{
		$account_id = $_POST["account_id"];
		
		//GET PARENT ACCOUNT
		$where = null;
		$where["id"] = $account_id;
		$parent_account = db_select_account($where);
		
		//GET SUB ACCOUNTS
		$where = null;
		$where["parent_account_id"] = $account_id;
		$sub_accounts = db_select_accounts($where,"account_name");
		
		$data['parent_account'] = $parent_account;
		$data['sub_accounts'] = $sub_accounts;
		$this->load->view('accounts/sub_accounts',$data);
		
	}
	
	function load_account_details()
	{
		$account_id = $_POST["account_id"];
		
		//GET ACCOUNT
		$where = null;
		$where["id"] = $account_id;
		$account = db_select_account($where);
		
		//GET COMPANY
		$where = null;
		$where["id"] = $account["company_id"];
		$company = db_select_company($where);
		
		//GET ACCOUNT ENTRIES
		$where = null;
		$where["account_id"] = $account_id;
		$account_entries = db_select_account_entrys($where,"id DESC");
		
		
		$data['account'] = $account;
		$data['company'] = $company;
		$data['account_entries'] = $account_entries;
		if(user_has_permission("view business accounts"))
		{
			$this->load->view('accounts/account_details',$data);
		}
		else
		{
			echo "You don't have permissions to view this content.";
		}
	}
	
	//ONE-TIME SCRIPT ------ THIS IS NOW HANDLED IN THE add_driver() FUNCTION IN THE PEOPLE CONTROLLER - CUSTOMER HELPER / create_default_accounts()
	function create_default_driver_accounts($driver_company_id)
	{
		//GET DRIVER COMPANY
		$where = null;
		$where["id"] = $driver_company_id;
		$driver_company = db_select_company($where);
		
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		//GET LEASING COMPANY
		$where = null;
		$where["category"] = "Leasing";
		$leasing_company = db_select_company($where);
		
		//GET RELATIONSHIP BETWEEN LEASING COMPANY AND COOP
		$where = null;
		$where["business_id"] = $leasing_company["id"];
		$where["related_business_id"] = $coop_company["id"];
		$leasing_coop_relationship = db_select_business_relationship($where);
		
		//GET RELATIONSHIP BETWEEN COOP AND DRIVER COMPANY
		$where = null;
		$where["business_id"] = $coop_company["id"];
		$where["related_business_id"] = $driver_company_id;
		$coop_member_relationship = db_select_business_relationship($where);
		
		//************************FUEL PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON FUEL PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Fuel Payments";
		$coop_default_fuel_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON DIRECT LEASE PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_fuel_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Fuel Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Fuel Payments";
		db_insert_default_account($default_acc);
		
		//************************INSURANCE PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON FUEL PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Insurance Payments";
		$coop_default_insurance_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON DIRECT LEASE PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_insurance_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Insurance Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Insurance Payments";
		db_insert_default_account($default_acc);
		
		//************************DIRECT LEASE PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON DIRECT LEASE PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Direct Lease Payments";
		$coop_default_direct_lease_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON DIRECT LEASE PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_direct_lease_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Direct Lease Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		
		//SET ACCOUNT AS DEFAULT DIRECT LEASE PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Lease Payments";
		db_insert_default_account($default_acc);
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO DIRECT LEASE ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Direct Lease on Member Invoices";
		$coop_default_direct_lease_payment_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO DIRECT LEASE ON MEMBER INVOICES
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "Member Invoices";
		$account["parent_account_id"] = $coop_default_direct_lease_payment_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/P to Direct Lease - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT DIRECT LEASE PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P to Lease Vendor";
		db_insert_default_account($default_acc);
		
		
		
		//************************LOBOS PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON DIRECT LEASE PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Lobos Payments";
		$coop_default_lobos_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON DIRECT LEASE PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_lobos_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Lobos Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		
		//SET ACCOUNT AS DEFAULT DIRECT LEASE PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Driver Services Payments";
		db_insert_default_account($default_acc);
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO DIRECT LEASE ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Lobos on Member Invoices";
		$coop_default_lobos_payment_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO DIRECT LEASE ON MEMBER INVOICES
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "Member Invoices";
		$account["parent_account_id"] = $coop_default_lobos_payment_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/P to Lobos - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT DIRECT LEASE PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P to Driver Services Vendor";
		db_insert_default_account($default_acc);
		
		
		//************************ARROWHEAD PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON ARROWHEAD PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Arrowhead Payments";
		$coop_default_arrowhead_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_arrowhead_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Arrowhead Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		
		//SET ACCOUNT AS DEFAULT ARROWHEAD PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Dispatch Payments";
		db_insert_default_account($default_acc);
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO ARROWHEAD ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Arrowhead on Member Invoices";
		$coop_default_arrowhead_payment_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO ARROWHEAD ON MEMBER INVOICES
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "Member Invoices";
		$account["parent_account_id"] = $coop_default_arrowhead_payment_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/P to Arrowhead - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT ARROWHEAD PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P to Dispatch Vendor";
		db_insert_default_account($default_acc);
		
		
		//************************FLEETPROTECT************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON FLEETPROTECT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on FleetProtect";
		$coop_default_fleetProtect_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "FleetProtect";
		$account["parent_account_id"] = $coop_default_fleetProtect_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on FleetProtect - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		
		//SET ACCOUNT AS DEFAULT ARROWHEAD PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on FleetProtect";
		db_insert_default_account($default_acc);
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO ARROWHEAD ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Members on Settlements";
		$coop_default_settlements_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO MEMBERS ON SETTLEMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "Settlements Payable";
		$account["parent_account_id"] = $coop_default_settlements_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "Settlements Payable - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT SETTLEMENTS PAYABLE ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P to Member on Settlements";
		db_insert_default_account($default_acc);
		
		
		
		//************************ARROWHEAD FLEETPROTECT GUARANTEE************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM ARROWHEAD ON FLEETPROTECT DEPOSIT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Arrowhead on FleetProtect Deposit";
		$coop_default_fleetProtect_deposit_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "FleetProtect Deposit Receivable";
		$account["parent_account_id"] = $coop_default_fleetProtect_deposit_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on FleetProtect Deposit - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT A/R ON ARROWHEAD FLEETPROTECT DEPOSIT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on FleetProtect Deposit";
		db_insert_default_account($default_acc);
		
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO ARROWHEAD ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Arrowhead on FleetProtect Deposit";
		$coop_default_fleetProtect_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO MEMBERS ON SETTLEMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "FleetProtect Deposit Payable";
		$account["parent_account_id"] = $coop_default_fleetProtect_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/P on FleetProtect Deposit - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT SETTLEMENTS PAYABLE ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P on FleetProtect Deposit";
		db_insert_default_account($default_acc);
		
		
		
		//************************COOP MEMBERSHIP DUES AND QUICK PAY ************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON MEMBERSHIP DUES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Membership Dues";
		$coop_default_membership_dues_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Membership Dues Receivable";
		$account["parent_account_id"] = $coop_default_membership_dues_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Membership Dues - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT A/R ON ARROWHEAD FLEETPROTECT DEPOSIT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Membership Dues";
		db_insert_default_account($default_acc);
		
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON QUICK PAY
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Quick Pay";
		$coop_default_quick_pay_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Quick Pay Receivables";
		$account["parent_account_id"] = $coop_default_quick_pay_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Quick Pay - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT A/R ON ARROWHEAD FLEETPROTECT DEPOSIT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Quick Pay";
		db_insert_default_account($default_acc);
		
		
		
		
		echo date("H:i:s")." Success!";
	}
	
	function view_transaction_details($transaction_id)
	{
		if(user_has_permission("view business accounts"))
		{
			//GET TRANSACTION
			$where = null;
			$where["id"] = $transaction_id;
			$transaction = db_select_transaction($where);
			
			//GET ACCOUNT ENTRIES
			$where = null;
			$where["transaction_id"] = $transaction["id"];
			$account_entries = db_select_account_entrys($where);
			
			
			$data['transaction'] = $transaction;
			$data['account_entries'] = $account_entries;
			$data['title'] = "Trans ".$transaction["id"];
			$this->load->view('accounts/transaction_details_view',$data);
		}
		else
		{
			echo "You don't have permissions to view this content.";
		}
		
		
		
	}
	
	function reverse_transaction()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		//GET TRANSACTION
		$where = null;
		$where["id"] = $_POST["transaction_id"];
		$transaction = db_select_transaction($where);
		
		//GET ACCOUNT ENTRIES
		$where = null;
		$where["transaction_id"] = $transaction["id"];
		$account_entries = db_select_account_entrys($where);
		
		foreach($account_entries as $account_entry)
		{
			$debit_credit = null;
			if($account_entry["debit_credit"] == "Debit")
			{
				$debit_credit = "Credit";
			}
			else
			{
				$debit_credit = "Debit";
			}
			
			if(!empty($debit_credit))
			{
				//CREATE CREDIT ENTRY
				$reverse_entry = null;
				$reverse_entry["account_id"] = $account_entry["account_id"];
				$reverse_entry["transaction_id"] = $account_entry["transaction_id"];
				$reverse_entry["recorder_id"] = $recorder_id;
				$reverse_entry["recorded_datetime"] = $recorded_datetime;
				$reverse_entry["entry_datetime"] = $account_entry["entry_datetime"];
				$reverse_entry["debit_credit"] = $debit_credit;
				$reverse_entry["entry_amount"] = $account_entry["entry_amount"];
				$reverse_entry["entry_description"] = $account_entry["entry_description"]." | REVERSED ";
				//$reverse_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				db_insert_account_entry($reverse_entry);
			}
		}
		
		redirect(base_url("index.php/accounts/view_transaction_details")."/".$transaction["id"]);
	}
	
	//ONE TIME SCRIPTS
	
	//CREDIT ACCOUNT
	function credit_fp_account($account_id,$entry_amount)
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		$account_balance = get_account_balance($account_id);
		
		$new_account_balance = round($account_balance - $entry_amount,2);
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $account_id;
		//$credit_entry["transaction_id"] = $account_entry["transaction_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = $recorded_datetime;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $entry_amount;
		$credit_entry["entry_description"] = "SYSTEM ADJUSTMENT INITIATED BY SYSTEM ADMIN TO FIX MISUSE OF KICK IN";
		$credit_entry["account_balance"] = $new_account_balance;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		db_insert_account_entry($credit_entry);
		
		
		echo "Success!<br>Credited account $account_id $entry_amount<br>New balance = $new_account_balance";
		
	}
	
	function test()
	{
		echo "hello world";
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}