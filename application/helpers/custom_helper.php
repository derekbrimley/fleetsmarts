<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	//FIND ERROR LOG AT xampp/php/logs/php_error_log
	//EXAMPLE:
	//error_log("text about error: | LINE ".__LINE__." ".__FILE__);
 
	function print_to_log($text)
	{
		$backtrace = debug_backtrace();
	
		$from_file = $backtrace[0]["file"];
		$line = $backtrace[0]["line"];
		
		//THIS IS THE FILE.... FOUND AT 
		$file = 'custom_message_log.txt';
		
		$message = date('m/d/y H:i:s')." ".$from_file." ".$line." - ".$text."\r\n";
		
		//WRITE THE CONTENTS TO THE FILE
		file_put_contents($file,$message , FILE_APPEND);
		
		//echo print_r($backtrace);
		//echo " - Success";
	}
		
	function create_daily_log($driver_id, $log_date)
	{
		$CI =& get_instance();
		//Find Week ID
		$sql = "SELECT * FROM week
				WHERE '".$log_date."' >= week_start
				AND '".$log_date."' <= week_end";
		$query_find_week = $CI->db->query($sql);
		
		foreach ($query_find_week->result() as $row)
		{
			$week_id = $row->ID;
		}
		
		//search for payroll where week_id = week_id and driver_id = driver_id
		$sql = "SELECT * FROM payroll WHERE week_id = ? AND driver_id = ?";
		$query_payroll = $CI->db->query($sql,array($week_id,$driver_id));
		$doesnt_exist = true;
		foreach($query_payroll->result() as $row)
		{
			$payroll_id = $row->ID;
			$doesnt_exist = false;
		}
		//if found, ID = payroll_id
		//if not found, find pay_rate from people where ID = driver_id
		if($doesnt_exist)
		{
			$where['ID'] = $driver_id;
			$driver = db_select_people($where);
			
			//insert payroll with driver_id,week_id,pay_rate,0,0,0
			$values = array($driver_id,$week_id,$driver['pay_rate'],0,0,0);
			db_insert_payroll($values);
			
			$payroll_where['driver_id'] = $driver_id;
			$payroll_where['week_id'] = $week_id;
			$payroll = db_select_payroll($payroll_where);
			$payroll_id = $payroll['id'];
			
		}
		
		$values = array($driver_id,$payroll_id,$log_date,$week_id);
		db_insert_daily_log($values);
	}
	
	function add_days($start_date,$days_to_add)
	{
		$newdate = strtotime ( $days_to_add." day" , strtotime ( $start_date ) ) ;
		$newdate = date ( 'Y-m-j' , $newdate );
 
		return $newdate;	

	}
	
	function calc_base_pay($payroll_id)
	{
		$trips_where['payroll_id'] = $payroll_id;
		$trips = db_select_trips($trips_where);
		
		$base_pay = array();
		foreach($trips as $trip)
		{
			$base_pay[] = $trip['base_pay'];
		}
		
		$base_pay_total = array_sum($base_pay);
		
		//Set new $base_pay_total in the database
		$set['total_base_pay'] = $base_pay_total;
		$payroll_where['ID'] = $payroll_id;
		db_update_payroll($set,$payroll_where);
		
	}
	
	function calc_weekly_pay($payroll_id)
	{
		
		$payroll_where['ID'] = $payroll_id;
		$payroll = db_select_payroll($payroll_where);
		
		$total_base_pay = 0;
		$total_adjustments =0;
		if(empty($payroll['total_base_pay']))
		{
			$total_base_pay = 0;
		}else
			{
				$total_base_pay = $payroll['total_base_pay'];
			}
		if(empty($payroll['total_adjustments']))
		{
			$total_adjustments =0;
		}else
			{
				$total_adjustments =$payroll['total_adjustments'];
			}
		
		$weekly_pay = $total_base_pay + $total_adjustments;
		
		$payroll_set['weekly_pay'] = $weekly_pay;
		db_update_payroll($payroll_set,$payroll_where);
	}
	
	function calc_total_adjustments($payroll_id)
	{
		$CI =& get_instance();
		$sql = "SELECT * FROM adjustments,daily_log,payroll
				WHERE adjustments.daily_log_id = daily_log.ID
				AND daily_log.payroll_id = payroll.Id
				AND payroll_id = ?";
		$query_payrolls = $CI->db->query($sql,array($payroll_id));
		
		$total_adj[] = 0;
		foreach($query_payrolls->result() as $row)
		{
			$total_adj[] = $row->amount;
		}
		
		$total = array_sum($total_adj);
		
		$payroll_where['ID'] = $payroll_id;
		$payroll_set['total_adjustments'] = $total;
		db_update_payroll($payroll_set,$payroll_where);
		
	}//end calc total adjustments
	
	function calc_pay_history_balance($payee_id)
	{
		$CI =& get_instance();
		$credit_balance = 0;
		$debit_balance = 0;
		$damage_balance = 0;
		
		$query_balance_totals = $CI->db->query("SELECT * FROM pay_history WHERE payee_id = '".$payee_id."'");
		//$i = 0;
		foreach ($query_balance_totals->result() as $row)
		{
			//echo $i."<br>";
			//$i = $i + 1;
			if ($row->debit_credit == 'credit')
			{
				$plus_amount = $row->debit_credit_amount;
				$credit_balance = ($credit_balance + $plus_amount);
				
			}else if ($row->debit_credit == 'debit')
			{
				$plus_amount = $row->debit_credit_amount;
				$debit_balance = ($debit_balance + $plus_amount);
			}
			
			$plus_damage = $row->damage_amount;
			$damage_balance = ($damage_balance + $plus_damage);
		}
		
		$total_balance = $credit_balance - $debit_balance;
		
		
		$sql = "UPDATE people SET account_balance = '".$total_balance."', damage_reserve = '".$damage_balance."' WHERE ID = '".$payee_id."'";
		$CI->db->query($sql);
	}//end calc_pay_history_balance()
	
	function clean_for_js_param($string)
	{
		$string = nl2br(str_replace("'","|.|",$string));
		
		$clean_string = "";
		for ($i = 0; $i <= strlen($string); $i++)
		{
			$clean_string = $clean_string.substr($string,$i,1);
			if(substr($string,$i,1) == ">")
			{
				$i = $i + 1;
			}
		}
		return $clean_string;
	}
	
	function delete_this_pick_drop($pd_number)
	{
		$pd_where['pd_number'] = $pd_number;
		$this_pick_drop = db_select_pick_drop($pd_where);
		
		db_delete_pick_drop($this_pick_drop['id']);
	}
	
	function make_sms_email_address($db_number,$db_carrier)
	{
		$sms_phone_num = preg_replace('/\D/', '', $db_number);
		if (strlen($sms_phone_num) != 10)
		{
			return "invalid";
		}
		
		
		if ($db_carrier == 'Sprint')
		{
			$sms_email_ext = "@messaging.sprintpcs.com";
			
		}else if ($db_carrier == 'T-mobile')
		{
			$sms_email_ext = "@tmomail.net";
			
		}else if ($db_carrier == 'Virgin Mobile')
		{
			$sms_email_ext = "@vmobl.com";
			
		}else if ($db_carrier == 'Cingular')
		{
			$sms_email_ext = "@cingularme.com";
			
		}else if ($db_carrier == 'Verizon')
		{
			$sms_email_ext = "@vtext.com";
			
		}else if ($db_carrier == 'Nextel')
		{
			$sms_email_ext = "@messaging.nextel.com";
		}else if ($db_carrier == 'AT&T')
		{
			$sms_email_ext = "@txt.att.net";
		}else if ($db_carrier == 'Boost')
		{
			$sms_email_ext = "@myboostmobile.com";
		}else if ($db_carrier == 'Cricket')
		{
			$sms_email_ext = "@sms.mycricket.com";
		}else
		{
			return "invalid";
		}
		
		return $sms_phone_num.$sms_email_ext;
		
	}//END MAKE SMS EMAIL ADDRESS
	
	//FORMAT TIME FOR DATABASE WITH OPTIONAL TIMEZONE OFFSET FROM UTAH TIME
	function make_db_time($hour,$minute,$ampm,$timezone = 0)
	{
		if($hour != '--' && $minute != '--')
		{
			if($ampm == 'pm')
			{
				$hour = $hour + 12;
				if($hour == 24)
				{
					$hour = 12;
				}
			}
			else if($ampm == 'am')
			{
				if($hour == 12)
				{
					$hour = 0;
				}
			}
			

			$hour = $hour + $timezone;
			return "$hour:$minute:00";
		}
		else
		{
			return null;
		}
		
	}//END MAKE DB TIME
	
	//RUNS ON EVERY PAGE LOAD FOR THE LOAD TAB
	function update_status_for_all_loads()
	{
		//SET TIMEZONE
		date_default_timezone_set('US/Mountain');
		
		//CHECK ALL RATE CON PENDING LOADS TO SEE IF THEY ARE OVERDUE
		$rate_con_pending_loads_where = "status_number = 4";
		$rate_con_pending_loads = db_select_loads($rate_con_pending_loads_where);
		foreach($rate_con_pending_loads as $load)
		{
			//echo "load ".$load["load_number"]."<br>";
			//echo "time of booking ".(strtotime($load["time_of_booking"]))."<br>";
			//echo "deadline ".(time()-(25 * 60))."<br>";
			
			//IF TIME OF BOOKING IS MORE THAN 25 MINUTES AGO
			if (strtotime($load["time_of_booking"]) < (time()-( 25 * 60)))
			{
				$set_load["status"] = "Rate Con Overdue";
				$set_load["status_number"] = '1';
				$set_load_where["ID"] = $load["id"];
				db_update_load($set_load,$set_load_where);
			}
		}
		
		//CHECK ALL PICK PENDING/OVERDUE LOADS TO SEE IF THEY HAVE BEEN PICKED
		$pick_pending_overdue_loads_where = "status_number = 2 OR status_number = 5 ";
		$pick_pending_overdue_loads = db_select_loads($pick_pending_overdue_loads_where);
		foreach($pick_pending_overdue_loads as $load)
		{
			//GET ALL PICKS FOR LOAD
			$picks_where["load_number"] = $load["load_number"];
			$picks_where["type"] = "Pick";
			$these_picks = db_select_pick_drops($picks_where);
			
			$is_picked = true;
			
			foreach ($these_picks as $pick)
			{
				//IF FOR ANY PICK THE IN AND OUT TIMES HAVE NOT BEEN REPORTED, STAY PENDING/OVERDUE
				if(empty($pick["in_time"]) || empty($pick["out_time"]) )
				{
					$is_picked = false;
					break;
				}
			}
			
			if($is_picked)
			{
				$set_load["status"] = "Drop Pending";
				$set_load["status_number"] = '6';
				$set_load_where["ID"] = $load["id"];
				db_update_load($set_load,$set_load_where);
			}
			
		}
		
		
		//CHECK ALL DROP PENDING/OVERDUE LOADS TO SEE IF THEY HAVBE BEEN DROPPED
		$drop_pending_overdue_loads_where = "status_number = 3 OR status_number = 6 ";
		$drop_pending_overdue_loads = db_select_loads($drop_pending_overdue_loads_where);
		foreach($drop_pending_overdue_loads as $load)
		{
			//GET ALL DROPS FOR LOAD
			$drops_where["load_number"] = $load["load_number"];
			$drops_where["type"] = "Drop";
			$these_drops = db_select_pick_drops($drops_where);
			
			$is_dropped = false;
			
			foreach ($these_drops as $drop)
			{
				//IF FOR ANY DROP THE IN AND OUT TIMES HAVE NOT BEEN REPORTED, STAY PENDING/OVERDUE
				if(empty($drop["in_time"]) || empty($drop["out_time"]) )
				{
					$is_dropped = false;
					break;
				}
				else
				{
					$is_dropped = true;
				}
			}
			
			if ($is_dropped)
			{
				$set_load["status"] = "Dropped";
				$set_load["status_number"] = '7';
				$set_load_where["ID"] = $load["id"];
				db_update_load($set_load,$set_load_where);
			}
			
		}
		
		//CHECK ALL PICK PENDING LOADS TO SEE IF THEY ARE OVERDUE
		$pick_pending_loads_where = "status_number = 5";
		$pick_pending_loads = db_select_loads($pick_pending_loads_where);
		foreach($pick_pending_loads as $load)
		{
			//GET ALL PICKS FOR LOAD
			$picks_where["load_number"] = $load["load_number"];
			$picks_where["type"] = "Pick";
			$these_picks = db_select_pick_drops($picks_where);
			
			$is_overdue = false;
			
			foreach($these_picks as $pick)
			{
				//echo $pick["pd_number"]."<br>";
				//echo "NOW = ".time()."<br>";
				//echo "App = ".(strtotime($pick["date"]." ".$pick["appointment_time_utah"])+(7*60*60))."<br>";
				//IF ANY PICK IS LATE, MARK PICK OVERDUE
				if (time() > (strtotime($pick["date"]." ".$pick["appointment_time_utah"])))
				{
					$is_overdue = true;
					break;
				}
			}
			
			if($is_overdue)
			{
				$set_load["status"] = "Pick Overdue";
				$set_load["status_number"] = '2';
				$set_load_where["ID"] = $load["id"];
				db_update_load($set_load,$set_load_where);
			}
		}
		
		
		//CHECK ALL DROP PENDING LOADS TO SEE IF THEY ARE OVERDUE
		$drop_pending_loads_where = "status_number = 6";
		$drop_pending_loads = db_select_loads($drop_pending_loads_where);
		foreach($drop_pending_loads as $load)
		{
			//GET ALL DROPS FOR LOAD
			$drops_where["load_number"] = $load["load_number"];
			$drops_where["type"] = "Drop";
			$these_drops = db_select_pick_drops($drops_where);
			
			$is_overdue = false;
			
			foreach($these_drops as $drop)
			{
				//IF ANY DROP IS LATE, MARK drop OVERDUE
				if (time() > (strtotime($drop["date"]." ".$drop["appointment_time_utah"])))
				{
					$is_overdue = true;
					break;
				}
			}
			
			if($is_overdue)
			{
				$set_load["status"] = "Drop Overdue";
				$set_load["status_number"] = '3';
				$set_load_where["ID"] = $load["id"];
				db_update_load($set_load,$set_load_where);
			}
		}
		
		
		
	}//END UPDATE STATUS FOR ALL LOADS
	
	//UPDATE A LOAD'S FIRST-PICK AND LAST-DROP DATE FIELDS
	function update_first_pick_last_drop($load_number)
	{
		$first_pick = null;
		$last_drop = null;
	
		$picks_where["load_number"] = $load_number;
		$picks_where["type"] = "Pick";
		$picks = db_select_pick_drops($picks_where);
		
		//GET FIRST PICK DATE
		foreach ($picks as $pick)
		{
			if($first_pick == null)
			{
				$first_pick = $pick["date"];
			}
			else if ($pick["date"] < $first_pick)
			{
				$first_pick = $pick["date"];
			}
		}
		
		$drops_where["load_number"] = $load_number;
		$drops_where["type"] = "Drop";
		$drops = db_select_pick_drops($drops_where);
		
		//GET LAST DROP DATE
		foreach ($drops as $drop)
		{
			if($last_drop == null)
			{
				$last_drop = $drop["date"];
			}
			else if ($drop["date"] > $last_drop)
			{
				$last_drop = $drop["date"];
			}
		}
		
		$load["first_pick_date"] = $first_pick;
		$load["final_drop_date"] = $last_drop;
		$load_where["load_number"] = $load_number;
		db_update_load($load,$load_where);
	}
	
	function my_urlencode($string)
	{
		$string = urlencode($string);
		$url_characters = array('%2F','+');
		$customer_characters = array('_backslash','_space');
		$string = str_replace($url_characters,$customer_characters,$string);
		return $string;
	}
	
	function my_urldecode($string)
	{
		//CODEIGNITER URL DECODES
		$url_characters = array('%2F','+');
		$customer_characters = array('_backslash','_space');
		$string = str_replace($customer_characters,$url_characters,$string);
		$string = urldecode($string);
		return $string;
	}
	
	function get_random_string($length, $valid_chars = "abcdefghijklmnopqrstuvwxyz0123456789")
	{
		// start with an empty random string
		$random_string = "";

		// count the number of chars in the valid chars string so we know how many choices we have
		$num_valid_chars = strlen($valid_chars);

		// repeat the steps until we've created a string of the right length
		for ($i = 0; $i < $length; $i++)
		{
			// pick a random number from 1 up to the number of valid chars
			$random_pick = mt_rand(1, $num_valid_chars);

			// take the random character out of the string of valid chars
			// subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
			$random_char = $valid_chars[$random_pick-1];

			// add the randomly-chosen char onto the end of our string so far
			$random_string .= $random_char;
		}

		// return our finished random string
		return $random_string;
	}
	
	function calc_mpg($stop_id)
	{
		//GET STOP
		$stop_where["id"] = $stop_id;
		$stop = db_select_stop($stop_where);
		
		//CALCULATE MPG FOR EACH FUEL - FILL
		$fuel_mpg = array();
		//FOREACH FUEL-FILL
		if ($stop["stop_type"] == "Fuel - Fill")
		{
			$fuel_stop_where["stop_id"] = $stop_id;
			$fuel_stop = db_select_fuel_stop($fuel_stop_where);
		
			//FIND THE PREVIOUS FUEL-FILL DATETIME
			$sql = "SELECT max(stop_datetime) as this_datetime
					FROM `stop`,`fuel_stop` 		
					WHERE stop.id = fuel_stop.stop_id	
					AND company_id = ?
					AND stop_datetime < ?		
					AND stop_type = 'Fuel - Fill'";
			$CI =& get_instance();
			$query_for_datetime = $CI->db->query($sql,array($stop["company_id"],$stop["stop_datetime"]));
			foreach ($query_for_datetime->result() as $row)
			{
				$previous_fill_datetime = $row->this_datetime;
			}
			
			if(!empty($previous_fill_datetime))
			{
				//GET PREVIOUS FUEL - FILL STOP FROM DATETIME
				$previous_fuel_fill_stop_where["stop_datetime"] = $previous_fill_datetime;
				$previous_fuel_fill_stop = db_select_stop($previous_fuel_fill_stop_where);
				
				//GET PREVIOUS FUEL_STOP FILL FROM STOP ID
				$fuel_stop_where["stop_id"] = $previous_fuel_fill_stop["id"];
				$previous_fuel_fill = db_select_fuel_stop($fuel_stop_where);
				
				//SUBTRACT THE ODOMETERS
				$miles = $stop["odometer"] - $previous_fuel_fill["stop"]["odometer"];
				
				//SUM UP THE TOTAL GALLONS OF ALL FUEL STOPS BETWEEN
				$total_gallons = 0;
				$previous_fill_odometer = null;
				$sql = "SELECT sum(gallons) AS gallons FROM stop,`fuel_stop` 
						WHERE stop.id = fuel_stop.stop_id
						AND company_id = ?
						AND stop_datetime > ? 
						AND stop_datetime < ?";
						
				$CI =& get_instance();
				$query_for_gallons = $CI->db->query($sql,array($stop["company_id"],$previous_fuel_fill["stop"]["stop_datetime"],$stop["stop_datetime"]));
				foreach ($query_for_gallons->result() as $row)
				{
					$total_gallons = $row->gallons + $fuel_stop["gallons"];
				}
				
				//DIVIDE MILES BY GALLONS
				@$fuel_mpg = round($miles/$total_gallons,2);
				
				return $fuel_mpg;
			}
			else
			{
				return 0;
			}
		}
	}//end calc_mpg()
	
	function get_fuel_allocations($stop_id)
	{
		//GET FILL TO FILL STOPS
		
		//GET TOTAL FILL TO FILL MILES
		
		//GET SUM OF INVOICES FOR FILL TO FILL
		
		//FOR EACH DISTINCT LOAD# CREATE FUEL_ALLOCATION
		
		//FOREACH FUEL_ALLOCATION
			//GET MILES ALLOCATED FOR FUEL_ALLOCATION
				//last_stop_for_first_load[odometer] - previous_fill[odometer]
		
		//CREATE A PERCENTAGE ALLOCATED FOR EACH FUEL_ALLOCATION
		
		//CREATED FUEL ALLOCATED FOR EACH FUEL_ALLOCATION
		
	}
	
	function update_billing_status($load_id)
	{
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		if(!empty($load["invoice_closed_datetime"]))
		{
			$update_load["billing_status"] = "Closed";
			$update_load["billing_status_number"] = 8;
			
			db_update_load($update_load,$where);
		}
		else if(empty($load["digital_received_datetime"]))
		{
			$update_load["billing_status"] = "Pending Digital Copy";
			$update_load["billing_status_number"] = 1;
			
			db_update_load($update_load,$where);
		}
		else if(empty($load["billing_datetime"]))
		{
			$update_load["billing_status"] = "Pending Billing";
			$update_load["billing_status_number"] = 2;
			
			db_update_load($update_load,$where);
		}
		else if(empty($load["invoice_number"]))
		{
			$update_load["billing_status"] = "Pending Funding";
			$update_load["billing_status_number"] = 3;
			
			db_update_load($update_load,$where);
		}
		else if(empty($load["hc_processed_datetime"]))
		{
			$update_load["billing_status"] = "Pending HC Processing";
			$update_load["billing_status_number"] = 4;
			
			db_update_load($update_load,$where);
		}
		else if(empty($load["hc_sent_datetime"]))
		{
			$update_load["billing_status"] = "Pending HC Sent";
			$update_load["billing_status_number"] = 5;
			
			db_update_load($update_load,$where);
		}
		else if(empty($load["hc_received_datetime"]))
		{
			$update_load["billing_status"] = "Pending HC Received";
			$update_load["billing_status_number"] = 6;
			
			db_update_load($update_load,$where);
		}
		else if(empty($load["invoice_closed_datetime"]))
		{
			$update_load["billing_status"] = "Pending Invoice Closed";
			$update_load["billing_status_number"] = 7;
			
			db_update_load($update_load,$where);
		}
		
		
	}
	
	//GETS THE FIRST PICK OF A GIVEN LOAD
	function get_first_pick($load_id)
	{
		$CI =& get_instance();
		
		$values[] = $load_id;
		
		$sql = "SELECT MIN(stop_datetime) as minDate FROM stop,pick
		WHERE stop.id = pick.stop_id
		AND load_id = ?";
		
		$query= $CI->db->query($sql,$values);
		
		$minDate = "";
		foreach ($query->result() as $row)
		{
			$minDate = $row->minDate;
		}
		
		return $minDate;
	}
	
	//CREATE DEFAULT CLIENT ACCOUNTS FOR GIVEN COMPANY_ID - AND COOP MEMBER RELATIONSHIP
	function create_default_accounts($client_id)
	{
		//GET CLIENT
		$where = null;
		$where["id"] = $client_id;
		$client = db_select_client($where);
		
		$driver_company_id = $client["company_id"];
		
		//GET ALL ACCOUNTS FOR THIS COMPANY
		$where = null;
		$where["company_id"] = $driver_company_id;
		$these_accounts = db_select_accounts($where);
		
		if(!empty($these_accounts))
		{
			foreach($these_accounts as $account)
			{
				//DELETE EVERY ACCOUNT ENTRY FOR THIS ACCOUNT
				$where = null;
				$where["account_id"] = $account["id"];
				db_delete_account_entry($where);
			}
		}
		
		//DELETE PREVIOUS ACCOUNTS
		$where = null;
		$where["company_id"] = $driver_company_id;
		db_delete_account($where);
		
		//DELETE PREVIOUS DEFAULT ACCOUNTS
		$where = null;
		$where["company_id"] = $driver_company_id;
		db_delete_default_account($where);
		
		
		
		//GET DRIVER COMPANY
		$where = null;
		$where["id"] = $driver_company_id;
		$driver_company = db_select_company($where);
		
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		//CREATE MEMBER RELATIONSHIP WITH THE COOP
		$insert = null;
		$insert["business_id"] = $coop_company["id"];
		$insert["relationship"] = "Member";
		$insert["related_business_id"] = $driver_company_id;
		db_insert_business_relationship($insert);
		
		
		//GET LEASING COMPANY
		$where = null;
		$where["category"] = "Leasing";
		$leasing_company = db_select_company($where);
		
		//GET RELATIONSHIP BETWEEN LEASING COMPANY AND COOP
		$where = null;
		$where["business_id"] = $leasing_company["id"];
		$where["related_business_id"] = $coop_company["id"];
		$leasing_coop_relationship = db_select_business_relationship($where);
		
		//GET RELATIONSHIP BETWEEN COOP AND DRIVER COMPANY
		$where = null;
		$where["business_id"] = $coop_company["id"];
		$where["related_business_id"] = $driver_company_id;
		$coop_member_relationship = db_select_business_relationship($where);
		
		//************************FUEL PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON FUEL PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Fuel Payments";
		$coop_default_fuel_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON DIRECT LEASE PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_fuel_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Fuel Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Fuel Payments";
		db_insert_default_account($default_acc);
		
		//************************INSURANCE PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON FUEL PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Insurance Payments";
		$coop_default_insurance_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON DIRECT LEASE PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_insurance_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Insurance Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Insurance Payments";
		db_insert_default_account($default_acc);
		
		//************************DIRECT LEASE PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON DIRECT LEASE PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Direct Lease Payments";
		$coop_default_direct_lease_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON DIRECT LEASE PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_direct_lease_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Direct Lease Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		
		//SET ACCOUNT AS DEFAULT DIRECT LEASE PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Lease Payments";
		db_insert_default_account($default_acc);
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO DIRECT LEASE ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Direct Lease on Member Invoices";
		$coop_default_direct_lease_payment_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO DIRECT LEASE ON MEMBER INVOICES
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "Member Invoices";
		$account["parent_account_id"] = $coop_default_direct_lease_payment_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/P to Direct Lease - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT DIRECT LEASE PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P to Lease Vendor";
		db_insert_default_account($default_acc);
		
		
		
		//************************LOBOS PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON DIRECT LEASE PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Lobos Payments";
		$coop_default_lobos_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON DIRECT LEASE PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_lobos_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Lobos Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		
		//SET ACCOUNT AS DEFAULT DIRECT LEASE PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Driver Services Payments";
		db_insert_default_account($default_acc);
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO DIRECT LEASE ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Lobos on Member Invoices";
		$coop_default_lobos_payment_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO DIRECT LEASE ON MEMBER INVOICES
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "Member Invoices";
		$account["parent_account_id"] = $coop_default_lobos_payment_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/P to Lobos - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT DIRECT LEASE PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P to Driver Services Vendor";
		db_insert_default_account($default_acc);
		
		
		//************************ARROWHEAD PAYMENTS************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON ARROWHEAD PAYMENTS
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Arrowhead Payments";
		$coop_default_arrowhead_payment_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Member Exp Payments";
		$account["parent_account_id"] = $coop_default_arrowhead_payment_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Arrowhead Payments - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		
		//SET ACCOUNT AS DEFAULT ARROWHEAD PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Dispatch Payments";
		db_insert_default_account($default_acc);
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO ARROWHEAD ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Arrowhead on Member Invoices";
		$coop_default_arrowhead_payment_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO ARROWHEAD ON MEMBER INVOICES
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "Member Invoices";
		$account["parent_account_id"] = $coop_default_arrowhead_payment_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/P to Arrowhead - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT ARROWHEAD PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P to Dispatch Vendor";
		db_insert_default_account($default_acc);
		
		
		//************************FLEETPROTECT************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON FLEETPROTECT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on FleetProtect";
		$coop_default_fleetProtect_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "FleetProtect";
		$account["parent_account_id"] = $coop_default_fleetProtect_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on FleetProtect - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		
		//SET ACCOUNT AS DEFAULT ARROWHEAD PAYMENT ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on FleetProtect";
		db_insert_default_account($default_acc);
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO ARROWHEAD ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Members on Settlements";
		$coop_default_settlements_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO MEMBERS ON SETTLEMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "Settlements Payable";
		$account["parent_account_id"] = $coop_default_settlements_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "Settlements Payable - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT SETTLEMENTS PAYABLE ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P to Member on Settlements";
		db_insert_default_account($default_acc);
		
		
		
		//************************ARROWHEAD FLEETPROTECT GUARANTEE************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM ARROWHEAD ON FLEETPROTECT DEPOSIT
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Arrowhead on FleetProtect Deposit";
		$coop_default_fleetProtect_deposit_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "FleetProtect Deposit Receivable";
		$account["parent_account_id"] = $coop_default_fleetProtect_deposit_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on FleetProtect Deposit - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT A/R ON ARROWHEAD FLEETPROTECT DEPOSIT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on FleetProtect Deposit";
		db_insert_default_account($default_acc);
		
		
		//GET COOP'S DEFAULT A/P ACCOUNT TO ARROWHEAD ON MEMBER INVOICES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/P to Arrowhead on FleetProtect Deposit";
		$coop_default_fleetProtect_ap_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/P TO MEMBERS ON SETTLEMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Liability";
		$account["category"] = "FleetProtect Deposit Payable";
		$account["parent_account_id"] = $coop_default_fleetProtect_ap_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/P on FleetProtect Deposit - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT SETTLEMENTS PAYABLE ACCOUNT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/P on FleetProtect Deposit";
		db_insert_default_account($default_acc);
		
		
		
		//************************COOP MEMBERSHIP DUES AND QUICK PAY ************************
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON MEMBERSHIP DUES
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Membership Dues";
		$coop_default_membership_dues_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Membership Dues Receivable";
		$account["parent_account_id"] = $coop_default_membership_dues_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Membership Dues - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT A/R ON ARROWHEAD FLEETPROTECT DEPOSIT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Membership Dues";
		db_insert_default_account($default_acc);
		
		
		//GET COOP'S DEFAULT ACCOUNT FOR A/R FROM MEMBERS ON QUICK PAY
		$where = null;
		$where["company_id"] = $coop_company["id"];
		$where["category"] = "A/R from Members on Quick Pay";
		$coop_default_quick_pay_ar_acc = db_select_default_account($where);
		
		//CREATE MEMBER SUB-ACCOUNT FOR COOP A/R ON ARROWHEAD PAYMENTS
		$account = null;
		$account["company_id"] = $coop_company["id"];
		$account["relationship_id"] = $coop_member_relationship["id"];
		$account["account_type"] = "Member";
		$account["account_class"] = "Asset";
		$account["category"] = "Quick Pay Receivables";
		$account["parent_account_id"] = $coop_default_quick_pay_ar_acc["account_id"];
		$account["account_status"] = "Open";
		$account["account_name"] = "A/R on Quick Pay - ".$driver_company["company_side_bar_name"];
		db_insert_account($account);
		
		//GET NEWLY CREATED ACCOUNT
		$newly_created_account = db_select_account($account);
		
		//SET ACCOUNT AS DEFAULT A/R ON ARROWHEAD FLEETPROTECT DEPOSIT
		$default_acc = null;
		$default_acc["company_id"] = $driver_company_id;
		$default_acc["account_id"] = $newly_created_account["id"];
		$default_acc["type"] = "Member";
		$default_acc["category"] = "Coop A/R on Quick Pay";
		db_insert_default_account($default_acc);
		
		
		
		
		
		
		
		
		
		
		/**
		//CREATE PAY ACCOUNT
		$account = null;
		$account['company_id'] = $company_id;
		$account['account_type'] = "Client";
		$account['category'] = "Pay";
		$account['account_status'] = "Active";
		$account['account_name'] = "Pay";
		db_insert_account($account);
		
		//CREATE DAMAGE ACCOUNT
		$account = null;
		$account['company_id'] = $company_id;
		$account['account_type'] = "Client";
		$account['category'] = "Client Damage";
		$account['account_status'] = "Active";
		$account['account_name'] = "Damage";
		db_insert_account($account);
		
		//CREATE BAHA ACCOUNT
		$baha_account = null;
		$baha_account["company_id"] = $company_id;
		$baha_account["account_type"] = "Client";
		$baha_account["category"] = "BAHA";
		$baha_account["account_status"] = "Active";
		$baha_account["account_name"] = "BA Holding Account";
		//db_insert_account($baha_account);
		
		//CREATE RESERVE ACCOUNT
		$account = null;
		$account['company_id'] = $company_id;
		$account['account_type'] = "Client";
		$account['category'] = "Reserve";
		$account['account_status'] = "Active";
		$account['account_name'] = "Reserve";
		//db_insert_account($account);
		
		//CREATE DRIVER EQUIPMENT ACCOUNT
		$de_account = null;
		$de_account["company_id"] = $client["company_id"];
		$de_account["account_type"] = "Client";
		$de_account["category"] = "Driver Equipment";
		$de_account["account_status"] = "Active";
		$de_account["account_name"] = "Driver Equipment";
		db_insert_account($de_account);
		**/
		
		
		/**
		//CREATE TRUCK ACCOUNT
		$account = null;
		$account['company_id'] = $company_id;
		$account['account_type'] = "Client";
		$account['category'] = "Bill";
		$account['account_status'] = "Active";
		$account['account_name'] = "Truck";
		//db_insert_account($account);
		
		//CREATE TRAILER ACCOUNT
		$account = null;
		$account['company_id'] = $company_id;
		$account['account_type'] = "Client";
		$account['category'] = "Bill";
		$account['account_status'] = "Active";
		$account['account_name'] = "Trailer";
		//db_insert_account($account);
		
		//CREATE INSURANCE ACCOUNT
		$account = null;
		$account['company_id'] = $company_id;
		$account['account_type'] = "Client";
		$account['category'] = "Bill";
		$account['account_status'] = "Active";
		$account['account_name'] = "Insurance";
		//db_insert_account($account);
		
		//CREATE FUEL ACCOUNT
		$account = null;
		$account['company_id'] = $company_id;
		$account['account_type'] = "Client";
		$account['account_status'] = "Active";
		$account['account_name'] = "Fuel";
		//db_insert_account($account);
		
		//CREATE ABC FACTORING ACCOUNT
		$account = null;
		$account['company_id'] = $company_id;
		$account['account_type'] = "Client";
		$account['account_status'] = "Active";
		$account['account_name'] = "ABC Factoring";
		//db_insert_account($account);
		**/
		
		/**
		//UPDATE CLIENT WITH MAIN_PAY_ACCOUNT
		$where = null;
		$where["company_id"] = $company_id;
		$where['category'] = "Pay";
		$main_account = db_select_account($where);
		
		$where = null;
		$where["id"] = $client["id"];
		$update_client["main_account"] = $main_account["id"];
		db_update_client($update_client,$where);
		
		
		//DELETE PREVIOUS CLIENT FEE SETTINGS
		$setting_where = null;
		$setting_where["client_id"] = $client_id;
		db_delete_client_fee_setting($setting_where);
		//echo $client_id;
		
		//PROVIDE DEFAULT CLIENT FEE SETTINGS
		$where = null;
		$where["category"] = "Client_Expense";
		$where["account_name"] = "Factoring";
		$account = db_select_account($where);
		$factoring_setting["account_id"] = $account["id"];
		$factoring_setting["client_id"] = $client["id"];
		$factoring_setting["fee_description"] = "Factoring";
		$factoring_setting["fee_type"] = "Map Mile";
		$factoring_setting["fee_amount"] = .04;
		$client_fee_settings[] = $factoring_setting;
		
		$where = null;
		$where["category"] = "Client_Expense";
		$where["account_name"] = "Bad Debt";
		$account = db_select_account($where);
		$bad_debt_setting["account_id"] = $account["id"];
		$bad_debt_setting["client_id"] = $client["id"];
		$bad_debt_setting["fee_description"] = "Bad Debt";
		$bad_debt_setting["fee_type"] = "Map Mile";
		$bad_debt_setting["fee_amount"] = .03;
		$client_fee_settings[] = $bad_debt_setting;
		
		$where = null;
		$where["company_id"] = $company_id;
		$where['account_name'] = "Fuel";
		$account = db_select_account($where);
		$fuel_setting["account_id"] = $account["id"];
		$fuel_setting["client_id"] = $client["id"];
		$fuel_setting["fee_description"] = "Fuel";
		$fuel_setting["fee_type"] = "Fuel Allocation";
		$fuel_setting["fee_amount"] = 0;
		$client_fee_settings[] = $fuel_setting;
		
		$where = null;
		$where["company_id"] = $company_id;
		$where['account_name'] = "Damage";
		$account = db_select_account($where);
		$damage_setting["account_id"] = $account["id"];
		$damage_setting["client_id"] = $client["id"];
		$damage_setting["fee_description"] = "Damage";
		$damage_setting["fee_type"] = "Map Mile";
		$damage_setting["fee_amount"] = 0;
		$client_fee_settings[] = $damage_setting;
		
		$where = null;
		$where["company_id"] = $company_id;
		$where['account_name'] = "Truck";
		$account = db_select_account($where);
		$truck_mileage_setting["account_id"] = $account["id"];
		$truck_mileage_setting["client_id"] = $client["id"];
		$truck_mileage_setting["fee_description"] = "Truck Mileage";
		$truck_mileage_setting["fee_type"] = "Odometer Mile";
		$truck_mileage_setting["fee_amount"] = .09;
		$client_fee_settings[] = $truck_mileage_setting;
		
		$truck_lease_setting["account_id"] = $account["id"];
		$truck_lease_setting["client_id"] = $client["id"];
		$truck_lease_setting["fee_description"] = "Truck Lease";
		$truck_lease_setting["fee_type"] = "Week";
		$truck_lease_setting["fee_amount"] = 600;
		$client_fee_settings[] = $truck_lease_setting;
		
		$where = null;
		$where["company_id"] = $company_id;
		$where['account_name'] = "Trailer";
		$account = db_select_account($where);
		$trailer_mileage_setting["account_id"] = $account["id"];
		$trailer_mileage_setting["client_id"] = $client["id"];
		$trailer_mileage_setting["fee_description"] = "Trailer Mileage";
		$trailer_mileage_setting["fee_type"] = "Odometer Mile";
		$trailer_mileage_setting["fee_amount"] = .03;
		$client_fee_settings[] = $trailer_mileage_setting;
		
		$trailer_lease_setting["account_id"] = $account["id"];
		$trailer_lease_setting["client_id"] = $client["id"];
		$trailer_lease_setting["fee_description"] = "Trailer Lease";
		$trailer_lease_setting["fee_type"] = "Week";
		$trailer_lease_setting["fee_amount"] = 60.40;
		$client_fee_settings[] = $trailer_lease_setting;
		
		$where = null;
		$where["company_id"] = $company_id;
		$where['account_name'] = "Insurance";
		$account = db_select_account($where);
		$insurance_setting["account_id"] = $account["id"];
		$insurance_setting["client_id"] = $client["id"];
		$insurance_setting["fee_description"] = "Insurance";
		$insurance_setting["fee_type"] = "Month";
		$insurance_setting["fee_amount"] = 1250;
		$client_fee_settings[] = $insurance_setting;
		
		foreach($client_fee_settings as $setting)
		{
			db_insert_client_fee_setting($setting);
		}
		**/
		
	}
	
	//CREATE LOAN ACCOUNT FOR GIVEN DRIVER AND FM
	function create_client_loan_account($driver_company_id,$owner_company_id)
	{
		//GET OWNER COMPANY
		$where = null;
		$where["id"] = $owner_company_id;
		$owner = db_select_company($where);
	
		//CREATE RESERVE ACCOUNT
		$account = null;
		$account['company_id'] = $driver_company_id;
		$account['account_type'] = "Client";
		$account['category'] = "Loan Account";
		$account['vendor_id'] = $owner["id"];
		$account['account_status'] = "Active";
		$account['account_name'] = "Loan Account With ".$owner["company_side_bar_name"];
		db_insert_account($account);
	}
	
	//CREATE DEFAULT ACCOUNTS FOR FLEET MANAGERS
	function create_default_fm_accounts($fm_company_id)
	{
		//GET FM COMPANY
		$where = null;
		$where["id"] = $fm_company_id;
		$fm_company = db_select_company($where);
		
		//GET FM PERSON
		$where = null;
		$where["id"] = $fm_company["person_id"];
		$fm_person = db_select_person($where);
	
		//DELETE PREVIOUS ACCOUNTS
		$where = null;
		$where["company_id"] = $fm_company_id;
		//db_delete_account($where);
		
		//CREATE PAY ACCOUNT
		$account = null;
		$account['company_id'] = $fm_company_id;
		$account['account_type'] = "Fleet Manager";
		$account['category'] = "Pay";
		$account['account_status'] = "Active";
		$account['account_name'] = "FM Pay (".$fm_person["f_name"].")";
		//db_insert_account($account);
		
		//CREATE FM RESERVE ACCOUNT
		$fm_reserve_account["company_id"] = $fm_company_id;
		$fm_reserve_account["account_type"] = "Fleet Manager";
		$fm_reserve_account["category"] = "Reserve";
		$fm_reserve_account["account_status"] = "Active";
		$fm_reserve_account["account_name"] = "FM Reserve (".$fm_person["f_name"].")";
		db_insert_account($fm_reserve_account);
		
		//CREATE PROFIT ACCOUNT
		$account = null;
		$account['company_id'] = $fm_company_id;
		$account['account_type'] = "Fleet Manager";
		$account['category'] = "Profit";
		$account['account_status'] = "Active";
		$account['account_name'] = "FM Profit (".$fm_person["f_name"]." ".$fm_person["l_name"].")";
		db_insert_account($account);
		
		//CREATE INVOICE ALLOCATIONS ACCOUNT
		$account = null;
		$account['company_id'] = $fm_company_id;
		$account['account_type'] = "Fleet Manager";
		$account['category'] = "Invoice Allocations";
		$account['account_status'] = "Active";
		$account['account_name'] = "Invoice Allocations (".$fm_person["f_name"]." ".$fm_person["l_name"].")";
		db_insert_account($account);
		
		//CREATE FUEL ALLOCATIONS ACCOUNT
		$account = null;
		$account['company_id'] = $fm_company_id;
		$account['account_type'] = "Fleet Manager";
		$account['category'] = "Track Expense";
		$account['account_group'] = "Fuel Allocations";
		$account['account_status'] = "Active";
		$account['account_name'] = "Fuel Allocations (".$fm_person["f_name"]." ".$fm_person["l_name"].")";
		db_insert_account($account);
		
		//CREATE CC ALLOCATIONS ACCOUNT
		$account = null;
		$account['company_id'] = $fm_company_id;
		$account['account_type'] = "Fleet Manager";
		$account['category'] = "Track Expense";
		$account['account_group'] = "Spark CC";
		$account['account_status'] = "Active";
		$account['account_name'] = "Spark CC (".$fm_person["f_name"].")";
		//db_insert_account($account);
		
		//CREATE SMARTPAY ALLOCATIONS ACCOUNT
		$account = null;
		$account['company_id'] = $fm_company_id;
		$account['account_type'] = "Fleet Manager";
		$account['category'] = "Track Expense";
		$account['account_group'] = "SmartPay";
		$account['account_status'] = "Active";
		$account['account_name'] = "SmartPay (".$fm_person["f_name"]." ".$fm_person["l_name"].")";
		db_insert_account($account);
		
		
	}
	
	//CALCULATE SETTLEMENTS
	function get_settlement($load_id)
	{
		//GET THE LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		if(!empty($load["map_miles"]))
		{
			//GET ALL THE ADJUSTMENTS
			$where = null;
			$where["load_id"] = $load_id;
			$adjustments = db_select_settlement_adjustments($where);
			
			$sum_of_adjustments = 0;
			foreach($adjustments as $adjustment)
			{
				$sum_of_adjustments = $sum_of_adjustments + $adjustment["amount"];
			}
			
			//GET ALL THE EXPENSES
			$where = null;
			$where["load_id"] = $load_id;
			$expenses = db_select_settlement_expenses($where);
			
			$sum_of_expenses = 0;
			foreach($expenses as $expense)
			{
				$sum_of_expenses = $sum_of_expenses + $expense["amount"];
			}
			
			$base_rate = 0.63;
			
			$loaded_rate = round($load["natl_fuel_avg"]/6 + $base_rate,2);
			$dead_head_rate = round($load["natl_fuel_avg"]/7.6 + $base_rate,2);
			$loaded_miles = round($load["map_miles"] - $load["dead_head_miles"],2);
			
			$performance_bonus = round($load["performance_rating"]*$load["performance_bonus"]*$load["map_miles"],2);
			
			$settlement  = null;
			$settlement["paid_on_miles"] = round(($loaded_miles * $loaded_rate) + ($load["dead_head_miles"] * $dead_head_rate),2);
			$settlement["carrier_revenue"] = round(($loaded_miles * $loaded_rate) + ($load["dead_head_miles"] * $dead_head_rate) + $sum_of_adjustments + $performance_bonus,2);
			$settlement["carrier_expenses_sum"] = round($sum_of_expenses,2);
			$settlement["carrier_profit"] = round($settlement["carrier_revenue"] - $settlement["carrier_expenses_sum"],2);
			$settlement["out_of_route"] = round($load["odometer_miles"] - $load["map_miles"],2);
			$settlement["mpg"] = round($load["odometer_miles"] / $load["gallons_used"],2);
			$settlement["loaded_miles"] = round($loaded_miles,2);
			$settlement["loaded_rate"] = round($loaded_rate,2);
			$settlement["dead_head_rate"] = round($dead_head_rate,2);
			$settlement["miles_per_day"] = round($load["map_miles"] / $load["total_hours"]*24,2);
			$settlement["profit_per_day"] = round($settlement["carrier_profit"] / $load["total_hours"]*24,2);
			$settlement["adjustments"] = $adjustments;
			$settlement["sum_of_adjustments"] = $sum_of_adjustments;
			$settlement["performance_bonus"] = $performance_bonus;
			$settlement["expenses"] = $expenses;
			
			return $settlement;
		}
		else
		{
			return NULL;
		}
		
		
	}
	
	//DETERMINE IF PERMISSION EXISTS
	function user_has_permission($permission_name)
	{
		$CI =& get_instance();
		$where = null;
		$where["permission_name"] = $permission_name;
		$permission = db_select_permission($where);
		
		//echo "alert(".$permission['id'].");";
		
		$where = null;
		$where["user_id"] = $CI->session->userdata('user_id');
		$where["permission_id"] = $permission["id"];
		$user_permission = db_select_user_permission($where);
		
		if(empty($user_permission))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	//GET ACCOUNT BALANCE
	function get_account_balance($account_id,$with_sub_accounts = false)
	{
		$CI =& get_instance();
		$values[] = $account_id;
		$values[] = $account_id;
	
		//GET SUM OF ACCOUNT CREDITS
		$credit_sum = 0;
		if($with_sub_accounts)
		{
			$sql = "SELECT sum(entry_amount) as sum 
					FROM `account_entry`, `account`
					WHERE account_entry.account_id = account.id
					AND (account_id = ? OR parent_account_id = ?)
					AND debit_credit = 'Credit'";
		}
		else
		{
			$sql = "SELECT sum(entry_amount) as sum 
					FROM `account_entry`
					WHERE account_id = ?
					AND debit_credit = 'Credit'";
		}
		
		$query_sum_credit = $CI->db->query($sql,$values);
		foreach($query_sum_credit->result() as $row)
		{
			$credit_sum = $row->sum;
		}
		
		
		//GET SUM OF ACCOUNT DEBITS
		$debit_sum = 0;
		if($with_sub_accounts)
		{
			$sql = "SELECT sum(entry_amount) as sum 
					FROM `account_entry`, `account`
					WHERE account_entry.account_id = account.id
					AND (account_id = ? OR parent_account_id = ?)
					AND debit_credit = 'Debit'";
		}
		else
		{
			$sql = "SELECT sum(entry_amount) as sum 
					FROM `account_entry`
					WHERE account_id = ?
					AND debit_credit = 'Debit'";
		}
		
		$query_sum_debit = $CI->db->query($sql,$values);
		foreach($query_sum_debit->result() as $row)
		{
			$debit_sum = $row->sum;
		}
		
		//GET ACCOUNT
		$where = null;
		$where["id"] = $account_id;
		$account = db_select_account($where);
		
		if($account["account_class"] == "Asset" || $account["account_class"] == "Expense" )
		{
			//SUBTRACT DEBITS FROM CREDITS
			return round($debit_sum - $credit_sum,2);
		}
		else if($account["account_class"] == "Liability" || $account["account_class"] == "Revenue" )
		{
			//SUBTRACT DEBITS FROM CREDITS
			return round($credit_sum - $debit_sum,2);
		}
		
	}//end get_account_balance
	
	//CHECK IF ENTRY EXISTS IN THE SYSTEM
	function entry_exists($entry)
	{
		//print_r($entry);
		
		//IF ENTRY IS SMARTPAY PAY OUT, IGNORE REPORT UPLOAD DATE
		$datetime = $entry["entry_datetime"];
		$amount = $entry["entry_amount"];
		//$replace = array("&amp;")
		$description = str_replace("&amp;","%",$entry["entry_description"]);
		//$where = " entry_datetime = '$datetime' AND entry_amount = $amount AND entry_description LIKE '%$description%' ";
		
		$where = null;
		$where["entry_datetime"] = $datetime;
		$where["entry_amount"] = $amount;
		$where["entry_description"] = "%$description%";
		
		//$where = " entry_datetime = '$datetime' AND entry_amount = $amount ";
		$check_entry = db_select_account_entry($where);
		if(empty($check_entry))
		{
			return false;
		}
		else
		{
			if($entry['report_guid'] == $check_entry['report_guid'])
			{
				return false;
			}
			else
			{
				return true;
			}
		}
	}
	
	
	//CHECK TO SEE IF EXPENSE EXISTS IN THE SYSTEM
	function expense_exists($expense)
	{
		//LOOK FOR EXPENSE WITH SAME GUID
		$where = null;
		$where["guid"] = $expense["guid"];
		$check_expense = db_select_expense($where);
		
		if(empty($check_expense))
		{
			return false;
		}
		else
		{
			//if($entry['report_guid'] == $check_entry['report_guid'])
			//{
			//	return false;
			//}
			//else
			//{
				return true;
			//}
		}
	}
	
	//CREATE GUID FOR EXPENSE DURING TRANSACTION UPLOADS
	function create_expense_guid($entry)
	{
		//print_r($entry);
		
		$datetime = date('m/d/y-H:i:s',strtotime($entry["entry_datetime"]));
		$amount = round($entry["entry_amount"],2);
		$description = str_replace("&amp;","%",$entry["entry_description"]);
		
		return hash("md5",$datetime.$amount.$description);
		
	}
	
	//SEND EMAIL
	function send_email()
	{
		$this->load->library('email');

		$this->email->from($from);
		$this->email->to($to);
		$this->email->cc('fleetsmarts@integratedlogicsticssolutions.co');
		$this->email->subject($subject);
		$this->email->message('Testing the email class.');

		$this->email->send();
	}
	
	//UPDATES THE LEG WITH THE CURRENT END LEG EVENT (DOES NOT UPDATE DB) INFO AND CALCULATES LEG DETAILS, RETURNS LEG OBJECT
	function get_leg_details($end_leg_entry_id)
	{
		
		//GET END LEG LOG ENTRY
		$where = null;
		$where["id"] = $end_leg_entry_id;
		$end_leg_entry = db_select_log_entry($where);
		
		//UPDATE LEG WITH LOG ENTRY INFO AND GET LEG
		$where = null;
		$where["log_entry_id"] = $end_leg_entry_id;
		
		//$update_leg["load_id"] = $end_leg_entry["load_id"];
		//$update_leg["truck_id"] = $end_leg_entry["truck_id"];
		//$update_leg["trailer_id"] = $end_leg_entry["trailer_id"];
		//$update_leg["main_driver_id"] = $end_leg_entry["main_driver_id"];
		//$update_leg["codriver_id"] = $end_leg_entry["codriver_id"];
		//db_update_leg($update_leg,$where);

		$existing_leg = db_select_leg($where);
		
		//IF PROFIT SPLITS ARE EMPTY, USE CLIENT DEFAULTS AS SUGGESTIONS
		if(empty($existing_leg["main_driver_split"]))
		{
			$where = null;
			$where["id"] = $existing_leg["main_driver_id"];
			$main_driver = db_select_client($where);
		
			$existing_leg["main_driver_split"] = $main_driver["profit_split"];
		}
		
		if(empty($existing_leg["codriver_split"]))
		{
			$where = null;
			$where["id"] = $existing_leg["codriver_id"];
			$codriver = db_select_client($where);
		
			$existing_leg["codriver_split"] = $codriver["profit_split"];
		}
		
		//GET PREVIOUS LEG LOG ENTRY
		if(empty($end_leg_entry["truck_id"]))
		{
			$driver_id = 0;
			if(!empty($end_leg_entry["main_driver_id"]))
			{
				$driver_id = $end_leg_entry["main_driver_id"];
			}
			elseif(!empty($end_leg_entry["codriver_id"]))
			{
				$driver_id = $end_leg_entry["codriver_id"];
			}
			else
			{
				echo "There has to be at least a driver, codriver, or truck on this event!";
			}
		
			$where = null;
			$where = " entry_type = 'End Leg' AND (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$end_leg_entry["entry_datetime"]."' ";
			$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
		}
		else
		{
			$where = null;
			$where = " entry_type = 'End Leg' AND truck_id = ".$end_leg_entry["truck_id"]." AND entry_datetime < '".$end_leg_entry["entry_datetime"]."' ";
			$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
		}
		
		$event_list = null;
		$route_url = null;
		$odometer_miles = 0;
		$map_miles = null;
		$oor = 0;
		$hours = null;
		if(!empty($begin_leg_entry)) //IF THERE IS A PREVIOUS END LEG EVENT
		{
			//IF LEG RATE TYPE IS NOT PERSONAL - IGNORE MAPPING INFO
			if($existing_leg["rate_type"] != 'Personal')
			{
			
				//GET MAPPABLE LEG EVENTS
				$where = null;
				$where = " truck_id = ".$end_leg_entry["truck_id"]." AND entry_datetime < '".$end_leg_entry["entry_datetime"]."'  AND entry_datetime >= '".$begin_leg_entry["entry_datetime"]."' AND (entry_type = 'Pick' OR entry_type = 'Drop' OR entry_type = 'Checkpoint' OR entry_type = 'Driver In' OR entry_type = 'Driver Out' OR entry_type = 'Pick Trailer' OR entry_type = 'Drop Trailer' OR entry_type = 'End Leg' OR entry_type = 'End Week') ";
				$event_list = db_select_log_entrys($where,"entry_datetime");
			
				/**		
					$url_search = array(" ","&","'");
					$url_replace = array("+","and","");
					
					//GET ROUTE URL
					//https://maps.google.com/maps?saddr=Los+Angeles,+CA&daddr=Las+Vegas,+NV+to:Salt+Lake+City,+UT+to:Provo,+UT&hl=en
					$route_url = "https://maps.google.com/maps?saddr=".str_replace($url_search,$url_replace,$begin_leg_entry["address"].", ".$begin_leg_entry["city"].", ".$begin_leg_entry["state"]);
					$waypoints = "";
					
					if(!empty($event_list))
					{
						$i = 1;
						foreach($event_list as $event)
						{
							if($i == 1)
							{
								$route_url = $route_url."&daddr=".str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
								//$origin = str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
							}
							else
							{
								$route_url = $route_url."+to:".str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
							}
							
							$waypoints = $waypoints."|via:".str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
							
							$i++;
						}
						
						$params["waypoints"] = substr($waypoints,5);
					}
					
					
					$origin = str_replace($url_search,$url_replace,$begin_leg_entry["address"].", ".$begin_leg_entry["city"].", ".$begin_leg_entry["state"]);
					$destination = str_replace($url_search,$url_replace,$end_leg_entry["address"].", ".$end_leg_entry["city"].", ".$end_leg_entry["state"]);
					//GET MAP MILES
					$endpoint = 'http://maps.googleapis.com/maps/api/directions/json?';
					$params["origin"] = $origin;
					$params["destination"] = $destination;
					$params["mode"] = 'driving';
					$params["sensor"] = 'false';
					//echo http_build_query($params);

					// Fetch and decode JSON string into a PHP object
					$json = file_get_contents($endpoint.http_build_query($params));
					$data = json_decode($json);

					// If we got directions, output all of the HTML instructions
					if ($data->status === 'OK') 
					{
						$map_miles = 0;
						$route = $data->routes[0];
						foreach($route->legs as $gleg)
						{
							$map_miles = $map_miles + $gleg->distance->value;
						}
						$map_miles = round($map_miles/1609.34);
					}
				**/
				
				$map_info = get_map_info($event_list);
				$route_url = $map_info["route_url"];
				$map_miles = $map_info["map_miles"];
			}
			else
			{
				$map_info = null;
				$route_url = null;
				$map_miles = 0;
			}
			
			if(!empty($end_leg_entry["truck_id"]))
			{
				//GET ODOMETER MILES
				$odometer_miles = $end_leg_entry["odometer"] - $begin_leg_entry["odometer"];
				
				//DETERMINE OOR
				if($map_miles != 0)
				{
					$oor = round(($odometer_miles - $map_miles)/$map_miles*100,2);
				}
			}
			
			//GET HOURS
			$hours = (strtotime($end_leg_entry["entry_datetime"]) - strtotime($begin_leg_entry["entry_datetime"]))/60/60;
		
			
			//DETERMINE WHICH MAIN DRIVER TO ASSIGN TO THE LEG
			//IF THIS END LEG AND THE PREVIOUS END LEG HAVE DIFFERENT DRIVERS
			if($end_leg_entry["main_driver_id"] != $begin_leg_entry["main_driver_id"])
			{
				//GET END LEG ENTRY SYNC EVENT
				$where = null;
				$where["id"] = $end_leg_entry["id"];
				$end_leg_sync_event = db_select_log_entry($where);
			
				//IF END LEG ENTRY SYNC EVENT TYPE IS "DRIVER IN"
				if($end_leg_sync_event["entry_type"] == "Driver In")
				{
					//ASSIGN DRIVER FROM BEGIN LEG ENTRY
					$main_driver_id = $begin_leg_entry["main_driver_id"];
				}
				else 
				{
					//GET BEGIN LEG ENTRY SYNC EVENT
					$where = null;
					$where["id"] = $begin_leg_entry["id"];
					$begin_leg_sync_event = db_select_log_entry($where);			

					//ELSE IF PREVIOUS END LEG SYNC EVENT IS "DRIVER OUT"
					if($begin_leg_sync_event["entry_type"] == "Driver Out")
					{
						//ASSIGN DRIVER FROM END LEG ENTRY
						$main_driver_id = $end_leg_entry["main_driver_id"];
					}
					else
					{
						//THIS SHOULD NEVER HAPPEN!
						$main_driver_id = NULL;
					}
				}
			}
			else //THIS SHOULD COVER DRIVER OUT EVENTS
			{
				$main_driver_id = $end_leg_entry["main_driver_id"];
			}
			
			//DETERMINE WHICH CODRIVER TO ASSIGN TO THE LEG
			//IF THIS END LEG AND THE PREVIOUS END LEG HAVE DIFFERENT CO-DRIVERS
			if($end_leg_entry["codriver_id"] != $begin_leg_entry["codriver_id"])
			{
				//GET END LEG ENTRY SYNC EVENT
				$where = null;
				$where["id"] = $end_leg_entry["id"];
				$end_leg_sync_event = db_select_log_entry($where);
			
				//IF END LEG ENTRY SYNC EVENT TYPE IS "DRIVER IN"
				if($end_leg_sync_event["entry_type"] == "Driver In")
				{
					//ASSIGN DRIVER FROM BEGIN LEG ENTRY
					$codriver_id = $begin_leg_entry["codriver_id"];
				}
				else 
				{
					//GET BEGIN LEG ENTRY SYNC EVENT
					$where = null;
					$where["id"] = $begin_leg_entry["id"];
					$begin_leg_sync_event = db_select_log_entry($where);			

					//ELSE IF PREVIOUS END LEG SYNC EVENT IS "DRIVER OUT"
					if($begin_leg_sync_event["entry_type"] == "Driver Out")
					{
						//ASSIGN DRIVER FROM END LEG ENTRY
						$codriver_id = $end_leg_entry["codriver_id"];
					}
					else
					{
						//THIS SHOULD NEVER HAPPEN!
						$codriver_id = NULL;
					}
				}
			}
			else //THIS SHOULD COVER DRIVER OUT EVENTS
			{
				$codriver_id = $end_leg_entry["codriver_id"];
			}
			
			//DETERMINE WHICH MAIN DRIVER TO ASSIGN TO THE LEG
			//IF THIS END LEG AND THE PREVIOUS END LEG HAVE DIFFERENT TRAILERS
			if($end_leg_entry["trailer_id"] != $begin_leg_entry["trailer_id"])
			{
				//GET END LEG ENTRY SYNC EVENT
				$where = null;
				$where["id"] = $end_leg_entry["id"];
				$end_leg_sync_event = db_select_log_entry($where);
			
				//IF END LEG ENTRY SYNC EVENT TYPE IS "Pick Trailer"
				if($end_leg_sync_event["entry_type"] == "Pick Trailer")
				{
					//ASSIGN DRIVER FROM BEGIN LEG ENTRY
					$trailer_id = $begin_leg_entry["trailer_id"];
				}
				else 
				{
					//GET BEGIN LEG ENTRY SYNC EVENT
					$where = null;
					$where["id"] = $begin_leg_entry["id"];
					$begin_leg_sync_event = db_select_log_entry($where);			

					//ELSE IF PREVIOUS END LEG SYNC EVENT IS "Drop Trailer"
					if($begin_leg_sync_event["entry_type"] == "Drop Trailer")
					{
						//ASSIGN DRIVER FROM END LEG ENTRY
						$trailer_id = $end_leg_entry["trailer_id"];
					}
					else
					{
						//THIS SHOULD NEVER HAPPEN!
						$trailer_id = NULL;
					}
				}
			}
			else //THIS SHOULD COVER TRAILER DROP EVENTS
			{
				$trailer_id = $end_leg_entry["trailer_id"];
				
			}
		
		}
		else
		{
			$main_driver_id = $end_leg_entry["main_driver_id"];
			$codriver_id = $end_leg_entry["codriver_id"];
			$trailer_id = $end_leg_entry["trailer_id"];
		}
		
		//DETERMINE RATE TYPE
		if(empty($end_leg_entry["trailer_id"]))
		{
			$rate_type = "Bobtail";
		}
		else
		{
			//GET SYNC ENTRY
			$where = null;
			$where["id"] = $end_leg_entry["sync_entry_id"];
			$sync_event = db_select_log_entry($where);
			
			
			if(empty($end_leg_entry["load_id"]))
			{
				$rate_type = "Dead Head";
			}
			else
			{
				if($sync_event["entry_type"] == "Pick")
				{
					$rate_type = "Dead Head";
				}
				else
				{
					$rate_type = "Loaded";
				}
			}
		}
		
		
		
		if(empty($existing_leg["load_id"]))
		{
			$existing_leg["natl_fuel_avg"] = get_natl_fuel_avg_from_db($end_leg_entry["entry_datetime"]);
		}
		
		//DETERMINE TARGET MPG FOR RATE TYPE
		if($rate_type == "Loaded")
		{
			//GET TARGET MPG FROM SYSTEMS SETTINGS
			$where = null;
			$where["setting_name"] = "Loaded Target MPG";
			$loaded_target_mpg_setting = db_select_setting($where);
			$target_mpg = $loaded_target_mpg_setting["setting_value"];
			//$target_mpg = 6;
		}
		/** IM NOT SURE IF THIS IS NEEDED SO I JUST COMMENTED IT OUT B/C I WAS SCARED TO CHANGE SOMETHING
		else if($rate_type == "Light Freight")
		{
			//GET TARGET MPG FROM SYSTEMS SETTINGS
			$where = null;
			$where["setting_name"] = "Light Freight Target MPG";
			$target_mpg_setting = db_select_setting($where);
			$target_mpg = $target_mpg_setting["setting_value"];
			//$target_mpg = 7;
		}
		**/
		else if($rate_type == "Dead Head")
		{
			//GET TARGET MPG FROM SYSTEMS SETTINGS
			$where = null;
			$where["setting_name"] = "Dead Head Target MPG";
			$dead_head_target_mpg_setting = db_select_setting($where);
			$target_mpg = $dead_head_target_mpg_setting["setting_value"];
			//$target_mpg = 7.5;
		}
		else if($rate_type == "Bobtail")
		{
			//GET TARGET MPG FROM SYSTEMS SETTINGS
			$where = null;
			$where["setting_name"] = "Bobtail Target MPG";
			$bobtail_target_mpg_setting = db_select_setting($where);
			$target_mpg = $bobtail_target_mpg_setting["setting_value"];
			//$target_mpg = 9;
			//echo $target_mpg;
		}
		
		//echo $rate_type;
		
		//GET TARGET MPG FROM SYSTEMS SETTINGS
		$where = null;
		$where["setting_name"] = "Contractor Base Rate";
		$contractor_base_rate_setting = db_select_setting($where);

		$base_rate = $contractor_base_rate_setting["setting_value"];//this is where rate is determined if set to Auto... save_leg() in logs.php controller is where it is actually saved once determined rate type
		//$base_rate = 0.56;

		//error_log("base_rate ".$base_rate." | LINE ".__LINE__." ".__FILE__);
		
		$revenue_rate = round($existing_leg["natl_fuel_avg"]/$target_mpg + $base_rate,2);
		//echo $revenue_rate;
		
		
		//IF DEAD HEAD, FIND NEXT LOAD TO DETERMINE ALLOCATED_LOAD
		$leg["allocated_load_id"] = $end_leg_entry["load_id"];
		if(empty($end_leg_entry["load_id"]))
		{
			if($end_leg_entry["truck_id"] != 0) //IF A TRUCK EXISTS ON THIS EVENT
			{
				//GET NEXT EVENT WITH A LOAD
				$where = null;
				$where = " truck_id = ".$end_leg_entry["truck_id"]." AND entry_datetime > '".$end_leg_entry["entry_datetime"]."' AND load_id IS NOT NULL ";
				$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
				
				if(!empty($next_loaded_event))
				{
					$leg["allocated_load_id"] = $next_loaded_event["load_id"];
				}
			}
			else
			{
				$leg["allocated_load_id"] = NULL;
			}
		}
		
		
		
		$leg["existing_leg"] = $existing_leg;
		$leg["load_id"] = $end_leg_entry["load_id"];
		$leg["truck_id"] = $end_leg_entry["truck_id"];
		$leg["trailer_id"] = $trailer_id;
		$leg["main_driver_id"] = $main_driver_id;
		$leg["codriver_id"] = $codriver_id;
		$leg["rate_type"] = $rate_type;
		$leg["revenue_rate"] = $revenue_rate;
		$leg["hours"] = $hours;
		$leg["oor"] = $oor;
		$leg["odometer_miles"] = $odometer_miles;
		$leg["map_miles"] = $map_miles;
		$leg["end_leg_entry"] = $end_leg_entry;
		$leg["begin_leg_entry"] = $begin_leg_entry;
		$leg["event_list"] = $event_list;
		$leg["route_url"] = $route_url;
		
		return $leg;
		
	}
	
	//GET FUEL STOP DETAILS
	function get_fuel_stop_details($log_entry_id) //$log_entry_id of the "Fuel Fill" log_entry
	{
		$fuel_stop_details = null;
		
		//GET THIS FUEL STOP EVENT
		$where = null;
		$where["id"] = $log_entry_id;
		$this_fill_event = db_select_log_entry($where);
		
		//GET THIS FUEL STOP
		$where = null;
		$where["log_entry_id"] = $this_fill_event["id"];
		$this_fuel_stop = db_select_fuel_stop($where);
		
		//SET VALUES FOR THIS FUEL STOP
		$fuel_stop_details["fuel_stop_id"] = $this_fuel_stop["id"];
		
		if($this_fuel_stop["is_fill"] == "Yes")
		{
			$fuel_stop_details["fill_type"] = "Fill";
		}
		else
		{
			$fuel_stop_details["fill_type"] = "Partial";
		}
		
		if($this_fill_event["entry_type"] == "Fuel Reefer")
		{
			$fuel_stop_details["fill_type"] = "Reefer";
		}
		
		$fuel_stop_details["is_fill"] = $this_fuel_stop["is_fill"];
		$fuel_stop_details["gallons"] = $this_fuel_stop["gallons"];
		$fuel_stop_details["fuel_expense"] = $this_fuel_stop["fuel_expense"];
		$fuel_stop_details["rebate_amount"] = $this_fuel_stop["rebate_amount"];
		
		//IF THIS EVENT IS A FILL, GET ALL THE FILL TO FILL INFO
		if($this_fuel_stop["is_fill"] == "Yes")
		{
			//GET PREVIOUS FILL
			$where = null;
			$where = " entry_datetime < '".$this_fill_event["entry_datetime"]."' AND truck_id = '".$this_fill_event["truck_id"]."' AND  entry_type = 'Fuel Fill' ";
			$previous_fuel_fill_event = db_select_log_entry($where,"entry_datetime DESC",1);
			
			if(empty($previous_fuel_fill_event))
			{
				//GET FIRST RECORDED MAPPABLE EVENT
				$where = null;
				$where = " entry_datetime < '".$this_fill_event["entry_datetime"]."' AND truck_id = '".$this_fill_event["truck_id"]."' ";
				$previous_fuel_fill_event = db_select_log_entry($where,"entry_datetime",1);
			}
			
			
			
			$fuel_stop_details["odometer_miles"] = null;
			$fuel_stop_details["f2f_gallons"] = null;
			$fuel_stop_details["f2f_reefer_gallons"] = null;
			$fuel_stop_details["f2f_expense"] = null;
			$fuel_stop_details["f2f_reefer_expense"] = null;
			$fuel_stop_details["f2f_discount"] = null;
			$fuel_stop_details["f2f_route_url"] = null;
			$fuel_stop_details["f2f_miles"] = null;
			$fuel_stop_details["f2f_oor"] = null;
			$fuel_stop_details["f2f_mpg"] = null;
			
			
			//IF A PREVIOUS FILL EVENT EXISTS
			if(!empty($previous_fuel_fill_event))
			{
				//CALCULATE ODOMETER MILES
				$odometer_miles = $this_fill_event["odometer"] - $previous_fuel_fill_event["odometer"];
				$fuel_stop_details["odometer_miles"] = $odometer_miles;
				
				//GET ALL FUEL PARTIALS STOPS BETWEEN F2F
				$where = null;
				$where = " entry_datetime <= '".$this_fill_event["entry_datetime"]."' AND entry_datetime > '".$previous_fuel_fill_event["entry_datetime"]."' AND truck_id = '".$this_fill_event["truck_id"]."' AND (entry_type = 'Fuel Partial' OR entry_type = 'Fuel Reefer') ";
				$fuel_partials = db_select_log_entrys($where,"entry_datetime");
				
				//echo $where;
				
				//GET FUEL PERMITS FOR THIS FUEL STOP
				$this_fuel_stop_id = $this_fuel_stop["id"];
				$where = null;
				$where = " fuel_stop_id = $this_fuel_stop_id AND account_entry_id IS NULL ";
				$fuel_permits = db_select_fuel_permits($where);
				
				$total_permit_expense = 0;
				if(!empty($fuel_permits))
				{
					//ADD UP EXTRA PERMIT EXPENSE
					foreach($fuel_permits as $permit)
					{
						$total_permit_expense = $total_permit_expense + $permit["permit_expense"];
					}
				}
				
				//echo $this_fill_event["truck_id"];
				
				//CALCULATE F2F GALLONS, EXPENSE, DISCOUNT
				$f2f_gallons = $this_fuel_stop["gallons"];
				$f2f_expense = $this_fuel_stop["fuel_expense"] + $total_permit_expense;
				$f2f_discount = $this_fuel_stop["rebate_amount"];
				$f2f_reefer_gallons = 0;
				$f2f_reefer_expense = 0;
				if(!empty($fuel_partials))
				{
					//echo "inside fuel partials loop ";
					foreach($fuel_partials as $fuel_event)
					{
						$where = null;
						$where["log_entry_id"] = $fuel_event["id"];
						$fuel_stop = db_select_fuel_stop($where);
						
						//GET FUEL PERMITS FOR THIS FUEL STOP
						$where = null;
						$where["fuel_stop_id"] = $fuel_stop["id"];
						$fuel_permits = db_select_fuel_permits($where);
						
						$total_permit_expense = 0;
						if(!empty($fuel_permits))
						{
							//ADD UP EXTRA PERMIT EXPENSE
							foreach($fuel_permits as $permit)
							{
								$total_permit_expense = $total_permit_expense + $permit["permit_expense"];
							}
						}
						
						$f2f_gallons = $f2f_gallons + $fuel_stop["gallons"];
						$f2f_expense = $f2f_expense + $fuel_stop["fuel_expense"] + $total_permit_expense;
						$f2f_discount = $f2f_discount + $fuel_stop["rebate_amount"];
						
						//TOTAL REEFER GALLONS
						if($fuel_event["entry_type"] == "Fuel Reefer")
						{
							$f2f_reefer_gallons = $f2f_reefer_gallons + $fuel_stop["gallons"];
							$f2f_reefer_expense = $f2f_reefer_expense + $fuel_stop["fuel_expense"];
						}
					}
				}
				$fuel_stop_details["f2f_gallons"] = $f2f_gallons;
				$fuel_stop_details["f2f_reefer_gallons"] = $f2f_reefer_gallons;
				$fuel_stop_details["f2f_expense"] = $f2f_expense;
				$fuel_stop_details["f2f_reefer_expense"] = $f2f_reefer_expense;
				$fuel_stop_details["f2f_discount"] = $f2f_discount;
				
				//CALCULATE F2F MAP MILES
				$where = null;
				$where = " entry_datetime <= '".$this_fill_event["entry_datetime"]."' AND entry_datetime >= '".$previous_fuel_fill_event["entry_datetime"]."' AND truck_id = '".$this_fill_event["truck_id"]."' AND (entry_type = 'Pick' OR entry_type = 'Drop' OR entry_type = 'Checkpoint' OR entry_type = 'Driver In' OR entry_type = 'Driver Out' OR entry_type = 'Pick Trailer' OR entry_type = 'Drop Trailer' OR entry_type = 'Fuel Fill') ";
				$map_events = db_select_log_entrys($where,"entry_datetime");

				$map_info = get_map_info($map_events);
				
				$map_miles = $map_info["map_miles"]; 
				
				$fuel_stop_details["f2f_route_url"] = $map_info["route_url"];
				$fuel_stop_details["f2f_miles"] = $map_miles;
				
				//DETERMINE OOR
				$f2f_oor = 0;
				if($map_miles != 0)
				{
					$f2f_oor = round(($odometer_miles - $map_miles)/$map_miles*100,2);
				}
				$fuel_stop_details["f2f_oor"] = $f2f_oor;
				
				
				//GET PREVIOUS FILL FUEL STOP
				$where = null;
				$where["log_entry_id"] = $previous_fuel_fill_event["id"];
				$previous_fuel_fill_fuel_stop = db_select_fuel_stop($where);
				
				
				//DETERMINE MPG -- THIS EXCLUDES REEFER GALLONS
				//IF PREVIOUS FUEL STOP IS ESTIMATE
				if($previous_fuel_fill_fuel_stop["source"] == "Estimate")
				{
					$fuel_stop_details["f2f_mpg"] = round($odometer_miles/($f2f_gallons - $f2f_reefer_gallons - $previous_fuel_fill_fuel_stop["gallons"]),2);
				}
				else
				{
					$fuel_stop_details["f2f_mpg"] = round($odometer_miles/($f2f_gallons - $f2f_reefer_gallons),2);
				}
			}
		
		}
		
		//GET NATL FUEL AVG COMPARE
		$fuel_price_diff =  $this_fuel_stop["natl_fuel_avg"] - $this_fuel_stop["fuel_price"];
		if($fuel_price_diff > 0)
		{
			$fuel_stop_details["price_compare"] = number_format($fuel_price_diff,2);
		}
		else
		{
			$fuel_stop_details["price_compare"] = number_format($fuel_price_diff,2);
		}
		
		return $fuel_stop_details;
	}
	
	//UPDATE FUEL FILL CALCULATIONS FOR ALL UNLOCKED FUEL FILL EVENTS
	function update_fuel_calculations()
	{
		//GET ALL FUEL FILLS THAT ARE UNLOCKED
		$where = null;
		$where = " entry_type = 'Fuel Fill' AND locked_datetime IS NULL ";
		$unlocked_fuel_fills = db_select_log_entrys($where);
		
		foreach($unlocked_fuel_fills as $log_entry)
		{
			$fuel_stop_details = get_fuel_stop_details($log_entry["id"]);
			
			//UPDATE LEG EVENT WITH CURRENT INFORMATION
			$this_leg_event = null;
			$this_leg_event["load_id"] = $log_entry["load_id"];
			$this_leg_event["truck_id"] = $log_entry["truck_id"];
			$this_leg_event["trailer_id"] = $log_entry["trailer_id"];
			$this_leg_event["main_driver_id"] = $log_entry["main_driver_id"];
			$this_leg_event["codriver_id"] = $log_entry["codriver_id"];
			$this_leg_event["miles"] = $fuel_stop_details["f2f_miles"];
			$this_leg_event["out_of_route"] = $fuel_stop_details["f2f_oor"];
			$this_leg_event["mpg"] = $fuel_stop_details["f2f_mpg"];
			$this_leg_event["route"] = $fuel_stop_details["f2f_route_url"];
			
			$where = null;
			$where["id"] = $log_entry["id"];
			db_update_log_entry($this_leg_event,$where);
		}
	}
	
	//CHECK IF FUEL STOP IS ALREADY IN DB
	function fuel_stop_exists($fuel_stop)
	{
			$where = null;
			$where["guid"] = $fuel_stop["guid"];
			$existing_fuel_stop = db_select_fuel_stop($where);
			
			if(empty($existing_fuel_stop))
			{
				return false;
			}
			else
			{
				return true;
			}
	}
	
	//CREATE AN ESTIMATE FUEL STOP BASED OFF OF A LOG ENTRY ID - DOES NOT INSERT FUEL STOP INTO DB
	function calculate_fuel_fill_estimate($log_entry_id)
	{
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$this_fill_event = db_select_log_entry($where);
		
		
		//GET PREVIOUS FILL
		$where = null;
		$where = " entry_datetime < '".$this_fill_event["entry_datetime"]."' AND truck_id = '".$this_fill_event["truck_id"]."' AND  entry_type = 'Fuel Fill' ";
		$previous_fuel_fill_event = db_select_log_entry($where,"entry_datetime DESC",1);
		
		if(empty($previous_fuel_fill_event))
		{
			//GET FIRST RECORDED MAPPABLE EVENT
			$where = null;
			$where = " entry_datetime < '".$this_fill_event["entry_datetime"]."' AND truck_id = '".$this_fill_event["truck_id"]."' ";
			$previous_fuel_fill_event = db_select_log_entry($where,"entry_datetime",1);
		}
		
		//IF A PREVIOUS FILL EVENT EXISTS
		if(!empty($previous_fuel_fill_event))
		{
			//CALCULATE ODOMETER MILES
			$odometer_miles = $this_fill_event["odometer"] - $previous_fuel_fill_event["odometer"];
			
			//GET ALL FUEL PARTIALS STOPS BETWEEN F2F
			$where = null;
			$where = " entry_datetime < '".$this_fill_event["entry_datetime"]."' AND entry_datetime > '".$previous_fuel_fill_event["entry_datetime"]."' AND truck_id = '".$this_fill_event["truck_id"]."' AND (entry_type = 'Fuel Partial' OR entry_type = 'Fuel Reefer') ";
			$fuel_partials = db_select_log_entrys($where,"entry_datetime");
			
			//CALCULATE F2F GALLONS, EXPENSE, DISCOUNT
			$f2f_gallons = 0;
			$f2f_expense = 0;
			$f2f_discount = 0;
			$f2f_reefer_gallons = 0;
			$f2f_reefer_expense = 0;
			if(!empty($fuel_partials))
			{
				foreach($fuel_partials as $fuel_event)
				{
					$where = null;
					$where["log_entry_id"] = $fuel_event["id"];
					$fuel_stop = db_select_fuel_stop($where);
					
					$f2f_gallons = $f2f_gallons + $fuel_stop["gallons"];
					$f2f_expense = $f2f_expense + $fuel_stop["fuel_expense"];
					$f2f_discount = $f2f_discount + $fuel_stop["rebate_amount"];
					
					//TOTAL REEFER GALLONS
					if($fuel_event["entry_type"] == "Fuel Reefer")
					{
						$f2f_reefer_gallons = $f2f_reefer_gallons + $fuel_stop["gallons"];
						$f2f_reefer_expense = $f2f_reefer_expense + $fuel_stop["fuel_expense"];
					}
				}
			}
			
			//CALCULATE TOTAL REQUIRED F2F GALLON WITH GIVEN MPG
			$estimate_mpg = 6;
			$total_required_gallons = $odometer_miles/$estimate_mpg;
			
			$estimate_gallons = $total_required_gallons - $f2f_gallons;
			
			//CALCULATE ESTIMATED EXPENSE
			$fuel_price = get_natl_fuel_avg_from_db($this_fill_event["entry_datetime"]);
			$estimate_expense = $fuel_price * $estimate_gallons;
			
		}
		else
		{
			return null;
		}
		
		$fuel_fill_estimate = null;
		$fuel_fill_estimate["is_fill"] = "Yes";
		$fuel_fill_estimate["gallons"] = round($estimate_gallons,2);
		$fuel_fill_estimate["fuel_price"] = round($fuel_price,2);
		$fuel_fill_estimate["fuel_expense"] = round($estimate_expense,2);
		$fuel_fill_estimate["rebate_amount"] = 0;
		$fuel_fill_estimate["source"] = "Estimate";
		
		
		
		
		return $fuel_fill_estimate;
	}
	
	//GET LEG CALCULATIONS -- NO UPDATE TO DB
	function get_leg_calculations($leg_id)
	{
		$where = null;
		$where["id"] = $leg_id;
		$leg = db_select_leg($where);
		
		//GET END LEG LOG ENTRY
		$where = null;
		$where["id"] = $leg["log_entry_id"];
		$end_leg_entry = db_select_log_entry($where);
		
		//GET PREVIOUS LEG LOG ENTRY
		if(empty($end_leg_entry["truck_id"]))
		{
			$driver_id = 0;
			if(!empty($end_leg_entry["main_driver_id"]))
			{
				$driver_id = $end_leg_entry["main_driver_id"];
			}
			elseif(!empty($end_leg_entry["codriver_id"]))
			{
				$driver_id = $end_leg_entry["codriver_id"];
			}
			else
			{
				echo "There has to be at least a driver, codriver, or truck on this event!";
			}
		
			$where = null;
			$where = " entry_type = 'End Leg' AND (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$end_leg_entry["entry_datetime"]."' ";
			$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
		}
		else
		{
			$where = null;
			$where = " entry_type = 'End Leg' AND truck_id = ".$end_leg_entry["truck_id"]." AND entry_datetime < '".$end_leg_entry["entry_datetime"]."' ";
			$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
		}
		
		
		$truck_rate = 0;
		
		//GET TRUCK
		$where = null;
		$where["id"] = $leg["truck_id"];
		$truck = db_select_truck($where);
		
		if($truck["rental_rate_period"] == "Day")
		{
			$truck_rate = $truck["rental_rate"]/24;
		}
		else if($truck["rental_rate_period"] == "Week")
		{
			$truck_rate = $truck["rental_rate"]/7/24;
		}
		else if($truck["rental_rate_period"] == "Month")
		{
			$truck_rate = $truck["rental_rate"]*12/365.25/24;
		}
		else if($truck["rental_rate_period"] == "Year")
		{
			$truck_rate = $truck["rental_rate"]/365.25/24;
		}
		
		$trailer_rate = 0;
		
		//GET TRAILER
		$where = null;
		$where["id"] = $leg["trailer_id"];
		$trailer = db_select_trailer($where);
		
		if($trailer["rental_period"] == "Day")
		{
			$trailer_rate = $trailer["rental_rate"]/24;
		}
		else if($trailer["rental_period"] == "Week")
		{
			$trailer_rate = $trailer["rental_rate"]/7/24;
		}
		else if($trailer["rental_period"] == "Month")
		{
			$trailer_rate = $trailer["rental_rate"]*12/365.25/24;
		}
		else if($trailer["rental_period"] == "Year")
		{
			$trailer_rate = $trailer["rental_rate"]/365.25/24;
		}
		
		//DEFINE RATES
		$where = null;
		$where["setting_name"] = "Standard Incidental Insurance Premium Rate";
		$damage_rate_setting = db_select_setting($where);
		$damage_rate = $damage_rate_setting["setting_value"];
		//$damage_rate = .03;
		
		$where = null;
		$where["setting_name"] = "Factoring Rate";
		$factoring_rate_setting = db_select_setting($where);
		$factoring_rate = $factoring_rate_setting["setting_value"];
		//$factoring_rate = .04;
		
		$where = null;
		$where["setting_name"] = "Bad Debt Rate";
		$bad_debt_rate_setting = db_select_setting($where);
		$bad_debt_rate = $bad_debt_rate_setting["setting_value"];
		//$bad_debt_rate = .04;
		
		
		
		$insurance_rate = 0;
		if(!empty($end_leg_entry["truck_id"]))
		{
			$insurance_rate = 1250;
		}
		
		//GET ATHORITY FEE RATE
		$where = null;
		$where["setting_name"] = "Authority Fee";
		$authority_fee_setting = db_select_setting($where);
		$authority_fee_rate = $authority_fee_setting["setting_value"];
		
		//GET LOBOS COMPLIANCE CONSULTING RATE
		$where = null;
		$where["setting_name"] = "Lobos Compliance Fee Rate";
		$lobos_compliance_consulting_fee_setting = db_select_setting($where);
		$lobos_compliance_consulting_rate = $lobos_compliance_consulting_fee_setting["setting_value"];
		
		//GET COOP MEMBERSHIP FEE RATE
		$where = null;
		$where["setting_name"] = "Coop Membership Fee";
		$authority_fee_setting = db_select_setting($where);
		$membership_fee_rate = $authority_fee_setting["setting_value"];
		
	
		$leg_calc = null;
		$leg_calc["leg"] = $leg;
		$leg_calc["leg_id"] = $leg_id;
		$leg_calc["locations"] = $begin_leg_entry["city"].", ".$begin_leg_entry["state"]." - ".$end_leg_entry["city"].", ".$end_leg_entry["state"];
		$leg_calc["date_range"] = date("m/d H:i",strtotime($begin_leg_entry["entry_datetime"]))." - ".date("m/d H:i",strtotime($end_leg_entry["entry_datetime"]));
		$leg_calc["truck_rent"] = round($truck_rate * $leg["hours"],2);
		$leg_calc["trailer_rent"] = round($trailer_rate * $leg["hours"],2);
		$leg_calc["insurance_expense"] = round(($insurance_rate*12/365.25/24) * $leg["hours"],2);
		$leg_calc["damage_expense"] = round($damage_rate * $leg["odometer_miles"],2);
		$leg_calc["truck_mileage"] = round($truck["mileage_rate"] * $leg["odometer_miles"],2);
		$leg_calc["trailer_mileage"] = round($trailer["mileage_rate"] * $leg["odometer_miles"],2);
		$leg_calc["factoring"] = round($factoring_rate * $leg["map_miles"],2);
		$leg_calc["bad_debt"] = round($bad_debt_rate * $leg["map_miles"],2);
		$leg_calc["authority_expense"] = round($authority_fee_rate * $leg["odometer_miles"],2);
		$leg_calc["compliance_consulting_expense"] = round($lobos_compliance_consulting_rate * $leg["odometer_miles"],2);
		$leg_calc["membership_expense"] = round($membership_fee_rate * $leg["odometer_miles"],2);
		$leg_calc["map_miles"] = $leg["map_miles"];
		$leg_calc["rate_type"] = $leg["rate_type"];
		$leg_calc["rate"] = $leg["revenue_rate"];
		$leg_calc["odometer_miles"] = $leg["odometer_miles"];
		$leg_calc["hours"] = $leg["hours"];
		$leg_calc["gallons_used"] = $leg["gallons_used"];
		$leg_calc["reefer_gallons_used"] = $leg["reefer_gallons_used"];
		$leg_calc["fuel_expense"] = $leg["fuel_expense"];
		$leg_calc["reefer_fuel_expense"] = $leg["reefer_fuel_expense"];
		$leg_calc["carrier_revenue"] = round($leg["revenue_rate"] * $leg["map_miles"],2);
		$leg_calc["carrier_expense"] = 
			round(
				  $leg_calc["truck_rent"]
				+ $leg_calc["trailer_rent"] 
				+ $leg_calc["insurance_expense"]
				+ $leg_calc["damage_expense"]
				+ $leg_calc["truck_mileage"]
				+ $leg_calc["trailer_mileage"]
				+ $leg_calc["factoring"]
				+ $leg_calc["bad_debt"]
				+ $leg_calc["authority_expense"]
				+ $leg_calc["compliance_consulting_expense"]
				+ $leg_calc["membership_expense"]
				+ $leg_calc["fuel_expense"],
				2
			);
		$leg_calc["carrier_profit"] = round($leg_calc["carrier_revenue"]  - $leg_calc["carrier_expense"],2);
		@$leg_calc["oor"] = round(($leg_calc["odometer_miles"] - $leg_calc["map_miles"])/$leg_calc["map_miles"]*100,2);
		@$leg_calc["mpg"] = round($leg_calc["odometer_miles"]/$leg_calc["gallons_used"],2);
		
		return $leg_calc;
	}
	
	//VALIDATE LEG FOR FUEL FILL VALIDATION -- RETURNS A VALIDATION CODE
	function leg_is_valid($log_entry)
	{
		//echo " hello";
		$validation_code = 0; //IS VALID
		
		//GET LEG
		$where = null;
		$where["log_entry_id"] = $log_entry["id"];
		$leg = db_select_leg($where);
		
		//VALIDATE THAT ALLOCATED_LOAD IS NOT EMPTY
		if($leg["rate_type"] != "In Shop")
		{
			if(empty($log_entry["allocated_load_id"]) || empty($leg["allocated_load_id"]))
			{
				if($log_entry["truck_id"] != 0) //IF A TRUCK EXISTS ON THIS EVENT
				{
					$validation_code = 1; //LEG IS MISSING AN ALLOCATED LOAD
				}
			}
		}
		else //IF LEG IS IN THE SHOP
		{
			if($leg["odometer_miles"] != 0 || $leg["map_miles"] != 0)
			{
				$validation_code = 7; //IN THE SHOP LEG CAN'T HAVE MILES ALLOCATED TO IT
			}
		}
	
		//GET PREVIOUS END LEG LOG ENTRY
		$where = null;
		$where = " entry_type = 'End Leg' AND truck_id = ".$log_entry["truck_id"]." AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
		$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
		
		//echo $begin_leg_entry["id"]."<br>";
		
		if(!empty($begin_leg_entry))
		{
			//GET ALL MAPPABLE EVENTS AND FUEL STOPS
			$event_list = null;
			$where = null;
			$where = " truck_id = ".$log_entry["truck_id"]." AND entry_datetime < '".$log_entry["entry_datetime"]."'  AND entry_datetime > '".$begin_leg_entry["entry_datetime"]."' AND (entry_type = 'Fuel Fill' OR entry_type = 'Fuel Partial' OR entry_type = 'Pick' OR entry_type = 'Drop' OR entry_type = 'Checkpoint') ";
			$event_list = db_select_log_entrys($where,"entry_datetime");
			//error_log("END LEG LOG ENTRY ID TO LOCK".$log_entry["id"]." | LINE ".__LINE__." ".__FILE__);
			if(!empty($event_list))
			{
				//VALIDATE THAT ODOMETERS ARE INCREASING CHRONOLOGICALLY
				$previous_event = $begin_leg_entry;
				foreach($event_list as $event)
				{
					//echo " *event_id ".$event["id"]."<br>";
					//error_log("event_id".$event["id"]." | LINE ".__LINE__." ".__FILE__);
					
					//UPDATE THE ALLOCATED LOAD
					$update = null;
					if(empty($event["load_id"]))
					{
						//GET NEXT EVENT WITH A LOAD
						$where = null;
						$where = " truck_id = ".$event["truck_id"]." AND entry_datetime > '".$event["entry_datetime"]."' AND load_id IS NOT NULL ";
						$next_loaded_event = db_select_log_entry($where,"entry_datetime ","1");
						
						if(!empty($next_loaded_event))
						{
							$update["allocated_load_id"] = $next_loaded_event["load_id"];
						}
						else
						{
							$update["allocated_load_id"] = $event["load_id"];
						}
					}
					else
					{
						$update["allocated_load_id"] = $event["load_id"];
					}
					$where = null;
					$where["id"] = $event["id"];
					db_update_log_entry($update,$where);
					$event = db_select_log_entry($where);
					
					//VALIDATE CHRONOLOGICAL ODOMETERS
					if($leg["rate_type"] != "Personal")
					{
						if($previous_event["odometer"] > $event["odometer"])
						{
							$validation_code = 2; // ODOMETERS ARE NOT IN CHRONOLOGICAL ORDER
							//error_log("event_id for non chronological odometers is ".$event["id"]." | LINE ".__LINE__." ".__FILE__);
							//error_log("this event id - odom ".$event["id"]." - ".$event["odometer"]." | LINE ".__LINE__." ".__FILE__);
							//error_log("previous event id - odom ".$previous_event["id"]." - ".$previous_event["odometer"]." | LINE ".__LINE__." ".__FILE__);
						}
					}
					
					$previous_event = $event;
					
					/*
					*** VALIDATE THAT MAIN DRIVER,CODRIVER,TRUCK,TRAILER,LOAD ARE CONSISTANT
					*/
					
					//GET END LEG SYNC EVENT
					$where = null;
					$where["id"] = $log_entry["sync_entry_id"];
					$end_leg_sync_event = db_select_log_entry($where);
					
					//GET PREVIOUS LEG LOG ENTRY
					if(empty($log_entry["truck_id"]))
					{
						$driver_id = 0;
						if(!empty($log_entry["main_driver_id"]))
						{
							$driver_id = $log_entry["main_driver_id"];
						}
						elseif(!empty($log_entry["codriver_id"]))
						{
							$driver_id = $log_entry["codriver_id"];
						}
						else
						{
							echo "There has to be at least a driver, codriver, or truck on this event!";
						}
					
						$where = null;
						$where = " entry_type = 'End Leg' AND (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
						$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
					}
					else
					{
						$where = null;
						$where = " entry_type = 'End Leg' AND truck_id = ".$log_entry["truck_id"]." AND entry_datetime < '".$log_entry["entry_datetime"]."' ";
						$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
					}
					
					
					if($end_leg_sync_event["entry_type"] == "End Week")
					{
						//IF RATE TYPE IS PERSONAL - NO NEED TO VALIDATE
						if($leg["rate_type"] != "Personal")
						{
							//VALIDATE SAME DRIVER
							if($event["main_driver_id"] != $log_entry["main_driver_id"])
							{
								$validation_code = 3; // LEG HAS MUTLITPLE DRIVERS
								//echo "event ".$event["main_driver_id"];
								//echo " log_entry ".$log_entry["main_driver_id"];
							}
						}
					}
					
					
					//IF END_LEG SYNC EVENT IS DRIVER IN
					if($end_leg_sync_event["entry_type"] == "Driver In" || $end_leg_sync_event["entry_type"] == "Driver Out")
					{
						//VALIDATE AGAINST BEGIN LEG EVENT... NOT THIS END LEG (log_entry)
						if(!($event["codriver_id"] == $begin_leg_entry["codriver_id"]))
						{
							if(!(empty($event["codriver_id"]) && empty($begin_leg_entry["codriver_id"])))
							{
								//echo "event codriver ".$event["codriver_id"]."<br>";
								//echo " previous_end_leg_id ".$begin_leg_entry["id"]." codriver_id ".$begin_leg_entry["codriver_id"]."<br>";
								//echo " event_id ".$event["id"]."<br>";
								$validation_code = 4; // LEG HAS MULTIPLE CO-DRIVERS
							}
						}
					}
					else //IF END_LEG SYNC EVENT IS NOT A DRIVER IN...
					{
						//VALIDATE AGAINST END LEG EVENT(log_entry)
						if($event["codriver_id"] != $log_entry["codriver_id"])
						{
							if(!(empty($event["codriver_id"]) && empty($log_entry["codriver_id"])))
							{
								//echo "event codriver ".$event["codriver_id"]."<br>";
								//echo " log_entry codriver ".$log_entry["codriver_id"]."<br>";
								//echo " event_id ".$event["id"]."<br>";
								$validation_code = 4; // LEG HAS MULTIPLE CO-DRIVERS
							}
						}
						
					}
					
					
					//VALIDATE SAME TRAILER
					if(empty($leg["trailer_id"]))
					{
						$leg["trailer_id"]  = 0;
					}
					
					if($log_entry["trailer_id"] != $leg["trailer_id"])
					{
						//echo "e ".$log_entry["trailer_id"]."<br>";
						//echo " L ".$leg["trailer_id"]."<br>";
						//echo " id ".$log_entry["id"]."<br>";
						$validation_code = 5; // LEG HAS MULTIPLE TRAILERS
					}
					
					//echo $log_entry["allocated_load_id"]." - ".$event["allocated_load_id"]." ";
					
					//VALIDATE SAME LOAD - DO THIS BY CHECKING ALLOCATED LOAD
					if($event["allocated_load_id"] != $log_entry["allocated_load_id"])
					{
						//GET THE LOAD FOR THIS EVENT
						$where = null;
						$where["id"] = $event["allocated_load_id"];
						$event_load = db_select_load($where);
						
						//GET THE LOAD FOR THIS EVENT
						$where = null;
						$where["id"] = $log_entry["allocated_load_id"];
						$end_leg_load = db_select_load($where);
						
						//EXCUSE POWER ONLY AND PARTIAL LOADS FROM VALIDATION
						if(!($event_load["load_type"] == "Partial" || $event_load["load_type"] == "Power Only" || $end_leg_load["load_type"] == "Partial" || $end_leg_load["load_type"] == "Power Only") && $leg["rate_type"]  != "Personal")
						{
							$validation_code = 6; //LEG HAS MULTIPLE LOADS
						}
					}
					
				}
			}


			
		
		}
		
		return $validation_code;
		
	}//end leg_is_valid()
	
	//CREATE FUEL ALLOCATIONS -- ASSUMES FUEL STOP DETAILS ARE UPDATED AND CORRECT
	function create_fuel_allocations($log_entry_id) //ID OF FUEL STOP LOG_ENTRY EVENT
	{
		print_to_log('fuel allocations');
		
		//GET FUEL STOP LOG ENTRY EVENT
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
	
		//GET FUEL STOP
		$where = null;
		$where["log_entry_id"] = $log_entry["id"];
		$fuel_stop = db_select_fuel_stop($where);
		print_to_log('fuel_stop');
	
		//GET FUEL STOP DETAILS
		$fuel_stop_details = get_fuel_stop_details($log_entry["id"]);
		print_to_log('fuel_stop_details');
		
		//GET PREVIOUS FILL EVENT
		$where = null;
		$where = " entry_datetime < '".$log_entry["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' AND  entry_type = 'Fuel Fill' ";
		$previous_fuel_fill_event = db_select_log_entry($where,"entry_datetime DESC",1);
		print_to_log('previous_fuel_fill_event');
		
		if($previous_fuel_fill_event["entry_type"] != "Fuel Fill")
		{
			$previous_fuel_fill_fuel_stop = null;
		}
		else
		{
			//GET PREVIOUS FILL FUEL STOP
			$where = null;
			$where["log_entry_id"] = $previous_fuel_fill_event["id"];
			$previous_fuel_fill_fuel_stop = db_select_fuel_stop($where);
		}
		
		
		//echo $log_entry_id
		//IF PREVIOUS FUEL STOP IS ESTIMATE
		if($previous_fuel_fill_fuel_stop["source"] == "Estimate")
		{
			//echo "Includes Estimate";
			//SUBTRACT ESTIMATE GALLONS AND EXPENSE FROM F2F TOTALS -- FOR FUEL ALLOCATION PURPOSES
			$expense_to_allocate = $fuel_stop_details["f2f_expense"] - $previous_fuel_fill_fuel_stop["fuel_expense"];
			$gallons_to_allocate = $fuel_stop_details["f2f_gallons"] - $previous_fuel_fill_fuel_stop["gallons"];
		}
		else
		{
			//echo " not an estimate";
			$expense_to_allocate = $fuel_stop_details["f2f_expense"];
			$gallons_to_allocate = $fuel_stop_details["f2f_gallons"];
		}
		
		
		
		//CHECK TO MAKE SURE FUEL STOP IS NOT LOCKED
		if(empty($log_entry["locked_datetime"]))
		{
			//DELETE ALL FUEL ALLOCAIONS WITH THIS FUEL ID
			$where = null;
			$where["fuel_stop_id"] = $fuel_stop["id"];
			db_delete_fuel_allocation($where);
			
			//GET NEXT END LEG EVENT
			$where = null;
			$where = " entry_datetime > '".$log_entry["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' AND  entry_type = 'End Leg' ";
			$next_end_leg_event = db_select_log_entry($where,"entry_datetime ",1);
			print_to_log('next_end_leg_event');
			
			if(!empty($next_end_leg_event))
			{
				//GET PREVIOUS FILL
				//$where = null;
				//$where = " entry_datetime < '".$log_entry["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' AND  entry_type = 'Fuel Fill' ";
				//$previous_fuel_fill_event = db_select_log_entry($where,"entry_datetime DESC",1);
				
				if(empty($previous_fuel_fill_event))
				{
					print_to_log('empty(previous_fuel_fill_event)');
					//GET FIRST RECORDED MAPPABLE EVENT
					$where = null;
					$where = " entry_datetime < '".$log_entry["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' ";
					$previous_fuel_fill_event = db_select_log_entry($where,"entry_datetime",1);
				}
				
				print_to_log('previous_fuel_fill_event = '.$previous_fuel_fill_event["id"]);
			
				//GET ALL END LEG EVENTS AND FUEL FILLS F2F
				$where = null;
				$where = " entry_datetime < '".$log_entry["entry_datetime"]."' AND entry_datetime >= '".$previous_fuel_fill_event["entry_datetime"]."' AND truck_id = '".$log_entry["truck_id"]."' AND  (entry_type = 'End Leg' OR entry_type = 'Fuel Fill') ";
				$allocation_events = db_select_log_entrys($where,"entry_datetime DESC");
				print_to_log('allocation_events');
				
				$fuel_allocations = array();
				
				//FOREACH END LEG
				foreach($allocation_events as $event)
				{
					//echo $event["id"]." ";
					
					//GET NEXT END LEG OR FUEL FILL EVENT
					$where = null;
					$where = " entry_datetime > '".$event["entry_datetime"]."' AND truck_id = '".$event["truck_id"]."' AND (entry_type = 'End Leg' OR entry_type = 'Fuel Fill') ";
					$next_allocation_event = db_select_log_entry($where,"entry_datetime ",1);
				
					//CALCULATE MILES ALLOCATED
					$miles_allocated = $next_allocation_event["odometer"] - $event["odometer"];
					
					//CALCULATE PERCENTAGE ALLOCATED
					$percentage_allocated = $miles_allocated/$fuel_stop_details["odometer_miles"];
					
					//CALCULATE GALLONS ALLOCATED
					//echo $fuel_stop_details["f2f_gallons"];
					//$gallons_allocated = round($fuel_stop_details["f2f_gallons"] * $percentage_allocated,2);
					$gallons_allocated = $gallons_to_allocate * $percentage_allocated;
					$reefer_gallons_allocated = $fuel_stop_details["f2f_reefer_gallons"] * $percentage_allocated;
					
					//CALCUALATE EXPENSE ALLOCATED
					$expense_allocated = $expense_to_allocate * $percentage_allocated;
					$reefer_expense_allocated = round(($fuel_stop_details["f2f_reefer_expense"]) * $percentage_allocated,2);
					
					//GET NEXT LEG EVENT
					$where = null;
					$where = " entry_datetime > '".$event["entry_datetime"]."' AND truck_id = '".$event["truck_id"]."' AND entry_type = 'End Leg' ";
					$next_leg_event = db_select_log_entry($where,"entry_datetime ",1);
					
					//GET LEG
					$where = null;
					$where["log_entry_id"] = $next_leg_event["id"];
					$next_leg = db_select_leg($where);
				
					//STORE ALLOCATION IN ALLOCATIONS ARRAY
					$fuel_allocation = null;
					$fuel_allocation["fuel_stop_id"] = $fuel_stop["id"]; 
					$fuel_allocation["leg_id"] = $next_leg["id"]; 
					$fuel_allocation["miles"] = $miles_allocated; 
					$fuel_allocation["percentage"] = $percentage_allocated; 
					$fuel_allocation["gallons"] = $gallons_allocated; //truck and reefer combined
					$fuel_allocation["reefer_gallons"] = $reefer_gallons_allocated; 
					$fuel_allocation["expense"] = $expense_allocated; //truck and reefer combined
					$fuel_allocation["reefer_expense"] = $reefer_expense_allocated; 
					
					$fuel_allocations[] = $fuel_allocation;
				}
				print_to_log('after foreach allocation_events');
				//FOREACH ALLOCATION
				foreach($fuel_allocations as $allocation)
				{
					//INSERT ALLOCATION INTO DB
					db_insert_fuel_allocation($allocation);
				}
				print_to_log('after db_insert_fuel_allocation');
			}
		}
		else //IF FUEL STOP IS LOCKED
		{
		
		}
		
	
				
	}
	
	//GETS THE NATL FUEL AVERAGE FOR A GIVEN DATETIME
	function get_natl_fuel_avg_from_db($datetime,$needs_exact = false)
	{
		$start_day = date("Y-m-d",strtotime($datetime));
		$end_day = date("Y-m-d",strtotime($datetime) + 24*60*60);
	
		$where = null;
		$where = " datetime > '".$start_day."' AND datetime < '".$end_day."' ";
		$fuel_average = db_select_fuel_average($where);
		
		if($needs_exact)
		{
			return $fuel_average["fuel_avg"];
		}
		else
		{
			if(!empty($fuel_average["fuel_avg"]))
			{
				return $fuel_average["fuel_avg"];
			}
			else
			{
				//IF NO FUEL PRICE AVG EXISTS FOR THIS DATE -- FIND THE MOST RECENT ONE AND GO OFF OF THAT ONE
				$where = null;
				$where = " datetime < '".$end_day."' AND fuel_avg IS NOT NULL";
				$fuel_average = db_select_fuel_average($where,"datetime");
				
				return $fuel_average["fuel_avg"];
			}
		}
		
	}
	
	//GETS ALLOCATED FUEL GALLONS AND EXPENSE FOR A GIVEN LEG
	function get_fuel_allocations_for_leg($leg_id)
	{
		$where = null;
		$where["leg_id"] = $leg_id;
		$allocations = db_select_fuel_allocations($where);
		
		$total_gallons = 0;
		$total_reefer_gallons = 0;
		$total_expense = 0;
		$total_reefer_expense = 0;
		
		if(!empty($allocations))
		{
			foreach($allocations as $allocation)
			{
				$total_gallons = $total_gallons + $allocation["gallons"];
				$total_reefer_gallons = $total_reefer_gallons + $allocation["reefer_gallons"];
				$total_expense = $total_expense + $allocation["expense"];
				$total_reefer_expense = $total_reefer_expense + $allocation["reefer_expense"];
			}
		}
		
		$fuel_allocations_for_leg["total_gallons"] = $total_gallons;
		$fuel_allocations_for_leg["total_reefer_gallons"] = $total_reefer_gallons;
		$fuel_allocations_for_leg["total_expense"] = $total_expense;
		$fuel_allocations_for_leg["total_reefer_expense"] = $total_reefer_expense;
		
		return $fuel_allocations_for_leg;
	}
	
	//GET MAP MILES AND ROUTE FOR GIVEN EVENT ARRAY
	function get_map_info($map_events) //RETURNS AN ARRAY WITH MAP MILES AND ROUTE
	{
		//echo "<br>--- new function --<br>";
		$first_event = null;
		$end_event = null;
		$map_requests = array();
		if(!empty($map_events))
		{
			//GET THE ROUTE URL AND BREAK THE MAP EVENTS INTO SEPERATE REQUESTS (8 AT A TIME)
			$previous_event = null;
			$request_number = 1;
			$event_count = 0;
			$i = 1;
			foreach($map_events as $event)//EVENT IS log_entry IN DB
			{
				
				
				$event_count++;
				//$event_list = null;
				//$event_list[] = $previous_event;
				if($event_count > 4)
				{
					//INCREMENT THE REQUEST NUMBER
					$request_number++;
					$event_count = 0;
					//echo "event list count ".count($event_list)."<br>";
					//echo "reset event list<br>";
					$event_list = null;
					$event_list[] = $previous_event;
				}
				
				//echo "add to event list ".$event["city"]." ".$event["state"]." ";
				//echo "event count = ".$event_count."<br>";
				$event_list[] = $event;
				
				$map_requests[$request_number] = $event_list;
				
				$previous_event = $event;
				
				//HANDLE EVENT WITH GPS COORDINATES
				if(empty($event["gps_coordinates"]))
				{
					$event_address = $event["address"]." ".$event["city"].", ".$event["state"];
				}
				else
				{
					$event_address = str_replace(" ","",$event["gps_coordinates"]);
				}
				
				//CREATE URL FOR THE LINK
				if($i == 1)
				{
					$first_event = $event;
				
					$url_search = array(" ","&");
					$url_replace = array("+","and");
					//$route_url = "https://maps.google.com/maps?saddr=".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
					$route_url = "https://maps.google.com/maps?saddr=".str_replace($url_search,$url_replace,$event_address);
					
				}
				else if($i == 2)
				{
					//$route_url = $route_url."&daddr=".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
					$route_url = $route_url."&daddr=".str_replace($url_search,$url_replace,$event_address);
				}
				else
				{
					//$route_url = $route_url."+to:".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
					$route_url = $route_url."+to:".str_replace($url_search,$url_replace,$event_address);
				}
				
				$i++;
				
				
			}//END FOREACH EVENT
			
			//echo count($map_requests)."<br>";
			
			
			$total_map_miles = 0;
			$this_request = 1;
			foreach($map_requests as $these_events)
			{
				//echo "<br>--- new request --<br>";
				
				$i = 0;
				$previous_waypoint = "";
				$waypoints = "";
				foreach($these_events as $event)
				{
					$i++;
					
					//error_log($event["city"]." ".$event["state"]." line ".__LINE__." ".__FILE__);
					if($i == 1)
					{
						//STORE THE FIRST EVENT - ORIGIN
						$first_event = $event;
						
						if(empty($event["gps_coordinates"]))
						{
							$origin_address = $event["address"]." ".$event["city"].", ".$event["state"];
						}
						else
						{
							$origin_address = str_replace(" ","",$event["gps_coordinates"]);
						}
						
						//$url_search = array(" ","&");
						//$url_replace = array("+","and");
						//$route_url = "https://maps.google.com/maps?saddr=".str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
					}
					else if($i > 1)
					{
						//$route_url = $route_url."&daddr=".str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
						//$origin = str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
						
						//ADD THE PREVIOUS WAYPOINT TO THE URL AND ADD THE | TO THE END TO PREPARE FOR THE NEXT
						$waypoints = $waypoints.$previous_waypoint."|";
						//$previous_waypoint = $waypoints."via:".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
						
						if(empty($event["gps_coordinates"]))
						{
							$previous_waypoint = "via:".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
						}
						else
						{
							$previous_waypoint = "via:".str_replace($url_search,$url_replace,str_replace(" ","",$event["gps_coordinates"]));
						}
					
					}
					
					
					
					//GET THE LAST EVENT - DESTINATION
					$end_event = $event;
					if(empty($event["gps_coordinates"]))
					{
						$destination_address = $end_event["address"]." ".$end_event["city"].", ".$end_event["state"];
					}
					else
					{
						$destination_address = str_replace(" ","",$end_event["gps_coordinates"]);
					}
					//error_log($destination_address." line ".__LINE__." ".__FILE__);
				}
				
				//echo $i."<br>";
					
				//GET MAP MILES
				//$origin = str_replace($url_search,$url_replace,$first_event["address"]." ".$first_event["city"].", ".$first_event["state"]);
				$origin = str_replace($url_search,$url_replace,$origin_address);
				//$destination = str_replace($url_search,$url_replace,$end_event["address"]." ".$end_event["city"].", ".$end_event["state"]);
				$destination = str_replace($url_search,$url_replace,$destination_address);
				$base_url = 'https://maps.googleapis.com/maps/api/directions/json?';
				$params["waypoints"] = "";
				$params["origin"] = $origin;
				$params["destination"] = $destination;
				if($i >= 3) //IF THERE ARE ANY WAYPOINTS - ADD THEM TO THE PARAMS ---- changed from if($i > 3)
				{
					$params["waypoints"] = substr($waypoints,5);
				}
				$params["mode"] = 'driving';
				$params["sensor"] = 'false';
				$params["key"] = 'AIzaSyAK_rUXKdQt8e-0Ytp31TPmtInfBKOMXL8';//CANT GET THE KEY TO WORK
				//$params["key"] = 'AIzaSyCuZiv5qr-WPqljQqEy28qFHOt-s4WqZ2M';

				
				//ADD THE WAY POINTS TO THE GOOGLE MAPS HTTP REQUEST 
				//$route_url = $route_url."+to:".str_replace($url_search,$url_replace,$end_event["address"].", ".$end_event["city"].", ".$end_event["state"]);
				
				
				//echo $base_url.http_build_query($params);
				//echo urlencode(http_build_query($params));
				
				//CREATE PARAM URL
				$param_url = http_build_query($params);
				
				//error_log($base_url.$param_url." line ".__LINE__." ".__FILE__);
				
				//SEARCH DB FOR PREVIOUS REQUEST
				$where = null;
				$where["param_url"] = $param_url;
				$previous_rr = db_select_route_request($where);
				
				//$previous_rr = null;
				
				//IF PREVIOUS REQUEST EXISTS IN DB
				if(!empty($previous_rr) && $previous_rr["status"] == "OK")
				{
					//echo "Previous request found";
					
					//SET MAP MILES FROM PREVIOUS REQUEST
					$map_miles = $previous_rr["map_miles"];
					
					//INCREMENT COUNT ON ROUTE REQUEST
					$update_rr = null;
					$update_rr["count"] = $previous_rr["count"]+1;
					
					$where = null;
					$where["id"] = $previous_rr["id"];
					db_update_route_request($update_rr,$where);
				
				}
				else//ELSE IF PREVIOUS REQUEST DOES NOT EXIST
				{
					//CHECK HOW MANY REQUEST HAVE BEEN MADE IN THE LAST 24 HOURS
					$CI =& get_instance();
					$sql = "SELECT COUNT(*) as hit_count FROM route_request WHERE request_datetime > '".date("Y-m-d H:i:s")."'";
					$query = $CI->db->query($sql);
					
					foreach ($query->result() as $row)
					{
						$request_count = $row->hit_count;
					}
					
					if($request_count > 2000)
					{
						echo "You are nearing the Google limit - You're at $request_count";
					}
					
					//REQUEST ROUTE AND STORE DATA IN DATA OBJECT
					//echo $base_url.$param_url;
					$json = file_get_contents($base_url.$param_url);
					$data = json_decode($json);

					if(isset($data))
					{
						$data_status = $data->status;
					}
					else
					{
						$data_status = "EMPTY";
					}
					
					//IF STATUS IS OK GET THE MAP MILES FROM THE ROUTES
					$map_miles = 0;
					if ($data_status == 'OK') 
					{
						$route = $data->routes[0];
						foreach($route->legs as $gleg)
						{
							$map_miles = $map_miles + $gleg->distance->value;
						}
					}
					//else
					//{
						//echo $data->status;
					//}
					
					$map_miles = round($map_miles/1609.34); //CONVERT FROM KM TO MILES
					
					date_default_timezone_set('America/Denver');
					
					if(!empty($previous_rr))
					{
						//INCREMENT COUNT ON ROUTE REQUEST
						$update_rr = null;
						$update_rr["count"] = $previous_rr["count"]+1;
						$update_rr["request_datetime"] = date("Y-m-d H:i:s");
						$update_rr["web_service"] = $base_url;
						$update_rr["param_url"] = $param_url;
						$update_rr["status"] = $data_status;
						$update_rr["map_miles"] = $map_miles;
						$update_rr["route_url"] = $route_url;
						
						$where = null;
						$where["id"] = $previous_rr["id"];
						db_update_route_request($update_rr,$where);
					}
					else
					{
						//INSERT ROUTE REQUEST INTO DB
						$rr = null;
						$rr["request_datetime"] = date("Y-m-d H:i:s");
						$rr["web_service"] = $base_url;
						$rr["param_url"] = $param_url;
						$rr["status"] = $data_status;
						$rr["map_miles"] = $map_miles;
						$rr["route_url"] = $route_url;
						db_insert_route_request($rr);
					}
		
				}
				
				//echo $first_event["city"]." -> ".$end_event["city"]." ".$map_miles."<br>";
		
				$total_map_miles = $total_map_miles + $map_miles;
				$this_request++;
			}
		}
		
		$map_info["route_url"] = $route_url;
		$map_info["map_miles"] = $total_map_miles;
		
		
		
		return $map_info;
		
	}
	
	// function to geocode address, it will return false if unable to geocode address
	function geocode($address)
	{
	 
		// url encode the address
		$address = urlencode($address);
		 
		// google map geocode api url
		$url = "http://maps.google.com/maps/api/geocode/json?address=$address";
		$url = "http://maps.google.com/maps/api/geocode/json?address=$address";
	 
		// get the json response
		$resp_json = file_get_contents($url);
		 
		// decode the json
		$resp = json_decode($resp_json, true);
	 
		// response status will be 'OK', if able to geocode given address 
		if($resp['status']=='OK')
		{
	 
			// get the important data
			$lati = $resp['results'][0]['geometry']['location']['lat'];
			$longi = $resp['results'][0]['geometry']['location']['lng'];
			$formatted_address = $resp['results'][0]['formatted_address'];
			 
			// verify if data is complete
			if($lati && $longi && $formatted_address)
			{
				// put the data in the array
				$data_arr = array();            
				 
				$data_arr["lat"] = $lati;
				$data_arr["long"] = $longi;
				$data_arr["formatted_address"] = $formatted_address;
				 
				return $data_arr;
			}
			else
			{
				return false;
			}
			 
		}
		else
		{
			return false;
		}
	}
	
	function reverse_geocode($latlng)
	{
		date_default_timezone_set('America/Denver');
		$datetime = date('Y-m-d H:i:s');
		
		if(empty($latlng))
		{
			return false;
		}
	 
		//TAKE ALL SPACES OUT LATLNG
		$latlng = str_replace(" ","",$latlng);
	 
		//CHECK TO SEE IF THIS GEOCODE REQUEST HAS ALREADY BEEN MADE
		$where = null;
		$where["latlng"] = $latlng;
		$geocode_request = db_select_geocode_request($where);
		if(empty($geocode_request))
		{
			 $url_latlng = urlencode($latlng);
			 
			// google map geocode api url
			//$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$url_latlng";//&key=AIzaSyAK_rUXKdQt8e-0Ytp31TPmtInfBKOMXL8";
			$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$url_latlng&key=AIzaSyAK_rUXKdQt8e-0Ytp31TPmtInfBKOMXL8";
			
			// get the json response
			$resp_json = file_get_contents($url);
			 
			// decode the json
			$resp = json_decode($resp_json, true);
		 
			//print_r($resp);
		 
			// response status will be 'OK', if able to geocode given address 
			if($resp['status']=='OK')
			{
				
				//var geo_city = extractFromAdress(results[0].address_components, "locality");//CITY
				//var geo_state = extractFromAdress(results[0].address_components, "administrative_area_level_1");//STATE
		 
				$street_number = "";
				$street = "";
				$city = "";
				$state = "";
				
				foreach($resp['results'][0]['address_components'] as $ac)
				{
					if($ac['types'][0] == "street_number")
					{
						$street_number = $ac['long_name'];
					}
					
					if($ac['types'][0] == "route")
					{
						$street = $ac['long_name'];
					}
					
					if($ac['types'][0] == "locality")
					{
						$city = $ac['long_name'];
					}
					
					if($ac['types'][0] == "administrative_area_level_1")
					{
						$state = $ac['short_name'];
					}
					
				}
				
				//GET CITY AND STATE
				//$city = $resp['results'][0]['address_components'][1]['long_name'];
				//$state = $resp['results'][0]['address_components']['administrative_area_level_1'];
				
				//INSERT NEW GEOCODE REQUEST INTO DB
				$new_gr = null;
				$new_gr["original_request_datetime"] = $datetime;
				$new_gr["latlng"] = $latlng;
				$new_gr["status"] = $resp['status'];
				$new_gr["street_number"] = $street_number;
				$new_gr["street"] = $street; 
				$new_gr["city"] = $city;
				$new_gr["state"] = $state;
				$new_gr["formatted_address"] = $resp['results'][0]['formatted_address'];
				$new_gr["request_count"] = 0;
				db_insert_geocode_request($new_gr);
				
				$data_arr = array();            
				 
				$data_arr["street_number"] = $street_number;
				$data_arr["street"] = $street; 
				$data_arr["city"] = $city;
				$data_arr["state"] = $state;
				$data_arr["formatted_address"] = $resp['results'][0]['formatted_address'];
				
				//print_r($data_arr);
				
				return $data_arr;
			}
			else
			{
				return false;
			}
		}
		else
		{
			//UPDATE COUNT OF GEOCODE_REQUEST
			$upate = null;
			$update["request_count"] = $geocode_request["request_count"] + 1;
			$where = null;
			$where["id"] = $geocode_request["id"];
			db_update_geocode_request($update,$where);
			
			//RETURN THE RESULTS THAT ARE ALREADY STORED IN THE SYSTEM
			$data_arr = array();            
				 
			$data_arr["street_number"] = $geocode_request["street_number"];
			$data_arr["street"] = $geocode_request["street"]; 
			$data_arr["city"] = $geocode_request["city"];
			$data_arr["state"] = $geocode_request["state"];
			$data_arr["formatted_address"] = $geocode_request['formatted_address'];
			
			//print_r($data_arr);
			
			return $data_arr;
		}
	 
	}
	
	function get_cardinal_directions($degrees)
	{
		if($degrees > 337.5 || $degrees <= 22.5)
		{
			return "N";
		}
		elseif($degrees > 22.5 || $degrees <= 67.5)
		{
			return "NE";
		}
		elseif($degrees > 67.5 || $degrees <= 112.5)
		{
			return "E";
		}
		elseif($degrees > 112.5 || $degrees <= 157.5)
		{
			return "SE";
		}
		elseif($degrees > 157.5 || $degrees <= 202.5)
		{
			return "S";
		}
		elseif($degrees > 202.5 || $degrees <= 247.5)
		{
			return "SW";
		}
		elseif($degrees > 247.5 || $degrees <= 292.5)
		{
			return "W";
		}
		elseif($degrees > 292.5 || $degrees <= 337.5)
		{
			return "NW";
		}
		else
		{
			return null;
		}
		
			
	}
	
	//CALCULATE FM COMMISSIONS FOR A LOAD
	function calc_commission($load)
	{
		//GET AMOUNT FUNDED .... FUNED AMOUNT PLUS FINANCING COST
		$rate_funded = $load["amount_funded"] + $load["financing_cost"];
		
		//GET ALL LOAD EXPENSES
		$total_load_expenses = sum_load_expenses($load);
		
		//CALCULATE COMMISSION
		$msioo_rate = .045;
		
		$factoring_driver_charge = .04 * $load["map_miles"];
		
		$three_percent_factoring = $rate_funded*.03;
		
		if($load["financing_cost"] > $three_percent_factoring )
		{
			$applied_factoring_charge = $load["financing_cost"];
		}
		else
		{
			$applied_factoring_charge = $three_percent_factoring;
		}
		
		$commission = ($rate_funded*(1-$msioo_rate))-$load["carrier_revenue"]+$factoring_driver_charge-$applied_factoring_charge-$total_load_expenses;
	
		return $commission;
	
	}
	
	//UPDATE LOAD WITH COMMISSION CALCULATIONS
	function update_commission_calc($load_id)
	{
		//GET ALL LEGS WITH THIS ALLOCATED LOAD ID
		$where = null;
		$where["allocated_load_id"] = $load_id;
		$these_legs = db_select_legs($where);
		
		//FOREACH LEG
		$total_cr = 0;
		$total_hours = 0;
		$total_map_miles = 0;
		foreach($these_legs as $leg)
		{
			//SUM CARRIER REVENUE
			$total_cr = $total_cr + ($leg["revenue_rate"] * $leg["map_miles"]);
			
			//SUM HOURS
			$total_hours = $total_hours + $leg["hours"];
			
			//SUM MAP MILES
			$total_map_miles = $total_map_miles + $leg["map_miles"];
			
		}
		
		//SAVE ALL CALCULATED TOTALS TO THE LOAD
		$update_load = null;
		$update_load["map_miles"] = $total_map_miles;
		$update_load["total_hours"] = $total_hours;
		$update_load["carrier_revenue"] = $total_cr;
		
		$where = null;
		$where["id"] = $load_id;
		db_update_load($update_load,$where);
	}
	
	//CALCULATE BOOKING STATISTICS
	function calc_booking_stats($loads)
	{
		$total_miles = 0;
		$total_funded = 0;
		$total_carrier_rev = 0;
		$total_commission = 0;
		
		foreach($loads as $load)
		{
			if(!empty($load["map_miles"]))
			{
				$total_miles = $total_miles + $load["map_miles"];
				$total_funded = $total_funded + $load["amount_funded"]+ $load["financing_cost"];
				$total_carrier_rev = $total_carrier_rev + $load["carrier_revenue"];
				$total_commission = $total_commission + (calc_commission($load)/2);
			}
		}
		
		$booking_rate = 0;
		$carrier_rate = 0;
		if($total_miles != 0)
		{
			$booking_rate = $total_funded/$total_miles;
			$carrier_rate = $total_carrier_rev/$total_miles;
		}
		
		$booking_stats["total_miles"] = $total_miles;
		$booking_stats["total_funded"] = $total_funded;
		$booking_stats["total_carrier_rev"] = $total_carrier_rev;
		$booking_stats["total_commission"] = $total_commission;
		$booking_stats["booking_rate"] = $booking_rate;
		$booking_stats["carrier_rate"] = $carrier_rate;
		
		return $booking_stats;
		
	}
	
	//SUM LOAD EXPENSES FOR GIVN LOAD
	function sum_load_expenses($load)
	{
		$where = null;
		$where["load_id"] = $load["id"];
		$load_expenses = db_select_load_expenses($where);
		
		$total = 0;
		if(!empty($load_expenses))
		{
			foreach($load_expenses as $expense)
			{
				$total = $total + $expense["expense_amount"];
			}
		}
		
		return $total;
	}
	
	//MAKE RESERVE ADJUSTMENTS FOR GIVEN FLEET MANAGER PERSON_ID
	function make_reserve_adjustments($fm_profit_account_id)
	{
		$CI =& get_instance();
	
		//GET RECORDER AND DATETIME
		$recorder_id = $CI->session->userdata('person_id');
		date_default_timezone_set('America/Denver');
		$entry_datetime = date("Y-m-d H:i:s");
		
		//GET FM PROFIT ACCOUNT
		$where = null;
		$where["id"] = $fm_profit_account_id;
		$fm_profit_account = db_select_account($where);
		
		//GET FM COMPANY
		$where = null;
		$where["id"] = $fm_profit_account["company_id"];
		$fm_company = db_select_company($where);
		
		//GET ALL DRIVERS FOR THIS FLEET MANAGER
		$where = null;
		$where["fleet_manager_id"] = $fm_company["person_id"];
		$drivers = db_select_clients($where);
		
		foreach($drivers as $client)
		{
			//GET DRIVERS PAY ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["account_type"] = "Client";
			$where["category"] = "Pay";
			$driver_pay_account = db_select_account($where);
			$pay = get_account_balance($driver_pay_account["id"]);
			
			//GET DRIVERS DAMAGE ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["account_type"] = "Client";
			$where["category"] = "Client Damage";
			$driver_damage_account = db_select_account($where);
			$damage = get_account_balance($driver_damage_account["id"]);
			
			//GET DRIVERS EQUIPMENT ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["account_type"] = "Client";
			$where["category"] = "Driver Equipment";
			$equipment_account = db_select_account($where);
			$equipment = get_account_balance($equipment_account["id"]);
			
			//GET DRIVERS BAHA ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["account_type"] = "Client";
			$where["category"] = "BAHA";
			$baha_account = db_select_account($where);
			$baha = get_account_balance($baha_account["id"]);
			
			//GET DRIVERS RESERVE ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["account_type"] = "Client";
			$where["category"] = "Reserve";
			$driver_reserve_account = db_select_account($where);
			$reserve = get_account_balance($driver_reserve_account["id"]);
			
			//IF PAY ACCOUNT AND DAMAGE ACCOUNT AND EQUIPMENT ACCOUNT AND BAHA ACCOUNT ARE NET NEGATIVE
			if(($pay + $damage + $baha + $equipment) < 0)
			{
				$adjustment_amount = $pay + $damage + $baha + $equipment + $reserve;
				
				//IF ADJUSTMENT AMOUNT IS POSITIVE
				if($adjustment_amount > 0)
				{
					//DEBIT RESERVE ACOUNT
					$reserve_adj["debit_credit"] = "Debit";
					$reserve_adj["entry_description"] = "Pull from the Reserve Account";
					
					//CREDIT FM PROFIT ACCOUNT
					$fm_profit_adj["debit_credit"] = "Credit";
					$fm_profit_adj["entry_description"] = "Pull from the Reserve Account for ".$client["client_nickname"];
				}
				else//IF ADJUSTMENT AMOUNT IS NEGATIVE
				{
					$adjustment_amount = $adjustment_amount * -1;
				
					//CREDIT RESERVE ACCOUNT
					$reserve_adj["debit_credit"] = "Credit";
					$reserve_adj["entry_description"] = "Add to the Reserve Account";
					
					//DEBIT FM PROFIT ACCOUNT
					$fm_profit_adj["debit_credit"] = "Debit";
					$fm_profit_adj["entry_description"] = "Add to the Reserve Account for ".$client["client_nickname"];
				}
				
				//RESERVE ADJUSTMENT ENTRY
				$reserve_adj["account_id"] = $driver_reserve_account["id"];
				$reserve_adj["recorder_id"] = $recorder_id;
				$reserve_adj["entry_datetime"] = $entry_datetime;
				$reserve_adj["entry_type"] ="Reserve Adjustment";
				$reserve_adj["entry_amount"] = round($adjustment_amount,2);
				
				db_insert_account_entry($reserve_adj);
				
				//FM PROFIT ACCOUNT ADJUSTMENT ENTRY
				$fm_profit_adj["account_id"] = $fm_profit_account["id"];
				$fm_profit_adj["recorder_id"] = $recorder_id;
				$fm_profit_adj["entry_datetime"] = $entry_datetime;
				$fm_profit_adj["entry_type"] ="Reserve Adjustment";
				$fm_profit_adj["entry_amount"] = round($adjustment_amount,2);
				
				db_insert_account_entry($fm_profit_adj);
			}
			else//ELSE IF PAY ACCOUNT AND DAMAGE ARE NET POSITIVE
			{
				//IF RESERVE ACCOUNT IS POSITIVE
				if($reserve > 0)
				{
					//ZERO OUT RESERVE ACCOUNT WITH DEBIT
					$reserve_adj["account_id"] = $driver_reserve_account["id"];
					$reserve_adj["recorder_id"] = $recorder_id;
					$reserve_adj["entry_datetime"] = $entry_datetime;
					$reserve_adj["entry_type"] ="Reserve Adjustment";
					$reserve_adj["entry_amount"] = round($reserve,2);
					$reserve_adj["debit_credit"] = "Debit";
					$reserve_adj["entry_description"] = "Pull from the Reserve Account";
					
					db_insert_account_entry($reserve_adj);
					
					//CREDIT FM PROFIT ACCOUNT
					$fm_profit_adj["account_id"] = $$fm_profit_account["id"];
					$fm_profit_adj["recorder_id"] = $recorder_id;
					$fm_profit_adj["entry_datetime"] = $entry_datetime;
					$fm_profit_adj["entry_type"] ="Reserve Adjustment";
					$fm_profit_adj["entry_amount"] = round($reserve,2);
					$fm_profit_adj["debit_credit"] = "Credit";
					$fm_profit_adj["entry_description"] = "Pull from the Reserve Account for ".$client["client_nickname"];
					
					db_insert_account_entry($fm_profit_adj);
					
					
				}
			}	
		}
	}
	
	//CHECKS TO SEE IF LOAD IS READY TO CALC COMMISSION - RETURNS VALIDATION ALERT, ICON, AND IS_GOOD BOOLEAN
	function is_commission_good($load)
	{
		//SET VALIDATION ICON AND ALERT MESSAGE
		$validation_icon = "/images/valid_icon.png";
		$validation_alert = "";
		
		$is_good = true;
		
		//IF IN TRANSIT
		if($load["status_number"] < 7 && empty($load["funded_datetime"]))
		{
			$validation_icon = "/images/invalid_icon.png";
			$validation_alert = "This load is still in transit. ";
			$is_good = false;
		}
		
		//IF PENDING FUNDING
		if($load["status_number"] == 7 && empty($load["funded_datetime"]))
		{
			$validation_icon = "/images/invalid_icon.png";
			$validation_alert = $validation_alert."This load has not funded. ";
			$is_good = false;
		}
		
		//IF CLOSED
		if($load["status_number"] == 7 && !empty($load["funded_datetime"]) && !empty($load["commission_approved_datetime"]))
		{
			$validation_icon = "/images/valid_icon.png";
			$validation_alert = "This load has been approved and settled!";
			$is_good = false;
		}
		
		//GET ALL EVENTS FOR THE LOAD
		$where = null;
		$where = " allocated_load_id = ".$load["id"]." AND locked_datetime IS NULL ";
		$unlocked_events = db_select_log_entrys($where);
		
		//IF THE LOAD HASN'T BEEN COMPLETELY LOCKED
		if(!empty($unlocked_events))
		{	
			$validation_icon = "/images/invalid_icon.png";
			$validation_alert = $validation_alert."This load has the following unlocked events:";
			$is_good = false;
			
			foreach($unlocked_events as $event)
			{
				$validation_alert = $validation_alert."\\n".$event["entry_type"]." in ".$event["city"].", ".$event["state"];
			}
		}
		
		//GET LOAD EXPENSES THAT ARE MISSING RECEIPT DATETIME
		$where = null;
		$where = " receipt_datetime IS NULL AND load_id = ".$load["id"]." ";
		$load_expenses = db_select_load_expenses($where);

		//VALIDATE THAT LOAD DOES NOT HAVE OUTSTANDING LOAD EXPENSES
		if(!empty($load_expenses))
		{
			$validation_icon = "/images/invalid_icon.png";
			$validation_alert = $validation_alert."\\n\\n This load as outstanding load expenses!";
			$is_good = false;
		}
		
		//VALIDATION ALERT IF LOAD IS GOOD
		if($is_good)
		{
			$validation_alert = "Good to go!";
		}
		
		$commission_status["validation_icon"] = $validation_icon;
		$commission_status["validation_alert"] = $validation_alert;
		$commission_status["is_good"] = $is_good;

		return $commission_status;
	}
	
	//GET TRUCK PERFORMANCE STATS
	function get_performance_stats($end_week_id)
	{
		
		//GET LOG ENTRY FOR END WEEK
		$where = null;
		$where["id"] = $end_week_id;
		$log_entry = db_select_log_entry($where);
		
		//GET END WEEK END LEG
		$where = null;
		$where["id"] = $log_entry["sync_entry_id"];
		$this_end_week_end_leg = db_select_log_entry($where);
	
		//GET PREVIOUS END WEEK
		$where = null;
		$where = " truck_id = ".$log_entry["truck_id"]." AND entry_type = 'End Week' AND entry_datetime = ( SELECT MAX(entry_datetime) FROM log_entry WHERE truck_id = ".$log_entry["truck_id"]." AND entry_type = 'End Week' AND entry_datetime < '".$log_entry["entry_datetime"]."')";
		$previous_end_week = db_select_log_entry($where);
		
		if(!empty($previous_end_week))
		{
			
			$stats = get_truck_end_week_stats($log_entry);
			
			$truck_gallons_used = $stats["total_gallons"] - $stats["total_reefer_gallons"];
			
			if($truck_gallons_used == 0)
			{
				$average_miles_per_gallon = 0;
			}
			else
			{
				$average_miles_per_gallon = $stats["total_odometer_miles"]/($truck_gallons_used);
			}
			
			$carrier_revenue = $stats["total_carrier_revenue"];
			
			//GET LOADS FOR WEEK
			$loads_for_week = get_loads_for_week($end_week_id);
			
			$i = 0;
			$total_revenue = 0;
			//$start = date('m/d/y H:i',strtotime($previous_end_week["entry_datetime"]))." ".$previous_end_week["city"].", ".$previous_end_week["state"];
			//$end = date('m/d/y H:i',strtotime($this_end_week_end_leg["entry_datetime"]))." ".$this_end_week_end_leg["city"].", ".$this_end_week_end_leg["state"];
			$start = date('m/d/y H:i',strtotime($previous_end_week["entry_datetime"]));
			$end = date('m/d/y H:i',strtotime($this_end_week_end_leg["entry_datetime"]));
			foreach($loads_for_week as $load_for_week)
			{
				$i++;
				if($i == 1)
				{
					//$start  = $load_for_week["pick"];
				}
				//$end = $load_for_week["drop"];
				
				$total_revenue = $total_revenue + $load_for_week["revenue"];
			}
			
			//CALC RAW PROFIT
			$raw_profit = $total_revenue - $carrier_revenue;
			
			//CALC BOOKING RATE
			
			//CALC CARRIER RATE
			if($stats["total_map_miles"] == 0)
			{
				$oor_percentage = 0;
				$rate_per_mile = 0;
				$carrier_rate = 0;
			}
			else
			{
				$oor_percentage = ($stats["total_odometer_miles"]-$stats["total_map_miles"])/$stats["total_map_miles"]*100;
				$rate_per_mile = round($total_revenue/$stats["total_map_miles"],2);
				$carrier_rate = round($carrier_revenue/$stats["total_map_miles"],2);
			}
			
			$truck_stats = null;
			$truck_stats["loads_for_week"] = $loads_for_week;
			$truck_stats["hours"] = $stats["total_truck_hours"];
			$truck_stats["map_miles"] = $stats["total_map_miles"];
			$truck_stats["total_bobtail_miles"] = $stats["total_bobtail_miles"];
			$truck_stats["total_deadhead_miles"] = $stats["total_deadhead_miles"];
			$truck_stats["total_light_miles"] = $stats["total_light_miles"];
			$truck_stats["total_loaded_miles"] = $stats["total_loaded_miles"];
			$truck_stats["total_reefer_miles"] = $stats["total_reefer_miles"];
			$truck_stats["odometer_miles"] = $stats["total_odometer_miles"];
			$truck_stats["standard_expenses"] = $stats["total_carrier_expenses"];
			$truck_stats["total_fuel_expense"] = $stats["total_fuel_expense"];
			$truck_stats["total_reefer_fuel_expense"] = $stats["total_reefer_fuel_expense"];
			$truck_stats["gallons_used"] = $truck_gallons_used;//JUST TRUCK GALLONS
			$truck_stats["oor"] = $oor_percentage;
			$truck_stats["mpg"] = $average_miles_per_gallon;
			$truck_stats["rate_per_mile"] = $rate_per_mile;//BOOKING RATE
			$truck_stats["carrier_rate"] = $carrier_rate;
			$truck_stats["total_revenue"] = $total_revenue;//BOOKING REVENUE
			$truck_stats["carrier_revenue"] = $carrier_revenue;//TRUCK'S REVENUE OFF OF GUARANTEED RATE
			$truck_stats["carrier_profit"] = $stats["total_carrier_profit"];
			$truck_stats["raw_profit"] = $raw_profit;
			$truck_stats["start"] = $start;
			$truck_stats["end"] = $end;
		}
		else
		{
			$truck_stats = null;
		}
		
		return $truck_stats;
		
	}
	
	function update_performance_review_with_new_calculations($performance_review)
	{
		date_default_timezone_set('America/Denver');
		$now_datetime = date("Y-m-d H:i:s");
		
		$truck_stats = get_performance_stats($performance_review["end_week_id"]);
		
		$update_pr = null;
		$update_pr["hours"] = $truck_stats["hours"];
		$update_pr["map_miles"] = $truck_stats["map_miles"];
		$update_pr["odometer_miles"] = $truck_stats["odometer_miles"];
		$update_pr["mpg"] = $truck_stats["mpg"];
		$update_pr["total_revenue"] = $truck_stats["total_revenue"];
		$update_pr["standard_expenses"] = $truck_stats["standard_expenses"];
		$update_pr["carrier_revenue"] = $truck_stats["carrier_revenue"];
		$update_pr["total_bobtail_miles"] = $truck_stats["total_bobtail_miles"];
		$update_pr["total_deadhead_miles"] = $truck_stats["total_deadhead_miles"];
		$update_pr["total_light_miles"] = $truck_stats["total_light_miles"];
		$update_pr["total_loaded_miles"] = $truck_stats["total_loaded_miles"];
		$update_pr["total_reefer_miles"] = $truck_stats["total_reefer_miles"];
		$update_pr["total_fuel_expense"] = $truck_stats["total_fuel_expense"];
		$update_pr["total_reefer_fuel_expense"] = $truck_stats["total_reefer_fuel_expense"];
		$update_pr["truck_gallons"] = $truck_stats["gallons_used"];//JUST TRUCK GALLONS
		$update_pr["oor_percentage"] = $truck_stats["oor"];
		$update_pr["booking_rate"] = $truck_stats["rate_per_mile"];//BOOKING RATE
		$update_pr["driver_rate"] = $truck_stats["carrier_rate"];
		$update_pr["driver_profit"] = $truck_stats["carrier_profit"];
		$update_pr["raw_profit"] = $truck_stats["raw_profit"];
		$update_pr["start_datetime"] = date('Y-m-d H:i:s',strtotime($truck_stats["start"]));
		$update_pr["end_datetime"] = date('Y-m-d H:i:s',strtotime($truck_stats["end"]));
		$update_pr["saved_datetime"] = $now_datetime;
		
		$where = null;
		$where["id"] = $performance_review["id"];
		db_update_performance_review($update_pr,$where);
		
		$where = null;
		$where["id"] = $performance_review["id"];
		return db_select_performance_review($where);
	}
	
	function get_loads_for_week($log_entry_id) //log_entry_id is for end_week
	{
		//GET LOG ENTRY FOR END WEEK
		$where = null;
		$where["id"] = $log_entry_id;
		$log_entry = db_select_log_entry($where);
		
		//GET END WEEK END LEG
		$where = null;
		$where["id"] = $log_entry["sync_entry_id"];
		$this_end_week_end_leg = db_select_log_entry($where);
	
		//GET PREVIOUS END WEEK
		$where = null;
		$where = " truck_id = ".$log_entry["truck_id"]." AND entry_type = 'End Week' AND entry_datetime = ( SELECT MAX(entry_datetime) FROM log_entry WHERE truck_id = ".$log_entry["truck_id"]." AND entry_type = 'End Week' AND entry_datetime < '".$log_entry["entry_datetime"]."')";
		$previous_end_week = db_select_log_entry($where);
		
		//GET PREVIOUS END WEEK END LEG
		$where = null;
		$where["id"] = $previous_end_week["sync_entry_id"];
		$previous_end_week_end_leg = db_select_log_entry($where);
	
		//GET LOADS FOR THIS TRUCK THIS WEEK
		$loads = array();
		$values = array();
		$where_sql = null;
		$where_sql = " WHERE leg.log_entry_id = log_entry.id AND load.id = leg.allocated_load_id AND leg.truck_id = ".$log_entry["truck_id"]." AND log_entry.entry_datetime > '".$previous_end_week_end_leg["entry_datetime"]."'  AND log_entry.entry_datetime <= '".$this_end_week_end_leg["entry_datetime"]."'";
		$sql = "SELECT DISTINCT(leg.allocated_load_id) FROM `leg`, `log_entry`, `load` ".$where_sql." ORDER BY load.final_drop_datetime";
		$CI =& get_instance();
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$performance_reviews = array();
		foreach ($query->result() as $row)
		{
			$load_id = $row->allocated_load_id;
			
			$where = null;
			$where["id"] = $load_id;
			$load = db_select_load($where);
			
			$loads[] = $load;
		}
		
		$pick = "";
		$drop = "";
		$loads_for_week = array();
		$total_revenue = 0;
		$load_i = 0;
		foreach($loads as $load)
		{
			//echo "Load ".$load["id"]."<br>";
			
			//GET ALL LEGS FOR THIS LOAD
			$where = null;
			$where = " leg.allocated_load_id = ".$load["id"]." AND leg.truck_id = ".$log_entry["truck_id"]." AND log_entry.entry_datetime > '".$previous_end_week_end_leg["entry_datetime"]."'  AND log_entry.entry_datetime <= '".$this_end_week_end_leg["entry_datetime"]."'";
			$legs_for_load = db_select_legs($where,"log_entry.entry_datetime");
			
			//GET TOTAL MILES DRIVEN FOR THE LOAD THIS WEEK
			$total_miles = 0;
			$leg_i = 0;
			foreach($legs_for_load as $leg)
			{
				$total_miles = $total_miles + $leg["map_miles"];
				//echo "miles on leg ".$leg["map_miles"]."<br>";
				
				//GET LOG ENTRY FOR THIS LEG'S END LEG
				$where = null;
				$where["id"] = $leg["log_entry_id"];
				$entry = db_select_log_entry($where);

				
					$where = null;
					$where = " entry_type = 'End Leg' AND truck_id = ".$entry["truck_id"]." AND entry_datetime < '".$entry["entry_datetime"]."' ";
					$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
					
				//echo $leg["id"]." ".$begin_leg_entry["entry_datetime"]." to ".$entry["entry_datetime"]."<br>";

				/**
				if($leg_i == 0)
				{
					$pick = date('m/d H:i',strtotime($begin_leg_entry["entry_datetime"]))." ".$begin_leg_entry["city"].", ".$begin_leg_entry["state"];
				}
				$drop = date('m/d H:i',strtotime($entry["entry_datetime"]))." ".$entry["city"].", ".$entry["state"];
				**/
				
				//THIS NEED TO BE REDONE AS GOALPOINTS
				foreach($load["load_picks"] as $pick)
				{
					$pick = date('m/d H:i',strtotime($pick["in_time"]))." ".$pick["stop"]["city"].", ".$pick["stop"]["state"];
					break;
				}
				foreach($load["load_drops"] as $drop)
				{
					$drop = date('m/d H:i',strtotime($drop["out_time"]))." ".$drop["stop"]["city"].", ".$drop["stop"]["state"];
				}
			
				$leg_i++;
			}
			//echo $total_miles." miles of ".$load["map_miles"]."<br>";
			
			//GET RATE AND RATE SOURCE FOR LOAD
			if(empty($load["amount_funded"]))
			{
				$rate = $load["expected_revenue"];
				$rate_source = "expected_revenue";
			}
			else
			{
				$rate = $load["amount_funded"] + $load["financing_cost"];
				$rate_source = "amount_billed";
			}
			
			if($load["map_miles"] != 0)
			{
				//GET MAP MILES
				$map_miles = $load["map_miles"];
				$miles_source = "map_miles";
			}
			else
			{
				if(empty($load["expected_miles"]))
				{
					$map_miles = 0;
				}
				else
				{
					$map_miles = round($load["expected_miles"],2);
					//$map_miles = 1000;
				}
				
				$miles_source = "expected_miles";
			}
			
			if($map_miles == 0)
			{
				$percent = 0;
				$rate_per_mile = 0;
			}
			else
			{
				//CALC PERCENT OF LOAD DONE THIS WEEK
				$percent = round($total_miles/$map_miles,4);
				//echo $load["map_miles"]." / ".$total_miles."<br>";
				
				//CALC RATE_PER_MILES
				$rate_per_mile = round($rate/$map_miles,2);
			}
			
			
			//CALC REVENUE FOR THIS LOAD DONE THIS WEEK
			$revenue  = round($rate * $percent,2);
			
			$load_on_week = null;
			$load_on_week["load_number"] = $load["customer_load_number"];
			$load_on_week["broker_name"] = $load["broker"]["customer_name"];
			$load_on_week["pick"] = $pick;
			$load_on_week["drop"] = $drop;
			$load_on_week["map_miles"] = $map_miles;
			$load_on_week["miles_source"] = $miles_source;
			$load_on_week["rate"] = $rate;
			$load_on_week["rate_source"] = $rate_source;
			$load_on_week["rate_per_mile"] = $rate_per_mile;
			$load_on_week["percentage_on_week"] = $percent;
			$load_on_week["revenue"] = $revenue;
			$load_on_week["billing_status"] = $load["billing_status"];
			
			
			$loads_for_week[] = $load_on_week;
			
			//echo ($percent*100)."% of ".$rate." is $".$revenue."<br>";
			
			$load_i++;
		}
		
		return $loads_for_week;
	}
	
	//GET DRIVERS END WEEK STATS FOR GIVEN LOG ENTRY AND DRIVER_ID
	function get_driver_end_week_stats($log_entry,$driver_id)
	{
		$is_ready_to_approve = true;
	
		$stats = null;
		$status = "Ready";
		
		//GET PREVIOUS END WEEK FOR THIS DRIVER
		$where = null;
		$where = " (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_type = 'End Week' AND entry_datetime = ( SELECT MAX(entry_datetime) FROM log_entry WHERE (main_driver_id = ".$driver_id." OR codriver_id = ".$driver_id.") AND entry_type = 'End Week' AND entry_datetime < '".$log_entry["entry_datetime"]."')";
		$previous_end_week = db_select_log_entry($where);
		
		//GET ALL MAP EVENTS FOR THE WEEK FOR THIS TRUCK
		$where = null;
		$where = " (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime <= '".$log_entry["entry_datetime"]."'  AND entry_datetime >= '".$previous_end_week["entry_datetime"]."' AND (entry_type = 'Pick' OR entry_type = 'Drop' OR entry_type = 'Checkpoint' OR entry_type = 'Driver In' OR entry_type = 'Driver Out' OR entry_type = 'Pick Trailer' OR entry_type = 'Drop Trailer' OR entry_type = 'End Week') ";
		$map_events = db_select_log_entrys($where,'entry_datetime');
		
		//echo "hello";

		//GET MAP INFO
		$stats["map_info"] = get_map_info($map_events);
		
		//GET END LEG ENTRY FOR PREVIOUS END WEEK
		if(!empty($previous_end_week["sync_entry_id"]))
		{
			$where = null;
			$where["id"] = $previous_end_week["sync_entry_id"];
			$previous_end_week_end_leg = db_select_log_entry($where);
		}
		else
		{
			$previous_end_week_end_leg = null;
			$status = "Not Ready";
		}

		//GET END LEG ENTRY FOR THIS LOG ENTRY
		if(!empty($log_entry["sync_entry_id"]))
		{
			$where = null;
			$where["id"] = $log_entry["sync_entry_id"];
			$this_end_week_end_leg = db_select_log_entry($where);
		}
		else
		{
			$status = "Not Ready";
		}
		
		//GET THIS SETTLEMENT
		$where = null;
		$where["end_week_id"] = $log_entry["id"];
		$where["client_id"] = $driver_id;
		$settlement = db_select_settlement($where);

		$client_expenses = null;
		$damage_ce = null;
		$equipment_ce = null;
		$total_client_expenses = 0;
		$total_statement_credits = 0;
		$statement_credits = null;
		
		
		if(!empty($settlement))
		{
			//GET ALL CLIENT EXPENSES FOR THIS SETTLEMENT
			$where = null;
			//$where["settlement_id"] = $settlement["id"];
			//$where = " settlement_id = ".$settlement["id"]." AND (paid_datetime IS NULL OR settlement_id = ".$settlement["id"].") ";
			$where = " settlement_id = ".$settlement["id"]." AND paid_datetime IS NULL "; // THIS SCREWS IT UP WHEN THE EXPENSE HAS BEEN MARKED AS PAID
			$client_expenses = db_select_client_expenses($where);
			
			if(!empty($client_expenses))
			{
				foreach($client_expenses as $client_expense)
				{
					$total_client_expenses = $total_client_expenses + $client_expense["expense_amount"];
					if($client_expense["is_reimbursable"] == "Yes")
					{
						$is_ready_to_approve = false;
					}
				}
			}
			
			//GET DAMAGE CLIENT EXPENSE
			$where = null;
			$where["settlement_id"] = $settlement["id"];
			$where["category"] = "Damage Adjustment";
			$damage_ce = db_select_client_expense($where);
			
			//GET EQUIPENT CLIENT EXPENSE
			$where = null;
			$where["settlement_id"] = $settlement["id"];
			$where["category"] = "Driver Equipment";
			$equipment_ce = db_select_client_expense($where);
			
			//GET ALL STATEMENT CREDITS FOR THIS SETTLEMENT
			$where = null;
			$where["settlement_id"] = $settlement["id"];
			$statement_credits = db_select_statement_credits($where);
			
			if(!empty($statement_credits))
			{
				foreach($statement_credits as $statement_credit)
				{
					$total_statement_credits = $total_statement_credits + $statement_credit["credit_amount"];
				}
			}
		}
		
		$total_map_miles = 0;
		$total_gallons = 0;
		$total_reefer_gallons = 0;
		$total_truck_hours = 0;
		$total_odometer_miles = 0;
		$leg_calcs = array();
		$total_carrier_revenue = 0;
		$total_carrier_expenses = 0;
		$total_carrier_profit = 0;
		$total_fuel_expense = 0;
		$total_reefer_fuel_expense = 0;
		$total_truck_rental_expense = 0;
		$total_truck_mileage_expense = 0;
		$total_trailer_rental_expense = 0;
		$total_trailer_mileage_expense = 0;
		$total_insurance_expense = 0;
		$total_authority_expense = 0;
		$total_compliance_consulting_expense = 0;
		$total_membership_expense = 0;
		$total_factoring_expense = 0;
		$total_bad_debt_expense = 0;
		$total_damage_expense = 0;
		$total_profit_share = 0;
		$total_damage_share = 0;
		$total_reefer_miles = 0;
		$total_loaded_miles = 0;
		$total_bobtail_miles = 0;
		$total_deadhead_miles = 0;
		$total_light_miles = 0;
		$total_reefer_rev = 0;
		$total_loaded_rev = 0;
		$total_bobtail_rev = 0;
		$total_deadhead_rev = 0;
		$total_light_rev = 0;
		$total_in_truck_hours = 0;
		$average_fuel_price = 0;
		if(!empty($previous_end_week_end_leg) && !empty($this_end_week_end_leg))
		{
			//GET ALL LEGS FOR THIS TRUCK THIS WEEK
			$where = null;
			$where = " (leg.main_driver_id = ".$driver_id." OR leg.codriver_id = ".$driver_id.") AND log_entry.entry_datetime > '".$previous_end_week_end_leg["entry_datetime"]."'  AND log_entry.entry_datetime <= '".$this_end_week_end_leg["entry_datetime"]."'";
			$legs = db_select_legs($where,"log_entry.entry_datetime");
			//echo $where;
			
			if(!empty($legs))
			{
				//ADD UP HOURS AND ODOMETER MILES FOR ALL LEGS
				foreach($legs as $leg)
				{
					//echo "Leg ".$leg["id"]."<br>";
					
					$total_truck_hours = $total_truck_hours + $leg["hours"];
					$total_odometer_miles = $total_odometer_miles + $leg["odometer_miles"];
					$leg_calc = get_leg_calculations($leg["id"]);
					$leg_calcs[] = $leg_calc;
					
					$total_map_miles = $total_map_miles + $leg["map_miles"];
					$total_gallons = $total_gallons + $leg_calc["gallons_used"]; //truck and reefer combined
					$total_reefer_gallons = $total_reefer_gallons + $leg_calc["reefer_gallons_used"];
					
					$total_carrier_revenue = $total_carrier_revenue + $leg_calc["carrier_revenue"];
					$total_carrier_expenses = $total_carrier_expenses + $leg_calc["carrier_expense"];
					$total_carrier_profit = $total_carrier_profit + $leg_calc["carrier_profit"];
					
					$total_fuel_expense = $total_fuel_expense + $leg_calc["fuel_expense"]; //truck and reefer combined
					$total_reefer_fuel_expense = $total_reefer_fuel_expense + $leg_calc["reefer_fuel_expense"];
					$total_truck_rental_expense = $total_truck_rental_expense + $leg_calc["truck_rent"];
					$total_truck_mileage_expense = $total_truck_mileage_expense + $leg_calc["truck_mileage"];
					$total_trailer_rental_expense = $total_trailer_rental_expense + $leg_calc["trailer_rent"];
					$total_trailer_mileage_expense = $total_trailer_mileage_expense + $leg_calc["trailer_mileage"];
					$total_insurance_expense = $total_insurance_expense + $leg_calc["insurance_expense"];
					$total_authority_expense = $total_authority_expense + $leg_calc["authority_expense"];
					$total_compliance_consulting_expense = $total_compliance_consulting_expense + $leg_calc["compliance_consulting_expense"];
					$total_membership_expense = $total_membership_expense + $leg_calc["membership_expense"];
					$total_factoring_expense = $total_factoring_expense + $leg_calc["factoring"];
					$total_bad_debt_expense = $total_bad_debt_expense + $leg_calc["bad_debt"];
					$total_damage_expense = $total_damage_expense + $leg_calc["damage_expense"];
					
					//DETERMINE PERCENTAGE SPLIT
					if($leg["main_driver_id"] == $driver_id)
					{
						$profit_split = $leg["main_driver_split"]/100;
					}
					else if($leg["codriver_id"] == $driver_id)
					{
						$profit_split = $leg["codriver_split"]/100;
					}

					$total_profit_share = $total_profit_share + ($leg_calc["carrier_profit"]*$profit_split);
					$total_damage_share = $total_damage_share + ($leg_calc["damage_expense"]*$profit_split);

					//echo $leg["id"]." ".$total_profit_share."<br>";
					
					
					//GET TOTAL MILES AND REVENUE BY TYPE
					if($leg["rate_type"] == "Reefer")
					{
						$total_reefer_miles = $total_reefer_miles + $leg["map_miles"];
						$total_reefer_rev = $total_reefer_rev + $leg_calc["carrier_revenue"];
					}
					elseif($leg["rate_type"] == "Loaded")
					{
						$total_loaded_miles = $total_loaded_miles + $leg["map_miles"];
						$total_loaded_rev = $total_loaded_rev + $leg_calc["carrier_revenue"];
					}
					elseif($leg["rate_type"] == "Bobtail")
					{
						$total_bobtail_miles = $total_bobtail_miles + $leg["map_miles"];
						$total_bobtail_rev = $total_bobtail_rev + $leg_calc["carrier_revenue"];
					}
					elseif($leg["rate_type"] == "Dead Head")
					{
						$total_deadhead_miles = $total_deadhead_miles + $leg["map_miles"];
						$total_deadhead_rev = $total_deadhead_rev + $leg_calc["carrier_revenue"];
					}
					elseif($leg["rate_type"] == "Light Freight")
					{
						$total_light_miles = $total_light_miles + $leg["map_miles"];
						$total_light_rev = $total_light_rev + $leg_calc["carrier_revenue"];
					}
					
					//CALCULATE TOTAL IN-TRUCK HOURS
					if(!empty($leg["truck_id"]))
					{
						$total_in_truck_hours = $total_in_truck_hours + $leg["hours"];
					}
				}
				
				$average_fuel_price = 0;
				if($total_gallons != 0)
				{
					$average_fuel_price = $total_fuel_expense/$total_gallons;
				}
			}
		}
		else
		{
			$status = "Not Ready";
		}
		
		$stats["is_ready_to_approve"] = $is_ready_to_approve;
		$stats["damage_adjustment_expense"] = $damage_ce;
		$stats["driver_equipment_expense"] = $equipment_ce;
		
		$stats["statement_amount"] = $total_profit_share - $total_client_expenses + $total_statement_credits;
		
		$stats["status"] = $status;
		$stats["leg_calcs"] = $leg_calcs;
		
		$stats["previous_end_week_end_leg"] = $previous_end_week_end_leg;
		$stats["this_end_week_end_leg"] = $this_end_week_end_leg;
		
		$stats["client_expenses"] = $client_expenses;
		$stats["statement_credits"] = $statement_credits;
		$stats["total_map_miles"] = $total_map_miles;
		$stats["total_odometer_miles"] = $total_odometer_miles;
		$stats["total_truck_hours"] = $total_truck_hours;
		$stats["total_gallons"] = $total_gallons;
		$stats["total_reefer_gallons"] = $total_reefer_gallons;
		$stats["total_carrier_revenue"] = $total_carrier_revenue;
		$stats["total_carrier_expenses"] = $total_carrier_expenses;
		$stats["total_carrier_profit"] = $total_carrier_profit;
		$stats["total_fuel_expense"] = $total_fuel_expense;
		$stats["total_reefer_fuel_expense"] = $total_reefer_fuel_expense;
		$stats["total_truck_rental_expense"] = $total_truck_rental_expense;
		$stats["total_truck_mileage_expense"] = $total_truck_mileage_expense;
		$stats["total_trailer_rental_expense"] = $total_trailer_rental_expense;
		$stats["total_trailer_mileage_expense"] = $total_trailer_mileage_expense;
		$stats["total_insurance_expense"] = $total_insurance_expense;
		$stats["total_authority_expense"] = $total_authority_expense;
		$stats["total_compliance_consulting_expense"] = $total_compliance_consulting_expense;
		$stats["total_membership_expense"] = $total_membership_expense;
		$stats["total_factoring_expense"] = $total_factoring_expense;
		$stats["total_bad_debt_expense"] = $total_bad_debt_expense;
		$stats["total_damage_expense"] = $total_damage_expense;
		$stats["total_client_expenses"] = $total_client_expenses;
		$stats["total_statement_credits"] = $total_statement_credits;
		$stats["total_profit_share"] = $total_profit_share;
		$stats["total_damage_share"] = $total_damage_share;
		$stats["total_reefer_miles"] = $total_reefer_miles;
		$stats["total_loaded_miles"] = $total_loaded_miles;
		$stats["total_bobtail_miles"] = $total_bobtail_miles;
		$stats["total_deadhead_miles"] = $total_deadhead_miles;
		$stats["total_light_miles"] = $total_light_miles;
		$stats["total_reefer_rev"] = $total_reefer_rev;
		$stats["total_loaded_rev"] = $total_loaded_rev;
		$stats["total_bobtail_rev"] = $total_bobtail_rev;
		$stats["total_deadhead_rev"] = $total_deadhead_rev;
		$stats["total_light_rev"] = $total_light_rev;
		$stats["total_in_truck_hours"] = $total_in_truck_hours;
		$stats["average_fuel_price"] = $average_fuel_price;
		
		$stats["settlement"] = $settlement;
		
		
		return $stats;
	}
	
	//GET STATS FOR TRUCK PERFORMANCE REPORT - TRUCK STATEMENT
	function get_truck_end_week_stats($log_entry)
	{
		$is_ready_to_approve = true;
	
		$stats = null;
		$status = "Ready";
		
		$truck_id = $log_entry["truck_id"];
		
		//GET PREVIOUS END WEEK FOR THIS DRIVER
		$where = null;
		$where = " (log_entry.truck_id = ".$truck_id.") AND entry_type = 'End Week' AND entry_datetime = ( SELECT MAX(entry_datetime) FROM log_entry WHERE (truck_id = ".$truck_id.") AND entry_type = 'End Week' AND entry_datetime < '".$log_entry["entry_datetime"]."')";
		$previous_end_week = db_select_log_entry($where);
		
		//GET ALL MAP EVENTS FOR THE WEEK FOR THIS TRUCK
		$where = null;
		$where = " (log_entry.truck_id = ".$truck_id.") AND entry_datetime <= '".$log_entry["entry_datetime"]."'  AND entry_datetime >= '".$previous_end_week["entry_datetime"]."' AND (entry_type = 'Pick' OR entry_type = 'Drop' OR entry_type = 'Checkpoint' OR entry_type = 'Driver In' OR entry_type = 'Driver Out' OR entry_type = 'Pick Trailer' OR entry_type = 'Drop Trailer' OR entry_type = 'End Week') ";
		$map_events = db_select_log_entrys($where,'entry_datetime');

		//echo "hello";

		//GET MAP INFO
		$stats["map_info"] = get_map_info($map_events);
		
		//GET END LEG ENTRY FOR PREVIOUS END WEEK
		if(!empty($previous_end_week["sync_entry_id"]))
		{
			$where = null;
			$where["id"] = $previous_end_week["sync_entry_id"];
			$previous_end_week_end_leg = db_select_log_entry($where);
		}
		else
		{
			$previous_end_week_end_leg = null;
			$status = "Not Ready";
		}

		//GET END LEG ENTRY FOR THIS LOG ENTRY
		if(!empty($log_entry["sync_entry_id"]))
		{
			$where = null;
			$where["id"] = $log_entry["sync_entry_id"];
			$this_end_week_end_leg = db_select_log_entry($where);
		}
		else
		{
			$status = "Not Ready";
		}
		
		
		$total_map_miles = 0;
		$total_gallons = 0;
		$total_reefer_gallons = 0;
		$total_truck_hours = 0;
		$total_odometer_miles = 0;
		$leg_calcs = array();
		$total_carrier_revenue = 0;
		$total_carrier_expenses = 0;
		$total_carrier_profit = 0;
		$total_fuel_expense = 0;
		$total_reefer_fuel_expense = 0;
		$total_truck_rental_expense = 0;
		$total_truck_mileage_expense = 0;
		$total_trailer_rental_expense = 0;
		$total_trailer_mileage_expense = 0;
		$total_insurance_expense = 0;
		$total_authority_expense = 0;
		$total_compliance_consulting_expense = 0;
		$total_membership_expense = 0;
		$total_factoring_expense = 0;
		$total_bad_debt_expense = 0;
		$total_damage_expense = 0;
		$total_profit_share = 0;
		$total_damage_share = 0;
		$total_reefer_miles = 0;
		$total_loaded_miles = 0;
		$total_bobtail_miles = 0;
		$total_deadhead_miles = 0;
		$total_light_miles = 0;
		$total_reefer_rev = 0;
		$total_loaded_rev = 0;
		$total_bobtail_rev = 0;
		$total_deadhead_rev = 0;
		$total_light_rev = 0;
		$total_in_truck_hours = 0;
		$total_shop_hours = 0;
		$average_fuel_price = 0;
		
		if(!empty($previous_end_week_end_leg) && !empty($this_end_week_end_leg))
		{
			//GET ALL LEGS FOR THIS TRUCK THIS WEEK
			$where = null;
			$where = " (leg.truck_id = ".$truck_id.") AND log_entry.entry_datetime > '".$previous_end_week_end_leg["entry_datetime"]."'  AND log_entry.entry_datetime <= '".$this_end_week_end_leg["entry_datetime"]."'";
			$legs = db_select_legs($where,"log_entry.entry_datetime");
			//echo $where;
			
			if(!empty($legs))
			{
				//ADD UP HOURS AND ODOMETER MILES FOR ALL LEGS
				foreach($legs as $leg)
				{
					//echo "Leg ".$leg["id"]."<br>";
					
					$total_truck_hours = $total_truck_hours + $leg["hours"];
					$total_odometer_miles = $total_odometer_miles + $leg["odometer_miles"];
					$leg_calc = get_leg_calculations($leg["id"]);
					$leg_calcs[] = $leg_calc;
					
					$total_map_miles = $total_map_miles + $leg["map_miles"];
					$total_gallons = $total_gallons + $leg_calc["gallons_used"]; //truck and reefer combined
					$total_reefer_gallons = $total_reefer_gallons + $leg_calc["reefer_gallons_used"];
					
					$total_carrier_revenue = $total_carrier_revenue + $leg_calc["carrier_revenue"];
					$total_carrier_expenses = $total_carrier_expenses + $leg_calc["carrier_expense"];
					$total_carrier_profit = $total_carrier_profit + $leg_calc["carrier_profit"];
					
					$total_fuel_expense = $total_fuel_expense + $leg_calc["fuel_expense"]; //truck and reefer combined
					$total_reefer_fuel_expense = $total_reefer_fuel_expense + $leg_calc["reefer_fuel_expense"];
					$total_truck_rental_expense = $total_truck_rental_expense + $leg_calc["truck_rent"];
					$total_truck_mileage_expense = $total_truck_mileage_expense + $leg_calc["truck_mileage"];
					$total_trailer_rental_expense = $total_trailer_rental_expense + $leg_calc["trailer_rent"];
					$total_trailer_mileage_expense = $total_trailer_mileage_expense + $leg_calc["trailer_mileage"];
					$total_insurance_expense = $total_insurance_expense + $leg_calc["insurance_expense"];
					$total_authority_expense = $total_authority_expense + $leg_calc["authority_expense"];
					$total_compliance_consulting_expense = $total_compliance_consulting_expense + $leg_calc["compliance_consulting_expense"];
					$total_membership_expense = $total_membership_expense + $leg_calc["membership_expense"];
					$total_factoring_expense = $total_factoring_expense + $leg_calc["factoring"];
					$total_bad_debt_expense = $total_bad_debt_expense + $leg_calc["bad_debt"];
					$total_damage_expense = $total_damage_expense + $leg_calc["damage_expense"];
					
					
					//GET TOTAL MILES AND REVENUE BY TYPE
					if($leg["rate_type"] == "Reefer")
					{
						$total_reefer_miles = $total_reefer_miles + $leg["map_miles"];
						$total_reefer_rev = $total_reefer_rev + $leg_calc["carrier_revenue"];
					}
					elseif($leg["rate_type"] == "Loaded")
					{
						$total_loaded_miles = $total_loaded_miles + $leg["map_miles"];
						$total_loaded_rev = $total_loaded_rev + $leg_calc["carrier_revenue"];
					}
					elseif($leg["rate_type"] == "Bobtail")
					{
						$total_bobtail_miles = $total_bobtail_miles + $leg["map_miles"];
						$total_bobtail_rev = $total_bobtail_rev + $leg_calc["carrier_revenue"];
					}
					elseif($leg["rate_type"] == "Dead Head")
					{
						$total_deadhead_miles = $total_deadhead_miles + $leg["map_miles"];
						$total_deadhead_rev = $total_deadhead_rev + $leg_calc["carrier_revenue"];
					}
					elseif($leg["rate_type"] == "Light Freight")
					{
						$total_light_miles = $total_light_miles + $leg["map_miles"];
						$total_light_rev = $total_light_rev + $leg_calc["carrier_revenue"];
					}
					elseif($leg["rate_type"] == "In Shop")
					{
						$total_shop_hours = $total_shop_hours + $leg["hours"];
						//test
						$total_loaded_miles = $total_loaded_miles + $leg["map_miles"];
					}
					else
					{
						//echo "<script>alert('unknown rate type on leg_id ".$leg["id"]." ".__LINE__." ".__FILE__."')</script>";
						echo $leg["id"];
					}
					
					//CALCULATE TOTAL IN-TRUCK HOURS
					if(!empty($leg["truck_id"]))
					{
						$total_in_truck_hours = $total_in_truck_hours + $leg["hours"];
						
					}
				}
				
				$average_fuel_price = 0;
				if($total_gallons != 0)
				{
					$average_fuel_price = $total_fuel_expense/$total_gallons;
				}
			}
		}
		else
		{
			$status = "Not Ready";
		}
		
		$stats["is_ready_to_approve"] = $is_ready_to_approve;
		
		$stats["status"] = $status;
		$stats["leg_calcs"] = $leg_calcs;
		
		$stats["previous_end_week_end_leg"] = $previous_end_week_end_leg;
		$stats["this_end_week_end_leg"] = $this_end_week_end_leg;
		
		$stats["total_map_miles"] = $total_map_miles;
		$stats["total_odometer_miles"] = $total_odometer_miles;
		$stats["total_truck_hours"] = $total_truck_hours;
		$stats["total_gallons"] = $total_gallons;
		$stats["total_reefer_gallons"] = $total_reefer_gallons;
		$stats["total_carrier_revenue"] = $total_carrier_revenue;
		$stats["total_carrier_expenses"] = $total_carrier_expenses;
		$stats["total_carrier_profit"] = $total_carrier_profit;
		$stats["total_fuel_expense"] = $total_fuel_expense;
		$stats["total_reefer_fuel_expense"] = $total_reefer_fuel_expense;
		$stats["total_truck_rental_expense"] = $total_truck_rental_expense;
		$stats["total_truck_mileage_expense"] = $total_truck_mileage_expense;
		$stats["total_trailer_rental_expense"] = $total_trailer_rental_expense;
		$stats["total_trailer_mileage_expense"] = $total_trailer_mileage_expense;
		$stats["total_damage_expense"] = $total_damage_expense;
		$stats["total_insurance_expense"] = $total_insurance_expense;
		$stats["total_authority_expense"] = $total_authority_expense;
		$stats["total_compliance_consulting_expense"] = $total_compliance_consulting_expense;
		$stats["total_membership_expense"] = $total_membership_expense;
		$stats["total_factoring_expense"] = $total_factoring_expense;
		$stats["total_bad_debt_expense"] = $total_bad_debt_expense;
		$stats["total_reefer_miles"] = $total_reefer_miles;
		$stats["total_loaded_miles"] = $total_loaded_miles;
		$stats["total_bobtail_miles"] = $total_bobtail_miles;
		$stats["total_deadhead_miles"] = $total_deadhead_miles;
		$stats["total_light_miles"] = $total_light_miles;
		$stats["total_reefer_rev"] = $total_reefer_rev;
		$stats["total_loaded_rev"] = $total_loaded_rev;
		$stats["total_bobtail_rev"] = $total_bobtail_rev;
		$stats["total_deadhead_rev"] = $total_deadhead_rev;
		$stats["total_light_rev"] = $total_light_rev;
		$stats["total_in_truck_hours"] = $total_in_truck_hours;
		$stats["total_shop_hours"] = $total_shop_hours;
		$stats["average_fuel_price"] = $average_fuel_price;
		
		
		
		return $stats;
	}
	
	//RETURNS AN AVERAGE FUEL PRICE WITH A GIVEN DATE RANGE
	function calculate_average_fuel_price($start_date, $end_date)
	{
		$where = null;
		$where = 	'datetime > "'.$start_date.'" 
					AND datetime < "'.$end_date.'"
					AND fuel_avg IS NOT NULL';
		$fuel_averages = db_select_fuel_averages($where);
		
		$total_count = 0;
		$total_fuel_prices = 0;
		if(!empty($fuel_averages))
		{
			foreach($fuel_averages as $fuel_avg)
			{
				$total_count++;
				$total_fuel_prices = $total_fuel_prices + $fuel_avg["fuel_avg"];
			}
		}
		
		if($total_count != 0)
		{
			return $total_fuel_prices/$total_count;
		}
		else
		{
			return 0;
		}
		
	}
	
	//ASSIGN ALL UNASSIGNED CLIENT EXPENSES FOR THIS STATEMENT
	function assign_client_expenses($log_entry_id,$driver_id)
	{
		scan_for_old_receipts($driver_id);
	
		//GET THIS SETTLEMENT
		$where = null;
		$where["end_week_id"] = $log_entry_id;
		$where["client_id"] = $driver_id;
		$settlement = db_select_settlement($where);
	
		//GET THIS END WEEK LOG ENTRY
		$where = null;
		$where["id"] = $log_entry_id;
		$this_end_week = db_select_log_entry($where);
	
		//GET PREVIOUS END WEEK FOR THIS DRIVER
		$where = null;
		$where = " (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_type = 'End Week' AND entry_datetime = ( SELECT MAX(entry_datetime) FROM log_entry WHERE (main_driver_id = ".$driver_id." OR codriver_id = ".$driver_id.") AND entry_type = 'End Week' AND entry_datetime < '".$this_end_week["entry_datetime"]."')";
		$previous_end_week = db_select_log_entry($where);
		
		//GET ALL UNASSIGNED CLIENT EXPENSES BETWEEN END WEEKS
		$where = null;
		$where = " paid_datetime IS NULL AND client_id = ".$driver_id." AND expense_datetime > '".$previous_end_week["entry_datetime"]."' AND expense_datetime <= '".$this_end_week["entry_datetime"]."' ";
		$client_expenses = db_select_client_expenses($where);
		
		if(!empty($client_expenses))
		{
			foreach($client_expenses as $client_expense)
			{
				//echo $client_expense["id"]."<br>";
				
				//UPDATE CLIENT EXPENSE WITH SETTTLEMENT ID
				$update_ce = null;
				$update_ce["settlement_id"] = $settlement["id"];
				
				$where = null;
				$where["id"] = $client_expense["id"];
				db_update_client_expense($update_ce,$where);
			}
		}
	}
	
	//SCAN FOR OLD RECEIPTS IN DRIVER'S BAHA ACCOUNT
	function scan_for_old_receipts($client_id)
	{

	/**
		//GET CLIENT
		$where = null;
		$where["id"] = $client_id;
		$client = db_select_client($where);
		
		//GET USER AND TIME
		$CI =& get_instance();
		$recorder_id = $CI->session->userdata('person_id');
		date_default_timezone_set('America/Denver');
		$entry_datetime = date("Y-m-d H:i:s");
	
		//GET DRIVER'S BAHA ACCOUNT
		$where = null;
		$where["category"] = "BAHA";
		$where["company_id"] = $client["company_id"];
		$baha_account = db_select_account($where);
		
		//GET DATETIME RIGHT NOW
		date_default_timezone_set('America/Denver');
		$entry_datetime = date("Y-m-d H:i:s");
		
		//GET TIME 96 HOURS AGO
		$hours = 96;
		$receipt_deadline = date("Y-m-d H:i:s", time() - 3600 * $hours);
		
		//GET ALL ACCOUNT ENTRIES IN BAHA ACCOUNT THAT ARE OLD AND ARE REIMBURSABLE
		$where = null;
		$where = " account_id = ".$baha_account["id"]."  AND is_reimbursable = 'Yes' AND reimbursement_datetime IS NULL AND entry_datetime < '".$receipt_deadline."' ";
		$old_advances = db_select_account_entrys($where,"entry_datetime DESC");
		
		if(!empty($old_advances))
		{
			foreach($old_advances as $advance_entry)
			{
				//echo $advance_entry["entry_description"]." ".$advance_entry["entry_amount"]."<br>";
				
				//CREATE ENTRY TO EITHER THE BAHA ACCOUNT OR THE DRIVER'S PAY ACCOUNT (OOP)
				$reimburse_advance["account_id"] = $baha_account["id"];
				$reimburse_advance["recorder_id"] = $recorder_id;
				$reimburse_advance["entry_datetime"] = $entry_datetime;
				$reimburse_advance["debit_credit"] = "Credit";
				$reimburse_advance["entry_amount"] = $advance_entry["entry_amount"];
				$reimburse_advance["entry_description"] = "BA Allocated | Credit for BA issued ".date("m/d/y H:i",strtotime($advance_entry["entry_datetime"]))." | Missed the 48 hour deadline";
				db_insert_account_entry($reimburse_advance);
			
				//MARK ADVANCE AS REIMBURSED
				$where = null;
				$where["id"] = $advance_entry["id"];
				$updated_entry["reimbursement_datetime"] = $entry_datetime;
				$updated_entry["entry_description"] = $advance_entry["entry_description"]." | "."REIMBURSED ".date("m/d/y H:i",strtotime($entry_datetime));
				db_update_account_entry($updated_entry, $where);
			
				//CREATE THE DEBIT ENTRY IN THE DRIVER'S PAY ACCOUNT FOR MISSING RECEIPT FEE
				$sys_gen_desc = "Missing Receipt | Fee for no receipt on $".$advance_entry["entry_amount"]." ".$advance_entry["entry_description"];
				
				$fee["account_id"] = $client["main_account"];
				$fee["recorder_id"] = $recorder_id;
				$fee["entry_datetime"] = $entry_datetime;
				$fee["entry_type"] = "Client Expense";
				$fee["debit_credit"] = "Debit";
				$fee["entry_amount"] = round($advance_entry["entry_amount"]*0.15,2);
				$fee["entry_description"] = "$sys_gen_desc | Missed the 48 hour deadline";
				db_insert_account_entry($fee);
				
				//CREATE CLIENT EXPENSE
				$client_expense = null;
				$client_expense["client_id"] = $client["id"];
				$client_expense["expense_datetime"] = $advance_entry["entry_datetime"];
				$client_expense["category"] = "Missing Receipt";
				$client_expense["description"] = "Missing Receipt | ".$advance_entry["entry_description"]." | Missed the 48 hour deadline";
				$client_expense["expense_amount"] = $advance_entry["entry_amount"];
				
				db_insert_client_expense($client_expense);
				
				//echo $advance_entry["load_expense_id"]." ".__LINE__."<br>";
				
				//GET LOAD EXPENSE
				$where = null;
				$where["id"] = $advance_entry["load_expense_id"];
				$load_expense = db_select_load_expense($where);
				
				//IF LOAD EXPENSE ALREADY EXISTS FOR THIS ADVANCE
				if(!empty($load_expense))
				{
					//echo $load_expense["id"]." ".__LINE__."<br>";
					//ADJUST LOAD EXPENSE WITH RECEIPT AMOUNT AND RECEIPT DATETIME
					$edit_load_expense = null;
					$edit_load_expense["explanation"] = $load_expense["explanation"]." | Missing Receipt";
					$edit_load_expense["receipt_datetime"] = $entry_datetime;
					
					//UPDATE
					$where = null;
					$where["id"] = $load_expense["id"];
					db_update_load_expense($edit_load_expense,$where);
				}
			}
		}
	
	**/
	}
	
	//CREATES CLIENT EXPENSE FOR DRIVER EQUIPMENT ACCOUNT *** THIS FUNCTION IS NOT USED AFTER THE AZKABAN UPDATE
	function create_client_equipment_expense($client,$settlement)
	{
		//echo " create_client_equipment_expense ";
	
		//GET USER
		$CI =& get_instance();
		$recorder_id = $CI->session->userdata('person_id');
		
		
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $settlement["end_week_id"];
		$log_entry = db_select_log_entry($where);

		
		//GET CLIENTS DRIVER EQUIPMENT ACCOUNT
		$where = null;
		$where["company_id"] = $client["company_id"];
		$where["category"] = "Driver Equipment";
		$de_account = db_select_account($where);
		
		//GET DRIVER EQUIPMENT BALANCE
		$de_balance = -1 * get_account_balance($de_account["id"]);
		
		//DETERMINE AMOUNT TO CHARGE FOR DRIVER EQUIPMENT
		if($de_balance > 0)
		{
			//1 CENT PER BLOCK
			$block = 200;
			$mileage_rate = round(($de_balance + $block/2)/($block*100),2);
			
			$mileage_rate = 0;
			if($de_balance > 0 && $de_balance < 300)
			{
				$mileage_rate = .01;
			}else if($de_balance >= 300 && $de_balance < 400)
			{
				$mileage_rate = .015;
			}
			else if($de_balance >= 400 && $de_balance < 500)
			{
				$mileage_rate = .02;
			}
			else if($de_balance >= 500)
			{
				$mileage_rate = .03;
			}
			
			//GET DRIVER END WEEK STATS
			$driver_end_week_stats = null;
			$driver_end_week_stats = get_driver_end_week_stats($log_entry,$client["id"]);
			
			//CALC DRIVER EQUIPMENT CLIENT EXPENSE AMOUNT
			$de_ce_amount = $mileage_rate * $driver_end_week_stats["total_odometer_miles"];
			
			if($de_ce_amount > $de_balance)
			{
				//ADJUST THE DRIVER EQUIPMENT EXPENSE AMOUNT TO THE REMAINING BALANCE
				$de_ce_amount = $de_balance;
			}
			
			//CREATE CLIENT EXPENSE
			$client_expense = null;
			$client_expense["settlement_id"] = $settlement["id"];
			$client_expense["client_id"] = $client["id"];
			$client_expense["expense_datetime"] = $log_entry["entry_datetime"];
			$client_expense["category"] = "Driver Equipment";
			$client_expense["description"] = "Pay towards Driver Equipment balance of ".number_format($de_balance*-1,2)." at rate of ".$mileage_rate." cents per mile ";
			$client_expense["expense_amount"] = round($de_ce_amount,2);
			
			db_insert_client_expense($client_expense);
			
			//GET CLIENT'S EQUIPMENT ACCOUNT
			$where = null;
			$where["company_id"] = $client["company_id"];
			$where["category"] = "Driver Equipment";
			$equipment_account = db_select_account($where);
			
			//DEBIT DRIVER'S DAMAGE ACCOUNT
			$credit_equipment["account_id"] = $equipment_account["id"];
			$credit_equipment["recorder_id"] = $recorder_id;
			$credit_equipment["entry_datetime"] = $log_entry["entry_datetime"];
			$credit_equipment["debit_credit"] = "Credit";
			$credit_equipment["entry_amount"] = round($de_ce_amount,2);
			$credit_equipment["entry_description"] = "Pay towards balance from statement ending ".date("m/d/y",strtotime($log_entry["entry_datetime"]));
	
			db_insert_account_entry($credit_equipment);
		}
	}
	
	//CREATES CLIENT EXPENSE FOR CLIENT DAMAGE ACCOUNT
	function create_client_damage_expense($client,$settlement)
	{
		//echo " create_client_damage_expense ";
		$entries = array();
		
		date_default_timezone_set('America/Denver');
		$entry_datetime = date("Y-m-d H:i:s");
		$recorded_datetime = date("Y-m-d G:i:s");
		
		//GET USER
		$CI =& get_instance();
		$recorder_id = $CI->session->userdata('person_id');
		
		//GET LOG ENTRY
		$where = null;
		$where["id"] = $settlement["end_week_id"];
		$log_entry = db_select_log_entry($where);
		
		//GET DRIVER SPECIFIC A/P ON SETTLEMENTS ACCOUNT
		$where = null;
		$where["company_id"] = $client["company_id"];
		$where["category"] = "Coop A/P to Member on Settlements";
		$coop_default_member_settlement_ap_account = db_select_default_account($where);
		
		//GET CLIENTS CLIENT FLEETPROTECT ACCOUNT
		$where = null;
		$where["company_id"] = $client["company_id"];
		$where["category"] = "Coop A/R on FleetProtect";
		$default_fleet_protect_account = db_select_default_account($where);
		
		//GET DRIVER EQUIPMENT BALANCE
		$damage_balance = get_account_balance($default_fleet_protect_account["account_id"]);
		
		//GET DRIVER END WEEK STATS
		$driver_end_week_stats = null;
		$driver_end_week_stats = get_driver_end_week_stats($log_entry,$client["id"]);
		
		
		
		//CALC DRIVER EQUIPMENT CLIENT EXPENSE AMOUNT
		$mileage_rate = 0;
		if($damage_balance > 400)
		{
			$mileage_rate = .05;
		}
		else if($damage_balance <= 400 && $damage_balance > 300)
		{
			$mileage_rate = .04;
		}
		else if($damage_balance <= 300 && $damage_balance > 200)
		{
			$mileage_rate = .03;
		}
		else if($damage_balance <= 200 && $damage_balance > 100)
		{
			$mileage_rate = .02;
		}
		else if($damage_balance <= 100 && $damage_balance > 0)
		{
			$mileage_rate = .01;
		}
		
		$ce_amount = $mileage_rate * $driver_end_week_stats["total_odometer_miles"];
		
		//$mileage_rate = $ce_amount/$driver_end_week_stats["total_odometer_miles"]
		
		if($ce_amount > $damage_balance)
		{
			$ce_amount = $damage_balance;
		}
		
		
		$description = "Payment from statement ending ".date("m/d/y",strtotime($log_entry["entry_datetime"]))." towards FleetProtect balance of ".number_format($damage_balance,2);
		
		//CREATE CREDIT ENTRY
		$credit_entry = null;
		$credit_entry["account_id"] = $default_fleet_protect_account["account_id"];
		$credit_entry["recorder_id"] = $recorder_id;
		$credit_entry["recorded_datetime"] = $recorded_datetime;
		$credit_entry["entry_datetime"] = $log_entry["entry_datetime"];
		$credit_entry["debit_credit"] = "Credit";
		$credit_entry["entry_amount"] = round($ce_amount,2);
		$credit_entry["entry_description"] = $description;
		//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		// $credit_entry = null;
		// $credit_entry["account_id"] = 1316;
		// $credit_entry["recorder_id"] = 1;
		// //$credit_entry["recorded_datetime"] = $recorded_datetime;
		// //$credit_entry["entry_datetime"] = $log_entry["entry_datetime"];
		// $credit_entry["debit_credit"] = "Credit";
		// $credit_entry["entry_amount"] = 10;
		// //$credit_entry["entry_description"] = $description;
		
		$entries[] = $credit_entry;
		
		//CREATE DEBIT ENTRY
		$debit_entry = null;
		//$debit_entry["account_id"] = $default_fleet_protect_account["account_id"];//test only
		$debit_entry["account_id"] = $coop_default_member_settlement_ap_account["account_id"];
		$debit_entry["recorder_id"] = $recorder_id;
		$debit_entry["recorded_datetime"] = $recorded_datetime;
		$debit_entry["entry_datetime"] = $log_entry["entry_datetime"];
		$debit_entry["debit_credit"] = "Debit";
		$debit_entry["entry_amount"] = round($ce_amount,2);
		$debit_entry["entry_description"] = $description;
		//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
		
		// $debit_entry = null;
		// $debit_entry["account_id"] = 1316;
		// $debit_entry["recorder_id"] = 1;
		// //$debit_entry["recorded_datetime"] = $recorded_datetime;
		// //$debit_entry["entry_datetime"] = $log_entry["entry_datetime"];
		// $debit_entry["debit_credit"] = "Debit";
		// $debit_entry["entry_amount"] = 10;
		// //$debit_entry["entry_description"] = $description;
		
		$entries[] = $debit_entry;
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		$transaction = null;
		$transaction["category"] = "FleetProtect Repayment";
		$transaction["description"] = $client["client_nickname"]." ".$description;
		$new_transaction = create_transaction_and_entries($transaction,$entries);
		
		//GET COOP COMPANY
		$where = null;
		$where["category"] = "Coop";
		$coop_company = db_select_company($where);
		
		//CREATE CLIENT EXPENSE
		$client_expense = null;
		$client_expense["settlement_id"] = $settlement["id"];
		$client_expense["client_id"] = $client["id"];
		$client_expense["owner_id"] = $coop_company["id"];
		$client_expense["expense_datetime"] = $log_entry["entry_datetime"];
		$client_expense["category"] = "FleetProtect";
		$client_expense["description"] = "Repayment of FleetProtect loans at rate of ".$mileage_rate." cents per mile on current balance of ".number_format($damage_balance,2);
		$client_expense["expense_amount"] = round($ce_amount,2);
		$client_expense["transaction_id"] = $new_transaction["id"];
		
		db_insert_client_expense($client_expense);
	}
	
	//GET FM PROFIT STATS FOR GIVEN FM AND DATE RANGE (dates are inclusive)
	function get_fm_profit_stats($fm_id,$start_date,$end_date)
	{
		$start_date = date("Y-m-d G:i:s",strtotime($start_date));
		$end_date = date("Y-m-d G:i:s",strtotime($end_date)+24*60*60);
		
		$start_date_next_week = date("Y-m-d G:i:s",strtotime($start_date)+7*24*60*60);
		$end_date_next_week = date("Y-m-d G:i:s",strtotime($end_date)+7*24*60*60);
		
		$start_date_plus_ten = date("Y-m-d G:i:s",strtotime($start_date)+10*24*60*60);
		$end_date_plus_ten = date("Y-m-d G:i:s",strtotime($end_date)+10*24*60*60);
	
		$CI =& get_instance();
		
		//GET FM PROFIT ACCOUNT
		$where = null;
		$where["company_id"] = $fm_id;
		$where["account_type"] = "Fleet Manager";
		$where["category"] = "Profit";
		$fm_profit_account = db_select_account($where);
		
		//CALCULATE TOTAL DEDUCTIONS
		$values = array();
		$values[] = $fm_profit_account["id"];
		$values[] = $start_date;
		$values[] = $end_date;
		
		$sql = "SELECT sum(entry_amount) as total
				FROM `account_entry` 
				WHERE account_id = ?
				AND entry_type = 'Funding Deduction'
				AND entry_datetime >= ?
				AND entry_datetime < ?";
		
		$query = $CI->db->query($sql,$values);
		
		$total_deductions = 0;
		foreach ($query->result() as $row)
		{
			$total_deductions = round($row->total,2);
		}
		
		//CALCULATE TOTAL NON-STANDARD EXPENSES (does not include deductions)
		
		//GET ALL EXPENSES
		$values = array();
		$values[] = $fm_id;
		$values[] = $start_date;
		$values[] = $end_date;
		
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE expense_type = 'Expense'
				AND company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?";
				
		
		$query = $CI->db->query($sql,$values);
		
		$total_expenses = 0;
		foreach ($query->result() as $row)
		{
			$total_expenses = round($row->total,2);
		}
		

		//GET PAYOUT FOR THIS WEEK'S DATE RANGE (to be applied to last week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Settlement Pay'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_pay_out_this_week = 0;
		foreach ($query->result() as $row)
		{
			$total_pay_out_this_week = round($row->total,2);
		}
		
		//GET KICK IN FOR THIS WEEK'S DATE RANGE (to be applied to last week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Kick In'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_kick_in_this_week = 0;
		foreach ($query->result() as $row)
		{
			$total_kick_in_this_week = round($row->total,2);
		}
		
		//GET ADVANCES FOR THIS WEEK'S DATE RANGE (to be applied to last week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Personal Advance'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_advances_this_week = 0;
		foreach ($query->result() as $row)
		{
			$total_advances_this_week = round($row->total,2);
		}
		
		$non_standard_expenses = round($total_expenses - $total_pay_out_this_week - $total_advances_this_week - $total_kick_in_this_week,2);
		
		//GET RECRUITING POOL FOR THIS WEEK'S DATE RANGE
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE category = 'Recruiting Pool' 
				AND expense_datetime >= '$start_date'
				AND expense_datetime < '$end_date'";
		
		$query = $CI->db->query($sql,$values);
		//echo $sql;
		//echo $end_date;
		
		$total_recruiting_pool = 0;
		foreach ($query->result() as $row)
		{
			$total_recruiting_pool = round($row->total,2);
			//echo " ".round($row->total,2);
		}
		
		//GET UNCLAIMED FM EXPENSES FOR THIS WEEK'S DATE RANGE
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE category = 'Unclaimed FM Expense' 
				AND expense_datetime >= '$start_date'
				AND expense_datetime < '$end_date'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_unclaimed_fm_expenses = 0;
		foreach ($query->result() as $row)
		{
			$total_unclaimed_fm_expenses = round($row->total,2);
		}
		
		//GET UNCLAIMED FM EXPENSES FOR THIS WEEK'S DATE RANGE
		$sql = "SELECT count(*) as total FROM `expense` 
				WHERE locked_datetime IS NULL
				AND expense_datetime >= '$start_date'
				AND expense_datetime < '$end_date'";
		
		$query = $CI->db->query($sql,$values);
		//echo $sql;
		$unlocked_count = 0;
		foreach ($query->result() as $row)
		{
			$unlocked_count = round($row->total,2);
		}
		
		
		
		
		//CALCULATE TOTAL PAY OUT -- PLUS 7 DAYS
		$values = array();
		$values[] = $fm_id;
		$values[] = $start_date_next_week;
		$values[] = $end_date_next_week;
		
		//GET PAYOUT FOR NEXT WEEK'S DATE RANGE (to be applied to this week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Settlement Pay'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_pay_out_next_week = 0;
		foreach ($query->result() as $row)
		{
			$total_pay_out_next_week = round($row->total,2);
		}
		
		//GET KICK IN FOR NEXT WEEK'S DATE RANGE (to be applied to this week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Kick In'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_kick_in_next_week = 0;
		foreach ($query->result() as $row)
		{
			$total_kick_in_next_week = round($row->total,2);
		}
		
		//GET ADVANCES FOR NEXT WEEK'S DATE RANGE (to be applied to this week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Personal Advance'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_advances_next_week = 0;
		foreach ($query->result() as $row)
		{
			$total_advances_next_week = round($row->total,2);
		}
		
		$total_pay_out = $total_pay_out_next_week + $total_advances_next_week;
		
		
		
		
		//CALCULATE TOTAL PAY OUT -- PLUS 7 DAYS
		$values = array();
		$values[] = $fm_id;
		$values[] = $start_date_plus_ten;
		$values[] = $end_date_plus_ten;
		
		//GET PAYOUT FOR NEXT WEEK'S DATE RANGE (to be applied to this week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Settlement Pay'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_settlements_ten = 0;
		foreach ($query->result() as $row)
		{
			$total_settlements_ten = round($row->total,2);
		}
		
		//GET KICK IN FOR NEXT WEEK'S DATE RANGE (to be applied to this week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Kick In'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_kick_ten = 0;
		foreach ($query->result() as $row)
		{
			$total_kick_ten = round($row->total,2);
		}
		
		//GET ADVANCES FOR NEXT WEEK'S DATE RANGE (to be applied to this week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Personal Advance'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_advances_ten = 0;
		foreach ($query->result() as $row)
		{
			$total_advances_ten = round($row->total,2);
		}
		
		$total_pay_out_ten = $total_settlements_ten + $total_advances_ten;
		
		
		
		
		
		
		
		$fm_profit_stats = array();
		$fm_profit_stats["total_deductions"] = $total_deductions;
		$fm_profit_stats["total_expenses"] = $total_expenses;
		$fm_profit_stats["total_kick_in_this_week"] = $total_kick_in_this_week;
		$fm_profit_stats["total_pay_out_this_week"] = $total_pay_out_this_week;
		$fm_profit_stats["total_advances_this_week"] = $total_advances_this_week;
		$fm_profit_stats["non_standard_expenses"] = $non_standard_expenses;
		$fm_profit_stats["total_recruiting_pool"] = $total_recruiting_pool;
		$fm_profit_stats["total_unclaimed_fm_expenses"] = $total_unclaimed_fm_expenses;
		$fm_profit_stats["unlocked_count"] = $unlocked_count;
		$fm_profit_stats["total_kick_in_next_week"] = $total_kick_in_next_week;
		$fm_profit_stats["total_pay_out_next_week"] = $total_pay_out_next_week;
		$fm_profit_stats["total_advances_next_week"] = $total_advances_next_week;
		$fm_profit_stats["total_pay_out"] = $total_pay_out;
		
		$fm_profit_stats["total_kick_ten"] = $total_kick_ten;
		$fm_profit_stats["total_settlements_ten"] = $total_settlements_ten;
		$fm_profit_stats["total_advances_ten"] = $total_advances_ten;
		$fm_profit_stats["total_pay_out_ten"] = $total_pay_out_ten;
		
		return $fm_profit_stats;
	}
	
	//GET ARROWHEAD STATS
	function get_arrowhead_stats($start_date,$end_date)
	{
		
		$start_date = date("Y-m-d G:i:s",strtotime($start_date));
		$end_date = date("Y-m-d G:i:s",strtotime($end_date)+24*60*60);
		
		$start_date_next_week = date("Y-m-d G:i:s",strtotime($start_date)+7*24*60*60);
		$end_date_next_week = date("Y-m-d G:i:s",strtotime($end_date)+7*24*60*60);
		
		$start_date_plus_ten = date("Y-m-d G:i:s",strtotime($start_date)+14*24*60*60);
		$end_date_plus_ten = date("Y-m-d G:i:s",strtotime($end_date)+14*24*60*60);
	
		$CI =& get_instance();
		
		//CALCULATE TOTAL DEDUCTIONS
		$values = array();
		$values[] = $start_date;
		$values[] = $end_date;
		
		$sql = "SELECT sum(entry_amount) as total
				FROM `account_entry` 
				WHERE entry_type = 'Funding Deduction'
				AND entry_amount > 0 
				AND entry_datetime >= ?
				AND entry_datetime < ?";
		
		$query = $CI->db->query($sql,$values);
		
		$total_deductions = 0;
		foreach ($query->result() as $row)
		{
			$total_deductions = round($row->total,2);
		}
		
		$sql = "SELECT sum(entry_amount) as total
				FROM `account_entry` 
				WHERE entry_type = 'Funding Deduction'
				AND entry_amount < 0 
				AND entry_datetime >= ?
				AND entry_datetime < ?";
		
		$query = $CI->db->query($sql,$values);
		
		$total_reimbursements = 0;
		foreach ($query->result() as $row)
		{
			$total_reimbursements = round($row->total,2)*-1;
		}
		
		//CALCULATE TOTAL NON-STANDARD EXPENSES (does not include deductions)
		
		//GET ARROWHEAD COMPANY ID
		$where = null;
		$where["type"] = 'Business';
		$where["category"] = 'Dispatch';
		$arrowhead_company = db_select_company($where);
		
		//GET ALL EXPENSES
		$values = array();
		$values[] = $arrowhead_company["id"];
		$values[] = $start_date;
		$values[] = $end_date;
		
		$sql = "SELECT * FROM `expense` 
				WHERE expense_type = 'Expense'
				AND company_id = ? 
				AND expense_datetime >= ?
				AND expense_datetime < ?
				AND category <> 'Kick In'
				ORDER BY `expense_datetime` ASC ";
				
		
		$query = $CI->db->query($sql,$values);
		
		//TOTAL ARROWHEAD EXPENSES
		$arrowhead_expenses = array();
		$total_expenses = 0;
		foreach ($query->result() as $row)
		{
			$total_expenses = $total_expenses + round($row->expense_amount,2);
			
			$expense = null;
			$expense["expense_datetime"] = $row->expense_datetime;
			$expense["category"] = $row->category;
			$expense["description"] = $row->description;
			$expense["expense_amount"] = $row->expense_amount;
			
			$arrowhead_expenses[] = $expense;
		}
		
			//GET THIS WEEK TOTALS
		
		//GET ALL SETTLEMENTS
		$values = array();
		$values[] = $start_date;
		$values[] = $end_date;
		
		//GET NON-STANDARD CLIENT EXPENSES
		$sql = "SELECT * FROM `expense` 
				WHERE expense_datetime >= ? 
				AND expense_datetime < ?
				AND 
					(category = 'ME - Driver Equipment'
					|| category = 'ME - Hotels'
					|| category = 'ME - Other Non-Standard'
					|| category = 'PA'
					|| category = 'Damage/Repairs'
					)
				ORDER BY `expense_datetime` ASC ";
		
		$query = $CI->db->query($sql,$values);
		
		//TOTAL CLIENT EXPENSES
		$client_expenses = array();
		$total_client_expenses = 0;
		foreach ($query->result() as $row)
		{
			$total_client_expenses = $total_client_expenses + round($row->expense_amount,2);
			
			$expense = null;
			$expense["expense_datetime"] = $row->expense_datetime;
			$expense["category"] = $row->category;
			$expense["description"] = $row->description;
			$expense["expense_amount"] = $row->expense_amount;
			
			$client_expenses[] = $expense;
		}
		
		//GET PAYOUT FOR THIS WEEK'S DATE RANGE (to be applied to last week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE expense_datetime >= ? 
				AND expense_datetime < ?
				AND category = 'Settlement'";
		
		$query = $CI->db->query($sql,$values);
		
		//TOTAL SETTLEMENTS
		$total_pay_out_this_week = 0;
		foreach ($query->result() as $row)
		{
			$total_pay_out_this_week = round($row->total,2);
		}
		
		//GET KICK IN FOR THIS WEEK'S DATE RANGE (to be applied to last week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE expense_datetime >= ? 
				AND expense_datetime < ?
				AND category = 'Kick In'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_kick_in_this_week = 0;
		foreach ($query->result() as $row)
		{
			$total_kick_in_this_week = round($row->total,2);
		}
		
		//GET ADVANCES FOR THIS WEEK'S DATE RANGE (to be applied to last week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE expense_datetime >= ? 
				AND expense_datetime < ?
				AND category = 'PA'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_advances_this_week = 0;
		foreach ($query->result() as $row)
		{
			$total_advances_this_week = round($row->total,2);
		}
		
		
		//CALCULATE TOTAL PAY OUT -- PLUS 14 DAYS
		$values = array();
		$values[] = $start_date_plus_ten;
		$values[] = $end_date_plus_ten;
		
		//GET PAYOUT FOR NEXT WEEK'S DATE RANGE (to be applied to this week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Settlement'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_settlements_ten = 0;
		foreach ($query->result() as $row)
		{
			$total_settlements_ten = round($row->total,2);
		}
		
		//GET KICK IN FOR NEXT WEEK'S DATE RANGE (to be applied to this week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'Kick In'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_kick_ten = 0;
		foreach ($query->result() as $row)
		{
			$total_kick_ten = round($row->total,2);
		}
		
		//GET ADVANCES FOR NEXT WEEK'S DATE RANGE (to be applied to this week)
		$sql = "SELECT sum(expense_amount) as total FROM `expense` 
				WHERE expense_datetime >= ?
				AND expense_datetime < ?
				AND category = 'PA'";
		
		$query = $CI->db->query($sql,$values);
		
		$total_advances_ten = 0;
		foreach ($query->result() as $row)
		{
			$total_advances_ten = round($row->total,2);
		}
		
		//GET CHECK LIST STATS
		
		//GET UNLOCKED EXPENSES
		$sql = "SELECT count(*) as total FROM `expense` 
				WHERE locked_datetime IS NULL
				AND expense_type = 'Expense'
				AND expense_datetime >= '$start_date'
				AND expense_datetime < '$end_date_plus_ten'";
		
		$query = $CI->db->query($sql,$values);
		//echo $sql;
		$unlocked_count = 0;
		foreach ($query->result() as $row)
		{
			$unlocked_count = round($row->total,2);
		}
		
		$sql = "SELECT count(*) as total FROM `expense` 
				WHERE recorded_datetime IS NULL
				AND expense_type = 'Expense'
				AND expense_datetime >= '$start_date'
				AND expense_datetime < '$end_date_plus_ten'";
		
		$query = $CI->db->query($sql,$values);
		//echo $sql;
		$unrecorded_count = 0;
		foreach ($query->result() as $row)
		{
			$unrecorded_count = round($row->total,2);
		}
		
		//GET LOADS FOR THIS WEEK
		$loads = array();
		$values = array();
		$where_sql = null;
		$where_sql = 	" WHERE leg.log_entry_id = log_entry.id 
						AND load.id = leg.allocated_load_id 
						AND log_entry.entry_datetime > '".$start_date."'  
						AND log_entry.entry_datetime <= '".$end_date."'";
						
		$sql = "SELECT DISTINCT(leg.allocated_load_id) FROM `leg`, `log_entry`, `load` ".$where_sql." ORDER BY load.final_drop_datetime";
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$performance_reviews = array();
		foreach ($query->result() as $row)
		{
			$load_id = $row->allocated_load_id;
			
			$where = null;
			$where["id"] = $load_id;
			$load = db_select_load($where);
			
			if(!empty($load["funded_datetime"]))
			{
				$loads[] = $load;
			}
		}
		
		//GET UNLOCKED EVENTS
		$sql = "SELECT count(*) as total FROM `log_entry` 
				WHERE locked_datetime IS NULL
				AND entry_datetime >= '$start_date'
				AND entry_datetime < '$end_date'";
		
		$query = $CI->db->query($sql,$values);
		//echo $sql;
		$unlocked_event_count = 0;
		foreach ($query->result() as $row)
		{
			$unlocked_event_count = round($row->total,2);
		}
		
		$arrowhead_stats = array();
		
		$arrowhead_stats["total_deductions"] = $total_deductions;
		$arrowhead_stats["total_reimbursements"] = $total_reimbursements;
		
		$arrowhead_stats["total_expenses"] = $total_expenses;
		$arrowhead_stats["arrowhead_expenses"] = $arrowhead_expenses;
		
		$arrowhead_stats["total_client_expenses"] = $total_client_expenses;
		$arrowhead_stats["client_expenses"] = $client_expenses;
		
		$arrowhead_stats["total_pay_out_this_week"] = $total_pay_out_this_week;
		$arrowhead_stats["total_kick_in_this_week"] = $total_kick_in_this_week;
		$arrowhead_stats["total_advances_this_week"] = $total_advances_this_week;
		
		$arrowhead_stats["total_kick_ten"] = $total_kick_ten;
		$arrowhead_stats["total_settlements_ten"] = $total_settlements_ten;
		$arrowhead_stats["total_advances_ten"] = $total_advances_ten;
		
		$arrowhead_stats["unlocked_count"] = $unlocked_count;
		$arrowhead_stats["unrecorded_count"] = $unrecorded_count;
		$arrowhead_stats["loads"] = $loads;
		$arrowhead_stats["unlocked_event_count"] = $unlocked_event_count;

		return $arrowhead_stats;
		
	}
	
	//GET DM REPORT STATS
	function get_dm_report_stats($fm_company_id)
	{
		//GET FM COMPANY
		$where = null;
		$where["id"] = $fm_company_id;
		$fm_company = db_select_company($where);
		
		$stats = array();
		
		$log_stats = array();
		
		//GET LIST OF TRUCKS
		$where = null;
		$where["fm_id"] = $fm_company["person_id"];
		$where["status"] = "On the road";
		$fm_trucks = db_select_trucks($where);
		
		//FOR EACH TRUCK
		foreach($fm_trucks as $truck)
		{
			$log_stat = null;
			
			//SCAN FOR CHECK CALL FROM TODAY
			
			//IF CHECK CALL IS FOUND
			
				//VALIDATE CHECK CALL IS COMPLETE
				
			
			//GET COUNT OF HOW MANY EVENTS HAVE BEEN LOGGED SINCED PREVIOUS FRIDAY MIDNIGHT
			
			
			//GET OLDEST UNLOCKED EVENT FOR THIS TRUCK
			$where = null;
			$where = "	truck_id = ".$truck["id"]." AND
						locked_datetime IS NULL AND
						entry_datetime = 
						(SELECT MIN(log_entry.entry_datetime) FROM `log_entry` WHERE 
						truck_id = ".$truck["id"]." AND
						locked_datetime IS NULL AND
						entry_datetime > '2013-12-31 00:00:00')";
			$last_locked_entry = db_select_log_entry($where);
			
			$log_stat["truck"] = $truck;
			$log_stat['log_entry'] = $last_locked_entry;
			
			$log_stats[] = $log_stat;
			
		}
		
		$CI =& get_instance();
		
		
		
		
		//GET MISSING RECEIPTS
		$values = null;
		$values[] = $fm_company["id"];
		$sql = null;
		$sql = "	SELECT COUNT(*) as recipt_count,SUM(expense_amount) as recipt_total FROM `client_expense` WHERE
					owner_id = ? AND
					is_reimbursable = 'Yes' AND
					paid_datetime IS NULL";
		$query = $CI->db->query($sql,$values);
		foreach ($query->result() as $row)
		{
			$missing_receipts = $row->recipt_count;
			$missing_receipts_total = $row->recipt_total;
		}
		
		//GET MISSING BOLS
		$values = null;
		$values[] = $fm_company["person_id"];
		$sql = null;
		$sql = "	SELECT COUNT(*) as bol_count,SUM(expected_revenue) as bol_total FROM `load` WHERE
					fleet_manager_id = ? AND
					status = 'Dropped' AND
					digital_received_datetime IS NULL";
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		foreach ($query->result() as $row)
		{
			$missing_bols = $row->bol_count;
			$missing_bols_total = $row->bol_total;
		}
		
		//GET UNLOCKED EXPENSES
		$values = null;
		$values[] = $fm_company["id"];
		$sql = null;
		$sql = "	SELECT COUNT(*) as expense_count,SUM(expense_amount) as expense_total FROM `expense` WHERE
					company_id = ? AND
					locked_datetime IS NULL AND
					expense_datetime > '2014-01-18 00:00:00'";
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		foreach ($query->result() as $row)
		{
			$unlocked_expense_count = $row->expense_count;
			$unlocked_expense_total = $row->expense_total;
		}
		
		//GET UNRECORDED EXPENSES
		$values = null;
		$values[] = $fm_company["id"];
		$sql = null;
		$sql = "	SELECT COUNT(*) as expense_count,SUM(expense_amount) as expense_total FROM `expense` WHERE
					company_id = ? AND
					recorded_datetime IS NULL AND
					expense_datetime > '2014-01-18 00:00:00'";
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		foreach ($query->result() as $row)
		{
			$unrecorded_expense_count = $row->expense_count;
			$unrecorded_expense_total = $row->expense_total;
		}
		
		//GET MISSING RATE CONS
		$values = null;
		$values[] = $fm_company["person_id"];
		$sql = null;
		$sql = "	SELECT COUNT(*) as rc_count,SUM(expected_revenue) as rc_total FROM `load` WHERE
					fleet_manager_id = ".$fm_company["person_id"]." AND
					status_number = 4";
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		foreach ($query->result() as $row)
		{
			$missing_rate_cons = $row->rc_count;
			$missing_rate_cons_total = $row->rc_total;
		}
		
		//GET MISSING RATE CONS
		$values = null;
		$values[] = $fm_company["person_id"];
		$sql = null;
		$sql = "	SELECT COUNT(*) as item_count FROM `settlement`,`log_entry` WHERE
					settlement.end_week_id= log_entry.id AND 
					log_entry.entry_datetime > '2014-02-07 00:00:00' AND
					fm_id = ? AND approved_datetime IS NULL";
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		foreach ($query->result() as $row)
		{
			$unapproved_statements = $row->item_count;
		}
		
		
		$stats["log_stats"] = $log_stats;
		$stats["missing_rate_cons"] = $missing_rate_cons;
		$stats["missing_rate_cons_total"] = $missing_rate_cons_total;
		$stats["missing_receipts"] = $missing_receipts;
		$stats["missing_receipts_total"] = $missing_receipts_total;
		$stats["missing_bols"] = $missing_bols;
		$stats["missing_bols_total"] = $missing_bols_total;
		$stats["unlocked_expense_count"] = $unlocked_expense_count;
		$stats["unlocked_expense_total"] = $unlocked_expense_total;
		$stats["unrecorded_expense_count"] = $unrecorded_expense_count;
		$stats["unrecorded_expense_total"] = $unrecorded_expense_total;
		$stats["unapproved_statements"] = $unapproved_statements;
		
		
		return $stats;
	}
	
	//STORE FILE TO FTP SERVER - INPUT NAME (POST NAME), GIVEN PATH, OFFICE_PERMISSION, AND DRIVER_PERMISSION - RETURNS SECURE_FILE
	function store_secure_ftp_file($input_name,$name,$type,$title,$category,$local_path,$server_path,$office_permission,$driver_permission)
	{
		date_default_timezone_set('US/Mountain');
		
		$CI =& get_instance();
		
		//SET UP CONNECTION TO FTP SERVER
		$CI->load->library('ftp');
		// $config['hostname'] = 'jellyfish.arvixe.com';
		// $config['hostname'] = 'ftp.fleetsmarts.net';
		// $config['username'] = 'covax13';
		// $config['password'] = 'retret13';
		
		//$config['hostname'] = 'dallas133.arvixeshared.com';
		// $config['hostname'] = 'ftp.fleetsmarts.net';
		// $config['username'] = 'covax13';
		// $config['password'] = 'retret13';
		// $config['debug']	= TRUE;
		
		$config['hostname'] = 'ftp.integratedlogicsticssolutions.co';
		$config['username'] = 'covax13';
		$config['password'] = 'Retret13!';
		$config['debug']	= TRUE;
		$CI->ftp->connect($config);
		
		//echo '<br>'/ENVIRONMENT;
		
		//DEFAULT TITLE
		if(empty($title))
		{
			$title = "File";
		}
		
		//GET EXTENTION
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$name_wo_ext = substr($name,0,strrpos($name,"."));
		$clean_name = preg_replace("/[^A-Za-z0-9 ]/", '', $name_wo_ext);
		$name = $clean_name.".".$ext;
		//error_log("clean_name ".$clean_name." ext ".$ext." | LINE ".__LINE__." ".__FILE__);
		
		//SET SERVER PATH ACCORDING TO ENVIRONMENT - MAKE NEW DIRECTORIES IF THEY DON'T ALREADY EXIST
		//$server_path = $server_path.ENVIRONMENT."/".date('Y')."/";
		$server_path = $server_path.ENVIRONMENT;
		$year_directories = $CI->ftp->list_files('/edocuments/'.ENVIRONMENT."/");
//		echo "year directories: ";
//		print_r($year_directories);
		if(!in_array(date('Y'),$year_directories))
		{
//			echo date('Y') , " directory does not exist. <br>";
//			echo "The directory " . $server_path . "/" . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . " was successfully created.";
			$CI->ftp->mkdir($server_path . "/" . date('Y') . "/", DIR_WRITE_MODE);
			$CI->ftp->mkdir($server_path . "/" . date('Y') . "/" . date('n') . "-" . date('Y') . "/", DIR_WRITE_MODE);
			$CI->ftp->mkdir($server_path . "/" . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . $category . "/", DIR_WRITE_MODE);
		}else{
//			echo date('Y') , " directory exists. <br>";
			$month_directories = $CI->ftp->list_files('/edocuments/'.ENVIRONMENT."/".date('Y'));
//			echo "month directories: ";
//			print_r($month_directories);
			if(!in_array(date('n') . "-" . date('Y'),$month_directories))
			{
				$CI->ftp->mkdir($server_path . "/" . date('Y') . "/" . date('n') . "-" . date('Y') . "/", DIR_WRITE_MODE);
				
				$CI->ftp->mkdir($server_path . "/" . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . $category . "/", DIR_WRITE_MODE);
				
//				echo date('Y') . "/" . date('n')."-".date('Y'), " directory does not exist. <br>";
//				echo "The directory " . $server_path."/".date('Y')."/".date('n')."-".date('Y') . "/" . " was successfully created.";
			}
			else
			{
				$category_directories = $CI->ftp->list_files('/edocuments/' . ENVIRONMENT . "/" . date('Y') . "/" . date('n') . "-" . date('Y') . "/");
				
				if(!in_array($category,$category_directories))
				{
					$CI->ftp->mkdir($server_path . "/" . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . $category . "/", DIR_WRITE_MODE);
				}
//				echo "The directory " . $server_path."/".date('n')."-".date('Y') . " already exists.";
			}
		}
		
		$server_path = $server_path . "/" . date('Y') . "/" . date('n') . "-" . date('Y') . "/" . $category . "/";
		$file_guid = get_random_string(5);
	
		//$secure_file = null;
		$secure_file['name'] = $name;
		$secure_file['type'] = $type;
		$secure_file['title'] = $title;
		$secure_file['category'] = $category;
		$secure_file['file_guid'] = $file_guid;
		$secure_file['server_path'] = $server_path;
		$secure_file['office_permission'] = $office_permission;
		$secure_file['driver_permission'] = $driver_permission;
		db_insert_secure_file($secure_file);
		
		//GET NEWLY INSERTED SECURE FILE
		$where = null;
		$where["file_guid"] = $file_guid;
		$where["name"] = $name;
		$new_secure_file = db_select_secure_file($where);
		
		//UPDATE GUID WITH ID APPENDED TO BEGINNING
		$update_file = null;
		$update_file["file_guid"] = $new_secure_file["id"].$file_guid;
		$update_file["name"] = "(".$new_secure_file["id"].")".$name;
		
		$where = null;
		$where["id"] = $new_secure_file["id"];
		db_update_secure_file($update_file,$where);
		
		//SELECT NEWLY UPDATED SECURE FILE
		$where = null;
		$where["id"] = $new_secure_file["id"];
		$new_secure_file = db_select_secure_file($where);
		
		//$full_path = $server_path."/".$new_secure_file["name"];
		$full_path = $server_path.$new_secure_file["name"];
		
				
		//GETS FILES TO SECURE FTP LOCATION DIFFERENTLY BASED ON ENVIRONMENT
		if(ENVIRONMENT == 'development')
		{
			//UPLOAD FILE TO FTP SERVER
			$CI->ftp->upload($local_path,$full_path, 'auto', 0775);
		}
		else if(ENVIRONMENT == 'production')
		{
			//LOAD UPLOAD LIBRARY
			$config = null;
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = '*';
			$config['remove_spaces'] = TRUE;
			//$config['max_size']	= '1000';
			$CI->load->library('upload', $config);
			
			//UPLOAD FILE TO PUBLIC UPLOADS FOLDER AND CHECK FOR ERRORS
			if ( ! $CI->upload->do_upload($input_name))
			{
				echo $CI->upload->display_errors($input_name);
			}
			else //IF UPLOAD WAS A SUCCESS
			{
				//GET FILE DATA
				$file = $CI->upload->data($input_name);
				//echo $file["full_path"];
			
				//MOVE FILE FROM PUBLIC UPLOADS FOLDER TO SECURE LOCATION
				$CI->ftp->move('/public_html/uploads'.'/'.$file["file_name"], $full_path);
				
				//echo '<br>success';
			}
		}
		
		return $new_secure_file;
	}
	
	//GET FILE FROM FTP SERVER - GIVEN FILE GUID, RETURNS READ_FILE HEADERS TO CLIENT BROWSER
	function get_secure_ftp_file($file_guid)
	{
		date_default_timezone_set('US/Mountain');

		
		$site_main_directory = "public_html_fleetsmarts";
		
		$CI =& get_instance();
	
		//SET UP CONNECTION TO FTP SERVER
		$CI->load->library('ftp');
		//error_log("Hit this line: | LINE ".__LINE__." ".__FILE__);

		// $config['hostname'] = 'jellyfish.arvixe.com';
		// $config['hostname'] = 'ftp.fleetsmarts.net';
		// $config['username'] = 'covax13';
		// $config['password'] = 'retret13';
		
		//$config['hostname'] = 'dallas133.arvixeshared.com';
		// $config['hostname'] = 'ftp.fleetsmarts.net';
		// $config['username'] = 'covax13';
		// $config['password'] = 'retret13';
		
		$config['hostname'] = 'ftp.integratedlogicsticssolutions.co';
		$config['username'] = 'covax13';
		$config['password'] = 'Retret13!';
		$config['debug']	= TRUE;
		$config['debug']	= TRUE;
		
		$CI->ftp->connect($config);
		//error_log("Hit this line: | LINE ".__LINE__." ".__FILE__);
		
		//GET LIST OF FILE IN THE PUBLIC FOLDER
		$public_folder = "/public_html/temp_files_for_download/".ENVIRONMENT;
		$files = $CI->ftp->list_files($public_folder);
		
		//MOVE ANY LEFT OVER FILES IN THE PUBLIC FOLDER BACK TO THE PRIVATE FOLDER
		foreach($files as $file)
		{
			//echo $file."<br>";
			//GET SECURE FILE
			$this_file = null;
			$where = null;
			$where["name"] = $file;
			$this_file = db_select_secure_file($where);
			//error_log("Hit this line: | LINE ".__LINE__." ".__FILE__);
			
			if(!empty($this_file))
			{
				$full_path = $this_file["server_path"].$this_file["name"];
		
				//MOVE FILE BACK FROM PUBLICLY ACCESSIBLE FOLDER TO NON ACCESSABLE FOLDER
				$CI->ftp->move('/public_html/temp_files_for_download'.'/'.ENVIRONMENT.'/'.$this_file["name"], $full_path);
				//echo "moved<br>";
			}
		}
		
		//GET SECURE_FILE FROM DB
		$where = null;
		$where["file_guid"] = $file_guid;
		$secure_file = db_select_secure_file($where);
		//error_log("Hit this line: | LINE ".__LINE__." ".__FILE__);
		
		// //echo '<br>http://fleetsmarts.nextgenmarketingsolutions.net/temp_files_for_download'.'/'.ENVIRONMENT.'/'.$secure_file["name"];
		
		if(user_has_file_access($secure_file))
		{
			$full_path = $secure_file["server_path"].$secure_file["name"];
			//error_log("Hit this line: | LINE ".__LINE__." ".__FILE__);
			
			//***** EVEN IN THE DEV ENVIRONMENT THIS ACTUALLY USES THE LIVE SERVER TO UPLOAD AND DOWNLOAD FILES
			
			//MOVE FILE FROM NON ACCESSIBLE FOLDER TO PUBLICLY ACCESSABLE FOLDER
			$CI->ftp->move($full_path, '/public_html/temp_files_for_download'.'/'.ENVIRONMENT.'/'.$secure_file["name"]);
			//$CI->ftp->move('/public_html/temp_files_for_download/temp_tester.pdf', '/edocuments/tester.pdf');
			//error_log("Hit this line: | LINE ".__LINE__." ".__FILE__);
			
			//GET FILE EXTENSION
			$ext_pos = strpos($secure_file["name"],".");
			$len = strlen($secure_file["name"]);
			$ext = substr($secure_file["name"],($ext_pos-$len));
		
			//echo "<a href='".'http://fleetsmarts.net/temp_files_for_download'.'/'.ENVIRONMENT.'/'.$secure_file["name"]."'>Click</a>";
			// header("Content-Description: File Transfer");
			header('Content-type: '.$secure_file["type"]);
			header('Content-Disposition: inline; filename="'.$secure_file["title"].$ext.'"');
			header("Cache-Control: no-cache, must-revalidate");
			readfile('http://integratedlogicsticssolutions.co/temp_files_for_download'.'/'.ENVIRONMENT.'/'.$secure_file["name"]);
			//readfile('http://fleetsmarts.net/temp_files_for_download'.'/'.ENVIRONMENT.'/'.$secure_file["name"]);
			
			//header("Location: ".'http://fleetsmarts.net/temp_files_for_download'.'/'.ENVIRONMENT.'/'.$secure_file["name"]);

			//MOVE FILE BACK FROM PUBLICLY ACCESSIBLE FOLDER TO NON ACCESSABLE FOLDER
			$CI->ftp->move('/public_html/temp_files_for_download'.'/'.ENVIRONMENT.'/'.$secure_file["name"], $full_path);
			//echo ENVIRONMENT;
			
			$CI->ftp->close();
		}
		else
		{
			echo "You do not have file access!";
		}
		//error_log("Hit this line: | LINE ".__LINE__." ".__FILE__);
	}
	
	//DETERMINE IS USER HAS FILE ACCESS
	function user_has_file_access($secure_file)
	{
		$CI =& get_instance();
		
		//GET USER AND ROLE
		$user_id = $CI->session->userdata('user_id');
		$role = $CI->session->userdata('role');
		
		if($role == 'Client')
		{
			if($secure_file["driver_permission"] == 'None')
			{
				return false;
			}
			else if($secure_file["driver_permission"] == 'All')
			{
				return true;
			}
			else if($secure_file["driver_permission"] == 'Access List')
			{
				//SEARCH FOR FILE_ACCESS_PERMISSION
				$where = null;
				$where["file_guid"] = $secure_file["file_guid"];
				$where["user_id"] = $user_id;
				$access_permission = db_select_file_access_permission($where);
				
				if(!empty($access_permission))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			if($secure_file["office_permission"] == 'None')
			{
				if(user_has_permission("download all attachments"))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else if($secure_file["office_permission"] == 'All')
			{
				return true;
			}
			else if($secure_file["office_permission"] == 'Access List')
			{
				//SEARCH FOR FILE_ACCESS_PERMISSION
				$where = null;
				$where["file_guid"] = $secure_file["file_guid"];
				$where["user_id"] = $user_id;
				$access_permission = db_select_file_access_permission($where);
				
				if(!empty($access_permission))
				{
					return true;
				}
				else
				{
					if(user_has_permission("download all attachments"))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
			}
		}
		
	}
	
	function fetch_fuel_avg()
	{
		$url = "http://fuelgaugereport.opisnet.com/index.asp";
		$html = file_get_contents($url);
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
		db_insert_fuel_average($fuel_average);
		
		//echo "Success!";
		
	}

	//RETURNS ARRAY OF THE COMPANIES AND THEIR EXPENSE CATEGORIES
	function get_company_categories()
	{
		
		//GET ALL BUSINESS USERS THAT SHOULD SHOW ON POs
		$where = null;
		$where["show_on_po"] = "Yes";
		$business_users = db_select_companys($where);
		
		$company_categories = null;
		foreach($business_users as $company)
		{
			//GET EXPENSE CATEGORIES
			
			$CI =& get_instance();
		
			//CALCULATE TOTAL DEDUCTIONS
			$values = array();
			$values[] = $company["id"];
			
			$sql = 'SELECT DISTINCT(category) 
					FROM `account` 
					WHERE  company_id = ? 
					AND account_class = "Expense" 
					AND parent_account_id IS NOT NULL 
					ORDER BY category';
			
			$query = $CI->db->query($sql,$values);
			
			$categories = null;
			foreach ($query->result() as $row)
			{
				$categories[] = $row->category;
			}
			
			$categories[] = "Bill Payment";
			$categories[] = "Ticket Payment";
			
			$expense_company = null;
			$expense_company["name"] = $company["company_side_bar_name"];
			$expense_company["show_on_po"] = true;
			$expense_company["id"] = $company["id"];
			$expense_company["categories"] = $categories;
			
			$company_categories[] = $expense_company;
		}
		
		
/**		
		//GET ADVANCETECH COMPANY
		$company = null;
		$where = null;
		$where["type"] = "Business";
		$where["category"] = "Consulting";
		$company = db_select_company($where);
		
		//GET ADVANCETECH EXPENSE CATEGORIES FROM DB
		$where = null;
		$where["company_id"] = $company["id"];
		$expense_categories = db_select_expense_categorys($where);
		
		$categories = null;
		foreach($expense_categories as $exp_cat)
		{
			$categories[] = $exp_cat["category"];
		}
		
		$expense_company = null;
		$expense_company["name"] = $company["company_side_bar_name"];
		$expense_company["show_on_po"] = false;
		$expense_company["id"] = $company["id"];
		$expense_company["categories"] = $categories;
		
		$company_categories['Consulting'] = $expense_company;
		
		
		//GET LEAD GEN COMPANY
		$company = null;
		$where = null;
		$where["type"] = "Business";
		$where["category"] = "Lead Gen";
		$company = db_select_company($where);
		
		//GET COMPANY EXPENSE CATEGORIES FROM DB
		$where = null;
		$where["company_id"] = $company["id"];
		$expense_categories = db_select_expense_categorys($where);
		
		$categories = null;
		foreach($expense_categories as $exp_cat)
		{
			$categories[] = $exp_cat["category"];
		}
		
		$expense_company = null;
		$expense_company["name"] = $company["company_side_bar_name"];
		$expense_company["show_on_po"] = true;
		$expense_company["id"] = $company["id"];
		$expense_company["categories"] = $categories;
		
		$company_categories['Lead Gen'] = $expense_company;
		
		
		//GET DISPATCH COMPANY
		$company = null;
		$where = null;
		$where["type"] = "Business";
		$where["category"] = "Dispatch";
		$company = db_select_company($where);
		
		//GET COMPANY EXPENSE CATEGORIES FROM DB
		$where = null;
		$where["company_id"] = $company["id"];
		$expense_categories = db_select_expense_categorys($where);
		
		$categories = null;
		foreach($expense_categories as $exp_cat)
		{
			$categories[] = $exp_cat["category"];
		}
		
		$expense_company = null;
		$expense_company["name"] = $company["company_side_bar_name"];
		$expense_company["show_on_po"] = true;
		$expense_company["id"] = $company["id"];
		$expense_company["categories"] = $categories;
		
		$company_categories['Dispatch'] = $expense_company;
		
		
		//GET COOP COMPANY
		$company = null;
		$where = null;
		$where["type"] = "Business";
		$where["category"] = "Coop";
		$company = db_select_company($where);
		
		//GET COMPANY EXPENSE CATEGORIES FROM DB
		$where = null;
		$where["company_id"] = $company["id"];
		$expense_categories = db_select_expense_categorys($where);
		
		$categories = null;
		foreach($expense_categories as $exp_cat)
		{
			$categories[] = $exp_cat["category"];
		}
		
		$expense_company = null;
		$expense_company["name"] = $company["company_side_bar_name"];
		$expense_company["show_on_po"] = true;
		$expense_company["id"] = $company["id"];
		$expense_company["categories"] = $categories;
		
		$company_categories['Coop'] = $expense_company;
		
		
		//GET LEASE COMPANY
		$company = null;
		$where = null;
		$where["type"] = "Business";
		$where["category"] = "Leasing";
		$company = db_select_company($where);
		
		//GET COMPANY EXPENSE CATEGORIES FROM DB
		$where = null;
		$where["company_id"] = $company["id"];
		$expense_categories = db_select_expense_categorys($where);
		
		$categories = null;
		foreach($expense_categories as $exp_cat)
		{
			$categories[] = $exp_cat["category"];
		}
		
		$expense_company = null;
		$expense_company["name"] = $company["company_side_bar_name"];
		$expense_company["show_on_po"] = true;
		$expense_company["id"] = $company["id"];
		$expense_company["categories"] = $categories;
		
		$company_categories['Leasing'] = $expense_company;
		
		
		//GET DRIVER SERVICES COMPANY
		$company = null;
		$where = null;
		$where["type"] = "Business";
		$where["category"] = "Driver Services";
		$company = db_select_company($where);
		
		//GET COMPANY EXPENSE CATEGORIES FROM DB
		$where = null;
		$where["company_id"] = $company["id"];
		$expense_categories = db_select_expense_categorys($where);
		
		$categories = null;
		foreach($expense_categories as $exp_cat)
		{
			$categories[] = $exp_cat["category"];
		}
		
		$expense_company = null;
		$expense_company["name"] = $company["company_side_bar_name"];
		$expense_company["show_on_po"] = true;
		$expense_company["id"] = $company["id"];
		$expense_company["categories"] = $categories;
		
		$company_categories['Driver Services'] = $expense_company;
		
		
**/		
		return $company_categories;
	}
	
	function get_expense_categories($company_id)
	{
		//GET EXPENSE CATEGORIES
		
		$CI =& get_instance();
	
		//CALCULATE TOTAL DEDUCTIONS
		$values = array();
		$values[] = $company_id;
		
		$sql = 'SELECT DISTINCT(category) 
				FROM `account` 
				WHERE  company_id = ? 
				AND account_class = "Expense" 
				AND parent_account_id IS NOT NULL 
				ORDER BY category';
		
		$query = $CI->db->query($sql,$values);
		
		$categories = null;
		foreach ($query->result() as $row)
		{
			$categories[] = $row->category;
		}
		
		
		return $categories;
	}
	
	//LOAD SUCCESS VIEW
	function load_upload_success_view()
	{
		$CI =& get_instance();
		
		$data["title"] = "Upload Success";
		$CI->load->view('upload_success_view',$data);
	}
	
	//CREATE AN ACCOUNT ENTRY WITH ACCOUNT BALANCE
	function create_account_entry($entry)
	{
		
		// error_log("*account_id ".$entry["account_id"]." | LINE ".__LINE__." ".__FILE__);
		// error_log("*transaction_id ".$entry["transaction_id"]." | LINE ".__LINE__." ".__FILE__);
		// error_log("*recorder_id ".$entry["recorder_id"]." | LINE ".__LINE__." ".__FILE__);
		// error_log("*recorded_datetime ".$entry["recorded_datetime"]." | LINE ".__LINE__." ".__FILE__);
		// error_log("*entry_datetime ".$entry["entry_datetime"]." | LINE ".__LINE__." ".__FILE__);
		// error_log("*debit_credit ".$entry["debit_credit"]." | LINE ".__LINE__." ".__FILE__);
		// error_log("*entry_amount ".$entry["entry_amount"]." | LINE ".__LINE__." ".__FILE__);
		// error_log("*entry_description ".$entry["entry_description"]." | LINE ".__LINE__." ".__FILE__);
		
		//INSERT ACCOUNT ENTRY
		db_insert_account_entry($entry);
		
		//GET NEWLY CREATED ACCOUNT ENTRY
		$where = null;
		$where["recorded_datetime"] = $entry["recorded_datetime"];
		$where["entry_datetime"] = $entry["entry_datetime"];
		$where["debit_credit"] = $entry["debit_credit"];
		$where["recorded_datetime"] = $entry["recorded_datetime"];
		$where["entry_description"] = $entry["entry_description"];
		$new_account_entry = db_select_account_entry($where);
		
		// error_log("entry_amount ".$new_account_entry["entry_amount"]." | LINE ".__LINE__." ".__FILE__);
		// error_log("new_account_entry id ".$new_account_entry["id"]." | LINE ".__LINE__." ".__FILE__);
		// error_log("account_id ".$entry["account_id"]." | LINE ".__LINE__." ".__FILE__);
		
		//UPDATE BALANCE ON ENTRY
		$update = null;
		$update["account_balance"] = get_account_balance($entry["account_id"]);
		
		$where = null;
		$where["id"] = $new_account_entry["id"];
		db_update_account_entry($update,$where);
	}
	
	//CREATE BALANCED TRANSACTION IN DB
	function create_transaction_and_entries($transaction,$entries,$flagged = FALSE)
	{
		$new_transaction = null;
		
		//ENTRIES: account_id, debit_credit, amount
		$total_credits = 0;
		$total_debits = 0;
		foreach($entries as $entry)
		{
			if($entry["debit_credit"] == "Credit")
			{
				$total_credits = $total_credits + $entry["entry_amount"];
			}
			else if($entry["debit_credit"] == "Debit")
			{
				$total_debits = $total_debits + $entry["entry_amount"];
			}
		}
		
		//echo "debits: ".$total_debits." credits: ".$total_credits;
		
		//CREATE GUID
		$guid = get_random_string(10);
		
		//INSERT TRANSACTION INTO DB
		$insert_transaction = null;
		$insert_transaction["category"] = $transaction["category"];
		$insert_transaction["description"] = $transaction["description"];
		$insert_transaction["guid"] = $guid;
		if($flagged)
		{
			$insert_transaction["flagged"] = "Yes";
		}
		if(round($total_debits - $total_credits,2) != 0)
		{
			$insert_transaction["flagged"] = "Unbalanced";
		}
		db_insert_transaction($insert_transaction);
		
		//GET NEWLY INSERTED TRANSACTION
		$new_transaction = db_select_transaction($insert_transaction);
		
		foreach($entries as $entry)
		{
			$entry["transaction_id"] = $new_transaction['id'];
			
			// error_log("account_id ".$entry["account_id"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("transaction_id ".$entry["transaction_id"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("recorder_id ".$entry["recorder_id"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("recorded_datetime ".$entry["recorded_datetime"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("entry_datetime ".$entry["entry_datetime"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("debit_credit ".$entry["debit_credit"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("entry_amount ".$entry["entry_amount"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("entry_description ".$entry["entry_description"]." | LINE ".__LINE__." ".__FILE__);
			
			create_account_entry($entry);
		}
		
		return $new_transaction;
	}
	
	//CALCULATE INVOICE BALANCE BASED ON ORIGINAL AMOUNT AND PAYMENTS
	function get_invoice_balance($invoice)
	{
		//GET INVOICE PAYMENTS
		$where = null;
		$where["invoice_id"] = $invoice["id"];
		$invoice_payments = db_select_invoice_payments($where);
		
		$total_payments = 0;
		if(!empty($invoice_payments))
		{
			foreach($invoice_payments as $payment)
			{
			
				$total_payments = $total_payments + $payment["account_entry"]["entry_amount"];
			}
		}
		
		$balance = $invoice["invoice_amount"] - $total_payments;
		
		return $balance;
	}
	
	//SETTLE THE A/R ASSOCIATED WITH THE SETTLEMENT EXPENSE WITH A DEBIT TO THE SETTLEMENT PAYABLE ACCOUNT
	function settle_expenses_w_driver_ap_account($account_entries,$driver_company)
	{
		$entries = array();
		$invoice_payment_entries = array();
		
		foreach($account_entries as $account_entry)
		{
			//CREATE CREDIT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $account_entry["account_id"];
			$credit_entry["recorder_id"] = $account_entry["recorder_id"];
			$credit_entry["recorded_datetime"] = $account_entry["recorded_datetime"];
			$credit_entry["entry_datetime"] = $account_entry["entry_datetime"];
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $account_entry["entry_amount"];
			$credit_entry["entry_description"] = "Settle Expense - ".$account_entry["entry_description"];
			//$credit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $credit_entry;
			
			if(!empty($account_entry["invoice_id"]))
			{
				//GET CREDIT ACCOUNT ENTRY
				$credit_entry["invoice_id"] = $account_entry["invoice_id"];
				$invoice_payment_entries[] = $credit_entry;
				
			}
			
			//GET DEFAULT DRIVER SETTLEMENT PAYABLE ACCOUNT
			$where = null;
			$where["company_id"] = $driver_company["id"];
			$where["category"] = "Coop A/P to Member on Settlements";
			$default_settlement_acc = db_select_default_account($where);
			
			//error_log("default_settlement_acc ".$default_settlement_acc["account_id"]." | LINE ".__LINE__." ".__FILE__);
			
			//CREATE DEBIT ENTRY
			$debit_entry = null;
			$debit_entry["account_id"] = $default_settlement_acc["account_id"];
			$debit_entry["recorder_id"] = $account_entry["recorder_id"];
			$debit_entry["recorded_datetime"] = $account_entry["recorded_datetime"];
			$debit_entry["entry_datetime"] = $account_entry["entry_datetime"];
			$debit_entry["debit_credit"] = "Debit";
			$debit_entry["entry_amount"] = $account_entry["entry_amount"];
			$debit_entry["entry_description"] = "Settle Expense - ".$account_entry["entry_description"];
			//$debit_entry["file_guid"] = $contract_secure_file["file_guid"];
			
			$entries[] = $debit_entry;
			
			
		}
		
		//CREATE THE TRANSACTION AND ENTER THE ENTRIES INTO THE DB
		$transaction = null;
		$transaction["category"] = "Settle Expenses";
		$transaction["description"] = "Settle Expenses - ".$driver_company["company_side_bar_name"]." ".date_format("m/d/y",strtotime($invoice_date));
		$new_transaction = create_transaction_and_entries($transaction,$entries);
		
		foreach($invoice_payment_entries as $account_entry)
		{
			//GET NEWLY CREATED ACCOUNT ENTRY
			$credit_entry = null;
			$credit_entry["account_id"] = $account_entry["account_id"];
			$credit_entry["recorder_id"] = $account_entry["recorder_id"];
			$credit_entry["recorded_datetime"] = $account_entry["recorded_datetime"];
			$credit_entry["entry_datetime"] = $account_entry["entry_datetime"];
			$credit_entry["debit_credit"] = "Credit";
			$credit_entry["entry_amount"] = $account_entry["entry_amount"];
			$credit_entry["entry_description"] = $account_entry["entry_description"];
			
			// error_log("account_id ".$credit_entry["account_id"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("recorder_id ".$credit_entry["recorder_id"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("recorded_datetime ".$credit_entry["recorded_datetime"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("entry_datetime ".$credit_entry["entry_datetime"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("debit_credit ".$credit_entry["debit_credit"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("entry_amount ".$credit_entry["entry_amount"]." | LINE ".__LINE__." ".__FILE__);
			// error_log("entry_description ".$credit_entry["entry_description"]." | LINE ".__LINE__." ".__FILE__);
			
			$newly_created_account_entry = db_select_account_entry($credit_entry);
			
			//CREATE INVOICE PAYMENT
			$invoice_payment = null;
			$invoice_payment["invoice_id"] = $account_entry["invoice_id"];
			$invoice_payment["account_entry_id"] = $newly_created_account_entry["id"];
			db_insert_invoice_payment($invoice_payment);
			
			//UPDATE INVOICE
			$update_invoice = null;
			$update_invoice["closed_datetime"] = $account_entry["recorded_datetime"];
			
			$where = null;
			$where["id"] = $account_entry["invoice_id"];
			db_update_invoice($update_invoice,$where);
		}
		
		return $new_transaction;
		
	}
	
	//CALCULATES THE BALANCE OF A GIVEN TICKET
	function get_ticket_balance($ticket)
	{
		//GET TOTAL OF TICKET PAYMENTS
		$where = null;
		$where["ticket_id"] = $ticket["id"];
		$ticket_payments = db_select_ticket_payments($where);
		
		$total_payments = 0;
		if(!empty($ticket_payments))
		{
			foreach($ticket_payments as $ticket_payment)
			{
				if($ticket_payment["account_entry"]["debit_credit"] = "Debit")
				{
					$total_payments = $total_payments + $ticket_payment["account_entry"]["entry_amount"];
				}
				else if($ticket_payment["account_entry"]["debit_credit"] = "Debit")
				{
					$total_payments = $total_payments - $ticket_payment["account_entry"]["entry_amount"];
				}
			}
		}
		
		return round($ticket["amount"] - $total_payments,2);
	}
	
	//UPDATE INVOICE STATUS BASED ON BALANCE
	function update_invoice_status($invoice_id)
	{
		//SET TIMEZONE
		date_default_timezone_set('America/Denver');
		$recorded_datetime = date("Y-m-d G:i:s");
		
		//GET INVOICE
		$where = null;
		$where["id"] = $invoice_id;
		$invoice = db_select_invoice($where);
		
		if(round(get_invoice_balance($invoice),2) == 0)
		{
			$update_invoice = null;
			$update_invoice["closed_datetime"] = $recorded_datetime;
			$where = null;
			$where["id"] = $invoice_id;
			db_update_invoice($update_invoice,$where);
		}
		
	}
	
	//DETERMINES IF USER IS RELATED TO GIVEN COMPANY ID: RETURNS TRUE OR FALSE BOOLEAN
	function user_is_assigned_to_business($company_id)
	{
		$CI =& get_instance();
		
		//GET USER AND ROLE
		$user_id = $CI->session->userdata('user_id');
		
		//GET USER
		$where = null;
		$where["id"] = $user_id;
		$user = db_select_user($where);
		
		//GET USER COMPANY
		$where = null;
		//$where["person_id"] = $user["person_id"];
		$where = " person_id = ".$user["person_id"]." AND (category = 'Office Staff' OR category = 'Fleet Manager')";
		$user_company = db_select_company($where);
		
		//GET RELATIONSHIP
		$where = null;
		$where["business_id"] = $company_id;
		$where["related_business_id"] = $user_company["id"];
		$where["relationship"] = "Staff";
		$relationship = db_select_business_relationship($where);
		
		if(empty($relationship))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	//VALIDATIONS FOR CHECK CALL COMPLETENESS
	function check_call_is_complete($check_call)
	{
		$is_complete = true;
		$message = "";
		
		//YESTERDAY'S HOS LOGS
		if(empty($check_call["d1_logbook_file_guid"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Logbook is missing. ";
		}
		
		if(empty($check_call["d2_logbook_file_guid"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Logbook is missing. ";
		}
		
		//YESTERDAY'S PERFORMANCE SECTION
		if(empty($check_call["night_recap"]))
		{
			$is_complete = false;
			$message = $message."Night Recap is missing. ";
		}
		
		if(empty($check_call["fuel_plan_followed"]))
		{
			$is_complete = false;
			$message = $message."Fuel Plan Followed is missing. ";
		}
		
		if(empty($check_call["paperwork_plan_followed"]))
		{
			$is_complete = false;
			$message = $message."Paperwork Plan Followed is missing. ";
		}
		
		if(empty($check_call["reefer_instructions_followed"]))
		{
			$is_complete = false;
			$message = $message."Reefer Instructions Followed is missing. ";
		}
		
		if(empty($check_call["goal_met"]))
		{
			$is_complete = false;
			$message = $message."Morning Goal Met is missing. ";
		}
		
		if(empty($check_call["map_miles"]))
		{
			$is_complete = false;
			$message = $message."Map Miles is missing. ";
		}
		
		if(empty($check_call["odometer_miles"]))
		{
			$is_complete = false;
			$message = $message."Odometer Miles is missing. ";
		}
		
		if(empty($check_call["last_mpg"]))
		{
			$is_complete = false;
			$message = $message."Recent MPG is missing. ";
		}
		
		if(empty($check_call["morning_checkcall_guid"]))
		{
			$is_complete = false;
			$message = $message."Morning Check Call Audio is missing. ";
		}
		
		
		//YESTERDAY'S DRIVER EVALUATION
		if(empty($check_call["d1_pleasantness"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Ability is missing. ";
		}
		
		if(empty($check_call["d1_attitude"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Attitude is missing. ";
		}
		
		if(empty($check_call["d1_skill"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Skill Level is missing. ";
		}
		
		if(empty($check_call["d1_eval_notes"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Eval is missing. ";
		}
		
		if(empty($check_call["d2_pleasantness"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Ability is missing. ";
		}
		
		if(empty($check_call["d2_attitude"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Attitude is missing. ";
		}
		
		if(empty($check_call["d2_skill"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Skill Level is missing. ";
		}
		
		if(empty($check_call["d2_eval_notes"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Eval is missing. ";
		}
		
		
		//TODAY'S NIGHT PLAN SECTION
		if(empty($check_call["day_recap"]))
		{
			$is_complete = false;
			$message = $message."Day Recap is missing. ";
		}
		
		if(empty($check_call["night_plan"]))
		{
			$is_complete = false;
			$message = $message."Plan For Night is missing. ";
		}
		
		if(empty($check_call["fuel_plan"]))
		{
			$is_complete = false;
			$message = $message."Fuel Plan is missing. ";
		}
		
		if(empty($check_call["paperwork_plan"]))
		{
			$is_complete = false;
			$message = $message."Paperwork Plan is missing. ";
		}
		
		if(empty($check_call["morning_goal"]))
		{
			$is_complete = false;
			$message = $message."Morning Goal is missing. ";
		}
		
		if(empty($check_call["reefer_instructions"]))
		{
			$is_complete = false;
			$message = $message."Reefer Instructions is missing. ";
		}
		
		
		
		//EVENING RECAP SECTION
		if(empty($check_call["night_dispatch_eval"]))
		{
			$is_complete = false;
			$message = $message."Evening Recap is missing. ";
		}
		
		if(empty($check_call["evening_checkcall_guid"]))
		{
			$is_complete = false;
			$message = $message."Evening Check Call Audio is missing. ";
		}
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
		
	}
	
	function check_call_hos_is_complete($check_call)
	{
		$is_complete = true;
		$message = "";
		
		//YESTERDAY'S HOS LOGS
		if(empty($check_call["d1_logbook_file_guid"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Logbook is missing. ";
		}
		
		if(empty($check_call["d2_logbook_file_guid"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Logbook is missing. ";
		}
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
		
	}
	
	function check_call_performance_is_complete($check_call)
	{
		$is_complete = true;
		$message = "";
		
		
		//YESTERDAY'S PERFORMANCE SECTION
		if(empty($check_call["night_recap"]))
		{
			$is_complete = false;
			$message = $message."Night Recap is missing. ";
		}
		
		if(empty($check_call["fuel_plan_followed"]))
		{
			$is_complete = false;
			$message = $message."Fuel Plan Followed is missing. ";
		}
		
		if(empty($check_call["paperwork_plan_followed"]))
		{
			$is_complete = false;
			$message = $message."Paperwork Plan Followed is missing. ";
		}
		
		if(empty($check_call["reefer_instructions_followed"]))
		{
			$is_complete = false;
			$message = $message."Reefer Instructions Followed is missing. ";
		}
		
		if(empty($check_call["goal_met"]))
		{
			$is_complete = false;
			$message = $message."Morning Goal Met is missing. ";
		}
		
		if(empty($check_call["map_miles"]))
		{
			$is_complete = false;
			$message = $message."Map Miles is missing. ";
		}
		
		if(empty($check_call["odometer_miles"]))
		{
			$is_complete = false;
			$message = $message."Odometer Miles is missing. ";
		}
		
		if(empty($check_call["last_mpg"]))
		{
			$is_complete = false;
			$message = $message."Recent MPG is missing. ";
		}
		
		if(empty($check_call["morning_checkcall_guid"]))
		{
			$is_complete = false;
			$message = $message."Morning Check Call Audio is missing. ";
		}
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
		
	}
	
	function check_call_evaluation_is_complete($check_call)
	{
		$is_complete = true;
		$message = "";
		
		
		//YESTERDAY'S DRIVER EVALUATION
		if(empty($check_call["d1_pleasantness"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Ability is missing. ";
		}
		
		if(empty($check_call["d1_attitude"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Attitude is missing. ";
		}
		
		if(empty($check_call["d1_skill"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Skill Level is missing. ";
		}
		
		if(empty($check_call["d1_eval_notes"]))
		{
			$is_complete = false;
			$message = $message."Driver 1 Eval is missing. ";
		}
		
		if(empty($check_call["d2_pleasantness"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Ability is missing. ";
		}
		
		if(empty($check_call["d2_attitude"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Attitude is missing. ";
		}
		
		if(empty($check_call["d2_skill"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Skill Level is missing. ";
		}
		
		if(empty($check_call["d2_eval_notes"]))
		{
			$is_complete = false;
			$message = $message."Driver 2 Eval is missing. ";
		}
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
		
	}
	
	function check_call_plan_is_complete($check_call)
	{
		$is_complete = true;
		$message = "";
		
		//TODAY'S NIGHT PLAN SECTION
		if(empty($check_call["day_recap"]))
		{
			$is_complete = false;
			$message = $message."Day Recap is missing. ";
		}
		
		if(empty($check_call["night_plan"]))
		{
			$is_complete = false;
			$message = $message."Plan For Night is missing. ";
		}
		
		if(empty($check_call["fuel_plan"]))
		{
			$is_complete = false;
			$message = $message."Fuel Plan is missing. ";
		}
		
		if(empty($check_call["paperwork_plan"]))
		{
			$is_complete = false;
			$message = $message."Paperwork Plan is missing. ";
		}
		
		if(empty($check_call["morning_goal"]))
		{
			$is_complete = false;
			$message = $message."Morning Goal is missing. ";
		}
		
		if(empty($check_call["reefer_instructions"]))
		{
			$is_complete = false;
			$message = $message."Reefer Instructions is missing. ";
		}
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
		
	}
	
	function check_call_recap_is_complete($check_call)
	{
		$is_complete = true;
		$message = "";
		
		//EVENING RECAP SECTION
		if(empty($check_call["night_dispatch_eval"]))
		{
			$is_complete = false;
			$message = $message."Evening Recap is missing. ";
		}
		
		if(empty($check_call["evening_checkcall_guid"]))
		{
			$is_complete = false;
			$message = $message."Evening Check Call Audio is missing. ";
		}
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
		
	}
	
	//VALIDATIONS FOR SHIFT REPORT COMPLETENESS
	function shift_report_is_complete($shift_report)
	{
		$is_complete = true;
		$shift_report_completeness_message = "";
		
		$shift_report_details_is_complete = shift_report_details_is_complete($shift_report);
		$shift_report_plans_is_complete = shift_report_plans_is_complete($shift_report);
		$shift_report_goalpoints_is_complete = shift_report_goalpoints_is_complete($shift_report);
		$shift_report_recap_is_complete = shift_report_recap_is_complete($shift_report);
		
		if(!$shift_report_details_is_complete["is_complete"])
		{
			$is_complete = false;
			$shift_report_completeness_message = $shift_report_completeness_message.$shift_report_details_is_complete["message"]." ";
		}
		if(!$shift_report_plans_is_complete["is_complete"])
		{
			$is_complete = false;
			$shift_report_completeness_message = $shift_report_completeness_message.$shift_report_plans_is_complete["message"]." ";
		}
		if(!$shift_report_goalpoints_is_complete["is_complete"])
		{
			$is_complete = false;
			$shift_report_completeness_message = $shift_report_completeness_message.$shift_report_goalpoints_is_complete["message"]." ";
		}
		if(!$shift_report_recap_is_complete["is_complete"])
		{
			$is_complete = false;
			$shift_report_completeness_message = $shift_report_completeness_message.$shift_report_recap_is_complete["message"]." ";
		}
		
		if($is_complete)
		{
			$shift_report_completeness_message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $shift_report_completeness_message;
		
		return $result;
	}
	function shift_report_details_is_complete($shift_report)
	{
		$is_complete = true;
		$message = "";
		
		//VALIDATE ALL FIELDS ARE COMPLETE
		if(!isset($shift_report["client_id"]))
		{
			$is_complete = false;
			$message = $message."Driver is missing. ";
		}
		if(!isset($shift_report["shift_s_time"]))
		{
			$is_complete = false;
			$message = $message."Start Time is missing. ";
		}
		if(!isset($shift_report["shift_s_gps"]))
		{
			$is_complete = false;
			$message = $message."Start Location is missing. ";
		}
		if(!isset($shift_report["shift_s_odometer"]))
		{
			$is_complete = false;
			$message = $message."Start Odometer is missing. ";
		}
		if(!isset($shift_report["shift_s_fuel_level"]))
		{
			$is_complete = false;
			$message = $message."Start Fuel Level is missing. ";
		}
		if(!isset($shift_report["shift_e_time"]))
		{
			$is_complete = false;
			$message = $message."End Time is missing. ";
		}
		if(!isset($shift_report["shift_e_gps"]))
		{
			$is_complete = false;
			$message = $message."End Location is missing. ";
		}
		if(!isset($shift_report["shift_e_odometer"]))
		{
			$is_complete = false;
			$message = $message."End Odometer is missing. ";
		}
		if(!isset($shift_report["shift_e_fuel_level"]))
		{
			$is_complete = false;
			$message = $message."End Fuel Level is missing. ";
		}
		// if(!isset($shift_report["hos_file_guid"]))
		// {
			// $is_complete = false;
			// $message = $message."Driver Log Book is missing. ";
		// }
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
	}
	function shift_report_plans_is_complete($shift_report)
	{
		$is_complete = true;
		$message = "";
		
		//VALIDATE ALL FIELDS ARE COMPLETE
		if(!isset($shift_report["plan_summary"]))
		{
			$is_complete = false;
			$message = $message."Plan Summary is missing. ";
		}
		if(!isset($shift_report["fuel_plan"]))
		{
			$is_complete = false;
			$message = $message."Fuel Plan is missing. ";
		}
		if(!isset($shift_report["toll_plan"]))
		{
			$is_complete = false;
			$message = $message."Toll Plan is missing. ";
		}
		if(!isset($shift_report["route_plan"]))
		{
			$is_complete = false;
			$message = $message."Route Plan is missing. ";
		}
		if(!isset($shift_report["audio_w_driver_file_guid"]))
		{
			$is_complete = false;
			$message = $message."Call with Driver audio file is missing. ";
		}
		if(!isset($shift_report["audio_w_dispatch_file_guid"]))
		{
			$is_complete = false;
			$message = $message."Call with Dispatcher audio file is missing. ";
		}
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
		
	}
	function shift_report_goalpoints_is_complete($shift_report)
	{
		$is_complete = true;
		$message = "";
		
		//GET END GOALPOINT THAT IS COMPLETE
		$where = null;
		$where = "shift_report_id = ".$shift_report["id"]." AND gp_type = 'End' AND completion_time IS NOT NULL";
		
		$end_gp = db_select_goalpoint($where);
		
		if(empty($end_gp))
		{
			$is_complete = false;
			$message = $message."Completed End Goalpoint is missing. ";
		}
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
		
	}
	function shift_report_recap_is_complete($shift_report)
	{
		$is_complete = true;
		$message = "";
		
		//VALIDATE ALL FIELDS ARE COMPLETE
		if(!isset($shift_report["dispatch_notes"]))
		{
			$is_complete = false;
			$message = $message."Dispatcher Notes is missing. ";
		}
		if(!isset($shift_report["idle_time"]))
		{
			$is_complete = false;
			$message = $message."Engine Idle Time is missing. ";
		}
		
		if($is_complete)
		{
			$message = "Complete!";
		}
		
		$result["is_complete"] = $is_complete;
		$result["message"] = $message;
		
		return $result;
		
	}
	
	//RETURNS MOST RECENT GEOPOINT FOR GIVEN TRUCK ID
	function get_most_recent_geopoint($truck_id)
	{
		$where = null;
		//$where["truck_id"] = $truck_id;
		$where = "truck_id = $truck_id AND datetime = (SELECT MAX(datetime) FROM geopoint WHERE truck_id = $truck_id)";
		$geopoint = db_select_geopoint($where);
		
		return $geopoint;
	}
	
	//RETURNS MOST RECENT TRAILER GEOPOINT FOR GIVEN TRAILER ID
	function get_most_recent_trailer_geopoint($trailer_id)
	{
		if(!empty($trailer_id))
		{
			$where = null;
			//$where["truck_id"] = $truck_id;
			$where = "trailer_id = $trailer_id AND datetime_occurred = (SELECT MAX(datetime_occurred) FROM trailer_geopoint WHERE trailer_id = $trailer_id)";
			$trailer_geopoint = db_select_trailer_geopoint($where);
			
			return $trailer_geopoint;
		}
		else
		{
			return null;
		}
	}
	
	//CREATES-UPDATES GOALPOINT THAT IS CURRENT POSITION AND TIME (called from load_goalpoints_div())
	function update_current_goalpoint_from_geopoint($load_id)
	{
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		if(!empty($load["load_truck_id"]))
		{
			$current_geopoint = get_most_recent_geopoint($load["load_truck_id"]);
			
			if(!empty($current_geopoint))
			{
				//GET LATEST GOALPOINT TO BE MARKED COMPLETED FOR LOAD
				$where = null;
				$where = "load_id = $load_id AND completion_time IS NOT NULL AND expected_time = (SELECT MAX(expected_time) FROM goalpoint WHERE load_id = $load_id AND completion_time IS NOT NULL)";
				$last_completed_goalpoint = db_select_goalpoint($where);
				if(empty($last_completed_goalpoint))
				{
					$last_complete_goalpoint_order = 0;
				}
				else
				{
					$last_complete_goalpoint_order = $last_completed_goalpoint["gp_order"];
				}
				
				//GET GOALPOINT THAT IS TYPE CURRENT GEOPOINT
				$where = null;
				$where["load_id"] = $load_id;
				$where["gp_type"] = "Current Geopoint";
				$current_goalpoint = db_select_goalpoint($where);
				
				$geocode_data = reverse_geocode($current_geopoint["latitude"].", ".$current_geopoint["longitude"]);
				$location_name = $geocode_data["street_number"]." ".$geocode_data["street"];
				//CREATE NOTES
				if($current_geopoint["speed"] == 0)
				{
					$dm_notes = "STOPPED";
				}
				else
				{
					$dm_notes = round($current_geopoint["speed"])." MPH ".get_cardinal_directions($current_geopoint["heading"]);
				}
				
				if(empty($current_goalpoint))
				{
					
					//CREATE NEW GOALPOINT
					$insert = null;
					$insert["load_id"] = $load["id"];
					$insert["truck_id"] = $load["load_truck_id"];
					$insert["trailer_id"] = $load["load_trailer_id"];
					$insert["client_id"] = $load["client_id"];
					$insert["gp_order"] = $last_complete_goalpoint_order + 1;
					$insert["sync_gp_guid"] = get_random_string(10);
					$insert["expected_time"] = $current_geopoint["datetime"];
					$insert["gp_type"] = "Current Geopoint";
					$insert["gps"] = $current_geopoint["latitude"].", ".$current_geopoint["longitude"];
					$insert["location_name"] = $location_name;
					$insert["location"] = $geocode_data["city"].", ".$geocode_data["state"];
					$insert["dm_notes"] = $dm_notes;
					
					db_insert_goalpoint($insert);
				}
				else
				{
					//UPDATE CURRENT GOALPOINT
					//CREATE NEW GOALPOINT
					$update = null;
					$update["load_id"] = $load["id"];
					$update["truck_id"] = $load["load_truck_id"];
					$update["trailer_id"] = $load["load_trailer_id"];
					$update["client_id"] = $load["client_id"];
					$update["gp_order"] = $last_complete_goalpoint_order + 1;
					//$update["sync_gp_guid"] = get_random_string(10);
					$update["expected_time"] = $current_geopoint["datetime"];
					$update["gp_type"] = "Current Geopoint";
					$update["gps"] = $current_geopoint["latitude"].", ".$current_geopoint["longitude"];
					$update["location_name"] = $location_name;
					$update["location"] = $geocode_data["city"].", ".$geocode_data["state"];
					$update["dm_notes"] = $dm_notes;
					
					$where = null;
					$where["id"] = $current_goalpoint["id"];
					db_update_goalpoint($update,$where);
				}
				
				$current_geopoint_order = $last_complete_goalpoint_order + 1;
				
				//REORDER ALL SUBSEQUENT GOALPOINTS
				$where = null;
				$where = "load_id = $load_id AND completion_time IS NULL AND gp_order >= $current_geopoint_order AND gp_type <> 'Current Geopoint'";
				$incomplete_goalpoints = db_select_goalpoints($where,"gp_order");
				if(!empty($incomplete_goalpoints))
				{
					$gp_order_counter = 1;
					foreach($incomplete_goalpoints as $incomplete_goalpoint)
					{
						$update = null;
						$update["gp_order"] = $current_geopoint_order + $gp_order_counter;
						
						$where = null;
						$where["id"] = $incomplete_goalpoint["id"];
						db_update_goalpoint($update,$where);
						
						$gp_order_counter++;
					}
				}
			}
		}
	}
	
	//REORDERS THE COMPLETED GOALPOINT ACCORDING TO COMPLETION TIME
	function order_completed_goalpoints($load_id)
	{
		//GET ALL COMPLETED GOALPOINTS FOR THIS LOAD
		$where = null;
		$where = " load_id = $load_id and completion_time IS NOT NULL";
		$completed_goalpoints = db_select_goalpoints($where,"completion_time");
		
		$counter = 0;
		foreach($completed_goalpoints as $gp)
		{
			$counter++;
			
			$update = null;
			$update["gp_order"] = $counter;
			
			$where = null;
			$where["id"] = $gp["id"];
			db_update_goalpoint($update,$where);
		}
	}
	
	//LIST OF EXPECTED DURATIONS FOR DIFFERENT TYPES OF GOALPOINT EVENTS
	function get_expected_goalpoint_durations($gp_type)
	{
		if($gp_type == "Pick")
		{
			$event_duration = 2*60;//1 hour
		}
		else if($gp_type == "Drop")
		{
			$event_duration = 2*60;//1 hour
		}
		else if($gp_type == "Truck Change")
		{
			$event_duration = 30;//15 minutes
		}
		else if($gp_type == "Driver Change")
		{
			$event_duration = 30;//15 minutes
		}
		else if($gp_type == "Trailer Change")
		{
			$event_duration = 15;//15 minutes
		}
		else if($gp_type == "Fuel")
		{
			$event_duration = 30;//30 minutes
		}
		else if($gp_type == "Break")
		{
			$event_duration = 15;//15 minutes
		}
		else
		{
			$event_duration = 0;//0 minutes
		}
		
		return $event_duration;
	}
	
	//CALCULATE EXPECTED GP TIME WITH GIVEN LOAD ID
	function calc_expected_gp_times($load_id)
	{
		date_default_timezone_set('America/Denver');
		
		$map_events = array();
		$starting_event_time = null;
		
		//DETERMINE STARTING POINT
		//SELECT THE MOST RECENT COMPLETED GOALPOINT
		$where = null;
		$where = "load_id = $load_id AND completion_time IS NOT NULL AND expected_time = (SELECT MAX(expected_time) FROM goalpoint WHERE load_id = $load_id AND completion_time IS NOT NULL)";
		$most_recently_completed_gp = db_select_goalpoint($where);
		
		//GET CURRENT GEOPOINT GOALPOINT
		$where = null;
		$where["gp_type"] = "Current Geopoint";
		$where["load_id"] = $load_id;
		$current_geopoint_goalpoint = db_select_goalpoint($where);
		
		if(!empty($current_geopoint_goalpoint))
		{
			$starting_event_time = strtotime($current_geopoint_goalpoint["expected_time"]);
			
			$map_event["gps_coordinates"] = $current_geopoint_goalpoint["gps"];
			if(!empty($map_event["gps_coordinates"]))
			{
				$map_events[] = $map_event;
			}
		}
		
		//GET GOALPOINTS THAT AREN'T MARKED COMPLETE YET
		$where = null;
		$where["load_id"] = $load_id;
		$where["completion_time"] = null;
		$goalpoints = db_select_goalpoints($where,"gp_order");
		
		//CALCULATE EXPECTED TIMES
		$i = 1;
		$total_event_durations = 0;//in seconds
		if(!empty($most_recently_completed_gp))
		{
			//MAKE SURE TO ACCOUNT FOR EXPECTED SITTING TIME ON THE FIRST EVENT EXPECTED TIME
			if($most_recently_completed_gp["arrival_departure"] == "Arrival")
			{
				$seconds_already_sat = strtotime($starting_event_time) - strtotime($most_recently_completed_gp["expected_time"]);
				
				//IF MOST RECENT CONTACT ATTEMPT AFTER CURRENT ARRIVAL TIME
				if($seconds_already_sat > 0)
				{
					$remaining_seconds_expected_at_stop = ($most_recently_completed_gp["duration"]*60) - ($seconds_already_sat);
					$total_event_durations = $remaining_seconds_expected_at_stop;
				}
				else
				{
					$total_event_durations = $most_recently_completed_gp["duration"]*60;
				}
			}
		}
		$previous_map_event = null;;
		if(!empty($goalpoints))
		{
			foreach($goalpoints as $gp)
			{
				if(empty($starting_event_time))
				{
					$starting_event_time = strtotime($gp["expected_time"]);
				}
				
				// if($i == 1)//ON FIRST LOOP, SET PREVIOUS EVENT TIME TO START EVENT TIME
				// {
					// $previous_event_expected_time = $starting_event_time;
				// }
				
				if($gp["gp_type"] != "Current Geopoint")
				{
					$map_event = null;
					$map_event["gps_coordinates"] = $gp["gps"];
					
					//REMOVE EVENTS WITH NO GPS
					if(!empty($map_event["gps_coordinates"]))
					{
						//REMOVE DUPLICATES
						if($map_event["gps_coordinates"] != $previous_map_event["gps_coordinates"])
						{
							$map_events[] = $map_event;
							$previous_map_event = $map_event;
						}
					}
					
					$map_info = null;
					if(!empty($map_events))
					{
						$map_info = get_map_info($map_events); 
					}
					
					$map_miles = $map_info["map_miles"];
					
					
					$average_speed = 55;
					$hours_to_gp = $map_miles/$average_speed;
					
					
					$expected_time = round($starting_event_time + $hours_to_gp*60*60 + $total_event_durations);//hour*minutes*seconds

					//echo $gp["gp_type"]." + ".$starting_event_time." + ".($hours_to_gp*60*60)." + ".$total_event_durations." = ".$expected_time." ".date("Y-m-d H:i:s",$expected_time)."<br>";
					
					$update = null;
					$update["expected_time"] = date("Y-m-d H:i:s",$expected_time);
					$update["leeway"] = $map_miles;
					$update["dispatch_notes"] = $map_info["route_url"];
					
					$where = null;
					$where["id"] = $gp["id"];
					db_update_goalpoint($update,$where);

					//DETERMINE GP DURATION
					$event_duration = $gp["duration"] * 60;//minutes to seconds
					
					$total_event_durations = $total_event_durations + $event_duration;
				}
				else
				{
					// $update = null;
					// $update["expected_time"] = date("Y-m-d H:i:s",strtotime($log_entry["entry_datetime"]));
					
					// $where = null;
					// $where["id"] = $gp["id"];
					// db_update_goalpoint($update,$where);
				}
				
				// $previous_event_expected_time = strtotime($gp["expected_time"]);
			}
		}

		//CALCULATE LEEWAY
		
		//GET GOALPOINTS THAT AREN'T MARKED COMPLETE YET
		$where = null;
		$where["load_id"] = $load_id;
		$where["completion_time"] = null;
		$goalpoints = db_select_goalpoints($where,"gp_order DESC");
		
		//REVERSE ORDER
		if(!empty($goalpoints))
		{
			$i = 1;
			foreach($goalpoints as $gp)
			{
				$leeway = strtotime($gp["deadline"]) - strtotime($gp["expected_time"]);
					
				if(!empty($gp["deadline"]))
				{
					if($i == 1)
					{
						$min_leeway = $leeway;
						//echo $leeway;
					}
					
					if($leeway < $min_leeway)
					{
						$min_leeway = $leeway;
					}
					
					$update = null;
					$update["leeway"] = $min_leeway/60/60;//in hours
					//echo $min_leeway/60/60;//in hours
					$i++;
				}
				else
				{
					//IF LEEWAY WAS CALCULATED FOR LATER EVENT
					if(isset($min_leeway))
					{
						$update = null;
						$update["leeway"] = $min_leeway/60/60;//in hours
					}
					else
					{
						$update = null;
						$update["leeway"] = null;
					}
					
				}
				
				$where = null;
				$where["id"] = $gp["id"];
				db_update_goalpoint($update,$where);
				//print_r($update);
			}
		}
	}
	
	function convert_hours_to_duration_text($hours)
	{
		//$minutes = ($hours*60%60);
		//$hours = floor(abs($hours));
		$minutes = str_pad((abs($hours)*60%60),2,'0',STR_PAD_LEFT);
		$hours = str_pad(floor(abs($hours)),2,'0',STR_PAD_LEFT);
		return $hours.":".$minutes;
	}
	
	function hours_to_text($hours)
	{
		if(abs($hours) >= 1)
		{
			return round($hours,2)." hr";
		}
		else
		{
			return round($hours*60)." min";
		}
		
	}
	
	function hours_to_text_mixed($hours,$hours_wording="hour",$minutes_wording="minute")
	{
		if($hours == 0)
		{
			return "0 ".$hours_wording."s";
		}
		
		$just_minutes = (abs($hours)*60%60);
		$just_hours = floor(abs($hours));
		
		$minutes_text = $just_minutes." ".$minutes_wording;
		$hours_text = $just_hours." ".$hours_wording;
		
		if($just_minutes <> 1)
		{
			$minutes_text = $minutes_text."s";
		}
		
		if($just_hours <> 1)
		{
			$hours_text = $hours_text."s";
		}
		
		if($just_hours == 0)
		{
			return $minutes_text;
		}
		else if($just_minutes == 0)
		{
			return $hours_text;
		}
		else
		{
			return $hours_text." and ".$minutes_text;
		}
	}
	
	//REPLACES THE LAST OCCURENCE OF STRING IN STRING
	function str_lreplace($search, $replace, $subject)
	{
		$pos = strrpos($subject, $search);

		if($pos !== false)
		{
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}

		return $subject;
	}
	
	//OLD - NO LONGER USED
	//CALCULATES EXPECTATIONS FROM ONE CONTACT ATTEMPT TO THE NEXT -- GENERATES EXPECTED MILES, ACTUAL MILES, AND COMPUTER NOTES
	function calculate_driving_expectations($new_ca)//needs $new_ca["shift_report_id"], $new_ca["gps"], $new_ca["ca_time"]
	{
		date_default_timezone_set('America/Denver');
		
		$expections = array();
		
		$expected_miles = 0;
		$actual_miles = 0;
		$efficiency = 0;
		$computer_notes = "No previous contact attempts";
		
		$shift_report_id = $new_ca["shift_report_id"];

		
		//GET PREVIOUS CONTACT ATTEMPT
		$where = null;
		$where = "shift_report_id = $shift_report_id AND ca_time <= '".date("Y-m-d H:i:s",strtotime($new_ca["ca_time"]))."' AND ca_time = (SELECT MAX(ca_time) FROM contact_attempt WHERE shift_report_id = $shift_report_id AND ca_time < '".date("Y-m-d H:i:s",strtotime($new_ca["ca_time"]))."')";
		$previous_ca = db_select_contact_attempt($where);
		
		$map_events = array();
		if(!empty($previous_ca))
		{
			$minutes_between_cas = (strtotime($new_ca["ca_time"]) - strtotime($previous_ca["ca_time"]))/60;
			
			//CALCULATE HOW MANY MILES THE DRIVER ACTUALLY WENT
			$starting_event["gps_coordinates"] = $previous_ca["ca_gps"];
			$map_events[] = $starting_event;
			
			//GET ALL GOALPOINTS MARKED COMPLETE THAT ARE AFTER PREVIOUS_CA AND BEFORE NEW_CA
			$where = null;
			$where = " expected_time > '".date("Y-m-d H:i:s",strtotime($previous_ca["ca_time"]))."' AND expected_time < '".date("Y-m-d H:i:s",strtotime($new_ca["ca_time"]))."' AND shift_report_id = $shift_report_id AND completion_time IS NOT NULL";
			$completed_goalpoints = db_select_goalpoints($where,"gp_order");
			
			$minutes_lost_text = ". In this time, the truck";
			$total_expected_minutes_lost = 0;//IN MINUTES
			if(!empty($completed_goalpoints))
			{
				
				$i = 0;
				foreach($completed_goalpoints as $gp)
				{
					$i++;
					$actual_minutes_lost = 0;//IN MINUTES
					
					//FOR FIRST EVENT - CHECK TO SEE IF PREVIOUS GP WAS AN ARRIVAL - IF SO, MARK ALL TIME AS TIME LOST TILL THIS GP
					if($i == 1)
					{
						//GET PREVIOUS GP
						$where = null;
						$where = "shift_report_id = ".$shift_report_id." AND completion_time IS NOT NULL AND expected_time = (SELECT MAX(expected_time) FROM goalpoint WHERE shift_report_id = ".$shift_report_id." AND expected_time < '".date("Y-m-d H:i:s",strtotime($gp["expected_time"]))."' AND completion_time IS NOT NULL)";
						$previous_gp = db_select_goalpoint($where);
						
						if($previous_gp["arrival_departure"] == "Arrival")
						{
							$seconds_lost = strtotime($gp["expected_time"]) - strtotime($previous_ca["ca_time"]);
							$actual_minutes_lost = $actual_minutes_lost + $seconds_lost/60;
						}
					}
					
					//ADD EVENT TO MAP LIST
					$map_event["gps_coordinates"] = $gp["gps"];
					$map_events[] = $map_event;
					
					
					//FIGURE OUT HOW MUCH TIME IN THIS DRIVING PERIOD WAS EXPECTED TO BE LOST DUE TO THIS GP
					$minutes_gp_to_end = (strtotime($new_ca["ca_time"]) - strtotime($gp["expected_time"]))/60;
					
					$actual_minutes_lost = $actual_minutes_lost + MIN($minutes_gp_to_end,$gp["duration"]);//IN MINUTES
					$expected_minutes_lost = $actual_minutes_lost;
					if($gp["gp_type"] != "Pick" && $gp["gp_type"] != "Drop") 
					{
						if($gp["arrival_departure"] == "Arrival")
						{

							// if($gp["gp_type"] == "Driver Change")
							// {
								// $expected_minutes_lost = 15;
							// }
							// else if($gp["gp_type"] == "Trailer Change")
							// {
								// $expected_minutes_lost = 15;
							// }
							// else if($gp["gp_type"] == "Break")
							// {
								// $expected_minutes_lost = 15;
							// }
							// else if($gp["gp_type"] == "Fuel")
							// {
								// $expected_minutes_lost = 30;
							// }
							// else
							// {
								// $expected_minutes_lost = 0;
							// }
							
							$expected_minutes_lost = $gp["expected_duration"];

							//$duration_text = "was planned to take ".hours_to_text_mixed($expected_minutes_lost/60)." but actually took ";
						
						}
						
					}
					
					$total_expected_minutes_lost = $total_expected_minutes_lost + $expected_minutes_lost;//IN MINUTES

					$action_text = " made";
					$duration_text = "";
					if($gp["arrival_departure"] == "Arrival")
					{
						$action_text = " started a ";
					}
					else if($gp["arrival_departure"] == "Departure")
					{
						$action_text = " finished the ";
						
						//GET PREVIOUS GP
						$where = null;
						$where["shift_report_id"] = $shift_report_id;
						$where["gp_order"] = $gp["gp_order"] - 1;
						$preceeding_gp = db_select_goalpoint($where);
						
						$duration_expectation_difference = $preceeding_gp["duration"] - $preceeding_gp["expected_duration"];
						
						if($duration_expectation_difference == 0)
						{
							$duration_text = " which took ".hours_to_text_mixed($preceeding_gp["expected_duration"]/60)." as expected";
						}
						else if($duration_expectation_difference > 0)
						{
							$duration_text = " which was ".hours_to_text_mixed($duration_expectation_difference/60)." longer than expected";
						}
						else if($duration_expectation_difference < 0)
						{
							//echo "actual = ".$preceeding_gp["duration"]." | preceeding = ".$preceeding_gp["expected_duration"];
							$duration_text = " which was ".hours_to_text_mixed(abs($duration_expectation_difference/60))." quicker than expected";
						}
					}
					
					
					
					if($i > 1)
					{
						if($i != count($completed_goalpoints))
						{
							$minutes_lost_text = $minutes_lost_text.". It";
						}
						else
						{
							$minutes_lost_text = $minutes_lost_text.", and it";
						}
					}
					
					
					$minutes_lost_text = $minutes_lost_text.$action_text.strtolower($gp["gp_type"])." in ".$gp["location"]." at ".date("n/j H:i",strtotime($gp["expected_time"])).$duration_text;
				
					// if($actual_minutes_lost > 0)
					// {
						// $minutes_lost_text = $minutes_lost_text." which ".$break_text.hours_to_text_mixed($actual_minutes_lost/60);
					// }
				}
				
				$minutes_lost_text = $minutes_lost_text.".";
				
			}
			else
			{
				
				//GET MOST RECENT COMPLETED GOALPOINT
				$where = null;
				$where = "shift_report_id = ".$shift_report_id." AND completion_time IS NOT NULL AND expected_time = (SELECT MAX(expected_time) FROM goalpoint WHERE shift_report_id = ".$shift_report_id." AND completion_time IS NOT NULL)";
				$most_recent_gp = db_select_goalpoint($where);
				
				if(!empty($most_recent_gp))
				{
					if($most_recent_gp["arrival_departure"] == "Arrival")
					{
						$minutes_lost_text = $minutes_lost_text." has been held up at its ".$most_recent_gp["gp_type"]." in ".$most_recent_gp["location"].".";
						$total_expected_minutes_lost = (strtotime($new_ca["ca_time"]) - strtotime($previous_ca["ca_time"]))/60;
					}
					else
					{
						$minutes_lost_text = $minutes_lost_text." should have made no stops.";
					}
				}
				else
				{
					$minutes_lost_text = $minutes_lost_text." should have made no stops.";
				}
				
				
				
				
			}
			
			$ending_event["gps_coordinates"] = $new_ca["ca_gps"];
			$map_events[] = $ending_event;
			
			$map_info = null;
			$map_info = get_map_info($map_events); 
			
			$expectations["route_url"] = $map_info["route_url"];
			
			$actual_miles = $map_info["map_miles"];
			
			//FIGURE OUT HOW MANY MILES HE SHOULD HAVE DRIVEN
			$hours_to_drive = (strtotime($new_ca["ca_time"]) - strtotime($previous_ca["ca_time"]))/60/60;
			
			$actual_possible_hours_of_drive_time = $hours_to_drive - ($total_expected_minutes_lost/60);
			
			$expected_miles = round($actual_possible_hours_of_drive_time * 55);
			
			if($actual_miles == $expected_miles)
			{
				$efficiency = 100;//THIS HANDLES 0 AND 0
			}
			else
			{
				$efficiency = round($actual_miles/$expected_miles*100);
			}
			
			//$this_results_text = " This results in an expected drive time of ".hours_to_text_mixed($actual_possible_hours_of_drive_time)." and non-driving time of ".hours_to_text_mixed($total_expected_minutes_lost/60).". At 55 mph, the truck should have been able to achieve ".$expected_miles." miles in the expected ".hours_to_text_mixed($actual_possible_hours_of_drive_time)." of driving.";
			$this_results_text = " This results in an expected non-driving time of ".hours_to_text_mixed($total_expected_minutes_lost/60)." and an expected drive time of the remaining ".hours_to_text_mixed($actual_possible_hours_of_drive_time).". At 55 mph, the truck should have been able to achieve ".$expected_miles." miles in the expected ".hours_to_text_mixed($actual_possible_hours_of_drive_time)." of driving.";
			
			$computer_notes = "The truck traveled ".$actual_miles." miles in the last ".hours_to_text_mixed($minutes_between_cas/60).$minutes_lost_text.$this_results_text;
		}
		else
		{
			$expectations["route_url"] = null;
		}
		
		$expectations["actual_miles"] = $actual_miles;
		$expectations["expected_miles"] = $expected_miles;
		$expectations["efficiency"] = $efficiency;
		$expectations["computer_notes"] = $computer_notes;
		
		return $expectations;
	}
	
	//GET THE FULL INSURANCE COVERAGE OF A UNIT COVERAGE
	function get_full_unit_coverage($truck_coverage,$snapshot_date_db_format)
	{
		//GET POLICY
		$where = null;
		$where["id"] = $truck_coverage["ins_policy_id"];
		$ins_policy = db_select_ins_policy($where);
		
		$policy_id = $ins_policy["id"];
		
		//GET POLICY PROFILE FOR SNAPSHOT DATE
		$where = null;
		$where = "profile_current_since	 <= '".$snapshot_date_db_format."' 
				AND (profile_current_till > '".$snapshot_date_db_format."' OR profile_current_till IS NULL)
				AND ins_policy_id = $policy_id";
		$ins_policy_profile = db_select_ins_policy_profile($where);
		
		//GET INSURED COMPANY
		$where = null;
		$where["id"] = $ins_policy_profile["insured_company_id"];
		$insured_company = db_select_company($where);
		
		//GET INSURER COMPANY
		$where = null;
		$where["id"] = $ins_policy_profile["insurer_id"];
		$insurer_company = db_select_company($where);
		
		//GET AGENT COMPANY
		$where = null;
		$where["id"] = $ins_policy_profile["agent_id"];
		$agent_company = db_select_company($where);
		
		//GET FINANCIAL GUARANTOR
		$where = null;
		$where["id"] = $ins_policy_profile["fg_id"];
		$fg_client = db_select_client($where);
		
		//GET UNIT
		$where = null;
		$where["id"] = $truck_coverage["unit_id"];
		if($truck_coverage["unit_type"] == "Truck")
		{
			$unit = db_select_truck($where);
			
			$unit_number = $unit["truck_number"];
		}
		else if($truck_coverage["unit_type"] == "Trailer")
		{
			$unit = db_select_trailer($where);
			$unit_number = $unit["trailer_number"];
		}
		else
		{
			$unit = null;
			$unit_number = "?";
		}
		
		$full_unit_coverage = null;
		$full_unit_coverage["ins_policy"] = $ins_policy;
		$full_unit_coverage["ins_policy_profile"] = $ins_policy_profile;
		$full_unit_coverage["insured_company"] = $insured_company;
		$full_unit_coverage["insurer_company"] = $insurer_company;
		$full_unit_coverage["agent_company"] = $agent_company;
		$full_unit_coverage["fg_client"] = $fg_client;
		$full_unit_coverage["unit"] = $unit;
		$full_unit_coverage["unit_number"] = $unit_number;
		
		return $full_unit_coverage;
	}
	
	//GET TOTAL COST OF INSURANCE FOR A TRUCK GIVEN A TRUCK
	//THIS CONSIDERS ALL POLICIES
	function get_total_ins_cost_for_truck($truck_id,$snapshot_date)
	{
		//GET ALL UNIT COVERAGES FOR TRUCK
		$where = null;
		$where = "coverage_current_since <= '".$snapshot_date_db_format."' 
		AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
		AND unit_type = 'Truck'
		AND unit_id = ".$insured_truck["id"];
		$truck_coverages = db_select_ins_unit_coverages($where);
		
		
	}
	
	//GET TOTAL COST OF INSURANCE FOR A TRUCK GIVEN A TRUCK COVERAGE
	//THIS ONLY CONSIDERS A SIGNLE POLICY
	function get_total_unit_coverage_cost($uc_id,$snapshot_date)
	{
		
	}
	
	//RETURN INSURANCE COVERAGE STATUS FOR GIVEN TRUCK AND SNAPSHOT DATE
	function get_truck_insurance_stats($truck_id,$snapshot_date)
	{
		
		$snapshot_date_db_format = date("Y-m-d H:i:s",strtotime($snapshot_date));
		
		//GET ALL UNIT COVERAGES FOR GIVEN UNIT (TRUCK)
		$where = null;
		$where = "coverage_current_since <= '".$snapshot_date_db_format."' 
				AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				AND unit_type = 'Truck' 
				AND unit_id = $truck_id";
		$unit_coverages = db_select_ins_unit_coverages($where,"unit_id");
		
		
		$total_cost_per_month = 0;
		
		$number_of_pd_coverages = 0;
		$number_of_al_coverages = 0;
		$number_of_cargo_coverages = 0;
		
		$submessage_reefer = "";
		$submessage_cargo = "";
		$submessage_al = "";
		$submessage_pd = "";
		$submessage_rental = "";
		$submessage_radius = "";
	
		$cargo_is_covered = false;
		$reefer_bd_is_covered = false;
		$pd_is_covered = false;
		$pd_ded_is_500 = false;
		$al_is_covered = false;
		$al_limit_covers_750k = false;
		$al_limit_covers_1m = false;
		$rental_is_covered = false;
		$radius_is_unlimited = false;
		$al_is_double_insured = false;
		$pd_is_double_insured = false;
		$cargo_is_double_insured = false;
		
		
		//FOR EACH UNIT COVERAGE
		if(!empty($unit_coverages))
		{
			foreach($unit_coverages as $uc)
			{
				//GET POLICY 
				$where = null;
				$where["id"] = $uc["ins_policy_id"];
				$policy = db_select_ins_policy($where);
				
				$policy_id = $policy["id"];
				
				//GET POLICY PROFILE AT SNAPSHOT DATE
				$where = null;
				$where = "profile_current_since	 <= '".$snapshot_date_db_format."' 
						AND (profile_current_till > '".$snapshot_date_db_format."' OR profile_current_till IS NULL)
						AND ins_policy_id = $policy_id";
				$ins_profile = db_select_ins_policy_profile($where);
				
				//echo " policy_id:".$policy["id"];
				//echo " profile_id:".$ins_profile["id"];
				//echo " uc_id:".$uc["id"];
				//echo "<br>";
				
				//CHECK FOP CARGO COVERAGE
				if($ins_profile["cargo_prem"] > 0)
				{
					$cargo_is_covered = true;
					$number_of_cargo_coverages++;
				}
				
				//CHECK FOP REEFER BREAK DOWN
				if($ins_profile["rbd_prem"] > 0 || ($ins_profile["cargo_ded"] > 0 && $ins_profile["cargo_limit"] > 0))
				{
					$reefer_bd_is_covered = true;
					//$number_of_cargo_coverages++;
				}
				
				//CHECK FOR PD COVERAGE - ADD TO NUMBER OF PD COVERAGE
				if($uc["pd_comp_prem"] > 0 || $uc["pd_coll_prem"] > 0)
				{
					$pd_is_covered = true;
					$number_of_pd_coverages++;
					//echo " ".$uc["id"]."-".$number_of_pd_coverages;
				}
				
				if($pd_is_covered)
				{
					//CHECK FOR PD DED OF 500
					if($uc["pd_comp_ded"] <= 500 && $uc["pd_coll_ded"] <= 500)
					{
						$pd_ded_is_500 = true;
					}
					else
					{
						$submessage_pd = $submessage_pd."Physical Damage deductable for this unit on policy ".$policy["policy_number"]." is greater than $500. ";
					}
					
					//CHECK FOR RENTAL REIMBURSEMENT
					if($uc["pd_rental_prem"] > 0)
					{
						$rental_is_covered = true;
					}
					else
					{
						$submessage_rental = $submessage_rental."The Physical damage coverage for this unit on policy ".$policy["policy_number"]." is missing rental reimbursement. ";
					}
				}
				
				//CHECK FOR AL COVERAGE - ADD TO NUMBER OF AL COVERAGE
				if($uc["al_um_bi_prem"] > 0 && $uc["al_uim_bi_prem"] > 0 && $uc["al_pip_prem"] > 0)
				{
					$al_is_covered = true;
					$number_of_al_coverages++;
				}
				
				if($al_is_covered)
				{
					//CHECK FOR AL COVERAGE UP TO 750k
					if($uc["al_um_bi_limit"] >= 750000 && $uc["al_uim_bi_limit"] >= 750000  && $uc["al_pip_limit"] >= 750000)
					{
						$al_limit_covers_750k = true;
					}
					else
					{
						$submessage_al = $submessage_al."Auto Liability limits for this unit on policy ".$policy["policy_number"]." is less than $750K. ";
					}
					
					//CHECK FOR AL COVERAGE UP TO 1M
					if($al_limit_covers_750k == true)
					{
						if($uc["al_um_bi_limit"] >= 1000000 && $uc["al_uim_bi_limit"] >= 1000000  && $uc["al_pip_limit"] >= 1000000)
						{
							$al_limit_covers_1m = true;
						}
						else
						{
							$submessage_al = $submessage_al."Auto Liability limits for this unit on policy ".$policy["policy_number"]." is less than $1M. ";
						}
					}
				}
				
				//CHECK FOR UNLIMITED RADIUS
				if($uc["radius"] == "Unlimited")
				{
					$radius_is_unlimited = true;
				}
				else
				{
					$submessage_radius = $submessage_radius."Radius for this unit on policy ".$policy["policy_number"]." is ".$uc["radius"].". ";
				}
				
				//GET TOTAL COST OF COVERAGE ADD TO TOTAL
				$total_cost = $uc["al_um_bi_prem"]+$uc["al_uim_bi_prem"]+$uc["al_pip_prem"]+$uc["pd_comp_prem"]+$uc["pd_coll_prem"]+$uc["pd_rental_prem"]+$uc["al_prem"];
				@$total_cost_per_month = $total_cost_per_month+($total_cost/$ins_profile["term"]);
			}
		}
		
		//CREATE MESSAGES FOR DUPLICATE INSURANCES
		if($number_of_pd_coverages > 1)
		{
			//REPLACE NUMBER WITH WORDS
			$search = array('2','3','4');
			$replace = array('double','triple','quadruple');
			
			$submessage_pd = str_replace($search,$replace,"This unit is ".$number_of_pd_coverages." insured for physical damage. ").$submessage_pd;
			//$submessage_pd = "This unit is ".$number_of_pd_coverages." insured for physical damage. ";
		}
		
		if($number_of_al_coverages > 1)
		{
			//REPLACE NUMBER WITH WORDS
			$search = array('2','3','4');
			$replace = array('double','triple','quadruple');
			
			$submessage_al = str_replace($search,$replace,"This unit is ".$number_of_al_coverages." insured for Auto Liability. ").$submessage_al;
		}
		
		if($number_of_cargo_coverages > 1)
		{
			//REPLACE NUMBER WITH WORDS
			$search = array('2','3','4');
			$replace = array('double','triple','quadruple');
			
			$submessage_cargo = str_replace($search,$replace,"This unit is ".$number_of_cargo_coverages." insured for Cargo. ").$submessage_cargo;
		}
		
		//CREATE MESSAGES FOR COVERAGES MISSING ENTIRELY
		if($cargo_is_covered == false)
		{
			$submessage_cargo = "No cargo coverage was found. ";
		}
		
		if($reefer_bd_is_covered == false)
		{
			$submessage_reefer = "No reefer breakdown coverage was found. ";
		}
		
		if($al_is_covered == false)
		{
			$submessage_al = "No Auto Liability coverage was found. ";
		}
		
		if($pd_is_covered == false)
		{
			$submessage_pd = "No Physical Damage coverage was found. ";
		}
		
		$status_message = $submessage_al.$submessage_pd.$submessage_radius.$submessage_cargo.$submessage_reefer.$submessage_rental;
		//echo $cargo_is_covered;
		
		//RETURN
		$truck_ins_stats = null;
		$truck_ins_stats["cargo_is_covered"] = $cargo_is_covered;
		$truck_ins_stats["reefer_bd_is_covered"] = $reefer_bd_is_covered;
		$truck_ins_stats["pd_is_covered"] = $pd_is_covered;
		$truck_ins_stats["pd_ded_is_500"] = $pd_ded_is_500;
		$truck_ins_stats["al_is_covered"] = $al_is_covered;
		$truck_ins_stats["al_limit_covers_750k"] = $al_limit_covers_750k;
		$truck_ins_stats["al_limit_covers_1m"] = $al_limit_covers_1m;
		$truck_ins_stats["radius_is_unlimited"] = $radius_is_unlimited;
		//$truck_ins_stats["rental_should_be_covered"] = $rental_should_be_covered;
		$truck_ins_stats["rental_is_covered"] = $rental_is_covered;
		$truck_ins_stats["al_is_double_insured"] = $al_is_double_insured;
		$truck_ins_stats["pd_is_double_insured"] = $pd_is_double_insured;
		$truck_ins_stats["cargo_is_double_insured"] = $cargo_is_double_insured;
		$truck_ins_stats["total_cost_per_month"] = $total_cost_per_month;
		$truck_ins_stats["status_message"] = $status_message;
		
		//print_r($truck_ins_stats);
		
		return $truck_ins_stats;
		
	}
	
	//GET POLICY INSURANCE STATS
	function get_full_policy_coverage($policy_id,$snapshot_date)
	{
		$snapshot_date_db_format = date("Y-m-d H:i:s",strtotime($snapshot_date));
		
		//GET POLICY
		$where = null;
		$where["id"] = $policy_id;
		$ins_policy = db_select_ins_policy($where);
		
		//GET POLICY PROFILE FOR SNAPSHOT DATE
		$where = null;
		$where = "profile_current_since	 <= '".$snapshot_date_db_format."' 
				AND (profile_current_till > '".$snapshot_date_db_format."' OR profile_current_till IS NULL)
				AND ins_policy_id = $policy_id";
		$ins_policy_profile = db_select_ins_policy_profile($where);
		
		//GET INSURED COMPANY
		$where = null;
		$where["id"] = $ins_policy_profile["insured_company_id"];
		$insured_company = db_select_company($where);
		
		//GET INSURER COMPANY
		$where = null;
		$where["id"] = $ins_policy_profile["insurer_id"];
		$insurer_company = db_select_company($where);
		
		//GET AGENT COMPANY
		$where = null;
		$where["id"] = $ins_policy_profile["agent_id"];
		$agent_company = db_select_company($where);
		
		//GET FINANCIAL GUARANTOR
		$where = null;
		$where["id"] = $ins_policy_profile["fg_id"];
		$fg_client = db_select_client($where);
		
		//GET UNIT COVERAGES
		$where = null;
		$where = "coverage_current_since <= '".$snapshot_date_db_format."' 
				AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
				AND ins_policy_id = $policy_id";
		$unit_coverages = db_select_ins_unit_coverages($where,"unit_id");
		
		
		$number_of_trucks = 0;
		$number_of_trailers = 0;
		$number_of_al_coverages = 0;
		$number_of_pd_coverages = 0;
		$number_of_cargo_coverages = 0;
		$number_of_dtr = 0;//DOWN TIME RENTAL
		$number_of_rbd = 0;//REEFER BREAKDOWN
		if(!empty($unit_coverages))
		{
			foreach($unit_coverages as $uc)
			{
				//COUNT TRUCKS AND TRAILERS
				if($uc["unit_type"] == "Truck")
				{
					$number_of_trucks++;
					
					//CHECK FOR AL COVERAGE - ADD TO NUMBER OF AL COVERAGE
					if($uc["al_um_bi_prem"] > 0 && $uc["al_uim_bi_prem"] > 0 && $uc["al_pip_prem"] > 0)
					{
						$number_of_al_coverages++;
					}
					
					//CHECK FOR PD COVERAGE - ADD TO NUMBER OF PD COVERAGE
					if($uc["pd_comp_prem"] > 0 || $uc["pd_coll_prem"] > 0)
					{
						$number_of_pd_coverages++;
						//echo " ".$uc["id"]."-".$number_of_pd_coverages;
					}
					
					//CHECK FOP CARGO COVERAGE
					if($ins_policy_profile["cargo_prem"] > 0)
					{
						$number_of_cargo_coverages++;
						
						//CHECK FOR RENTAL REIMBURSEMENT
						if($uc["pd_rental_prem"] > 0)
						{
							$number_of_dtr++;
						}
					}
					
					//CHECK FOP REEFER BREAK DOWN
					if($ins_policy_profile["rbd_prem"] > 0 || ($ins_policy_profile["cargo_ded"] > 0 && $ins_policy_profile["cargo_limit"] > 0))
					{
						$number_of_rbd++;
					}
				}
				elseif($uc["unit_type"] == "Trailer")
				{
					$number_of_trailers++;
				}
				
				
			}
		}
		
		$full_policy_coverage = null;
		$full_policy_coverage["ins_policy"] = $ins_policy;
		$full_policy_coverage["ins_policy_profile"] = $ins_policy_profile;
		$full_policy_coverage["insured_company"] = $insured_company;
		$full_policy_coverage["insurer_company"] = $insurer_company;
		$full_policy_coverage["agent_company"] = $agent_company;
		$full_policy_coverage["fg_client"] = $fg_client;
		$full_policy_coverage["number_of_trucks"] = $number_of_trucks;
		$full_policy_coverage["number_of_trailers"] = $number_of_trailers;
		$full_policy_coverage["number_of_al_coverages"] = $number_of_al_coverages;
		$full_policy_coverage["number_of_pd_coverages"] = $number_of_pd_coverages;
		$full_policy_coverage["number_of_cargo_coverages"] = $number_of_cargo_coverages;
		$full_policy_coverage["number_of_dtr"] = $number_of_dtr;
		$full_policy_coverage["number_of_rbd"] = $number_of_rbd;
		
		return $full_policy_coverage;
	}
	
	//FUNCTION FOR TRANSITION AFTER CODE UPDATE: CREATES GEOPOINTS FOR PICKS AND DROPS THAT WOULD HAVE BEEN CREATED ON THE RCR SAVE
	function create_geopoints_for_load($load)
	{
		$load_id = $load["id"];
		
		//GET GOALPOINTS
		$where = null;
		$where["load_id"] = $load_id;
		$goalpoints = db_select_goalpoints($where);
		
		if(empty($goalpoints))
		{
			//FOR EACH PICK, UPDATE AND INSERT PICKS
			foreach($load["load_picks"] as $pick)
			{
				//GET GOALPOINT GUID FOR SYNCING
				$gp_guid = get_random_string(10);
				
				//DETERMINE DURATION
				$event_duration = get_expected_goalpoint_durations("Pick");
				
				for($gp_i=1; $gp_i <= 2; $gp_i++)//RUN LOOP TWICE
				{
					//CREATE NEW GOALPOINT
					$new_gp = null;
					$new_gp["deadline"] = $pick["appointment_time"];
					$new_gp["client_id"] = $load["client_id"];
					$new_gp["truck_id"] = $load["load_truck_id"];
					$new_gp["trailer_id"] = $load["load_trailer_id"];
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
							$new_gp["dm_notes"] = "PU: ".$pick["pu_number"]." ".$pick["dispatch_notes"];
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
						$new_gp["dm_notes"] = "PU: ".$pick["pu_number"]." ".$pick["dispatch_notes"];
						$new_gp["gp_type"] = "Pick";
						$new_gp["duration"] = 0;
						$gp_i++;//ONLY DO LOOP ONCE
					}
					
					$new_gp["load_id"] = $load_id;
					//$new_gp["gps"] = $_POST["rcr_pick_gps_$i"];
					$new_gp["location_name"] = $pick["stop"]["location_name"];
					$new_gp["location"] = $pick["stop"]["city"].", ".$pick["stop"]["state"];
					$new_gp["sync_gp_guid"] = $gp_guid;
					
					db_insert_goalpoint($new_gp);
					
				}
			}//END FOR EACH PICK
			
			//FOR EACH DROP, UPDATE AND INSERT DROPS
			foreach($load["load_drops"] as $drop)
			{
				//GET GOALPOINT GUID FOR SYNCING
				$gp_guid = get_random_string(10);
				
				//DETERMINE DURATION
				$event_duration = get_expected_goalpoint_durations("Drop");
				
				for($gp_i=1; $gp_i <= 2; $gp_i++)//RUN LOOP TWICE
				{
					//CREATE NEW GOALPOINT
					$new_gp = null;
					$new_gp["deadline"] = $drop["appointment_time"];
					$new_gp["client_id"] = $load["client_id"];
					$new_gp["truck_id"] = $load["load_truck_id"];
					$new_gp["trailer_id"] = $load["load_trailer_id"];
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
							$new_gp["dm_notes"] = "Ref: ".$drop["ref_number"]." ".$drop["dispatch_notes"];
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
						$new_gp["dm_notes"] = "Ref: ".$drop["ref_number"]." ".$drop["dispatch_notes"];
						$new_gp["gp_type"] = "Drop";
						$new_gp["duration"] = 0;
						$gp_i++;//ONLY DO LOOP ONCE
					}
					
					$new_gp["load_id"] = $load_id;
					//$new_gp["gps"] = $_POST["rcr_pick_gps_$i"];
					$new_gp["location_name"] = $drop["stop"]["location_name"];
					$new_gp["location"] = $drop["stop"]["city"].", ".$drop["stop"]["state"];
					$new_gp["sync_gp_guid"] = $gp_guid;
					
					db_insert_goalpoint($new_gp);
				}
			}//END FOR EACH DROP
			
		}
	}
	
	//DETERMINE BILLING STATUS
	function determine_billing_status($load_id)
	{
		//GET LOAD
		$where = null;
		$where["id"] = $load_id;
		$load = db_select_load($where);
		
		if(!empty($load["invoice_closed_datetime"]))
		{
			$status = "Closed";
			$status_number = 10;
		}
		elseif(empty($load["hc_processed_datetime"]) && empty($load["digital_received_datetime"]))
		{
			$status = "Digital";
			$status_number = 1;
		}
		elseif(empty($load["billing_datetime"]))
		{
			$status = "Billing";
			$status_number = 2;
		}
		elseif(empty($load["amount_funded"]))
		{
			$status = "Funding";
			$status_number = 3;
		}
		elseif(empty($load["hc_processed_datetime"]) && empty($load["envelope_pic_datetime"]))
		{
			$status = "Envelope";
			$status_number = 4;
		}
		elseif(empty($load["hc_processed_datetime"]) && empty($load["dropbox_pic_datetime"]))
		{
			$status = "Dropbox";
			$status_number = 5;
		}
		elseif(empty($load["hc_processed_datetime"]))
		{
			$status = "Scanning";
			$status_number = 6;
		}
		else
		{
			$status = "Closing";
			$status_number = 9;
		}
		
		if(!empty($load["denied_reason"]))
		{
			$status = "Hold";
			$status_number = 7;
		}
		
		if(!empty($load["recoursed_datetime"]) && empty($load["reimbursed_datetime"]))
		{
			$status = "Recoursed";
			$status_number = 8;
		}
		
		$billing_status = array();
		$billing_status["status"] = $status;
		$billing_status["status_number"] = $status_number;
		
		return $billing_status;
	}
	
	function update_load_status($load)
	{
		if($load["status"] != "Dropped" && $load["status"] != "Cancelled" && $load["status_number"] >= 3)
		{
			
			$load_id = $load["id"];
			
			//CHECK TO SEE IF THERE ARE ANY MORE PICKS OR DROPS
			if($load["status_number"] == 3)
			{
				//GET ALL REMAINING PICKS GOALPOINTS
				$where = null;
				$where = " load_id = $load_id AND completion_time IS NULL AND gp_type = 'Pick' ";
				$pick_gps = db_select_goalpoints($where);
			
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
				//$update_load["client_id"] = $goalpoint["client_id"];
				$update_load["driver2_id"] = $_POST["codriver_id"];
				if($truck_number % 2 == 0)
				{
					//IF EVEN
					$update_load["ar_specialist_id"] = 915;//Lenneth user_id
					$ars = "Lenneth";
					
					
				}
				else
				{
					//IF ODD
					$update_load["ar_specialist_id"] = 947;//Geanette user_id
					$ars = "Jeannette";
				}
				$update_load["status_number"] = 5;
				$update_load["status"] = "Dropped";
				$update["final_drop_datetime"] = date("Y-m-d H:i:s", strtotime($completion_time));
				$update_load["pushed_datetime"] = $now_datetime;
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
	}
	
	//GET FINAL DROP GOALPOINT FOR LOAD
	function get_final_drop_goalpoint($load_id)
	{
		$where = null;
		$where = " load_id = $load_id AND gp_type = 'Drop' AND gp_order = (SELECT MAX(gp_order) from goalpoint WHERE load_id = $load_id and gp_type = 'Drop')";
		$goalpoint = db_select_goalpoint($where);
		
		return $goalpoint;
	}
	
	function get_hold_report($client_id)
	{
		//GET CLIENT
		$where = null;
		$where["id"] = $client_id;
		$client = db_select_client($where);
		
		$hold_status = "No Hold";
		
		//GET ALL LOADS WITH MISSING DIGITAL COPIES OF BOL
		$where = null;
		$where = " (client_id = $client_id OR driver2_id = $client_id) AND status = 'Dropped' AND billing_status <> 'Closed' AND digital_received_datetime IS NULL AND final_drop_datetime > '2016-07-15 23:59'";
		$loads_missing_dc = db_select_loads($where);
		
		if(!empty($loads_missing_dc))
		{
			$hold_status = "Hold";
		}
		
		//GET ALL LOADS WITH MISSING HARD COPIES OF BOL
		$where = null;
		$where = " (client_id = $client_id OR driver2_id = $client_id) AND status = 'Dropped' AND billing_status <> 'Closed' AND (hc_processed_datetime IS NULL AND envelope_pic_datetime IS NULL AND final_drop_datetime > '2016-07-15 23:59')";
		$loads_missing_hc = db_select_loads($where);
		
		if(!empty($loads_missing_hc))
		{
			$hold_status = "Hold";
		}
		
		//GET ALL CLIENT EXPENSES (RECEIPTS) THAT ARE OUTSTANDING
		$where = null;
		//$where = " client_id = $client_id AND (receipt_datetime IS NOT NULL OR paid_datetime IS NOT NULL) AND is_reimbursable = 'Yes' AND file_guid IS NULL AND expense_datetime  > '2016-07-15 23:59'";
		$where = " client_id = $client_id AND is_reimbursable = 'Yes' AND (receipt_datetime IS NULL AND paid_datetime IS NULL) AND file_guid IS NULL AND expense_datetime  > '2016-07-15 23:59'";
		$client_expenses = db_select_client_expenses($where);
		
		if(!empty($client_expenses))
		{
			$hold_status = "Hold";
		}
		
		$hold_report = null;
		$hold_report["client"] = $client;
		$hold_report["hold_status"] = $hold_status;
		$hold_report["loads_missing_dc"] = $loads_missing_dc;
		$hold_report["loads_missing_hc"] = $loads_missing_hc;
		$hold_report["client_expenses"] = $client_expenses;
		
		return $hold_report;
	}
	
	function send_driver_hold_report_email($client_id)
	{
		date_default_timezone_set('America/Denver');
		$CI =& get_instance();
		
		//GET CLIENT
		$where = null;
		$where["id"] = $client_id;
		$client = db_select_client($where);
		
		if(!empty($client))
		{
			$hold_report = get_hold_report($client_id);
			
			//SEND EMAIL
			$email_data = null;
			$email_data["hold_report"] = $hold_report;
			$message = $CI->load->view('emails/hold_report_email',$email_data, TRUE);
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
			$CI->load->library('email');
			$CI->email->from("paperwork.dispatch@gmail.com","Dispatch");
			$CI->email->to($to);
			$CI->email->cc('paperwork.dispatch@gmail.com');
			$CI->email->subject($subject);
			$CI->email->message($message);
			$CI->email->send();
			
			//echo $CI->email->print_debugger();
			echo "Email sent to ".$to." ".date("m/d/y H:i");
		}
	}
	
	function add_clock_in_verification($user_id)
	{
		date_default_timezone_set('America/Denver');
		$datetime = date('Y-m-d H:i:s');
		
		$clock_in_verification = null;
		$clock_in_verification['user_id'] = $user_id;
		$clock_in_verification['email_sent_datetime'] = $datetime;
		db_insert_clock_in_verification($clock_in_verification);
		
		$new_clock_in = db_select_clock_in_verification($clock_in_verification);
		
		return $new_clock_in['id'];
	}

	function send_clock_in_verification_email($email,$clock_in_id,$user_id)
	{
		$CI =& get_instance();
		date_default_timezone_set('America/Denver');
		$date = date('Y-m-d');
		$time = date('g:i A');

		$data['clock_in_id'] = $clock_in_id;
		$message = $CI->load->view('emails/clock_in_verification_email',$data, TRUE);

		echo "send email function clock in id: " . $clock_in_id . "<br>";
		$CI->load->library('email');

		$CI->email->from('fleetsmarts@integratedlogicsticssolutions.co', 'Fleetsmarts');
		$CI->email->to($email);
		$CI->email->cc('fleetsmarts@integratedlogicsticssolutions.co');

		$CI->email->subject("Verification Email - Sent on $date at $time");
		$CI->email->message($message);
		$CI->email->send();

		echo "Email sent to " . $email . "<br>";
//		echo $this->email->print_debugger();

		$where = null;
		$where['id'] = $user_id;
		$user = db_select_user($where);
		$slack_username = $user['slack_username'];
		
		$url = "<http://fleetsmarts.integratedlogicsticssolutions.co/index.php/time_clock/clock_in_verification?id=$clock_in_id";
		
		$message = "$url|Click here> to verify that you are at your computer.";
		$channel = "@$slack_username";
		send_slack_message($message,$channel);
	}

	function clock_out($user_id)
	{
		date_default_timezone_set('America/Denver');
		$datetime = date('Y-m-d H:i:s');
		
		$time_punch = null;
		$time_punch["user_id"] = $user_id;
		$time_punch["datetime"] = $datetime;
		$time_punch["in_out"] = "Out";
		$time_punch["location"] = "Time Clock";

		db_insert_time_punch($time_punch);
	}
	
	//SEND SLACK MESSAGE
	function send_slack_message($message,$channel="notifications",$bot_name = "Fleetsmarts")
	{
//		interstaterevolution
		$data = "payload=" . json_encode(array(
				"channel"       =>  $channel,
				"text"          =>  $message,
				"username"		=>	$bot_name
			));

		//print_r($data);

		$c = curl_init('https://hooks.slack.com/services/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');
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($c);
		curl_close($c);

		//echo "<br>Result: " . $result;
	}
	
	function create_slack_channel($name)
	{
//		KEY: xoxp-4249413880-4273184029-66844202993-71954a5c59
		$token = 'xoxp-4249413880-4273184029-66844202993-71954a5c59';
//		$name = 'test_channel';
		
		$channels_json_string = file_get_contents('https://slack.com/api/channels.list?token=' . $token);
		
		$parsed_channels_json = json_decode($channels_json_string,TRUE);
		
//		print_r($parsed_channels);
		$current_channels = array();
		foreach($parsed_channels_json['channels'] as $key => $channel){
//			echo "Channel: " . $channel['name'] . "<br>";
			$current_channels[] = $channel['name'];
		}
//		echo "Current channels:<br>";
//		print_r($current_channels);
//		echo "New channel: " . $name . "<br>";
		if(!in_array($name,$current_channels))
		{
			echo "new channel not in current channels<br>";
			$c = curl_init('https://slack.com/api/channels.create?name=' . $name . '&token=' . $token);
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($c, CURLOPT_POST, true);
			$result = curl_exec($c);
			curl_close($c);

			echo $result;
		}
		else
		{
			echo "<br>channel $name already in the system.<br>";
		}
	}
	
	function save_load_update($load_id,$current_geopoint_id,$current_trailer_geopoint_id)
	{
		//SET TIMEZONE
		date_default_timezone_set('US/Mountain');
		$recorded_time = date("Y-m-d H:i:s");
		$CI =& get_instance();
		$recorder_id = $CI->session->userdata('user_id');
		
		// $trailer_fuel = $_POST["trailer_fuel"];
		// $reefer_temp = $_POST["reefer_temp"];
		// $reefer_set = $_POST["reefer_set"];
		// $trailer_codes = $_POST["trailer_codes_status"];
		
		//GET LOAD
		$where = null;
		$where['id'] = $load_id;
		$load = db_select_load($where);
		
		//GET CURRENT GEOPOINT
		$where = null;
		$where["id"] = $current_geopoint_id;
		$current_geopoint = db_select_geopoint($where);
		
		$geocode = reverse_geocode($current_geopoint["latitude"].", ".$current_geopoint["longitude"]);
		
		if(!empty($current_trailer_geopoint_id))
		{
			//GET CURRENT TRAILER GEOPOINT
			$where = null;
			$where["id"] = $current_trailer_geopoint_id;
			$current_trailer_geopoint = db_select_trailer_geopoint($where);
		}
		else
		{
			$current_trailer_geopoint = null;
		}
		
		//GET TRUCK
		$where = null;
		$where["id"] = $load["load_truck_id"];
		$truck = db_select_truck($where);
		
		//GET TRAILER
		$where = null;
		$where["id"] = $load["load_trailer_id"];
		$trailer = db_select_trailer($where);
		
		//GET DRIVER COMPANY
		$where = null;
		$where["id"] = $load["client"]["company_id"];
		$driver_company = db_select_company($where);
		
		//GET DRIVER PERSON
		$where = null;
		$where["id"] = $driver_company["person_id"];
		$driver_person = db_select_person($where);
		
		//GET CARRIER COMPANY
		$where = null;
		$where["id"] = $load["billed_under"];
		$carrier_company = db_select_company($where);
		
		//GET FLEET MANAGER COMPANY
		$where = null;
		$where["person_id"] = $load["fleet_manager"]["id"];
		$fm_company = db_select_company($where);
		
		//GET DRIVER MANAGER COMPANY
		$where = null;
		$where["person_id"] = $load["driver_manager"]["id"];
		$dm_company = db_select_company($where);
		
		//GET PREVIOUS DISPATCH UPDATE
		// $where = null;
		// $where = " load_id = $load_id AND update_datetime = (SELECT MAX(update_datetime) FROM `dispatch_update` WHERE load_id = $load_id)";
		// $previous_dispatch_update = db_select_dispatch_update($where);
		
	
		
		$update_guid = get_random_string(10);
		
		//CREATE DISPATCH UPDATE
		$insert_du = null;
		$insert_du["load_id"] = $load["id"];
		$insert_du["client_id"] = $load["client"]["id"];
		$insert_du["client_email"] = $driver_person["email"];
		$insert_du["carrier_id"] = $load["billed_under"];
		$insert_du["carrier_email"] = $carrier_company["company_gmail"];;
		$insert_du["fleet_manager_id"] = $load["fleet_manager_id"];
		$insert_du["fleet_manager_email"] = $fm_company["company_email"];
		$insert_du["driver_manager_id"] = $load["dm_id"];
		$insert_du["driver_manager_email"] = $dm_company["company_email"];
		$insert_du["truck_id"] = $load["load_truck_id"];
		$insert_du["trailer_id"] = $load["load_trailer_id"];
		$insert_du["location"] = $geocode["city"].", ".$geocode["state"];
		$insert_du["gps"] = $current_geopoint["latitude"].", ".$current_geopoint["longitude"];
		$insert_du["update_datetime"] = date("Y-m-d H:i",strtotime($current_geopoint["datetime"]));
		if(!empty($current_trailer_geopoint))
		{
			$insert_du["trailer_fuel"] = $current_trailer_geopoint["fuel_level"];
			$insert_du["reefer_temp"] = $current_trailer_geopoint["return_temperature"];
			$insert_du["reefer_set"] = $current_trailer_geopoint["set_temperature"];
			$insert_du["truck_codes"] = $current_trailer_geopoint["status"];
		}
		$insert_du["recorder_id"] = $recorder_id;
		$update_du["recorded_time"] = $recorded_time;
		$update_du["is_oor"] = $current_geopoint["is_oor"];
		$update_du["oor_url"] = $current_geopoint["oor_url"];
		$insert_du["email_html"] = $update_guid;
		
		//print_r($insert_du);
		db_insert_dispatch_update($insert_du);
		
		//GET THIS DISPATCH UPDATE AND UPDATE WITH HTML FROM EMAIL
		$where = null;
		$where["email_html"] = $update_guid;
		$this_du = db_select_dispatch_update($where);
		$this_du_id = $this_du["id"];
		
		//UPDATE DISPATCH UPDATE
		$where = null;
		$where["id"] = $this_du_id;
		db_update_dispatch_update($update_du,$where);


		$update_du = null;
		$update_du["email_html"] = file_get_contents(base_url("index.php/public_functions/send_dispatch_email/$this_du_id"));
		//echo file_get_contents(base_url("index.php/public_functions/send_dispatch_email/$this_du_id"));
		$where = null;
		$where["id"] = $this_du_id;
		db_update_dispatch_update($update_du,$where);
		
		
		
		//DISPLAY UPLOAD SUCCESS MESSAGE
		//load_upload_success_view();
		
		echo "success!";
	}
	
	function vars_not_empty()
	{
		foreach(func_get_args() as $arg)
		{
			if(!empty($arg) || !is_null($arg))
			{
				continue;
			}
			else
			{
				return false;
			}
		}
		return true;
	}
	

	function get_idle_info($truck_number,$start_time,$endtime)
	{
		date_default_timezone_set('America/Denver');
		
		if(!is_numeric($start_time))
		{
			$start_time = strtotime($start_time);
		}
		if(!is_numeric($endtime))
		{
			$endtime = strtotime($endtime);
		}
//		echo "Start time: " . $start_time . "<br>";
//		echo "End time: " . $endtime . "<br>";
		
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
									"Cookie: foo=bar\r\n",
				'user_agent'=>    $_SERVER['HTTP_USER_AGENT'] 
			)
		);

		$context = stream_context_create($opts);

		$url = "http://dir3696.zonarsystems.net/interface.php?customer=dir3696&username=system&password=password&action=showposition&operation=idlestoptotals&format=xml&fromdate=$start_time&todate=$endtime&target=$truck_number&reqtype=fleet&type=Standard&version=2&logvers=3.3";
//		echo "url: " . $url . "<br>";
		$xml = file_get_contents($url, false, $context);

		$parsed_xml = simplexml_load_string($xml);

		$idle_info = array();
		$idle_info['truck_number'] = $parsed_xml->assetidle->attributes()->fleet;
		$idle_info['stop_count'] = $parsed_xml->assetidle->stopcount;
		$idle_info['idle_count'] = $parsed_xml->assetidle->idlecount;
		$idle_info['total_stop_time'] = $parsed_xml->assetidle->totalstop;
		$idle_info['total_idle_time'] = $parsed_xml->assetidle->totalidle;
		
		return $idle_info;
	}
	
	
	
	
	
	
	
	
	