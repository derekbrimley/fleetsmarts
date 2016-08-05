<?php		


	
class Settlements extends MY_Controller 
{

	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		
		//GET ALL ACTIVE MAIN DRIVERS
		$where = null;
		$where["client_status"] = "Active";
		$main_drivers = db_select_clients($where,"client_nickname");
		
		$main_driver_dropdown_options = array();
		$main_driver_dropdown_options["All"] = "All Clients";
		foreach($main_drivers as $main_driver)
		{
			$main_driver_dropdown_options[$main_driver["id"]] = $main_driver["client_nickname"];
		}
		
		//GET OPTIONS FOR FLEET MANAGER DROPDOWN LIST
		$where = null;
		$where['role'] = "Fleet Manager";
		$fleet_managers = db_select_persons($where);
		
		$fleet_manager_dropdown_options = array();
		$fleet_manager_dropdown_options['All'] = "All Fleet Managers";
		foreach ($fleet_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$fleet_manager_dropdown_options[$manager['id']] = $title;
		}

		
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$data['main_driver_dropdown_options'] = $main_driver_dropdown_options;
		$data['tab'] = 'Statements';
		$data['title'] = "Statements";
		$this->load->view('settlements_view',$data);
		
	}// end index()

	//LOAD LOG
	function load_list()
	{
		
		//GET FILTER PARAMETERS
		$fm_id = (int)$_POST["fm_filter_dropdown"];
		$client_id = (int)$_POST["client_filter_dropdown"];
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		$get_pending_kick_in =  $_POST["get_pending_kick_in"];
		$get_pending_approval =  $_POST["get_pending_approval"];
		$get_pending_settlement =  $_POST["get_pending_settlement"];
		$get_closed =  $_POST["get_closed"];
		
		
		//GET SETTLEMENTS
		//$where = " AND locked_datetime IS NOT NULL ";
		$where = " AND 1 = 1 ";
		
		//FLEET MANAGER FILTER
		if($fm_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($fm_id))
			{
				$where = $where." AND fm_id = ".$fm_id;
			}
		}
		
		//CLIENT FILTER
		if($client_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($client_id))
			{
				$where = $where." AND client_id = ".$client_id;
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
		
		
		//CREATE EVENT FILTER SQL
		if
			(
				$get_pending_kick_in == "false" || 
				$get_pending_approval == "false" ||
				$get_pending_settlement == "false" ||
				$get_closed == "false" 
			)
		{
		
			$an_event_is_selected = false;
			$where = $where." AND (";
			
			if($get_pending_kick_in == "true")
			{
				$where = $where."(kick_in IS NULL) OR ";
				$an_event_is_selected = true;
			}
			
			if($get_pending_approval == "true")
			{
				$where = $where."(kick_in IS NOT NULL AND approved_datetime IS NULL) OR ";
				$an_event_is_selected = true;
			}
			
			if($get_pending_settlement == "true")
			{
				$where = $where."(kick_in IS NOT NULL AND approved_datetime IS NOT NULL AND settled_datetime IS NULL) OR ";
				$an_event_is_selected = true;
			}
			
			if($get_closed == "true")
			{
				$where = $where."(kick_in IS NOT NULL AND approved_datetime IS NOT NULL AND settled_datetime IS NOT NULL) OR ";
				$an_event_is_selected = true;
			}
			
			//PREPARE WHERE STRING
			if($an_event_is_selected)
			{
				$where = substr($where,0,-4).") ";//this takes away the extra " OR "
			}
			else
			{
				$where = substr($where,0,-5);//this take away the " AND ("
			}
			
			
		}
		
		//IF ALL EVENTS ARE UNSELECTED
		if
			(
				$get_pending_kick_in == "false" && 
				$get_pending_approval == "false" &&
				$get_closed == "false" 
			)
		{
			$where = " AND 1 = 2 ";
		}
		
		$where = substr($where,4);
		
		if(empty($where))
		{
			$where = " 1 = 1 "; //IF NOTHING IS FILTERED
		}
		
		//echo $where;
		$settlements = null;
		$settlements = db_select_settlements($where,"entry_datetime DESC");
		
		$data['settlements'] = $settlements;
		$this->load->view('settlements/settlements_div',$data);
		
	}//end load_log()

	//OPEN SETTLEMENT DETAILS
	function open_settlement_details()
	{
		$settlement_id = $_POST["settlement_id"];
		
		//GET SETTLEMENT
		$where = null;
		$where["id"] = $settlement_id;
		$settlement = db_select_settlement($where);
		
		
		
		//GET LOG ENTRY FOR END WEEK
		$where = null;
		$where["id"] = $settlement["end_week_id"];
		$log_entry = db_select_log_entry($where);
		
		$log_entry_id = $log_entry["id"];
		
		//WHY TWICE... 12 LINES DOWN
		//$stats = get_driver_end_week_stats($log_entry,$settlement["client_id"]);
		
		$driver_id = $settlement["client_id"];
		
		assign_client_expenses($log_entry_id,$driver_id);
		
		//GET DRIVER
		$where = null;
		$where["id"] = $driver_id;
		$driver = db_select_client($where);
		
		$stats = get_driver_end_week_stats($log_entry,$driver_id);
		
		if(empty($settlement["approved_datetime"]))
		{
			//PREPARE UPDATE SETTLMENET WITH NEW HTML AND RECALCULATED KICK IN
			$update_settlement["html"] = file_get_contents(base_url("index.php/public_functions/load_driver_settlement_view/$log_entry_id/$driver_id"));
			//$update_settlement["kick_in"] = $settlement["target_pay"] - $stats["statement_amount"];
			
			//DO UPDATE
			$where = null;
			$where["id"] = $settlement_id;
			db_update_settlement($update_settlement,$where);
			
			//GET SETTLEMENT
			$where = null;
			$where["id"] = $settlement_id;
			$settlement = db_select_settlement($where);
		}
		
		//GET ALL CHECK CALLS FOR THIS DRIVER ON THIS PAY PERIOD
		$where = null;
		$where = " (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime <= '".$log_entry["entry_datetime"]."'  AND entry_datetime >= '".$stats["previous_end_week_end_leg"]["entry_datetime"]."' AND entry_type = 'Check Call' ";
		$log_entries = db_select_log_entrys($where,'entry_datetime');
		
		//GET FM DROPDOWN OPTIONS
		$where = null;
		$where['role'] = "Fleet Manager";
		$fleet_managers = db_select_persons($where);
		
		$fleet_manager_dropdown_options = array();
		$fleet_manager_dropdown_options['Select'] = "Select";
		foreach ($fleet_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$fleet_manager_dropdown_options[$manager['id']] = $title;
		}
		
		$where = null;
		$where["settlement_id"] = $settlement["id"];
		$statement_credits = db_select_statement_credits($where);
		
		//GET COMPANY OPTIONS TO BE INVOICED FOR CREDITS
		$where = null;
		$where["category"] = "Vendor Credits";
		$vendor_credits_default_accounts = db_select_default_accounts($where);
		
		//CREATE DROPDOWN LIST OF BUSINESS USERS
		$business_users_options = array();
		$business_users_options["Select"] = "Select";
		foreach($vendor_credits_default_accounts as $default_account)
		{
			//GET ACCOUNT
			$where = null;
			$where["id"] = $default_account["account_id"];
			$vendor_credit_account = db_select_account($where);
			
			
			if($vendor_credit_account["relationship_id"] == 0)
			{
				//GET COMPANY
				$where = null;
				$where["id"] = $vendor_credit_account["company_id"];
				$vendor_credits_company = db_select_company($where);
				
				$title = $vendor_credits_company["company_side_bar_name"];
				$business_users_options[$vendor_credit_account["id"]] = $title;
			}
			else
			{
				
				//GET RELATIONSHIP
				$where = null;
				$where["id"] = $vendor_credit_account["relationship_id"];
				$vendor_credit_relationship = db_select_business_relationship($where);
				
				//GET COMPANY
				$where = null;
				$where["id"] = $vendor_credit_relationship["related_business_id"];
				$vendor_credits_company = db_select_company($where);
				
				$title = $vendor_credits_company["company_side_bar_name"];
				$business_users_options[$vendor_credit_account["id"]] = $title;
			}
			
			
		}
		
		
		$data = null;
		$data['business_users_options'] = $business_users_options;
		$data['statement_credits'] = $statement_credits;
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$data['log_entries'] = $log_entries;
		$data['settlement'] = $settlement;
		$data['settlement_id'] = $settlement_id;
		$data['log_entry'] = $log_entry;
		$data['stats'] = $stats;
		$this->load->view('settlements/settlement_details',$data);
	}
	
	//REFRESH ROW
	function refresh_row()
	{
		$settlement_id = $_POST["settlement_id"];
		
		//GET SETTLEMENT
		$where = null;
		$where["id"] = $settlement_id;
		$settlement = db_select_settlement($where);
		
		//GET LOG ENTRY FOR END WEEK
		$where = null;
		$where["id"] = $settlement["end_week_id"];
		$log_entry = db_select_log_entry($where);
		
		$stats = get_driver_end_week_stats($log_entry,$settlement["client_id"]);
		
		$data['settlement'] = $settlement;
		$data['log_entry'] = $log_entry;
		$data['stats'] = $stats;
		$this->load->view('settlements/settlement_row',$data);
	}
	
	//SAVE SETTLEMENT EDIT
	function save_settlement_edit()
	{
		//GET POST DATA
		$profit_share = $_POST["profit_share"];
		$kick_in = $_POST["kick_in"];;
		$target_pay = $_POST["target_pay"];
		$fm_id = $_POST["fm_dropdown"];
		$notes_to_driver = $_POST["notes_to_driver"];
		$settlement_id = $_POST["settlement_id"];
		
		// if($kick_in == 0)
		// {
			// $kick_in_db_value = 0.00;
		// }
		// else if($kick_in != "")
		// {
			// $kick_in_db_value = round($kick_in,2);
		// }
		// else
		// {
			// $kick_in_db_value = NULL;
		// }
		
		//echo $kick_in;
		
		//UPDATE SETTLEMENT
		$update["fm_id"] = $fm_id;
		$update["kick_in"] = $kick_in;
		$update["target_pay"] = round($target_pay,2);
		$update["notes_to_driver"] = $notes_to_driver;
		$update_settlement["approved_datetime"] = NULL;
		$update_settlement["approved_by"] = NULL;
		
		$where = null;
		$where["id"] = $settlement_id;
		db_update_settlement($update,$where);
		
	}
	
	//UNLOCK SETTLEMENT
	function unlock_settlement($settlement_id)
	{
	
		//GET SETTLEMENT
		$where = null;
		$where["id"] = $settlement_id;
		$settlement = db_select_settlement($where);
		
		//UPDATE SETTLEMENT
		$update["approved_datetime"] = null;
		$update["approved_by"] = null;
		
		$where = null;
		$where["id"] = $settlement_id;
		db_update_settlement($update,$where);
		
		//REMOVE PAID_DATETIME FROM CLIENT EXPENSES THAT ARE ASSIGNED TO STATEMENT
		$update = null;
		$update["paid_datetime"] = NULL;
		
		$where = null;
		$where["paid_datetime"] = $settlement["approved_datetime"];
		db_update_client_expense($update,$where);
		
	}
	
	//LOAD SETTLEMENT VIEW
	function load_driver_settlement_view($log_entry_id,$driver_id)
	{
		//RUN SCRIPT TO GRAB ALL UNASSIGNED CLIENT EXPENSES FOR THIS SETTLEMENT
		assign_client_expenses($log_entry_id,$driver_id);
	
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		//GET DRIVER
		$where = null;
		$where["id"] = $driver_id;
		$driver = db_select_client($where);
		
		//GET ALL LEGS FOR THIS SETTLEMENT
		$stats = get_driver_end_week_stats($log_entry,$driver_id);
		
		
		
		$data['title'] = "Statement ".date('m-d-y',strtotime($log_entry["entry_datetime"]))." ".$driver["client_nickname"];
		$data['stats'] = $stats;
		$data['driver'] = $driver;
		$data['log_entry'] = $log_entry;
		$data['log_entry'] = $log_entry;
		$this->load->view('settlements/driver_settlement_view',$data);
	}
	
	//APPROVE SETTLEMENT
	function approve_settlement()
	{
		if(user_has_permission("approve settlements"))
		{
			$recorder_id = $this->session->userdata('person_id');
			
			date_default_timezone_set('America/Denver');
			$entry_datetime = date("Y-m-d H:i:s");
			
			$settlement_id = $_POST["settlement_id"];
			
			//MARK SETTLEMENT APPROVED
			$update_settlement = null;
			$update_settlement["approved_datetime"] = $entry_datetime;
			$update_settlement["approved_by"] = $recorder_id;
			
			$where = null;
			$where["id"] = $settlement_id;
			db_update_settlement($update_settlement,$where);
		}
		
	}
	
	function settle_settlement()
	{
		$entries = array();
		$settlement_entries = array();
		
		
		date_default_timezone_set('America/Denver');
		$entry_datetime = date("Y-m-d H:i:s");
		$invoice_created_datetime = date("Y-m-d G:i:s");
		
		$recorder_id = $this->session->userdata('person_id');
		
		$settlement_id = $_POST["settlement_id"];
		
		//GET SETTLEMENT
		$where = null;
		$where["id"] = $settlement_id;
		$settlement = db_select_settlement($where);
		
		//GET DRIVER (CLIENT)
		$where = null;
		$where["id"] = $settlement["client_id"];
		$client = db_select_client($where);
		
		$driver_id = $client["id"];
		
		//GET DRIVER COMPANY
		$where = null;
		$where["id"] = $client["company_id"];
		$driver_company = db_select_company($where);
		
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		//GET LOG ENTRY FOR END WEEK
		$where = null;
		$where["id"] = $settlement["end_week_id"];
		$log_entry = db_select_log_entry($where);
		
		$stats = get_driver_end_week_stats($log_entry,$settlement["client_id"]);
		
		$drivers_share_of_fuel_exp = 0;
		$drivers_share_of_insurance_exp = 0;
		$drivers_share_of_truck_rental_exp = 0;
		$drivers_share_of_truck_maintenance_exp = 0;
		$drivers_share_of_trailer_rental_exp = 0;
		$drivers_share_of_trailer_maintenance_exp = 0;
		$drivers_share_of_deposit_alternative_exp = 0;
		$drivers_share_of_compliance_exp = 0;
		$drivers_share_of_authority_exp = 0;
		$drivers_share_of_membership_dues_exp = 0;
		$drivers_share_of_quick_pay_exp = 0;
		$drivers_share_of_hours = 0;
		$drivers_share_of_map_miles = 0;
		$drivers_share_of_odometer_miles = 0;
		$drivers_share_of_gallons = 0;
		$drivers_share_of_revenue = 0;
		//GET DRIVER'S SHARE OF TRUCK EXPENSES
		foreach($stats["leg_calcs"] as $leg_calc)
		{
			$leg = $leg_calc["leg"];
			//DETERMINE PERCENTAGE SPLIT
			if($leg["main_driver_id"] == $driver_id)
			{
				$profit_split = $leg["main_driver_split"]/100;
			}
			else if($leg["codriver_id"] == $driver_id)
			{
				$profit_split = $leg["codriver_split"]/100;
			}

			$drivers_share_of_fuel_exp = $drivers_share_of_fuel_exp + ($leg_calc["fuel_expense"]*$profit_split);
			$drivers_share_of_insurance_exp = $drivers_share_of_insurance_exp + ($leg_calc["insurance_expense"]*$profit_split);
			$drivers_share_of_truck_rental_exp = $drivers_share_of_truck_rental_exp + ($leg_calc["truck_rent"]*$profit_split);
			$drivers_share_of_truck_maintenance_exp = $drivers_share_of_truck_maintenance_exp + ($leg_calc["truck_mileage"]*$profit_split);
			$drivers_share_of_trailer_rental_exp = $drivers_share_of_trailer_rental_exp + ($leg_calc["trailer_rent"]*$profit_split);
			$drivers_share_of_trailer_maintenance_exp = $drivers_share_of_trailer_maintenance_exp + ($leg_calc["trailer_mileage"]*$profit_split);
			$drivers_share_of_deposit_alternative_exp = $drivers_share_of_deposit_alternative_exp + ($leg_calc["damage_expense"]*$profit_split);
			$drivers_share_of_compliance_exp = $drivers_share_of_compliance_exp + ($leg_calc["compliance_consulting_expense"]*$profit_split);
			$drivers_share_of_authority_exp = $drivers_share_of_authority_exp + ($leg_calc["authority_expense"]*$profit_split);
			$drivers_share_of_membership_dues_exp = $drivers_share_of_membership_dues_exp + ($leg_calc["membership_expense"]*$profit_split);
			$drivers_share_of_quick_pay_exp = $drivers_share_of_quick_pay_exp + (($leg_calc["bad_debt"]+$leg_calc["factoring"])*$profit_split);
		
			$drivers_share_of_hours = $drivers_share_of_hours + ($leg_calc["hours"]*$profit_split);
			$drivers_share_of_map_miles = $drivers_share_of_map_miles + ($leg_calc["map_miles"]*$profit_split);
			$drivers_share_of_odometer_miles = $drivers_share_of_odometer_miles + ($leg_calc["odometer_miles"]*$profit_split);
			$drivers_share_of_gallons = $drivers_share_of_gallons + ($leg_calc["gallons_used"]*$profit_split);
			
			$drivers_share_of_revenue = $drivers_share_of_revenue + ($leg_calc["carrier_revenue"]*$profit_split);
			
			//error_log("drivers_share_of_fuel_exp ".$drivers_share_of_fuel_exp." | LINE ".__LINE__." ".__FILE__);
			//error_log("drivers_share_of_gallons ".$drivers_share_of_gallons." | LINE ".__LINE__." ".__FILE__);
		}
		
		$invoice_date = $stats["this_end_week_end_leg"]["entry_datetime"];
		
		//*********************************** TRANSFER A/P FROM GENERIC HOLDING ACCOUTN TO DRIVER SPECIFIC A/P TO MEMBER ACOCUNT ****************
		
		//GET DRIVER SPECIFIC A/P ON SETTLEMENTS ACCOUNT
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/P to Member on Settlements";
		$coop_default_member_settlement_ap_account = db_select_default_account($where);
		
		//GET GENERIC A/P HOLDING ACCOUNT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Members on Settlements";
		$coop_default_settlement_ap_account = db_select_default_account($where);
		
		$invoice_desc = "Revenues calculated from statement";
		$invoice_desc = $client["client_nickname"]." - ".$invoice_desc;
		
		$invoice_amount = round($drivers_share_of_revenue,2);
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $coop_default_member_settlement_ap_account["account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $coop_default_settlement_ap_account["account_id"];
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		//************************************ FUEL PAYMENTS ******************************
		
		//GET COOP'S DEFAULT A/R FROM MEMEBERS ON FUEL PAYMENTS ACCOUNT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Fuel Payments";
		$coop_ar_fuel_acc = db_select_default_account($where);
		
		//GET COOP'S MEMBER-SPECIFIC A/R ON FUEL PAYMENTS ACCOUNT
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Fuel Payments";
		$coop_member_ar_on_fuel_payments_acc = db_select_default_account($where);
		
		//GENERATE DESCRIPTION
		$invoice_desc = "Fuel";
		if(!empty($drivers_share_of_gallons))
		{
			$invoice_desc = "Fuel ".$drivers_share_of_gallons." gallons @ $".number_format($drivers_share_of_fuel_exp/$drivers_share_of_gallons,2)." per gallon";
		}
		$invoice_desc = $client["client_nickname"]." - ".$invoice_desc;
		$invoice_amount = round($drivers_share_of_fuel_exp,2);
		
		//echo $invoice_amount;
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $coop_ar_fuel_acc["account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $coop_member_ar_on_fuel_payments_acc["account_id"];
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		$settlement_entries[] = $debit_entry;
		
		
		
		
		
		//************************************ INSURANCE PAYMENTS ******************************
		
		//GET COOP'S DEFAULT A/R FROM MEMEBERS ON INSURANCE PAYMENTS ACCOUNT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Insurance Payments";
		$coop_ar_ins_acc = db_select_default_account($where);
		
		//GET COOP'S MEMBER-SPECIFIC A/R ON INSURANCE PAYMENTS ACCOUNT
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Insurance Payments";
		$coop_member_ar_on_ins_payments_acc = db_select_default_account($where);
		
		//GENERATE DESCRIPTION
		$invoice_desc = "Insurance";
		if(!empty($drivers_share_of_hours))
		{
			$invoice_desc = "Insurance ".$drivers_share_of_hours." hours @ $".number_format($drivers_share_of_insurance_exp/$drivers_share_of_hours,2)." per hour";
		}
		$invoice_desc = $client["client_nickname"]." - ".$invoice_desc;
		$invoice_amount = round($drivers_share_of_insurance_exp,2);
		
		//echo $invoice_amount;
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $coop_ar_ins_acc["account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $coop_member_ar_on_ins_payments_acc["account_id"];
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		$settlement_entries[] = $debit_entry;
		
		
		
		//************************************ DIRECT LEASE INVOICES ******************************
		
		//GET LEASING COMPANY
		$where = null;
		$where["category"] = "Leasing";
		$leasing_company = db_select_company($where);
		
		//GET RELATIONSHIP BETWEEN LEASING COMPANY AND COOP
		$where = null;
		$where["business_id"] = $leasing_company["id"];
		$where["related_business_id"] = $coop_company["id"];
		$leasing_coop_relationship = db_select_business_relationship($where);
		
		//GET RELATIONSHIP BETWEEN COOP AND LEASING COMPANY
		$where = null;
		$where["business_id"] = $coop_company["id"];
		$where["related_business_id"] = $leasing_company["id"];
		$coop_leasing_relationship = db_select_business_relationship($where);
		
		

		
		
		
		
		//INVOICE FOR TRUCK RENTAL *************************************
		
		//GENERATE DESCRIPTION
		$truck_rental_desc = "Truck Rental";
		if(!empty($drivers_share_of_hours))
		{
			$truck_rental_desc = "Truck rental ".$drivers_share_of_hours." hours @ $".number_format($drivers_share_of_truck_rental_exp/$drivers_share_of_hours,2)." per hour";
		}
		
		//GET DEFAULT DIRECT LEASE ACCOUNTS
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "A/R on Truck Rental";
		$dl_default_truck_rental_ar_acc = db_select_default_account($where);
		
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "Revenue on Truck Rental";
		$dl_default_truck_rental_rev_acc = db_select_default_account($where);
		
		//GATHER DATA ABOUT INVOICE
		$balance_sheet_id = $dl_default_truck_rental_ar_acc["account_id"];
		$income_statement_id = $dl_default_truck_rental_rev_acc["account_id"];
		$invoice_amount = round($drivers_share_of_truck_rental_exp,2);
		$invoice_desc = $client["client_nickname"]." - ".$truck_rental_desc;
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_invoice = null;
		$insert_invoice['business_id'] = $leasing_company["id"];
		$insert_invoice['relationship_id'] = $leasing_coop_relationship["id"];
		$insert_invoice['debit_account_id'] = $balance_sheet_id;
		$insert_invoice['credit_account_id'] = $income_statement_id;
		$insert_invoice['invoice_type'] = "Revenue Generated";
		$insert_invoice['invoice_description'] = $invoice_desc;
		$insert_invoice['invoice_category'] = $category_account["category"];
		$insert_invoice['invoice_datetime'] = $invoice_date;
		$insert_invoice['invoice_amount'] = $invoice_amount;
		$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $income_statement_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $balance_sheet_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		
		//***** CREATE BILL FOR COOP FOR MEMBER TRUCK RENTAL *****
		
		//GET COOP MEMBER A/R ACCOUNT
		
		//GET DEFAULT COOP ACCOUNTS FOR MEMBER
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Lease Payments";
		$coop_member_lease_payment_ar_acc = db_select_default_account($where);
		
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/P to Lease Vendor";
		$coop_member_lease_vendor_ap_acc = db_select_default_account($where);
		
		//SET DEFAULT ACCOUNTS TO VARIABLE
		$ar_acc_id = $coop_member_lease_payment_ar_acc["account_id"];
		$ap_acc_id = $coop_member_lease_vendor_ap_acc["account_id"];
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_bill_invoice = null;
		$insert_bill_invoice['business_id'] = $coop_company["id"];
		$insert_bill_invoice['relationship_id'] = $coop_leasing_relationship["id"];
		$insert_bill_invoice['debit_account_id'] = $ar_acc_id;
		$insert_bill_invoice['credit_account_id'] = $ap_acc_id;
		$insert_bill_invoice['invoice_type'] = "Expense Incurred";
		$insert_bill_invoice['invoice_description'] = $invoice_desc;
		$insert_bill_invoice['invoice_category'] = $category_account["category"];
		$insert_bill_invoice['invoice_datetime'] = $invoice_date;
		$insert_bill_invoice['invoice_amount'] = $invoice_amount;
		$insert_bill_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_bill_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_bill_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_bill_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $ap_acc_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $ar_acc_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		$settlement_entries[] = $debit_entry;
		
		
		

		
		//INVOICE FOR TRUCK MAINTENANCE *************************************
		
		//GENERATE DESCRIPTION
		$invoice_details = "Truck Maintenance";
		if(!empty($drivers_share_of_odometer_miles))
		{
			$invoice_details = "Truck Maintenance ".number_format($drivers_share_of_odometer_miles,2)." miles @ $".number_format($drivers_share_of_truck_maintenance_exp/$drivers_share_of_odometer_miles,4)." per mile";
		}
		
		//GET DEFAULT DIRECT LEASE ACCOUNTS
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "A/R on Truck Maintenance";
		$dl_default_truck_maintenance_ar_acc = db_select_default_account($where);
		$balance_sheet_id = $dl_default_truck_maintenance_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "Revenue on Truck Maintenance";
		$dl_default_truck_maintenance_rev_acc = db_select_default_account($where);
		$income_statement_id = $dl_default_truck_maintenance_rev_acc["account_id"];
		
		//GATHER DATA ABOUT INVOICE
		$invoice_amount = round($drivers_share_of_truck_maintenance_exp,2);
		$invoice_desc = $client["client_nickname"]." - ".$invoice_details;
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_invoice = null;
		$insert_invoice['business_id'] = $leasing_company["id"];
		$insert_invoice['relationship_id'] = $leasing_coop_relationship["id"];
		$insert_invoice['debit_account_id'] = $balance_sheet_id;
		$insert_invoice['credit_account_id'] = $income_statement_id;
		$insert_invoice['invoice_type'] = "Revenue Generated";
		$insert_invoice['invoice_description'] = $invoice_desc;
		$insert_invoice['invoice_category'] = $category_account["category"];
		$insert_invoice['invoice_datetime'] = $invoice_date;
		$insert_invoice['invoice_amount'] = $invoice_amount;
		$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $income_statement_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $balance_sheet_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		//***** CREATE BILL FOR COOP FOR MEMBER TRUCK RENTAL *****
		
		//GET COOP MEMBER A/R ACCOUNT
		
		//GET DEFAULT COOP ACCOUNTS FOR MEMBER
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Lease Payments";
		$coop_member_lease_payment_ar_acc = db_select_default_account($where);
		
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/P to Lease Vendor";
		$coop_member_lease_vendor_ap_acc = db_select_default_account($where);
		
		//SET DEFAULT ACCOUNTS TO VARIABLE
		$ar_acc_id = $coop_member_lease_payment_ar_acc["account_id"];
		$ap_acc_id = $coop_member_lease_vendor_ap_acc["account_id"];
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_bill_invoice = null;
		$insert_bill_invoice['business_id'] = $coop_company["id"];
		$insert_bill_invoice['relationship_id'] = $coop_leasing_relationship["id"];
		$insert_bill_invoice['debit_account_id'] = $ar_acc_id;
		$insert_bill_invoice['credit_account_id'] = $ap_acc_id;
		$insert_bill_invoice['invoice_type'] = "Expense Incurred";
		$insert_bill_invoice['invoice_description'] = $invoice_desc;
		$insert_bill_invoice['invoice_category'] = $category_account["category"];
		$insert_bill_invoice['invoice_datetime'] = $invoice_date;
		$insert_bill_invoice['invoice_amount'] = $invoice_amount;
		$insert_bill_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_bill_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_bill_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_bill_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $ap_acc_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $ar_acc_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		$settlement_entries[] = $debit_entry;
		
		
		
		
		
		
		
		//INVOICE FOR TRAILER RENTAL *************************************
		
		//GENERATE DESCRIPTION
		$invoice_details = "Trailer Rental";
		if(!empty($drivers_share_of_hours))
		{
			$invoice_details = "Trailer Rental ".$drivers_share_of_hours." hours @ $".number_format($drivers_share_of_trailer_rental_exp/$drivers_share_of_hours,2)." per hour";
		}
		
		//GET DEFAULT DIRECT LEASE ACCOUNTS
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "A/R on Trailer Rental";
		$dl_default_trailer_rental_ar_acc = db_select_default_account($where);
		$balance_sheet_id = $dl_default_trailer_rental_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "Revenue on Trailer Rental";
		$dl_default_trailer_rental_rev_acc = db_select_default_account($where);
		$income_statement_id = $dl_default_trailer_rental_rev_acc["account_id"];
		
		//GATHER DATA ABOUT INVOICE
		$invoice_amount = round($drivers_share_of_trailer_rental_exp,2);
		$invoice_desc = $client["client_nickname"]." - ".$invoice_details;
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_invoice = null;
		$insert_invoice['business_id'] = $leasing_company["id"];
		$insert_invoice['relationship_id'] = $leasing_coop_relationship["id"];
		$insert_invoice['debit_account_id'] = $balance_sheet_id;
		$insert_invoice['credit_account_id'] = $income_statement_id;
		$insert_invoice['invoice_type'] = "Revenue Generated";
		$insert_invoice['invoice_description'] = $invoice_desc;
		$insert_invoice['invoice_category'] = $category_account["category"];
		$insert_invoice['invoice_datetime'] = $invoice_date;
		$insert_invoice['invoice_amount'] = $invoice_amount;
		$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $income_statement_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $balance_sheet_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		//***** CREATE BILL FOR COOP FOR MEMBER TRUCK RENTAL *****
		
		//GET COOP MEMBER A/R ACCOUNT
		
		//GET DEFAULT COOP ACCOUNTS FOR MEMBER
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Lease Payments";
		$coop_member_lease_payment_ar_acc = db_select_default_account($where);
		
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/P to Lease Vendor";
		$coop_member_lease_vendor_ap_acc = db_select_default_account($where);
		
		//SET DEFAULT ACCOUNTS TO VARIABLE
		$ar_acc_id = $coop_member_lease_payment_ar_acc["account_id"];
		$ap_acc_id = $coop_member_lease_vendor_ap_acc["account_id"];
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_bill_invoice = null;
		$insert_bill_invoice['business_id'] = $coop_company["id"];
		$insert_bill_invoice['relationship_id'] = $coop_leasing_relationship["id"];
		$insert_bill_invoice['debit_account_id'] = $ar_acc_id;
		$insert_bill_invoice['credit_account_id'] = $ap_acc_id;
		$insert_bill_invoice['invoice_type'] = "Expense Incurred";
		$insert_bill_invoice['invoice_description'] = $invoice_desc;
		$insert_bill_invoice['invoice_category'] = $category_account["category"];
		$insert_bill_invoice['invoice_datetime'] = $invoice_date;
		$insert_bill_invoice['invoice_amount'] = $invoice_amount;
		$insert_bill_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_bill_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_bill_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_bill_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $ap_acc_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $ar_acc_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		$settlement_entries[] = $debit_entry;
		
		
		
		
		
		
		
		//INVOICE FOR TRAILER MAINTENANCE *************************************
		
		//GENERATE DESCRIPTION
		$invoice_details = "Trailer Maintenance";
		if(!empty($drivers_share_of_odometer_miles))
		{
			$invoice_details = "Trailer Maintenance ".number_format($drivers_share_of_odometer_miles,2)." miles @ $".number_format($drivers_share_of_trailer_maintenance_exp/$drivers_share_of_odometer_miles,4)." per mile";
		}
		
		//GET DEFAULT DIRECT LEASE ACCOUNTS
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "A/R on Trailer Maintenance";
		$dl_default_trailer_maintenance_ar_acc = db_select_default_account($where);
		$balance_sheet_id = $dl_default_trailer_maintenance_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "Revenue on Trailer Maintenance";
		$dl_default_trailer_maintenance_rev_acc = db_select_default_account($where);
		$income_statement_id = $dl_default_trailer_maintenance_rev_acc["account_id"];
		
		//GATHER DATA ABOUT INVOICE
		$invoice_amount = round($drivers_share_of_trailer_maintenance_exp,2);
		$invoice_desc = $client["client_nickname"]." - ".$invoice_details;
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_invoice = null;
		$insert_invoice['business_id'] = $leasing_company["id"];
		$insert_invoice['relationship_id'] = $leasing_coop_relationship["id"];
		$insert_invoice['debit_account_id'] = $balance_sheet_id;
		$insert_invoice['credit_account_id'] = $income_statement_id;
		$insert_invoice['invoice_type'] = "Revenue Generated";
		$insert_invoice['invoice_description'] = $invoice_desc;
		$insert_invoice['invoice_category'] = $category_account["category"];
		$insert_invoice['invoice_datetime'] = $invoice_date;
		$insert_invoice['invoice_amount'] = $invoice_amount;
		$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $income_statement_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $balance_sheet_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		//***** CREATE BILL FOR COOP FOR MEMBER TRUCK RENTAL *****
		
		//GET COOP MEMBER A/R ACCOUNT
		
		//GET DEFAULT COOP ACCOUNTS FOR MEMBER
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Lease Payments";
		$coop_member_lease_payment_ar_acc = db_select_default_account($where);
		
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/P to Lease Vendor";
		$coop_member_lease_vendor_ap_acc = db_select_default_account($where);
		
		//SET DEFAULT ACCOUNTS TO VARIABLE
		$ar_acc_id = $coop_member_lease_payment_ar_acc["account_id"];
		$ap_acc_id = $coop_member_lease_vendor_ap_acc["account_id"];
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_bill_invoice = null;
		$insert_bill_invoice['business_id'] = $coop_company["id"];
		$insert_bill_invoice['relationship_id'] = $coop_leasing_relationship["id"];
		$insert_bill_invoice['debit_account_id'] = $ar_acc_id;
		$insert_bill_invoice['credit_account_id'] = $ap_acc_id;
		$insert_bill_invoice['invoice_type'] = "Expense Incurred";
		$insert_bill_invoice['invoice_description'] = $invoice_desc;
		$insert_bill_invoice['invoice_category'] = $category_account["category"];
		$insert_bill_invoice['invoice_datetime'] = $invoice_date;
		$insert_bill_invoice['invoice_amount'] = $invoice_amount;
		$insert_bill_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_bill_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_bill_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_bill_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $ap_acc_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $ar_acc_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		$settlement_entries[] = $debit_entry;
		
		
		
		
		
		
		
		
		//INVOICE FOR TRAILER MAINTENANCE *************************************
		
		//GENERATE DESCRIPTION
		$invoice_details = "Deposit Alternative";
		if(!empty($drivers_share_of_odometer_miles))
		{
			$invoice_details = "Deposit Alternative ".number_format($drivers_share_of_odometer_miles,2)." miles @ $".number_format($drivers_share_of_deposit_alternative_exp/$drivers_share_of_odometer_miles,4)." per mile";
		}
		
		//GET DEFAULT DIRECT LEASE ACCOUNTS
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "A/R on Deposit Alternative";
		$dl_default_deposit_alt_ar_acc = db_select_default_account($where);
		$balance_sheet_id = $dl_default_deposit_alt_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "Revenue on Deposit Alternative";
		$dl_default_deposit_alt_rev_acc = db_select_default_account($where);
		$income_statement_id = $dl_default_deposit_alt_rev_acc["account_id"];
		
		//GATHER DATA ABOUT INVOICE
		$invoice_amount = round($drivers_share_of_deposit_alternative_exp,2);
		$invoice_desc = $client["client_nickname"]." - ".$invoice_details;
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_invoice = null;
		$insert_invoice['business_id'] = $leasing_company["id"];
		$insert_invoice['relationship_id'] = $leasing_coop_relationship["id"];
		$insert_invoice['debit_account_id'] = $balance_sheet_id;
		$insert_invoice['credit_account_id'] = $income_statement_id;
		$insert_invoice['invoice_type'] = "Revenue Generated";
		$insert_invoice['invoice_description'] = $invoice_desc;
		$insert_invoice['invoice_category'] = $category_account["category"];
		$insert_invoice['invoice_datetime'] = $invoice_date;
		$insert_invoice['invoice_amount'] = $invoice_amount;
		$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $income_statement_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $balance_sheet_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		//***** CREATE BILL FOR COOP FOR MEMBER TRUCK RENTAL *****
		
		//GET COOP MEMBER A/R ACCOUNT
		
		//GET DEFAULT COOP ACCOUNTS FOR MEMBER
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Lease Payments";
		$coop_member_lease_payment_ar_acc = db_select_default_account($where);
		
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/P to Lease Vendor";
		$coop_member_lease_vendor_ap_acc = db_select_default_account($where);
		
		//SET DEFAULT ACCOUNTS TO VARIABLE
		$ar_acc_id = $coop_member_lease_payment_ar_acc["account_id"];
		$ap_acc_id = $coop_member_lease_vendor_ap_acc["account_id"];
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_bill_invoice = null;
		$insert_bill_invoice['business_id'] = $coop_company["id"];
		$insert_bill_invoice['relationship_id'] = $coop_leasing_relationship["id"];
		$insert_bill_invoice['debit_account_id'] = $ar_acc_id;
		$insert_bill_invoice['credit_account_id'] = $ap_acc_id;
		$insert_bill_invoice['invoice_type'] = "Expense Incurred";
		$insert_bill_invoice['invoice_description'] = $invoice_desc;
		$insert_bill_invoice['invoice_category'] = $category_account["category"];
		$insert_bill_invoice['invoice_datetime'] = $invoice_date;
		$insert_bill_invoice['invoice_amount'] = $invoice_amount;
		$insert_bill_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_bill_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_bill_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_bill_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $ap_acc_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $ar_acc_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		$settlement_entries[] = $debit_entry;
		
		
		
		
		
		
		//************************************ LOBOS INVOICES ******************************
		
		//GET CARRIER SERVICES COMPANY
		$where = null;
		$where["category"] = "Driver Services";
		$driver_services_company = db_select_company($where);
		
		//GET RELATIONSHIP BETWEEN LEASING COMPANY AND COOP
		$where = null;
		$where["business_id"] = $driver_services_company["id"];
		$where["related_business_id"] = $coop_company["id"];
		$driver_services_coop_relationship = db_select_business_relationship($where);
		
		//GET RELATIONSHIP BETWEEN COOP AND LEASING COMPANY
		$where = null;
		$where["business_id"] = $coop_company["id"];
		$where["related_business_id"] = $driver_services_company["id"];
		$coop_driver_services_relationship = db_select_business_relationship($where);
		
		
		//INVOICE FOR COMPLIANCE *************************************
		
		//GENERATE DESCRIPTION
		$invoice_details = "Compliance & Consulting";
		if(!empty($drivers_share_of_odometer_miles))
		{
			$invoice_details = "Compliance & Consulting ".number_format($drivers_share_of_odometer_miles,2)." miles @ $".number_format($drivers_share_of_compliance_exp/$drivers_share_of_odometer_miles,4)." per mile";
		}
		
		//GET DEFAULT LOBOS ACCOUNTS
		$where = null;
		$where["company_id"] = $driver_services_company["id"];
		$where["category"] = "A/R on Compliance & Consulting";
		$dl_default_deposit_alt_ar_acc = db_select_default_account($where);
		$balance_sheet_id = $dl_default_deposit_alt_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $driver_services_company["id"];
		$where["category"] = "Revenue on Compliance & Consulting";
		$dl_default_deposit_alt_rev_acc = db_select_default_account($where);
		$income_statement_id = $dl_default_deposit_alt_rev_acc["account_id"];
		
		//GATHER DATA ABOUT INVOICE
		$invoice_amount = round($drivers_share_of_compliance_exp,2);
		$invoice_desc = $client["client_nickname"]." - ".$invoice_details;
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_invoice = null;
		$insert_invoice['business_id'] = $driver_services_company["id"];
		$insert_invoice['relationship_id'] = $driver_services_coop_relationship["id"];
		$insert_invoice['debit_account_id'] = $balance_sheet_id;
		$insert_invoice['credit_account_id'] = $income_statement_id;
		$insert_invoice['invoice_type'] = "Revenue Generated";
		$insert_invoice['invoice_description'] = $invoice_desc;
		$insert_invoice['invoice_category'] = $category_account["category"];
		$insert_invoice['invoice_datetime'] = $invoice_date;
		$insert_invoice['invoice_amount'] = $invoice_amount;
		$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $income_statement_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $balance_sheet_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		//***** CREATE BILL FOR COOP FOR MEMBER TRUCK RENTAL *****
		
		//GET COOP MEMBER A/R ACCOUNT
		
		//GET DEFAULT COOP ACCOUNTS FOR MEMBER
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Driver Services Payments";
		$coop_member_compliance_ar_acc = db_select_default_account($where);
		$ar_acc_id = $coop_member_compliance_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/P to Driver Services Vendor";
		$coop_member_driver_services_vendor_ap_acc = db_select_default_account($where);
		$ap_acc_id = $coop_member_driver_services_vendor_ap_acc["account_id"];
		
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_bill_invoice = null;
		$insert_bill_invoice['business_id'] = $coop_company["id"];
		$insert_bill_invoice['relationship_id'] = $coop_driver_services_relationship["id"];
		$insert_bill_invoice['debit_account_id'] = $ar_acc_id;
		$insert_bill_invoice['credit_account_id'] = $ap_acc_id;
		$insert_bill_invoice['invoice_type'] = "Expense Incurred";
		$insert_bill_invoice['invoice_description'] = $invoice_desc;
		$insert_bill_invoice['invoice_category'] = $category_account["category"];
		$insert_bill_invoice['invoice_datetime'] = $invoice_date;
		$insert_bill_invoice['invoice_amount'] = $invoice_amount;
		$insert_bill_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_bill_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_bill_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_bill_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $ap_acc_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $ar_acc_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		$settlement_entries[] = $debit_entry;
		
		
		
		
		
		//INVOICE FOR AUTHORITY MAINTENANCE *************************************
		
		//GENERATE DESCRIPTION
		$invoice_details = "Authority Maintenance";
		if(!empty($drivers_share_of_odometer_miles))
		{
			$invoice_details = "Authority Maintenance ".number_format($drivers_share_of_odometer_miles,2)." miles @ $".number_format($drivers_share_of_compliance_exp/$drivers_share_of_odometer_miles,4)." per mile";
		}
		
		//GET DEFAULT LOBOS ACCOUNTS
		$where = null;
		$where["company_id"] = $driver_services_company["id"];
		$where["category"] = "A/R on Authority Maintenance";
		$dl_default_am_ar_acc = db_select_default_account($where);
		$balance_sheet_id = $dl_default_am_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $driver_services_company["id"];
		$where["category"] = "Revenue on Authority Maintenance";
		$dl_default_am_rev_acc = db_select_default_account($where);
		$income_statement_id = $dl_default_am_rev_acc["account_id"];
		
		//GATHER DATA ABOUT INVOICE
		$invoice_amount = round($drivers_share_of_compliance_exp,2);
		$invoice_desc = $client["client_nickname"]." - ".$invoice_details;
		
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_invoice = null;
		$insert_invoice['business_id'] = $driver_services_company["id"];
		$insert_invoice['relationship_id'] = $driver_services_coop_relationship["id"];
		$insert_invoice['debit_account_id'] = $balance_sheet_id;
		$insert_invoice['credit_account_id'] = $income_statement_id;
		$insert_invoice['invoice_type'] = "Revenue Generated";
		$insert_invoice['invoice_description'] = $invoice_desc;
		$insert_invoice['invoice_category'] = $category_account["category"];
		$insert_invoice['invoice_datetime'] = $invoice_date;
		$insert_invoice['invoice_amount'] = $invoice_amount;
		$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $income_statement_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $balance_sheet_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		//***** CREATE BILL FOR COOP FOR MEMBER TRUCK RENTAL *****
		
		//GET COOP MEMBER A/R ACCOUNT
		
		//GET DEFAULT COOP ACCOUNTS FOR MEMBER
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Driver Services Payments";
		$coop_member_compliance_ar_acc = db_select_default_account($where);
		$ar_acc_id = $coop_member_compliance_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/P to Driver Services Vendor";
		$coop_member_driver_services_vendor_ap_acc = db_select_default_account($where);
		$ap_acc_id = $coop_member_driver_services_vendor_ap_acc["account_id"];
		
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_bill_invoice = null;
		$insert_bill_invoice['business_id'] = $coop_company["id"];
		$insert_bill_invoice['relationship_id'] = $coop_driver_services_relationship["id"];
		$insert_bill_invoice['debit_account_id'] = $ar_acc_id;
		$insert_bill_invoice['credit_account_id'] = $ap_acc_id;
		$insert_bill_invoice['invoice_type'] = "Expense Incurred";
		$insert_bill_invoice['invoice_description'] = $invoice_desc;
		$insert_bill_invoice['invoice_category'] = $category_account["category"];
		$insert_bill_invoice['invoice_datetime'] = $invoice_date;
		$insert_bill_invoice['invoice_amount'] = $invoice_amount;
		$insert_bill_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_bill_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_bill_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_bill_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $category_account["relationship_id"];
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $ap_acc_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $ar_acc_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		$settlement_entries[] = $debit_entry;
		
		
		
		
		
		
		//************************************ UNITED COOP INVOICES ******************************
		
		//GET RELATIONSHIP BETWEEN LEASING COMPANY AND COOP
		$where = null;
		$where["business_id"] = $coop_company["id"];
		$where["related_business_id"] = $driver_company["id"];
		$coop_driver_relationship = db_select_business_relationship($where);
		
		
		//INVOICE FOR MEMBERSHIP DUES *************************************
		
		//GENERATE DESCRIPTION
		$invoice_desc = "Membership Dues";
		if(!empty($drivers_share_of_odometer_miles))
		{
			$invoice_desc = "Membership Dues ".number_format($drivers_share_of_odometer_miles,2)." miles @ $".number_format($drivers_share_of_membership_dues_exp/$drivers_share_of_odometer_miles,2)." per miles";
		}
		
		//GET DEFAULT COOP ACCOUNTS
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Membership Dues";
		$default_membership_dues_ar_acc = db_select_default_account($where);
		$balance_sheet_id = $default_membership_dues_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "Revenue on Membership Dues";
		$default_membership_dues_rev_acc = db_select_default_account($where);
		$income_statement_id = $default_membership_dues_rev_acc["account_id"];
		
		//GATHER DATA ABOUT INVOICE
		$invoice_amount = round($drivers_share_of_membership_dues_exp,2);
		$invoice_desc = $client["client_nickname"]." - ".$invoice_desc;
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_invoice = null;
		$insert_invoice['business_id'] = $coop_company["id"];
		$insert_invoice['relationship_id'] = $coop_driver_relationship["id"];
		$insert_invoice['debit_account_id'] = $balance_sheet_id;
		$insert_invoice['credit_account_id'] = $income_statement_id;
		$insert_invoice['invoice_type'] = "Revenue Generated";
		$insert_invoice['invoice_description'] = $invoice_desc;
		$insert_invoice['invoice_category'] = $category_account["category"];
		$insert_invoice['invoice_datetime'] = $invoice_date;
		$insert_invoice['invoice_amount'] = $invoice_amount;
		$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		
		//GET RELATIONSHIP
		$relationship = $coop_driver_relationship;
		
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $income_statement_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $balance_sheet_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		$settlement_entry = null;
		$settlement_entry = $debit_entry;
		$settlement_entry["invoice_id"] = $newly_created_invoice["id"];
		$settlement_entries[] = $settlement_entry;
		
		
		
		//INVOICE FOR QUICK PAY *************************************
		
		//GENERATE DESCRIPTION
		$invoice_desc = "Quick Pay";
		if(!empty($drivers_share_of_map_miles))
		{
			$invoice_desc = "Quick Pay ".number_format($drivers_share_of_map_miles,2)." miles @ $".number_format($drivers_share_of_quick_pay_exp/$drivers_share_of_map_miles,2)." per miles";
		}
		
		//GET DEFAULT COOP ACCOUNTS
		$where = null;
		$where["company_id"] = $driver_company["id"];
		$where["category"] = "Coop A/R on Quick Pay";
		$default_quick_pay_ar_acc = db_select_default_account($where);
		$balance_sheet_id = $default_quick_pay_ar_acc["account_id"];
		
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "Revenue on Quick Pay";
		$default_quick_pay_rev_acc = db_select_default_account($where);
		$income_statement_id = $default_quick_pay_rev_acc["account_id"];
		
		//GATHER DATA ABOUT INVOICE
		$invoice_amount = round($drivers_share_of_quick_pay_exp,2);
		$invoice_desc = $client["client_nickname"]." - ".$invoice_desc;
		
		//GET ACCOUNT FOR CATEGORY
		$where = null;
		$where["id"] = $income_statement_id;
		$category_account = db_select_account($where);
		
		//INSERT INVOICE INTO DB
		$insert_invoice = null;
		$insert_invoice['business_id'] = $coop_company["id"];
		$insert_invoice['relationship_id'] = $coop_driver_relationship["id"];
		$insert_invoice['debit_account_id'] = $balance_sheet_id;
		$insert_invoice['credit_account_id'] = $income_statement_id;
		$insert_invoice['invoice_type'] = "Revenue Generated";
		$insert_invoice['invoice_description'] = $invoice_desc;
		$insert_invoice['invoice_category'] = $category_account["category"];
		$insert_invoice['invoice_datetime'] = $invoice_date;
		$insert_invoice['invoice_amount'] = $invoice_amount;
		$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_invoice);
		
		//GENERATE INVOICE NUMBER FOR INVOICE (NOT BILLS)
		
		//GET RELATIONSHIP
		$relationship = $coop_driver_relationship;
		
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
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//INSERT DEBIT AND CREDIT ACCOUNT ENTRIES
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $income_statement_id;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $invoice_amount;
		$credit_entry["entry_description"] = $invoice_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $balance_sheet_id;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $invoice_amount;
		$debit_entry["entry_description"] = $invoice_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		$settlement_entry = null;
		$settlement_entry = $debit_entry;
		$settlement_entry["invoice_id"] = $newly_created_invoice["id"];
		$settlement_entries[] = $settlement_entry;
		
		//CREDIT DRIVER'S A/P ACCOUNT (KICK IN AMOUNT), DEBIT DRIVER'S FLEETPROTECT ACCOUNT (KICK IN AMOUNT)
		
		$kick_in = round($settlement["kick_in"],2);
		$entry_link = $settlement["settlement_link"];
		
		//GET DRIVER'S FLEETPROTECT ACCOUNT
		$where = null;
		$where["company_id"] = $client["company_id"];
		$where["category"] = "Coop A/R on FleetProtect";
		$default_fleet_protect_account = db_select_default_account($where);

		$entry_desc = "Settlement Loan from statement ending ".date("m/d/y",strtotime($invoice_date));;
		$entry_desc = $client["client_nickname"]." - ".$entry_desc;
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $coop_default_member_settlement_ap_account["account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $invoice_created_datetime;
		$credit_entry["entry_datetime"] = $invoice_date;
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $kick_in;
		$credit_entry["entry_description"] = $entry_desc;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $default_fleet_protect_account["account_id"];
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $invoice_created_datetime;
		$debit_entry["entry_datetime"] = $invoice_date;
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $kick_in;
		$debit_entry["entry_description"] = $entry_desc;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		
		//MARK ALL CLIENT EXPENSES AS PAID
		if(!empty($stats["client_expenses"]))
		{
			foreach($stats["client_expenses"] as $client_expense)
			{
				//UPDATE CLIENT EXPENSE WITH PAID DATETIME
				$update_expense = null;
				$update_expense["paid_datetime"] = $entry_datetime;
				
				$where = null;
				$where["id"] = $client_expense["id"];
				db_update_client_expense($update_expense,$where);
			}
		}
		
		//ACCOUNT FOR ALL CREDITS
		if(!empty($stats["statement_credits"]))
		{
			foreach($stats["statement_credits"] as $statement_credit)
			{
				//GET ACCOUNT
				$where = null;
				$where["id"] = $statement_credit["debited_account_id"];
				$vendor_credit_account = db_select_account($where);
				
				if($vendor_credit_account["relationship_id"] == 0)
				{
					//GET COMPANY
					$where = null;
					$where["id"] = $vendor_credit_account["company_id"];
					$vendor_credits_company = db_select_company($where);
				}
				else
				{
					
					//GET RELATIONSHIP
					$where = null;
					$where["id"] = $vendor_credit_account["relationship_id"];
					$vendor_credit_relationship = db_select_business_relationship($where);
					
					//GET COMPANY
					$where = null;
					$where["id"] = $vendor_credit_relationship["related_business_id"];
					$vendor_credits_company = db_select_company($where);
				}
				
				//GET DEFAULT ACCOUNT FOR INVOICED COMPANY
				
				//CREATE CREDIT ENTRY
				$credit_entry = null;
				$credit_entry["account_id"] = $coop_default_member_settlement_ap_account["account_id"];
				$credit_entry["recorder_id"] = $recorder_id;
				$credit_entry["recorded_datetime"] = $invoice_created_datetime;
				$credit_entry["entry_datetime"] = $invoice_date;
				$credit_entry["debit_credit"] = "Credit";
				$credit_entry["entry_amount"] = $statement_credit["credit_amount"];
				$credit_entry["entry_description"] = "Credit from ".$vendor_credits_company["company_side_bar_name"]." | ".$statement_credit["credit_description"];
				//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $credit_entry;
				
				//CREATE DEBIT ENTRY
				$debit_entry = null;
				$debit_entry["account_id"] = $statement_credit["debited_account_id"];
				$debit_entry["recorder_id"] = $recorder_id;
				$debit_entry["recorded_datetime"] = $invoice_created_datetime;
				$debit_entry["entry_datetime"] = $invoice_date;
				$debit_entry["debit_credit"] = "Debit";
				$debit_entry["entry_amount"] = $statement_credit["credit_amount"];
				$debit_entry["entry_description"] = "Credit to ".$client["client_nickname"]." from ".$vendor_credits_company["company_side_bar_name"]." | ".$statement_credit["credit_description"];
				//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
				
				$entries[] = $debit_entry;
				
				//CREATE INVOICE IF NOT COOP EXPENSE
				if($vendor_credit_account["relationship_id"] != 0)
				{
					//GET INVOICE CODE FROM BUSINESS NAME
					$name_array = explode(" ",$coop_company["company_name"]);
					$business_user_code_long = "";
					foreach($name_array as $w)
					{
						$business_user_code_long .= $w[0];
					}
					$business_user_code = substr($business_user_code_long,0,3);
					
					//GET INVOICE CODE FROM RELATED-BUSINESS NAME
					$name_array = explode(" ",$vendor_credits_company["company_name"]);
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
					
					
					//INSERT INVOICE INTO DB
					$insert_invoice = null;
					$insert_invoice['business_id'] = $coop_company["id"];
					$insert_invoice['relationship_id'] = $vendor_credit_account["relationship_id"];
					$insert_invoice['debit_account_id'] = $statement_credit["debited_account_id"];
					$insert_invoice['credit_account_id'] = $coop_default_member_settlement_ap_account["account_id"];
					$insert_invoice['invoice_type'] = "Revenue Generated";
					$insert_invoice['invoice_description'] = "Credit to ".$client["client_nickname"]." | ".$statement_credit["credit_description"];
					$insert_invoice['invoice_category'] = "Vendor Credit";
					$insert_invoice['invoice_datetime'] = $invoice_date;
					$insert_invoice['invoice_number'] = $invoice_number;
					$insert_invoice['invoice_amount'] = $statement_credit["credit_amount"];
					$insert_invoice['invoice_created_datetime'] = $invoice_created_datetime;
					$insert_invoice['settlement_id'] = $settlement["id"];
					
					db_insert_invoice($insert_invoice);
					
					//GET NEWLY CREATED INVOICE
					$where = null;
					$newly_created_invoice = db_select_invoice($insert_invoice);
					
					//CREATE BILL HOLDER
					$bill_holder = null;
					$bill_holder['invoice_id'] = $newly_created_invoice["id"];
					$bill_holder['company_id'] = $vendor_credits_company["id"];
					$bill_holder['from_company_id'] = $coop_company["id"];
					$bill_holder['created_datetime'] = $invoice_created_datetime;
					$bill_holder['bill_datetime'] = $invoice_date;
					$bill_holder['description'] = "Credit to ".$client["client_nickname"]." | ".$statement_credit["credit_description"];
					$bill_holder['amount'] = $statement_credit["credit_amount"];
					
					db_insert_bill_holder($bill_holder);
				}
				
				//UPDATE STATEMENT CREDIT
				$where = null;
				$where["id"] = $statement_credit["id"];
				$update_statement_credit = null;
				$update_statement_credit["settled_datetime"] = $invoice_created_datetime;
				db_update_statement_credit($update_statement_credit,$where);
			}
		}
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		$transaction = null;
		$transaction["category"] = "Statement Charges";
		$transaction["description"] = "Statement Charges - ".$client["client_nickname"]." ".date_format("m/d/y",strtotime($invoice_date));
		$transaction_1 = create_transaction_and_entries($transaction,$entries);
		
		//CALL HELPER FUNCTION TO GO THROUGH ALL THE SETTLEMENT ENTRIES TO COVER A/R FROM MEMBER WITH A/P TO MEMBER - ALSO MARK COOP INVOICES PAID
		$transaction_2 = settle_expenses_w_driver_ap_account($settlement_entries,$driver_company);
		
		//ADD TRANSACTIONs TO SETTLEMENT_TRANSACTION TABLE
		$settlement_transaction = null;
		$settlement_transaction["settlement_id"] = $settlement["id"];
		$settlement_transaction["transaction_id"] = $transaction_1["id"];
		db_insert_settlement_transaction($settlement_transaction);
		
		$settlement_transaction = null;
		$settlement_transaction["settlement_id"] = $settlement["id"];
		$settlement_transaction["transaction_id"] = $transaction_2["id"];
		db_insert_settlement_transaction($settlement_transaction);
		
		
		//***** CREATE BILL FOR COOP FOR SETTLEMENT PAYABLE TO DRIVER *****
		
		
		//GET RELATIONSHIP BETWEEN COOP AND MEMBER
		$where = null;
		$where["business_id"] = $coop_company["id"];
		$where["related_business_id"] = $driver_company["id"];
		$coop_driver_relationship = db_select_business_relationship($where);
		
		$invoice_amount = round($stats["statement_amount"]+$stats["settlement"]["kick_in"],2);
		$invoice_desc = "Settlement Payment from statement ending ".date("m/d/y",strtotime($invoice_date));;
		$invoice_desc = $client["client_nickname"]." - ".$invoice_desc;
		
		//INSERT INVOICE INTO DB
		$insert_bill_invoice = null;
		$insert_bill_invoice['business_id'] = $coop_company["id"];
		$insert_bill_invoice['relationship_id'] = $coop_driver_relationship["id"];
		$insert_bill_invoice['debit_account_id'] = $coop_default_settlement_ap_account["account_id"];
		$insert_bill_invoice['credit_account_id'] = $coop_default_member_settlement_ap_account["account_id"];//DRIVER SETTLEMENT PAYABLE ACCOUNT
		$insert_bill_invoice['invoice_type'] = "Expense Incurred";
		$insert_bill_invoice['invoice_description'] = $invoice_desc;
		$insert_bill_invoice['invoice_category'] = "Driver Settlement";
		$insert_bill_invoice['invoice_datetime'] = $invoice_date;
		$insert_bill_invoice['invoice_amount'] = $invoice_amount;
		$insert_bill_invoice['invoice_created_datetime'] = $invoice_created_datetime;
		$insert_bill_invoice['settlement_id'] = $settlement["id"];
		
		db_insert_invoice($insert_bill_invoice);
		
		//GET NEWLY CREATED INVOICE
		$where = null;
		$newly_created_invoice = db_select_invoice($insert_bill_invoice);
		
		//GET INVOICE CODE FROM BUSINESS NAME
		$name_array = explode(" ",$coop_company["company_name"]);
		$business_user_code_long = "";
		foreach($name_array as $w)
		{
			$business_user_code_long .= $w[0];
		}
		$business_user_code = substr($business_user_code_long,0,3);
		
	
		//GET INVOICE CODE FROM RELATED-BUSINESS NAME
		$name_array = explode(" ",$driver_company["company_name"]);
		$related_company_code_long = "";
		foreach($name_array as $w)
		{
			$related_company_code_long .= $w[0];
		}
		$related_company_code = substr($related_company_code_long,0,3);
		
		$invoice_number = $business_user_code."-".$related_company_code.$newly_created_invoice["id"];
		
		/**
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
		**/
		
		//UPDATE INVOICE
		$update_invoice = null;
		//$update_invoice["file_guid"] = $contract_secure_file["file_guid"];
		$update_invoice["invoice_number"] = $invoice_number;
		
		$where = null;
		$where["id"] = $newly_created_invoice["id"];
		db_update_invoice($update_invoice,$where);
		
		
		//MARK SETTLEMENT AS SETTLED
		$update_settlement = null;
		$update_settlement["settled_datetime"] = $entry_datetime;
		
		$where = null;
		$where["id"] = $settlement_id;
		db_update_settlement($update_settlement,$where);
		
		//******* CREATE PO REQUEST ***************
		
		//MAKE ISSUER NAME TEXT
		$issuer_name = $this->session->userdata('f_name')." ".$this->session->userdata('l_name');
		
		$po_notes = $client["client_nickname"]." settlement payment for statement ending ".date("m/d/y",strtotime($invoice_date));;
		
		//INSERT NEW PO
		$po["expense_datetime"] = $invoice_date;
		$po["expense_amount"] = round($stats["statement_amount"]+$stats["settlement"]["kick_in"],2);
		$po["owner_id"] = $coop_company["id"];
		$po["category"] = "Driver Settlements";//THIS IS HARD CODED SO IT NEEDS TO MATCH UP WITH THE CURRENT EXPENSE CATEGORY | TODO: MAKE DEFAULT ACCOUNT FOR THE DRIVER SETTLEMENT EXPENSE ACCOUNT
		$po["issuer_id"] = $this->session->userdata('person_id');
		$po["issued_datetime"] = $invoice_created_datetime;
		$po["po_notes"] = $po_notes;
		$po["po_log"] = date("m/d/y H:i",strtotime($invoice_created_datetime))." | PO created by ".$issuer_name;
		$po["settlement_id"] = $settlement_id;
		//db_insert_purchase_order($po);
		
	}
	
	/**
	//APPROVE SETTLEMENT - CREATE ACCOUNT ENTRIES FOR SETTLEMENT
	function approve_settlement()
	{
		$recorder_id = $this->session->userdata('person_id');
		
		date_default_timezone_set('America/Denver');
		$entry_datetime = date("Y-m-d H:i:s");
		
		$settlement_id = $_POST["settlement_id"];
		
		//GET SETTLEMENT
		$where = null;
		$where["id"] = $settlement_id;
		$settlement = db_select_settlement($where);
		
		//GET LOG ENTRY FOR END WEEK
		$where = null;
		$where["id"] = $settlement["end_week_id"];
		$log_entry = db_select_log_entry($where);
		
		$stats = get_driver_end_week_stats($log_entry,$settlement["client_id"]);
		
		//MARK SETTLEMENT APPROVED
		$update_settlement = null;
		$update_settlement["approved_datetime"] = $entry_datetime;
		$update_settlement["approved_by"] = $recorder_id;
		
		$where = null;
		$where["id"] = $settlement_id;
		db_update_settlement($update_settlement,$where);
		
		$profit = $stats["statement_amount"];
		$reserve = $stats["total_damage_share"];
		$kick_in = $settlement["kick_in"];
		$entry_link = $settlement["settlement_link"];

		//GET CLIENT
		$where = null;
		$where["id"] = $settlement["client_id"];
		$this_client = db_select_client($where);
		
		//GET CLIENT'S DAMAGE ACCOUNT
		$where = null;
		$where["company_id"] = $this_client["company"]["id"];
		$where["category"] = "Client Damage";
		$damage_account = db_select_account($where);
		
		//GET FM COMPANY
		$where = null;
		$where["person_id"] = $settlement["fm_id"];
		$where["type"] = "Fleet Manager";
		$fm_company = db_select_company($where);
		
		//GET FM PROFIT ACCOUNT
		$where = null;
		$where["company_id"] = $fm_company["id"];
		$where["account_type"] = "Fleet Manager";
		$where["category"] = "Profit";
		$fm_profit_account = db_select_account($where);
		
		//CREATE THE CREDIT ENTRY IN THE DATABASE FOR CARRIER PROFIT
		$sys_gen_desc = "Amount Earned | P&L Statement ending ".date("n/j/y",strtotime($settlement["log_entry"]["entry_datetime"]));
		
		$earned_entry["account_id"] = $this_client["main_account"];
		$earned_entry["recorder_id"] = $recorder_id;
		$earned_entry["entry_datetime"] = $entry_datetime;
		$earned_entry["entry_type"] = "Weekly Settlement";
		$earned_entry["debit_credit"] = "Credit";
		$earned_entry["entry_amount"] = round($profit,2);
		$earned_entry["entry_description"] = "Weekly Settlement | $sys_gen_desc";
		$earned_entry["entry_link"] = $entry_link;
		
		if($earned_entry["entry_amount"] != 0)
		{
			db_insert_account_entry($earned_entry);
		}
		
		//CREATE THE CREDIT ENTRY IN THE DATABASE FOR THE CLIENTS DAMAGE ACCOUNT
		$sys_gen_desc = "Money added to damage reserve from weekly settlement ending ".date("n/j/y",strtotime($settlement["log_entry"]["entry_datetime"]));
		
		$damage_reserve_entry["account_id"] = $damage_account["id"];
		$damage_reserve_entry["recorder_id"] = $recorder_id;
		$damage_reserve_entry["entry_datetime"] = $entry_datetime;
		$damage_reserve_entry["entry_type"] = "Weekly Settlement";
		$damage_reserve_entry["debit_credit"] = "Credit";
		$damage_reserve_entry["entry_amount"] = round($reserve,2);
		$damage_reserve_entry["entry_description"] = "Weekly Settlement | $sys_gen_desc";
		$damage_reserve_entry["entry_link"] = $entry_link;
		
		db_insert_account_entry($damage_reserve_entry);
		
		//CREATE THE CREDIT ENTRY IN THE DATABASE FOR PAYMENT TO FACTORING
			//TODO
		
		//CREATE THE CREDIT ENTRY IN THE DATABASE FOR PAYMENT TO BAD DEBT
			//TODO
		
		
		//MARK ALL CLIENT EXPESNES AS PAID
		if(!empty($stats["client_expenses"]))
		{
			foreach($stats["client_expenses"] as $client_expense)
			{
				//UPDATE CLIENT EXPENSE WITH PAID DATETIME
				$update_expense = null;
				$update_expense["paid_datetime"] = $entry_datetime;
				
				$where = null;
				$where["id"] = $client_expense["id"];
				db_update_client_expense($update_expense,$where);
			}
		}
		
		
	}
	**/
	
	//GET SUMMARY STATS
	function get_summary_stats()
	{
		//GET FILTER PARAMETERS
		$fm_id = (int)$_POST["fm_filter_dropdown"];
		$client_id = (int)$_POST["client_filter_dropdown"];
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		$get_pending_kick_in =  $_POST["get_pending_kick_in"];
		$get_pending_approval =  $_POST["get_pending_approval"];
		$get_closed =  $_POST["get_closed"];
		
		
		//GET SETTLEMENTS
		$where = " AND locked_datetime IS NOT NULL ";
		
		//CLIENT FILTER
		if($fm_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($fm_id))
			{
				$where = $where." AND fm_id = ".$fm_id;
			}
		}
		
		//CLIENT FILTER
		if($client_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($client_id))
			{
				$where = $where." AND client_id = ".$client_id;
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
		
		
		//CREATE EVENT FILTER SQL
		if
			(
				$get_pending_kick_in == "false" || 
				$get_pending_approval == "false" ||
				$get_closed == "false" 
			)
		{
		
			$an_event_is_selected = false;
			$where = $where." AND (";
			
			if($get_pending_kick_in == "true")
			{
				$where = $where."(kick_in IS NULL) OR ";
				$an_event_is_selected = true;
			}
			
			if($get_pending_approval == "true")
			{
				$where = $where."(kick_in IS NOT NULL AND approved_datetime IS NULL) OR ";
				$an_event_is_selected = true;
			}
			
			if($get_closed == "true")
			{
				$where = $where."(kick_in IS NOT NULL AND approved_datetime IS NOT NULL) OR ";
				$an_event_is_selected = true;
			}
			
			//PREPARE WHERE STRING
			if($an_event_is_selected)
			{
				$where = substr($where,0,-4).") ";//this takes away the extra " OR "
			}
			else
			{
				$where = substr($where,0,-5);//this take away the " AND ("
			}
			
			
		}
		
		//IF ALL EVENTS ARE UNSELECTED
		if
			(
				$get_pending_kick_in == "false" && 
				$get_pending_approval == "false" &&
				$get_closed == "false" 
			)
		{
			$where = " AND 1 = 2 ";
		}
		
		$where = substr($where,4);
		
		if(empty($where))
		{
			$where = " 1 = 1 "; //IF NOTHING IS FILTERED
		}
		
		//echo $where;
		//$settlements = null;
		$settlements = db_select_settlements($where,"entry_datetime DESC");
		
		//CALCULATE STATS
		$avg_miles = 0;
		$avg_mpg = 0;
		$avg_oor = 0;
		$avg_fuel_price = 0;
		$total_fuel_exp = 0;
		$total_reserve = 0;
		$total_earned = 0;
		$total_kick_in = 0;
		$total_map_miles = 0;
		$total_odometer_miles = 0;
		$total_gallons = 0;
		$total_hours = 0;
		$i = 1;
		
		$summary_stats = null;
		if(!empty($settlements))
		{
			foreach($settlements as $settlement)
			{
				//GET LOG ENTRY FOR END WEEK
				$where = null;
				$where["id"] = $settlement["end_week_id"];
				$log_entry = db_select_log_entry($where);
				
				$stats = null;
				$stats = get_driver_end_week_stats($log_entry,$settlement["client_id"]);
				
				//GET TOTAL RESERVE
				$total_reserve = $total_reserve + $stats["total_damage_share"] + $stats["damage_adjustment_expense"]["expense_amount"];
				
				//GET EARNED
				$total_earned = $total_earned + $stats["statement_amount"];
				
				//GET TOTAL KICK IN
				$total_kick_in = $total_kick_in + $settlement["kick_in"];
				
				//GET TOTAL HOURS
				$total_hours = $total_hours + $stats["total_in_truck_hours"];
				
				//GET TOTAL GALLONS
				$total_gallons = $total_gallons + $stats["total_gallons"];
				
				//GET TOTAL FUEL EXPENSE
				$total_fuel_exp = $total_fuel_exp + $stats["total_fuel_expense"];
				
				//GET TOTAL MAP MILES
				$total_map_miles = $total_map_miles + $stats["total_map_miles"];
				
				//GET TOTAL ODOMETER MILES
				$total_odometer_miles = $total_odometer_miles + $stats["total_odometer_miles"];
				
				$i++;
			}
			
			$summary_stats = null;
			$summary_stats["total_earned"] = $total_earned;
			$summary_stats["total_kick_in"] = $total_kick_in;
			$summary_stats["total_reserve"] = $total_reserve;
			$summary_stats["avg_fuel_price"] = round($total_fuel_exp/$total_gallons,2);
			$summary_stats["avg_oor"] = round((($total_odometer_miles-$total_map_miles)/$total_map_miles)*100,2);
			$summary_stats["avg_mpg"] = round($total_odometer_miles/$total_gallons,2);
			$summary_stats["avg_miles"] = round($total_map_miles/$total_hours*24);
		}
		
		$data['summary_stats'] = $summary_stats;
		$this->load->view('settlements/settlements_summary_stats_div',$data);
	}
	
	//DISPLAY DB SETTLEMENT
	function display_db_settlement($settlement_id)
	{
		//GET SETTLEMENT
		$where = null;
		$where["id"] = $settlement_id;
		$settlement = db_select_settlement($where);
		
		echo $settlement["html"];
	}

	function add_statement_credit()
	{
		$settlement_id = $_POST["settlement_id"];
		$business_user = $_POST["invoiced_company_dd"];
		$credit_description = $_POST["credit_description"];
		$credit_amount = round($_POST["credit_amount"],2);
		
		
		//INSERT NEW STATEMENT CREDIT
		$statement_credit = null;
		$statement_credit["settlement_id"] = $settlement_id;
		$statement_credit["debited_account_id"] = $business_user;
		$statement_credit["credit_description"] = $credit_description;
		$statement_credit["credit_amount"] = $credit_amount;
		db_insert_statement_credit($statement_credit);
	}
	
}