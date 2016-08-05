<?php		


	
class Todo extends MY_Controller 
{

	function index()
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		$data['title'] = "ToDo";
		$data['tab'] = 'ToDo';
		$this->load->view('todo_view.php',$data);
	}
	
	function load_filter()
	{
		//GET STAFF (PEOPLE) AND FLEET MANAGERS
		$where = null;
		$where = " role = 'Office Staff' OR role = 'Fleet Manager' ";
		$staff_and_fm_people = db_select_persons($where,"f_name");
		
		$user_options = array();
		$user_options["All"] = "All";
		foreach($staff_and_fm_people as $person)
		{
			$where = null;
			$where["person_id"] = $person["id"];
			$where["user_status"] = "Active";
			$user = db_select_user($where);
			
			if(!empty($user))
			{
				$title = $person["f_name"]." ".$person["l_name"];
				$user_options[$user['id']] = $title;
			}
		}
			
		
		$data['user_options'] = $user_options;
		$this->load->view('todo/todo_filter_div',$data);
		
	}
	
	function load_report()
	{
		//GET FILTER PARAMETERS
		$owner_user_id = $_POST["owner_filter_dropdown"];
		$manager_user_id = $_POST["manager_filter_dropdown"];
		$type = $_POST["type_dropdown"];
		$after_due_date_filter = $_POST["after_due_date_filter"];
		$before_due_date_filter = $_POST["before_due_date_filter"];
		$after_completion_date_filter = $_POST["after_completion_date_filter"];
		$before_completion_date_filter = $_POST["before_completion_date_filter"];
		$status = $_POST["status_dropdown"];
		
		//echo $owner_user_id." ".$manager_user_id." ".$type." ".$after_due_date_filter." ".$before_due_date_filter." ".$after_completion_date_filter." ".$before_completion_date_filter." ".$status."<br>";
		
		//SET WHERE FOR ACTION_ITEMS
		$where = " 1 = 1";
		
		//SET WHERE FOR OWNER (user_id)
		if($owner_user_id != "All")
		{
			$where = $where." AND owner_id = '".$owner_user_id."'";
		}
		
		//SET WHERE FOR MANAGER (user_id)
		if($manager_user_id != "All")
		{
			$where = $where." AND manager_id = ".$manager_user_id;
		}
		
		//SET WHERE FOR TYPE
		if($type != "All")
		{
			$where = $where." AND type = '".$type."' ";
		}
		
		//SET WHERE FOR DUE START DATE
		if(!empty($after_due_date_filter))
		{
			$after_due_date_filter = date("Y-m-d G:i:s",strtotime($after_due_date_filter));
			$where = $where." AND due_date >= '".$after_due_date_filter."' ";
		}
		
		//SET WHERE FOR DUE END DATE
		if(!empty($before_due_date_filter))
		{
			$before_due_date_filter = date("Y-m-d G:i:s",strtotime($before_due_date_filter)+24*60*60);
			$where = $where." AND due_date < '".$before_due_date_filter."' ";
		}
		
		//SET WHERE FOR DUE START DATE
		if(!empty($after_completion_date_filter))
		{
			$after_completion_date_filter = date("Y-m-d G:i:s",strtotime($after_completion_date_filter));
			$where = $where." AND due_date >= '".$after_completion_date_filter."' ";
		}
		
		//SET WHERE FOR DUE END DATE
		if(!empty($before_completion_date_filter))
		{
			$before_completion_date_filter = date("Y-m-d G:i:s",strtotime($before_completion_date_filter)+24*60*60);
			$where = $where." AND due_date < '".$before_completion_date_filter."' ";
		}
		
		//SET WHERE FOR ACCOUNT
		if($status != "All")
		{
			if($status == "Open")
			{
				$where = $where." AND completion_date IS NULL ";
			}
			else
			{
				$where = $where." AND completion_date IS NOT NULL ";
			}
			
		}
		
		
		//echo $where;
		
		//GET ACTION_ITEMS
		$action_items = db_select_action_items($where,"due_date DESC");
		
		$data['action_items'] = $action_items;
		$this->load->view('todo/todo_report_div',$data);
		
		//echo print_r($action_items);
	}
	
	//GET NOTES
	function get_notes($row_id)
	{
		$where = null;
		$where["id"] = $row_id;
		$action_item = db_select_action_item($where);
		
		$data['action_item'] = $action_item;
		$this->load->view('todo/action_item_notes_div',$data);
	}//end get_notes
	
	//SAVE NOTE
	function save_note()
	{
		$action_item_id = $_POST["row_id"];
		
		$text = $_POST["new_note"];
		$initials = substr($this->session->userdata('f_name'),0,1).substr($this->session->userdata('l_name'),0,1);
		date_default_timezone_set('America/Denver');
		$date_text = date("m/d/y H:i");
		
		$full_note = $date_text." - ".$initials." | ".$text."\n\n";
		
		$where["id"] = $action_item_id;
		$action_item = db_select_action_item($where);
		
		$update = null;
		$update["notes"] = $full_note.$action_item["notes"];
		db_update_action_item($update,$where);
		
		$this->get_notes($action_item_id);
		
		//echo $update_load["settlement_notes"];
	}
	
	function test()
	{
		//test
	}
	
}
