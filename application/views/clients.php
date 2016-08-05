<?php		


	
class Clients extends MY_Controller 
{

	function index($view,$client_id)
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//GET CLIENT LIST
		$where = null;
		$where["client_status"] = "Main Driver";
		$all_clients = db_select_clients($where,"client_nickname");
		
		//GET THIS CLIENT
		$this_client_where["id"] = $client_id;
		$this_client = db_select_client($this_client_where);
		
		//IF CREATING A NEW CLIENT
		if ($client_id == "new" || $client_id == "none")
		{
			//PROVIDE DEFAULT CLIENT FEE SETTINGS
			$factoring_setting["id"] = "default_1";
			$factoring_setting["client_id"] = "new";
			$factoring_setting["fee_description"] = "Factoring";
			$factoring_setting["fee_type"] = "Map Mile";
			$factoring_setting["fee_amount"] = .04;
			$client_fee_settings[] = $factoring_setting;
			
			$bad_debt_setting["id"] = "default_2";
			$bad_debt_setting["client_id"] = "new";
			$bad_debt_setting["fee_description"] = "Bad Debt";
			$bad_debt_setting["fee_type"] = "Map Mile";
			$bad_debt_setting["fee_amount"] = .03;
			$client_fee_settings[] = $bad_debt_setting;
			
			$start_up_setting["id"] = "default_3";
			$start_up_setting["client_id"] = "new";
			$start_up_setting["fee_description"] = "Start Up";
			$start_up_setting["fee_type"] = "Map Mile";
			$start_up_setting["fee_amount"] = .06;
			$client_fee_settings[] = $start_up_setting;
			
			$fuel_setting["id"] = "default_4";
			$fuel_setting["client_id"] = "new";
			$fuel_setting["fee_description"] = "Fuel";
			$fuel_setting["fee_type"] = "Fuel Allocation";
			$fuel_setting["fee_amount"] = 0;
			$client_fee_settings[] = $fuel_setting;
			
			$damage_setting["id"] = "default_5";
			$damage_setting["client_id"] = "new";
			$damage_setting["fee_description"] = "Damage";
			$damage_setting["fee_type"] = "Map Mile";
			$damage_setting["fee_amount"] = 0;
			$client_fee_settings[] = $damage_setting;
			
			$truck_mileage_setting["id"] = "default_6";
			$truck_mileage_setting["client_id"] = "new";
			$truck_mileage_setting["fee_description"] = "Truck Mileage";
			$truck_mileage_setting["fee_type"] = "Odometer Mile";
			$truck_mileage_setting["fee_amount"] = .09;
			$client_fee_settings[] = $truck_mileage_setting;
			
			$trailer_mileage_setting["id"] = "default_7";
			$trailer_mileage_setting["client_id"] = "new";
			$trailer_mileage_setting["fee_description"] = "Trailer Mileage";
			$trailer_mileage_setting["fee_type"] = "Odometer Mile";
			$trailer_mileage_setting["fee_amount"] = .03;
			$client_fee_settings[] = $trailer_mileage_setting;
			
			$truck_lease_setting["id"] = "default_8";
			$truck_lease_setting["client_id"] = "new";
			$truck_lease_setting["fee_description"] = "Truck Lease";
			$truck_lease_setting["fee_type"] = "Week";
			$truck_lease_setting["fee_amount"] = 600;
			$client_fee_settings[] = $truck_lease_setting;
			
			$trailer_lease_setting["id"] = "default_9";
			$trailer_lease_setting["client_id"] = "new";
			$trailer_lease_setting["fee_description"] = "Trailer Lease";
			$trailer_lease_setting["fee_type"] = "Week";
			$trailer_lease_setting["fee_amount"] = 60.40;
			$client_fee_settings[] = $trailer_lease_setting;
			
			$insurance_setting["id"] = "default_10";
			$insurance_setting["client_id"] = "new";
			$insurance_setting["fee_description"] = "Insurance";
			$insurance_setting["fee_type"] = "Month";
			$insurance_setting["fee_amount"] = 1200;
			$client_fee_settings[] = $insurance_setting;
			
		}
		else
		{
			$client_fee_settings = $this_client["client_fee_settings"];
		}
		
		//GET CLIENT ACCOUNTS
		$where = null;
		$where["company_id"] = $this_client["company_id"];
		$client_accounts = db_select_accounts($where);
		
		//GET CLIENT_EXPENSE ACCOUNTS
		$where = null;
		$where["category"] = "Client_Expense";
		$client_expense_accounts = db_select_accounts($where);
		
		//CREATE DROPDOWN LIST OF PAY ACCOUNTS
		$client_account_options = array();
		$client_account_options["Select Account"] = "Select Account";
		foreach($client_accounts as $account)
		{
			$client_account_options[$account["id"]] = $account["account_name"];
		}
		foreach($client_expense_accounts as $account)
		{
			$client_account_options[$account["id"]] = $account["account_name"];
		}
		
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
		
		$data["all_users"] = $all_users;
		@$data['this_client'] = $this_client;
		$data['client_fee_settings'] = $client_fee_settings;
		$data['all_clients'] = $all_clients;
		$data['fleet_manager_dropdown_options'] = $fleet_manager_dropdown_options;
		$data['client_account_options'] = $client_account_options;
		$data['view'] = $view;
		$data['client_id'] = $client_id;
		$data['tab'] = 'Clients';
		$data['title'] = "Clients";
		$this->load->view('clients_view',$data);
	}// end index
	
	//SAVE EITHER A NEW CLIENT OR AN EXISTING ONE
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
		
		redirect(base_url("index.php/clients/index/details/$client_id"));
	}
	
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
		$this->load->view('clients/client_list_div',$data);
		
	}//end client_status_selected()
	
	function change_status()
	{
		$where = null;
		$where = "1 = 1";
		$clients = db_select_clients($where);
		
		foreach($clients as $client)
		{
			if($client["client_status"] == "Active")
			{
				$update_client["client_status"] = "Main Driver";
			}
			elseif($client["client_status"] == "Inactive")
			{
				$update_client["client_status"] = "Closed";
			}
			
			$where = null;
			$where["id"] = $client["id"];
			db_update_client($update_client,$where);
		}
		
		echo "Update complete!";
	}
	
}
?>