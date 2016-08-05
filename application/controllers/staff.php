<?php		


	
class Staff extends MY_Controller 
{

	function index($staff_role,$mode,$staff_id)
	{	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Driver')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		$sql = "SELECT * FROM people WHERE Role = ? ORDER BY F_name";
		$staff_role = str_replace("_"," ",$staff_role);
		$sql_role = $staff_role;
		if($staff_role == 'All')
		{
			$sql = "SELECT * FROM people WHERE Role != ? AND Role != ? ORDER BY F_name";
			$sql_role = array('Driver','place_holder');
		}
		
		
		$query_staff = $this->db->query($sql,$sql_role);
		
		$this_staff_where['ID'] = $staff_id;
		$this_staff = db_select_people($this_staff_where);
		
		//GET ALL USERS FOR USERNAME VALIDATION LIST
		$all_people_where[1] = true;
		$all_people = db_select_peoples($all_people_where);
		
		$data["all_people"] = $all_people;
		$data['user_id'] = $this->session->userdata('user_id');
		@$data['this_staff'] = $this_staff;
		$data['query_staff'] = $query_staff;
		$data['staff_role'] = $staff_role;
		$data['mode'] = $mode;
		$data['tab'] = 'Staff';
		$data['title'] = "Staff";
		$this->load->view('staff_view',$data);
	}// end index
	
	function select_staff_type()
	{
		$url_staff_role = str_replace(" ","_",$_POST['staff_role_dropdown']);
		redirect(base_url("index.php/staff/index/".$url_staff_role."/none/0"));
	}
	
	function save_staff()
	{
		
		if ($_POST['password'] == 'hidden')
		{
			$this_staff_where['ID'] = $_POST['id'];
			$this_staff = db_select_people($this_staff_where);
			$password = $this_staff['password'];
		}else
		{
			$password = $_POST['password'];
		}
		
		$sql = "UPDATE people 
				SET	F_name = ?,
					L_name = ?, 
					Phone_num = ?, 
					Phone_carrier = ?, 
					Email = ?, 
					Password = ?,
					Role = ?,
					Status = ?, 
					pay_rate = ?, 
					damage_reserve_rate = ?,
					damage_reserve_limit = ?
					WHERE ID = ?";
					
		$sql_array = array(	$_POST['f_name'],
							$_POST['l_name'],
							$_POST['phone_num'],
							$_POST['phone_carrier'],
							$_POST['email'],
							$password,
							$_POST['role'],
							$_POST['status'],
							0,
							0,
							0,
							$_POST['id']);
							
		$this->db->query($sql,$sql_array);
		
		redirect(base_url('index.php/staff/index/'.str_replace(" ","_",$_POST['role']).'/view/'.$_POST['id']));
	}
	
	function add_staff()
	{
		//THIS IS WHERE YOU WILL ASSIGN ACCESS LEVELS DEPENDENT ON ROLES
		$access_level = 0;
		
		$values = array(	
							$_POST['add_staff_f_name'],
							$_POST['add_staff_l_name'],
							$_POST['add_staff_phone_num'],
							$_POST['add_staff_phone_carrier'],
							$_POST['add_staff_email'],
							$_POST['add_staff_username'],
							$_POST['add_staff_password'],
							$_POST['add_staff_role'],
							$access_level,
							$_POST['add_staff_status'],
							0,
							0,
							0
						);
							
		db_insert_people($values);
		
		$new_staff_where['username'] = $_POST['add_staff_username'];
		$new_staff = db_select_people($new_staff_where);
		
		redirect(base_url('index.php/staff/index/'.str_replace(" ","_",$_POST['add_staff_role']).'/view/'.$new_staff['id']));
		
	}
	
	function delete_staff()
	{
		db_delete_people($_POST['staff_id']);
		
		redirect(base_url("index.php/staff/index/All/none/0"));
	}
}
?>