<?php

class Loads extends MY_Controller 
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
		
		//GET ALL ACTIVE TRAILERS
		$where = null;
		$where["dropdown_status"] = "Show";
		$trailers = db_select_trailers($where,"trailer_number");
		$trailer_dropdown_options = array();
		$trailer_dropdown_options["All"] = "All Trailers";
		foreach($trailers as $trailer)
		{
			$trailer_dropdown_options[$trailer["id"]] = $trailer["trailer_number"];
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
		
		//GET ALL THE BROKERS
		$where = null;
		$where["status"] = "Good";
		$brokers = db_select_customers($where,"customer_name");
		
		$data['brokers'] = $brokers;
		$data['billed_under_options'] = $billed_under_options;
		$data['broker_dropdown_options'] = $broker_dropdown_options;
		$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
		$data['dm_filter_dropdown_options'] = $dm_filter_dropdown_options;
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['trailer_dropdown_options'] = $trailer_dropdown_options;
		$data['tab'] = 'Loads';
		$data['title'] = "Loads";
		$this->load->view('loads_view',$data);
	}
	
	//LOAD REPORT
	function load_list()
	{
		//GET FILTER PARAMETERS
		$fleet_manager_dropdown = $_POST["fleet_managers_dropdown"];
		$driver_manager_dropdown = $_POST["dm_filter_dropdown"];
		$broker_dropdown = $_POST["broker_dropdown"];
		$carrier_filter = $_POST["carrier_filter"];
		$drop_start_date = $_POST["drop_start_date_filter"];
		$drop_end_date = $_POST["drop_end_date_filter"];
		$truck_filter_dropdown = $_POST["truck_filter_dropdown"];
		$trailer_filter_dropdown = $_POST["trailer_filter_dropdown"];
		$status = $_POST["load_status_dropdown"];
		
		
		//CREATE WHERE CLAUSE FOR LOAD QUERY
		$where = " AND 1 = 1";
		
		
		//SET WHERE FOR BROKER
		if($broker_dropdown != "All")
		{
			$where = $where." AND broker_id = ".$broker_dropdown;
		}
		
		//SET WHERE FOR CARRIER
		if($carrier_filter != "All")
		{
			$where = $where." AND billed_under = ".$carrier_filter;
		}
		
		//SET WHERE FOR FM
		if($fleet_manager_dropdown != "All")
		{
			$where = $where." AND fleet_manager_id = ".$fleet_manager_dropdown;
		}
		
		//SET WHERE FOR DM
		if($driver_manager_dropdown != "All")
		{
			$where = $where." AND dm_id = ".$driver_manager_dropdown;
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
		
		//SET WHERE FOR TRUCK
		if($truck_filter_dropdown != "All")
		{
			$where = $where." AND load_truck_id = ".$truck_filter_dropdown;
		}
		
		//SET WHERE FOR TRUCK
		if($trailer_filter_dropdown != "All")
		{
			$where = $where." AND load_trailer_id = ".$trailer_filter_dropdown;
		}
		
		
		
		if($status == "active")
		{
			$where = $where." AND status_number < 5";
		}
		else if($status == "booked")
		{
			$where = $where." AND status_number < 3";
		}
		else if($status == "in_transit")
		{
			$where = $where." AND status = 'Drop Pending'";
		}
		else if($status == "dropped")
		{
			$where = $where." AND status = 'Dropped'";
		}
		else
		{
			$where = $where." AND status_number > 0";
		}
		
		//SEARCH
		//echo mysql_real_escape_string($_POST["search_term"]);
		if(!empty($_POST["search_term"]))
		{
			$search = mysql_real_escape_string($_POST["search_term"]);
			$where = "     customer_load_number LIKE '%$search%' ";//NEEDED TO ADD 4 BLANKS FOR SUBSTR
		}
		
		
		$where = substr($where,4);
		//echo $where;
		$loads = db_select_loads($where,"status_number ",200);
		
		//GET COUNT OF ACTIVE LOADS
		$sql = "Select count(*) as active_count FROM `load` WHERE ".$where." AND load_truck_id IS NOT NULL";
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $this->db->query($sql);
		$active_count = 0;
		foreach ($query->result() as $row)
		{
			$active_count = $row->active_count;
		}
		
		$where = null;
		$where['permission_id'] = 84;
		$users = db_select_user_permissions($where);
		
		$ricky_dispatchers = array();
		$ezra_dispatchers = array();
		$james_dispatchers = array();
		$taylor_dispatchers = array();
		if(!empty($users))
		{
			
			foreach($users as $user)
			{
				$where = null;
				$where['id'] = $user['user_id'];
				$this_user = db_select_user($where);

				//GET LAST PUNCH
				$where = null;
				$where["user_id"] = $this_user["id"];
				$last_punch = db_select_time_punch($where,"datetime");

				if($last_punch['in_out'] == 'In')
				{
//					echo $user['id'] . "<br>";
					$where = null;
					$where['id'] = $this_user['person_id'];
					$person = db_select_person($where);

					$where = null;
					$where['person_id'] = $person['id'];
					$dispatcher = db_select_company($where);
					$where = null;
					$where['id'] = $dispatcher['managed_by_id'];
					$fleet_manager = db_select_company($where);

					$where = null;
					$where['id'] = $fleet_manager['person_id'];
					$fleet_manager_person = db_select_person($where);
//					print_r($fleet_manager_person);
					
					if(!empty($fleet_manager_person))
					{
						if($fleet_manager_person['id'] == 149)
						{
							$ricky_dispatchers[] = ucfirst(substr($this_user['person']['f_name'],0,1).substr($this_user['person']['l_name'],0,1));
						}//Ricky
						elseif($fleet_manager_person['id'] == 660)
						{
							$ezra_dispatchers[] = ucfirst(substr($this_user['person']['f_name'],0,1).substr($this_user['person']['l_name'],0,1));
						}//Ezra
						elseif($fleet_manager_person['id'] == 794)
						{
							$james_dispatchers[] = ucfirst(substr($this_user['person']['f_name'],0,1).substr($this_user['person']['l_name'],0,1));
						}//James
						elseif($fleet_manager_person['id'] == 880)
						{
							$taylor_dispatchers[] = ucfirst(substr($this_user['person']['f_name'],0,1).substr($this_user['person']['l_name'],0,1));
						}//Taylor
					}
				}
			}
		}

//		print_r($ricky_dispatchers);
		$data['ricky_dispatchers'] = $ricky_dispatchers;
		$data['ezra_dispatchers'] = $ezra_dispatchers;
		$data['james_dispatchers'] = $james_dispatchers;
		$data['taylor_dispatchers'] = $taylor_dispatchers;
		$data['active_count'] = $active_count;
		$data['loads'] = $loads;
		$this->load->view('loads/loads_report',$data);
	}
	
	//LOAD ROW
	function refresh_row()
	{
		$load_id = $_POST["load_id"];
		
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		$data['load'] = $load;
		$this->load->view('loads/load_row',$data);
	}
	
	//LOAD THE ADD NEW LOAD DIALOG
	function load_add_new_load_dialog()
	{
		//GET FLEET MANAGERS
		$where = null;
		//$where["role"] = "Fleet Manager";
		$where = " role = 'Fleet Manager' OR role = 'Driver Manager' ";
		$fleet_managers = db_select_persons($where,"f_name");
		$fleet_managers_dropdown_options = array();
		$fleet_managers_dropdown_options["Select"] = "Select";
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
		$dm_dropdown_options = array();
		$dm_dropdown_options['Select'] = "Select";
		foreach ($driver_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$dm_dropdown_options[$manager['id']] = $title;
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
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$clients_dropdown_options = array();
		$clients_dropdown_options["Select"] = "Select";
		foreach($dd_all_clients as $client)
		{
			$clients_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		$clients_dropdown_options["UNASSIGNED"] = "UNASSIGNED";
		
		
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['billed_under_options'] = $billed_under_options;
		$data['broker_dropdown_options'] = $broker_dropdown_options;
		$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
		$data['dm_dropdown_options'] = $dm_dropdown_options;
		$this->load->view('loads/add_new_load_dialog',$data);
	}
	
	//SEARCH FOR BROKER IN ADD NEW LOAD DIALOG
	function search_for_broker()
	{
		$mc_number = $_POST["mc_number"];
		
		$where = null;
		$where["mc_number"] = $mc_number;
		$customer = db_select_customer($where);
		
		if(empty($customer))
		{
			echo "No broker found";
		}
		else
		{
			echo $customer["customer_name"];
		}
	}
	
	//ADD NEW LOAD DIALOG SAVE
	function add_new_load()
	{
		//SET TIMEZONE
		date_default_timezone_set('US/Mountain');
		
		//CREATE NEW BROKER IF NEEDED
		@$broker_is_new = $_POST["broker_is_new"];
		if($broker_is_new == "new")
		{
			//CREATE COMPANY
			$company["type"] = "Broker";
			$company["category"] = "Broker";
			$company["company_status"] = "Active";
			$company["company_name"] = $_POST["broker_name"];
			$company["company_side_bar_name"] = $_POST["broker_name"];
			
			db_insert_company($company);
			$company = db_select_company($company);
			
			//INSERT NEW CUSTOMER INTO DB
			$broker = null;
			$broker["company_id"] = $company["id"];
			$broker["customer_name"] = $_POST["broker_name"];
			$broker["mc_number"] = $_POST["broker_mc"];
			$broker["status"] = "Good";
			db_insert_customer($broker);
			
			//GET COOP COMPANY
			$where = null;
			$where["category"] = "Coop";
			$coop_company = db_select_company($where);
			
			//CREATE BUSINESS RELATIONSHIP
			$relationship = null;
			$relationship["business_id"] = $coop_company["id"];
			$relationship["relationship"] = "Member Customer";
			$relationship["related_business_id"] = $company["id"];
			db_insert_business_relationship($relationship);
			
			$coop_broker_relationship = db_select_business_relationship($relationship);
			
			//GET DEFAULT ACCOUNT FOR A/R FROM BROKERS ON LOADS HAULED
			$where = null;
			$where["company_id"] = $coop_company["id"];
			$where["category"] = "A/R from Brokers on Loads Hauled";
			$coop_ar_from_brokers_default_account = db_select_default_account($where);
			
			//GET A/R ACCOUNT
			$where = null;
			$where["id"] = $coop_ar_from_brokers_default_account["account_id"];
			$parent_account = db_select_account($where);
			
			//CREATE A/R ACCOUNT WITH COOP
			$account = null;
			$account["company_id"] = $coop_company["id"];
			$account["relationship_id"] = $coop_broker_relationship["id"];
			$account["account_type"] = "Holding";
			$account["account_class"] = "Asset";
			$account["category"] = $parent_account["category"];
			$account["account_status"] = "Open";
			$account["account_name"] = "A/R from ".$company["company_side_bar_name"];
			$account["parent_account_id"] = $parent_account["id"];
			db_insert_account($account);
			
			//GET NEWLY CREATED ACCOUNT
			$newly_created_account = db_select_account($account);
			
			//SET ACCOUNT AS DEFAULT A/R ON ARROWHEAD FLEETPROTECT DEPOSIT
			$default_acc = null;
			$default_acc["company_id"] = $company["id"];
			$default_acc["account_id"] = $newly_created_account["id"];
			$default_acc["type"] = "Broker";
			$default_acc["category"] = "Coop A/R on Loads Hauled";
			db_insert_default_account($default_acc);
		}
		else
		{
			//GET CLIENT WITH GIVEN MC
			$where = null;
			$where["mc_number"] = $_POST["broker_mc"];
			$customer = db_select_customer($where);
			
			//GET COMPANY
			$where = null;
			$where["id"] = $customer["company_id"];
			$company = db_select_company($where);
			
		}
		
		
		//ADD A BLANK LOAD TO THE DB
		$blank_load["fleet_manager_id"] = $_POST['new_load_fm_dropdown'];
		$blank_load["dm_id"] = $_POST['new_load_dm_dropdown'];
		$blank_load["booking_datetime"] = date("Y-m-d H:i:s", time());
		$blank_load['status_number'] = 1;
		$blank_load['status'] = 'Rate Con Pending';
		$blank_load['internal_load_number'] = get_random_string(10);
		db_insert_load($blank_load);

		//GET NEWLY CREATED LOAD FROM THE DB
		$internal_load_number = $blank_load["internal_load_number"];
		$blank_load_where["internal_load_number"] = $internal_load_number;
		$blank_load = null;
		$blank_load = db_select_load($blank_load_where);
		
		//GET BROKER
		$where = null;
		$where['company_id'] = $company['id'];
		$broker = db_select_customer($where);
		
		//UPDATE NEWLY CREATED LOAD
		$load = null;
		
		$load['broker_id'] = $broker['id'];
		
		//UPDATE NEW LOAD WITH INTERNAL LOAD NUMBER
		$this_load_number = "L".$blank_load["id"];
		$load['internal_load_number'] = $this_load_number;
		
		if(!empty($_FILES['new_load_file']["type"]))
		{
			//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
			$post_name = 'new_load_file';
			$file = $_FILES[$post_name];
			$name = str_replace(' ','_',$file["name"]);
			$type = $file["type"];
			//$title = pathinfo($file["name"], PATHINFO_FILENAME);
			$title = 'RC '.date("Y-m-d H:i:s", time()).' '.$broker["customer_name"].' '.$this_load_number;
			$category = "Rate Con";
			$local_path = $file["tmp_name"];
			$server_path = '/edocuments/';
			$office_permission = 'All';
			$driver_permission = 'None';
			$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
			
			//UPDATE LOAD WITH FILE GUID FOR RC LINK
			$load["rc_link"] = $secure_file["file_guid"];
			
			//CREATE ATTACHMENT IN DB
			$attachment = null;
			$attachment["type"] = "load";
			$attachment["attached_to_id"] = $blank_load["id"];
			$attachment["file_guid"] = $secure_file["file_guid"];
			$attachment["attachment_name"] = "Rate Con";

			db_insert_attachment($attachment);
		}
		

		
		//GET CLIENT
		if($_POST["client_id"] == "Select" || $_POST["client_id"] == "UNASSIGNED")
		{
			//DO NOTHING
		}
		else
		{
			$load['client_id'] = $_POST["client_id"];
		}
		
		$load['expected_miles'] = $_POST['expected_miles'];	
		$load['expected_revenue'] = $_POST['expected_revenue'];
		$load['load_desc'] = $_POST['load_notes'];
		$load['billing_status'] = "Booked";
		$load['billing_status_number'] = 1;
		$load['billed_under'] = $_POST['carrier_id'];
		$load['originals_required'] = $_POST['originals_required'];
		
		$load_where['id'] = $blank_load["id"];
		db_update_load($load,$load_where);
		
		if(!empty($_POST["proof_notes"]))
		{
			//ADD NOTES TO LOAD ABOUT WHERE TO FIND PROOF
			$text = "PROOF NOTES: ".$_POST["proof_notes"];
			$initials = substr($this->session->userdata('first_name'),0,1).substr($this->session->userdata('last_name'),0,1);
			$date_text = date("m/d/y H:i");
			
			$full_note = $date_text." - ".$initials." | ".$text."\n\n";
			
			//SELECT
			$where = null;
			$where['id'] = $blank_load["id"];;
			$load = db_select_load($where);
			
			//UPDATE
			$where = null;
			$where['id'] = $load["id"];
			$update = null;
			$update["load_notes"] = $full_note.$load["load_notes"];
			db_update_load($update,$where);
		}
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		load_upload_success_view();
	
	}//END ADD NEW LOAD
	
	//CANCEL LOAD WITH REASON
	function cancel_load()
	{
		$load_id =  $_POST["cancelled_load_id"];
		$reason =  $_POST["load_cancel_reason"];
		
		
		//echo $load_id;
		//echo $reason;
		
		$text = $reason;
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		date_default_timezone_set('America/Denver');
		$date_text = date("m/d/y");
		
		$full_note = $date_text." - ".$initials." - ".$text."\n";
		
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		$update_load["load_notes"] = $load["load_notes"].$full_note;
		$update_load["status"] = "Cancelled";
		$update_load["status_number"] = "100";
		db_update_load($update_load,$where);
		
	}
	
	//GET NOTES FOR SPECIFIED LOAD
    function get_notes($load_id)
    {
        $where = null;
        $where['id'] = $load_id;
        $load = db_select_load($where);
        //echo $lead['id'];
        $data['load'] = $load;
        $this->load->view('loads/load_notes_div',$data);
    }//end get_notes
	
	//SAVE NOTE
    function save_note()
    {
        $load_id = $_POST["row_id"];
        
        $text = $_POST["new_note"];
        $initials = substr($this->session->userdata('first_name'),0,1).substr($this->session->userdata('last_name'),0,1);
        date_default_timezone_set('America/Denver');
        $date_text = date("m/d/y H:i");
        
        $full_note = $date_text." - ".$initials." | ".$text."\n\n";
        
		//SELECT
		$where = null;
        $where['id'] = $load_id;
        $load = db_select_load($where);
        
		//UPDATE
		$where = null;
        $where['id'] = $load_id;
		$update = null;
        $update["load_notes"] = $full_note.$load["load_notes"];
        db_update_load($update,$where);
        
		// echo $full_note;
        $this->get_notes($load_id);
        
        // echo $update_load["settlement_notes"];
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
		$upload_options["signed_load_plan_guid"] = "Accepted Load Plan";
		$upload_options["unsigned_bol_guid"] = "Pre-drop BoL";
		$upload_options["rc_link"] = "Rate Con";
		$upload_options["Attachment"] = "Other Attachment";
			
		$data = null;
		$data["load"] = $load;
		$data["upload_options"] = $upload_options;
		$this->load->view('loads/loads_attachment_dialog',$data);
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
		load_upload_success_view();
	}
	
	//OPEN LOAD DETAILS
	function open_details()
	{
		$load_id = $_POST["load_id"];
		
		//update_current_goalpoint_from_geopoint($load_id);
		
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		//FUNCTION FOR TRANSITION AFTER CODE UPDATE: CREATES GEOPOINTS FOR PICKS AND DROPS THAT WOULD HAVE BEEN CREATED ON THE RCR SAVE
		create_geopoints_for_load($load);
		
		//UPDATE LOAD STATUS
		update_load_status($load);
		
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
		$truck_dropdown_options["Select"] = "Select";
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
		
		//GET ALL DISPATCH UPDATES
		$where = null;
		$where["load_id"] = $load_id;
		$dispatch_updates = db_select_dispatch_updates($where,"update_datetime");
		
		//GET ALL DISPATCH UPDATES
		$where = null;
		$where["load_id"] = $load_id;
		$load_check_calls = db_select_load_check_calls($where,"recorded_datetime");
		
		
		//GET ALL ATTACHMENTS FOR THIS TRAILER
		$where = null;
		$where['type'] = "load";
		$where['attached_to_id'] = $load['id'];
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data['load_check_calls'] = $load_check_calls;
		$data['dispatch_updates'] = $dispatch_updates;
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['billed_under_options'] = $billed_under_options;
		$data['broker_dropdown_options'] = $broker_dropdown_options;
		$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
		$data['dm_filter_dropdown_options'] = $dm_filter_dropdown_options;
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['trailer_dropdown_options'] = $trailer_dropdown_options;
		$data['load'] = $load;
		$this->load->view('loads/load_details',$data);
	}
	
	//LOAD GOALPOINTS
	function load_goalpoints_div()
	{
		$load_id = $_POST["load_id"];
		
		update_current_goalpoint_from_geopoint($load_id);
		calc_expected_gp_times($load_id);
		
		//GET GOALPOINTS
		$where = null;
		$where["load_id"] = $load_id;
		$goalpoints = db_select_goalpoints($where,"gp_order");
		
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
		
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['trailer_dropdown_options'] = $trailer_dropdown_options;
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['goalpoints'] = $goalpoints;
		$this->load->view('loads/goalpoints_div',$data);
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
		$truck_id = $_POST["edit_truck"];
		$trailer_id = $_POST["edit_trailer"];
		$is_reefer = $_POST["edit_is_reefer"];
		$reefer_low_set = $_POST["edit_reefer_low_set"];
		$reefer_high_set = $_POST["edit_reefer_high_set"];
		$exp_miles = $_POST["edit_expected_miles"];
		$broker_id = $_POST["edit_broker"];
		$contact_info = $_POST["edit_contact_info"];
		$billing_method = $_POST["edit_billing_method"];
		$load_type = $_POST["edit_load_type"];
		
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
		$update_load["billed_under"] = $carrier_id;
		$update_load["billing_method"] = $billing_method;
		$update_load['broker_id'] = $broker_id;
		$update_load["contact_info"] = $contact_info;
		$update_load["expected_miles"] = $exp_miles;
		$update_load["expected_revenue"] = $exp_rev;
		$update_load["natl_fuel_avg"] = $natl_fuel_avg;
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
		
		//echo "saved";
	}
	
	//CHECK IF TRUCK IS ALREADY ASSIGNED TO LOAD
	function check_if_truck_is_assigned()
	{
		$truck_id = $_POST["truck_id"];
		$load_id = $_POST["load_id"];
		
		if($truck_id != "Select")
		{
			//GET TRUCKS THAT ARE CURRENTLY ASSIGNED TO LOADS
			$where = null;
			$where = " status_number < 5 AND load_truck_id = $truck_id and `load`.id <> '$load_id' ";
			$loads_for_truck = db_select_loads($where);
			
			//echo $load_id;
			
			if(empty($loads_for_truck))
			{
				echo "<script>$('#truck_is_already_assigned').val('no')</script>";
			}
			else
			{
				echo "<script>alert('This truck is already assigned to another active load!!')</script>";
				echo "<script>$('#truck_is_already_assigned').val('yes')</script>";
			}
		}
		
		
	}
	
	//ADD NEW GOALPOINT
	function add_new_goalpoint()
	{
		$load_id = $_POST["add_new_gp_load_id"];
		$truck_id = $_POST["temp_gp_truck"];
		$trailer_id = $_POST["temp_gp_trailer"];
		$client_id = $_POST["temp_gp_client"];
		$deadline = $_POST["temp_deadline"];
		$gp_type = $_POST["temp_gp_type"];
		$gps = $_POST["temp_gp_gps"];
		$location_name = $_POST["temp_gp_location_name"];
		$dm_notes = $_POST["temp_gp_notes"];
		
		$geocode = reverse_geocode($gps);
		
		//GET GP WITH MAX GP_ORDER
		$where = null;
		$where = " gp_order = (SELECT MAX(gp_order) FROM goalpoint WHERE load_id = ".$load_id.")";
		$last_gp = db_select_goalpoint($where);
		
		//DETERMINE DURATION
		$event_duration = get_expected_goalpoint_durations($gp_type);
		
		//GET GOALPOINT GUID FOR SYNCING
		$gp_guid = get_random_string(10);
		
		for($i=1; $i <= 2; $i++)//RUN LOOP TWICE
		{
			//CREATE NEW GOALPOINT
			$new_gp = null;
			$new_gp["truck_id"] = $truck_id;
			$new_gp["trailer_id"] = $trailer_id;
			$new_gp["client_id"] = $client_id;
			$new_gp["gp_order"] = ($last_gp["gp_order"] + $i);
			
			if($event_duration <> 0)
			{
				if($i == 1)
				{
					$new_gp["gp_type"] = $gp_type;
					$new_gp["duration"] = $event_duration;
					$new_gp["arrival_departure"] = "Arrival";
					$new_gp["dm_notes"] = $dm_notes;
				}
				else if($i == 2)
				{
					$new_gp["gp_type"] = $gp_type;
					$new_gp["duration"] = 0;
					$new_gp["arrival_departure"] = "Departure";
					
					$deadline = null;
				}
			}
			else
			{
				$new_gp["gp_type"] = $gp_type;
				$new_gp["duration"] = 0;
				$i++;//ONLY DO LOOP ONCE
			}
			
			if(!empty($deadline))
			{
				$new_gp["deadline"] = date("Y-m-d H:i:s",strtotime($deadline));
			}
			$new_gp["load_id"] = $load_id;
			$new_gp["gps"] = $gps;
			$new_gp["location_name"] = $location_name;
			$new_gp["location"] = $geocode["city"].", ".$geocode["state"];
			$new_gp["sync_gp_guid"] = $gp_guid;
			
			db_insert_goalpoint($new_gp);
			
		}
	}
	
	//SAVE GOALPOINT EDIT
	function save_goalpoint()
	{
		$gp_id = $_POST["goalpoint_id"];
		$client_id = $_POST["gp_client_id"];
		$truck_id = $_POST["gp_truck_id"];
		$trailer_id = $_POST["gp_trailer_id"];
		$deadline = $_POST["edit_gp_deadline"];
		$duration = $_POST["edit_gp_duration"];
		$gps = $_POST["edit_gp_gps"];
		$location_name = $_POST["edit_gp_location_name"];
		$dm_notes = $_POST["edit_gp_notes"];
		
		//echo $gps;
		$geocode = reverse_geocode($gps);
		//echo $geocode["city"].", ".$geocode["state"];
		
		//GET GOALPOINT
		$where = null;
		$where["id"] = $gp_id;
		$goalpoint = db_select_goalpoint($where);
		
		//UPDATE GOALPOINT
		$update_gp = null;
		$update_gp["client_id"] = $client_id;
		$update_gp["truck_id"] = $truck_id;
		$update_gp["trailer_id"] = $trailer_id;
		$update_gp["gps"] = $gps;
		$update_gp["location"] = $geocode["city"].", ".$geocode["state"];
		$update_gp["location_name"] = $location_name;
		
		//UPDATE BOTH SYNC GOALPOINT WITH ALL INFO -- EXCLUDING DURATION, DEADLINE, & NOTES
		$where = null;
		$where["sync_gp_guid"] = $goalpoint["sync_gp_guid"];
		db_update_goalpoint($update_gp,$where);
		
		//UPDATE THIS GOALPOINT WITH DURATION, DEADLINE, & NOTES
		$update_gp = null;
		$update_gp["duration"] = $duration;
		if(!empty($deadline))
		{
			$update_gp["deadline"] = date("Y-m-d H:i",strtotime($deadline));
		}
		else
		{
			$update_gp["deadline"] = null;
		}
		$update_gp["dm_notes"] = $dm_notes;
		
		$where = null;
		$where["id"] = $gp_id;
		db_update_goalpoint($update_gp,$where);
	}
	
	//DELETE GOALPOINT
	function delete_goalpoint()
	{
		$gp_id = $_POST["gp_id"];
		$row_id = $_POST["row_id"];
		
		//GET GOALPOINT
		$where = null;
		$where["id"] = $gp_id;
		$goalpoint = db_select_goalpoint($where);
		
		$where = null;
		$where["sync_gp_guid"] = $goalpoint["sync_gp_guid"];
		db_delete_goalpoint($where);
	}
	
	//CHANGE GOALPOINT ORDER
	function change_gp_order()
	{
		$goalpoint_id = $_POST["gp_id"];
		$direction = $_POST["direction"];
		
		//echo $direction;
		
		//GET THIS GP
		$where = null;
		$where["id"] = $goalpoint_id;
		$this_gp = db_select_goalpoint($where);
		
		$load_id = $this_gp["load_id"];
		
		if($direction == "up")
		{
			
			//GET THE GP DIRECTLY PRECEEDING THIS GP
			$where = null;
			$where["load_id"] = $load_id;
			$where["completion_time"] = null;
			$where["gp_order"] = $this_gp["gp_order"] - 1;
			$preceeding_gp = db_select_goalpoint($where);
			
			//IF THIS GP IS THE DEPARTURE
			if($preceeding_gp["sync_gp_guid"] == $this_gp["sync_gp_guid"])
			{
				//SELECT THE GP THAT PRECEEDS THE ARRIVAL EVENT ASSOCIATED WITH THIS GP
				$where = null;
				$where["load_id"] = $load_id;
				$where["completion_time"] = null;
				$where["gp_order"] = $preceeding_gp["gp_order"] - 1;
				$preceeding_gp = db_select_goalpoint($where);
			}
			
			//GET SYNCED GOALPOINT OF PRECEEDING GOALPOINT
			$where = null;
			//$where["load_id"] = $load_id;
			//$where["sync_gp_guid"] = $preceeding_gp["sync_gp_guid"];
			$where = " load_id = $load_id AND sync_gp_guid = '".$preceeding_gp["sync_gp_guid"]."' AND id <> ".$preceeding_gp["id"];
			$preceeding_synced_gp = db_select_goalpoint($where);
			//echo $preceeding_gp["id"];
			//echo $preceeding_synced_gp["id"];
			//echo $preceeding_synced_gp["completion_time"];
				
			if($preceeding_gp["gp_type"] == "Current Geopoint" || (!empty($preceeding_synced_gp) && !empty($preceeding_synced_gp["completion_time"])))
			{
				//DO NOTHING
			}
			else
			{
				
				//GET ALL THE GP'S FOR THE EVENT DIRECTLY PRECEEDING THIS GP EVENT
				$where = null;
				$where["load_id"] = $load_id;
				$where["sync_gp_guid"] = $preceeding_gp["sync_gp_guid"];
				$preceeding_goalpoints = db_select_goalpoints($where);
				
				//GRAB GP'S FOR THIS EVENT
				$where = null;
				$where = null;
				$where["load_id"] = $load_id;
				$where["sync_gp_guid"] = $this_gp["sync_gp_guid"];
				$these_goalpoints = db_select_goalpoints($where);
				
				foreach($preceeding_goalpoints as $p_gp)
				{
					//UPDATE PROCEEDING GOALPOINTS WITH
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
				
			}//do nothing if previous gp is current geopoint
			
			
		}
		else if($direction == "down")
		{
			//GET THE GP DIRECTLY FOLLOWING THIS GP
			$where = null;
			$where["load_id"] = $load_id;
			$where["completion_time"] = null;
			$where["gp_order"] = $this_gp["gp_order"] + 1;
			$following_gp = db_select_goalpoint($where);
			
			if($following_gp["sync_gp_guid"] == $this_gp["sync_gp_guid"])
			{
				$where = null;
				$where["load_id"] = $load_id;
				$where["completion_time"] = null;
				$where["gp_order"] = $following_gp["gp_order"] + 1;
				$following_gp = db_select_goalpoint($where);
			}
			
			
			//GET ALL THE GPS FOR THE EVENT DIRECTLY PRECEEDING THIS GP EVENT
			$where = null;
			$where["load_id"] = $load_id;
			$where["sync_gp_guid"] = $following_gp["sync_gp_guid"];
			$following_goalpoints = db_select_goalpoints($where);
			
			//GRAB GPS FOR THIS EVENT
			$where = null;
			$where = null;
			$where["load_id"] = $load_id;
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
	
	function open_mark_goalpoint_complete_dialog()
	{
		$gp_id = $_POST["gp_id"];
		
		//GET GOALPOINT
		$where = null;
		$where["id"] = $gp_id;
		$goalpoint = db_select_goalpoint($where);
		
		//GET LAT AND LNG
		$latlng = explode(",",$goalpoint["gps"]);
		$lat = $latlng[0];
		$lng = $latlng[1];
		
		//CREATE LAT AND LONG GEOFENCE
		$radius = .0075;
		$big_lat = $lat + $radius;
		$small_lat = $lat - $radius;
		$big_lng = $lng + $radius;
		$small_lng = $lng - $radius;
		//$max_date = date("Y-m-d H:i",strtotime($goalpoint[""])
		
		//GET ALL RECENT GEOPOINTS THAT ARE NEAR THE LOCATION
		$where = null;
		//$where["truck_id"] = $goalpoint["truck_id"];
		//$where["speed"] = '0';
		$where = " truck_id = ".$goalpoint["truck_id"]." AND latitude < $big_lat AND latitude > $small_lat AND longitude < $big_lng AND longitude > $small_lng ";
		$geopoints = db_select_geopoints($where,'datetime',30);
		$geopoint_options = array();
		$geopoint_options["Not Found"] = "Not Found";
		if(!empty($geopoints))
		{
			foreach($geopoints as $gp)
			{
				$geopoint_options[$gp["id"]] = date("m/d/y H:i",strtotime($gp["datetime"]))." ".round($gp["speed"])." MPH";
			}
		}
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$clients_dropdown_options = array();
		$clients_dropdown_options["Select"] = "Select";
		foreach($dd_all_clients as $client)
		{
			$clients_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		$clients_dropdown_options["None"] = "None";
		
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['geopoints'] = $geopoints;
		$data['lat'] = $lat;
		$data['lng'] = $lng;
		$data['geopoint_options'] = $geopoint_options;
		$data['goalpoint'] = $goalpoint;
		$this->load->view('loads/goalpoint_completion_dialog',$data);
	}
	
	function load_geopoint_details_for_mark_goalpoint_complete_dialog()
	{
		$geopoint_id = $_POST["geopoint_id"];
		$goalpoint_id = $_POST["goalpoint_id"];
		
		//GET GEOPOINT
		$where = null;
		$where["id"] = $geopoint_id;
		$geopoint = db_select_geopoint($where);
		
		//GET GOALPOINT
		$where = null;
		$where["id"] = $goalpoint_id;
		$goalpoint = db_select_goalpoint($where);
		
		
		$data['geopoint'] = $geopoint;
		$data['goalpoint'] = $goalpoint;
		$this->load->view('loads/geopoint_details_for_mark_goalpoint_complete_dialog',$data);
	}
	
	function mark_goalpoint_complete()
	{
		date_default_timezone_set('America/Denver');
		$now_datetime = date("Y-m-d H:i:s");
		
		$goalpoint_id = $_POST["goalpoint_id"];
		$geopoint_id = $_POST["geopoint_id"];
		
		//echo $_POST["gp_complete_date"]." ";
		//echo $_POST["gp_complete_time"]." ";
		
		//$completion_time = date("m/d/y H:i", strtotime($_POST["gp_complete_date"]." ".$_POST["gp_complete_time"]));
		//echo $completion_time;
		
		//GET GOALPOINT
		$where = null;
		$where["id"] = $goalpoint_id;
		$goalpoint = db_select_goalpoint($where);
		
		$load_id = $goalpoint["load_id"];
		
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		//GET GEOPOINT
		$geopoint = null;
		if(!empty($geopoint_id))
		{
			$where = null;
			$where["id"] = $geopoint_id;
			$geopoint = db_select_geopoint($where);
			
			//UPDATE GEOPOINT WITH
		}
		
		$geocode = reverse_geocode($_POST["gp_complete_gps"]);
		
		//CREATE NEW LOG ENTRY
		$new_log_entry = null;
		$new_log_entry["recorder_id"] = $this->session->userdata('person_id');
		$new_log_entry["load_id"] = $goalpoint["load_id"];
		$new_log_entry["allocated_load_id"] = $goalpoint["load_id"];
		$new_log_entry["truck_id"] = $goalpoint["truck_id"];
		$new_log_entry["trailer_id"] = $goalpoint["trailer_id"];
		$new_log_entry["main_driver_id"] = $goalpoint["client_id"];
		$new_log_entry["codriver_id"] = $_POST["codriver_id"];
		$new_log_entry["recorded_datetime"] = $now_datetime;
		$new_log_entry["city"] = $geocode["city"];
		$new_log_entry["state"] =  $geocode["state"];
		$new_log_entry["address"] =  $geocode["street_number"]." ".$geocode["street"];
		
		
		
		if(!empty($geopoint))
		{
			
			$new_log_entry["entry_datetime"] = date("Y-m-d H:i:s", strtotime($geopoint["datetime"]));
			$new_log_entry["gps_coordinates"] = $geopoint["latitude"].",".$geopoint["longitude"];
			$new_log_entry["odometer"] = $geopoint["odometer"];
			$new_log_entry["entry_notes"] = "Generated from Geopoint";
			
			$completion_time = date("m/d/y H:i", strtotime($geopoint["datetime"]));
			
		}
		else
		{
			$new_log_entry["entry_datetime"] = date("Y-m-d H:i:s", strtotime($_POST["gp_complete_date"]." ".$_POST["gp_complete_time"]));
			$new_log_entry["gps_coordinates"] = $_POST["gp_complete_gps"];
			$new_log_entry["odometer"] = $_POST["gp_complete_odometer"];
			$new_log_entry["entry_notes"] = "Recorded by ".$this->session->userdata('f_name');
			
			$completion_time = date("m/d/y H:i", strtotime($_POST["gp_complete_date"]." ".$_POST["gp_complete_time"]));
		}
		
		$db_completion_time = $new_log_entry["entry_datetime"];
		
		//ONLY LOG AFTER THE DEAPARTURE IS MARKED COMPLETE BUT LOG WITH THE ARRIVAL TIME (DEPARTURE TIME IN NOTES) -- ADD WAIT DURATION ATTRIBUTE TO LOAD FOR AUTO DETENTION BILLING
		if(($goalpoint["gp_type"] == "Pick" || $goalpoint["gp_type"] == "Drop") && $goalpoint["arrival_departure"] == "Departure")
		{
			//GET ARRIVAL GOALPOINT
			$where = null;
			$where["arrival_departure"] = "Arrival";
			$where["sync_gp_guid"] = $goalpoint["sync_gp_guid"];
			$arrival_goalpoint = db_select_goalpoint($where);
			
			$new_log_entry["entry_notes"] = "Arrived: ".date("d/m/y H:i", strtotime($arrival_goalpoint["completion_time"]))." Departed: ".$completion_time;
			$new_log_entry["entry_type"] = $goalpoint["gp_type"];
			db_insert_log_entry($new_log_entry);
		}
		else
		{
			$new_log_entry["entry_notes"] = $goalpoint["gp_type"]." ".$goalpoint["arrival_departure"];
			$new_log_entry["entry_type"] = "Checkpoint OOR";
			db_insert_log_entry($new_log_entry);
		}
		
		//IF PICK OR DROP IS MARKED ARRIVAL -- SEND SHIPPER OR RECEIVER DOCS EMAIL
		if(($goalpoint["gp_type"] == "Pick" || $goalpoint["gp_type"] == "Drop") && $goalpoint["arrival_departure"] == "Arrival")
		{
			//GET DRIVER1
			$where = null;
			$where["id"] = $goalpoint["client_id"];
			$driver1 = db_select_client($where);
			
			$recipients  = $driver1["company"]["person"]["email"];
			
			//GET DRIVER2
			$where = null;
			$where["id"] = $_POST["codriver_id"];
			$driver2 = db_select_client($where);
			
			if(!empty($driver2))
			{
				$recipients  = $driver1["company"]["person"]["email"].", ".$driver2["company"]["person"]["email"];
			}
			
			if(($goalpoint["gp_type"] == "Pick"))
			{
				//SEND RECEIVER DOCS EMAIL
				$email_data = null;
				$email_data["location"] = $geocode["city"].", ".$geocode["state"];
				$email_data["completion_time"] = $completion_time;
				$message = $this->load->view('emails/shipper_docs_email',$email_data, TRUE);
				// $message = "test";
				// $to = 'covax13@gmail.com';
				$to = $recipients;
				$subject = 'Shipper Docs for Load '.$load["customer_load_number"];
				// $headers = "From: paperwork.dispatch@gmail.com\r\n";
				// $headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
				// $headers .= "CC: paperwork.dispatch@gmail.com\r\n";
				// $headers .= "MIME-Version: 1.0\r\n";
				// $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				// mail("covax13@gmail.com","FleetSmarts Now Does Email!","You better believe it! Guess who just figured out how to send emails from FleetSmarts!!","From: fleetsmarts@fleetsmarts.net");
				// mail($to, $subject, $message, $headers);
				
				$this->email->from("paperwork.dispatch@gmail.com","Dispatch");
				$this->email->to($to);
				$this->email->cc('paperwork.dispatch@gmail.com');
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->send();
				//echo $this->email->print_debugger();
			}
			else if(($goalpoint["gp_type"] == "Drop"))
			{
				//SEND RECEIVER DOCS EMAIL
				$email_data = null;
				$email_data["location"] = $geocode["city"].", ".$geocode["state"];
				$email_data["completion_time"] = $completion_time;
				$message = $this->load->view('emails/receiver_docs_email',$email_data, TRUE);
				//$to = 'covax13@gmail.com';
				$to = $recipients;
				$subject = 'Receiver Docs for Load '.$load["customer_load_number"];
				//$message = "test";
				// $headers = "From: paperwork.dispatch@gmail.com\r\n";
				// //$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
				// $headers .= "CC: paperwork.dispatch@gmail.com\r\n";
				// $headers .= "MIME-Version: 1.0\r\n";
				// $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				// //mail("covax13@gmail.com","FleetSmarts Now Does Email!","You better believe it! Guess who just figured out how to send emails from FleetSmarts!!","From: fleetsmarts@fleetsmarts.net");
				// mail($to, $subject, $message, $headers);
				
				$this->email->from("paperwork.dispatch@gmail.com","Dispatch");
				$this->email->to($to);
				$this->email->cc('paperwork.dispatch@gmail.com');
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->send();
			}
		}
		
		//UPDATE GOALPOINT WITH COMPLETION TIME
		$update = null;
		$update["expected_time"] = $db_completion_time;
		$update["completion_time"] = $db_completion_time;
		
		$where = null;
		$where["id"] = $goalpoint["id"];
		db_update_goalpoint($update,$where);
		
		//REORDERS THE COMPLETED GOALPOINT ACCORDING TO COMPLETION TIME
		order_completed_goalpoints($goalpoint["load_id"]);
		
		//UPDATE LOAD WITH LUMPER INFO
		if($_POST["is_lumper"] == "Yes")
		{
			$text = "This load had a $".number_format($_POST["gp_complete_lumper_amount"],2)." lumper.";
			$initials = substr($this->session->userdata('first_name'),0,1).substr($this->session->userdata('last_name'),0,1);
			$date_text = date("m/d/y H:i");
			$full_note = $date_text." - ".$initials." | ".$text."\n\n";
			
			$update_load = null;
			$update_load["has_lumper"] = "Yes";
			$update_load["load_notes"] = $full_note.$load["load_notes"];
			$where = null;
			$where["id"] = $load_id;
			db_update_load($update_load,$where);
			
			//INSERT NEW BILLING NOTE
			$insert_note = null;
			$insert_note["note_type"] = "load_billing";
			$insert_note["note_for_id"] = $load_id;
			$insert_note["note_datetime"] = date("Y-m-d H:i");
			$insert_note["user_id"] = $this->session->userdata('user_id');
			$insert_note["note_text"] = $text;
			db_insert_note($insert_note);
		}
		
		//GET NEW LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		//GET ALL REMAINING PICKS GOALPOINTS
		$where = null;
		$where = " load_id = $load_id AND completion_time IS NULL AND gp_type = 'Pick' ";
		$pick_gps = db_select_goalpoints($where);
		
		//CHECK TO SEE IF THERE ARE ANY MORE PICKS OR DROPS
		if($load["status_number"] == 3)
		{
			if(empty($pick_gps))
			{
				//UPDATE STATUS TO REFLECT DROP PENDING
				$update_load = null;
				$update_load["status_number"] = 4;
				$update_load["status"] = "Drop Pending";
				$where = null;
				$where["id"] = $load_id;
				db_update_load($update_load,$where);
			}
		}
		
		//GET NEW LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		//GET ALL REMAINING DROPS GOALPOINTS
		$where = null;
		$where = " load_id = $load_id AND completion_time IS NULL AND gp_type = 'Drop' ";
		$drop_gps = db_select_goalpoints($where);
		
		//IF FINAL DROP
		if(empty($drop_gps) && empty($pick_gps))
		{
			
			
			//DETERMINE AR SPECIALIST TO ASSIGN TO LOAD FOR BILLING
			$truck_number = (int) $load["load_truck_id"];
			
			//GET A/R SPECIALISTS
			//GET PERMISSION FOR MANAGING A/R
			$where = null;
			$where["permission_name"] = "manage A/R";
			$ar_permission = db_select_permission($where);
			
			//GET ALL USER_PERMISSIONS FOR THIS PERMISSION
			$where = null;
			$where["permission_id"] = $ar_permission["id"];
			$ar_user_permissions = db_select_user_permissions($where);
			
			//UPDATE STATUS TO REFLECT DROPPED
			$update_load = null;
			$update_load["client_id"] = $goalpoint["client_id"];
			$update_load["driver2_id"] = $_POST["codriver_id"];
			if($load["dm_id"] == 880)//Taylor person_id
			{
				$update_load["ar_specialist_id"] = 678;//Mylene user_id
				$ars = "Mylene";
			}
			else if($load["dm_id"] == 660)//Ezra person_id
			{
				$update_load["ar_specialist_id"] = 678;//Mylene user_id
				$ars = "Mylene";
			}
			else if($load["dm_id"] == 794)//James person_id
			{
				$update_load["ar_specialist_id"] = 947;//Jeannette user_id
				$ars = "Jeannette";
			}
			else if($load["dm_id"] == 828)//Payne person_id
			{
				$update_load["ar_specialist_id"] = 915;//Lenneth user_id
				$ars = "Lenneth";
			}
			else
			{
				$update_load["ar_specialist_id"] = 678;//Mylene user_id
				$ars = "Mylene";
			}
			$update_load["status_number"] = 5;
			$update_load["status"] = "Dropped";
			$update["final_drop_datetime"] = $db_completion_time;
			$update_load["pushed_datetime"] = $now_datetime;
			$update_load["billing_status"] = "Digital";
			$update_load["billing_status_number"] = 1;
			$where = null;
			$where["id"] = $load_id;
			db_update_load($update_load,$where);
			
			//INSERT NEW BILLING NOTE
			$insert_note = null;
			$insert_note["note_type"] = "load_billing";
			$insert_note["note_for_id"] = $load_id;
			$insert_note["note_datetime"] = date("Y-m-d H:i");
			$insert_note["user_id"] = $this->session->userdata('user_id');
			$insert_note["note_text"] = "Load marked dropped";
			db_insert_note($insert_note);
			
			//INSERT BILLING NOTE SAYING THAT LOAD IS MARKED COMPLETE AND ARS IS ASSINGED
			$insert_note = null;
			$insert_note["note_type"] = "load_billing";
			$insert_note["note_for_id"] = $load_id;
			$insert_note["note_datetime"] = date("Y-m-d H:i");
			$insert_note["user_id"] = $this->session->userdata('user_id');
			$insert_note["note_text"] = $ars." was assigned to load as AR specialist";
			db_insert_note($insert_note);
			
		}
	}
	
	function test_of_even_odd_trucks()
	{
		//GET ALL TRUCKS
		$where = null;
		$where["status"] = "On the road";
		$trucks = db_select_trucks($where);
		
		foreach($trucks as $truck)
		{
			
			$truck_number = (int) $truck["truck_number"];
			
			if($truck_number % 2 == 0)
			{
				echo "Even ";
			}
			else
			{
				echo "Odd  ";
			}
			echo $truck["truck_number"]."<br>";
		}
		
	}
	
	//OPEN LOAD DISPATCH DIALOG
	function open_load_dispatch_dialog()
	{
		$load_id = $_POST['load_number'];
		
		//update_current_goalpoint_from_geopoint($load_id);
		//calc_expected_gp_times($load_id);
		
		//GET LOAD INFO
		$where = null;
		$where['id'] = $load_id;
		$load = db_select_load($where);
		
		//GET GOALPOINTS
		$where = null;
		$where = " load_id = $load_id AND gp_type <> 'Current Geopoint' AND completion_time IS NULL ";
		$goalpoints = db_select_goalpoints($where,"gp_order");
		
		//GET CURRENT GEOPOINT GOALPOINT
		$where = null;
		$where["gp_type"] = "Current Geopoint";
		$where["load_id"] = $load_id;
		$current_geopoint_goalpoint = db_select_goalpoint($where);
		
		//GET HOLD REPORT
		$hold_report = get_hold_report($load["client_id"]);
		
		$data['hold_report'] = $hold_report;
		$data['goalpoints'] = $goalpoints;
		$data['current_geopoint_goalpoint'] = $current_geopoint_goalpoint;
		$data['load'] = $load;
		$this->load->view('loads/load_dispatch_dialog',$data);
	}
	
	function send_driver_hold_report_email()
	{
		send_driver_hold_report_email($_POST["client_id"]);
	}
	
	//OPEN RATE CON RECEIVED DIALOG
	function rate_con_receieved_ajax()
	{
		//GET LOAD INFO
		$load_where['id'] = $_POST['load_number'];
		$load = db_select_load($load_where);
		
		//GET ACTIVE CLIENTS FOR CLIENT DROPDOWN
		$where = null;
		$where["client_type"] = "Main Driver";
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$clients_dropdown_options = array();
		$clients_dropdown_options["Select"] = "Select";
		foreach($dd_all_clients as $client)
		{
			$clients_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		$clients_dropdown_options["UNASSIGNED"] = "UNASSIGNED";
		
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
		
		
		//GET OPTIONS FOR FLEET MANAGER DROPDOWN LIST
		//$fleet_managers_where['Role'] = "Fleet Manager";
		$fleet_managers_where = " role = 'Fleet Manager' OR role = 'Driver Manager' ";
		$fleet_managers = db_select_persons($fleet_managers_where);
		$fleet_manager_dropdown_options = array();
		$title = "Select";
		$fleet_manager_dropdown_options[0] = $title;
		foreach ($fleet_managers as $manager):
			$title = $manager['f_name']." ".$manager['l_name'];
			$fleet_manager_dropdown_options[$manager['id']] = $title;
		endforeach;
		
		//GET OPTIONS FOR DRIVER MANAGER DROPDOWN LIST
		$where = null;
		//$where['role'] = "Driver Manager";
		$where = " role = 'Fleet Manager' OR role = 'Driver Manager' ";
		$driver_managers = db_select_persons($where);
		$dm_dropdown_options = array();
		$dm_dropdown_options['Select'] = "Select";
		foreach ($driver_managers as $manager)
		{
			$title = $manager['f_name']." ".$manager['l_name'];
			$dm_dropdown_options[$manager['id']] = $title;
		}
		
		//CREATE 5 BLANK PICKS FOR VIEW
		for ($i=1; $i<=5; $i++)
		{
			$pick_stop["id"] = null;
			$pick_stop["company_id"] = null;
			$pick_stop["stop_type"] = null;
			$pick_stop["stop_datetime"] = null;
			$pick_stop["location_name"] = null;
			$pick_stop["city"] = null;
			$pick_stop["state"] = null;
			$pick_stop["address"] = null;
			$pick_stop["latitude"] = null;
			$pick_stop["longitude"] = null;
			$pick_stop["odometer"] = null;
			$pick_stop["notes"] = null;
		
			$pick['id'] = null;
			$pick['stop_id'] = null;
			$pick['load_id'] = null;
			$pick['pick_number'] = null;
			$pick['pu_number'] = null;
			$pick['appointment_time'] = null;
			$pick['appointment_time_mst'] = null;
			$pick['in_time'] = null;
			$pick['out_time'] = null;
			$pick['dispatch_datetime'] = null;
			$pick['dispatch_notes'] = null;
			$pick['internal_notes'] = null;
			$pick['stop'] = $pick_stop;
			
			$this_loads_picks[$i] = $pick;
		}
		//GET ALL PICKS
		$i = 1;
		foreach ($load["load_picks"] as $pick)
		{
			$this_loads_picks[$i] = $pick;
			$i++;
		}
	
		//CREATE 5 BLANK DROPS FOR VIEW
		for ($i=1; $i<=5; $i++)
		{
			$drop_stop["id"] = null;
			$drop_stop["company_id"] = null;
			$drop_stop["stop_type"] = null;
			$drop_stop["stop_datetime"] = null;
			$drop_stop["location_name"] = null;
			$drop_stop["city"] = null;
			$drop_stop["state"] = null;
			$drop_stop["address"] = null;
			$drop_stop["latitude"] = null;
			$drop_stop["longitude"] = null;
			$drop_stop["odometer"] = null;
			$drop_stop["notes"] = null;
			
			$drop['id'] = null;
			$drop['stop_id'] = null;
			$drop['load_id'] = null;
			$drop['drop_number'] = null;
			$drop['ref_number'] = null;
			$drop['appointment_time'] = null;
			$drop['appointment_time_mst'] = null;
			$drop['in_time'] = null;
			$drop['out_time'] = null;
			$drop['dispatch_datetime'] = null;
			$drop['dispatch_notes'] = null;
			$drop['internal_notes'] = null;
			$drop['stop'] = $drop_stop;
			
			$this_loads_drops[$i] = $drop;
		}
		//GET ALL DROPS
		$i = 1;
		foreach ($load["load_drops"] as $drop)
		{
			$this_loads_drops[$i] = $drop;
			$i++;
		}
	
		//GET ALL BROKERS
		$customers_where['1'] = 1;
		$brokers = db_select_customers($customers_where);
	
		//GET NATL FUEL AVG FOR THIS LOAD
		$natl_fuel_avg = get_natl_fuel_avg_from_db($load["booking_datetime"],true);
		
		//GET ALL ACTIVE TRUCKS
		$where = null;
		$where["dropdown_status"] = "Show";
		$trucks = db_select_trucks($where,"truck_number");
		$truck_dropdown_options = array();
		$truck_dropdown_options["Select"] = "Select";
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
	
		$data["dm_dropdown_options"] = $dm_dropdown_options;
		$data["truck_dropdown_options"] = $truck_dropdown_options;
		$data["trailer_dropdown_options"] = $trailer_dropdown_options;
		$data["natl_fuel_avg"] = $natl_fuel_avg;
		$data['brokers'] = $brokers;
		$data['load'] = $load;
		$data['picks'] = $this_loads_picks;
		$data['drops'] = $this_loads_drops;
		$data['billed_under_options'] = $billed_under_options;
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$this->load->view('loads/loads_rate_con_received_div',$data);
	
	}//END RATE CON RECEIVED AJAX	
	
	//SAVE RATE CON RECEIVED INFO
	function rcr_save()
	{
		//SET TIMEZONE
		date_default_timezone_set('US/Mountain');
		
		$load_id = $_POST["load_id"];
		
		//GET BROKER
		$customer_where['customer_name'] = $_POST['rcr_broker'];
		$broker = db_select_customer($customer_where);
		
		//GET CLIENT
		if($_POST["rcr_client_dropdown"] == "Select" || $_POST["rcr_client_dropdown"] == "UNASSIGNED")
		{
			$client_id = null;
			$client_company_id = null;
		}
		else
		{
			$client_id = $_POST['rcr_client_dropdown'];
			
			//GET CLIENT
			$where = null;
			$where["id"] = $client_id;
			$client = db_select_client($where);
			
			$client_company_id = $client["company"]["id"];
		}
		
		$load_set["billed_under"] = $_POST["rcr_billed_under_dropdown"];
		$load_set["billing_method"] = $_POST["rcr_billing_method_dropdown"];
		$load_set["natl_fuel_avg"] = $_POST["natl_fuel_avg"];

		$load_set["originals_required"] = $_POST["rcr_originals_required"];
		$load_set["dm_id"] = $_POST["rcr_driver_manager_dropdown"];
		$load_set["is_reefer"] = $_POST["rcr_is_reefer"];
		$load_set["reefer_low_set"] = $_POST["rcr_reefer_low_set"];
		$load_set["reefer_high_set"] = $_POST["rcr_reefer_high_set"];
		$load_set["client_id"] = $client_id;
		if($_POST["rcr_truck_id"] != "Select")
		{
			$load_set["load_truck_id"] = $_POST["rcr_truck_id"];
		}
		if($_POST["rcr_truck_id"] != "Select")
		{
			$load_set["load_trailer_id"] = $_POST["rcr_trailer_id"];
		}
		
		$load_set["rcr_datetime"] = date("Y-m-d H:i:s", time());
		$load_set["customer_load_number"] = $_POST["rcr_load_number"];
		$load_set["internal_load_number"] = $_POST["previous_load_number"]."-".$_POST["rcr_load_number"];
		$load_set["fleet_manager_id"] = $_POST["rcr_fleet_manager_dropdown"];
		$load_set["broker_id"] = $broker["id"];
		$load_set["load_type"] = $_POST["load_type"];
		$load_set["contact_info"] = $_POST["rcr_contact_info"];
		$load_set["expected_revenue"] = $_POST["rcr_expected_revenue"];
		$load_set["load_desc"] = $_POST["rcr_load_notes"];
		

		$where = null;
		$where['id'] = $_POST["load_id"];
		db_update_load($load_set,$where);

		//GET LOAD
		$where = null;
		$where["id"] = $_POST["load_id"];

		$load = db_select_load($where);

		//UPDATE NAT'L FUEL AVG WITH PRICE
		$booked_date = date('Y-m-d',strtotime($load["booking_datetime"]));

		//GET FUEL AVERAGE FOR THIS DATE
		$where = null;
		$where = " datetime > '".$booked_date." 00:00:00' AND datetime < '".$booked_date." 23:59:00'";
		$fuel_average = db_select_fuel_average($where);

		//echo $where;

		if(!empty($fuel_average))
		{
			//UPDATE FUEL AVERAGE
			$where = null;
			$where["id"] = $fuel_average["id"];

			$update = null;
			$update["fuel_avg"] = $_POST["natl_fuel_avg"];
			//db_update_fuel_average($update,$where);
		}
		else
		{
			//INSERT FUEL AVG TO DB
			$fuel_avg = null;
			$fuel_avg["datetime"] = $booked_date." 00:00:01";
			$fuel_avg["fuel_avg"] = round($_POST["natl_fuel_avg"],3);
			db_insert_fuel_average($fuel_avg);
		}
		
		
		//FOR EACH PICK, UPDATE AND INSERT PICKS
		for ($i=1;$i<=5;$i++)
		{
			
			//READY APPOINTMENT TIME FOR DB
			$hour = $_POST["rcr_pick_app_hour_$i"];
			$minute = $_POST["rcr_pick_app_minute_$i"];
			$ampm = $_POST["rcr_pick_app_ampm_$i"];
			$timezone = $_POST["rcr_pick_app_timezone_$i"];
			
			if($hour == "--" || $minute == "--" || $ampm == "--" || $timezone == "---")
			{
				$appointment_time = "00:00:01";
				$appointment_time_mst = "00:00:01";
			}
			else
			{
				$appointment_time = make_db_time($hour,$minute,$ampm);
				$appointment_time_mst = make_db_time($hour,$minute,$ampm,$timezone);
			}
			
			//UPDATE STOP WITH NEW STOP INFO
			$pick_stop["location_name"] = $_POST["rcr_pick_location_$i"];
			$pick_stop["city"] = $_POST["rcr_pick_city_$i"];
			$pick_stop["state"] = $_POST["rcr_pick_state_$i"];
			$pick_stop["address"] = $_POST["rcr_pick_address_$i"];
			$pick_stop["company_id"] = $client_company_id;
			
			//UPDATE PICK WITH NEW PICK INFO
			$pick["pick_number"] = $_POST["previous_load_number"]."-".$_POST["rcr_load_number"]."-P$i" ;
			$pick["load_id"] = $_POST["load_id"];
			$pick['appointment_time'] = date('Y-m-j',strtotime($_POST["rcr_pick_date_$i"]))." ".$appointment_time;
			$pick['appointment_time_mst'] = date('Y-m-j',strtotime($_POST["rcr_pick_date_$i"]))." ".$appointment_time_mst;
			$pick["pu_number"] = $_POST["rcr_pick_pu_number_$i"];
			$pick["dispatch_notes"] = $_POST["rcr_pick_dispatch_notes_$i"];
			//$pick["internal_notes"] = $_POST["rcr_pick_internal_notes_$i"];
			
			$this_pick = null;
			if(!empty($_POST["pick_id_$i"]))
			{
				$this_pick_where["id"] = $_POST["pick_id_$i"];
				@$this_pick = db_select_pick($this_pick_where);
			}
			//IF THIS PICK ALREADY EXISTS IN THE DB
			if(!empty($this_pick))
			{
				//UPDATE STOP
				$pick_stop_where["id"] = $_POST["pick_stop_id_$i"];
				db_update_stop($pick_stop,$pick_stop_where);
				
				//UPDATE PICK
				$pick_where["id"] = $this_pick["id"];
				db_update_pick($pick,$pick_where);
			}
			else //IF THIS PICK DOESN'T EXIST IN THE DB
			{
				//IF THERE IS A DATE, INSERT NEW PICK INTO THE DATABASE
				if (!empty($_POST["rcr_pick_date_$i"]))
				{
					//INSERT STOP
					db_insert_stop($pick_stop);
					
					//GET STOP FOR STOP_ID
					$pick_stop_where["location_name"] = $_POST["rcr_pick_location_$i"];
					$pick_stop_where["city"] = $_POST["rcr_pick_city_$i"];
					$pick_stop_where["state"] = $_POST["rcr_pick_state_$i"];
					$pick_stop_where["address"] = $_POST["rcr_pick_address_$i"];
					$new_stop = db_select_stop($pick_stop_where);
					
					$pick["stop_id"] = $new_stop["id"];
					
					//INSERT PICK
					db_insert_pick($pick);
					
					//GET GOALPOINT GUID FOR SYNCING
					$gp_guid = get_random_string(10);
					
					//DETERMINE DURATION
					$event_duration = get_expected_goalpoint_durations("Pick");
					
					for($gp_i=1; $gp_i <= 2; $gp_i++)//RUN LOOP TWICE
					{
						//CREATE NEW GOALPOINT
						$new_gp = null;
						$new_gp["deadline"] = date('Y-m-d',strtotime($_POST["rcr_pick_date_$i"]))." ".$appointment_time;
						$new_gp["client_id"] = $client_id;
						if($_POST["rcr_truck_id"] != "Select")
						{
							$new_gp["truck_id"] = $_POST["rcr_truck_id"];
						}
						if($_POST["rcr_truck_id"] != "Select")
						{
							$new_gp["trailer_id"] = $_POST["rcr_trailer_id"];
						}
						//GET GP WITH MAX GP_ORDER
						$where = null;
						$where = " gp_order = (SELECT MAX(gp_order) FROM goalpoint WHERE load_id = ".$load_id.")";
						$last_gp = db_select_goalpoint($where);
						if(!empty($last_gp))
						{
							$new_gp["gp_order"] = ($last_gp["gp_order"] + 1);
						}
						else
						{
							$new_gp["gp_order"] = 1;
						}
						
						if($event_duration <> 0)
						{
							if($gp_i == 1)
							{
								$new_gp["dm_notes"] = "PU: ".$_POST["rcr_pick_pu_number_$i"]." ".$_POST["rcr_pick_dispatch_notes_$i"];
								$new_gp["gp_type"] = "Pick";
								$new_gp["duration"] = $event_duration;
								$new_gp["arrival_departure"] = "Arrival";
							}
							else if($gp_i == 2)
							{
								$new_gp["gp_type"] = "Pick";
								$new_gp["duration"] = 0;
								$new_gp["arrival_departure"] = "Departure";
								$new_gp["deadline"] = null;
								
								$deadline = null;
							}
						}
						else
						{
							$new_gp["dm_notes"] = "PU: ".$_POST["rcr_pick_pu_number_$i"]." ".$_POST["rcr_pick_dispatch_notes_$i"];
							$new_gp["gp_type"] = "Pick";
							$new_gp["duration"] = 0;
							$gp_i++;//ONLY DO LOOP ONCE
						}
						
						$new_gp["load_id"] = $load_id;
						$new_gp["gps"] = $_POST["rcr_pick_gps_$i"];
						$new_gp["location_name"] = $_POST["rcr_pick_location_$i"];
						$new_gp["location"] = $_POST["rcr_pick_city_$i"].", ".$_POST["rcr_pick_state_$i"];
						$new_gp["sync_gp_guid"] = $gp_guid;
						
						db_insert_goalpoint($new_gp);
						
					}
				}
				
			}
			
			if($i == 1)
			{
				//UPDATE LOAD WITH FIRST PICK DATETIME
				$where = null;
				$where['id'] = $_POST["load_id"];
				$update_load = null;
				$update_load["first_pick_datetime"] =  date('Y-m-j',strtotime($_POST["rcr_pick_date_$i"]))." ".$appointment_time_mst;
				db_update_load($update_load, $where);
			}
			
		}//END FOR EACH PICK
		
		//FOR EACH DROP, UPDATE AND INSERT DROPS
		for ($i=1;$i<=5;$i++)
		{
			
			//READY APPOINTMENT TIME FOR DB
			$hour = $_POST["rcr_drop_app_hour_$i"];
			$minute = $_POST["rcr_drop_app_minute_$i"];
			$ampm = $_POST["rcr_drop_app_ampm_$i"];
			$timezone = $_POST["rcr_drop_app_timezone_$i"];
			
			if($hour == "--" || $minute == "--" || $ampm == "--" || $timezone == "---")
			{
				$appointment_time = "00:00:01";
				$appointment_time_mst = "00:00:01";
			}
			else
			{
				$appointment_time = make_db_time($hour,$minute,$ampm);
				$appointment_time_mst = make_db_time($hour,$minute,$ampm,$timezone);
			}
			
			//UPDATE STOP WITH NEW STOP INFO
			$drop_stop["location_name"] = $_POST["rcr_drop_location_$i"];
			$drop_stop["city"] = $_POST["rcr_drop_city_$i"];
			$drop_stop["state"] = $_POST["rcr_drop_state_$i"];
			$drop_stop["address"] = $_POST["rcr_drop_address_$i"];
			$drop_stop["company_id"] = $client_company_id;
			
			//UPDATE DROP WITH NEW drop INFO
			$drop["drop_number"] = $_POST["previous_load_number"]."-".$_POST["rcr_load_number"]."-D$i" ;
			$drop["load_id"] = $_POST["load_id"];
			$drop['appointment_time'] = date('Y-m-j',strtotime($_POST["rcr_drop_date_$i"]))." ".$appointment_time;
			$drop['appointment_time_mst'] = date('Y-m-j',strtotime($_POST["rcr_drop_date_$i"]))." ".$appointment_time_mst;
			$drop["ref_number"] = $_POST["rcr_drop_ref_number_$i"];
			$drop["dispatch_notes"] = $_POST["rcr_drop_dispatch_notes_$i"];
			//$drop["internal_notes"] = $_POST["rcr_drop_internal_notes_$i"];
			
			$this_drop = null;
			if(!empty($_POST["drop_id_$i"]))
			{
				$this_drop_where["id"] = $_POST["drop_id_$i"];
				@$this_drop = db_select_drop($this_drop_where);
			}
			//IF THIS DROP ALREADY EXISTS IN THE DB
			if(!empty($this_drop))
			{
				//UPDATE STOP
				$drop_stop_where["id"] = $_POST["drop_stop_id_$i"];
				db_update_stop($drop_stop,$drop_stop_where);
				
				//UPDATE DROP
				$drop_where["id"] = $this_drop["id"];
				db_update_drop($drop,$drop_where);
			}
			else //IF THIS DROP DOESN'T EXIST IN THE DB
			{
				//IF THERE IS A DATE, INSERT NEW drop INTO THE DATABASE
				if (!empty($_POST["rcr_drop_date_$i"]))
				{
					//INSERT STOP
					db_insert_stop($drop_stop);
					
					//GET STOP FOR STOP_ID
					$drop_stop_where["location_name"] = $_POST["rcr_drop_location_$i"];
					$drop_stop_where["city"] = $_POST["rcr_drop_city_$i"];
					$drop_stop_where["state"] = $_POST["rcr_drop_state_$i"];
					$drop_stop_where["address"] = $_POST["rcr_drop_address_$i"];
					$new_stop = db_select_stop($drop_stop_where);
					$drop["stop_id"] = $new_stop["id"];
					
					//INSERT DROP
					db_insert_drop($drop);
					
					//GET GOALPOINT GUID FOR SYNCING
					$gp_guid = get_random_string(10);
					
					//DETERMINE DURATION
					$event_duration = get_expected_goalpoint_durations("Drop");
					
					for($gp_i=1; $gp_i <= 2; $gp_i++)//RUN LOOP TWICE
					{
						//CREATE NEW GOALPOINT
						$new_gp = null;
						$new_gp["deadline"] = date('Y-m-d',strtotime($_POST["rcr_drop_date_$i"]))." ".$appointment_time;
						$new_gp["client_id"] = $client_id;
						if($_POST["rcr_truck_id"] != "Select")
						{
							$new_gp["truck_id"] = $_POST["rcr_truck_id"];
						}
						if($_POST["rcr_truck_id"] != "Select")
						{
							$new_gp["trailer_id"] = $_POST["rcr_trailer_id"];
						}
						
						//GET GP WITH MAX GP_ORDER
						$where = null;
						$where = " gp_order = (SELECT MAX(gp_order) FROM goalpoint WHERE load_id = ".$load_id.")";
						$last_gp = db_select_goalpoint($where);
						if(!empty($last_gp))
						{
							$new_gp["gp_order"] = ($last_gp["gp_order"] + 1);
						}
						else
						{
							$new_gp["gp_order"] = 1;
						}
						
						if($event_duration <> 0)
						{
							if($gp_i == 1)
							{
								$new_gp["dm_notes"] = "Ref: ".$_POST["rcr_drop_ref_number_$i"]." ".$_POST["rcr_drop_dispatch_notes_$i"];
								$new_gp["gp_type"] = "Drop";
								$new_gp["duration"] = $event_duration;
								$new_gp["arrival_departure"] = "Arrival";
							}
							else if($gp_i == 2)
							{
								$new_gp["gp_type"] = "Drop";
								$new_gp["duration"] = 0;
								$new_gp["arrival_departure"] = "Departure";
								$new_gp["deadline"] = null;
								
								$deadline = null;
							}
						}
						else
						{
							$new_gp["dm_notes"] = "PU: ".$_POST["rcr_drop_ref_number_$i"]." ".$_POST["rcr_drop_dispatch_notes_$i"];
							$new_gp["gp_type"] = "Drop";
							$new_gp["duration"] = 0;
							$gp_i++;//ONLY DO LOOP ONCE
						}
						
						$new_gp["load_id"] = $load_id;
						$new_gp["gps"] = $_POST["rcr_drop_gps_$i"];
						$new_gp["location_name"] = $_POST["rcr_drop_location_$i"];
						$new_gp["location"] = $_POST["rcr_drop_city_$i"].", ".$_POST["rcr_drop_state_$i"];
						$new_gp["sync_gp_guid"] = $gp_guid;
						
						db_insert_goalpoint($new_gp);
						
					}
				}
				
				
			}
			
			if(!empty($_POST["rcr_drop_date_$i"]))
			{
				//UPDATE LOAD WITH FIRST PICK DATETIME
				$update_load = null;
				$update_load["final_drop_datetime"] =  date('Y-m-j',strtotime($_POST["rcr_drop_date_$i"]))." ".$appointment_time_mst;
			}
			
		}//END FOR EACH DROP
		
		
		$file_uploaded = FALSE;
		
		if(!empty($_FILES['rc_save_attachment_file']["type"]))
		{
			//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
			$post_name = 'rc_save_attachment_file';
			$file = $_FILES[$post_name];
			$name = str_replace(' ','_',$file["name"]);
			$type1 = $file["type"];
			//$title = pathinfo($file["name"], PATHINFO_FILENAME);
			$title = $file["name"];
			$category = "Load Attachment";
			$local_path = $file["tmp_name"];
			$server_path = '/edocuments/';
			$office_permission = 'All';
			$driver_permission = 'None';
			$secure_file = store_secure_ftp_file($post_name,$name,$type1,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
			
			$update_load["rc_link"] = $secure_file["file_guid"];
			
			//CREATE ATTACHMENT IN DB
			$attachment = null;
			$attachment["type"] = "load";
			$attachment["attached_to_id"] = $load["id"];
			$attachment["file_guid"] = $secure_file["file_guid"];
			$attachment["attachment_name"] = "Rate Con";

			db_insert_attachment($attachment);
			
			$file_uploaded = TRUE;
		}
		
		if(!empty($_FILES['rcr_proof_of_no_org']["type"]))
		{
			//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
			$post_name = 'rcr_proof_of_no_org';
			$file = $_FILES[$post_name];
			$name = str_replace(' ','_',$file["name"]);
			$type1 = $file["type"];
			//$title = pathinfo($file["name"], PATHINFO_FILENAME);
			$title = $file["name"];
			$category = "Load Attachment";
			$local_path = $file["tmp_name"];
			$server_path = '/edocuments/';
			$office_permission = 'All';
			$driver_permission = 'None';
			$secure_file = store_secure_ftp_file($post_name,$name,$type1,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
			
			$update_load["no_originals_proof_guid"] = $secure_file["file_guid"];
			
			//CREATE ATTACHMENT IN DB
			$attachment = null;
			$attachment["type"] = "load";
			$attachment["attached_to_id"] = $load["id"];
			$attachment["file_guid"] = $secure_file["file_guid"];
			$attachment["attachment_name"] = "No Original Proof";

			db_insert_attachment($attachment);
			
			$file_uploaded = TRUE;
		}
		
		$update_load["status"] = "Dispatch Pending";
		$update_load["status_number"] = 2;
		
		//PERFORM THE UPDATE
		$where = null;
		$where['id'] = $_POST["load_id"];
		db_update_load($update_load,$where);
		
		
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		load_upload_success_view();
	
		
	}//END RCR SAVE
	
	//SAVE LOAD CHECK CALL
	function save_check_call()
	{
		//SET TIMEZONE
		date_default_timezone_set('US/Mountain');
		$recorded_time = date("Y-m-d H:i:s");
		
		$recorder_id = $this->session->userdata('user_id');
		
		$current_geopoint_goalpoint_id = $_POST["current_geopoint_goalpoint_id"];
		$load_id = $_POST["dispatch_update_load_id"];
		$truck_fuel = $_POST["truck_fuel"];
		$truck_codes = $_POST["truck_codes_status"];
		
		//GET LOAD
		$where = null;
		$where['id'] = $load_id;
		$load = db_select_load($where);
		
		//GET CURRENT GEOPOINT GOALPOINT
		$where = null;
		$where["id"] = $current_geopoint_goalpoint_id;
		$current_geopoint_goalpoint = db_select_goalpoint($where);

		$geocode = reverse_geocode($current_geopoint_goalpoint["gps"]);
		
		//GET TRUCK
		$where = null;
		$where["id"] = $load["load_truck_id"];
		//$truck = db_select_truck($where);
		
		// //GET TRAILER
		// $where = null;
		// $where["id"] = $load["load_trailer_id"];
		// //$trailer = db_select_trailer($where);
		
		// //GET DRIVER COMPANY
		// $where = null;
		// $where["id"] = $load["client"]["company_id"];
		// //$driver_company = db_select_company($where);
		
		// //GET DRIVER PERSON
		// $where = null;
		// $where["id"] = $driver_company["person_id"];
		// //$driver_person = db_select_person($where);
		
		// //GET CARRIER COMPANY
		// $where = null;
		// $where["id"] = $load["billed_under"];
		// //$carrier_company = db_select_company($where);
		
		// //GET FLEET MANAGER COMPANY
		// $where = null;
		// $where["person_id"] = $load["fleet_manager"]["id"];
		// //$fm_company = db_select_company($where);
		
		// //GET DRIVER MANAGER COMPANY
		// $where = null;
		// $where["person_id"] = $load["driver_manager"]["id"];
		// //$dm_company = db_select_company($where);
		
		$update_guid = get_random_string(10);
		
		//CREATE DISPATCH UPDATE
		$insert_lcc = null;
		$insert_lcc["load_id"] = $load["id"];
		$insert_lcc["truck_id"] = $load["load_truck_id"];
		$insert_lcc["trailer_id"] = $load["load_trailer_id"];
		$insert_lcc["driver_id"] = $load["client_id"];
		$insert_lcc["user_id"] = $recorder_id;
		if($truck_fuel != 'Select')
		{
			$insert_lcc["truck_fuel_level"] = $truck_fuel;
		}
		$insert_lcc["truck_code_status"] = $truck_codes;
		$insert_lcc["location"] = $geocode["city"].", ".$geocode["state"];
		$insert_lcc["gps"] = $current_geopoint_goalpoint["gps"];
		$insert_lcc["on_hold"] = $_POST["on_hold"];
		$insert_lcc["recorded_datetime"] = $recorded_time;
		$insert_lcc["driver_answered"] = $_POST["driver_answer"];
		$insert_lcc["audio_guid"] = $update_guid;//JUST TEMPORARY FOR THE FILE GUID UPDATE
		//print_r($insert_du);
		
		db_insert_load_check_call($insert_lcc);
		
		$file_array = array();
		$file_array[] = "truck_code_guid";
		$file_array[] = "audio_guid";
		
		$update_lcc = null;
		foreach($file_array as $file_input)
		{
			//UPDATE DISPATCH UPDATE WITH FILE GUIDS
			//if(!empty($_FILES['rc_save_attachment_file']["type"]))
			if(!empty($_FILES[$file_input]["type"]))
			{
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
				$secure_file = null;
				//$post_name = 'truck_codes_guid';
				$post_name = $file_input;
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type1 = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = $file["name"];
				$category = "Load Check Call";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = 'All';
				$driver_permission = 'None';
				$secure_file = store_secure_ftp_file($post_name,$name,$type1,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
				
				//$update_load["rc_link"] = $secure_file["file_guid"];
				$update_lcc[$file_input] = $secure_file["file_guid"];
				
				//CREATE ATTACHMENT IN DB
				// $attachment = null;
				// $attachment["type"] = "load";
				// $attachment["attached_to_id"] = $load["id"];
				// $attachment["file_guid"] = $secure_file["file_guid"];
				// $attachment["attachment_name"] = "Rate Con";

				// db_insert_attachment($attachment);
				
				//$file_uploaded = TRUE;
			}
		}
		
		//GET THIS CHECK CALL
		$where = null;
		$where["audio_guid"] = $update_guid;
		$this_lcc = db_select_load_check_call($where);
		$this_lcc_id = $this_lcc["id"];
		
		//UPDATE DISPATCH UPDATE WITH FILE GUIDS
		$where = null;
		$where["id"] = $this_lcc_id;
		db_update_load_check_call($update_lcc,$where);
		
		echo "success!";
	}
	
	//ADD LOAD UPDATE
	function save_load_update()
	{
		
		
		$load_id = $_POST["load_id"];
		$current_geopoint_id = $_POST["current_geopoint_id"];
		$current_trailer_geopoint_id = $_POST["current_trailer_geopoint_id"];

		//echo $current_trailer_geopoint_id;
		
		save_load_update($load_id,$current_geopoint_id,$current_trailer_geopoint_id);
		
		
	}
	
	function display_dispatch_email()
	{
		$du_id = $_POST["du_id"];
		
		//GET DISPATCH UPDATE
		$where = null;
		$where["id"] = $du_id;
		$dispatch_update = db_select_dispatch_update($where);
		
		//GET LOAD
		$where = null;
		$where["id"] = $dispatch_update["load_id"];
		$load = db_select_load($where);
		
		$recipients = null;
		if(!empty($dispatch_update["client_email"]))
		{
			if(empty($recipients))
			{
				$recipients  .= $dispatch_update["client_email"];
			}
			else
			{
				$recipients  .= ", ".$dispatch_update["client_email"];
			}
		}
		
		if(!empty($dispatch_update["carrier_email"]))
		{
			if(empty($recipients))
			{
				$recipients  .= $dispatch_update["carrier_email"];
			}
			else
			{
				$recipients  .= ", ".$dispatch_update["carrier_email"];
			}
		}
		
		if(!empty($dispatch_update["fleet_manager_email"]))
		{
			if(empty($recipients))
			{
				$recipients  .= $dispatch_update["fleet_manager_email"];
			}
			else
			{
				$recipients  .= ", ".$dispatch_update["fleet_manager_email"];
			}
		}
		
		if(!empty($dispatch_update["driver_manager_email"]))
		{
			if(empty($recipients))
			{
				$recipients  .= $dispatch_update["driver_manager_email"];
			}
			else
			{
				$recipients  .= ", ".$dispatch_update["driver_manager_email"];
			}
		}
		
		$data['load'] = $load;
		$data['recipients'] = $recipients;
		$data['dispatch_update'] = $dispatch_update;
		$this->load->view('loads/load_plan_email_confirmation_dialog',$data);
	}
	
	function send_dispatch_email()
	{
		date_default_timezone_set('US/Mountain');
		
		$du_id = $_POST["du_id"];
		
		//GET DISPATCH UPDATE
		$where = null;
		$where["id"] = $du_id;
		$dispatch_update = db_select_dispatch_update($where);
		
		//GET LOAD
		$where = null;
		$where["id"] = $dispatch_update["load_id"];
		$load = db_select_load($where);
		
		$recipients = null;
		if(!empty($dispatch_update["client_email"]))
		{
			if(empty($recipients))
			{
				$recipients  .= $dispatch_update["client_email"];
			}
			else
			{
				$recipients  .= ", ".$dispatch_update["client_email"];
			}
		}
		
		if(!empty($dispatch_update["carrier_email"]))
		{
			if(empty($recipients))
			{
				$recipients  .= $dispatch_update["carrier_email"];
			}
			else
			{
				$recipients  .= ", ".$dispatch_update["carrier_email"];
			}
		}
		
		if(!empty($dispatch_update["fleet_manager_email"]))
		{
			if(empty($recipients))
			{
				$recipients  .= $dispatch_update["fleet_manager_email"];
			}
			else
			{
				$recipients  .= ", ".$dispatch_update["fleet_manager_email"];
			}
		}
		
		if(!empty($dispatch_update["driver_manager_email"]))
		{
			if(empty($recipients))
			{
				$recipients  .= $dispatch_update["driver_manager_email"];
			}
			else
			{
				$recipients  .= ", ".$dispatch_update["driver_manager_email"];
			}
		}
		
		
		//SEND LOAD DISPATCH EMAIL
		$message = $dispatch_update["email_html"];
		//$to = 'covax13@gmail.com';
		$to = $recipients;
		$subject = 'Load Plan for Load '.$load["customer_load_number"];
		//$message = "test";
		// $headers = "From: paperwork.dispatch@gmail.com\r\n";
		// //$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
		// $headers .= "CC: paperwork.dispatch@gmail.com\r\n";
		// $headers .= "MIME-Version: 1.0\r\n";
		// $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		//mail("covax13@gmail.com","FleetSmarts Now Does Email!","You better believe it! Guess who just figured out how to send emails from FleetSmarts!!","From: fleetsmarts@fleetsmarts.net");
		//mail($to, $subject, $message, $headers);
		
		$this->email->from("paperwork.dispatch@gmail.com","Dispatch");
		$this->email->to($to);
		$this->email->cc('paperwork.dispatch@gmail.com');
		$this->email->subject($subject);
		$this->email->message($message);
		$this->email->send();
		//echo $this->email->print_debugger();
		
		//UPDATE DISPATCH UPDATE
		$update = null;
		$update["email_sent_datetime"] = date("Y-m-d H:i:s");
		$where = null;
		$where["id"] = $du_id;
		db_update_dispatch_update($update,$where);
		
		//UPDATE LOAD WITH INITIAL DISPATCH (FIRST TIME ONLY) TIME AND DISPATCH SENT DATETIME (LATEST DISPATCH UPDATE)
		$recorded_time = date("Y-m-d H:i:s");
		$update_load = null;
		if(empty($load["initial_dispatch_datetime"]))
		{
			$update_load["initial_dispatch_datetime"] = $recorded_time;
			$update_load["status"] = "Pick Pending";
			$update_load["status_number"] = 3;
		}
		$update_load["dispatch_sent_datetime"] = $recorded_time;
		$where = null;
		$where["id"] = $load["id"];
		db_update_load($update_load,$where);
		
		echo "Email sent to ".$to;
	}
	
	
	
	
	//ONE-TIME SCRIPT ----------------------------------------------------------
	function change_load_status_numbers()
	{
		// echo "start1<Br>";
		// echo "start2<Br>";
		// $where = null;
		// //$where = "1 = 1";
		// $where["status"] = "Drop Pending";
		// $loads = db_select_loads($where);
		// echo "start3<Br>";
		// //print_r($loads);
		// echo "start4<Br>";
		// foreach($loads as $load)
		// {
			// echo $load["id"]."<Br>";
			// $update_load = null;
			// if($load["status"] == "Dropped")
			// {
				// $update_load["status_number"] = 5;
				// $where = null;
				// $where["id"] = $load["id"];
				// db_update_load($update_load,$where);
			// }
			// elseif($load["status"] == "Rate Con Pending")
			// {
				// $update_load["status_number"] = 1;
				// $where = null;
				// $where["id"] = $load["id"];
				// db_update_load($update_load,$where);
			// }
			// elseif($load["status"] == "Pick Pending")
			// {
				// if(empty($load["initial_dispatch_datetime"]))
				// {
					// $update_load["status_number"] = 2;
					// $update_load["status"] = "Dispatch Pending";
					// $where = null;
					// $where["id"] = $load["id"];
					// db_update_load($update_load,$where);
				// }
				// else
				// {
					// $update_load["status_number"] = 3;
					// $where = null;
					// $where["id"] = $load["id"];
					// db_update_load($update_load,$where);
				// }
			// }
			// elseif($load["status"] == "Drop Pending")
			// {
				// $update_load["status_number"] = 4;
				// $where = null;
				// $where["id"] = $load["id"];
				// db_update_load($update_load,$where);
			// }
			
			
			
			// echo $load["customer_load_number"]." updated!<br>";
		// }
	}
	
	
}