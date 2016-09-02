<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//TEMPLATE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TEMPLATE
	function db_insert_template($template)
	{
		db_insert_table("template",$template);
	
	}//END db_insert_template	

	//SELECT TEMPLATES (many)
	function db_select_templates($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_template($where,$order_by,$limit,"many");
		
	}//end db_select_templates() many	

	//SELECT TEMPLATE (one)
	function db_select_template($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." template.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." template.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." template.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				template.id as id,
				template.recorder_id as recorder_id,
				person.f_name as f_name ,
				template.load_id as load_id,
				`load`.customer_load_number,
				template.truck_id as truck_id ,
				truck.truck_number,
				template.trailer_id as trailer_id ,
				trailer.trailer_number,
				miles_type,
				template.main_driver_id as main_driver_id ,
				main_driver.client_nickname as main_driver_nickname ,
				template.codriver_id as codriver_id ,
				codriver.client_nickname as codriver_nickname ,
				entry_type,
				entry_datetime,
				city,
				state,
				address,
				odometer,
				route,
				miles,
				out_of_route,
				gallons,
				fuel_expense,
				template.mpg AS entry_mpg,
				entry_notes
				FROM `template`
				LEFT JOIN person ON template.recorder_id = person.id 
				LEFT JOIN  `load` ON  `load_id` =  `load`.id
				LEFT JOIN truck ON template.truck_id = truck.id 
				LEFT JOIN trailer ON template.trailer_id = trailer.id 
				LEFT JOIN client as main_driver ON template.main_driver_id = main_driver.id 
				LEFT JOIN client as codriver ON template.codriver_id = codriver.id ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$templates = array();
		foreach ($query->result() as $row)
		{
			$template['id'] = $row->id;
			$template['load_id'] = $row->load_id;
			$template['truck_id'] = $row->truck_id;
			$template['trailer_id'] = $row->trailer_id;
			$template['miles_type'] = $row->miles_type;
			$template['main_driver_id'] = $row->main_driver_id;
			$template['codriver_id'] = $row->codriver_id;
			$template['entry_type'] = $row->entry_type;
			$template['entry_datetime'] = $row->entry_datetime;
			$template['city'] = $row->city;
			$template['state'] = $row->state;
			$template['address'] = $row->address;
			$template['odometer'] = $row->odometer;
			$template['route'] = $row->route;
			$template['miles'] = $row->miles;
			$template['out_of_route'] = $row->out_of_route;
			$template['gallons'] = $row->gallons;
			$template['fuel_expense'] = $row->fuel_expense;
			$template['mpg'] = $row->entry_mpg;
			$template['entry_notes'] = $row->entry_notes;
			
			$recorder["f_name"] = $row->f_name;
			$template["recorder"] = $recorder;
			
			$load["customer_load_number"] = $row->customer_load_number;
			$template["load"] = $load;
			
			$truck["truck_number"] = $row->truck_number;
			$template["truck"] = $truck;
			
			$trailer["trailer_number"] = $row->trailer_number;
			$template["trailer"] = $trailer;
			
			$main_driver["client_nickname"] = $row->main_driver_nickname;
			$template["main_driver"] = $main_driver;
			
			$codriver["client_nickname"] = $row->codriver_nickname;
			$template["codriver"] = $codriver;
			
			$templates[] = $template;
			
		}// end foreach
		
		if (empty($template))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $template;
		}
		else if($many == "many")
		{
			return $templates;
		}
	}//end db_select_template()

	//UPDATE TEMPLATE
	function db_update_template($set,$where)
	{
		db_update_table("template",$set,$where);
		
	}//end update template	
	
	//DELETE TEMPLATE	
	function db_delete_template($where)
	{
		db_delete_from_table("template",$where);
		
	}//end db_delete_template()	
	
	
	
//GENERIC FUNCTIONS TO A HANDLE THE VARIOUS DATABASE FUNCTIONS

	//INSERT TABLE
	function db_insert_table($table,$object)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($object as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO `$table` (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}//END db_insert_template	
	
	//SELECT TABLES (many)  ************* NEEDS TO BE UPDATED EVERY TIME A TABLE IS ADDED TO THE DB ************
	function db_select_tables($table,$where,$order_by = 'id')
	{
		$CI =& get_instance();
		$where_sql = " ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `$table` WHERE ".$where_sql." ORDER BY ".$order_by;
		$query_table = $CI->db->query($sql,$values);
		
		$object = array();
		$objects = array();
		foreach ($query_table->result() as $row)
		{
			//DEPENDING ON THE TABLE SELECT THE ROWS FROM THAT TABLE
			$object_where['id'] = $row->id;
			if($table == "account")
			{
				$object = db_select_account($object_where);
			}
			else if($table == "account_entry")
			{
				$object = db_select_account_entry($object_where);
			}
			else if($table == "client")
			{
				$object = db_select_client($object_where);
			}
			else if($table == "customer")
			{
				$object = db_select_customer($object_where);
			}
			else if($table == "drop")
			{
				$object = db_select_drop($object_where);
			}
			else if($table == "invoice")
			{
				$object = db_select_invoice($object_where);
			}
			else if($table == "invoice_allocation")
			{
				$object = db_select_invoice_allocation($object_where);
			}
			else if($table == "load")
			{
				$object = db_select_load($object_where);
			}
			else if($table == "load_expense")
			{
				$object = db_select_load_expense($object_where);
			}
			else if($table == "pick")
			{
				$object = db_select_pick($object_where);
			}
			else if($table == "permission")
			{
				$object = db_select_permission($object_where);
			}
			else if($table == "route_request")
			{
				$object = db_select_route_request($object_where);
			}
			else if($table == "settlement_adjustment")
			{
				$object = db_select_settlement_adjustment($object_where);
			}
			else if($table == "settlement_expense")
			{
				$object = db_select_settlement_expense($object_where);
			}
			else if($table == "settlement_profit_split")
			{
				$object = db_select_settlement_profit_split($object_where);
			}
			else if($table == "stop")
			{
				$object = db_select_stop($object_where);
			}
			else if($table == "truck")
			{
				$object = db_select_truck($object_where);
			}
			else if($table == "user_permission")
			{
				$object = db_select_user_permission($object_where);
			}
			else
			{
				echo "You forgot to add this table to the db select tables function";
			}
			
			
			$objects[] = $object;
		}
		
		return $objects;
	}//end db_select_tables() many		
	
	//UPDATE TABLE
	function db_update_table($table,$set,$where)
	{
		$CI =& get_instance();
		$i = 0;
		$set_sql = " ";
		$values = array();
		foreach($set as $key => $value)
		{
			if ($i > 0)
			{
				$set_sql = $set_sql.", ";
			}
			
			if ($value == null)
			{
				$set_sql = $set_sql." ".$key." = NULL ";
			}
			else
			{
				$set_sql = $set_sql." ".$key." = ?";
				$values[] = $value;
			}
			$i++;
		}
		
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "UPDATE `$table` SET ".$set_sql." WHERE ".$where_sql;
		//echo $sql;
		//print_r($values);
		$CI->db->query($sql,$values);
	}//end update table
	
	//DELETE FROM TABLE
	function db_delete_from_table($table,$where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "DELETE FROM `$table` WHERE ".$where_sql;
		$CI->db->query($sql,$values);
		
	}//end db_delete_from_table()	
	
	//GET LIST OF DISTINT EXPENSE CATEGORIES
	function get_distinct($column_name,$table_name,$where = null,$order_by = "none")
	{
		if($order_by = "none")
		{
			$order_by = $column_name;
		}
	
		$CI =& get_instance();
		
		$categories = array();
		
		$values = array();
		$where_sql = " ";
		if(!empty($where))
		{
			if(is_array($where))
			{
				$i = 0;
				foreach($where as $key => $value)
				{
					
					if ($i > 0)
					{
						$where_sql = $where_sql." And";
					}
					
					if ($value == null)
					{
						$where_sql = $where_sql." ".$key." is ?";
					}
					else
					{
						$where_sql = $where_sql." ".$key." = ?";
					}
					$values[$i] = $value;
					//echo "value[$i] = $value ";
					$i++;
				}
				
			}
			else
			{
				$where_sql = $where;
			}
			
			$where_sql = " WHERE ".$where_sql;
		}
		
		$sql = "SELECT distinct(".$column_name.") AS column_name FROM `".$table_name."`".$where_sql." ORDER BY ".$order_by;
		//error_log("SQL: ".$sql." | LINE ".__LINE__." ".__FILE__);
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		foreach ($query->result() as $row)
		{
			$categories[] = $row->column_name;
		}
		
		return $categories;
	}
	
	
	
	
//ACCOUNT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT ACCOUNT
	function db_insert_account($account)
	{
		db_insert_table("account",$account);
	
	}//END db_insert_account	

	//SELECT ACCOUNTS (many)
	function db_select_accounts($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_account($where,$order_by,$limit,"many");
		
	}//end db_select_accounts() many	

	//SELECT ACCOUNT (one)
	function db_select_account($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				//error_log($i." | LINE ".__LINE__." ".__FILE__);
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." account.".$key." IS ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." account.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." account.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `account`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$accounts = array();
		foreach ($query->result() as $row)
		{
			$account['id'] = $row->id;
			$account['company_id'] = $row->company_id;
			$account['relationship_id'] = $row->relationship_id;
			$account['account_type'] = $row->account_type;
			$account['account_class'] = $row->account_class;
			$account['category'] = $row->category;
			$account['account_group'] = $row->account_group;
			$account['parent_account_id'] = $row->parent_account_id;
			$account['related_account_id'] = $row->related_account_id;
			$account['account_status'] = $row->account_status;
			$account['account_name'] = $row->account_name;
			$account['account_code'] = $row->account_code;
			$account['account_notes'] = $row->account_notes;
			
			$accounts[] = $account;
			
		}// end foreach
		
		if (empty($account))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $account;
		}
		else if($many == "many")
		{
			return $accounts;
		}
	}//end db_select_account()

	//UPDATE ACCOUNT
	function db_update_account($set,$where)
	{
		db_update_table("account",$set,$where);
		
	}//end update account	
	
	//DELETE ACCOUNT	
	function db_delete_account($where)
	{
		db_delete_from_table("account",$where);
		
	}//end db_delete_account()	
	
	
	
//ACCOUNT_ENTRY: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT ACCOUNT_ENTRY
	function db_insert_account_entry($account_entry)
	{
		db_insert_table("account_entry",$account_entry);
	
	}//END db_insert_account_entry	

	//SELECT ACCOUNT_ENTRYS (many)
	function db_select_account_entrys($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_account_entry($where,$order_by,$limit,"many");
		
	}//end db_select_account_entrys() many	

	//SELECT ACCOUNT_ENTRY (one)
	function db_select_account_entry($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." account_entry.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." account_entry.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." account_entry.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `account_entry` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$account_entrys = array();
		foreach ($query->result() as $row)
		{
			$account_entry['id'] = $row->id;
			$account_entry['account_id'] = $row->account_id;
			$account_entry['transaction_id'] = $row->transaction_id;
			$account_entry['recorder_id'] = $row->recorder_id;
			$account_entry['recorded_datetime'] = $row->recorded_datetime;
			$account_entry['entry_datetime'] = $row->entry_datetime;
			$account_entry['debit_credit'] = $row->debit_credit;
			$account_entry['entry_amount'] = $row->entry_amount;
			$account_entry['entry_description'] = $row->entry_description;
			$account_entry['file_guid'] = $row->file_guid;
			$account_entry['report_guid'] = $row->report_guid;
			$account_entry['account_balance'] = $row->account_balance;
			
			$account_entrys[] = $account_entry;
			
		}// end foreach
		
		if (empty($account_entry))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $account_entry;
		}
		else if($many == "many")
		{
			return $account_entrys;
		}
	}//end db_select_account_entry()

	//UPDATE ACCOUNT_ENTRY
	function db_update_account_entry($set,$where)
	{
		db_update_table("account_entry",$set,$where);
		
	}//end update account_entry	
	
	//DELETE ACCOUNT_ENTRY	
	function db_delete_account_entry($where)
	{
		db_delete_from_table("account_entry",$where);
		
	}//end db_delete_account_entry()	



//ACTION_ITEM: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT ACTION_ITEM
	function db_insert_action_item($action_item)
	{
		db_insert_table("action_item",$action_item);
	
	}//END db_insert_action_item	

	//SELECT ACTION_ITEMS (many)
	function db_select_action_items($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_action_item($where,$order_by,$limit,"many");
		
	}//end db_select_action_items() many	

	//SELECT ACTION_ITEM (one)
	function db_select_action_item($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." action_item.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." action_item.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." action_item.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `action_item`".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$action_items = array();
		foreach ($query->result() as $row)
		{
			$action_item['id'] = $row->id;
			$action_item['owner_id'] = $row->owner_id;
			$action_item['manager_id'] = $row->manager_id;
			$action_item['object_type'] = $row->object_type;
			$action_item['object_id'] = $row->object_id;
			$action_item['ticket_id'] = $row->ticket_id;//this needs to be removed... replaced with object_id
			$action_item['due_date'] = $row->due_date;
			$action_item['description'] = $row->description;
			$action_item['completion_date'] = $row->completion_date;
			$action_item['notes'] = $row->notes;
			
			$action_items[] = $action_item;
			
		}// end foreach
		
		if (empty($action_item))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $action_item;
		}
		else if($many == "many")
		{
			return $action_items;
		}
	}//end db_select_action_item()

	//UPDATE ACTION_ITEM
	function db_update_action_item($set,$where)
	{
		db_update_table("action_item",$set,$where);
		
	}//end update action_item	
	
	//DELETE ACTION_ITEM	
	function db_delete_action_item($where)
	{
		db_delete_from_table("action_item",$where);
		
	}//end db_delete_action_item()	
		


	
//ATTACHMENT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT ATTACHMENT
	function db_insert_attachment($attachment)
	{
		db_insert_table("attachment",$attachment);
	
	}//END db_insert_attachment	

	//SELECT ATTACHMENTS (many)
	function db_select_attachments($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_attachment($where,$order_by,$limit,"many");
		
	}//end db_select_attachments() many	

	//SELECT ATTACHMENT (one)
	function db_select_attachment($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." attachment.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." attachment.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." attachment.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `attachment` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$attachments = array();
		foreach ($query->result() as $row)
		{
			$attachment['id'] = $row->id;
			$attachment['type'] = $row->type;
			$attachment['attached_to_id'] = $row->attached_to_id;
			$attachment['file_guid'] = $row->file_guid;
			$attachment['attachment_name'] = $row->attachment_name;
			
			$attachments[] = $attachment;
			
		}// end foreach
		
		if (empty($attachment))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $attachment;
		}
		else if($many == "many")
		{
			return $attachments;
		}
	}//end db_select_attachment()

	//UPDATE ATTACHMENT
	function db_update_attachment($set,$where)
	{
		db_update_table("attachment",$set,$where);
		
	}//end update attachment	
	
	//DELETE ATTACHMENT	
	function db_delete_attachment($where)
	{
		db_delete_from_table("attachment",$where);
		
	}//end db_delete_attachment()	
	
		

		

//BILL_HOLDER: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT BILL_HOLDER
	function db_insert_bill_holder($bill_holder)
	{
		db_insert_table("bill_holder",$bill_holder);
	
	}//END db_insert_bill_holder	

	//SELECT BILL_HOLDERS (many)
	function db_select_bill_holders($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_bill_holder($where,$order_by,$limit,"many");
		
	}//end db_select_bill_holders() many	

	//SELECT BILL_HOLDER (one)
	function db_select_bill_holder($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." bill_holder.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." bill_holder.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." bill_holder.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `bill_holder`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$bill_holders = array();
		foreach ($query->result() as $row)
		{
			$bill_holder['id'] = $row->id;
			$bill_holder['invoice_id'] = $row->invoice_id;
			$bill_holder['company_id'] = $row->company_id;
			$bill_holder['from_company_id'] = $row->from_company_id;
			$bill_holder['created_datetime'] = $row->created_datetime;
			$bill_holder['bill_datetime'] = $row->bill_datetime;
			$bill_holder['description'] = $row->description;
			$bill_holder['amount'] = $row->amount;
			$bill_holder['file_guid'] = $row->file_guid;
			$bill_holder['closed_datetime'] = $row->closed_datetime;
			
			$bill_holders[] = $bill_holder;
			
		}// end foreach
		
		if (empty($bill_holder))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $bill_holder;
		}
		else if($many == "many")
		{
			return $bill_holders;
		}
	}//end db_select_bill_holder()

	//UPDATE BILL_HOLDER
	function db_update_bill_holder($set,$where)
	{
		db_update_table("bill_holder",$set,$where);
		
	}//end update bill_holder	
	
	//DELETE BILL_HOLDER	
	function db_delete_bill_holder($where)
	{
		db_delete_from_table("bill_holder",$where);
		
	}//end db_delete_bill_holder()	
	
	
		
		
	

//BUSINESS_RELATIONSHIP: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT BUSINESS_RELATIONSHIP
	function db_insert_business_relationship($business_relationship)
	{
		db_insert_table("business_relationship",$business_relationship);
	
	}//END db_insert_business_relationship	

	//SELECT BUSINESS_RELATIONSHIPS (many)
	function db_select_business_relationships($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_business_relationship($where,$order_by,$limit,"many");
		
	}//end db_select_business_relationships() many	

	//SELECT BUSINESS_RELATIONSHIP (one)
	function db_select_business_relationship($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." business_relationship.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." business_relationship.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." business_relationship.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				business_relationship.id,
				business_relationship.business_id,
				business.company_name AS business_name,
				business_relationship.relationship,
				business_relationship.related_business_id,
				related_company.company_name AS related_company_name
				FROM `business_relationship`
				LEFT JOIN `company` AS business ON business_relationship.business_id = business.id
				LEFT JOIN `company` AS related_company ON business_relationship.related_business_id = related_company.id
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$business_relationships = array();
		foreach ($query->result() as $row)
		{
			$business_relationship['id'] = $row->id;
			$business_relationship['business_id'] = $row->business_id;
			$business_relationship['relationship'] = $row->relationship;
			$business_relationship['related_business_id'] = $row->related_business_id;
			
			//GET BUSINESS
			$where = null;
			$where["id"] = $row->business_id;
			$business = db_select_company($where);
			$business_relationship["business"] = $business;
			
			//GET RELATED BUSINESS
			$where = null;
			$where["id"] = $row->related_business_id;
			$related_business = db_select_company($where);
			$business_relationship["related_business"] = $related_business;

			
			$business_relationships[] = $business_relationship;
			
		}// end foreach
		
		if (empty($business_relationship))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $business_relationship;
		}
		else if($many == "many")
		{
			return $business_relationships;
		}
	}//end db_select_business_relationship()

	//UPDATE BUSINESS_RELATIONSHIP
	function db_update_business_relationship($set,$where)
	{
		db_update_table("business_relationship",$set,$where);
		
	}//end update business_relationship	
	
	//DELETE BUSINESS_RELATIONSHIP	
	function db_delete_business_relationship($where)
	{
		db_delete_from_table("business_relationship",$where);
		
	}//end db_delete_business_relationship()	
	
	
	
	
	
	
	
//CHECK_CALL: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT CHECK_CALL
	function db_insert_check_call($check_call)
	{
		db_insert_table("check_call",$check_call);
	
	}//END db_insert_check_call	

	//SELECT CHECK_CALLS (many)
	function db_select_check_calls($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_check_call($where,$order_by,$limit,"many");
		
	}//end db_select_check_calls() many	

	//SELECT CHECK_CALL (one)
	function db_select_check_call($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." check_call.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." check_call.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." check_call.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `check_call` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
				 
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$check_calls = array();
		foreach ($query->result() as $row)
		{
			$check_call['id'] = $row->id;
			$check_call['log_entry_id'] = $row->log_entry_id;
			$check_call['night_dispatcher_id'] = $row->night_dispatcher_id;
			$check_call['day_recap'] = $row->day_recap;
			$check_call['effort_eval'] = $row->effort_eval;
			$check_call['night_plan'] = $row->night_plan;
			$check_call['fuel_plan'] = $row->fuel_plan;
			$check_call['paperwork_plan'] = $row->paperwork_plan;
			$check_call['morning_goal'] = $row->morning_goal;
			$check_call['goal_met'] = $row->goal_met;
			$check_call['night_recap'] = $row->night_recap;
			$check_call['fuel_plan_followed'] = $row->fuel_plan_followed;
			$check_call['paperwork_plan_followed'] = $row->paperwork_plan_followed;
			$check_call['reefer_instructions'] = $row->reefer_instructions;
			$check_call['reefer_instructions_followed'] = $row->reefer_instructions_followed;
			$check_call['last_mpg'] = $row->last_mpg;
			$check_call['driver_fuel_analysis'] = $row->driver_fuel_analysis;
			$check_call['fuel_analysis_response'] = $row->fuel_analysis_response;
			$check_call['map_miles'] = $row->map_miles;
			$check_call['odometer_miles'] = $row->odometer_miles;
			$check_call['driver_mileage_analysis'] = $row->driver_mileage_analysis;
			$check_call['mileage_analysis_response'] = $row->mileage_analysis_response;
			$check_call['oor'] = $row->oor;
			$check_call['driver_oor_analysis'] = $row->driver_oor_analysis;
			$check_call['oor_analysis_response'] = $row->oor_analysis_response;
			$check_call['notes_to_dispatcher'] = $row->notes_to_dispatcher;
			$check_call['logs_complete'] = $row->logs_complete;
			$check_call['d1_pleasantness'] = $row->d1_pleasantness;
			$check_call['d1_attitude'] = $row->d1_attitude;
			$check_call['d1_skill'] = $row->d1_skill;
			$check_call['d1_eval_notes'] = $row->d1_eval_notes;
			$check_call['d2_pleasantness'] = $row->d2_pleasantness;
			$check_call['d2_attitude'] = $row->d2_attitude;
			$check_call['d2_skill'] = $row->d2_skill;
			$check_call['d2_eval_notes'] = $row->d2_eval_notes;
			$check_call['night_dispatch_eval'] = $row->night_dispatch_eval;
			$check_call['d1_logbook_file_guid'] = $row->d1_logbook_file_guid;
			$check_call['d2_logbook_file_guid'] = $row->d2_logbook_file_guid ;
			$check_call['morning_checkcall_guid'] = $row->morning_checkcall_guid;
			$check_call['evening_checkcall_guid'] = $row->evening_checkcall_guid ;
			
			$check_calls[] = $check_call;
			
		}// end foreach
		
		if (empty($check_call))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $check_call;
		}
		else if($many == "many")
		{
			return $check_calls;
		}
	}//end db_select_check_call()

	//UPDATE CHECK_CALL
	function db_update_check_call($set,$where)
	{
		db_update_table("check_call",$set,$where);
		
	}//end update check_call	
	
	//DELETE CHECK_CALL	
	function db_delete_check_call($where)
	{
		db_delete_from_table("check_call",$where);
		
	}//end db_delete_check_call()	
	

	
	
	
//CLIENT: INSERT, SELECT (one), SELECT (many), UPDATE, DELETE

	//INSERT CLIENT
	function db_insert_client($client)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($client as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO client (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}//END db_insert_client


	//SELECT CLIENT (one)
	function db_select_client($where)
	{
		$CI =& get_instance();
		$i = 0;
		$values = array();
		$where_sql = " ";
		if(is_array($where))
		{
			foreach($where as $key => $value)
			{
				if ($i > 0)
				{
				$where_sql = $where_sql." And";
				}
				$where_sql = $where_sql." ".$key." = ?";
				$values[$i] = $value;
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM client WHERE ".$where_sql;
		$query_client = $CI->db->query($sql,$values);
		foreach ($query_client->result() as $row)
		{
			//GET COMPANY
			$company_where["id"] = $row->company_id;
			$company = db_select_company($company_where);
			
			//GET COMPANY
			$carrier_where["id"] = $row->carrier_id;
			$carrier = db_select_company($carrier_where);
			
			//GET USER
			$user_where["person_id"] = $company["person_id"];
			$user = db_select_user($user_where);
			
			//GET USER
			$fm_where["id"] = $row->fleet_manager_id;
			$fleet_manager = db_select_person($fm_where);
			
			//GET FEE SETTINGS
			$fee_settings_where["client_id"] = $row->id;
			$client_fee_settings = db_select_client_fee_settings($fee_settings_where);
			
			$client['id'] = $row->id;
			$client['company_id'] = $row->company_id;
			$client['fleet_manager_id'] = $row->fleet_manager_id;
			$client['carrier_id'] = $row->carrier_id;
			$client['client_nickname'] = $row->client_nickname;
			$client['main_account'] = $row->main_account;
			$client['client_type'] = $row->client_type;
			$client['pay_structure'] = $row->pay_structure;
			$client['profit_split'] = $row->profit_split;
			$client['fuel_card_name'] = $row->fuel_card_name;
			$client['fuel_card_number'] = $row->fuel_card_number;
			$client['pay_card_number'] = $row->pay_card_number;
			$client['bigroad_username'] = $row->bigroad_username;
			$client['bigroad_password'] = $row->bigroad_password;
			$client['license_state'] = $row->license_state;
			$client['license_number'] = $row->license_number;
			$client['license_expiration'] = $row->license_expiration;
			$client['cdl_since'] = $row->cdl_since;
			$client['years_of_experience'] = $row->years_of_experience;
			$client['desired_company_name'] = $row->desired_company_name;
			$client['start_date'] = $row->start_date;
			$client['end_date'] = $row->end_date;
			$client['first_full_settlement_date'] = $row->first_full_settlement_date;
			$client['dropdown_status'] = $row->dropdown_status;
			$client['client_status'] = $row->client_status;
			$client["driver_application_link"] = $row->driver_application_link;
			$client["drug_test_link"] = $row->drug_test_link;
			$client["medical_card_link"] = $row->medical_card_link;
			$client["link_license"] = $row->link_license;
			$client["link_contract"] = $row->link_contract;
			$client["contract_guid"] = $row->contract_guid;
			$client["credit_score"] = $row->credit_score;
			$client["number_of_violations"] = $row->number_of_violations;
			$client["mvr_guid"] = $row->mvr_guid;
			$client["credit_score_guid"] = $row->credit_score_guid;
			
			$client["company"] = $company;
			$client["carrier"] = $carrier;
			$client["user"] = $user;
			$client["fleet_manager"] = $fleet_manager;
			$client["client_fee_settings"] = $client_fee_settings;
			
			
		}
		
		if (empty($client))
		{
			return null;
		}else
			{
				return $client;
			}
	}//end db_select_client()	
	
	//SELECT TEMPLATES (many)
	function db_select_clients($where,$order_by = 'id')
	{
		return db_select_tables("client",$where,$order_by);
		
	}//end db_select_templates() many	
	
	//UPDATE CLIENT
	function db_update_client($set,$where)
	{
		$CI =& get_instance();
		$i = 0;
		$set_sql = " ";
		foreach($set as $key => $value)
		{
			if ($i > 0)
			{
			$set_sql = $set_sql.", ";
			}
			$set_sql = $set_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$sql = "UPDATE client SET ".$set_sql." WHERE ".$where_sql;
		$CI->db->query($sql,$values);
		
		
	}//end update client

	
	//DELETE CLIENT
	function db_delete_client($client_id)
	{
		$CI =& get_instance();
		//ONLY COVAX13 CAN PERFORM DELETE OPERATIONS
		if($CI->session->userdata('username') == "covax13")
		{
			$sql = "DELETE FROM client WHERE id = ?";
			$CI->db->query($sql,array($client_id));
		}
		
	}// end delete client


	
	
	
//CLIENT_EXPENSE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT CLIENT_EXPENSE
	function db_insert_client_expense($client_expense)
	{
		db_insert_table("client_expense",$client_expense);
	
	}//END db_insert_client_expense	

	//SELECT CLIENT_EXPENSES (many)
	function db_select_client_expenses($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_client_expense($where,$order_by,$limit,"many");
		
	}//end db_select_client_expenses() many	

	//SELECT CLIENT_EXPENSE (one)
	function db_select_client_expense($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." client_expense.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." client_expense.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." client_expense.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `client_expense` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$client_expenses = array();
		foreach ($query->result() as $row)
		{
			$client_expense['id'] = $row->id;
			$client_expense['expense_id'] = $row->expense_id;
			$client_expense['settlement_id'] = $row->settlement_id;
			$client_expense['client_id'] = $row->client_id;
			$client_expense['owner_id'] = $row->owner_id;
			$client_expense['expense_datetime'] = $row->expense_datetime;
			$client_expense['category'] = $row->category;
			$client_expense['second_category'] = $row->second_category;
			$client_expense['expense_amount'] = $row->expense_amount;
			$client_expense['receipt_amount'] = $row->receipt_amount;
			$client_expense['description'] = $row->description;
			$client_expense['is_reimbursable'] = $row->is_reimbursable;
			$client_expense['receipt_datetime'] = $row->receipt_datetime;
			$client_expense['paid_datetime'] = $row->paid_datetime;
			$client_expense['transaction_id'] = $row->transaction_id;
			$client_expense['link'] = $row->link;
			$client_expense['file_guid'] = $row->file_guid;
			$client_expense['receipt_notes'] = $row->receipt_notes;
			
			$client_expenses[] = $client_expense;
			
		}// end foreach
		
		if (empty($client_expense))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $client_expense;
		}
		else if($many == "many")
		{
			return $client_expenses;
		}
	}//end db_select_client_expense()

	//UPDATE CLIENT_EXPENSE
	function db_update_client_expense($set,$where)
	{
		db_update_table("client_expense",$set,$where);
		
	}//end update client_expense	
	
	//DELETE CLIENT_EXPENSE	
	function db_delete_client_expense($where)
	{
		db_delete_from_table("client_expense",$where);
		
	}//end db_delete_client_expense()	
		
		
	



//CLIENT_FEE_SETTING: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT CLIENT_FEE_SETTING
	function db_insert_client_fee_setting($client_fee_setting)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($client_fee_setting as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO client_fee_setting (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}

	//SELECT CLIENT_FEE_SETTING (one)
	function db_select_client_fee_setting($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[$i] = $value;
			$i++;
		}
		
		$sql = "SELECT * FROM client_fee_setting WHERE ".$where_sql;
		$query_client_fee_setting = $CI->db->query($sql,$values);
		
		foreach ($query_client_fee_setting->result() as $row)
		{
			//GET COMPANY
			$client_where["id"] = $row->client_id;
			$account_where["id"] = $row->account_id;
			
			$client_fee_setting['id'] = $row->id;
			$client_fee_setting['client_id'] = $row->client_id;
			$client_fee_setting['account_id'] = $row->account_id;
			$client_fee_setting['fee_description'] = $row->fee_description;
			$client_fee_setting['fee_type'] = $row->fee_type;
			$client_fee_setting['fee_amount'] = $row->fee_amount;
			$client_fee_setting['fee_tax'] = $row->fee_tax;
			
			$client_fee_setting["client"] = db_select_company($client_where);
			$client_fee_setting["account"] = db_select_account($account_where);
		}
		
		if (empty($client_fee_setting))
		{
			return null;
		}else
			{
				return $client_fee_setting;
			}
	}//end db_select_client_fee_setting()	
	
	//SELECT CLIENT_FEE_SETTINGS (many)
	function db_select_client_fee_settings($where,$order_by = 'id')
	{
		$CI =& get_instance();
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				if ($i > 0)
				{
				$where_sql = $where_sql." And";
				}
				$where_sql = $where_sql." ".$key." = ?";
				$values[$i] = $value;
				$i++;
			}
			
			
		}
		else
		{
			$where_sql = $where;
		}
		$sql = "SELECT * FROM `client_fee_setting` WHERE ".$where_sql." ORDER BY ".$order_by;
		$query_client_fee_setting = $CI->db->query($sql,$values);
		
		$client_fee_setting = array();
		$client_fee_settings = array();
		foreach ($query_client_fee_setting->result() as $row)
		{
			$client_fee_setting_where['id'] = $row->id;
			$client_fee_setting = db_select_client_fee_setting($client_fee_setting_where);
			
			$client_fee_settings[] = $client_fee_setting;
		}
		
		return $client_fee_settings;
	}//end db_select_client_fee_settings() many	
	
	
	//UPDATE CLIENT_FEE_SETTING
	function db_update_client_fee_setting($set,$where)
	{
		$CI =& get_instance();
		$i = 0;
		$set_sql = " ";
		foreach($set as $key => $value)
		{
			if ($i > 0)
			{
			$set_sql = $set_sql.", ";
			}
			$set_sql = $set_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$sql = "UPDATE client_fee_setting SET ".$set_sql." WHERE ".$where_sql;
		$CI->db->query($sql,$values);
		
		
	}//end update client_fee_setting

	
	//DELETE client_fee_setting
	function db_delete_client_fee_setting($where)
	{
		db_delete_from_table("client_fee_setting",$where);
		
	}//end db_delete_client_fee_setting()	


	
	
//CLOCK_IN_VERIFICATION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT CLOCK_IN_VERIFICATION
	function db_insert_clock_in_verification($clock_in_verification)
	{
		db_insert_table("clock_in_verification",$clock_in_verification);
	
	}//END db_insert_clock_in_verification	

	//SELECT CLOCK_IN_VERIFICATIONS (many)
	function db_select_clock_in_verifications($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_clock_in_verification($where,$order_by,$limit,"many");
		
	}//end db_select_clock_in_verifications() many	

	//SELECT CLOCK_IN_VERIFICATION (one)
	function db_select_clock_in_verification($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." clock_in_verification.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." clock_in_verification.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." clock_in_verification.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `clock_in_verification` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$clock_in_verifications = array();
		foreach ($query->result() as $row)
		{
			$clock_in_verification['id'] = $row->id;
			$clock_in_verification['user_id'] = $row->user_id;
			$clock_in_verification['email_sent_datetime'] = $row->email_sent_datetime;
			$clock_in_verification['screenshot_uploaded_datetime'] = $row->screenshot_uploaded_datetime;
			$clock_in_verification['screenshot_guid'] = $row->screenshot_guid;
			
			
			$clock_in_verifications[] = $clock_in_verification;
			
		}// end foreach
		
		if (empty($clock_in_verification))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $clock_in_verification;
		}
		else if($many == "many")
		{
			return $clock_in_verifications;
		}
	}//end db_select_clock_in_verification()

	//UPDATE CLOCK_IN_VERIFICATION
	function db_update_clock_in_verification($set,$where)
	{
		db_update_table("clock_in_verification",$set,$where);
		
	}//end update clock_in_verification	
	
	//DELETE CLOCK_IN_VERIFICATION	
	function db_delete_clock_in_verification($where)
	{
		db_delete_from_table("clock_in_verification",$where);
		
	}//end db_delete_clock_in_verification()
	
	
	
	
//COMPANY: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT COMPANY
	function db_insert_company($company)
	{
		db_insert_table("company",$company);
	
	}//END db_insert_company	

	//SELECT COMPANYS (many)
	function db_select_companys($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_company($where,$order_by,$limit,"many");
		
	}//end db_select_companys() many	

	//SELECT COMPANY (one)
	function db_select_company($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." company.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." company.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." company.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `company`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$companys = array();
		foreach ($query->result() as $row)
		{
			
			$company['id'] = $row->id;
			$company['person_id'] = $row->person_id;
			$company['expenses_permission_id'] = $row->expenses_permission_id;
			$company['type'] = $row->type;
			$company['category'] = $row->category;
			$company['company_name'] = $row->company_name;
			$company['dba'] = $row->dba;
			$company['company_side_bar_name'] = $row->company_side_bar_name;
			$company['show_on_po'] = $row->show_on_po;
			$company['spark_cc_number'] = $row->spark_cc_number;
			$company['fein'] = $row->fein;
			$company['company_gmail'] = $row->company_gmail;
			$company['gmail_password'] = $row->gmail_password;
			$company['google_voice'] = $row->google_voice;
			$company['mc_number'] = $row->mc_number;
			$company['dot_number'] = $row->dot_number;
			$company['docket_pin'] = $row->docket_pin;
			$company['usdot_pin'] = $row->usdot_pin;
			$company['access_id'] = $row->access_id;
			$company['entity_number'] = $row->entity_number;
			$company['fl_username'] = $row->fl_username;
			$company['fl_password'] = $row->fl_password;
			$company['insurance_company'] = $row->insurance_company;
			$company['policy_number'] = $row->policy_number;
			$company['oregon_permit'] = $row->oregon_permit;
			$company['ucr_renewal_date'] = $row->ucr_renewal_date;
			$company['running_since'] = $row->running_since;
			$company['address'] = $row->address;
			$company['city'] = $row->city;
			$company['state'] = $row->state;
			$company['zip'] = $row->zip;
			$company['mailing_address'] = $row->mailing_address;
			$company['mailing_city'] = $row->mailing_city;
			$company['mailing_state'] = $row->mailing_state;
			$company['mailing_zip'] = $row->mailing_zip;
			$company['contact'] = $row->contact;
			$company['company_email'] = $row->company_email;
			$company['company_phone'] = $row->company_phone;
			$company['company_fax'] = $row->company_fax;
			$company['company_status'] = $row->company_status;
			$company['company_notes'] = $row->company_notes;
			$company['link_osbr'] = $row->link_osbr;
			$company['link_aoo'] = $row->link_aoo;
			$company['link_ein_letter'] = $row->link_ein_letter;
			$company['link_mc_letter'] = $row->link_mc_letter;
			$company['link_usdot_pin_letter'] = $row->link_usdot_pin_letter;
			$company['link_docket_pin_letter'] = $row->link_docket_pin_letter;
			$company['carrier_packet_guid'] = $row->carrier_packet_guid;
			$company['mcs_150_guid'] = $row->mcs_150_guid;
			$company['op_1_guid'] = $row->op_1_guid;
			$company['oregon_permit_guid'] = $row->oregon_permit_guid;
			$company['ucr_guid'] = $row->ucr_guid;
			$company['proof_of_ppb_guid'] = $row->proof_of_ppb_guid;
			$company['insurance_cert_guid'] = $row->insurance_cert_guid;
			$company['buy_sell_chain_guid'] = $row->buy_sell_chain_guid;
			$company['logo_img_src'] = $row->logo_img_src;
			$company['managed_by_id'] = $row->managed_by_id;
			
			//GET PERSON
			$where = null;
			$where["id"] = $row->person_id;
			$company["person"] = db_select_person($where);
			
			$companys[] = $company;
			
		}// end foreach
		
		if (empty($company))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $company;
		}
		else if($many == "many")
		{
			return $companys;
		}
	}//end db_select_company()

	//UPDATE COMPANY
	function db_update_company($set,$where)
	{
		db_update_table("company",$set,$where);
		
	}//end update company	
	
	//DELETE COMPANY	
	function db_delete_company($where)
	{
		db_delete_from_table("company",$where);
		
	}//end db_delete_company()	
	



/**
//COMPANY: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT COMPANY
	function db_insert_company($company)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($company as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO company (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}//END db_insert_company

	
	//SELECT COMPANY (one)
	function db_select_company($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[$i] = $value;
			$i++;
		}
		
		$sql = "SELECT * FROM company WHERE ".$where_sql;
		error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query_company = $CI->db->query($sql,$values);
		
		foreach ($query_company->result() as $row)
		{
			//GET PERSON
			$person_where["id"] = $row->person_id;
			
			$company['id'] = $row->id;
			$company['person_id'] = $row->person_id;
			$company['expenses_permission_id'] = $row->expenses_permission_id;
			$company['type'] = $row->type;
			$company['category'] = $row->category;
			$company['company_name'] = $row->company_name;
			$company['company_side_bar_name'] = $row->company_side_bar_name;
			$company['show_on_po'] = $row->show_on_po;
			$company['spark_cc_number'] = $row->spark_cc_number;
			$company['fein'] = $row->fein;
			$company['company_gmail'] = $row->company_gmail;
			$company['gmail_password'] = $row->gmail_password;
			$company['google_voice'] = $row->google_voice;
			$company['mc_number'] = $row->mc_number;
			$company['dot_number'] = $row->dot_number;
			$company['docket_pin'] = $row->docket_pin;
			$company['usdot_pin'] = $row->usdot_pin;
			$company['access_id'] = $row->access_id;
			$company['entity_number'] = $row->entity_number;
			$company['fl_username'] = $row->fl_username;
			$company['fl_password'] = $row->fl_password;
			$company['insurance_company'] = $row->insurance_company;
			$company['policy_number'] = $row->policy_number;
			$company['oregon_permit'] = $row->oregon_permit;
			$company['ucr_renewal_date'] = $row->ucr_renewal_date;
			$company['running_since'] = $row->running_since;
			$company['address'] = $row->address;
			$company['city'] = $row->city;
			$company['state'] = $row->state;
			$company['zip'] = $row->zip;
			$company['mailing_address'] = $row->mailing_address;
			$company['contact'] = $row->contact;
			$company['company_email'] = $row->company_email;
			$company['company_phone'] = $row->company_phone;
			$company['company_fax'] = $row->company_fax;
			$company['company_status'] = $row->company_status;
			$company['company_notes'] = $row->company_notes;
			$company['link_osbr'] = $row->link_osbr;
			$company['link_aoo'] = $row->link_aoo;
			$company['link_ein_letter'] = $row->link_ein_letter;
			$company['link_mc_letter'] = $row->link_mc_letter;
			$company['link_usdot_pin_letter'] = $row->link_usdot_pin_letter;
			$company['link_docket_pin_letter'] = $row->link_docket_pin_letter;
			$company['carrier_packet_guid'] = $row->carrier_packet_guid;
			
			$company["person"] = db_select_person($person_where);
			
		}
		
		if (empty($company))
		{
			return null;
		}else
			{
				return $company;
			}
	}//end db_select_company()	
	
	//SELECT COMPANYS (many)
	function db_select_companys($where,$order_by = 'id')
	{
		$CI =& get_instance();
		$where_sql = " ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				if ($i > 0)
				{
				$where_sql = $where_sql." And";
				}
				$where_sql = $where_sql." ".$key." = ?";
				$values[$i] = $value;
				$i++;
			}
			
			
		}
		else
		{
			$where_sql = $where;
		}
		$sql = "SELECT * FROM `company` WHERE ".$where_sql." ORDER BY ".$order_by;
		$query_company = $CI->db->query($sql,$values);
		
		$company = array();
		$companys = array();
		foreach ($query_company->result() as $row)
		{
			$company_where['id'] = $row->id;
			$company = db_select_company($company_where);
			
			$companys[] = $company;
		}
		
		return $companys;
	}//end db_select_companys() many	
	
	
	//UPDATE COMPANY
	function db_update_company($set,$where)
	{
		$CI =& get_instance();
		$i = 0;
		$set_sql = " ";
		foreach($set as $key => $value)
		{
			if ($i > 0)
			{
			$set_sql = $set_sql.", ";
			}
			$set_sql = $set_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$sql = "UPDATE company SET ".$set_sql." WHERE ".$where_sql;
		$CI->db->query($sql,$values);
		
		
	}//end update company

	
	//DELETE COMPANY
	function db_delete_company($company_id)
	{
		$CI =& get_instance();
		//ONLY COVAX13 CAN PERFORM DELETE OPERATIONS
		if($CI->session->userdata('username') == "covax13")
		{
			$sql = "DELETE FROM company WHERE id = ?";
			$CI->db->query($sql,array($company_id));
		}
		
	}// end delete company
**/

	
	
	
//CONTACT_ATTEMPT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT CONTACT_ATTEMPT
	function db_insert_contact_attempt($contact_attempt)
	{
		db_insert_table("contact_attempt",$contact_attempt);
	
	}//END db_insert_contact_attempt	

	//SELECT CONTACT_ATTEMPTS (many)
	function db_select_contact_attempts($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_contact_attempt($where,$order_by,$limit,"many");
		
	}//end db_select_contact_attempts() many	

	//SELECT CONTACT_ATTEMPT (one)
	function db_select_contact_attempt($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." contact_attempt.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." contact_attempt.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." contact_attempt.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `contact_attempt`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$contact_attempts = array();
		foreach ($query->result() as $row)
		{
			$contact_attempt['id'] = $row->id;
			$contact_attempt['truck_id'] = $row->truck_id;
			$contact_attempt['shift_report_id'] = $row->shift_report_id;
			$contact_attempt['dispatcher_person_id'] = $row->dispatcher_person_id;
			$contact_attempt['ca_time'] = $row->ca_time;
			$contact_attempt['ca_gps'] = $row->ca_gps;
			$contact_attempt['contact_method'] = $row->contact_method;
			$contact_attempt['expected_map_url'] = $row->expected_map_url;
			$contact_attempt['contact_result'] = $row->contact_result;
			$contact_attempt['expected_miles'] = $row->expected_miles;
			$contact_attempt['actual_miles'] = $row->actual_miles;
			$contact_attempt['efficiency_rating'] = $row->efficiency_rating;
			$contact_attempt['notes'] = $row->notes;
			$contact_attempt['computer_notes'] = $row->computer_notes;
			$contact_attempt['file_guid'] = $row->file_guid;
			
			$contact_attempts[] = $contact_attempt;
			
		}// end foreach
		
		if (empty($contact_attempt))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $contact_attempt;
		}
		else if($many == "many")
		{
			return $contact_attempts;
		}
	}//end db_select_contact_attempt()

	//UPDATE CONTACT_ATTEMPT
	function db_update_contact_attempt($set,$where)
	{
		db_update_table("contact_attempt",$set,$where);
		
	}//end update contact_attempt	
	
	//DELETE CONTACT_ATTEMPT	
	function db_delete_contact_attempt($where)
	{
		db_delete_from_table("contact_attempt",$where);
		
	}//end db_delete_contact_attempt()	
	
		
	
	
	
	
//CORPORATE_CARD: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT CORPORATE_CARD
	function db_insert_corporate_card($corporate_card)
	{
		db_insert_table("corporate_card",$corporate_card);
	
	}//END db_insert_corporate_card	

	//SELECT CORPORATE_CARDS (many)
	function db_select_corporate_cards($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_corporate_card($where,$order_by,$limit,"many");
		
	}//end db_select_corporate_cards() many	

	//SELECT CORPORATE_CARD (one)
	function db_select_corporate_card($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." corporate_card.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." corporate_card.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." corporate_card.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `corporate_card`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$corporate_cards = array();
		foreach ($query->result() as $row)
		{
			$corporate_card['id'] = $row->id;
			$corporate_card['person_id'] = $row->person_id;
			$corporate_card['account_id'] = $row->account_id;
			$corporate_card['card_name'] = $row->card_name;
			$corporate_card['last_four'] = $row->last_four;
			
			//GET PERSON
			$where = null;
			$where["id"] = $row->person_id;
			$person = db_select_person($where);
			
			$corporate_card['person'] = $person;
			
			//GET ACCOUNT
			$where = null;
			$where["id"] = $row->account_id;
			$account = db_select_account($where);
			
			$corporate_card['account'] = $account;
			
			$corporate_cards[] = $corporate_card;
			
		}// end foreach
		
		if (empty($corporate_card))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $corporate_card;
		}
		else if($many == "many")
		{
			return $corporate_cards;
		}
	}//end db_select_corporate_card()

	//UPDATE CORPORATE_CARD
	function db_update_corporate_card($set,$where)
	{
		db_update_table("corporate_card",$set,$where);
		
	}//end update corporate_card	
	
	//DELETE CORPORATE_CARD	
	function db_delete_corporate_card($where)
	{
		db_delete_from_table("corporate_card",$where);
		
	}//end db_delete_corporate_card()	
	
	
	
	
//CORPORATE_LOGIN: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT CORPORATE_LOGIN
	function db_insert_corporate_login($corporate_login)
	{
		db_insert_table("corporate_login",$corporate_login);
	
	}//END db_insert_corporate_login	

	//SELECT CORPORATE_LOGINS (many)
	function db_select_corporate_logins($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_corporate_login($where,$order_by,$limit,"many");
		
	}//end db_select_corporate_logins() many	

	//SELECT CORPORATE_LOGIN (one)
	function db_select_corporate_login($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." corporate_login.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." corporate_login.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." corporate_login.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `corporate_login`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$corporate_logins = array();
		foreach ($query->result() as $row)
		{
			$corporate_login['id'] = $row->id;
			$corporate_login['person_id'] = $row->person_id;
			$corporate_login['system_name'] = $row->system_name;
			$corporate_login['login'] = $row->login;
			$corporate_login['password'] = $row->password;
			$corporate_login['pin'] = $row->pin;
			
			
			$corporate_logins[] = $corporate_login;
			
		}// end foreach
		
		if (empty($corporate_login))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $corporate_login;
		}
		else if($many == "many")
		{
			return $corporate_logins;
		}
	}//end db_select_corporate_login()

	//UPDATE CORPORATE_LOGIN
	function db_update_corporate_login($set,$where)
	{
		db_update_table("corporate_login",$set,$where);
		
	}//end update corporate_login	
	
	//DELETE CORPORATE_LOGIN	
	function db_delete_corporate_login($where)
	{
		db_delete_from_table("corporate_login",$where);
		
	}//end db_delete_corporate_login()	
	
		
	
	
	
	
//CUSTOMER: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT CUSTOMER
	function db_insert_customer($customer)
	{
		db_insert_table("customer",$customer);
	
	}//END db_insert_customer	

	//SELECT CUSTOMERS (many)
	function db_select_customers($where,$order_by = 'id')
	{
		return db_select_tables("customer",$where,$order_by);
		
	}//end db_select_customers() many	

	//SELECT CUSTOMER (one)
	function db_select_customer($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `customer` WHERE ".$where_sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			$customer['id'] = $row->id;
			$customer['company_id'] = $row->company_id;
			$customer['customer_name'] = $row->customer_name;
			$customer['address'] = $row->address;
			$customer['city'] = $row->city;
			$customer['state'] = $row->state;
			$customer['zip'] = $row->zip;
			$customer['contact'] = $row->contact;
			$customer['phone'] = $row->phone;
			$customer['fax'] = $row->fax;
			$customer['email'] = $row->email;
			$customer['mc_number'] = $row->mc_number;
			$customer['form_of_payment'] = $row->form_of_payment;
			$customer['avg_payment_period'] = $row->avg_payment_period;
			$customer['status'] = $row->status;
			$customer['notes'] = $row->notes;
			
		}// end foreach
		
		if (empty($customer))
		{
			return null;
		}else
			{
				return $customer;
			}
	}//end db_select_customer()

	//UPDATE CUSTOMER
	function db_update_customer($set,$where)
	{
		db_update_table("customer",$set,$where);
		
	}//end update customer	
	
	//DELETE CUSTOMER	
	function db_delete_customer($where)
	{
		db_delete_from_table("customer",$where);
		
	}//end db_delete_customer()	


	
	

//DISPATCH_UPDATE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT DISPATCH_UPDATE
	function db_insert_dispatch_update($dispatch_update)
	{
		db_insert_table("dispatch_update",$dispatch_update);
	
	}//END db_insert_dispatch_update	

	//SELECT DISPATCH_UPDATES (many)
	function db_select_dispatch_updates($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_dispatch_update($where,$order_by,$limit,"many");
		
	}//end db_select_dispatch_updates() many	

	//SELECT DISPATCH_UPDATE (one)
	function db_select_dispatch_update($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." dispatch_update.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." dispatch_update.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." dispatch_update.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `dispatch_update`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$dispatch_updates = array();
		foreach ($query->result() as $row)
		{
			$dispatch_update['id'] = $row->id;
			$dispatch_update['load_id'] = $row->load_id;
			$dispatch_update['client_id'] = $row->client_id;
			$dispatch_update['client_email'] = $row->client_email;
			$dispatch_update['carrier_id'] = $row->carrier_id;
			$dispatch_update['carrier_email'] = $row->carrier_email;
			$dispatch_update['fleet_manager_id'] = $row->fleet_manager_id;
			$dispatch_update['fleet_manager_email'] = $row->fleet_manager_email;
			$dispatch_update['driver_manager_id'] = $row->driver_manager_id;
			$dispatch_update['driver_manager_email'] = $row->driver_manager_email;
			$dispatch_update['truck_id'] = $row->truck_id;
			$dispatch_update['trailer_id'] = $row->trailer_id;
			$dispatch_update['location'] = $row->location;
			$dispatch_update['gps'] = $row->gps;
			$dispatch_update['update_datetime'] = $row->update_datetime;
			$dispatch_update['hos_break'] = $row->hos_break;
			$dispatch_update['hos_drive'] = $row->hos_drive;
			$dispatch_update['hos_shift'] = $row->hos_shift;
			$dispatch_update['hos_cycle'] = $row->hos_cycle;
			$dispatch_update['hos_remaining_guid'] = $row->hos_remaining_guid;
			$dispatch_update['truck_fuel'] = $row->truck_fuel;
			$dispatch_update['truck_codes'] = $row->truck_codes;
			$dispatch_update['truck_codes_guid'] = $row->truck_codes_guid;
			$dispatch_update['trailer_fuel'] = $row->trailer_fuel;
			$dispatch_update['trailer_fuel_guid'] = $row->trailer_fuel_guid;
			$dispatch_update['reefer_temp'] = $row->reefer_temp;
			$dispatch_update['reefer_temp_guid'] = $row->reefer_temp_guid;
			$dispatch_update['trailer_codes'] = $row->trailer_codes;
			$dispatch_update['trailer_codes_guid'] = $row->trailer_codes_guid;
			$dispatch_update['recorder_id'] = $row->recorder_id;
			$dispatch_update['recorded_time'] = $row->recorded_time;
			$dispatch_update['email_html'] = $row->email_html;
			$dispatch_update['email_sent_datetime'] = $row->email_sent_datetime;
			$dispatch_update['reefer_set'] = $row->reefer_set;
			$dispatch_update['is_oor'] = $row->is_oor;
			$dispatch_update['oor_url'] = $row->oor_url;
			$dispatch_update['miles_driven'] = $row->miles_driven;
			$dispatch_update['miles_driven_url'] = $row->miles_driven_url;
			
			$dispatch_updates[] = $dispatch_update;
			
		}// end foreach
		
		if (empty($dispatch_update))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $dispatch_update;
		}
		else if($many == "many")
		{
			return $dispatch_updates;
		}
	}//end db_select_dispatch_update()

	//UPDATE DISPATCH_UPDATE
	function db_update_dispatch_update($set,$where)
	{
		db_update_table("dispatch_update",$set,$where);
		
	}//end update dispatch_update	
	
	//DELETE DISPATCH_UPDATE	
	function db_delete_dispatch_update($where)
	{
		db_delete_from_table("dispatch_update",$where);
		
	}//end db_delete_dispatch_update()	
	
	
	
	
	
	
	
//DEFAULT_ACCOUNT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT DEFAULT_ACCOUNT
	function db_insert_default_account($default_account)
	{
		db_insert_table("default_account",$default_account);
	
	}//END db_insert_default_account	

	//SELECT DEFAULT_ACCOUNTS (many)
	function db_select_default_accounts($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_default_account($where,$order_by,$limit,"many");
		
	}//end db_select_default_accounts() many	

	//SELECT DEFAULT_ACCOUNT (one)
	function db_select_default_account($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." default_account.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." default_account.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." default_account.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM default_account ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$default_accounts = array();
		foreach ($query->result() as $row)
		{
			$default_account['id'] = $row->id;
			$default_account['company_id'] = $row->company_id;
			$default_account['account_id'] = $row->account_id;
			$default_account['type'] = $row->type;
			$default_account['category'] = $row->category;
		
			$default_accounts[] = $default_account;
			
		}// end foreach
		
		if (empty($default_account))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $default_account;
		}
		else if($many == "many")
		{
			return $default_accounts;
		}
	}//end db_select_default_account()

	//UPDATE DEFAULT_ACCOUNT
	function db_update_default_account($set,$where)
	{
		db_update_table("default_account",$set,$where);
		
	}//end update default_account	
	
	//DELETE DEFAULT_ACCOUNT	
	function db_delete_default_account($where)
	{
		db_delete_from_table("default_account",$where);
		
	}//end db_delete_default_account()	
	
	
	
	
	
	

//DROP: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT DROP
	function db_insert_drop($drop)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($drop as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO `drop` (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}//END db_insert_drop	

	//SELECT DROPS (many)
	function db_select_drops($where,$order_by = 'id')
	{
		return db_select_tables("drop",$where,$order_by);
		
	}//end db_select_drops() many	

	//SELECT DROP (one)
	function db_select_drop($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `drop` WHERE ".$where_sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			//GET STOP
			$stop_where["id"] = $row->stop_id;
			$stop = db_select_stop($stop_where);
			
			$drop['id'] = $row->id;
			$drop['stop_id'] = $row->stop_id;
			$drop['load_id'] = $row->load_id;
			$drop['drop_number'] = $row->drop_number;
			$drop['ref_number'] = $row->ref_number;
			$drop['appointment_time'] = $row->appointment_time;
			$drop['appointment_time_mst'] = $row->appointment_time_mst;
			$drop['in_time'] = $row->in_time;
			$drop['out_time'] = $row->out_time;
			$drop['dispatch_datetime'] = $row->dispatch_datetime;
			$drop['dispatch_notes'] = $row->dispatch_notes;
			$drop['internal_notes'] = $row->internal_notes;
			
			$drop['stop'] = $stop;
			
		}// end foreach
		
		if (empty($drop))
		{
			return null;
		}else
			{
				return $drop;
			}
	}//end db_select_drop()

	//UPDATE DROP
	function db_update_drop($set,$where)
	{
		db_update_table("drop",$set,$where);
	}//end update drop	
	
	//DELETE DROP
	function db_delete_drop($drop_id)
	{
		$sql = "DELETE FROM `drop` WHERE id = ?";
		$CI =& get_instance();
		$CI->db->query($sql,array($drop_id));
		
	}//end db_delete_drop()		

	
	
	
//DRIVER_APPLICATION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT DRIVER_APPLICATION
	function db_insert_driver_application($driver_application)
	{
		db_insert_table("driver_application",$driver_application);
	
	}//END db_insert_driver_application	

	//SELECT DRIVER_APPLICATIONS (many)
	function db_select_driver_applications($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_driver_application($where,$order_by,$limit,"many");
		
	}//end db_select_driver_applications() many	

	//SELECT DRIVER_APPLICATION (one)
	function db_select_driver_application($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." driver_application.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." driver_application.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." driver_application.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM driver_application ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$driver_applications = array();
		foreach ($query->result() as $row)
		{
			$driver_application['id'] = $row->id;
			$driver_application['client_id'] = $row->client_id;
			$driver_application['application_datetime'] = $row->application_datetime;
			$driver_application['application_status'] = $row->application_status;
			$driver_application['f_name'] = $row->f_name;
			$driver_application['m_name'] = $row->m_name;
			$driver_application['l_name'] = $row->l_name;
			$driver_application['phone_number'] = $row->phone_number;
			$driver_application['email'] = $row->email;
			$driver_application['dob'] = $row->dob;
			$driver_application['ssn'] = $row->ssn;
			$driver_application['driving_experience'] = $row->driving_experience;
			$driver_application['drive_team'] = $row->drive_team;
			$driver_application['drive_otr'] = $row->drive_otr;
			$driver_application['availability_date'] = $row->availability_date;
			$driver_application['current_address'] = $row->current_address;
			$driver_application['previous_address_1'] = $row->previous_address_1;
			$driver_application['previous_address_2'] = $row->previous_address_2;
			$driver_application['previous_address_3'] = $row->previous_address_3;
			
			$driver_application['previous_license_number_1'] = $row->previous_license_number_1;
			$driver_application['previous_license_state_1'] = $row->previous_license_state_1;
			$driver_application['previous_license_type_1'] = $row->previous_license_type_1;
			$driver_application['previous_license_exp_date_1'] = $row->previous_license_exp_date_1;
			
			$driver_application['previous_license_number_2'] = $row->previous_license_number_2;
			$driver_application['previous_license_state_2'] = $row->previous_license_state_2;
			$driver_application['previous_license_type_2'] = $row->previous_license_type_2;
			$driver_application['previous_license_exp_date_2'] = $row->previous_license_exp_date_2;
			
			$driver_application['previous_license_number_3'] = $row->previous_license_number_3;
			$driver_application['previous_license_state_3'] = $row->previous_license_state_3;
			$driver_application['previous_license_type_3'] = $row->previous_license_type_3;
			$driver_application['previous_license_exp_date_3'] = $row->previous_license_exp_date_4;
			
			$driver_application['previous_license_number_4'] = $row->previous_license_number_4;
			$driver_application['previous_license_state_4'] = $row->previous_license_state_4;
			$driver_application['previous_license_type_4'] = $row->previous_license_type_4;
			$driver_application['previous_license_exp_date_4'] = $row->previous_license_exp_date_4;
			
			$driver_application['accident_date_1'] = $row->accident_date_1;
			$driver_application['accident_nature_1'] = $row->accident_nature_1;
			$driver_application['accident_fatalities_1'] = $row->accident_fatalities_1;
			$driver_application['accident_injuries_1'] = $row->accident_injuries_1;
			
			$driver_application['accident_date_2'] = $row->accident_date_2;
			$driver_application['accident_nature_2'] = $row->accident_nature_2;
			$driver_application['accident_fatalities_2'] = $row->accident_fatalities_2;
			$driver_application['accident_injuries_2'] = $row->accident_injuries_2;
			
			$driver_application['accident_date_3'] = $row->accident_date_3;
			$driver_application['accident_nature_3'] = $row->accident_nature_3;
			$driver_application['accident_fatalities_3'] = $row->accident_fatalities_3;
			$driver_application['accident_injuries_3'] = $row->accident_injuries_3;
			
			$driver_application['previous_job_employer_name_1'] = $row->previous_job_employer_name_1;
			$driver_application['previous_job_address_1'] = $row->previous_job_address_1;
			$driver_application['previous_job_position_1'] = $row->previous_job_position_1;
			$driver_application['previous_job_start_date_1'] = $row->previous_job_start_date_1;
			$driver_application['previous_job_end_date_1'] = $row->previous_job_end_date_1;
			$driver_application['previous_job_salary_1'] = $row->previous_job_salary_1;
			$driver_application['previous_job_reason_for_leaving_1'] = $row->previous_job_reason_for_leaving_1;
			$driver_application['previous_job_subject_to_fmcsr_1'] = $row->previous_job_subject_to_fmcsr_1;
			$driver_application['previous_job_drug_test_1'] = $row->previous_job_drug_test_1;
			
			$driver_application['previous_job_employer_name_2'] = $row->previous_job_employer_name_2;
			$driver_application['previous_job_address_2'] = $row->previous_job_address_2;
			$driver_application['previous_job_position_2'] = $row->previous_job_position_2;
			$driver_application['previous_job_start_date_2'] = $row->previous_job_start_date_2;
			$driver_application['previous_job_end_date_2'] = $row->previous_job_end_date_2;
			$driver_application['previous_job_salary_2'] = $row->previous_job_salary_2;
			$driver_application['previous_job_reason_for_leaving_2'] = $row->previous_job_reason_for_leaving_2;
			$driver_application['previous_job_subject_to_fmcsr_2'] = $row->previous_job_subject_to_fmcsr_2;
			$driver_application['previous_job_drug_test_2'] = $row->previous_job_drug_test_2;
			
			$driver_application['previous_job_employer_name_3'] = $row->previous_job_employer_name_3;
			$driver_application['previous_job_address_3'] = $row->previous_job_address_3;
			$driver_application['previous_job_position_3'] = $row->previous_job_position_3;
			$driver_application['previous_job_start_date_3'] = $row->previous_job_start_date_3;
			$driver_application['previous_job_end_date_3'] = $row->previous_job_end_date_3;
			$driver_application['previous_job_salary_3'] = $row->previous_job_salary_3;
			$driver_application['previous_job_reason_for_leaving_3'] = $row->previous_job_reason_for_leaving_3;
			$driver_application['previous_job_subject_to_fmcsr_3'] = $row->previous_job_subject_to_fmcsr_3;
			$driver_application['previous_job_drug_test_3'] = $row->previous_job_drug_test_3;
			
			$driver_application['tested_positive_or_refused'] = $row->tested_positive_or_refused;
			$driver_application['medical_card_link'] = $row->medical_card_link;
			$driver_application['license_link'] = $row->license_link;
			$driver_application['ss_card_link'] = $row->ss_card_link;
			
			$driver_application['personal_reference_1'] = $row->personal_reference_1;
			$driver_application['personal_reference_relationship_1'] = $row->personal_reference_relationship_1;
			$driver_application['personal_reference_number_1'] = $row->personal_reference_number_1;
			$driver_application['personal_reference_address_1'] = $row->personal_reference_address_1;
			
			$driver_application['personal_reference_2'] = $row->personal_reference_2;
			$driver_application['personal_reference_relationship_2'] = $row->personal_reference_relationship_2;
			$driver_application['personal_reference_number_2'] = $row->personal_reference_number_2;
			$driver_application['personal_reference_address_2'] = $row->personal_reference_address_2;
			
			$driver_application['personal_reference_3'] = $row->personal_reference_3;
			$driver_application['personal_reference_relationship_3'] = $row->personal_reference_relationship_3;
			$driver_application['personal_reference_number_3'] = $row->personal_reference_number_3;
			$driver_application['personal_reference_address_3'] = $row->personal_reference_address_3;
			
			$driver_application['applicant_status_log'] = $row->applicant_status_log;
			
			$driver_applications[] = $driver_application;
			
		}// end foreach
		
		if (empty($driver_application))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $driver_application;
		}
		else if($many == "many")
		{
			return $driver_applications;
		}
	}//end db_select_driver_application()

	//UPDATE DRIVER_APPLICATION
	function db_update_driver_application($set,$where)
	{
		db_update_table("driver_application",$set,$where);
		
	}//end update driver_application	
	
	//DELETE DRIVER_APPLICATION	
	function db_delete_driver_application($where)
	{
		db_delete_from_table("driver_application",$where);
		
	}//end db_delete_driver_application()	
	

	
	
	
//DRIVER_IN: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT DRIVER_IN
	function db_insert_driver_in($driver_in)
	{
		db_insert_table("driver_in",$driver_in);
	
	}//END db_insert_driver_in	

	//SELECT DRIVER_INS (many)
	function db_select_driver_ins($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_driver_in($where,$order_by,$limit,"many");
		
	}//end db_select_driver_ins() many	

	//SELECT DRIVER_IN (one)
	function db_select_driver_in($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." driver_in.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." driver_in.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." driver_in.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `driver_in`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$driver_ins = array();
		foreach ($query->result() as $row)
		{
			$driver_in['id'] = $row->id;
			$driver_in['log_entry_id'] = $row->log_entry_id;
			$driver_in['rental_agreement_guid'] = $row->rental_agreement_guid;
			$driver_in['oo_lease_agreement_guid'] = $row->oo_lease_agreement_guid;
			
			$driver_ins[] = $driver_in;
			
		}// end foreach
		
		if (empty($driver_in))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $driver_in;
		}
		else if($many == "many")
		{
			return $driver_ins;
		}
	}//end db_select_driver_in()

	//UPDATE DRIVER_IN
	function db_update_driver_in($set,$where)
	{
		db_update_table("driver_in",$set,$where);
		
	}//end update driver_in	
	
	//DELETE DRIVER_IN	
	function db_delete_driver_in($where)
	{
		db_delete_from_table("driver_in",$where);
		
	}//end db_delete_driver_in()	
	
	
	
	
	
	

//ESIGN_DOC: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT ESIGN_DOC
	function db_insert_esign_doc($esign_doc)
	{
		db_insert_table("esign_doc",$esign_doc);
	
	}//END db_insert_esign_doc	

	//SELECT ESIGN_DOCS (many)
	function db_select_esign_docs($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_esign_doc($where,$order_by,$limit,"many");
		
	}//end db_select_esign_docs() many	

	//SELECT ESIGN_DOC (one)
	function db_select_esign_doc($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." esign_doc.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." esign_doc.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." esign_doc.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				esign_doc.id,
				esign_doc.recipient_person_id,
				person.f_name,
				person.l_name,
				esign_doc.recipient_user_id,
				user.username,
				esign_doc.upload_datetime,
				esign_doc.unsigned_doc_guid,
				unsigned_file.title AS unsigned_title,
				unsigned_file.category AS unsigned_category,
				esign_doc.signed_doc_guid,
				signed_file.title AS signed_title,
				signed_file.category AS signed_category,
				esign_doc.signed_doc_hash,
				esign_doc.explanation_link,
				esign_doc.esign_disclaimer,
				esign_doc.disclaimer_agreed_datetime,
				esign_doc.esign_statement,
				esign_doc.statement_agreed_datetime,
				esign_doc.signature_text,
				esign_doc.signer_user_id,
				esign_doc.signed_datetime,
				esign_doc.signed_gps,
				esign_doc.signed_ip 
				FROM `esign_doc`
				LEFT JOIN person ON esign_doc.recipient_person_id = person.id 
				LEFT JOIN user ON esign_doc.recipient_user_id = user.id
				LEFT JOIN secure_file AS unsigned_file ON esign_doc.unsigned_doc_guid = unsigned_file.file_guid
				LEFT JOIN secure_file AS signed_file ON esign_doc.signed_doc_guid = signed_file.file_guid
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$esign_docs = array();
		foreach ($query->result() as $row)
		{
			$esign_doc['id'] = $row->id;
			$esign_doc['recipient_person_id'] = $row->recipient_person_id;
			$esign_doc['recipient_user_id'] = $row->recipient_user_id;
			$esign_doc['upload_datetime'] = $row->upload_datetime;
			$esign_doc['unsigned_doc_guid'] = $row->unsigned_doc_guid;
			$esign_doc['signed_doc_guid'] = $row->signed_doc_guid;
			$esign_doc['signed_doc_hash'] = $row->signed_doc_hash;
			$esign_doc['explanation_link'] = $row->explanation_link;
			$esign_doc['esign_disclaimer'] = $row->esign_disclaimer;
			$esign_doc['disclaimer_agreed_datetime'] = $row->disclaimer_agreed_datetime;
			$esign_doc['esign_statement'] = $row->esign_statement;
			$esign_doc['statement_agreed_datetime'] = $row->statement_agreed_datetime;
			$esign_doc['signature_text'] = $row->signature_text;
			$esign_doc['signer_user_id'] = $row->signer_user_id;
			$esign_doc['signed_datetime'] = $row->signed_datetime;
			$esign_doc['signed_gps'] = $row->signed_gps;
			$esign_doc['signed_ip'] = $row->signed_ip;
			
			$person["f_name"] = $row->f_name;
			$person["l_name"] = $row->l_name;
			$esign_doc["person"] = $person;
			
			$unsigned_doc["title"] = $row->unsigned_title;
			$unsigned_doc["category"] = $row->unsigned_category;
			$esign_doc["unsigned_doc"] = $unsigned_doc;
			
			$signed_doc["title"] = $row->signed_title;
			$signed_doc["category"] = $row->signed_category;
			$esign_doc["signed_doc"] = $signed_doc;
			
			$esign_docs[] = $esign_doc;
			
		}// end foreach
		
		if (empty($esign_doc))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $esign_doc;
		}
		else if($many == "many")
		{
			return $esign_docs;
		}
	}//end db_select_esign_doc()

	//UPDATE ESIGN_DOC
	function db_update_esign_doc($set,$where)
	{
		db_update_table("esign_doc",$set,$where);
		
	}//end update esign_doc	
	
	//DELETE ESIGN_DOC	
	function db_delete_esign_doc($where)
	{
		db_delete_from_table("esign_doc",$where);
		
	}//end db_delete_esign_doc()	
	
	
	
	
	
	
//EXPENSE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT EXPENSE
	function db_insert_expense($expense)
	{
		db_insert_table("expense",$expense);
	
	}//END db_insert_expense	

	//SELECT EXPENSES (many)
	function db_select_expenses($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_expense($where,$order_by,$limit,"many");
		
	}//end db_select_expenses() many	

	//SELECT EXPENSE (one)
	function db_select_expense($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." expense.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." expense.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." expense.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `expense` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$expenses = array();
		foreach ($query->result() as $row)
		{
			$expense['id'] = $row->id;
			$expense['expense_type'] = $row->expense_type;
			$expense['expense_account_id'] = $row->expense_account_id;
			$expense['settlement_id'] = $row->settlement_id;
			$expense['fm_id'] = $row->fm_id;
			$expense['client_id'] = $row->client_id;
			$expense['truck_id'] = $row->truck_id;
			$expense['trailer_id'] = $row->trailer_id;
			$expense['issuer_id'] = $row->issuer_id;
			$expense['owner_type'] = $row->owner_type;
			$expense['company_id'] = $row->company_id;
			$expense['expense_datetime'] = $row->expense_datetime;
			$expense['category'] = $row->category;
			$expense['second_category'] = $row->second_category;
			$expense['debit_credit'] = $row->debit_credit;
			$expense['expense_amount'] = $row->expense_amount;
			$expense['description'] = $row->description;
			$expense['recorded_datetime'] = $row->recorded_datetime;
			$expense['allocated_datetime'] = $row->allocated_datetime;
			$expense['paid_datetime'] = $row->paid_datetime;
			$expense['link'] = $row->link;
			$expense['expense_notes'] = $row->expense_notes;
			$expense['locked_datetime'] = $row->locked_datetime;
			$expense['guid'] = $row->guid;
			$expense['report_guid'] = $row->report_guid;
			
			$expenses[] = $expense;
			
		}// end foreach
		
		if (empty($expense))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $expense;
		}
		else if($many == "many")
		{
			return $expenses;
		}
	}//end db_select_expense()

	//UPDATE EXPENSE
	function db_update_expense($set,$where)
	{
		db_update_table("expense",$set,$where);
		
	}//end update expense	
	
	//DELETE EXPENSE	
	function db_delete_expense($where)
	{
		db_delete_from_table("expense",$where);
		
	}//end db_delete_expense()	
		

		
		
//EXPENSE_CATEGORY: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT EXPENSE_CATEGORY
	function db_insert_expense_category($expense_category)
	{
		db_insert_table("expense_category",$expense_category);
	
	}//END db_insert_expense_category	

	//SELECT EXPENSE_CATEGORYS (many)
	function db_select_expense_categorys($where,$order_by = 'category',$limit = 'all')
	{
		return db_select_expense_category($where,$order_by,$limit,"many");
		
	}//end db_select_expense_categorys() many	

	//SELECT EXPENSE_CATEGORY (one)
	function db_select_expense_category($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." expense_category.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." expense_category.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." expense_category.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = 	"
					SELECT 
					*
					FROM `expense_category`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$expense_categorys = array();
		foreach ($query->result() as $row)
		{
			$expense_category['id'] = $row->id;
			$expense_category['company_id'] = $row->company_id;
			$expense_category['category'] = $row->category;
			$expense_category['approver_role'] = $row->approver_role;
			
			$expense_categorys[] = $expense_category;
			
		}// end foreach
		
		if (empty($expense_category))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $expense_category;
		}
		else if($many == "many")
		{
			return $expense_categorys;
		}
	}//end db_select_expense_category()

	//UPDATE EXPENSE_CATEGORY
	function db_update_expense_category($set,$where)
	{
		db_update_table("expense_category",$set,$where);
		
	}//end update expense_category	
	
	//DELETE EXPENSE_CATEGORY	
	function db_delete_expense_category($where)
	{
		db_delete_from_table("expense_category",$where);
		
	}//end db_delete_expense_category()	
	
		
		


//FILE_ACCESS_PERMISSION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT FILE_ACCESS_PERMISSION
	function db_insert_file_access_permission($file_access_permission)
	{
		db_insert_table("file_access_permission",$file_access_permission);
	
	}//END db_insert_file_access_permission	

	//SELECT FILE_ACCESS_PERMISSIONS (many)
	function db_select_file_access_permissions($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_file_access_permission($where,$order_by,$limit,"many");
		
	}//end db_select_file_access_permissions() many	

	//SELECT FILE_ACCESS_PERMISSION (one)
	function db_select_file_access_permission($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." file_access_permission.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." file_access_permission.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." file_access_permission.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM `file_access_permission` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$file_access_permissions = array();
		foreach ($query->result() as $row)
		{
			$file_access_permission['id'] = $row->id;
			$file_access_permission['file_guid'] = $row->file_guid;
			$file_access_permission['user_id'] = $row->user_id;
			
			$file_access_permissions[] = $file_access_permission;
			
		}// end foreach
		
		if (empty($file_access_permission))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $file_access_permission;
		}
		else if($many == "many")
		{
			return $file_access_permissions;
		}
	}//end db_select_file_access_permission()

	//UPDATE FILE_ACCESS_PERMISSION
	function db_update_file_access_permission($set,$where)
	{
		db_update_table("file_access_permission",$set,$where);
		
	}//end update file_access_permission	
	
	//DELETE FILE_ACCESS_PERMISSION	
	function db_delete_file_access_permission($where)
	{
		db_delete_from_table("file_access_permission",$where);
		
	}//end db_delete_file_access_permission()	
	


		
	
	
	
//FUEL_ALLOCATION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT FUEL_ALLOCATION
	function db_insert_fuel_allocation($fuel_allocation)
	{
		db_insert_table("fuel_allocation",$fuel_allocation);
	
	}//END db_insert_fuel_allocation	

	//SELECT FUEL_ALLOCATIONS (many)
	function db_select_fuel_allocations($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_fuel_allocation($where,$order_by,$limit,"many");
		
	}//end db_select_fuel_allocations() many	

	//SELECT FUEL_ALLOCATION (one)
	function db_select_fuel_allocation($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." fuel_allocation.".$key." LIKE ?";
				}
				else if ($value == null)
				{
					$where_sql = $where_sql." fuel_allocation.".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." fuel_allocation.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM `fuel_allocation` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$fuel_allocations = array();
		foreach ($query->result() as $row)
		{
			$fuel_allocation['id'] = $row->id;
			$fuel_allocation['fuel_stop_id'] = $row->fuel_stop_id;
			$fuel_allocation['leg_id'] = $row->leg_id;
			$fuel_allocation['miles'] = $row->miles;
			$fuel_allocation['percentage'] = $row->percentage;
			$fuel_allocation['gallons'] = $row->gallons;
			$fuel_allocation['reefer_gallons'] = $row->reefer_gallons;
			$fuel_allocation['expense'] = $row->expense;
			$fuel_allocation['reefer_expense'] = $row->reefer_expense;
			$fuel_allocation['allocation_datetime'] = $row->allocation_datetime;
			
			$fuel_allocations[] = $fuel_allocation;
			
		}// end foreach
		
		if (empty($fuel_allocation))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $fuel_allocation;
		}
		else if($many == "many")
		{
			return $fuel_allocations;
		}
	}//end db_select_fuel_allocation()

	//UPDATE FUEL_ALLOCATION
	function db_update_fuel_allocation($set,$where)
	{
		db_update_table("fuel_allocation",$set,$where);
		
	}//end update fuel_allocation	
	
	//DELETE FUEL_ALLOCATION	
	function db_delete_fuel_allocation($where)
	{
		db_delete_from_table("fuel_allocation",$where);
		
	}//end db_delete_fuel_allocation()	
	
	
	
//FUEL_AVERAGE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT FUEL_AVERAGE
	function db_insert_fuel_average($fuel_average)
	{
		db_insert_table("fuel_average",$fuel_average);
	
	}//END db_insert_fuel_average	

	//SELECT FUEL_AVERAGES (many)
	function db_select_fuel_averages($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_fuel_average($where,$order_by,$limit,"many");
		
	}//end db_select_fuel_averages() many	

	//SELECT FUEL_AVERAGE (one)
	function db_select_fuel_average($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." fuel_average.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." fuel_average.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." fuel_average.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM `fuel_average` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$fuel_averages = array();
		foreach ($query->result() as $row)
		{
			$fuel_average['id'] = $row->id;
			$fuel_average['datetime'] = $row->datetime;
			$fuel_average['fuel_avg'] = $row->fuel_avg;
			
			$fuel_averages[] = $fuel_average;
			
		}// end foreach
		
		if (empty($fuel_average))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $fuel_average;
		}
		else if($many == "many")
		{
			return $fuel_averages;
		}
	}//end db_select_fuel_average()

	//UPDATE FUEL_AVERAGE
	function db_update_fuel_average($set,$where)
	{
		db_update_table("fuel_average",$set,$where);
		
	}//end update fuel_average	
	
	//DELETE FUEL_AVERAGE	
	function db_delete_fuel_average($where)
	{
		db_delete_from_table("fuel_average",$where);
		
	}//end db_delete_fuel_average()	
	
	

	

//FUEL_PERMIT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT FUEL_PERMIT
	function db_insert_fuel_permit($fuel_permit)
	{
		db_insert_table("fuel_permit",$fuel_permit);
	
	}//END db_insert_fuel_permit	

	//SELECT FUEL_PERMITS (many)
	function db_select_fuel_permits($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_fuel_permit($where,$order_by,$limit,"many");
		
	}//end db_select_fuel_permits() many	

	//SELECT FUEL_PERMIT (one)
	function db_select_fuel_permit($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." fuel_permit.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." fuel_permit.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." fuel_permit.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `fuel_permit` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$fuel_permits = array();
		foreach ($query->result() as $row)
		{
			$fuel_permit['id'] = $row->id;
			$fuel_permit['fuel_stop_id'] = $row->fuel_stop_id;
			$fuel_permit['account_entry_id'] = $row->account_entry_id;
			$fuel_permit['permit_type'] = $row->permit_type;
			$fuel_permit['permit_datetime'] = $row->permit_datetime;
			$fuel_permit['permit_expense'] = $row->permit_expense;
			$fuel_permit['permit_link'] = $row->permit_link;
			$fuel_permit['permit_notes'] = $row->permit_notes;
			
			$fuel_permits[] = $fuel_permit;
			
		}// end foreach
		
		if (empty($fuel_permit))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $fuel_permit;
		}
		else if($many == "many")
		{
			return $fuel_permits;
		}
	}//end db_select_fuel_permit()

	//UPDATE FUEL_PERMIT
	function db_update_fuel_permit($set,$where)
	{
		db_update_table("fuel_permit",$set,$where);
		
	}//end update fuel_permit	
	
	//DELETE FUEL_PERMIT	
	function db_delete_fuel_permit($where)
	{
		db_delete_from_table("fuel_permit",$where);
		
	}//end db_delete_fuel_permit()	
	
		


	

//FUEL_STOP: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT FUEL_STOP
	function db_insert_fuel_stop($fuel_stop)
	{
		db_insert_table("fuel_stop",$fuel_stop);
	
	}//END db_insert_fuel_stop	

	//SELECT FUEL_STOPS (many)
	function db_select_fuel_stops($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_fuel_stop($where,$order_by,$limit,"many");
		
	}//end db_select_fuel_stops() many	

	//SELECT FUEL_STOP (one)
	function db_select_fuel_stop($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." fuel_stop.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." fuel_stop.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." fuel_stop.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM `fuel_stop` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$fuel_stops = array();
		foreach ($query->result() as $row)
		{
			$fuel_stop['id'] = $row->id;
			$fuel_stop['log_entry_id'] = $row->log_entry_id;
			$fuel_stop['is_fill'] = $row->is_fill;
			$fuel_stop['gallons'] = $row->gallons;
			$fuel_stop['fuel_price'] = $row->fuel_price;
			$fuel_stop['fuel_expense'] = $row->fuel_expense;
			$fuel_stop['rebate_amount'] = $row->rebate_amount;
			$fuel_stop['natl_fuel_avg'] = $row->natl_fuel_avg;
			$fuel_stop['source'] = $row->source;
			$fuel_stop['fill_to_fill_gallons'] = $row->fill_to_fill_gallons;
			$fuel_stop['fill_to_fill_expense'] = $row->fill_to_fill_expense;
			$fuel_stop['fill_to_fill_rebate'] = $row->fill_to_fill_rebate;
			$fuel_stop['map_miles'] = $row->map_miles;
			$fuel_stop['odom_miles'] = $row->odom_miles;
			$fuel_stop['allocation_account_id'] = $row->allocation_account_id;
			$fuel_stop['allocated_entry_id'] = $row->allocated_entry_id;
			$fuel_stop['guid'] = $row->guid;
			
			$fuel_stops[] = $fuel_stop;
			
		}// end foreach
		
		if (empty($fuel_stop))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $fuel_stop;
		}
		else if($many == "many")
		{
			return $fuel_stops;
		}
	}//end db_select_fuel_stop()

	//UPDATE FUEL_STOP
	function db_update_fuel_stop($set,$where)
	{
		db_update_table("fuel_stop",$set,$where);
		
	}//end update fuel_stop	
	
	//DELETE FUEL_STOP	
	function db_delete_fuel_stop($where)
	{
		db_delete_from_table("fuel_stop",$where);
		
	}//end db_delete_fuel_stop()	

	
	
	
//GEOCODE_REQUEST: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT GEOCODE_REQUEST
	function db_insert_geocode_request($geocode_request)
	{
		db_insert_table("geocode_request",$geocode_request);
	
	}//END db_insert_geocode_request	

	//SELECT GEOCODE_REQUESTS (many)
	function db_select_geocode_requests($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_geocode_request($where,$order_by,$limit,"many");
		
	}//end db_select_geocode_requests() many	

	//SELECT GEOCODE_REQUEST (one)
	function db_select_geocode_request($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." geocode_request.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." geocode_request.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." geocode_request.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `geocode_request`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$geocode_requests = array();
		foreach ($query->result() as $row)
		{
			$geocode_request['id'] = $row->id;
			$geocode_request['original_request_datetime'] = $row->original_request_datetime;
			$geocode_request['latlng'] = $row->latlng;
			$geocode_request['status'] = $row->status;
			$geocode_request['street_number'] = $row->street_number;
			$geocode_request['street'] = $row->street;
			$geocode_request['city'] = $row->city;
			$geocode_request['state'] = $row->state;
			$geocode_request['formatted_address'] = $row->formatted_address;
			$geocode_request['request_count'] = $row->request_count;
			
			$geocode_requests[] = $geocode_request;
			
		}// end foreach
		
		if (empty($geocode_request))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $geocode_request;
		}
		else if($many == "many")
		{
			return $geocode_requests;
		}
	}//end db_select_geocode_request()

	//UPDATE GEOCODE_REQUEST
	function db_update_geocode_request($set,$where)
	{
		db_update_table("geocode_request",$set,$where);
		
	}//end update geocode_request	
	
	//DELETE GEOCODE_REQUEST	
	function db_delete_geocode_request($where)
	{
		db_delete_from_table("geocode_request",$where);
		
	}//end db_delete_geocode_request()	
	
	
	
	
	
	
//GEOPOINT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT GEOPOINT
	function db_insert_geopoint($geopoint)
	{
		db_insert_table("geopoint",$geopoint);
	
	}//END db_insert_geopoint	

	//SELECT GEOPOINTS (many)
	function db_select_geopoints($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_geopoint($where,$order_by,$limit,"many");
		
	}//end db_select_geopoints() many	

	//SELECT GEOPOINT (one)
	function db_select_geopoint($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where)){
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where)){
			$i = 0;
			$values = array();
			foreach($where as $key => $value){
				
				if ($i > 0){
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null){
					$where_sql = $where_sql." geopoint.".$key." is ?";
				}else if (substr($value,0,1) == "%" || substr($value,-1) == "%"){
					$where_sql = $where_sql." geopoint.".$key." LIKE ?";
				}else{
					$where_sql = $where_sql." geopoint.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM geopoint ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		//echo $sql;
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$geopoints = array();
		foreach ($query->result() as $row)
		{
			$geopoint['id'] = $row->id;
			$geopoint['truck_id'] = $row->truck_id;
			$geopoint['latitude'] = $row->latitude;
			$geopoint['longitude'] = $row->longitude;
			$geopoint['heading'] = $row->heading;
			$geopoint['datetime'] = $row->datetime;
			$geopoint['speed'] = $row->speed;
			$geopoint['power'] = $row->power;
			$geopoint['odometer'] = $row->odometer;
			$geopoint['is_oor'] = $row->is_oor;
			$geopoint['oor_url'] = $row->oor_url;
			
			$geopoints[] = $geopoint;
			
		}// end foreach
		
		if (empty($geopoint))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $geopoint;
		}
		else if($many == "many")
		{
			return $geopoints;
		}
	}//end db_select_geopoint()

	//UPDATE GEOPOINT
	function db_update_geopoint($set,$where)
	{
		db_update_table("geopoint",$set,$where);
		
	}//end update geopoint	
	
	//DELETE GEOPOINT	
	function db_delete_geopoint($where)
	{
		db_delete_from_table("geopoint",$where);
		
	}//end db_delete_geopoint()	


	
	
	
	
//GOALPOINT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT GOALPOINT
	function db_insert_goalpoint($goalpoint)
	{
		db_insert_table("goalpoint",$goalpoint);
	
	}//END db_insert_goalpoint	

	//SELECT GOALPOINTS (many)
	function db_select_goalpoints($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_goalpoint($where,$order_by,$limit,"many");
		
	}//end db_select_goalpoints() many	

	//SELECT GOALPOINT (one)
	function db_select_goalpoint($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." goalpoint.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." goalpoint.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." goalpoint.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `goalpoint`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$goalpoints = array();
		foreach ($query->result() as $row)
		{
			$goalpoint['id'] = $row->id;
			$goalpoint['load_id'] = $row->load_id;
			$goalpoint['truck_id'] = $row->truck_id;
			$goalpoint['trailer_id'] = $row->trailer_id;
			$goalpoint['client_id'] = $row->client_id;
			$goalpoint['shift_report_id'] = $row->shift_report_id;
			$goalpoint['gp_order'] = $row->gp_order;
			$goalpoint['sync_gp_guid'] = $row->sync_gp_guid;
			$goalpoint['expected_time'] = $row->expected_time;
			$goalpoint['duration'] = $row->duration;//in minutes
			$goalpoint['expected_duration'] = $row->expected_duration;//in minutes
			$goalpoint['deadline'] = $row->deadline;
			$goalpoint['leeway'] = $row->leeway;
			$goalpoint['gp_type'] = $row->gp_type;
			$goalpoint['arrival_departure'] = $row->arrival_departure;
			$goalpoint['gps'] = $row->gps;
			$goalpoint['location_name'] = $row->location_name;
			$goalpoint['location'] = $row->location;
			$goalpoint['dm_notes'] = $row->dm_notes;
			$goalpoint['dispatch_notes'] = $row->dispatch_notes;
			$goalpoint['completion_time'] = $row->completion_time;
			
			$goalpoints[] = $goalpoint;
			
		}// end foreach
		
		if (empty($goalpoint))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $goalpoint;
		}
		else if($many == "many")
		{
			return $goalpoints;
		}
	}//end db_select_goalpoint()

	//UPDATE GOALPOINT
	function db_update_goalpoint($set,$where)
	{
		db_update_table("goalpoint",$set,$where);
		
	}//end update goalpoint	
	
	//DELETE GOALPOINT	
	function db_delete_goalpoint($where)
	{
		db_delete_from_table("goalpoint",$where);
		
	}//end db_delete_goalpoint()	
	
	
	


//INSURANCE_CLAIM: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INSURANCE_CLAIM
	function db_insert_insurance_claim($insurance_claim)
	{
		db_insert_table("insurance_claim",$insurance_claim);
	
	}//END db_insert_insurance_claim	

	//SELECT INSURANCE_CLAIMS (many)
	function db_select_insurance_claims($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_insurance_claim($where,$order_by,$limit,"many");
		
	}//end db_select_insurance_claims() many	

	//SELECT INSURANCE_CLAIM (one)
	function db_select_insurance_claim($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." insurance_claim.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." insurance_claim.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." insurance_claim.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `insurance_claim`".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$insurance_claims = array();
		foreach ($query->result() as $row)
		{
			$insurance_claim['id'] = $row->id;
			$insurance_claim['ticket_id'] = $row->ticket_id;
			$insurance_claim['claim_number'] = $row->ticket_id;
			
			$insurance_claims[] = $insurance_claim;
			
		}// end foreach
		
		if (empty($insurance_claim))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $insurance_claim;
		}
		else if($many == "many")
		{
			return $insurance_claims;
		}
	}//end db_select_insurance_claim()

	//UPDATE INSURANCE_CLAIM
	function db_update_insurance_claim($set,$where)
	{
		db_update_table("insurance_claim",$set,$where);
		
	}//end update insurance_claim	
	
	//DELETE INSURANCE_CLAIM	
	function db_delete_insurance_claim($where)
	{
		db_delete_from_table("insurance_claim",$where);
		
	}//end db_delete_insurance_claim()	
	
	
	
	
//INS_CHANGE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INS_CHANGE
	function db_insert_ins_change($ins_change)
	{
		db_insert_table("ins_change",$ins_change);
	
	}//END db_insert_ins_change	

	//SELECT INS_CHANGES (many)
	function db_select_ins_changes($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_ins_change($where,$order_by,$limit,"many");
		
	}//end db_select_ins_changes() many	

	//SELECT INS_CHANGE (one)
	function db_select_ins_change($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ins_change.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." ins_change.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." ins_change.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `ins_change`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$ins_changes = array();
		foreach ($query->result() as $row)
		{
			$ins_change['id'] = $row->id;
			$ins_change['ins_policy_id'] = $row->ins_policy_id;
			$ins_change['change_date'] = $row->change_date;
			$ins_change['obj_changed'] = $row->obj_changed;
			$ins_change['obj_id'] = $row->obj_id;
			$ins_change['change_reason'] = $row->change_reason;
			$ins_change['change_desc'] = $row->change_desc;
			$ins_change['change_proof'] = $row->change_proof;
			$ins_change['proof_guid'] = $row->proof_guid;
			$ins_change['user_id'] = $row->user_id;
			
			$ins_changes[] = $ins_change;
			
		}// end foreach
		
		if (empty($ins_change))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $ins_change;
		}
		else if($many == "many")
		{
			return $ins_changes;
		}
	}//end db_select_ins_change()

	//UPDATE INS_CHANGE
	function db_update_ins_change($set,$where)
	{
		db_update_table("ins_change",$set,$where);
		
	}//end update ins_change	
	
	//DELETE INS_CHANGE	
	function db_delete_ins_change($where)
	{
		db_delete_from_table("ins_change",$where);
		
	}//end db_delete_ins_change()	
	
	
	
	
//INS_LISTED_DRIVER: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INS_LISTED_DRIVER
	function db_insert_ins_listed_driver($ins_listed_driver)
	{
		db_insert_table("ins_listed_driver",$ins_listed_driver);
	
	}//END db_insert_ins_listed_driver	

	//SELECT INS_LISTED_DRIVERS (many)
	function db_select_ins_listed_drivers($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_ins_listed_driver($where,$order_by,$limit,"many");
		
	}//end db_select_ins_listed_drivers() many	

	//SELECT INS_LISTED_DRIVER (one)
	function db_select_ins_listed_driver($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ins_listed_driver.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." ins_listed_driver.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." ins_listed_driver.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `ins_listed_driver`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$ins_listed_drivers = array();
		foreach ($query->result() as $row)
		{
			$ins_listed_driver['id'] = $row->id;
			$ins_listed_driver['client_id'] = $row->client_id;
			$ins_listed_driver['ins_profile_id'] = $row->ins_profile_id;
			
			$where = null;
			$where["id"] = $row->client_id;
			$ins_listed_driver['client'] = db_select_client($where);
			
			$ins_listed_drivers[] = $ins_listed_driver;
			
		}// end foreach
		
		if (empty($ins_listed_driver))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $ins_listed_driver;
		}
		else if($many == "many")
		{
			return $ins_listed_drivers;
		}
	}//end db_select_ins_listed_driver()

	//UPDATE INS_LISTED_DRIVER
	function db_update_ins_listed_driver($set,$where)
	{
		db_update_table("ins_listed_driver",$set,$where);
		
	}//end update ins_listed_driver	
	
	//DELETE INS_LISTED_DRIVER	
	function db_delete_ins_listed_driver($where)
	{
		db_delete_from_table("ins_listed_driver",$where);
		
	}//end db_delete_ins_listed_driver()	
	
	
	
	
	
	
//INS_PLAYER: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INS_PLAYER
	function db_insert_ins_player($ins_player)
	{
		db_insert_table("ins_player",$ins_player);
	
	}//END db_insert_ins_player	

	//SELECT INS_PLAYERS (many)
	function db_select_ins_players($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_ins_player($where,$order_by,$limit,"many");
		
	}//end db_select_ins_players() many	

	//SELECT INS_PLAYER (one)
	function db_select_ins_player($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ins_player.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." ins_player.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." ins_player.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `ins_player`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$ins_players = array();
		foreach ($query->result() as $row)
		{
			$ins_player['id'] = $row->id;
			$ins_player['ins_policy_id'] = $row->ins_policy_id;
			$ins_player['ins_profile_id'] = $row->ins_profile_id;
			$ins_player['ins_unit_coverage_id'] = $row->ins_unit_coverage_id;
			$ins_player['role'] = $row->role;
			$ins_player['name'] = $row->name;
			$ins_player['ssn'] = $row->ssn;
			$ins_player['address'] = $row->address;
			$ins_player['city'] = $row->city;
			$ins_player['state'] = $row->state;
			$ins_player['zip'] = $row->zip;
			
			$ins_players[] = $ins_player;
			
		}// end foreach
		
		if (empty($ins_player))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $ins_player;
		}
		else if($many == "many")
		{
			return $ins_players;
		}
	}//end db_select_ins_player()

	//UPDATE INS_PLAYER
	function db_update_ins_player($set,$where)
	{
		db_update_table("ins_player",$set,$where);
		
	}//end update ins_player	
	
	//DELETE INS_PLAYER	
	function db_delete_ins_player($where)
	{
		db_delete_from_table("ins_player",$where);
		
	}//end db_delete_ins_player()	
	
	
	
	
//INS_POLICY: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INS_POLICY
	function db_insert_ins_policy($ins_policy)
	{
		db_insert_table("ins_policy",$ins_policy);
	
	}//END db_insert_ins_policy	

	//SELECT INS_POLICYS (many)
	function db_select_ins_policys($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_ins_policy($where,$order_by,$limit,"many");
		
	}//end db_select_ins_policys() many	

	//SELECT INS_POLICY (one)
	function db_select_ins_policy($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ins_policy.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." ins_policy.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." ins_policy.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `ins_policy`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$ins_policys = array();
		foreach ($query->result() as $row)
		{
			$ins_policy['id'] = $row->id;
			$ins_policy['quoted_date'] = $row->quoted_date;
			$ins_policy['quoted_by_id'] = $row->quoted_by_id;
			$ins_policy['policy_active_date'] = $row->policy_active_date;
			$ins_policy['policy_cancelled_date'] = $row->policy_cancelled_date;
			$ins_policy['quote_status'] = $row->quote_status;
			$ins_policy['quote_code'] = $row->quote_code;
			$ins_policy['policy_number'] = $row->policy_number;
			$ins_policy['policy_log'] = $row->policy_log;
			
			$ins_policys[] = $ins_policy;
			
		}// end foreach
		
		if (empty($ins_policy))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $ins_policy;
		}
		else if($many == "many")
		{
			return $ins_policys;
		}
	}//end db_select_ins_policy()

	//UPDATE INS_POLICY
	function db_update_ins_policy($set,$where)
	{
		db_update_table("ins_policy",$set,$where);
		
	}//end update ins_policy	
	
	//DELETE INS_POLICY	
	function db_delete_ins_policy($where)
	{
		db_delete_from_table("ins_policy",$where);
		
	}//end db_delete_ins_policy()	
	

	
	
//INS_POLICY_PROFILE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INS_POLICY_PROFILE
	function db_insert_ins_policy_profile($ins_policy_profile)
	{
		db_insert_table("ins_policy_profile",$ins_policy_profile);
	
	}//END db_insert_ins_policy_profile	

	//SELECT INS_POLICY_PROFILES (many)
	function db_select_ins_policy_profiles($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_ins_policy_profile($where,$order_by,$limit,"many");
		
	}//end db_select_ins_policy_profiles() many	

	//SELECT INS_POLICY_PROFILE (one)
	function db_select_ins_policy_profile($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ins_policy_profile.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." ins_policy_profile.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." ins_policy_profile.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				 FROM ins_policy_profile ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$ins_policy_profiles = array();
		foreach ($query->result() as $row)
		{
			$ins_policy_profile['id'] = $row->id;
			$ins_policy_profile['ins_policy_id'] = $row->ins_policy_id;
			$ins_policy_profile['profile_current_since'] = $row->profile_current_since;
			$ins_policy_profile['profile_current_till'] = $row->profile_current_till;
			$ins_policy_profile['insurer_id'] = $row->insurer_id;
			$ins_policy_profile['agent_id'] = $row->agent_id;
			$ins_policy_profile['contact_notes'] = $row->contact_notes;
			$ins_policy_profile['insured_company_id'] = $row->insured_company_id;
			$ins_policy_profile['email'] = $row->email;
			$ins_policy_profile['phone'] = $row->phone;
			$ins_policy_profile['garaging_address'] = $row->garaging_address;
			$ins_policy_profile['garaging_city'] = $row->garaging_city;
			$ins_policy_profile['garaging_state'] = $row->garaging_state;
			$ins_policy_profile['garaging_zip'] = $row->garaging_zip;
			$ins_policy_profile['mailing_address'] = $row->mailing_address;
			$ins_policy_profile['mailing_city'] = $row->mailing_city;
			$ins_policy_profile['mailing_state'] = $row->mailing_state;
			$ins_policy_profile['mailing_zip'] = $row->mailing_zip;
			$ins_policy_profile['fg_id'] = $row->fg_id;
			$ins_policy_profile['cc_number'] = $row->cc_number;
			$ins_policy_profile['cc_exp'] = $row->cc_exp;
			$ins_policy_profile['cc_cvv'] = $row->cc_cvv;
			$ins_policy_profile['cc_address'] = $row->cc_address;
			$ins_policy_profile['cc_city'] = $row->cc_city;
			$ins_policy_profile['cc_state'] = $row->cc_state;
			$ins_policy_profile['cc_zip'] = $row->cc_zip;
			$ins_policy_profile['term'] = $row->term;
			$ins_policy_profile['expected_cancellation_date'] = $row->expected_cancellation_date;
			$ins_policy_profile['cargo_limit'] = $row->cargo_limit;
			$ins_policy_profile['cargo_ded'] = $row->cargo_ded;
			$ins_policy_profile['cargo_prem'] = $row->cargo_prem;
			$ins_policy_profile['rbd_limit'] = $row->rbd_limit;
			$ins_policy_profile['rbd_ded'] = $row->rbd_ded;
			$ins_policy_profile['rbd_prem'] = $row->rbd_prem;
			$ins_policy_profile['fees'] = $row->fees;
			$ins_policy_profile['total_cost'] = $row->total_cost;
			$ins_policy_profile['online_url'] = $row->online_url;
			$ins_policy_profile['online_username'] = $row->online_username;
			$ins_policy_profile['online_password'] = $row->online_password;
			
			$ins_policy_profiles[] = $ins_policy_profile;
			
		}// end foreach
		
		if (empty($ins_policy_profile))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $ins_policy_profile;
		}
		else if($many == "many")
		{
			return $ins_policy_profiles;
		}
	}//end db_select_ins_policy_profile()

	//UPDATE INS_POLICY_PROFILE
	function db_update_ins_policy_profile($set,$where)
	{
		db_update_table("ins_policy_profile",$set,$where);
		
	}//end update ins_policy_profile	
	
	//DELETE INS_POLICY_PROFILE	
	function db_delete_ins_policy_profile($where)
	{
		db_delete_from_table("ins_policy_profile",$where);
		
	}//end db_delete_ins_policy_profile()	
	
	
	
	
//INS_UNIT_COVERAGE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INS_UNIT_COVERAGE
	function db_insert_ins_unit_coverage($ins_unit_coverage)
	{
		db_insert_table("ins_unit_coverage",$ins_unit_coverage);
	
	}//END db_insert_ins_unit_coverage	

	//SELECT INS_UNIT_COVERAGES (many)
	function db_select_ins_unit_coverages($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_ins_unit_coverage($where,$order_by,$limit,"many");
		
	}//end db_select_ins_unit_coverages() many	

	//SELECT INS_UNIT_COVERAGE (one)
	function db_select_ins_unit_coverage($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ins_unit_coverage.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." ins_unit_coverage.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." ins_unit_coverage.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				 FROM `ins_unit_coverage`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$ins_unit_coverages = array();
		foreach ($query->result() as $row)
		{
			$ins_unit_coverage['id'] = $row->id;
			$ins_unit_coverage['ins_policy_id'] = $row->ins_policy_id;
			$ins_unit_coverage['ins_policy_profile_id'] = $row->ins_policy_profile_id;
			$ins_unit_coverage['coverage_current_since'] = $row->coverage_current_since;
			$ins_unit_coverage['reason_started'] = $row->reason_started;
			$ins_unit_coverage['coverage_current_till'] = $row->coverage_current_till;
			$ins_unit_coverage['reason_ended'] = $row->reason_ended;
			$ins_unit_coverage['unit_type'] = $row->unit_type;
			$ins_unit_coverage['unit_id'] = $row->unit_id;
			$ins_unit_coverage['radius'] = $row->radius;
			$ins_unit_coverage['al_prem'] = $row->al_prem;
			$ins_unit_coverage['al_um_bi_limit'] = $row->al_um_bi_limit;
			$ins_unit_coverage['al_um_bi_prem'] = $row->al_um_bi_prem;
			$ins_unit_coverage['al_uim_bi_limit'] = $row->al_uim_bi_limit;
			$ins_unit_coverage['al_uim_bi_prem'] = $row->al_uim_bi_prem;
			$ins_unit_coverage['al_pip_limit'] = $row->al_pip_limit;
			$ins_unit_coverage['al_pip_prem'] = $row->al_pip_prem;
			$ins_unit_coverage['pd_limit'] = $row->pd_limit;
			$ins_unit_coverage['pd_comp_ded'] = $row->pd_comp_ded;
			$ins_unit_coverage['pd_comp_prem'] = $row->pd_comp_prem;
			$ins_unit_coverage['pd_coll_ded'] = $row->pd_coll_ded;
			$ins_unit_coverage['pd_coll_prem'] = $row->pd_coll_prem;
			$ins_unit_coverage['pd_rental_daily_limit'] = $row->pd_rental_daily_limit;
			$ins_unit_coverage['pd_rental_max_limit'] = $row->pd_rental_max_limit;
			$ins_unit_coverage['pd_rental_prem'] = $row->pd_rental_prem;
			
			$ins_unit_coverages[] = $ins_unit_coverage;
			
		}// end foreach
		
		if (empty($ins_unit_coverage))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $ins_unit_coverage;
		}
		else if($many == "many")
		{
			return $ins_unit_coverages;
		}
	}//end db_select_ins_unit_coverage()

	//UPDATE INS_UNIT_COVERAGE
	function db_update_ins_unit_coverage($set,$where)
	{
		db_update_table("ins_unit_coverage",$set,$where);
		
	}//end update ins_unit_coverage	
	
	//DELETE INS_UNIT_COVERAGE	
	function db_delete_ins_unit_coverage($where)
	{
		db_delete_from_table("ins_unit_coverage",$where);
		
	}//end db_delete_ins_unit_coverage()	
	
	
	

	
//INVOICE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INVOICE
	function db_insert_invoice($invoice)
	{
		db_insert_table("invoice",$invoice);
	
	}//END db_insert_invoice	

	//SELECT INVOICES (many)
	function db_select_invoices($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_invoice($where,$order_by,$limit,"many");
		
	}//end db_select_invoices() many	

	//SELECT INVOICE (one)
	function db_select_invoice($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." invoice.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." invoice.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." invoice.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `invoice` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$invoices = array();
		foreach ($query->result() as $row)
		{
			$invoice['id'] = $row->id;
			$invoice['business_id'] = $row->business_id;
			$invoice['relationship_id'] = $row->relationship_id;
			$invoice['debit_account_id'] = $row->debit_account_id;
			$invoice['credit_account_id'] = $row->credit_account_id;
			$invoice['invoice_type'] = $row->invoice_type;
			$invoice['invoice_description'] = $row->invoice_description;
			$invoice['invoice_category'] = $row->invoice_category;
			$invoice['invoice_datetime'] = $row->invoice_datetime;
			$invoice['invoice_number'] = $row->invoice_number;
			$invoice['invoice_amount'] = $row->invoice_amount;
			$invoice['file_guid'] = $row->file_guid;
			$invoice['invoice_notes'] = $row->invoice_notes;
			$invoice['invoice_created_datetime'] = $row->invoice_created_datetime;
			$invoice['closed_datetime'] = $row->closed_datetime;
			$invoice['settlement_id'] = $row->settlement_id;
			
			//GET CUSTOMER_VENDOR
			
			
			//GET PAYMENT HISTORY
			
			
			//GET INVOICE BALANCE
			
			/**
			$codriver["client_nickname"] = $row->codriver_nickname;
			$invoice["codriver"] = $codriver;
			**/
			
			$invoices[] = $invoice;
			
		}// end foreach
		
		if (empty($invoice))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $invoice;
		}
		else if($many == "many")
		{
			return $invoices;
		}
	}//end db_select_invoice()

	//UPDATE INVOICE
	function db_update_invoice($set,$where)
	{
		db_update_table("invoice",$set,$where);
		
	}//end update invoice	
	
	//DELETE INVOICE	
	function db_delete_invoice($where)
	{
		db_delete_from_table("invoice",$where);
		
	}//end db_delete_invoice()	
	
	
	
	

//INVOICE_ALLOCATION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INVOICE_ALLOCATION
	function db_insert_invoice_allocation($invoice_allocation)
	{
		db_insert_table("invoice_allocation",$invoice_allocation);
	
	}//END db_insert_invoice_allocation	

	//SELECT INVOICE_ALLOCATIONS (many)
	function db_select_invoice_allocations($where,$order_by = 'id')
	{
		return db_select_tables("invoice_allocation",$where,$order_by);
		
	}//end db_select_invoice_allocations() many	

	//SELECT INVOICE_ALLOCATION (one)
	function db_select_invoice_allocation($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." invoice_allocation.".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." invoice_allocation.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = 	"SELECT
				invoice_allocation.id AS id,
				invoice_id,
				invoice_allocation.company_id AS invoice_allocation_company_id,
				account_id,
				expense_type,
				allocation_amount,
				allocation_notes,
				company_side_bar_name,
				account_name
				FROM `invoice_allocation`,`company`,`account`
				WHERE invoice_allocation.account_id  = account.id 
				AND invoice_allocation.company_id = company.id
				AND ".$where_sql;
		
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			$invoice_allocation['id'] = $row->id;
			$invoice_allocation['invoice_id'] = $row->invoice_id;
			$invoice_allocation['company_id'] = $row->invoice_allocation_company_id;
			$invoice_allocation['expense_type'] = $row->expense_type;
			$invoice_allocation['allocation_amount'] = $row->allocation_amount;
			$invoice_allocation['allocation_notes'] = $row->allocation_notes;
			
			$company["company_side_bar_name"] = $row->company_side_bar_name;
			$invoice_allocation["company"] = $company;
			
			$account["account_name"] = $row->account_name;
			$invoice_allocation["account"] = $account;
			
			
			
		}// end foreach
		
		if (empty($invoice_allocation))
		{
			return null;
		}else
			{
				return $invoice_allocation;
			}
	}//end db_select_invoice_allocation()

	//UPDATE INVOICE_ALLOCATION
	function db_update_invoice_allocation($set,$where)
	{
		db_update_table("invoice_allocation",$set,$where);
		
	}//end update invoice_allocation	
	
	//DELETE INVOICE_ALLOCATION	
	function db_delete_invoice_allocation($where)
	{
		db_delete_from_table("invoice_allocation",$where);
		
	}//end db_delete_invoice_allocation()	


	
//INVOICE_PAYMENT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT INVOICE_PAYMENT
	function db_insert_invoice_payment($invoice_payment)
	{
		db_insert_table("invoice_payment",$invoice_payment);
	
	}//END db_insert_invoice_payment	

	//SELECT INVOICE_PAYMENTS (many)
	function db_select_invoice_payments($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_invoice_payment($where,$order_by,$limit,"many");
		
	}//end db_select_invoice_payments() many	

	//SELECT INVOICE_PAYMENT (one)
	function db_select_invoice_payment($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." invoice_payment.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." invoice_payment.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." invoice_payment.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `invoice_payment`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$invoice_payments = array();
		foreach ($query->result() as $row)
		{
			$invoice_payment['id'] = $row->id;
			$invoice_payment['invoice_id'] = $row->invoice_id;
			$invoice_payment['account_entry_id'] = $row->account_entry_id;
			
			//GET INVOICE
			$where = null;
			$where["id"] = $row->invoice_id;
			$invoice = db_select_invoice($where);
			
			$invoice_payment["invoice"] = $invoice;
			
			//GET ACCOUNT ENTRY
			$where = null;
			$where["id"] = $row->account_entry_id;
			$account_entry = db_select_account_entry($where);
			
			$invoice_payment["account_entry"] = $account_entry;
			
			$invoice_payments[] = $invoice_payment;
			
		}// end foreach
		
		if (empty($invoice_payment))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $invoice_payment;
		}
		else if($many == "many")
		{
			return $invoice_payments;
		}
	}//end db_select_invoice_payment()

	//UPDATE INVOICE_PAYMENT
	function db_update_invoice_payment($set,$where)
	{
		db_update_table("invoice_payment",$set,$where);
		
	}//end update invoice_payment	
	
	//DELETE INVOICE_PAYMENT	
	function db_delete_invoice_payment($where)
	{
		db_delete_from_table("invoice_payment",$where);
		
	}//end db_delete_invoice_payment()	
		
	
	
	
//LEG: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT LEG
	function db_insert_leg($leg)
	{
		db_insert_table("leg",$leg);
	
	}//END db_insert_leg	

	//SELECT LEGS (many)
	function db_select_legs($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_leg($where,$order_by,$limit,"many");
		
	}//end db_select_legs() many	

	//SELECT LEG (one)
	function db_select_leg($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." leg.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." leg.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." leg.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				leg.id as id,
				leg.log_entry_id as log_entry_id,
				leg.load_id as load_id,
				`load`.customer_load_number,
				`load`.natl_fuel_avg,
				leg.allocated_load_id,
				allocated_load.customer_load_number AS allocated_load_number,
				allocated_load.natl_fuel_avg AS allocated_load_natl_fuel_avg,
				leg.fm_id as fm_id,
				fm_company.company_side_bar_name as fm_company_name,
				leg.carrier_id as carrier_id,
				carrier.company_side_bar_name as carrier_name,
				leg.truck_id as truck_id ,
				truck.truck_number,
				leg.trailer_id as trailer_id ,
				trailer.trailer_number,
				leg.main_driver_id as main_driver_id ,
				main_driver.client_nickname as main_driver_nickname ,
				main_driver.profit_split as main_driver_default_split ,
				leg.codriver_id as codriver_id ,
				codriver.client_nickname as codriver_nickname ,
				codriver.profit_split as codriver_default_split ,
				leg.rate_type,
				leg.revenue_rate,
				leg.odometer_miles,
				leg.map_miles,
				leg.hours,
				leg.fuel_expense,
				leg.reefer_fuel_expense,
				leg.truck_rental_expense,
				leg.truck_mileage_expense,
				leg.trailer_rental_expense,
				leg.trailer_mileage_expense,
				leg.insurance_expense,
				leg.factoring_expense,
				leg.bad_debt_expense,
				leg.damage_expense,
				leg.gallons_used,
				leg.reefer_gallons_used,
				leg.main_driver_split,
				leg.codriver_split,
				leg.notes,
				leg.approved_by_id as approved_by_id,
				person.f_name as f_name ,
				leg.approved_datetime,
				log_entry.locked_datetime as locked_datetime
				FROM `leg`
				LEFT JOIN log_entry ON leg.log_entry_id = log_entry.id
				LEFT JOIN company AS fm_company ON leg.fm_id = fm_company.id
				LEFT JOIN company AS carrier ON leg.carrier_id = carrier.id
				LEFT JOIN person ON leg.approved_by_id = person.id 
				LEFT JOIN  `load` ON  leg.`load_id` =  `load`.id
				LEFT JOIN  `load` AS allocated_load ON leg.allocated_load_id =  allocated_load.id
				LEFT JOIN truck ON leg.truck_id = truck.id 
				LEFT JOIN trailer ON leg.trailer_id = trailer.id 
				LEFT JOIN client as main_driver ON leg.main_driver_id = main_driver.id 
				LEFT JOIN client as codriver ON leg.codriver_id = codriver.id ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$legs = array();
		foreach ($query->result() as $row)
		{
			$leg['id'] = $row->id;
			$leg['log_entry_id'] = $row->log_entry_id;
			$leg['load_id'] = $row->load_id;
			$leg['allocated_load_id'] = $row->allocated_load_id;
			$leg['fm_id'] = $row->fm_id;
			$leg['carrier_id'] = $row->carrier_id;
			$leg['truck_id'] = $row->truck_id;
			$leg['trailer_id'] = $row->trailer_id;
			$leg['main_driver_id'] = $row->main_driver_id;
			$leg['codriver_id'] = $row->codriver_id;
			$leg['natl_fuel_avg'] = $row->allocated_load_natl_fuel_avg;
			$leg['rate_type'] = $row->rate_type;
			$leg['revenue_rate'] = $row->revenue_rate;
			$leg['odometer_miles'] = $row->odometer_miles;
			$leg['map_miles'] = $row->map_miles;
			$leg['hours'] = $row->hours;
			$leg['fuel_expense'] = $row->fuel_expense;
			$leg['reefer_fuel_expense'] = $row->reefer_fuel_expense;
			$leg['truck_rental_expense'] = $row->truck_rental_expense;
			$leg['truck_mileage_expense'] = $row->truck_mileage_expense;
			$leg['trailer_rental_expense'] = $row->trailer_rental_expense;
			$leg['trailer_mileage_expense'] = $row->trailer_mileage_expense;
			$leg['insurance_expense'] = $row->insurance_expense;
			$leg['factoring_expense'] = $row->factoring_expense;
			$leg['bad_debt_expense'] = $row->bad_debt_expense;
			$leg['damage_expense'] = $row->damage_expense;
			$leg['gallons_used'] = $row->gallons_used;
			$leg['reefer_gallons_used'] = $row->reefer_gallons_used;
			$leg['main_driver_split'] = $row->main_driver_split;
			$leg['codriver_split'] = $row->codriver_split;
			$leg['notes'] = $row->notes;
			$leg['approved_by_id'] = $row->approved_by_id;
			$leg['approved_datetime'] = $row->approved_datetime;
			
			$approved_by["f_name"] = $row->f_name;
			$leg["approved_by"] = $approved_by;
			
			$load["customer_load_number"] = $row->customer_load_number;
			$load["natl_fuel_avg"] = $row->natl_fuel_avg;
			$leg["load"] = $load;
			
			$fleet_manager["company_side_bar_name"] = $row->fm_company_name;
			$leg["fleet_manager"] = $fleet_manager;
			
			$carrier["company_side_bar_name"] = $row->carrier_name;
			$leg["carrier"] = $carrier;
			
			$allocated_load["customer_load_number"] = $row->allocated_load_number;
			$allocated_load["natl_fuel_avg"] = $row->allocated_load_natl_fuel_avg;
			$leg["allocated_load"] = $allocated_load;
			
			$truck["truck_number"] = $row->truck_number;
			$leg["truck"] = $truck;
			
			$trailer["trailer_number"] = $row->trailer_number;
			$leg["trailer"] = $trailer;
			
			$main_driver["client_nickname"] = $row->main_driver_nickname;
			$main_driver["profit_split"] = $row->main_driver_default_split;
			$leg["main_driver"] = $main_driver;
			
			$codriver["client_nickname"] = $row->codriver_nickname;
			$codriver["profit_split"] = $row->codriver_default_split;
			$leg["codriver"] = $codriver;
			
			$log_entry["locked_datetime"] = $row->locked_datetime;
			$leg["log_entry"] = $log_entry;
			
			$legs[] = $leg;
			
		}// end foreach
		
		if (empty($leg))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $leg;
		}
		else if($many == "many")
		{
			return $legs;
		}
	}//end db_select_leg()

	//UPDATE LEG
	function db_update_leg($set,$where)
	{
		db_update_table("leg",$set,$where);
		
	}//end update leg	
	
	//DELETE LEG	
	function db_delete_leg($where)
	{
		db_delete_from_table("leg",$where);
		
	}//end db_delete_leg()	

	
	
	
//LOAD: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT LOAD
	function db_insert_load($load)
	{
		db_insert_table("load",$load);
	
	}//END db_insert_load	

	//SELECT LOADS (many)
	function db_select_loads($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_load($where,$order_by,$limit,"many");
		
	}//end db_select_loads() many	

	//SELECT LOAD (one)
	function db_select_load($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND ";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." load.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM `load` WHERE ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		//echo $sql;
		$query_trips = $CI->db->query($sql,$values);
		$loads = array();
		foreach ($query_trips->result() as $row)
		{
			$load['id'] = $row->id;
			$load['fleet_manager_id'] = $row->fleet_manager_id;
			$load['dm_id'] = $row->dm_id;
			$load['broker_id'] = $row->broker_id;
			$load['client_id'] = $row->client_id;
			$load['driver2_id'] = $row->driver2_id;
			$load['ar_specialist_id'] = $row->ar_specialist_id;
			$load['load_type'] = $row->load_type;
			$load['customer_load_number'] = $row->customer_load_number;
			$load['internal_load_number'] = $row->internal_load_number;
			$load['is_reefer'] = $row->is_reefer;
			$load['reefer_low_set'] = $row->reefer_low_set;
			$load['reefer_high_set'] = $row->reefer_high_set;
			$load['load_truck_id'] = $row->load_truck_id;
			$load['load_trailer_id'] = $row->load_trailer_id;
			$load['dispatch_sent_datetime'] = $row->dispatch_sent_datetime;
			$load['envelope_pic_datetime'] = $row->envelope_pic_datetime;
			$load['envelope_pic_guid'] = $row->envelope_pic_guid;
			$load['dropbox_pic_datetime'] = $row->dropbox_pic_datetime;
			$load['dropbox_pic_guid'] = $row->dropbox_pic_guid;
			$load['status_number'] = $row->status_number;
			$load['status'] = $row->status;
			$load['contact_info'] = $row->contact_info;
			$load['expected_miles'] = $row->expected_miles;
			$load['expected_revenue'] = $row->expected_revenue;
			$load['map_miles'] = $row->map_miles;
			$load['odometer_miles'] = $row->odometer_miles;
			$load['dead_head_miles'] = $row->dead_head_miles;
			$load['natl_fuel_avg'] = $row->natl_fuel_avg;
			$load['mpg'] = $row->mpg;
			$load['gallons_used'] = $row->gallons_used;
			$load['total_hours'] = $row->total_hours;
			$load['performance_rating'] = $row->performance_rating;
			$load['performance_bonus'] = $row->performance_bonus;
			$load['carrier_profit'] = $row->carrier_profit;
			$load['carrier_revenue'] = $row->carrier_revenue;
			$load['booking_datetime'] = $row->booking_datetime;
			$load['rcr_datetime'] = $row->rcr_datetime;
			$load['ready_for_dispatch_datetime'] = $row->ready_for_dispatch_datetime;
			$load['initial_dispatch_datetime'] = $row->initial_dispatch_datetime;
			$load['signed_load_plan_guid'] = $row->signed_load_plan_guid;
			$load['first_pick_datetime'] = $row->first_pick_datetime;
			$load['final_drop_datetime'] = $row->final_drop_datetime;
			$load['pushed_datetime'] = $row->pushed_datetime;
			$load['digital_received_datetime'] = $row->digital_received_datetime;
			$load['hc_processed_datetime'] = $row->hc_processed_datetime;
			$load['hc_guid'] = $row->hc_guid;
			$load['hc_sent_datetime'] = $row->hc_sent_datetime;
			$load['hc_sent_proof_guid'] = $row->hc_sent_proof_guid;
			$load['hc_received_datetime'] = $row->hc_received_datetime;
			$load['hc_received_proof_guid'] = $row->hc_received_proof_guid;
			$load['billing_status'] = $row->billing_status;
			$load['billing_status_number'] = $row->billing_status_number;
			$load['billed_under'] = $row->billed_under;
			$load['billing_method'] = $row->billing_method;
			$load['originals_required'] = $row->originals_required;
			$load['billing_datetime'] = $row->billing_datetime;
			$load['amount_billed'] = $row->amount_billed;
			$load['amount_funded'] = $row->amount_funded;
			$load['funded_datetime'] = $row->funded_datetime;
			$load['invoice_number'] = $row->invoice_number;
			$load['invoice_closed_datetime'] = $row->invoice_closed_datetime;
			$load['fm_approved_by'] = $row->fm_approved_by;
			$load['fm_approved_datetime'] = $row->fm_approved_datetime;
			$load['approved_by'] = $row->approved_by;
			$load['approved_datetime'] = $row->approved_datetime;
			$load['commission_approved_by'] = $row->commission_approved_by;
			$load['commission_approved_datetime'] = $row->commission_approved_datetime;
			$load['financing_cost'] = $row->financing_cost;
			$load['amount_short_paid'] = $row->amount_short_paid;
			$load['fm_split'] = $row->fm_split;
			$load['load_notes'] = $row->load_notes;
			$load['load_desc'] = $row->load_desc;
			$load['billing_notes'] = $row->billing_notes;
			$load['settlement_notes'] = $row->settlement_notes;
			$load['commission_notes'] = $row->commission_notes;
			$load['rc_link'] = $row->rc_link;
			$load['unsigned_bol_guid'] = $row->unsigned_bol_guid;
			$load['bol_link'] = $row->bol_link;
			$load['load_offer_guid'] = $row->load_offer_guid;
			$load['no_originals_proof_guid'] = $row->no_originals_proof_guid;
			$load['has_lumper'] = $row->has_lumper;
			$load['recoursed_datetime'] = $row->recoursed_datetime;
			$load['reimbursed_datetime'] = $row->reimbursed_datetime;
			$load['denied_datetime'] = $row->denied_datetime;
			$load['denied_reason'] = $row->denied_reason;
			$load['expected_pay_datetime'] = $row->expected_pay_datetime;
			$load['process_audit'] = $row->process_audit;
			$load['short_pay_report_guid'] = $row->short_pay_report_guid;
			
			$pd_where["load_id"] = $row->id;
			$load["load_picks"] = db_select_picks($pd_where);
			$load["load_drops"] = db_select_drops($pd_where);
			
			$broker_where["id"] = $load["broker_id"];
			$load["broker"] = db_select_customer($broker_where);
			
			$person_where["id"] = $load["dm_id"];
			$load["driver_manager"] = db_select_person($person_where);
			
			$person_where["id"] = $load["fleet_manager_id"];
			$load["fleet_manager"] = db_select_person($person_where);
			
			$client_where["id"] = $load["client_id"];
			$load["client"] = db_select_client($client_where);
			
			$billed_under_client_where["id"] = $load["billed_under"];
			$load["billed_under_client"] = db_select_client($billed_under_client_where);
			
			$billed_under_carrier_where["id"] = $load["billed_under"];
			$load["billed_under_carrier"] = db_select_company($billed_under_carrier_where);
			
			$fm_approved_by_where["id"] = $load["fm_approved_by"];
			$load["approved_by_fm"] = db_select_person($fm_approved_by_where);
			
			$approved_by_where["id"] = $load["approved_by"];
			$load["approved_by_person"] = db_select_person($approved_by_where);
			
			$commission_approved_by_where["id"] = $load["commission_approved_by"];
			$load["commission_approved_by_person"] = db_select_person($commission_approved_by_where);
			
			//GET TOTAL AMOUNT TO BE BILLED
			$where = null;
			$where["load_id"] = $row->id;
			$where["is_billable"] = "Yes";
			$billable_load_expenses = db_select_load_expenses($where);
			$total_amount = $row->expected_revenue;
			if(!empty($billable_load_expenses))
			{
				foreach($billable_load_expenses as $expense)
				{
					$total_amount = $total_amount + $expense["expense_amount"];
				}
				$load["amount_to_bill"] = $total_amount;
			}
			else
			{
				$load["amount_to_bill"] = $total_amount;
			}
			
			$loads[] = $load;
			
		}// end foreach
		
		if($many == 'one')
		{
			if (empty($load))
			{
				return null;
			}
			else
			{
				return $load;
			}
		}
		else if($many == "many")
		{
			return $loads;
		}
		
	}//end db_select_load()

	//UPDATE LOAD
	function db_update_load($set,$where)
	{
		db_update_table("load",$set,$where);
		
	}//end update load	
	
	//DELETE LOAD	
	function db_delete_load($where)
	{
		db_delete_from_table("load",$where);
		
	}//end db_delete_load()	
	

	
	
//LOAD_AUDIT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT LOAD_AUDIT
	function db_insert_load_audit($load_audit)
	{
		db_insert_table("load_audit",$load_audit);
	
	}//END db_insert_load_audit	

	//SELECT LOAD_AUDITS (many)
	function db_select_load_audits($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_load_audit($where,$order_by,$limit,"many");
		
	}//end db_select_load_audits() many	

	//SELECT LOAD_AUDIT (one)
	function db_select_load_audit($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." load_audit.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." load_audit.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." load_audit.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `load_audit`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$load_audits = array();
		foreach ($query->result() as $row)
		{
			$load_audit['id'] = $row->id;
			$load_audit['loads_audited'] = $row->loads_audited;
			$load_audit['loads_passed'] = $row->loads_passed;
			$load_audit['audit_text'] = $row->audit_text;
			$load_audit['pass_fail'] = $row->pass_fail;
			$load_audit['dispatcher_id'] = $row->dispatcher_id;
			$load_audit['audit_datetime'] = $row->audit_datetime;
			
		}// end foreach
		
		if (empty($load_audit))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $load_audit;
		}
		else if($many == "many")
		{
			return $load_audits;
		}
	}//end db_select_load_audit()

	//UPDATE LOAD_AUDIT
	function db_update_load_audit($set,$where)
	{
		db_update_table("load_audit",$set,$where);
		
	}//end update load_audit	
	
	//DELETE LOAD_AUDIT	
	function db_delete_load_audit($where)
	{
		db_delete_from_table("load_audit",$where);
		
	}//end db_delete_load_audit()		




//LOAD_CHECK_CALL: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT LOAD_CHECK_CALL
	function db_insert_load_check_call($load_check_call)
	{
		db_insert_table("load_check_call",$load_check_call);
	
	}//END db_insert_load_check_call	

	//SELECT LOAD_CHECK_CALLS (many)
	function db_select_load_check_calls($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_load_check_call($where,$order_by,$limit,"many");
		
	}//end db_select_load_check_calls() many	

	//SELECT LOAD_CHECK_CALL (one)
	function db_select_load_check_call($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." load_check_call.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." load_check_call.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." load_check_call.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `load_check_call`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$load_check_calls = array();
		foreach ($query->result() as $row)
		{
			$load_check_call['id'] = $row->id;
			$load_check_call['load_id'] = $row->load_id;
			$load_check_call['truck_id'] = $row->truck_id;
			$load_check_call['trailer_id'] = $row->trailer_id;
			$load_check_call['driver_id'] = $row->driver_id;
			$load_check_call['user_id'] = $row->user_id;
			$load_check_call['truck_fuel_level'] = $row->truck_fuel_level;
			$load_check_call['truck_code_status'] = $row->truck_code_status;
			$load_check_call['truck_code_guid'] = $row->truck_code_guid;
			$load_check_call['location'] = $row->location;
			$load_check_call['gps'] = $row->gps;
			$load_check_call['on_hold'] = $row->on_hold;
			$load_check_call['audio_guid'] = $row->audio_guid;
			$load_check_call['recorded_datetime'] = $row->recorded_datetime;
			$load_check_call['driver_answered'] = $row->driver_answered;
			
			$load_check_calls[] = $load_check_call;
			
		}// end foreach
		
		if (empty($load_check_call))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $load_check_call;
		}
		else if($many == "many")
		{
			return $load_check_calls;
		}
	}//end db_select_load_check_call()

	//UPDATE LOAD_CHECK_CALL
	function db_update_load_check_call($set,$where)
	{
		db_update_table("load_check_call",$set,$where);
		
	}//end update load_check_call	
	
	//DELETE LOAD_CHECK_CALL	
	function db_delete_load_check_call($where)
	{
		db_delete_from_table("load_check_call",$where);
		
	}//end db_delete_load_check_call()	
	
	
	
	
	
	
	
//LOAD_PROCESS_AUDIT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT LOAD_PROCESS_AUDIT
	function db_insert_load_process_audit($load_process_audit)
	{
		db_insert_table("load_process_audit",$load_process_audit);
	
	}//END db_insert_load_process_audit	

	//SELECT LOAD_PROCESS_AUDITS (many)
	function db_select_load_process_audits($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_load_process_audit($where,$order_by,$limit,"many");
		
	}//end db_select_load_process_audits() many	

	//SELECT LOAD_PROCESS_AUDIT (one)
	function db_select_load_process_audit($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." load_process_audit.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." load_process_audit.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." load_process_audit.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `load_process_audit`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$load_process_audits = array();
		foreach ($query->result() as $row)
		{
			$load_process_audit['id'] = $row->id;
			$load_process_audit['load_id'] = $row->load_id;
			$load_process_audit['user_id'] = $row->user_id;
			$load_process_audit['audit_datetime'] = $row->audit_datetime;
			$load_process_audit['defer_to_tarriff'] = $row->defer_to_tarriff;
			$load_process_audit['ontime_by_rc'] = $row->ontime_by_rc;
			$load_process_audit['shipper_load_and_count'] = $row->shipper_load_and_count;
			$load_process_audit['seal_pic_depart'] = $row->seal_pic_depart;
			$load_process_audit['load_pic_depart'] = $row->load_pic_depart;
			$load_process_audit['seal_number'] = $row->seal_number;
			$load_process_audit['seal_pic_arrive'] = $row->seal_pic_arrive;
			$load_process_audit['load_pic_arrive'] = $row->load_pic_arrive;
			$load_process_audit['seal_intact'] = $row->seal_intact;
			$load_process_audit['clean_bills'] = $row->clean_bills;
			
			$load_process_audits[] = $load_process_audit;
			
		}// end foreach
		
		if (empty($load_process_audit))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $load_process_audit;
		}
		else if($many == "many")
		{
			return $load_process_audits;
		}
	}//end db_select_load_process_audit()

	//UPDATE LOAD_PROCESS_AUDIT
	function db_update_load_process_audit($set,$where)
	{
		db_update_table("load_process_audit",$set,$where);
		
	}//end update load_process_audit	
	
	//DELETE LOAD_PROCESS_AUDIT	
	function db_delete_load_process_audit($where)
	{
		db_delete_from_table("load_process_audit",$where);
		
	}//end db_delete_load_process_audit()	
	
	
	

	
//LOAD_EXPENSE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT LOAD_EXPENSE
	function db_insert_load_expense($load_expense)
	{
		db_insert_table("load_expense",$load_expense);
	
	}//END db_insert_load_expense	

	//SELECT LOAD_EXPENSES (many)
	function db_select_load_expenses($where,$order_by = 'id')
	{
		return db_select_tables("load_expense",$where,$order_by);
		
	}//end db_select_load_expenses() many	

	//SELECT LOAD_EXPENSE (one)
	function db_select_load_expense($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `load_expense` WHERE ".$where_sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			$load_expense['id'] = $row->id;
			$load_expense['load_id'] = $row->load_id;
			$load_expense['expense_amount'] = $row->expense_amount;
			$load_expense['explanation'] = $row->explanation;
			$load_expense['is_billable'] = $row->is_billable;
			$load_expense['link'] = $row->link;
			$load_expense['receipt_datetime'] = $row->receipt_datetime;
			$load_expense['file_guid'] = $row->file_guid;
			
		}// end foreach
		
		if (empty($load_expense))
		{
			return null;
		}else
			{
				return $load_expense;
			}
	}//end db_select_load_expense()

	//UPDATE LOAD_EXPENSE
	function db_update_load_expense($set,$where)
	{
		db_update_table("load_expense",$set,$where);
		
	}//end update load_expense	
	
	//DELETE LOAD_EXPENSE	
	function db_delete_load_expense($where)
	{
		db_delete_from_table("load_expense",$where);
		
	}//end db_delete_load_expense()	


	

//LOG_ENTRY: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT LOG_ENTRY
	function db_insert_log_entry($log_entry)
	{
		db_insert_table("log_entry",$log_entry);
	
	}//END db_insert_log_entry	

	//SELECT LOG_ENTRYS (many)
	function db_select_log_entrys($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_log_entry($where,$order_by,$limit,"many");
		
	}//end db_select_log_entrys() many	

	//SELECT LOG_ENTRY (one)
	function db_select_log_entry($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." log_entry.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." log_entry.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." log_entry.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		//echo $where_sql;
		$sql = "SELECT 
				log_entry.id as id,
				log_entry.recorder_id as recorder_id,
				person.f_name as f_name ,
				log_entry.load_id as load_id,
				this_load.customer_load_number as load_number,
				log_entry.allocated_load_id as allocated_load_id,
				allocated_load.customer_load_number as allocated_load_number,
				log_entry.truck_id as truck_id ,
				truck.truck_number,
				log_entry.trailer_id as trailer_id ,
				trailer.trailer_number,
				miles_type,
				log_entry.main_driver_id as main_driver_id ,
				main_driver.client_nickname as main_driver_nickname ,
				log_entry.codriver_id as codriver_id ,
				codriver.client_nickname as codriver_nickname ,
				sync_entry_id,
				entry_type,
				entry_datetime,
				city,
				state,
				address,
				gps_coordinates,
				odometer,
				route,
				miles,
				out_of_route,
				log_entry.mpg AS entry_mpg,
				entry_notes,
				locked_datetime,
				recorded_datetime
				FROM `log_entry`
				LEFT JOIN person ON log_entry.recorder_id = person.id 
				LEFT JOIN  `load` AS this_load ON log_entry.`load_id` =  this_load.id
				LEFT JOIN  `load` AS allocated_load ON allocated_load_id =  allocated_load.id
				LEFT JOIN truck ON log_entry.truck_id = truck.id 
				LEFT JOIN trailer ON log_entry.trailer_id = trailer.id 
				LEFT JOIN client AS main_driver ON log_entry.main_driver_id = main_driver.id 
				LEFT JOIN client AS codriver ON log_entry.codriver_id = codriver.id ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$log_entrys = array();
		foreach ($query->result() as $row)
		{
			$log_entry['id'] = $row->id;
			$log_entry['load_id'] = $row->load_id;
			$log_entry['allocated_load_id'] = $row->allocated_load_id;
			$log_entry['truck_id'] = $row->truck_id;
			$log_entry['trailer_id'] = $row->trailer_id;
			$log_entry['miles_type'] = $row->miles_type;
			$log_entry['main_driver_id'] = $row->main_driver_id;
			$log_entry['codriver_id'] = $row->codriver_id;
			$log_entry['sync_entry_id'] = $row->sync_entry_id;
			$log_entry['entry_type'] = $row->entry_type;
			$log_entry['entry_datetime'] = $row->entry_datetime;
			$log_entry['city'] = $row->city;
			$log_entry['state'] = $row->state;
			$log_entry['address'] = $row->address;
			$log_entry['gps_coordinates'] = $row->gps_coordinates;
			$log_entry['odometer'] = $row->odometer;
			$log_entry['route'] = $row->route;
			$log_entry['miles'] = $row->miles;
			$log_entry['out_of_route'] = $row->out_of_route;
			$log_entry['mpg'] = $row->entry_mpg;
			$log_entry['entry_notes'] = $row->entry_notes;
			$log_entry['locked_datetime'] = $row->locked_datetime;
			$log_entry['recorded_datetime'] = $row->recorded_datetime;
			
			$recorder["f_name"] = $row->f_name;
			$log_entry["recorder"] = $recorder;
			
			$load["customer_load_number"] = $row->load_number;
			$log_entry["load"] = $load;
			
			$allocated_load["customer_load_number"] = $row->allocated_load_number;
			$log_entry["alocated_load"] = $allocated_load;
			
			$truck["truck_number"] = $row->truck_number;
			$log_entry["truck"] = $truck;
			
			$trailer["trailer_number"] = $row->trailer_number;
			$log_entry["trailer"] = $trailer;
			
			$main_driver["client_nickname"] = $row->main_driver_nickname;
			$log_entry["main_driver"] = $main_driver;
			
			$codriver["client_nickname"] = $row->codriver_nickname;
			$log_entry["codriver"] = $codriver;
			
			$log_entrys[] = $log_entry;
			
		}// end foreach
		
		if (empty($log_entry))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $log_entry;
		}
		else if($many == "many")
		{
			return $log_entrys;
		}
	}//end db_select_log_entry()

	//UPDATE LOG_ENTRY
	function db_update_log_entry($set,$where)
	{
		db_update_table("log_entry",$set,$where);
		
	}//end update log_entry	
	
	//DELETE LOG_ENTRY	
	function db_delete_log_entry($where)
	{
		db_delete_from_table("log_entry",$where);
		
	}//end db_delete_log_entry()	
	

	
	
	
//NOTE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT NOTE
	function db_insert_note($note)
	{
		db_insert_table("note",$note);
	
	}//END db_insert_note	

	//SELECT NOTES (many)
	function db_select_notes($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_note($where,$order_by,$limit,"many");
		
	}//end db_select_notes() many	

	//SELECT NOTE (one)
	function db_select_note($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." note.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." note.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." note.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `note`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$notes = array();
		foreach ($query->result() as $row)
		{
			$note['id'] = $row->id;
			$note['note_type'] = $row->note_type;
			$note['note_for_id'] = $row->note_for_id;
			$note['note_datetime'] = $row->note_datetime;
			$note['user_id'] = $row->user_id;
			$note['note_text'] = $row->note_text;
			
			$notes[] = $note;
			
		}// end foreach
		
		if (empty($note))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $note;
		}
		else if($many == "many")
		{
			return $notes;
		}
	}//end db_select_note()

	//UPDATE NOTE
	function db_update_note($set,$where)
	{
		db_update_table("note",$set,$where);
		
	}//end update note	
	
	//DELETE NOTE	
	function db_delete_note($where)
	{
		db_delete_from_table("note",$where);
		
	}//end db_delete_note()	
	
	
	
	
	
//NOTIFICATION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT NOTIFICATION
	function db_insert_notification($notification)
	{
		db_insert_table("notification",$notification);
	
	}//END db_insert_notification	

	//SELECT NOTIFICATIONS (many)
	function db_select_notifications($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_notification($where,$order_by,$limit,"many");
		
	}//end db_select_notifications() many	

	//SELECT NOTIFICATION (one)
	function db_select_notification($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." notification.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." notification.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." notification.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `notification`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$notifications = array();
		foreach ($query->result() as $row)
		{
			$notification['id'] = $row->id;
			$notification['category'] = $row->category;
			$notification['title'] = $row->title;
			$notification['text'] = $row->text;
			$notification['image_path'] = $row->image_path;
			$notification['generated_datetime'] = $row->generated_datetime;
			$notification['displayed_datetime'] = $row->displayed_datetime;
			$notification['clicked_datetime'] = $row->clicked_datetime;
			$notification['responded_datetime'] = $row->responded_datetime;
			
			$notifications[] = $notification;
			
		}// end foreach
		
		if (empty($notification))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $notification;
		}
		else if($many == "many")
		{
			return $notifications;
		}
	}//end db_select_notification()

	//UPDATE NOTIFICATION
	function db_update_notification($set,$where)
	{
		db_update_table("notification",$set,$where);
		
	}//end update notification	
	
	//DELETE NOTIFICATION	
	function db_delete_notification($where)
	{
		db_delete_from_table("notification",$where);
		
	}//end db_delete_notification()
	
	
	
	

//PERFORMANCE_REVIEW: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT PERFORMANCE_REVIEW
	function db_insert_performance_review($performance_review)
	{
		db_insert_table("performance_review",$performance_review);
	
	}//END db_insert_performance_review	

	//SELECT PERFORMANCE_REVIEWS (many)
	function db_select_performance_reviews($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_performance_review($where,$order_by,$limit,"many");
		
	}//end db_select_performance_reviews() many	

	//SELECT PERFORMANCE_REVIEW (one)
	function db_select_performance_review($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." performance_review.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." performance_review.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." performance_review.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				performance_review.id as id,
				performance_review.truck_id as truck_id,
				truck.truck_number as truck_number,
				performance_review.fm_id as fm_id,
				performance_review.dm_id as dm_id,
				fm.f_name as fm_f_name,
				fm.l_name as fm_l_name,
				dm.f_name as dm_f_name,
				dm.l_name as dm_l_name,
				performance_review.end_week_id as end_week_id,
				performance_review.solo_or_team as solo_or_team,
				log_entry.entry_datetime as entry_datetime,
				performance_review.hours as hours,
				performance_review.map_miles as map_miles,
				performance_review.odometer_miles as odometer_miles,
				performance_review.mpg as mpg,
				performance_review.total_revenue as total_revenue,
				performance_review.standard_expenses as standard_expenses,
				performance_review.carrier_revenue as carrier_revenue,
				performance_review.total_bobtail_miles as total_bobtail_miles,
				performance_review.total_deadhead_miles as total_deadhead_miles,
				performance_review.total_light_miles as total_light_miles,
				performance_review.total_loaded_miles as total_loaded_miles,
				performance_review.total_reefer_miles as total_reefer_miles,
				performance_review.total_fuel_expense as total_fuel_expense,
				performance_review.total_reefer_fuel_expense as total_reefer_fuel_expense,
				performance_review.truck_gallons as truck_gallons,
				performance_review.oor_percentage as oor_percentage,
				performance_review.booking_rate as booking_rate,
				performance_review.driver_rate as driver_rate,
				performance_review.driver_profit as driver_profit,
				performance_review.raw_profit as raw_profit,
				performance_review.start_datetime as start_datetime,
				performance_review.end_datetime as end_datetime,
				performance_review.saved_datetime as saved_datetime
				FROM `performance_review`
				LEFT JOIN truck ON performance_review.truck_id = truck.id 
				LEFT JOIN person as fm ON performance_review.fm_id = fm.id 
				LEFT JOIN person as dm ON performance_review.dm_id = dm.id 
				LEFT JOIN log_entry ON performance_review.end_week_id = log_entry.id ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$performance_reviews = array();
		foreach ($query->result() as $row)
		{
			$performance_review['id'] = $row->id;
			$performance_review['truck_id'] = $row->truck_id;
			$performance_review['fm_id'] = $row->fm_id;
			$performance_review['dm_id'] = $row->dm_id;
			$performance_review['end_week_id'] = $row->end_week_id;
			$performance_review['solo_or_team'] = $row->solo_or_team;
			$performance_review['hours'] = $row->hours;
			$performance_review['map_miles'] = $row->map_miles;
			$performance_review['odometer_miles'] = $row->odometer_miles;
			$performance_review['mpg'] = $row->mpg;
			$performance_review['total_revenue'] = $row->total_revenue;
			$performance_review['standard_expenses'] = $row->standard_expenses;
			$performance_review['carrier_revenue'] = $row->carrier_revenue;
			$performance_review['total_bobtail_miles'] = $row->total_bobtail_miles;
			$performance_review['total_deadhead_miles'] = $row->total_deadhead_miles;
			$performance_review['total_light_miles'] = $row->total_light_miles;
			$performance_review['total_loaded_miles'] = $row->total_loaded_miles;
			$performance_review['total_reefer_miles'] = $row->total_reefer_miles;
			$performance_review['total_fuel_expense'] = $row->total_fuel_expense;
			$performance_review['total_reefer_fuel_expense'] = $row->total_reefer_fuel_expense;
			$performance_review['truck_gallons'] = $row->truck_gallons;
			$performance_review['oor_percentage'] = $row->oor_percentage;
			$performance_review['booking_rate'] = $row->booking_rate;
			$performance_review['driver_rate'] = $row->driver_rate;
			$performance_review['driver_profit'] = $row->driver_profit;
			$performance_review['raw_profit'] = $row->raw_profit;
			$performance_review['start_datetime'] = $row->start_datetime;
			$performance_review['end_datetime'] = $row->end_datetime;
			$performance_review['saved_datetime'] = $row->saved_datetime;
			
			//TRUCK
			$truck['truck_number'] = $row->truck_number;
			$performance_review['truck'] = $truck;
			
			//FLEET MANAGER (PERSON)
			$fleet_manager['f_name'] = $row->fm_f_name;
			$fleet_manager['l_name'] = $row->fm_l_name;
			$performance_review['fleet_manager'] = $fleet_manager;
			
			//DRIVER MANAGER (PERSON)
			$driver_manager['f_name'] = $row->dm_f_name;
			$driver_manager['l_name'] = $row->dm_l_name;
			$performance_review['driver_manager'] = $driver_manager;
			
			//END WEEK (LOG ENTRY)
			$end_week['entry_datetime'] = $row->entry_datetime;
			$performance_review['end_week'] = $end_week;
			
			$performance_reviews[] = $performance_review;
			
		}// end foreach
		
		if (empty($performance_review))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $performance_review;
		}
		else if($many == "many")
		{
			return $performance_reviews;
		}
	}//end db_select_performance_review()

	//UPDATE PERFORMANCE_REVIEW
	function db_update_performance_review($set,$where)
	{
		db_update_table("performance_review",$set,$where);
		
	}//end update performance_review	
	
	//DELETE PERFORMANCE_REVIEW	
	function db_delete_performance_review($where)
	{
		db_delete_from_table("performance_review",$where);
		
	}//end db_delete_performance_review()	
	
	
	
	
	
	
//PERMISSION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT PERMISSION
	function db_insert_permission($permission)
	{
		db_insert_table("permission",$permission);
	
	}//END db_insert_permission	

	//SELECT PERMISSIONS (many)
	function db_select_permissions($where,$order_by = 'id')
	{
		return db_select_tables("permission",$where,$order_by);
		
	}//end db_select_permissions() many	

	//SELECT PERMISSION (one)
	function db_select_permissions_new($where,$order_by = 'id',$limit = 'all',$many = 'many')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." permission.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." permission.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." permission.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `permission` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$permissions = array();
		foreach ($query->result() as $row)
		{
			$permission['id'] = $row->id;
			$permission['permission_name'] = $row->permission_name;
			$permission['category'] = $row->category;
			$permission['secondary_category'] = $row->secondary_category;
			$permission['permission_description'] = $row->permission_description;
			
			$permissions[] = $permission;
			
		}// end foreach
		
		if (empty($permission))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $permission;
		}
		else if($many == "many")
		{
			return $permissions;
		}
	}//end db_select_permission()
	
	//SELECT PERMISSION (one)
	function db_select_permission($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `permission` WHERE ".$where_sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			$permission['id'] = $row->id;
			$permission['permission_name'] = $row->permission_name;
			$permission['category'] = $row->category;
			$permission['secondary_category'] = $row->secondary_category;
			$permission['permission_description'] = $row->permission_description;
			
		}// end foreach
		
		if (empty($permission))
		{
			return null;
		}else
			{
				return $permission;
			}
	}//end db_select_permission()

	//UPDATE PERMISSION
	function db_update_permission($set,$where)
	{
		db_update_table("permission",$set,$where);
		
	}//end update permission	
	
	//DELETE PERMISSION	
	function db_delete_permission($where)
	{
		db_delete_from_table("permission",$where);
		
	}//end db_delete_permission()	

	
	

//PERSON: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT PERSON
	function db_insert_person($person)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($person as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO person (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}//END db_insert_person

	//SELECT PEOPLE (many) - UPDATED VERSION OF SELECT PERSONS (OLD)
	function db_select_people($where,$order_by = 'id',$limit = 'all',$many = 'many')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." person.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." person.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." person.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `person` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$persons = array();
		foreach ($query->result() as $row)
		{
			$person['id'] = $row->id;
			$person['f_name'] = $row->f_name;
			$person['l_name'] = $row->l_name;
			$person['phone_number'] = $row->phone_number;
			$person['phone_carrier'] = $row->phone_carrier;
			$person['email'] = $row->email;
			$person['home_address'] = $row->home_address;
			$person['home_city'] = $row->home_city;
			$person['home_state'] = $row->home_state;
			$person['home_zip'] = $row->home_zip;
			$person['date_of_birth'] = $row->date_of_birth;
			$person['ssn'] = $row->ssn;
			$person['role'] = $row->role;
			$person['person_notes'] = $row->person_notes;
			$person["link_ss_card"] = $row->link_ss_card;
			$person["signature_guid"] = $row->signature_guid;
			$person["initials_guid"] = $row->initials_guid;
			$person['full_name'] = $row->f_name." ".$row->l_name;
			
			$persons[] = $person;
			
		}// end foreach
		
		if (empty($person))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $person;
		}
		else if($many == "many")
		{
			return $persons;
		}
	}//end db_select_person()
	
	//SELECT PERSON (one)
	function db_select_person($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[$i] = $value;
			$i++;
		}
		
		$sql = "SELECT * FROM person WHERE ".$where_sql;
		$query_person = $CI->db->query($sql,$values);
		
		foreach ($query_person->result() as $row)
		{
			$person['id'] = $row->id;
			$person['f_name'] = $row->f_name;
			$person['l_name'] = $row->l_name;
			$person['home_phone'] = $row->home_phone;
			$person['phone_number'] = $row->phone_number;
			$person['phone_carrier'] = $row->phone_carrier;
			$person['email'] = $row->email;
			$person['home_address'] = $row->home_address;
			$person['home_city'] = $row->home_city;
			$person['home_state'] = $row->home_state;
			$person['home_zip'] = $row->home_zip;
			$person['date_of_birth'] = $row->date_of_birth;
			$person['ssn'] = $row->ssn;
			$person['role'] = $row->role;
			$person['person_notes'] = $row->person_notes;
			$person["link_ss_card"] = $row->link_ss_card;
			$person["signature_guid"] = $row->signature_guid;
			$person["initials_guid"] = $row->initials_guid;
			$person["emergency_contact_name"] = $row->emergency_contact_name;
			$person["emergency_contact_phone"] = $row->emergency_contact_phone;
			$person['full_name'] = $row->f_name." ".$row->l_name;
			
		}
		
		if (empty($person))
		{
			return null;
		}else
			{
				return $person;
			}
	}//end db_select_person()	
	
	//SELECT PERSONS (many)
	function db_select_persons($where,$order_by = 'id')
	{
		$CI =& get_instance();
		$where_sql = " ";
		$values = null;
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				if ($i > 0)
				{
				$where_sql = $where_sql." And";
				}
				$where_sql = $where_sql." ".$key." = ?";
				$values[$i] = $value;
				$i++;
			}
			
			
		}
		else
		{
			$where_sql = $where;
		}
		$sql = "SELECT * FROM `person` WHERE ".$where_sql." ORDER BY ".$order_by;
		$query_person = $CI->db->query($sql,$values);
		
		$person = array();
		$persons = array();
		foreach ($query_person->result() as $row)
		{
			$person_where['id'] = $row->id;
			$person = db_select_person($person_where);
			
			$persons[] = $person;
		}
		
		return $persons;
	}//end db_select_persons() many	
	
	
	//UPDATE PERSON
	function db_update_person($set,$where)
	{
		$CI =& get_instance();
		$i = 0;
		$set_sql = " ";
		foreach($set as $key => $value)
		{
			if ($i > 0)
			{
			$set_sql = $set_sql.", ";
			}
			$set_sql = $set_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$sql = "UPDATE person SET ".$set_sql." WHERE ".$where_sql;
		$CI->db->query($sql,$values);
		
		
	}//end update person

	
	//DELETE PERSON
	function db_delete_person($person_id)
	{
		$CI =& get_instance();
		//ONLY COVAX13 CAN PERFORM DELETE OPERATIONS
		if($CI->session->userdata('username') == "covax13")
		{
			$sql = "DELETE FROM person WHERE id = ?";
			$CI->db->query($sql,array($person_id));
		}
		
	}// end delete person







//PICK: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT PICK
	function db_insert_pick($pick)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($pick as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO pick (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}//END db_insert_pick	

	//SELECT PICKS (many)
	function db_select_picks($where,$order_by = 'id')
	{
		return db_select_tables("pick",$where,$order_by);
		
	}//end db_select_picks() many	

	
	//SELECT PICK (one)
	function db_select_pick($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `pick` WHERE ".$where_sql;
		//echo $sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			//GET STOP
			$stop_where["id"] = $row->stop_id;
			$stop = db_select_stop($stop_where);
			
			$pick['id'] = $row->id;
			$pick['stop_id'] = $row->stop_id;
			$pick['load_id'] = $row->load_id;
			$pick['pick_number'] = $row->pick_number;
			$pick['pu_number'] = $row->pu_number;
			$pick['appointment_time'] = $row->appointment_time;
			$pick['appointment_time_mst'] = $row->appointment_time_mst;
			$pick['in_time'] = $row->in_time;
			$pick['out_time'] = $row->out_time;
			$pick['dispatch_datetime'] = $row->dispatch_datetime;
			$pick['dispatch_notes'] = $row->dispatch_notes;
			$pick['internal_notes'] = $row->internal_notes;
			
			$pick['stop'] = $stop;
			
		}// end foreach
		
		if (empty($pick))
		{
			return null;
		}else
			{
				return $pick;
			}
	}//end db_select_pick()

	//UPDATE PICK
	function db_update_pick($set,$where)
	{
		db_update_table("pick",$set,$where);
	}//end update pick	
	
	//DELETE PICK
	function db_delete_pick($pick_id)
	{
		$sql = "DELETE FROM `pick` WHERE id = ?";
		$CI =& get_instance();
		$CI->db->query($sql,array($pick_id));
		
	}//end db_delete_pick()	
		
	

	
//PURCHASE_ORDER: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT PURCHASE_ORDER
	function db_insert_purchase_order($purchase_order)
	{
		db_insert_table("purchase_order",$purchase_order);
	
	}//END db_insert_purchase_order	

	//SELECT PURCHASE_ORDERS (many)
	function db_select_purchase_orders($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_purchase_order($where,$order_by,$limit,"many");
		
	}//end db_select_purchase_orders() many	

	//SELECT PURCHASE_ORDER (one)
	function db_select_purchase_order($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." purchase_order.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." purchase_order.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." purchase_order.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				purchase_order.id as id,
				purchase_order.expense_datetime as expense_datetime,
				purchase_order.expense_amount as expense_amount,
				purchase_order.expense_id as expense_id,
				purchase_order.owner_id as owner_id,
				owner.company_side_bar_name as company_side_bar_name ,
				purchase_order.category as category,
				purchase_order.issuer_id as issuer_id,
				issuer.f_name as issuer_f_name ,
				issuer.l_name as issuer_l_name ,
				purchase_order.issued_datetime as issued_datetime,
				purchase_order.account_id as account_id,
				account.account_name as account_name ,
				purchase_order.approved_by_id as approved_by_id,
				approved_by.f_name as approved_by_f_name ,
				approved_by.l_name as approved_by_l_name ,
				purchase_order.approved_datetime as approved_datetime,
				purchase_order.po_notes as po_notes,
				purchase_order.email_datetime as email_datetime,
				purchase_order.po_log as po_log,
				purchase_order.settlement_id as settlement_id,
				purchase_order.load_id as load_id,
				purchase_order.client_id as client_id,
				purchase_order.po_truck_id as po_truck_id,
				purchase_order.po_gps as po_gps,
				purchase_order.po_city as po_city,
				purchase_order.po_state as po_state
				FROM `purchase_order`
				LEFT JOIN company as owner ON purchase_order.owner_id = owner.id 
				LEFT JOIN person as issuer ON purchase_order.issuer_id = issuer.id 
				LEFT JOIN account ON purchase_order.account_id =  account.id
				LEFT JOIN person as approved_by ON purchase_order.approved_by_id = approved_by.id ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$purchase_orders = array();
		foreach ($query->result() as $row)
		{
			$purchase_order['id'] = $row->id;
			$purchase_order['expense_datetime'] = $row->expense_datetime;
			$purchase_order['expense_amount'] = $row->expense_amount;
			$purchase_order['expense_id'] = $row->expense_id;
			$purchase_order['owner_id'] = $row->owner_id;
			$purchase_order['category'] = $row->category;
			$purchase_order['issuer_id'] = $row->issuer_id;
			$purchase_order['issued_datetime'] = $row->issued_datetime;
			$purchase_order['account_id'] = $row->account_id;
			$purchase_order['approved_by_id'] = $row->approved_by_id;
			$purchase_order['approved_datetime'] = $row->approved_datetime;
			$purchase_order['po_notes'] = $row->po_notes;
			$purchase_order['email_datetime'] = $row->email_datetime;
			$purchase_order['po_log'] = $row->po_log;
			$purchase_order['settlement_id'] = $row->settlement_id;
			$purchase_order['load_id'] = $row->load_id;
			$purchase_order['client_id'] = $row->client_id;
			$purchase_order['po_truck_id'] = $row->po_truck_id;
			$purchase_order['po_gps'] = $row->po_gps;
			$purchase_order['po_city'] = $row->po_city;
			$purchase_order['po_state'] = $row->po_state;
			
			$owner["company_side_bar_name"] = $row->company_side_bar_name;
			$purchase_order["owner"] = $owner;
			
			$issuer["f_name"] = $row->issuer_f_name;
			$issuer["l_name"] = $row->issuer_l_name;
			$issuer["full_name"] = $issuer["f_name"]." ".$issuer["l_name"];
			$purchase_order["issuer"] = $issuer;
			
			$approved_by["f_name"] = $row->approved_by_f_name;
			$approved_by["l_name"] = $row->approved_by_l_name;
			$approved_by["full_name"] = $approved_by["f_name"]." ".$approved_by["l_name"];
			$purchase_order["approved_by"] = $approved_by;
			
			$account["account_name"] = $row->account_name;
			$purchase_order["account"] = $account;
			
			$purchase_orders[] = $purchase_order;
			
		}// end foreach
		
		if (empty($purchase_order))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $purchase_order;
		}
		else if($many == "many")
		{
			return $purchase_orders;
		}
	}//end db_select_purchase_order()

	//UPDATE PURCHASE_ORDER
	function db_update_purchase_order($set,$where)
	{
		db_update_table("purchase_order",$set,$where);
		
	}//end update purchase_order	
	
	//DELETE PURCHASE_ORDER	
	function db_delete_purchase_order($where)
	{
		db_delete_from_table("purchase_order",$where);
		
	}//end db_delete_purchase_order()	
	
	
	
	
//REVENUE_SPLIT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT REVENUE_SPLIT
	function db_insert_revenue_split($revenue_split)
	{
		db_insert_table("revenue_split",$revenue_split);
	
	}//END db_insert_revenue_split	

	//SELECT REVENUE_SPLITS (many)
	function db_select_revenue_splits($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_revenue_split($where,$order_by,$limit,"many");
		
	}//end db_select_revenue_splits() many	

	//SELECT REVENUE_SPLIT (one)
	function db_select_revenue_split($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." revenue_split.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." revenue_split.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." revenue_split.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				revenue_split.id as id,
				revenue_split.client_id as client_id,
				revenue_split.owner_type as owner_type,
				revenue_split.owner_id as owner_id,
				owner.company_side_bar_name as company_side_bar_name ,
				revenue_split.description as description,
				revenue_split.percent as percent ,
				revenue_split.account_id as account_id ,
				account.account_name
				FROM `revenue_split`
				LEFT JOIN company AS owner ON revenue_split.owner_id = owner.id 
				LEFT JOIN  account ON  account_id =  account.id".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$revenue_splits = array();
		foreach ($query->result() as $row)
		{
			$revenue_split['id'] = $row->id;
			$revenue_split['client_id'] = $row->client_id;
			$revenue_split['owner_type'] = $row->owner_type;
			$revenue_split['owner_id'] = $row->owner_id;
			$revenue_split['description'] = $row->description;
			$revenue_split['percent'] = $row->percent;
			$revenue_split['account_id'] = $row->account_id;
			
			$owner["company_side_bar_name"] = $row->company_side_bar_name;
			$revenue_split["owner"] = $owner;
			
			$account["account_name"] = $row->account_name;
			$revenue_split["account"] = $account;
			
			$revenue_splits[] = $revenue_split;
			
		}// end foreach
		
		if (empty($revenue_split))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $revenue_split;
		}
		else if($many == "many")
		{
			return $revenue_splits;
		}
	}//end db_select_revenue_split()

	//UPDATE REVENUE_SPLIT
	function db_update_revenue_split($set,$where)
	{
		db_update_table("revenue_split",$set,$where);
		
	}//end update revenue_split	
	
	//DELETE REVENUE_SPLIT	
	function db_delete_revenue_split($where)
	{
		db_delete_from_table("revenue_split",$where);
		
	}//end db_delete_revenue_split()	
	
	
	
	
	
//ROUTE_REQUEST: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT ROUTE_REQUEST
	function db_insert_route_request($route_request)
	{
		db_insert_table("route_request",$route_request);
	
	}//END db_insert_route_request	

	//SELECT ROUTE_REQUESTS (many)
	function db_select_route_requests($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_route_request($where,$order_by,$limit,"many");
		
	}//end db_select_route_requests() many	

	//SELECT ROUTE_REQUEST (one)
	function db_select_route_request($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." route_request.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." route_request.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." route_request.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `route_request`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$route_requests = array();
		foreach ($query->result() as $row)
		{
			$route_request['id'] = $row->id;
			$route_request['web_service'] = $row->web_service;
			$route_request['param_url'] = $row->param_url;
			$route_request['status'] = $row->status;
			$route_request['map_miles'] = $row->map_miles;
			$route_request['route_url'] = $row->route_url;
			$route_request['count'] = $row->count;
			
			$route_requests[] = $route_request;
			
		}// end foreach
		
		if (empty($route_request))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $route_request;
		}
		else if($many == "many")
		{
			return $route_requests;
		}
	}//end db_select_route_request()

	//UPDATE ROUTE_REQUEST
	function db_update_route_request($set,$where)
	{
		db_update_table("route_request",$set,$where);
		
	}//end update route_request	
	
	//DELETE ROUTE_REQUEST	
	function db_delete_route_request($where)
	{
		db_delete_from_table("route_request",$where);
		
	}//end db_delete_route_request()	
	

	
	
	
//SECURE_FILE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT SECURE_FILE
	function db_insert_secure_file($secure_file)
	{
		db_insert_table("secure_file",$secure_file);
	
	}//END db_insert_secure_file	

	//SELECT SECURE_FILES (many)
	function db_select_secure_files($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_secure_file($where,$order_by,$limit,"many");
		
	}//end db_select_secure_files() many	

	//SELECT SECURE_FILE (one)
	function db_select_secure_file($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." secure_file.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." secure_file.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." secure_file.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM `secure_file` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$secure_files = array();
		foreach ($query->result() as $row)
		{
			$secure_file['id'] = $row->id;
			$secure_file['file_guid'] = $row->file_guid;
			$secure_file['name'] = $row->name;
			$secure_file['type'] = $row->type;
			$secure_file['title'] = $row->title;
			$secure_file['category'] = $row->category;
			$secure_file['server_path'] = $row->server_path;
			$secure_file['office_permission'] = $row->office_permission;
			$secure_file['driver_permission'] = $row->driver_permission;
			
			$secure_files[] = $secure_file;
			
		}// end foreach
		
		if (empty($secure_file))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $secure_file;
		}
		else if($many == "many")
		{
			return $secure_files;
		}
	}//end db_select_secure_file()

	//UPDATE SECURE_FILE
	function db_update_secure_file($set,$where)
	{
		db_update_table("secure_file",$set,$where);
		
	}//end update secure_file	
	
	//DELETE SECURE_FILE	
	function db_delete_secure_file($where)
	{
		db_delete_from_table("secure_file",$where);
		
	}//end db_delete_secure_file()	
	
	
	
	
	
	
//SETTING: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT SETTING
	function db_insert_setting($setting)
	{
		db_insert_table("setting",$setting);
	
	}//END db_insert_setting	

	//SELECT SETTINGS (many)
	function db_select_settings($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_setting($where,$order_by,$limit,"many");
		
	}//end db_select_settings() many	

	//SELECT SETTING (one)
	function db_select_setting($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." setting.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." setting.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." setting.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM `setting` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		//error_log($values[0]." | LINE ".__LINE__." ".__FILE__);
		$settings = array();
		foreach ($query->result() as $row)
		{
			$setting['id'] = $row->id;
			$setting['tab'] = $row->tab;
			$setting['category'] = $row->category;
			$setting['access_level'] = $row->access_level;
			$setting['setting_name'] = $row->setting_name;
			$setting['setting_value'] = $row->setting_value;
			$setting['setting_notes'] = $row->setting_notes;
			
			$settings[] = $setting;
			
		}// end foreach
		
		if (empty($setting))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $setting;
		}
		else if($many == "many")
		{
			return $settings;
		}
	}//end db_select_setting()

	//UPDATE SETTING
	function db_update_setting($set,$where)
	{
		db_update_table("setting",$set,$where);
		
	}//end update setting	
	
	//DELETE SETTING	
	function db_delete_setting($where)
	{
		db_delete_from_table("setting",$where);
		
	}//end db_delete_setting()	
	

	
	
//SETTLEMENT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT SETTLEMENT
	function db_insert_settlement($settlement)
	{
		db_insert_table("settlement",$settlement);
	
	}//END db_insert_settlement	

	//SELECT SETTLEMENTS (many)
	function db_select_settlements($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_settlement($where,$order_by,$limit,"many");
		
	}//end db_select_settlements() many	

	//SELECT SETTLEMENT (one)
	function db_select_settlement($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." settlement.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." settlement.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." settlement.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				settlement.id as id,
				settlement.client_id as client_id,
				client.client_nickname as client_nickname ,
				settlement.fm_id as fm_id,
				fm.f_name as fm_f_name ,
				fm.l_name as fm_l_name ,
				settlement.end_week_id as end_week_id ,
				end_week.entry_datetime as entry_datetime,
				end_week.locked_datetime as locked_datetime,
				settlement.kick_in as kick_in ,
				settlement.target_pay as target_pay ,
				settlement.notes_to_driver as notes_to_driver ,
				settlement.approved_datetime as approved_datetime ,
				settlement.approved_by as approved_by ,
				settlement.settled_datetime as settled_datetime ,
				approved_by_person.f_name as approved_by_f_name ,
				approved_by_person.l_name as approved_by_l_name ,
				settlement.settlement_link as settlement_link,
				settlement.html as html				
				FROM `settlement`
				LEFT JOIN client ON settlement.client_id = client.id 
				LEFT JOIN person as fm ON settlement.fm_id = fm.id 
				LEFT JOIN log_entry as end_week ON settlement.end_week_id = end_week.id 
				LEFT JOIN person as approved_by_person ON settlement.approved_by = approved_by_person.id ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//echo $sql;
		$query = $CI->db->query($sql,$values);
		$settlements = array();
		foreach ($query->result() as $row)
		{
			$settlement['id'] = $row->id;
			$settlement['client_id'] = $row->client_id;
			$settlement['fm_id'] = $row->fm_id;
			$settlement['end_week_id'] = $row->end_week_id;
			$settlement['kick_in'] = $row->kick_in;
			$settlement['target_pay'] = $row->target_pay;
			$settlement['notes_to_driver'] = $row->notes_to_driver;
			$settlement['approved_datetime'] = $row->approved_datetime;
			$settlement['approved_by'] = $row->approved_by;
			$settlement['settled_datetime'] = $row->settled_datetime;
			$settlement['settlement_link'] = $row->settlement_link;
			$settlement['html'] = $row->html;
			
			$client["client_nickname"] = $row->client_nickname;
			$settlement["client"] = $client;
			
			$fleet_manager["f_name"] = $row->fm_f_name;
			$fleet_manager["l_name"] = $row->fm_l_name;
			$settlement["fleet_manager"] = $fleet_manager;
			
			$log_entry["entry_datetime"] = $row->entry_datetime;
			$log_entry["locked_datetime"] = $row->locked_datetime;
			$settlement["log_entry"] = $log_entry;
			
			$approved_by_person["f_name"] = $row->approved_by_f_name;
			$approved_by_person["l_name"] = $row->approved_by_l_name;
			$settlement["approved_by_person"] = $approved_by_person;
			
			$settlements[] = $settlement;
			
		}// end foreach
		
		if (empty($settlement))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $settlement;
		}
		else if($many == "many")
		{
			return $settlements;
		}
	}//end db_select_settlement()

	//UPDATE SETTLEMENT
	function db_update_settlement($set,$where)
	{
		db_update_table("settlement",$set,$where);
		
	}//end update settlement	
	
	//DELETE SETTLEMENT	
	function db_delete_settlement($where)
	{
		db_delete_from_table("settlement",$where);
		
	}//end db_delete_settlement()	
		

	
	
//SETTLEMENT_ADJUSTMENT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT SETTLEMENT_ADJUSTMENT
	function db_insert_settlement_adjustment($settlement_adjustment)
	{
		db_insert_table("settlement_adjustment",$settlement_adjustment);
	
	}//END db_insert_settlement_adjustment	

	//SELECT SETTLEMENT_ADJUSTMENTS (many)
	function db_select_settlement_adjustments($where,$order_by = 'id')
	{
		return db_select_tables("settlement_adjustment",$where,$order_by);
		
	}//end db_select_settlement_adjustments() many	

	//SELECT SETTLEMENT_ADJUSTMENT (one)
	function db_select_settlement_adjustment($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `settlement_adjustment` WHERE ".$where_sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			$settlement_adjustment['id'] = $row->id;
			$settlement_adjustment['load_id'] = $row->load_id;
			$settlement_adjustment['explanation'] = $row->explanation;
			$settlement_adjustment['amount'] = $row->amount;
			
		}// end foreach
		
		if (empty($settlement_adjustment))
		{
			return null;
		}else
			{
				return $settlement_adjustment;
			}
	}//end db_select_settlement_adjustment()

	//UPDATE SETTLEMENT_ADJUSTMENT
	function db_update_settlement_adjustment($set,$where)
	{
		db_update_table("settlement_adjustment",$set,$where);
		
	}//end update settlement_adjustment	
	
	//DELETE SETTLEMENT_ADJUSTMENT	
	function db_delete_settlement_adjustment($where)
	{
		db_delete_from_table("settlement_adjustment",$where);
		
	}//end db_delete_settlement_adjustment()	
	
	
	
	
	
//SETTLEMENT_EXPENSE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT SETTLEMENT_EXPENSE
	function db_insert_settlement_expense($settlement_expense)
	{
		db_insert_table("settlement_expense",$settlement_expense);
	
	}//END db_insert_settlement_expense	

	//SELECT SETTLEMENT_EXPENSES (many)
	function db_select_settlement_expenses($where,$order_by = 'id')
	{
		return db_select_tables("settlement_expense",$where,$order_by);
		
	}//end db_select_settlement_expenses() many	

	//SELECT SETTLEMENT_EXPENSE (one)
	function db_select_settlement_expense($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `settlement_expense` WHERE ".$where_sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			$setting_where["id"] = $row->client_fee_setting_id;
			
			$settlement_expense['id'] = $row->id;
			$settlement_expense['load_id'] = $row->load_id;
			$settlement_expense['client_fee_setting_id'] = $row->client_fee_setting_id;
			$settlement_expense['explanation'] = $row->explanation;
			$settlement_expense['amount'] = $row->amount;
			
			$settlement_expense["client_fee_setting"] = db_select_client_fee_setting($setting_where);
			
		}// end foreach
		
		if (empty($settlement_expense))
		{
			return null;
		}else
			{
				return $settlement_expense;
			}
	}//end db_select_settlement_expense()

	//UPDATE SETTLEMENT_EXPENSE
	function db_update_settlement_expense($set,$where)
	{
		db_update_table("settlement_expense",$set,$where);
		
	}//end update settlement_expense	
	
	//DELETE SETTLEMENT_EXPENSE	
	function db_delete_settlement_expense($where)
	{
		db_delete_from_table("settlement_expense",$where);
		
	}//end db_delete_settlement_expense()	

	
	
	
	
//SETTLEMENT_PROFIT_SPLIT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT SETTLEMENT_PROFIT_SPLIT
	function db_insert_settlement_profit_split($settlement_profit_split)
	{
		db_insert_table("settlement_profit_split",$settlement_profit_split);
	
	}//END db_insert_settlement_profit_split	

	//SELECT SETTLEMENT_PROFIT_SPLITS (many)
	function db_select_settlement_profit_splits($where,$order_by = 'id')
	{
		return db_select_tables("settlement_profit_split",$where,$order_by);
		
	}//end db_select_settlement_profit_splits() many	

	//SELECT SETTLEMENT_PROFIT_SPLIT (one)
	function db_select_settlement_profit_split($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `settlement_profit_split` WHERE ".$where_sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			
			$where_account["id"] = $row->account_id;
			
			$settlement_profit_split['id'] = $row->id;
			$settlement_profit_split['load_id'] = $row->load_id;
			$settlement_profit_split['account_id'] = $row->account_id;
			$settlement_profit_split['percentage'] = $row->percentage;
			
			$settlement_profit_split['account'] = db_select_account($where_account);
			
		}// end foreach
		
		if (empty($settlement_profit_split))
		{
			return null;
		}else
			{
				return $settlement_profit_split;
			}
	}//end db_select_settlement_profit_split()

	//UPDATE SETTLEMENT_PROFIT_SPLIT
	function db_update_settlement_profit_split($set,$where)
	{
		db_update_table("settlement_profit_split",$set,$where);
		
	}//end update settlement_profit_split	
	
	//DELETE SETTLEMENT_PROFIT_SPLIT	
	function db_delete_settlement_profit_split($where)
	{
		db_delete_from_table("settlement_profit_split",$where);
		
	}//end db_delete_settlement_profit_split()	


	
	
	
//SETTLEMENT_TRANSACTION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT SETTLEMENT_TRANSACTION
	function db_insert_settlement_transaction($settlement_transaction)
	{
		db_insert_table("settlement_transaction",$settlement_transaction);
	
	}//END db_insert_settlement_transaction	

	//SELECT SETTLEMENT_TRANSACTIONS (many)
	function db_select_settlement_transactions($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_settlement_transaction($where,$order_by,$limit,"many");
		
	}//end db_select_settlement_transactions() many	

	//SELECT SETTLEMENT_TRANSACTION (one)
	function db_select_settlement_transaction($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." settlement_transaction.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." settlement_transaction.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." settlement_transaction.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `settlement_transaction`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$settlement_transactions = array();
		foreach ($query->result() as $row)
		{
			$settlement_transaction['id'] = $row->id;
			$settlement_transaction['settlement_id'] = $row->settlement_id;
			$settlement_transaction['transaction_id'] = $row->transaction_id;
			
			$settlement_transactions[] = $settlement_transaction;
			
		}// end foreach
		
		if (empty($settlement_transaction))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $settlement_transaction;
		}
		else if($many == "many")
		{
			return $settlement_transactions;
		}
	}//end db_select_settlement_transaction()

	//UPDATE SETTLEMENT_TRANSACTION
	function db_update_settlement_transaction($set,$where)
	{
		db_update_table("settlement_transaction",$set,$where);
		
	}//end update settlement_transaction	
	
	//DELETE SETTLEMENT_TRANSACTION	
	function db_delete_settlement_transaction($where)
	{
		db_delete_from_table("settlement_transaction",$where);
		
	}//end db_delete_settlement_transaction()	
	
	
	
	
	
//SHIFT_REPORT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT SHIFT_REPORT
	function db_insert_shift_report($shift_report)
	{
		db_insert_table("shift_report",$shift_report);
	
	}//END db_insert_shift_report	

	//SELECT SHIFT_REPORTS (many)
	function db_select_shift_reports($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_shift_report($where,$order_by,$limit,"many");
		
	}//end db_select_shift_reports() many	

	//SELECT SHIFT_REPORT (one)
	function db_select_shift_report($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." shift_report.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." shift_report.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." shift_report.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `shift_report`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$shift_reports = array();
		foreach ($query->result() as $row)
		{
			$shift_report['id'] = $row->id;
			$shift_report['log_entry_id'] = $row->log_entry_id;
			$shift_report['client_id'] = $row->client_id;
			$shift_report['shift_s_time'] = $row->shift_s_time;
			$shift_report['shift_s_gps'] = $row->shift_s_gps;
			$shift_report['shift_s_odometer'] = $row->shift_s_odometer;
			$shift_report['shift_s_fuel_level'] = $row->shift_s_fuel_level;
			$shift_report['shift_e_time'] = $row->shift_e_time;
			$shift_report['shift_e_gps'] = $row->shift_e_gps;
			$shift_report['shift_e_odometer'] = $row->shift_e_odometer;
			$shift_report['shift_e_fuel_level'] = $row->shift_e_fuel_level;
			$shift_report['plan_summary'] = $row->plan_summary;
			$shift_report['fuel_plan'] = $row->fuel_plan;
			$shift_report['toll_plan'] = $row->toll_plan;
			$shift_report['route_plan'] = $row->route_plan;
			$shift_report['dispatch_notes'] = $row->dispatch_notes;
			$shift_report['audio_w_driver_file_guid'] = $row->audio_w_driver_file_guid;
			$shift_report['audio_w_dispatch_file_guid'] = $row->audio_w_dispatch_file_guid;
			$shift_report['contact_percentage'] = $row->contact_percentage;
			$shift_report['hours_worked'] = $row->hours_worked;
			$shift_report['miles_driven'] = $row->miles_driven;
			$shift_report['goalpoint_percentage'] = $row->goalpoint_percentage;
			$shift_report['idle_time'] = $row->idle_time;
			$shift_report['comms_pf'] = $row->comms_pf;
			$shift_report['ontime_pf'] = $row->ontime_pf;
			$shift_report['oor'] = $row->oor;
			$shift_report['efficiency_rating'] = $row->efficiency_rating;
			$shift_report['map_miles'] = $row->map_miles;
			$shift_report['odometer_miles'] = $row->odometer_miles;
			$shift_report['hos_file_guid'] = $row->hos_file_guid;
			
			$shift_reports[] = $shift_report;
			
		}// end foreach
		
		if (empty($shift_report))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $shift_report;
		}
		else if($many == "many")
		{
			return $shift_reports;
		}
	}//end db_select_shift_report()

	//UPDATE SHIFT_REPORT
	function db_update_shift_report($set,$where)
	{
		db_update_table("shift_report",$set,$where);
		
	}//end update shift_report	
	
	//DELETE SHIFT_REPORT	
	function db_delete_shift_report($where)
	{
		db_delete_from_table("shift_report",$where);
		
	}//end db_delete_shift_report()	
	
		
	
	

	

//STATEMENT_CREDIT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT STATEMENT_CREDIT
	function db_insert_statement_credit($statement_credit)
	{
		db_insert_table("statement_credit",$statement_credit);
	
	}//END db_insert_statement_credit	

	//SELECT STATEMENT_CREDITS (many)
	function db_select_statement_credits($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_statement_credit($where,$order_by,$limit,"many");
		
	}//end db_select_statement_credits() many	

	//SELECT STATEMENT_CREDIT (one)
	function db_select_statement_credit($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." statement_credit.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." statement_credit.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." statement_credit.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `statement_credit`
				 ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$statement_credits = array();
		foreach ($query->result() as $row)
		{
			$statement_credit['id'] = $row->id;
			$statement_credit['settlement_id'] = $row->settlement_id;
			$statement_credit['debited_account_id'] = $row->debited_account_id;
			$statement_credit['credit_description'] = $row->credit_description;
			$statement_credit['credit_amount'] = $row->credit_amount;
			$statement_credit['settled_datetime'] = $row->settled_datetime;
			
			$statement_credits[] = $statement_credit;
			
		}// end foreach
		
		if (empty($statement_credit))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $statement_credit;
		}
		else if($many == "many")
		{
			return $statement_credits;
		}
	}//end db_select_statement_credit()

	//UPDATE STATEMENT_CREDIT
	function db_update_statement_credit($set,$where)
	{
		db_update_table("statement_credit",$set,$where);
		
	}//end update statement_credit	
	
	//DELETE STATEMENT_CREDIT	
	function db_delete_statement_credit($where)
	{
		db_delete_from_table("statement_credit",$where);
		
	}//end db_delete_statement_credit()	
	
	
	
	
	
	
	
	
//STOP: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT STOP
	function db_insert_stop($stop)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($stop as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO stop (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}//END db_insert_stop	

	//SELECT STOPS (many)
	function db_select_stops($where,$order_by = 'id')
	{
		return db_select_tables("stop",$where,$order_by);
		
	}//end db_select_stops() many	

	
	//SELECT STOP (one)
	function db_select_stop($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[$i] = $value;
			$i++;
		}
		
		$sql = "SELECT * FROM `stop` WHERE ".$where_sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			$company_where["id"] = $row->company_id;
			$company = db_select_company($company_where);
			
			
			$stop['id'] = $row->id;
			$stop['company_id'] = $row->company_id;
			$stop['stop_type'] = $row->stop_type;
			$stop['stop_datetime'] = $row->stop_datetime;
			$stop['location_name'] = $row->location_name;
			$stop['city'] = $row->city;
			$stop['state'] = $row->state;
			$stop['address'] = $row->address;
			$stop['latitude'] = $row->latitude;
			$stop['longitude'] = $row->longitude;
			$stop['odometer'] = $row->odometer;
			$stop['notes'] = $row->notes;
			
			$stop["company"] = $company;
			
		}// end foreach
		
		if (empty($stop))
		{
			return null;
		}else
			{
				return $stop;
			}
	}//end db_select_stop()

	//UPDATE STOP
	function db_update_stop($set,$where)
	{
		db_update_table('stop',$set,$where);
	}//end update stop	
	
	//DELETE STOP
	function db_delete_stop($where)
	{
		$stop = db_select_stop($where);
		db_delete_from_table("stop",$where);
		
		$stop_where["stop_id"] = $stop["id"];
		if($stop["stop_type"] == "Fuel - Fill" || $stop["stop_type"] == "Fuel - Partial")
		{
			db_delete_from_table("fuel_stop",$stop_where);
		}
		if($stop["stop_type"] == "Drop")
		{
			db_delete_from_table("drop",$stop_where);
		}
		if($stop["stop_type"] == "Pick")
		{
			db_delete_from_table("pick",$stop_where);
		}
		
	}//end db_delete_stop()		
	
	
//TICKET: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TICKET
	function db_insert_ticket($ticket)
	{
		db_insert_table("ticket",$ticket);
	
	}//END db_insert_ticket	

	//SELECT TICKETS (many)
	function db_select_tickets($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_ticket($where,$order_by,$limit,"many");
		
	}//end db_select_tickets() many	

	//SELECT TICKET (one)
	function db_select_ticket($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ticket.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." ticket.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." ticket.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT
					ticket.id,
					ticket.balance_sheet_account_id,
					ticket.claim_ticket_id,
					ticket.inspection_id,
					ticket.inspection_type,
					ticket.truck_id,
					ticket.trailer_id,
					category,
					description,
					ticket_number,
					responsible_party,
					incident_date,
					estimated_completion_date,
					amount,
					truck_or_trailer,
					completion_date,
					notes,
					truck.truck_number AS truck_number,
					trailer.trailer_number AS trailer_number,
					insurance_claim.claim_number as insurance_claim_number,
					(SELECT
						MIN(due_date) 
					FROM `ticket` t2 
					JOIN `action_item` a 
					ON t2.id = a.ticket_id 
					WHERE a.ticket_id = ticket.id 
					AND a.completion_date IS NULL) as action_item_due_date
					FROM `ticket`
					LEFT JOIN `truck` ON ticket.truck_id = truck.id
					LEFT JOIN `trailer` ON ticket.trailer_id = trailer.id 
					LEFT JOIN `insurance_claim` ON insurance_claim.ticket_id = ticket.id".$where_sql." ORDER BY ".$order_by.$limit_txt;
		// echo $sql;
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$tickets = array();
		foreach ($query->result() as $row)
		{
			$ticket['id'] = $row->id;
			$ticket['balance_sheet_account_id'] = $row->balance_sheet_account_id;
			$ticket['claim_ticket_id'] = $row->claim_ticket_id;
			$ticket['truck_id'] = $row->truck_id;
			$ticket['trailer_id'] = $row->trailer_id;
			$ticket['category'] = $row->category;
			$ticket['description'] = $row->description;
			$ticket['ticket_number'] = $row->ticket_number;
			$ticket['responsible_party'] = $row->responsible_party;
			$ticket['incident_date'] = $row->incident_date;
			$ticket['estimated_completion_date'] = $row->estimated_completion_date;
			$ticket['amount'] = $row->amount;
			$ticket['truck_or_trailer'] = $row->truck_or_trailer;
			$ticket['completion_date'] = $row->completion_date;
			$ticket['notes'] = $row->notes;
			$ticket['inspection_id'] = $row->inspection_id;
			$ticket['inspection_type'] = $row->inspection_type;
			
			$ticket['truck_number'] = $row->truck_number;
			$ticket['trailer_number'] = $row->trailer_number;
			$ticket['insurance_claim_number'] = $row->insurance_claim_number;
			$ticket['action_item_due_date'] = $row->action_item_due_date;
			
			$tickets[] = $ticket;
			
		}// end foreach
		
		if (empty($ticket))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $ticket;
		}
		else if($many == "many")
		{
			return $tickets;
		}
	}//end db_select_ticket()

	//UPDATE TICKET
	function db_update_ticket($set,$where)
	{
		db_update_table("ticket",$set,$where);
		
	}//end update ticket	
	
	//DELETE TICKET	
	function db_delete_ticket($where)
	{
		db_delete_from_table("ticket",$where);
		
	}//end db_delete_ticket()	
		

		
//TICKET_PAYMENT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TICKET_PAYMENT
	function db_insert_ticket_payment($ticket_payment)
	{
		db_insert_table("ticket_payment",$ticket_payment);
	
	}//END db_insert_ticket_payment	

	//SELECT TICKET_PAYMENTS (many)
	function db_select_ticket_payments($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_ticket_payment($where,$order_by,$limit,"many");
		
	}//end db_select_ticket_payments() many	

	//SELECT TICKET_PAYMENT (one)
	function db_select_ticket_payment($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ticket_payment.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." ticket_payment.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." ticket_payment.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `ticket_payment`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$ticket_payments = array();
		foreach ($query->result() as $row)
		{
			$ticket_payment['id'] = $row->id;
			$ticket_payment['ticket_id'] = $row->ticket_id;
			$ticket_payment['account_entry_id'] = $row->account_entry_id;
			
			//GET INVOICE
			$where = null;
			$where["id"] = $row->ticket_id;
			$ticket = db_select_invoice($where);
			
			$ticket_payment["ticket"] = $ticket;
			
			//GET ACCOUNT ENTRY
			$where = null;
			$where["id"] = $row->account_entry_id;
			$account_entry = db_select_account_entry($where);
			
			$ticket_payment["account_entry"] = $account_entry;
			
			$ticket_payments[] = $ticket_payment;
			
		}// end foreach
		
		if (empty($ticket_payment))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $ticket_payment;
		}
		else if($many == "many")
		{
			return $ticket_payments;
		}
	}//end db_select_ticket_payment()

	//UPDATE TICKET_PAYMENT
	function db_update_ticket_payment($set,$where)
	{
		db_update_table("ticket_payment",$set,$where);
		
	}//end update ticket_payment	
	
	//DELETE TICKET_PAYMENT	
	function db_delete_ticket_payment($where)
	{
		db_delete_from_table("ticket_payment",$where);
		
	}//end db_delete_ticket_payment()	


	



//TIME_PUNCH: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TIME_PUNCH
	function db_insert_time_punch($time_punch)
	{
		db_insert_table("time_punch",$time_punch);
	
	}//END db_insert_time_punch	

	//SELECT TIME_PUNCHS (many)
	function db_select_time_punchs($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_time_punch($where,$order_by,$limit,"many");
		
	}//end db_select_time_punchs() many	

	//SELECT TIME_PUNCH (one)
	function db_select_time_punch($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." time_punch.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." time_punch.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." time_punch.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `time_punch` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$time_punchs = array();
		foreach ($query->result() as $row)
		{
			$time_punch['id'] = $row->id;
			$time_punch['user_id'] = $row->user_id;
			$time_punch['datetime'] = $row->datetime;
			$time_punch['in_out'] = $row->in_out;
			$time_punch['location'] = $row->location;
			
			$time_punchs[] = $time_punch;
			
		}// end foreach
		
		if (empty($time_punch))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $time_punch;
		}
		else if($many == "many")
		{
			return $time_punchs;
		}
	}//end db_select_time_punch()

	//UPDATE TIME_PUNCH
	function db_update_time_punch($set,$where)
	{
		db_update_table("time_punch",$set,$where);
		
	}//end update time_punch	
	
	//DELETE TIME_PUNCH	
	function db_delete_time_punch($where)
	{
		db_delete_from_table("time_punch",$where);
		
	}//end db_delete_time_punch()	
	
	
		
	
//TRAILER: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TRAILER
	function db_insert_trailer($trailer)
	{
		db_insert_table("trailer",$trailer);
	
	}//END db_insert_trailer	

	//SELECT TRAILERS (many)
	function db_select_trailers($where,$order_by = 'id')
	{
		return db_select_trailer($where,"many",$order_by);
		
	}//end db_select_trailers() many	

	//SELECT TRAILER (one)
	function db_select_trailer($where,$many = 'one',$order_by = 'id')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " WHERE ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." trailer.".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." trailer.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$sql = "SELECT 
				trailer.id as id,
				client_id,
				client.client_nickname,
				fm.f_name as fm_name,
				vendor_id,
				trailer_status,
				trailer.dropdown_status,
				vendor.company_side_bar_name,
				trailer_number,
				trailer_type,
				length,
				door_type,
				tire_model,
				tire_make,
				tire_size,
				insulation_type,
				vent_type,
				etracks,
				suspension_type,
				make,
				model,
				year,
				vin,
				plate_number,
				plate_state,
				insurance_policy,
				value,
				mileage_rate,
				rental_rate,
				rental_period,
				last_inspection,
				last_service,
				lease_agreement_link,
				registration_link,
				insurance_link,
				service_log,
				ibright_id
				FROM `trailer`
				LEFT JOIN client ON trailer.client_id = client.id
				LEFT JOIN company as vendor ON trailer.vendor_id = vendor.id 
				LEFT JOIN person as fm ON client.fleet_manager_id = fm.id".$where_sql." ORDER BY ".$order_by;
		$query = $CI->db->query($sql,$values);
		$trailers = array();
		foreach ($query->result() as $row)
		{
			$trailer['id'] = $row->id;
			$trailer['client_id'] = $row->client_id;
			$trailer['vendor_id'] = $row->vendor_id;
			$trailer['trailer_status'] = $row->trailer_status;
			$trailer['dropdown_status'] = $row->dropdown_status;
			$trailer['trailer_number'] = $row->trailer_number;
			$trailer['trailer_type'] = $row->trailer_type;
			$trailer['length'] = $row->length;
			$trailer['door_type'] = $row->door_type;
			$trailer['tire_model'] = $row->tire_model;
			$trailer['tire_make'] = $row->tire_make;
			$trailer['tire_size'] = $row->tire_size;
			$trailer['insulation_type'] = $row->insulation_type;
			$trailer['vent_type'] = $row->vent_type;
			$trailer['etracks'] = $row->etracks;
			$trailer['suspension_type'] = $row->suspension_type;
			$trailer['make'] = $row->make;
			$trailer['model'] = $row->model;
			$trailer['year'] = $row->year;
			$trailer['vin'] = $row->vin;
			$trailer['plate_number'] = $row->plate_number;
			$trailer['plate_state'] = $row->plate_state;
			$trailer['insurance_policy'] = $row->insurance_policy;
			$trailer['value'] = $row->value;
			$trailer['mileage_rate'] = $row->mileage_rate;
			$trailer['rental_rate'] = $row->rental_rate;
			$trailer['rental_period'] = $row->rental_period;
			$trailer['last_inspection'] = $row->last_inspection;
			$trailer['last_service'] = $row->last_service;
			$trailer['lease_agreement_link'] = $row->lease_agreement_link;
			$trailer['registration_link'] = $row->registration_link;
			$trailer['insurance_link'] = $row->insurance_link;
			$trailer['service_log'] = $row->service_log;
			$trailer['ibright_id'] = $row->ibright_id;
			
			$client["client_nickname"] = $row->client_nickname;
			$trailer["client"] = $client;
			
			$vendor["company_side_bar_name"] = $row->company_side_bar_name;
			$trailer["vendor"] = $vendor;
			
			$fleet_manager["f_name"] = $row->fm_name;
			$trailer["fleet_manager"] = $fleet_manager;
			
			$trailers[] = $trailer;
			
		}// end foreach
		
		if (empty($trailer))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $trailer;
		}
		else if($many == "many")
		{
			return $trailers;
		}
	}//end db_select_trailer()

	//UPDATE TRAILER
	function db_update_trailer($set,$where)
	{
		db_update_table("trailer",$set,$where);
		
	}//end update trailer	
	
	//DELETE TRAILER	
	function db_delete_trailer($where)
	{
		db_delete_from_table("trailer",$where);
		
	}//end db_delete_trailer()	



//TRAILER_GEOPOINT: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TRAILER_GEOPOINT
	function db_insert_trailer_geopoint($trailer_geopoint)
	{
		db_insert_table("trailer_geopoint",$trailer_geopoint);
	
	}//END db_insert_trailer_geopoint	

	//SELECT TRAILER_GEOPOINTS (many)
	function db_select_trailer_geopoints($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_trailer_geopoint($where,$order_by,$limit,"many");
		
	}//end db_select_trailer_geopoints() many	

	//SELECT TRAILER_GEOPOINT (one)
	function db_select_trailer_geopoint($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." trailer_geopoint.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." trailer_geopoint.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." trailer_geopoint.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `trailer_geopoint` ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$trailer_geopoints = array();
		foreach ($query->result() as $row)
		{
			$trailer_geopoint['id'] = $row->id;
			$trailer_geopoint['trailer_number'] = $row->trailer_number;
			$trailer_geopoint['trailer_id'] = $row->trailer_id;
			$trailer_geopoint['status'] = $row->status;
			$trailer_geopoint['fuel_level'] = $row->fuel_level;
			$trailer_geopoint['battery_voltage'] = $row->battery_voltage;
			$trailer_geopoint['latitude'] = $row->latitude;
			$trailer_geopoint['longitude'] = $row->longitude;
			$trailer_geopoint['location'] = $row->location;
			$trailer_geopoint['set_temperature'] = $row->set_temperature;
			$trailer_geopoint['return_temperature'] = $row->return_temperature;
			$trailer_geopoint['supply_temperature'] = $row->supply_temperature;
			$trailer_geopoint['ambient_temperature'] = $row->ambient_temperature;
			$trailer_geopoint['datetime_added'] = $row->datetime_added;
			$trailer_geopoint['datetime_occurred'] = $row->datetime_occurred;
			
			$trailer_geopoints[] = $trailer_geopoint;
			
		}// end foreach
		
		if (empty($trailer_geopoint))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $trailer_geopoint;
		}
		else if($many == "many")
		{
			return $trailer_geopoints;
		}
	}//end db_select_trailer_geopoint()

	//UPDATE TRAILER_GEOPOINT
	function db_update_trailer_geopoint($set,$where)
	{
		db_update_table("trailer_geopoint",$set,$where);
		
	}//end update trailer_geopoint	
	
	//DELETE TRAILER_GEOPOINT	
	function db_delete_trailer_geopoint($where)
	{
		db_delete_from_table("trailer_geopoint",$where);
		
	}//end db_delete_trailer_geopoint()	
	



	
//TRAILER_INSPECTION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TRAILER_INSPECTION
	function db_insert_trailer_inspection($trailer_inspection)
	{
		db_insert_table("trailer_inspection",$trailer_inspection);
	
	}//END db_insert_trailer_inspection	

	//SELECT TRAILER_INSPECTIONS (many)
	function db_select_trailer_inspections($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_trailer_inspection($where,$order_by,$limit,"many");
		
	}//end db_select_trailer_inspections() many	

	//SELECT TRAILER_INSPECTION (one)
	function db_select_trailer_inspection($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." trailer_inspection.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." trailer_inspection.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." trailer_inspection.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM trailer_inspection ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$trailer_inspections = array();
		foreach ($query->result() as $row)
		{
			$trailer_inspection['id'] = $row->id;
			$trailer_inspection['ticket_id'] = $row->ticket_id;
			$trailer_inspection['is_dog_tailing'] = $row->is_dog_tailing;
			$trailer_inspection['are_trailer_tires_wearing_uniformly'] = $row->are_trailer_tires_wearing_uniformly;
			$trailer_inspection['are_trailer_tires_wearing_uniformly_desc'] = $row->are_trailer_tires_wearing_uniformly_desc;
			$trailer_inspection['is_new_trailer_tire_incident'] = $row->is_new_trailer_tire_incident;
			$trailer_inspection['is_new_trailer_tire_incident_desc'] = $row->is_new_trailer_tire_incident_desc;
			$trailer_inspection['additional_trailer_notes'] = $row->additional_trailer_notes;
			$trailer_inspection['is_new_trailer_damage'] = $row->is_new_trailer_damage;
			$trailer_inspection['is_new_trailer_damage_desc'] = $row->is_new_trailer_damage_desc;
			$trailer_inspection['are_electric_or_air_connections_damaged'] = $row->are_electric_or_air_connections_damaged;
			$trailer_inspection['are_electric_or_air_connections_damaged_desc'] = $row->are_electric_or_air_connections_damaged_desc;
			$trailer_inspection['is_headboard_damaged'] = $row->is_headboard_damaged;
			$trailer_inspection['is_headboard_damaged_desc'] = $row->is_headboard_damaged_desc;
			$trailer_inspection['are_fifth_wheel_or_kingpin_damaged'] = $row->are_fifth_wheel_or_kingpin_damaged;
			$trailer_inspection['are_fifth_wheel_or_kingpin_damaged_desc'] = $row->are_fifth_wheel_or_kingpin_damaged_desc;
			$trailer_inspection['are_lights_damaged'] = $row->are_lights_damaged;
			$trailer_inspection['are_lights_damaged_desc'] = $row->are_lights_damaged_desc;
			$trailer_inspection['is_landing_gear_damaged'] = $row->is_landing_gear_damaged;
			$trailer_inspection['is_landing_gear_damaged_desc'] = $row->is_landing_gear_damaged_desc;
			$trailer_inspection['are_reflectors_damaged'] = $row->are_reflectors_damaged;
			$trailer_inspection['are_reflectors_damaged_desc'] = $row->are_reflectors_damaged_desc;
			$trailer_inspection['are_tires_damaged'] = $row->are_tires_damaged;
			$trailer_inspection['are_tires_damaged_desc'] = $row->are_tires_damaged_desc;
			$trailer_inspection['are_wheels_or_lugs_damaged'] = $row->are_wheels_or_lugs_damaged;
			$trailer_inspection['are_wheels_or_lugs_damaged_desc'] = $row->are_wheels_or_lugs_damaged_desc;
			$trailer_inspection['are_spare_tire_rack_chains_lock_works_sign_damaged'] = $row->are_spare_tire_rack_chains_lock_works_sign_damaged;
			$trailer_inspection['are_spare_tire_rack_chains_lock_works_sign_damaged_desc'] = $row->are_spare_tire_rack_chains_lock_works_sign_damaged_desc;
			$trailer_inspection['is_unit_number_decal_damaged'] = $row->is_unit_number_decal_damaged;
			$trailer_inspection['is_unit_number_decal_damaged_desc'] = $row->is_unit_number_decal_damaged_desc;
			$trailer_inspection['are_mud_flaps_damaged'] = $row->are_mud_flaps_damaged;
			$trailer_inspection['are_mud_flaps_damaged_desc'] = $row->are_mud_flaps_damaged_desc;
			$trailer_inspection['is_rear_bumper_damaged'] = $row->is_rear_bumper_damaged;
			$trailer_inspection['is_rear_bumper_damaged_desc'] = $row->is_rear_bumper_damaged_desc;
			$trailer_inspection['are_doors_or_hinges_damaged'] = $row->are_doors_or_hinges_damaged;
			$trailer_inspection['are_doors_or_hinges_damaged_desc'] = $row->are_doors_or_hinges_damaged_desc;
			$trailer_inspection['are_brakes_damaged'] = $row->are_brakes_damaged;
			$trailer_inspection['are_brakes_damaged_desc'] = $row->are_brakes_damaged_desc;
			$trailer_inspection['are_lube_grease_zerks_damaged'] = $row->are_lube_grease_zerks_damaged;
			$trailer_inspection['are_lube_grease_zerks_damaged_desc'] = $row->are_lube_grease_zerks_damaged_desc;
			$trailer_inspection['is_hub_oil_ok'] = $row->is_hub_oil_ok;
			$trailer_inspection['is_hub_oil_ok_desc'] = $row->is_hub_oil_ok_desc;
			$trailer_inspection['are_spare_mudflap_holders_installed'] = $row->are_spare_mudflap_holders_installed;
			$trailer_inspection['are_spare_mudflap_holders_installed_desc'] = $row->are_spare_mudflap_holders_installed_desc;
			$trailer_inspection['is_slide_rail_lubed'] = $row->is_slide_rail_lubed;
			$trailer_inspection['is_slide_rail_lubed_desc'] = $row->is_slide_rail_lubed_desc;
			$trailer_inspection['are_vent_headboard_and_rear_door_damaged'] = $row->are_vent_headboard_and_rear_door_damaged;
			$trailer_inspection['are_vent_headboard_and_rear_door_damaged_desc'] = $row->are_vent_headboard_and_rear_door_damaged_desc;
			$trailer_inspection['is_ceiling_insulatin_damaged'] = $row->is_ceiling_insulatin_damaged;
			$trailer_inspection['is_ceiling_insulatin_damaged_desc'] = $row->is_ceiling_insulatin_damaged_desc;
			$trailer_inspection['are_cross_members_damaged'] = $row->are_cross_members_damaged;
			$trailer_inspection['are_cross_members_damaged_desc'] = $row->are_cross_members_damaged_desc;
			$trailer_inspection['is_side_rail_damaged'] = $row->is_side_rail_damaged;
			$trailer_inspection['is_side_rail_damaged_desc'] = $row->is_side_rail_damaged_desc;
			$trailer_inspection['is_patch_required'] = $row->is_patch_required;
			$trailer_inspection['is_patch_required_desc'] = $row->is_patch_required_desc;
			$trailer_inspection['is_registration_in_box'] = $row->is_registration_in_box;
			$trailer_inspection['is_registration_in_box_desc'] = $row->is_registration_in_box_desc;
			$trailer_inspection['is_tracker_installed_and_working'] = $row->is_tracker_installed_and_working;
			$trailer_inspection['is_tracker_installed_and_working_desc'] = $row->is_tracker_installed_and_working_desc;
			$trailer_inspection['is_inside_trailer_damaged'] = $row->is_inside_trailer_damaged;
			$trailer_inspection['is_inside_trailer_damaged_desc'] = $row->is_inside_trailer_damaged_desc;
			$trailer_inspection['trailer_pic_unit_number_guid'] = $row->trailer_pic_unit_number_guid;
			$trailer_inspection['trailer_pic_tire_rack_guid'] = $row->trailer_pic_tire_rack_guid;
			$trailer_inspection['trailer_pic_spare_tires_guid'] = $row->trailer_pic_spare_tires_guid;
			$trailer_inspection['trailer_pic_interior_guid'] = $row->trailer_pic_interior_guid;
			$trailer_inspection['trailer_pic_right_side_guid'] = $row->trailer_pic_right_side_guid;
			$trailer_inspection['trailer_pic_left_side_guid'] = $row->trailer_pic_left_side_guid;
			$trailer_inspection['trailer_pic_front_guid'] = $row->trailer_pic_front_guid;
			$trailer_inspection['trailer_pic_rear_guid'] = $row->trailer_pic_rear_guid;
			$trailer_inspection['trailer_pic_front_right_axle_guid'] = $row->trailer_pic_front_right_axle_guid;
			$trailer_inspection['trailer_pic_front_left_axle_guid'] = $row->trailer_pic_front_left_axle_guid;
			$trailer_inspection['trailer_pic_back_right_axle_guid'] = $row->trailer_pic_back_right_axle_guid;
			$trailer_inspection['trailer_pic_back_left_axle_guid'] = $row->trailer_pic_back_left_axle_guid;
			
			$trailer_inspections[] = $trailer_inspection;
			
		}// end foreach
		
		if (empty($trailer_inspection))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $trailer_inspection;
		}
		else if($many == "many")
		{
			return $trailer_inspections;
		}
	}//end db_select_trailer_inspection()

	//UPDATE TRAILER_INSPECTION
	function db_update_trailer_inspection($set,$where)
	{
		db_update_table("trailer_inspection",$set,$where);
		
	}//end update trailer_inspection	
	
	//DELETE TRAILER_INSPECTION	
	function db_delete_trailer_inspection($where)
	{
		db_delete_from_table("trailer_inspection",$where);
		
	}//end db_delete_trailer_inspection()	

	
	
	
//TRANSACTION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TRANSACTION
	function db_insert_transaction($transaction)
	{
		db_insert_table("transaction",$transaction);
	
	}//END db_insert_transaction	

	//SELECT TRANSACTIONS (many)
	function db_select_transactions($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_transaction($where,$order_by,$limit,"many");
		
	}//end db_select_transactions() many	

	//SELECT TRANSACTION (one)
	function db_select_transaction($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." transaction.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." transaction.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." transaction.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				*
				FROM `transaction`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$transactions = array();
		foreach ($query->result() as $row)
		{
			$transaction['id'] = $row->id;
			$transaction['transaction_number'] = $row->transaction_number;
			$transaction['category'] = $row->category;
			$transaction['description'] = $row->description;
			$transaction['flagged'] = $row->flagged;
			$transaction['guid'] = $row->guid;
			
			$transactions[] = $transaction;
			
		}// end foreach
		
		if (empty($transaction))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $transaction;
		}
		else if($many == "many")
		{
			return $transactions;
		}
	}//end db_select_transaction()

	//UPDATE TRANSACTION
	function db_update_transaction($set,$where)
	{
		db_update_table("transaction",$set,$where);
		
	}//end update transaction	
	
	//DELETE TRANSACTION	
	function db_delete_transaction($where)
	{
		db_delete_from_table("transaction",$where);
		
	}//end db_delete_transaction()	
	
	
	
	
//TRIPPAK: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TRIPPAK
	function db_insert_trippak($trippak)
	{
		db_insert_table("trippak",$trippak);
	
	}//END db_insert_trippak	

	//SELECT TRIPPAK (many)
	function db_select_trippaks($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_trippak($where,$order_by,$limit,"many");
		
	}//end db_select_trippaks() many	

	//SELECT TRIPPAK (one)
	function db_select_trippak($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." trippak.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." trippak.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." trippak.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `trippak`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$trippaks = array();
		foreach ($query->result() as $row)
		{
			$trippak['id'] = $row->id;
			$trippak['load_number'] = $row->load_number;
			$trippak['load_id'] = $row->load_id;
			$trippak['carrier_id'] = $row->carrier_id;
			$trippak['final_drop_city'] = $row->final_drop_city;
			$trippak['truck_number'] = $row->truck_number;
			$trippak['truck_id'] = $row->truck_id;
			$trippak['odometer'] = $row->odometer;
			$trippak['trailer_id'] = $row->trailer_id;
			$trippak['in_time'] = $row->in_time;
			$trippak['out_time'] = $row->out_time;
			$trippak['driver_1_id'] = $row->driver_1_id;
			$trippak['driver_2_id'] = $row->driver_2_id;
			$trippak['has_lumper'] = $row->has_lumper;
			$trippak['lumper_amount'] = $row->lumper_amount;
			$trippak['scan_datetime'] = $row->scan_datetime;
			$trippak['completion_datetime'] = $row->completion_datetime;
			$trippak['completed_by_id'] = $row->completed_by_id;
			$trippak['zip_file_name'] = $row->zip_file_name;
			
			$trippaks[] = $trippak;
			
		}// end foreach
		
		if (empty($trippak))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $trippak;
		}
		else if($many == "many")
		{
			return $trippaks;
		}
	}//end db_select_trippak()

	//UPDATE TRIPPAK
	function db_update_trippak($set,$where)
	{
		db_update_table("trippak",$set,$where);
		
	}//end update trippak	
	
	//DELETE TRIPPAK	
	function db_delete_trippak($where)
	{
		db_delete_from_table("trippak",$where);
		
	}//end db_delete_trippak()	
	
	
	
	
	
	
	
	
//TRUCK: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TRUCK
	function db_insert_truck($truck)
	{
		db_insert_table("truck",$truck);
	
	}//END db_insert_truck	

	//SELECT TRUCKS (many)
	function db_select_trucks($where,$order_by = 'id')
	{
		return db_select_truck($where,"many",$order_by);
		
	}//end db_select_trucks() many	

	//SELECT TRUCK (one)
	function db_select_truck($where,$many = 'one',$order_by = 'id')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " WHERE ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." truck.".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." truck.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$sql = "SELECT 
				truck.id as id,
				truck.client_id as client_id,
				client.client_nickname,
				client.license_number,
				client.license_state,
				truck.codriver_id as codriver_id,
				codriver.client_nickname as codriver_nickname,
				codriver.license_number as codriver_license_number,
				codriver.license_state as codriver_license_state,
				fm_id as fm_id,
				dm_id as dm_id,
				fm.f_name as fm_name,
				dm.f_name as dm_name,
				trailer_id as trailer_id,
				trailer.trailer_number as trailer_number,
				trailer.vin as trailer_vin,
				truck.company_id as company_id,
				company.company_side_bar_name as company_name,
				truck.vendor_id as vendor_id,
				vendor.company_side_bar_name as vendor_name,
				truck_number,
				truck.make as make,
				truck.model as model,
				truck.year as year,
				truck.vin as vin,
				truck.plate_number as plate_number,
				truck.insurance_policy as insurance_policy,
				truck.value as value,
				truck.mileage_rate as mileage_rate,
				truck.rental_rate as rental_rate,
				truck.rental_rate_period as rental_rate_period,
				last_wet_service,
				next_wet_service,
				last_dry_service,
				next_dry_service,
				truck.status,
				truck.dropdown_status,
				truck.registration_link as registration_link,
				truck.insurance_link as insurance_link,
				ifta_link,
				truck.lease_agreement_link as lease_agreement_link,
				truck.service_log_notes as service_log_notes,
				truck_notes,
				fuel_tank_capacity
				FROM `truck`
				LEFT JOIN client ON truck.client_id = client.id
				LEFT JOIN client as codriver ON truck.codriver_id = codriver.id
				LEFT JOIN company ON truck.company_id = company.id 
				LEFT JOIN company as vendor ON truck.vendor_id = vendor.id 
				LEFT JOIN trailer as trailer ON truck.trailer_id = trailer.id
				LEFT JOIN person as fm ON truck.fm_id = fm.id
				LEFT JOIN person as dm ON truck.dm_id = dm.id".$where_sql." ORDER BY ".$order_by;
		$query = $CI->db->query($sql,$values);
		$trucks = array();
		foreach ($query->result() as $row)
		{
			$truck['id'] = $row->id;
			$truck['client_id'] = $row->client_id;
			$truck['codriver_id'] = $row->codriver_id;
			$truck['company_id'] = $row->company_id;
			$truck['vendor_id'] = $row->vendor_id;
			$truck['fm_id'] = $row->fm_id;
			$truck['dm_id'] = $row->dm_id;
			$truck['trailer_id'] = $row->trailer_id;
			$truck['truck_number'] = $row->truck_number;
			$truck['make'] = $row->make;
			$truck['model'] = $row->model;
			$truck['year'] = $row->year;
			$truck['vin'] = $row->vin;
			$truck['plate_number'] = $row->plate_number;
			$truck['insurance_policy'] = $row->insurance_policy;
			$truck['value'] = $row->value;
			$truck['mileage_rate'] = $row->mileage_rate;
			$truck['rental_rate'] = $row->rental_rate;
			$truck['rental_rate_period'] = $row->rental_rate_period;
			$truck['last_wet_service'] = $row->last_wet_service;
			$truck['next_wet_service'] = $row->next_wet_service;
			$truck['last_dry_service'] = $row->last_dry_service;
			$truck['next_dry_service'] = $row->next_dry_service;
			$truck['status'] = $row->status;
			$truck['dropdown_status'] = $row->dropdown_status;
			$truck['registration_link'] = $row->registration_link;
			$truck['insurance_link'] = $row->insurance_link;
			$truck['ifta_link'] = $row->ifta_link;
			$truck['lease_agreement_link'] = $row->lease_agreement_link;
			$truck['service_log_notes'] = $row->service_log_notes;
			$truck['truck_notes'] = $row->truck_notes;
			$truck['fuel_tank_capacity'] = $row->fuel_tank_capacity;
			
			$client["client_nickname"] = $row->client_nickname;
			$client["license_number"] = $row->license_number;
			$client["license_state"] = $row->license_state;
			$truck["client"] = $client;
			
			$codriver["client_nickname"] = $row->codriver_nickname;
			$codriver["license_number"] = $row->codriver_license_number;
			$codriver["license_state"] = $row->codriver_license_state;
			$truck["codriver"] = $codriver;
			
			$company["company_side_bar_name"] = $row->company_name;
			$truck["company"] = $company;
			
			$vendor["company_side_bar_name"] = $row->vendor_name;
			$truck["vendor"] = $vendor;
			
			$fleet_manager["f_name"] = $row->fm_name;
			$truck["fleet_manager"] = $fleet_manager;
			
			$driver_manager["f_name"] = $row->dm_name;
			$truck["driver_manager"] = $driver_manager;
			
			$trailer["trailer_number"] = $row->trailer_number;
			$trailer["vin"] = $row->trailer_vin;
			$truck["trailer"] = $trailer;
			
			$trucks[] = $truck;
			
		}// end foreach
		
		if (empty($truck))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $truck;
		}
		else if($many == "many")
		{
			return $trucks;
		}
	}//end db_select_truck()

	//UPDATE TRUCK
	function db_update_truck($set,$where)
	{
		db_update_table("truck",$set,$where);
		
	}//end update truck	
	
	//DELETE TRUCK	
	function db_delete_truck($where)
	{
		db_delete_from_table("truck",$where);
		
	}//end db_delete_truck()	

	
	
//TRUCK_INSPECTION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TRUCK_INSPECTION
	function db_insert_truck_inspection($truck_inspection)
	{
		db_insert_table("truck_inspection",$truck_inspection);
	
	}//END db_insert_truck_inspection	

	//SELECT TRUCK_INSPECTIONS (many)
	function db_select_truck_inspections($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_truck_inspection($where,$order_by,$limit,"many");
		
	}//end db_select_truck_inspections() many	

	//SELECT TRUCK_INSPECTION (one)
	function db_select_truck_inspection($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." truck_inspection.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." truck_inspection.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." truck_inspection.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT * FROM truck_inspection ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$truck_inspections = array();
		foreach ($query->result() as $row)
		{
			$truck_inspection['id'] = $row->id;
			$truck_inspection['ticket_id'] = $row->ticket_id;
			$truck_inspection['odometer'] = $row->odometer;
			$truck_inspection['is_steering_vibrating'] = $row->is_steering_vibrating;
			$truck_inspection['is_steering_vibrating_desc'] = $row->is_steering_vibrating_desc;
			$truck_inspection['are_truck_tires_wearing_uniformly'] = $row->are_truck_tires_wearing_uniformly;
			$truck_inspection['are_truck_tires_wearing_uniformly_desc'] = $row->are_truck_tires_wearing_uniformly_desc;
			$truck_inspection['is_pulling_left'] = $row->is_pulling_left;
			$truck_inspection['is_pulling_left_desc'] = $row->is_pulling_left_desc;
			$truck_inspection['is_pulling_right'] = $row->is_pulling_right;
			$truck_inspection['is_pulling_right_desc'] = $row->is_pulling_right_desc;
			$truck_inspection['is_new_truck_tire_incident'] = $row->is_new_truck_tire_incident;
			$truck_inspection['is_new_truck_tire_incident_desc'] = $row->is_new_truck_tire_incident_desc;
			$truck_inspection['governed_at_drive_test'] = $row->governed_at_drive_test;
			$truck_inspection['are_vibrations_drive_test'] = $row->are_vibrations_drive_test;
			$truck_inspection['are_vibrations_desc_drive_test'] = $row->are_vibrations_desc_drive_test;
			$truck_inspection['is_pulling_drive_test'] = $row->is_pulling_drive_test;
			$truck_inspection['is_pulling_drive_test_desc'] = $row->is_pulling_drive_test_desc;
			$truck_inspection['is_engine_break_working_drive_test'] = $row->is_engine_break_working_drive_test;
			$truck_inspection['is_engine_break_working_drive_test_desc'] = $row->is_engine_break_working_drive_test_desc;
			$truck_inspection['is_shifter_working_drive_test'] = $row->is_shifter_working_drive_test;
			$truck_inspection['is_shifter_working_drive_test_desc'] = $row->is_shifter_working_drive_test_desc;
			$truck_inspection['is_interior_clean_drive_test'] = $row->is_interior_clean_drive_test;
			$truck_inspection['is_interior_clean_drive_test_desc'] = $row->is_interior_clean_drive_test_desc;
			$truck_inspection['is_seat_damage_drive_test'] = $row->is_seat_damage_drive_test;
			$truck_inspection['is_seat_damage_drive_test_desc'] = $row->is_seat_damage_drive_test_desc;
			$truck_inspection['is_curtain_divider_damaged_drive_test'] = $row->is_curtain_divider_damaged_drive_test;
			$truck_inspection['is_curtain_divider_damaged_drive_test_desc'] = $row->is_curtain_divider_damaged_drive_test_desc;
			$truck_inspection['are_knobs_working_drive_test'] = $row->are_knobs_working_drive_test;
			$truck_inspection['are_knobs_working_drive_test_desc'] = $row->are_knobs_working_drive_test_desc;
			$truck_inspection['are_vents_working_drive_test'] = $row->are_vents_working_drive_test;
			$truck_inspection['are_vents_working_drive_test_desc'] = $row->are_vents_working_drive_test_desc;
			$truck_inspection['is_dash_clear_drive_test'] = $row->is_dash_clear_drive_test;
			$truck_inspection['is_dash_clear_drive_test_desc'] = $row->is_dash_clear_drive_test_desc;
			$truck_inspection['are_cracks_in_windshield_drive_test'] = $row->are_cracks_in_windshield_drive_test;
			$truck_inspection['are_cracks_in_windshield_drive_test_desc'] = $row->are_cracks_in_windshield_drive_test_desc;
			$truck_inspection['are_interior_lights_working_drive_test'] = $row->are_interior_lights_working_drive_test;
			$truck_inspection['are_interior_lights_working_drive_test_desc'] = $row->are_interior_lights_working_drive_test_desc;
			$truck_inspection['is_air_suspension_working_drive_test'] = $row->is_air_suspension_working_drive_test;
			$truck_inspection['is_air_suspension_working_drive_test_desc'] = $row->is_air_suspension_working_drive_test_desc;
			$truck_inspection['is_apu_working_drive_test'] = $row->is_apu_working_drive_test;
			$truck_inspection['is_apu_working_drive_test_desc'] = $row->is_apu_working_drive_test_desc;
			$truck_inspection['are_headlights_working_drive_test'] = $row->are_headlights_working_drive_test;
			$truck_inspection['are_headlights_working_drive_test_desc'] = $row->are_headlights_working_drive_test_desc;
			$truck_inspection['are_fog_lights_working_drive_test'] = $row->are_fog_lights_working_drive_test;
			$truck_inspection['are_fog_lights_working_drive_test_desc'] = $row->are_fog_lights_working_drive_test_desc;
			$truck_inspection['are_windshield_wipers_working_drive_test'] = $row->are_windshield_wipers_working_drive_test;
			$truck_inspection['are_windshield_wipers_working_drive_test_desc'] = $row->are_windshield_wipers_working_drive_test_desc;
			$truck_inspection['are_documents_in_truck_drive_test'] = $row->are_documents_in_truck_drive_test;
			$truck_inspection['are_documents_in_truck_drive_test_desc'] = $row->are_documents_in_truck_drive_test_desc;
			$truck_inspection['is_dispatch_contact_info_in_truck_drive_test'] = $row->is_dispatch_contact_info_in_truck_drive_test;
			$truck_inspection['is_dispatch_contact_info_in_truck_drive_test_desc'] = $row->is_dispatch_contact_info_in_truck_drive_test_desc;
			$truck_inspection['are_doors_working_drive_test'] = $row->are_doors_working_drive_test;
			$truck_inspection['are_doors_working_drive_test_desc'] = $row->are_doors_working_drive_test_desc;
			$truck_inspection['are_windows_working_drive_test'] = $row->are_windows_working_drive_test;
			$truck_inspection['are_windows_working_drive_test_desc'] = $row->are_windows_working_drive_test_desc;
			$truck_inspection['are_back_windows_working_drive_test'] = $row->are_back_windows_working_drive_test;
			$truck_inspection['are_back_windows_working_drive_test_desc'] = $row->are_back_windows_working_drive_test_desc;
			$truck_inspection['are_exterior_doors_under_bunk_working_drive_test'] = $row->are_exterior_doors_under_bunk_working_drive_test;
			$truck_inspection['are_exterior_doors_under_bunk_working_drive_test_drive_test'] = $row->are_exterior_doors_under_bunk_working_drive_test_drive_test;
			$truck_inspection['are_air_or_electrical_lines_damaged_drive_test'] = $row->are_air_or_electrical_lines_damaged_drive_test;
			$truck_inspection['are_air_or_electrical_lines_damaged_drive_test_drive_test'] = $row->are_air_or_electrical_lines_damaged_drive_test_drive_test;
			$truck_inspection['are_dents_in_back_of_cab_drive_test'] = $row->are_dents_in_back_of_cab_drive_test;
			$truck_inspection['are_dents_in_back_of_cab_drive_test_desc'] = $row->are_dents_in_back_of_cab_drive_test_desc;
			$truck_inspection['are_dents_or_scratches_drive_test'] = $row->are_dents_or_scratches_drive_test;
			$truck_inspection['are_dents_or_scratches_drive_test_test_desc'] = $row->are_dents_or_scratches_drive_test_test_desc;
			$truck_inspection['is_hood_working_properly_drive_test'] = $row->is_hood_working_properly_drive_test;
			$truck_inspection['is_hood_working_properly_drive_test_desc'] = $row->is_hood_working_properly_drive_test_desc;
			$truck_inspection['are_fluids_good_drive_test'] = $row->are_fluids_good_drive_test;
			$truck_inspection['are_fluids_good_drive_test_desc'] = $row->are_fluids_good_drive_test_desc;
			$truck_inspection['is_air_filter_working_drive_test'] = $row->is_air_filter_working_drive_test;
			$truck_inspection['is_air_filter_working_drive_test_desc'] = $row->is_air_filter_working_drive_test_desc;
			$truck_inspection['are_bracket_supports_damaged_drive_test'] = $row->are_bracket_supports_damaged_drive_test;
			$truck_inspection['are_bracket_supports_damaged_drive_test_desc'] = $row->are_bracket_supports_damaged_drive_test_desc;
			$truck_inspection['are_side_ferrings_damaged_drive_test'] = $row->are_side_ferrings_damaged_drive_test;
			$truck_inspection['are_side_ferrings_damaged_drive_test_desc'] = $row->are_side_ferrings_damaged_drive_test_desc;
			$truck_inspection['are_vertical_ferrings_damaged_drive_test'] = $row->are_vertical_ferrings_damaged_drive_test;
			$truck_inspection['are_vertical_ferrings_damaged_drive_test_desc'] = $row->are_vertical_ferrings_damaged_drive_test_desc;
			$truck_inspection['is_u_joint_tight_drive_test'] = $row->is_u_joint_tight_drive_test;
			$truck_inspection['is_u_joint_tight_drive_test_desc'] = $row->is_u_joint_tight_drive_test_desc;
			$truck_inspection['are_tires_damaged_drive_test'] = $row->are_tires_damaged_drive_test;
			$truck_inspection['are_tires_damaged_drive_test_desc'] = $row->are_tires_damaged_drive_test_desc;
			$truck_inspection['is_tire_tread_depth_ok_drive_test'] = $row->is_tire_tread_depth_ok_drive_test;
			$truck_inspection['is_tire_tread_depth_ok_drive_test_desc'] = $row->is_tire_tread_depth_ok_drive_test_desc;
			$truck_inspection['is_tire_air_pressure_ok_drive_test'] = $row->is_tire_air_pressure_ok_drive_test;
			$truck_inspection['is_tire_air_pressure_ok_drive_test_desc'] = $row->is_tire_air_pressure_ok_drive_test_desc;
			$truck_inspection['is_hub_oil_ok_drive_test'] = $row->is_hub_oil_ok_drive_test;
			$truck_inspection['is_hub_oil_ok_drive_test_desc'] = $row->is_hub_oil_ok_drive_test_desc;
			$truck_inspection['is_grill_or_hood_damaged_drive_test'] = $row->is_grill_or_hood_damaged_drive_test;
			$truck_inspection['is_grill_or_hood_damaged_drive_test_desc'] = $row->is_grill_or_hood_damaged_drive_test_desc;
			$truck_inspection['are_mirrors_damaged_drive_test'] = $row->are_mirrors_damaged_drive_test;
			$truck_inspection['are_mirrors_damaged_drive_test_desc'] = $row->are_mirrors_damaged_drive_test_desc;
			$truck_inspection['other_problems_drive_test'] = $row->other_problems_drive_test;
			$truck_inspection['additional_truck_notes'] = $row->additional_truck_notes;
			$truck_inspection['is_new_truck_damage'] = $row->is_new_truck_damage;
			$truck_inspection['is_new_truck_damage_desc'] = $row->is_new_truck_damage_desc;
			$truck_inspection['truck_pic_right_side_guid'] = $row->truck_pic_right_side_guid;
			$truck_inspection['truck_pic_left_side_guid'] = $row->truck_pic_left_side_guid;
			$truck_inspection['truck_pic_front_guid'] = $row->truck_pic_front_guid;
			$truck_inspection['truck_pic_back_guid'] = $row->truck_pic_back_guid;
			$truck_inspection['truck_pic_transponder_guid'] = $row->truck_pic_transponder_guid;
			$truck_inspection['truck_pic_driver_seat_guid'] = $row->truck_pic_driver_seat_guid;
			$truck_inspection['truck_pic_passenger_seat_guid'] = $row->truck_pic_passenger_seat_guid;
			$truck_inspection['truck_pic_dash_board_guid'] = $row->truck_pic_dash_board_guid;
			$truck_inspection['truck_pic_odometer_guid'] = $row->truck_pic_odometer_guid;
			$truck_inspection['truck_pic_front_right_axle_guid'] = $row->truck_pic_front_right_axle_guid;
			$truck_inspection['truck_pic_front_left_axle_guid'] = $row->truck_pic_front_left_axle_guid;
			$truck_inspection['truck_pic_back_right_axle_guid'] = $row->truck_pic_back_right_axle_guid;
			$truck_inspection['truck_pic_back_left_axle_guid'] = $row->truck_pic_back_left_axle_guid;
			
			$truck_inspections[] = $truck_inspection;
			
		}// end foreach
		
		if (empty($truck_inspection))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $truck_inspection;
		}
		else if($many == "many")
		{
			return $truck_inspections;
		}
	}//end db_select_truck_inspection()

	//UPDATE TRUCK_INSPECTION
	function db_update_truck_inspection($set,$where)
	{
		db_update_table("truck_inspection",$set,$where);
		
	}//end update truck_inspection	
	
	//DELETE TRUCK_INSPECTION	
	function db_delete_truck_inspection($where)
	{
		db_delete_from_table("truck_inspection",$where);
		
	}//end db_delete_truck_inspection()	
	
	
	
//USER: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT USER
	function db_insert_user($user)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($user as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO user (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}//END db_insert_user

	//SELECT USER (one)
	function db_select_user($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[$i] = $value;
			$i++;
		}
		
		$sql = "SELECT * FROM user WHERE ".$where_sql;
		$query_user = $CI->db->query($sql,$values);
		
		foreach ($query_user->result() as $row)
		{
			//GET PERSON
			$person_where["id"] = $row->person_id;
			
			$user['id'] = $row->id;
			$user['person_id'] = $row->person_id;
			$user['username'] = $row->username;
			$user['password'] = $row->password;
			$user['pin'] = $row->pin;
			$user['user_status'] = $row->user_status;
			$user['fleetsmarts_session_token'] = $row->fleetsmarts_session_token;
			$user['copilot_session_token'] = $row->copilot_session_token;
			$user['slack_username'] = $row->slack_username;
			$user["person"] = db_select_person($person_where);
			
		}
		
		if (empty($user))
		{
			return null;
		}else
			{
				return $user;
			}
	}//end db_select_user()	
	
	//SELECT USERS (many)
	function db_select_users($where,$order_by = 'id')
	{
		$CI =& get_instance();
		$where_sql = " ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				if ($i > 0)
				{
				$where_sql = $where_sql." And";
				}
				$where_sql = $where_sql." ".$key." = ?";
				$values[$i] = $value;
				$i++;
			}
			
			
		}
		else
		{
			$where_sql = $where;
		}
		$sql = "SELECT * FROM `user` WHERE ".$where_sql." ORDER BY ".$order_by;
		$query_user = $CI->db->query($sql,$values);
		
		$user = array();
		$users = array();
		foreach ($query_user->result() as $row)
		{
			$user_where['id'] = $row->id;
			$user = db_select_user($user_where);
			
			$users[] = $user;
		}
		
		return $users;
	}//end db_select_users() many	
	
	
	//UPDATE USER
	function db_update_user($set,$where)
	{
		$CI =& get_instance();
		$i = 0;
		$set_sql = " ";
		foreach($set as $key => $value)
		{
			if ($i > 0)
			{
			$set_sql = $set_sql.", ";
			}
			$set_sql = $set_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$i = 0;
		$where_sql = " ";
		foreach($where as $key => $value)
		{
			if ($i > 0)
			{
			$where_sql = $where_sql." And";
			}
			$where_sql = $where_sql." ".$key." = ?";
			$values[] = $value;
			$i++;
		}
		
		$sql = "UPDATE user SET ".$set_sql." WHERE ".$where_sql;
		$CI->db->query($sql,$values);
		
		
	}//end update user

	
	//DELETE USER
	function db_delete_user($user_id)
	{
		$CI =& get_instance();
		//ONLY COVAX13 CAN PERFORM DELETE OPERATIONS
		if($CI->session->userdata('username') == "covax13")
		{
			$sql = "DELETE FROM user WHERE id = ?";
			$CI->db->query($sql,array($user_id));
		}
		
	}// end delete user


	
//USER_PERMISSION: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT USER_PERMISSION
	function db_insert_user_permission($user_permission)
	{
		db_insert_table("user_permission",$user_permission);
	
	}//END db_insert_user_permission	

	//SELECT USER_PERMISSIONS (many)
	function db_select_user_permissions($where,$order_by = 'id')
	{
		return db_select_tables("user_permission",$where,$order_by);
		
	}//end db_select_user_permissions() many	

	//SELECT USER_PERMISSION (one)
	function db_select_user_permission($where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `user_permission` WHERE ".$where_sql;
		$query_trips = $CI->db->query($sql,$values);
		
		foreach ($query_trips->result() as $row)
		{
			$user_permission['id'] = $row->id;
			$user_permission['user_id'] = $row->user_id;
			$user_permission['permission_id'] = $row->permission_id;
			
		}// end foreach
		
		if (empty($user_permission))
		{
			return null;
		}else
			{
				return $user_permission;
			}
	}//end db_select_user_permission()

	//UPDATE USER_PERMISSION
	function db_update_user_permission($set,$where)
	{
		db_update_table("user_permission",$set,$where);
		
	}//end update user_permission	
	
	//DELETE USER_PERMISSION	
	function db_delete_user_permission($where)
	{
		db_delete_from_table("user_permission",$where);
		
	}//end db_delete_user_permission()	

	
//SQL QUERIES USED FOR REPORTS FROM THE DATABASE	
/**
	//GET REVENUES
	SELECT 
	truck.truck_number,
	main_driver.client_nickname as main_driver_nickname ,
	codriver.client_nickname as codriver_nickname ,
	leg.map_miles,
	leg.odometer_miles,
	`load`.customer_load_number,
	`load`.natl_fuel_avg,
	trailer.trailer_number,
	leg.rate_type,
	leg.revenue_rate,
	leg.hours,
	leg.fuel_expense,
	leg.gallons_used,
	FROM `leg`
	LEFT JOIN log_entry ON leg.log_entry_id = log_entry.id
	LEFT JOIN person ON leg.approved_by_id = person.id 
	LEFT JOIN  `load` ON  leg.`load_id` =  `load`.id
	LEFT JOIN  `load` AS allocated_load ON leg.allocated_load_id =  allocated_load.id
	LEFT JOIN truck ON leg.truck_id = truck.id 
	LEFT JOIN trailer ON leg.trailer_id = trailer.id 
	LEFT JOIN client as main_driver ON leg.main_driver_id = main_driver.id 
	LEFT JOIN client as codriver ON leg.codriver_id = codriver.id
	WHERE
	log_entry.entry_datetime > '2014-02-01' AND
	log_entry.entry_datetime < '2014-02-08'
	

	//GET EXPENSES
	SELECT 
	expense_datetime as 'Date',
	company.company_side_bar_name as 'Owner',
	expense.category as 'Category',
	expense.description as 'Description',
	expense.expense_amount as 'Amount'
	FROM `expense`,`company` 
	WHERE
	expense.company_id = company.id
	AND expense_datetime >= "2014-04-19 00:00:00"
	AND expense_datetime < "2014-04-26 00:00:00"
	
	//EXPENSE REPORT FOR CHRIS
	SELECT 
	expense_datetime,
	company_side_bar_name AS 'entity',
	account_name,
	expense.category,
	description,
	expense_type,
	debit_credit,
	expense_amount
	FROM `expense`
	LEFT JOIN company ON expense.company_id = company.id
	LEFT JOIN account ON expense.expense_account_id = account.id
	WHERE expense_datetime >= '2014-10-04 00:00:00'
	AND expense_datetime < '2014-10-11 00:00:00'
	
	
	//DAMAGE ADJUSTMENT REPORT
	SELECT 
	expense_datetime,
	client_nickname,
	client_expense.category,
	description,
	expense_amount
	FROM client_expense
	LEFT JOIN client ON client_expense.client_id = client.id
	WHERE client_expense.category = 'Damage Adjustment'
	AND expense_datetime >= '2014-08-30 00:00:00'
	AND expense_datetime < '2014-10-18 00:00:00'
	
	//GET LEGS FOR STANDARD EXPENSES
	SELECT 
	leg.id as id,
	allocated_load.customer_load_number AS allocated_load_number,
	allocated_load.natl_fuel_avg AS allocated_load_natl_fuel_avg,
	company.company_side_bar_name,
	truck.truck_number,
	trailer.trailer_number,
	main_driver.client_nickname as main_driver_nickname ,
	codriver.client_nickname as codriver_nickname ,
	leg.rate_type,
	leg.revenue_rate,
	leg.odometer_miles,
	leg.map_miles,
	leg.hours,
	leg.fuel_expense,
	leg.reefer_fuel_expense,
	leg.truck_rental_expense,
	leg.truck_mileage_expense,
	leg.trailer_rental_expense,
	leg.trailer_mileage_expense,
	leg.insurance_expense,
	leg.factoring_expense,
	leg.bad_debt_expense,
	leg.damage_expense,
	leg.gallons_used,
	leg.reefer_gallons_used,
	log_entry.entry_datetime,
	log_entry.locked_datetime as locked_datetime
	FROM `leg`
	LEFT JOIN log_entry ON leg.log_entry_id = log_entry.id
	LEFT JOIN company ON leg.fm_id = company.id
	LEFT JOIN person ON leg.approved_by_id = person.id 
	LEFT JOIN  `load` ON  leg.`load_id` =  `load`.id
	LEFT JOIN  `load` AS allocated_load ON leg.allocated_load_id =  allocated_load.id
	LEFT JOIN truck ON leg.truck_id = truck.id 
	LEFT JOIN trailer ON leg.trailer_id = trailer.id 
	LEFT JOIN client as main_driver ON leg.main_driver_id = main_driver.id 
	LEFT JOIN client as codriver ON leg.codriver_id = codriver.id
	WHERE entry_datetime >= '2014-05-03 00:00:00' 
	AND entry_datetime < '2014-05-10 00:00:00'
	
	//GET EXPENSES BY CATEGORY - FOR STATEMENT OF CASH FLOWS
	SELECT category, sum(expense_amount) as 'amount', count(*) as 'count' 
	FROM `expense` 
	WHERE expense_datetime >= '2014-05-23' AND expense_datetime <= '2014-06-27'
	AND expense_type = 'expense'
	GROUP BY category
	
	//GET CLIENT EXPNESES BY CATEGORY - FOR INCOME STATEMENT
	SELECT category,count(*) sum(expense_amount)
	FROM `client_expense`
	WHERE expense_datetime >= '2014-05-23' 
	AND expense_datetime <= '2014-06-27'
	GROUP BY category

	//GET KICK IN AND EARNED FOR DRIVERS - NET INCOME STATEMENT
	SELECT sum(kick_in),sum(target_pay)
	FROM settlement,log_entry
	WHERE end_week_id = log_entry.id
	AND log_entry.entry_datetime >= '2014-05-24 00:00:00' 
	AND log_entry.entry_datetime <= '2014-06-27 23:59:59'
	
	//GET SUM OF FUEL EXPENSE ACCORDING TO ALLOCATIONS IN DATE RANGE
	SELECT sum(expense) AS 'truck and reefer', sum(reefer_expense) as 'reefer'
	FROM `fuel_allocation`,leg,log_entry
	WHERE fuel_allocation.leg_id = leg.id
	AND leg.log_entry_id = log_entry.id
	AND log_entry.entry_datetime >= '2014-05-23' AND log_entry.entry_datetime <= '2014-06-27'

	//GET FUEL STOP INFO
	SELECT sum(fuel_expense),sum(rebate_amount),count(*) 
	FROM `fuel_stop`,log_entry
	WHERE fuel_stop.log_entry_id = log_entry.id
	AND source = 'ComData'
	AND log_entry.entry_datetime >= '2014-05-24 00:00:00' 
	AND log_entry.entry_datetime <= '2014-06-27 23:59:59'
	
	//GET FUEL STOPS FOR DTR REPORT
	SELECT
	log_entry.entry_datetime AS "Date Time",
	truck.truck_number AS "Unit",
	log_entry.odometer,
	entry_notes AS "Truck Stop",
	address AS "Address",
	city AS "City",
	state AS "State",
	entry_type AS "Fuel Type",
	gallons AS "Gallons",
	fuel_price AS "Price Per Gallon",
	fuel_expense AS "Total Invoice",
	route
	FROM `log_entry`
	LEFT JOIN fuel_stop ON log_entry.id = fuel_stop.log_entry_id
	LEFT JOIN truck ON log_entry.truck_id = truck.id
	WHERE 
	(entry_type = "Fuel Fill" OR entry_type = "Fuel Partial" OR entry_type = "Fuel Reefer")
	AND log_entry.entry_datetime >= '2014-11-01 00:00:00' 
	AND log_entry.entry_datetime <= '2014-11-30 23:59:59'
	ORDER BY log_entry.entry_datetime
	
**/	

?>