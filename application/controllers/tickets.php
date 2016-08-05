<?php		
class Tickets extends MY_Controller 
{
	//INDEX
	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//GET BUSINESS USERS
		$where = null;
		$where["company_status"] = "Active";
		$where["type"] = "Business";
			
		$business_users = db_select_companys($where,"company_side_bar_name");
		
		// //CREATE DROPDOWN LIST OF BUSINESS USERS
		// $business_users_options = array();
		// $business_users_options["Select"] = "Select";
		// foreach($business_users as $company)
		// {
			// $title = $company["company_side_bar_name"];
			// $business_users_options[$company["id"]] = $title;
		// }
		
		//GET TICKET CATEGORIES
		$ticket_category_options = array();
		$ticket_category_options["All"] = "All";
		foreach(get_distinct("category","ticket") as $option)
		{
			$title = $option;
			$ticket_category_options[$title] = $title;
			//echo $option;
		}
		
		//GET TRAILER NUMBER
		$trailer_number_options = array();
		$trailer_number_options["All"] = "All";
		foreach(get_distinct("trailer_number","trailer") as $option)
		{
			$title = $option;
			$trailer_number_options[$title] = $title;
			//echo $option;
		}
		
		//GET TRUCK NUMBER
		$truck_number_options = array();
		$truck_number_options["All"] = "All";
		foreach(get_distinct("truck_number","truck") as $option)
		{
			$title = $option;
			$truck_number_options[$title] = $title;
			//echo $option;
		}
		
		$data['truck_number_options'] = $truck_number_options;
		$data['trailer_number_options'] = $trailer_number_options;
		$data['ticket_category_options'] = $ticket_category_options;
		// $data['business_users_options'] = $business_users_options;
		$data['title'] = "Tickets";
		$data['tab'] = 'Tickets';
		$this->load->view('tickets_view',$data);
	
	}// end index
	
	function add_note()
	{
		$ticket_id = $_POST['ticket_id'];
		$text = $_POST['note'];
		
        $initials = substr($this->session->userdata('first_name'),0,1).substr($this->session->userdata('last_name'),0,1);
        date_default_timezone_set('America/Denver');
        $date_text = date("m/d/y H:i");
        
        $full_note = $date_text." - ".$initials." | ".$text."\n\n";
        
        $where['id'] = $ticket_id;
        $ticket = db_select_ticket($where);
        
        $update_ticket["notes"] = $full_note.$ticket["notes"];
        db_update_ticket($update_ticket,$where);
        
		// echo $full_note;
        //$this->get_notes($ticket_id);
		
	}
	
	function add_action_item()
	{
		$ticket_id = $_POST['ticket_id'];
		$due_date = $_POST['due_date'];
		$note = $_POST['note'];
		
		//GET USER
		//$user_id = $this->session->userdata('user_id');
		
		//GET TICKET MANAGER USER ID
		$where = null;
		$where["setting_name"] = "Ticket System Manager";
		$ticket_manager_setting = db_select_setting($where);
		
		$action_item = null;
		$action_item['due_date'] = date('Y-m-d',strtotime($due_date));
		$action_item['description'] = $note;
		$action_item['object_type'] = "Ticket";
		$action_item['object_id'] = $ticket_id;
		$action_item['manager_id'] = $ticket_manager_setting["setting_value"];
		$action_item['owner_id'] = $ticket_manager_setting["setting_value"];
		
		db_insert_action_item($action_item);
		
	}
	
	function complete_action()
	{
		$action_id = $_POST['id'];
		
		date_default_timezone_set('America/Denver');
		$current_datetime = date("Y-m-d H:i:s");
		
		$set = array();
		$set['completion_date'] = $current_datetime;
		
		$where = null;
		$where['id'] = $action_id;
		
		db_update_action_item($set,$where);
		
		echo date('m/d/y',strtotime($current_datetime));
	}
	
	function create_new_ticket()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		$recorder_id = $this->session->userdata('user_id');
		
		$truck_or_trailer = $_POST["unit_type"];
		$truck_id = $_POST["truck_id_dropdown"];
		$trailer_id = $_POST["trailer_id_dropdown"];
		$category = $_POST["category_dropdown"];
		$description = $_POST["description_input"];
		$responsible_party = $_POST["responsible_party_dropdown"];
		$incident_date = date('Y-m-d',strtotime($_POST["incident_date_input"]));
		if($_POST["estimated_completion_date_input"]=="")
		{
			$estimated_completion_date = null;
		}
		else
		{
			$estimated_completion_date = date('Y-m-d',strtotime($_POST["estimated_completion_date_input"]));
		}
		$amount = round($_POST["amount_input"],2);
		
		//GET LEASING COMPANY
		$where = null;
		$where["category"] = "Leasing";
		$leasing_company = db_select_company($where);
		
		
		//IF TICKET IS DAMAGE, INSERT PROPER ACCOUNT ENTRIES FOR NEW EXPENSE AND LIABILITY
		if($category == "Damage")
		{
			//GET DEFAULT DAMAGE REPAIR LIABILITY ACCOUNT
			$where = null;
			$where["company_id"] = $leasing_company["id"];
			$where["category"] = "Damage Repair Liability";
			$default_damage_liability_account = db_select_default_account($where);
			
			$new_ticket = null;
			if($truck_or_trailer == "Select")
			{
				$new_ticket['unit_number'] = "";
			}
			else if($truck_or_trailer == "Truck")
			{
				$new_ticket['truck_id'] = $truck_id;
				$new_ticket['truck_or_trailer'] = "Truck";
			}
			else if($truck_or_trailer == "Trailer")
			{
				$new_ticket['trailer_id'] = $trailer_id;
				$new_ticket['truck_or_trailer'] = "Trailer";
			}
			else if($truck_or_trailer == "Other")
			{
				$new_ticket['truck_or_trailer'] = "Other";
			}
			
			$new_ticket["balance_sheet_account_id"] = $default_damage_liability_account["account_id"];
			$new_ticket["category"] = $category;
			$new_ticket["description"] = $description;
			$new_ticket["responsible_party"] = $responsible_party;
			$new_ticket["incident_date"] = $incident_date;
			$new_ticket["estimated_completion_date"] = $estimated_completion_date;
			
			if($amount == 0)
			{
				$amount = "NULL";
			}
			$new_ticket["amount"] = $amount;
			
			db_insert_ticket($new_ticket);
			
			//GET NEWLY CREATED TICKET
			$where = null;
			$newly_created_ticket = db_select_ticket($new_ticket);
			
			//GET DEFAULT DAMAGE REPAIR EXPENSE ACCOUNT
			$where = null;
			$where["company_id"] = $leasing_company["id"];
			$where["category"] = "Damage Repair Expense";
			$default_damage_expense_account = db_select_default_account($where);
			
			//CREDIT LIABILITY ACCOUNT, DEBIT EXPENSE ACCOUNT

			$transaction = null;
			$transaction["category"] = "Damage Incurred";
			$transaction["description"] = $description;
			
			$entries = array();
			
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $default_damage_liability_account["account_id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($incident_date));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $amount;
			$credit_entry["entry_description"] = "Damage Incurred | Ticket# ".$newly_created_ticket["id"]." | ".$description;
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $default_damage_expense_account["account_id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($incident_date));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $amount;
			$debit_entry["entry_description"] = "Damage Incurred | Ticket# ".$newly_created_ticket["id"]." | ".$description;
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			create_transaction_and_entries($transaction,$entries);
		}
		else if($category == "Claim")
		{
			//GET DEFAULT INSURANCE CLAIM A/R ACCOUNT
			$where = null;
			$where["company_id"] = $leasing_company["id"];
			$where["category"] = "Insurance Claim A/R";
			$default_claim_ar_account = db_select_default_account($where);
			
			$new_ticket = null;
			if($truck_or_trailer == "Select")
			{
				$new_ticket['unit_number'] = "";
			}
			else if($truck_or_trailer == "Truck")
			{
				$new_ticket['truck_id'] = $truck_id;
				$new_ticket['truck_or_trailer'] = "Truck";
			}
			else if($truck_or_trailer == "Trailer")
			{
				$new_ticket['trailer_id'] = $trailer_id;
				$new_ticket['truck_or_trailer'] = "Trailer";
			}
			else if($truck_or_trailer == "Other")
			{
				$new_ticket['truck_or_trailer'] = "Other";
			}
			
			$new_ticket["balance_sheet_account_id"] = $default_claim_ar_account["account_id"];
			$new_ticket["category"] = $category;
			$new_ticket["description"] = $description;
			$new_ticket["responsible_party"] = $responsible_party;
			$new_ticket["incident_date"] = $incident_date;
			$new_ticket["estimated_completion_date"] = $estimated_completion_date;
			
			if($amount == 0)
			{
				$amount = "NULL";
			}
			$new_ticket["amount"] = $amount;
			
			db_insert_ticket($new_ticket);
			
			//GET NEWLY CREATED TICKET
			$where = null;
			$newly_created_ticket = db_select_ticket($new_ticket);
			
			//GET DEFAULT INSURANCE CLAIM REVENUE ACCOUNT
			$where = null;
			$where["company_id"] = $leasing_company["id"];
			$where["category"] = "Insurance Claim Revenue";
			$default_claim_revenue_account = db_select_default_account($where);
			
			//CREDIT LIABILITY ACCOUNT, DEBIT EXPENSE ACCOUNT

			$transaction = null;
			$transaction["category"] = "Claim Filed";
			$transaction["description"] = $description;
			
			$entries = array();
			
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $default_claim_revenue_account["account_id"];
			$credit_entry["recorder_id"] = $recorder_id;
			$credit_entry["recorded_datetime"] = $recorded_datetime;
			$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($incident_date));
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $amount;
			$credit_entry["entry_description"] = "Claim Filed | Ticket# ".$newly_created_ticket["id"]." | ".$description;
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $default_claim_ar_account["account_id"];
			$debit_entry["recorder_id"] = $recorder_id;
			$debit_entry["recorded_datetime"] = $recorded_datetime;
			$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($incident_date));
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $amount;
			$debit_entry["entry_description"] = "Claim Filed | Ticket# ".$newly_created_ticket["id"]." | ".$description;
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
			create_transaction_and_entries($transaction,$entries);
		}
		else if($category == "Inspection")
		{
			$new_ticket = null;
			
			if($truck_or_trailer == "Select")
			{
				$new_ticket['unit_number'] = "";
			}
			else if($truck_or_trailer == "Truck")
			{
				$new_ticket['truck_id'] = $truck_id;
				$new_ticket['truck_or_trailer'] = "Truck";
			}
			else if($truck_or_trailer == "Trailer")
			{
				$new_ticket['trailer_id'] = $trailer_id;
				$new_ticket['truck_or_trailer'] = "Trailer";
			}
			else if($truck_or_trailer == "Other")
			{
				$new_ticket['truck_or_trailer'] = "Other";
			}
			$new_ticket["inspection_type"] = "Quick";
			$new_ticket["category"] = $category;
			$new_ticket["description"] = $description;
			$new_ticket["responsible_party"] = $responsible_party;
			$new_ticket["incident_date"] = $incident_date;
			$new_ticket["estimated_completion_date"] = $estimated_completion_date;
			
			if($amount == 0)
			{
				$amount = "NULL";
			}
			$new_ticket["amount"] = $amount;
			
			db_insert_ticket($new_ticket);
			
			$ticket = db_select_ticket($new_ticket);
			$ticket_id = $ticket['id'];
			
			$new_inspection = null;
			$new_inspection['ticket_id'] = $ticket_id;
			
			if($truck_or_trailer == "Truck")
			{
				db_insert_truck_inspection($new_inspection);
				$inspection = db_select_truck_inspection($new_inspection);
			}
			else if($truck_or_trailer == "Trailer")
			{
				db_insert_trailer_inspection($new_inspection);
				$inspection = db_select_trailer_inspection($new_inspection);
			}
			
			$inspection_id = $inspection['id'];
			
			$where = null;
			$where['id'] = $ticket_id;
			$set['inspection_id'] = $inspection_id;
			db_update_ticket($set,$where);
		}
		
	}
	
	//GET NOTES FOR SPECIFIED LEAD
    function get_notes($ticket_id)
    {
        $where = null;
        $where['id'] = $ticket_id;
        $ticket = db_select_ticket($where);
        //echo $lead['id'];
        $data['ticket'] = $ticket;
        $this->load->view('tickets/ticket_notes_div',$data);
    }//end get_notes
	
	function load_new_ticket_form()
	{
		if(user_has_permission("create new ticket"))
		{
			
			//GET TRAILER NUMBER
			$trailer_options = array();
			$trailer_options["Select"] = "Select Trailer";
			
			$where = null;
			$where['dropdown_status'] = "Show";
			
			$trailers = db_select_trailers($where);
			
			if(!empty($trailers))
			{
				foreach($trailers as $trailer)
				{
					$trailer_options[$trailer['id']] = $trailer['trailer_number'];
				}
			}
			
			//GET TRUCK NUMBER
			$truck_options = array();
			$truck_options["Select"] = "Select Truck";
			
			$where = null;
			$where['dropdown_status'] = "Show";
			
			$trucks = db_select_trucks($where);
			
			if(!empty($trucks))
			{
				foreach($trucks as $truck)
				{
					$truck_options[$truck['id']] = $truck['truck_number'];
				}
			}
			
			//GET DRIVERS AND BUSINESS USERS
			$driver_options = array();
			$driver_options["Select"] = "Select";
			
			$where = null;
			$where['dropdown_status'] = "Show";
			
			$drivers = db_select_clients($where);
			
			if(!empty($drivers))
			{
				foreach($drivers as $driver)
				{
					$driver_options[$driver['client_nickname']] = $driver['client_nickname'];
					//echo $option;
				}
			}
			
			$data['driver_options'] = $driver_options;
			$data['trailer_options'] = $trailer_options;
			$data['truck_options'] = $truck_options;
			$this->load->view('tickets/new_ticket_form',$data);
		}
		else
		{
			echo "<br><br><div style='color:red'>You don't have permission to add a new ticket.</div>";
		}
	}
	
	function load_report()
	{
		$search_term = $_POST['ticket_search_input'];
		$truck_number = $_POST['truck_number_input'];
		$trailer_number = $_POST['trailer_number_input'];
		$category = $_POST['ticket_category_input'];
		$status = $_POST['status_filter'];
		$after_incident_date = $_POST['after_incident_date_filter'];
		$before_incident_date = $_POST['before_incident_date_filter'];
		$after_action_date = $_POST['after_action_date_filter'];
		$before_action_date = $_POST['before_action_date_filter'];
		$after_estimated_date = $_POST['after_estimated_date_filter'];
		$before_estimated_date = $_POST['before_estimated_date_filter'];
		$after_completion_date = $_POST['after_completion_date_filter'];
		$before_completion_date = $_POST['before_completion_date_filter'];
		
		$where = 'AND ticket.id IS NOT NULL';
		
		if(!empty($search_term))
		{
			$where = $where." AND ticket_number = '".$search_term."' OR insurance_claim.claim_number = '".$search_term."' OR ticket.id = '".$search_term."' OR truck_number = '".$search_term."' OR trailer_number = '".$search_term."'";
		}
		else
		{
			if($truck_number != "All")
			{
				$where = $where." AND truck_number = '".$truck_number."'";
			}
			if($trailer_number != "All")
			{
				$where = $where." AND trailer_number = '".$trailer_number."'";
			}
			if($category != "All")
			{
				$where = $where." AND category = '".$category."'";
			}
			if($status != "All")
			{
				if($status=="Open")
				{
					$where = $where." AND completion_date IS NULL";
				}
				else if($status=="Closed")
				{
					$where = $where." AND completion_date IS NOT NULL";
				}
			}
			if(!empty($after_incident_date))
			{
				$where = $where." AND incident_date > '".date('Y-m-d',strtotime($after_incident_date))."'";
			}
			if(!empty($before_incident_date))
			{
				$where = $where." AND incident_date < '".date('Y-m-d',strtotime($before_incident_date))."'";
			}
			if(!empty($after_action_date))
			{
				$where = $where." HAVING action_item_due_date > '".date('Y-m-d',strtotime($after_action_date))."'";
				if(!empty($before_action_date))
				{
					$where = $where." AND action_item_due_date < '".date('Y-m-d',strtotime($before_action_date))."'";
				}
			}
			else if(!empty($before_action_date))
			{
				$where = $where." HAVING action_item_due_date < '".date('Y-m-d',strtotime($before_action_date))."'";
			}
			if(!empty($after_estimated_date))
			{
				$where = $where." AND estimated_completion_date > '".date('Y-m-d',strtotime($after_estimated_date))."'";
			}
			if(!empty($before_estimated_date))
			{
				$where = $where." AND estimated_completion_date < '".date('Y-m-d',strtotime($before_estimated_date))."'";
			}
			if(!empty($after_completion_date))
			{
				$where = $where." AND completion_date > '".date('Y-m-d',strtotime($after_completion_date))."'";
			}
			if(!empty($before_completion_date))
			{
				$where = $where." AND completion_date < '".date('Y-m-d',strtotime($before_completion_date))."'";
			}
		}
		$where = substr($where,4);
		
		//echo $where;
		
		$tickets = db_select_tickets($where,"id DESC");
		
		if(!empty($tickets))
		{
			$ticket_count = 0;
			$amount_total = 0;
			
			foreach($tickets as $ticket)
			{
				$ticket_count += 1;
				$amount_total += $ticket['amount'];
			}
			
			$data['amount_total'] = $amount_total;
			$data['ticket_count'] = $ticket_count;
		
			$where = null;
			$where['dropdown_status'] = "Show";
			
			$trucks = db_select_trucks($where);
			
			$where = null;
			$where['dropdown_status'] = "Show";
			
			$trailers = db_select_trailers($where);
			
			$data['trailers'] = $trailers;
			$data['trucks'] = $trucks;
			$data['tickets'] = $tickets;
			$this->load->view('tickets/ticket_report',$data);
		}
		else
		{
			$data['ticket_count'] = 0;
			$data['amount_total'] = 0;
			$this->load->view('tickets/ticket_report',$data);
		}
	}
	
	function load_sub_ticket()
	{
		
		$ticket_id = $_POST['ticket_id'];
		
		$where = null;
		$where['id'] = $ticket_id;
		
		$ticket = db_select_ticket($where);
		
		//GET DRIVERS AND BUSINESS USERS
		$driver_options = array();
		$driver_options["Select"] = "Select";
		
		$where = null;
		$where['dropdown_status'] = "Show";
		
		$drivers = db_select_clients($where);
		
		if(!empty($drivers))
		{
			foreach($drivers as $driver)
			{
				$driver_options[$driver['client_nickname']] = $driver['client_nickname'];
				//echo $option;
			}
		}
		
		$inspection = '';
		
		$truck_options = array();
		$truck_options["Select"] = "Select";
		$trailer_options = array();
		$trailer_options["Select"] = "Select";
		if($ticket['truck_or_trailer']=="Truck")
		{
			//GET TRUCK NUMBER
			
			$where = null;
			$where['dropdown_status'] = "Show";
			
			$trucks = db_select_trucks($where);
			
			if(!empty($trucks))
			{
				foreach($trucks as $truck)
				{
					$truck_options[$truck['id']] = $truck['truck_number'];
				}
			}
			
			if(!is_null($ticket['inspection_id']))
			{
				$where = null;
				$where['ticket_id'] = $ticket['id'];
				$inspection = db_select_truck_inspection($where);
				
			}
			
		}
		else
		{
			//GET TRAILER NUMBER
			
			$where = null;
			$where['dropdown_status'] = "Show";
			
			$trailers = db_select_trailers($where);
			
			if(!empty($trailers))
			{
				foreach($trailers as $trailer)
				{
					$trailer_options[$trailer['id']] = $trailer['trailer_number'];
				}
			}
			
			if(!is_null($ticket['inspection_id']))
			{
				$where = null;
				$where['ticket_id'] = $ticket['id'];
				$inspection = db_select_trailer_inspection($where);
			}
		}
		
		$where = null;
		$where['id'] = $ticket_id;
		$ticket = db_select_ticket($where);
		
		$where = null;
		$where['type'] = "ticket";
		$where['attached_to_id'] = $ticket_id;
		$attachments = db_select_attachments($where);
		
		
		$data['truck_options'] = $truck_options;
		$data['trailer_options'] = $trailer_options;
		$data['inspection'] = $inspection;
		$data['ticket_id'] = $ticket_id;
		$data['attachments'] = $attachments;
		$data['driver_options'] = $driver_options;
		$data['ticket'] = $ticket;
		$this->load->view('tickets/sub_ticket',$data);
		
	}
	
	function load_ticket_file_upload()
	{
		$ticket_id = $_POST["ticket_id"];
		
		$data = null;
		$data["ticket_id"] = $ticket_id;
		$this->load->view('tickets/ticket_attachment_div',$data);
	}
	
	function load_inspection_picture_dialog()
	{
		$inspection_id = $_POST["inspection_id"];
		$ticket_id = $_POST["ticket_id"];
		$pic_title = $_POST["pic_title"];
		
		$pic_title_text = "Picture";
		if($pic_title == "truck_pic_right_side_guid")
		{
			$pic_title_text = "Right Side Picture";
		}
		else if($pic_title == "truck_pic_left_side_guid")
		{
			$pic_title_text = "Left Side Picture";
		}
		else if($pic_title == "truck_pic_front_guid")
		{
			$pic_title_text = "Front Picture";
		}
		else if($pic_title == "truck_pic_back_guid")
		{
			$pic_title_text = "Back Picture";
		}
		else if($pic_title == "truck_pic_transponder_guid")
		{
			$pic_title_text = "Transponder Picture";
		}
		else if($pic_title == "truck_pic_driver_seat_guid")
		{
			$pic_title_text = "Driver Seat Picture";
		}
		else if($pic_title == "truck_pic_passenger_seat_guid")
		{
			$pic_title_text = "Passenger Seat Picture";
		}
		else if($pic_title == "truck_pic_dash_board_guid")
		{
			$pic_title_text = "Dash Board Picture";
		}
		else if($pic_title == "truck_pic_odometer_guid")
		{
			$pic_title_text = "Odometer Picture";
		}
		else if($pic_title == "truck_pic_front_right_axle_guid")
		{
			$pic_title_text = "Front Right Axle Picture";
		}
		else if($pic_title == "truck_pic_front_left_axle_guid")
		{
			$pic_title_text = "Front Left Axle Picture";
		}
		else if($pic_title == "truck_pic_back_right_axle_guid")
		{
			$pic_title_text = "Back Right Axle Picture";
		}
		else if($pic_title == "truck_pic_back_left_axle_guid")
		{
			$pic_title_text = "Back Left Axle Picture";
		}
		
		$data = null;
		$data["pic_title_text"] = $pic_title_text;
		$data["inspection_id"] = $inspection_id;
		$data["ticket_id"] = $ticket_id;
		$data["pic_title"] = $pic_title;
		$this->load->view('tickets/inspection_pictures_upload_div',$data);
	}
	
	function load_ticket_row()
	{
		$ticket_id = $_POST['ticket_id'];
		
		$where = null;
		$where['id'] = $ticket_id;
		$ticket = db_select_ticket($where);
		
	
		$where = null;
		$where['dropdown_status'] = "Show";
		
		$trucks = db_select_trucks($where);
		
		$where = null;
		$where['dropdown_status'] = "Show";
		
		$trailers = db_select_trailers($where);
		
		$data['trailers'] = $trailers;
		$data['trucks'] = $trucks;
		$data['ticket'] = $ticket;
		$this->load->view("tickets/ticket_row",$data);
	}
	
	function save_inspection()
	{
		$ticket_id = $_POST['ticket_id'];
		
		$set = null;
		$set["inspection_type"] = $_POST["truck_inspection_type"];
		$where = null;
		$where["id"] = $ticket_id;
		db_update_ticket($set,$where);
		
		$where = null;
		$where['id'] = $ticket_id;
		$ticket = db_select_ticket($where);
		
		$unit_type = $ticket['truck_or_trailer'];
		
		if($unit_type=="Truck")
		{
			$set = null;
			
			$set['odometer'] = $_POST['odometer'];
			$set['is_steering_vibrating'] = $_POST['is_steering_vibrating'];
			$set['is_steering_vibrating_desc'] = $_POST['is_steering_vibrating_desc'];
			$set['are_truck_tires_wearing_uniformly'] = $_POST['are_truck_tires_wearing_uniformly'];
			$set['are_truck_tires_wearing_uniformly_desc'] = $_POST['are_truck_tires_wearing_uniformly_desc'];
			$set['is_pulling_left'] = $_POST['is_pulling_left'];
			$set['is_pulling_left_desc'] = $_POST['is_pulling_left_desc'];
			$set['is_pulling_right'] = $_POST['is_pulling_right'];
			$set['is_pulling_right_desc'] = $_POST['is_pulling_right_desc'];
			$set['is_new_truck_tire_incident'] = $_POST['is_new_truck_tire_incident'];
			$set['is_new_truck_tire_incident_desc'] = $_POST['is_new_truck_tire_incident_desc'];
			$set['additional_truck_notes'] = $_POST['additional_truck_notes'];
			$set['is_new_truck_damage'] = $_POST['is_new_truck_damage'];
			$set['is_new_truck_damage_desc'] = $_POST['is_new_truck_damage_desc'];
			
			// $set['governed_at_drive_test'] = $_POST['governed_at_drive_test'];
			// $set['are_vibrations_drive_test'] = $_POST['are_vibrations_drive_test'];
			// $set['are_vibrations_desc_drive_test'] = $_POST['are_vibrations_desc_drive_test'];
			// $set['is_pulling_drive_test'] = $_POST['is_pulling_drive_test'];
			// $set['is_pulling_drive_test_desc'] = $_POST['is_pulling_drive_test_desc'];
			// $set['is_engine_break_working_drive_test'] = $_POST['is_engine_break_working_drive_test'];
			// $set['is_engine_break_working_drive_test_desc'] = $_POST['is_engine_break_working_drive_test_desc'];
			// $set['is_shifter_working_drive_test'] = $_POST['is_shifter_working_drive_test'];
			// $set['is_shifter_working_drive_test_desc'] = $_POST['is_shifter_working_drive_test_desc'];
			// $set['is_interior_clean_drive_test'] = $_POST['is_interior_clean_drive_test'];
			// $set['is_interior_clean_drive_test_desc'] = $_POST['is_interior_clean_drive_test_desc'];
			// $set['is_seat_damage_drive_test'] = $_POST['is_seat_damage_drive_test'];
			// $set['is_seat_damage_drive_test_desc'] = $_POST['is_seat_damage_drive_test_desc'];
			// $set['is_curtain_divider_damaged_drive_test'] = $_POST['is_curtain_divider_damaged_drive_test'];
			// $set['is_curtain_divider_damaged_drive_test_desc'] = $_POST['is_curtain_divider_damaged_drive_test_desc'];
			// $set['are_knobs_working_drive_test'] = $_POST['are_knobs_working_drive_test'];
			// $set['are_knobs_working_drive_test_desc'] = $_POST['are_knobs_working_drive_test_desc'];
			// $set['are_vents_working_drive_test'] = $_POST['are_vents_working_drive_test'];
			// $set['are_vents_working_drive_test_desc'] = $_POST['are_vents_working_drive_test_desc'];
			// $set['is_dash_clear_drive_test'] = $_POST['is_dash_clear_drive_test'];
			// $set['is_dash_clear_drive_test_desc'] = $_POST['is_dash_clear_drive_test_desc'];
			// $set['are_cracks_in_windshield_drive_test'] = $_POST['are_cracks_in_windshield_drive_test'];
			// $set['are_cracks_in_windshield_drive_test_desc'] = $_POST['are_cracks_in_windshield_drive_test_desc'];
			// $set['are_interior_lights_working_drive_test'] = $_POST['are_interior_lights_working_drive_test'];
			// $set['are_interior_lights_working_drive_test_desc'] = $_POST['are_interior_lights_working_drive_test_desc'];
			// $set['is_air_suspension_working_drive_test'] = $_POST['is_air_suspension_working_drive_test'];
			// $set['is_air_suspension_working_drive_test_desc'] = $_POST['is_air_suspension_working_drive_test_desc'];
			// $set['is_apu_working_drive_test'] = $_POST['is_apu_working_drive_test'];
			// $set['is_apu_working_drive_test_desc'] = $_POST['is_apu_working_drive_test_desc'];
			// $set['are_headlights_working_drive_test'] = $_POST['are_headlights_working_drive_test'];
			// $set['are_headlights_working_drive_test_desc'] = $_POST['are_headlights_working_drive_test_desc'];
			// $set['are_fog_lights_working_drive_test'] = $_POST['are_fog_lights_working_drive_test'];
			// $set['are_fog_lights_working_drive_test_desc'] = $_POST['are_fog_lights_working_drive_test_desc'];
			// $set['are_windshield_wipers_working_drive_test'] = $_POST['are_windshield_wipers_working_drive_test'];
			// $set['are_windshield_wipers_working_drive_test_desc'] = $_POST['are_windshield_wipers_working_drive_test_desc'];
			// $set['are_documents_in_truck_drive_test'] = $_POST['are_documents_in_truck_drive_test'];
			// $set['are_documents_in_truck_drive_test_desc'] = $_POST['are_documents_in_truck_drive_test_desc'];
			// $set['is_dispatch_contact_info_in_truck_drive_test'] = $_POST['is_dispatch_contact_info_in_truck_drive_test'];
			// $set['is_dispatch_contact_info_in_truck_drive_test_desc'] = $_POST['is_dispatch_contact_info_in_truck_drive_test_desc'];
			// $set['are_doors_working_drive_test'] = $_POST['are_doors_working_drive_test'];
			// $set['are_doors_working_drive_test_desc'] = $_POST['are_doors_working_drive_test_desc'];
			// $set['are_windows_working_drive_test'] = $_POST['are_windows_working_drive_test'];
			// $set['are_windows_working_drive_test_desc'] = $_POST['are_windows_working_drive_test_desc'];
			// $set['are_back_windows_working_drive_test'] = $_POST['are_back_windows_working_drive_test'];
			// $set['are_back_windows_working_drive_test_desc'] = $_POST['are_back_windows_working_drive_test_desc'];
			// $set['are_exterior_doors_under_bunk_working_drive_test'] = $_POST['are_exterior_doors_under_bunk_working_drive_test'];
			// $set['are_exterior_doors_under_bunk_working_drive_test_drive_test'] = $_POST['are_exterior_doors_under_bunk_working_drive_test_drive_test'];
			// $set['are_air_or_electrical_lines_damaged_drive_test'] = $_POST['are_air_or_electrical_lines_damaged_drive_test'];
			// $set['are_air_or_electrical_lines_damaged_drive_test_drive_test'] = $_POST['are_air_or_electrical_lines_damaged_drive_test_drive_test'];
			// $set['are_dents_in_back_of_cab_drive_test'] = $_POST['are_dents_in_back_of_cab_drive_test'];
			// $set['are_dents_in_back_of_cab_drive_test_desc'] = $_POST['are_dents_in_back_of_cab_drive_test_desc'];
			// $set['are_dents_or_scratches_drive_test'] = $_POST['are_dents_or_scratches_drive_test'];
			// $set['are_dents_or_scratches_drive_test_test_desc'] = $_POST['are_dents_or_scratches_drive_test_test_desc'];
			// $set['is_hood_working_properly_drive_test'] = $_POST['is_hood_working_properly_drive_test'];
			// $set['is_hood_working_properly_drive_test_desc'] = $_POST['is_hood_working_properly_drive_test_desc'];
			// $set['are_fluids_good_drive_test'] = $_POST['are_fluids_good_drive_test'];
			// $set['are_fluids_good_drive_test_desc'] = $_POST['are_fluids_good_drive_test_desc'];
			// $set['is_air_filter_working_drive_test'] = $_POST['is_air_filter_working_drive_test'];
			// $set['is_air_filter_working_drive_test_desc'] = $_POST['is_air_filter_working_drive_test_desc'];
			// $set['are_bracket_supports_damaged_drive_test'] = $_POST['are_bracket_supports_damaged_drive_test'];
			// $set['are_bracket_supports_damaged_drive_test_desc'] = $_POST['are_bracket_supports_damaged_drive_test_desc'];
			// $set['are_side_ferrings_damaged_drive_test'] = $_POST['are_side_ferrings_damaged_drive_test'];
			// $set['are_side_ferrings_damaged_drive_test_desc'] = $_POST['are_side_ferrings_damaged_drive_test_desc'];
			// $set['are_vertical_ferrings_damaged_drive_test'] = $_POST['are_vertical_ferrings_damaged_drive_test'];
			// $set['are_vertical_ferrings_damaged_drive_test_desc'] = $_POST['are_vertical_ferrings_damaged_drive_test_desc'];
			// $set['is_u_joint_tight_drive_test'] = $_POST['is_u_joint_tight_drive_test'];
			// $set['is_u_joint_tight_drive_test_desc'] = $_POST['is_u_joint_tight_drive_test_desc'];
			// $set['are_tires_damaged_drive_test'] = $_POST['are_tires_damaged_drive_test'];
			// $set['are_tires_damaged_drive_test_desc'] = $_POST['are_tires_damaged_drive_test_desc'];
			// $set['is_tire_tread_depth_ok_drive_test'] = $_POST['is_tire_tread_depth_ok_drive_test'];
			// $set['is_tire_tread_depth_ok_drive_test_desc'] = $_POST['is_tire_tread_depth_ok_drive_test_desc'];
			// $set['is_tire_air_pressure_ok_drive_test'] = $_POST['is_tire_air_pressure_ok_drive_test'];
			// $set['is_tire_air_pressure_ok_drive_test_desc'] = $_POST['is_tire_air_pressure_ok_drive_test_desc'];
			// $set['is_hub_oil_ok_drive_test'] = $_POST['is_hub_oil_ok_drive_test'];
			// $set['is_hub_oil_ok_drive_test_desc'] = $_POST['is_hub_oil_ok_drive_test_desc'];
			// $set['is_grill_or_hood_damaged_drive_test'] = $_POST['is_grill_or_hood_damaged_drive_test'];
			// $set['is_grill_or_hood_damaged_drive_test_desc'] = $_POST['is_grill_or_hood_damaged_drive_test_desc'];
			// $set['are_mirrors_damaged_drive_test'] = $_POST['are_mirrors_damaged_drive_test'];
			// $set['are_mirrors_damaged_drive_test_desc'] = $_POST['are_mirrors_damaged_drive_test_desc'];
			// $set['other_problems_drive_test'] = $_POST['other_problems_drive_test'];
			
		
			$where = null;
			$where['ticket_id'] = $ticket_id;
			db_update_truck_inspection($set,$where);
			
		}
		else if($unit_type=="Trailer")
		{
			$set['is_dog_tailing'] = $_POST['is_dog_tailing'];
			$set['are_trailer_tires_wearing_uniformly'] = $_POST['are_trailer_tires_wearing_uniformly'];
			$set['are_trailer_tires_wearing_uniformly_desc'] = $_POST['are_trailer_tires_wearing_uniformly_desc'];
			$set['is_new_trailer_tire_incident'] = $_POST['is_new_trailer_tire_incident'];
			$set['is_new_trailer_tire_incident_desc'] = $_POST['is_new_trailer_tire_incident_desc'];
			$set['additional_trailer_notes'] = $_POST['additional_trailer_notes'];
			$set['is_new_trailer_damage'] = $_POST['is_new_trailer_damage'];
			$set['is_new_trailer_damage_desc'] = $_POST['is_new_trailer_damage_desc'];

			$set['are_electric_or_air_connections_damaged'] = $_POST['are_electric_or_air_connections_damaged'];
			$set['are_electric_or_air_connections_damaged_desc'] = $_POST['are_electric_or_air_connections_damaged_desc'];
			$set['is_headboard_damaged'] = $_POST['is_headboard_damaged'];
			$set['is_headboard_damaged_desc'] = $_POST['is_headboard_damaged_desc'];
			$set['are_fifth_wheel_or_kingpin_damaged'] = $_POST['are_fifth_wheel_or_kingpin_damaged'];
			$set['are_fifth_wheel_or_kingpin_damaged_desc'] = $_POST['are_fifth_wheel_or_kingpin_damaged_desc'];
			$set['are_lights_damaged'] = $_POST['are_lights_damaged'];
			$set['are_lights_damaged_desc'] = $_POST['are_lights_damaged_desc'];
			$set['is_landing_gear_damaged'] = $_POST['is_landing_gear_damaged'];
			$set['is_landing_gear_damaged_desc'] = $_POST['is_landing_gear_damaged_desc'];
			$set['are_reflectors_damaged'] = $_POST['are_reflectors_damaged'];
			$set['are_reflectors_damaged_desc'] = $_POST['are_reflectors_damaged_desc'];
			$set['are_tires_damaged'] = $_POST['are_tires_damaged'];
			$set['are_tires_damaged_desc'] = $_POST['are_tires_damaged_desc'];
			$set['are_wheels_or_lugs_damaged'] = $_POST['are_wheels_or_lugs_damaged'];
			$set['are_wheels_or_lugs_damaged_desc'] = $_POST['are_wheels_or_lugs_damaged_desc'];

			$set['are_spare_tire_rack_chains_lock_works_sign_damaged'] = $_POST['are_spare_tire_rack_chains_lock_works_sign_damaged'];
			$set['are_spare_tire_rack_chains_lock_works_sign_damaged_desc'] = $_POST['are_spare_tire_rack_chains_lock_works_sign_damaged_desc'];
			$set['is_unit_number_decal_damaged'] = $_POST['is_unit_number_decal_damaged'];
			$set['is_unit_number_decal_damaged_desc'] = $_POST['is_unit_number_decal_damaged_desc'];
			$set['are_mud_flaps_damaged'] = $_POST['are_mud_flaps_damaged'];
			$set['are_mud_flaps_damaged_desc'] = $_POST['are_mud_flaps_damaged_desc'];
			$set['is_rear_bumper_damaged'] = $_POST['is_rear_bumper_damaged'];
			$set['is_rear_bumper_damaged_desc'] = $_POST['is_rear_bumper_damaged_desc'];
			$set['are_doors_or_hinges_damaged'] = $_POST['are_doors_or_hinges_damaged'];
			$set['are_doors_or_hinges_damaged_desc'] = $_POST['are_doors_or_hinges_damaged_desc'];
			$set['are_brakes_damaged'] = $_POST['are_brakes_damaged'];
			$set['are_brakes_damaged_desc'] = $_POST['are_brakes_damaged_desc'];
			$set['are_lube_grease_zerks_damaged'] = $_POST['are_lube_grease_zerks_damaged'];
			$set['are_lube_grease_zerks_damaged_desc'] = $_POST['are_lube_grease_zerks_damaged_desc'];
			$set['is_hub_oil_ok'] = $_POST['is_hub_oil_ok'];
			$set['is_hub_oil_ok_desc'] = $_POST['is_hub_oil_ok_desc'];
			$set['are_spare_mudflap_holders_installed'] = $_POST['are_spare_mudflap_holders_installed'];
			$set['are_spare_mudflap_holders_installed_desc'] = $_POST['are_spare_mudflap_holders_installed_desc'];
			$set['is_slide_rail_lubed'] = $_POST['is_slide_rail_lubed'];
			$set['is_slide_rail_lubed_desc'] = $_POST['is_slide_rail_lubed_desc'];
			$set['are_vent_headboard_and_rear_door_damaged'] = $_POST['are_vent_headboard_and_rear_door_damaged'];
			$set['are_vent_headboard_and_rear_door_damaged_desc'] = $_POST['are_vent_headboard_and_rear_door_damaged_desc'];
			$set['is_ceiling_insulatin_damaged'] = $_POST['is_ceiling_insulatin_damaged'];
			$set['is_ceiling_insulatin_damaged_desc'] = $_POST['is_ceiling_insulatin_damaged_desc'];
			$set['are_cross_members_damaged'] = $_POST['are_cross_members_damaged'];
			$set['are_cross_members_damaged_desc'] = $_POST['are_cross_members_damaged_desc'];
			$set['is_side_rail_damaged'] = $_POST['is_side_rail_damaged'];
			$set['is_side_rail_damaged_desc'] = $_POST['is_side_rail_damaged_desc'];
			$set['is_patch_required'] = $_POST['is_patch_required'];
			$set['is_patch_required_desc'] = $_POST['is_patch_required_desc'];
			$set['is_registration_in_box'] = $_POST['is_registration_in_box'];
			$set['is_registration_in_box_desc'] = $_POST['is_registration_in_box_desc'];
			$set['is_tracker_installed_and_working'] = $_POST['is_tracker_installed_and_working'];
			$set['is_tracker_installed_and_working_desc'] = $_POST['is_tracker_installed_and_working_desc'];
			$set['is_inside_trailer_damaged'] = $_POST['is_inside_trailer_damaged'];
			$set['is_inside_trailer_damaged_desc'] = $_POST['is_inside_trailer_damaged_desc'];

			$set['trailer_pic_unit_number_guid'] = $_POST['trailer_pic_unit_number_guid'];
			$set['trailer_pic_tire_rack_guid'] = $_POST['trailer_pic_tire_rack_guid'];
			$set['trailer_pic_spare_tires_guid'] = $_POST['trailer_pic_spare_tires_guid'];
			$set['trailer_pic_interior_guid'] = $_POST['trailer_pic_interior_guid'];
			$set['trailer_pic_right_side_guid'] = $_POST['trailer_pic_right_side_guid'];
			$set['trailer_pic_left_side_guid'] = $_POST['trailer_pic_left_side_guid'];
			$set['trailer_pic_front_guid'] = $_POST['trailer_pic_front_guid'];
			$set['trailer_pic_rear_guid'] = $_POST['trailer_pic_rear_guid'];
			$set['trailer_pic_front_right_axle_guid'] = $_POST['trailer_pic_front_right_axle_guid'];
			$set['trailer_pic_front_left_axle_guid'] = $_POST['trailer_pic_front_left_axle_guid'];
			$set['trailer_pic_back_right_axle_guid'] = $_POST['trailer_pic_back_right_axle_guid'];
			$set['trailer_pic_back_left_axle_guid'] = $_POST['trailer_pic_back_left_axle_guid'];
		
			$where = null;
			$where['ticket_id'] = $ticket_id;
			db_update_trailer_inspection($set,$where);
		}
		else
		{
			
		}
		
		
	}
	
	//SAVE NOTE
    function save_note()
    {
        $ticket_id = $_POST["ticket_id"];
        
        $text = $_POST["new_note"];
        $initials = substr($this->session->userdata('first_name'),0,1).substr($this->session->userdata('last_name'),0,1);
        date_default_timezone_set('America/Denver');
        $date_text = date("m/d/y H:i");
        
        $full_note = $date_text." - ".$initials." | ".$text."\n\n";
        
        $where['id'] = $ticket_id;
        $ticket = db_select_ticket($where);
        
        $update_ticket["notes"] = $full_note.$ticket["notes"];
        db_update_ticket($update_ticket,$where);
        
		// echo $full_note;
        $this->get_notes($ticket_id);
        
        // echo $update_load["settlement_notes"];
    }
	
	function save_ticket()
	{
		$ticket_id = $_POST['ticket_id'];
		$incident_date = $_POST['incident_date'];
		$unit_type = $_POST['unit_type'];
		$responsible_party = $_POST['responsible_party'];
		$unit_number = $_POST['unit_number'];
		$category = $_POST['category'];
		$estimated_completion_date = $_POST['estimated_completion_date'];
		$description = $_POST['description'];
		$amount = $_POST['amount'];
		$completion_date = $_POST['completion_date'];
		
		//echo $ticket_id;
		if(!empty($incident_date))
		{
			$set['incident_date']= date('Y-m-d',strtotime($incident_date));
		}
		else
		{
			$set['incident_date']= '';
		}
		if($unit_type=="Truck")
		{
			$where = null;
			$where['id'] = $unit_number;
			$truck = db_select_truck($where);
			$truck_id = $truck['id'];
			
			$set['truck_id']= $truck_id;
		}
		else if($unit_type=="Trailer")
		{
			$where = null;
			$where['id'] = $unit_number;
			$trailer = db_select_trailer($where);
			$trailer_id = $trailer['id'];
			
			$set['trailer_id']= $trailer_id;
		}
		if(!empty($estimated_completion_date))
		{
			if($estimated_completion_date=="TBD")
			{
				$set['estimated_completion_date']= null;
			}
			else
			{
				$set['estimated_completion_date']= date('Y-m-d',strtotime($estimated_completion_date));
			}
		}
		else
		{
			$set['estimated_completion_date']= null;
		}
		if(!empty($completion_date))
		{
			$set['completion_date']= date('Y-m-d',strtotime($completion_date));
		}
		else
		{
			$set['completion_date']= '';
		}
		
		
		$set['responsible_party']= $responsible_party;
		$set['truck_or_trailer']= $unit_type;
		$set['category']= $category;
		$set['description']= $description;
		$set['amount']= $amount;
		
		$where = null;
		$where['id'] = $ticket_id;
		
		db_update_ticket($set,$where);
		
		$this->load_sub_ticket($ticket_id);
	}

	function close_ticket()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		$recorder_id = $this->session->userdata('user_id');
		
		$ticket_id = $_POST['ticket_id'];
		
		$where = null;
		$where["id"] = $ticket_id;
		$ticket = db_select_ticket($where);
		
		
		
		//GET LEASING COMPANY
		$where = null;
		$where["category"] = "Leasing";
		$leasing_company = db_select_company($where);
		
		$transaction = null;
		$credit_account = null;
		$debit_account = null;
		if($ticket["category"] == "Damage")
		{
			//GET DEFAULT DAMAGE REPAIR LIABILITY ACCOUNT
			$where = null;
			$where["company_id"] = $leasing_company["id"];
			$where["category"] = "Damage Repair Liability";
			$default_damage_liability_account = db_select_default_account($where);
			
			//GET DEFAULT DAMAGE REPAIR EXPENSE ACCOUNT
			$where = null;
			$where["company_id"] = $leasing_company["id"];
			$where["category"] = "Damage Repair Expense";
			$default_damage_expense_account = db_select_default_account($where);
			
			$amount = get_ticket_balance($ticket);
			
			if($amount > 0)
			{
				$description = "Ticket Closed | Ticket# ".$ticket_id." | Overestimated costs of repairs. $".$amount." to be credited back";
			}
			else
			{
				$description = "Ticket Closed | Ticket# ".$ticket_id." | Underestimated costs of repairs. Additional $".($amount*-1)." of repairs required to close ticket";
			}
			
			//CREDIT LIABILITY ACCOUNT -(BALANCE), DEBIT EXPENSE ACCOUNT -(BALANCE)
			$transaction["category"] = "Damage Ticket Closed";
			$transaction["description"] = $description;
			
			$credit_account = $default_damage_expense_account["account_id"];
			$debit_account = $default_damage_liability_account["account_id"];
		}
		else if($ticket["category"] == "Claim")
		{
			//GET DEFAULT INSURANCE CLAIM REVENUE ACCOUNT
			$where = null;
			$where["company_id"] = $leasing_company["id"];
			$where["category"] = "Insurance Claim Revenue";
			$default_claim_revenue_account = db_select_default_account($where);
			
			//GET DEFAULT INSURANCE CLAIM A/R ACCOUNT
			$where = null;
			$where["company_id"] = $leasing_company["id"];
			$where["category"] = "Insurance Claim A/R";
			$default_claim_ar_account = db_select_default_account($where);
			
			$amount = get_ticket_balance($ticket);
			
			if($amount > 0)
			{
				$description = "Ticket Closed | Ticket# ".$ticket_id." | Overestimated payout of insurance claim. $".$amount." of revenues to be deducted.";
			}
			else
			{
				$description = "Ticket Closed | Ticket# ".$ticket_id." | Underestimated payout of insurance claim. Additional $".($amount*-1)." of revenues realized.";
			}
			
			//CREDIT A/R ACCOUNT -(BALANCE), DEBIT REVENUE ACCOUNT -(BALANCE)
			$transaction["category"] = "Claim Ticket Closed";
			$transaction["description"] = $description;

			$credit_account = $default_claim_ar_account["account_id"];
			$debit_account = $default_claim_revenue_account["account_id"];
		}
		
		
		$entries = array();
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $credit_account;
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($incident_date));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $amount;
		$credit_entry["entry_description"] = $description;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $debit_account;
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($incident_date));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $amount;
		$debit_entry["entry_description"] = $description;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries);
		
		//GET PAYMENT ACCOUNT ENTRY TO ATTACH TO INVOICE
		$where = null;
		$where["account_id"] = $credit_entry["account_id"];
		$where["recorder_id"] = $credit_entry["recorder_id"];
		$where["recorded_datetime"] = $credit_entry["recorded_datetime"];
		$where["entry_datetime"] = $credit_entry["entry_datetime"];
		$where["debit_credit"] = $credit_entry["debit_credit"];
		$where["entry_description"] = $description;
		$ticket_account_entry = db_select_account_entry($where);
		
		//CREATE NEW TICKET PAYMENT
		$ticket_payment = null;
		$ticket_payment["ticket_id"] = $ticket_id;
		$ticket_payment["account_entry_id"] = $ticket_account_entry["id"];
		db_insert_ticket_payment($ticket_payment);
		
		$update_ticket = null;
		$update_ticket["completion_date"] = $recorded_datetime;
		
		$where = null;
		$where["id"] = $ticket_id;
		db_update_ticket($update_ticket,$where);
	}
	
	function generate_insurance_claim()
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		$recorder_id = $this->session->userdata('user_id');
		
		$ticket_id = $_POST['ticket_id'];
		
		$where = null;
		$where["id"] = $ticket_id;
		$ticket = db_select_ticket($where);
		
		//GET LEASING COMPANY
		$where = null;
		$where["category"] = "Leasing";
		$leasing_company = db_select_company($where);
		
		//GET DEFAULT INSURANCE CLAIM REVENUE ACCOUNT
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "Insurance Claim Revenue";
		$default_claim_revenue_account = db_select_default_account($where);
		
		//GET DEFAULT INSURANCE CLAIM A/R ACCOUNT
		$where = null;
		$where["company_id"] = $leasing_company["id"];
		$where["category"] = "Insurance Claim A/R";
		$default_claim_ar_account = db_select_default_account($where);
		
		$new_ticket = null;
		$new_ticket['truck_or_trailer'] = $ticket['truck_or_trailer'];
		$new_ticket['truck_id'] = $ticket['truck_id'];
		$new_ticket['trailer_id'] = $ticket['trailer_id'];
		$new_ticket["balance_sheet_account_id"] = $default_claim_ar_account;
		$new_ticket["category"] = "Claim";
		$new_ticket["description"] = $ticket['description'];
		$new_ticket["responsible_party"] = $ticket['responsible_party'];
		$new_ticket["incident_date"] = $ticket['incident_date'];
		$new_ticket["estimated_completion_date"] = $ticket['estimated_completion_date'];
		$new_ticket["amount"] = $ticket['amount'];
		
		db_insert_ticket($new_ticket);
		
		//GET NEWLY CREATED TICKET
		$where = null;
		$newly_created_ticket = db_select_ticket($new_ticket);
		
		//INSERT NEW INSURANCE CLAIM
		$new_claim = null;
		$new_claim["ticket_id"] = $newly_created_ticket["id"];
		
		db_insert_insurance_claim($new_claim);
		
		//UPDATE ORIGINAL TICKET WITH CLAIM_TICKET_ID
		$update_ticket = null;
		$update_ticket["claim_ticket_id"] = $newly_created_ticket["id"];
		
		$where = null;
		$where["id"] = $ticket["id"];
		db_update_ticket($update_ticket,$where);

		
		//CREDIT REVENUE ACCOUNT, DEBIT A/R ACCOUNT

		$transaction = null;
		$transaction["category"] = "Claim Filed";
		$transaction["description"] = $ticket["description"];
		
		$entries = array();
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $default_claim_revenue_account["account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($ticket["incident_date"]));
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = $ticket["amount"];
		$credit_entry["entry_description"] = "Claim Filed | Ticket# ".$newly_created_ticket["id"]." | ".$ticket["description"];
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		$debit_entry["account_id"] = $default_claim_ar_account["account_id"];
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = date("Y-m-d G:i:s",strtotime($ticket["incident_date"]));
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = $ticket["amount"];
		$debit_entry["entry_description"] = "Claim Filed | Ticket# ".$newly_created_ticket["id"]." | ".$ticket["description"];
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		create_transaction_and_entries($transaction,$entries);
			
	}

	function update_insurance()
	{
		$ticket_id = $_POST["ticket_id"];
		$claim_ticket_id = $_POST["claim_ticket_id"];
		$claim_number = $_POST["claim_number"];
		
		$where = null;
		$where['ticket_id'] = $claim_ticket_id;
		
		$set = array();
		$set['claim_number'] = $claim_number;
		db_update_insurance_claim($set,$where);
		
		load_sub_ticket($ticket_id);
	}
	
	//UPLOAD TICKET ATTACHMENT
	function upload_ticket_attachment()
	{
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
		$post_name = 'attachment_file';
		$file = $_FILES[$post_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		//$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$title = $_POST["attachment_name"];
		$category = "Ticket Attachment";
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$contract_secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		//CREATE ATTACHMENT IN DB
		$attachment = null;
		$attachment["type"] = "ticket";
		$attachment["attached_to_id"] = $_POST["ticket_id"];
		$attachment["file_guid"] = $contract_secure_file["file_guid"];
		$attachment["attachment_name"] = $_POST["attachment_name"];

		db_insert_attachment($attachment);
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		load_upload_success_view();
	}
	
	//UPLOAD TICKET ATTACHMENT
	function upload_inspection_pic()
	{
		$inspection_id = $_POST["inspection_id"];
		
		$ticket_id = $_POST['ticket_id'];
		
		$where = null;
		$where['id'] = $ticket_id;
		$ticket = db_select_ticket($where);
		
		
		
		// //INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER
		// $post_name = 'attachment_file';
		// $file = $_FILES[$post_name];
		// $name = str_replace(' ','_',$file["name"]);
		// $type = $file["type"];
		// //$title = pathinfo($file["name"], PATHINFO_FILENAME);
		// $title = $_POST["pic_title"]."[".$_POST["ticket_id"]."]";
		// $category = "Inspection Picture";
		// $local_path = $file["tmp_name"];
		// $server_path = '/edocuments/';
		// $office_permission = 'All';
		// $driver_permission = 'None';
		// $secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		//INSERT NEW UNSECURE_FILE AND UPLOAD FILE TO FTP SERVER
		$post_name = 'attachment_file';
		$file = $_FILES[$post_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];
		//$title = pathinfo($file["name"], PATHINFO_FILENAME);
		$title = $_POST["pic_title"]."[".$_POST["ticket_id"]."]";
		$category = "Inspection Picture";
		$local_path = $file["tmp_name"];
		$server_path = './inspection_pics/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);
		
		
		$unit_type = $ticket['truck_or_trailer'];
		
		if($unit_type=="Truck")
		{
			$set = null;
			$set[$_POST["pic_title"]] = $secure_file["file_guid"];
			
			$where = null;
			$where['ticket_id'] = $ticket_id;
			db_update_truck_inspection($set,$where);
		}
		else if($unit_type=="Trailer")
		{
			$set = null;
			$set[$_POST["pic_title"]] = $secure_file["file_guid"];
			
			$where = null;
			$where['ticket_id'] = $ticket_id;
			db_update_truck_inspection($set,$where);
		}
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		load_upload_success_view();
	}
	
	//--------------------------------------------------------------
	
	//ONE TIME SCRIPT
	function fix_ticket_ids()
	{
		$where = null;
		$where = "1 = 1";
		$action_items = db_select_action_items($where);
		
		//GET TICKET MANAGER USER ID
		$where = null;
		$where["setting_name"] = "Ticket System Manager";
		$ticket_manager_setting = db_select_setting($where);
		
		foreach($action_items as $action_item)
		{
			$update = null;
			// $update["object_id"] = $action_item["ticket_id"];
			// $update["object_type"] = "ticket";
			$update["owner_id"] = $ticket_manager_setting["setting_value"];
			$update["manager_id"] = $ticket_manager_setting["setting_value"];
			// $update["description"] = $action_item["notes"];
			
			$where = null;
			$where["id"] = $action_item["id"];
			//db_update_action_item($update,$where);
		}
		
		echo "Success!";
		
		
	}
}