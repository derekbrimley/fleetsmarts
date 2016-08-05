<?php		


	
class Time_clock extends MY_Controller 
{
	//INDEX
	function index()
	{	
		//echo 'hello time clock';
		if(user_has_permission('can clock in remotely'))
		{
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
		//GET POST DATA
		$user_id = $_POST["user_id"];
		$datetime = $_POST["datetime"];
		$in_out = $_POST["in_out"];
		$location = $_POST["location"];
		
		//GET USER
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		
		
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
				
				//SEND EMAIL TO USER
				
				
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
	
	function clock_in_verification(){
		$clock_in_id = $_GET['id'];
		
		$where = null;
		$where['id'] = $clock_in_id;
		$clock_in = db_select_clock_in_verification($where);
		
		if(!isset($clock_in)){
			echo "Verification does not exist!";
		}else if(empty($clock_in['screenshot_uploaded_datetime'])){
			
			date_default_timezone_set('America/Denver');
			$datetime = date('Y-m-d H:i:s');


			$data['title'] = 'Clock-In Verification';
			$data['clock_in_id'] = $clock_in_id;
			$data['datetime'] = $datetime;

			$this->load->view('time_clock/clock_in_verification_view',$data);
		}else{
			echo "You have already submitted a screenshot for this verification!";
		}
		
	}
	
	function verify_clock_in(){
		
		date_default_timezone_set('America/Denver');
		$datetime = date('Y-m-d H:i:s');

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

//		print_r($data);
		$this->load->view('time_clock/clock_in_response',$data);
			
		
		
	}
}