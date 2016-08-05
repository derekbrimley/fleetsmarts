<?php		


	
class driver_logs extends MY_Controller 
{

	function index($client_id)
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//GET THIS FLEET MANAGER
		$this_fm = $this->session->userdata('person_id');
		if($this->session->userdata('role') != "Fleet Manager")
		{
			$this_fm = "all";
		}
		if($this->session->userdata('person_id') == 26)
		{
			$this_fm = "19";
		}
		
		//GET CLIENT LIST AND ALPHABETIZE
		$where = null;
		$where["type"] = "Client";
		$client_companies = db_select_companys($where,"company_side_bar_name");
		$all_clients = array();
		foreach($client_companies as $company)
		{
			$where = null;
			$where["company_id"] = $company["id"];
			$where["client_status"] = "Active";
			if($this_fm != "all")
			{
				$where["fleet_manager_id"] = $this_fm;
			}
			$abc_client = db_select_client($where);
			if(!empty($abc_client))
			{
				$all_clients[] = $abc_client;
			}
		}
		
		//CREATE CLIENT DROP DOWN
		$client_options["Select Client"] = 'Select Client';
		foreach ($all_clients as $client)
		{
			$title = $client["company"]["company_side_bar_name"];
			$company_id = $client["company"]["id"];
			$client_options[$company_id] = $title;
		}
		
		//GET THIS CLIENT
		$this_client_where["id"] = $client_id;
		$this_client = db_select_client($this_client_where);
		
		//GET ALL STOPS
		$company_id = $this_client["company_id"];
		
		$stops_where = "";
		if(is_numeric($client_id))
		{
			$stops_where = $stops_where." company_id =  $company_id AND ";
		}
		
		$stops_where = $stops_where." stop_datetime IS NOT NULL";
		
		$all_stops = db_select_stops($stops_where,"stop_datetime DESC");
		
		//CALCULATE MPG FOR EACH FUEL - FILL
		$fuel_mpg = array();
		foreach($all_stops as $stop)
		{
			$stop_id = $stop["id"];
			$fuel_mpg[$stop_id] = calc_mpg($stop["id"]);
		
		}
		
		
		
		$data["client_options"] = $client_options;
		@$data['this_client'] = $this_client;
		$data['all_clients'] = $all_clients;
		$data['all_stops'] = $all_stops;
		$data['fuel_mpg'] = $fuel_mpg;
		$data['client_id'] = $client_id;
		$data['tab'] = 'Driver Logs';
		$data['title'] = "Driver Logs";
		$this->load->view('driver_logs_view',$data);
	}// end index
	
	function save_edit()
	{
		$client_id = $_POST["client_id"];
		
		//GET THIS CLIENT
		$this_client_where["id"] = $client_id;
		$this_client = db_select_client($this_client_where);
		
		//GET ALL STOPS
		$stops_where = "company_id = ".$this_client["company_id"]." AND stop_datetime IS NOT NULL";
		$all_stops = db_select_stops($stops_where,"stop_datetime");
		
		foreach ($all_stops as $stop)
		{
			$stop_id = $stop["id"];
			if ($_POST["should_update_$stop_id"] == "YES")
			{
				
				
				
				//IF ITS A FUEL STOP SAVE THE FUEL STOP
				if ($stop["stop_type"] == "Fuel - Fill" || $stop["stop_type"] == "Fuel - Partial")
				{
					$fuel_stop["gallons"] = $_POST["gallons_$stop_id"];
					$fuel_stop["invoice_amount"] = $_POST["invoice_$stop_id"];
					$fuel_stop["is_fill"] = $_POST["stop_type_$stop_id"];
					$fuel_stop_where["stop_id"] = $stop_id;
					db_update_fuel_stop($fuel_stop,$fuel_stop_where);
				
				
					$update_stop["stop_type"] = "Fuel - ".$_POST["stop_type_$stop_id"];
				}
				
				
				//UPDATE THE STOP
				$update_stop["stop_datetime"] = date("Y-m-d G:i:s",strtotime($_POST["date_$stop_id"]." ".$_POST["time_$stop_id"]));
				$update_stop["location_name"] = $_POST["location_$stop_id"];
				$update_stop["city"] = $_POST["city_$stop_id"];
				$update_stop["state"] = $_POST["state_$stop_id"];
				$update_stop["address"] = $_POST["address_$stop_id"];
				$update_stop["odometer"] = $_POST["odometer_$stop_id"];
				
				$stop_where["id"] = $stop_id;
				db_update_stop($update_stop,$stop_where);
				
			}
		}
		redirect(base_url("index.php/driver_logs/index/$client_id"));
	}
	
	function delete_stop()
	{
		$stop_id = $_POST["stop_id"];
		$stop_where["id"] = $stop_id;
		
		db_delete_stop($stop_where);
		
		$client_id = $_POST["client_id"];
		redirect(base_url("index.php/driver_logs/index/$client_id"));
	}
	
	function create_new_stop()
	{
		
		//GET THIS CLIENT
		$this_client_where["company_id"] = $_POST["client_dropdown"];
		$this_client = db_select_client($this_client_where);
	
		$client_id = $this_client["id"];
		
		//FIGURE OUT STOP TYPE
		$stop_type = $_POST["stop_type_dropdown"];
		if($stop_type == "Fuel Stop")
		{
			$stop_type = "Fuel - ".$_POST["fill_or_partial"];
		}
		
		//CREATE DATETIME FOR DB
		$date = $_POST["date"];
		$time = $_POST["time"];
		$stop_datetime = date("Y-m-d",strtotime($date))." ".$time;
		
		//SAVE THE STOP
		$stop['company_id'] = $this_client["company_id"];
		$stop['stop_type'] = $stop_type;
		$stop['stop_datetime'] = $stop_datetime;
		$stop['location_name'] = $_POST["location"];
		$stop['city'] = $_POST["city"];
		$stop['state'] = $_POST["state"];
		$stop['address'] = $_POST["address"];
		$stop['odometer'] = $_POST["odometer"];
		$stop['notes'] = $_POST["notes"];
		
		db_insert_stop($stop);
		$new_stop = db_select_stop($stop);
		
		$pick_drop['stop_id'] = $new_stop["id"];
		$pick_drop['load_id'] = $_POST["load_id"];
		//IF STOP IS PICK, SAVE PICK
		if($_POST["stop_type_dropdown"] == "Pick")
		{
			
			$pick_drop['pick_number'] = "P-".get_random_string(10);
			db_insert_pick($pick_drop);
		}
		//IF STOP IS DROP, SAVE DROP
		else if($_POST["stop_type_dropdown"] == "Drop")
		{	
			$pick_drop['drop_number'] = "D-".get_random_string(10);
			db_insert_drop($pick_drop);
		}
		//IF STOP IS FUEL, SAVE FUEL_STOP
		else if($_POST["stop_type_dropdown"] == "Fuel Stop")
		{
			$fuel_stop['stop_id'] = $new_stop["id"];
			$fuel_stop['is_fill'] = $_POST["fill_or_partial"];
			$fuel_stop['gallons'] = $_POST["gallons"];
			$fuel_stop['invoice_amount'] = $_POST["invoice_amount"];
			
			db_insert_fuel_stop($fuel_stop);
		}
	
		redirect(base_url("index.php/driver_logs/index/$client_id"));
	
	}
	
	//ADD STOP NOTE
	function add_stop_note()
	{
		$stop_id = $_POST["notes_stop_id"];
		$text = $_POST["note_text"];
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		date_default_timezone_set('America/Denver');
		$date_text = date("m/d/y");
		
		$full_note = $date_text." - ".$initials." - ".$text."\n";
		
		$where["id"] = $stop_id;
		$stop = db_select_stop($where);
		$update_stop["notes"] = $stop["notes"].$full_note;
		db_update_stop($update_stop,$where);
		
		$client_id = $_POST['client_id'];
		redirect(base_url("index.php/driver_logs/index/$client_id"));
	}
}
?>