<?php		


	
class Equipment extends MY_Controller 
{
	//INDEX
	function index()
	{	
	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Driver')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//GET ALL VENDORS
		$where = null;
		$where["type"] = "Vendor";
		$vendors = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN LIST OF VENDORS
		$vendor_dropdown_options = array();
		$vendor_dropdown_options["Select"] = "Select";
		foreach($vendors as $vendor)
		{
			$vendor_dropdown_options[$vendor["id"]] = $vendor["company_side_bar_name"];
		}
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$client_dropdown_options = array();
		$client_dropdown_options["Select"] = "Select";
		foreach($dd_all_clients as $client)
		{
			$client_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		$client_dropdown_options[0] = "Unassigned";
		
		//GET ALL ACTIVE COMPANIES
		$where = null;
		$where["type"] = "Carrier";
		$where["company_status"] = "Active";
		$companies = db_select_companys($where);
		
		//CREATE DROPDOWN LIST OF ALL ACTIVE CLIENTS
		$company_dropdown_options = array();
		$company_dropdown_options["Select"] = "Select";
		foreach($companies as $company)
		{
			$company_dropdown_options[$company["id"]] = $company["company_side_bar_name"];
		}
		
		//GET ALL TRUCKS
		$where = null;
		$where = "TRUE"; //SELECT ALL
		$trucks = db_select_trucks($where);
		
		
		$data['trucks'] = $trucks;
		$data['company_dropdown_options'] = $company_dropdown_options;
		$data['client_dropdown_options'] = $client_dropdown_options;
		$data['vendor_dropdown_options'] = $vendor_dropdown_options;
		$data['tab'] = 'Equipment';
		$data['title'] = "Equipment";
		$this->load->view('equipment_view',$data);
	
	}//END INDEX
	
	function load_equipment_file_upload()
	{
		$equipment_id = $_POST["equipment_id"];
		$equipment_type = $_POST['type'];
		
		if($equipment_type=="truck")
		{
			$where = null;
			$where['id'] = $equipment_id;
			$equipment = db_select_truck($where);
			$equipment_number = $equipment['truck_number'];
			
			$upload_options = array();
			$upload_options["Select"] = "Select";
			$upload_options["registration_link"] = "Registration";
			$upload_options["insurance_link"] = "Insurance";
			$upload_options["ifta_link"] = "IFTA";
			$upload_options["lease_agreement_link"] = "Lease Agreement";
			$upload_options["Attachment"] = "Other Attachment";
			
			
		}
		else if($equipment_type=="trailer")
		{
			$where = null;
			$where['id'] = $equipment_id;
			$equipment = db_select_trailer($where);
			$equipment_number = $equipment['trailer_number'];
			
			$upload_options = array();
			$upload_options["Select"] = "Select";
			$upload_options["registration_link"] = "Registration";
			$upload_options["insurance_link"] = "Insurance";
			$upload_options["lease_agreement_link"] = "Lease Agreement";
			$upload_options["Attachment"] = "Other Attachment";
		}
		else
		{
			$where = null;
			$where['id'] = $equipment_id;
			$equipment = db_select_ins_policy($where);
			
			$equipment_number = $equipment["policy_number"];
			
			$upload_options = array();
			$upload_options["Select"] = "Select";
			$upload_options["Attachment"] = "Attachment";
		}
		$data = null;
		$data["upload_options"] = $upload_options;
		$data["equipment_type"] = $equipment_type;
		$data["equipment_number"] = $equipment_number;
		$data["equipment"] = $equipment;
		$this->load->view('equipment/equipment_attachment_div',$data);
	}
	
	
	//----------------------- TRUCKS ------------------------------- //
	
	//LOAD TRUCK FILTER
	function load_truck_filter()
	{
		//GET ALL VENDORS
		$where = null;
		$where["type"] = "Vendor";
		$vendors = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN LIST OF VENDORS
		$vendor_dropdown_options = array();
		$vendor_dropdown_options["All"] = "All Vendors";
		foreach($vendors as $vendor)
		{
			$vendor_dropdown_options[$vendor["id"]] = $vendor["company_side_bar_name"];
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
		
		//GET OPTIONS FOR DRIVER MANAGER DROPDOWN LIST
		$where = null;
		$where['role'] = "Driver Manager";
		$driver_managers = db_select_persons($where);
		
		$driver_manager_dropdown_options = array();
		$driver_manager_dropdown_options['All'] = "All DM's";
		foreach ($driver_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$driver_manager_dropdown_options[$manager['id']] = $title;
		}
		
		
		$data['vendor_dropdown_options'] = $vendor_dropdown_options;
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$data['driver_manager_dropdown_options'] = $driver_manager_dropdown_options;
		$this->load->view('equipment/truck_filter_div',$data);
	}
	
	//LOAD TRUCK LIST
	function load_truck_list()
	{	
		//echo 'in load truck list';
		
		//GET ALL FILTERS
		$status =  $_POST["truck_status"];
		$fm_id =  $_POST["fm_filter_dropdown"];
		$vendor_id =  $_POST["vendor_dropdown_options"];
	
		//GET ALL TRUCKS
		$where = " 1 = 1 ";
		if($status != "All Statuses")
		{
			if($status == "On the road")
			{
				$where = $where." AND (truck.status = '".$status."' OR truck.status = 'Subtruck') ";
			}
			else
			{
				$where = $where." AND truck.status = '".$status."' ";
			}
		}
		
		if($vendor_id != "All")
		{
			$where = $where." AND truck.vendor_id = ".$vendor_id;
		}
		
		if($fm_id != "All")
		{
			$where = $where." AND truck.fm_id = ".$fm_id;
		}
			
		$trucks = db_select_trucks($where,"truck_number");
		
		//echo $where;
		
		$data['status_filter'] = "All";
		$data['equipment_type'] = "Trucks";
		$data['trucks'] = $trucks;
		$this->load->view('equipment/equipment_list_div',$data);
		
	}//end load_truck_list
	
	//LOAD TRUCK SUMMARY
	function load_truck_summary()
	{
		//GET ALL FILTERS
		$status =  $_POST["truck_status"];
		$fm_id =  $_POST["fm_filter_dropdown"];
		$dm_id =  $_POST["dm_filter_dropdown"];
		$vendor_id =  $_POST["vendor_dropdown_options"];
	
		//GET ALL TRUCKS
		$where = " 1 = 1 ";
		if($status != "All Statuses")
		{
			if($status == "On the road")
			{
				$where = $where." AND (truck.status = '".$status."' OR truck.status = 'Subtruck') ";
			}
			else
			{
				$where = $where." AND truck.status = '".$status."' ";
			}
		}
		
		if($vendor_id != "All")
		{
			$where = $where." AND truck.vendor_id = ".$vendor_id;
		}
		
		if($fm_id != "All")
		{
			$where = $where." AND truck.fm_id = ".$fm_id;
		}
		
		if($dm_id != "All")
		{
			$where = $where." AND truck.dm_id = ".$dm_id;
		}
			
		$trucks = db_select_trucks($where,"truck_number");
		
		$data['equipment_type'] = "Trucks";
		$data['trucks'] = $trucks;
		$this->load->view('equipment/truck_summary',$data);
		
	}//end load_truck_summary
	
	//GET MILES TILL NEXT WET SERVICE FOR TRUCK SUMMARY
	function get_current_odometer_for_truck()
	{
		$truck_id = $_POST["truck_id"];
		
		//GET TRUCK
		$where = null;
		$where["id"] = $truck_id;
		$truck = db_select_truck($where);
		
		//GET MOST RECENT EVENT
		$where = null;
		$where = "truck_id = ".$truck["id"]."
					AND entry_datetime = (
					SELECT MAX( entry_datetime ) 
					FROM log_entry
					WHERE truck_id = ".$truck["id"]." AND odometer > 0 AND (entry_type = 'Geopoint' OR entry_type = 'Fuel Fill' OR entry_type = 'Fuel Partial')) ";
		$log_entry = db_select_log_entry($where);
		
		$odometer_title = $log_entry["entry_type"]." ".$log_entry["city"].", ".$log_entry["state"]." ".date("n/j",strtotime($log_entry["entry_datetime"]));
		
		$odometer = number_format($log_entry["odometer"]);
		
		echo "<span title='$odometer_title' style=''>$odometer</span>";
	}
	
	//GET MILES TILL NEXT WET SERVICE FOR TRUCK SUMMARY
	function get_miles_till_next_service()
	{
		$truck_id = $_POST["truck_id"];
		
		//GET TRUCK
		$where = null;
		$where["id"] = $truck_id;
		$truck = db_select_truck($where);
		
		//GET MOST RECENT EVENT
		$where = null;
		$where = "truck_id = ".$truck["id"]."
					AND entry_datetime = (
					SELECT MAX( entry_datetime ) 
					FROM log_entry
					WHERE truck_id = ".$truck["id"]." AND odometer > 0 AND (entry_type = 'Geopoint' OR entry_type = 'Fuel Fill' OR entry_type = 'Fuel Partial')) ";
		$log_entry = db_select_log_entry($where);
		
		$odometer_title = $log_entry["entry_type"]." ".$log_entry["city"].", ".$log_entry["state"]." ".date("n/j",strtotime($log_entry["entry_datetime"]));
		
		
		//GET NEXT WET
		$where = null;
		$where = "	truck_id = ".$truck["id"]."
					AND entry_type = 'Wet Service'
					AND entry_datetime = ( 
					SELECT MAX( entry_datetime ) 
					FROM log_entry
					WHERE truck_id = ".$truck["id"]."
					AND entry_type = 'Wet Service')";
		$last_wet = db_select_log_entry($where);
		
		$wet_style = "";
		if(!empty($last_wet))
		{
			$next_wet = $last_wet["odometer"] + $truck["next_wet_service"] - $log_entry["odometer"];
			$next_wet_title = "Due at ".($last_wet["odometer"] + $truck["next_wet_service"]);
			$next_wet_text = number_format($next_wet);
			//SET STYLE
			if($next_wet < 6000 && $next_wet > 0)
			{
				$wet_style = "font-weight:bold; color:#FF9100; position:relative;";
			}
			else if($next_wet <= 0)
			{
				$wet_style = "font-weight:bold; color:red; position:relative;";
			}
		}
		else
		{
			$next_wet_text = "?";
			$next_wet_title = "No service records";
		}
		
		echo "<span title='$next_wet_title' style='$wet_style'>$next_wet_text</span>";
	}
	
	//GET INSURANCE STATUS FOR TRUCK
	function get_insurance_status()
	{
		date_default_timezone_set('America/Denver');
		
		$truck_id = $_POST["truck_id"];

		$snapshot_date = date("m/d/y H:i");
		
		$truck_ins_stats = get_truck_insurance_stats($truck_id,$snapshot_date);
		
		
		
//************ THIS GOES TO CUSTOMER HELPER *************************	
		
		// $snapshot_date_db_format = date("Y-m-d H:i:s",strtotime($snapshot_date));
		
		// //GET ALL UNIT COVERAGES FOR GIVEN UNIT (TRUCK)
		// $where = null;
		// $where = "coverage_current_since <= '".$snapshot_date_db_format."' 
				// AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				// AND unit_type = 'Truck' 
				// AND unit_id = $truck_id";
		// $unit_coverages = db_select_ins_unit_coverages($where,"unit_id");
		
		
		// $total_cost_per_month = 0;
		
		// $number_of_pd_coverages = 0;
		// $number_of_al_coverages = 0;
		// $number_of_cargo_coverages = 0;
		
		// $submessage_reefer = "";
		// $submessage_cargo = "";
		// $submessage_al = "";
		// $submessage_pd = "";
		// $submessage_rental = "";
		// $submessage_radius = "";
	
		// $cargo_is_covered = false;
		// $reefer_bd_is_covered = false;
		// $pd_is_covered = false;
		// $pd_ded_is_500 = false;
		// $al_is_covered = false;
		// $al_limit_covers_750k = false;
		// $al_limit_covers_1m = false;
		// $rental_is_covered = false;
		// $radius_is_unlimited = false;
		// $al_is_double_insured = false;
		// $pd_is_double_insured = false;
		// $cargo_is_double_insured = false;
		
		
		// //FOR EACH UNIT COVERAGE
		// if(!empty($unit_coverages))
		// {
			// foreach($unit_coverages as $uc)
			// {
				// //GET POLICY 
				// $where = null;
				// $where["id"] = $uc["ins_policy_id"];
				// $policy = db_select_ins_policy($where);
				
				// $policy_id = $policy["id"];
				
				// //GET POLICY PROFILE AT SNAPSHOT DATE
				// $where = null;
				// $where = "profile_current_since	 <= '".$snapshot_date_db_format."' 
						// AND (profile_current_till > '".$snapshot_date_db_format."' OR profile_current_till IS NULL)
						// AND ins_policy_id = $policy_id";
				// $ins_profile = db_select_ins_policy_profile($where);
				
				// //echo " policy_id:".$policy["id"];
				// //echo " profile_id:".$ins_profile["id"];
				// //echo " uc_id:".$uc["id"];
				// //echo "<br>";
				
				// //CHECK FOP CARGO COVERAGE
				// if($ins_profile["cargo_prem"] > 0)
				// {
					// $cargo_is_covered = true;
					// $number_of_cargo_coverages++;
				// }
				
				// //CHECK FOP REEFER BREAK DOWN
				// if($ins_profile["rbd_prem"] > 0 || ($ins_profile["cargo_ded"] > 0 && $ins_profile["cargo_limit"] > 0))
				// {
					// $reefer_bd_is_covered = true;
					// //$number_of_cargo_coverages++;
				// }
				
				// //CHECK FOR PD COVERAGE - ADD TO NUMBER OF PD COVERAGE
				// if($uc["pd_comp_prem"] > 0 || $uc["pd_coll_prem"] > 0)
				// {
					// $pd_is_covered = true;
					// $number_of_pd_coverages++;
					// //echo " ".$uc["id"]."-".$number_of_pd_coverages;
				// }
				
				// if($pd_is_covered)
				// {
					// //CHECK FOR PD DED OF 500
					// if($uc["pd_comp_ded"] <= 500 && $uc["pd_coll_ded"] <= 500)
					// {
						// $pd_ded_is_500 = true;
					// }
					// else
					// {
						// $submessage_pd = $submessage_pd."Physical Damage deductable for this unit on policy ".$policy["policy_number"]." is greater than $500. ";
					// }
					
					// //CHECK FOR RENTAL REIMBURSEMENT
					// if($uc["pd_rental_prem"] > 0)
					// {
						// $rental_is_covered = true;
					// }
					// else
					// {
						// $submessage_rental = $submessage_rental."The Physical damage coverage for this unit on policy ".$policy["policy_number"]." is missing rental reimbursement. ";
					// }
				// }
				
				// //CHECK FOR AL COVERAGE - ADD TO NUMBER OF AL COVERAGE
				// if($uc["al_um_bi_prem"] > 0 && $uc["al_uim_bi_prem"] > 0 && $uc["al_pip_prem"] > 0)
				// {
					// $al_is_covered = true;
					// $number_of_al_coverages++;
				// }
				
				// if($al_is_covered)
				// {
					// //CHECK FOR AL COVERAGE UP TO 750k
					// if($uc["al_um_bi_limit"] >= 750000 && $uc["al_uim_bi_limit"] >= 750000  && $uc["al_pip_limit"] >= 750000)
					// {
						// $al_limit_covers_750k = true;
					// }
					// else
					// {
						// $submessage_al = $submessage_al."Auto Liability limits for this unit on policy ".$policy["policy_number"]." is less than $750K. ";
					// }
					
					// //CHECK FOR AL COVERAGE UP TO 1M
					// if($al_limit_covers_750k == true)
					// {
						// if($uc["al_um_bi_limit"] >= 1000000 && $uc["al_uim_bi_limit"] >= 1000000  && $uc["al_pip_limit"] >= 1000000)
						// {
							// $al_limit_covers_1m = true;
						// }
						// else
						// {
							// $submessage_al = $submessage_al."Auto Liability limits for this unit on policy ".$policy["policy_number"]." is less than $1M. ";
						// }
					// }
				// }
				
				// //CHECK FOR UNLIMITED RADIUS
				// if($uc["radius"] == "Unlimited")
				// {
					// $radius_is_unlimited = true;
				// }
				// else
				// {
					// $submessage_radius = $submessage_radius."Radius for this unit on policy ".$policy["policy_number"]." is ".$uc["radius"].". ";
				// }
				
				// //GET TOTAL COST OF COVERAGE ADD TO TOTAL
				// $total_cost = $uc["al_um_bi_prem"]+$uc["al_uim_bi_prem"]+$uc["al_pip_prem"]+$uc["pd_comp_prem"]+$uc["pd_coll_prem"]+$uc["pd_rental_prem"]+$uc["al_prem"];
				// $total_cost_per_month = $total_cost_per_month+($total_cost/$ins_profile["term"]);
			// }
		// }
		
		// //CREATE MESSAGES FOR DUPLICATE INSURANCES
		// if($number_of_pd_coverages > 1)
		// {
			// //REPLACE NUMBER WITH WORDS
			// $search = array('2','3','4');
			// $replace = array('double','triple','quadruple');
			
			// $submessage_pd = str_replace($search,$replace,"This unit is ".$number_of_pd_coverages." insured for physical damage. ").$submessage_pd;
			// //$submessage_pd = "This unit is ".$number_of_pd_coverages." insured for physical damage. ";
		// }
		
		// if($number_of_al_coverages > 1)
		// {
			// //REPLACE NUMBER WITH WORDS
			// $search = array('2','3','4');
			// $replace = array('double','triple','quadruple');
			
			// $submessage_al = str_replace($search,$replace,"This unit is ".$number_of_al_coverages." insured for Auto Liability. ").$submessage_al;
		// }
		
		// if($number_of_cargo_coverages > 1)
		// {
			// //REPLACE NUMBER WITH WORDS
			// $search = array('2','3','4');
			// $replace = array('double','triple','quadruple');
			
			// $submessage_cargo = str_replace($search,$replace,"This unit is ".$number_of_cargo_coverages." insured for Cargo. ").$submessage_cargo;
		// }
		
		// //CREATE MESSAGES FOR COVERAGES MISSING ENTIRELY
		// if($cargo_is_covered == false)
		// {
			// $submessage_cargo = "No cargo coverage was found. ";
		// }
		
		// if($reefer_bd_is_covered == false)
		// {
			// $submessage_reefer = "No reefer breakdown coverage was found. ";
		// }
		
		// if($al_is_covered == false)
		// {
			// $submessage_al = "No Auto Liability coverage was found. ";
		// }
		
		// if($pd_is_covered == false)
		// {
			// $submessage_pd = "No Physical Damage coverage was found. ";
		// }
		
		// $status_message = $submessage_al.$submessage_pd.$submessage_radius.$submessage_cargo.$submessage_reefer.$submessage_rental;
		// //echo $cargo_is_covered;
		
		// //RETURN
		// $truck_ins_stats = null;
		// $truck_ins_stats["cargo_is_covered"] = $cargo_is_covered;
		// $truck_ins_stats["reefer_bd_is_covered"] = $reefer_bd_is_covered;
		// $truck_ins_stats["pd_is_covered"] = $pd_is_covered;
		// $truck_ins_stats["pd_ded_is_500"] = $pd_ded_is_500;
		// $truck_ins_stats["al_is_covered"] = $al_is_covered;
		// $truck_ins_stats["al_limit_covers_750k"] = $al_limit_covers_750k;
		// $truck_ins_stats["al_limit_covers_1m"] = $al_limit_covers_1m;
		// $truck_ins_stats["radius_is_unlimited"] = $radius_is_unlimited;
		// //$truck_ins_stats["rental_should_be_covered"] = $rental_should_be_covered;
		// $truck_ins_stats["rental_is_covered"] = $rental_is_covered;
		// $truck_ins_stats["al_is_double_insured"] = $al_is_double_insured;
		// $truck_ins_stats["pd_is_double_insured"] = $pd_is_double_insured;
		// $truck_ins_stats["cargo_is_double_insured"] = $cargo_is_double_insured;
		// $truck_ins_stats["total_cost_per_month"] = $total_cost_per_month;
		// $truck_ins_stats["status_message"] = $status_message;
		
		//print_r($truck_ins_stats);
		
//************ END OF WHAT GOES TO CUSTOMER HELPER *************************	
		
		$data['truck_ins_stats'] = $truck_ins_stats;
		$this->load->view('equipment/insurance/ins_status_td_for_truck_summary',$data);
		
	}
	
	//GET INSURANCE AUDIT ROW FOR TRUCK
	function get_insurance_audit_row()
	{
		date_default_timezone_set('America/Denver');
		
		$truck_id = $_POST["truck_id"];

		$snapshot_date = date("m/d/y H:i");
		
		$truck_ins_stats = get_truck_insurance_stats($truck_id,$snapshot_date);
		
		$data['truck_id'] = $truck_id;
		$data['truck_ins_stats'] = $truck_ins_stats;
		$this->load->view('equipment/insurance/ins_by_audit_row',$data);
		
	}
	
	
	//LOAD TRUCK DETAILS
	function load_truck_details($truck_id)
	{
		$where = null;
		$where["id"] = $truck_id;
		$truck = db_select_truck($where);
		
		//GET ALL ATTACHMENTS FOR THIS TRAILER
		$where = null;
		$where['type'] = "truck";
		$where['attached_to_id'] = $truck['id'];
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data['truck'] = $truck;
		$this->load->view('equipment/truck_details',$data);
		
	}//end load_truck_details
	
	//LOAD TRUCK EDIT
	function load_truck_edit($truck_id)
	{
		//GET TRUCK
		$where = null;
		$where["id"] = $truck_id;
		$truck = db_select_truck($where);
		
		//GET ALL VENDORS
		$where = null;
		$where["type"] = "Vendor";
		$vendors = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN LIST OF VENDORS
		$vendor_dropdown_options = array();
		$vendor_dropdown_options["Select"] = "Select";
		foreach($vendors as $vendor)
		{
			$vendor_dropdown_options[$vendor["id"]] = $vendor["company_side_bar_name"];
		}
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$client_dropdown_options = array();
		$client_dropdown_options["Select"] = "Select";
		foreach($dd_all_clients as $client)
		{
			$client_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		$client_dropdown_options[0] = "Unassigned";
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		$where["client_type"] = "Co-Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$codriver_dropdown_options = array();
		$codriver_dropdown_options["Select"] = "Select";
		foreach($dd_all_clients as $client)
		{
			$codriver_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		$codriver_dropdown_options[0] = "None";
		
		//GET ALL ACTIVE COMPANIES
		$where = null;
		$where["type"] = "Carrier";
		$where["company_status"] = "Active";
		$companies = db_select_companys($where);
		
		//CREATE DROPDOWN LIST OF ALL ACTIVE CLIENTS
		$company_dropdown_options = array();
		$company_dropdown_options["Select"] = "Select";
		foreach($companies as $company)
		{
			$company_dropdown_options[$company["id"]] = $company["company_side_bar_name"];
		}
		
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
		
		//GET TRAILERS
		$sql = "SELECT trailer.id as id,trailer.trailer_number as trailer_number FROM trailer
				LEFT JOIN truck ON trailer.id = truck.trailer_id
				WHERE trailer_status = 'On the road'
				AND truck_number IS NULL";
		$query = $this->db->query($sql);
		$trailers = array();
		foreach ($query->result() as $row)
		{
			$trailer['id'] = $row->id;
			$trailer['trailer_number'] = $row->trailer_number;
		
			$trailers[] = $trailer;
		}
		
		//GET PO TRAILER
		$where = null;
		$where["trailer_number"] = "PO";
		$po_trailer = db_select_trailer($where);
		
		$trailer_dropdown_options = array();
		$trailer_dropdown_options['Select'] = "Select";
		if(!empty($truck["trailer_id"]))
		{
			$trailer_dropdown_options[$truck['trailer_id']] = $truck['trailer']["trailer_number"];
		}
		foreach ($trailers as $trailer)
		{
			$title = $trailer['trailer_number'];
			$trailer_dropdown_options[$trailer['id']] = $title;
		}
		$trailer_dropdown_options['Bobtail'] = "Bobtail";
		$trailer_dropdown_options[$po_trailer["id"]] = $po_trailer["trailer_number"];
		
		
		//GET ALL TRUCKS
		$where = null;
		$where = "TRUE"; //SELECT ALL
		$trucks = db_select_trucks($where);
		
		$data['trucks'] = $trucks;
		$data['trailer_dropdown_options'] = $trailer_dropdown_options;
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$data['driver_manager_dropdown_options'] = $driver_manager_dropdown_options;
		$data['company_dropdown_options'] = $company_dropdown_options;
		$data['client_dropdown_options'] = $client_dropdown_options;
		$data['codriver_dropdown_options'] = $codriver_dropdown_options;
		$data['vendor_dropdown_options'] = $vendor_dropdown_options;
		$data['truck'] = $truck;
		$this->load->view('equipment/truck_edit',$data);
		//echo $truck["truck_number"];
		
	}//end load_truck_edit
	
	//GET TRUCK SERVICE NOTES
	function get_truck_service_notes($truck_id)
	{
		$where = null;
		$where["id"] = $truck_id;
		$truck = db_select_truck($where);
		
		$data['truck'] = $truck;
		$this->load->view('equipment/truck_service_notes_div',$data);
	}
	
	//ADD TRUCK SERVICE LOG NOTE
	function add_truck_service_note()
	{
		$truck_id = $_POST["truck_id"];
		
		$text = $_POST["new_note"];
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		date_default_timezone_set('America/Denver');
		$date_text = date("m/d/y");
		
		$full_note = $date_text." - ".$initials." | ".$text."\n\n";
		
		$where["id"] = $truck_id;
		$truck = db_select_truck($where);
		
		$update_truck["service_log_notes"] = $full_note.$truck["service_log_notes"];
		db_update_truck($update_truck,$where);
		
		$this->get_truck_service_notes($truck_id);
		
		//echo $update_load["settlement_notes"];
	}
	
	//SAVE TRUCK EDIT
	function save_truck()
	{
		$truck_id = $_POST['truck_id'];
	
		//UPDATE TRUCK
		$truck["status"] = $_POST["edit_truck_status"];
		$truck["dropdown_status"] = $_POST["edit_truck_dropdown_status"];
		$truck["fm_id"] = $_POST["edit_fm"];
		$truck["dm_id"] = $_POST["edit_dm"];
		$truck["client_id"] = $_POST["edit_client"];
		$truck["codriver_id"] = $_POST["edit_codriver"];
		$truck["trailer_id"] = $_POST["edit_trailer"];
		$truck["company_id"] = $_POST["edit_company"];
		$truck["vendor_id"] = $_POST["edit_leasing_company"];
		$truck["truck_number"] = $_POST["edit_truck_number"];
		$truck["make"] = $_POST["edit_make"];
		$truck["model"] = $_POST["edit_model"];
		$truck["year"] = $_POST["edit_year"];
		$truck["value"] = $_POST["edit_value"];
		$truck["vin"] = $_POST["edit_vin"];
		$truck["plate_number"] = $_POST["edit_plate_number"];
		$truck["insurance_policy"] = $_POST["edit_insurance_policy"];
		$truck["rental_rate"] = $_POST["edit_rental_rate"];
		$truck["rental_rate_period"] = $_POST["edit_rental_rate_period"];
		$truck["mileage_rate"] = $_POST["edit_mileage_rate"];
		//$truck["registration_link"] = $_POST["edit_registration_link"];
		//$truck["insurance_link"] = $_POST["edit_insurance_link"];
		//$truck["ifta_link"] = $_POST["edit_ifta_link"];
		//$truck["lease_agreement_link"] = $_POST["edit_lease_agreement_link"];
		$truck["next_wet_service"] = $_POST["edit_next_wet_service"];
		$truck["next_dry_service"] = $_POST["edit_next_dry_service"];
		$truck["truck_notes"] = $_POST["edit_truck_notes"];
		
		$where["id"] = $truck_id;
		db_update_truck($truck,$where);
		
		$this->load_truck_details($truck_id);
	}
	
	//ADD NEW TRUCK
	function add_truck()
	{
		
		//CREATE NEW TRUCK
		$truck["status"] = $_POST["truck_status"];
		$truck["client_id"] = $_POST["client"];
		$truck["company_id"] = $_POST["company"];
		$truck["vendor_id"] = $_POST["leasing_company"];
		$truck["truck_number"] = $_POST["truck_number"];
		$truck["make"] = $_POST["make"];
		$truck["model"] = $_POST["model"];
		$truck["year"] = $_POST["year"];
		$truck["value"] = $_POST["value"];
		$truck["vin"] = $_POST["vin"];
		$truck["plate_number"] = $_POST["plate_number"];
		$truck["insurance_policy"] = $_POST["insurance_policy"];
		$truck["rental_rate"] = $_POST["rental_rate"];
		$truck["rental_rate_period"] = $_POST["rental_rate_period"];
		$truck["mileage_rate"] = $_POST["mileage_rate"];
		$truck["registration_link"] = $_POST["registration_link"];
		$truck["insurance_link"] = $_POST["insurance_link"];
		$truck["ifta_link"] = $_POST["ifta_link"];
		$truck["lease_agreement_link"] = $_POST["lease_agreement_link"];
		$truck["next_wet_service"] = $_POST["next_wet_service"];
		$truck["next_dry_service"] = $_POST["next_dry_service"];
		$truck["truck_notes"] = $_POST["truck_notes"];
	
		db_insert_truck($truck);
		
		$where = null;
		$where["truck_number"] = $_POST["truck_number"];
		$new_truck = db_select_truck($where);
		
		$this->load_truck_details($new_truck["id"]);
		
	}
	
	
	//----------------------- TRAILERS ------------------------------- //
	
	
	//LOAD TRAILER LIST
	function load_trailer_list()
	{	
		//GET ALL TRAILERS
		$where = null;
		$where = "TRUE"; //SELECT ALL
		$trailers = db_select_trailers($where,"trailer_number");
		
		$data['equipment_type'] = "Trailers";
		$data['trailers'] = $trailers;
		$this->load->view('equipment/equipment_list_div',$data);
		
	}//end load_truck_list
	
	//LOAD TRUCK FILTER
	function load_trailer_filter()
	{
		//GET ALL VENDORS
		$where = null;
		$where["type"] = "Vendor";
		$vendors = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN LIST OF VENDORS
		$vendor_dropdown_options = array();
		$vendor_dropdown_options["All"] = "All Vendors";
		foreach($vendors as $vendor)
		{
			$vendor_dropdown_options[$vendor["id"]] = $vendor["company_side_bar_name"];
		}
		
		
		$data['vendor_dropdown_options'] = $vendor_dropdown_options;
		$this->load->view('equipment/trailer_filter_div',$data);
	}
	
	//LOAD TRAILER DETAILS
	function load_trailer_details($trailer_id)
	{
		//GET THIS TRAILER
		$where = null;
		$where["id"] = $trailer_id;
		$trailer = db_select_trailer($where);
		
		//GET ALL ATTACHMENTS FOR THIS TRAILER
		$where = null;
		$where['type'] = "trailer";
		$where['attached_to_id'] = $trailer['id'];
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data['trailer'] = $trailer;
		$this->load->view('equipment/trailer_details',$data);
		
	}//end load_trailer_details
	
	//LOAD TRAILER EDIT
	function load_trailer_edit($trailer_id)
	{
		//GET THIS TRAILER
		$where = null;
		$where["id"] = $trailer_id;
		$trailer = db_select_trailer($where);
		
		//GET ALL VENDORS
		$where = null;
		$where["type"] = "Vendor";
		$vendors = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN LIST OF VENDORS
		$vendor_dropdown_options = array();
		foreach($vendors as $vendor)
		{
			$vendor_dropdown_options[$vendor["id"]] = $vendor["company_side_bar_name"];
		}
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$client_dropdown_options = array();
		foreach($dd_all_clients as $client)
		{
			$client_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		$client_dropdown_options[0] = "Unassigned";
		
		// //GET ALL ACTIVE COMPANIES
		// $where = null;
		// $where["type"] = "Client";
		// $where["company_status"] = "Active";
		// $companies = db_select_companys($where);
		
		// //CREATE DROPDOWN LIST OF ALL ACTIVE CLIENTS
		// $company_dropdown_options = array();
		// $company_dropdown_options["Select"] = "Select";
		// foreach($companies as $company)
		// {
			// $company_dropdown_options[$company["id"]] = $company["company_side_bar_name"];
		// }
		
		// $data['company_dropdown_options'] = $company_dropdown_options;
		
		$data['client_dropdown_options'] = $client_dropdown_options;
		$data['vendor_dropdown_options'] = $vendor_dropdown_options;
		$data['trailer'] = $trailer;
		$this->load->view('equipment/trailer_edit',$data);
		
	}//end load_trailer_edit
	
	//LOAD TRAILER SUMMARY
	function load_trailer_summary()
	{
		$where = null;
		//$where = "TRUE"; //SELECT ALL
		if($_POST["vendor_dropdown_options"] != "All")
		{
			$where["vendor_id"] = $_POST["vendor_dropdown_options"];
		}
		if($_POST["trailer_status"] != "All Statuses")
		{
			$where["trailer_status"] = $_POST["trailer_status"];
		}
		if(empty($where))
		{
			$where = "TRUE"; //SELECT ALL
		}
		$trailers = db_select_trailers($where,"trailer_number");
		
		$data['equipment_type'] = "Trailers";
		$data['trailers'] = $trailers;
		$this->load->view('equipment/trailer_summary',$data);
		
	}//end load_truck_summary
	
	//VALIDATE NEW TRAILER NUMBER
	function validate_new_trailer_number()
	{
		$where = null;
		$where["trailer_number"] =  $_POST["trailer_number"];
		$trailer = db_select_trailer($where);
		
		if(!empty($trailer))
		{
			echo "Exists!<script>$('#trailer_number_is_valid').val('false');</script>";
		}
		else
		{
			echo "*<script>$('#trailer_number_is_valid').val('true');</script>";
		}
	}
	
	//VALIDATE NEW TRAILER NUMBER
	function validate_edit_trailer_number()
	{
		$where = null;
		$where["trailer_number"] =  $_POST["trailer_number"];
		$trailer = db_select_trailer($where);
		
		if(!empty($trailer))
		{
			echo "Exists!<script>$('#trailer_number_edit_is_valid').val('false');</script>";
		}
		else
		{
			echo "*<script>$('#trailer_number_edit_is_valid').val('true');</script>";
		}
	}
	
	//SAVE TRAILER EDIT
	function save_trailer()
	{
		$trailer_id = $_POST['trailer_id'];
		
		//UPDATE TRAILER
		$trailer["client_id"] = $_POST["edit_client"];
		$trailer["vendor_id"] = $_POST["edit_leasing_company"];
		$trailer["trailer_status"] = $_POST["trailer_status"];
		$trailer["dropdown_status"] = $_POST["dropdown_status"];
		$trailer["trailer_number"] = $_POST["edit_trailer_number"];
		$trailer["trailer_type"] = $_POST["trailer_type"];
		$trailer["length"] = $_POST["length"];
		$trailer["door_type"] = $_POST["door_type"];
		$trailer["tire_model"] = $_POST["tire_model"];
		$trailer["tire_make"] = $_POST["tire_make"];
		$trailer["tire_size"] = $_POST["tire_size"];
		$trailer["insulation_type"] = $_POST["insulation_type"];
		$trailer["vent_type"] = $_POST["vent_type"];
		$trailer["etracks"] = $_POST["etracks"];
		$trailer["suspension_type"] = $_POST["suspension_type"];
		$trailer["make"] = $_POST["make"];
		$trailer["model"] = $_POST["model"];
		$trailer["year"] = $_POST["year"];
		$trailer["vin"] = $_POST["vin"];
		$trailer["plate_number"] = $_POST["plate_number"];
		$trailer["plate_state"] = $_POST["plate_state"];
		$trailer["insurance_policy"] = $_POST["insurance_policy"];
		$trailer["value"] = $_POST["value"];
		$trailer["mileage_rate"] = $_POST["mileage_rate"];
		$trailer["rental_rate"] = $_POST["rental_rate"];
		$trailer["rental_period"] = $_POST["trailer_rental_period"];
		$trailer["last_inspection"] = $_POST["last_inspection"];
		$trailer["last_service"] = $_POST["last_service"];
		//$trailer["lease_agreement_link"] = $_POST["lease_agreement_link"];
		//$trailer["registration_link"] = $_POST["registration_link"];
		//$trailer["insurance_link"] = $_POST["insurance_link"];
		
		//CLEAN UP DROP DOWN INPUTS THAT SAY SELECT
		if($_POST["trailer_type"] == "Select")
		{
			$trailer["trailer_type"] = null;
		}
		if($_POST["door_type"] == "Select")
		{
			$trailer["door_type"] = null;
		}
		if($_POST["insulation_type"] == "Select")
		{
			$trailer["insulation_type"] = null;
		}
		if($_POST["vent_type"] == "Select")
		{
			$trailer["vent_type"] = null;
		}
		if($_POST["etracks"] == "Select")
		{
			$trailer["etracks"] = null;
		}
		if($_POST["suspension_type"] == "Select")
		{
			$trailer["suspension_type"] = null;
		}
		if($_POST["trailer_rental_period"] == "Select")
		{
			$trailer["rental_period"] = null;
		}
	
		$where["id"] = $trailer_id;
		db_update_trailer($trailer,$where);
		
		$this->load_trailer_details($trailer_id);
	}
	
	//ADD NEW TRAILER
	function add_trailer()
	{
		$trailer = null;
		$trailer["trailer_status"] = $_POST["trailer_status"];
		$trailer["vendor_id"] = $_POST["trailer_leasing_company"];
		$trailer["trailer_number"] = $_POST["trailer_number"];
		$trailer["rental_rate"] = $_POST["trailer_rental_rate"];
		$trailer["rental_period"] = $_POST["trailer_rental_period"];
		$trailer["mileage_rate"] = $_POST["trailer_mileage_rate"];
		
		db_insert_trailer($trailer);
		
		$where = null;
		$where["trailer_number"] = $_POST["trailer_number"];
		$new_trailer= db_select_trailer($where);
		
		$this->load_trailer_details($new_trailer["id"]);
		
		//echo "saved";
	}
	
	//UPLOAD EQUIPMENT ATTACHMENT
	function upload_equipment_attachment()
	{
		$equipment_type = $_POST['attachment_equipment_type'];
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
		$post_name = 'equipment_attachment_file';
		$file = $_FILES[$post_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		//$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$title = $_POST["attachment_name"];
		$category = ucfirst($equipment_type)." Attachment";//TRUCK ATTACHEMENT OR TRAILER ATTACHMENT
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$contract_secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		//CREATE ATTACHMENT IN DB
		$attachment = null;
		$attachment["type"] = $equipment_type;
		$attachment["attached_to_id"] = $_POST["equipment_id"];
		$attachment["file_guid"] = $contract_secure_file["file_guid"];
		$attachment["attachment_name"] = $_POST["attachment_name"];

		db_insert_attachment($attachment);
		
		// $file_access_permission = null;
		// $file_access_permission['file_guid'] = $contract_secure_file["file_guid"];
		// $file_access_permission['user_id'] = $this->session->userdata('user_id');
		// db_insert_file_access_permission($file_access_permission);
		
		if($equipment_type == 'truck')
		{
			if($_POST["upload_type"] != "Attachment")
			{
				$update = null;
				$update[$_POST["upload_type"]] = $contract_secure_file["file_guid"];
				
				$where = null;
				$where["id"] = $_POST["equipment_id"];
				db_update_truck($update,$where);
			}
		}
		else if($equipment_type == 'trailer')
		{
			if($_POST["upload_type"] != "Attachment")
			{
				$update = null;
				$update[$_POST["upload_type"]] = $contract_secure_file["file_guid"];
				
				$where = null;
				$where["id"] = $_POST["equipment_id"];
				db_update_trailer($update,$where);
			}
		}
		else if($equipment_type == 'policy')
		{
			if($_POST["upload_type"] != "Attachment")
			{
				$update = null;
				$update[$_POST["upload_type"]] = $contract_secure_file["file_guid"];
				
				$where = null;
				$where["id"] = $_POST["equipment_id"];
				db_update_ins_policy($update,$where);
			}
		}
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		load_upload_success_view();
	}
	
	
	
	//--------------- INSURANCE -----------------------------------//
	
	//LOAD NEW QUOTE DIALOG FORM
	function load_new_quote_dialog()
	{
		//GET LIST OF INSRANCE AGENT COMPANIES
		$where = null;
		$where["type"] = "Carrier";
		$carriers = db_select_companys($where,"company_name");
		
		//CREATE DROPDOWN LIST OF CARRIERS
		$insured_options = array();
		$insured_options["Select"] = "Select";
		foreach($carriers as $company)
		{
			$insured_options[$company["id"]] = $company["company_name"];
		}
		
		
		$data["insured_options"] = $insured_options;
		$this->load->view('equipment/new_quote_dialog',$data);
	}
	
	//LOAD INSURANCE FILTER
	function load_insurance_filter()
	{
		$data = null;
		//$data['vendor_dropdown_options'] = $vendor_dropdown_options;
		//$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$this->load->view('equipment/insurance_filter_div',$data);
	}
	
	//LOAD LIST OF UNIT COVERAGES
	function load_ins_by_unit_summary()
	{
		$group_by = $_POST["group_by_selection"];
		$snapshot_date = $_POST["ins_snapshot_date"];
		
		$snapshot_date_db_format = date("Y-m-d H:i:s",strtotime($snapshot_date));
		
		//GET ALL TRUCKS THAT ARE MARKED ON THE ROAD
		$where = null;
		$where["status"] = "On the road";
		$onroad_trucks = db_select_trucks($where,"truck_number");
		
		//GET ALL TRAILER THAT ARE MARKED ON THE ROAD
		$where = null;
		$where["trailer_status"] = "On the road";
		$onroad_trailers = db_select_trailers($where,"trailer_number");
		
		//GET ALL INSURED TRUCKS
		$CI =& get_instance();
		$sql = "SELECT DISTINCT(unit_id) FROM `ins_unit_coverage`
				WHERE coverage_current_since <= '".$snapshot_date_db_format."' 
				AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				AND unit_type = 'Truck'";
		$query = $CI->db->query($sql);
		$where = "";
		if($query->result())
		{
			foreach ($query->result() as $row)
			{
				$where = $where." truck.id = ".$row->unit_id." OR";
			}
			$where = substr($where,0,-3);
			$insured_trucks = db_select_trucks($where,"truck_number");
		}
		else
		{
			$insured_trucks = null;
		}
		
		//GET ALL INSURED TRAILERS
		$CI =& get_instance();
		$sql = "SELECT DISTINCT(unit_id) FROM `ins_unit_coverage`
				WHERE coverage_current_since <= '".$snapshot_date_db_format."' 
				AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				AND unit_type = 'Trailer'";
		$query = $CI->db->query($sql);
		$where = "";
		if($query->result())
		{
			foreach ($query->result() as $row)
			{
				$where = $where." trailer.id = ".$row->unit_id." OR";
			}
			$where = substr($where,0,-3);
			$insured_trailers = db_select_trailers($where,"trailer_number");
		}
		else
		{
			$insured_trailers = null;
		}
		
		//GET ALL UC's FROM BRAND NEW POLICIES
		$where = null;
				$where = "coverage_current_since <= '".$snapshot_date_db_format."' 
				AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				AND unit_type = 'Unknown'";
				$unknown_unit_coverages = db_select_ins_unit_coverages($where);
		
		
		$data = null;
		$data["snapshot_date_db_format"] = $snapshot_date_db_format;
		$data["onroad_trucks"] = $onroad_trucks;
		$data["onroad_trailers"] = $onroad_trailers;
		$data["insured_trucks"] = $insured_trucks;
		$data["insured_trailers"] = $insured_trailers;
		$data["unknown_unit_coverages"] = $unknown_unit_coverages;
		$this->load->view('equipment/ins_by_unit_summary',$data);
	}
	
	//LOAD LIST OF UNIT COVERAGES
	function load_ins_by_policy_summary()
	{
		$group_by = $_POST["group_by_selection"];
		$snapshot_date = $_POST["ins_snapshot_date"];
		
		$snapshot_date_db_format = date("Y-m-d H:i:s",strtotime($snapshot_date));
		
		
		//GET ALL ACTIVE POLICIES FOR GIVEN SNAPSHOT DATE
		$where = null;
		$where = 	"profile_current_since < '$snapshot_date_db_format' 
					AND (profile_current_till >= '$snapshot_date_db_format' OR profile_current_till IS NULL)";
		$active_profiles = db_select_ins_policy_profiles($where);
		
		$data = null;
		$data["snapshot_date_db_format"] = $snapshot_date_db_format;
		$data["active_profiles"] = $active_profiles;
		$this->load->view('equipment/insurance/ins_by_policy_summary',$data);
		
		//echo "hi";
	}
	
	//LOAD LIST OF UNIT COVERAGES
	function load_ins_by_audit_summary()
	{
		$group_by = $_POST["group_by_selection"];
		$snapshot_date = $_POST["ins_snapshot_date"];
		
		$snapshot_date_db_format = date("Y-m-d H:i:s",strtotime($snapshot_date));
		
		//GET ALL TRUCKS THAT ARE MARKED ON THE ROAD
		$where = null;
		$where["status"] = "On the road";
		$onroad_trucks = db_select_trucks($where,"truck_number");
		
		//GET ALL TRAILER THAT ARE MARKED ON THE ROAD
		$where = null;
		$where["trailer_status"] = "On the road";
		$onroad_trailers = db_select_trailers($where,"trailer_number");
		
		//GET ALL INSURED TRUCKS
		$CI =& get_instance();
		$sql = "SELECT DISTINCT(unit_id) FROM `ins_unit_coverage`
				WHERE coverage_current_since <= '".$snapshot_date_db_format."' 
				AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				AND unit_type = 'Truck'";
		$query = $CI->db->query($sql);
		$where = "";
		if($query->result())
		{
			foreach ($query->result() as $row)
			{
				$where = $where." truck.id = ".$row->unit_id." OR";
			}
			$where = substr($where,0,-3);
			$insured_trucks = db_select_trucks($where,"truck_number");
		}
		else
		{
			$insured_trucks = null;
		}
		
		//GET ALL INSURED TRAILERS
		$CI =& get_instance();
		$sql = "SELECT DISTINCT(unit_id) FROM `ins_unit_coverage`
				WHERE coverage_current_since <= '".$snapshot_date_db_format."' 
				AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				AND unit_type = 'Trailer'";
		$query = $CI->db->query($sql);
		$where = "";
		if($query->result())
		{
			foreach ($query->result() as $row)
			{
				$where = $where." trailer.id = ".$row->unit_id." OR";
			}
			$where = substr($where,0,-3);
			$insured_trailers = db_select_trailers($where,"trailer_number");
		}
		else
		{
			$insured_trailers = null;
		}
		
		//GET ALL UC's FROM BRAND NEW POLICIES
		$where = null;
				$where = "coverage_current_since <= '".$snapshot_date_db_format."' 
				AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				AND unit_type = 'Unknown'";
				$unknown_unit_coverages = db_select_ins_unit_coverages($where);
		
		
		$data = null;
		$data["snapshot_date_db_format"] = $snapshot_date_db_format;
		$data["onroad_trucks"] = $onroad_trucks;
		$data["onroad_trailers"] = $onroad_trailers;
		$data["insured_trucks"] = $insured_trucks;
		$data["insured_trailers"] = $insured_trailers;
		$data["unknown_unit_coverages"] = $unknown_unit_coverages;
		$this->load->view('equipment/insurance/ins_by_audit_summary',$data);
	}
	
	//CREATE NEW POLICY AFTER NEW QUOTE DIALOG SAVE PRESS
	function create_new_policy()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$quote_status = $_POST["quote_or_policy"];
		$policy_number = $_POST["qd_policy_number"];
		$quote_id = $_POST["quote_id"];
		$policy_current_since = $_POST["qd_policy_current_since"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//INSERT NEW POLICY
		$insert_policy = null;
		$insert_policy["quoted_date"] = date("Y-m-d H:i:s");
		$insert_policy["quoted_by_id"] = $user_id;
		if(!empty($policy_current_since))
		{
			$insert_policy["policy_active_date"] = date("Y-m-d H:i:s", strtotime($policy_current_since));
		}
		$insert_policy["quote_status"] = $quote_status;
		$insert_policy["quote_code"] = $quote_id;
		$insert_policy["policy_number"] = $policy_number;
		db_insert_ins_policy($insert_policy);
		
		//GET NEWLY INSERTED INS POLICY
		$where = null;
		$where["quote_code"] = $quote_id;
		$new_policy = db_select_ins_policy($where);
		
		//INSERT INS PROFILE FOR NEW POLICY
		$new_ins_profile = null;
		$new_ins_profile["ins_policy_id"] = $new_policy["id"];
		if(!empty($policy_current_since))
		{
			$new_ins_profile["profile_current_since"] = date("Y-m-d H:i:s", strtotime($policy_current_since));
		}
		else
		{
			$new_ins_profile["profile_current_since"] = date("Y-m-d H:i:s");
		}
		db_insert_ins_policy_profile($new_ins_profile);
		
		//INSERT UNIT COVERAGE
		$insert_unit_coverage = null;
		$insert_unit_coverage["ins_policy_id"] = $new_policy["id"];
		$insert_unit_coverage["coverage_current_since"] = $new_ins_profile["profile_current_since"];
		$insert_unit_coverage["unit_type"] = "Unknown";
		db_insert_ins_unit_coverage($insert_unit_coverage);
		
		//DETERMINE CHANGE REASON AND CHANGE DESC
		if($quote_status == 'Quote')
		{
			$change_reason = "New Quote";
			$change_desc = "New quote requested and entered into system";
		}
		else if($quote_status == 'Policy')
		{
			$change_reason = "New Policy";
			$change_desc = "New policy entered into system";
		}
		
		//CREATE INS CHANGE
		$ins_change = null;
		$ins_change["ins_policy_id"] = $new_policy["id"];
		$ins_change["change_date"] = $new_ins_profile["profile_current_since"];
		$ins_change["change_reason"] = $change_reason;
		$ins_change["change_desc"] = $change_desc." | ".date("m/d/y")." ".$user["person"]["f_name"];;
		$ins_change["user_id"] = $user_id;
		
		db_insert_ins_change($ins_change);
		
		$policy_id = $new_policy["id"];
		if(!empty($new_policy["policy_active_date"]))
		{
			$snapshot_date = $new_policy["policy_active_date"];
		}
		else
		{
			$snapshot_date = $new_policy["quoted_date"];
		}
		
		echo "<script>load_policy_details_view('$policy_id','$snapshot_date')</script>";
		
		//echo $quote_status;
	}
	
	//LOAD POLICY DETAILS VIEW
	function load_policy_details_view()
	{
		$policy_id = $_POST["policy_id"];
		$snapshot_date = $_POST["snapshot_date"];
		$snapshot_date_db_format = date("Y-m-d H:i:s",strtotime($snapshot_date));
		
		//GET INS POLICY
		$where = null;
		$where["id"] = $policy_id;
		$ins_policy = db_select_ins_policy($where);
		
		//GET POLICY PROFILE FOR SNAPSHOT DATE
		$where = null;
		$where = "profile_current_since	 <= '".$snapshot_date_db_format."' 
				AND (profile_current_till > '".$snapshot_date_db_format."' OR profile_current_till IS NULL)
				AND ins_policy_id = $policy_id";
		$ins_profile = db_select_ins_policy_profile($where);
		
		//GET CHANGE LOGS FOR POLICY
		$where = null;
		$where["ins_policy_id"] = $policy_id;
		$change_logs = db_select_ins_changes($where,"change_date DESC, id DESC");
		
		
		//GET UNIT COVERAGES FOR SNAPSHOT DATE
		$where = null;
		$where = "coverage_current_since <= '".$snapshot_date_db_format."' 
				AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				AND ins_policy_id = $policy_id";
		$unit_coverages = db_select_ins_unit_coverages($where,"unit_id");
		
		//GET ALL ATTACHMENTS FOR THIS TRAILER
		$where = null;
		$where['type'] = "policy";
		$where['attached_to_id'] = $policy_id;
		$attachments = db_select_attachments($where);
		
		//IF SNAPSHOT DATE IS AFTER CANCELLED DATE -- SHOW CANCELLED VIEW (IE: NO PROFILE, POLICY COVERAGE, LISTED DRIVERS, NEW UNIT)
		if(empty($ins_policy["policy_cancelled_date"]))
		{
			$show_cancelled_view = false;
		}
		else if(strtotime($snapshot_date_db_format) > strtotime($ins_policy["policy_cancelled_date"]))
		{
			$show_cancelled_view = true;
		}
		else
		{
			$show_cancelled_view = false;
		}
		
		$data['show_cancelled_view'] = $show_cancelled_view;
		$data['attachments'] = $attachments;
		$data['unit_coverages'] = $unit_coverages;
		$data['change_logs'] = $change_logs;
		$data['ins_profile'] = $ins_profile;
		$data['ins_policy'] = $ins_policy;
		$data['snapshot_date'] = date("m/d/y",strtotime($snapshot_date));
		$data['policy_id'] = $policy_id;
		$this->load->view('equipment/policy_details_view',$data);
	}
	
	//LOAD POLICY DETAILS BOX IN POLICY DETAILS VIEW -- IS THIS EVER USED??
	function load_policy_details_box()
	{
		$policy_id = $_POST["policy_id"];
		$snapshot_date = $_POST["snapshot_date"];
		$snapshot_date_db_format = date("Y-m-d H:i:s",strtotime($snapshot_date));
		
		//GET INS POLICY
		$where = null;
		$where["id"] = $policy_id;
		$ins_policy = db_select_ins_policy($where);
		
		$data['ins_policy'] = $ins_policy;
		$data['snapshot_date'] = date("m/d/y",strtotime($snapshot_date));
		$data['policy_id'] = $policy_id;
		$this->load->view('equipment/policy_details_box',$data);
	}
	
	//UPDATE INSURANCE POLICY
	function update_ins_policy()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_policy_id = $_POST["ins_policy_id"];
		$policy_number = $_POST["policy_number"];
		$policy_active_date = $_POST["policy_active_date"];
		//$policy_cancelled_date = $_POST["policy_cancelled_date"];
		$quote_status = $_POST["quote_status"];
		$current_since_date = $_POST["current_since_date"];
		$current_since_hour = $_POST["current_since_hour"];
		$current_since_minute = $_POST["current_since_minute"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET OLD POLICY
		$where = null;
		$where["id"] = $ins_policy_id;
		$old_policy = db_select_ins_policy($where);
		
		//UPDATE OLD POLICY
		$update_old_policy = null;
		
		$update_old_policy["policy_number"] = $policy_number;
		if(!empty($policy_active_date))
		{
			$update_old_policy["policy_active_date"] = date("Y-m-d H:i:s",strtotime($policy_active_date));
		}
		else
		{
			$update_old_policy["policy_active_date"] = null;
		}
		// if(!empty($policy_cancelled_date))
		// {
			// $update_old_policy["policy_cancelled_date"] = date("Y-m-d H:i:s",strtotime($policy_cancelled_date));
		// }
		// else
		// {
			// $update_old_policy["policy_cancelled_date"] = null;
		// }
		$update_old_policy["quote_status"] = $quote_status;
		
		$where = null;
		$where["id"] = $ins_policy_id;
		db_update_ins_policy($update_old_policy,$where);
		
		//GET NEWLY UPDATED POLICY
		$where = null;
		$where = $update_old_policy;
		$new_policy = db_select_ins_policy($where);
		
		//CREATE CHANGE LOG ENTRY
		$change_desc = "";
		foreach($new_policy as $key => $value)
		{
			if($key == 'id')
			{
				//SKIP
			}
			else
			{
				if($new_policy[$key] != $old_policy[$key])
				{
					$change_desc = $change_desc." ".$key." changed from ".$old_policy[$key]." to ".$new_policy[$key].".";
				}
			}
		}
		
		$db_keys = array(
			'policy_active_date',
			'policy_cancelled_date',
			'quote_status',
			'policy_number'
			);
		$key_conversion = array(
			'Policy Active Date',
			'Policy Cancelled Date',
			'Policy Status',
			'Policy Number'
			);
		
		$change_desc = substr(str_replace($db_keys,$key_conversion,$change_desc),1)." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
		
		//INSERT NEW INS_CHANGE
		$ins_change = null;
		$ins_change["ins_policy_id"] = $ins_policy_id;
		$ins_change["change_date"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
		$ins_change["obj_changed"] = "ins_policy";
		$ins_change["obj_id"] = $ins_policy_id;
		$ins_change["change_reason"] = "Po Edit";
		$ins_change["change_desc"] = $change_desc;
		$ins_change["user_id"] = $user_id;
		db_insert_ins_change($ins_change);
		
		//REFRESH DETAILS PAGE
		echo "<script>refresh_policy_details('$ins_policy_id');</script>";
	}
	
	//LOAD CANCEL DIALOG
	function load_cancel_dialog()
	{
		$data['policy_id'] = $_POST["policy_id"];
		$this->load->view('equipment/insurance/cancel_policy_dialog_div',$data);
	}
	
	//CANCEL POLICY
	function cancel_ins_policy()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_policy_id = $_POST["cancel_policy_id"];
		$cancel_date = $_POST["cancel_date"];
		$cancel_hour = $_POST["cancel_hour"];
		$cancel_minute = $_POST["cancel_minute"];
		$cancel_reason = $_POST["cancel_reason"];
		
		$cancel_date_db_format = date("Y-m-d H:i",strtotime($cancel_date." ".$cancel_hour.":".$cancel_minute));
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET OLD POLICY
		$where = null;
		$where["id"] = $ins_policy_id;
		$policy = db_select_ins_policy($where);
		
		//UPDATE POLICY WITH CANCELLED DATE
		$update_old_policy = null;
		$update_old_policy["policy_cancelled_date"] = $cancel_date_db_format;
		
		$where = null;
		$where["id"] = $ins_policy_id;
		db_update_ins_policy($update_old_policy,$where);
		
		
		//GET ACTIVE PROFILE AND MARK CANCELLED
		$where = null;
		$where["ins_policy_id"] = $ins_policy_id;
		$where["profile_current_till"] = null;
		$active_profile = db_select_ins_policy_profile($where);
		
		$update_profile = null;
		$update_profile["profile_current_till"] = $cancel_date_db_format;
		$update_profile["expected_cancellation_date"] = $cancel_date_db_format;
		
		$where = null;
		$where["id"] = $active_profile["id"];
		db_update_ins_policy_profile($update_profile,$where);
		
		
		//GET ALL ACTIVE UNIT COVERAGES AND MARK CANCELLED
		$where = null;
		$where["ins_policy_id"] = $ins_policy_id;
		$where["coverage_current_till"] = null;
		$active_ucs = db_select_ins_unit_coverages($where);
		
		//UPDATE ALL UNIT COVERAGES
		if(!empty($active_ucs))
		{
			foreach($active_ucs as $uc)
			{
				//UPDATE UC WITH NEW COVERAGE CURRENT TILL DATE
				$update_uc = null;
				$update_uc["coverage_current_till"] = $cancel_date_db_format;
				
				$where = null;
				$where["id"] = $uc["id"];
				db_update_ins_unit_coverage($update_uc,$where);
			}
		}
		
		//CREATE NEW CHANGE LOG ENTRIES
		$change_desc = "Insurance policy was cancelled for the following reason: ".$cancel_reason." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
		
		//INSERT NEW INS_CHANGE
		$ins_change = null;
		$ins_change["ins_policy_id"] = $ins_policy_id;
		$ins_change["change_date"] = $cancel_date_db_format;
		$ins_change["obj_changed"] = "ins_policy";
		$ins_change["obj_id"] = $ins_policy_id;
		$ins_change["change_reason"] = "Po Change";
		$ins_change["change_desc"] = $change_desc;
		$ins_change["user_id"] = $user_id;
		db_insert_ins_change($ins_change);
		
		
		//REFRESH DETAILS PAGE
		echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		
	}
	
	//SAVE INSURANCE PROFILE ON UPDATE
	function save_ins_profile()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_profile_id = $_POST["ins_profile_id"];
		$current_since_date = $_POST["current_since_date"];
		$current_since_hour = $_POST["current_since_hour"];
		$current_since_minute = $_POST["current_since_minute"];
		$agent_id = $_POST["agent_id"];
		$insured_id = $_POST["insured_id"];
		$cc_number = $_POST["cc_number"];
		$cc_address = $_POST["cc_address"];
		$cc_city = $_POST["cc_city"];
		$cc_state = $_POST["cc_state"];
		$cc_zip = $_POST["cc_zip"];
		$cc_exp = $_POST["cc_exp"];
		$cc_cvv = $_POST["cc_cvv"];
		$insurer_id = $_POST["insurer_id"];
		$fg_id = $_POST["fg_id"];
		$online_url = $_POST["online_url"];
		$online_username = $_POST["online_username"];
		$online_password = $_POST["online_password"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET OLD PROFILE
		$where = null;
		$where["id"] = $ins_profile_id;
		$old_profile = db_select_ins_policy_profile($where);
		
		if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) > strtotime($old_profile["profile_current_since"]))
		{
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE AND THEN CREATE NEW PROFILE
			
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE
			$update_old_profile = null;
			$update_old_profile["profile_current_till"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			
			$where = null;
			$where["id"] = $old_profile["id"];
			db_update_ins_policy_profile($update_old_profile,$where);
			
			$ins_policy_id = $old_profile["ins_policy_id"];
			
			//GET INSURED COMPANY
			$where = null;
			$where["id"] = $insured_id;
			$insured_company = db_select_company($where);
			
			//CREATE NEW PROFILE
			$new_profile = null;
			$new_profile["ins_policy_id"] = $ins_policy_id;
			$new_profile["profile_current_since"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			$new_profile["insurer_id"] = $insurer_id;
			$new_profile["agent_id"] = $agent_id;
			$new_profile["insured_company_id"] = $insured_company["id"];
			$new_profile["email"] = $insured_company["company_gmail"];
			$new_profile["phone"] = $insured_company["company_phone"];
			$new_profile["garaging_address"] = $insured_company["address"];
			$new_profile["garaging_city"] = $insured_company["city"];
			$new_profile["garaging_state"] = $insured_company["state"];
			$new_profile["garaging_zip"] = $insured_company["zip"];
			$new_profile["mailing_address"] = $insured_company["mailing_address"];
			$new_profile["mailing_city"] = $insured_company["mailing_city"];
			$new_profile["mailing_state"] = $insured_company["mailing_state"];
			$new_profile["mailing_zip"] = $insured_company["mailing_zip"];
			$new_profile["fg_id"] = $fg_id;
			$new_profile["cc_number"] = $cc_number;
			$new_profile["cc_exp"] = $cc_exp;
			$new_profile["cc_cvv"] = $cc_cvv;
			$new_profile["cc_address"] = $cc_address;
			$new_profile["cc_city"] = $cc_city;
			$new_profile["cc_state"] = $cc_state;
			$new_profile["cc_zip"] = $cc_zip;
			$new_profile["online_url"] = $online_url;
			$new_profile["online_username"] = $online_username;
			$new_profile["online_password"] = $online_password;
			
			$new_profile["term"] = $old_profile["term"];
			$new_profile["expected_cancellation_date"] = $old_profile["expected_cancellation_date"];
			$new_profile["cargo_limit"] = $old_profile["cargo_limit"];
			$new_profile["cargo_ded"] = $old_profile["cargo_ded"];
			$new_profile["cargo_prem"] = $old_profile["cargo_prem"];
			$new_profile["rbd_limit"] = $old_profile["rbd_limit"];
			$new_profile["rbd_ded"] = $old_profile["rbd_ded"];
			$new_profile["rbd_prem"] = $old_profile["rbd_prem"];
			$new_profile["fees"] = $old_profile["fees"];
			$new_profile["total_cost"] = $old_profile["total_cost"];
			
			db_insert_ins_policy_profile($new_profile);
			
			//GET NEWLY INSERTED PROFILE
			$where = null;
			$where["ins_policy_id"] = $new_profile["ins_policy_id"];
			$where["profile_current_since"] = $new_profile["profile_current_since"];
			$new_profile = db_select_ins_policy_profile($where);
			
			
			
			//CREATE CHANGE LOG ENTRY
			$change_desc = "";
			foreach($new_profile as $key => $value)
			{
				if($key == 'profile_current_since' || $key == 'profile_current_till' || $key == 'id')
				{
					//SKIP
				}
				else
				{
					if($new_profile[$key] != $old_profile[$key])
					{
						if($key == "insurer_id" || $key == "agent_id" || $key == "insured_company_id")
						{
							//GET OLD COMPANY
							$where = null;
							$where["id"] = $old_profile[$key];
							$old_company = db_select_company($where);
							
							//GET NEW COMPANY
							$where = null;
							$where["id"] = $new_profile[$key];
							$new_company = db_select_company($where);
							
							$change_desc = $change_desc." ".$key." changed from ".$old_company["company_name"]." to ".$new_company["company_name"].".";
						}
						else if($key == "fg_id")
						{
							//GET OLD CLIENT
							$where = null;
							$where["id"] = $old_profile[$key];
							$old_client = db_select_client($where);
							
							//GET NEW CLIENT
							$where = null;
							$where["id"] = $new_profile[$key];
							$new_client = db_select_client($where);
							
							$change_desc = $change_desc." ".$key." changed from ".$old_client["client_nickname"]." to ".$new_client["client_nickname"].".";
						}
						else
						{
							$change_desc = $change_desc." ".$key." changed from ".$old_profile[$key]." to ".$new_profile[$key].".";
						}
							
					}
				}
			}
			
			$db_keys = array(
				'insurer_id',
				'agent_id',
				'insured_company_id',
				'email',
				'phone',
				'garaging_address',
				'garaging_city',
				'garaging_state',
				'garaging_zip',
				'mailing_address',
				'mailing_city',
				'mailing_state',
				'mailing_zip',
				'fg_id',
				'cc_number',
				'cc_exp',
				'cc_cvv',
				'cc_address',
				'cc_city',
				'cc_state',
				'cc_zip',
				'online_url',
				'online_username',
				'online_password'
				);
			$key_conversion = array(
				'Insurer',
				'Agent',
				'Insured',
				'Email',
				'Phone',
				'Garaging Address',
				'Garaging City',
				'Garaging State',
				'Garaging Zip',
				'Mailing Address',
				'Mailing City',
				'Mailing State',
				'Mailing Zip',
				'Financial Guarantor',
				'CC Number',
				'CC Exp',
				'CC CVV',
				'CC Address',
				'CC City',
				'CC State',
				'CC Zip',
				'Online URL',
				'Online Username',
				'Online Password'
				);
			
			$change_desc = substr(str_replace($db_keys,$key_conversion,$change_desc),1)." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = $new_profile["profile_current_since"];
			$ins_change["obj_changed"] = "ins_policy_profile";
			$ins_change["obj_id"] = $new_profile["id"];
			$ins_change["change_reason"] = "Pr Change";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//----------------CREATE ALL THE OLD LISTED DRIVERS FOR THE NEW PROFILE - COPY OF OLD ONES
			
			//GET ALL LISTED DRIVERS FROM OLD PROFILE
			$where = null;
			$where["ins_profile_id"] = $old_profile["id"];
			$old_listed_drivers = db_select_ins_listed_drivers($where);
			
			if(!empty($old_listed_drivers))
			{
				foreach($old_listed_drivers as $listed_driver)
				{
					//echo $listed_driver["client"]["client_nickname"];
					//MAKE A COPY OF THE ADDTIONAL INSURED
					$new_listed_driver = null;
					$new_listed_driver["ins_profile_id"] = $new_profile["id"];
						
					foreach($listed_driver as $key => $value)
					{
						if($key == 'id' || $key == 'ins_profile_id' || $key == 'client')
						{
							//SKIP
						}
						else
						{
							$new_listed_driver[$key] = $value;
						}
					}
					db_insert_ins_listed_driver($new_listed_driver);
					//print_r($new_listed_driver);
				}
			}
			
			
			//----------------CREATE ALL THE NEW ADDITIONAL INSURED FOR THE NEW PROFILE - COPY OF OLD ONES
			
			//GET ALL ADDITIONAL INSURED
			$where = null;
			$where["ins_profile_id"] = $old_profile["id"];
			$where["role"] = "Additional Insured";
			$additional_insured_players = db_select_ins_players($where);
			
			if(!empty($additional_insured_players))
			{
				foreach($additional_insured_players as $player)
				{
					//MAKE A COPY OF THE ADDTIONAL INSURED
					$new_player = null;
					$new_player["ins_profile_id"] = $new_profile["id"];
					foreach($player as $key => $value)
					{
						if($key == 'id' || $key == 'ins_profile_id')
						{
							//SKIP
						}
						else
						{
							$new_player[$key] = $value;
						}
					}
					db_insert_ins_player($new_player);
				}
			}
			
			
			
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		else if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) == strtotime($old_profile["profile_current_since"]))
		{
			//UPDATE CURRENT PROFILE -- DONT CREATE A NEW ONE
			
			//GET INSURED COMPANY
			$where = null;
			$where["id"] = $insured_id;
			$insured_company = db_select_company($where);
			
			$ins_policy_id = $old_profile["ins_policy_id"];
			
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE
			$update_old_profile = null;
			
			$update_old_profile["ins_policy_id"] = $ins_policy_id;
			$update_old_profile["insurer_id"] = $insurer_id;
			$update_old_profile["agent_id"] = $agent_id;
			$update_old_profile["insured_company_id"] = $insured_company["id"];
			$update_old_profile["email"] = $insured_company["company_gmail"];
			$update_old_profile["phone"] = $insured_company["company_phone"];
			$update_old_profile["garaging_address"] = $insured_company["address"];
			$update_old_profile["garaging_city"] = $insured_company["city"];
			$update_old_profile["garaging_state"] = $insured_company["state"];
			$update_old_profile["garaging_zip"] = $insured_company["zip"];
			$update_old_profile["mailing_address"] = $insured_company["mailing_address"];
			$update_old_profile["mailing_city"] = $insured_company["mailing_city"];
			$update_old_profile["mailing_state"] = $insured_company["mailing_state"];
			$update_old_profile["mailing_zip"] = $insured_company["mailing_zip"];
			$update_old_profile["fg_id"] = $fg_id;
			$update_old_profile["cc_number"] = $cc_number;
			$update_old_profile["cc_exp"] = $cc_exp;
			$update_old_profile["cc_cvv"] = $cc_cvv;
			$update_old_profile["cc_address"] = $cc_address;
			$update_old_profile["cc_city"] = $cc_city;
			$update_old_profile["cc_state"] = $cc_state;
			$update_old_profile["cc_zip"] = $cc_zip;
			$update_old_profile["online_url"] = $online_url;
			$update_old_profile["online_username"] = $online_username;
			$update_old_profile["online_password"] = $online_password;
			
			$where = null;
			$where["id"] = $old_profile["id"];
			db_update_ins_policy_profile($update_old_profile,$where);
			
			//GET NEWLY UPDATED PROFILE
			$where = null;
			$where = $update_old_profile;
			$new_profile = db_select_ins_policy_profile($where);
			
			//CREATE CHANGE LOG ENTRY
			$change_desc = "";
			foreach($new_profile as $key => $value)
			{
				if($key == 'profile_current_since' || $key == 'profile_current_till' || $key == 'id')
				{
					//SKIP
				}
				else
				{
					if($new_profile[$key] != $old_profile[$key])
					{
						if($key == "insurer_id" || $key == "agent_id" || $key == "insured_company_id")
						{
							//GET OLD COMPANY
							$where = null;
							$where["id"] = $old_profile[$key];
							$old_company = db_select_company($where);
							
							//GET NEW COMPANY
							$where = null;
							$where["id"] = $new_profile[$key];
							$new_company = db_select_company($where);
							
							$change_desc = $change_desc." ".$key." changed from ".$old_company["company_name"]." to ".$new_company["company_name"].".";
						}
						else if($key == "fg_id")
						{
							//GET OLD CLIENT
							$where = null;
							$where["id"] = $old_profile[$key];
							$old_client = db_select_client($where);
							
							//GET NEW CLIENT
							$where = null;
							$where["id"] = $new_profile[$key];
							$new_client = db_select_client($where);
							
							$change_desc = $change_desc." ".$key." changed from ".$old_client["client_nickname"]." to ".$new_client["client_nickname"].".";
						}
						else
						{
							$change_desc = $change_desc." ".$key." changed from ".$old_profile[$key]." to ".$new_profile[$key].".";
						}
							
					}
				}
			}
			
			$db_keys = array(
				'insurer_id',
				'agent_id',
				'insured_company_id',
				'email',
				'phone',
				'garaging_address',
				'garaging_city',
				'garaging_state',
				'garaging_zip',
				'mailing_address',
				'mailing_city',
				'mailing_state',
				'mailing_zip',
				'fg_id',
				'cc_number',
				'cc_exp',
				'cc_cvv',
				'cc_address',
				'cc_city',
				'cc_state',
				'cc_zip',
				'online_url',
				'online_username',
				'online_password'
				);
			$key_conversion = array(
				'Insurer',
				'Agent',
				'Insured',
				'Email',
				'Phone',
				'Garaging Address',
				'Garaging City',
				'Garaging State',
				'Garaging Zip',
				'Mailing Address',
				'Mailing City',
				'Mailing State',
				'Mailing Zip',
				'Financial Guarantor',
				'CC Number',
				'CC Exp',
				'CC CVV',
				'CC Address',
				'CC City',
				'CC State',
				'CC Zip',
				'Online URL',
				'Online Username',
				'Online Password'
				);
			
			$change_desc = substr(str_replace($db_keys,$key_conversion,$change_desc),1)." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = $new_profile["profile_current_since"];
			$ins_change["obj_changed"] = "ins_policy_profile";
			$ins_change["obj_id"] = $new_profile["id"];
			$ins_change["change_reason"] = "Pr Edit";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		else
		{
			//CODE HERE TO HANDLE IF DATE IS NOT AFTER PREVIOUS CURRENT SINCE DATE
			echo "<script>alert('Change Date cannot be before current active date!');</script>";
		}
		
		
		//echo $change_desc;
	}
	
	//SAVE THE COVERAGE SECTIONS OF INSURANCE PROFILE -- POLICY COVERAGE
	function save_ins_policy_coverage()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_profile_id = $_POST["ins_profile_id"];
		$current_since_date = $_POST["current_since_date"];
		$current_since_hour = $_POST["current_since_hour"];
		$current_since_minute = $_POST["current_since_minute"];
		$term = $_POST["term"];
		$expected_cancellation_date = $_POST["expected_cancellation_date"];
		$cargo_limit = $_POST["cargo_limit"];
		$cargo_ded = $_POST["cargo_ded"];
		$cargo_prem = $_POST["cargo_prem"];
		$rbd_limit = $_POST["rbd_limit"];
		$rbd_ded = $_POST["rbd_ded"];
		$rbd_prem = $_POST["rbd_prem"];
		$fees = $_POST["fees"];
		$total_cost = $_POST["total_cost"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET OLD PROFILE
		$where = null;
		$where["id"] = $ins_profile_id;
		$old_profile = db_select_ins_policy_profile($where);
		
		//IF NEW CURRENT DATE IS LATER THAN OLD CURRENT DATE -- CLOSE OUT OLD PROFILE AND CREATE NEW PROFILE
		if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) > strtotime($old_profile["profile_current_since"]))
		{
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE AND THEN CREATE NEW PROFILE
			
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE
			$update_old_profile = null;
			$update_old_profile["profile_current_till"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			
			$where = null;
			$where["id"] = $old_profile["id"];
			db_update_ins_policy_profile($update_old_profile,$where);
			
			$ins_policy_id = $old_profile["ins_policy_id"];
			
			//CREATE NEW PROFILE
			$new_profile = null;
			$new_profile["ins_policy_id"] = $ins_policy_id;
			$new_profile["profile_current_since"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			
			$new_profile["insurer_id"] = $old_profile["insurer_id"];
			$new_profile["agent_id"] = $old_profile["agent_id"];
			$new_profile["insured_company_id"] = $old_profile["insured_company_id"];
			$new_profile["email"] = $old_profile["email"];
			$new_profile["phone"] = $old_profile["phone"];
			$new_profile["garaging_address"] = $old_profile["garaging_address"];
			$new_profile["garaging_city"] = $old_profile["garaging_city"];
			$new_profile["garaging_state"] = $old_profile["garaging_state"];
			$new_profile["garaging_zip"] = $old_profile["garaging_zip"];
			$new_profile["mailing_address"] = $old_profile["mailing_address"];
			$new_profile["mailing_city"] = $old_profile["mailing_city"];
			$new_profile["mailing_state"] = $old_profile["mailing_state"];
			$new_profile["mailing_zip"] = $old_profile["mailing_zip"];
			$new_profile["fg_id"] =  $old_profile["fg_id"];
			$new_profile["cc_number"] =  $old_profile["cc_number"];
			$new_profile["cc_exp"] =  $old_profile["cc_exp"];
			$new_profile["cc_cvv"] =  $old_profile["cc_cvv"];
			$new_profile["cc_address"] =  $old_profile["cc_address"];
			$new_profile["cc_city"] =  $old_profile["cc_city"];
			$new_profile["cc_state"] =  $old_profile["cc_state"];
			$new_profile["cc_zip"] =  $old_profile["cc_zip"];
			$new_profile["online_url"] =  $old_profile["online_url"];
			$new_profile["online_username"] =  $old_profile["online_username"];
			$new_profile["online_password"] =  $old_profile["online_password"];
			
			$new_profile["term"] = $term;
			if(!empty($expected_cancellation_date))
			{
				$new_profile["expected_cancellation_date"] = date("Y-m-d H:i:s",strtotime($expected_cancellation_date));
			}
			else
			{
				$new_profile["expected_cancellation_date"] = null;
			}
			$new_profile["cargo_limit"] = $cargo_limit;
			$new_profile["cargo_ded"] = $cargo_ded;
			$new_profile["cargo_prem"] = $cargo_prem;
			$new_profile["rbd_limit"] = $rbd_limit;
			$new_profile["rbd_ded"] = $rbd_ded;
			$new_profile["rbd_prem"] = $rbd_prem;
			$new_profile["fees"] = $fees;
			$new_profile["total_cost"] = $total_cost;
			
			db_insert_ins_policy_profile($new_profile);
			
			echo $new_profile["profile_current_since"];
			
			//GET NEWLY INSERTED PROFILE
			$where = null;
			$where["ins_policy_id"] = $new_profile["ins_policy_id"];
			$where["profile_current_since"] = date("Y-m-d H:i:s",strtotime($new_profile["profile_current_since"]));
			$new_profile = db_select_ins_policy_profile($where);
			
			//CREATE CHANGE LOG ENTRY
			$change_desc = "";
			foreach($new_profile as $key => $value)
			{
				if($key == 'profile_current_since' || $key == 'profile_current_till' || $key == 'id')
				{
					//SKIP
				}
				else
				{
					if($new_profile[$key] != $old_profile[$key])
					{
						$change_desc = $change_desc." ".$key." changed from ".$old_profile[$key]." to ".$new_profile[$key].".";
							
					}
				}
			}
			
			$db_keys = array(
				'term',
				'expected_cancellation_date',
				'cargo_limit',
				'cargo_ded',
				'cargo_prem',
				'rbd_limit',
				'rbd_ded',
				'rbd_prem',
				'fees',
				'total_cost'
				);
			$key_conversion = array(
				'Term',
				'Expected Cancellation Date',
				'Cargo Limit',
				'Cargo Deductible',
				'Cargo Premium',
				'Reefer Breakdown Limit',
				'Reefer Breakdown Deductible',
				'Reefer Breakdown Premium',
				'Fees',
				'Total Cost'
				);
			
			$change_desc = substr(str_replace($db_keys,$key_conversion,$change_desc),1)." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = $new_profile["profile_current_since"];
			$ins_change["obj_changed"] = "ins_policy_profile";
			$ins_change["obj_id"] = $new_profile["id"];
			$ins_change["change_reason"] = "PC Change";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//----------------CREATE ALL THE OLD LISTED DRIVERS FOR THE NEW PROFILE - COPY OF OLD ONES
			
			//GET ALL LISTED DRIVERS FROM OLD PROFILE
			$where = null;
			$where["ins_profile_id"] = $old_profile["id"];
			$old_listed_drivers = db_select_ins_listed_drivers($where);
			
			if(!empty($old_listed_drivers))
			{
				foreach($old_listed_drivers as $listed_driver)
				{
					echo $listed_driver["client"]["client_nickname"];
					//MAKE A COPY OF THE ADDTIONAL INSURED
					$new_listed_driver = null;
					$new_listed_driver["ins_profile_id"] = $new_profile["id"];
						
					foreach($listed_driver as $key => $value)
					{
						if($key == 'id' || $key == 'ins_profile_id' || $key == 'client')
						{
							//SKIP
						}
						else
						{
							$new_listed_driver[$key] = $value;
						}
					}
					db_insert_ins_listed_driver($new_listed_driver);
					//print_r($new_listed_driver);
				}
			}
			
			
			//----------------CREATE ALL THE NEW ADDITIONAL INSURED FOR THE NEW PROFILE - COPY OF OLD ONES
			
			//GET ALL ADDITIONAL INSURED
			$where = null;
			$where["ins_profile_id"] = $old_profile["id"];
			$where["role"] = "Additional Insured";
			$additional_insured_players = db_select_ins_players($where);
			
			if(!empty($additional_insured_players))
			{
				foreach($additional_insured_players as $player)
				{
					//MAKE A COPY OF THE ADDTIONAL INSURED
					$new_player = null;
					$new_player["ins_profile_id"] = $new_profile["id"];
					foreach($player as $key => $value)
					{
						if($key == 'id' || $key == 'ins_profile_id')
						{
							//SKIP
						}
						else
						{
							$new_player[$key] = $value;
						}
					}
					db_insert_ins_player($new_player);
				}
			}
			
			
			
			
			
			
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		//IF THE DATES ARE THE EXACT SAME -- UPDATE THE CURRENT PROFILE -- DON'T CREATE A NEW ONE
		else if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) == strtotime($old_profile["profile_current_since"]))
		{
			//UPDATE CURRENT PROFILE -- DONT CREATE A NEW ONE
			
			$ins_policy_id = $old_profile["ins_policy_id"];
			
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE
			$update_old_profile = null;
			
			$update_old_profile["term"] = $term;
			$update_old_profile["expected_cancellation_date"] = date("Y-m-d H:i:s",strtotime($expected_cancellation_date));
			$update_old_profile["cargo_limit"] = $cargo_limit;
			$update_old_profile["cargo_ded"] = $cargo_ded;
			$update_old_profile["cargo_prem"] = $cargo_prem;
			$update_old_profile["rbd_limit"] = $rbd_limit;
			$update_old_profile["rbd_ded"] = $rbd_ded;
			$update_old_profile["rbd_prem"] = $rbd_prem;
			$update_old_profile["fees"] = $fees;
			$update_old_profile["total_cost"] = $total_cost;
			
			$where = null;
			$where["id"] = $old_profile["id"];
			db_update_ins_policy_profile($update_old_profile,$where);
			
			//GET NEWLY UPDATED PROFILE
			$where = null;
			$where = $update_old_profile;
			$new_profile = db_select_ins_policy_profile($where);
			
			//CREATE CHANGE LOG ENTRY
			$change_desc = "";
			foreach($new_profile as $key => $value)
			{
				if($key == 'profile_current_since' || $key == 'profile_current_till' || $key == 'id')
				{
					//SKIP
				}
				else
				{
					if($new_profile[$key] != $old_profile[$key])
					{
						if($key == "insurer_id" || $key == "agent_id" || $key == "insured_company_id")
						{
							//GET OLD COMPANY
							$where = null;
							$where["id"] = $old_profile[$key];
							$old_company = db_select_company($where);
							
							//GET NEW COMPANY
							$where = null;
							$where["id"] = $new_profile[$key];
							$new_company = db_select_company($where);
							
							$change_desc = $change_desc." ".$key." changed from ".$old_company["company_name"]." to ".$new_company["company_name"].".";
						}
						else if($key == "fg_id")
						{
							//GET OLD CLIENT
							$where = null;
							$where["id"] = $old_profile[$key];
							$old_client = db_select_client($where);
							
							//GET NEW CLIENT
							$where = null;
							$where["id"] = $new_profile[$key];
							$new_client = db_select_client($where);
							
							$change_desc = $change_desc." ".$key." changed from ".$old_client["client_nickname"]." to ".$new_client["client_nickname"].".";
						}
						else
						{
							$change_desc = $change_desc." ".$key." changed from ".$old_profile[$key]." to ".$new_profile[$key].".";
						}
							
					}
				}
			}
			
			$db_keys = array(
				'term',
				'expected_cancellation_date',
				'cargo_limit',
				'cargo_ded',
				'cargo_prem',
				'rbd_limit',
				'rbd_ded',
				'rbd_prem',
				'fees',
				'total_cost'
				);
			$key_conversion = array(
				'Term',
				'Expected Cancellation Date',
				'Cargo Limit',
				'Cargo Deductible',
				'Cargo Premium',
				'Reefer Breakdown Limit',
				'Reefer Breakdown Deductible',
				'Reefer Breakdown Premium',
				'Fees',
				'Total Cost'
				);
			
			$change_desc = substr(str_replace($db_keys,$key_conversion,$change_desc),1)." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = $new_profile["profile_current_since"];
			$ins_change["obj_changed"] = "ins_policy_profile";
			$ins_change["obj_id"] = $new_profile["id"];
			$ins_change["change_reason"] = "PC Edit";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		else
		{
			//CODE HERE TO HANDLE IF DATE IS NOT AFTER PREVIOUS CURRENT SINCE DATE
			echo "<script>alert('Change Date cannot be before current active date!');</script>";
		}
		
		
		//echo $change_desc;
	}
	
	//SAVE UNIT COVERAGE 
	function save_ins_unit_coverage()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_unit_coverage_id = $_POST["ins_unit_coverage_id"];
		$current_since_date = $_POST["current_since_date"];
		$current_since_hour = $_POST["current_since_hour"];
		$current_since_minute = $_POST["current_since_minute"];
		$truck_id = $_POST["truck_id"];
		$trailer_id = $_POST["trailer_id"];
		$unit_type = $_POST["unit_type"];
		$pd_limit = $_POST["pd_limit"];
		$radius = $_POST["radius"];
		$al_um_bi_limit = $_POST["al_um_bi_limit"];
		$al_uim_bi_limit = $_POST["al_uim_bi_limit"];
		$al_pip_limit = $_POST["al_pip_limit"];
		$al_prem = $_POST["al_prem"];
		$al_um_bi_prem = $_POST["al_um_bi_prem"];
		$al_uim_bi_prem = $_POST["al_uim_bi_prem"];
		$al_pip_prem = $_POST["al_pip_prem"];
		$pd_comp_ded = $_POST["pd_comp_ded"];
		$pd_comp_prem = $_POST["pd_comp_prem"];
		$pd_coll_ded = $_POST["pd_coll_ded"];
		$pd_coll_prem = $_POST["pd_coll_prem"];
		$pd_rental_daily_limit = $_POST["pd_rental_daily_limit"];
		$pd_rental_max_limit = $_POST["pd_rental_max_limit"];
		$pd_rental_prem = $_POST["pd_rental_prem"];
		
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET NEW UNIT NUMBER
		$unit_id = null;
		if($unit_type == "Truck")
		{
			$unit_id = $truck_id;
			
			//GET TRUCK
			$where = null;
			$where["id"] = $truck_id;
			$truck = db_select_truck($where);
			
			$new_unit_number = $truck["truck_number"];
		}
		else if($unit_type == "Trailer")
		{
			$unit_id = $trailer_id;
			
			//GET TRAILER
			$where = null;
			$where["id"] = $trailer_id;
			$trailer = db_select_trailer($where);
			
			$new_unit_number = $trailer["trailer_number"];
		}
		
		//GET OLD UNIT COVERAGE
		$where = null;
		$where["id"] = $ins_unit_coverage_id;
		$old_uc = db_select_ins_unit_coverage($where);
		
		//GET OLD UNIT NUMBER
		if($old_uc["unit_type"] == "Truck")
		{
			//GET TRUCK
			$where = null;
			$where["id"] = $old_uc["unit_id"];
			$truck = db_select_truck($where);
			
			$old_unit_number = $truck["truck_number"];
		}
		else if($old_uc["unit_type"] == "Trailer")
		{
			//GET TRAILER
			$where = null;
			$where["id"] = $old_uc["unit_id"];
			$trailer = db_select_trailer($where);
			
			$old_unit_number = $trailer["trailer_number"];
		}
		else if($old_uc["unit_type"] == "Unknown")
		{
			$old_unit_number = "Unknown";
		}

		//IF NEW CURRENT DATE IS LATER THAN OLD CURRENT DATE -- CLOSE OUT OLD PROFILE AND CREATE NEW PROFILE
		if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) > strtotime($old_uc["coverage_current_since"]))
		{
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE AND THEN CREATE NEW PROFILE
			
			//UPDATE OLD UNIT COVERAGE WITH NEW COVERAGE CURRENT TILL DATE
			$update_old_uc = null;
			$update_old_uc["coverage_current_till"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			
			$where = null;
			$where["id"] = $old_uc["id"];
			db_update_ins_unit_coverage($update_old_uc,$where);
			
			$ins_policy_id = $old_uc["ins_policy_id"];
			
			
			//CREATE NEW PROFILE
			$new_uc = null;
			$new_uc["ins_policy_id"] = $ins_policy_id;
			$new_uc["coverage_current_since"] = date("Y-m-d H:i:s",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			$new_uc["unit_type"] = $unit_type;
			$new_uc["unit_id"] = $unit_id;
			$new_uc["pd_limit"] = $pd_limit;
			$new_uc["radius"] = $radius;
			$new_uc["al_um_bi_limit"] = $al_um_bi_limit;
			$new_uc["al_uim_bi_limit"] = $al_uim_bi_limit;
			$new_uc["al_pip_limit"] = $al_pip_limit;
			$new_uc["al_prem"] = $al_prem;
			$new_uc["al_um_bi_prem"] = $al_um_bi_prem;
			$new_uc["al_uim_bi_prem"] = $al_uim_bi_prem;
			$new_uc["al_pip_prem"] = $al_pip_prem;
			$new_uc["pd_comp_ded"] = $pd_comp_ded;
			$new_uc["pd_comp_prem"] = $pd_comp_prem;
			$new_uc["pd_coll_ded"] = $pd_coll_ded;
			$new_uc["pd_coll_prem"] = $pd_coll_prem;
			$new_uc["pd_rental_daily_limit"] = $pd_rental_daily_limit;
			$new_uc["pd_rental_max_limit"] = $pd_rental_max_limit;
			$new_uc["pd_rental_prem"] = $pd_rental_prem;
			
			db_insert_ins_unit_coverage($new_uc);
			
			//GET NEWLY INSERTED PROFILE
			$where = null;
			$where["ins_policy_id"] = $ins_policy_id;
			$where["coverage_current_since"] = date("Y-m-d H:i:s",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			$new_uc = db_select_ins_unit_coverage($where);
			
			//CREATE CHANGE LOG ENTRY
			$change_desc = "";
			foreach($new_uc as $key => $value)
			{
				if($key == 'coverage_current_since' || $key == 'coverage_current_till' || $key == 'id')
				{
					//SKIP
				}
				else
				{
					if($new_uc[$key] != $old_uc[$key])
					{
						if($key == "unit_id")
						{
							$change_desc = $change_desc." ".$key." changed from ".$old_unit_number." to ".$new_unit_number.".";
						}
						else
						{
							$change_desc = $change_desc." ".$key." changed from ".$old_uc[$key]." to ".$new_uc[$key].".";
						}
							
					}
				}
			}
			
			$db_keys = array(
				'unit_type',
				'unit_id',
				'pd_limit',
				'radius',
				'al_um_bi_limit',
				'al_uim_bi_limit',
				'al_pip_limit',
				'al_prem',
				'al_um_bi_prem',
				'al_uim_bi_prem',
				'al_pip_prem',
				'pd_comp_ded',
				'pd_comp_prem',
				'pd_coll_ded',
				'pd_coll_prem',
				'pd_rental_daily_limit',
				'pd_rental_max_limit',
				'pd_rental_prem'
				);
			$key_conversion = array(
				'Unit Type',
				'Unit Number',
				'Unit Value',
				'Radius',
				'UM BI Limit',
				'UIM BI Limit',
				'PIP Limit',
				'Liability Premium',
				'UM BI Premium',
				'UIM BI Premium',
				'PIP Premium',
				'Phys Dam Comp Deductible',
				'Phys Dam Comp Premium',
				'Phys Dam Collision Deductible',
				'Phys Dam Collision Premium',
				'Rental Reimbursement Daily Limit',
				'Rental Reimbursement Max Limit',
				'Rental Reimbursement Premium'
				);
			
			$change_desc = substr(str_replace($db_keys,$key_conversion,$change_desc),1)." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = date("Y-m-d H:i:s",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			$ins_change["obj_changed"] = "ins_unit_coverage";
			$ins_change["obj_id"] = $new_uc["id"];
			$ins_change["change_reason"] = "UC Change";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//----------------CREATE ALL THE NEW ADDITIONAL INSURED FOR THE NEW PROFILE - COPY OF OLD ONES
			
			//GET ALL ADDITIONAL INSURED
			$where = null;
			$where["ins_unit_coverage_id"] = $old_uc["id"];
			$where["role"] = "Loss Payee";
			$loss_payee_players = db_select_ins_players($where);
			
			if(!empty($loss_payee_players))
			{
				foreach($loss_payee_players as $player)
				{
					//MAKE A COPY OF THE LOSS PAYEE
					$new_player = null;
					$new_player["ins_unit_coverage_id"] = $new_uc["id"];
					foreach($player as $key => $value)
					{
						if($key == 'id' || $key == 'ins_unit_coverage_id')
						{
							//SKIP
						}
						else
						{
							$new_player[$key] = $value;
						}
					}
					db_insert_ins_player($new_player);
				}
			}
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		//IF THE DATES ARE THE EXACT SAME -- UPDATE THE CURRENT PROFILE -- DON'T CREATE A NEW ONE
		else if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) == strtotime($old_uc["coverage_current_since"]))
		{
			//UPDATE CURRENT UNIT COVERAGE -- DONT CREATE A NEW ONE
			
			$ins_policy_id = $old_uc["ins_policy_id"];
			
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE
			$update_old_uc = null;
			
			$update_old_uc["coverage_current_since"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			$update_old_uc["unit_type"] = $unit_type;
			$update_old_uc["unit_id"] = $unit_id;
			$update_old_uc["pd_limit"] = $pd_limit;
			$update_old_uc["radius"] = $radius;
			$update_old_uc["al_um_bi_limit"] = $al_um_bi_limit;
			$update_old_uc["al_uim_bi_limit"] = $al_uim_bi_limit;
			$update_old_uc["al_pip_limit"] = $al_pip_limit;
			$update_old_uc["al_prem"] = $al_prem;
			$update_old_uc["al_um_bi_prem"] = $al_um_bi_prem;
			$update_old_uc["al_uim_bi_prem"] = $al_uim_bi_prem;
			$update_old_uc["al_pip_prem"] = $al_pip_prem;
			$update_old_uc["pd_comp_ded"] = $pd_comp_ded;
			$update_old_uc["pd_comp_prem"] = $pd_comp_prem;
			$update_old_uc["pd_coll_ded"] = $pd_coll_ded;
			$update_old_uc["pd_coll_prem"] = $pd_coll_prem;
			$update_old_uc["pd_rental_daily_limit"] = $pd_rental_daily_limit;
			$update_old_uc["pd_rental_max_limit"] = $pd_rental_max_limit;
			$update_old_uc["pd_rental_prem"] = $pd_rental_prem;
			
			$where = null;
			$where["id"] = $old_uc["id"];
			db_update_ins_unit_coverage($update_old_uc,$where);
			
			//GET NEWLY UPDATED PROFILE
			$where = null;
			$where = $old_uc["id"];
			$new_uc = db_select_ins_unit_coverage($where);
			
			//CREATE CHANGE LOG ENTRY
			$change_desc = "";
			foreach($new_uc as $key => $value)
			{
				if($key == 'coverage_current_since' || $key == 'coverage_current_till' || $key == 'id')
				{
					//SKIP
				}
				else
				{
					if($new_uc[$key] != $old_uc[$key])
					{
						if($key == "unit_id")
						{
							$change_desc = $change_desc." ".$key." changed from ".$old_unit_number." to ".$new_unit_number.".";
						}
						else
						{
							$change_desc = $change_desc." ".$key." changed from ".$old_uc[$key]." to ".$new_uc[$key].".";
						}
							
					}
				}
			}
			
			$db_keys = array(
				'unit_type',
				'unit_id',
				'pd_limit',
				'radius',
				'al_um_bi_limit',
				'al_uim_bi_limit',
				'al_pip_limit',
				'al_prem',
				'al_um_bi_prem',
				'al_uim_bi_prem',
				'al_pip_prem',
				'pd_comp_ded',
				'pd_comp_prem',
				'pd_coll_ded',
				'pd_coll_prem',
				'pd_rental_daily_limit',
				'pd_rental_max_limit',
				'pd_rental_prem'
				);
			$key_conversion = array(
				'Unit Type',
				'Unit Number',
				'Unit Value',
				'Radius',
				'UM BI Limit',
				'UIM BI Limit',
				'PIP Limit',
				'Liability Premium',
				'UM BI Premium',
				'UIM BI Premium',
				'PIP Premium',
				'Phys Dam Comp Deductible',
				'Phys Dam Comp Premium',
				'Phys Dam Collision Deductible',
				'Phys Dam Collision Premium',
				'Rental Reimbursement Daily Limit',
				'Rental Reimbursement Max Limit',
				'Rental Reimbursement Premium'
				);
			
			$change_desc = substr(str_replace($db_keys,$key_conversion,$change_desc),1)." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = $new_uc["coverage_current_since"];
			$ins_change["obj_changed"] = "ins_unit_coverage";
			$ins_change["obj_id"] = $new_uc["id"];
			$ins_change["change_reason"] = "UC Edit";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		else
		{
			//CODE HERE TO HANDLE IF DATE IS NOT AFTER PREVIOUS CURRENT SINCE DATE
			echo "<script>alert('Change Date cannot be before current active date!');</script>";
		}
		
		
		//echo $change_desc;
	}
	
	//ADD LISTED DRIVER TO PROFILE -- CREATE NEW PROFILE IF NEW DATE
	function add_listed_driver()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_profile_id = $_POST["ins_profile_id"];
		$current_since_date = $_POST["current_since_date"];
		$current_since_hour = $_POST["current_since_hour"];
		$current_since_minute = $_POST["current_since_minute"];
		$ld_client_id = $_POST["ld_client_id"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET OLD PROFILE
		$where = null;
		$where["id"] = $ins_profile_id;
		$old_profile = db_select_ins_policy_profile($where);
		
		$ins_policy_id = $old_profile["ins_policy_id"];
		
		//GET CLIENT
		$where = null;
		$where["id"] = $ld_client_id;
		$client = db_select_client($where);
		
		//CHECK TO SEE IF THIS DRIVER IS ALREADY ON THE POLICY
		$where = null;
		$where["client_id"] = $ld_client_id;
		$where["ins_profile_id"] = $ins_profile_id;
		$driver_check = db_select_ins_listed_driver($where);
		
		if(empty($driver_check))
		{
			if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) > strtotime($old_profile["profile_current_since"]))
			{
				//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE AND THEN CREATE NEW PROFILE
				
				//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE
				$update_old_profile = null;
				$update_old_profile["profile_current_till"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
				
				$where = null;
				$where["id"] = $old_profile["id"];
				db_update_ins_policy_profile($update_old_profile,$where);
				
				//CREATE NEW PROFILE
				$insert_new_profile = null;
				$insert_new_profile["profile_current_since"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
				foreach($old_profile as $key => $value)
				{
					if($key == 'id' || $key == 'profile_current_since')
					{
						//SKIP
					}
					else
					{
						$insert_new_profile[$key] = $value;
					}
				}
				
				db_insert_ins_policy_profile($insert_new_profile);
				
				//GET NEWLY INSERTED PROFILE
				$where = null;
				$where["ins_policy_id"] = $insert_new_profile["ins_policy_id"];
				$where["profile_current_since"] = date("Y-m-d H:i:s",strtotime($insert_new_profile["profile_current_since"]));
				$new_profile = db_select_ins_policy_profile($where);
				
				
				
				//----------------CREATE ALL THE OLD LISTED DRIVERS FOR THE NEW PROFILE - COPY OF OLD ONES
				
				//GET ALL LISTED DRIVERS FROM OLD PROFILE
				$where = null;
				$where["ins_profile_id"] = $old_profile["id"];
				$old_listed_drivers = db_select_ins_listed_drivers($where);
				
				if(!empty($old_listed_drivers))
				{
					foreach($old_listed_drivers as $listed_driver)
					{
						echo $listed_driver["client"]["client_nickname"];
						//MAKE A COPY OF THE ADDTIONAL INSURED
						$new_listed_driver = null;
						$new_listed_driver["ins_profile_id"] = $new_profile["id"];
							
						foreach($listed_driver as $key => $value)
						{
							if($key == 'id' || $key == 'ins_profile_id' || $key == 'client')
							{
								//SKIP
							}
							else
							{
								$new_listed_driver[$key] = $value;
							}
						}
						db_insert_ins_listed_driver($new_listed_driver);
						//print_r($new_listed_driver);
					}
				}
				
				
				//----------------CREATE ALL THE NEW ADDITIONAL INSURED FOR THE NEW PROFILE - COPY OF OLD ONES
				
				//GET ALL ADDITIONAL INSURED
				$where = null;
				$where["ins_profile_id"] = $old_profile["id"];
				$where["role"] = "Additional Insured";
				$additional_insured_players = db_select_ins_players($where);
				
				if(!empty($additional_insured_players))
				{
					foreach($additional_insured_players as $player)
					{
						//MAKE A COPY OF THE ADDTIONAL INSURED
						$new_player = null;
						$new_player["ins_profile_id"] = $new_profile["id"];
						foreach($player as $key => $value)
						{
							if($key == 'id' || $key == 'ins_profile_id')
							{
								//SKIP
							}
							else
							{
								$new_player[$key] = $value;
							}
						}
						db_insert_ins_player($new_player);
					}
				}
				
				//CREATE NEW LISTED DRIVER
				$new_listed_driver = null;
				$new_listed_driver["client_id"] = $ld_client_id;
				$new_listed_driver["ins_profile_id"] = $new_profile["id"];
				db_insert_ins_listed_driver($new_listed_driver);
				
				$change_desc = $client["client_nickname"]." added as Listed Driver"." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
				
				//INSERT NEW INS_CHANGE
				$ins_change = null;
				$ins_change["ins_policy_id"] = $ins_policy_id;
				$ins_change["change_date"] = $new_profile["profile_current_since"];
				$ins_change["obj_changed"] = "ins_policy_profile";
				$ins_change["obj_id"] = $new_profile["id"];
				$ins_change["change_reason"] = "Pr Change";
				$ins_change["change_desc"] = $change_desc;
				$ins_change["user_id"] = $user_id;
				db_insert_ins_change($ins_change);
				
				//REFRESH DETAILS PAGE
				echo "<script>refresh_policy_details('$ins_policy_id');</script>";
			}
			else if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) == strtotime($old_profile["profile_current_since"]))
			{
				//CREATE NEW LISTED DRIVER
				$new_listed_driver = null;
				$new_listed_driver["client_id"] = $ld_client_id;
				$new_listed_driver["ins_profile_id"] = $ins_profile_id;
				db_insert_ins_listed_driver($new_listed_driver);
				
				$change_desc = $client["client_nickname"]." added as Listed Driver"." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
				
				//INSERT NEW INS_CHANGE
				$ins_change = null;
				$ins_change["ins_policy_id"] = $ins_policy_id;
				$ins_change["change_date"] = $old_profile["profile_current_since"];
				$ins_change["obj_changed"] = "ins_policy_profile";
				$ins_change["obj_id"] = $old_profile["id"];
				$ins_change["change_reason"] = "Pr Edit";
				$ins_change["change_desc"] = $change_desc;
				$ins_change["user_id"] = $user_id;
				db_insert_ins_change($ins_change);
				
				//REFRESH DETAILS PAGE
				echo "<script>refresh_policy_details('$ins_policy_id');</script>";
			}
			else
			{
				//CODE HERE TO HANDLE IF DATE IS NOT AFTER PREVIOUS CURRENT SINCE DATE
				echo "<script>alert('Change Date cannot be before current active date!');</script>";
			}
		}
		else
		{
			echo "<script>alert('This driver is already listed on the policy!');</script>";
		}
			
	}
	
	//ADD ADDITIONAL INSURED TO PROFILE -- CREATE NEW PROFILE IF NEW DATE
	function add_additional_insured()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_profile_id = $_POST["ins_profile_id"];
		$current_since_date = $_POST["current_since_date"];
		$current_since_hour = $_POST["current_since_hour"];
		$current_since_minute = $_POST["current_since_minute"];
		$ai_name = $_POST["ai_name"];
		$ai_address = $_POST["ai_address"];
		$ai_city = $_POST["ai_city"];
		$ai_state = $_POST["ai_state"];
		$ai_zip = $_POST["ai_zip"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET OLD PROFILE
		$where = null;
		$where["id"] = $ins_profile_id;
		$old_profile = db_select_ins_policy_profile($where);
		
		$ins_policy_id = $old_profile["ins_policy_id"];
	
		if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) > strtotime($old_profile["profile_current_since"]))
		{
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE AND THEN CREATE NEW PROFILE
			
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE
			$update_old_profile = null;
			$update_old_profile["profile_current_till"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			
			$where = null;
			$where["id"] = $old_profile["id"];
			db_update_ins_policy_profile($update_old_profile,$where);
			
			//CREATE NEW PROFILE
			$insert_new_profile = null;
			$insert_new_profile["profile_current_since"] = date("Y-m-d H:i:s",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			foreach($old_profile as $key => $value)
			{
				if($key == 'id' || $key == 'profile_current_since')
				{
					//SKIP
				}
				else
				{
					$insert_new_profile[$key] = $value;
				}
			}
			
			db_insert_ins_policy_profile($insert_new_profile);
			
			//GET NEWLY INSERTED PROFILE
			$where = null;
			$where["ins_policy_id"] = $insert_new_profile["ins_policy_id"];
			$where["profile_current_since"] = date("Y-m-d H:i:s",strtotime($insert_new_profile["profile_current_since"]));
			$new_profile = db_select_ins_policy_profile($where);
			
			
			
			//----------------CREATE ALL THE OLD LISTED DRIVERS FOR THE NEW PROFILE - COPY OF OLD ONES
			
			//GET ALL LISTED DRIVERS FROM OLD PROFILE
			$where = null;
			$where["ins_profile_id"] = $old_profile["id"];
			$old_listed_drivers = db_select_ins_listed_drivers($where);
			
			if(!empty($old_listed_drivers))
			{
				foreach($old_listed_drivers as $listed_driver)
				{
					//echo $listed_driver["client"]["client_nickname"];
					//MAKE A COPY OF THE ADDTIONAL INSURED
					$new_listed_driver = null;
					$new_listed_driver["ins_profile_id"] = $new_profile["id"];
						
					foreach($listed_driver as $key => $value)
					{
						if($key == 'id' || $key == 'ins_profile_id' || $key == 'client')
						{
							//SKIP
						}
						else
						{
							$new_listed_driver[$key] = $value;
						}
					}
					db_insert_ins_listed_driver($new_listed_driver);
					//print_r($new_listed_driver);
				}
			}
			
			
			//----------------CREATE ALL THE NEW ADDITIONAL INSURED FOR THE NEW PROFILE - COPY OF OLD ONES
			
			//GET ALL ADDITIONAL INSURED
			$where = null;
			$where["ins_profile_id"] = $old_profile["id"];
			$where["role"] = "Additional Insured";
			$additional_insured_players = db_select_ins_players($where);
			
			if(!empty($additional_insured_players))
			{
				foreach($additional_insured_players as $player)
				{
					//MAKE A COPY OF THE ADDTIONAL INSURED
					$new_player = null;
					$new_player["ins_profile_id"] = $new_profile["id"];
					foreach($player as $key => $value)
					{
						if($key == 'id' || $key == 'ins_profile_id')
						{
							//SKIP
						}
						else
						{
							$new_player[$key] = $value;
						}
					}
					db_insert_ins_player($new_player);
				}
			}
			
			//CREATE NEW ADDITIONAL INSURED
			$new_ins_player = null;
			$new_ins_player["ins_profile_id"] = $new_profile["id"];
			$new_ins_player["role"] = "Additional Insured";
			$new_ins_player["name"] = $ai_name;
			$new_ins_player["address"] = $ai_address;
			$new_ins_player["city"] = $ai_city;
			$new_ins_player["state"] = $ai_state;
			$new_ins_player["zip"] = $ai_zip;
			db_insert_ins_player($new_ins_player);
			
			$change_desc = $ai_name." added as Additional Insured | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = $new_profile["profile_current_since"];
			$ins_change["obj_changed"] = "ins_policy_profile";
			$ins_change["obj_id"] = $new_profile["id"];
			$ins_change["change_reason"] = "Pr Change";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		else if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) == strtotime($old_profile["profile_current_since"]))
		{
			//CREATE NEW ADDITIONAL INSURED
			$new_ins_player = null;
			$new_ins_player["ins_profile_id"] = $old_profile["id"];
			$new_ins_player["role"] = "Additional Insured";
			$new_ins_player["name"] = $ai_name;
			$new_ins_player["address"] = $ai_address;
			$new_ins_player["city"] = $ai_city;
			$new_ins_player["state"] = $ai_state;
			$new_ins_player["zip"] = $ai_zip;
			db_insert_ins_player($new_ins_player);
			
			$change_desc = $ai_name." added as Additional Insured | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = $old_profile["profile_current_since"];
			$ins_change["obj_changed"] = "ins_policy_profile";
			$ins_change["obj_id"] = $old_profile["id"];
			$ins_change["change_reason"] = "Pr Edit";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		else
		{
			//CODE HERE TO HANDLE IF DATE IS NOT AFTER PREVIOUS CURRENT SINCE DATE
			echo "<script>alert('Change Date cannot be before current active date!');</script>";
		}
	}
	
	//ADD LOSS PAYEE TO UNIT COVERAGE -- CREATE NEW UNIT COVERAGE IF NEW DATE
	function add_loss_payee()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_unit_coverage_id = $_POST["ins_unit_coverage_id"];
		$current_since_date = $_POST["current_since_date"];
		$current_since_hour = $_POST["current_since_hour"];
		$current_since_minute = $_POST["current_since_minute"];
		$lp_name = $_POST["lp_name"];
		$lp_address = $_POST["lp_address"];
		$lp_city = $_POST["lp_city"];
		$lp_state = $_POST["lp_state"];
		$lp_zip = $_POST["lp_zip"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET OLD UC
		$where = null;
		$where["id"] = $ins_unit_coverage_id;
		$old_uc = db_select_ins_unit_coverage($where);
		
		//IF NEW CURRENT DATE IS LATER THAN OLD CURRENT DATE -- CLOSE OUT OLD PROFILE AND CREATE NEW PROFILE
		if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) > strtotime($old_uc["coverage_current_since"]))
		{
			//UPDATE OLD PROFILE WITH NEW CURRENT TILL DATE AND THEN CREATE NEW PROFILE
			
			//UPDATE OLD UNIT COVERAGE WITH NEW COVERAGE CURRENT TILL DATE
			$update_old_uc = null;
			$update_old_uc["coverage_current_till"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			
			$where = null;
			$where["id"] = $old_uc["id"];
			db_update_ins_unit_coverage($update_old_uc,$where);
			
			$ins_policy_id = $old_uc["ins_policy_id"];
			
			
			//CREATE NEW UC
			$new_uc = null;
			$new_uc["coverage_current_since"] = date("Y-m-d H:i:s",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			foreach($old_uc as $key => $value)
			{
				if($key == 'id' || $key == 'coverage_current_since')
				{
					//SKIP
				}
				else
				{
					$new_uc[$key] = $value;
				}
			}
			
			db_insert_ins_unit_coverage($new_uc);
			
			//GET NEWLY INSERTED PROFILE
			$where = null;
			$where["ins_policy_id"] = $new_uc["ins_policy_id"];
			$where["coverage_current_since"] = date("Y-m-d H:i:s",strtotime($new_uc["coverage_current_since"]));
			$new_uc = db_select_ins_unit_coverage($where);
			
			//----------------CREATE ALL THE NEW LOSS PAYEES FOR THE NEW UNIT COVERAGE - COPY OF OLD ONES
			
			//GET ALL ADDITIONAL INSURED
			$where = null;
			$where["ins_unit_coverage_id"] = $old_uc["id"];
			$where["role"] = "Loss Payee";
			$loss_payee_players = db_select_ins_players($where);
			
			if(!empty($loss_payee_players))
			{
				foreach($loss_payee_players as $player)
				{
					//MAKE A COPY OF THE LOSS PAYEE
					$new_player = null;
					$new_player["ins_unit_coverage_id"] = $new_uc["id"];
					foreach($player as $key => $value)
					{
						if($key == 'id' || $key == 'ins_unit_coverage_id')
						{
							//SKIP
						}
						else
						{
							$new_player[$key] = $value;
						}
					}
					db_insert_ins_player($new_player);
				}
			}
			
			
			//INSERT NEW LOSS PAYEE
			$new_ins_player = null;
			$new_ins_player["ins_unit_coverage_id"] = $new_uc["id"];
			$new_ins_player["role"] = "Loss Payee";
			$new_ins_player["name"] = $lp_name;
			$new_ins_player["address"] = $lp_address;
			$new_ins_player["city"] = $lp_city;
			$new_ins_player["state"] = $lp_state;
			$new_ins_player["zip"] = $lp_zip;
			db_insert_ins_player($new_ins_player);
			
			$change_desc = $lp_name." added as Additional Insured | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = $new_uc["coverage_current_since"];
			$ins_change["obj_changed"] = "ins_unit_coverage";
			$ins_change["obj_id"] = $new_uc["id"];
			$ins_change["change_reason"] = "UC Change";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		//IF THE DATES ARE THE EXACT SAME -- UPDATE THE CURRENT PROFILE -- DON'T CREATE A NEW ONE
		else if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) == strtotime($old_uc["coverage_current_since"]))
		{
			//UPDATE CURRENT UNIT COVERAGE -- DONT CREATE A NEW ONE
			
			$ins_policy_id = $old_uc["ins_policy_id"];
			
			//INSERT NEW LOSS PAYEE
			$new_ins_player = null;
			$new_ins_player["ins_unit_coverage_id"] = $old_uc["id"];
			$new_ins_player["role"] = "Loss Payee";
			$new_ins_player["name"] = $lp_name;
			$new_ins_player["address"] = $lp_address;
			$new_ins_player["city"] = $lp_city;
			$new_ins_player["state"] = $lp_state;
			$new_ins_player["zip"] = $lp_zip;
			db_insert_ins_player($new_ins_player);
			
			$change_desc = $lp_name." added as Additional Insured | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = $old_uc["coverage_current_since"];
			$ins_change["obj_changed"] = "ins_unit_coverage";
			$ins_change["obj_id"] = $old_uc["id"];
			$ins_change["change_reason"] = "UC Edit";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		else
		{
			//CODE HERE TO HANDLE IF DATE IS NOT AFTER PREVIOUS CURRENT SINCE DATE
			echo "<script>alert('Change Date cannot be before current active date!');</script>";
		}
	}
	
	//CREATE NEW UNIT COVERAGE FOR POLICY
	function add_new_unit_coverage()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_policy_id = $_POST["ins_policy_id"];
		$current_since_date = $_POST["current_since_date"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//INSERT NEW UNIT COVERAGE
		$insert_unit_coverage = null;
		$insert_unit_coverage["ins_policy_id"] = $ins_policy_id;
		$insert_unit_coverage["coverage_current_since"] = date("Y-m-d H:i:s",strtotime($current_since_date));
		$insert_unit_coverage["unit_type"] = "Unknown";
		db_insert_ins_unit_coverage($insert_unit_coverage);
		
		//GET NEWLY INSERTED UNIT COVERAGE
		$where = null;
		$where["ins_policy_id"] = $insert_unit_coverage["ins_policy_id"];
		$where["coverage_current_since"] = date("Y-m-d H:i:s",strtotime($insert_unit_coverage["coverage_current_since"]));
		$new_uc = db_select_ins_unit_coverage($where);
		
		//INSERT CHANGE FOR CHANGE LOG
		$change_desc = "New Unit Coverage added | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
		//INSERT NEW INS_CHANGE
		$ins_change = null;
		$ins_change["ins_policy_id"] = $ins_policy_id;
		$ins_change["change_date"] = $new_uc["coverage_current_since"];
		$ins_change["obj_changed"] = "ins_unit_coverage";
		$ins_change["obj_id"] = $new_uc["id"];
		$ins_change["change_reason"] = "UC Added";
		$ins_change["change_desc"] = $change_desc;
		$ins_change["user_id"] = $user_id;
		db_insert_ins_change($ins_change);
	}
	
	//DELETE UNIT COVERAGE ON OOPS
	function delete_unit_coverage()
	{
		//DELETE UNIT COVERAGE
		$where = null;
		$where["id"] = $_POST["uc_id"];
		db_delete_ins_unit_coverage($where);
	}
	
	function remove_unit_coverage()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$ins_unit_coverage_id = $_POST["ins_unit_coverage_id"];
		$current_since_date = $_POST["current_since_date"];
		$current_since_hour = $_POST["current_since_hour"];
		$current_since_minute = $_POST["current_since_minute"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET OLD UNIT COVERAGE
		$where = null;
		$where["id"] = $ins_unit_coverage_id;
		$old_uc = db_select_ins_unit_coverage($where);
		
		//GET OLD UNIT NUMBER
		if($old_uc["unit_type"] == "Truck")
		{
			//GET TRUCK
			$where = null;
			$where["id"] = $old_uc["unit_id"];
			$truck = db_select_truck($where);
			
			$old_unit_number = $truck["truck_number"];
		}
		else if($old_uc["unit_type"] == "Trailer")
		{
			//GET TRAILER
			$where = null;
			$where["id"] = $old_uc["unit_id"];
			$trailer = db_select_trailer($where);
			
			$old_unit_number = $trailer["trailer_number"];
		}
		else if($old_uc["unit_type"] == "Unknown")
		{
			$old_unit_number = "Unknown";
		}
		
		if(strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute) > strtotime($old_uc["coverage_current_since"]))
		{
			//UPDATE OLD UNIT COVERAGE WITH NEW COVERAGE CURRENT TILL DATE
			$update_old_uc = null;
			$update_old_uc["coverage_current_till"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			
			$where = null;
			$where["id"] = $old_uc["id"];
			db_update_ins_unit_coverage($update_old_uc,$where);
			
			$ins_policy_id = $old_uc["ins_policy_id"];

			//INSERT CHANGE FOR CHANGE LOG
			$change_desc = "Unit Coverage Removed for ".$old_uc["unit_type"]." ".$old_unit_number." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
				
			//INSERT NEW INS_CHANGE
			$ins_change = null;
			$ins_change["ins_policy_id"] = $ins_policy_id;
			$ins_change["change_date"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			$ins_change["obj_changed"] = "ins_unit_coverage";
			$ins_change["obj_id"] = $new_uc["id"];
			$ins_change["change_reason"] = "UC Removed";
			$ins_change["change_desc"] = $change_desc;
			$ins_change["user_id"] = $user_id;
			db_insert_ins_change($ins_change);
			
			//REFRESH DETAILS PAGE
			echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		}
		else
		{
			//CODE HERE TO HANDLE IF DATE IS NOT AFTER PREVIOUS CURRENT SINCE DATE
			echo "<script>alert('Change Date cannot be before or the same as the current active date!');</script>";
		}
		
	}
	
	function load_duplicate_uc_dialog()
	{
		$data['uc_id'] = $_POST["uc_id"];
		$this->load->view('equipment/insurance/duplicate_uc_dialog',$data);
		//echo 'hello';
	}
	
	//DUPLICATE UNIT COVERAGE GIVEN UC_ID, UNIT_ID, AND DATE
	function duplicate_unit_coverage()
	{
		date_default_timezone_set('America/Denver');
		$user_id = $this->session->userdata('user_id');
		
		$duplicate_uc_id = $_POST["duplicate_uc_id"];
		$unit_type = $_POST["duplicate_uc_unit_type"];
		$truck_id = $_POST["duplicate_uc_truck_id"];
		$trailer_id = $_POST["duplicate_uc_trailer_id"];
		$current_since_date = $_POST["duplicate_uc_date"];
		$current_since_hour = $_POST["duplicate_uc_hour"];
		$current_since_minute = $_POST["duplicate_uc_minute"];
		
		//GET USER PERSON
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET UC
		$where = null;
		$where["id"] = $duplicate_uc_id;
		$original_uc = db_select_ins_unit_coverage($where);
		
		$ins_policy_id = $original_uc["ins_policy_id"];
		
		//CREATE DUPLICATE -- CHANGE UNIT_TYPE, UNIT_ID, AND COVERAGE_CURRENT_SINCE
		$new_uc = null;
		foreach($original_uc as $key => $value)
		{
			
			if($key != "id" && $key != "coverage_current_since" && $key != "unit_type" && $key != "unit_id")
			{
				$new_uc[$key] = $value;
			}
			
			$new_uc["coverage_current_since"] = date("Y-m-d H:i",strtotime($current_since_date." ".$current_since_hour.":".$current_since_minute));
			$new_uc["unit_type"] = $unit_type;
			if($unit_type == "Truck")
			{
				$new_uc["unit_id"] = $truck_id;
			}
			else if($unit_type == "Trailer")
			{
				$new_uc["unit_id"] = $trailer_id;
			}
		}
		
		db_insert_ins_unit_coverage($new_uc);
		
		//GET NEWLY INSERTED UC
		$where = null;
		$where["coverage_current_since"] = $new_uc["coverage_current_since"];
		$where["ins_policy_id"] = $ins_policy_id;
		$where["unit_type"] = $unit_type;
		$where["unit_id"] = $new_uc["unit_id"];
		$newly_inserted_uc = db_select_ins_unit_coverage($where);
		
		//GET OLD UNIT NUMBER
		if($new_uc["unit_type"] == "Truck")
		{
			//GET TRUCK
			$where = null;
			$where["id"] = $new_uc["unit_id"];
			$truck = db_select_truck($where);
			
			$new_unit_number = $truck["truck_number"];
		}
		else if($new_uc["unit_type"] == "Trailer")
		{
			//GET TRAILER
			$where = null;
			$where["id"] = $new_uc["unit_id"];
			$trailer = db_select_trailer($where);
			
			$new_unit_number = $trailer["trailer_number"];
		}
		else if($new_uc["unit_type"] == "Unknown")
		{
			$new_unit_number = "Unknown";
		}
		
		//DUPLICATE LOSS PAYEES
		
		//GET ALL LOSS PAYEES FOR ORIGINAL UC
		$where = null;
		$where["ins_unit_coverage_id"] = $original_uc["id"];
		$where["role"] = "Loss Payee";
		$loss_payee_players = db_select_ins_players($where);
		
		foreach($loss_payee_players as $lp)
		{
			$insert_lp = null;
			$insert_lp["ins_unit_coverage_id"] = $newly_inserted_uc["id"];
			$insert_lp["role"] = $lp["role"];
			$insert_lp["name"] = $lp["name"];
			$insert_lp["address"] = $lp["address"];
			$insert_lp["city"] = $lp["city"];
			$insert_lp["state"] = $lp["state"];
			$insert_lp["zip"] = $lp["zip"];
			
			db_insert_ins_player($insert_lp);
		}
		
		//INSERT CHANGE FOR CHANGE LOG
		$change_desc = "New Unit Coverage added - ".$unit_type." ".$new_unit_number." | ".date("m/d/y H:i")." ".$user["person"]["f_name"];
			
		//INSERT NEW INS_CHANGE
		$ins_change = null;
		$ins_change["ins_policy_id"] = $ins_policy_id;
		$ins_change["change_date"] = $new_uc["coverage_current_since"];
		$ins_change["obj_changed"] = "ins_unit_coverage";
		$ins_change["obj_id"] = $newly_inserted_uc["id"];
		$ins_change["change_reason"] = "UC Added";
		$ins_change["change_desc"] = $change_desc;
		$ins_change["user_id"] = $user_id;
		db_insert_ins_change($ins_change);
		
		//REFRESH DETAILS PAGE
		echo "<script>refresh_policy_details('$ins_policy_id');</script>";
		
		//echo $duplicate_uc_id;
	}
	
	
	//DEPRECATED -- GENERATE DAILY INSURANCE SHEET - THIS IS THE ONE TO PRINT AND GET SIGNED BY FM'S
	function load_daily_insurance_sheet()
	{
		//LOAD HEADER
		$this->load->view('equipment/insurance_sheet_header');
	
		//GET ACTIVE FLEET MANAGERS
		$where = null;
		$where['role'] = "Fleet Manager";
		$fleet_managers = db_select_persons($where);
		
	
		//FOREACH FLEET MANAGER
		foreach($fleet_managers as $fm)
		{
			//echo $fm["f_name"]."<br>";
		
			//GET FM COMPANY
			$where = null;
			$where["person_id"] = $fm["id"];
			$fm_company = db_select_company($where);
			
			//echo $fm_company["company_status"];
			
			if($fm_company["company_status"] == 'Active')
			{
				//GET ALL TRUCKS FOR THIS FM
				$where = null;
				$where = "fm_id = ".$fm["id"]." AND status != 'Returned' ";
				$trucks = db_select_trucks($where,"client_id DESC");
				if(!empty($trucks))
				{
					//LOAD THE INSURANCE LIST
					$data = null;
					$data["list_title"] = $fm["f_name"]."'s Fleet";
					$data["trucks"] = $trucks;
					$this->load->view('equipment/fm_insurance_sheet',$data);
				}
			}
		}
		
		//GET ALL TRUCKS THAT ARE UNASSIGNED
		$where = null;
		$where = "fm_id IS NULL AND status != 'Returned' ";
		$trucks = db_select_trucks($where,"client_id DESC");
		
		if(!empty($trucks))
		{
			$data = null;
			$data["list_title"] = "Unnassigned Trucks";
			$data["trucks"] = $trucks;
			$this->load->view('equipment/fm_insurance_sheet',$data);
		}
	}
	
	//--------------------   ZONAR AND MAP -----------------------//
	//ZONAR AND MAP FUNCTIONS
	function load_asset_map()
	{
		$json_geopoints = $this->return_asset_data();
		
		$data['json_geopoints'] = $json_geopoints;
		//echo "hello world";
		$this->load->view('equipment/asset_map.php',$data);
	}
	
	function get_asset_data()
	{
		$trucks = array();
		$sql = 'SELECT DISTINCT truck_id FROM geopoint WHERE truck_id IS NOT NULL';
		
		$query = $this->db->query($sql);
		foreach ($query->result() as $row){
			
//			echo "Truck ID: ".$row->truck_id."<br>";
			
			$truck_sql = 'SELECT * FROM truck WHERE id = ' . $row->truck_id;
			$truck_query = $this->db->query($truck_sql);
			foreach($truck_query->result() as $truck_row){
				$truck = array();
				$truck['id'] = $row->truck_id;
				$truck['truck_number'] = $truck_row->truck_number;
				$truck['dropdown_status'] = $truck_row->dropdown_status;
				
				$trucks[$row->truck_id] = $truck;
			}
		}
		
		$geopoints = array();
		foreach($trucks as $truck){
			$sql="SELECT * from geopoint where truck_id = " . $truck['id'] . " ORDER BY id DESC LIMIT 1";
			$query = $this->db->query($sql);
			foreach($query->result() as $row){
				$geopoint['truck_number'] = $truck['truck_number'];
				$geopoint['lat'] = $row->latitude;
				$geopoint['long'] = $row->longitude;
				$geopoint['heading'] = $row->heading;
				$geopoint['speed'] = $row->speed;
				$geopoint['power'] = $row->power;
				$geopoint['odom'] = $row->odometer;
				
//				echo "Truck: " . $truck['truck_number'] . " lat: " . $row->latitude . " lng: " . $row->longitude . "<br>";
				
				$geopoints[] = $geopoint;
			}
		}
		echo json_encode((array)$geopoints);
	}
	
	function return_asset_data()
	{
		
		
		
		$trucks = array();
		$sql = 'SELECT DISTINCT truck_id FROM geopoint WHERE truck_id IS NOT NULL';
		
		$query = $this->db->query($sql);
		foreach ($query->result() as $row){
			
//			echo "Truck ID: ".$row->truck_id."<br>";
			
			$truck_sql = 'SELECT * FROM truck WHERE id = ' . $row->truck_id;
			$truck_query = $this->db->query($truck_sql);
			foreach($truck_query->result() as $truck_row){
				$truck = array();
				$truck['id'] = $row->truck_id;
				$truck['truck_number'] = $truck_row->truck_number;
				$truck['dropdown_status'] = $truck_row->dropdown_status;
				
				$trucks[$row->truck_id] = $truck;
			}
		}
		
		$geopoints = array();
		foreach($trucks as $truck){
			$sql="SELECT * from geopoint where truck_id = " . $truck['id'] . " ORDER BY id DESC LIMIT 1";
			$query = $this->db->query($sql);
			foreach($query->result() as $row){
				$geopoint['truck_number'] = $truck['truck_number'];
				$geopoint['lat'] = $row->latitude;
				$geopoint['long'] = $row->longitude;
				$geopoint['heading'] = $row->heading;
				$geopoint['speed'] = $row->speed;
				$geopoint['power'] = $row->power;
				$geopoint['odom'] = round($row->odometer);
				
				$geopoints[] = $geopoint;
			}
		}
		return json_encode((array)$geopoints);
	}
	
	function idle_time()
	{
		date_default_timezone_set('America/Denver');
		$toDatetime = time();
		$fromDatetime = time() - (60 * 60 * 1);
		
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
									"Cookie: foo=bar\r\n",
				'user_agent'=>    $_SERVER['HTTP_USER_AGENT'] 
			)
		);

		$context = stream_context_create($opts);
		
		$asset_xml = file_get_contents('http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showopen&operation=showassets&format=xml',false,$context);
		
		$parsed_asset_xml = simplexml_load_string($asset_xml);
		
		$assets = array();
		foreach($parsed_asset_xml->children() as $child){
			$assets[] = intval($child->fleet);
		}
		
		sort($assets);
		
		$xml = file_get_contents('http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showposition&operation=idlestopallidle&fromdate='.$toDatetime.'&todate='.$fromDatetime.'&idletime=00:10:00&idletimecomp=>',false, $context);
		
		$parsed_xml = simplexml_load_string($xml);
		
		$assetTimes = array();
		foreach($assets as $asset){
//			echo "Asset: " . $asset . "<br>";
			$idle_time = strtotime("1970-01-01 00:00:00 UTC");
			foreach($parsed_xml->children() as $child){
				if(intval($child->attributes()->fleet) == $asset){
					$length = $child->event->length;
//					echo "Before: " . $idle_time . "<br>";
//					echo "Length: " . strtotime("1970-01-01 $length UTC") . "<br>";
					$idle_time += strtotime("1970-01-01 $length UTC");
//					echo "Added: " . $idle_time . '<br>';
				}
			}
			
			$fuel_xml = file_get_contents('http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showopen&operation=jbustrip&format=xml&target='.$asset.'&reqtype=fleet&start='.$fromDatetime.'&end='.$toDatetime,false,$context);
//			
			$parsed_fuel_xml = simplexml_load_string($fuel_xml);
//			
			$mile_total = 0;
			$fuel_total = 0;
			$idle_fuel_total = 0;
			foreach($parsed_fuel_xml->children() as $child){
//				echo "odometer for ".$asset.": ".$child->asset->totals->odometer."<br>";
//				echo "Fuel used for ".$asset.": ".$child->asset->totals->fuel_total."<br>";
				$mile_total += round(intval($child->asset->miles) * 0.000621371);
				$fuel_total += intval($child->asset->fuel);
				$idle_fuel_total += floatval($child->asset->idle_fuel);
			}
//			echo 'Total Fuel for '. $asset . ': ' . $fuel_total . '<br>';
//			echo 'Total miles for '. $asset . ': ' . $mile_total . '<br>';
			$assetData['asset'] = $asset;
			$assetData['idle_time'] = $idle_time;
			$assetData['mile_total'] = $mile_total;
			$assetData['fuel_total'] = $fuel_total;
			$assetData['idle_fuel_total'] = $idle_fuel_total;
			if($mile_total==0 || $fuel_total == 0){
				$assetData['mpg'] = 0;
			}else{
				$assetData['mpg'] = round(($mile_total / $fuel_total),2);
			}
			$assetDatas[] = $assetData;
//			echo "Total Idle Time: " . gmdate("H:i:s", $idle_time) . "<br>";
		}
		
		$data['startTime'] = date('m/d/Y',$toDatetime);
		$data['endTime'] = date('m/d/Y',$fromDatetime);
		$data['assetDatas'] = $assetDatas;
		$data['title'] = 'Idle Time Report';
		$this->load->view('equipment/idle_time',$data);//OVERALL VIEW WHICH LATER CALLS THE INDIVIDUAL BOXES
		
	}
	
	function idle_report()
	{
		$fromDatetime = $_POST['fromDatetime'];
		$toDatetime = $_POST['toDatetime'];
		
		$fromDatetime = strtotime($fromDatetime);
		$toDatetime = strtotime($toDatetime);
		
//		echo 'from ' . $fromDatetime . '<br>';
//		echo 'to ' . $toDatetime . '<br>';
		
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
									"Cookie: foo=bar\r\n",
				'user_agent'=>    $_SERVER['HTTP_USER_AGENT'] 
			)
		);

		$context = stream_context_create($opts);
		
		$asset_xml = file_get_contents('http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showopen&operation=showassets&format=xml',false,$context);
		
		$parsed_asset_xml = simplexml_load_string($asset_xml);
		
		$assets = array();
		foreach($parsed_asset_xml->children() as $child){
			$assets[] = intval($child->fleet);
		}
		
		sort($assets);
		
		$xml = file_get_contents('http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showposition&operation=idlestopallidle&fromdate='.$fromDatetime.'&todate='.$toDatetime.'&idletime=00:10:00&idletimecomp=>',false, $context);
		
		$parsed_xml = simplexml_load_string($xml);
		
		$assetTimes = array();
		foreach($assets as $asset){
//			echo "Asset: " . $asset . "<br>";
			$idle_time = strtotime("1970-01-01 00:00:00 UTC");
			foreach($parsed_xml->children() as $child){
				if(intval($child->attributes()->fleet) == $asset)
				{
					$length = $child->event->length;
//					echo "Before: " . $idle_time . "<br>";
//					echo "Length: " . strtotime("1970-01-01 $length UTC") . "<br>";
					$idle_time += strtotime("1970-01-01 $length UTC");
//					echo "Added: " . $idle_time . '<br>';
				}
			}
			
			$fuel_xml = file_get_contents('http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showopen&operation=jbustrip&format=xml&target='.$asset.'&reqtype=fleet&start='.$fromDatetime.'&end='.$toDatetime,false,$context);
//			
			$parsed_fuel_xml = simplexml_load_string($fuel_xml);
//			
			$mile_total = 0;
			$fuel_total = 0;
			$idle_fuel_total = 0;
			foreach($parsed_fuel_xml->children() as $child){
//				echo "odometer for ".$asset.": ".$child->asset->totals->odometer."<br>";
//				echo "Fuel used for ".$asset.": ".$child->asset->totals->fuel_total."<br>";
				$mile_total += round(intval($child->asset->miles) * 0.000621371);
				$fuel_total += intval($child->asset->fuel);
				$idle_fuel_total += floatval($child->asset->idle_fuel);
			}
//			echo 'Total Fuel for '. $asset . ': ' . $fuel_total . '<br>';
//			echo 'Total miles for '. $asset . ': ' . $mile_total . '<br>';
			$assetData['asset'] = $asset;
			$assetData['idle_time'] = $idle_time;
			$assetData['mile_total'] = $mile_total;
			$assetData['fuel_total'] = $fuel_total;
			$assetData['idle_fuel_total'] = $idle_fuel_total;
			if($mile_total==0 || $fuel_total == 0){
				$assetData['mpg'] = 0;
			}else{
				$assetData['mpg'] = round(($mile_total / $fuel_total),2);
			}
			$assetDatas[] = $assetData;
//			echo "Total Idle Time: " . gmdate("H:i:s", $idle_time) . "<br>";
		}
		
		$data['startTime'] = date('m/d/Y',$fromDatetime);
		$data['endTime'] = date('m/d/Y',$toDatetime);
		$data['assetDatas'] = $assetDatas;
		$data['title'] = 'Idle Time Report';
		$this->load->view('equipment/idle_report',$data); //TRUCK SPECIFIC REPORT
	}
	
	function get_truck_data($truck_id=1,$fromDatetime='2016-03-17 09:45:00',$toDatetime='2016-03-18 09:45:00')
	{
		
		$fromDatetime = strtotime($fromDatetime);
		$toDatetime = strtotime($toDatetime);
		
//		echo 'from ' . $fromDatetime . '<br>';
//		echo 'to ' . $toDatetime . '<br>';
		
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
									"Cookie: foo=bar\r\n",
				'user_agent'=>    $_SERVER['HTTP_USER_AGENT'] 
			)
		);

		$context = stream_context_create($opts);
		
		$idle_xml = file_get_contents('http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showposition&operation=idlestopallidle&fromdate='.$fromDatetime.'&todate='.$toDatetime.'&idletime=00:10:00&idletimecomp=>',false, $context);
		
		$parsed_idle_xml = simplexml_load_string($idle_xml);
		
		$idle_time = strtotime("1970-01-01 00:00:00 UTC");
		foreach($parsed_idle_xml->children() as $child){
				$length = $child->event->length;
//					echo "Before: " . $idle_time . "<br>";
//					echo "Length: " . strtotime("1970-01-01 $length UTC") . "<br>";
				$idle_time += strtotime("1970-01-01 $length UTC");
//					echo "Added: " . $idle_time . '<br>';
		}

		$fuel_xml = file_get_contents('http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showopen&operation=jbustrip&format=xml&target='.$truck_id.'&reqtype=fleet&start='.$fromDatetime.'&end='.$toDatetime,false,$context);
//			
		$parsed_fuel_xml = simplexml_load_string($fuel_xml);
//			
		$mile_total = 0;
		$fuel_total = 0;
		$idle_fuel_total = 0;
		foreach($parsed_fuel_xml->children() as $child){
//				echo "odometer for ".$asset.": ".$child->asset->totals->odometer."<br>";
//				echo "Fuel used for ".$asset.": ".$child->asset->totals->fuel_total."<br>";
			$mile_total += round(intval($child->asset->miles) * 0.000621371);
			$fuel_total += intval($child->asset->fuel);
			$idle_fuel_total += floatval($child->asset->idle_fuel);
		}
//			echo 'Total Fuel for '. $asset . ': ' . $fuel_total . '<br>';
//			echo 'Total miles for '. $asset . ': ' . $mile_total . '<br>';
		$assetData['asset'] = $truck_id;
		$assetData['idle_time'] = $idle_time;
		$assetData['mile_total'] = $mile_total;
		$assetData['fuel_total'] = $fuel_total;
		$assetData['idle_fuel_total'] = $idle_fuel_total;
		if($mile_total==0 || $fuel_total == 0){
			$assetData['mpg'] = 0;
		}else{
			$assetData['mpg'] = $mile_total / $fuel_total;
		}
		
		print_r( $assetData);
	}
	
}