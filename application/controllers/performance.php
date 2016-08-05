<?php		


	
class Performance extends MY_Controller 
{

	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		
		//GET ALL TRUCKS
		$where = null;
		$where = " status <> 'Returned' ";
		$trucks = db_select_trucks($where,"truck_number");
		
		$truck_dropdown_options = array();
		$truck_dropdown_options["All"] = "All Trucks";
		foreach($trucks as $truck)
		{
			$truck_dropdown_options[$truck["id"]] = $truck["truck_number"];
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
		
		//GET OPTIONS FOR DRIVER MANAGER DROPDOWN LIST
		$where = null;
		$where['role'] = "Driver Manager";
		$driver_managers = db_select_persons($where);
		
		$driver_manager_dropdown_options = array();
		$driver_manager_dropdown_options['All'] = "All Driver Managers";
		foreach ($driver_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$driver_manager_dropdown_options[$manager['id']] = $title;
		}

		$data['driver_manager_dropdown_options'] = $driver_manager_dropdown_options;
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['tab'] = 'Performance';
		$data['title'] = "Performance";
		$this->load->view('performance_view',$data);
		
	}// end index()

	//LOAD LOG
	function load_list()
	{
		
		//GET FILTER PARAMETERS
		$fm_id = (int)$_POST["fm_filter_dropdown"];
		$dm_id = (int)$_POST["dm_filter_dropdown"];
		$truck_id = (int)$_POST["truck_filter_dropdown"];
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		
		
		//GET SETTLEMENTS
		$where = " AND locked_datetime IS NOT NULL ";
		
		//FLEET MANAGER FILTER
		if($fm_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($fm_id))
			{
				$where = $where." AND performance_review.fm_id = ".$fm_id;
			}
		}
		
		//DRIVER MANAGER FILTER
		if($dm_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($dm_id))
			{
				$where = $where." AND performance_review.dm_id = ".$dm_id;
			}
		}
		
		//TRUCK FILTER
		if($truck_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($truck_id))
			{
				$where = $where." AND performance_review.truck_id = ".$truck_id;
			}
		}
		
		//START DATE FILTER
		if(!empty($start_date))
		{
			$start_datetime = date("Y-m-d G:i:s",strtotime($start_date));
			$where = $where." AND log_entry.entry_datetime > '".$start_datetime."'";
		}
		
		//END DATE FILTER
		if(!empty($end_date))
		{
			$end_datetime = date("Y-m-d G:i:s",strtotime($end_date)+60*60*24);
			$where = $where." AND log_entry.entry_datetime < '".$end_datetime."'";
		}
		
		$where = substr($where,4);
		
		if(empty($where))
		{
			$where = " 1 = 1 "; //IF NOTHING IS FILTERED
		}
		
		//echo $where;
		$performance_reviews = null;
		$performance_reviews = db_select_performance_reviews($where,"entry_datetime DESC");
		
		$data['performance_reviews'] = $performance_reviews;
		$this->load->view('performance/performance_div',$data);
		
	}//end load_log()

	//OPEN SETTLEMENT DETAILS
	function open_details()
	{
		$pr_id = $_POST["performance_id"];

		//GET SETTLEMENT
		$where = null;
		$where["id"] = $pr_id;
		$pr = db_select_performance_review($where);
		
		
		//GET LOG ENTRY FOR END WEEK
		$where = null;
		$where["id"] = $pr["end_week_id"];
		$log_entry = db_select_log_entry($where);
		
		$log_entry_id = $log_entry["id"];
		
		//GET FM FOR THIS TRUCK
		$where = null;
		$where["id"] = $log_entry["truck_id"];
		$truck = db_select_truck($where);
		
		$pr_stats = get_performance_stats($pr["end_week_id"]);
		
		//UPDATE PERFORMANCE REVIEW WITH UPDATED CALCULATIONS
		$performance_review = null;
		$performance_review["truck_id"] = $log_entry["truck_id"];
		$performance_review["fm_id"] = $truck["fm_id"];
		$performance_review["end_week_id"] = $log_entry["id"];
		$performance_review["hours"] = $pr_stats["hours"];
		$performance_review["map_miles"] = $pr_stats["map_miles"];
		$performance_review["odometer_miles"] = $pr_stats["odometer_miles"];
		$performance_review["mpg"] = $pr_stats["mpg"];
		$performance_review["total_revenue"] = $pr_stats["total_revenue"];
		$performance_review["standard_expenses"] = $pr_stats["standard_expenses"];
		$performance_review["carrier_revenue"] = $pr_stats["carrier_revenue"];
		
		//UPDATE EXISTING PR
		$where = null;
		$where["id"] = $pr_id;
		db_update_performance_review($performance_review,$where);
		
		//GET PREVIOUS END WEEK FOR THIS DRIVER
		$where = null;
		$where = " (log_entry.truck_id = ".$truck["id"].") AND entry_type = 'End Week' AND entry_datetime = ( SELECT MAX(entry_datetime) FROM log_entry WHERE (truck_id = ".$truck["id"].") AND entry_type = 'End Week' AND entry_datetime < '".$log_entry["entry_datetime"]."')";
		$previous_end_week = db_select_log_entry($where);
		
		//GET ALL SHIFT REPORTS (log_entries) FOR CHECKLIST
		$where = null;
		$where = " truck_id = ".$truck["id"]." AND entry_type = 'Shift Report' AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
		if(!empty($previous_end_week))
		{
			$where = $where." AND entry_datetime > '".$previous_end_week["entry_datetime"]."'";
		}
		
		//echo $where;
		$shift_report_log_entries = db_select_log_entrys($where);
		
		//GET OPTIONS FOR FLEET MANAGER DROPDOWN LIST
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
		
		//GET OPTIONS FOR DRIVER MANAGER DROPDOWN LIST
		$where = null;
		$where['role'] = "Driver Manager";
		$driver_managers = db_select_persons($where);
		
		$driver_manager_dropdown_options = array();
		$driver_manager_dropdown_options['Select'] = "Select";
		foreach ($driver_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$driver_manager_dropdown_options[$manager['id']] = $title;
		}

		$data['driver_manager_dropdown_options'] = $driver_manager_dropdown_options;
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$data['shift_report_log_entries'] = $shift_report_log_entries;
		$data['pr'] = $pr;
		$data['log_entry'] = $log_entry;
		$data['log_entry_id'] = $log_entry["id"];
		$data['pr_stats'] = $pr_stats;
		$this->load->view('performance/performance_details',$data);
	}
	
	function save_pr_details()
	{
		$pr_id = $_POST["pr_id"];
		$solo_or_team = $_POST["solo_or_team"];
		$fm_id = $_POST["pr_fm"];
		$dm_id = $_POST["pr_dm"];
		
		//UPDATE PERFORMANCE REVIEW
		$update = null;
		$update["fm_id"] = $fm_id;
		$update["dm_id"] = $dm_id;
		$update["solo_or_team"] = $solo_or_team;
		
		$where = null;
		$where["id"] = $pr_id;
		db_update_performance_review($update,$where);
	}
	
	//REFRESH ROW
	function refresh_row()
	{
		$pr_id = $_POST["performance_id"];

		//GET SETTLEMENT
		$where = null;
		$where["id"] = $pr_id;
		$pr = db_select_performance_review($where);
		
		//GET LOG ENTRY FOR END WEEK
		$where = null;
		$where["id"] = $pr["end_week_id"];
		$log_entry = db_select_log_entry($where);
		
		$log_entry_id = $log_entry["id"];
		
		$pr_stats = get_performance_stats($pr["end_week_id"]);
		
		//CHECK IF PR CHECKLIST IS COMPLETE
		$pr_is_complete = true;
		if(!empty($pr_stats["loads_for_week"]))
		{
			foreach($pr_stats["loads_for_week"] as $load_for_week)
			{
				 if($load_for_week["rate_source"] == "expected_revenue")
				{
					 $pr_is_complete = false;
					 break;
				}
				if($load_for_week["miles_source"] == "expected_miles")
				{
					 $pr_is_complete = false;
					 break;
				}
			}
		}
		else
		{
			$pr_is_complete = false;
		}
		
		//GET PREVIOUS END WEEK FOR THIS DRIVER
		$where = null;
		$where = " (log_entry.truck_id = ".$log_entry["truck_id"].") AND entry_type = 'End Week' AND entry_datetime = ( SELECT MAX(entry_datetime) FROM log_entry WHERE (truck_id = ".$log_entry["truck_id"].") AND entry_type = 'End Week' AND entry_datetime < '".$log_entry["entry_datetime"]."')";
		$previous_end_week = db_select_log_entry($where);
		
		//GET ALL SHIFT REPORTS (log_entries) FOR CHECKLIST
		$where = null;
		$where = " truck_id = ".$log_entry["truck_id"]." AND entry_type = 'Shift Report' AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
		if(!empty($previous_end_week))
		{
			$where = $where." AND entry_datetime > '".$previous_end_week["entry_datetime"]."'";
		}
		
		//echo $where;
		$shift_report_log_entries = db_select_log_entrys($where);
		$shift_report_hours = 0;
		if(!empty($shift_report_log_entries))
		{
			foreach($shift_report_log_entries as $sr_log_entry)
			{
				//GET SHIFT REPORT
				$where = null;
				$where["log_entry_id"] = $sr_log_entry["id"];
				$shift_report = db_select_shift_report($where);
				
				if(!empty($shift_report["shift_s_time"]) && !empty($shift_report["shift_e_time"]))
				{
					$shift_report_hours = $shift_report_hours + (strtotime($shift_report["shift_e_time"]) - strtotime($shift_report["shift_s_time"]))/60/60;
				}
				
				$shift_report_is_complete = shift_report_is_complete($shift_report);
				
				if(!$shift_report_is_complete["is_complete"])
				{
					 $pr_is_complete = false;
					 break;
				}
			}
		}
		else
		{
			 $pr_is_complete = false;
		}
		
		$data = null;
		$data['shift_report_hours'] = $shift_report_hours;
		$data['pr_is_complete'] = $pr_is_complete;
		$data['pr'] = $pr;
		$data['log_entry'] = $log_entry;
		$data['pr_stats'] = $pr_stats;
		$this->load->view('performance/performance_row',$data);
	}
	
	//GET STATS FOR SUMMARY
	function get_summary_stats()
	{
		//GET FILTER PARAMETERS
		$fm_id = (int)$_POST["fm_filter_dropdown"];
		$dm_id = (int)$_POST["dm_filter_dropdown"];
		$truck_id = (int)$_POST["truck_filter_dropdown"];
		$start_date = $_POST["start_date_filter"];
		$end_date = $_POST["end_date_filter"];
		
		
		//GET SETTLEMENTS
		$where = " AND locked_datetime IS NOT NULL ";
		
		//FLEET MANAGER FILTER
		if($fm_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($fm_id))
			{
				$where = $where." AND performance_review.fm_id = ".$fm_id;
			}
		}
		
		//DRIVER MANAGER FILTER
		if($dm_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($dm_id))
			{
				$where = $where." AND performance_review.dm_id = ".$dm_id;
			}
		}
		
		//TRUCK FILTER
		if($truck_id != "All")
		{
			//MAKE SURE INPUT IS AN INT
			if(is_int($truck_id))
			{
				$where = $where." AND performance_review.truck_id = ".$truck_id;
			}
		}
		
		//START DATE FILTER
		$start_datetime = date("Y-m-d G:i:s",time()-60*60*24*365);//ONE YEAR AGO
		if(!empty($start_date))
		{
			$start_datetime = date("Y-m-d G:i:s",strtotime($start_date));
			$where = $where." AND log_entry.entry_datetime > '".$start_datetime."'";
		}
		
		//END DATE FILTER
		$end_datetime = date("Y-m-d G:i:s",time()+60*60*24);
		if(!empty($end_date))
		{
			$end_datetime = date("Y-m-d G:i:s",strtotime($end_date)+60*60*24);
			$where = $where." AND log_entry.entry_datetime < '".$end_datetime."'";
		}
		
		$where = substr($where,4);
		
		if(empty($where))
		{
			$where = " 1 = 1 "; //IF NOTHING IS FILTERED
		}
		
		//echo $where;
		$performance_reviews = null;
		$performance_reviews = db_select_performance_reviews($where,"entry_datetime DESC");
		
		$total_trucks = 0;
		$total_teams = 0;
		$total_solos = 0;
		$total_map_miles = null;
		$total_team_miles = 0;
		$total_solo_miles = 0;
		$total_odom_miles = null;
		$total_gallons_used = null;
		$total_revenue = null;
		$total_carrier_revenue = null;
		$total_standard_expenses = null;
		$total_truck_fuel_expense = null;
		$total_raw_profit = null;
		$total_truck_profit = null;
		$total_team_profit = 0;
		$total_solo_profit = 0;
		$total_bobtail_miles = null;
		$total_deadhead_miles = null;
		$total_light_miles = null;
		$total_loaded_miles = null;
		$total_reefer_miles = null;
		if(!empty($performance_reviews))
		{
			foreach($performance_reviews as $pr)
			{
				$pr_stats = get_performance_stats($pr["end_week_id"]);
				
				
				//CALC MAP MILES
				$total_map_miles = $total_map_miles + $pr_stats["map_miles"];
				
				//CALC ODOM MILES
				$total_odom_miles = $total_odom_miles + $pr_stats["odometer_miles"];
				
				//CALC GALLONS USED
				$total_gallons_used = $total_gallons_used + $pr_stats["gallons_used"];
				
				//CALC TOTAL CARRIER REV
				$total_carrier_revenue = $total_carrier_revenue + $pr_stats["carrier_revenue"];
				
				//CALC GALLONS USED
				$total_revenue = $total_revenue + $pr_stats["total_revenue"];
				
				//CALC RAW PROFIT
				$total_raw_profit = $total_raw_profit + $pr_stats["raw_profit"];
				
				//CALC TOTAL TRUCK PROFIT
				$total_truck_profit = $total_truck_profit + $pr_stats["carrier_profit"];
				
				//CALC TOTAL STANDARD EXPENSES
				$total_standard_expenses = $total_standard_expenses + $pr_stats["standard_expenses"];
				
				//CALC TOTAL FUEL EXPENSE
				$total_truck_fuel_expense = $total_truck_fuel_expense + ($pr_stats["total_fuel_expense"] - $pr_stats["total_reefer_fuel_expense"]);
				

				//GET ALL THE MILEAGE FOR ALL THE LEGS
				$total_bobtail_miles = $total_bobtail_miles + $pr_stats["total_bobtail_miles"];
				$total_deadhead_miles = $total_deadhead_miles + $pr_stats["total_deadhead_miles"];
				$total_light_miles = $total_light_miles + $pr_stats["total_light_miles"];
				$total_loaded_miles = $total_loaded_miles + $pr_stats["total_loaded_miles"];
				$total_reefer_miles = $total_reefer_miles + $pr_stats["total_reefer_miles"];
				
				$total_trucks++;
				//TOTAL TEAMS AND SOLOS
				if($pr["solo_or_team"] == "Solo")
				{
					$total_solos++;
					$total_solo_profit = $total_solo_profit + $pr_stats["carrier_profit"];
					$total_solo_miles = $total_solo_miles + $pr_stats["map_miles"];
				}
				else if($pr["solo_or_team"] == "Team")
				{
					$total_teams++;
					$total_team_profit = $total_team_profit + $pr_stats["carrier_profit"];
					$total_team_miles = $total_team_miles + $pr_stats["map_miles"];
				}
			}
		}
		
		//CALC BOOKING RATE
		@$rate_per_mile = round($total_revenue/$total_map_miles,2);
			
		//CALCULATE OOR %
		@$oor = round((($total_odom_miles - $total_map_miles)/$total_map_miles)*100,2);
		
		//CALCULATE MPG
		@$mpg = round($total_odom_miles/$total_gallons_used,2);
		
		//CALUCULATE CARRIER RATE
		
		@$carrier_rate = round($total_carrier_revenue/$total_map_miles,2);
		
		$avg_fuel_price = calculate_average_fuel_price($start_datetime, $end_datetime);
		
		$average_fuel_expense = 0;
		if($total_gallons_used != 0)
		{
			$average_fuel_expense = $total_truck_fuel_expense/$total_gallons_used;
		}
		
		
		$summary_stats = null;
		$summary_stats["total_trucks"] = $total_trucks;
		$summary_stats["total_solos"] = $total_solos;
		$summary_stats["total_teams"] = $total_teams;
		$summary_stats["book_rate"] = $rate_per_mile;
		$summary_stats["oor"] = $oor;
		$summary_stats["mpg"] = $mpg;
		$summary_stats["map_miles"] = $total_map_miles;
		$summary_stats["total_solo_miles"] = $total_solo_miles;
		$summary_stats["total_team_miles"] = $total_team_miles;
		$summary_stats["total_revenue"] = $total_revenue;
		$summary_stats["total_standard_expenses"] = $total_standard_expenses;
		$summary_stats["total_raw_profit"] = $total_raw_profit;
		$summary_stats["total_truck_profit"] = $total_truck_profit;
		$summary_stats["total_team_profit"] = $total_team_profit;
		$summary_stats["total_solo_profit"] = $total_solo_profit;
		$summary_stats["carrier_rate"] = $carrier_rate;
		$summary_stats["avg_fuel_price"] = $avg_fuel_price;
		$summary_stats["average_fuel_expense"] = $average_fuel_expense;
		$summary_stats["total_bobtail_miles"] = $total_bobtail_miles;
		$summary_stats["total_deadhead_miles"] = $total_deadhead_miles;
		$summary_stats["total_light_miles"] = $total_light_miles;
		$summary_stats["total_loaded_miles"] = $total_loaded_miles;
		$summary_stats["total_reefer_miles"] = $total_reefer_miles;
		
		
		$data['summary_stats'] = $summary_stats;
		$this->load->view('performance/performance_summary_stats_div',$data);
	}
	
	//LOAD SETTLEMENT VIEW
	function load_truck_performance_report($log_entry_id)
	{
	
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		//GET TRUCK
		$where = null;
		$where["id"] = $log_entry["truck_id"];
		$truck = db_select_truck($where);
		
		//GET ALL LEGS FOR THIS SETTLEMENT
		$stats = get_truck_end_week_stats($log_entry);
		
		$data['title'] = "Truck ".$truck["truck_number"];
		$data['stats'] = $stats;
		$data['truck'] = $truck;
		$data['log_entry'] = $log_entry;
		$this->load->view('performance/truck_statement_view',$data);
	}
}