<?php

class Trippak extends MY_Controller 
{
	function index()
	{
		//GET ACTIVE CARRIERS FOR BILLED UNDER DROPDOWN
		$where = null;
		$where["type"] = "Carrier";
		$where["company_status"] = "Active";
		$carriers = db_select_companys($where,"company_side_bar_name");
		
		$carrier_options = array();
		$carrier_options["All"] = "All Carriers";
		foreach($carriers as $carrier)
		{
			$carrier_options[$carrier["id"]] = $carrier["company_side_bar_name"];
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
		
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['carrier_options'] = $carrier_options;
		$data['tab'] = 'Trippak';
		$data['title'] = "Trippak";
		$this->load->view('trippak_view',$data);
	}
	
	function load_trippak_report()
	{
		//FILTERS
		/*
			LOAD NUMBER
			CARRIER
			CLIENT (DRIVERS)
			FINAL DROP CITY
			TRUCK
			TRAILER
			SCAN DATETIME
			ZIP FILE?
		*/
		$status_dropdown = $_POST['status_dropdown'];
		$load_number = $_POST['search_term'];
		$carrier_dropdown = $_POST['carrier_dropdown'];
		$driver_dropdown = $_POST['driver_dropdown'];
		$scan_start_date = $_POST['scan_start_date_filter'];
		$scan_end_date = $_POST['scan_end_date_filter'];
		
		//CREATE WHERE CLAUSE FOR LOAD QUERY
		$where = "";
		
		if(!empty($status_dropdown))
		{
			if($status_dropdown == 'Complete')
			{
				$where .= " AND completion_datetime IS NOT NULL";
			}
			else if($status_dropdown == 'Incomplete')
			{
				$where .= " AND completion_datetime IS NULL";
			}
		}
		
		if($load_number != '')
		{
			$where .= " AND load_number = " . $load_number;
		}
		
		if($carrier_dropdown != 'All')
		{
			$where .= " AND carrier_id = " . $carrier_dropdown;
		}
		
		if($driver_dropdown != 'All')
		{
			$where .= " AND (driver_1_id = ".$driver_dropdown . " OR driver_2_id = ".$driver_dropdown.")";
		}
		
		if(!empty($scan_start_date))
		{
			$scan_start_date = date("Y-m-d G:i:s",strtotime($scan_start_date)+24*60*60);
			$where .= " AND scan_datetime >= '".$scan_start_date."' ";
		}
		
		if(!empty($scan_end_date))
		{
			$scan_end_date = date("Y-m-d G:i:s",strtotime($scan_end_date)+24*60*60);
			$where .= " AND scan_datetime <= '".$scan_end_date."' ";
		}
		
		$where = substr($where,4);
		$trippaks = db_select_trippaks($where,'scan_datetime DESC');
		
		$data['trippaks'] = $trippaks;
		$this->load->view('trippak/trippak_report',$data);
	}
	
	function open_details()
	{
		$trippak_id = $_POST['trippak_id'];
		
		$where = null;
		$where['id'] = $trippak_id;
		$trippak = db_select_trippak($where);
		
		$where = null;
		$where["type"] = "Carrier";
		$where["company_status"] = "Active";
		$carriers = db_select_companys($where,"company_side_bar_name");
		
		$carrier_options = array();
		$carrier_options["Select"] = "Select";
		foreach($carriers as $carrier)
		{
			$carrier_options[$carrier["id"]] = $carrier["company_side_bar_name"];
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
		
		$where = null;
		$where["client_status"] = "Active";
		$dd_all_clients = db_select_clients($where,"client_nickname");
		$secondary_clients_dropdown_options = array();
		$secondary_clients_dropdown_options["None"] = "None";
		foreach($dd_all_clients as $client)
		{
			$secondary_clients_dropdown_options[$client["id"]] = $client["client_nickname"];
		}
		
		$where = null;
		$where['customer_load_number'] = $trippak['load_number'];
		$load = db_select_load($where);

		$load_id = $load['id'];
		
		//GET ALL ATTACHMENTS FOR THIS TRAILER
		$where = null;
		$where['type'] = "Trippak";
		$where['attached_to_id'] = $trippak['id'];
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data['trippak'] = $trippak;
		$data['carrier_options'] = $carrier_options;
		$data['truck_dropdown_options'] = $truck_dropdown_options;
		$data['trailer_dropdown_options'] = $trailer_dropdown_options;
		$data['clients_dropdown_options'] = $clients_dropdown_options;
		$data['secondary_clients_dropdown_options'] = $secondary_clients_dropdown_options;
		$data['trippak'] = $trippak;
		$this->load->view('trippak/trippak_details',$data);
	}
	
	function refresh_row()
	{
		$trippak_id = $_POST["trippak_id"];
		
		$where = null;
		$where["id"] = $trippak_id;
		$trippak = db_select_trippak($where);
		
		$data['trippak'] = $trippak;
		$this->load->view('trippak/trippak_row',$data);
	}
	
	function save_trippak_edit()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$datetime = date('Y-m-d h:i:s');
		
		$trippak_id = $_POST["trippak_id"];
		$load_number = $_POST["edit_load_number"];
		$final_drop_city = $_POST["edit_final_drop_city"];
		$truck_id = $_POST["edit_truck_number"];
		$trailer_id = $_POST["edit_trailer_number"];
		$odometer = $_POST["edit_odometer"];
		$driver_1_id = $_POST["edit_driver_1"];
		$driver_2_id = $_POST["edit_driver_2"];
		$lumper_amount = $_POST["edit_lumper_amount"];
		
		//GET THIS TRIPPAK THAT WE ARE UPDATED
		$where = null;
		$where["id"] = $trippak_id;
		$trippak = db_select_trippak($where);
		
		if(!empty($load_number))
		{
			$where = null;
			$where['customer_load_number'] = $load_number;
			$load = db_select_load($where);
		}
		
		$where = null;
		$where['id'] = $truck_id;
		$truck = db_select_truck($where);
		
		if($lumper_amount > 0)
		{
			$has_lumper = 'True';
		}
		else
		{
			$has_lumper = 'False';
		}

		//CREATE TRIPPAK TO UPDATE
		$update_trippak = null;
		if(!empty($load))
		{
			$load_id = $load['id'];
			
			
			$where = null;
			$where['id'] = $load['client_id'];
			$client = db_select_client($where);
			
			$where = null;
			$where['id'] = $client['company_id'];
			$company = db_select_company($where);
			
			if($company)
			{
				$carrier_id = $company['id'];	
				$update_trippak['carrier_id'] = $company['id'];	
			}
			
			$update_trippak["load_number"] = $load_number;
			$update_trippak["load_id"] = $load['id'];
			
			$where = null;
			$where = "load_id = $load_id AND gp_type = 'Drop' AND gp_order = (select MIN(gp_order) from `goalpoint` where load_id = $load_id and gp_type = 'Drop')";
			$start_drop_goalpoint = db_select_goalpoint($where);

			if($start_drop_goalpoint)
			{
				$in_time = $start_drop_goalpoint['completion_time'];

				$update = null;
				$update['in_time'] = $in_time;
				$where = null;
				$where['id'] = $trippak['id'];
				db_update_trippak($update,$where);
			}

			$where = null;
			$where = "load_id = $load_id AND gp_type = 'Drop' AND gp_order = (select MAX(gp_order) from `goalpoint` where load_id = $load_id and gp_type = 'Drop')";
			$end_drop_goalpoint = db_select_goalpoint($where);

			if($end_drop_goalpoint)
			{
				$out_time = $end_drop_goalpoint['completion_time'];

				$update = null;
				$update['out_time'] = $out_time;
				$where = null;
				$where['id'] = $trippak['id'];
				db_update_trippak($update,$where);
			}

			$trippak = null;
			$where = null;
			$where['id'] = $trippak_id;
			$trippak = db_select_trippak($where);
		}
		if(empty($load_number))
		{
			$update_trippak["load_number"] = null;
			$update_trippak["load_id"] = null;
			$update_trippak["carrier_id"] = null;
		}
		$update_trippak["final_drop_city"] = $final_drop_city;
		if($truck_id == "Select")
		{
			$truck_id = null;
			$update_trippak["truck_id"] = null;
		}
		else
		{
			$update_trippak["truck_id"] = $truck_id;
		}
		$update_trippak["truck_number"] = $truck['truck_number'];
		$update_trippak["odometer"] = $odometer;
		if($trailer_id == "Select")
		{
			$trailer_id = null;
			$update_trippak["trailer_id"] = null;
		}
		else
		{
			$update_trippak["trailer_id"] = $trailer_id;
		}
		if($driver_1_id == "Select")
		{
			$driver_1_id = null;
			$update_trippak["driver_1_id"] = null;
		}
		else
		{
			$update_trippak["driver_1_id"] = $driver_1_id;
		}
		if($driver_2_id == "Select")
		{
			$driver_2_id = null;
			$update_trippak["driver_2_id"] = null;
		}
		else
		{
			$update_trippak["driver_2_id"] = $driver_2_id;
		}
		$update_trippak["driver_2_id"] = $driver_2_id;
		$update_trippak["has_lumper"] = $has_lumper;
		$update_trippak["lumper_amount"] = $lumper_amount;
		if(vars_not_empty($load_number,$load['id'],$carrier_id,$final_drop_city,$truck['truck_number'],$truck_id,$odometer,$trailer_id,$in_time,$out_time,$driver_1_id,$has_lumper,$lumper_amount))
		{
			$update_trippak['completion_datetime'] = $datetime;
		}
		else
		{
			$update_trippak['completion_datetime'] = NULL;
		}

		$update_trippak['completed_by_id'] = $this->session->userdata('user_id');
		
		//UPDATE TRIPPAK
		$where = null;
		$where["id"] = $trippak_id;
		db_update_trippak($update_trippak,$where);
		
		
		//echo "saved";
	}
	
	function validate_load()
	{
		$load_number = $_POST["load_number"];
		
		$where = null;
		$where['customer_load_number'] = $load_number;
		$load = db_select_load($where);
		
		if(!empty($load))
		{
			echo 'True';
		}
		else
		{
			echo 'False';
		}
	}
	
	function load_file_upload_dialog()
	{
		$trippak_id = $_POST["row_id"];
		
		//GET LOAD
		$where = null;
		$where["id"] = $trippak_id;
		$trippak = db_select_trippak($where);
			
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
		$data["trippak"] = $trippak;
		$data["upload_options"] = $upload_options;
		$this->load->view('trippak/trippak_attachment_dialog',$data);
	}
	
	function upload_trippak_attachment()
	{
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
		$post_name = 'attachment_file';
		$file = $_FILES[$post_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		//$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$title = $_POST["attachment_name"];
		$category = "Trippak Attachment";//TRUCK ATTACHEMENT OR TRAILER ATTACHMENT
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		//CREATE ATTACHMENT IN DB
		$attachment = null;
		$attachment["type"] = "Trippak";
		$attachment["attached_to_id"] = $_POST["trippak_id"];
		$attachment["file_guid"] = $secure_file["file_guid"];
		$attachment["attachment_name"] = $_POST["attachment_name"];

		db_insert_attachment($attachment);
		
		// $file_access_permission = null;
		// $file_access_permission['file_guid'] = $secure_file["file_guid"];
		// $file_access_permission['user_id'] = $this->session->userdata('user_id');
		// db_insert_file_access_permission($file_access_permission);
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		//load_upload_success_view();
		echo "File uploaded successfully!";
	}
}