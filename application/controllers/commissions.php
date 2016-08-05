<?php		


	
class Commissions extends MY_Controller 
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
		$where["client_type"] = "Main Driver";
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
		$fleet_manager_dropdown_options['All'] = "All FM's";
		foreach ($fleet_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$fleet_manager_dropdown_options[$manager['id']] = $title;
		}

		
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$data['main_driver_dropdown_options'] = $main_driver_dropdown_options;
		$data['tab'] = 'Commissions';
		$data['title'] = "Commissions";
		$this->load->view('commissions_view',$data);
		
	}// end index()

	//LOAD LOG
	function load_list()
	{
		
		//GET FILTER PARAMETERS
		$fm_id = (int)$_POST["fm_filter_dropdown"];
		$client_id = (int)$_POST["client_filter_dropdown"];
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		$load_number = $_POST["load_filter"];
		$get_in_transit =  $_POST["get_in_transit"];
		$get_pending_funding =  $_POST["get_pending_funding"];
		$get_pending_settlement =  $_POST["get_pending_settlement"];
		$get_closed =  $_POST["get_closed"];
		
		
		//GET ENTRY LOGS
		$where = " AND status_number > 5 AND status_number < 100 AND final_drop_datetime > '2013-06-22 00:00:00' "; //START DATE OF COMMISSIONS
		
		//CLIENT FILTER
		if($fm_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($fm_id))
			{
				$where = $where." AND fleet_manager_id = ".$fm_id;
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
			$where = $where." AND final_drop_datetime > '".$start_datetime."'";
		}
		
		//END DATE FILTER
		if(!empty($end_date))
		{
			$end_datetime = date("Y-m-d G:i:s",strtotime($end_date)+60*60*24);
			$where = $where." AND final_drop_datetime < '".$end_datetime."'";
		}
		
		//LOAD NUMBER FILTER
		if(!empty($load_number))
		{
			//GET THE LOAD WITH THIS LOAD NUMBER
			$load_where = null;
			$load_where["customer_load_number"] = $load_number;
			$load = db_select_load($load_where);
			
			if(!empty($load))
			{
				$where = $where." AND id = '".$load["id"]."'";
			}
		}
		
		
		//echo $get_pick_trailers;
		
		
		//CREATE EVENT FILTER SQL
		if
			(
				$get_in_transit == "false" || 
				$get_pending_funding == "false" ||
				$get_pending_settlement == "false" ||
				$get_closed == "false"
			)
		{
			//echo $get_pick_trailers;
		
			$an_event_is_selected = false;
			$where = $where." AND (";
			
			if($get_in_transit == "true")
			{
				$where = $where."(status_number < 7 AND funded_datetime IS NULL AND commission_approved_datetime IS NULL) OR ";
				$an_event_is_selected = true;
			}
			
			if($get_pending_funding == "true")
			{
				$where = $where."(status = 'Dropped' AND funded_datetime IS NULL AND commission_approved_datetime IS NULL) OR ";
				$an_event_is_selected = true;
			}
			
			if($get_pending_settlement == "true")
			{
				$where = $where."(status = 'Dropped' AND funded_datetime IS NOT NULL AND commission_approved_datetime IS NULL) OR ";
				$an_event_is_selected = true;
			}
			
			if($get_closed == "true")
			{
				$where = $where."(status = 'Dropped' AND funded_datetime IS NOT NULL AND commission_approved_datetime IS NOT NULL) OR ";
				$an_event_is_selected = true;
			}
			
			
			if($an_event_is_selected)
			{
				$where = substr($where,0,-4).") ";//this takes away the extra " OR " and adds the ") "
			}
			
		}
		
		//IF NONE OF THEM ARE SELECTED
		if
			(
				$get_in_transit == "false" && 
				$get_pending_funding == "false" &&
				$get_pending_settlement == "false" &&
				$get_closed == "false"
			)
		{
			$where = "     1 != 1"; //adds extra spaces for the substr($where, 4)
		}
		
		$where = substr($where,4);
		if(empty($where))
		{
			$where = " 1 = 1 "; //IF NOTHING IS FILTERED
		}
		
		//echo $where;
		$loads = db_select_loads($where,"final_drop_datetime DESC");
		
		$booking_stats = calc_booking_stats($loads);
		
		
		$data['booking_stats'] = $booking_stats;
		$data['loads'] = $loads;
		$this->load->view('commissions/commissions_div',$data);
		
	}//end load_log()

	//OPEN COMMISSION DETAILS
	function open_commission_details()
	{
		$load_id = $_POST["load_id"];
		
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
	
		//GET ALL LOCKED LEGS WITH THIS ALLOCATED LOAD ID
		$where = null;
		$where = " leg.allocated_load_id = '".$load["id"]."' AND log_entry.locked_datetime IS NOT NULL ";
		$these_legs = db_select_legs($where);
		
		$commission_status = is_commission_good($load);
		
		if($commission_status["is_good"])
		{
			
			//IF COMMISSION HASN'T BEEN CALCULATED YET
			if(empty($load["map_miles"]))
			{
				//CALCULATE COMMISSION
				update_commission_calc($load["id"]);
			}
		}
		
		//GET LOAD EXPENSES
		$where = null;
		$where["load_id"] = $load["id"];
		$load_expenses = db_select_load_expenses($where);
		
		$data['is_good'] = $commission_status["is_good"];
		$data['load_expenses'] = $load_expenses;
		$data['these_legs'] = $these_legs;
		$data['load'] = $load;
		$this->load->view('commissions/commission_details',$data);
	}
	
	//REFRESH COMMISSION ROW
	function refresh_row()
	{
		$load_id = $_POST["load_id"];
	
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
	
		$data['load'] = $load;
		$this->load->view('commissions/commission_row',$data);
	}
	
	//APPROVE COMMISSION
	function approve_commission()
	{
		
		$load_id = $_POST["load_id"];
		
		$recorder_id = $this->session->userdata('person_id');
		date_default_timezone_set('America/Denver');
		$entry_datetime = date("Y-m-d H:i:s");
	
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		//IF USER HAS PERMISSION TO APPROVE COMMISSIONS
		if(user_has_permission("approve commissions"))
		{
			
			//GET FM COMPANY
			$where = null;
			$where["person_id"] = $load["fleet_manager_id"];
			$where["type"] = "Fleet Manager";
			$fm_company = db_select_company($where);
			
			//GET FM PROFIT ACCOUNT
			$where = null;
			$where["company_id"] = $fm_company["id"];
			$where["account_type"] = "Fleet Manager";
			$where["category"] = "Profit";
			$fm_profit_account = db_select_account($where);
			
			
			//CREDIT FM PROFIT ACCOUNT THE TOTAL MARGIN
			$credit_fm["account_id"] = $fm_profit_account["id"];
			$credit_fm["recorder_id"] = $recorder_id;
			$credit_fm["entry_type"] = "Profit";
			$credit_fm["entry_datetime"] = $entry_datetime;
			$credit_fm["debit_credit"] = "Credit";
			$credit_fm["entry_amount"] = calc_commission($load);
			$credit_fm["entry_description"] = "Profit on Load ".$load["customer_load_number"];

			db_insert_account_entry($credit_fm);
			
			//GET MSIOO COMPANY
			$where = null;
			$where["company_name"] = "Management Services";
			$msioo_company = db_select_company($where);
			
			//GET MSIOO PROFIT ACCOUNT
			$where = null;
			$where["company_id"] = $msioo_company["id"];
			$where["account_type"] = "Business";
			$where["category"] = "Profit";
			$msioo_profit_account = db_select_account($where);
			
			//GET TOTAL RATE ON LOAD
			$rate_funded = $load["amount_funded"] + $load["financing_cost"];
			
			//CREDIT MSIOO THE 4.5%
			$credit_msioo["account_id"] = $msioo_profit_account["id"];
			$credit_msioo["recorder_id"] = $recorder_id;
			$credit_msioo["entry_type"] = "4.5 Percent";
			$credit_msioo["entry_datetime"] = $entry_datetime;
			$credit_msioo["debit_credit"] = "Credit";
			$credit_msioo["entry_amount"] = round($rate_funded*.045,2);
			$credit_msioo["entry_description"] = "4.5% of the revenue on Load ".$load["customer_load_number"];

			db_insert_account_entry($credit_msioo);
			
			
			//UPDATE LOAD WITH APPROVE COMMISSION DATETIME
			$update = null;
			$update["commission_approved_datetime"] = $entry_datetime;
			$update["commission_approved_by"] = $recorder_id;
			
			$where = null;
			$where["id"] = $load["id"];
			db_update_load($update,$where);
			
		
			$data['load'] = $load;
			$this->load->view('commissions/commission_row',$data);
		}
		else //IF USER DOES NOT HAVE PERMISSIONS
		{
			$data['load'] = $load;
			$this->load->view('commissions/commission_row',$data);
			echo "<script>alert('The big cheese says you do not have permission to do this!')</script>";
		}
	}
}