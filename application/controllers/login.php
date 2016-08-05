<?php		

	
class Login extends CI_Controller 
{

	function index($ref_uri = '')
	{
		if(empty($ref_uri))
		{
			$ref_uri = "home";
		}
		$data['title'] = "Login";
		$data['ref_uri'] = $ref_uri;
		$this->load->view('login_view',$data);
	}
	
	function authenticate()
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$ref_uri = $_POST['ref_uri'];
		//$sql = "SELECT * FROM people WHERE Username = ?";
		//$query = $this->db->query($sql,array($username));
		
		$user_where['username'] = $username;
		@$user = db_select_user($user_where);
		
		if (empty($user['password']) || $user['user_status'] == "Inactive")
		{
			echo "Invalid credentials";
		}
		elseif ($user['person']["role"] == "Client")
		{
			redirect("http://clients.fleetsmarts.net");
			break;
		}
		elseif ($user['password'] == $password)
		{
			$fleetsmarts_session_token = get_random_string(10);
		
			$this->session->set_userdata('username', $username);
			$this->session->set_userdata('user_id', $user['id']);
			$this->session->set_userdata('person_id', $user['person']["id"]);
			$this->session->set_userdata('role', $user['person']["role"]);
			$this->session->set_userdata('f_name', $user['person']["f_name"]);
			$this->session->set_userdata('l_name', $user['person']["l_name"]);
			$this->session->set_userdata('fleetsmarts_session_token',$fleetsmarts_session_token);
			
			//SET FLEETSMARTS SESSION TOKEN IN THE DATABASE
			$user_set["fleetsmarts_session_token"] = $fleetsmarts_session_token;
			$user_where["id"] = $user["id"];
			db_update_user($user_set,$user_where);
			
			$ref_uri = str_replace('.','/',$ref_uri);
			redirect(base_url("index.php/home"));
		}
		else
		{
			echo "Invalid credentials";
		}
	}
	
	
}
?>