<?php		


	
class People extends MY_Controller 
{

	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//GET OPTIONS FOR FLEET MANAGER DROPDOWN LIST
		$fleet_managers_where['role'] = "Fleet Manager";
		$fleet_managers = db_select_persons($fleet_managers_where);
		$fleet_manager_dropdown_options = array();
		$title = "Select";
		$fleet_manager_dropdown_options[0] = $title;
		foreach ($fleet_managers as $manager):
			$title = $manager['f_name']." ".$manager['l_name'];
			$fleet_manager_dropdown_options[$manager['id']] = $title;
		endforeach;
		
		//GET ALL CUSTOMER-VENDORS FOR DROPDOWN IN ADD NEW CUSTOMER/VENDOR DIALOG
		$where = null;
		$where = ' type = "customer-vendor" OR type = "Business" OR type = "Fleet Manager" ';
		$customer_vendors = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN OPTIONS FOR ADD CUSTOMER/VENDOR DROPDOWN
		$customer_vendor_options["Select"] = "Select";
		foreach($customer_vendors as $company)
		{
			$customer_vendor_options[$company['id']] = $company["company_side_bar_name"];
		}
		
		//GET CASH ACCOUNTS
		$where = null;
		$where = ' category = "Cash" AND parent_account_id IS NOT NULL ';
		$cash_accounts = db_select_accounts($where,"account_name");
		$cash_accounts_options = array();
		$cash_accounts_options["Select"] = "Select";
		foreach($cash_accounts as $account)
		{
			$title = $account["account_name"];
			$cash_accounts_options[$account['id']] = $title;
		}
		
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		//GET COOP PARENT ACCOUNT TO ASSIGN TO NEW BROKER ACCOUNT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["account_class"] = "Asset";
		$where["parent_account_id"] = NULL;
		//$where = " company_id = '$business_user_id' AND account_class = 'Asset' AND parent_account_id IS NULL ";
		$asset_accounts = db_select_accounts($where);
		
		$asset_account_options = array();
		$asset_account_options["Select"] = "Select";
		if(!empty($asset_accounts))
		{
			foreach($asset_accounts as $account)
			{
				$title = $account["account_name"];
				$asset_account_options[$account["id"]] = $title;
				//echo $option;
			}
		}
		
		$data['asset_account_options'] = $asset_account_options;
		$data['cash_accounts_options'] = $cash_accounts_options;
		$data['customer_vendor_options'] = $customer_vendor_options;
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$data['tab'] = 'Contacts';
		$data['title'] = "Contacts";
		$this->load->view('people_view',$data);
	}// end index()
	
	//SAVE EITHER A NEW CLIENT OR AN EXISTING ONE
	/**
	function save_client()
	{
		$client_id = $_POST['client_id'];
		$company_id = $_POST['company_id'];
		$user_id = $_POST['user_id'];
		$person_id = $_POST['person_id'];
		
		$client_status = $_POST['client_status_edit'];
		$short_name = $_POST['short_name_edit'];
		$f_name = $_POST['f_name_edit'];
		$l_name = $_POST['l_name_edit'];
		$username = $_POST['username_edit'];
		$password = $_POST['password_edit'];
		$fuel_card = $_POST['fuel_card_edit'];
		$pay_card = $_POST['pay_card_edit'];
		$phone_number = $_POST['phone_num_edit'];
		$phone_carrier = $_POST['phone_carrier_edit'];
		$email = $_POST['email_edit'];
		$home_address = $_POST['home_address_edit'];
		$date_of_birth = date('Y-m-j',strtotime($_POST['dob_edit']))." 00:00:00";
		$link_license = $_POST["link_license_edit"];
		$license_number = $_POST['license_number_edit'];
		$license_state = $_POST['license_state_edit'];
		$license_expiration = date('Y-m-j',strtotime($_POST['license_expiration_edit']))." 00:00:00";
		$cdl_since = date('Y-m-j',strtotime($_POST['cdl_since_edit']))." 00:00:00";
		$link_ssn = $_POST['link_ssn_edit'];
		$ssn = $_POST['ssn_edit'];
		$link_contract = $_POST['link_contract_edit'];
		$start_date = date('Y-m-j',strtotime($_POST['start_date_edit']))." 00:00:00";
		$end_date = date('Y-m-j',strtotime($_POST['end_date_edit']))." 00:00:00";
		$person_notes = $_POST['person_notes_edit'];
		
		$company_status = $_POST['company_status_edit'];
		$fleet_manager_id = $_POST['fleet_manager_edit'];
		$company_name = $_POST['company_name_edit'];
		$company_side_bar_name = $_POST['company_side_bar_name_edit'];
		$link_fein = $_POST["link_ein_edit"];
		$fein = $_POST['fein_edit'];
		$link_mc = $_POST["link_mc_edit"];
		$mc_number = $_POST['mc_edit'];
		$dot_number = $_POST['dot_edit'];
		$link_docket_pin = $_POST['link_docket_pin_edit'];
		$docket_pin = $_POST['docket_pin_edit'];
		$usdot_pin = $_POST['usdot_pin_edit'];
		$access_id = $_POST['access_id_edit'];
		$entity_number = $_POST['entity_number_edit'];
		$fl_login = $_POST['fl_username_edit'];
		$fl_password = $_POST['fl_password_edit'];
		$insurance_company = $_POST['insurance_company_edit'];
		$policy_number = $_POST['policy_number_edit'];
		$company_phone = $_POST['company_phone_edit'];
		$company_fax = $_POST['company_fax_edit'];
		$company_gmail = $_POST['company_gmail_edit'];
		$gmail_password = $_POST['gmail_password_edit'];
		$address = $_POST['address_edit'];
		$city = $_POST['city_edit'];
		$state = $_POST['state_edit'];
		$zip = $_POST['zip_edit'];
		$mailing_address = $_POST['mailing_address_edit'];
		$oregon_permit = $_POST['oregon_permit_edit'];
		$ucr_renewal_date = $_POST['ucr_edit'];
		$running_since = date('Y-m-j',strtotime($_POST['running_since_edit']))." 00:00:00";
		$link_aoo = $_POST['link_aoo_edit'];
		$company_notes = $_POST['company_notes_edit'];
		
		
		
		//IF NEW CLIENT
		if (empty($client_id))
		{
			//CREATE PERSON
			$person["f_name"] = $f_name;
			$person["l_name"] = $l_name;
			$person["phone_number"] = $phone_number;
			$person["phone_carrier"] = $phone_carrier;
			$person["email"] = $email;
			$person["home_address"] = $home_address;
			$person["date_of_birth"] = $date_of_birth;
			$person["ssn"] = $ssn;
			$person["role"] = "Client";
			$person["person_notes"] = $person_notes;
			$person["link_license"] = $link_license;
			$person["link_ss_card"] = $link_ssn;
			
			db_insert_person($person);
			$person = db_select_person($person);
			
			//CREATE USER
			$user["person_id"] = $person["id"];
			$user["username"] = $username;
			$user["password"] = $password;
			$user["user_status"] = "Active";
			
			db_insert_user($user);
			
			//CREATE COMPANY
			$company["person_id"] = $person["id"];
			$company["type"] = "Client";
			$company["company_name"] = $company_name;
			$company["company_side_bar_name"] = $company_side_bar_name;
			$company["fein"] = $fein;
			$company["docket_pin"] = $docket_pin;
			$company["usdot_pin"] = $usdot_pin;
			$company["access_id"] = $access_id;
			$company["entity_number"] = $entity_number;
			$company["fl_username"] = $fl_login;
			$company["fl_password"] = $fl_password;
			$company["address"] = $address;
			$company["city"] = $city;
			$company["state"] = $state;
			$company["zip"] = $zip;
			$company["mailing_address"] = $mailing_address;
			$company["company_phone"] = $company_phone;
			$company["company_fax"] = $company_fax;
			$company["company_status"] = $company_status;
			$company["company_notes"] = $company_notes;
			$company["link_aoo"] = $link_aoo;
			$company["link_ein_letter"] = $link_fein;
			$company["link_mc_letter"] = $link_mc;
			$company["link_docket_pin_letter"] = $link_docket_pin;
			
			db_insert_company($company);
			$company = db_select_company($company);
			
			
			
			//CREATE CLIENT
			$client["company_id"] = $company["id"];
			$client["client_nickname"] = $short_name;
			$client["fleet_manager_id"] = $fleet_manager_id;
			$client["company_gmail"] = $company_gmail;
			$client["gmail_password"] = $gmail_password;
			$client["mc_number"] = $mc_number;
			$client["dot_number"] = $dot_number;
			$client["fuel_card_number"] = $fuel_card;
			$client["pay_card_number"] = $pay_card;
			$client["license_state"] = $license_state;
			$client["license_number"] = $license_number;
			$client["license_expiration"] = $license_expiration;
			$client["cdl_since"] = $cdl_since;
			$client["insurance_company"] = $insurance_company;
			$client["policy_number"] = $policy_number;
			$client["oregon_permit"] = $oregon_permit;
			if(!empty($ucr_renewal_date))
			{
				$client["ucr_renewal_date"] = $ucr_renewal_date;
			}
			$client["running_since"] = $running_since;
			$client["start_date"] = $start_date;
			if(!empty($end_date))
			{
				$client["end_date"] = $end_date;
			}
			$client["client_status"] = $client_status;
			$client["link_contract"] = $link_contract;
			
			db_insert_client($client);
			$where = null;
			$where["company_id"] = $company["id"];
			$client = db_select_client($where);
			$client_id = $client["id"];
			
			//CREATE DEFAULT ACCOUNTS
			create_defualt_accounts($client_id);
			
			//UPDATE CLIENT WITH MAIN_PAY_ACCOUNT
			$where = null;
			$where["company_id"] = $company["id"];
			$where["account_name"] = "MSIOO";
			$main_account = db_select_account($where);
			
			$client["main_account"] = $main_account["id"];
			
			//CREATE DEFAULT CLENT FEE SETTINGS
			for ($i = 1; $i <= 10; $i++)
			{
				$this_setting["client_id"] = $client_id;
				$this_setting["fee_description"] = $_POST["fee_description_default_$i"];
				$this_setting["fee_amount"] = $_POST["fee_amount_default_$i"];
				$this_setting["fee_type"] = $_POST["fee_type_default_$i"];
				$this_setting["fee_tax"] = $_POST["fee_tax_default_$i"];
				if(!empty($this_setting["fee_description"]))
				{
					db_insert_client_fee_setting($this_setting);
				}
			}
			
			//CREATE ADDITIONAL CLIENT FEE SETTINGS
			for ($i = 1; $i <= 10; $i++)
			{
				$this_setting["client_id"] = $client_id;
				$this_setting["fee_description"] = $_POST["fee_description_add_$i"];
				$this_setting["fee_amount"] = $_POST["fee_amount_add_$i"];
				$this_setting["fee_type"] = $_POST["fee_type_add_$i"];
				$this_setting["fee_tax"] = $_POST["fee_tax_add_$i"];
				
				if(!empty($this_setting["fee_description"]))
				{
					db_insert_client_fee_setting($this_setting);
				}
			}
		}
		else //IF CLIENT ALREADY EXISTS
		{
			//GET THIS CLIENT
			$this_client_where["id"] = $client_id;
			$this_client = db_select_client($this_client_where);
		
			//SAVE USER
			$user["username"] = $username;
			$user["password"] = $password;
			
			$user_where["id"] = $user_id;
			db_update_user($user,$user_where);
			
			//SAVE PERSON
			$person["f_name"] = $f_name;
			$person["l_name"] = $l_name;
			$person["phone_number"] = $phone_number;
			$person["phone_carrier"] = $phone_carrier;
			$person["email"] = $email;
			$person["home_address"] = $home_address;
			$person["date_of_birth"] = $date_of_birth;
			$person["ssn"] = $ssn;
			$person["role"] = "Client";
			$person["person_notes"] = $person_notes;
			$person["link_license"] = $link_license;
			$person["link_ss_card"] = $link_ssn;
			
			$person_where["id"] = $person_id;
			db_update_person($person,$person_where);
			
			//SAVE COMPANY
			$company["type"] = "Client";
			$company["company_name"] = $company_name;
			$company["company_side_bar_name"] = $company_side_bar_name;
			$company["fein"] = $fein;
			$company["docket_pin"] = $docket_pin;
			$company["usdot_pin"] = $usdot_pin;
			$company["access_id"] = $access_id;
			$company["entity_number"] = $entity_number;
			$company["fl_username"] = $fl_login;
			$company["fl_password"] = $fl_password;
			$company["address"] = $address;
			$company["city"] = $city;
			$company["state"] = $state;
			$company["zip"] = $zip;
			$company["mailing_address"] = $mailing_address;
			$company["company_phone"] = $company_phone;
			$company["company_fax"] = $company_fax;
			$company["company_status"] = $company_status;
			$company["company_notes"] = $company_notes;
			$company["link_aoo"] = $link_aoo;
			$company["link_ein_letter"] = $link_fein;
			$company["link_mc_letter"] = $link_mc;
			$company["link_docket_pin_letter"] = $link_docket_pin;
			
			$company_where["id"] = $company_id;
			db_update_company($company,$company_where);
			
			//SAVE CLIENT
			$client["fleet_manager_id"] = $fleet_manager_id;
			$client["client_nickname"] = $short_name;
			$client["company_gmail"] = $company_gmail;
			$client["gmail_password"] = $gmail_password;
			$client["mc_number"] = $mc_number;
			$client["dot_number"] = $dot_number;
			$client["fuel_card_number"] = $fuel_card;
			$client["pay_card_number"] = $pay_card;
			$client["license_state"] = $license_state;
			$client["license_number"] = $license_number;
			$client["license_expiration"] = $license_expiration;
			$client["cdl_since"] = $cdl_since;
			$client["insurance_company"] = $insurance_company;
			$client["policy_number"] = $policy_number;
			$client["oregon_permit"] = $oregon_permit;
			$client["ucr_renewal_date"] = $ucr_renewal_date;
			$client["running_since"] = $running_since;
			$client["start_date"] = $start_date;
			if($end_date == "0000-00-00 00:00:00")
			{
				$client["end_date"] = null;
			}else
			{
				$client["end_date"] = $end_date;
			}
			$client["client_status"] = $client_status;
			$client["link_contract"] = $link_contract;
		
			$client_where["id"] = $client_id;
			db_update_client($client,$client_where);
			
			//SAVE EXISTING CLIENT FEE SETTINGS
			foreach ($this_client["client_fee_settings"] as $setting)
			{
				$this_setting = null;
				$setting_id = $setting["id"];
				if($_POST["fee_account_$setting_id"] != "Select Account")
				{
					$this_setting["account_id"] = $_POST["fee_account_$setting_id"];
				}
				$this_setting["fee_description"] = $_POST["fee_description_$setting_id"];
				$this_setting["fee_amount"] = $_POST["fee_amount_$setting_id"];
				$this_setting["fee_type"] = $_POST["fee_type_$setting_id"];
				$this_setting["fee_tax"] = $_POST["fee_tax_$setting_id"];
				
				$this_setting_where["id"] = $setting_id;
				if(!empty($this_setting["fee_description"]))
				{
					db_update_client_fee_setting($this_setting,$this_setting_where);
				}
			}
			
			//SAVE NEW CLIENT FEE SETTINGS
			for ($i = 1; $i <= 10; $i++)
			{
				$this_setting = null;
				if($_POST["fee_account_add_$i"] != "Select Account")
				{
					$this_setting["account_id"] = $_POST["fee_account_add_$i"];
				}
				$this_setting["client_id"] = $client_id;
				$this_setting["fee_description"] = $_POST["fee_description_add_$i"];
				$this_setting["fee_amount"] = $_POST["fee_amount_add_$i"];
				$this_setting["fee_type"] = $_POST["fee_type_add_$i"];
				$this_setting["fee_tax"] = $_POST["fee_tax_add_$i"];
				
				if(!empty($this_setting["fee_description"]))
				{
					db_insert_client_fee_setting($this_setting);
				}
			}
		} 
		
		redirect(base_url("index.php/people/index/details/$client_id"));
	}
	**/
	
	//CLIENT STATUS SELECTED
	function client_status_selected()
	{
		$status = $_POST["driver_type_dropdown"];
		
		//GET CLIENT LIST
		$where = null;
		if($status != "all")
		{
			$where["client_status"] = $status;
		}
		else
		{
			$where = "1 = 1"; //CHOOSE ALL
		}
		
		$clients = db_select_clients($where,"client_nickname");
		
		$data['clients'] = $clients;
		$this->load->view('people/client_list_div',$data);
		
	}//end client_status_selected()
	
	//LOAD UPPER LIST
	function load_upper_list()
	{
		$people_type = $_POST["people_type"];
		$status = "Active";
		
		if($people_type == "Main Driver") //MAIN DRIVER, CO-DRIVER, OR APPLICANT
		{
			//CREATE OPTIONS FOR STATUS DROPDOWN
			$type_options = array(
				'Main Driver'=> 'Main Drivers',
				'Co-Driver' => 'Co-Drivers',
				'Applicant' => 'Applicants',
			);
			
						
			//CREATE OPTIONS FOR DRIVER STATUS DROPDOWN
			$driver_status_options = array(
				'All'=> 'All Statuses',
				'Active'=> 'Active',
				'Pending Closure' => 'Pending Closure',
				'Closed' => 'Closed',
			);
			
			//CREATE OPTIONS FOR APPLICANT STATUS DROPDOWN
			$apllicant_status_options = array(
				'All'=> 'All',
				'All but closed' => 'All but closed',
				'Replied to Ad' => 'Replied to Ad',
				'Submitted App'=> 'Submitted App',
				'Communicating'=> 'Communicating',
				'Committed'=> 'Committed',
				'On the Truck'=> 'On the Truck',
				'Closed' => 'Closed',
			);
			
			$data["type"] = "All";
			$data["status"] = $status;
			$data["type_options"] = $type_options;
			$data["driver_status_options"] = $driver_status_options;
			$data["apllicant_status_options"] = $apllicant_status_options;
			$data["people_type"] = $people_type;
			$data["heading"] = "Drivers";
			$this->load->view('people/driver_upper_list',$data);
		}
		elseif($people_type == "Broker")
		{
			//CREATE OPTIONS FOR STATUS DROPDOWN
			$options = array(
				'All'	=> 'All',
				'Good'  => 'Good',
				'Bad'  => 'Bad',
				'Setup Pending'  	=> 'Setup Pending',
			);
			
			$data["status"] = "Good";
			$data["status_options"] = $options;
			$data["people_type"] = $people_type;
			$data["heading"] = "Brokers";
			$this->load->view('people/upper_list',$data);
		}
		elseif($people_type == "Business User")
		{
			//CREATE OPTIONS FOR STATUS DROPDOWN
			$options = array(
				'All'=> 'All',
				'Active'=> 'Active',
				'Inactive' => 'Inactive',
			);
			
			$data["status"] = $status;
			$data["status_options"] = $options;
			$data["people_type"] = $people_type;
			$data["heading"] = "Business Users";
			$this->load->view('people/upper_list',$data);
		}
		elseif($people_type == "customer-vendor")
		{
			//CREATE OPTIONS FOR STATUS DROPDOWN
			$options = array(
				'All'=> 'All',
				'Active'=> 'Active',
				'Inactive' => 'Inactive',
			);
			
			$data["status"] = $status;
			$data["status_options"] = $options;
			$data["people_type"] = $people_type;
			$data["heading"] = "Customers/Vendors";
			$this->load->view('people/upper_list',$data);
		}
		elseif($people_type == "Carrier")
		{
			//CREATE OPTIONS FOR STATUS DROPDOWN
			$options = array(
				'All'=> 'All',
				'Pending Setup'  	=> 'Pending Setup',
				'Active'  	=> 'Active',
				'Inactive'  => 'Inactive',
			);
			
			$data["status"] = $status;
			$data["status_options"] = $options;
			$data["people_type"] = $people_type;
			$data["heading"] = "Carriers";
			$this->load->view('people/upper_list',$data);
		}
		elseif($people_type == "Fleet Manager")
		{
			//CREATE OPTIONS FOR STATUS DROPDOWN
			$options = array(
				'All'=> 'All',
				'Active'=> 'Active',
				'Inactive' => 'Inactive',
			);
			
			$data["status"] = $status;
			$data["status_options"] = $options;
			$data["people_type"] = $people_type;
			$data["heading"] = "Fleet Managers";
			$this->load->view('people/upper_list',$data);
		}
		elseif($people_type == "Staff")
		{
			//CREATE OPTIONS FOR STATUS DROPDOWN
			$options = array(
				'All'=> 'All',
				'Active'=> 'Active',
				'Inactive' => 'Inactive',
			);
			
			$data["status"] = $status;
			$data["status_options"] = $options;
			$data["people_type"] = $people_type;
			$data["heading"] = "Staff";
			$this->load->view('people/upper_list',$data);
		}
		elseif($people_type == "Insurance Agent")
		{
			//CREATE OPTIONS FOR STATUS DROPDOWN
			$options = array(
				'All'=> 'All',
				'Active'=> 'Active',
				'Inactive' => 'Inactive',
			);
			
			$data["status"] = $status;
			$data["status_options"] = $options;
			$data["people_type"] = $people_type;
			$data["heading"] = "Insurance Agents";
			$this->load->view('people/upper_list',$data);
		}
		//NOTHING OUTSIDE OF THE IF STATEMENT
		
	}
	
	//LOAD FILE UPLOAD DIALOG
	function load_people_file_upload()
	{
		$entity_id = $_POST["company_id"];
		$entity_type = $_POST['person_type'];
		
		
		//echo $entity_type;
		
		if($entity_type == "carrier")
		{
			$where = null;
			$where['id'] = $entity_id;
			$entity = db_select_company($where);

			$upload_options = array();
			$upload_options["Select"] = "Select";
			$upload_options["link_aoo"] = "Articles of Organization";
			$upload_options["buy_sell_chain_guid"] = "Buy-Sell Chain";
			$upload_options["carrier_packet_guid"] = "Carrier Broker Packet";
			$upload_options["link_docket_pin_letter"] = "Docket PIN Letter";
			$upload_options["link_ein_letter"] = "FEIN Letter";
			$upload_options["insurance_cert_guid"] = "Insurance Cert";
			$upload_options["link_mc_letter"] = "MC Authority Letter";
			$upload_options["mcs_150_guid"] = "MCS-150";
			$upload_options["op_1_guid"] = "OP-1";
			$upload_options["oregon_permit_guid"] = "Oregon Permit";
			$upload_options["link_osbr"] = "OSBR";
			$upload_options["prof_of_ppb_guid"] = "Proof of PPB";
			$upload_options["ucr_guid"] = "UCR";
			$upload_options["link_usdot_pin_letter"] = "USDOT PIN Letter";
			$upload_options["Attachment"] = "Other Attachment";
		}
		else if($entity_type == "driver")
		{
			$where = null;
			$where['id'] = $entity_id;
			$entity = db_select_client($where);

			$upload_options = array();
			$upload_options["Select"] = "Select";
			$upload_options["link_license"] = "License";
			$upload_options["link_ss_card"] = "Social Security Card";
			$upload_options["medical_card_link"] = "Medical Card";
			$upload_options["driver_application_link"] = "Driver Application";
			$upload_options["drug_test_link"] = "Drug Test";
			$upload_options["contract_guid"] = "Driver Contract";
			$upload_options["credit_score_guid"] = "Credit Score";
			$upload_options["mvr_guid"] = "MVR";
			$upload_options["Attachment"] = "Other Attachment";
		}
		else
		{
			$where = null;
			$where['id'] = $entity_id;
			$entity = db_select_company($where);
			
			$upload_options = array();
			$upload_options["Select"] = "Select";
			$upload_options["Attachment"] = "Attachment";
		}
		
		$data = null;
		$data["upload_options"] = $upload_options;
		$data["entity_type"] = $entity_type;
		$data["entity"] = $entity;
		$this->load->view('people/people_attachment_div',$data);
		
	}
	
	function upload_people_attachment()
	{
		$set_office_permission = "All";
		
		//GET COMPANY ID
		if($_POST["entity_type"] == "carrier")
		{
			//GET COMPANY
			$where = null;
			$where["id"] = $_POST["entity_id"];
			$company = db_select_company($where);
			
			$set_category = "Carrier Attachment";
			
		}
		else if($_POST["entity_type"] == "broker")
		{
			//GET COMPANY
			$where = null;
			$where["id"] = $_POST["entity_id"];
			$company = db_select_company($where);
			
			$set_category = "Broker Attachment";
			
		}
		else if($_POST["entity_type"] == "customer_vendor")
		{
			//GET COMPANY
			$where = null;
			$where["id"] = $_POST["entity_id"];
			$company = db_select_company($where);
			
			$set_category = "Customer Vendor Attachment";
			
		}
		else if($_POST["entity_type"] == "driver")
		{
			//GET CLIENT
			$where = null;
			$where["id"] = $_POST["entity_id"];
			$client = db_select_client($where);
			
			//GET COMPANY
			$where = null;
			$where["id"] = $client["company_id"];
			$company = db_select_company($where);
			
			$set_category = "Driver Attachment";
		}
		else if($_POST["entity_type"] == "staff")
		{
			$set_office_permission = "Access List";
			
			//GET COMPANY
			$where = null;
			$where["id"] = $_POST["entity_id"];
			$company = db_select_company($where);
			
			$set_category = "Staff Attachment";
		}
		else if($_POST["entity_type"] == "business_user")
		{
			$set_office_permission = "Access List";
			
			//GET COMPANY
			$where = null;
			$where["id"] = $_POST["entity_id"];
			$company = db_select_company($where);
			
			$set_category = "Business User Attachment";
		}
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
		$post_name = 'attachment_file';
		$file = $_FILES[$post_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		//$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$title = $_POST["attachment_name"];
		$category = $set_category;
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = $set_office_permission;
		$driver_permission = 'None';
		$contract_secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		
		//CREATE ATTACHMENT IN DB
		$attachment = null;
		$attachment["type"] = "people";
		$attachment["attached_to_id"] = $company["id"];
		$attachment["file_guid"] = $contract_secure_file["file_guid"];
		$attachment["attachment_name"] = $_POST["attachment_name"];

		db_insert_attachment($attachment);
		
		//UPDATE CARRIER/COMPANY WITH FILE GUID FOR SELECTED UPLOAD TYPE
		if($_POST["entity_type"] == "carrier")
		{
			if($_POST["upload_type"] != "Attachment")
			{
				$update = null;
				$update[$_POST["upload_type"]] = $contract_secure_file["file_guid"];
				
				$where = null;
				$where["id"] = $company["id"];
				db_update_company($update,$where);
			}
		}
		else if($_POST["entity_type"] == "driver")
		{
			if($_POST["upload_type"] != "Attachment")
			{
				$update = null;
				$update[$_POST["upload_type"]] = $contract_secure_file["file_guid"];
				
				if($_POST["upload_type"] == "link_ss_card")
				{
					$where = null;
					$where["id"] = $company["person_id"];
					db_update_person($update,$where);
				}
				else
				{
					$where = null;
					$where["id"] = $client["id"];
					db_update_client($update,$where);
				}
			}
		}
		else if($_POST["entity_type"] == "staff")
		{
		}
		
		//IF
		if($set_office_permission == "Access List")
		{
			$file_access_permission = null;
			$file_access_permission['file_guid'] = $contract_secure_file["file_guid"];
			$file_access_permission['user_id'] = $this->session->userdata('user_id');
			db_insert_file_access_permission($file_access_permission);
		}
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		load_upload_success_view();
	}
	
	//LOAD PEOPLE LIST
	function load_people_list()
	{
		$people_type = $_POST["people_type"];
		$status = $_POST["status"];
		
		
		$data["people_type"] = $people_type;
		$data["status"] = $status;
		
		//echo $people_type;
		//echo $status;
		
		
		if($people_type == "Main Driver" || $people_type == "Co-Driver")
		{
			//echo $people_type;
			//echo $status;
			
			//GET LIST OF MAIN DRIVERS ACCORDING TO STATUS
			//$where["client_type"] = $people_type;
			$where = null;
			$where = " (client_type = 'Main Driver' OR client_type = 'Co-Driver') ";
			
			if(!empty($_POST["driver_search"]))
			{
				$where = $where." AND client_nickname LIKE '%".$_POST["driver_search"]."%'";
			}
			else
			{
				if($status != "All")
				{
					$where = $where." AND client_status = '".$status."' ";
				}
			}
			//echo $where;
			$data["people"] = db_select_clients($where,"client_nickname");
			
			$this->load->view('people/driver_list',$data);
		}
		// elseif($people_type == "Applicant")
		// {
			// //GET LIST OF MAIN DRIVERS ACCORDING TO STATUS
			// $where = null;
			// $where["client_type"] = $people_type;
			// if($status != "All")
			// {
				// if($status == "All but closed")
				// {
					// $where = " client_type = 'Applicant' AND client_status <> 'Closed' ";
				// }
				// else
				// {
					// $where["client_status"] = $status;
				// }
			// }
			// $data["people"] = db_select_clients($where,"client_nickname");
			
			// $this->load->view('people/driver_list',$data);
		// }
		elseif($people_type == "Carrier")
		{
			//GET LIST OF CARRIERS ACCORDING TO STATUS
			$where = null;
			if($status != "All")
			{
				$where["company_status"] = $status;
			}
			$where["type"] = $people_type;
			
			$data["carriers"] = db_select_companys($where,"company_side_bar_name");
			
			$this->load->view('people/carrier_list',$data);
		}
		elseif($people_type == "Business User")
		{
			//GET LIST OF FLEET MANAGER COMPANIES ACCORDING TO STATUS
			$where = null;
			if($status != "All")
			{
				$where["company_status"] = $status;
			}
			$where["type"] = "Business";
			
			$data["companies"] = db_select_companys($where,"company_side_bar_name");
			
			$this->load->view('people/business_user_list',$data);
		}
		elseif($people_type == "customer-vendor")
		{
			//GET LIST OF FLEET MANAGER COMPANIES ACCORDING TO STATUS
			$where = null;
			$where = " type = 'customer-vendor' AND NOT (category <=> 'Office staff')";
			
			if($status != "All")
			{
				$where = $where." AND company_status = '$status'";
			}
			//echo $where;
			$data["companies"] = db_select_companys($where,"company_side_bar_name");
			
			$this->load->view('people/customer_vendor_list',$data);
		}
		elseif($people_type == "Broker")
		{
			//GET LIST OF FLEET MANAGER COMPANIES ACCORDING TO STATUS
			$where = null;
			if($status == "All")
			{
				$where = " 1 = 1";
			}
			else if($status == "Active") //THE FIRST LIST LOAD
			{
				$where["status"] = "Good";
			}
			else
			{
				$where["status"] = $status;
			}
			
			//GET CUSTOMERS
			$customers = db_select_customers($where,"customer_name");
			
			$companies = array();
			foreach($customers as $customer)
			{
				//GET COMPANY
				$where = null;
				$where["id"] = $customer["company_id"];
				$company = db_select_company($where);
				
				$companies[] = $company;
			}
			
			$data["companies"] = $companies;
			
			$this->load->view('people/broker_list',$data);
		}
		elseif($people_type == "Fleet Manager")
		{
			//GET LIST OF FLEET MANAGER COMPANIES ACCORDING TO STATUS
			$where = null;
			if($status != "All")
			{
				$where["company_status"] = $status;
			}
			$where["type"] = $people_type;
			
			$data["fleet_managers"] = db_select_companys($where,"company_side_bar_name");
			
			$this->load->view('people/fleet_manager_list',$data);
		}
		elseif($people_type == "Staff")
		{
			//GET LIST OF STAFF COMPANIES ACCORDING TO STATUS
			$where = null;
			if($status != "All")
			{
				$where["company_status"] = $status;
			}
			
			//SWITH THESE TO INITALLY MARK THE PROPER GUYS AS OFFICE STAFF THROUGH THE SYSTEM
			$where["category"] = "Office Staff";
			//$where["type"] = "Vendor";
			
			$data["companies"] = db_select_companys($where,"company_side_bar_name");
			
			$this->load->view('people/staff_list',$data);
		}
		elseif($people_type == "Insurance Agent")
		{
			//GET LIST OF FLEET MANAGER COMPANIES ACCORDING TO STATUS
			$where = null;
			$where = " type = 'Insurance Agency' AND category = 'Insurance'";
			
			if($status != "All")
			{
				$where = $where." AND company_status = '$status'";
			}
			//echo $where;
			$data["companies"] = db_select_companys($where,"company_side_bar_name");
			
			$this->load->view('people/customer_vendor_list',$data);
		}
		
	} //end load_people_list()
	
	//LOAD SUMMARY VIEW FOR PEOPLE
	function load_people_summary_view()
	{
		$people_type = $_POST["people_type"];
		
		if($people_type == "Applicant")
		{
			//GET LIST OF MAIN DRIVERS ACCORDING TO STATUS
			$where = null;
			$where = " application_status IS NULL OR application_status <> 'Closed' ";
			$applications = db_select_driver_applications($where,"application_datetime DESC");

			$data["applications"] = $applications;
			
			$this->load->view('people/applicant_summary_view',$data);
		}
	}
	
	function load_driver_details_by_social($social)
	{
		//GET PERSON
		$where = null;
		$where["ssn"] = $social;
		$person = db_select_person($where);
		
		//GET COMPANY
		$where = null;
		$where["person_id"] = $person["id"];
		$company = db_select_company($where);
		
		//GET CLIENT
		$where = null;
		$where["company_id"] = $company["id"];
		$client = db_select_client($where);
		
		$this->load_driver_details($client["id"]);
	}
	
	//LOAD DRIVER DETAILS
	function load_driver_details($client_id)
	{
		//GET THIS CLIENT
		$this_client_where["id"] = $client_id;
		$this_client = db_select_client($this_client_where);
		
		//IF CLIENT IS AN APPLICANT
		if($this_client["client_type"] == "Applicant")
		{
			//LOAD APPLICANT VIEW
			
			//GET APPLICATION
			$where = null;
			$where["client_id"] = $client_id;
			$driver_app = db_select_driver_application($where);
			
			$data['this_client'] = $this_client;
			$data['driver_app'] = $driver_app;
			$this->load->view('people/driver_app_details',$data);
		}
		else
		{
			//LOAD DRIVER VIEW
			
			$where = null;
			$where["client_id"] = $client_id;
			$revenue_splits = db_select_revenue_splits($where);
			
			// //GET CLIENT ACCOUNTS
			// $where = null;
			// $where["company_id"] = $this_client["company_id"];
			// $client_accounts = db_select_accounts($where);
			
			// //GET CLIENT_EXPENSE ACCOUNTS
			// $where = null;
			// $where["category"] = "Client_Expense";
			// $client_expense_accounts = db_select_accounts($where);
			
			// //CREATE DROPDOWN LIST OF PAY ACCOUNTS
			// $client_account_options = array();
			// $client_account_options["Select Account"] = "Select Account";
			// foreach($client_accounts as $account)
			// {
				// $client_account_options[$account["id"]] = $account["account_name"];
			// }
			// foreach($client_expense_accounts as $account)
			// {
				// $client_account_options[$account["id"]] = $account["account_name"];
			// }
			
			//GET ALL USERS FOR USERNAME VALIDATION LIST
			$all_users_where[1] = true;
			$all_users = db_select_users($all_users_where);
			
			//GET OPTIONS FOR FLEET MANAGER DROPDOWN LIST
			$fleet_managers_where['role'] = "Fleet Manager";
			$fleet_managers = db_select_persons($fleet_managers_where);
			$fleet_manager_dropdown_options = array();
			$title = "Select";
			$fleet_manager_dropdown_options[0] = $title;
			foreach ($fleet_managers as $manager):
				$title = $manager['f_name']." ".$manager['l_name'];
				$fleet_manager_dropdown_options[$manager['id']] = $title;
			endforeach;
			
			//GET ALL ATTACHMENTS FOR THIS CLIENT
			$where = null;
			$where['type'] = "people";
			//$where['attached_to_id'] = $client_id;
			$where['attached_to_id'] = $this_client["company_id"];
			$attachments = db_select_attachments($where);
			
			$data['attachments'] = $attachments;
			$data["all_users"] = $all_users;
			$data['this_client'] = $this_client;
			$data['revenue_splits'] = $revenue_splits;
			$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
			//$data['client_account_options'] = $client_account_options;
			$this->load->view('people/driver_details',$data);
		}
			
		
	}
	
	//LOAD DRIVER EDIT
	function load_driver_edit()
	{
		$client_id = $_POST["client_id"];
		
		//GET THIS CLIENT
		$this_client_where["id"] = $client_id;
		$this_client = db_select_client($this_client_where);
		
		//IF CLIENT IS AN APPLICANT
		if($this_client["client_type"] == "Applicant")
		{
			//GET APPLICATION
			$where = null;
			$where["client_id"] = $client_id;
			$driver_app = db_select_driver_application($where);
			
			//GET OPTIONS FOR FLEET MANAGER DROPDOWN LIST
			$fleet_managers_where['role'] = "Fleet Manager";
			$fleet_managers = db_select_persons($fleet_managers_where);
			$fleet_manager_dropdown_options = array();
			$title = "Select";
			$fleet_manager_dropdown_options[0] = $title;
			foreach ($fleet_managers as $manager):
				$title = $manager['f_name']." ".$manager['l_name'];
				$fleet_manager_dropdown_options[$manager['id']] = $title;
			endforeach;
			
			$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
			$data['this_client'] = $this_client;
			$data['driver_app'] = $driver_app;
			$this->load->view('people/driver_app_edit',$data);
		}
		else //IF CLIENT IS NOT AN APPLICANT
		{
		
			$where = null;
			$where["client_id"] = $client_id;
			$revenue_splits = db_select_revenue_splits($where);
			
			// //GET CLIENT ACCOUNTS
			// $where = null;
			// $where["company_id"] = $this_client["company_id"];
			// $client_accounts = db_select_accounts($where);
			
			// //GET CLIENT_EXPENSE ACCOUNTS
			// $where = null;
			// $where["category"] = "Client_Expense";
			// $client_expense_accounts = db_select_accounts($where);
			
			// //CREATE DROPDOWN LIST OF PAY ACCOUNTS
			// $client_account_options = array();
			// $client_account_options["Select Account"] = "Select Account";
			// foreach($client_accounts as $account)
			// {
				// $client_account_options[$account["id"]] = $account["account_name"];
			// }
			// foreach($client_expense_accounts as $account)
			// {
				// $client_account_options[$account["id"]] = $account["account_name"];
			// }
			
			//GET ALL USERS FOR USERNAME VALIDATION LIST
			$all_users_where[1] = true;
			$all_users = db_select_users($all_users_where);
			
			//GET OPTIONS FOR FLEET MANAGER DROPDOWN LIST
			$fleet_managers_where['role'] = "Fleet Manager";
			$fleet_managers = db_select_persons($fleet_managers_where);
			$fleet_manager_dropdown_options = array();
			$title = "Select";
			$fleet_manager_dropdown_options[0] = $title;
			foreach ($fleet_managers as $manager):
				$title = $manager['f_name']." ".$manager['l_name'];
				$fleet_manager_dropdown_options[$manager['id']] = $title;
			endforeach;
			
			//GET DROPDOWN OF CARRIERS
			$where = null;
			$where["type"] = "Carrier";
			$carriers = db_select_companys($where,"company_side_bar_name");
			$carrier_options = array();
			$carrier_options["None"] = "None";
			foreach($carriers as $carrier)
			{
				$carrier_options[$carrier["id"]] = $carrier["company_side_bar_name"];
			}
			
			
			
			$owner_options = array(
				"Select" => "Select",
				"Fleet Manager" => "Fleet Manager",
				"MSIOO" => "MSIOO",
				"Driver" => "Driver",
			);
			
			$data["owner_options"] = $owner_options;
			$data["carrier_options"] = $carrier_options;
			$data["all_users"] = $all_users;
			@$data['this_client'] = $this_client;
			$data['revenue_splits'] = $revenue_splits;
			$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
			//$data['client_account_options'] = $client_account_options;
			$this->load->view('people/driver_edit',$data);
		}
		
	}
	
	//LOAD CARRIER DETAILS
	function load_carrier_details($carrier_id)
	{
		//GET CARRIER COMPANY
		$where = null;
		$where["id"] = $carrier_id;
		$carrier = db_select_company($where);
		
		//GET LIST OF OWNERS
		$where = null;
		$where["carrier_id"] = $carrier_id;
		$owners = db_select_clients($where);
		
		//GET ALL ATTACHMENTS FOR THIS CARRIER
		$where = null;
		$where['type'] = "people";
		$where['attached_to_id'] = $carrier_id;
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data["owners"] = $owners;
		$data["carrier"] = $carrier;
		$this->load->view('people/carrier_details',$data);
	}
	
	//LOAD CARRIER DETAILS
	function load_carrier_edit()
	{
		//GET CARRIER COMPANY
		$where = null;
		$where["id"] = $_POST["carrier_id"];
		$carrier = db_select_company($where);
		
		$data["carrier"] = $carrier;
		$this->load->view('people/carrier_edit',$data);
	}
	
	//LOAD FLEET MANAGER DETAILS
	function load_fleet_manager_details($company_id)
	{
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET USER
		$where = null;
		$where["person_id"] = $company["person_id"];
		$user = db_select_user($where);
		
		//GET THIS PERSON'S CORPORATE CARDS
		$where = null;
		$where["person_id"] = $company["person_id"];
		$cards = db_select_corporate_cards($where,"card_name");
		
		//GET ALL ATTACHMENTS FOR THIS FLEET MANAGER
		$where = null;
		$where['type'] = "people";
		$where['attached_to_id'] = $company_id;
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data['user'] = $user;
		$data['cards'] = $cards;
		$data["company"] = $company;
		$this->load->view('people/fleet_manager_details',$data);
	}
	
	//LOAD FLEET MANAGER EDIT
	function load_fleet_manager_edit()
	{
		$company_id = $_POST["company_id"];
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET THIS PERSON'S CORPORATE CARDS
		$where = null;
		$where["person_id"] = $company["person_id"];
		$cards = db_select_corporate_cards($where,"card_name");
		
		//GET ALL COMPANIES TO VALIDATE AGAINST DUPLICATES
		$where = null;
		$where["type"] = "Vendor";
		$companies = db_select_companys($where,"company_side_bar_name");
		
		//GET USER
		$where = null;
		$where["person_id"] = $company["person_id"];
		$user = db_select_user($where);
		
		//GET ALL USERS
		$where = null;
		$where = " 1 = 1 ";
		$all_users = db_select_users($where);
		
		$data['all_users'] = $all_users;
		$data['companies'] = $companies;
		$data['cards'] = $cards;
		$data["user"] = $user;
		$data["company"] = $company;
		$this->load->view('people/fleet_manager_edit',$data);
	}
	
	//LOAD STAFF DETAILS
	function load_staff_details($company_id)
	{
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET USER
		$where = null;
		$where["person_id"] = $company["person_id"];
		$user = db_select_user($where);
		
		//GET THIS PERSON'S CORPORATE CARDS
		$where = null;
		$where["person_id"] = $company["person_id"];
		$cards = db_select_corporate_cards($where,"card_name");
		
		//GET ALL ATTACHMENTS FOR THIS STAFF
		$where = null;
		$where['type'] = "people";
		$where['attached_to_id'] = $company_id;
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data['user'] = $user;
		$data['cards'] = $cards;
		$data["company"] = $company;
		
		if(user_has_permission("view staff in contacts tab"))
		{
			$this->load->view('people/staff_details',$data);
		}
		else
		{
			echo "You don't have permission to view this report.";
		}
	}
	
	//LOAD STAFF EDIT
	function load_staff_edit()
	{
		$company_id = $_POST["company_id"];
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET THIS PERSON'S CORPORATE CARDS
		$where = null;
		$where["person_id"] = $company["person_id"];
		$cards = db_select_corporate_cards($where,"card_name");
		
		
		//GET ALL COMPANIES TO VALIDATE AGAINST DUPLICATES
		$where = null;
		$where["type"] = "Vendor";
		$companies = db_select_companys($where,"company_side_bar_name");
		
		//GET USER
		$where = null;
		$where["person_id"] = $company["person_id"];
		$user = db_select_user($where);
		
		//GET ALL USERS
		$where = null;
		$where = " 1 = 1 ";
		$all_users = db_select_users($where);
		
		$data['all_users'] = $all_users;
		$data['companies'] = $companies;
		$data['cards'] = $cards;
		$data["user"] = $user;
		$data["company"] = $company;
		$this->load->view('people/staff_edit',$data);
	}
	
	//LOAD BUSINESS USER DETAILS
	function load_business_user_details($company_id)
	{
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET LIST OF CUSTOMERS FOR THIS BUSINESS
		$where = null;
		$where["business_id"] = $company_id;
		$where["relationship"] = "Customer";
		$related_customers = db_select_business_relationships($where);
		
		//GET LIST OF VENDORS FOR THIS BUSINESS
		$where = null;
		$where["business_id"] = $company_id;
		$where["relationship"] = "Vendor";
		$related_vendors = db_select_business_relationships($where);
		
		//GET LIST OF STAFF FOR THIS BUSINESS
		$where = null;
		$where["business_id"] = $company_id;
		$where["relationship"] = "Staff";
		$related_staff = db_select_business_relationships($where);
		
		//GET LIST OF VENDORS FOR THIS BUSINESS
		$where = null;
		$where["business_id"] = $company_id;
		$where["relationship"] = "Member";
		$related_members = db_select_business_relationships($where);
		
		// //GET LIST OF VENDORS FOR THIS BUSINESS
		// $where = null;
		// $where["business_id"] = $company_id;
		// $where["relationship"] = "Member Customer";
		// $related_member_customers = db_select_business_relationships($where);
		
		//GET ALL ATTACHMENTS FOR THIS BUSINESS USER
		$where = null;
		$where['type'] = "people";
		$where['attached_to_id'] = $company_id;
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data['related_vendors'] = $related_vendors;
		$data['related_customers'] = $related_customers;
		$data['related_staff'] = $related_staff;
		$data['related_members'] = $related_members;
		//$data['related_member_customers'] = $related_member_customers;
		$data["company"] = $company;
		
		if(user_has_permission("view and edit all business users"))
		{
			$this->load->view('people/business_user_details',$data);
		}
	}
	
	//LOAD BUSINESS USER EDIT
	function load_business_user_edit()
	{
		$company_id = $_POST["company_id"];
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET ALL COMPANIES TO VALIDATE AGAINST DUPLICATES
		$where = null;
		$where["type"] = "Vendor";
		$companies = db_select_companys($where,"company_side_bar_name");
		
		//GET LIST OF CUSTOMERS FOR THIS BUSINESS
		$where = null;
		$where["business_id"] = $company_id;
		$where["relationship"] = "Customer";
		$related_customers = db_select_business_relationships($where);
		
		//GET LIST OF VENDORS FOR THIS BUSINESS
		$where = null;
		$where["business_id"] = $company_id;
		$where["relationship"] = "Vendor";
		$related_vendors = db_select_business_relationships($where);
		
		$data['related_vendors'] = $related_vendors;
		$data['related_customers'] = $related_customers;
		$data['companies'] = $companies;
		$data["company"] = $company;
		
		if(user_has_permission("view and edit all business users"))
		{
			$this->load->view('people/business_user_edit',$data);
		}
	}
	
	//LOAD CUSTOMER/VENDOR DETAILS
	function load_customer_vendor_details($company_id)
	{
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET ALL ATTACHMENTS FOR THIS CUSTOMER/VENDOR
		$where = null;
		$where['type'] = "people";
		$where['attached_to_id'] = $company_id;
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data["company"] = $company;
		$this->load->view('people/customer_vendor_details',$data);
	}
	
	//LOAD CUSTOMER/VENDOR EDIT
	function load_customer_vendor_edit()
	{
		$company_id = $_POST["company_id"];
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET ALL COMPANIES TO VALIDATE AGAINST DUPLICATES
		$where = null;
		$where["type"] = "customer-vendor";
		$companies = db_select_companys($where,"company_side_bar_name");
		
		
		$data['companies'] = $companies;
		$data["company"] = $company;
		$this->load->view('people/customer_vendor_edit',$data);
	}
	
	//LOAD BROKER DETAILS
	function load_broker_details($company_id)
	{
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET CUSTOMER
		$where = null;
		$where["company_id"] = $company["id"];
		$customer = db_select_customer($where);
		
		//GET ALL ATTACHMENTS FOR THIS BROKER
		$where = null;
		$where['type'] = "people";
		$where['attached_to_id'] = $company_id;
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data['customer'] = $customer;
		$data["company"] = $company;
		$this->load->view('people/broker_details',$data);
	}
	
	//LOAD BUSINESS USER EDIT
	function load_broker_edit()
	{
		$company_id = $_POST["company_id"];
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET CUSTOMER
		$where = null;
		$where["company_id"] = $company["id"];
		$customer = db_select_customer($where);
		
		
		//GET ALL COMPANIES TO VALIDATE AGAINST DUPLICATES
		$where = null;
		$where["type"] = "Broker";
		$companies = db_select_companys($where,"company_side_bar_name");
		
		$data['customer'] = $customer;
		$data['companies'] = $companies;
		$data["company"] = $company;
		$this->load->view('people/broker_edit',$data);
	}
	
	function load_insurance_agent_details()
	{
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//GET ALL ATTACHMENTS FOR THIS CUSTOMER/VENDOR
		$where = null;
		$where['type'] = "people";
		$where['attached_to_id'] = $company_id;
		$attachments = db_select_attachments($where);
		
		$data['attachments'] = $attachments;
		$data["company"] = $company;
		$this->load->view('people/insurance_agent_details',$data);
	}
	
	
	//LOAD NEW RELATIONSHIP DIALOG
	function load_new_relationship_dialog()
	{
		//GET ALL CUSTOMER-VENDORS FOR DROPDOWN IN ADD NEW CUSTOMER/VENDOR DIALOG
		$where = null;
		$where = ' (type = "customer-vendor" OR type = "Business") AND (category <> "Office Staff") ';
		$customer_vendors = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN OPTIONS FOR ADD CUSTOMER/VENDOR DROPDOWN
		$customer_vendor_options["Select"] = "Select";
		if(!empty($customer_vendors))
		{
			foreach($customer_vendors as $company)
			{
				$customer_vendor_options[$company['id']] = $company["company_side_bar_name"];
			}
		}
		
		//GET ALL STAFF FOR DROPDOWN IN ADD NEW RELATIONSHIOP DIALOG
		$where = null;
		$where = ' category = "Office Staff" OR category = "Fleet Manager" ';
		$staff = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN OPTIONS FOR ADD CUSTOMER/VENDOR DROPDOWN
		$staff_options["Select"] = "Select";
		if(!empty($staff))
		{
			foreach($staff as $company)
			{
				$staff_options[$company['id']] = $company["company_side_bar_name"];
			}
		}
		
		//GET ALL DRIVERS AND CARRIERS FOR DROPDOWN IN ADD NEW RELATIONSHIOP DIALOG
		$where = null;
		$where = ' type = "Client" OR type = "Carrier" ';
		$members = db_select_companys($where,"company_side_bar_name");
		
		//CREATE DROPDOWN OPTIONS FOR ADD CUSTOMER/VENDOR DROPDOWN
		$member_options["Select"] = "Select";
		if(!empty($members))
		{
			foreach($members as $company)
			{
				$member_options[$company['id']] = $company["company_side_bar_name"];
			}
		}
	
		//GET ALL FLEET MANAGERS FOR DROPDOWN IN ADD NEW RELATIONSHIOP DIALOG
		$where = null;
		$where = ' type = "Fleet Manager"';
		$fleet_managers = db_select_companys($where,"company_side_bar_name");

		//CREATE DROPDOWN OPTIONS FOR ADD FLEET MANAGER DROPDOWN
		$fleet_manager_options["Select"] = "Select";
		if(!empty($fleet_managers))
		{
			foreach($fleet_managers as $company)
			{
				$fleet_manager_options[$company['id']] = $company["company_side_bar_name"];
			}
		}
		
		$data["company_id"] = $_POST["company_id"];
		$data["fleet_manager_options"] = $fleet_manager_options;
		$data["member_options"] = $member_options;
		$data["staff_options"] = $staff_options;
		$data["customer_vendor_options"] = $customer_vendor_options;
		$this->load->view('people/new_relationship_dialog',$data);
	}
	
	//ADD BUSINESS RELATIONSHIP TO BUSINESS
	function add_customer_vendor()
	{
		$business_id = $_POST["business_id"];
		$relationship = $_POST["relationship"];
		
		if($relationship == "Customer" || $relationship == "Vendor")
		{
			$related_business_id = $_POST["customer_vendor"];
		}
		else if($relationship == "Staff")
		{
			$related_business_id = $_POST["staff"];
		}
		else if($relationship == "Member")
		{
			$related_business_id = $_POST["member"];
		}
		
		
		//INSERT NEW BUSINESS RELATIONSHIP
		$insert = null;
		$insert["business_id"] = $business_id;
		$insert["relationship"] = $relationship;
		$insert["related_business_id"] = $related_business_id;
		db_insert_business_relationship($insert);
		
	}
	
	//ADD DRIVER
	function add_driver()
	{
	
		//if(user_has_permission("Create Driver"))
		//{
			
			//CHECK TO MAKE SURE THIS SOCIAL IS NOT ALREADY IN DB
			//GET PERSON
			$where = null;
			$where["ssn"] = $_POST["social"];
			$person = db_select_person($where);
			if(empty($person))
			{
				//CREATE PERSON
				$person["f_name"] = $_POST["first_name"];
				$person["l_name"] = $_POST["last_name"];
				$person["role"] = "Client";
				$person["ssn"] = $_POST["social"];
				
				db_insert_person($person);
				$person = db_select_person($person);
				
				//CREATE USER
				$user["person_id"] = $person["id"];
				$user["user_status"] = "Active";
				
				db_insert_user($user);
				
				//CREATE COMPANY
				$company["person_id"] = $person["id"];
				$company["type"] = "Client";
				$company["company_name"] = $_POST["side_bar_name"];
				$company["company_side_bar_name"] = $_POST["side_bar_name"];
				
				db_insert_company($company);
				$company = db_select_company($company);
				
				//CREATE CLIENT
				$client["company_id"] = $company["id"];
				$client["client_nickname"] = $_POST["side_bar_name"];
				$client["client_type"] = $_POST["driver_type"];
				$client["client_status"] =  $_POST["driver_status"];
				if($_POST["driver_type"] == "Applicant")
				{
					$client["client_status"] =  "Replied to Ad";
				}
				
				db_insert_client($client);
				$where = null;
				$where["company_id"] = $company["id"];
				$client = db_select_client($where);
				$client_id = $client["id"];
				
				
				//SAVE CONTRACT FILE TO DB
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
				$post_name = 'attachment_file';
				$file = $_FILES[$post_name];
				$name = str_replace(' ','_',$file["name"]);
				$type = $file["type"];
				//$title = pathinfo($file["name"], PATHINFO_FILENAME);
				$title = "Contract ".$client["client_nickname"];
				$category = "Driver Attachment";
				$local_path = $file["tmp_name"];
				$server_path = '/edocuments/';
				$office_permission = "All";
				$driver_permission = 'None';
				$contract_secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
				
				
				//CREATE ATTACHMENT IN DB
				$attachment = null;
				$attachment["type"] = "people";
				$attachment["attached_to_id"] = $company["id"];
				$attachment["file_guid"] = $contract_secure_file["file_guid"];
				$attachment["attachment_name"] = "Contract ".$client["client_nickname"];

				db_insert_attachment($attachment);
				
				$update = null;
				$update["contract_guid"] = $contract_secure_file["file_guid"];
				
				$where = null;
				$where["id"] = $client["id"];
				db_update_client($update,$where);
					
				
				//CREATE DEFAULT ACCOUNTS
				create_default_accounts($client_id);
				
				//CREATE DRIVER APP
				$driver_app = null;
				$driver_app["client_id"] = $client_id;
				$driver_app["f_name"] = $_POST["first_name"];
				$driver_app["l_name"] = $_POST["last_name"];
				db_insert_driver_application($driver_app);
				
				//$this->load_driver_details($client_id);
				
				//DISPLAY UPLOAD SUCCESS MESSAGE
				load_upload_success_view();
			}
			else
			{
				echo "This Social Security Number already exists in the system.";
			}
		
			
			
		//}
		//else
		//{
		//	echo "<span style='font-weight:bold; color:red;'>You don't have permission to create new drivers in the system.</span>";
		//}

	}//end add_driver()
	
	//ADD COMPANY - BUSINESS, BROKER, CARRIER, CUSTOMER, VENDOR, DRIVER, STAFF
	function create_new_company()
	{
		$company_type = $_POST["company_type"];
	
		//CREATE COMPANY
		$company["type"] = $company_type;
		$company["category"] = $company_type;
		$company["company_status"] = $_POST["carrier_status_add"];
		$company["company_name"] = $_POST["company_name_add"];
		$company["company_side_bar_name"] = $_POST["company_side_bar_name_add"];
		
		db_insert_company($company);
		$company = db_select_company($company);
		
		if($company_type == "Business")
		{
			$this->load_business_user_details($company["id"]);
		}
		else if($company_type == "Broker")
		{
			//INSERT NEW CUSTOMER INTO DB
			$broker = null;
			$broker["company_id"] = $company["id"];
			$broker["customer_name"] = $_POST["company_name_add"];
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
			
			
			$this->load_broker_details($company["id"]);
		}
		else if($company_type == "Carrier")
		{
			$this->load_carrier_details($company["id"]);
		}
		else if($company_type == "customer_vendor")
		{
			//UPDATE COMPANY WITH PROPER TYPE (customer-vendor)
			$update = null;
			$update["type"] = "customer-vendor";
			
			$where = null;
			$where["id"] = $company["id"];
			db_update_company($update,$where);
			
			$this->load_customer_vendor_details($company["id"]);
		}
		else if($company_type == "Driver")
		{
			$this->load_driver_details($company["id"]);
		}
		else if($company_type == "Staff")
		{
			$this->load_staff_details($company["id"]);
		}
		
	}//end add_carrier()
	
	//ADD NEW STAFF
	function add_staff()
	{
		//CREATE PERSON
		$person["f_name"] = $_POST["staff_first_name"];
		$person["l_name"] = $_POST["staff_last_name"];
		$person["role"] = "Office Staff";
		db_insert_person($person);
		
		$new_person = db_select_person($person);
		
		//CREATE USER
		$user['person_id'] = $new_person["id"];
		$user["user_status"] = "Active";
		db_insert_user($user);
		
		//CREATE NEW VENDOR
		$vendor['person_id'] = $new_person["id"];
		$vendor['company_name'] = $_POST["staff_first_name"]." ".$_POST["staff_last_name"];
		$vendor['company_side_bar_name'] = $_POST["staff_first_name"]." ".$_POST["staff_last_name"];
		$vendor['company_status'] = "Active";
		$vendor['type'] = "Vendor";
		$vendor['category'] = "Office Staff";
		db_insert_company($vendor);
		
		$new_vendor = db_select_company($vendor);
		
		//CREATE PAY ACCOUNT FOR STAFF
		$account = null;
		$account['company_id'] = $new_vendor["id"];
		$account['account_type'] = "Business";
		$account['category'] = "Vendor";
		$account['vendor_id'] = $new_vendor["id"];
		$account['account_status'] = "Active";
		$account['account_name'] = "Pay (".$new_person["f_name"]." ".$new_person["l_name"].")";
		// db_insert_account($account);
		
		$this->load_staff_details($new_vendor["id"]);
	}
	
	function add_insurance_agent()
	{
		$agent_company = null;
		$agent_company["company_status"] = "Active";
		$agent_company["category"] = "Insurance";
		$agent_company["type"] = "Insurance Agency";
		$agent_company["company_name"] = $_POST["agency_name"];
		$agent_company["company_side_bar_name"] = $_POST["agency_name"];
		$agent_company["contact"] = $_POST["contact_name"];
		$agent_company["company_email"] = $_POST["contact_email"];
		$agent_company["company_phone"] = $_POST["contact_phone"];
		$agent_company["company_notes"] = $_POST["agent_company_notes"];
		db_insert_company($agent_company);
		
		
		$where = null;
		$where["company_name"] = $_POST["agency_name"];
		$where["company_email"] = $_POST["contact_email"];
		$where["company_phone"] = $_POST["contact_phone"];
		$company = db_select_company($where);
		
		$this->load_customer_vendor_details($company["id"]);
		
	}
	
	//SAVE DRIVER
	function save_driver()
	{
		$client_id = $_POST['client_id'];
		$user_id = $_POST['user_id'];
		$person_id = $_POST['person_id'];
		
		$short_name = $_POST['short_name_edit'];
		$client_type = $_POST['client_type_edit'];
		$pay_structure = $_POST['pay_structure_dropdown'];
		$profit_split = $_POST['profit_split_edit'];
		$client_status = $_POST['client_status_edit'];
		$dropdown_status = $_POST['dropdown_status_edit'];
		$fleet_manager_id = $_POST['fleet_manager_edit'];
		$f_name = $_POST['f_name_edit'];
		$l_name = $_POST['l_name_edit'];
		$fuel_card_name = $_POST['fuel_card_name_edit'];
		$fuel_card_number = $_POST['fuel_card_number_edit'];
		$pay_card = $_POST['pay_card_edit'];
		$home_phone_number = $_POST['home_phone_num_edit'];
		$phone_number = $_POST['phone_num_edit'];
		$phone_carrier = $_POST['phone_carrier_edit'];
		$email = $_POST['email_edit'];
		$home_address = $_POST['home_address_edit'];
		$home_city = $_POST['home_city_edit'];
		$home_state = $_POST['home_state_edit'];
		$home_zip = $_POST['home_zip_edit'];
		$date_of_birth = date('Y-m-j',strtotime($_POST['dob_edit']))." 00:00:00";
		$license_number = $_POST['license_number_edit'];
		$license_state = $_POST['license_state_edit'];
		$license_expiration = date('Y-m-j',strtotime($_POST['license_expiration_edit']))." 00:00:00";
		$cdl_since = date('Y-m-j',strtotime($_POST['cdl_since_edit']))." 00:00:00";
		$years_of_experience = $_POST['years_of_experience_edit'];
		$desired_company_name = $_POST['desired_company_name_edit'];
		$ssn = $_POST['ssn_edit'];
		$start_date = date('Y-m-j',strtotime($_POST['start_date_edit']))." 00:00:00";
		$end_date = date('Y-m-j',strtotime($_POST['end_date_edit']))." 00:00:00";
		$first_full_settlement_date = date('Y-m-j',strtotime($_POST['first_full_settlement_date_edit']))." 00:00:00";
		//$link_license = $_POST["link_license_edit"];
		$carrier_id = $_POST["carrier_edit"];
		//$link_ss_card = $_POST['link_ssn_edit'];
		//$medical_card_link = $_POST['link_medical_card_edit'];
		//$link_application = $_POST['link_application_edit'];
		//$drug_test_link = $_POST['link_drug_test_edit'];
		//$link_contract = $_POST['link_contract_edit'];
		$person_notes = $_POST['person_notes_edit'];
		$emergency_contact_name = $_POST['emergency_contact_name_edit'];
		$emergency_contact_number = $_POST['emergency_contact_phone_edit'];
		
		if($carrier_id == "None")
		{
			$carrier_id = null;
		}
		else
		{
			//UPDATE CARRIER WITH PERSON ID, IF SELECTED
			$company_update["person_id"] = $person_id;
			
			$where = null;
			$where["id"] = $carrier_id;
			//db_update_company($company_update,$where);
		}
		
		$username = $_POST['username_edit'];
		$password = $_POST['password_edit'];
		
		//GET THIS CLIENT
		$this_client_where["id"] = $client_id;
		$this_client = db_select_client($this_client_where);
	
		//SAVE USER
		$user["username"] = $username;
		$user["password"] = $password;
		
		$user_where["id"] = $user_id;
		db_update_user($user,$user_where);
		
		
		//SAVE PERSON
		$person["f_name"] = $f_name;
		$person["l_name"] = $l_name;
		$person["home_phone"] = $home_phone_number;
		$person["phone_number"] = $phone_number;
		$person["phone_carrier"] = $phone_carrier;
		$person["email"] = $email;
		$person["home_address"] = $home_address;
		$person["home_city"] = $home_city;
		$person["home_state"] = $home_state;
		$person["home_zip"] = $home_zip;
		$person["date_of_birth"] = $date_of_birth;
		$person["ssn"] = $ssn;
		$person["role"] = "Client";
		$person["person_notes"] = $person_notes;
		$person["emergency_contact_name"] = $emergency_contact_name;
		$person["emergency_contact_phone"] = $emergency_contact_number;
		//$person["link_ss_card"] = $link_ss_card;
		//$person["signature_guid"] = $signature_secure_file["file_guid"];
		//$person["initials_guid"] = $initials_secure_file["file_guid"];
		
		$person_where["id"] = $person_id;
		db_update_person($person,$person_where);
		
		
		//SAVE CLIENT
		$client["client_type"] = $client_type;
		$client["pay_structure"] = $pay_structure;
		$client["profit_split"] = $profit_split;
		$client["client_status"] = $client_status;
		$client["dropdown_status"] = $dropdown_status;
		$client["fleet_manager_id"] = $fleet_manager_id;
		$client["carrier_id"] = $carrier_id;
		$client["client_nickname"] = $short_name;
		$client["fuel_card_name"] = $fuel_card_name;
		$client["fuel_card_number"] = $fuel_card_number;
		$client["pay_card_number"] = $pay_card;
		$client["bigroad_username"] = $_POST["bigroad_username_edit"];
		$client["bigroad_password"] = $_POST["bigroad_password_edit"];
		$client["license_state"] = $license_state;
		$client["license_number"] = $license_number;
		$client["license_expiration"] = $license_expiration;
		$client["cdl_since"] = $cdl_since;
		$client["years_of_experience"] = $years_of_experience;
		$client["desired_company_name"] = $desired_company_name;
		$client["start_date"] = $start_date;
		if($end_date == "0000-00-00 00:00:00")
		{
			$client["end_date"] = null;
		}
		else
		{
			$client["end_date"] = $end_date;
		}
		$client["first_full_settlement_date"] = $first_full_settlement_date;
		if(!empty($_POST["credit_score_edit"]))
		{
			$client["credit_score"] = $_POST["credit_score_edit"];
		}
		else
		{
			$client["credit_score"] = null;
		}
		if($_POST["num_of_violations_edit"] != 'Select')
		{
			$client["number_of_violations"] = $_POST["num_of_violations_edit"];
		}
		else
		{
			$client["number_of_violations"] = null;
		}
		//$client["link_license"] = $link_license;
		//$client["medical_card_link"] = $medical_card_link;
		//$client["driver_application_link"] = $link_application;
		//$client["drug_test_link"] = $drug_test_link;
		//$client["link_contract"] = $link_contract;
	
		$client_where["id"] = $client_id;
		db_update_client($client,$client_where);
		
		//GET NEWLY UPDATED CLIENT
		$this_client = db_select_client($client_where);
		
		//UPDATE COMPANY SIDE BAR NAME WITH CLIENT NICKNAME
		$update_company = null;
		$update_company["company_side_bar_name"] = $this_client["client_nickname"];
		
		$where = null;
		$where["id"] = $this_client["company_id"];
		db_update_company($update_company,$where);
		
		
		$where = null;
		$where["client_id"] = $this_client["id"];
		$revenue_splits = db_select_revenue_splits($where);
		
		//SAVE EXISTING REVENUE SPLITS
		if(!empty($revenue_splits))
		{
			foreach($revenue_splits as $revenue_split)
			{
				$rs_id = $revenue_split["id"];
				
				//IF PERCENTAGE ISN'T EMPTY
				if(!empty($_POST["rs_percentage_$rs_id"]))
				{
					$account_where = null;
				
					if($_POST["rs_owner_$rs_id"] == "Fleet Manager")
					{
						//GET FLEETMANAGER COMPANY
						$where = null;
						$where["person_id"] = $this_client["fleet_manager_id"];
						$fm_company = db_select_company($where);
						
						$owner_id = $fm_company["id"];
						
						$account_where["company_id"] = $owner_id;
						$account_where["category"] = "Pay";
						
					}
					
					if($_POST["rs_owner_$rs_id"] == "MSIOO")
					{
						//GET MSIOO ACCOUNT
						$where = null;
						$where["company_name"] = "Management Services";
						$msioo_company = db_select_company($where);
			
						$owner_id = $msioo_company["id"];
						
						$account_where["company_id"] = $owner_id;
						$account_where["category"] = "Profit";
					
					}
					
					if($_POST["rs_owner_$rs_id"] == "Driver")
					{
						//GET DRIVER COMPANY
						$owner_id = $this_client["company_id"];
						
						$account_where["company_id"] = $owner_id;
						$account_where["category"] = "Pay";
					}
					
					$account = db_select_account($account_where);
					$account_id = $account["id"];
				
					$this_split = null;
					$this_split["owner_type"] = $_POST["rs_owner_$rs_id"];
					$this_split["owner_id"] = $owner_id;
					$this_split["description"] = $_POST["rs_desc_$rs_id"];
					$this_split["percent"] = $_POST["rs_percentage_$rs_id"]/100;
					$this_split["account_id"] = $account_id;
					
					$this_split_where["id"] = $rs_id;
					db_update_revenue_split($this_split,$this_split_where);
				}
			}
		}
		
		//SAVE NEW REVENUE SPLITS
		for ($i = 1; $i <= 10; $i++)
		{
			$rs_id = $i;
			
			//IF PERCENTAGE ISN'T EMPTY
			if(!empty($_POST["add_rs_percentage_$rs_id"]))
			{
				$account_where = null;
			
				if($_POST["add_rs_owner_$rs_id"] == "Fleet Manager")
				{
					//GET FLEETMANAGER COMPANY
					$where = null;
					$where["person_id"] = $this_client["fleet_manager_id"];
					$fm_company = db_select_company($where);
					
					$owner_id = $fm_company["id"];
					
					$account_where["company_id"] = $owner_id;
					$account_where["category"] = "Pay";
					
				}
				
				if($_POST["add_rs_owner_$rs_id"] == "MSIOO")
				{
					//GET MSIOO ACCOUNT
					$where = null;
					$where["company_name"] = "Management Services";
					$msioo_company = db_select_company($where);
		
					$owner_id = $msioo_company["id"];
					
					$account_where["company_id"] = $owner_id;
					$account_where["category"] = "Profit";
				
				}
				
				if($_POST["add_rs_owner_$rs_id"] == "Driver")
				{
					//GET DRIVER COMPANY
					$owner_id = $this_client["company_id"];
					
					$account_where["company_id"] = $owner_id;
					$account_where["category"] = "Pay";
				}
				
				$account = db_select_account($account_where);
				$account_id = $account["id"];
			
				$this_split = null;
				$this_split["client_id"] = $this_client["id"];
				$this_split["owner_type"] = $_POST["add_rs_owner_$rs_id"];
				$this_split["owner_id"] = $owner_id;
				$this_split["description"] = $_POST["add_rs_desc_$rs_id"];
				$this_split["percent"] = $_POST["add_rs_percentage_$rs_id"]/100;
				$this_split["account_id"] = $account_id;
				
				db_insert_revenue_split($this_split);
			}
		}
		
		/**
		//SAVE EXISTING CLIENT FEE SETTINGS
		foreach ($this_client["client_fee_settings"] as $setting)
		{
			$this_setting = null;
			$setting_id = $setting["id"];
			if($_POST["fee_account_$setting_id"] != "Select Account")
			{
				$this_setting["account_id"] = $_POST["fee_account_$setting_id"];
			}
			$this_setting["fee_description"] = $_POST["fee_description_$setting_id"];
			$this_setting["fee_amount"] = $_POST["fee_amount_$setting_id"];
			$this_setting["fee_type"] = $_POST["fee_type_$setting_id"];
			$this_setting["fee_tax"] = $_POST["fee_tax_$setting_id"];
			
			$this_setting_where["id"] = $setting_id;
			if(!empty($this_setting["fee_description"]))
			{
				db_update_client_fee_setting($this_setting,$this_setting_where);
			}
		}
		
		//SAVE NEW CLIENT FEE SETTINGS
		for ($i = 1; $i <= 10; $i++)
		{
			$this_setting = null;
			if($_POST["fee_account_add_$i"] != "Select Account")
			{
				$this_setting["account_id"] = $_POST["fee_account_add_$i"];
			}
			$this_setting["client_id"] = $client_id;
			$this_setting["fee_description"] = $_POST["fee_description_add_$i"];
			$this_setting["fee_amount"] = $_POST["fee_amount_add_$i"];
			$this_setting["fee_type"] = $_POST["fee_type_add_$i"];
			$this_setting["fee_tax"] = $_POST["fee_tax_add_$i"];
			
			if(!empty($this_setting["fee_description"]))
			{
				db_insert_client_fee_setting($this_setting);
			}
		}
		**/
		
		$this->load_driver_details($client_id);
		
		
	}//end save_driver()
	
	//SAVE APPLICANT
	function save_applicant()
	{
		$client_id = $_POST['client_id'];
		//$client_id = 212;
		
		$client_type = $_POST['client_type_edit'];
		$client_status = $_POST['client_status_edit'];
		$fleet_manager_id = $_POST['fleet_manager_edit'];
		
		//SAVE CLIENT
		$client["client_type"] = $client_type;
		$client["client_status"] = $client_status;
		$client["fleet_manager_id"] = $fleet_manager_id;
	
		$client_where["id"] = $client_id;
		db_update_client($client,$client_where);
		
		//GET THIS CLIENT'S APPLICATION
		$where = null;
		$where["client_id"] = $client_id;
		$application = db_select_driver_application($where);
	
		//UPDATE APPLICATION
		$update_app["application_datetime"] = date('Y-m-d H:i:s',strtotime($_POST['app_datetime_edit']));
		
		//DETERMINE APPLIATION STATUS
		if($client_status == "On the Truck" || $client_status == "Closed")
		{
			$update_app["application_status"] = "Closed";
		}
		else
		{
			$update_app["application_status"] = "Open";
		}
		
		$app_where["id"] = $application["id"];
		db_update_driver_application($update_app,$app_where);
	
		$this->load_driver_details($client_id);
	}
	
	//SAVE CARRIER
	function save_carrier()
	{
		$company_id = $_POST["carrier_id"];
	
		//GET DATA FROM FORM
		$company_status = $_POST['company_status_edit'];
		$company_name = $_POST['company_name_edit'];
		$company_side_bar_name = $_POST['company_side_bar_name_edit'];
		$dba = $_POST['company_dba_edit'];
		$fein = $_POST['fein_edit'];
		$mc_number = $_POST['mc_edit'];
		$dot_number = $_POST['dot_edit'];
		$docket_pin = $_POST['docket_pin_edit'];
		$usdot_pin = $_POST['usdot_pin_edit'];
		$access_id = $_POST['access_id_edit'];
		$entity_number = $_POST['entity_number_edit'];
		$fl_login = $_POST['fl_username_edit'];
		$fl_password = $_POST['fl_password_edit'];
		$insurance_company = $_POST['insurance_company_edit'];
		$policy_number = $_POST['policy_number_edit'];
		$company_phone = $_POST['company_phone_edit'];
		$company_fax = $_POST['company_fax_edit'];
		$company_gmail = $_POST['company_gmail_edit'];
		$gmail_password = $_POST['gmail_password_edit'];
		$address = $_POST['address_edit'];
		$city = $_POST['city_edit'];
		$state = $_POST['state_edit'];
		$zip = $_POST['zip_edit'];
		$mailing_address = $_POST['mailing_address_edit'];
		$mailing_city = $_POST['mailing_city_edit'];
		$mailing_state = $_POST['mailing_state_edit'];
		$mailing_zip = $_POST['mailing_zip_edit'];
		$oregon_permit = $_POST['oregon_permit_edit'];
		$ucr_renewal_date = $_POST['ucr_edit'];
		$running_since = $_POST['running_since_edit'];
		$link_osbr = $_POST["link_osbr_edit"];
		$link_fein = $_POST["link_ein_edit"];
		$link_mc = $_POST["link_mc_edit"];
		$link_usdot_pin = $_POST["link_usdot_pin_edit"];
		$link_docket_pin = $_POST['link_docket_pin_edit'];
		$link_aoo = $_POST['link_aoo_edit'];
		$company_notes = $_POST['company_notes_edit'];

		//FORMAT DATE FIELDS
		if(!empty($ucr_renewal_date))
		{
			$ucr_renewal_date = date('Y-m-j',strtotime($ucr_renewal_date))." 00:00:00";
		}
		else
		{
			$ucr_renewal_date = null;
		}
		if(!empty($running_since))
		{
			$running_since = date('Y-m-j',strtotime($running_since))." 00:00:00";
		}
		else
		{
			$running_since = null;
		}
		
		//SAVE COMPANY
		$company["company_name"] = $company_name;
		$company["company_side_bar_name"] = $company_side_bar_name;
		$company["company_status"] = $company_status;
		$company["dba"] = $dba;
		$company["fein"] = $fein;
		$company["mc_number"] = $mc_number;
		$company["dot_number"] = $dot_number;
		$company["docket_pin"] = $docket_pin;
		$company["usdot_pin"] = $usdot_pin;
		$company["access_id"] = $access_id;
		$company["entity_number"] = $entity_number;
		$company["fl_username"] = $fl_login;
		$company["fl_password"] = $fl_password;
		$company["insurance_company"] = $insurance_company;
		$company["policy_number"] = $policy_number;
		$company["company_phone"] = $company_phone;
		$company["company_fax"] = $company_fax;
		$company["company_gmail"] = $company_gmail;
		$company["gmail_password"] = $gmail_password;
		$company["address"] = $address;
		$company["city"] = $city;
		$company["state"] = $state;
		$company["zip"] = $zip;
		$company["mailing_address"] = $mailing_address;
		$company["mailing_city"] = $mailing_city;
		$company["mailing_state"] = $mailing_state;
		$company["mailing_zip"] = $mailing_zip;
		$company["oregon_permit"] = $oregon_permit;
		$company["ucr_renewal_date"] = $ucr_renewal_date;
		$company["running_since"] = $running_since;
		
		$company["link_osbr"] = $link_osbr;
		$company["link_ein_letter"] = $link_fein;
		$company["link_mc_letter"] = $link_mc;
		$company["link_usdot_pin_letter"] = $link_usdot_pin;
		$company["link_docket_pin_letter"] = $link_docket_pin;
		$company["link_aoo"] = $link_aoo;
		
		$company["company_notes"] = $company_notes;
		
		$company_where["id"] = $company_id;
		db_update_company($company,$company_where);
		
		$this->load_carrier_details($company_id);
		
	}//end save_carrier()
	
	//SAVE FLEET MANAGER (COMPANY)
	function save_fleet_manager()
	{
		//UPDATE VENDOR
		$update_company["company_name"] = $_POST["company_name"];
		$update_company["company_side_bar_name"] = $_POST["company_short_name"];
		$update_company["company_status"] = $_POST["company_status"];
		$update_company["address"] = $_POST["address"];
		$update_company["city"] = $_POST["city"];
		$update_company["state"] = $_POST["state"];
		$update_company["zip"] = $_POST["zip"];
		$update_company["contact"] = $_POST["contact"];
		$update_company["company_email"] = $_POST["email"];
		$update_company["company_phone"] = $_POST["phone"];
		$update_company["company_fax"] = $_POST["fax"];
		$update_company["company_notes"] = $_POST["notes"];
		@$update_company["spark_cc_number"] = $_POST["spark_cc_number"];
		
		$where["id"] = $_POST['id'];
		db_update_company($update_company,$where);
		
		//GET COMPANY
		$where = null;
		$where["id"] = $_POST['id'];
		$company = db_select_company($where);
		
		//UPDATE PERSON
		if(user_has_permission('View personal staff info'))
		{
			$update_person["link_ss_card"] = $_POST["link_ssn_edit"];
			
			$where = null;
			$where["id"] = $company["person"]["id"];
			
			db_update_person($update_person,$where);
		}
		
		//UPDATE USER INFO
		$update_user["username"] = $_POST["username_edit"];
		$update_user["password"] = $_POST["password_edit"];
		
		$where = null;
		$where["id"] = $_POST["user_id"];
		db_update_user($update_user,$where);
		
		
		$this->load_fleet_manager_details($_POST['id']);
	}
	
	//SAVE FLEET MANAGER (COMPANY)
	function save_staff()
	{
		//UPDATE COMPANY
		$company["company_name"] = $_POST["company_name"];
		$company["company_side_bar_name"] = $_POST["company_short_name"];
		$company["category"] = $_POST["company_category"];
		$company["company_status"] = $_POST["company_status"];
		$company["address"] = $_POST["address"];
		$company["city"] = $_POST["city"];
		$company["state"] = $_POST["state"];
		$company["zip"] = $_POST["zip"];
		$company["contact"] = $_POST["contact"];
		$company["company_email"] = $_POST["email"];
		$company["company_phone"] = $_POST["phone"];
		$company["company_fax"] = $_POST["fax"];
		$company["company_notes"] = $_POST["notes"];
		@$company["spark_cc_number"] = $_POST["spark_cc_number"];
		
		$where["id"] = $_POST['id'];
		db_update_company($company,$where);
		
		//GET COMPANY
		$where = null;
		$where["id"] = $_POST['id'];
		$company = db_select_company($where);
		
		//UPDATE PERSON
		if(user_has_permission('View personal staff info'))
		{
			$update_person["link_ss_card"] = $_POST["link_ssn_edit"];
			
			$where = null;
			$where["id"] = $company["person"]["id"];
			
			db_update_person($update_person,$where);
		}
		
		//UPDATE USER INFO
		$update_user["username"] = $_POST["username_edit"];
		$update_user["password"] = $_POST["password_edit"];
		$update_user["user_status"] = $_POST["company_status"];
		
		$where = null;
		$where["id"] = $_POST["user_id"];
		db_update_user($update_user,$where);
		
		
		$this->load_staff_details($_POST['id']);
	}
	
	//SAVE BUSINESS USER (COMPANY)
	function save_business_user()
	{
		//UPDATE COMPANY
		$company["company_name"] = $_POST["company_name"];
		$company["company_side_bar_name"] = $_POST["company_short_name"];
		$company["company_status"] = $_POST["company_status"];
		$company["address"] = $_POST["address"];
		$company["city"] = $_POST["city"];
		$company["state"] = $_POST["state"];
		$company["zip"] = $_POST["zip"];
		$company["contact"] = $_POST["contact"];
		$company["company_email"] = $_POST["email"];
		$company["company_phone"] = $_POST["phone"];
		$company["company_fax"] = $_POST["fax"];
		$company["company_notes"] = $_POST["notes"];
		@$company["spark_cc_number"] = $_POST["spark_cc_number"];
		
		$where["id"] = $_POST['id'];
		
		if(user_has_permission("view and edit all business users"))
		{
			db_update_company($company,$where);
		}
		
		$this->load_business_user_details($_POST['id']);
	}
	
	//SAVE BROKER (COMPANY)
	function save_broker()
	{
		//UPDATE COMPANY
		$company["company_name"] = $_POST["company_name"];
		$company["company_side_bar_name"] = $_POST["company_short_name"];
		$company["company_status"] = $_POST["company_status"];
		$company["address"] = $_POST["address"];
		$company["city"] = $_POST["city"];
		$company["state"] = $_POST["state"];
		$company["zip"] = $_POST["zip"];
		$company["contact"] = $_POST["contact"];
		$company["company_email"] = $_POST["email"];
		$company["company_phone"] = $_POST["phone"];
		$company["company_fax"] = $_POST["fax"];
		$company["company_notes"] = $_POST["notes"];
		@$company["spark_cc_number"] = $_POST["spark_cc_number"];
		
		$where["id"] = $_POST['id'];
		db_update_company($company,$where);
		
		//UPDATE CUSTOMER
		$update_customer = null;
		$update_customer["mc_number"] = $_POST["mc_number"];
		$update_customer["status"] = $_POST["status"];
		$update_customer["form_of_payment"] = $_POST["form_of_payment"];
		
		$where = null;
		$where["company_id"] = $_POST['id'];
		db_update_customer($update_customer,$where);
		
		
		$this->load_broker_details($_POST['id']);
	}
	
	//SAVE BUSINESS USER (COMPANY)
	function save_customer_vendor()
	{
		//UPDATE COMPANY
		$company["company_name"] = $_POST["company_name"];
		$company["company_side_bar_name"] = $_POST["company_short_name"];
		$company["company_status"] = $_POST["company_status"];
		$company["address"] = $_POST["address"];
		$company["city"] = $_POST["city"];
		$company["state"] = $_POST["state"];
		$company["zip"] = $_POST["zip"];
		$company["contact"] = $_POST["contact"];
		$company["company_email"] = $_POST["email"];
		$company["company_phone"] = $_POST["phone"];
		$company["company_fax"] = $_POST["fax"];
		$company["company_notes"] = $_POST["notes"];
		@$company["spark_cc_number"] = $_POST["spark_cc_number"];
		
		$where["id"] = $_POST['id'];
		db_update_company($company,$where);
		
		
		$this->load_customer_vendor_details($_POST['id']);
	}
	
	//LOAD UPLOAD SIGNATURE DIALOG
	function load_signature_div()
	{
		$person_id = $_POST["person_id"];
		
		$where = null;
		$where["id"] = $person_id;
		$person = db_select_person($where);
		
		
		$data["person"] = $person;
		$this->load->view('people/upload_signatures_dialog',$data);
	}
	
	//LOAD UPLOAD CONTRACT DIALOG
	function load_contract_div()
	{
		$client_id = $_POST["client_id"];
		
		$where = null;
		$where["id"] = $client_id;
		$client = db_select_client($where);
		
		
		$data["client"] = $client;
		$this->load->view('people/upload_contract_dialog',$data);
	}	
	
	//LOAD UPLOAD PACKET DIALOG
	function load_packet_div()
	{
		$company_id = $_POST["company_id"];
		
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		
		$data["company"] = $company;
		$this->load->view('people/upload_packet_dialog',$data);
	}	
	
	//UPLOAD SIGNATURE TO PERSON
	function upload_signatures()
	{
		$person_id = $_POST["person_id"];
		
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
		$input_name = 'e_signature';
		$file = $_FILES[$input_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$category = "E Signature";
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$signature_secure_file = store_secure_ftp_file($input_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- INITIALS
		$input_name = 'e_initials';
		$file = $_FILES[$input_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$category = "E Initials";
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$initials_secure_file = store_secure_ftp_file($input_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		//UPDATE PERSON WITH FILE GUIDS FOR SIGNATURE AND INITIALS
		$update = null;
		$update["signature_guid"] = $signature_secure_file["file_guid"];
		$update["initials_guid"] = $initials_secure_file["file_guid"];
		
		$where = null;
		$where["id"] = $person_id;
		db_update_person($update,$where);
		
		
		redirect(base_url('index.php/people/load_upload_success_view'));
	}
	
	//UPLOAD CONTRACT TO PERSON
	function upload_contract()
	{
		$client_id = $_POST["client_id"];
		
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
		$input_name = 'contract';
		$file = $_FILES[$input_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$category = "Contract";
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$contract_secure_file = store_secure_ftp_file($input_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		//UPDATE PERSON WITH FILE GUIDS FOR SIGNATURE AND INITIALS
		$update = null;
		$update["contract_guid"] = $contract_secure_file["file_guid"];
		
		$where = null;
		$where["id"] = $client_id;
		db_update_client($update,$where);
		
		
		redirect(base_url('index.php/people/load_upload_success_view'));
	}
	
	//UPLOAD PACKET PERSON
	function upload_packet()
	{
		$company_id = $_POST["company_id"];
		
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
		$input_name = 'packet';
		$file = $_FILES[$input_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$category = "Packet";
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$contract_secure_file = store_secure_ftp_file($input_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		//UPDATE PERSON WITH FILE GUIDS FOR SIGNATURE AND INITIALS
		$update = null;
		$update["carrier_packet_guid"] = $contract_secure_file["file_guid"];
		
		$where = null;
		$where["id"] = $company_id;
		db_update_company($update,$where);
		
		
		redirect(base_url('index.php/people/load_upload_success_view'));
	}
	
	
	//LOAD SUCCESS VIEW
	function load_upload_success_view()
	{
		$data["title"] = "Upload Success";
		$this->load->view('people/upload_success_view',$data);
	}
	
	//PROMOTE STAFF TO FLEET MANAGER
	function promote_to_fm()
	{
		$company_id = $_POST["company_id"];
		
		//UPDATE COMPANY ATTRIBUTES TO REFLECT CHANGE TO FM
		$update_company["type"] = 'Fleet Manager';
		$update_company["category"] = 'Fleet Manager';
		
		$where = null;
		$where["id"] = $company_id;
		db_update_company($update_company,$where);
		$company = db_select_company($where);
		
		//UPDATE PERSON
		$update_person["role"] = 'Fleet Manager';
		
		$where = null;
		$where["id"] = $company["person_id"];
		db_update_person($update_person,$where);
		
		//CREATE DEFAULT ACCOUNTS
		create_default_fm_accounts($company_id);
		
		//UPDATE PAY ACCOUNT
		$update_account = null;
		$update_account['account_type'] = "Fleet Manager";
		
		$where = null;
		$where['company_id'] = $company_id;
		$where['account_type'] = "Fleet Manager";
		$where['category'] = "Pay";
		db_update_account($update_account,$where);
		
		//LOAD FLEET MANAGER DETAILS
		$this->load_fleet_manager_details($company_id);
	}
	
	//ADD CREDIT CARD ACCOUNT STAFF/FLEET MANAGER
	function add_card()
	{
		$company_id = $_POST["new_card_company_id"];
		$new_card_account = $_POST["new_card_account"];
		$last_four = $_POST["last_four"];
		$card_name = $_POST["card_name"];
		
		//GET COMPANY
		$where = null;
		$where["id"] = $company_id;
		$company = db_select_company($where);
		
		//INSERT NEW CARD
		$new_card["person_id"] = $company["person_id"];
		$new_card["account_id"] = $new_card_account;
		$new_card["card_name"] = $card_name;
		$new_card["last_four"] = $last_four;
		db_insert_corporate_card($new_card);
		
		if($company["category"] == 'Fleet Manager')
		{
			$this->load_fleet_manager_details($company['id']);
		}
		elseif($company["category"] == 'Office Staff')
		{
			$this->load_staff_details($company['id']);
		}
	}
	
	//GET APPLICANT STATUS LOG
	function get_app_notes($app_id)
	{
		//echo $app_id;
	
		$where = null;
		$where["id"] = $app_id;
		$driver_app = db_select_driver_application($where);
		
		$data['driver_app'] = $driver_app;
		$this->load->view('people/driver_app_notes_div',$data);
	}//end get_invoice_notes
	
	//SAVE NOTE
	function save_note()
	{
		$app_id = $_POST["application_id"];
		
		$text = $_POST["new_note"];
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		date_default_timezone_set('America/Denver');
		$date_text = date("m/d/y");
		
		$full_note = $date_text." - ".$initials." | ".$text."\n\n";
		
		$where = null;
		$where["id"] = $app_id;
		$driver_app = db_select_driver_application($where);
		
		$update = null;
		$update["applicant_status_log"] = $full_note.$driver_app["applicant_status_log"];
		db_update_driver_application($update,$where);
		
		$this->get_app_notes($app_id);
		
		//echo $update_load["settlement_notes"];
	}
	
	
	function fix_carrier_id()
	{
		$where = null;
		$where["billed_under"] = 13;
		$loads = db_select_loads($where);
		foreach($loads as $load)
		{
			$update_load["billed_under"] = 91;
			$where = null;
			$where["id"] = $load["id"];
			//db_update_load($update_load,$where);
		}
		
		echo "Successful update!";
	}
	
	function fix_billed_under()
	{
		$where = null;
		$where["1"] = 1;
		$loads = db_select_loads($where);
		
		foreach($loads as $load)
		{
			$update_load["billed_under"] = $load["billed_under_client"]["company"]["id"];
			
			$where = null;
			$where["id"] = $load["id"];
			//db_update_load($update_load,$where);
		}
		
		echo "Update success!";
	}
	
	function create_nicknames()
	{
		$where = null;
		$where = "1 = 1";
		$clients = db_select_clients($where);
		
		foreach($clients as $client)
		{
				echo $client["company"]["person"]["full_name"]."<br><br>";
				$update_client["client_nickname"] = $client["company"]["person"]["full_name"];
				$where = null;
				$where["id"] = $client["id"];
				//db_update_client($update_client,$where);
			
		}
			
		
		echo "Update complete!";
	}
	
	function change_status()
	{
		$where = null;
		$where = "1 = 1";
		$clients = db_select_clients($where);
		
		foreach($clients as $client)
		{
			if($client["client_status"] == "Main Driver")
			{
				$update_client["client_status"] = "Active";
				$update_client["client_type"] = "Main Driver";
				$where = null;
				$where["id"] = $client["id"];
				//db_update_client($update_client,$where);
			}
		}
			
		
		echo "Update complete!";
	}
	
	function mark_main_driver()
	{
		$where = null;
		$where = "1 = 1";
		$clients = db_select_clients($where);
		
		foreach($clients as $client)
		{
			$update_client["client_type"] = "Main Driver";
			$where = null;
			$where["id"] = $client["id"];
			//db_update_client($update_client,$where);
		}
			
		
		echo "Update complete!";
	}
	
	//ALERT!! YOU HAVE TO ADD A COMPANY ID TO CUSTOMER DB TABLE
	function convert_customers_to_companies()
	{
		//GET ALL CUSTOMERS
		$where = null;
		$where = "1 = 1";
		$customers = db_select_customers($where);
		
		//FOREACH CUSTOMER
		foreach($customers as $customer)
		{
			echo $customer["customer_name"]."<br>";
			//CREATE NEW COMPANY WITH SAME NAME AND SIDE BAR NAME, TYPE = CUSTOMER-VENDOR
			$new_company = null;
			$new_company["company_name"] = $customer["customer_name"];
			$new_company["company_side_bar_name"] = $customer["customer_name"];
			$new_company["type"] = "Broker";
			//db_insert_company($new_company);
			
			//GET NEWLY CREATED COMPANY
			$newly_inserted_company = db_select_company($new_company);
			
			//UPDATE CUSTOMER WITH COMPANY ID
			$update_customer = null;
			$update_customer["company_id"] = $newly_inserted_company["id"];
			
			$where = null;
			$where["id"] = $customer["id"];
			//db_update_customer($update_customer,$where);
		}
	}
	
	function fix_broker_company_type()
	{
		//GET ALL CUSTOMERS
		$where = null;
		$where = "1 = 1";
		$customers = db_select_customers($where);
		
		//FOREACH CUSTOMER
		foreach($customers as $customer)
		{
			echo $customer["customer_name"]."<br>";
			//CREATE NEW COMPANY WITH SAME NAME AND SIDE BAR NAME, TYPE = CUSTOMER-VENDOR
			$update = null;
			$update["type"] = "Broker";
			
			$where = null;
			$where["id"] = $customer["company_id"];
			//db_update_company($update,$where);
			
		}
	}
	
	//ONE-TIME SCRIPT
	function create_business_relationship_for_all_brokers()
	{
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		$where = null;
		$where = "1 = 1";
		$customers = db_select_customers($where);
		
		foreach($customers as $customer)
		{
			$relationship = null;
			$relationship["business_id"] = $coop_company["id"];
			$relationship["relationship"] = "member customer";
			$relationship["related_business_id"] = $customer["company_id"];
			
			$relationship_found = db_select_business_relationship($relationship);
			if(empty($relationship_found))
			{
				db_insert_business_relationship($relationship);
				
			}
			
		}
		
		
		echo date("H:i:s")." Success!";
	}
	
	//ONE-TIME SCRIPT
	function create_ar_accounts_for_all_brokers()
	{
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		$where = null;
		$where = "1 = 1";
		$customers = db_select_customers($where);
		
		foreach($customers as $customer)
		{
			//GET BROKER COMPANY
			$where = null;
			$where["id"] = $customer["company_id"];
			$company = db_select_company($where);
			
			//GET COOP BROKER RELATIONSHIP
			$where = null;
			$where["business_id"] = $coop_company["id"];
			$where["relationship"] = "member customer";
			$where["related_business_id"] = $customer["company_id"];
			
			$coop_broker_relationship = db_select_business_relationship($where);
			
			//GET DEFAULT PARENT A/R ACCOUNT
			$where = null;
			$where["company_id"] = $coop_company["id"];
			$where["category"] = "A/R from Brokers on Loads Hauled";
			$coop_default_broker_ar_acc = db_select_default_account($where);
			
			$where = null;
			$where["id"] = $coop_default_broker_ar_acc["account_id"];
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
		
		
		
		echo date("H:i:s")." Success!";
	}

	//ONE-TIME SCRIPT
	function delete_drivers()
	{
		//GET ALL PERSONS WHERE ROLE IS CLIENT
		$where = null;
		$where["role"] = "Client";
		$client_persons = db_select_persons($where);
		
		foreach($client_persons as $person)
		{
			//GET USER FOR THIS PERSON
			$where = null;
			$where["person_id"] = $person["id"];
			$user = db_select_user($where);
			
			echo $person["full_name"]." -- ".$user["username"]."<br>";
			
			//db_delete_person($person["id"]);
			
			//db_delete_user($$user["id"]);
		}
		
		echo "Success 3!";
	}
	
	// //ONE-TIME SCRIPT
	// function fix_bad_brokers()
	// {
		// //GET ALL CUSTOMERS WHERE COMPANY_ID IS NULL
		// $where = null;
		// $where = " company_id IS NULL ";
		// $customers = db_select_customers($where);
		
		// foreach($customers as $customer)
		// {
			// //CREATE COMPANY
			// $company = null;
			// $company["type"] = "Broker";
			// $company["category"] = "Broker";
			// $company["company_status"] = "Active";
			// $company["company_name"] = $customer["customer_name"];
			// $company["company_side_bar_name"] = $customer["customer_name"];
			
			// db_insert_company($company);
			// $company = db_select_company($company);
			
			// //UPDATE CUSTOMER
			// $update_broker = null;
			// $update_broker["company_id"] = $company["id"];
			// $update_broker["status"] = "Good";
			
			// $where = null;
			// $where["id"] = $customer["id"];
			// db_update_customer($update_broker,$where);
			
			// //GET COOP COMPANY
			// $where = null;
			// $where["category"] = "Coop";
			// $coop_company = db_select_company($where);
			
			// //CREATE BUSINESS RELATIONSHIP
			// $relationship = null;
			// $relationship["business_id"] = $coop_company["id"];
			// $relationship["relationship"] = "Member Customer";
			// $relationship["related_business_id"] = $company["id"];
			// db_insert_business_relationship($relationship);
			
			// $coop_broker_relationship = db_select_business_relationship($relationship);
			
			// //GET DEFAULT ACCOUNT FOR A/R FROM BROKERS ON LOADS HAULED
			// $where = null;
			// $where["company_id"] = $coop_company["id"];
			// $where["category"] = "A/R from Brokers on Loads Hauled";
			// $coop_ar_from_brokers_default_account = db_select_default_account($where);
			
			// //GET A/R ACCOUNT
			// $where = null;
			// $where["id"] = $coop_ar_from_brokers_default_account["account_id"];
			// $parent_account = db_select_account($where);
			
			// //CREATE A/R ACCOUNT WITH COOP
			// $account = null;
			// $account["company_id"] = $coop_company["id"];
			// $account["relationship_id"] = $coop_broker_relationship["id"];
			// $account["account_type"] = "Holding";
			// $account["account_class"] = "Asset";
			// $account["category"] = $parent_account["category"];
			// $account["account_status"] = "Open";
			// $account["account_name"] = "A/R from ".$company["company_side_bar_name"];
			// $account["parent_account_id"] = $parent_account["id"];
			// db_insert_account($account);
			
			// //GET NEWLY CREATED ACCOUNT
			// $newly_created_account = db_select_account($account);
			
			// //SET ACCOUNT AS DEFAULT A/R ON ARROWHEAD FLEETPROTECT DEPOSIT
			// $default_acc = null;
			// $default_acc["company_id"] = $company["id"];
			// $default_acc["account_id"] = $newly_created_account["id"];
			// $default_acc["type"] = "Broker";
			// $default_acc["category"] = "Coop A/R on Loads Hauled";
			// db_insert_default_account($default_acc);
			
			// echo $customer["customer_name"]." SUCCESS!<br>";
		// }
		
		
		
		
		
		
		
		
		
		
		
	// }
}
?>