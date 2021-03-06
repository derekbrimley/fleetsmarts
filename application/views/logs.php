<?php		


	
class Logs extends MY_Controller 
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
		//$where["client_type"] = "Main Driver";
		$main_drivers = db_select_clients($where,"client_nickname");
		
		$main_driver_dropdown_options = array();
		$main_driver_dropdown_options["All"] = "All Main Drivers";
		foreach($main_drivers as $main_driver)
		{
			$main_driver_dropdown_options[$main_driver["id"]] = $main_driver["client_nickname"];
		}
		
		//GET ALL ACTIVE CODRIVERS
		$where = null;
		$where["client_status"] = "Active";
		//$where["client_type"] = "Co-Driver";
		$codrivers = db_select_clients($where,"client_nickname");
		
		$codriver_dropdown_options = array();
		$codriver_dropdown_options["All"] = "All Co-Drivers";
		foreach($codrivers as $codriver)
		{
			$codriver_dropdown_options[$codriver["id"]] = $codriver["client_nickname"];
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
		
		//GET ALL ACTIVE TRAILERS
		$where = null;
		$where = " trailer_status != 'Retired' ";
		$trailers = db_select_trailers($where,"trailer_number");
		
		$trailer_dropdown_options = array();
		$trailer_dropdown_options["All"] = "All Trailers";
		foreach($trailers as $trailer)
		{
			$trailer_dropdown_options[$trailer["id"]] = $trailer["trailer_number"];
		}
		
		
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['trailer_dropdown_options'] = $trailer_dropdown_options;
		$data['main_driver_dropdown_options'] = $main_driver_dropdown_options;
		$data['codriver_dropdown_options'] = $codriver_dropdown_options;
		$data['tab'] = 'Logs';
		$data['title'] = "Logs";
		$this->load->view('logs_view',$data);
	}// end index()

	
	//LOAD LOG
	function load_log()
	{
		
		//GET FILTER PARAMETERS
		$main_driver_id = (int)$_POST["main_driver_filter_dropdown"];
		$codriver_id = (int)$_POST["codriver_filter_dropdown"];
		$truck_id = (int)$_POST["truck_filter_dropdown"];
		$trailer_id = (int)$_POST["trailer_filter_dropdown"];
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		$load_number = $_POST["load_filter"];
		$get_picks =  $_POST["get_picks"];
		$get_drops =  $_POST["get_drops"];
		$get_fuel_fills =  $_POST["get_fuel_fills"];
		$get_fuel_partials =  $_POST["get_fuel_partials"];
		$get_checkpoints =  $_POST["get_checkpoints"];
		$get_driver_ins =  $_POST["get_driver_ins"];
		$get_driver_outs =  $_POST["get_driver_outs"];
		$get_pick_trailers =  $_POST["get_pick_trailers"];
		$get_drop_trailers =  $_POST["get_drop_trailers"];
		$get_check_calls =  $_POST["get_check_calls"];
		$get_dry_services =  $_POST["get_dry_services"];
		$get_wet_services =  $_POST["get_wet_services"];
		$get_credit_cards =  $_POST["get_credit_cards"];
		$get_end_legs =  $_POST["get_end_legs"];
		$get_end_weeks =  $_POST["get_end_weeks"];
		
		
		
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
		
		//CO-DRIVER FILTER
		if($codriver_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($codriver_id))
			{
				$where = $where." AND log_entry.codriver_id = ".$codriver_id;
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
		
		//TRAILER FILTER
		if($trailer_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($trailer_id))
			{
				$where = $where." AND trailer_id = ".$trailer_id;
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
		
		//LOAD NUMBER FILTER
		if(!empty($load_number))
		{
			//GET THE LOAD WITH THIS LOAD NUMBER
			$load_where = null;
			$load_where["customer_load_number"] = $load_number;
			$load = db_select_load($load_where);
			
			if(!empty($load))
			{
				$where = $where." AND log_entry.allocated_load_id = '".$load["id"]."'";
			}
		}
		
		
		//echo $get_pick_trailers;
		
		
		//CREATE EVENT FILTER SQL
		if
			(
				$get_picks == "false" || 
				$get_drops == "false" ||
				$get_fuel_fills == "false" ||
				$get_fuel_partials == "false" ||
				$get_checkpoints == "false" ||
				$get_driver_ins == "false" ||
				$get_driver_outs == "false" ||
				$get_pick_trailers == "false" ||
				$get_drop_trailers == "false" ||
				$get_check_calls == "false" ||
				$get_dry_services == "false" ||
				$get_wet_services == "false" ||
				$get_credit_cards == "false" ||
				$get_end_legs == "false" ||
				$get_end_weeks == "false"
			)
		{
			//echo $get_pick_trailers;
		
			$an_event_is_selected = false;
			$where = $where." AND (";
			
			if($get_picks == "true")
			{
				$where = $where."entry_type = 'Pick' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_drops == "true")
			{
				$where = $where."entry_type = 'Drop' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_fuel_fills == "true")
			{
				$where = $where."entry_type = 'Fuel Fill' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_fuel_partials == "true")
			{
				$where = $where."entry_type = 'Fuel Partial' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_checkpoints == "true")
			{
				$where = $where."entry_type = 'Checkpoint' OR entry_type = 'Checkpoint OOR' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_driver_ins == "true")
			{
				$where = $where."entry_type = 'Driver In' OR entry_type = 'Driver In OOR' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_driver_outs == "true")
			{
				$where = $where."entry_type = 'Driver Out' OR entry_type = 'Driver Out OOR' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_pick_trailers== "true")
			{
				$where = $where."entry_type = 'Pick Trailer' OR entry_type = 'Pick Trailer OOR' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_drop_trailers == "true")
			{
				$where = $where."entry_type = 'Drop Trailer' OR entry_type = 'Drop Trailer OOR' OR ";
				$an_event_is_selected = true;
			}

			if($get_check_calls == "true")
			{
				$where = $where."entry_type = 'Check Call' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_dry_services == "true")
			{
				$where = $where."entry_type = 'Dry Service' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_wet_services == "true")
			{
				$where = $where."entry_type = 'Wet Service' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_credit_cards == "true")
			{
				$where = $where."entry_type = 'Credit Card' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_end_legs == "true")
			{
				$where = $where."entry_type = 'End Leg' OR ";
				$an_event_is_selected = true;
			}
			
			if($get_end_weeks == "true")
			{
				$where = $where."entry_type = 'End Week' OR ";
				$an_event_is_selected = true;
			}
			
			
			if($an_event_is_selected)
			{
				$where = substr($where,0,-4).") ";//this takes away the extra " OR "
			}
			else
			{
				$where = "     entry_type = 'none'"; //ADDS SPACES TO WORK WITH substr()
			}
			
		}
		
		$where = substr($where,4);
		//echo $where;
		$logs = db_select_log_entrys($where,"entry_datetime DESC",75);
		
		$data['logs'] = $logs;
		$this->load->view('logs/log_div',$data);
		
		
	}//end load_log()

	//OPEN DETAIL BOX FOR EVENT
	function open_event_details()
	{
		$log_entry_id = $_POST["event_id"];
		
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		//UPDATE THE ALLOCATED LOAD
		$update = null;
		if(empty($log_entry["load_id"])) //IF THERE IS NO LOAD
		{
			if($log_entry["truck_id"] != 0) //IF A TRUCK EXISTS ON THIS EVENT
			{
				//GET NEXT EVENT WITH A LOAD
				$where = null;
				$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
				$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
				
				if(!empty($next_loaded_event))
				{
					$update["allocated_load_id"] = $next_loaded_event["load_id"];
				}
				else
				{
					$update["allocated_load_id"] = $log_entry["load_id"];
				}
			}
			else
			{
				$update["allocated_load_id"] = Null;
			}
		}
		else
		{
			$update["allocated_load_id"] = $log_entry["load_id"];
		}
		$where = null;
		$where["id"] = $log_entry["id"];
		db_update_log_entry($update,$where);
		
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		
		//LOAD VIEW DEPENDING ON ENTRY TYPE
		if($log_entry["entry_type"] == "Pick")
		{
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/pick_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Drop")
		{
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/pick_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Fuel Fill")
		{
			//GET THIS FUEL STOP
			$where = null;
			$where["log_entry_id"] = $log_entry["id"];
			$fuel_stop = db_select_fuel_stop($where);
		
			//LOOK FOR UNASSIGNED FUEL PERMITS BETWEEN THIS AND PREVIOUS FUEL STOP AND ASSIGNED THEM TO THIS FUEL STOP
			$truck_id = $log_entry["truck_id"];
			$this_fuel_stop_datetime = $log_entry["entry_datetime"];
			
			//GET PREVIOUS FUEL STOP
			$where = null;
			$where = " log_entry.truck_id = $truck_id AND log_entry.entry_datetime < '$this_fuel_stop_datetime' AND log_entry.locked_datetime IS NULL AND (log_entry.entry_type = 'Fuel Fill' OR log_entry.entry_type = 'Fuel Partial')  ";
			$previous_fuel_stop = db_select_log_entry($where,"entry_datetime DESC",1);
			$previous_fuel_stop_datetime = $previous_fuel_stop["entry_datetime"];
			
			//GET ANY UNASSIGNED FUEL PERMITS INBETWEEN THIS FUEL STOP AND THE PREVIOUS FUEL STOP
			$where = null;
			$where = " fuel_stop_id IS NULL AND permit_datetime < '$this_fuel_stop_datetime' AND  permit_datetime > '$previous_fuel_stop_datetime' ";
			$unassigned_permits = db_select_fuel_permits($where);
			
			if(!empty($unassigned_permits))
			{
				foreach($unassigned_permits as $permit)
				{
					//IF FUEL PERMIT WAS CHARGED TO FM
					if(!empty($permit["account_entry_id"]))
					{
						//DELETE ACCOUNT ENTRY
						$where = null;
						$where["id"] = $$permit["account_entry_id"];
						db_delete_account_entry($where);
						
						//RESET ACCOUNT ENTRY ID AND NOTES TO NULL
						$update_permit["account_entry_id"] = null;
						$update_permit["permit_notes"] = null;
					}
					
					//AS LONG AS FUEL STOP IS NOT LOCKED - UPDATE
					if(empty($log_entry["locked_datetime"]))
					{
						//UPDATE FUEL PERMIT WITH THIS FUEL STOP ID
						$update_permit["fuel_stop_id"] = $fuel_stop["id"];
						$where = null;
						$where["id"] = $permit["id"];
						db_update_fuel_permit($update_permit,$where);
					}
				}
			}
			
			//GET ALL ASSIGNED FUEL PERMITS
			$where = null;
			$where["fuel_stop_id"] = $fuel_stop["id"];
			$assigned_permits = db_select_fuel_permits($where);
			if(!empty($assigned_permits))
			{
				foreach($assigned_permits as $permit)
				{
					//echo $permit["id"]." ".$permit["account_entry_id"]." ";
					//IF FUEL PERMIT WAS CHARGED TO FM AND FUEL STOP IS NOT LOCKED
					if(!empty($permit["account_entry_id"]))
					{
						//DELETE ACCOUNT ENTRY
						$where = null;
						$where["id"] = $permit["account_entry_id"];
						db_delete_account_entry($where);
						
						//RESET ACCOUNT ENTRY ID AND NOTES TO NULL
						$update_permit["account_entry_id"] = null;
						$update_permit["permit_notes"] = null;
						//echo "hello";
					}
					//ONLY UPDATE IF EVENT IS NOT LOCKED
					if(empty($log_entry["locked_datetime"]))
					{
						//UPDATE FUEL PERMIT WITH THIS FUEL STOP ID
						$update_permit["fuel_stop_id"] = $fuel_stop["id"];
						$where = null;
						$where["id"] = $permit["id"];
						db_update_fuel_permit($update_permit,$where);
					}
				}
			}
		
			$fuel_stop_details = get_fuel_stop_details($log_entry_id);
			
			//UPDATE FUEL STOP IN THE DB
			$update_fuel_stop["is_fill"] = $fuel_stop_details["is_fill"];
			$update_fuel_stop["natl_fuel_avg"] =get_natl_fuel_avg_from_db($log_entry["entry_datetime"]);
			$update_fuel_stop["fill_to_fill_gallons"] = $fuel_stop_details["f2f_gallons"];
			$update_fuel_stop["fill_to_fill_expense"] = $fuel_stop_details["f2f_expense"];
			$update_fuel_stop["fill_to_fill_rebate"] = $fuel_stop_details["f2f_discount"];
			$update_fuel_stop["map_miles"] = $fuel_stop_details["f2f_miles"];
			$update_fuel_stop["odom_miles"] = $fuel_stop_details["odometer_miles"] ;
			
			$where = null;
			$where["id"] = $fuel_stop_details["fuel_stop_id"];
			//ONLY UPDATE IF EVENT IS NOT LOCKED
			if(empty($log_entry["locked_datetime"]))
			{
				db_update_fuel_stop($update_fuel_stop,$where);
			}
			$this_fuel_stop = db_select_fuel_stop($where);
			
			//GET FUEL PERMITS FOR THIS FUEL STOP
			$where = null;
			$where["fuel_stop_id"] = $this_fuel_stop["id"];
			$fuel_permits = db_select_fuel_permits($where);
			
			
			//UPDATE LEG EVENT WITH CURRENT INFORMATION
			$this_fuel_event = null;
			$this_fuel_event["load_id"] = $log_entry["load_id"];
			$this_fuel_event["allocated_load_id"] = $log_entry["allocated_load_id"];
			$this_fuel_event["truck_id"] = $log_entry["truck_id"];
			$this_fuel_event["trailer_id"] = $log_entry["trailer_id"];
			$this_fuel_event["main_driver_id"] = $log_entry["main_driver_id"];
			$this_fuel_event["codriver_id"] = $log_entry["codriver_id"];
			$this_fuel_event["miles"] = $fuel_stop_details["f2f_miles"];
			$this_fuel_event["out_of_route"] = $fuel_stop_details["f2f_oor"];
			$this_fuel_event["mpg"] = $fuel_stop_details["f2f_mpg"];
			$this_fuel_event["route"] = $fuel_stop_details["f2f_route_url"];
			
			$where = null;
			$where["id"] = $log_entry["id"];
			//ONLY UPDATE IF EVENT IS NOT LOCKED
			if(empty($log_entry["locked_datetime"]))
			{
				db_update_log_entry($this_fuel_event,$where);
			}
			
			$data["fill_partial_style"] = "";
			$data["fuel_permits"] = $fuel_permits;
			$data["this_fuel_stop"] = $this_fuel_stop;
			$data["fuel_stop_details"] = $fuel_stop_details;
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/fuel_fill_details',$data);
		}
		else if($log_entry["entry_type"] == "Fuel Partial")
		{
			//LOOK FOR UNASSIGNED FUEL PERMITS BETWEEN THIS AND PREVIOUS FUEL STOP AND ASSIGNED THEM TO THIS FUEL STOP
			$truck_id = $log_entry["truck_id"];
			$this_fuel_stop_datetime = $log_entry["entry_datetime"];
			
			//GET PREVIOUS FUEL STOP
			$where = null;
			$where = " log_entry.truck_id = $truck_id AND log_entry.entry_datetime < '$this_fuel_stop_datetime' AND log_entry.locked_datetime IS NULL AND (log_entry.entry_type = 'Fuel Fill' OR log_entry.entry_type = 'Fuel Partial')  ";
			$previous_fuel_stop = db_select_log_entry($where,"entry_datetime DESC",1);
			$previous_fuel_stop_datetime = $previous_fuel_stop["entry_datetime"];
			
			//GET ANY UNASSIGNED FUEL PERMITS INBETWEEN THIS FUEL STOP AND THE PREVIOUS FUEL STOP
			$where = null;
			$where = " fuel_stop_id IS NULL AND permit_datetime < '$this_fuel_stop_datetime' AND  permit_datetime > '$previous_fuel_stop_datetime' ";
			$unassigned_permits = db_select_fuel_permits($where);
			
			if(!empty($unassigned_permits))
			{
				foreach($unassigned_permits as $permit)
				{
					//IF FUEL PERMIT WAS CHARGED TO FM
					if(!empty($permit["account_entry_id"]))
					{
						//DELETE ACCOUNT ENTRY
						$where = null;
						$where["id"] = $$permit["account_entry_id"];
						db_delete_account_entry($where);
						
						//RESET ACCOUNT ENTRY ID AND NOTES TO NULL
						$update_permit["account_entry_id"] = null;
						$update_permit["permit_notes"] = null;
					}
					
					//AS LONG AS FUEL STOP IS NOT LOCKED - UPDATE
					if(empty($log_entry["locked_datetime"]))
					{
						//UPDATE FUEL PERMIT WITH THIS FUEL STOP ID
						$update_permit["fuel_stop_id"] = $fuel_stop["id"];
						$where = null;
						$where["id"] = $permit["id"];
						db_update_fuel_permit($update_permit,$where);
					}
				}
			}
			
			//GET ALL ASSIGNED FUEL PERMITS
			$where = null;
			$where["fuel_stop_id"] = $fuel_stop["id"];
			$assigned_permits = db_select_fuel_permits($where);
			foreach($assigned_permits as $permit)
			{
				//IF FUEL PERMIT WAS CHARGED TO FM AND FUEL STOP IS NOT LOCKED
				if(!empty($permit["account_entry_id"]) && empty($log_entry["locked_datetime"]))
				{
					//DELETE ACCOUNT ENTRY
					$where = null;
					$where["id"] = $$permit["account_entry_id"];
					db_delete_account_entry($where);
					
					//RESET ACCOUNT ENTRY ID AND NOTES TO NULL
					$update_permit["account_entry_id"] = null;
					$update_permit["permit_notes"] = null;
				}
				
				//UPDATE FUEL PERMIT WITH THIS FUEL STOP ID
				$update_permit["fuel_stop_id"] = $fuel_stop["id"];
				$where = null;
				$where["id"] = $permit["id"];
				db_update_fuel_permit($update_permit,$where);
			}
		
			$fuel_stop_details = get_fuel_stop_details($log_entry_id);
			
			$fuel_stop_details["odometer_miles"] = null;
			$fuel_stop_details["f2f_miles"] = null;
			$fuel_stop_details["f2f_gallons"] = null;
			$fuel_stop_details["f2f_expense"] = null;
			$fuel_stop_details["f2f_discount"] = null;
			
			//UPDATE FUEL STOP IN THE DB
			$update_fuel_stop = null;
			$update_fuel_stop["is_fill"] = $fuel_stop_details["is_fill"];
			$update_fuel_stop["natl_fuel_avg"] =get_natl_fuel_avg_from_db($log_entry["entry_datetime"]);
			
			$where = null;
			$where["id"] = $fuel_stop_details["fuel_stop_id"];
			//ONLY UPDATE IF EVENT IS NOT LOCKED
			if(empty($log_entry["locked_datetime"]))
			{
				db_update_fuel_stop($update_fuel_stop,$where);
			}
			$this_fuel_stop = db_select_fuel_stop($where);
			
			//GET FUEL PERMITS FOR THIS FUEL STOP
			$where = null;
			$where["fuel_stop_id"] = $this_fuel_stop["id"];
			$fuel_permits = db_select_fuel_permits($where);
			
			//UPDATE LOG EVENT WITH CURRENT INFORMATION
			$this_fuel_event = null;
			$this_fuel_event["load_id"] = $log_entry["load_id"];
			$this_fuel_event["allocated_load_id"] = $log_entry["allocated_load_id"];
			$this_fuel_event["truck_id"] = $log_entry["truck_id"];
			$this_fuel_event["trailer_id"] = $log_entry["trailer_id"];
			$this_fuel_event["main_driver_id"] = $log_entry["main_driver_id"];
			$this_fuel_event["codriver_id"] = $log_entry["codriver_id"];
			
			$where = null;
			$where["id"] = $log_entry["id"];
			//ONLY UPDATE IF EVENT IS NOT LOCKED
			if(empty($log_entry["locked_datetime"]))
			{
				db_update_log_entry($this_fuel_event,$where);
			}
			
			$data["fill_partial_style"] = "display:none;";
			$data["fuel_permits"] = $fuel_permits;
			$data["this_fuel_stop"] = $this_fuel_stop;
			$data["fuel_stop_details"] = $fuel_stop_details;
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/fuel_fill_details',$data);
		}
		else if($log_entry["entry_type"] == "Checkpoint")
		{
			$data["checked"] = "";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/checkpoint_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Checkpoint OOR")
		{
			$data["checked"] = "checked";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/checkpoint_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Driver In")
		{
			$data["checked"] = "";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/driver_in_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Driver In OOR")
		{
			$data["checked"] = "checked";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/driver_in_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Driver Out")
		{
			$data["checked"] = "";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/driver_in_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Driver Out OOR")
		{
			$data["checked"] = "checked";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/driver_in_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Pick Trailer")
		{
			$data["checked"] = "";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/driver_in_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Drop Trailer")
		{
			$data["checked"] = "";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/driver_in_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Pick Trailer OOR")
		{
			$data["checked"] = "checked";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/driver_in_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Drop Trailer OOR")
		{
			$data["checked"] = "checked";
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/driver_in_details_box',$data);
		}
		else if($log_entry["entry_type"] == "Check Call")
		{
			//GET CHECK_CALL
			$where = null;
			$where["log_entry_id"] = $log_entry_id;
			$check_call = db_select_check_call($where);
			
			//IF THERE IS NO CHECK CALL
			if(empty($check_call))
			{
				//INSERT NEW CHECK CALL
				$new_check_call = null;
				$new_check_call["log_entry_id"] = $log_entry_id;
				$new_check_call["night_dispatcher_id"] = $this->session->userdata('person_id');
				db_insert_check_call($new_check_call);
				
				//GET CHECK_CALL
				$where = null;
				$where["log_entry_id"] = $log_entry_id;
				$check_call = db_select_check_call($where);
			}

			//GET NIGHT DISPATCHER
			$where = null;
			$where["id"] = $check_call["night_dispatcher_id"];
			$night_dispatcher = db_select_person($where);
			
			$data["night_dispatcher"] = $night_dispatcher;
			$data["check_call"] = $check_call;
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/check_call_details_box',$data);
		}
		else if($log_entry["entry_type"] == "End Leg")
		{
			
			$leg_details = get_leg_details($log_entry_id);
			
			//UPDATE LEG IN DB
			$where = null;
			$where["log_entry_id"] = $log_entry_id;
			
			$update_leg = null;
			$update_leg["log_entry_id"] = $log_entry_id;
			$update_leg["load_id"] = $leg_details["load_id"];
			$update_leg["allocated_load_id"] = $leg_details["allocated_load_id"];
			$update_leg["truck_id"] = $leg_details["truck_id"];
			$update_leg["trailer_id"] = $leg_details["trailer_id"];
			$update_leg["main_driver_id"] = $leg_details["main_driver_id"];
			$update_leg["codriver_id"] = $leg_details["codriver_id"];
			//$update_leg["rate_type"] = $leg_details["rate_type"];
			//$update_leg["revenue_rate"] = $leg_details["revenue_rate"];
			$update_leg["odometer_miles"] = $leg_details["odometer_miles"];
			$update_leg["map_miles"] = $leg_details["map_miles"];
			$update_leg["hours"] = $leg_details["hours"];
			
			//ONLY UPDATE IF ENTRY IS NOT LOCKED
			if(empty($log_entry["locked_datetime"]))
			{
				db_update_leg($update_leg,$where);
			}
			$leg = db_select_leg($where);
			
			//UPDATE LEG EVENT WITH CURRENT INFORMATION
			$this_leg_event = null;
			//$this_leg_event["load_id"] = $log_entry["load_id"];
			$this_leg_event["allocated_load_id"] = $leg_details["allocated_load_id"];
			//$this_leg_event["truck_id"] = $log_entry["truck_id"];
			//$this_leg_event["trailer_id"] = $log_entry["trailer_id"];
			//$this_leg_event["main_driver_id"] = $log_entry["main_driver_id"];
			//$this_leg_event["codriver_id"] = $log_entry["codriver_id"];
			$this_leg_event["miles"] = $leg["map_miles"];
			$this_leg_event["out_of_route"] = $leg_details["oor"];
			$this_leg_event["route"] = $leg_details["route_url"];
			
			$where = null;
			$where["id"] = $log_entry_id;
			//ONLY UPDATE IF ENTRY IS NOT LOCKED
			if(empty($log_entry["locked_datetime"]))
			{
				db_update_log_entry($this_leg_event,$where);
			}
			
			//SET PROFIT SPLITS TO DRIVER DEFAULTS IF EMPTY
			if(empty($leg["main_driver_split"]))
			{
				$leg["main_driver_split"] = $leg["main_driver"]["profit_split"];
			}
			if(empty($leg["codriver_split"]))
			{
				$leg["codriver_split"] = $leg["codriver"]["profit_split"];
			}
			
			
			//GET FUEL EXPENSE ALLOCATED AND FUEL GALLONS ALLOCATED
			$fuel_allocation = get_fuel_allocations_for_leg($leg["id"]);
			
			//LEG VALIDATION ALERT CODES AND DIALOGS
			$leg_validation_alert[0] = "This leg looks good!";
			$leg_validation_alert[1] = "It looks like it is missing an Allocated Load. Try refreshing the Leg.";
			$leg_validation_alert[2] = "Check that the Odometers are chronological.";
			$leg_validation_alert[3] = "Check that the leg has a consistant Main Driver.";
			$leg_validation_alert[4] = "Check that the leg has a consistant Co-Driver.";
			$leg_validation_alert[5] = "Check that the leg has a consistant Trailer.";
			$leg_validation_alert[6] = "Check that the leg does not have multiple Loads (Partials and POs are OK).";
			
			$validation_code = leg_is_valid($log_entry);
			if($validation_code > 0) //IF LEG IS NOT VALID;
			{
				$validation_icon = "/images/invalid_icon.png";
			}
			else
			{
				$validation_icon = "/images/valid_icon.png";
			}
			
			
			$data["validation_icon"] = $validation_icon ;
			$data["leg_validation_alert"] = $leg_validation_alert[$validation_code] ;
			$data["fuel_allocation"] = $fuel_allocation;
			$data["leg"] = $leg;
			$data["leg_details"] = $leg_details;
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/new_leg_details',$data);
		}
		else if($log_entry["entry_type"] == "End Week")
		{
		
		
			$total_truck_hours = null;
			$odometer_miles = null;
			$map_info = null;
			$driver_1_stats = null;
			$driver_2_stats = null;
			
			$previous_truck_end_week_exists = false;
			$allow_lock = false;
			
			//GET END LEG ENTRY FOR THIS LOG ENTRY
			if(!empty($log_entry["sync_entry_id"]))
			{
				$where = null;
				$where["id"] = $log_entry["sync_entry_id"];
				$this_end_week_end_leg = db_select_log_entry($where);
				
			
				//IF END LEG FOR THIS END WEEK EXISTS
				if(!empty($this_end_week_end_leg))
				{
					//IF THIS END WEEK END LEG IS LOCKED - ALLOW LOCK FOR END WEEK
					if(!empty($this_end_week_end_leg["locked_datetime"]))
					{
						$allow_lock = true;
					}
				
					//IF THERE IS A TRUCK ON THIS END WEEK THEN GET ALL THE TRUCK STATISTICS
					if(!empty($log_entry["truck_id"]))
					{
						//GET PREVIOUS END WEEK
						$where = null;
						$where = " truck_id = ".$log_entry["truck_id"]." AND entry_type = 'End Week' AND entry_datetime = ( SELECT MAX(entry_datetime) FROM log_entry WHERE truck_id = ".$log_entry["truck_id"]." AND entry_type = 'End Week' AND entry_datetime < '".$log_entry["entry_datetime"]."')";
						$previous_end_week = db_select_log_entry($where);
						
						if(!empty($previous_end_week))
						{
							$previous_truck_end_week_exists = true;
						
							//GET ALL MAP EVENTS FOR THE WEEK FOR THIS TRUCK
							$where = null;
							$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime <= '".$log_entry["entry_datetime"]."'  AND entry_datetime >= '".$previous_end_week["entry_datetime"]."' AND (entry_type = 'Pick' OR entry_type = 'Drop' OR entry_type = 'Checkpoint' OR entry_type = 'Driver In' OR entry_type = 'Driver Out' OR entry_type = 'Pick Trailer' OR entry_type = 'Drop Trailer' OR entry_type = 'End Week') ";
							$map_events = db_select_log_entrys($where,'entry_datetime');

							//GET MAP INFO
							$map_info = get_map_info($map_events);
							
							//CALCULATE ODOMETER MILES
							$odometer_miles = $log_entry["odometer"] - $previous_end_week["odometer"];
							
							//GET END LEG ENTRY FOR PREVIOUS END WEEK
							$where = null;
							$where["id"] = $previous_end_week["sync_entry_id"];
							$previous_end_week_end_leg = db_select_log_entry($where);
							
							
						
							//GET ALL LEGS FOR THIS TRUCK THIS WEEK
							$where = null;
							$where = " leg.truck_id = ".$log_entry["truck_id"]." AND log_entry.entry_datetime > '".$previous_end_week_end_leg["entry_datetime"]."'  AND log_entry.entry_datetime <= '".$this_end_week_end_leg["entry_datetime"]."'";
							$legs = db_select_legs($where);
							//echo $where;
							
							
							//ADD UP HOURS FOR ALL LEGS
							$total_truck_hours = 0;
							foreach($legs as $leg)
							{
								$total_truck_hours = $total_truck_hours + $leg["hours"];
								//echo "Leg ".$leg["id"]."<br>";
							}
						}
						else
						{
							$previous_truck_end_week_exists = false;
						}
					}
				
					//IF THERE IS A DRIVER 1 ON THIS END WEEK THEN GET ALL THE DRIVERS STATISTICS
					if(!empty($log_entry["main_driver_id"]))
					{
						$driver_1_stats = get_driver_end_week_stats($log_entry,$log_entry["main_driver_id"]);
					}
					
					//IF THERE IS A DRIVER 2 ON THIS END WEEK THEN GET ALL THE DRIVERS STATISTICS
					if(!empty($log_entry["codriver_id"]))
					{
						$driver_2_stats = get_driver_end_week_stats($log_entry,$log_entry["codriver_id"]);
					}
				}
			}
			
			//CHECK TO SEE IF SETTLEMENTS WITH THIS END WEEK ID HAVE BEEN APPROVED
			$where = null;
			$where = " end_week_id = ".$log_entry_id." AND approved_datetime IS NOT NULL ";
			$settlements = db_select_settlements($where);
			
			$allow_delete = false;
			//IF NO SETTLEMENTS WITH THIS END WEEK ID HAVE BEEN APPROVED
			if(empty($settlements))
			{
				$allow_delete = true;
			}
			$data["allow_lock"] = $allow_lock;
			$data["allow_delete"] = $allow_delete;
			$data["driver_1_stats"] = $driver_1_stats;
			$data["driver_2_stats"] = $driver_2_stats;
			$data["previous_truck_end_week_exists"] = $previous_truck_end_week_exists;
			$data["total_truck_hours"] = $total_truck_hours;
			$data["odometer_miles"] = $odometer_miles;
			$data["map_info"] = $map_info;
			$data["log_entry_id"] = $log_entry_id;
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/detail_boxes/end_week_details_box',$data);
		}
		
	}
	
	function refresh_event()
	{
		$event_id = $_POST["event_id"];
		
		$where = null;
		$where["id"] = $event_id;
		$log_entry = db_select_log_entry($where);
		
		$data["log_entry"] = $log_entry;
		$this->load->view('logs/log_entry_row',$data);
		
	}
	
	function create_new_leg()
	{
		$log_entry_id = $_POST["event_id"];
		
		//GET ORIGIN EVENT ... this is the event that the New Leg will duplicate
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		//CHECK TO SEE IF THIS EVENT HAS ALREADY BEEN MADE INTO A NEW LEG
		if(!empty($log_entry["sync_entry_id"]))
		{
			echo "<script>alert('This event has already been marked as an End Leg!')</script>";
		}
		else
		{
			//echo "first leg <br>";
			
			//CREATE NEW LOG ENTRY
			$new_leg_event = null;
			$new_leg_event["recorder_id"] = $this->session->userdata('person_id');
			$new_leg_event["load_id"] = $log_entry["load_id"];
			$new_leg_event["allocated_load_id"] = $log_entry["allocated_load_id"];
			$new_leg_event["truck_id"] = $log_entry["truck_id"];
			$new_leg_event["trailer_id"] = $log_entry["trailer_id"];
			$new_leg_event["main_driver_id"] = $log_entry["main_driver_id"];
			$new_leg_event["codriver_id"] = $log_entry["codriver_id"];
			$new_leg_event["sync_entry_id"] = $log_entry["id"];
			$new_leg_event["entry_type"] = "End Leg";
			$new_leg_event["entry_datetime"] = date("Y-m-d G:i:s",(strtotime($log_entry["entry_datetime"]) + 1));
			$new_leg_event["city"] = $log_entry["city"];
			$new_leg_event["state"] =  $log_entry["state"];
			$new_leg_event["address"] =  $log_entry["address"];
			$new_leg_event["odometer"] =$log_entry["odometer"];
			$new_leg_event["entry_notes"] = "End Leg on ".$log_entry["entry_type"];
			db_insert_log_entry($new_leg_event);
			
			$where = null;
			$where["recorder_id"] = $this->session->userdata('person_id');
			$where["entry_datetime"] = date("Y-m-d G:i:s",(strtotime($log_entry["entry_datetime"]) + 1));
			$this_new_leg_event = db_select_log_entry($where);
			
			//UPDATE THE ALLOCATED LOAD
			$update = null;
			if(empty($this_new_leg_event["load_id"]))
			{
				//GET NEXT EVENT WITH A LOAD
				$where = null;
				$where = " truck_id = ".$this_new_leg_event["truck_id"]." AND entry_datetime > '".$this_new_leg_event["entry_datetime"]."' AND load_id IS NOT NULL ";
				$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
				
				if(!empty($next_loaded_event))
				{
					$update["allocated_load_id"] = $next_loaded_event["load_id"];
				}
				else
				{
					$update["allocated_load_id"] = $this_new_leg_event["load_id"];
				}
			}
			else
			{
				$update["allocated_load_id"] = $this_new_leg_event["load_id"];
			}
			$where = null;
			$where["id"] = $this_new_leg_event["id"];
			db_update_log_entry($update,$where);
			$where = null;
			$where["log_entry_id"] = $this_new_leg_event["id"];
			db_update_leg($update,$where);
			
			//UPDATE ORIGIN LOG ENTRY WITH NEW SYNC_ENTRY_ID
			$where = null;
			$where["id"] = $log_entry["id"];
			$update_log_entry["sync_entry_id"] = $this_new_leg_event["id"];
			db_update_log_entry($update_log_entry,$where);
			
			//echo "after update leg entry";
			
			//CREATE LEG OBJECT IN DB
			$leg = null;
			$leg["log_entry_id"] = $this_new_leg_event["id"];
			$leg["load_id"] = $this_new_leg_event["load_id"];
			$leg["allocated_load_id"] = $this_new_leg_event["allocated_load_id"];
			$leg["truck_id"] = $this_new_leg_event["truck_id"];
			$leg["trailer_id"] = $this_new_leg_event["trailer_id"];
			$leg["main_driver_id"] = $this_new_leg_event["main_driver_id"];
			$leg["codriver_id"] = $this_new_leg_event["codriver_id"];
			$leg["rate_type"] = "Auto";
			db_insert_leg($leg);
			
			
			echo "<script>load_log_list();</script>";
			
		}
	}
	
	//NEW EVENT DIALOG
	function load_new_event_form()
	{
		
		//GET ALL ACTIVE MAIN DRIVERS
		$where = null;
		$where["client_status"] = "Active";
		//$where["client_type"] = "Main Driver";
		$main_drivers = db_select_clients($where,"client_nickname");
		
		$main_driver_dropdown_options = array();
		$main_driver_dropdown_options["Select"] = "Select";
		foreach($main_drivers as $main_driver)
		{
			$main_driver_dropdown_options[$main_driver["id"]] = $main_driver["client_nickname"];
		}
		$main_driver_dropdown_options["None"] = "None";
		
		//GET ALL ACTIVE CODRIVERS
		$where = null;
		$where["client_status"] = "Active";
		//$where["client_type"] = "Co-Driver";
		$codrivers = db_select_clients($where,"client_nickname");
		
		$codriver_dropdown_options = array();
		$codriver_dropdown_options["Select"] = "Select";
		foreach($codrivers as $codriver)
		{
			$codriver_dropdown_options[$codriver["id"]] = $codriver["client_nickname"];
		}
		$codriver_dropdown_options["None"] = "None";
		
		//GET ALL ACTIVE TRUCKS
		$where = null;
		$where = " status != 'Returned' ";
		$trucks = db_select_trucks($where,"truck_number");
		
		$truck_dropdown_options = array();
		$truck_dropdown_options["Select"] = "Select";
		foreach($trucks as $truck)
		{
			$truck_dropdown_options[$truck["id"]] = $truck["truck_number"];
		}
		$truck_dropdown_options["None"] = "None";
		
		//GET ALL ACTIVE TRAILERS
		$where = null;
		$where = " trailer_status != 'Retired' ";
		$trailers = db_select_trailers($where,"trailer_number");
		
		$trailer_dropdown_options = array();
		$trailer_dropdown_options["Select"] = "Select";
		foreach($trailers as $trailer)
		{
			$trailer_dropdown_options[$trailer["id"]] = $trailer["trailer_number"];
		}
		$trailer_dropdown_options["None"] = "None";
		
		$data['main_driver_dropdown_options'] = $main_driver_dropdown_options;
		$data['codriver_dropdown_options'] = $codriver_dropdown_options;
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['trailer_dropdown_options'] = $trailer_dropdown_options;
		$this->load->view('logs/new_event_form',$data);
	}
	
	function check_load()
	{
		$load_number = $_POST["load_number"];
		
		$where = null;
		$where["customer_load_number"] = $load_number;
		$load = db_select_load($where);
		
		if(empty($load))
		{
			echo "<span style='font-weight:bold; color:red;'>Not Found</span>";
			echo "
				<script>
					$('#load_number_is_valid').val('false');
				</script>
				";
		}
		else
		{
			echo "<span style='font-weight:bold; color:green;'>Found</span>";
			echo "
				<script>
					$('#load_number_is_valid').val('true');
				</script>
				";
		}
		
	}
	
	function create_new_event()
	{
		date_default_timezone_set('America/Denver');
		$now_datetime = date("Y-m-d H:i:s");
	
		//GET _POST DATA
		$event_type = $_POST["event_type"];
		$load_number = $_POST["load_number"];
		$main_driver_id = $_POST["main_driver_id"];
		$codriver_id = $_POST["codriver_id"];
		$truck_id = $_POST["truck_id"];
		$trailer_id = $_POST["trailer_id"];
		$date = $_POST["date"];
		$time = $_POST["time"];
		$city = $_POST["city"];
		$state = $_POST["state"];
		$address = $_POST["address"];
		$odometer = $_POST["odometer"];
		$notes = $_POST["notes"];
		
		if(empty($load_number))
		{
			$load_id = null;
		}
		else
		{
			//GET LOAD ID
			$where = null;
			$where["customer_load_number"] = $load_number;
			$load = db_select_load($where);
			
			$load_id = null;
			if(!empty($load))
			{
				$load_id = $load["id"];
			}
		}
		
		if($event_type == "Fuel Stop")
		{
			if($_POST["is_fill"] == "Yes")
			{
				$event_type = "Fuel Fill";
			}
			else if($_POST["is_fill"] == "No")
			{
				$event_type = "Fuel Partial";
			}
		}
		
		if($main_driver_id == "None")
		{
			$main_driver_id = null;
		}
		
		if($codriver_id == 0)
		{
			$codriver_id = null;
		}
		
		if($trailer_id == 0)
		{
			$trailer_id = null;
		}
		
		if($event_type == "Check Call")
		{
			$entry_datetime =  $now_datetime;
			$new_log_entry["load_id"] = null;
			$new_log_entry["allocated_load_id"] = null;
			$new_log_entry["trailer_id"] = null;
			$new_log_entry["city"] = null;
			$new_log_entry["state"] =  null;
			$new_log_entry["address"] =  null;
			$new_log_entry["odometer"] = null;
			$new_log_entry["entry_notes"] = null;
			$new_log_entry["recorded_datetime"] = null;
		}
		else
		{
			$entry_datetime = date("Y-m-d G:i:s",strtotime($date." ".$time));
		}
		
		//CREATE NEW LOG ENTRY
		$new_log_entry = null;
		$new_log_entry["entry_type"] = $event_type;
		$new_log_entry["recorder_id"] = $this->session->userdata('person_id');
		$new_log_entry["truck_id"] = $truck_id;
		$new_log_entry["main_driver_id"] = $main_driver_id;
		$new_log_entry["codriver_id"] = $codriver_id;
		$new_log_entry["entry_datetime"] = $entry_datetime;
		$new_log_entry["load_id"] = $load_id;
		$new_log_entry["allocated_load_id"] = $load_id;
		$new_log_entry["trailer_id"] = $trailer_id;
		$new_log_entry["city"] = $city;
		$new_log_entry["state"] =  $state;
		$new_log_entry["address"] =  $address;
		$new_log_entry["odometer"] = $odometer;
		$new_log_entry["entry_notes"] = $notes;
		$new_log_entry["recorded_datetime"] = $now_datetime;
		db_insert_log_entry($new_log_entry);
		
		//GET NEWLY INSERTED LOG ENTRY
		$where = null;
		$where["recorder_id"] = $new_log_entry["recorder_id"];
		$where["recorded_datetime"] = $now_datetime;
		$log_entry = db_select_log_entry($where);
				
		//UPDATE THE ALLOCATED LOAD
		$update = null;
		if(empty($log_entry["load_id"]))
		{
			//GET NEXT EVENT WITH A LOAD
			$where = null;
			$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
			$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
			
			if(!empty($next_loaded_event))
			{
				$update["allocated_load_id"] = $next_loaded_event["load_id"];
			}
			else
			{
				$update["allocated_load_id"] = $log_entry["load_id"];
			}
		}
		else
		{
			$update["allocated_load_id"] = $log_entry["load_id"];
		}
		
		$where = null;
		$where["id"] = $log_entry["id"];
		db_update_log_entry($update,$where);
		
		if($event_type == "Fuel Fill" || $event_type == "Fuel Partial")
		{
			//INSERT FUEL STOP INTO THE DB
			$fuel_stop = null;
			$fuel_stop["log_entry_id"] = $log_entry["id"];
			$fuel_stop["is_fill"] = $_POST["is_fill"];
			$fuel_stop["gallons"] = $_POST["gallons"];
			$fuel_stop["fuel_price"] = $_POST["fuel_price"];
			$fuel_stop["fuel_expense"] = $_POST["fuel_expense"];
			$fuel_stop["rebate_amount"] = 0;
			$fuel_stop["source"] = $_POST["source"];
			
			db_insert_fuel_stop($fuel_stop);
			
			//UPDATE ALL FUEL STOP CALCULATIONS FOR UNLOCKED FUEL STOPS EVENTS
			update_fuel_calculations();
		}
		else if($event_type == "Check Call")
		{
			$check_call["log_entry_id"] = $log_entry["id"];
			$check_call["night_dispatcher_id"] = $this->session->userdata('person_id');
			
			db_insert_check_call($check_call);
		}
	}
	
	function get_dropdown()
	{
		$field_name = $_POST["field_name"];
		
		if($field_name == "main_driver_id")
		{
			//GET ALL ACTIVE MAIN DRIVERS
			$where = null;
			$where["client_status"] = "Active";
			$where["client_type"] = "Main Driver";
			$main_drivers = db_select_clients($where,"client_nickname");
			
			$main_driver_dropdown_options = array();
			$main_driver_dropdown_options["None"] = "None";
			foreach($main_drivers as $main_driver)
			{
				$main_driver_dropdown_options[$main_driver["id"]] = $main_driver["client_nickname"];
			}
			
			$options = $main_driver_dropdown_options;
		}
		else if($field_name == "codriver_id")
		{
			//GET ALL ACTIVE CODRIVERS
			$where = null;
			$where["client_status"] = "Active";
			$where["client_type"] = "Co-Driver";
			$codrivers = db_select_clients($where,"client_nickname");
			
			$codriver_dropdown_options = array();
			$codriver_dropdown_options["None"] = "None";
			foreach($codrivers as $codriver)
			{
				$codriver_dropdown_options[$codriver["id"]] = $codriver["client_nickname"];
			}
			
			$options = $codriver_dropdown_options;
		}
		else if($field_name == "truck_id")
		{
			//GET ALL ACTIVE TRUCKS
			$where = null;
			$where = " status != 'Returned' ";
			$trucks = db_select_trucks($where,"truck_number");
			
			$truck_dropdown_options = array();
			$truck_dropdown_options["None"] = "None";
			foreach($trucks as $truck)
			{
				$truck_dropdown_options[$truck["id"]] = $truck["truck_number"];
			}
			
			$options = $truck_dropdown_options;
		}
		else if($field_name == "trailer_id")
		{
			//GET ALL ACTIVE TRAILERS
			$where = null;
			$where = " trailer_status != 'Retired' ";
			$trailers = db_select_trailers($where,"trailer_number");
			
			$trailer_dropdown_options = array();
			$trailer_dropdown_options["None"] = "None";
			foreach($trailers as $trailer)
			{
				$trailer_dropdown_options[$trailer["id"]] = $trailer["trailer_number"];
			}
			
			$options = $trailer_dropdown_options;
		}
		
		$data["options"] = $options;
		$this->load->view('logs/edit_cell_header_dropdown',$data);
	}
	
	function save_edit_cell()
	{
		$log_entry_id = $_POST["log_entry_id"];
		$field_name = $_POST["field_name"];
		$cell_value = $_POST["cell_value"];
		
		$is_valid = true;
			
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		$sync_event = null;
		if(!empty($log_entry["sync_entry_id"]))
		{
			$where = null;
			$where["id"] = $log_entry["sync_entry_id"];
			$sync_event = db_select_log_entry($where);
		}
		
		//ONLY UPDATE IF EVENT IS NOT LOCKED
		if(empty($log_entry["locked_datetime"]) && empty($sync_event["locked_datetime"]))
		{
		
			//VALIDATE LOAD NUMBER
			if($field_name == "load_number")
			{
				$field_name = "load_id";
				
				
				if(empty($cell_value))
				{
					$cell_value = null;
				}
				else
				{
					//CHECK IF LOAD EXISTS
					$where = null;
					$where["customer_load_number"] = $cell_value;
					$load = db_select_load($where);
					
					if(!empty($load))
					{
						$cell_value = $load["id"];
					}
					else
					{
						$is_valid = false;
					}
				}
			}
				
			
			if($field_name == "time")
			{
				
				$field_name = "entry_datetime";
				
				//IF ENTRY IS AN END LEG, ADD ONE SECOND TO THE DATETIME
				if($log_entry["entry_type"] == "End Leg")
				{
					$datetime = date("Y-m-d",strtotime($log_entry["entry_datetime"]))." ".date("H:i",strtotime($cell_value));
					$cell_value = date("Y-m-d G:i:s",(strtotime($datetime) + 1));
				}
				else
				{
					$cell_value = date("Y-m-d",strtotime($log_entry["entry_datetime"]))." ".date("H:i",strtotime($cell_value));
				}
			}
			
			if($field_name == "date")
			{
				
				$field_name = "entry_datetime";
				//IF ENTRY IS AN END LEG, ADD ONE SECOND TO THE DATETIME
				if($log_entry["entry_type"] == "End Leg")
				{
					$datetime = date("Y-m-d",strtotime($cell_value))." ".date("H:i",strtotime($log_entry["entry_datetime"]));
					$cell_value = date("Y-m-d H:i:s",(strtotime($datetime) + 1));
				}
				else
				{
					$cell_value = date("Y-m-d",strtotime($cell_value))." ".date("H:i",strtotime($log_entry["entry_datetime"]));
				}
			}
			
			
			if($is_valid)
			{
			
				$update["$field_name"] = $cell_value;
				
				$where = null;
				$where["id"] = $log_entry_id;
				db_update_log_entry($update,$where);
				
				$log_entry = db_select_log_entry($where);
				
				//UPDATE THE ALLOCATED LOAD
				$update = null;
				if(empty($log_entry["load_id"]))
				{
					//GET NEXT EVENT WITH A LOAD
					$where = null;
					$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
					$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
					
					if(!empty($next_loaded_event))
					{
						$update["allocated_load_id"] = $next_loaded_event["load_id"];
					}
					else
					{
						$update["allocated_load_id"] = $log_entry["load_id"];
					}
				}
				else
				{
					$update["allocated_load_id"] = $log_entry["load_id"];
				}
				$where = null;
				$where["id"] = $log_entry["id"];
				db_update_log_entry($update,$where);
				$where = null;
				$where["log_entry_id"] = $log_entry["id"];
				db_update_leg($update,$where);
				
				$extra_script = "";
				if(!empty($log_entry["sync_entry_id"]))
				{
					$where = null;
					$where["id"] = $log_entry["sync_entry_id"];
					$sync_event = db_select_log_entry($where);
					
					//SYNC LEG ENTRY WITH ORIGIN ENTRY... KEEP THE SAME NOTES... CHANGE THE REST
					$update_sync["load_id"] = $log_entry["load_id"];
					$update_sync["truck_id"] = $log_entry["truck_id"];
					$update_sync["trailer_id"] = $log_entry["trailer_id"];
					$update_sync["main_driver_id"] = $log_entry["main_driver_id"];
					$update_sync["codriver_id"] = $log_entry["codriver_id"];
					$update_sync["city"] = $log_entry["city"];
					$update_sync["state"] = $log_entry["state"];
					$update_sync["address"] = $log_entry["address"];
					$update_sync["odometer"] = $log_entry["odometer"];

					//IF ENTRY IS AND END LEG, AT ONE SECOND TO THE DATETIME
					if($sync_event["entry_type"] == "End Leg")
					{
						$update_sync["entry_datetime"] = date("Y-m-d G:i:s",(strtotime($log_entry["entry_datetime"]) + 1));
					}
					else
					{
						$update_sync["entry_datetime"] = date("Y-m-d G:i:s",(strtotime($log_entry["entry_datetime"]) - 1));
					}
					
					$where = null;
					$where["id"] = $sync_event["id"];
					db_update_log_entry($update_sync,$where);
					
					$extra_script = "refresh_event('".$log_entry["sync_entry_id"]."');";
					
				}
				
				if($field_name == "entry_datetime")
				{
					$extra_script = "load_log_list();";
				}
				
				
				$data["log_entry"] = $log_entry;
				$this->load->view('logs/log_entry_row',$data);
				echo "
					<script>
						
						$('#edit_cell_header_dropdown').hide();
						$('#edit_cell_header').hide();
						$('#plain_header').show();
						$extra_script
					</script>
					";
			}
			else // IF NOT VALID
			{
				$where = null;
				$where["id"] = $log_entry_id;
				$log_entry = db_select_log_entry($where);
				
				$data["log_entry"] = $log_entry;
				$this->load->view('logs/log_entry_row',$data);
				echo '
					<script>
						$("#"+previous_field_name+"_"+previous_log_entry_id).css("border","solid");
						$("#"+previous_field_name+"_"+previous_log_entry_id).css("border-color","#6295FC");
						$("#edit_cell_header").show();
						$("#plain_header").hide();
						$("#cell_value").focus();
						alert("Invalid Load Number!");
					</script>
					';
			}
		}
		else //IF ENTRY IS LOCKED
		{
			$where = null;
			$where["id"] = $log_entry_id;
			$log_entry = db_select_log_entry($where);
			
			$data["log_entry"] = $log_entry;
			$this->load->view('logs/log_entry_row',$data);
			echo '
					<script>
						alert("This event is locked!");
					</script>
					';
		}
	}
	
	function open_fuel_allocations()
	{
		$log_entry_id = $_POST["log_entry_id"];
	
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$this_event = db_select_log_entry($where);
	
		//IF THIS EVENT IS NOT LOCKED
		if(empty($this_event["locked_datetime"]))
		{
			//CREATE NEW FUEL ALLOCATIONS WITH UPDATED INFO
			create_fuel_allocations($log_entry_id);
		}
		
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$this_event = db_select_log_entry($where);
		
		//GET FUEL STOP
		$where = null;
		$where["log_entry_id"] = $log_entry_id;
		$fuel_stop = db_select_fuel_stop($where);
		
		//GET FUEL ALLOCATIONS FOR THIS FUEL STOP
		$where = null;
		$where["fuel_stop_id"] = $fuel_stop["id"];
		$fuel_allocations = db_select_fuel_allocations($where);
		
		if(empty($fuel_allocations))
		{
			echo "<div style='margin-left:75px; margin-bottom:15px; margin-top:15px;'>There currently are no fuel allocations for this fuel stop</div>";
		}
		else
		{
			$data["fuel_stop"] = $fuel_stop;
			$data["fuel_allocations"] = $fuel_allocations;
			$this->load->view('logs/fuel_allocations_details',$data);
		}
		
	}
	
	//ACTION WHEN CHECKBOX ON CHECKPOINT EVENT DETAILS IS CLICKED
	function mark_oor()
	{
		$log_entry_id = $_POST["log_entry_id"];
		$is_oor = $_POST["is_oor"]; //yes or no
		
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		if($log_entry["entry_type"] == "Checkpoint" || $log_entry["entry_type"] == "Checkpoint OOR")
		{
			if($is_oor == "yes")
			{
				$update_entry["entry_type"] = "Checkpoint OOR";
			}
			else
			{
				$update_entry["entry_type"] = "Checkpoint";
			}
		}
		else if($log_entry["entry_type"] == "Driver In" || $log_entry["entry_type"] == "Driver In OOR")
		{
			if($is_oor == "yes")
			{
				$update_entry["entry_type"] = "Driver In OOR";
			}
			else
			{
				$update_entry["entry_type"] = "Driver In";
			}
		}
		else if($log_entry["entry_type"] == "Driver Out" || $log_entry["entry_type"] == "Driver Out OOR")
		{
			if($is_oor == "yes")
			{
				$update_entry["entry_type"] = "Driver Out OOR";
			}
			else
			{
				$update_entry["entry_type"] = "Driver Out";
			}
		}
		else if($log_entry["entry_type"] == "Pick Trailer" || $log_entry["entry_type"] == "Pick Trailer OOR")
		{
			if($is_oor == "yes")
			{
				$update_entry["entry_type"] = "Pick Trailer OOR";
			}
			else
			{
				$update_entry["entry_type"] = "Pick Trailer";
			}
		}
		else if($log_entry["entry_type"] == "Drop Trailer" || $log_entry["entry_type"] == "Drop Trailer OOR")
		{
			if($is_oor == "yes")
			{
				$update_entry["entry_type"] = "Drop Trailer OOR";
			}
			else
			{
				$update_entry["entry_type"] = "Drop Trailer";
			}
		}
		
		$where = null;
		$where["id"] = $log_entry_id;
		db_update_log_entry($update_entry,$where);
		
	}
	
	//DELETE EVENT
	function delete_event()
	{
		$log_entry_id = $_POST["log_entry_id"];
		
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		//IF LOG ENTRY IS AN END LEG, DELETE LEG
		$where = null;
		$where["log_entry_id"] = $log_entry["id"];
		db_delete_leg($where);
		
		//IF LOG ENTRY IS AN END WEEK, DELETE ANY SETTLEMENTS WITH THIS END WEEK ID
		$where = null;
		$where["end_week_id"] = $log_entry_id;
		db_delete_settlement($where);
				
		//DELETE LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		db_delete_log_entry($where);
		
		//IF LOG ENTRY HAS SYNC EVENT, DELETE
		if(!empty($log_entry["sync_entry_id"]))
		{
			if($log_entry["entry_type"] == "End Leg")
			{
				//DELETE SYNC ENTRY ID
				$update_event["sync_entry_id"] = null;
			
				$where = null;
				$where["id"] = $log_entry["sync_entry_id"];
				db_update_log_entry($update_event,$where);
			}
			else
			{
				//IF SYNC EVENT IS AN END LEG, DELETE LEG
				$where = null;
				$where["log_entry_id"] = $log_entry["sync_entry_id"];
				db_delete_leg($where);
			
				//DELETE LOG ENTRY SYNC EVENT
				$where = null;
				$where["id"] = $log_entry["sync_entry_id"];
				db_delete_log_entry($where);
			}
		}
		
		//IF LOG ENTRY IS A FUEL STOP
		if($log_entry["entry_type"] == "Fuel Fill" || $log_entry["entry_type"] == "Fuel Partial")
		{
			//GET FUEL STOP
			$where = null;
			$where["log_entry_id"] = $log_entry["id"];
			$fuel_stop = db_select_fuel_stop($where);
			
			//IF FUEL STOP SOURCE IS ESTIMATE
			if($fuel_stop["source"] == "Estimate")
			{
				//DELETE FUEL ALLOCATIONS
				$where = null;
				$where["fuel_stop_id"] = $fuel_stop["id"];
				db_delete_fuel_allocation($where);
			
				//DELETE THE FUEL STOP
				$where = null;
				$where["log_entry_id"] = $log_entry["id"];
				db_delete_fuel_stop($where);
			}
			
		}
				
	}
	
	//DISPLAYS THE FUEL REPORT BEFORE ACTUAL UPLOAD
	function upload_fuel_report()
	{
		
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
			$file = $this->upload->data();
			$file_name = $file["file_name"];
			
			//echo $file_name;
			
			$entries = array();
			$csv_doc = fopen("./uploads/$file_name", "r");
			$row_number = 1;
			//FOREACH ROW
			while (($row = fgetcsv($csv_doc)) !== false) 
			{
				//echo $row_number;
				//echo "<br>";
				if($row_number > 9)
				{
					$column = 1;
					//FOR EACH CELL
					foreach ($row as $cell) 
					{
						if($column == 3)
						{
							$date = htmlspecialchars($cell);
						}
						else if($column == 5)
						{
							$time = htmlspecialchars($cell);
						}
						elseif($column == 10)
						{
							$card_name = htmlspecialchars($cell);
						}
						elseif($column == 14)
						{
							$unit_number = htmlspecialchars($cell);
						}
						elseif($column == 15)
						{
							$odometer = (int)str_replace(",","",htmlspecialchars($cell));
						}
						elseif($column == 17)
						{
							$trailer = htmlspecialchars($cell);
						}
						elseif($column == 21)
						{
							$is_fill = htmlspecialchars($cell);
						}
						elseif($column == 25)
						{
							$truck_stop = htmlspecialchars($cell);
						}
						elseif($column == 26)
						{
							$address = htmlspecialchars($cell);
						}
						elseif($column == 27)
						{
							$city = htmlspecialchars($cell);
						}
						elseif($column == 28)
						{
							$state = htmlspecialchars($cell);
						}
						elseif($column == 31)
						{
							$total_amount = round(substr(htmlspecialchars($cell),1),2);
						}
						elseif($column == 34)
						{
							$gallons = round(htmlspecialchars($cell),2);
						}
						elseif($column == 58)
						{
							$rebate_amount =round(substr(htmlspecialchars($cell),1),2);
						}
						
						$column++;
					}//END CELL
					
					
					$entry["truck_number"] = $unit_number;
					$entry["trailer_number"] = $trailer;
					$entry["card_name"] = $card_name;
					$entry["entry_datetime"] = date("Y-m-d H:i:s",strtotime($date." ".$time));
					$entry["is_fill"] = $is_fill;
					$entry["city"] = $city;
					$entry["state"] = $state;
					$entry["address"] = $address;
					$entry["odometer"] = $odometer;
					$entry["gallons"] = $gallons;
					$entry["fuel_expense"] = round($total_amount - $rebate_amount,2);
					$entry["rebate_amount"] = $rebate_amount;
					$entry["entry_notes"] = $truck_stop;
					
					//CREATE GUID FOR FUEL STOP
					$entry["guid"] = strtotime($entry["entry_datetime"])+ $entry["fuel_expense"];
					
					
					
					//ADD ENTRY TO THE ARRAY
					if(!empty($date))
					{
						$entries[] = $entry;
						//db_insert_account_entry($entry);
					}
					
				}//END ROW
				
				$row_number++;
			}
			fclose($csv_doc);
			
			//GET ALL ACTIVE MAIN DRIVERS
			$where = null;
			$where = " (client_status = 'Active' OR client_status = 'Pending Closure') AND client_type = 'Main Driver' ";
			$main_drivers = db_select_clients($where,"fuel_card_name");
			
			$driver_array = array();
			$reverse_driver_array = array();
			$main_driver_dropdown_options = array();
			$main_driver_dropdown_options["Select"] = "Select";
			foreach($main_drivers as $main_driver)
			{
				$driver_array[] = substr(strtoupper($main_driver["fuel_card_name"]),0,12);
				$reverse_driver_array[substr(strtoupper($main_driver["fuel_card_name"]),0,12)] = $main_driver["id"];
				$main_driver_dropdown_options[$main_driver["id"]] = substr(strtoupper($main_driver["fuel_card_name"]),0,12);
			}
			
			
			//GET ALL ACTIVE TRUCKS
			$where = null;
			$where = " status != 'Returned' ";
			$trucks = db_select_trucks($where,"truck_number");
			
			$truck_array = array();
			$reverse_truck_array = array();
			$truck_dropdown_options = array();
			$truck_dropdown_options["Select"] = "Select";
			foreach($trucks as $truck)
			{
				$truck_array[] = $truck["truck_number"];
				$reverse_truck_array[$truck["truck_number"]] = $truck["id"];
				$truck_dropdown_options[$truck["id"]] = $truck["truck_number"];
			}
			
			//GET ALL ACTIVE TRAILERS
			$where = null;
			$where = " trailer_status != 'Retired' ";
			$trailers = db_select_trailers($where,"trailer_number");
			
			$trailer_array = array();
			$reverse_trailer_array = array();
			$trailer_dropdown_options = array();
			$trailer_dropdown_options["Select"] = "Select";
			foreach($trailers as $trailer)
			{
				$trailer_array[] = $trailer["trailer_number"];
				$reverse_trailer_array[$trailer["trailer_number"]] = $trailer["id"];
				$trailer_dropdown_options[$trailer["id"]] = $trailer["trailer_number"];
			}
			$trailer_dropdown_options['None'] = 'None';
			
			//GET ALL FLEETMANAGERS
			$where = null;
			$where["role"] = "Fleet Manager";
			$fleet_managers = db_select_persons($where);
			$fm_dropdown_options = array();
			$fm_dropdown_options["Select"] = "Select";
			foreach($fleet_managers as $fm)
			{
				$where = null;
				$where["person_id"] = $fm["id"];
				$where["type"] = "Fleet Manager";
				$fm_company = db_select_company($where);
				
				$where = null;
				$where["company_id"] = $fm_company["id"];
				$where["category"] = "Pay";
				$fm_pay_account = db_select_account($where);
				
				if(!empty($fm_pay_account))
				{
					$fm_dropdown_options[$fm_pay_account["id"]] = $fm["f_name"];
				}
			}
			
			$data['fm_dropdown_options'] = $fm_dropdown_options;
			$data['main_drivers'] = $main_drivers;
			$data['reverse_driver_array'] = $reverse_driver_array;
			$data['driver_array'] = $driver_array;
			$data['main_driver_dropdown_options'] = $main_driver_dropdown_options;
			$data['trucks'] = $trucks;
			$data['reverse_truck_array'] = $reverse_truck_array;
			$data['truck_array'] = $truck_array;
			$data['truck_dropdown_options'] = $truck_dropdown_options;
			$data['trailers'] = $trailers;
			$data['reverse_trailer_array'] = $reverse_trailer_array;
			$data['trailer_array'] = $trailer_array;
			$data['trailer_dropdown_options'] = $trailer_dropdown_options;
			$data['entries'] = $entries;
			$data['file_name'] = $file_name;
			$this->load->view('logs/fuel_report_table',$data);
			
		}
	}
	
	//THIS ACTUALLY ADDS THE FUEL STOPS TO THE DB
	function add_fuel_stops()
	{
		$number_of_trans = $_POST["number_of_trans"];
		date_default_timezone_set('America/Denver');
		for($i = 1; $i <= $number_of_trans; $i++)
		{
			$fuel_stop["guid"] = $_POST["guid_$i"];
			if(!fuel_stop_exists($fuel_stop))
			{
				if($_POST["is_fill_$i"] == "Y")
				{
					$entry_type = "Fuel Fill";
					$is_fill = "Yes";
				}
				else
				{
					$entry_type = "Fuel Partial";
					$is_fill = "No";
				}
				
				
				//INSERT LOG ENTRY INTO DB
				$log_entry = null;
				
				if($_POST["trailer_number_$i"] == "None")
				{
					//DONT INCLUDE TRAILER
				}
				else
				{
					$log_entry["trailer_id"] = $_POST["trailer_number_$i"];
				}
				
				$log_entry["recorder_id"] = $this->session->userdata('person_id');
				$log_entry["recorded_datetime"] = date("Y-m-d H:i:s");
				$log_entry["truck_id"] = $_POST["truck_number_$i"];
				$log_entry["main_driver_id"] = $_POST["card_name_$i"];
				$log_entry["entry_type"] = $entry_type;
				$log_entry["entry_datetime"] = $_POST["entry_datetime_$i"];
				$log_entry["city"] = $_POST["city_$i"];
				$log_entry["state"] = $_POST["state_$i"];
				$log_entry["address"] = $_POST["address_$i"];
				$log_entry["odometer"] = $_POST["odometer_$i"];
				$log_entry["entry_notes"] = $_POST["notes_$i"];
				db_insert_log_entry($log_entry);
				
				
				//GET THE NEWLY CREATED LOG ENTRY
				$new_log_entry = db_select_log_entry($log_entry);
				
				if($_POST["gallons_$i"] == 0)
				{
					$fuel_price = 0;
				}
				else
				{
					$fuel_price = round($_POST["fuel_expense_$i"]/$_POST["gallons_$i"],3);//AFTER REBATE PRICE PER GALLON
				}
				
				//GET FUEL STOP INFO AND INSERT INTO DB
				$fuel_stop = null;
				$fuel_stop["log_entry_id"] = $new_log_entry["id"];
				$fuel_stop["is_fill"] = $is_fill;
				$fuel_stop["gallons"] = $_POST["gallons_$i"];
				$fuel_stop["fuel_price"] = $fuel_price;//AFTER REBATE PRICE PER GALLON
				$fuel_stop["fuel_expense"] = $_POST["fuel_expense_$i"]; //AFTER THE REBATE
				$fuel_stop["rebate_amount"] = $_POST["rebate_amount_$i"];
				$fuel_stop["natl_fuel_avg"] = get_natl_fuel_avg_from_db($log_entry["entry_datetime"]);
				$fuel_stop["source"] = "ComData";
				$fuel_stop["guid"] = $_POST["guid_$i"];
				db_insert_fuel_stop($fuel_stop);
				
				//GET MAIN DRIVER
				$where = null;
				$where["id"] = $_POST["card_name_$i"];
				$driver = db_select_client($where);
				
				//INSERT DEBIT ENTRY INTO FM PAY ACCOUNT
				$driver_pay_entry = null;
				$driver_pay_entry["account_id"] = $_POST["fm_$i"];
				$driver_pay_entry["recorder_id"] = $this->session->userdata('person_id');
				$driver_pay_entry["entry_datetime"] = $_POST["entry_datetime_$i"];
				$driver_pay_entry["entry_type"] = "Fuel Expense";
				$driver_pay_entry["debit_credit"] = "Debit";
				$driver_pay_entry["entry_amount"] = $_POST["fuel_expense_$i"]; //AFTER THE REBATE
				$driver_pay_entry["entry_description"] = "Fuel Stop | ".$driver["client_nickname"]." | ".$_POST["notes_$i"]." ".$_POST["city_$i"].", ".$_POST["state_$i"];
				
				//db_insert_account_entry($driver_pay_entry);
				
				if($driver["pay_structure"] == "Revenue Based")
				{
					//INSERT DEBIT ENTRY INTO DRIVER PAY ACCOUNT
					$driver_pay_entry = null;
					$driver_pay_entry["account_id"] = $driver["main_account"];
					$driver_pay_entry["recorder_id"] = $this->session->userdata('person_id');
					$driver_pay_entry["entry_datetime"] = $_POST["entry_datetime_$i"];
					$driver_pay_entry["entry_type"] = "Fuel Expense";
					$driver_pay_entry["debit_credit"] = "Debit";
					$driver_pay_entry["entry_amount"] = $_POST["fuel_expense_$i"]; //AFTER THE REBATE
					$driver_pay_entry["entry_description"] = "Fuel Stop | ".$driver["client_nickname"]." | ".$_POST["notes_$i"]." ".$_POST["city_$i"].", ".$_POST["state_$i"];
					
					db_insert_account_entry($driver_pay_entry);
				}
				else
				{
					//GET FM PAY ACCOUNT
					$where = null;
					$where["id"] = $_POST["fm_$i"];
					$fm_pay_account = db_select_account($where);
					
					//GET FM FUEL ALLOCATION ACCOUNT
					$where = null;
					$where["company_id"] = $fm_pay_account["company_id"];
					$where["account_group"] = "Fuel Allocations";
					$fm_fuel_allocation_account = db_select_account($where);
					
					
					//INSERT DEBIT TO FUEL ALLOCATION ACCOUNT
					$fuel_allocation_entry = null;
					$fuel_allocation_entry["account_id"] = $fm_fuel_allocation_account["id"];
					$fuel_allocation_entry["recorder_id"] = $this->session->userdata('person_id');
					$fuel_allocation_entry["entry_datetime"] = $_POST["entry_datetime_$i"];
					$fuel_allocation_entry["entry_type"] = "Fuel Expense";
					$fuel_allocation_entry["debit_credit"] = "Debit";
					$fuel_allocation_entry["entry_amount"] = $_POST["fuel_expense_$i"]; //AFTER THE REBATE
					$fuel_allocation_entry["entry_description"] = "Fuel Stop | ".$driver["client_nickname"]." | ".$_POST["notes_$i"]." ".$_POST["city_$i"].", ".$_POST["state_$i"];
					
					db_insert_account_entry($fuel_allocation_entry);
					
					//UPDATE FUEL STOP WITH ALLOCATION ENTRY ID
					$where = null;
					$where["guid"] = $_POST["guid_$i"];
					$update_fuel_stop = null;
					$update_fuel_stop["allocation_account_id"] = $fm_fuel_allocation_account["id"];
					db_update_fuel_stop($update_fuel_stop,$where);
					
				}
				
				
				
			}
			
		}
		
		//UPDATE ALL FUEL STOP CALCULATIONS FOR UNLOCKED FUEL STOPS EVENTS
		update_fuel_calculations();
		
		redirect(base_url("index.php/logs"));
		
	}
	
	//SAVE FUEL STOP EDIT
	function save_fuel_stop()
	{
		$log_entry_id = $_POST["log_entry_id"];
		$fuel_stop_id = $_POST["fuel_stop_id"];
		$is_fill = $_POST["is_fill"];
		$fill_to_fill_gallons = $_POST["f2f_gallons"];
		$fill_to_fill_expense = $_POST["f2f_expense"];
		$fill_to_fill_rebate = $_POST["fill_to_fill_rebate"];
		$map_miles = $_POST["f2f_miles"];
		$odom_miles = $_POST["odom_miles"];
		
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		//UPDATE THE ALLOCATED LOAD
		$update = null;
		if(empty($log_entry["load_id"]))
		{
			//GET NEXT EVENT WITH A LOAD
			$where = null;
			$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
			$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
			
			if(!empty($next_loaded_event))
			{
				$update["allocated_load_id"] = $next_loaded_event["load_id"];
			}
			else
			{
				$update["allocated_load_id"] = $log_entry["load_id"];
			}
		}
		else
		{
			$update["allocated_load_id"] = $log_entry["load_id"];
		}
		$where = null;
		$where["id"] = $log_entry["id"];
		db_update_log_entry($update,$where);
		
		
		if($is_fill == "Yes")
		{
			$entry_type = "Fuel Fill";
			
			//UPDATE FUEL STOP
			$update_fuel_stop["is_fill"] = $is_fill;
			$update_fuel_stop["natl_fuel_avg"] =get_natl_fuel_avg_from_db($log_entry["entry_datetime"]);
			$update_fuel_stop["fill_to_fill_gallons"] = $fill_to_fill_gallons;
			$update_fuel_stop["fill_to_fill_expense"] = $fill_to_fill_expense;
			$update_fuel_stop["fill_to_fill_rebate"] = $fill_to_fill_rebate;
			$update_fuel_stop["map_miles"] = $map_miles;
			$update_fuel_stop["odom_miles"] = $odom_miles;
			
			$where = null;
			$where["id"] = $fuel_stop_id;
			db_update_fuel_stop($update_fuel_stop,$where);
			
			
			//UPDATE FUEL STOP EVENT
			$update_log_entry["entry_type"] = $entry_type;
			
			$where = null;
			$where["id"] = $log_entry_id;
			db_update_log_entry($update_log_entry,$where);
		
		}
		else
		{
			$entry_type = "Fuel Partial";
			
			//UPDATE FUEL STOP
			$update_fuel_stop["is_fill"] = $is_fill;
			
			$where = null;
			$where["id"] = $fuel_stop_id;
			db_update_fuel_stop($update_fuel_stop,$where);
			
			
			//UPDATE FUEL STOP EVENT
			$update_log_entry["entry_type"] = $entry_type;
			$update_log_entry["route"] = null;
			$update_log_entry["miles"] = null;
			$update_log_entry["out_of_route"] = null;
			$update_log_entry["mpg"] = null;
			
			$where = null;
			$where["id"] = $log_entry_id;
			db_update_log_entry($update_log_entry,$where);
			
			//DELETE FUEL ALLOCATIONS
			$where = null;
			$where["fuel_stop_id"] = $fuel_stop_id;
			db_delete_fuel_allocation($where);
		}
		
	}
	
	//SAVE LEG
	function save_leg()
	{
		$log_entry_id = $_POST["log_entry_id"];
		$fuel_expense = $_POST["fuel_expense_".$log_entry_id];
		$gallons_used = $_POST["gallons_used_".$log_entry_id];
		
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		//UPDATE THE ALLOCATED LOAD
		$update = null;
		if(empty($log_entry["load_id"]))
		{
			//GET NEXT EVENT WITH A LOAD
			$where = null;
			$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
			$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
			
			if(!empty($next_loaded_event))
			{
				$update["allocated_load_id"] = $next_loaded_event["load_id"];
			}
			else
			{
				$update["allocated_load_id"] = $log_entry["load_id"];
			}
		}
		else
		{
			$update["allocated_load_id"] = $log_entry["load_id"];
		}
		$where = null;
		$where["id"] = $log_entry["id"];
		db_update_log_entry($update,$where);
		
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		
		//FORMAT FUEL EXPENSE AND GALLONS USED
		if(empty($fuel_expense))
		{
			$fuel_expense = null;
		}
		else
		{
			$fuel_expense = round($fuel_expense,2);
		}
		
		if(empty($gallons_used))
		{
			$gallons_used = null;
		}
		else
		{
			$gallons_used = round($gallons_used,2);
		}
		
		//DETERMINE REVENUE RATE
		$rate_type = $_POST["rate_type"];
		
		if($rate_type == "Loaded")
		{
			$target_mpg = 6;
		}
		else if($rate_type == "Dead Head")
		{
			$target_mpg = 7.5;
		}
		else if($rate_type == "Bobtail")
		{
			$target_mpg = 9;
		}
		
		$revenue_rate = round($_POST["natl_fuel_avg"]/$target_mpg + 0.66,2);
		
		
		$where = null;
		$where["log_entry_id"] = $_POST["log_entry_id"];
		
		$update_leg["log_entry_id"] = $log_entry["id"];
		$update_leg["load_id"] = $log_entry["load_id"];
		$update_leg["allocated_load_id"] = $log_entry["allocated_load_id"];
		$update_leg["truck_id"] = $log_entry["truck_id"];
		$update_leg["trailer_id"] = $log_entry["trailer_id"];
		$update_leg["main_driver_id"] = $log_entry["main_driver_id"];
		$update_leg["codriver_id"] = $log_entry["codriver_id"];
		$update_leg["rate_type"] = $rate_type;
		$update_leg["revenue_rate"] = $revenue_rate;
		$update_leg["odometer_miles"] = $_POST["odometer_miles"];
		$update_leg["map_miles"] = $_POST["map_miles"];
		$update_leg["hours"] = $_POST["hours"];
		$update_leg["fuel_expense"] = $fuel_expense;
		$update_leg["gallons_used"] = $gallons_used;
		$update_leg["main_driver_split"] = $_POST["main_driver_split_".$log_entry_id];
		$update_leg["codriver_split"] = $_POST["codriver_split_".$log_entry_id];
		db_update_leg($update_leg,$where);
		
	}
	
	//SAVE CHECK CALL
	function save_check_call()
	{
		$log_entry_id = $_POST["log_entry_id"];
		
		$day_recap = $_POST["day_recap"];
		$effort_eval = $_POST["effort_eval"];
		$night_plan = $_POST["night_plan"];
		$night_recap = $_POST["night_recap"];
		$fuel_plan = $_POST["fuel_plan"];
		$fuel_plan_followed = $_POST["fuel_plan_followed"];
		$morning_goal = $_POST["morning_goal"];
		$goal_met = $_POST["goal_met"];
		
		$last_mpg = $_POST["last_mpg"];
		$driver_fuel_analysis = $_POST["driver_fuel_analysis"];
		$fuel_analysis_response = $_POST["fuel_analysis_response"];
		$map_miles = $_POST["map_miles"];
		$driver_mileage_analysis = $_POST["driver_mileage_analysis"];
		$mileage_analysis_response = $_POST["mileage_analysis_response"];
		$oor = $_POST["oor"];
		$driver_oor_analysis = $_POST["driver_oor_analysis"];
		$oor_analysis_response = $_POST["oor_analysis_response"];
		
		$notes_to_dispatcher = $_POST["notes_to_dispatcher"];
		$logs_complete = $_POST["logs_complete"];
		$night_dispatch_eval = $_POST["night_dispatch_eval"];
		
		//SET GOAL MET
		if($goal_met == "Select")
		{
			$goal_met = null;
		}

		//GET CHECK CALL
		$where = null;
		$where["log_entry_id"] = $log_entry_id;
		$check_call = db_select_check_call($where);
		
		$update = null;
		$update["day_recap"] = $day_recap;
		$update["effort_eval"] = $effort_eval;
		$update["night_plan"] = $night_plan;
		$update["fuel_plan"] = $fuel_plan;
		$update["fuel_plan_followed"] = $fuel_plan_followed;
		$update["morning_goal"] = $morning_goal;
		$update["goal_met"] = $goal_met;
		$update["night_recap"] = $night_recap;
		
		$update["last_mpg"] = $last_mpg;
		$update["driver_fuel_analysis"] = $driver_fuel_analysis;
		$update["fuel_analysis_response"] = $fuel_analysis_response;
		$update["map_miles"] = $map_miles;
		$update["driver_mileage_analysis"] = $driver_mileage_analysis;
		$update["mileage_analysis_response"] = $mileage_analysis_response;
		$update["oor"] = $oor;
		$update["driver_oor_analysis"] = $driver_oor_analysis;
		$update["oor_analysis_response"] = $oor_analysis_response;
		
		$update["notes_to_dispatcher"] = $notes_to_dispatcher;
		$update["logs_complete"] = $logs_complete;
		$update["night_dispatch_eval"] = $night_dispatch_eval;
		
		$where = null;
		$where["log_entry_id"] = $log_entry_id;
		db_update_check_call($update,$where);
		
		
	}
	
	//GET LEG DETAILS FOR EXPORT
	function get_leg_export()
	{
		$log_entry_id = $_POST["log_entry_id"];
		
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
	
		$leg = get_leg_details($log_entry_id);
			
		$leg_id = $leg["existing_leg"]["id"];
		
		$leg_calc = get_leg_calculations($leg_id);
		
		$data["log_entry"] = $log_entry;
		$data["leg_calc"] = $leg_calc;
		$data["leg_id"] = $leg_id;
		$this->load->view('logs/export_leg_dialog',$data);
	}
	
	//GET LEG CALCULATIONS FOR DIALOG
	function get_leg_calculations()
	{
		$log_entry_id = $_POST["log_entry_id"];
		
		//GET END LEG EVENT
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		
		//GET LEG DETAILS
		$where = null;
		$where["id"] = $log_entry_id;
		$leg_details = get_leg_details($log_entry_id);
		
		//GET LEG FROM DB
		$where = null;
		$where["log_entry_id"] = $log_entry_id;
		$leg = db_select_leg($where);
		
		//DETERMINE RATE TYPE
		if($leg["rate_type"] == 'Auto')
		{
			$rate_type = $leg_details["rate_type"];
		}
		else
		{
			$rate_type = $leg["rate_type"];
		}
		
		//DETERMINE REVENUE RATE
		if($rate_type == "Loaded")
		{
			$target_mpg = 6;
		}
		else if($rate_type == "Dead Head")
		{
			$target_mpg = 7.5;
		}
		else if($rate_type == "Bobtail")
		{
			$target_mpg = 9;
		}
		
		@$revenue_rate = round($leg["natl_fuel_avg"]/$target_mpg + 0.66,2);
		
		//UPDATE LEG IN DB
		$where = null;
		$where["log_entry_id"] = $log_entry_id;
		
		$update_leg["log_entry_id"] = $log_entry["id"];
		$update_leg["load_id"] = $log_entry["load_id"];
		$update_leg["truck_id"] = $leg_details["truck_id"];
		$update_leg["trailer_id"] = $leg_details["trailer_id"];
		$update_leg["main_driver_id"] = $leg_details["main_driver_id"];
		$update_leg["codriver_id"] = $leg_details["codriver_id"];
		//$update_leg["rate_type"] = $leg["rate_type"];
		$update_leg["revenue_rate"] = $revenue_rate;
		$update_leg["odometer_miles"] = $leg_details["odometer_miles"];
		$update_leg["map_miles"] = $leg_details["map_miles"];
		$update_leg["hours"] = $leg_details["hours"];
		
		//ONLY UPDATE LEG IF LEG IS NOT LOCKED
		if(empty($log_entry["locked_datetime"]))
		{
			db_update_leg($update_leg,$where);
		}
	
		$leg_id = $leg_details["existing_leg"]["id"];
		
		$leg_calc = get_leg_calculations($leg_id);
		$leg_calc["rate_type"] = $rate_type;
		
		$data["log_entry"] = $log_entry;
		$data["leg_calc"] = $leg_calc;
		$data["leg_id"] = $leg_id;
		$this->load->view('logs/leg_calculations_dialog',$data);
	}
	
	//LOCK AN EVENT (FUEL FILL, END LEG)
	function lock_event()
	{
		//echo "start";
		$is_valid = true;
		$script = "";
	
		$log_entry_id = $_POST["log_entry_id"];
		
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		date_default_timezone_set('America/Denver');
		$lock_datetime = date("Y-m-d H:i:s");
		
		if($log_entry["entry_type"] == "Fuel Fill")
		{
			/*
			*** UPDATE THE FUEL FILL INFORMATION ONE LAST TIME WITH THE CURRENT INFO
			*/
			//echo "test";
		
			//GET FUEL STOP DETAILS
			$fuel_stop_details = get_fuel_stop_details($log_entry_id);
			
			//UPDATE FUEL STOP IN THE DB
			$update_fuel_stop["is_fill"] = $fuel_stop_details["is_fill"];
			$update_fuel_stop["natl_fuel_avg"] =get_natl_fuel_avg_from_db($log_entry["entry_datetime"]);
			$update_fuel_stop["fill_to_fill_gallons"] = $fuel_stop_details["f2f_gallons"];
			$update_fuel_stop["fill_to_fill_expense"] = $fuel_stop_details["f2f_expense"];
			$update_fuel_stop["fill_to_fill_rebate"] = $fuel_stop_details["f2f_discount"];
			$update_fuel_stop["map_miles"] = $fuel_stop_details["f2f_miles"];
			$update_fuel_stop["odom_miles"] = $fuel_stop_details["odometer_miles"] ;
			
			$where = null;
			$where["id"] = $fuel_stop_details["fuel_stop_id"];
			//db_update_fuel_stop($update_fuel_stop,$where);
			
			$this_fuel_stop = db_select_fuel_stop($where);
			
			//UPDATE LEG EVENT WITH CURRENT INFORMATION
			$this_fuel_event = null;
			$this_fuel_event["load_id"] = $log_entry["load_id"];
			$this_fuel_event["truck_id"] = $log_entry["truck_id"];
			$this_fuel_event["trailer_id"] = $log_entry["trailer_id"];
			$this_fuel_event["main_driver_id"] = $log_entry["main_driver_id"];
			$this_fuel_event["codriver_id"] = $log_entry["codriver_id"];
			$this_fuel_event["miles"] = $fuel_stop_details["f2f_miles"];
			$this_fuel_event["out_of_route"] = $fuel_stop_details["f2f_oor"];
			$this_fuel_event["mpg"] = $fuel_stop_details["f2f_mpg"];
			$this_fuel_event["route"] = $fuel_stop_details["f2f_route_url"];
			
			$where = null;
			$where["id"] = $log_entry["id"];
			//db_update_log_entry($this_fuel_event,$where);
			
			//UPDATE THE ALLOCATED LOAD
			$update = null;
			if(empty($log_entry["load_id"]))
			{
				//GET NEXT EVENT WITH A LOAD
				$where = null;
				$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
				$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
				
				if(!empty($next_loaded_event))
				{
					$update["allocated_load_id"] = $next_loaded_event["load_id"];
				}
				else
				{
					$update["allocated_load_id"] = $log_entry["load_id"];
				}
			}
			else
			{
				$update["allocated_load_id"] = $log_entry["load_id"];
			}
			$where = null;
			$where["id"] = $log_entry["id"];
			db_update_log_entry($update,$where);
			
			/*
			*** GET NEWLY UPDATED EVENTS AND VALIDATE
			*/
			
			//GET LOG ENTRY
			$where = null;
			$where["id"] = $log_entry_id;
			$log_entry = db_select_log_entry($where);
			
			
			//GET PREVIOUS FILL
			$where = null;
			$where = " entry_datetime < '".$log_entry["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' AND  entry_type = 'Fuel Fill' ";
			$previous_fuel_fill_event = db_select_log_entry($where,"entry_datetime DESC",1);
			
			if(empty($previous_fuel_fill_event))
			{
				//GET FIRST RECORDED MAPPABLE EVENT
				$where = null;
				$where = " entry_datetime < '".$log_entry["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' ";
				$previous_fuel_fill_event = db_select_log_entry($where,"entry_datetime",1);
			}
			
			//IF A PREVIOUS FILL EVENT EXISTS
			if(!empty($previous_fuel_fill_event))
			{
				//VALIDATE THAT PREVIOUS FUEL FILL IS LOCKED
				if(empty($previous_fuel_fill_event["locked_datetime"]))
				{
					if($previous_fuel_fill_event["entry_type"] == "Fuel Fill")
					{
						$is_valid = false;
						$script = $script." alert('The previous Fuel Fill has NOT been locked!');";
					}
				}
			
				//GET ALL MAPPABLE EVENTS
				$where = null;
				$where = " entry_datetime < '".$log_entry["entry_datetime"]."' AND entry_datetime > '".$previous_fuel_fill_event["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' AND (entry_type = 'Pick' OR entry_type = 'Fuel Partial' OR entry_type = 'Drop' OR entry_type = 'Checkpoint') ";
				$map_events = db_select_log_entrys($where,"entry_datetime");
				
				if(!empty($map_events))
				{
					//VALIDATE THAT ODOMETERS ARE INCREASING CHRONOLOGICALLY
					$previous_event = $previous_fuel_fill_event;
					foreach($map_events as $event)
					{
						if($previous_event["odometer"] > $event["odometer"])
						{
							$is_valid = false;
							$script = $script." alert('The odometers for this Fill to Fill are NOT chronological ".date("n/j H:i",strtotime($event["entry_datetime"]))."!');";
						}

						$previous_event = $event;
					}
					
					
				}
			}
			
			/*
			*** VALIDATE ALL ASSOCIATED LEGS
			*/
			
			$leg_validation_alert[1] = "It looks like it is missing an Allocated Load. Try refreshing the Leg.";
			$leg_validation_alert[2] = "Check that the Odometers are chronological.";
			$leg_validation_alert[3] = "Check that the leg has a consistant Main Driver.";
			$leg_validation_alert[4] = "Check that the leg has a consistant Co-Driver.";
			$leg_validation_alert[5] = "Check that the leg has a consistant Trailer.";
			$leg_validation_alert[6] = "Check that the leg does not have multiple Loads (Partials and POs are OK).";
			
			
			//GET NEXT END LEG EVENT
			$where = null;
			$where = " entry_datetime > '".$log_entry["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' AND  entry_type = 'End Leg' ";
			$next_end_leg_event = db_select_log_entry($where,"entry_datetime ",1);
			
			//echo $next_end_leg_event["id"]."<br>";
			
			if(empty($next_end_leg_event))
			{
				$is_valid = false;
				$script = $script." alert('This leg must be completed with an End Leg event before it can be locked!');";
			}
			else
			{
				$validation_code = leg_is_valid($next_end_leg_event);
				//VALIDATE NEXT END LEG EVENT
				if($validation_code > 0)
				{
					//GET LEG
					$leg = null;
					$where = null;
					$where["log_entry_id"] = $next_end_leg_event["id"];
					$leg = db_select_leg($where);
		
					$is_valid = false;
					$script = $script." alert('Leg ".$leg["id"]." did not pass validation! ".$leg_validation_alert[$validation_code]."');";
					
				}
			}
			
			//GET ALL END LEGS BETWEEN THIS FUEL FILL AND PREVIOUS FUEL FILL
			$where = null;
			$where = " entry_datetime < '".$log_entry["entry_datetime"]."' AND entry_datetime > '".$previous_fuel_fill_event["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' AND  entry_type = 'End Leg' ";
			$end_leg_events = db_select_log_entrys($where,"entry_datetime ");
			
			//VALIDATE EACH END LEG F2F
			if(!empty($end_leg_events ))
			{
				foreach($end_leg_events as $end_leg_event)
				{
					//echo $end_leg_event["id"]." ";
					$validation_code = leg_is_valid($end_leg_event);
					if($validation_code > 0) //IF LEG IS NOT VALID;
					{
						//GET LEG
						$leg = null;
						$where = null;
						$where["log_entry_id"] = $end_leg_event["id"];
						$leg = db_select_leg($where);
					
						$is_valid = false;
						$script = $script." alert('Leg ".$leg["id"]." did not pass validation! ".$leg_validation_alert[$validation_code]."');";
					}
					
				}
			}
			
			if($is_valid)
			{
				//CREATE NEW FUEL ALLOCATIONS WITH UPDATED INFO
				create_fuel_allocations($log_entry_id);
				
				
				//GET FUEL STOP
				$where = null;
				$where["log_entry_id"] = $log_entry_id;
				$fuel_stop = db_select_fuel_stop($where);
				
				//GET FUEL ALLOCATIONS FOR THIS FUEL STOP
				$where = null;
				$where["fuel_stop_id"] = $fuel_stop["id"];
				$fuel_allocations = db_select_fuel_allocations($where);
				
				$total_percentage = 0;
				$total_gallons = 0;
				$total_expense = 0;
				foreach($fuel_allocations as $allocation)
				{
					//ADD UP TOTAL PERCENTAGE, GALLONS, AND EXPENSE TO PREPARE FOR VALIDATION
					$total_percentage = $total_percentage + $allocation["percentage"];
					$total_gallons = $total_gallons + $allocation["gallons"];
					$total_expense = $total_expense + $allocation["expense"];
				}
				
				//VALIDATE THAT ALLOCATIONS ARE 100%
				if(round($total_percentage,2) == 1 && (round($total_gallons,2) <= round($fuel_stop["fill_to_fill_gallons"],2)+.02 && round($total_gallons,2) >= round($fuel_stop["fill_to_fill_gallons"],2)-.02)  && (round($total_expense,2) <= round($fuel_stop["fill_to_fill_expense"],2)+.02 &&  round($total_expense,2) >= round($fuel_stop["fill_to_fill_expense"],2)-.02))
				{
					//ASSIGN FUEL ALLOCATIONS TO THE CORRECT LEGS
					foreach($fuel_allocations as $allocation)
					{
						//GET FUEL EXPENSE ALLOCATED AND FUEL GALLONS ALLOCATED
						$fuel_allocation = null;
						$fuel_allocation = get_fuel_allocations_for_leg($allocation["leg_id"]);
						
						$update_leg = null;
						$update_leg["fuel_expense"] = $fuel_allocation["total_expense"];
						$update_leg["gallons_used"] = $fuel_allocation["total_gallons"];
						
						$where = null;
						$where["id"] = $allocation["leg_id"];
						db_update_leg($update_leg,$where);
					}
				
				
					//IF PREVIOUS FUEL FILL EVENT IS ACTUALLY NOT A FILL (PICKED UP TRUCK FROM LEASING COMPANY)
					//THEN UPDATE ALLOCATED LOAD AND LOCK THIS EVENT
					if($previous_fuel_fill_event["entry_type"] != "Fuel Fill")
					{
						//UPDATE THE ALLOCATED LOAD
						$update = null;
						if(empty($previous_fuel_fill_event["load_id"]))
						{
							//GET NEXT EVENT WITH A LOAD
							$where = null;
							$where = " truck_id = ".$previous_fuel_fill_event["truck_id"]." AND entry_datetime > '".$previous_fuel_fill_event["entry_datetime"]."' AND load_id IS NOT NULL ";
							$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
							
							if(!empty($next_loaded_event))
							{
								$update["allocated_load_id"] = $next_loaded_event["load_id"];
							}
							else
							{
								$update["allocated_load_id"] = $previous_fuel_fill_event["load_id"];
							}
						}
						else
						{
							$update["allocated_load_id"] = $previous_fuel_fill_event["load_id"];
						}
						$where = null;
						$where["id"] = $previous_fuel_fill_event["id"];
						db_update_log_entry($update,$where);
						
						//UPDATE EVENT WITH LOCK DATETIME
						$update_fuel_event = null;
						$update_fuel_event["locked_datetime"] = $lock_datetime;
						
						$where = null;
						$where["id"] = $previous_fuel_fill_event["id"];
						db_update_log_entry($update_fuel_event,$where);
						
					}
				
					//LOCK THIS FILL
					$update_fuel_event = null;
					$update_fuel_event["locked_datetime"] = $lock_datetime;
					
					$where = null;
					$where["id"] = $log_entry_id;
					db_update_log_entry($update_fuel_event,$where);
					
					//GET THE FUEL STOP FOR THIS ENTRY
					$where = null;
					$where["log_entry_id"] = $log_entry["id"];
					$this_fuel_fill= db_select_fuel_stop($where);
				
					//CREATE FM FUEL ALLOCATION ACCOUNT CREDIT
					$fuel_allocated_entry = null;
					$fuel_allocated_entry["account_id"] = $this_fuel_fill["allocation_account_id"];
					$fuel_allocated_entry["recorder_id"] = $this->session->userdata('person_id');;
					$fuel_allocated_entry["entry_datetime"] = $lock_datetime;
					$fuel_allocated_entry["entry_type"] = "Fuel Allocated";
					$fuel_allocated_entry["debit_credit"] = "Credit";
					$fuel_allocated_entry["entry_amount"] = $this_fuel_fill["fuel_expense"];
					$fuel_allocated_entry["entry_description"] = "ALLOCATED Fuel  ".$this_fuel_fill["id"]." | ".$log_entry["main_driver"]["client_nickname"]." | ".$log_entry["address"]." ".$log_entry["city"].", ".$log_entry["state"]." | Locked ".date("n/j/y",strtotime($lock_datetime));
					
					//MAKE SURE THERE IS AN ALLOCATION ACCOUNT ID
					if(!empty($this_fuel_fill["allocation_account_id"]))
					{
						db_insert_account_entry($fuel_allocated_entry);
						
						//GET NEWLY CREATED ACCOUNT ENTRY
						$where = null;
						$where["account_id"] = $this_fuel_fill["allocation_account_id"];
						$where["recorder_id"] = $this->session->userdata('person_id');
						$where["entry_datetime"] = $lock_datetime;
						$where["entry_type"] = "Fuel Allocated";
						$where["debit_credit"] = "Credit";
						$where["entry_amount"] = $this_fuel_fill["fuel_expense"];
						$where["entry_description"] = "ALLOCATED Fuel  ".$this_fuel_fill["id"]." | ".$log_entry["main_driver"]["client_nickname"]." | ".$log_entry["address"]." ".$log_entry["city"].", ".$log_entry["state"]." | Locked ".date("n/j/y",strtotime($lock_datetime));
						
						$allocation_entry = db_select_account_entry($where);
						
						//SET ALLOCATED ENTRY ID
						$update_fuel_stop = null;
						$update_fuel_stop["allocated_entry_id"] = $allocation_entry["id"];
						
						$where = null;
						$where["id"] = $this_fuel_fill["id"];
						db_update_fuel_stop($update_fuel_stop, $where);
					}
					
					
					
					//IF A PREVIOUS FILL EVENT EXISTS
					if(!empty($previous_fuel_fill_event))
					{
						//GET ALL MAPPABLE EVENTS BETWEEN F2F
						$where = null;
						$where = " entry_datetime < '".$log_entry["entry_datetime"]."' AND entry_datetime > '".$previous_fuel_fill_event["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' AND (entry_type = 'Pick' OR entry_type = 'Fuel Partial' OR entry_type = 'Drop' OR entry_type = 'Checkpoint') ";
						$map_events = db_select_log_entrys($where,"entry_datetime");
						
						if(!empty($map_events))
						{
							//LOCK ALL PARTIAL FUELS
							foreach($map_events as $fuel_event)
							{
								//UPDATE THE ALLOCATED LOAD
								$log_entry = $fuel_event;
								$update = null;
								if(empty($log_entry["load_id"]))
								{
									//GET NEXT EVENT WITH A LOAD
									$where = null;
									$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
									$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
									
									if(!empty($next_loaded_event))
									{
										$update["allocated_load_id"] = $next_loaded_event["load_id"];
									}
									else
									{
										$update["allocated_load_id"] = $log_entry["load_id"];
									}
								}
								else
								{
									$update["allocated_load_id"] = $log_entry["load_id"];
								}
								$where = null;
								$where["id"] = $log_entry["id"];
								db_update_log_entry($update,$where);
								
								//UPDATE EVENT WITH LOCK DATETIME
								$update_fuel_event = null;
								$update_fuel_event["locked_datetime"] = $lock_datetime;
								
								$where = null;
								$where["id"] = $fuel_event["id"];
								db_update_log_entry($update_fuel_event,$where);
								
								//IF EVENT IS A FUEL PARTIAL
								if($fuel_event["entry_type"] == "Fuel Partial")
								{
									//GET THE FUEL STOP FOR THIS ENTRY
									$where = null;
									$where["log_entry_id"] = $fuel_event["id"];
									$this_fuel_stop = db_select_fuel_stop($where);
								
									//CREATE FM FUEL ALLOCATION ACCOUNT CREDIT
									$fuel_allocated_entry = null;
									$fuel_allocated_entry["account_id"] = $this_fuel_stop["allocation_account_id"];
									$fuel_allocated_entry["recorder_id"] = $this->session->userdata('person_id');
									$fuel_allocated_entry["entry_datetime"] = $lock_datetime;
									$fuel_allocated_entry["entry_type"] = "Fuel Allocated";
									$fuel_allocated_entry["debit_credit"] = "Credit";
									$fuel_allocated_entry["entry_amount"] = $this_fuel_stop["fuel_expense"];
									$fuel_allocated_entry["entry_description"] = "ALLOCATED Fuel ".$this_fuel_stop["id"]." | ".$fuel_event["main_driver"]["client_nickname"]." | ".$fuel_event["address"]." ".$fuel_event["city"].", ".$fuel_event["state"]." | Locked ".date("n/j/y",strtotime($lock_datetime));
									
									
									//MAKE SURE THERE IS AN ALLOCATION ACCOUNT ID
									if(!empty($this_fuel_stop["allocation_account_id"]))
									{
										db_insert_account_entry($fuel_allocated_entry);
										
										//GET NEWLY CREATED ACCOUNT ENTRY
										$where = null;
										$where["account_id"] = $this_fuel_fill["allocation_account_id"];
										$where["recorder_id"] = $this->session->userdata('person_id');
										$where["entry_datetime"] = $lock_datetime;
										$where["entry_type"] = "Fuel Allocated";
										$where["debit_credit"] = "Credit";
										$where["entry_amount"] = $this_fuel_fill["fuel_expense"];
										$where["entry_description"] = "ALLOCATED Fuel ".$this_fuel_stop["id"]." | ".$fuel_event["main_driver"]["client_nickname"]." | ".$fuel_event["address"]." ".$fuel_event["city"].", ".$fuel_event["state"]." | Locked ".date("n/j/y",strtotime($lock_datetime));
										
										$allocation_entry = db_select_account_entry($where);
										
										//SET ALLOCATED ENTRY ID
										$update_fuel_stop = null;
										$update_fuel_stop["allocated_entry_id"] = $allocation_entry["id"];
										
										$where = null;
										$where["id"] = $this_fuel_fill["id"];
										db_update_fuel_stop($update_fuel_stop, $where);
									}
								}
								
							}
						}
					}
					echo "<script>load_log_list()</script>";
				}
				else //IF NOT FULLY ALLOCATED
				{
					//$allocated = (boolean)(round($total_percentage,2) == 1 && (round($total_gallons,2) <= round($fuel_stop["fill_to_fill_gallons"],2)+.01 && round($total_gallons,2) >= round($fuel_stop["fill_to_fill_gallons"],2)-.01)  && (round($total_expense,2) <= round($fuel_stop["fill_to_fill_expense"],2)+.01 &&  round($total_expense,2) >= round($fuel_stop["fill_to_fill_expense"],2)-.01));
					//$allocated = (boolean)(round($total_percentage,2) == 1 && (round($total_gallons,2) <= round($fuel_stop["fill_to_fill_gallons"],2)+.01 && round($total_gallons,2) >= round($fuel_stop["fill_to_fill_gallons"],2)-.01)  && (round($total_expense,2) <= round($fuel_stop["fill_to_fill_expense"],2)+.01 &&  round($total_expense,2) >= round($fuel_stop["fill_to_fill_expense"],2)-.01));
					//$allocated = (boolean)(round($total_gallons,2) >= round($fuel_stop["fill_to_fill_gallons"],2)-.02);
				
					//$fs_g = round($fuel_stop["fill_to_fill_gallons"],2)-.01;
					//$a_g = round($total_gallons,2);
					//$script = "alert('This fuel stop is not correctly allocated! $allocated fs: $fs_g a: $a_g');";
					$script = "alert('This fuel stop is not correctly allocated!');";
					$data["log_entry"] = $log_entry;
					$this->load->view('logs/log_entry_row',$data);
					echo "<script>".$script."</script>";
				}
			}
			else //IF NOT VALID
			{
				$data["log_entry"] = $log_entry;
				$this->load->view('logs/log_entry_row',$data);
				echo "<script>".$script."</script>";
				//echo "<script>alert('Something did not validate');</script>";
			}
			
		}
		else if($log_entry["entry_type"] == "End Leg")
		{
			$is_valid = true;
			$script = "";
			
			//GET LEG
			$where = null;
			$where["log_entry_id"] = $log_entry["id"];
			$leg = db_select_leg($where);
			
			//VALIDATE THE FOLLOWING IF THERE IS A TRUCK OR IF RATE TYPE IS ANYTHING OTHER THAN PERSONAL
			if($log_entry["truck_id"] != 0 || $leg["rate_type"] != "Personal")
			{
				//VALIDATE THAT ALLOCATED_LOAD IS NOT EMPTY
				if(empty($log_entry["allocated_load_id"]) || empty($leg["allocated_load_id"]))
				{
					$is_valid = false;
					$script = $script." alert('This leg is missing an Allocated Load!');";
				}
				
				//GET NEXT FUEL FILL 
				$where = null;
				$where = " entry_type = 'Fuel Fill' AND truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' ";
				$next_fuel_fill = db_select_log_entry($where,"entry_datetime ","1");

				//VALIDATE THAT NEXT FUEL FILL AS BEEN LOCKED
				if(empty($next_fuel_fill["locked_datetime"]))
				{
					$is_valid = false;
					$script = $script." alert('The next Fuel Fill has NOT been locked! next fuel fill = ".$next_fuel_fill["id"]."');";
				}
			}
			
			//GET PREVIOUS END LEG LOG ENTRY
			if($log_entry["truck_id"] == 0)
			{
				$driver_id = 0;
				if(!empty($log_entry["main_driver_id"]))
				{
					$driver_id = $log_entry["main_driver_id"];
				}
				elseif(!empty($log_entry["codriver_id"]))
				{
					$driver_id = $log_entry["codriver_id"];
				}
				else
				{
					echo "There has to be at least a driver, codriver, or truck on this event!";
				}
			
				$where = null;
				$where = " entry_type = 'End Leg' AND (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
				$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
			}
			else
			{
				$where = null;
				$where = " entry_type = 'End Leg' AND truck_id = ".$log_entry["truck_id"]." AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
				$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
			}
			
			//GET ALL FUEL STOPS DURING LEG
			if(!empty($begin_leg_entry))
			{
				//GET PREVIOUS END LEG SYNC ENTRY
				$where = null;
				$where["id"] = $begin_leg_entry["sync_entry_id"];
				$sync_entry = db_select_log_entry($where);
				
				//VALIDATE THAT PREVIOUS END LEG SYNC ENTRY IS LOCKED
				if(empty($sync_entry["locked_datetime"]))
				{
					$is_valid = false;
					$script = $script." alert('The previous End Leg sync event has NOT been locked!');";
				}
			
				//IF A TRUCK EXISTS ON THE LEG, VALIDATE FUEL FILLS HAVE BEEN LOCKED
				if($log_entry["truck_id"] != 0)
				{
					//GET LEG FUEL STOP EVENTS
					$where = null;
					$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime < '".$log_entry["entry_datetime"]."'  AND entry_datetime > '".$begin_leg_entry["entry_datetime"]."' AND (entry_type = 'Fuel Fill' OR entry_type = 'Fuel Partial') ";
					$event_list = null;
					$event_list = db_select_log_entrys($where,"entry_datetime");
				
					if(!empty($event_list))
					{
						//VALIDATE ALL FUEL FILLS DURING LEG HAVE BEEN LOCKED
						foreach($event_list as $event)
						{
							if(empty($event["locked_datetime"]))
							{
								$is_valid = false;
								$script = $script." alert('Not all Fuel stops for this leg have been locked!next fuel fill = ".$next_fuel_fill["id"]."');";
							}
						}
					}
				}
			}
			
			//LEG VALIDATION ALERT CODES AND DIALOGS
			$leg_validation_alert[0] = "This leg looks good!";
			$leg_validation_alert[1] = "It looks like it is missing an Allocated Load. Try refreshing the Leg.";
			$leg_validation_alert[2] = "Check that the Odometers are chronological.";
			$leg_validation_alert[3] = "Check that the leg has a consistant Main Driver.";
			$leg_validation_alert[4] = "Check that the leg has a consistant Co-Driver.";
			$leg_validation_alert[5] = "Check that the leg has a consistant Trailer.";
			$leg_validation_alert[6] = "Check that the leg does not have multiple Loads (Partials and POs are OK).";
			
			$validation_code = leg_is_valid($log_entry);
			
			if($validation_code > 0) //IF LEG IS NOT VALID;
			{
				$is_valid = false;
				$script = $script." alert('".$leg_validation_alert[$validation_code]."');";
			}
		
			//echo $is_valid;
			if($is_valid)
			{
			
				//DETERMINE RATE TYPE
				if($leg["rate_type"] == 'Auto')
				{
					$leg_details = get_leg_details($log_entry_id);
					$rate_type = $leg_details["rate_type"];
				}
				else
				{
					$rate_type = $leg["rate_type"];
				}
				
				//UPDATE LEG WITH CORRECT RATE TYPE
				$update_leg = null;
				$update_leg["rate_type"] = $rate_type;
				
				//SET DRIVER SPLITS
				//SET PROFIT SPLITS TO DRIVER DEFAULTS IF EMPTY
				if(empty($leg["main_driver_split"]))
				{
					$update_leg["main_driver_split"] = $leg["main_driver"]["profit_split"];
				}
				if(empty($leg["codriver_split"]))
				{
					$update_leg["codriver_split"] = $leg["codriver"]["profit_split"];
				}
				
				$where = null;
				$where["log_entry_id"] = $log_entry_id;
				db_update_leg($update_leg,$where);
			
				//LOCK THIS END LEG
				$update_end_leg = null;
				$update_end_leg["locked_datetime"] = $lock_datetime;
				
				$where = null;
				$where["id"] = $log_entry_id;
				db_update_log_entry($update_end_leg,$where);
				
				//GET END LEG SYNC EVENT
				$where = null;
				$where["id"] = $log_entry["sync_entry_id"];
				$end_leg_sync_event = db_select_log_entry($where);
				
				//IF SYNC EVENT IS SOMETHING OTHER THAN AN END WEEK - LOCK IT
				if($end_leg_sync_event["entry_type"] != "End Week")
				{
					//LOCK END LEG SYNC ENTRY
					$where = null;
					$where["id"] = $log_entry["sync_entry_id"];
				
					$update_sync_entry = null;
					$update_sync_entry["locked_datetime"] = $lock_datetime;
					db_update_log_entry($update_sync_entry,$where);
				}

				if($log_entry["truck_id"] != 0)
				{
					//GET LEG EVENTS
					$where = null;
					//$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime < '".$log_entry["entry_datetime"]."'  AND entry_datetime > '".$begin_leg_entry["entry_datetime"]."' AND (entry_type = 'Pick' OR entry_type = 'Drop' OR entry_type = 'Fuel Fill' OR entry_type = 'Fuel Partial' OR entry_type = 'Checkpoint') ";
					$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime < '".$log_entry["entry_datetime"]."'  AND entry_datetime > '".$begin_leg_entry["entry_datetime"]."'  ";
					$event_list = db_select_log_entrys($where,"entry_datetime");
				
					if(!empty($event_list))
					{
						//LOCK ALL EVENTS FOR LEG
						foreach($event_list as $event)
						{
							//UPDATE THE ALLOCATED LOAD
							$log_entry = $event;
							$update = null;
							if(empty($log_entry["load_id"]))
							{
								//GET NEXT EVENT WITH A LOAD
								$where = null;
								$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
								$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
								
								if(!empty($next_loaded_event))
								{
									$update["allocated_load_id"] = $next_loaded_event["load_id"];
								}
								else
								{
									$update["allocated_load_id"] = $log_entry["load_id"];
								}
							}
							else
							{
								$update["allocated_load_id"] = $log_entry["load_id"];
							}
							$where = null;
							$where["id"] = $log_entry["id"];
							db_update_log_entry($update,$where);
							
							//LOCK ALL EVENTS EXCEPT END WEEK EVENTS
							if($event["entry_type"] != "End Week")
							{
								//UPDATE EVENT WITH LOCK DATETIME
								$update_event = null;
								$update_event["locked_datetime"] = $lock_datetime;
								
								$where = null;
								$where["id"] = $event["id"];
								db_update_log_entry($update_event,$where);
							}
						}
					}
				}
				
				//FIGURE OUT IF THIS IS THE LAST LEG OF THE LOAD AND PERFORM COMMISSION CALCULATIONS
				if(!empty($log_entry["allocated_load_id"]))
				{
					//GET ALL UNLOCKED EVENTS WITH THIS ALLOCATED LOAD ID
					$where = null;
					$where = " allocated_load_id = '".$log_entry["allocated_load_id"]."' AND locked_datetime IS NULL ";
					$unlocked_events = db_select_log_entrys($where);
					
					//THIS LOAD IS COMPLETELY LOCKED
					if(empty($unlocked_events))//IF NO EVENTS EXIST
					{
						//GET LOAD
						$where = null;
						$where["id"] = $log_entry["allocated_load_id"];
						$load = db_select_load($where);
						
						$is_good = true;
			
						//IF IN TRANSIT
						if($load["status_number"] < 7 && empty($load["funded_datetime"]))
						{
							$is_good = false;
						}
						
						//IF PENDING FUNDING
						if($load["status_number"] == 7 && empty($load["funded_datetime"]))
						{
							$is_good = false;
						}
						
						if($is_good)
						{
							update_commission_calc($log_entry["allocated_load_id"]);
							echo "<script>alert('Commission Calculated!')</script>";
						}
					}
				}
				
				//WHEN ALL IS DONE - REFRESH THE LOG LIST
				echo "<script>load_log_list()</script>";
			}
			else
			{
				$data["log_entry"] = $log_entry;
				$this->load->view('logs/log_entry_row',$data);
				echo "<script>".$script."</script>";
			}
		}
		else if($log_entry["entry_type"] == "End Week")
		{
			$is_valid = true;
			$script = "";
			
			//GET PREVIOUS END WEEK FOR THE TRUCK
			if($log_entry["truck_id"] != 0)
			{
				$where = null;
				$where = " entry_type = 'End Week' AND truck_id = ".$log_entry["truck_id"]." AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
				$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
			
				$event_list = null;
				
				//GET ALL UNLOCKED EVENTS FOR THIS TRUCK FOR THIS WEEK
				$where = null;
				$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime < '".$log_entry["entry_datetime"]."'  AND entry_datetime > '".$begin_leg_entry["entry_datetime"]."' AND locked_datetime IS NULL ";
				$event_list = db_select_log_entrys($where,"entry_datetime");
			
				if(!empty($event_list))
				{
					$is_valid = false;
					$script = $script." alert('Not all the events are locked for this truck for this week!');";
				}
			}
			
			
			//GET PREVIOUS END WEEK FOR DRIVER 1
			if(!empty($log_entry["main_driver_id"]))
			{
				$driver_id = $log_entry["main_driver_id"];
				
				$where = null;
				$where = " entry_type = 'End Week' AND (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
				$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
				
				$event_list = null;
				
				//GET ALL UNLOCKED EVENTS FOR THIS DRIVER FOR THIS WEEK
				$where = null;
				$where = " (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$log_entry["entry_datetime"]."'  AND entry_datetime > '".$begin_leg_entry["entry_datetime"]."' AND locked_datetime IS NULL ";
				//echo $where;
				$event_list = db_select_log_entrys($where,"entry_datetime");
			
				if(!empty($event_list))
				{
					$is_valid = false;
					$script = $script." alert('Not all the events are locked for Driver 1 for this week!');";
				}
			}
			
			//GET PREVIOUS END WEEK FOR DRIVER 2
			if(!empty($log_entry["codriver_id"]))
			{
				$driver_id = $log_entry["codriver_id"];
				
				$where = null;
				$where = " entry_type = 'End Week' AND (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
				$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
				
				$event_list = null;
				
				//GET ALL UNLOCKED EVENTS FOR THIS DRIVER FOR THIS WEEK
				$where = null;
				$where = " (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$log_entry["entry_datetime"]."'  AND entry_datetime > '".$begin_leg_entry["entry_datetime"]."' AND locked_datetime IS NULL ";
				//echo $where;
				$event_list = db_select_log_entrys($where,"entry_datetime");
			
				if(!empty($event_list))
				{
					$is_valid = false;
					$script = $script." alert('Not all the events are locked for Driver 2 for this week!');";
				}
			}
		
		
			//VALIDATE THAT ALL EVENTS HAVE BEEN LOCKED FOR THIS WEEK
			
			if($is_valid)
			{
				
				//UPDATE LOCKED DATETIME FOR THIS LOG ENTRY
				$update_end_week = null;
				$update_end_week["locked_datetime"] = $lock_datetime;
				
				$where = null;
				$where["id"] = $log_entry_id;
				db_update_log_entry($update_end_week,$where);
				
				//IF MAIN DRIVER EXISTS ON END WEEK
				if(!empty($log_entry["main_driver_id"]))
				{
					//GET CLIENT
					$where = null;
					$where["id"] = $log_entry["main_driver_id"];
					$main_driver = db_select_client($where);
					
					//CREATE SETTLEMENT FOR DRIVER 1
					$main_driver_settlement = null;
					$main_driver_settlement["client_id"] = $log_entry["main_driver_id"];
					$main_driver_settlement["fm_id"] = $main_driver["fleet_manager_id"];
					$main_driver_settlement["end_week_id"] = $log_entry["id"];
				}
				
				//IF CODRIVER EXISTS ON END WEEK
				if(!empty($log_entry["codriver_id"]))
				{
					//GET CLIENT
					$where = null;
					$where["id"] = $log_entry["codriver_id"];
					$codriver = db_select_client($where);
					
					//CREATE SETTLEMENT FOR DRIVER 1
					$codriver_settlement = null;
					$codriver_settlement["client_id"] = $log_entry["codriver_id"];
					$codriver_settlement["fm_id"] = $codriver["fleet_manager_id"];
					$codriver_settlement["end_week_id"] = $log_entry["id"];
				}
				
				
				//CHECK TO SEE IF ANY SETTLEMENTS EXISTS FOR THIS END WEEK ID TO DETERMINE CREATE OR UPDATE
				$where = null;
				$where["end_week_id"] = $log_entry_id;
				$settlements = db_select_settlements($where);
				
				//IF SETTLEMENTS EXIST - UPDATE EXISTING SETTLEMENTS
				if(!empty($settlements))
				{
					//IF MAIN DRIVER EXISTS ON END WEEK
					if(!empty($log_entry["main_driver_id"]))
					{
						
				
						//GET SETTLEMENT WITH THIS DRIVER ID AND END WEEK ID
						$where = null;
						$where["client_id"] = $log_entry["main_driver_id"];
						$where["end_week_id"] = $log_entry["id"];
						$existing_d1_settlement = db_select_settlement($where);
						
						//UPDATE SETTLEMENT FOR DRIVER 1
						$where = null;
						$where["id"] = $existing_d1_settlement["id"];
						db_update_settlement($main_driver_settlement,$where);
					}
					
					if(!empty($log_entry["codriver_id"]))
					{
						//GET SETTLEMENT WITH THIS DRIVER ID AND END WEEK ID
						$where = null;
						$where["client_id"] = $log_entry["codriver_id"];
						$where["end_week_id"] = $log_entry["id"];
						$existing_d2_settlement = db_select_settlement($where);
						
						//UPDATE SETTLEMENT FOR DRIVER 2
						$where = null;
						$where["id"] = $existing_d2_settlement["id"];
						db_update_settlement($codriver_settlement,$where);
					}
				}
				else //ELSE IF SETTLEMENT DON'T EXIST - CREATE NEW SETTLEMENTS
				{
					if(!empty($log_entry["main_driver_id"]))
					{
						//GET PREVIOUS END WEEK FOR MAIN DRIVER
						$driver_id = $log_entry["main_driver_id"];
						$begin_leg_entry = null;
						$where = null;
						$where = " entry_type = 'End Week' AND (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
						$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
						
						//IF PREVIOUS END WEEK EXISTS - CREATE SETTLEMENT IN SYSTEM
						if(!empty($begin_leg_entry))
						{
							//CREATE SETTLEMENT FOR DRIVER 1
							db_insert_settlement($main_driver_settlement);
						}
					}
					
					if(!empty($log_entry["codriver_id"]))
					{
						//GET PREVIOUS END WEEK FOR CODRIVER
						$driver_id = $log_entry["codriver_id"];
						$begin_leg_entry = null;
						$where = null;
						$where = " entry_type = 'End Week' AND (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
						$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
					
						//IF PREVIOUS END WEEK EXISTS - CREATE SETTLEMENT IN SYSTEM
						if(!empty($begin_leg_entry))
						{
							//CREATE SETTLEMENT FOR DRIVER 2
							db_insert_settlement($codriver_settlement);
						}
					}
				}
				
				echo "<script>load_log_list()</script>";
			}
			else
			{
				$data["log_entry"] = $log_entry;
				$this->load->view('logs/log_entry_row',$data);
				echo "<script>".$script."</script>";
			}
		}
		
		
		//echo " end";
	}
	
	//UNLOCK EVENT --- THIS NEEDS TO BE CHANGED TO UNLOCK FUEL FILL TO FUEL FILL... RESET COMMISSIONS FOR AFFECTED LOADS
	function unlock_event()
	{
		//USER MUST HAVE PERMISSION TO UNLOCK THE EVENT
		if(user_has_permission("unlock log event"))
		{
			$log_entry_id = $_POST["log_entry_id"];
			
			//GET LOG ENTRY
			$where = null;
			$where["id"] = $log_entry_id;
			$log_entry = db_select_log_entry($where);
			
			$event_update = null;
			$event_update["locked_datetime"] = null;
			
			$where = null;
			$where["id"] = $log_entry_id;
			db_update_log_entry($event_update,$where);
			
			//GET DATETIME
			date_default_timezone_set('America/Denver');
			$datetime = date("Y-m-d H:i:s");
			
			//GET THE FUEL STOP FOR THIS ENTRY
			$where = null;
			$where["log_entry_id"] = $log_entry_id;
			$this_fuel_stop = db_select_fuel_stop($where);
		
			//CREATE FM FUEL ALLOCATION ACCOUNT CREDIT
			$fuel_allocated_entry = null;
			$fuel_allocated_entry["account_id"] = $this_fuel_stop["allocation_account_id"];
			$fuel_allocated_entry["recorder_id"] = $this->session->userdata('person_id');;
			$fuel_allocated_entry["entry_datetime"] = $datetime;
			$fuel_allocated_entry["entry_type"] = "Fuel Allocated";
			$fuel_allocated_entry["debit_credit"] = "Debit";
			$fuel_allocated_entry["entry_amount"] = $this_fuel_stop["fuel_expense"];
			$fuel_allocated_entry["entry_description"] = "ADJUSTMENT for Fuel ".$this_fuel_stop["id"]." | ".$log_entry["main_driver"]["client_nickname"]." | ".$log_entry["address"]." ".$log_entry["city"].", ".$log_entry["state"]." | Unlocked ".date("n/j/y",strtotime($datetime));
			
			
			//MAKE SURE THERE IS AN ALLOCATION ACCOUNT ID
			if(!empty($this_fuel_stop["allocation_account_id"]))
			{
				db_insert_account_entry($fuel_allocated_entry);
				
				//RESET ALLOCATED ENTRY ID TO NULL
				$update_fuel_stop = null;
				$update_fuel_stop["allocated_entry_id"] = null;
				
				$where = null;
				$where["id"] = $this_fuel_stop["id"];
				db_update_fuel_stop($update_fuel_stop, $where);
				
			}
		}
		else
		{
			echo "<script>alert('You are not cool enough to unlock events!');</script>";
		}
		
		
	}
	
	//ESTIMATE ODOMETER FOR PICK OR DROP
	function estimate_odometer()
	{
		$log_entry_id = $_POST["log_entry_id"];
		
		//GET THIS FUEL STOP EVENT
		$where = null;
		$where["id"] = $log_entry_id;
		$this_event = db_select_log_entry($where);
		
		//GET PREVIOUS FILL
		$where = null;
		$where = " entry_datetime < '".$this_event["entry_datetime"]."' AND truck_id = '".$this_event["truck_id"]."' AND  (entry_type = 'Fuel Fill' OR entry_type = 'Fuel Partial' )";
		$starting_event = db_select_log_entry($where,"entry_datetime DESC",1);
		
		//GET ALL EVENTS (EXEPT CHECK CALLS) FROM STARTING EVENT TO THIS EVENT
		$where = null;
		$where = " log_entry.id = '".$starting_event["id"]."' OR log_entry.id = '".$this_event["id"]."' OR (entry_datetime < '".$this_event["entry_datetime"]."' AND entry_datetime > '".$starting_event["entry_datetime"]."' AND truck_id = '".$this_event["truck_id"]."' AND 
									(	entry_type = 'Pick' OR 
										entry_type = 'Drop' OR 
										entry_type = 'Checkpoint' OR 
										entry_type = 'Driver In' OR 
										entry_type = 'Driver Out' OR 
										entry_type = 'Pick Trailer' OR 
										entry_type = 'Drop Trailer' OR 
										entry_type = 'Checkpoint OOR' OR 
										entry_type = 'Driver In OOR' OR 
										entry_type = 'Driver Out OOR' OR 
										entry_type = 'Pick Trailer OOR' OR 
										entry_type = 'Drop Trailer OOR' OR 
										entry_type = 'Fuel Fill' OR 
										entry_type = 'Fuel Partial'
									)) ";
		//echo $where;
		$map_events = db_select_log_entrys($where,"entry_datetime");
		
		/**
			//GET ROUTE URL - FILL TO FILL
			//https://maps.google.com/maps?saddr=Los+Angeles,+CA&daddr=Las+Vegas,+NV+to:Salt+Lake+City,+UT+to:Provo,+UT&hl=en
			$url_search = array(" ","&");
			$url_replace = array("+","and");
			$waypoints = "";
			if(!empty($map_events))
			{
				$i = 1;
				foreach($map_events as $event)
				{
					if($i == 1)
					{
						$origin = str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
					}
					
					$waypoints = $waypoints."|via:".str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
					
					$i++;
				}
				$params["waypoints"] = substr($waypoints,5);
			}
				
			//GET MAP MILES
			$origin = str_replace($url_search,$url_replace,$previous_fuel_fill_event["address"].", ".$previous_fuel_fill_event["city"].", ".$previous_fuel_fill_event["state"]);
			$destination = str_replace($url_search,$url_replace,$this_event["address"].", ".$this_event["city"].", ".$this_event["state"]);
			$endpoint = 'http://maps.googleapis.com/maps/api/directions/json?';
			$params["origin"] = $origin;
			$params["destination"] = $destination;
			$params["mode"] = 'driving';
			$params["sensor"] = 'false';
			//echo $endpoint.http_build_query($params);

			// Fetch and decode JSON string into a PHP object
			$json = file_get_contents($endpoint.http_build_query($params));
			$data = json_decode($json);

			// If we got directions, output all of the HTML instructions
			$map_miles = 0;
			if ($data->status === 'OK') 
			{
				$route = $data->routes[0];
				foreach($route->legs as $gleg)
				{
					$map_miles = $map_miles + $gleg->distance->value;
				}
			}
			$map_miles = round($map_miles/1609.34); //CONVERT FROM KM TO MILES
		**/
		
		$map_info = get_map_info($map_events);
		
		$map_miles = $map_info["map_miles"];
		
		//echo $map_miles;
		
		if($map_miles == 0)
		{
			$starting_odometer = 0;
		}
		else
		{
			$starting_odometer = $starting_event["odometer"];
		}
		
		$update = null;
		$update["odometer"] = $starting_odometer + $map_miles;
		
		$where = null;
		$where["id"] = $log_entry_id;
		db_update_log_entry($update,$where);
		
		if(!empty($this_event["sync_entry_id"]))
		{
			$where = null;
			$where["id"] = $this_event["sync_entry_id"];
			db_update_log_entry($update,$where);
		}
	}
	
	//VALIDATE THE FUEL AMOUNTS ALLOCATED WITH THE ALLOCATIONS IN THE DB
	function validate_fuel_allocations()
	{
		$log_entry_id = $_POST["log_entry_id"];
		
		//GET LEG
		$where = null;
		$where["log_entry_id"] = $log_entry_id;
		$leg = db_select_leg($where);
		
		//GET FUEL EXPENSE ALLOCATED AND FUEL GALLONS ALLOCATED
		$fuel_allocation = get_fuel_allocations_for_leg($leg["id"]);
		
		//IF AMOUNTS DON'T MATCH WHAT IS IN THE DB
		if(round($leg["fuel_expense"],2) == round($fuel_allocation["total_expense"],2) && round($leg["gallons_used"],2) == round($fuel_allocation["total_gallons"],2))
		{
			//$script = "alert('Match')";
			$script = 
					"
						lock_event('$log_entry_id');
					";
			echo "<script>".$script."</script>";
		}
		else
		{
			
			//$script = "alert('fuel_expense: ".$leg["fuel_expense"]." total_expense: ".$fuel_allocation["total_expense"]."');";
			//$script = $script."alert('gallons_used: ".$leg["gallons_used"]." total_gallons: ".$fuel_allocation["total_gallons"]."');";
			
			$script = 
					"
						if(confirm(\"The computer is calculating the fuel allocations as something DIFFERENT from what you are trying to submit. Are you SURE you want to lock this event??\"))
						{
							lock_event('$log_entry_id');
						}
					";
			echo "<script>".$script."</script>";
		}
	}
	
	//CHECK GOOGLE FOR LOCATION
	function check_google_for_location()
	{
		$events = array();
	
		$start_event["address"] = $_POST["address"];
		$start_event["city"] = $_POST["city"];
		$start_event["state"] = $_POST["state"];
		$events[] = $start_event;
		
		$end_event["address"] = "501 Duanesberg Road";
		$end_event["city"] = "Schenectady";
		$end_event["state"] = "NY";
		$events[] = $end_event;
		
		//@echo get_map_info($events)["map_miles"];
	}
	
	//GET NAT'L FUEL AVG
	function get_natl_fuel_avg()
	{
		fetch_fuel_avg();
	}
	
	//ONE-TIME SCRIPT: FIX ALLOCATED LOAD FOR EXISTING LOG ENTRIES
	function fix_load_types()
	{
		$where = null;
		$where = " 1 = 1 ";
		$all_events = db_select_log_entrys($where);
		
		foreach($all_events as $event)
		{
			$where = null;
			$where["id"] = $event["allocated_load_id"];
			$load = db_select_load($where);
			
			$update = null;
			$update["load_type"] = "Full Load";
			db_update_load($update,$where);
		}
		
		echo "Success!!";
	}
	
	//ONE-TIME SCRIPT: FIX ALLOCATED LOAD FOR EXISTING LOG ENTRIES
	function fix_allocated_loads()
	{
		$where = null;
		$where = " 1 = 1 ";
		$all_events = db_select_log_entrys($where);
		
		foreach($all_events as $event)
		{
			//UPDATE THE ALLOCATED LOAD
			$log_entry = $event;
			$update = null;
			if(empty($log_entry["load_id"]))
			{
				//GET NEXT EVENT WITH A LOAD
				$where = null;
				$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime > '".$log_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
				$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
				
				if(!empty($next_loaded_event))
				{
					$update["allocated_load_id"] = $next_loaded_event["load_id"];
				}
				else
				{
					$update["allocated_load_id"] = $log_entry["load_id"];
				}
			}
			else
			{
				$update["allocated_load_id"] = $log_entry["load_id"];
			}
			$where = null;
			$where["id"] = $log_entry["id"];
			db_update_log_entry($update,$where);
		}
		
		echo "Success!!";
	}	
	
	//ONE-TIME SCRIPT: UNLOCK ALL EVENTS
	function unlock_events()
	{
		$where = null;
		$where = " 1 = 1";
		$all_events = db_select_log_entrys($where);
		
		foreach($all_events as $event)
		{
			$update["locked_datetime"] = null;
			
			$where = null;
			$where["id"] = $event["id"];
			db_update_log_entry($update,$where);
		}
		
		echo "Success!!!";
	}

	//ONE-TIME SCRIPT: MAKE ALL FUEL STOPS COMDATA SOURCE
	function mark_fuel_as_comdata()
	{
		$where = null;
		$where = "1 = 1"; //GET ALL FUEL STOPS
		$fuel_stops = db_select_fuel_stops($where);
		
		foreach($fuel_stops as $fuel_stop)
		{
			$update = null;
			$update["source"] = "ComData";
			
			$where = null;
			$where["id"] = $fuel_stop["id"];
			//echo  $fuel_stop["id"];
			//echo "<br>";
			//db_update_fuel_stop($update,$where);
		}
		
		echo "All Fuel Stops have been updated!";
		
	}
	
	function get_miles($parameters)
	{
		echo  urldecode($parameters);
	}
}