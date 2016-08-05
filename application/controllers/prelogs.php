<?php	

class Prelogs extends MY_Controller
{
	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//GET ALL ACTIVE TRUCKS
		$where = null;
		$where["dropdown_status"] = "Show";
		$trucks = db_select_trucks($where,"truck_number");
		
		$truck_dropdown_options = array();
		$truck_dropdown_options["All"] = "All Trucks";
		foreach($trucks as $truck)
		{
			$truck_dropdown_options[$truck["id"]] = $truck["truck_number"];
		}
		
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['tab'] = 'Pre-Logs';
		$data['title'] = "Pre-Logs";
		$this->load->view('prelogs_view',$data);
	}// end index()
	
	function load_event_list()
	{
		$truck_id = $_POST["truck_filter_dropdown"];
		
		$last_known_loc_text = "No Record";
		$last_known_loc_gps = "";
		if($truck_id == "All")
		{
			$filter_truck_id = null;	
			
			//GET GOALPOINTS
			$where = null;
			$where["completion_time"] = null;	
			//SORT BY EXPECTED TIME
			$goalpoints = db_select_goalpoints($where,"expected_time DESC");
		}
		else //IF FILTERED BY TRUCK
		{
			$filter_truck_id = $truck_id;	
			
			//GET GOALPOINTS
			$where = null;
			$where["truck_id"] = $filter_truck_id;
			//SORT BY GP ORDER
			$goalpoints = db_select_goalpoints($where,"gp_order DESC");
			
			//DETERMINE LAST KNOW LOCATION TO DISPLAY AT TOP OF REPORT
			//SELECT THE MOST RECENT COMPLETED GOALPOINT
			$where = null;
			$where = "truck_id = $filter_truck_id AND completion_time IS NOT NULL AND expected_time = (SELECT MAX(expected_time) FROM goalpoint WHERE truck_id = $filter_truck_id AND completion_time IS NOT NULL)";
			$most_recently_completed_gp = db_select_goalpoint($where);
			
			//SELECT THE MOST RECENT CONTACT ATTEMPT
			$where = null;
			$where = " truck_id = $filter_truck_id AND ca_time = (SELECT MAX(ca_time) FROM contact_attempt WHERE truck_id = $filter_truck_id)";
			$most_recent_ca = db_select_contact_attempt($where);
			
			//SELECT THE MOST RECENT GEOPOINT
			$where = null;
			$where = " truck_id = $filter_truck_id AND datetime = (SELECT MAX(datetime) FROM geopoint WHERE truck_id = $filter_truck_id)";
			
			$most_recent_geopoint = db_select_geopoint($where);
			
			
			//IF THERE ARE NO COMPELTED GOALPOINT AND NO CONTACT ATTEMPTS - USE THE START EVENT GOALPOINT
			//ELSE, USE THE MOST RECENT OF THE THE COMPLETED GOALPOINT AND CONTACT ATTEMPTS
			if(!empty($most_recently_completed_gp) || !empty($most_recent_ca) || !empty($most_recent_geopoint["datetime"]))
			{
				$gp_time = strtotime($most_recently_completed_gp["expected_time"]);
				$ca_time = strtotime($most_recent_ca["ca_time"]);
				$geopoint_time = strtotime($most_recent_geopoint["datetime"]);
				
				if($geopoint_time >= $gp_time && $geopoint_time >= $ca_time)
				{
					$last_known_loc_gps = $most_recent_geopoint["latitude"].",".$most_recent_geopoint["longitude"];
					$starting_event_time = $geopoint_time;
					$last_known_loc_text = "Geopoint ".date("m/d H:i",$starting_event_time);
				}
				else if($gp_time >= $ca_time && $gp_time >= $geopoint_time)
				{
					$last_known_loc_gps = $most_recently_completed_gp["gps"];
					$starting_event_time = $gp_time;
					$last_known_loc_text = "Goalpoint ".date("m/d H:i",$starting_event_time);
				}
				else if($ca_time >= $gp_time && $ca_time >= $geopoint_time)
				{
					$last_known_loc_gps = $most_recent_ca["ca_gps"];
					$starting_event_time = $ca_time;
					$last_known_loc_text = "Contact ".date("m/d H:i",$starting_event_time);
				}
			}
			
		}
		
		//GET ALL ACTIVE TRUCKS
		$where = null;
		$where["dropdown_status"] = "Show";
		$trucks = db_select_trucks($where,"truck_number");
		
		$gp_edit_truck_options = array();
		$truck_dropdown_options["Select"] = "Select";
		foreach($trucks as $truck)
		{
			$gp_edit_truck_options[$truck["id"]] = $truck["truck_number"];
		}
		
		$data['last_known_loc_gps'] = $last_known_loc_gps;
		$data['last_known_loc_text'] = $last_known_loc_text;
		$data['filter_truck_id'] = $filter_truck_id;
		$data['gp_edit_truck_options'] = $gp_edit_truck_options;
		$data['goalpoints'] = $goalpoints;
		$this->load->view('prelogs/prelog_div',$data);
	}
	
	function update_gp_exp_time($truck_id)
	{
		date_default_timezone_set('America/Denver');
		
		$map_events = array();
		$starting_event_time = null;
		
		
		//DETERMINE STARTING POINT
		//SELECT THE MOST RECENT COMPLETED GOALPOINT
		$where = null;
		$where = "truck_id = $truck_id AND completion_time IS NOT NULL AND expected_time = (SELECT MAX(expected_time) FROM goalpoint WHERE truck_id = $truck_id AND completion_time IS NOT NULL)";
		$most_recently_completed_gp = db_select_goalpoint($where);
		
		//SELECT THE MOST RECENT CONTACT ATTEMPT
		$where = null;
		$where = " truck_id = $truck_id AND ca_time = (SELECT MAX(ca_time) FROM contact_attempt WHERE truck_id = $truck_id)";
		$most_recent_ca = db_select_contact_attempt($where);
		
		//SELECT THE MOST RECENT GEOPOINT
		$where = null;
		$where = " truck_id = $truck_id AND datetime = MAX(SELECT datetime from geopoint WHERE truck_id = $truck_id)";
		$most_recent_geopoint = db_select_geopoint($where);
		
		
		//IF THERE ARE NO COMPELTED GOALPOINT AND NO CONTACT ATTEMPTS - USE THE START EVENT GOALPOINT
		//ELSE, USE THE MOST RECENT OF THE THE COMPLETED GOALPOINT AND CONTACT ATTEMPTS
		$map_event = null;
		$last_know_loc_text = "No Record";
		$last_know_loc_gps = "";
		if(!empty($most_recently_completed_gp) || !empty($most_recent_ca) || !empty($most_recent_geopoint["datetime"]))
		{
			$gp_time = strtotime($most_recently_completed_gp["expected_time"]);
			$ca_time = strtotime($most_recent_ca["ca_time"]);
			$geopoint_time = strtotime($most_recent_geopoint["datetime"]);
			
			if($geopoint_time >= $gp_time && $geopoint_time >= $ca_time)
			{
				$map_event["gps_coordinates"] = $most_recent_geopoint["latitude"].",".$most_recent_geopoint["longitude"];
				$starting_event_time = $geopoint_time;
				$last_know_loc_text = "Geopoint ".date("m/d H:i",$geopoint_time);
			}
			else if($gp_time >= $ca_time && $gp_time >= $geopoint_time)
			{
				$map_event["gps_coordinates"] = $most_recently_completed_gp["gps"];
				$starting_event_time = $gp_time;
				$last_know_loc_text = "Goalpoint ".date("m/d H:i",$geopoint_time);
			}
			else if($ca_time >= $gp_time && $ca_time >= $geopoint_time)
			{
				$map_event["gps_coordinates"] = $most_recent_ca["ca_gps"];
				$starting_event_time = $ca_time;
				$last_know_loc_text = "Contact ".date("m/d H:i",$geopoint_time);
			}

			$last_know_loc_gps = $map_event["gps_coordinates"];
			
			$map_events[] = $map_event;
		}
		else if(!empty($most_recently_completed_gp))
		{
			$map_event["gps_coordinates"] = $most_recently_completed_gp["gps"];
			$starting_event_time = strtotime($most_recently_completed_gp["expected_time"]);
			
			$map_events[] = $map_event;
		}
		
		
		//GET GOALPOINTS THAT AREN'T MARKED COMPLETE YET
		$where = null;
		$where["truck_id"] = $truck_id;
		$where["completion_time"] = null;
		$goalpoints = db_select_goalpoints($where,"gp_order");
		
		//CALCULATE EXPECTED TIMES
		$i = 1;
		$total_event_durations = 0;//in seconds
		if(!empty($most_recently_completed_gp))
		{
			//MAKE SURE TO ACCOUNT FOR EXPECTED SITTING TIME ON THE FIRST EVENT EXPECTED TIME
			if($most_recently_completed_gp["arrival_departure"] == "Arrival")
			{
				$seconds_already_sat = strtotime($most_recent_ca["ca_time"]) - strtotime($most_recently_completed_gp["expected_time"]);
				
				//IF MOST RECENT CONTACT ATTEMPT AFTER CURRENT ARRIVAL TIME
				if($seconds_already_sat > 0)
				{
					$remaining_seconds_expected_at_stop = ($most_recently_completed_gp["duration"]*60) - ($seconds_already_sat);
					$total_event_durations = $remaining_seconds_expected_at_stop;
				}
				else
				{
					$total_event_durations = $most_recently_completed_gp["duration"]*60;
				}
				
				
			}
		}
		
		if(!empty($goalpoints))
		{
			foreach($goalpoints as $gp)
			{
				if(empty($starting_event_time))
				{
					$starting_event_time = strtotime($gp["expected_time"]);
				}
				
				// if($i == 1)//ON FIRST LOOP, SET PREVIOUS EVENT TIME TO START EVENT TIME
				// {
					// $previous_event_expected_time = $starting_event_time;
				// }
				
				if($gp["gp_type"] != "Start")
				{
					$map_event = null;
					$map_event["gps_coordinates"] = $gp["gps"];
					
					$map_events[] = $map_event;
					
					$map_info = null;
					$map_info = get_map_info($map_events); 
					
					$map_miles = $map_info["map_miles"];
					
					
					$average_speed = 55;
					$hours_to_gp = $map_miles/55;
					
					
					$expected_time = round($starting_event_time + $hours_to_gp*60*60 + $total_event_durations);//hour*minutes*seconds

					//echo $gp["gp_type"]." + ".$starting_event_time." + ".($hours_to_gp*60*60)." + ".$total_event_durations." = ".$expected_time." ".date("Y-m-d H:i:s",$expected_time)."<br>";
					
					$update = null;
					$update["expected_time"] = date("Y-m-d H:i:s",$expected_time);
					$update["leeway"] = $map_miles;
					$update["dispatch_notes"] = $map_info["route_url"];
					
					$where = null;
					$where["id"] = $gp["id"];
					db_update_goalpoint($update,$where);

					//DETERMINE GP DURATION
					$event_duration = $gp["duration"] * 60;//minutes to seconds
					
					$total_event_durations = $total_event_durations + $event_duration;
				}
				else
				{
					$update = null;
					$update["expected_time"] = date("Y-m-d H:i:s",strtotime($log_entry["entry_datetime"]));
					
					$where = null;
					$where["id"] = $gp["id"];
					db_update_goalpoint($update,$where);
				}
				
				// $previous_event_expected_time = strtotime($gp["expected_time"]);
			}
		}

		//CALCULATE LEEWAY
		
		//GET GOALPOINTS THAT AREN'T MARKED COMPLETE YET
		$where = null;
		$where["shift_report_id"] = $shift_report_id;
		$where["completion_time"] = null;
		$goalpoints = db_select_goalpoints($where,"gp_order DESC");
		
		//REVERSE ORDER
		if(!empty($goalpoints))
		{
			$i = 1;
			foreach($goalpoints as $gp)
			{
				$leeway = strtotime($gp["deadline"]) - strtotime($gp["expected_time"]);
					
				if(!empty($gp["deadline"]))
				{
					if($i == 1)
					{
						$min_leeway = $leeway;
						//echo $leeway;
					}
					
					if($leeway < $min_leeway)
					{
						$min_leeway = $leeway;
					}
					
					$update = null;
					$update["leeway"] = $min_leeway/60/60;//in hours
					//echo $min_leeway/60/60;//in hours
					$i++;
				}
				else
				{
					//IF LEEWAY WAS CALCULATED FOR LATER EVENT
					if(isset($min_leeway))
					{
						$update = null;
						$update["leeway"] = $min_leeway/60/60;//in hours
					}
					else
					{
						$update = null;
						$update["leeway"] = null;
					}
					
				}
				
				$where = null;
				$where["id"] = $gp["id"];
				db_update_goalpoint($update,$where);
				//print_r($update);
			}
		}
	}
	
	function change_gp_order()
	{
		$goalpoint_id = $_POST["gp_id"];
		$direction = $_POST["direction"];
		
		//echo $direction;
		
		//GET THIS GP
		$where = null;
		$where["id"] = $goalpoint_id;
		$this_gp = db_select_goalpoint($where);
		
		if($direction == "down")
		{
			
			//GET THE GP DIRECTLY PRECEEDING THIS GP
			$where = null;
			$where["truck_id"] = $this_gp["truck_id"];
			$where["completion_time"] = null;
			$where["gp_order"] = $this_gp["gp_order"] - 1;
			$preceeding_gp = db_select_goalpoint($where);
			
			if($preceeding_gp["sync_gp_guid"] == $this_gp["sync_gp_guid"])
			{
				$where = null;
				$where["truck_id"] = $this_gp["truck_id"];
				$where["completion_time"] = null;
				$where["gp_order"] = $preceeding_gp["gp_order"] - 1;
				$preceeding_gp = db_select_goalpoint($where);
			}
			
			
			//GET ALL THE GPS FOR THE EVENT DIRECTLY PRECEEDING THIS GP EVENT
			$where = null;
			$where["truck_id"] = $this_gp["truck_id"];
			$where["sync_gp_guid"] = $preceeding_gp["sync_gp_guid"];
			$preceeding_goalpoints = db_select_goalpoints($where);
			
			//GRAB GPS FOR THIS EVENT
			$where = null;
			$where = null;
			$where["truck_id"] = $this_gp["truck_id"];
			$where["sync_gp_guid"] = $this_gp["sync_gp_guid"];
			$these_goalpoints = db_select_goalpoints($where);
			
			foreach($preceeding_goalpoints as $p_gp)
			{
				$update = null;
				$update["gp_order"] = $p_gp["gp_order"] + count($these_goalpoints);
				
				$where = null;
				$where["id"] = $p_gp["id"];
				db_update_goalpoint($update,$where);
			}
			
			
			foreach($these_goalpoints as $t_gp)
			{
				//SUBTRACT 1 FROM THIS GP
				$update = null;
				$update["gp_order"] = $t_gp["gp_order"] - count($preceeding_goalpoints);
				
				$where = null;
				$where["id"] = $t_gp["id"];
				db_update_goalpoint($update,$where);
			}
			
			
		}
		else if($direction == "up")
		{
			//GET THE GP DIRECTLY FOLLOING THIS GP
			$where = null;
			$where["truck_id"] = $this_gp["truck_id"];
			$where["completion_time"] = null;
			$where["gp_order"] = $this_gp["gp_order"] + 1;
			$following_gp = db_select_goalpoint($where);
			
			if($following_gp["sync_gp_guid"] == $this_gp["sync_gp_guid"])
			{
				$where = null;
				$where["truck_id"] = $this_gp["truck_id"];
				$where["completion_time"] = null;
				$where["gp_order"] = $following_gp["gp_order"] + 1;
				$following_gp = db_select_goalpoint($where);
			}
			
			
			//GET ALL THE GPS FOR THE EVENT DIRECTLY PRECEEDING THIS GP EVENT
			$where = null;
			$where["truck_id"] = $this_gp["truck_id"];
			$where["sync_gp_guid"] = $following_gp["sync_gp_guid"];
			$following_goalpoints = db_select_goalpoints($where);
			
			//GRAB GPS FOR THIS EVENT
			$where = null;
			$where = null;
			$where["truck_id"] = $this_gp["truck_id"];
			$where["sync_gp_guid"] = $this_gp["sync_gp_guid"];
			$these_goalpoints = db_select_goalpoints($where);
			
			foreach($following_goalpoints as $p_gp)
			{
				$update = null;
				$update["gp_order"] = $p_gp["gp_order"] - count($these_goalpoints);
				
				$where = null;
				$where["id"] = $p_gp["id"];
				db_update_goalpoint($update,$where);
			}
			
			
			foreach($these_goalpoints as $t_gp)
			{
				//SUBTRACT 1 FROM THIS GP
				$update = null;
				$update["gp_order"] = $t_gp["gp_order"] + count($following_goalpoints);
				
				$where = null;
				$where["id"] = $t_gp["id"];
				db_update_goalpoint($update,$where);
			}
		}
		
		//$this->calc_expected_gp_times($shift_report["id"]);
		//echo "<br>-DONE-";
	}
	
	//SAVE GOALPOINT EDIT FROM SHIFT REPORT
	function save_goalpoint()
	{
		$goalpoint_id = $_POST["goalpoint_id"];
		$truck_id = $_POST["gp_truck_id"];
		//$gp_type = $_POST["gp_type"];
		$gp_gps = $_POST["edit_gp_gps"];
		$gp_location = $_POST["edit_gp_location"];
		$gp_notes = $_POST["edit_gp_notes"];
		
		//GET GOALPOINT
		$where = null;
		$where["id"] = $goalpoint_id;
		$goalpoint = db_select_goalpoint($where);
		
		
		//UPDATE GOALPOINT
		$update_gp = null;
		$update_gp["truck_id"] = $truck_id;
		//$update_gp["gp_type"] = $gp_type;
		$update_gp["gps"] = $gp_gps;
		$update_gp["location"] = $gp_location;
		$update_gp["dm_notes"] = $gp_notes;
		
		$where = null;
		$where["sync_gp_guid"] = $goalpoint["sync_gp_guid"];
		db_update_goalpoint($update_gp,$where);
		
		
		
		//$this->calc_expected_gp_times($shift_report["id"]);
		
	}
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------------------------------------------------------
	
	//ONE TIME SCRIPT -- ADDS TRUCK ID TO GOALPOINT
	function add_truck_id_to_goalpoint()
	{
		$where = null;
		$where = true;
		$goalpoints = db_select_goalpoints($where);
		
		foreach($goalpoints as $gp)
		{
			//GET SHIFT REPORT
			$where = null;
			$where["id"] = $gp["shift_report_id"];
			$shift_report = db_select_shift_report($where);
			
			//GET LOG ENTRY
			$where = null;
			$where["id"] = $shift_report["log_entry_id"];
			$log_entry = db_select_log_entry($where);
			
			$update = null;
			$update["truck_id"] = $log_entry["truck_id"];
			
			$where = null;
			$where["id"] = $gp["id"];
			
			//db_update_goalpoint($update,$where);
			
			echo $shift_report["id"]." ".$log_entry["truck_id"]."<br>";
			
		}
	}
	
	//ONE TIME SCRIPT -- ADDS TRUCK ID TO GOALPOINT
	function add_truck_id_to_ca()
	{
		$where = null;
		$where = true;
		$contact_attempts = db_select_contact_attempts($where);
		
		foreach($contact_attempts as $ca)
		{
			//GET SHIFT REPORT
			$where = null;
			$where["id"] = $ca["shift_report_id"];
			$shift_report = db_select_shift_report($where);
			
			//GET LOG ENTRY
			$where = null;
			$where["id"] = $shift_report["log_entry_id"];
			$log_entry = db_select_log_entry($where);
			
			$update = null;
			$update["truck_id"] = $log_entry["truck_id"];
			
			$where = null;
			$where["id"] = $ca["id"];
			
			//db_update_contact_attempt($update,$where);
			
			echo $shift_report["id"]." ".$log_entry["truck_id"]."<br>";
			
		}
	}
}