<?php		
class Settings extends MY_Controller 
{
	function index()
	{
		
		//GET STAFF
		$where = null;
		//$where['category'] = "Office Staff";
		//$where['company_status'] = "Active";
		$where = " (category = 'Office Staff' OR category = 'Fleet Manager') AND company_status = 'Active' ";
		$staff = db_select_companys($where,"company_name");
		
		$user_options = array();
		$user_options[0] = "Select";
		foreach($staff as $user)
		{
			$user_options[$user["id"]] = $user["company_name"];
		}
		
		//GET PERMISSIONS
		$where = null;
		$where = "1 = 1";
		$permissions = db_select_permissions($where,"permission_name");
		
		$permission_options = array();
		$permission_options[0] = "Select";
		foreach($permissions as $permission)
		{
			$permission_options[$permission["id"]] = $permission["permission_name"];
		}
		
		$data['permission_options'] = $permission_options;
		$data['user_options'] = $user_options;
		$data['title'] = "Settings";
		$data['tab'] = 'Settings';
		
		if(user_has_permission("view settings tab"))
		{
			$this->load->view('settings_view',$data);
		}
		
	}
	
	function add_permission()
	{
		if(user_has_permission("manage permission settings"))
		{
			$user_id = $_POST['user_id'];
			$permission_id = $_POST['permission_id'];
			
			$where = null;
			$where['id'] = $user_id;
			$user = db_select_user($where);
			
			$set = array();
			$set['user_id'] = $user['id'];
			$set['permission_id'] = $permission_id;
			db_insert_user_permission($set);
		}
		else
		{
			echo "<script>alert('You do not have permission to perform this action.');</script>";
		}
	}
	
	function add_user()
	{
		if(user_has_permission("manage permission settings"))
		{
			$company_id = $_POST['user_id'];
			$permission_id = $_POST['permission_id'];
			
			$where = null;
			$where['id'] = $company_id;
			$company = db_select_company($where);
			
			$where = null;
			$where['id'] = $company['person_id'];
			$person = db_select_person($where);
			
			$where = null;
			$where['person_id'] = $person['id'];
			$user = db_select_user($where);
			
			$set = array();
			$set['user_id'] = $user['id'];
			$set['permission_id'] = $permission_id;
			db_insert_user_permission($set);
		}
		else
		{
			echo "<script>alert('You do not have permission to perform this action.');</script>";
		}
	}
	
	function delete_permission()
	{
		if(user_has_permission("manage permission settings"))
		{
			$user_id = $_POST['user_id'];
			$permission_id = $_POST['permission_id'];
			
			$where = null;
			$where['id'] = $user_id;
			$user = db_select_user($where);
			
			$where = null;
			$where['user_id'] = $user['id'];
			$where['permission_id'] = $permission_id;
			db_delete_user_permission($where);
		}
		else
		{
			echo "<script>alert('You do not have permission to perform this action.');</script>";
		}
	}
	
	function delete_user()
	{
		if(user_has_permission("manage permission settings"))
		{
			$company_id = $_POST['user_id'];
			$permission_id = $_POST['permission_id'];
			
			$where = null;
			$where['id'] = $company_id;
			$company = db_select_company($where);
			
			$where = null;
			$where['id'] = $company['person_id'];
			$person = db_select_person($where);
			
			$where = null;
			$where['person_id'] = $person['id'];
			$user = db_select_user($where);
			
			$where = null;
			$where['user_id'] = $user['id'];
			$where['permission_id'] = $permission_id;
			db_delete_user_permission($where);
		}
		else
		{
			echo "<script>alert('You do not have permission to perform this action.');</script>";
		}
	}
	
	function load_permission_report()
	{
		$permission_id = $_POST['permission_id'];
		
		$where = null;
		$where['id'] = $permission_id;
		$permission = db_select_permission($where);
		
		$where = null;
		$where['permission_id'] = $permission_id;
		$user_permissions = db_select_user_permissions($where);
		
		$available_users = array();
		$current_users = array();
		foreach($user_permissions as $user_permission)
		{
			$where = null;
			$where['id'] = $user_permission['user_id'];
			$user = db_select_user($where);
			$current_users[] = $user;
		}
		$where = null;
		$where = "1 = 1";
		$all_users = db_select_users($where);
		foreach($all_users as $selected_user)
		{
			if(!in_array($selected_user,$current_users) && !is_null($selected_user['person_id']))
			{
				$available_users[] = $selected_user;
			}
		}
		
		$available_persons = array();
		$current_persons = array();
		foreach($current_users as $selected_user)
		{
			$where = null;
			$where['person_id'] = $selected_user['person_id'];
			$where['category'] = "Office Staff";
			$current_person = db_select_company($where);
			if($current_person['company_name']!="" || in_array($current_person,$current_persons))
			{
				$current_persons[] = $current_person;
			}
		}
		
		foreach($available_users as $available_user)
		{
			$where = null;
			$where['person_id'] = $available_user['person_id'];
			$where['category'] = "Office Staff";
			$available_person = db_select_company($where);
			//echo $available_person[];
			if(!is_null($available_person['company_name']) || !is_null($available_person['person_id']))
			{
				$available_persons[] = $available_person;
			}
		}
		// echo "<pre>".print_r($current_persons)."</pre>";
		//echo "<pre>".print_r($available_users)."</pre>";
		$data['available_persons'] = $available_persons;
		$data['current_persons'] = $current_persons;
		$data['permission'] = $permission;
		$this->load->view('settings/permission_report',$data);
		
	}
	
	function load_user_report()
	{
		$user_id = $_POST['user_id'];
		
		$where = null;
		$where['id'] = $user_id;
		$company = db_select_company($where);
		
		$where = null;
		$where['person_id'] = $company['person_id'];
		$user = db_select_user($where);
		
		$where = null;
		$where['user_id'] = $user['id'];
		$current_user_permissions = db_select_user_permissions($where);
		
		$current_permissions = array();
		foreach($current_user_permissions as $current_user_permission)
		{
			$where = null;
			$where['id'] = $current_user_permission['permission_id'];
			$permission = db_select_permission($where);
			if(!$permission['permission_name']=="")
			{
				$current_permissions[] = $permission;
			}
			
		}
		
		$where = null;
		$where = "1 = 1";
		$all_permissions = db_select_permissions($where,"permission_name");
		
		$available_permissions = array();
		foreach($all_permissions as $permission)
		{
			if(!in_array($permission,$current_permissions))
			{
				$available_permissions[] = $permission;
			}
		}
		//print_r($current_permissions);
		$data['available_permissions'] = $available_permissions;
		$data['current_permissions'] = $current_permissions;
		$data['company'] = $company;
		$data['user'] = $user;
		$this->load->view('settings/user_report',$data);
		
	}
}