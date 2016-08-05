<?php		

	
class MY_Controller extends CI_Controller 
{
	
	function MY_Controller()
	{
		parent::__construct();
		
		$current_user = $this->session->userdata('username');
		
		if ($current_user == "")
		{
			$ref_uri = $this->uri->uri_string;
			$ref_uri = str_replace('/','.',$ref_uri);
			redirect(base_url("/index.php/login/index/$ref_uri"));
		}
	}

}

?>