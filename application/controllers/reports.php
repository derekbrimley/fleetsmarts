<?php		

class Reports extends MY_Controller 
{

	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		$data['title'] = "Reports";
		$data['tab'] = 'Reports';
		$this->load->view('reports_view',$data);
	}
	
	function load_all_drivers_report()
	{
		$start_date = date('Y-m-d H:i:s',strtotime($_POST['start_date_filter']));
		$end_date = date('Y-m-d H:i:s',strtotime($_POST['end_date_filter']));
		
		$sql = "select distinct client_nickname
				from client
				where id in 
					(SELECT distinct main_driver_id 
					FROM log_entry
					WHERE entry_datetime BETWEEN '$start_date' AND '$end_date')
				or id in 
					(SELECT distinct codriver_id 
					FROM log_entry
					WHERE entry_datetime BETWEEN '$start_date' AND '$end_date')
				ORDER BY client_nickname";
		
		$query = $this->db->query($sql);
		
		$drivers = array();
		foreach ($query->result() as $row){
			$drivers[] = $row->client_nickname;
		}
		
		$count = count($drivers);
		
		$data['count'] = $count;
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['drivers'] = $drivers;
		$this->load->view('reports/all_drivers',$data);
	}
	
	//LOAD THE REPORT LEFT BAR DIV WHEN SELECTED FROM THE DROPDOWN
	function load_report()
	{
		$report_type = $_POST["report_type"];
		
		if($report_type == "Arrowhead Expense Report")
		{
			$data = null;
			//$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
			$this->load->view('reports/arrowhead_expense_report_left_bar',$data);
		}
		else if($report_type == "Carrier-Driver Report")
		{
			
			//GET CARRIERS
			$where = null;
			$where["type"] = "Carrier";
			$carriers = db_select_companys($where,"company_name");
			
			$carriers_dropdown_options = array();
			$carriers_dropdown_options["Select"] = "Select";
			foreach($carriers as $carrier)
			{
				$carriers_dropdown_options[$carrier["id"]] = $carrier["company_name"];
			}
			
			$data['carriers_dropdown_options'] = $carriers_dropdown_options;
			$this->load->view('reports/carrier_driver_left_bar',$data);
		}
		else if($report_type == "Driver Accounts")
		{
			
			//GET FLEET MANAGERS
			$where = null;
			$where["role"] = "Fleet Manager";
			$fleet_managers = db_select_persons($where,"f_name");
			
			$fleet_managers_dropdown_options = array();
			$fleet_managers_dropdown_options["Select"] = "Select";
			$fleet_managers_dropdown_options["All"] = "All FMs";
			foreach($fleet_managers as $fleet_manager)
			{
				$fleet_managers_dropdown_options[$fleet_manager["id"]] = $fleet_manager["f_name"]." ".$fleet_manager["l_name"];
			}
			
			$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
			$this->load->view('reports/driver_accounts_left_bar',$data);
		}
		else if($report_type == "Driver Manager Report")
		{
			
			//GET FLEET MANAGERS
			$where = null;
			$where["role"] = "Fleet Manager";
			$fleet_managers = db_select_persons($where,"f_name");
			
			$fleet_managers_dropdown_options = array();
			$fleet_managers_dropdown_options["Select"] = "Select";
			$fleet_managers_dropdown_options["All"] = "All FMs";
			foreach($fleet_managers as $fleet_manager)
			{
				$fleet_managers_dropdown_options[$fleet_manager["id"]] = $fleet_manager["f_name"]." ".$fleet_manager["l_name"];
			}
			
			$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
			$this->load->view('reports/dm_report_left_bar',$data);
		}
		else if($report_type == "Driver Hold Report")
		{
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
			
			$data['clients_dropdown_options'] = $clients_dropdown_options;
			$this->load->view('reports/driver_hold_left_bar',$data);
			
		}
		else if($report_type == "Deduction Report")
		{
			
			//GET FLEET MANAGERS
			$where = null;
			$where["type"] = "Fleet Manager";
			$fleet_managers = db_select_companys($where,"company_side_bar_name");
			
			$fleet_managers_dropdown_options = array();
			$fleet_managers_dropdown_options["Select"] = "Select";
			$fleet_managers_dropdown_options["All"] = "All FMs";
			foreach($fleet_managers as $fleet_manager)
			{
				$fleet_managers_dropdown_options[$fleet_manager["id"]] = $fleet_manager["company_side_bar_name"];
			}
			
			$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
			$this->load->view('reports/deduction_report_left_bar',$data);
		}
		else if ($report_type == "DTR Export")
		{
			$this->load->view('reports/dtr_report_left_bar');
		}
		else if($report_type == "End Leg Export")
		{
		
			$this->load->view('reports/leg_report_left_bar');
		}
		else if($report_type == "Financial Report")
		{
			$data = null;
			//$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
			$this->load->view('reports/financial_report_left_bar',$data);
		}
		else if($report_type == "FleetProtect Account")
		{
			//GET ALL DRIVERS - CLIENTS
			$where = null;
			$where["dropdown_status"] = "Show";
			$clients = db_select_clients($where);
			
			$client_options = null;
			$client_options['Select'] = 'Select';
			foreach($clients as $client)
			{
				$client_options[$client["company_id"]] = $client["client_nickname"];
			}
			
			$data = null;
			$data['client_options'] = $client_options;
			$this->load->view('reports/fleetprotect_report_left_bar',$data);
		}
		else if($report_type == "FM Expenses")
		{
			
			//GET FLEET MANAGERS
			$where = null;
			$where["type"] = "Fleet Manager";
			$fleet_managers = db_select_companys($where,"company_side_bar_name");
			
			$fleet_managers_dropdown_options = array();
			$fleet_managers_dropdown_options["Select"] = "Select";
			$fleet_managers_dropdown_options["All"] = "All FMs";
			foreach($fleet_managers as $fleet_manager)
			{
				$fleet_managers_dropdown_options[$fleet_manager["id"]] = $fleet_manager["company_side_bar_name"];
			}
			
			$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
			$this->load->view('reports/fm_expense_report_left_bar',$data);
			
		}
		else if($report_type == "Expenses")
		{
			
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
			
			$data['bill_owner_sidebar_options'] = $bill_owner_sidebar_options;
			$this->load->view('reports/expense_report_left_bar',$data);
			
		}
		else if($report_type == "Fuel Report")
		{
			
			//GET ALL ACTIVE MAIN DRIVERS
			$where = null;
			$where["client_status"] = "Active";
			$where["client_type"] = "Main Driver";
			$main_drivers = db_select_clients($where,"client_nickname");
			
			$main_driver_dropdown_options = array();
			$main_driver_dropdown_options["All"] = "All Main Drivers";
			foreach($main_drivers as $main_driver)
			{
				$main_driver_dropdown_options[$main_driver["id"]] = $main_driver["client_nickname"];
			}
			
			//GET ALL ACTIVE TRUCKS
			$where = null;
			$where = " status != 'Returned' ";
			$trucks = db_select_trucks($where,"truck_number");
			
			$truck_dropdown_options = array();
			$truck_dropdown_options["All"] = "All Trucks";
			foreach($trucks as $truck)
			{
				$truck_dropdown_options[$truck["id"]] = $truck["truck_number"];
			}
			
			
			$data['truck_dropdown_options'] = $truck_dropdown_options;
			$data['main_driver_dropdown_options'] = $main_driver_dropdown_options;
			$this->load->view('reports/fuel_report_left_bar',$data);
		}
		else if($report_type == "Funding Report")
		{
			
			//GET FLEET MANAGERS
			$where = null;
			$where["role"] = "Fleet Manager";
			$fleet_managers = db_select_persons($where,"f_name");
			
			$fleet_managers_dropdown_options = array();
			$fleet_managers_dropdown_options["All"] = "All FMs";
			foreach($fleet_managers as $fleet_manager)
			{
				$fleet_managers_dropdown_options[$fleet_manager["id"]] = $fleet_manager["f_name"]." ".$fleet_manager["l_name"];
			}
			
			//GET BROKERS
			$where = null;
			$where = " status <> 'Bad' ";
			$brokers = db_select_customers($where,"customer_name");
			
			$broker_dropdown_options = array();
			$broker_dropdown_options["All"] = "All Brokers";
			foreach($brokers as $broker)
			{
				$broker_dropdown_options[$broker["id"]] = $broker["customer_name"];
			}
			
			
			$data['broker_dropdown_options'] = $broker_dropdown_options;
			$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
			$this->load->view('reports/funding_report_left_bar',$data);
		}
		else if($report_type == "Income Statement")
		{
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
		
			$data['business_users_options'] = $business_users_options;
			$this->load->view('reports/income_statement_left_bar',$data);
		}
		else if($report_type == "Missing Paperwork")
		{
			//GET FLEET MANAGERS
			$where = null;
			$where["role"] = "Fleet Manager";
			$fleet_managers = db_select_persons($where,"f_name");
			
			$fleet_managers_dropdown_options = array();
			$fleet_managers_dropdown_options["All"] = "All FMs";
			foreach($fleet_managers as $fleet_manager)
			{
				$fleet_managers_dropdown_options[$fleet_manager["id"]] = $fleet_manager["f_name"]." ".$fleet_manager["l_name"];
			}
			
			//GET ALL DRIVERS
			$where = "";
			$where = " client_status <> 'Closed'";
			$clients = db_select_clients($where,"client_nickname");
			
			$driver_dd_options = array();
			$driver_dd_options["All"]  = "All Drivers";
			foreach($clients as $client)
			{
				
				$driver_dd_options[$client["id"]]  = $client["client_nickname"];
			}
			
			$data['driver_dd_options'] = $driver_dd_options;
			$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
			$this->load->view('reports/missing_paperwork/missing_paperwork_report_left_bar',$data);
		}
		else if($report_type == "Reimbursement Report")
		{
			
			//GET ALL ACTIVE MAIN DRIVERS
			$where = null;
			$where["client_status"] = "Active";
			$drivers = db_select_clients($where,"client_nickname");
			
			$driver_dropdown_options = array();
			$driver_dropdown_options["All"] = "All Drivers";
			foreach($drivers as $driver)
			{
				$driver_dropdown_options[$driver["company_id"]] = $driver["client_nickname"];
			}
			
			
			$data['driver_dropdown_options'] = $driver_dropdown_options;
			$this->load->view('reports/reimbursement_report_left_bar',$data);
		}
		else if($report_type == "Time and Attendance")
		{
			$user_sidebar_options = array();
			$user_sidebar_options["All"] = "All";
			foreach(get_distinct("user_id","time_punch") as $user_id)
			{
				//GET USER
				$where = null;
				$where["id"] = $user_id;
				$user = db_select_user($where);
				
				$title = $user["person"]["full_name"];
				$user_sidebar_options[$user_id] = $title;
			}
			
			$data['user_sidebar_options'] = $user_sidebar_options;
			$this->load->view('reports/time_and_attendance_left_bar',$data);
		}
		else if ($report_type == "Transactions Export")
		{
			$this->load->view('reports/transactions_export_left_bar');
		}
		else if($report_type == 'All Drivers Report')
		{
			$this->load->view('reports/all_drivers_left_bar');
			
		}
	}
	
	function load_carrier_driver_report()
	{
		$carrier_id = $_POST['carrier_dropdown'];
		$start_date = date('Y-m-d',strtotime($_POST['start_date_filter']));
		$end_date = date('Y-m-d',strtotime($_POST['end_date_filter'].' + 1 days'));
		
		$where = null;
		$where['id'] = $carrier_id;
		$carrier = db_select_company($where);
		
		if(!is_null($start_date) && !is_null($end_date))
		{
			$sql = "SELECT
						DISTINCT leg.main_driver_id, leg.codriver_id
					FROM leg
					JOIN company ON company.id = leg.carrier_id
					JOIN log_entry ON log_entry.id = leg.log_entry_id
					WHERE carrier_id = '".$carrier_id."'
					AND entry_datetime > '".$start_date."'
					AND entry_datetime < '".$end_date."'";
			// echo $sql;
			$query = $this->db->query($sql);
			$drivers = null;
			foreach($query->result() as $row)
			{
				$driver = null;
				$driver['id'] = $row->main_driver_id;
				
				$drivers[] = $driver;
			}
			
			$companies = array();
			if(!empty($drivers))
			{
				foreach($drivers as $driver)
				{
					$where = null;
					$where['id'] = $driver['id'];
					$company = db_select_company($where);
					if(!empty($company['company_name']))
					{
						$companies[$company['id']] = $company['company_name'];
					}
				}
			}
			
			$data['companies'] = $companies;
			$data['carrier'] = $carrier;
			$this->load->view("reports/carrier_driver_report",$data);
		}
	}
	
	//LOAD DRIVER MANAGER REPORT
	function load_dm_report()
	{
		$fm_person_id = $_POST["fleet_managers_dropdown"];
		
		//GET FM COMPANY
		$where = null;
		$where["person_id"] = $fm_person_id;
		$where["type"] = "Fleet Manager";
		$fm_company = db_select_company($where);
		
		$stats = get_dm_report_stats($fm_company["id"]);
	
		$data['stats'] = $stats;
		$data['fm_company'] = $fm_company;
		$this->load->view('reports/dm_report',$data);
	}
	
	//LOAD DRIVER MANAGER REPORT
	function print_dm_report($fm_person_id)
	{
		//GET FM COMPANY
		$where = null;
		$where["person_id"] = $fm_person_id;
		$where["type"] = "Fleet Manager";
		$fm_company = db_select_company($where);
		
		$stats = get_dm_report_stats($fm_company["id"]);
	
		$data['stats'] = $stats;
		$data['fm_company'] = $fm_company;
		$this->load->view('reports/dm_report_printable',$data);
	}
	
	//LOAD FUEL REPORT
	function load_fuel_report()
	{
		
		//GET FILTER PARAMETERS
		$main_driver_id = (int)$_POST["main_driver_filter_dropdown"];
		$truck_id = (int)$_POST["truck_filter_dropdown"];
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		
		
		
		//GET ENTRY LOGS
		$where = "";
		
		//MAIN DRIVER FILTER
		if($main_driver_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($main_driver_id))
			{
				$where = $where." AND main_driver_id = ".$main_driver_id;
			}
		}
		
		
		//TRUCK FILTER
		if($truck_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($truck_id))
			{
				$where = $where." AND truck_id = ".$truck_id;
			}
		}
		
		//START DATE FILTER
		if(!empty($start_date))
		{
			$start_datetime = date("Y-m-d G:i:s",strtotime($start_date));
			$where = $where." AND entry_datetime > '".$start_datetime."'";
		}
		
		//END DATE FILTER
		if(!empty($end_date))
		{
			$end_datetime = date("Y-m-d G:i:s",strtotime($end_date)+60*60*24);
			$where = $where." AND entry_datetime < '".$end_datetime."'";
		}
		
		$where = $where." AND (entry_type = 'Fuel Fill' OR entry_type = 'Fuel Partial') ";
		
		
		$where = substr($where,4);
		//echo $where;
		$logs = db_select_log_entrys($where,"entry_datetime DESC");
		
		$fuel_lines = array();
		$total_expense = 0;
		$total_rebate = 0;
		$total_gallons = 0;
		$total_count = 0;
		if(!empty($logs))
		{
			foreach($logs as $log_entry)
			{
				
				//GET FUEL STOP
				$where = null;
				$where["log_entry_id"] = $log_entry["id"];
				$fuel_stop = db_select_fuel_stop($where);
				
				//ONLY INCLUDE ComData FUEL STOPS
				if($fuel_stop["source"] == 'ComData')
				{
					$total_expense = $total_expense + $fuel_stop["fuel_expense"];
					$total_gallons = $total_gallons + $fuel_stop["gallons"];
					$total_rebate = $total_rebate + $fuel_stop["rebate_amount"];
					$total_count++;
					
					$main_driver = $log_entry["main_driver"]["client_nickname"];
					$fuel_line["driver"] = substr($main_driver,0,strpos($main_driver," ")+2);
					$fuel_line["truck"] = $log_entry["truck"]["truck_number"];
					$fuel_line["datetime"] = date("m/d/y H:i",strtotime($log_entry["entry_datetime"]));
					$fuel_line["city_state"] = $log_entry["city"].", ".$log_entry["state"];
					$fuel_line["gallons"] = $fuel_stop["gallons"];
					$fuel_line["fuel_expense"] = $fuel_stop["fuel_expense"];
					$fuel_line["rebate_amount"] = $fuel_stop["rebate_amount"];
					$fuel_line["allocated_entry_id"] = $fuel_stop["allocated_entry_id"];
					$fuel_line["locked_datetime"] = date("m/d/y",strtotime($log_entry["locked_datetime"]));
					
					$fuel_lines[] = $fuel_line;
				}
			}
		}
		
		$data['total_count'] = $total_count;
		$data['total_expense'] = $total_expense;
		$data['total_gallons'] = $total_gallons;
		$data['total_rebate'] = $total_rebate;
		$data['fuel_lines'] = $fuel_lines;
		$this->load->view('reports/fuel_report',$data);
		
		
	}//end load_log()
	
	//LOAD FUNDING REPORT
	function load_funding_report()
	{
		//echo $print;
		//GET FILTER PARAMETERS
		$broker_dropdown = $_POST["broker_dropdown"];
		$fleet_manager_dropdown = $_POST["fleet_managers_dropdown"];
		$funding_status_dropdown = $_POST["funding_status_dropdown"];
		$closed_status_dropdown = $_POST["closed_status_dropdown"];
		$drop_start_date = $_POST["drop_start_date_filter"];
		$drop_end_date = $_POST["drop_end_date_filter"];
		$billing_start_date = $_POST["billing_start_date_filter"];
		$billing_end_date = $_POST["billing_end_date_filter"];
		$funding_start_date = $_POST["funding_start_date_filter"];
		$funding_end_date = $_POST["funding_end_date_filter"];
		$get_factors = $_POST["get_factors"];
		$get_direct_bills = $_POST["get_direct_bills"];
		
		
		//CREATE WHERE CLAUSE FOR LOAD QUERY
		$where = " AND status = 'Dropped' ";
		
		//SET WHERE FOR BROKER
		if($broker_dropdown != "All")
		{
			$where = $where." AND broker_id = ".$broker_dropdown;
		}
		
		//SET WHERE FOR FUNDING STATUS
		if($funding_status_dropdown != "All")
		{
			if($funding_status_dropdown == "Funded")
			{
				$where = $where." AND  amount_funded IS NOT NULL ";
			}
			else
			{
				$where = $where." AND  (amount_funded IS NULL AND billing_status_number <> 8) ";
			}
		}
		
		//SET WHERE FOR CLOSED STATUS
		if($closed_status_dropdown != "All")
		{
			if($closed_status_dropdown == "Closed")
			{
				$where = $where." AND  invoice_closed_datetime IS NOT NULL ";
			}
			else
			{
				$where = $where." AND  invoice_closed_datetime IS NULL ";
			}
		}
		
		//SET WHERE FOR FM
		if($fleet_manager_dropdown != "All")
		{
			$where = $where." AND fleet_manager_id = ".$fleet_manager_dropdown;
		}
		
		//SET WHERE FOR DROP START DATE
		if(!empty($drop_start_date))
		{
			$drop_start_date = date("Y-m-d G:i:s",strtotime($drop_start_date));
			$where = $where." AND final_drop_datetime > '".$drop_start_date."' ";
		}
		
		//SET WHERE FOR DROP END DATE
		if(!empty($drop_end_date))
		{
			$drop_end_date = date("Y-m-d G:i:s",strtotime($drop_end_date)+24*60*60);
			$where = $where." AND final_drop_datetime < '".$drop_end_date."' ";
		}
		
		//SET WHERE FOR BILLING START DATE
		if(!empty($billing_start_date))
		{
			$billing_start_date = date("Y-m-d G:i:s",strtotime($billing_start_date));
			$where = $where." AND billing_datetime > '".$billing_start_date."' ";
		}
		
		//SET WHERE FOR BILLING END DATE
		if(!empty($billing_end_date))
		{
			$billing_end_date = date("Y-m-d G:i:s",strtotime($billing_end_date)+24*60*60);
			$where = $where." AND billing_datetime < '".$billing_end_date."' ";
		}
		
		//SET WHERE FOR FUNDED START DATE
		if(!empty($funding_start_date))
		{
			$funding_start_date = date("Y-m-d G:i:s",strtotime($funding_start_date));
			$where = $where." AND funded_datetime > '".$funding_start_date."' ";
		}
		
		//SET WHERE FOR FUNDED END DATE
		if(!empty($funding_end_date))
		{
			$funding_end_date = date("Y-m-d G:i:s",strtotime($funding_end_date)+24*60*60);
			$where = $where." AND funded_datetime < '".$funding_end_date."' ";
		}
		
		//IF GET ANY CHECK BOX IS UNCHECKED
		if
			(
				$get_factors == "false" || 
				$get_direct_bills == "false"
			)
		{
			$an_event_is_selected = false;
			$where = $where." AND (";
			
			if($get_factors == "true")
			{
				$where = $where."billing_method = 'Factor' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_direct_bills == "true")
			{
				$where = $where."billing_method = 'Direct Bill' OR ";
				$an_event_is_selected = true;
			}
			
			if($an_event_is_selected)
			{
				$where = substr($where,0,-4).") ";//this takes away the extra " OR "
			}
			else
			{
				$where = "     billing_method = 'none'"; //ADDS SPACES TO WORK WITH substr()
			}
		}
		
		$where = substr($where,4);
		//echo $where;
		$loads = db_select_loads($where,"final_drop_datetime ");
		
		$data['loads'] = $loads;
		
		$this->load->view('reports/funding_report',$data);
	
	}
	
	//LOAD INCOME STATEMENT
	function load_income_statement()
	{
		$business_user_id = $_POST['business_user_dropdown'];
		$start_date = date('Y-m-d',strtotime($_POST['start_date_filter']));
		$end_date = date('Y-m-d',strtotime($_POST['end_date_filter'])+60*60*24);
		
		//echo $end_date;
		

		if(user_has_permission("view income statement for non-assigned business")|| (user_has_permission("view income statement for assigned business")&&user_is_assigned_to_business($business_user_id)))
		{
			//GET BUSINESS USER
			$where = null;
			$where["id"] = $business_user_id;
				
			$business_user = db_select_company($where);
			
			$where = null;
			$where = "1 = 1";
			$account_entries = db_select_account_entrys($where);
			
			//GET SUM OF DEBITS FOR REVENUE ACCOUNTS
			$sql = null;
			$sql = "SELECT SUM(entry_amount) AS revenue_debit_sum FROM `account_entry` JOIN `account` ON account_entry.account_id = account.id WHERE company_id = '".$business_user_id."' AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND account_class ='Revenue' AND debit_credit='Debit'";
			$query = $this->db->query($sql);
			foreach($query->result() as $row)
			{
				$revenue_debit_sum = $row->revenue_debit_sum;
			}
			
			//GET SUM OF CREDITS FOR REVENUE ACCOUNTS
			$sql = null;
			$sql = "SELECT SUM(entry_amount) AS revenue_credit_sum FROM `account_entry` JOIN `account` ON account_entry.account_id = account.id WHERE company_id = '".$business_user_id."' AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND account_class ='Revenue' AND debit_credit='Credit'";
			$query = $this->db->query($sql);
			foreach($query->result() as $row)
			{
				$revenue_credit_sum = $row->revenue_credit_sum;
			}
			$revenue_total = $revenue_credit_sum - $revenue_debit_sum;
			
			//GET SUM OF DEBITS FOR EXPENSE ACCOUNTS	
			$sql = null;
			$sql = "SELECT SUM(entry_amount) AS expense_debit_sum FROM `account_entry` JOIN `account` ON account_entry.account_id = account.id WHERE company_id = '".$business_user_id."' AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND account_class ='Expense' AND debit_credit='Debit'";
			$query = $this->db->query($sql);
			foreach($query->result() as $row)
			{
				$expense_debit_sum = $row->expense_debit_sum;
			}
			
			//GET SUM OF CREDITS FOR EXPENSE ACCOUNTS
			$sql = null;
			$sql = "SELECT SUM(entry_amount) AS expense_credit_sum FROM `account_entry` JOIN `account` ON account_entry.account_id = account.id WHERE company_id = '".$business_user_id."' AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND account_class ='Expense' AND debit_credit='Credit'";
			$query = $this->db->query($sql);
			foreach($query->result() as $row)
			{
				$expense_credit_sum = $row->expense_credit_sum;
			}
			$expense_total = $expense_debit_sum - $expense_credit_sum;	
			
			//GET ALL CATEGORIES FROM ACCOUNT WHERE COMPANY_ID = ? AND ACCOUNT_CLASS = REVENUE
			
			$where = null;
			$where['company_id'] = $business_user_id;
			$where['account_class'] = "Revenue";
			$revenue_categories = get_distinct("category","account",$where);
			
			//ARRAY TO HOLD ALL CATEGORY DATA
			$revenue_category_infos = array();
			$revenue_account_entries = array();
			
			//LOOP THROUGH EACH CATEGORY
			foreach($revenue_categories as $revenue_category)
			{
				//ADD CATEGORY NAME TO INFO ARRAY
				$revenue_category_info = null;
				$revenue_category_info["category_name"] = $revenue_category;
				
				//GET SUM CREDIT FOR CATEGORY
				$credit_revenue_sql = null;
				$credit_revenue_sql = "SELECT SUM(entry_amount) AS credit_category_sum FROM `account_entry` JOIN `account` ON account_entry.account_id = account.id WHERE category='".$revenue_category."' AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND company_id = '".$business_user_id."' AND account_class ='Revenue' AND debit_credit='Credit'";
				$query = $this->db->query($credit_revenue_sql);
				foreach($query->result() as $row)
				{
					$credit_category_sum = $row->credit_category_sum;
				}
				
				//GET SUM DEBIT FOR CATEGORY
				$debit_revenue_sql = null;
				$debit_revenue_sql = "SELECT SUM(entry_amount) AS debit_category_sum FROM `account_entry` JOIN `account` ON account_entry.account_id = account.id WHERE category='".$revenue_category."' AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND company_id = '".$business_user_id."' AND account_class ='Revenue' AND debit_credit='Debit'";
				$query = $this->db->query($debit_revenue_sql);
				foreach($query->result() as $row)
				{
					$debit_category_sum = $row->debit_category_sum;
				}
				
				//GET DIFFERENCE
				$revenue_difference = $credit_category_sum - $debit_category_sum;
				
				//ADD DIFFERENCE TO CATEGORY INFO ARRAY
				$revenue_category_info["net"] = $revenue_difference;
				
				//GET ALL REVENUE ACCOUNTS ASSOCIATED WITH THIS CATEGORY AND BUSINESS USER
				$revenue_account_entries_sql = null;
				$revenue_account_entries_sql = "SELECT * FROM `account_entry` JOIN `account` ON account_entry.account_id = account.id WHERE company_id = '".$business_user_id."' AND category = '".$revenue_category."' AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND account_class = 'Revenue'";
				//echo $revenue_account_entries_sql."<br><br>";
				$query = $this->db->query($revenue_account_entries_sql);
				
				$revenue_account_entries = null;
				foreach($query->result() as $row)
				{
					$revenue_account_entry = null;
					$revenue_account_entry['account_id'] = $row->account_id;
					$revenue_account_entry['transaction_id'] = $row->transaction_id;
					$revenue_account_entry['recorded_datetime'] = $row->recorded_datetime;
					$revenue_account_entry['entry_datetime'] = $row->entry_datetime;
					$revenue_account_entry['debit_credit'] = $row->debit_credit;
					$revenue_account_entry['entry_amount'] = $row->entry_amount;
					$revenue_account_entry['entry_description'] = $row->entry_description;
					$revenue_account_entry['company_id'] = $row->company_id;
					$revenue_account_entry['account_name'] = $row->account_name;
					
					$revenue_account_entries[] = $revenue_account_entry;
				}
				
				
				//ADD REVENUE ACCOUNT ENTRIES TO CATEGORY INFO ARRAY
				$revenue_category_info["account_entries_by_category"] = $revenue_account_entries;
				
				//ADD REVENUE CATEGORY INFO TO LARGER ARRAY
				$revenue_category_infos[] = $revenue_category_info;
				// foreach($revenue_category_infos as $revenue_category_info)
				// {
					// foreach($revenue_category_info['account_entries_by_category'] as $entries)
					// {
						// echo $entries['debit_credit']."<br>";
					// }
				// }
			}
			
			//GET ALL CATEGORIES FROM ACCOUNT WHERE COMPANY_ID = ? AND ACCOUNT_CLASS = EXPENSE
			
			$where = null;
			$where['company_id'] = $business_user_id;
			$where['account_class'] = "Expense";
			$expense_categories = get_distinct("category","account",$where);
			
			//ARRAY TO HOLD ALL CATEGORY DATA
			$expense_category_infos = array();
			$expense_account_entries = array();
			
			
			
			
			
			
			
			$i = 1;
			//LOOP THROUGH EACH CATEGORY
			foreach($expense_categories as $expense_category)
			{
				//ADD CATEGORY NAME TO INFO ARRAY
				$expense_category_info = null;
				$expense_category_info["category_name"] = $expense_category;
				
				$values = array();
				$values[] = $expense_category;
				
				//GET SUM CREDIT FOR CATEGORY
				$credit_expense_sql = null;
				$credit_expense_sql = "SELECT SUM(entry_amount) AS credit_category_sum FROM `account_entry` JOIN `account` ON `account_entry`.account_id = `account`.id WHERE account.category = ? AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND company_id = '".$business_user_id."' AND account_class = 'Expense' AND debit_credit='Credit'";
				//$credit_expense_sql = "Select * from company";
				//echo $credit_expense_sql;
				//echo "<br>$i<br>";
				//$i++;
				$query = $this->db->query($credit_expense_sql,$values);
				foreach($query->result() as $row)
				{
					$credit_category_sum = $row->credit_category_sum;
				}
				
				//GET SUM DEBIT FOR CATEGORY
				$debit_expense_sql = null;
				$debit_expense_sql = "SELECT SUM(entry_amount) AS debit_category_sum FROM `account_entry` JOIN `account` ON account_entry.account_id = account.id WHERE category = ? AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND company_id = '".$business_user_id."' AND account_class ='Expense' AND debit_credit='Debit'";
				$query = $this->db->query($debit_expense_sql,$values);
				foreach($query->result() as $row)
				{
					$debit_category_sum = $row->debit_category_sum;
				}
				
				//GET DIFFERENCE
				$expense_difference = $credit_category_sum - $debit_category_sum;
				
				//ADD DIFFERENCE TO CATEGORY INFO ARRAY
				$expense_category_info["net"] = $expense_difference;
				
				//GET ALL REVENUE ACCOUNTS ASSOCIATED WITH THIS CATEGORY AND BUSINESS USER
				$expense_account_entries_sql = null;
				$expense_account_entries_sql = "SELECT * FROM `account_entry` JOIN `account` ON account_entry.account_id = account.id WHERE company_id = '".$business_user_id."' AND category = ? AND entry_datetime > '".$start_date."' AND entry_datetime <= '".$end_date."' AND account_class = 'Expense' ORDER BY category";
				$query = $this->db->query($expense_account_entries_sql,$values);
				
				$expense_account_entries = null;
				foreach($query->result() as $row)
				{
					$expense_account_entry = null;
					$expense_account_entry['account_id'] = $row->account_id;
					$expense_account_entry['transaction_id'] = $row->transaction_id;
					$expense_account_entry['recorded_datetime'] = $row->recorded_datetime;
					$expense_account_entry['entry_datetime'] = $row->entry_datetime;
					$expense_account_entry['debit_credit'] = $row->debit_credit;
					$expense_account_entry['entry_amount'] = $row->entry_amount;
					$expense_account_entry['entry_description'] = $row->entry_description;
					$expense_account_entry['company_id'] = $row->company_id;
					$expense_account_entry['account_name'] = $row->account_name;
					
					$expense_account_entries[] = $expense_account_entry;
				}
				
				
				//ADD REVENUE ACCOUNT ENTRIES TO CATEGORY INFO ARRAY
				$expense_category_info["account_entries_by_category"] = $expense_account_entries;
				$expense_category_infos[] = $expense_category_info;
			}
			
			$data['expense_account_entries'] = $expense_account_entries;
			$data['revenue_account_entries'] = $revenue_account_entries;
			$data['expense_category_infos'] = $expense_category_infos;
			$data['revenue_category_infos'] = $revenue_category_infos;
			$data['revenue_total'] = $revenue_total;
			$data['expense_total'] = $expense_total;
			$data['start_date'] = $start_date;
			$data['end_date'] = $end_date;
			$data['business_user'] = $business_user;
			$this->load->view("reports/income_statement_report.php",$data);
			
			//echo time();
		}
		else
		{
			echo "<div id='main_content_header'>
				<div id='plain_header' style='font-size:16px;'>
					<div style='float:right; width:25px;'>
						<img id='filter_loading_icon' name='filter_loading_icon' src='/images/loading.gif' style='float:right; height:20px; padding-top:5px; display:none;' />
						<img id='refresh_statement' name='refresh_statement' src='/images/refresh.png' title='Refresh Income Statement' style='cursor:pointer; float:right; height:20px; padding-top:5px;' onclick='load_income_statement()' />
					</div>
					<div style='float:left; font-weight:bold;'>Income Statement</div>
				</div>
			</div>
			</div><br><br><div style='margin-left:15px;color:red'>You don't have permission to view this report.</div>";
		}
	}
	
	//LOAD PRINTABLE FUNDING REPORT
	function load_funding_report_printable()
	{
		//echo $print;
		//GET FILTER PARAMETERS
		$broker_dropdown = $_POST["print_broker_dropdown"];
		$fleet_manager_dropdown = $_POST["print_fleet_managers_dropdown"];
		$funding_status_dropdown = $_POST["print_funding_status_dropdown"];
		$drop_start_date = $_POST["print_drop_start_date_filter"];
		$drop_end_date = $_POST["print_drop_end_date_filter"];
		$billing_start_date = $_POST["print_billing_start_date_filter"];
		$billing_end_date = $_POST["print_billing_end_date_filter"];
		$funding_start_date = $_POST["print_funding_start_date_filter"];
		$funding_end_date = $_POST["print_funding_end_date_filter"];
		$get_factors = $_POST["print_get_factors"];
		$get_direct_bills = $_POST["print_get_direct_bills"];
		
		//echo $broker_dropdown."<br>";
		//echo $billing_end_date."<br>";
		//echo $funding_start_date."<br>";
		//echo $funding_end_date."<br>";
		
		
		//CREATE WHERE CLAUSE FOR LOAD QUERY
		$where = " AND status = 'Dropped' ";
		
		//SET WHERE FOR BROKER
		if($broker_dropdown != "All")
		{
			$where = $where." AND broker_id = ".$broker_dropdown;
		}
		
		//SET WHERE FOR FM
		if($fleet_manager_dropdown != "All")
		{
			$where = $where." AND fleet_manager_id = ".$fleet_manager_dropdown;
		}
		
		//SET WHERE FOR FUNDING STATUS
		if($funding_status_dropdown != "All")
		{
			if($funding_status_dropdown == "Funded")
			{
				$where = $where." AND  amount_funded IS NOT NULL ";
			}
			else
			{
				$where = $where." AND  (amount_funded IS NULL AND billing_status_number <> 8) ";
			}
		}
		
		//SET WHERE FOR DROP START DATE
		if(!empty($drop_start_date))
		{
			$drop_start_date = date("Y-m-d G:i:s",strtotime($drop_start_date));
			$where = $where." AND final_drop_datetime > '".$drop_start_date."' ";
		}
		
		//SET WHERE FOR DROP END DATE
		if(!empty($drop_end_date))
		{
			$drop_end_date = date("Y-m-d G:i:s",strtotime($drop_end_date)+24*60*60);
			$where = $where." AND final_drop_datetime < '".$drop_end_date."' ";
		}
		
		//SET WHERE FOR BILLING START DATE
		if(!empty($billing_start_date))
		{
			$billing_start_date = date("Y-m-d G:i:s",strtotime($billing_start_date));
			$where = $where." AND billing_datetime > '".$billing_start_date."' ";
		}
		
		//SET WHERE FOR BILLING END DATE
		if(!empty($billing_end_date))
		{
			$billing_end_date = date("Y-m-d G:i:s",strtotime($billing_end_date)+24*60*60);
			$where = $where." AND billing_datetime < '".$billing_end_date."' ";
		}
		
		//SET WHERE FOR FUNDED START DATE
		if(!empty($funding_start_date))
		{
			$funding_start_date = date("Y-m-d G:i:s",strtotime($funding_start_date));
			$where = $where." AND funded_datetime > '".$funding_start_date."' ";
		}
		
		//SET WHERE FOR FUNDED END DATE
		if(!empty($funding_end_date))
		{
			$funding_end_date = date("Y-m-d G:i:s",strtotime($funding_end_date)+24*60*60);
			$where = $where." AND funded_datetime < '".$funding_end_date."' ";
		}
		
		//IF GET ANY CHECK BOX IS UNCHECKED
		if
			(
				$get_factors == "false" || 
				$get_direct_bills == "false"
			)
		{
			$an_event_is_selected = false;
			$where = $where." AND (";
			
			if($get_factors == "true")
			{
				$where = $where."billing_method = 'Factor' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_direct_bills == "true")
			{
				$where = $where."billing_method = 'Direct Bill' OR ";
				$an_event_is_selected = true;
			}
			
			if($an_event_is_selected)
			{
				$where = substr($where,0,-4).") ";//this takes away the extra " OR "
			}
			else
			{
				$where = "     billing_method = 'none'"; //ADDS SPACES TO WORK WITH substr()
			}
		}
		
		$where = substr($where,4);
		//echo $where;
		$loads = db_select_loads($where,"final_drop_datetime ");
		
		$data['loads'] = $loads;
		$this->load->view('reports/funding_report_printable',$data);
	
	}
	
	//LOAD MISSING PAPERWORK REPORT
	function load_missing_paperwork_report()
	{
		$fm_id = $_POST["fleet_managers_dropdown"];
		$client_id = $_POST["driver_dropdown"];
		$digital_status = $_POST["digital_status_dropdown"];
		$hc_status = $_POST["hc_status_dropdown"];
		
		//GET LOADS
		$where = " AND status_number = 7 ";
		
		//WHERE FOR FM
		if($fm_id != "All")
		{
			$where = $where." AND fleet_manager_id = $fm_id ";
		}
		
		//WHERE FOR CLIENT
		if($client_id != "All")
		{
			$where = $where." AND client_id = $client_id ";
		}
		
		//WHERE FOR DIGITAL STATUS
		if($digital_status != "All")
		{
			if($digital_status == "Missing")
			{
				$where = $where." AND digital_received_datetime IS NULL ";
			}
			else if($digital_status == "Received")
			{
				$where = $where." AND digital_received_datetime IS NOT NULL ";
			}
		}
		
		//WHERE FOR DIGITAL STATUS
		if($hc_status != "All")
		{
			if($hc_status == "Missing")
			{
				$where = $where." AND hc_processed_datetime IS NULL ";
			}
			else if($hc_status == "Received")
			{
				$where = $where." AND hc_processed_datetime IS NOT NULL ";
			}
		}
		
		$where = substr($where,4);
		//echo $where;
		$loads = db_select_loads($where,"final_drop_datetime",100);
		
		$data['loads'] = $loads;
		$this->load->view('reports/missing_paperwork/missing_paperwork_report',$data);
	
	}
	
	function load_leg_report()
	{
		$this->load->view("reports/leg_report.php",$data);
	}
	
	function download_dtr_csv()
	{
		$after_date = date('Y-m-d',strtotime($_POST['start_date_filter']));
		$before_date = date('Y-m-d',strtotime($_POST['end_date_filter'].' + 1 days'));
		
		$sql = "SELECT
		log_entry.id AS id,
		log_entry.entry_datetime AS datetime,
		truck.truck_number AS unit,
		log_entry.odometer,
		entry_notes AS truck_stop,
		address AS address,
		city AS city,
		state AS state,
		entry_type AS fuel_type,
		gallons AS gallons,
		fuel_price AS price_per_gallon,
		fuel_expense AS total_invoice,
		route
		FROM `log_entry`
		LEFT JOIN fuel_stop ON log_entry.id = fuel_stop.log_entry_id
		LEFT JOIN truck ON log_entry.truck_id = truck.id
		WHERE 
		(entry_type = 'Fuel Fill' OR entry_type = 'Fuel Partial' OR entry_type = 'Fuel Reefer')
		AND log_entry.entry_datetime >= '".$after_date."'
		AND log_entry.entry_datetime <= '".$before_date."'
		ORDER BY log_entry.entry_datetime";
		//echo $sql;
		
		$query = $this->db->query($sql);
		$log_entries = null;
		$formatted_before_date = date('Y-m-d',strtotime($before_date));
		foreach($query->result() as $row)
		{
			$log_entry = null;
			$log_entry['id'] = $row->id;
			$log_entry['datetime'] = $row->datetime;
			$log_entry['unit'] = $row->unit;
			$log_entry['odometer'] = $row->odometer;
			$log_entry['truck_stop'] = $row->truck_stop;
			$log_entry['address'] = $row->address;
			$log_entry['city'] = $row->city;
			$log_entry['state'] = $row->state;
			$log_entry['fuel_type'] = $row->fuel_type;
			$log_entry['gallons'] = $row->gallons;
			$log_entry['price_per_gallon'] = $row->price_per_gallon;
			$log_entry['total_invoice'] = $row->total_invoice;
			$log_entry['route'] = $row->route;
			
			$log_entries[] = $log_entry;
		}
		
		if(!empty($log_entries))
		{
			header('Content-Type: text/csv');
			header("Content-Disposition: attachment; filename='DTR Report $after_date - $formatted_before_date.csv'");

			$output = fopen('php://output','w');
			
			$header_array = array();
			$header_array[] = "Log Entry ID";
			$header_array[] = "Datetime";
			$header_array[] = "Unit";
			$header_array[] = "Odometer";
			$header_array[] = "Truck Stop";
			$header_array[] = "Address";
			$header_array[] = "City";
			$header_array[] = "State";
			$header_array[] = "Fuel Type";
			$header_array[] = "Gallons";
			$header_array[] = "Price Per Gallon";
			$header_array[] = "Total Invoice";
			$header_array[] = "Route";
			
			fputcsv($output,$header_array);
			
			foreach($log_entries as $row)
			{
				//print_r($row);
				fputcsv($output,$row);
			}
			fclose($output);
			// echo "Downloading...";
		}
		else
		{
			echo "No data in given range.";
		}
	}
	
	function download_leg_csv()
	{
		$after_date = date('Y-m-d',strtotime($_POST['start_date_filter']));
		$before_date = date('Y-m-d',strtotime($_POST['end_date_filter'].' + 1 days'));
		
		$sql = "SELECT 
				leg.id as id,
				allocated_load.customer_load_number AS allocated_load_number,
				allocated_load.natl_fuel_avg AS allocated_load_natl_fuel_avg,
				company.company_side_bar_name,
				truck.truck_number,
				trailer.trailer_number,
				main_driver.client_nickname as main_driver_nickname ,
				codriver.client_nickname as codriver_nickname ,
				leg.rate_type,
				leg.revenue_rate,
				leg.odometer_miles,
				leg.map_miles,
				leg.hours,
				leg.fuel_expense,
				leg.reefer_fuel_expense,
				leg.truck_rental_expense,
				leg.truck_mileage_expense,
				leg.trailer_rental_expense,
				leg.trailer_mileage_expense,
				leg.insurance_expense,
				leg.factoring_expense,
				leg.bad_debt_expense,
				leg.damage_expense,
				leg.gallons_used,
				leg.reefer_gallons_used,
				log_entry.entry_datetime,
				log_entry.locked_datetime as locked_datetime
				FROM `leg`
				LEFT JOIN log_entry ON leg.log_entry_id = log_entry.id
				LEFT JOIN company ON leg.fm_id = company.id
				LEFT JOIN person ON leg.approved_by_id = person.id 
				LEFT JOIN `load` ON  leg.`load_id` =  `load`.id
				LEFT JOIN `load` AS allocated_load ON leg.allocated_load_id =  allocated_load.id
				LEFT JOIN truck ON leg.truck_id = truck.id 
				LEFT JOIN trailer ON leg.trailer_id = trailer.id 
				LEFT JOIN client as main_driver ON leg.main_driver_id = main_driver.id 
				LEFT JOIN client as codriver ON leg.codriver_id = codriver.id
				WHERE entry_datetime >= '".$after_date."' AND entry_datetime < '".$before_date."'";
		//echo $sql;
		$query = $this->db->query($sql);
		$legs = null;
		$formatted_before_date = date('Y-m-d',strtotime($before_date));
		foreach($query->result() as $row)
		{
			$leg = null;
			$leg['id'] = $row->id;
			$leg['allocated_load_number'] = $row->allocated_load_number;
			$leg['allocated_load_natl_fuel_avg'] = $row->allocated_load_natl_fuel_avg;
			$leg['company_side_bar_name'] = $row->company_side_bar_name;
			$leg['truck_number'] = $row->truck_number;
			$leg['trailer_number'] = $row->trailer_number;
			$leg['main_driver_nickname'] = $row->main_driver_nickname;
			$leg['codriver_nickname'] = $row->codriver_nickname;
			$leg['rate_type'] = $row->rate_type;
			$leg['revenue_rate'] = $row->revenue_rate;
			$leg['odometer_miles'] = $row->odometer_miles;
			$leg['map_miles'] = $row->map_miles;
			$leg['hours'] = $row->hours;
			$leg['fuel_expense'] = $row->fuel_expense;
			$leg['reefer_fuel_expense'] = $row->reefer_fuel_expense;
			$leg['truck_rental_expense'] = $row->truck_rental_expense;
			$leg['truck_mileage_expense'] = $row->truck_mileage_expense;
			$leg['trailer_rental_expense'] = $row->trailer_rental_expense;
			$leg['trailer_mileage_expense'] = $row->trailer_mileage_expense;
			$leg['insurance_expense'] = $row->insurance_expense;
			$leg['factoring_expense'] = $row->factoring_expense;
			$leg['bad_debt_expense'] = $row->bad_debt_expense;
			$leg['damage_expense'] = $row->damage_expense;
			$leg['gallons_used'] = $row->gallons_used;
			$leg['reefer_gallons_used'] = $row->reefer_gallons_used;
			$leg['entry_datetime'] = $row->entry_datetime;
			$leg['locked_datetime'] = $row->locked_datetime;
			
			$legs[] = $leg;
		}
		
		if(!empty($legs))
		{
			header('Content-Type: text/csv');
			header("Content-Disposition: attachment; filename='End Leg $after_date - $formatted_before_date.csv'");

			$output = fopen('php://output','w');
			
			$header_array = array();
			$header_array[] = "Leg ID";
			$header_array[] = "Load Number";
			$header_array[] = "National Fuel Average";
			$header_array[] = "Company";
			$header_array[] = "Truck Number";
			$header_array[] = "Trailer Number";
			$header_array[] = "Main Driver";
			$header_array[] = "Codriver";
			$header_array[] = "Rate Type";
			$header_array[] = "Revenue Rate";
			$header_array[] = "Odometer Miles";
			$header_array[] = "Map Miles";
			$header_array[] = "Hours";
			$header_array[] = "Fuel Expense";
			$header_array[] = "Reefer Fuel Expense";
			$header_array[] = "Truck Rental Expense";
			$header_array[] = "Truck Mileage Expense";
			$header_array[] = "Trailer Rental Expense";
			$header_array[] = "Trailer Mileage Expense";
			$header_array[] = "Insurance Expense";
			$header_array[] = "Factoring Expense";
			$header_array[] = "Bad Debt Expense";
			$header_array[] = "Damage Expense";
			$header_array[] = "Gallons Used";
			$header_array[] = "Reefer Gallons Used";
			$header_array[] = "Entry Datetime";
			$header_array[] = "Locked Datetime";
			
			fputcsv($output,$header_array);
			
			foreach($legs as $row)
			{
				//print_r($row);
				fputcsv($output,$row);
			}
			fclose($output);
			// echo "Downloading...";
		}
		else
		{
			echo "No data in given range.";
		}
	}
	
	function download_transactions_csv()
	{
		$after_date = date('Y-m-d',strtotime($_POST['start_date_filter']));
		$before_date = date('Y-m-d',strtotime($_POST['end_date_filter'].' + 1 days'));
		
		$sql = "SELECT
				expense_datetime,
				company_side_bar_name,
				account_name,
				expense.category AS category,
				description,
				debit_credit,
				expense_amount
				FROM `expense`,account,company
				WHERE expense.expense_account_id = account.id
				AND account.company_id = company.id
				AND expense_datetime >= '".$after_date."' AND expense_datetime < '".$before_date."'";
		//echo $sql;
		$query = $this->db->query($sql);
		$transactions = null;
		$formatted_before_date = date('Y-m-d',strtotime($before_date));
		foreach($query->result() as $row)
		{
			$transaction = null;
			$transaction['expense_datetime'] = $row->expense_datetime;
			$transaction['company_side_bar_name'] = $row->company_side_bar_name;
			$transaction['account_name'] = $row->account_name;
			$transaction['category'] = $row->category;
			$transaction['description'] = $row->description;
			$transaction['debit_credit'] = $row->debit_credit;
			$transaction['expense_amount'] = $row->expense_amount;
			
			$transactions[] = $transaction;
		}
		
		if(!empty($transactions))
		{
			header('Content-Type: text/csv');
			header("Content-Disposition: attachment; filename='Transactions $after_date - $formatted_before_date.csv'");

			$output = fopen('php://output','w');
			
			$header_array = array();
			$header_array[] = "Expense Datetime";
			$header_array[] = "Company";
			$header_array[] = "Account";
			$header_array[] = "Category";
			$header_array[] = "Description";
			$header_array[] = "Debit or Credit";
			$header_array[] = "Amount";
			
			fputcsv($output,$header_array);
			
			foreach($transactions as $row)
			{
				//print_r($row);
				fputcsv($output,$row);
			}
			fclose($output);
			// echo "Downloading...";
		}
		else
		{
			echo "No data in given range.";
		}
	}
	
	//LOAD REIMBURSEMENT REPORT
	function load_reimbursement_report()
	{
		//echo " load_reimbursement_report ";
		
		//GET CLIENT ID
		$company_id = $_POST["driver_filter_dropdown"];
		if($_POST["driver_filter_dropdown"] != 'All')
		{
			//GET CLIENT
			$where = null;
			$where["company_id"] = $company_id;
			$client = db_select_client($where);
			
			//scan_for_old_receipts($client["id"]);
		}
		
		//GET BAHA ACCOUNT FOR
		$where = null;
		$where["company_id"] = $company_id;
		$where["category"] = "BAHA";
		$account = db_select_account($where);
		
		//SET SQL FOR ALL DRIVERS
		$sql = " account_id = ".$account["id"]." AND";
		if($company_id == "All")
		{
			$sql = " ";
		}
	
		//GET LIST OF ADVANCES TO MATCH EXPENSE
		$where = null;
		$where = $sql."  is_reimbursable = 'Yes' AND reimbursement_datetime IS NULL AND entry_datetime > '2013-10-19' ";
		$unfunded_advances = db_select_account_entrys($where);
		
		$total = 0;
		if(!empty($unfunded_advances))
		{
			foreach($unfunded_advances as $advance)
			{
				$total = $total + $advance["entry_amount"];
			}
		}
		
		$data['total'] = $total;
		$data['unfunded_advances'] = $unfunded_advances;
		$this->load->view('reports/reimbursement_report',$data);
		
	}
	
	//LOAD DRIVER ACCOUNTS
	function load_driver_accounts()
	{
		
		//GET CLIENT ID
		$fm_id = $_POST["fleet_managers_dropdown"];
		
		//GET FLEET MANAGER
		$where = null;
		$where["id"] = $fm_id;
		$fleet_manager = db_select_person($where);
		
		//GET ALL CLIENTS FOR THIS FM
		$where = null;
		if($fm_id == 'All')
		{
			$where = " (client_type = 'Main Driver' OR client_type = 'Co-Driver') ";
			$clients = db_select_clients($where,"client_nickname");
		}
		else if($fm_id == 'Select')
		{
			$where = " 1 = 2 ";
			$clients = db_select_clients($where,"client_nickname");
		}
		else
		{
			$where = " fleet_manager_id = ".$fm_id." AND (client_type = 'Main Driver' OR client_type = 'Co-Driver') AND client_status = 'Active' ";
			$clients = db_select_clients($where,"client_nickname");
		}
		
		$total_pay = 0;
		$total_baha = 0;
		$total_de = 0;
		$total_damage = 0;
		$total_reserve = 0;
		$total_total= 0;
		$driver_account_balances = array();
		//GET CLIENTS ACCOUNTS AND STORE BALANCES IN ARRAY
		foreach($clients as $client)
		{
			//scan_for_old_receipts($client["id"]);
		
			$driver_account_balance["client_nickname"] = $client["client_nickname"];
			
			//GET BALANCE FOR PAY ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["category"] = "Pay";
			$pay_account = db_select_account($where);
			$driver_account_balance["pay_balance"] = get_account_balance($pay_account["id"]);
			$driver_account_balance["pay_account_id"] = $pay_account["id"];
			$total_pay = $total_pay + $driver_account_balance["pay_balance"];
			
			//GET BALANCE FOR BAHA ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["category"] = "BAHA";
			$baha_account = db_select_account($where);
			$driver_account_balance["baha_balance"] = get_account_balance($baha_account["id"]);
			$driver_account_balance["baha_account_id"] = $baha_account["id"];
			$total_baha = $total_baha + $driver_account_balance["baha_balance"];
			
			//GET BALANCE FOR DRIVER EQUIPMENT ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["category"] = "Driver Equipment";
			$de_account = db_select_account($where);
			$driver_account_balance["de_balance"] = get_account_balance($de_account["id"]);
			$driver_account_balance["de_account_id"] = $de_account["id"];
			$total_de = $total_de + $driver_account_balance["de_balance"];
			
			//GET BALANCE FOR DAMAGE ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["category"] = "Client Damage";
			$damage_account = db_select_account($where);
			$driver_account_balance["damage_balance"] = get_account_balance($damage_account["id"]);
			$driver_account_balance["damage_account_id"] = $damage_account["id"];
			$total_damage = $total_damage + $driver_account_balance["damage_balance"];
			
			//GET BALANCE FOR RESERVE ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["category"] = "Reserve";
			$reserve_account = db_select_account($where);
			$driver_account_balance["reserve_balance"] = get_account_balance($reserve_account["id"]);
			$driver_account_balance["reserve_account_id"] = $reserve_account["id"];
			$total_reserve = $total_reserve + $driver_account_balance["reserve_balance"];
			
			$driver_account_balance["total_balance"] = round($driver_account_balance["pay_balance"] + $driver_account_balance["damage_balance"] + $driver_account_balance["reserve_balance"],2);
			$total_total = round($total_total + $driver_account_balance["total_balance"],2);
			
			//ADD BALANCE INFO TO ARRAY
			$driver_account_balances[] = $driver_account_balance;
		}
		
		$data['total_pay'] = $total_pay;
		$data['total_baha'] = $total_baha;
		$data['total_de'] = $total_de;
		$data['total_damage'] = $total_damage;
		$data['total_reserve'] = $total_reserve;
		$data['total_total'] = $total_total;
		$data['fleet_manager'] = $fleet_manager;
		$data['driver_account_balances'] = $driver_account_balances;
		$this->load->view('reports/driver_accounts',$data);
	}
	
	//LOAD DEDUCTION REPORT
	function load_deduction_report()
	{
		$fm_company_id = $_POST["fleet_managers_dropdown"];
		$after_date = $_POST["start_date_filter"];
		$before_date = $_POST["end_date_filter"];
		
		//GET FM PROFIT ACCOUNT
		$where = null;
		$where["company_id"] = $fm_company_id;
		$where["account_type"] = "Fleet Manager";
		$where["category"] = "Profit";
		$fm_profit_account = db_select_account($where);
		
		//GET DEDUCTION ENTRIES
		$where = " entry_type = 'Funding Deduction' ";
		
		//GET ACCOUNT ENTRIES FOR THESE PARAMETERS THAT ARE TYPE = EXPENSE
		if(!empty($fm_profit_account))
		{
			$where = $where." AND account_id = ".$fm_profit_account["id"];
		}
		
		//SET AFTER DATE
		if(!empty($after_date))
		{
			$where = $where." AND entry_datetime > '".date("Y-m-d G:i:s",strtotime($after_date))."'";
		}
		
		//SET BEFORE DATE
		if(!empty($before_date))
		{
			$where = $where." AND entry_datetime < '".date("Y-m-d G:i:s",strtotime($before_date)+24*60*60)."'";
		}
		
		//echo $where;
		
		$account_entries = db_select_account_entrys($where,"entry_datetime DESC");
		
		$data['account_entries'] = $account_entries;
		$this->load->view('reports/deduction_report',$data);
	}
	
	//LOAD FM EXPENSE REPORT
	function load_fm_expense_report()
	{
		$fm_company_id = $_POST["fleet_managers_dropdown"];
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		
		//GET FM COMPANY
		$where = null;
		$where["id"] = $fm_company_id;
		$fm_company = db_select_company($where);
		
		
		//GET FM PROFIT STATS FOR GIVEN FM AND DATES (dates are inclusive)
		$fm_profit_stats = get_fm_profit_stats($fm_company_id,$start_date,$end_date);
		
		$start_date = date("Y-m-d G:i:s",strtotime($start_date));
		$end_date = date("Y-m-d G:i:s",strtotime($end_date)+24*60*60);
		//GET ALL NON-STANDARD EXPENSES
		$where = null;
		$where = "	company_id = ".$fm_company["id"]." 
					AND expense_type = 'Expense'
					AND expense_datetime >= '".$start_date."'
					AND expense_datetime < '".$end_date."'
					AND 
					(
						(
							category <> 'Personal Advance' 
							AND category <> 'Settlement Pay' 
							AND category <> 'Kick In'
						)
						OR 
						(
							category IS NULL
						)
					)";
					
		//echo 
		$non_standard_expenses = db_select_expenses($where,"expense_datetime");
		
		$data['non_standard_expenses'] = $non_standard_expenses;
		$data['start_date'] = date("m/d/y",strtotime($start_date));
		$data['end_date'] = date("m/d/y",strtotime($end_date));;
		$data['fm_company'] = $fm_company;
		$data['fm_profit_stats'] = $fm_profit_stats;
		$this->load->view('reports/fm_expenses_report',$data);
		//$this->load->view('reports/pie_chart',$data);
	}
	
	//LOAD FM EXPENSE REPORT
	function load_expense_report()
	{
		$owner_id = $_POST["owner_dropdown"];
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		
		//GET OWNER
		$where = null;
		$where["id"] = $owner_id;
		$owner = db_select_company($where);
		
		//VALUES ARRAY
		$values = array();
		
		//GET ALL NON-STANDARD EXPENSES
		$sql = "	SELECT category,sum(expense_amount) AS expense_total ,count(*) AS expense_count 
					FROM `expense` 
					WHERE expense_type = 'Expense' ";
		
		if(!empty($start_date))
		{
			$start_date = date("Y-m-d G:i:s",strtotime($start_date));
			$sql = $sql." AND expense_datetime >= ? ";
			$values[] = $start_date;
		}
		
		if(!empty($end_date))
		{
			$end_date = date("Y-m-d G:i:s",strtotime($end_date)+24*60*60);
			$sql = $sql." AND expense_datetime < ? ";
			$values[] = $end_date;
		}

		if($owner_id != 'Select')
		{
			$sql = $sql." AND company_id = ? ";
			$values[] = $owner_id;
		}
					
		$sql = $sql." GROUP BY category ";
		//echo $sql;
					
		//RUN QUERY
		$query = $this->db->query($sql,$values);
		$expense_categories = array();
		foreach ($query->result() as $row)
		{
			$expense_category['category'] = $row->category;
			$expense_category['expense_total'] = $row->expense_total;
			$expense_category['expense_count'] = $row->expense_count;

			$expense_categories[] = $expense_category;
			
		}// end foreach
		
		$data['expense_categories'] = $expense_categories;
		$data['start_date'] = date("m/d/y",strtotime($start_date));
		$data['end_date'] = date("m/d/y",strtotime($end_date));
		$data['owner'] = $owner;
		
		//print_r($expense_categories);
		$this->load->view('reports/expenses_report',$data);
		//$this->load->view('reports/pie_chart',$data);
	}

	//SCRIPT TO DISPLAY REVENUES BY LEG
	function load_financial_report()
	{
		
		$start_date = date("Y-m-d G:i:s",strtotime($_POST["start_date_filter"]));
		$end_date = date("Y-m-d G:i:s",strtotime($_POST["end_date_filter"]));
		/*
		$CI =& get_instance();
		
		//GET ALL LEGS,LOG ENTRIES BETWEEN DATES
		$sql = "SELECT
				*
				FROM `leg`,log_entry
				WHERE `leg`.log_entry_id = log_entry.id
				AND leg.allocated_load_id IS NOT NULL
				AND log_entry.entry_datetime >= '2014-04-19 00:00:00' 
				AND log_entry.entry_datetime < '2014-04-26 00:00:00'";
		$query_user = $CI->db->query($sql);
		
		$main_leg = array();
		$main_legs = array();
		foreach ($query_user->result() as $row)
		{
			$main_leg['id'] = $row->id;
			$main_leg['allocated_load_id'] = $row->allocated_load_id;
			
			$main_legs[] = $main_leg;
		}
		
		$total_revenue = 0;
		
		//FOR EACH LEG
		foreach($main_legs as $main_leg)
		{
			//GET LOAD
			$where = null;
			$where["id"] = $main_leg["allocated_load_id"];
			$load = db_select_load($where);
			
			//GET ALL LEGS ALLOCATED TO LOAD
			$where = null;
			$where["allocated_load_id"] = $main_leg["allocated_load_id"];
			$legs = db_select_legs($where);
			
			//FOREACH LEGS
			$total_miles = 0;
			foreach($legs as $leg)
			{
				//ADD UP TOTAL MILES FOR LOAD
				$total_miles = $total_miles + $leg["map_miles"];
				
			}
			
			//CALULATE RATE FOR LOAD
			$rate = ($load["amount_funded"]+$load["financing_cost"])/$total_miles;
			
			//MULTIPLY RATE BY MAP MILES FOR REVENUE
			$revenue = $rate * $leg["map_miles"];
			
			//ADD REVENUE TO TOTAL REVENUE
			$total_revenue = $total_revenue + $revenue;
			
		}
			
		echo $total_revenue;
		*/
		
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$this->load->view('reports/get_revenues_view',$data);
	}
	
	//LOAD ARROWHEAD EXPENSE REPORT
	function load_arrowhead_expense_report()
	{
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		
		
		//GET FM PROFIT STATS FOR GIVEN FM AND DATES (dates are inclusive)
		$arrowhead_stats = get_arrowhead_stats($start_date,$end_date);
		
		
		$data['start_date'] = date("m/d/y",strtotime($start_date));
		$data['end_date'] = date("m/d/y",strtotime($end_date));;
		$data['arrowhead_stats'] = $arrowhead_stats;
		$this->load->view('reports/arrowhead_expense_report',$data);
		
	}
	
	function load_time_and_attendance_report()
	{
		$user_id = $_POST["user_sidebar_options"];
		$after_date = $_POST["start_date_filter"];
		$before_date = $_POST["end_date_filter"];
		
		$where = "1 = 1 ";
		
		//WHERE FOR USER
		if($user_id != "All")
		{
			$where = $where." AND user_id = $user_id ";
		}
		
		//SET AFTER DATE
		if(!empty($after_date))
		{
			$where = $where." AND datetime > '".date("Y-m-d G:i:s",strtotime($after_date))."'";
		}
		
		//SET BEFORE DATE
		if(!empty($before_date))
		{
			$where = $where." AND datetime < '".date("Y-m-d G:i:s",strtotime($before_date)+24*60*60)."'";
		}
		
		//GET PUNCHES
		$punches = db_select_time_punchs($where,"datetime DESC");
		
		$data['punches'] = $punches;
		$this->load->view('reports/time_and_attendance_report',$data);
	}
	
	function load_fleetprotect_account_report()
	{
		$driver_company_id = $_POST["driver_company_id"];
		
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		//GET RELATIONSHIP BETWEEN COOP AND DRIVER COMPANY
		$where = null;
		$where["business_id"] = $coop_company["id"];
		$where["related_business_id"] = $driver_company_id;
		$coop_member_relationship = db_select_business_relationship($where);
		
		//GET ACCOUNT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["relationship_id"] = $coop_member_relationship["id"];
		$where["account_type"] = "Member";
		$where["account_class"] = "Asset";
		$where["category"] = "FleetProtect";
		$account = db_select_account($where);
		
		//GET ACCOUNT ENTRIES
		$where = null;
		$where["account_id"] = $account["id"];
		$account_entries = db_select_account_entrys($where,"id DESC");
		
		$data['account'] = $account;
		$data['company'] = $coop_company;
		$data['account_entries'] = $account_entries;
		$this->load->view('accounts/account_details',$data);
		
		echo '	<script>
					$(document).ready(function()
					{
						$("#back_icon").hide();
						$("#refresh_logs").hide();
						
					});
				</script>';
	}
	
	function load_driver_hold_report()
	{
		$client_id = $_POST["clients_dropdown_options"];
		
		$hold_report = get_hold_report($client_id);
		
		$data['hold_report'] = $hold_report;
		$this->load->view('reports/driver_hold_report',$data);
	}
	
	
	
}