<?php		


	
class Documents extends MY_Controller 
{

	function index(){	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Client')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		//GET ALL ESIGN_DOCS
		$where = null;
		$where = "1 = 1";
		$esign_docs = db_select_esign_docs($where);
		
		//GET ALL PERSONS FOR SIGNERS
		$where = null;
		$where["role"] = "Client";
		$signers = db_select_persons($where,"f_name");
		
		$signer_options = array();
		$signer_options[] = "Select";
		foreach($signers as $person)
		{
			$signer_options[$person["id"]] = $person["f_name"]." ".$person["l_name"];
		}
		
		$categories = get_distinct("category","secure_file");
		
		$category_options = array();
		$category_options[] = "Select";
		foreach($categories as $option)
		{
			$category_options[$option] = $option;
		}
		
		$document_options = array();
		$document_options[] = "Select";
		foreach(get_distinct("title","secure_file") as $document)
		{
			$document_options[$document] = $document;
			
		}
		
		$data['document_options'] = $document_options;
		$data['category_options'] = $category_options;
		$data['title'] = "Documents";
		$data['tab'] = 'Documents';
		$data['signer_options'] = $signer_options;
		$this->load->view('documents_view',$data);
	}
	
	//LOAD ESIGN_DOCS REPORT
	function load_esign_docs_report(){
		$search_input = $_POST['document_search_input'];
		$signer_input = $_POST['signer_input'];
		$category_input = $_POST['category_input'];
		$document_input = $_POST['document_input'];
		$after_upload_date_filter = $_POST['after_upload_date_filter'];
		$before_upload_date_filter = $_POST['before_upload_date_filter'];
		$after_signed_date_filter = $_POST['after_signed_date_filter'];
		$before_signed_date_filter = $_POST['before_signed_date_filter'];
		
		
		$where = '';
		
		if(!empty($search_input))
		{
			$where = $where."AND signed_file.title LIKE '%".$search_input."%'";
		}
		else
		{
			if(!empty($signer_input))
			{
				$where = $where." AND recipient_person_id = '".$signer_input."'";
			}
			if(!empty($category_input))
			{
				$where = $where." AND unsigned_file.category = '".$category_input."' OR signed_file.category = '".$category_input."'";
			}
			if(!empty($document_input))
			{
				$where = $where." AND signed_file.title = '".$document_input."'";
			}
			if(!empty($after_upload_date_filter))
			{
				$where = $where." AND upload_datetime > '".date('Y-m-d',strtotime($after_upload_date_filter))."'";
			}
			if(!empty($before_upload_date_filter))
			{
				$where = $where." AND upload_datetime <= '".date('Y-m-d',strtotime($before_upload_date_filter))."'";
			}
			if(!empty($after_signed_date_filter))
			{
				$where = $where." AND signed_datetime > '".date('Y-m-d',strtotime($after_signed_date_filter))."'";
			}
			if(!empty($before_signed_date_filter))
			{
				$where = $where." AND signed_datetime <= '".date('Y-m-d',strtotime($before_signed_date_filter))."'";
			}
		}
		
		$where = substr($where,4);
		// echo $where;
		$esign_docs = db_select_esign_docs($where);
	
		$data["esign_docs"] = $esign_docs;
		$this->load->view('documents/esign_docs_report',$data);
	}
	
	//TAKE THE DOCUMENTS AND INFO AND UPLOADS IT TO THE DB
	function add_new_doc(){
		//GET USER
		$where = null;
		$where["person_id"] = $_POST["recipient_dropdown"];
		$recipient_user = db_select_user($where);
	
		//echo print_r($_FILES["unsigned_doc"]);
		
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- UNSIGNED
		$input_name = 'unsigned_doc';
		$name = str_replace(' ','_',$_FILES["unsigned_doc"]["name"]);
		$type = $_FILES["unsigned_doc"]["type"];
		$title = $_POST["doc_title"];
		$category = $_POST["doc_type_dropdown"];
		$local_path = $_FILES["unsigned_doc"]["tmp_name"];
		$server_path = '/edocuments/'; //ALL FOLDERS HAVE A production AND development SUBFOLDER
		$unsigned_secure_file = store_secure_ftp_file($input_name,$name,$type,$title,$category,$local_path,$server_path,"All","Access List");
		
		//SET FILE ACCESS PERMISSIONS FOR DOC -- UNSIGNED
		$access_permission = null;
		$access_permission["file_guid"] = $unsigned_secure_file["file_guid"];
		$access_permission["user_id"] = $recipient_user["id"];
		db_insert_file_access_permission($access_permission);
		
		//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNED
		$input_name = 'signed_doc';
		$name = str_replace(' ','_',$_FILES["signed_doc"]["name"]);
		$type = $_FILES["signed_doc"]["type"];
		$title = $_POST["doc_title"];
		$category = $_POST["doc_type_dropdown"];
		$local_path = $_FILES["signed_doc"]["tmp_name"];
		$server_path = '/edocuments/';
		$signed_secure_file = store_secure_ftp_file($input_name,$name,$type,$title,$category,$local_path,$server_path,"All","Access List");
		
		$server_path = '/edocuments_signed/';
		$backup_signed_secure_file = store_secure_ftp_file($input_name,$name,$type,$title,$category,$local_path,$server_path,"All","Access List");
		
		//SET FILE ACCESS PERMISSIONS FOR DOC -- SIGNED
		$access_permission = null;
		$access_permission["file_guid"] = $signed_secure_file["file_guid"];
		$access_permission["user_id"] = $recipient_user["id"];
		db_insert_file_access_permission($access_permission);
		
		//GET DATA FOR ESIGN_DOC
		date_default_timezone_set('America/Denver');
		$entry_datetime = date("Y-m-d H:i:s");
		
		//SET DISCLAIMER
		$esign_disclaimer = "Pursuant to the Uniform Electronic Transactions Act, Utah Code § 46-4-101 et. seq., electronic documents and signatures have the same legal effect as non-electronic documents and signatures. I hereby agree and acknowledge that electronic mail, electronic forms, records, photocopies, and /or facsimile copies of the documents submitted through FleetSmarts.Net are valid and enforceable as the original. I further agree and understand that by typing my name as an electronic signature, it is acknowledged and understood that it constitutes an acceptance of all terms and conditions of this Agreement and such Agreement is legally binding and enforceable.";
		
		//SET STATEMENT
		$esign_statement = "I understand that by typing my name, I am creating an electronic signature and confirming that I acknowlege the terms of the agreement associated with this signature. I also understand that my electronic signature is as legally binding as an ink signature.";
		
		//INSERT NEW ESIGN_DOC
		$edoc = null;
		$edoc["recipient_person_id"] = $_POST["recipient_dropdown"];
		$edoc["recipient_user_id"] = $recipient_user["id"];
		$edoc["upload_datetime"] = $entry_datetime;
		$edoc["unsigned_doc_guid"] = $unsigned_secure_file["file_guid"];
		$edoc["signed_doc_guid"] = $signed_secure_file["file_guid"];
		$edoc["explanation_link"] = $_POST["explanation_link"];
		$edoc["esign_disclaimer"] = $esign_disclaimer;
		$edoc["esign_statement"] = $esign_statement;
		db_insert_esign_doc($edoc);
		
		//echo "<br>Success!<br>";
		
		//RELOAD E DOCUMENTS TAB
		redirect(base_url("index.php/documents"));
	}
	
	//CALL FROM THE BROWSER TO DOWNLOAD A SECURE_FILE
	function download_file($file_guid){
		get_secure_ftp_file($file_guid);
	}
	
	function test(){
		$CI =& get_instance();
	
		//SET UP CONNECTION TO FTP SERVER
		$CI->load->library('ftp');

		$config['hostname'] = 'jellyfish.arvixe.com';
		$config['username'] = 'covax13';
		$config['password'] = 'retret13';
		$config['debug']	= TRUE;
		
		$CI->ftp->connect($config);
		
		//GET LIST OF FILE IN THE PUBLIC FOLDER
		$public_folder = "/public_html/temp_files_for_download/".ENVIRONMENT;
		$files = $CI->ftp->list_files($public_folder);
		
		//MOVE ANY LEFT OVER FILES IN THE PUBLIC FOLDER BACK TO THE PRIVATE FOLDER
		foreach($files as $file)
		{
			echo $file."<br>";
			//GET SECURE FILE
			$this_file = null;
			$where = null;
			$where["name"] = $file;
			$this_file = db_select_secure_file($where);
			
			if(!empty($this_file))
			{
				$full_path = $this_file["server_path"].$this_file["name"];
		
				//MOVE FILE BACK FROM PUBLICLY ACCESSIBLE FOLDER TO NON ACCESSABLE FOLDER
				$CI->ftp->move('/public_html/temp_files_for_download'.'/'.ENVIRONMENT.'/'.$this_file["name"], $full_path);
				echo "moved<br>";
			}
		}
	}
	
	function load_driver_information_sheet(){
		
	}
	
	function generate_documents_page(){
		
		$where = null;
		$where['role'] = 'Client';
		$drivers = db_select_persons($where);
		
		$driver_options = array();
		foreach($drivers as $driver){
			$driver_options[$driver['id']] = $driver['f_name']." ".$driver['l_name'];
		}
		
		$where = null;
		$where['type'] = 'Carrier';
		$companies = db_select_companys($where);
		
		$data['companies'] = $companies;
		$data['driver_options'] = $driver_options;
		$data['title'] = "Documents";
		$data['tab'] = 'Documents';
		$this->load->view("generate_documents_view",$data);
	}
	
	function generate_document(){
		date_default_timezone_set('America/Denver');
		
		$document_id = $_POST['documentSelect'];
		$driver_id = $_POST['driverSelect'];
		$company_id = $_POST['companySelect'];
		
		$where = null;
		$where['id'] = $driver_id;
		$driver = db_select_person($where);
		
		$where = null;
		$where['person_id'] = $driver['id'];
		$drivercompany = db_select_company($where);
		
		$where = null;
		$where['id'] = $company_id;
		$company = db_select_company($where);
		
		$where = null;
		$where['company_id'] = $drivercompany['id'];
		$client = db_select_client($where);
		
		$fullname = $driver['f_name']." ".$driver['l_name'];
		$initials = strtolower(substr($driver['f_name'],0,1).substr($driver['l_name'],0,1));
		
		if(!is_null(strtotime($driver['date_of_birth']))){
			$data['dob'] = date("F j, Y",strtotime($driver['date_of_birth']));
		}else{
			$data['dob'] = '';
		}
		
		$data['date'] = date("F j, Y");
		$data['initials'] = $initials;
		$data['fullname'] = $fullname;
		$data['drivercompany'] = $drivercompany;
		$data['client'] = $client;
		$data['driver'] = $driver;
		$data['company'] = $company;
		
		if($document_id == 1){
			$this->load->view('documents/driver_information_sheet',$data);
			$this->load->view('documents/lobos_summary_contract',$data);
			$this->load->view('documents/recommendation_for_legal_counsel',$data);
			$this->load->view('documents/lobos_service_agreement',$data);
			$this->load->view('documents/limited_power_of_attorney',$data);
			$this->load->view('documents/utah_registration',$data);
			$this->load->view('documents/statement_of_authority',$data);
			$this->load->view('documents/ucr_registration_form',$data);
			$this->load->view('documents/op1',$data);
			$this->load->view('documents/ss4',$data);
			$this->load->view('documents/mcs150',$data);
			$this->load->view('documents/oregon_application',$data);
			$this->load->view('documents/tmt1',$data);
			$this->load->view('documents/owner_operator_agreement',$data);
			$this->load->view('documents/addendum_a',$data);
			$this->load->view('documents/addendum_b',$data);
			$this->load->view('documents/arrowhead_limited_power_of_attorney',$data);
			$this->load->view('documents/limited_agency_agreement',$data);
			$this->load->view('documents/receipt_of_bylaws',$data);
			$this->load->view('documents/collect_manage_funds',$data);
			$this->load->view('documents/umcc_authorization',$data);
		}
	}
}