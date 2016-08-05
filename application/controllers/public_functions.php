<?php		

	
class Public_functions extends CI_Controller 
{
	function index()
	{
		echo "index";
	}
	//LOAD SETTLEMENT VIEW
	function load_driver_settlement_view($log_entry_id,$driver_id)
	{
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		//GET DRIVER
		$where = null;
		$where["id"] = $driver_id;
		$driver = db_select_client($where);
		
		$stats = get_driver_end_week_stats($log_entry,$driver_id);
		
		//GET ALL LEGS FOR THIS SETTLEMENT
		
		
		$data['title'] = "P&L Statement ".date('m-d-y',strtotime($log_entry["entry_datetime"]))." ".$driver["client_nickname"];
		$data['stats'] = $stats;
		$data['driver'] = $driver;
		$data['log_entry'] = $log_entry;
		$this->load->view('settlements/driver_settlement_view',$data);
	}
	
	function test($number)
	{
		echo $number;
	}
	
	function cron_test()
	{
		date_default_timezone_set('America/Denver');
		
		$data = null;
		$message = "Test Email";
		//$to = 'covax13@gmail.com';
		$to = 'derekbrimley@gmail.com';
		$subject = 'Cron Test';
		//$message = "test";
		$headers = "From: paperwork.dispatch@gmail.com\r\n";
		//$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
		$headers .= "CC: paperwork.dispatch@gmail.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		//mail("covax13@gmail.com","FleetSmarts Now Does Email!","You better believe it! Guess who just figured out how to send emails from FleetSmarts!!","From: fleetsmarts@fleetsmarts.net");
		mail($to, $subject, $message, $headers);
		
	}
	
	function send_dispatch_email($dispatch_update_id)//DOESN'T ACTUALLY SEND, JUST DISPLAYS THE HTML
	{
		//GET DISPATCH UPDATE
		$where = null;
		$where["id"] = $dispatch_update_id;
		$dispatch_update = db_select_dispatch_update($where);
		
		//GET LOAD
		$where = null;
		$where["id"] = $dispatch_update["load_id"];
		$load = db_select_load($where);
		
		//GET GOALPOINTS
		$where = null;
		$where["load_id"] = $load["id"];
		$where["completion_time"] = null;
		$goalpoints = db_select_goalpoints($where,"gp_order");
		
		//SEND DISPATCH EMAIL
		$data['load'] = $load;
		$data['goalpoints'] = $goalpoints;
		$this->load->view('emails/dispatch_email',$data);
		
		
		//$this->load->view('emails/dispatch_email',$data, TRUE);
		//$message = $this->load->view('emails/po_request_email',$data, TRUE);
		// $to = 'covax13@gmail.com';
		// $subject = 'Load Dispatch '.$load["id"];
		// $message = "test";
		// $headers = "From: fleetsmarts@fleetsmarts.net\r\n";
		// //$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
		// //$headers .= "CC: susan@example.com\r\n";
		// $headers .= "MIME-Version: 1.0\r\n";
		// $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		// //mail("covax13@gmail.com","FleetSmarts Now Does Email!","You better believe it! Guess who just figured out how to send emails from FleetSmarts!!","From: fleetsmarts@fleetsmarts.net");
		// mail($to, $subject, $message, $headers);
	}
	
	function test_email()
	{
		date_default_timezone_set('America/Denver');
		
		
		$to = 'covax13@gmail.com';
		//$to = $recipients;
		$subject = 'test email from fs';
		$message = "test";
		$headers = "From: fleetsmart@fleetsmart.net\r\n";
		//$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
		//$headers .= "CC: paperwork.dispatch@gmail.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		//mail("covax13@gmail.com","FleetSmarts Now Does Email!","You better believe it! Guess who just figured out how to send emails from FleetSmarts!!","From: fleetsmarts@fleetsmarts.net");
		mail($to, $subject, $message, $headers);
		
		echo "email sent ".date("m/d/y H:i");
	}
	
	function test_ci_email()
	{
		date_default_timezone_set('America/Denver');
		
		$this->load->library('email');

		$this->email->from('derekbrimley@gmail.com', 'D-dog');
		$this->email->to('covax13@gmail.com');
		//$this->email->cc('fleetsmarts@integratedlogicsticssolutions.co');

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');

		$this->email->send();

		echo "ci email sent ".date("m/d/y H:i");
		
		echo $this->email->print_debugger();
	}
	
	function show_carrier_legs($random,$carrier_id)
	{
		//GET ALL LEGS THAT FOR THIS CARRIER ID
		$where = null;
		//$where["carrier_id"] = $carrier_id;
		$where = " leg.carrier_id = $carrier_id AND log_entry.locked_datetime IS NOT NULL AND leg.rate_type <> 'Personal' ";
		$legs = db_select_legs($where,"id DESC");
		
		$data['legs'] = $legs;
		$this->load->view('reports/carrier_legs_report_view',$data);
	}
	
	//********************************* CHRON JOBS ************************************
	
	//DOESN'T WORK ANYMORE
	function fetch_fuel_avg()
	{
		//$url = "http://fuelgaugereport.opisnet.com/index.asp";
		$url = "http://fuelgaugereport.aaa.com/todays-gas-prices/";
		$html = file_get_contents($url);
		
		echo $html;
		
		
        preg_match_all("/\d\.\d{3}/", strip_tags($html), $matches, PREG_SET_ORDER);
        $i=0;
        foreach ($matches as $val) 
		{
           if($i==3)
           {   //National fuel Average of diesel is saved right here as val[0]
               $price =  $val[0];
           }
            $i++;
        }
		
		//STORE PRICE IN DB
		
		date_default_timezone_set('US/Mountain');
		
		$fuel_average["datetime"] = date("Y-m-d H:i:s");
		$fuel_average["fuel_avg"] = $price;
		//db_insert_fuel_average($fuel_average);
		
		echo date("Y-m-d H:i:s")."<br>";
		//echo $price;
		
	}
	
	function test_chron()
	{
		$where = null;
		$where["id"] = 1;
		
		$user = db_select_user($where);
		
		echo $user["username"];
	}

	//CHRON FOR GETTING ZONAR TRUCK DATA
	function add_geopoint_data()
	{
		date_default_timezone_set('America/Denver');
		
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
									"Cookie: foo=bar\r\n",
				'user_agent'=>    $_SERVER['HTTP_USER_AGENT'] 
			)
		);

		$context = stream_context_create($opts);

		$xml = file_get_contents('http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showposition&operation=current&format=xml&version=2&logvers=3.3', false, $context);

		$parsed_xml = simplexml_load_string($xml);

		foreach($parsed_xml->children() as $child)
		{
			$time = intval ($child->time);
			$datetime = date("Y-m-d H:i:s",$time);

			$meters = intval($child->odometer);
			$miles = round($meters * .000621371);
			
			//GET TRUCK ID
			$where = null;
			$where["truck_number"] = $child->attributes()->fleet;
			$truck = db_select_truck($where);

			$asset['truck_id'] = $truck["id"];
			$asset['datetime'] = $datetime;
			$asset['latitude'] = $child->lat;
			$asset['longitude'] = $child->long;
			$asset['heading'] = $child->heading;
			$asset['speed'] = $child->speed;
			$asset['power'] = "$child->power";
			$asset['odometer'] = $miles;

			$where = null;
			$where['truck_id'] = $asset['truck_id'];
			$where['datetime'] = $asset['datetime'];

			$duplicate_truck = db_select_geopoint($where);

			if(empty($duplicate_truck))
			{
				db_insert_geopoint($asset);
				
				$gp = $asset;
				if($gp["speed"] == 0)
				{
					//CREATE LOG_ENTRY
					$log_entry = null;
					$log_entry["truck_id"] = $gp["truck_id"];
					$log_entry["entry_type"] = "Geopoint Stop";
					$log_entry["entry_datetime"] = $gp["datetime"];
					$log_entry["address"] = $gp["latitude"].", ".$gp["longitude"];
					$log_entry["gps_coordinates"] = $gp["latitude"].", ".$gp["longitude"];
					$log_entry["odometer"] = $gp["odometer"];
					$log_entry["entry_notes"] = "Zonar - ".$gp["speed"]." MPH Power".$gp["power"];
					$log_entry["route"] = "http://maps.google.com/maps?q=".urlencode($gp["latitude"].",".$gp["longitude"]);
					
					//$geocode_data = reverse_geocode($log_entry["address"]);
					
					//$log_entry["city"] = $geocode_data["city"];
					//$log_entry["state"] = $geocode_data["state"];
					
					db_insert_log_entry($log_entry);
				}
				else
				{
					//CREATE LOG_ENTRY
					$log_entry = null;
					$log_entry["truck_id"] = $gp["truck_id"];
					$log_entry["entry_type"] = "Geopoint";
					$log_entry["entry_datetime"] = $gp["datetime"];
					$log_entry["address"] = $gp["latitude"].", ".$gp["longitude"];
					$log_entry["gps_coordinates"] = $gp["latitude"].", ".$gp["longitude"];
					$log_entry["odometer"] = $gp["odometer"];
					$log_entry["entry_notes"] = "Zonar - ".$gp["speed"]." MPH Power".$gp["power"];
					$log_entry["route"] = "http://maps.google.com/maps?q=".urlencode($gp["latitude"].",".$gp["longitude"]);
					
					//$geocode_data = reverse_geocode($log_entry["address"]);
					
					//$log_entry["city"] = $geocode_data["city"];
					//$log_entry["state"] = $geocode_data["state"];
					
					db_insert_log_entry($log_entry);
				}
				$readable_datetime = date("F j, Y, g:i a",$time);
				
				$truck_number = $truck['truck_number'];
				$speed = $asset['speed'];
				$odometer = $asset['odometer'];
				$power = $asset['power'];
				$latitude = $asset['latitude'];
				$longitude = $asset['longitude'];
				
				$message = "Truck $truck_number is going $speed MPH as of $readable_datetime.\nThe odometer is at $odometer.\nThe power is $power.\nThe coordinates are $latitude, $longitude.";
				$channel = "notifications";

				send_slack_message($message,$channel="notifications");
			}
			
		}
		
		echo "Success! ".date('m/d/y H:i:s');
	}

	
	//CHRON JOB FOR SENDING OUT HOLD REPORTS TO DRIVERS
	function send_hold_emails()
	{
		date_default_timezone_set('America/Denver');
		
		//GET ALL ACTIVE DRIVERS
		$where = null;
		$where["client_status"] = "Active";
		$clients = db_select_clients($where);
		
		$i = 0;
		foreach($clients as $client)
		{
			//echo $client["client_nickname"]."<br>";
			//GET HOLD REPORT FOR CLIENT
			$hold_report = get_hold_report($client["id"]);
			
			if($hold_report["hold_status"] == "Hold")
			{
				//$i++;
				//if($i < 2)
				//{
					//SEND EMAIL
					$email_data = null;
					$email_data["hold_report"] = $hold_report;
					$message = $this->load->view('emails/hold_report_email',$email_data, TRUE);
					//$message = "test";
					$to = $client["company"]["person"]["email"];
					//$to = 'covax13@gmail.com';
					$subject = 'Driver Hold Report '.date("m/d/y H:i")." | ".$client["client_nickname"];
					// //$headers = "From: paperwork.dispatch@gmail.com\r\n";
					// $headers = "From: fleetsmarts@fleetsmarts.net\r\n";
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
					//echo $this->email->print_debugger();
					echo "Emails sent to ".$to." ".date("m/d/y H:i")."<br>";
					
					//FOR TESTING SHOW EMAIL OUTPUT
					//$this->load->view('emails/hold_report_email',$email_data);
					
				//}
				
			}
			
		}
	}
	
	function old_trippak()
	{
		$this->load->library('ftp');
		$config['hostname'] = 'ftp.integratedlogicsticssolutions.co';
		$config['username'] = 'covax13';
		$config['password'] = 'Retret13!';
		$config['debug']    = TRUE;

		$this->ftp->connect($config);

		$list = $this->ftp->list_files('/trippak');
		
		$zip_list = [];
		
//		print_r($list);
//		echo "<br>";
		foreach($list as $filename)
		{
			if ($filename != "." && $filename != ".." && strtolower(substr($filename, strrpos($filename, '.') + 1)) == 'zip')
			{
				$zip_list[] = $filename;
			}
		}
		
		print_r($zip_list);
//		echo "<br>";
		
		
		foreach($zip_list as $zip_file)
		{
			$new_files = [];
			$new_file_names = [];
			
			$path = '/home/covax13/trippak/' . $zip_file;
//			echo $path . "<br>";
			$zip = new ZipArchive;

			if ($zip->open($path) === true) 
			{
				
				for($i = 0; $i < $zip->numFiles; $i++) 
				{
					$filename = $zip->getNameIndex($i);
					
//					echo "Filename: " . $filename . "<br>";
					
					$fileinfo = pathinfo($filename);
					
//					print_r($fileinfo);
//					echo "<br>";
					
					copy("zip://".$path."#".$filename, "/home/covax13/trippak_pics/".$fileinfo['basename']);
					
					$new_files[] = "/home/covax13/trippak_pics/".$fileinfo['basename'];
					$new_file_names[] = $fileinfo['basename'];
				}
				
				$zip->close();
			}else{
//				echo "false <br>";
				print_r($zip->open($path));
			}
//			echo "New Files: ";
//			print_r($new_files);
			
			
			$path = '/home/covax13/trippak_pics/BatchInfo.xml';

			$xml = file_get_contents($path);

			$data = new SimpleXMLElement($xml);

			$trip_number = $data->Fields->TripNumber->Value;
			$truck_number = $data->Fields->TruckNumber->Value;

			$i = 1;
			foreach($new_files as $new_file){
				
				$ext = pathinfo($new_file, PATHINFO_EXTENSION);
				
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
				$name = basename($new_file);
				if($ext == 'tif'){
					$type = "application/tif";
				}else if($ext == 'pdf'){
					$type = "application/pdf";
				}else if($ext == 'xml'){
					$type = "application/xml";
				}

				$file_guid = get_random_string(5);

				$secure_file = null;
				$secure_file['name'] = $name;
				$secure_file['type'] = $type;
				$secure_file['title'] = $name;
				$secure_file['category'] = "Trippak";
				$secure_file['file_guid'] = $file_guid;
				$secure_file['server_path'] = '/trippak_pics/';
				$secure_file['office_permission'] = 'All';
				$secure_file['driver_permission'] = 'None';
				db_insert_secure_file($secure_file);
				
				//GET NEWLY INSERTED SECURE FILE
				$where = null;
				$where["file_guid"] = $file_guid;
				$where["name"] = $name;
				$new_secure_file = db_select_secure_file($where);
				
				//UPDATE GUID WITH ID APPENDED TO BEGINNING
				$update_file = null;
				$update_file["file_guid"] = $new_secure_file["id"].$file_guid;
				
				
				if($ext == 'tif'){
					$update_file["name"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif";
					$update_file["title"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif";
				}else if($ext == 'pdf'){
					$update_file["name"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf";
					$update_file["title"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf";
				}else if($ext == 'xml'){
					$update_file["name"] = "(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml";
					$update_file["title"] = "(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml";
				}
				
				$where = null;
				$where["id"] = $new_secure_file["id"];
				db_update_secure_file($update_file,$where);

				$trippak = null;
				$trippak['load_number'] = $trip_number;
				$trippak['truck_number'] = $truck_number;
				db_insert_trippak($trippak);

				$trippak_object = db_select_trippak($trippak);
				$trippak_id = $trippak_object['id'];

				//CREATE ATTACHMENT IN DB
				$attachment = null;
				$attachment["type"] = "trippak";
				$attachment["attached_to_id"] = $trippak_id;
				$attachment["file_guid"] = $new_secure_file["id"].$file_guid;
				
				if($ext == 'tif'){
					$attachment["attachment_name"] = "Scan " . $i;
				}else if($ext == 'pdf'){
					$attachment["attachment_name"] = "Scan " . $i;
				}else if($ext == 'xml'){
					$attachment["attachment_name"] = "XML";
				}

				db_insert_attachment($attachment);
				
				if($ext == 'tif'){
					rename($new_file, "/home/covax13/trippak_pics/(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif");
				}else if($ext == 'pdf'){
					rename($new_file, "/home/covax13/trippak_pics/(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf");
				}else if($ext == 'xml'){
					rename($new_file, "/home/covax13/trippak_pics/(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml");
				}
				
				$i++;
			}

			rename ("/home/covax13/trippak/" . $zip_file, "/home/covax13/trippak_pics/old_zips/" . $zip_file);
//			unlink("/home/covax13/trippak/" . $zip_file);
		}
		
		$this->ftp->close();
		echo "Success!";
	}
	
	//CHRON - CHECK THE DIRECTORY, UNZIPS THE FILES, STORE FILES TRIPPAK_PICS FOLDER, CREATES SECURE FILES, CREATES TRIPPAK AND TRIPPAK ATTACHEMENTS
	function trippak()
	{
		
		date_default_timezone_set('US/Mountain');
		
		$this->load->library('ftp');
		$config['hostname'] = 'ftp.integratedlogicsticssolutions.co';
		$config['username'] = 'covax13';
		$config['password'] = 'Retret13!';
		$config['debug']    = TRUE;

		$this->ftp->connect($config);

		$list = $this->ftp->list_files('/trippak');

		$zip_list = [];
		
//		print_r($list);
//		echo "<br>";
		foreach($list as $filename)
		{
			if ($filename != "." && $filename != ".." && strtolower(substr($filename, strrpos($filename, '.') + 1)) == 'zip')
			{
				$zip_list[] = $filename;
			}
		}
		
//		print_r($zip_list);
//		echo "<br>";
		
		foreach($zip_list as $zip_file)
		{
			$new_files = [];
			$new_file_names = [];
			
			$path = '/home/covax13/trippak/' . $zip_file;
			$new_path = '/trippak_pics/';
//			echo $path . "<br>";
			
			$dir = $new_path . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . basename($zip_file, '.zip') . "_" . date('mdhis') . "/";
			echo "DIRECTORY: " . $dir . "<br>";
			
			$zip = new ZipArchive;
			
			if ($zip->open($path) === true) 
			{
				
				$year_directories = $this->ftp->list_files('/trippak_pics');
				echo "Year directories of /trippak_pics:<br>";
				print_r($year_directories);
				echo "<br>";
				if(!in_array(date('Y'),$year_directories))
				{
					echo "no " . date('Y') . " dir<br>";
					echo "new dir: " . $new_path . date('Y') . "/";
					$this->ftp->mkdir($new_path . date('Y') . "/", DIR_WRITE_MODE);
					echo $new_path . date('Y') . "/" . date('n') . "-" . date('Y') . "/";
					$this->ftp->mkdir($new_path . date('Y') . "/" . date('n') . "-" . date('Y') . "/", DIR_WRITE_MODE);
					echo $dir . "...". "<br>";
					$this->ftp->mkdir($dir, DIR_WRITE_MODE);
					echo "Created directory: " . $dir . "<br>";
				}
				else
				{
					echo date('Y') . " dir exists<br>";
					$month_directories = $this->ftp->list_files($new_path."/".date('Y'));
					print_r($month_directories);
					echo "<br>";
					if(!in_array(date('n') . "-" . date('Y'),$month_directories))
					{
						echo "no " . date('n') . " dir<br>";
						echo $new_path . date('Y') . "/" . date('n') . "-" . date('Y') . "/ ...";
						$this->ftp->mkdir($new_path . date('Y') . "/" . date('n') . "-" . date('Y') . "/", DIR_WRITE_MODE);
						echo $dir . "...". "<br>";
						$this->ftp->mkdir($dir, DIR_WRITE_MODE);
						echo "Created directory: " . $dir . "<br>";
					}
					else
					{
						echo date('n') . " dir exists<br>";
						echo '/home/covax13/trippak_pics/' . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . date('mdhis') . "/ ...<br>";
						$this->ftp->mkdir($dir, DIR_WRITE_MODE);
						echo "Created directory: " . '/home/covax13/trippak_pics/' . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . date('mdhis') . "/<br>";
					}
				}
				$zip->extractTo('/home/covax13/trippak_pics/' . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . basename($zip_file,'.zip') . "_" . date('mdhis') . "/");
				
				$files = $this->ftp->list_files('/trippak_pics/' . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . basename($zip_file,'.zip') . "_" . date('mdhis') . "/");
				echo "File path: " . '/trippak_pics/' . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . basename($zip_file,'.zip') . "_" . date('mdhis') . "/";
				print_r($files);
				
				$file_list = array();
				foreach($files as $filename)
				{
					if ($filename != "." && $filename != "..")
					{
						$file_list[] = $filename;
					}
				}
				echo "File List: <br>";
				print_r($file_list);
				
				echo "<br>";
				$batch_path = '/home/covax13/trippak_pics/' . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . basename($zip_file,'.zip') . "_" . date('mdhis') . "/" . 'BatchInfo.xml';
				
				sleep(2);
				
				do{
					$xml = file_get_contents($batch_path);
				}while($xml === false);
				
				if($xml !== false)
				{
					$data = new SimpleXMLElement($xml);

					$location_id = $data->LocationID;
					$store_number = $data->StoreNumber;
					$start_datetime = $data->StartDateTime;
					$completion_datetime = $data->CompletionDateTime;
					$trip_number = $data->Fields->TripNumber->Value;
					$truck_number = $data->Fields->TruckNumber->Value;

					$trippak = null;
					$trippak['truck_number'] = $truck_number;
					$trippak['scan_datetime'] = date('Y-m-d H:i:s');
					db_insert_trippak($trippak);

					$trippak_object = db_select_trippak($trippak);
					$trippak_id = $trippak_object['id'];

					echo "Trippak ID: " . $trippak_id;

					$file_guids = array();
					$i = 1;
					foreach($file_list as $this_file)
					{
						echo "File: " . $this_file . "<br>";
						$ext = pathinfo($dir.$this_file, PATHINFO_EXTENSION);
						echo "Extension: " . $ext . "<br>";
						//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
						$name = basename($this_file);
						if($ext == 'tif'){
							$type = "application/tif";
						}else if($ext == 'pdf'){
							$type = "application/pdf";
						}else if($ext == 'xml'){
							$type = "application/xml";
						}

						$file_guid = get_random_string(5);

						$secure_file = null;
						$secure_file['name'] = $name;
						$secure_file['type'] = $type;
						$secure_file['title'] = $name;
						$secure_file['category'] = "Trippak";
						$secure_file['file_guid'] = $file_guid;
						$secure_file['server_path'] = $dir;
						$secure_file['office_permission'] = 'All';
						$secure_file['driver_permission'] = 'None';
						db_insert_secure_file($secure_file);

	//					GET NEWLY INSERTED SECURE FILE
						$where = null;
						$where["file_guid"] = $file_guid;
						$where["name"] = $name;
						$new_secure_file = db_select_secure_file($where);

						//UPDATE GUID WITH ID APPENDED TO BEGINNING
						$update_file = null;
						$update_file["file_guid"] = $new_secure_file["id"].$file_guid;

						$file_guid = $new_secure_file["id"].$file_guid;

						if($ext == 'tif')
						{
							$update_file["name"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif";
							$update_file["title"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif";

							rename('/home/covax13' . $dir.$this_file, '/home/covax13' . $dir . "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif");

							$attachment["attachment_name"] = "Scan ";
						}
						else if($ext == 'pdf')
						{
							$update_file["name"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf";
							$update_file["title"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf";

							rename('/home/covax13' . $dir.$this_file, '/home/covax13' . $dir . "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf");

							$attachment["attachment_name"] = "Scan ";
						}
						else if($ext == 'xml')
						{
							$update_file["name"] = "(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml";
							$update_file["title"] = "(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml";

							rename('/home/covax13' . $dir.$this_file, '/home/covax13' . $dir . "(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml");

							$attachment["attachment_name"] = "XML";
						}

						$where = null;
						$where["id"] = $new_secure_file["id"];
						db_update_secure_file($update_file,$where);

						if($ext == 'tif')
						{

							$zip_file_paths[$file_guid] = $dir . "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif";
						}
						else if($ext == 'pdf')
						{

							$zip_file_paths[$file_guid] = $dir . "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf";
						}
						else if($ext == 'xml')
						{

							$zip_file_paths[$file_guid] = $dir . "(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml";
						}

						//CREATE ATTACHMENT IN DB
						$attachment = null;
						$attachment["type"] = "trippak";
						$attachment["attached_to_id"] = $trippak_id;
						$attachment["file_guid"] = $file_guid;

						db_insert_attachment($attachment);

						$file_guids[$name] = $file_guid;

						$i += 1;
					}

					$this->load->library('email');

					$context['truck_number'] = $trippak_object['truck_number'];
					$context['load_id'] = $trippak_object['load_id'];
					$context['scan_datetime'] = $trippak_object['scan_datetime'];
					$context['file_guids'] = $file_guids;
					echo "Data:<br>";
					print_r($context);
					$message = $this->load->view('emails/trippak_email',$context, TRUE);

					echo "start datetime: " . $trippak_object['scan_datetime'];
					echo "trip number: " . $trippak_object['load_id'];
					echo "truck number: " . $trippak_object['truck_number'];

					$this->email->from('fleetsmarts@integratedlogicsticssolutions.co', 'Fleetsmarts');
					$this->email->to('paperwork.dispatch@gmail.com, philip.dispatch@gmail.com');
					//$this->email->cc('fleetsmarts@integratedlogicsticssolutions.co');

					$this->email->subject('Trippak sent at ' . date('F j, Y, g:i a',strtotime($trippak_object['scan_datetime'])) . 	" - Truck number " . $trippak_object['truck_number'] . ", Load number " . $trippak_object['load_id']);
					$this->email->message($message);

					$this->email->send();

					echo "ci email sent ".date("m/d/y H:i");

					echo $this->email->print_debugger();
				}
				
			}
			$this->ftp->move('/trippak/' . $zip_file,'/trippak_pics/old_zips/' . $zip_file);
			
			
		}
		
		
		$this->ftp->close();
		echo "Success!";
	}

	//CHRON (5 MINUTES) - CHECKS WHO IS LOGGED IN REMOTELY - CREATES CLOCK_IN_VERIFICATIONS AT RANDOM AND SENDS EMAILS
	function send_verification_email()
	{
			
		date_default_timezone_set('America/Denver');
		$datetime = strtotime(date('Y-m-d H:i:s'));
		
		$where = null;
		$where["permission_name"] = "can clock in remotely";
		$clock_in_permission = db_select_permission($where);
		
		$where = null;
		$where["permission_id"] = $clock_in_permission['id'];
		$clock_in_user_permissions = db_select_user_permissions($where);
		
		$user_ids = array();
			
		foreach($clock_in_user_permissions as $clock_in_user_permission)
		{
			$user_ids[] = $clock_in_user_permission['user_id'];
		}
		
		$logged_in_users_email = array();
		foreach($user_ids as $user_id)
		{
			echo "User ID: " . $user_id . "<br>";
			$where = null;
			$where["user_id"] = $user_id;
			$last_punch = db_select_time_punch($where,"datetime");

			if($last_punch['in_out'] == "In")
			{
				echo "in<br>";
				$where = null;
				$where['id'] = $user_id;
				$user = db_select_user($where);
				
				$where = null;
				$where['id'] = $user['person_id'];
				$person = db_select_person($where);

				$email = $person['email'];
				
				echo "Email: " . $email . "<br>";
				
				$logged_in_users_email[] = $email;
				
				$where = null;
				$where["user_id"] = $user_id;
				$last_email_sent = db_select_clock_in_verification($where,"email_sent_datetime");

				$screenshot_datetime = strtotime($last_email_sent['screenshot_uploaded_datetime']);

				$email_datetime = strtotime($last_email_sent['email_sent_datetime']);
				
				$email_difference = round(abs($datetime - $email_datetime) / 60,2);
				
				echo "Now: " . $datetime . "<br>";
				echo "Last Email: " . $email_datetime . "<br>";
				echo "Last Verified: " . $screenshot_datetime . "<br>";
				echo "Email Difference: " . $email_difference . "<br>";
				
				if($last_email_sent['screenshot_uploaded_datetime'] != NULL)
				{
					$verification_difference = round(abs($datetime - $screenshot_datetime) / 60,2);
					echo "Verification Difference: " . $verification_difference . "<br>";
					
					$random = rand(0,100);
					echo "Random: " . $random . "<br>";
					if($verification_difference <= 10 && $verification_difference > 5)//5 MINUTES
					{
						if($random <= 5)//5% CHANCE
						{
							$clock_in_id = add_clock_in_verification($user_id);
							echo "logical clock in id: " . $clock_in_id . "<br>";
							send_clock_in_verification_email($email,$clock_in_id,$user_id);
						}
					}
					else if($verification_difference <= 20)//20 MINUTES
					{
						if($random <= 10)//10% CHANCE
						{
							$clock_in_id = add_clock_in_verification($user_id);
							send_clock_in_verification_email($email,$clock_in_id,$user_id);
						}
					}
					else if($verification_difference <= 30)//30 MINUTES
					{
						if($random <= 10)//10% CHANCE
						{
							$clock_in_id = add_clock_in_verification($user_id);
							send_clock_in_verification_email($email,$clock_in_id,$user_id);
						}
					}
					else if($verification_difference <= 40)//40 MINUTES
					{
						if($random <= 25)//25% CHANCE
						{
							$clock_in_id = add_clock_in_verification($user_id);
							send_clock_in_verification_email($email,$clock_in_id,$user_id);
						}
					}
					else if($verification_difference <= 50)//50 MINUTES
					{
						if($random <= 30)//30% CHANCE
						{
							$clock_in_id = add_clock_in_verification($user_id);
							send_clock_in_verification_email($email,$clock_in_id,$user_id);
						}
					}
					else
					{
						$clock_in_id = add_clock_in_verification($user_id);
						echo "logical clock in id: " . $clock_in_id . "<br>";
						send_clock_in_verification_email($email,$clock_in_id,$user_id);
					}
				}
				else if($last_email_sent['screenshot_uploaded_datetime'] == NULL)
				{
					if($email_difference > 10)//GREATER THAN 10 MINUTES AGO
					{
						//LOG EM OUT
//						$this->session->sess_destroy();
//						redirect(base_url("index.php/home"));
//						clock_out($user_id);
						echo "Logged out <br>";
					}
				}
			}
		}
		
//		print_r($logged_in_users_email);
		
	}

	//CHRON TO GET IBRIGHT DATA FROM IBRIGHT API
	function get_ibright_data()
	{
		date_default_timezone_set('America/Denver');
		
		$api_key = '84da4e57-d779-4853-a773-add3ce369e85';

		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
							"Cookie: foo=bar\r\n" . 
							"Content-Type: application/json",
				'user_agent'=>    $_SERVER['HTTP_USER_AGENT'] 
			)
		);

		$context = stream_context_create($opts);

		$json_string = file_get_contents("https://api.ibright.info/v2/asset?apikey=$api_key", false, $context);
//		echo "JSON string: " . $json_string . "<br>";
		$json_assets = json_decode($json_string,TRUE);
//		echo "JSON assets: ";
//		print_r($json_assets);
//		echo "<br>";
//		print_r($json_assets[0]);
		foreach ($json_assets as $key => $output) {
			echo "asset: " . $output['Name'] . "<br>";
			$reefer_info_string = file_get_contents("https://api.ibright.info/v2/rawdata/latest?AssetNameFilter=" . $output['Name'] . "&LogNameFilter=reeferInfo&apikey=$api_key", false, $context);
			
			$reefer_info_json = json_decode($reefer_info_string,TRUE);
			
			if(array_key_exists('ID',$output)){
				echo "id key exists: " . $output["ID"] . "<br>";
				$ibright_id = $output['ID'];
			}else{
				echo "no id key exists<br>";
				$ibright_id = '';
			}
			if(array_key_exists('Name',$output)){
				echo "name key exists: " . $output["Name"] . "<br>";
				$trailer_number = $output['Name'];
			}else{
				echo "no name key exists<br>";
				$trailer_number = '';
			}
			
			if(array_key_exists('0',$reefer_info_json)){
				if(array_key_exists('overallstatus',$reefer_info_json['0']['Data'])){
//					echo "status key exists<br>";
					$status = $reefer_info_json['0']['Data']['overallstatus'];
				}else{
					$status = '';
//					echo "no status key exists<br>";
				}
				if(array_key_exists('fuellevel',$reefer_info_json['0']['Data'])){
					$fuel_level = $reefer_info_json['0']['Data']['fuellevel'];
				}else{
					$fuel_level = '';
				}
				if(array_key_exists('voltage',$reefer_info_json['0']['Data']['system'])){
					$battery_voltage = $reefer_info_json['0']['Data']['system']['voltage'];
				}else{
					$battery_voltage = '';
				}
				if(array_key_exists('latitude',$reefer_info_json['0']['Data'])){
					$latitude = $reefer_info_json['0']['Data']['latitude'];
				}else{
					$latitude = '';
				}
				if(array_key_exists('longitude',$reefer_info_json['0']['Data'])){
					$longitude = $reefer_info_json['0']['Data']['longitude'];
				}else{
					$longitude = '';
				}
				if(array_key_exists('WhenOccurred',$reefer_info_json['0'])){
					$datetime_occurred = date('Y-m-d H:i:s',strtotime($reefer_info_json['0']['WhenOccurred']));
				}else{
					$datetime_occurred = '';
				}
			}else{
				$status = '';
				$fuel_level = '';
				$battery_voltage = '';
				$latitude = '';
				$longitude = '';
				$datetime_occurred = '';
			}
			
			$datetime_added = date('Y-m-d H:i:s');
//			
			echo "<b>ID:</b> " . $ibright_id . "<br>";
			echo "<b>Name:</b> " . $trailer_number . "<br>";
			echo "<b>Status:</b> " . $status . "<br>";
			echo "<b>Fuel level:</b> " . $fuel_level . "%<br>";
			echo "<b>When occured:</b> " . $datetime_occurred . "<br>";
			echo "<b>Battery voltage:</b> " . $battery_voltage . "<br>";
			echo "<b>Latitude:</b> " . $latitude . "<br>";
			echo "<b>Longitude:</b> " . $longitude . "<br><br>";
			
			
			$temperature_info_string = file_get_contents("https://api.ibright.info/v2/rawdata/latest?AssetNameFilter=" . $output['Name'] . "&LogNameFilter=temperature1&apikey=$api_key", false, $context);
			
			$temperature_info_json = json_decode($temperature_info_string,TRUE);
			
			if(array_key_exists('0',$temperature_info_json)){
				echo "0 key exists<br>";
				if(array_key_exists('set',$temperature_info_json['0']['Data'])){
					if($temperature_info_json['0']['Data']['aat'] !=''){
						$set_temperature = $temperature_info_json['0']['Data']['set'] * (9/5) + 32;
					}else{
						$set_temperature = '';
					}
				}else{
					$set_temperature = '';
				}
				if(array_key_exists('temp',$temperature_info_json['0']['Data'])){
					if($temperature_info_json['0']['Data']['aat'] !=''){
						$return_temperature = $temperature_info_json['0']['Data']['temp'] * (9/5) + 32;
					}else{
						$return_temperature = '';
					}
				}else{
					$return_temperature = '';
				}
				if(array_key_exists('sat',$temperature_info_json['0']['Data'])){
					if($temperature_info_json['0']['Data']['aat'] !=''){
						$supply_temperature = $temperature_info_json['0']['Data']['sat'] * (9/5) + 32;
					}else{
						$supply_temperature = '';
					}
				}else{
					$supply_temperature = '';
				}
				if(array_key_exists('aat',$temperature_info_json['0']['Data'])){
					if($temperature_info_json['0']['Data']['aat'] !=''){
						$ambient_temperature = $temperature_info_json['0']['Data']['aat'] * (9/5) + 32;
					}else{
						$ambient_temperature = '';
					}
				}else{
					$ambient_temperature = '';
				}
			}else{
				echo "0 key does not exist<br>";
				$set_temperature = '';
				$return_temperature = '';
				$supply_temperature = '';
				$ambient_temperature = '';
			}
			
			echo "<b>Set temperature:</b> " . $set_temperature . "<br>";
			echo "<b>Return temperature:</b> " . $return_temperature . "<br>";
			echo "<b>Supply temperature:</b> " . $supply_temperature . "<br>";
			echo "<b>Ambient temperature:</b> " . $ambient_temperature . "<br><br>";
			
			$trailer_geopoint = null;
			$trailer_geopoint['ibright_id'] = $ibright_id;
			$trailer_geopoint['trailer_number'] = $trailer_number;
			$trailer_geopoint['status'] = $status;
			$trailer_geopoint['fuel_level'] = $fuel_level;
			$trailer_geopoint['battery_voltage'] = $battery_voltage;
			$trailer_geopoint['latitude'] = $latitude;
			$trailer_geopoint['longitude'] = $longitude;
			$trailer_geopoint['set_temperature'] = $set_temperature;
			$trailer_geopoint['return_temperature'] = $return_temperature;
			$trailer_geopoint['supply_temperature'] = $supply_temperature;
			$trailer_geopoint['ambient_temperature'] = $ambient_temperature;
			$trailer_geopoint['datetime_added'] = $datetime_added;
			$trailer_geopoint['datetime_occurred'] = $datetime_occurred;
			db_insert_trailer_geopoint($trailer_geopoint);
			
			$readable_datetime_occurred = date('F j, Y, g:i a',strtotime($datetime_occurred)); 
			
			$message = "Trailer $trailer_number is $status as of $readable_datetime_occurred.\nThe fuel level is at $fuel_level%.\nThe battery voltage is at $battery_voltage.\nThe coordinates are $latitude, $longitude.\nSet temperature: $set_temperature;\nReturn temperature: $return_temperature;\nSupply temperature: $supply_temperature;\nAmbient temperature: $ambient_temperature.";
			$channel = "notifications";
			
			send_slack_message($message,$channel="notifications");
//			print_r($reefer_info_json);
//			echo "<br><br>";
//			print_r($temperature_info_json);
		}
//			foreach($json_string->arrayofasset as $asset)
//			{
//				echo "ASSET: " . $asset.id;
//				
//			}
		echo "Success!";
	}
	
	
//	function test_slack_function(){
//		$message = "Hi! I'm FleetBot Prime. I'll be notifying you about stuff you need to know. Remember: I'm always watching.";
//		$channel = "@garrett";
//		$this->send_slack_message($message,$channel);
//	}
	
	//****************** TRYING TO DOWNLOAD FILES FROM CURRUPTED SERVER ********************
	function return_secure_file_list()
	{
		//GET ALL SERCURE FILES
		$where = null;
		$where = " id < 22514 AND id > 16000";
		$secure_files = db_select_secure_files($where);
		
		echo "success";
		//$print_r($secure_files);
		//echo json_encode($secure_files);
	}
	
	
	function download_file()
	{
		date_default_timezone_set('America/Denver');
		set_time_limit(0);
		
		// connect and login to FTP server
		$ftp_server = "ftp.fleetsmarts.net";
		$ftp_username = "covax13";
		$ftp_userpass = "retret13";

		// $this->load->library('ftp');
		// $config['hostname'] = $ftp_server;
		// $config['username'] = $ftp_username;
		// $config['password'] = $ftp_userpass;
		// $config['debug']    = TRUE;

		// $this->ftp->connect($config);

		// $list = $this->ftp->list_files('/edocuments/production');
		
		// print_r($list);
		// echo "<br>";
		// foreach($list as $filename)
		// {
			// echo $filename."<br>";
		// }
		
		$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
		$login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);
		
		//GET ALL SERCURE FILES
		$where = null;
		$where = " id < 8001 AND type IS NOT NULL";
		$secure_files = db_select_secure_files($where,"id DESC");
		
		// $json = file_get_contents('http://fleetsmarts.net/index.php/public_functions/return_secure_file_list');
		// $secure_files = json_decode($json);
		
		$attempts = 0;
		$successes = 0;
		echo date('m/d/y H:i:s')." starting download of ".count($secure_files)." files<br>";
		
		foreach($secure_files as $secure_file)
		{
			$attempts++;
			$server_file = $secure_file["server_path"].$secure_file["name"];
			$local_file = "D:/production 8000 - 0/".$secure_file["name"];

			//download server file
			if (ftp_get($ftp_conn, $local_file, $server_file, FTP_BINARY))
			{
				echo date('m/d/y H:i:s')." ".$secure_file["name"];
				$successes++;
			}
			else
			{
				echo "Error downloading $server_file.";
			}
			echo "<br>";
			
			//break;
		}
		
		echo "$successes of $attempts were successful!";


		// close connection
		ftp_close($ftp_conn);
	}
	
	function fix_file_path()
	{
		$CI =& get_instance();
	
		//SET UP CONNECTION TO FTP SERVER
		$CI->load->library('ftp');
		$config['hostname'] = 'ftp.integratedlogicsticssolutions.co';
		$config['username'] = 'covax13';
		$config['password'] = 'Retret13!';
		$config['debug']	= TRUE;
		$config['debug']	= TRUE;
		
		$CI->ftp->connect($config);
		$server_path = '/edocuments/production/2015/';
		$files = $CI->ftp->list_files($server_path);
		
		foreach($files as $file)
		{
			//FIND SECURE FILE IN DB
			$where = null;
			$where["name"] = $file;
			$secure_file = db_select_secure_file($where);
			if(!empty($secure_file))
			{
				echo $file;
				//UPDATE SECURE FILE WITH NEW SERVER PATH
				$update = null;
				$update["server_path"] = $server_path;
				$where = null;
				$where["id"] = $secure_file["id"];
				db_update_secure_file($update,$where);
				
				echo " ******************************** UPDATED ";
				echo "<br>";
			}
			else
			{
				echo " - NOT FOUND";
				echo "<br>";
			}
		}
	}
	
	
	
	//************** ANDROID APP API'S *****************************
	
	//USERNAME AND PASSWORD LOGIN - RETURNS SUCCESS/FAIL, PIN, SECURITY TOKEN
	function app_authenticate_login($username, $password)
	{
		$is_valid = true;
		
		if(empty($username))
		{
			$is_valid = false;
		}
		
		if(empty($password))
		{
			$is_valid = false;
		}
		
		//IF USERNAME AND PASSWORD ARE GIVEN
		if($is_valid)
		{
			$username = urldecode($username);
			$password = urldecode($password);
			
			//GET USER
			$where = null;
			$where["username"] = $username;
			$user = db_select_user($where);
			
			if(empty($user))
			{
				$is_valid = false;
			}
			
			//IF USER IS FOUND
			if($is_valid)
			{
				if($password != $user["password"])
				{
					$is_valid = false;
				}
				
				if($user["person"]["role"] != "Client")
				{
					$is_valid = false;
				}
				
				//IF PASSWORD IS A MATCH AND USER IS A CLIENT
				if($is_valid)
				{
					//SET NEW TOKEN
					$fleetsmarts_session_token = get_random_string(10);
					
					$update_user = null;
					$update_user["fleetsmarts_session_token"] = $fleetsmarts_session_token;
					
					$where = null;
					$where["id"] = $user["id"];
					db_update_user($update_user,$where);
					
					
				}
			}
			
		}
		
		//IF VALID AFTER ALL THE VALIDATION CHECKS - PASS BACK SUCCESS, PIN, AND TOKEN
		if($is_valid)
		{
			//GET USER
			$where = null;
			$where["username"] = $username;
			$user = db_select_user($where);
					
			$return_array = array();
			
			$return_array["status"] = "Success";
			$return_array["pin"] = $user["pin"];
			$return_array["token"] = $user["fleetsmarts_session_token"];
			
			echo json_encode($return_array);
		}
		else
		{
			$return_array = array();
			
			$return_array["status"] = "Fail";
			$return_array["pin"] = null;
			$return_array["token"] = null;
			
			echo json_encode($return_array);
		}
		
	}
}