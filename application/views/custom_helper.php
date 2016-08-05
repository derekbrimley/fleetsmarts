<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


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
	
	
	
	
	
	
	
?>