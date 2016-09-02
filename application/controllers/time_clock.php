<?php

class Time_clock extends MY_Controller
{
	//INDEX
	function index()
	{
		//echo 'hello time clock';
		if(user_has_permission('can clock in remotely'))
		{
			if(user_has_permission("is dispatcher"))
			{
				$is_dispatcher = 'true';
			}
			else
			{
				$is_dispatcher = 'false';
			}
		
			$where = null;
			$where['permission_id'] = 84;
			$users = db_select_user_permissions($where);
			
			$fleet_manager_and_dispatchers = array();
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
					
					if($last_punch == 'In')
					{
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

						if(!empty($fleet_manager_person))
						{
							$fleet_manager_and_dispatchers[$fleet_manager_person["f_name"]." ".$fleet_manager_person["l_name"]] = $this_user['person']['full_name'];
						}
					}
					
				}
				$data['fleet_manager_and_dispatchers'] = $fleet_manager_and_dispatchers;
			}
			
			//GET FLEET MANAGERS
			$where = null;
			$where = " role = 'Fleet Manager' or role = 'Driver Manager'";
			$fleet_managers = db_select_persons($where,"f_name");
			$fleet_managers_dropdown_options = array();
			$fleet_managers_dropdown_options["None"] = "None";
			foreach($fleet_managers as $fleet_manager)
			{
				$fleet_managers_dropdown_options[$fleet_manager["id"]] = $fleet_manager["f_name"]." ".$fleet_manager["l_name"];
			}

			$data['fleet_manager_and_dispatchers'] = $fleet_manager_and_dispatchers;
			$data['fleet_managers_dropdown_options'] = $fleet_managers_dropdown_options;
			$data['is_dispatcher'] = $is_dispatcher;
			$data['tab'] = "Time Clock";
			$data['title'] = "Time Clock";
			$this->load->view('time_clock_view',$data);
		}
		else
		{
			echo "I don't like what you're thinking.";
		}
	}
	
	//VALIDATE PIN
	function validate_pin()
	{
		$pin = $_POST["pin"];
		
		//GET USER WITH THIS PIN
		$where = null;
		$where["pin"] = $pin;
		$user = db_select_user($where);
	
		//echo 'validated - '.$pin.' '.$user["username"];
		
		//GET PUNCHES
		$where = null;
		$where["user_id"] = $user["id"];
		$last_punch = db_select_time_punch($where,"datetime DESC");
		
		//GET PUNCHES
		$where = null;
		$where["user_id"] = $user["id"];
		$punches = db_select_time_punchs($where,"datetime DESC",20);
		
		//GET LAST PUNCH
		$where = null;
		$where["user_id"] = $user["id"];
		$last_punch = db_select_time_punch($where,"datetime");
		
		$data['last_punch'] = $last_punch;
		$data['punches'] = $punches;
		$data['user'] = $user;
		$data['tab'] = "Time Clock";
		$data['title'] = "Time Clock";
		$this->load->view('time_clock/pin_validated_view',$data);
	}
	
	//SUBMIT PUNCH
	function submit_punch()
	{
		date_default_timezone_set('America/Denver');
		$readable_datetime = date('m/d/y H:i');
		
		//GET POST DATA
		$user_id = $_POST["user_id"];
		$datetime = $_POST["datetime"];
		$in_out = $_POST["in_out"];
		$location = $_POST["location"];
		
		//GET USER
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
				
		$slack_username = $user['slack_username'];
		
		
		//if(!empty($time_punch))
		//{
			//IF LAST PUNCH IS NOT OPPOSITE THIS PUNCH
				//LOAD MISSED PUNCH VIEW
			
			//ELSE
		
				//SAVE PUNCH
				$time_punch = null;
				$time_punch["user_id"] = $user_id;
				$time_punch["datetime"] = $datetime;
				$time_punch["in_out"] = $in_out;
				$time_punch["location"] = $location;
				
				db_insert_time_punch($time_punch);
				
				$last_punch = db_select_time_punch($time_punch);
				
				//SEND NOTIFICATION TO USER
				if($in_out == 'In')
				{
					if(user_has_permission("can clock in remotely"))
					{

						$last_punch_datetime = $last_punch['datetime'];

						$difference = strtotime($datetime) - strtotime($last_punch_datetime);

						$clock_in_id = add_clock_in_verification($user_id);

						$url = "<http://fleetsmarts.integratedlogicsticssolutions.co/index.php/time_clock/clock_in_verification?id=$clock_in_id";

						$message = "$readable_datetime - $url|Click here> to verify that you are at your computer.";
						$channel = "@$slack_username";

						send_slack_message($message,$channel);
					}
				}
				
				//GET PUNCHES
				$where = null;
				$where["user_id"] = $user["id"];
				$punches = db_select_time_punchs($where,"datetime DESC",20);
				
				
				//LOAD TIME CLOCK VIEW
				$data['punches'] = $punches;
				$data['user'] = $user;
				$data['tab'] = "Time Clock";
				$data['title'] = "Time Clock";
				$this->load->view('time_clock/punch_success_view',$data);
		//}
		
	}
	
	function clock_in_verification()
	{
		$clock_in_id = $_GET['id'];

		$where = null;
		$where['id'] = $clock_in_id;
		$clock_in = db_select_clock_in_verification($where);

		$user_id = $clock_in['user_id'];
		
		$where = null;
		$where["user_id"] = $user_id;
		$last_email_sent = db_select_clock_in_verification($where,"email_sent_datetime");
//		print_r($last_email_sent);
		
//		echo "<br>";
		$where = null;
		$where["user_id"] = $user_id;
		$last_punch = db_select_time_punch($where,"datetime");
//		print_r($last_email_sent);
		
//		echo "<br>";
		$last_punch_in_out = $last_punch['in_out'];
		if($last_punch_in_out=="In")
		{
			if(empty($clock_in))
			{
				echo "Verification does not exist!";
			}
			else if(!empty($clock_in['screenshot_uploaded_datetime']))
			{
				echo "You have already submitted a screenshot for this verification!";
			}
			
			else if(empty($clock_in['screenshot_uploaded_datetime'])){

				date_default_timezone_set('America/Denver');
				$datetime = date('Y-m-d H:i:s');


				$data['title'] = 'Clock-In Verification';
				$data['clock_in_id'] = $clock_in_id;
				$data['datetime'] = $datetime;

				$this->load->view('time_clock/clock_in_verification_view',$data);
			}
		}
		else if($last_punch_in_out=="Out")
		{
			$data['tab'] = "Time Clock";
			$data['title'] = "Time Clock";
			$this->load->view('time_clock_view',$data);
		}
		
	}
	
	function verify_clock_in()
	{
		
		date_default_timezone_set('America/Denver');
		$datetime = date('Y-m-d H:i:s');

		$readable_datetime = date('m/d/y H:i');
		$clock_in_id = $_POST['clock_in_id'];

		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
		$post_name = 'attachment_name';
		$file = $_FILES[$post_name];
		$name = str_replace(' ','_',$file["name"]);
		$type = $file["type"];

		$title = $name;
		$category = "Clock-In Verification";
		$local_path = $file["tmp_name"];
		$server_path = '/edocuments/';
		$office_permission = 'All';
		$driver_permission = 'None';
		$screenshot_secure_file = store_secure_ftp_file($post_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission);

		$where = null;
		$where['id'] = $clock_in_id;
		$update = null;
		$update['screenshot_uploaded_datetime'] = $datetime;
		$update["screenshot_guid"] = $screenshot_secure_file["file_guid"];
		db_update_clock_in_verification($update,$where);

		$where = null;
		$where['id'] = $clock_in_id;
		$clock_in_verification = db_select_clock_in_verification($where);

		$email_sent_datetime = strtotime($clock_in_verification['email_sent_datetime']);

		$difference = round(abs(strtotime($datetime) - $email_sent_datetime) / 60,0);

		$hours = round(abs(strtotime($datetime) - $email_sent_datetime) / 60 / 60,2);

		$difference_text = hours_to_text_mixed($hours);
			
		$data['difference_text'] = $difference_text;
		$data['difference'] = $difference;
		$data['email_sent_datetime'] = $email_sent_datetime;
		$data['datetime'] = strtotime($datetime);

		$where = null;
		$where['id'] = $clock_in_verification['user_id'];
		$user = db_select_user($where);
		$user_slack_username = $user['slack_username'];
		
		if($difference < 5)
		{
			$message = "$readable_datetime - Verification successful!";
			
		}
		else if($difference >= 5)
		{
			$url = "<http://fleetsmarts.integratedlogicsticssolutions.co/index.php/time_clock";
			$message = "Unfortunately, it took too long to respond, and you have been logged out of the system. $url|Click Here> to log back in.";
		}
		$channel = "@$user_slack_username";
		send_slack_message($message,$channel);
		
//		print_r($data);
		$this->load->view('time_clock/clock_in_response',$data);
	}
	
	function update_fleet_manager()
	{
		$fleet_manager_id = $_POST['fleet_manager_id'];
		
		if($fleet_manager_id!="None")
		{
			$user_id = $this->session->userdata('user_id');

			$where = null;
			$where['person_id'] = $fleet_manager_id;
			$fleet_manager_company = db_select_company($where);

			$where = null;
			$where['id'] = $user_id;
			$user = db_select_user($where);

			$where = null;
			$where['person_id'] = $user['person_id'];
			$company = db_select_company($where);

			$where = null;
			$where['id'] = $company['id'];

			$update = null;
			$update['managed_by_id'] = $fleet_manager_company['id'];

			db_update_company($update,$where);
		}
	}
}