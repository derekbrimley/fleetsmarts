<?php		

class Billing extends MY_Controller 
{
	//INDEX
	function index()
	{
		//GET FLEET MANAGERS
		$where = null;
		//$where["role"] = "Fleet Manager";
		$where = " role = 'Fleet Manager' OR role = 'Driver Manager' ";
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
		
		//GET OPTIONS FOR DRIVER MANAGER DROPDOWN LIST
		$where = null;
		//$where['role'] = "Driver Manager";
		$where = " role = 'Fleet Manager' OR role = 'Driver Manager' ";
		$driver_managers = db_select_persons($where);
		$dm_filter_dropdown_options = array();
		$dm_filter_dropdown_options['All'] = "All DMs";
		foreach ($driver_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$dm_filter_dropdown_options[$manager['id']] = $title;
		}
		
		//GET ACTIVE CARRIERS FOR BILLED UNDER DROPDOWN
		$where = null;
		$where["type"] = "Carrier";
		$where["company_status"] = "Active";
		$carriers = db_select_companys($where,"company_side_bar_name");
		$billed_under_options = array();
		$billed_under_options["All"] = "All Carriers";
		foreach($carriers as $carrier)
		{
			$billed_under_options[$carrier["id"]] = $carrier["company_side_bar_name"];
		}
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		//$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$clients_dropdown_options = array();
		$clients_dropdown_options["All"] = "All Drivers";
		foreach($dd_all_clients as $client)
		{
			$clients_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		
		//GET LIST OF AR SPECIALISTS
		$ar_specialist_ids = get_distinct("ar_specialist_id","load");
		$ars_dropdown_options = array();
		$ars_dropdown_options["All"] = "All Specialists";
		foreach($ar_specialist_ids as $ar_sp)
		{
			//GET USER
			$where = null;
			$where["id"] = $ar_sp;
			$ar_user = db_select_user($where);
			if(!empty($ar_user))
			{
				$ars_dropdown_options[$ar_user["id"]] = $ar_user["person"]["f_name"];
			}
		}
		
		//GET OPTIONS FOR HOLD REASON
		$hold_reasons = get_distinct('denied_reason','load');
		$hold_reasons_options = array();
		$hold_reasons_options["All"] = "All";
		foreach($hold_reasons as $reason)
		{
			if(!empty($reason))
			{
				$hold_reasons_options[$reason] = $reason;
			}
		}
		$hold_reasons_options["No Hold"] = "No Hold";
		
		$data['hold_reasons_options'] = $hold_reasons_options;
		$data['broker_dropdown_options'] = $broker_dropdown_options;
		$data['billed_under_options'] = $billed_under_options;
		$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
		$data['dm_filter_dropdown_options'] = $dm_filter_dropdown_options;
		$data['ars_dropdown_options'] = $ars_dropdown_options;
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['tab'] = 'Billing';
		$data['title'] = "Billing";
		$this->load->view('billing_view',$data);
	}
	
	//LOAD REPORT
	function load_list()
	{
		//GET FILTER PARAMETERS
		$broker_dropdown = $_POST["broker_dropdown"];
		$billed_under = $_POST["carrier_dropdown"];
		$fleet_manager_dropdown = $_POST["fleet_managers_dropdown"];
		$driver_managers_dropdown = $_POST["driver_managers_dropdown"];
		$ar_specialist_dropdown = $_POST["ar_specialist_dropdown"];
		$driver_dropdown = $_POST["driver_dropdown"];
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
		$view_dropdown = $_POST["get_direct_bills"];
		
		//CREATE WHERE CLAUSE FOR LOAD QUERY
		$where = " AND status = 'Dropped' ";
		
		//SET WHERE FOR BROKER
		if($broker_dropdown != "All")
		{
			$where = $where." AND broker_id = ".$broker_dropdown;
		}
		
		//SET WHERE FOR CARRIER
		if($billed_under != "All")
		{
			$where = $where." AND billed_under = ".$billed_under;
		}
		
		//SET WHERE FOR FM
		if($fleet_manager_dropdown != "All")
		{
			$where = $where." AND fleet_manager_id = ".$fleet_manager_dropdown;
		}
		
		//SET WHERE FOR DM
		if($driver_managers_dropdown != "All")
		{
			$where = $where." AND dm_id = ".$driver_managers_dropdown;
		}
		
		//SET WHERE FOR AR SPECIALIST
		if($ar_specialist_dropdown != "All")
		{
			$where = $where." AND ar_specialist_id = ".$ar_specialist_dropdown;
		}
		
		//SET WHERE FOR DRIVER
		if($driver_dropdown != "All")
		{
			$where = $where." AND (client_id = ".$driver_dropdown." OR driver2_id = ".$driver_dropdown.")";
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
		
		//SET WHERE FOR VERIFIED STATUS
		if($_POST["funding_verified_dropdown"] != "All")
		{
			if($_POST["funding_verified_dropdown"] == "Verified")
			{
				$where = $where." AND  funded_datetime IS NOT NULL ";
			}
			else
			{
				$where = $where." AND  funded_datetime IS NULL ";
			}
		}
		
		//SET WHERE FOR PUSH START DATE
		if(!empty($_POST["push_start_date_filter"]))
		{
			$push_start_date = date("Y-m-d G:i:s",strtotime($_POST["push_start_date_filter"]));
			$where = $where." AND pushed_datetime > '".$push_start_date."' ";
		}
		
		//SET WHERE FOR PUSH END DATE
		if(!empty($_POST["push_end_date_filter"]))
		{
			$push_end_date = date("Y-m-d G:i:s",strtotime($_POST["push_end_date_filter"])+24*60*60);
			$where = $where." AND pushed_datetime < '".$push_end_date."' ";
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
		
		//SET WHERE FOR FUNDED END DATE
		if($_POST["hold_reason_filter"] != "All")
		{
			if($_POST["hold_reason_filter"] == 'No Hold')
			{
				$where = $where." AND denied_reason IS NULL ";
			}
			else
			{
				$where = $where." AND denied_reason = '".$_POST["hold_reason_filter"]."' ";
			}
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
		
		//SEARCH
		//echo mysql_real_escape_string($_POST["search_term"]);
		if(!empty($_POST["search_term"]))
		{
			$search = mysql_real_escape_string($_POST["search_term"]);
			$where = "      status = 'Dropped' AND customer_load_number LIKE '%$search%' OR invoice_number LIKE '%$search%'";//NEEDED TO ADD 4 BLANKS FOR SUBSTR
		}
		
		$where = substr($where,4);
		//echo $where;
		$loads = db_select_loads($where,"billing_status_number, final_drop_datetime ");
		
		$data['loads'] = $loads;
		$data['view'] = $view_dropdown;
		$this->load->view('billing/funding_report',$data);
	}
	
	//LOAD ROW
	function refresh_row()
	{
		$load_id = $_POST["load_id"];
		
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		$data['load'] = $load;
		$this->load->view('billing/billing_row',$data);
	}
	
	//OPEN LOAD DETAILS
	function open_details()
	{
		$load_id = $_POST["load_id"];
		
		//UPDATE BILLING STATUS
		$billing_status = determine_billing_status($load_id);
		$update_load = null;
		$update_load["billing_status"] = $billing_status["status"];
		$update_load["billing_status_number"] = $billing_status["status_number"];
		$where = null;
		$where = null;
		$where["id"] = $load_id;
		db_update_load($update_load,$where);
		
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		//FUNCTION FOR TRANSITION AFTER CODE UPDATE: CREATES GEOPOINTS FOR PICKS AND DROPS THAT WOULD HAVE BEEN CREATED ON THE RCR SAVE
		create_geopoints_for_load($load);
		
		//GET FLEET MANAGERS
		$where = null;
		//$where["role"] = "Fleet Manager";
		$where = " role = 'Fleet Manager' OR role = 'Driver Manager' ";
		$fleet_managers = db_select_persons($where,"f_name");
		$fleet_managers_dropdown_options = array();
		$fleet_managers_dropdown_options["All"] = "All FMs";
		foreach($fleet_managers as $fleet_manager)
		{
			$fleet_managers_dropdown_options[$fleet_manager["id"]] = $fleet_manager["f_name"]." ".$fleet_manager["l_name"];
		}
		
		//GET ACTIVE CARRIERS FOR BILLED UNDER DROPDOWN
		$where = null;
		$where["type"] = "Carrier";
		$where["company_status"] = "Active";
		$carriers = db_select_companys($where,"company_side_bar_name");
		$billed_under_options = array();
		$billed_under_options["Select"] = "Select";
		foreach($carriers as $carrier)
		{
			$billed_under_options[$carrier["id"]] = $carrier["company_side_bar_name"];
		}
		
		//GET OPTIONS FOR DRIVER MANAGER DROPDOWN LIST
		$where = null;
		//$where['role'] = "Driver Manager";
		$where = " role = 'Fleet Manager' OR role = 'Driver Manager' ";
		$driver_managers = db_select_persons($where);
		$dm_filter_dropdown_options = array();
		$dm_filter_dropdown_options['Select'] = "Select";
		foreach ($driver_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$dm_filter_dropdown_options[$manager['id']] = $title;
		}
		
		//GET BROKERS
		$where = null;
		$where = " status <> 'Bad' ";
		$brokers = db_select_customers($where,"customer_name");
		$broker_dropdown_options = array();
		$broker_dropdown_options["Select"] = "Select";
		foreach($brokers as $broker)
		{
			$broker_dropdown_options[$broker["id"]] = $broker["customer_name"];
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
		
		//GET ALL ACTIVE TRAILERS
		$where = null;
		$where["dropdown_status"] = "Show";
		$trailers = db_select_trailers($where,"trailer_number");
		$trailer_dropdown_options = array();
		$trailer_dropdown_options["Select"] = "Select";
		foreach($trailers as $trailer)
		{
			$trailer_dropdown_options[$trailer["id"]] = $trailer["trailer_number"];
		}
		
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
		$clients_dropdown_options["UNASSIGNED"] = "UNASSIGNED";
		
		//GET A/R SPECIALISTS
		//GET PERMISSION FOR MANAGING A/R
		$where = null;
		$where["permission_name"] = "manage A/R";
		$ar_permission = db_select_permission($where);
		
		//GET ALL USER_PERMISSIONS FOR THIS PERMISSION
		$where = null;
		$where["permission_id"] = $ar_permission["id"];
		$ar_user_permissions = db_select_user_permissions($where);
		$ars_dropdown_options = array();
		$ars_dropdown_options["Select"] = "Select";
		foreach($ar_user_permissions as $u_p)
		{
			//GET USER
			$where = null;
			$where["id"] = $u_p["user_id"];
			$ars_user = db_select_user($where);
			$ars_dropdown_options[$u_p["user_id"]] = $ars_user["person"]["f_name"];
		}
		
		//GET GOALPOINTS
		$where = null;
		$where = " load_id = $load_id AND gp_type <> 'Current Geopoint' ";
		$goalpoints = db_select_goalpoints($where,"gp_order");
		
		//GET ALL ATTACHMENTS FOR THIS TRAILER
		$where = null;
		$where['type'] = "load";
		$where['attached_to_id'] = $load['id'];
		$attachments = db_select_attachments($where);
		
		
		$data['attachments'] = $attachments;
		$data['goalpoints'] = $goalpoints;
		$data['ars_dropdown_options'] = $ars_dropdown_options;
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['billed_under_options'] = $billed_under_options;
		$data['broker_dropdown_options'] = $broker_dropdown_options;
		$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
		$data['dm_filter_dropdown_options'] = $dm_filter_dropdown_options;
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['trailer_dropdown_options'] = $trailer_dropdown_options;
		$data['load'] = $load;
		$this->load->view('billing/billing_load_details',$data);
	}
	
	//SAVE LOAD EDIT
	function save_load_edit()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		
		$load_id = $_POST["load_id"];
		$load_number = $_POST["edit_load_number"];
		$carrier_id = $_POST["edit_billed_under"];
		$fm_id = $_POST["edit_fleet_manager"];
		$dm_id = $_POST["edit_driver_manager"];
		$natl_fuel_avg = $_POST["edit_natl_avg"];
		$exp_rev = $_POST["edit_expected_rate"];
		$client_id = $_POST["edit_client"];
		$driver2_id = $_POST["edit_driver2"];
		$truck_id = $_POST["edit_truck"];
		$trailer_id = $_POST["edit_trailer"];
		$is_reefer = $_POST["edit_is_reefer"];
		//$reefer_low_set = $_POST["edit_reefer_low_set"];
		//$reefer_high_set = $_POST["edit_reefer_high_set"];
		$exp_miles = $_POST["edit_expected_miles"];
		$broker_id = $_POST["edit_broker"];
		$contact_info = $_POST["edit_contact_info"];
		$billing_method = $_POST["edit_billing_method"];
		$load_type = $_POST["edit_load_type"];
		$ar_specialist_id = $_POST["edit_ars"];
		$expected_pay_datetime = $_POST["expected_pay_date"];
		
		//GET THIS LOAD THAT WE ARE UPDATED
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		//IF NO CLIENT IS SELECTED KEEP IT AS NULL
		if($client_id  == "Select" || $client_id  == "UNASSIGNED")
		{
			$client_id = NULL;
		}
		
		//CREATE LOAD TO UPDATE
		$update_load = null;
		$update_load["customer_load_number"] = $load_number;
		$update_load["load_type"] = $load_type;
		$update_load["internal_load_number"] = "L".$load["id"]."-".$load_number;
		$update_load["fleet_manager_id"] = $fm_id;
		$update_load["dm_id"] = $dm_id;
		$update_load["is_reefer"] = $is_reefer;
		$update_load["reefer_low_set"] = $reefer_low_set;
		$update_load["reefer_high_set"] = $reefer_high_set;
		$update_load["load_truck_id"] = $truck_id;
		$update_load["load_trailer_id"] = $trailer_id;
		$update_load["client_id"] = $client_id;
		$update_load["driver2_id"] = $driver2_id;
		$update_load["billed_under"] = $carrier_id;
		$update_load["billing_method"] = $billing_method;
		$update_load['broker_id'] = $broker_id;
		$update_load["contact_info"] = $contact_info;
		$update_load["expected_miles"] = $exp_miles;
		$update_load["expected_revenue"] = $exp_rev;
		$update_load["natl_fuel_avg"] = $natl_fuel_avg;
		$update_load["ar_specialist_id"] = $ar_specialist_id;
		if(!empty($expected_pay_datetime))
		{
			$update_load["expected_pay_datetime"] = date("Y-m-d H:i",strtotime($expected_pay_datetime));
		}
		if(empty($load["ready_for_dispatch_datetime"]))
		{
			if($_POST["edit_ready_for_dispatch"] == "Ready")
			{
				$update_load["ready_for_dispatch_datetime"] = date("Y-m-d H:i");
			}
		}
		
		//UPDATE LOAD
		$where = null;
		$where["id"] = $load["id"];
		db_update_load($update_load,$where);
		
		//CHECK IF ARS CHANGED
		if($load["ar_specialist_id"] != $update_load["ar_specialist_id"])
		{
			//GET ARS
			$where = null;
			$where["id"] = $update_load["ar_specialist_id"];
			$user = db_select_user($where);
			
			//INSERT BILLING NOTE SAYING THAT LOAD IS MARKED COMPLETE AND ARS IS ASSINGED
			$insert_note = null;
			$insert_note["note_type"] = "load_billing";
			$insert_note["note_for_id"] = $load_id;
			$insert_note["note_datetime"] = date("Y-m-d H:i");
			$insert_note["user_id"] = $this->session->userdata('user_id');
			$insert_note["note_text"] = $user["person"]["f_name"]." was assigned to load as AR specialist";
			db_insert_note($insert_note);
		}
		
		//echo "saved";
	}
	
	function open_notes_dialog()
	{
		$load_id = $_POST["load_id"];
		
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		//GET NOTES
		$where = null;
		$where["note_type"] = "load_billing";
		$where["note_for_id"] = $load_id;
		$notes = db_select_notes($where,"note_datetime DESC");
		
		
		$data = null;
		$data["load"] = $load;
		$data["notes"] = $notes;
		$this->load->view('billing/add_notes_dialog',$data);
	}
	
	function save_note()
	{
		date_default_timezone_set('America/Denver');
		
		$load_id = $_POST["row_id"];
		$note_text = $_POST["new_note"];
		
		//INSERT NEW NOTE
		$insert_note = null;
		$insert_note["note_type"] = "load_billing";
		$insert_note["note_for_id"] = $load_id;
		$insert_note["note_datetime"] = date("Y-m-d H:i:s");
		$insert_note["user_id"] = $this->session->userdata('user_id');
		$insert_note["note_text"] = $note_text;
		db_insert_note($insert_note);
		
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		//GET NOTES
		$where = null;
		$where["note_type"] = "load_billing";
		$where["note_for_id"] = $load_id;
		$notes = db_select_notes($where,"note_datetime DESC");
		
		
		$data = null;
		$data["load"] = $load;
		$data["notes"] = $notes;
		$this->load->view('billing/add_notes_dialog',$data);
	}
	
	function load_process_audit_dialog()
	{
		$load_id = $_POST["row_id"];
		
		//GET LOAD
		$where = null;
		$where["load_id"] = $load_id;
		$process_audit = db_select_load_process_audit($where);
		
		$data["process_audit"] = $process_audit;
		$this->load->view('billing/process_audit_dialog',$data);
	}
	
	function load_file_upload_dialog()
	{
		$load_id = $_POST["row_id"];
		
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
			
		$upload_options = array();
		$upload_options["Select"] = "Select";
		$upload_options["rc_link"] = "Rate Con";
		$upload_options["signed_load_plan_guid"] = "Accepted Load Plan";
		$upload_options["unsigned_bol_guid"] = "Pre-drop BoL";
		$upload_options["no_originals_proof_guid"] = "No orignals required proof";
		$upload_options["bol_link"] = "Digital BoL";
		$upload_options["envelope_pic_guid"] = "Envelope Pic";
		$upload_options["dropbox_pic_guid"] = "Dropbox Pic";
		$upload_options["hc_guid"] = "BoL Scan";
		$upload_options["short_pay_report_guid"] = "Short Pay Report";
		$upload_options["Attachment"] = "Other Attachment";
			
		$data = null;
		$data["load"] = $load;
		$data["upload_options"] = $upload_options;
		$this->load->view('billing/billing_attachment_dialog',$data);
	}
	
	//UPLOAD EQUIPMENT ATTACHMENT
	function upload_load_attachment()
	{
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
		$post_name = 'attachment_file';
		$file = $_FILES[$post_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		//$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$title = $_POST["attachment_name"];
		$category = "Load Attachment";//TRUCK ATTACHEMENT OR TRAILER ATTACHMENT
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		//CREATE ATTACHMENT IN DB
		$attachment = null;
		$attachment["type"] = "Load";
		$attachment["attached_to_id"] = $_POST["load_id"];
		$attachment["file_guid"] = $secure_file["file_guid"];
		$attachment["attachment_name"] = $_POST["attachment_name"];

		db_insert_attachment($attachment);
		
		// $file_access_permission = null;
		// $file_access_permission['file_guid'] = $secure_file["file_guid"];
		// $file_access_permission['user_id'] = $this->session->userdata('user_id');
		// db_insert_file_access_permission($file_access_permission);
		
		if($_POST["upload_type"] != "Attachment")
		{
			$update = null;
			$update[$_POST["upload_type"]] = $secure_file["file_guid"];
			
			$where = null;
			$where["id"] = $_POST["load_id"];
			db_update_load($update,$where);
		}
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		//load_upload_success_view();
		echo "File uploaded successfully!";
	}
	
	//OPEN BILLING CHECKLIST DIALOG
	function open_billing_checklist_dialog()
	{
		$load_id = $_POST["load_number"];
		
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		$data['load'] = $load;
		$this->load->view('billing/billing_checklist_dialog',$data);
	}
	
	//UPDATE CHECKLIST STATUS
	function save_checklist_update()
	{
		date_default_timezone_set('America/Denver');
		$now_datetime = date("Y-m-d H:i:s");

		
		$load_id = $_POST["load_id"];
		//echo $load_id;
		
		
		$action =  $_POST["action_$load_id"];
		if($action == "process_audit")
		{
			//INSERT PROCESS AUDIT
			$insert_process_audit = null;
			$insert_process_audit["load_id"] = $load_id;
			$insert_process_audit["user_id"] = $this->session->userdata('user_id');
			$insert_process_audit["audit_datetime"] = $now_datetime;
			$insert_process_audit["defer_to_tarriff"] = $_POST["defer_to_tarriff"];
			$insert_process_audit["ontime_by_rc"] = $_POST["ontime_by_rc"];
			$insert_process_audit["shipper_load_and_count"] = $_POST["shipper_load_and_count"];
			$insert_process_audit["seal_pic_depart"] = $_POST["seal_pic_depart"];
			$insert_process_audit["load_pic_depart"] = $_POST["load_pic_depart"];
			$insert_process_audit["seal_number"] = $_POST["seal_number"];
			$insert_process_audit["seal_pic_arrive"] = $_POST["seal_pic_arrive"];
			$insert_process_audit["load_pic_arrive"] = $_POST["load_pic_arrive"];
			$insert_process_audit["seal_intact"] = $_POST["seal_intact"];
			$insert_process_audit["clean_bills"] = $_POST["clean_bills"];
			//$insert_process_audit["easy_sign_bills"] = $_POST["easy_sign_bills"];
			
			db_insert_load_process_audit($insert_process_audit);
			
			$update_load = null;
			if(	$_POST["defer_to_tarriff"] == "Fail" ||
				$_POST["ontime_by_rc"] == "Fail" ||
				$_POST["shipper_load_and_count"] == "Fail" ||
				$_POST["seal_pic_depart"] == "Fail" ||
				$_POST["load_pic_depart"] == "Fail" ||
				$_POST["seal_number"] == "Fail" ||
				$_POST["seal_pic_arrive"] == "Fail" ||
				$_POST["load_pic_arrive"] == "Fail" ||
				$_POST["seal_intact"] == "Fail" ||
				$_POST["clean_bills"] == "Fail"
			)
			{
				$update_load["process_audit"] = "Fail";
			}
			else
			{
				$update_load["process_audit"] = "Pass";
			}
			
			//UPDATE LOAD WITH PROCESS AUDIT RESULT
			$where = null;
			$where["id"] = $load_id;
			db_update_load($update_load,$where);
			
		}
		else
		{
			if($action == "digital")
			{
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
				$post_name = "dc_file_$load_id";
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = 'BOL DC '.date("Y-m-d H:i:s", time()).' '.$load['customer_load_number'];
				$category = "BOL DC";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = 'All';
				$driver_permission = 'None';
				$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
				
				$update_load["digital_received_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["digital_received_date_$load_id"]));
				$update_load["bol_link"] = $secure_file["file_guid"];
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				//CREATE ATTACHMENT IN DB
				$attachment = null;
				$attachment["type"] = "Load";
				$attachment["attached_to_id"] = $load_id;
				$attachment["file_guid"] = $secure_file["file_guid"];
				$attachment["attachment_name"] = "BOL Pic";

				db_insert_attachment($attachment);
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "BOL Pic uploaded";
				db_insert_note($insert_note);
			}
			else if($action == "billed")
			{
				$update_load["billing_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["invoice_billed_date_$load_id"]));
				$update_load["amount_billed"] = $_POST["amount_billed_$load_id"];
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "Invoice billed";
				db_insert_note($insert_note);
				
				// //INSERT PROPER ACCOUNT ENTRIES
				// $entries = array();
				
				
				// //GET COOP COMPANY
				// $where = null;
				// $where["category"] = "Coop";
				// $coop_company = db_select_company($where);
				
				
				// //GET RELATIONSHIP BETWEEN COOP AND BROKER
				// $where = null;
				// $where["business_id"] = $coop_company["id"];
				// $where["related_business_id"] = $broker_customer["company_id"];
				// $coop_broker_relationship = db_select_business_relationship($where);
				
				// //GET BROKER A/R ACCOUNT
				// $where = null;
				// $where["account_class"] = "Asset";
				// $where["account_type"] = "Holding";
				// $where["relationship_id"] = $coop_broker_relationship["id"];
				// $broker_ar_account = db_select_account($where);
				
				// //echo $broker_ar_account["account_name"];
				
				// //DEBIT A/R ACCOUNT FROM BROKER ()
				// $debit_entry = null;
				// $debit_entry["account_id"] = $broker_ar_account["id"];
				// $debit_entry["recorder_id"] = $recorder_id;
				// $debit_entry["recorded_datetime"] = $recorded_datetime;
				// $debit_entry["entry_datetime"] = $load["final_drop_datetime"];
				// $debit_entry["debit_credit"] = "Debit";
				// $debit_entry["entry_amount"] = round($_POST["amount_billed_$load_id"],2);
				// $debit_entry["entry_description"] = "Invoice for payment on load ".$load["customer_load_number"];
				
				// $entries[] = $debit_entry;
				
				// //GET GENERIC A/P HOLDING ACCOUNT
				// $where = null;
				// $where["company_id"] = $coop_company["id"];
				// $where["category"] = "A/P to Members on Settlements";
				// $coop_default_settlement_ap_account = db_select_default_account($where);
				
				// //CREDIT GENERIC A/P TO MEMBERS ACCOUNT (INSTEAD OF REV ACCOUNT)
				// $credit_entry = null;
				// $credit_entry["account_id"] = $coop_default_settlement_ap_account["account_id"];
				// $credit_entry["recorder_id"] = $recorder_id;
				// $credit_entry["recorded_datetime"] = $recorded_datetime;
				// $credit_entry["entry_datetime"] = $load["final_drop_datetime"];
				// $credit_entry["debit_credit"] = "Credit";
				// $credit_entry["entry_amount"] = round($_POST["amount_billed_$load_id"],2);
				// $credit_entry["entry_description"] = "Amount due to members for load ".$load["customer_load_number"];
				
				// $entries[] = $credit_entry;
				
				// //INSERT TRANSACTION INTO DB
				// $transaction = null;
				// $transaction["category"] = "Load Billed";
				// $transaction["description"] = "Freight Invoice Billed";

				// create_transaction_and_entries($transaction,$entries);
				
			}
			else if($action == "funded")
			{
				//$update_load["funded_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["invoice_funded_date_$load_id"]));
				$update_load["amount_funded"] = $_POST["amount_funded_$load_id"];
				$update_load["invoice_number"] = $_POST["invoice_number_$load_id"];
				$update_load["financing_cost"] = $_POST["finance_cost_$load_id"];
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				$load = db_select_load($where);
				
				$update_load = null;
				$update_load["amount_short_paid"] = ($load["amount_billed"] - ($load["financing_cost"] + $load["amount_funded"]));
				db_update_load($update_load,$where);
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "Load Funded for $".number_format($_POST["amount_funded_$load_id"],2);
				db_insert_note($insert_note);
			}
			elseif($action == "envelope")
			{
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
				$post_name = "envelope_file_$load_id";
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = 'Envelope Pic '.date("Y-m-d H:i:s", time()).' '.$load['customer_load_number'];
				$category = "Envelope Pic";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = 'All';
				$driver_permission = 'None';
				$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
				
				$update_load["envelope_pic_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["envelope_pic_date_$load_id"]));
				$update_load["envelope_pic_guid"] = $secure_file["file_guid"];
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				//CREATE ATTACHMENT IN DB
				$attachment = null;
				$attachment["type"] = "Load";
				$attachment["attached_to_id"] = $load_id;
				$attachment["file_guid"] = $secure_file["file_guid"];
				$attachment["attachment_name"] = "Envelope Pic";

				db_insert_attachment($attachment);
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "Envelope Pic uploaded";
				db_insert_note($insert_note);
			}
			elseif($action == "dropbox")
			{
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
				$post_name = "dropbox_file_$load_id";
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = 'Dropbox Pic '.date("Y-m-d H:i:s", time()).' '.$load['customer_load_number'];
				$category = "Dropbox Pic";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = 'All';
				$driver_permission = 'None';
				$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
				
				$update_load["dropbox_pic_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["dropbox_date_$load_id"]));
				$update_load["dropbox_pic_guid"] = $secure_file["file_guid"];
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				//CREATE ATTACHMENT IN DB
				$attachment = null;
				$attachment["type"] = "Load";
				$attachment["attached_to_id"] = $load_id;
				$attachment["file_guid"] = $secure_file["file_guid"];
				$attachment["attachment_name"] = "Dropbox Pic";

				db_insert_attachment($attachment);
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "Dropbox Pic uploaded";
				db_insert_note($insert_note);
			}
			else if($action == "hc_processed")
			{
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
				$post_name = "hc_file_$load_id";
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = 'BOL Scan '.date("Y-m-d H:i:s", time()).' '.$load['customer_load_number'];
				$category = "BOL Scan";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = 'All';
				$driver_permission = 'None';
				$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
			
				$update_load["hc_processed_datetime"] = date("Y-m-d G:i:s",strtotime($now_datetime));
				$update_load["hc_guid"] = $secure_file["file_guid"];
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "BOL Scanned uploaded";
				db_insert_note($insert_note);
			}
			else if($action == "hc_sent")
			{
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
				$post_name = "hc_sent_proof_$load_id";
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = 'BOL Sent Proof '.date("Y-m-d H:i:s", time()).' '.$load['customer_load_number'];;
				$category = "BOL Sent Proof";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = 'All';
				$driver_permission = 'None';
				$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
			
				$update_load["hc_sent_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["hc_sent_date_$load_id"]));
				$update_load["hc_sent_proof_guid"] = $secure_file["file_guid"];
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "Originals requested to be sent";
				db_insert_note($insert_note);
			}
			else if($action == "hc_received")
			{
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
				$post_name = "hc_received_proof_$load_id";
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = 'BOL Delivered Proof '.date("Y-m-d H:i:s", time()).' '.$broker_customer["customer_name"].' '.$load['customer_load_number'];
				$category = "BOL Delivered Proof";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = 'All';
				$driver_permission = 'None';
				$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
			
				$update_load["hc_received_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["hc_received_date_$load_id"]));
				$update_load["hc_received_proof_guid"] = $secure_file["file_guid"];
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "Originals Delivered";
				db_insert_note($insert_note);
			}
			else if($action == "hold")
			{
				if($_POST["hold_reason"] == "No Hold")
				{
					$update_load["denied_datetime"] = null;
					$update_load["denied_reason"] = null;
					
					//INSERT NEW NOTE
					$insert_note = null;
					$insert_note["note_type"] = "load_billing";
					$insert_note["note_for_id"] = $load_id;
					$insert_note["note_datetime"] = date("Y-m-d H:i");
					$insert_note["user_id"] = $this->session->userdata('user_id');
					$insert_note["note_text"] = "Hold Removed ".date("m/d/y",strtotime($_POST["hold_date_$load_id"]))." | ".$_POST["hold_notes"];
					db_insert_note($insert_note);
				}
				else
				{
					$update_load["denied_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["hold_date_$load_id"]));
					$update_load["denied_reason"] = $_POST["hold_reason"];
					
					//INSERT NEW NOTE
					$insert_note = null;
					$insert_note["note_type"] = "load_billing";
					$insert_note["note_for_id"] = $load_id;
					$insert_note["note_datetime"] = date("Y-m-d H:i");
					$insert_note["user_id"] = $this->session->userdata('user_id');
					$insert_note["note_text"] = "Invoice on Hold | ".$_POST["hold_notes"];
					db_insert_note($insert_note);
				}
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
			}
			else if($action == "recoursed")
			{
				$update_load["denied_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["recourse_date_$load_id"]));
				$update_load["recoursed_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["recourse_date_$load_id"]));
				$update_load["denied_reason"] = $_POST["recourse_reason"];
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "Invoice Recoursed | ".$_POST["recourse_notes"];
				db_insert_note($insert_note);
				
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
			}
			else if($action == "reimbursed")
			{
				$update_load["reimbursed_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["reimbursed_date_$load_id"]));
				$update_load["amount_funded"] = $_POST["amount_reimbursed_$load_id"];
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
				
				$load = db_select_load($where);
				
				$update_load = null;
				$update_load["amount_short_paid"] = ($load["amount_billed"] - ($load["financing_cost"] + $load["amount_funded"]));
				db_update_load($update_load,$where);
				
				//INSERT NEW NOTE
				$insert_note = null;
				$insert_note["note_type"] = "load_billing";
				$insert_note["note_for_id"] = $load_id;
				$insert_note["note_datetime"] = date("Y-m-d H:i");
				$insert_note["user_id"] = $this->session->userdata('user_id');
				$insert_note["note_text"] = "Load Reimbursed for $".number_format($_POST["amount_reimbursed_$load_id"],2);
				db_insert_note($insert_note);
			}
			else if($action == "invoice_closed")
			{
				if(user_has_permission('close out load in billing'))
				{
					$update_load["invoice_closed_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["reimbursed_date_$load_id"]));
					$where = null;
					$where["id"] = $load_id;
					db_update_load($update_load,$where);
					
					//INSERT NEW NOTE
					$insert_note = null;
					$insert_note["note_type"] = "load_billing";
					$insert_note["note_for_id"] = $load_id;
					$insert_note["note_datetime"] = date("Y-m-d H:i");
					$insert_note["user_id"] = $this->session->userdata('user_id');
					$insert_note["note_text"] = "Load marked Closed";
					db_insert_note($insert_note);
				}
			}
			
			//UPDATE BILLING STATUS
			$billing_status = determine_billing_status($load_id);
			$update_load = null;
			$update_load["billing_status"] = $billing_status["status"];
			$update_load["billing_status_number"] = $billing_status["status_number"];
			$where = null;
			$where["id"] = $load_id;
			db_update_load($update_load,$where);
		}
		
		echo "Success!";
	}
	
	//CREATE DIGITAL COVER SHEET
	function hc_coversheet($load_id)
	{
		
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		$data['title'] = "HC Coversheet";
		$data['load'] = $load;
		$this->load->view('billing/hc_cover_sheet_view',$data);
	}
	
	
	
	
	
	
	//ONE-TIME SCRIPTS ---------------------------------------------------
	
	//SQL SCRIPT
	//UPDATE `load` SET `pushed_datetime` = final_drop_datetime WHERE status = 'Dropped'
	
	function update_funding_loads()
	{
		// $where = null;
		// $where["billing_status"] = "Funding";
		// $loads = db_select_loads($where);
		
		// foreach($loads as $load)
		// {
			// $load_id = $load["id"];
			// //UPDATE BILLING STATUS
			// $billing_status = determine_billing_status($load_id);
			// $update_load = null;
			// $update_load["billing_status"] = $billing_status["status"];
			// $update_load["billing_status_number"] = $billing_status["status_number"];
			// $where = null;
			// $where = null;
			// $where["id"] = $load_id;
			// db_update_load($update_load,$where);
		
			// echo $load["customer_load_number"]."<br>";
		// }
		
	}
	
	function update_envelope_loads()
	{
		// $where = null;
		// $where["billing_status"] = "Envelope";
		// $loads = db_select_loads($where);
		
		// foreach($loads as $load)
		// {
			// $load_id = $load["id"];
			// //UPDATE BILLING STATUS
			// $billing_status = determine_billing_status($load_id);
			// $update_load = null;
			// $update_load["billing_status"] = $billing_status["status"];
			// $update_load["billing_status_number"] = $billing_status["status_number"];
			// $where = null;
			// $where = null;
			// $where["id"] = $load_id;
			// db_update_load($update_load,$where);
		
			// echo $load["customer_load_number"]."<br>";
		// }
		
	}
	
	//UPDATE BILLING STATUS FOR ALL LOADS
	function update_billing_statuses()
	{
		echo "start process<br>";
		
		//GET ALL LOADS
		//$where = null;
		//$where = "status_number <> 100";
		//$loads = db_select_loads($where);
		$sql = "Select id from `load` WHERE status_number <> 100";
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $this->db->query($sql);
		$loads = array();
		foreach ($query->result() as $row)
		{
			$load_id = $row->id;
			
			echo $load_id."<br>";
			
			//UPDATE BILLING STATUS
			$billing_status = determine_billing_status($load_id);
			$update_load = null;
			$update_load["billing_status"] = $billing_status["status"];
			$update_load["billing_status_number"] = $billing_status["status_number"];
			$where = null;
			$where["id"] = $load_id;
			//db_update_load($update_load,$where);
		}
		
		
		// foreach($loads as $load)
		// {
			// $load_id = $load["id"];
			
			// echo $load_id."<br>";
			
			// //UPDATE BILLING STATUS
			// $billing_status = determine_billing_status($load_id);
			// $update_load = null;
			// $update_load["billing_status"] = $billing_status["status"];
			// $update_load["billing_status_number"] = $billing_status["status_number"];
			// $where = null;
			// $where["id"] = $load_id;
			// db_update_load($update_load,$where);
		// }
	}
	
}//	END LOADS CLASS
?>