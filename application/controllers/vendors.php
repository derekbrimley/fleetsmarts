<?php		


	
class Vendors extends MY_Controller 
{

	function index($mode,$vendor_id)
	{	
	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Driver')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		
		//GET VENDORS ACCORDING TO STATUS
		$where = null;
		$where["type"] = "customer-vendor";
		$where["company_status"] = "Active";
		$vendors = db_select_companys($where,"company_side_bar_name");
		
		$this_vendor_where['ID'] = $vendor_id;
		$this_vendor = db_select_company($this_vendor_where);
		
		//GET THIS VENDOR'S SPARK CC ACCOUNT
		$spark_cc_account = null;
		if(!empty($this_vendor["spark_cc_number"]))
		{
			//GET SPARK CC ACCOUNT FOR THIS VENDOR
			$where = null;
			$where["company_id"] = $this_vendor["id"];
			$where["account_group"] = "Spark CC";
			$spark_cc_account = db_select_account($where);
			
		}
		
		$data['spark_cc_account'] = $spark_cc_account;
		@$data['this_vendor'] = $this_vendor;
		$data['vendors'] = $vendors;
		$data['mode'] = $mode;
		$data['tab'] = 'Vendors';
		$data['title'] = "Vendors";
		$this->load->view('vendors_view',$data);
	
	}//END INDEX

	//SAVE VENDOR EDIT
	function save_vendor()
	{
		//UPDATE VENDOR
		$vendor["company_name"] = $_POST["company_name"];
		$vendor["company_side_bar_name"] = $_POST["company_short_name"];
		$vendor["address"] = $_POST["address"];
		$vendor["city"] = $_POST["city"];
		$vendor["state"] = $_POST["state"];
		$vendor["zip"] = $_POST["zip"];
		$vendor["contact"] = $_POST["contact"];
		$vendor["company_email"] = $_POST["email"];
		$vendor["company_phone"] = $_POST["phone"];
		$vendor["company_fax"] = $_POST["fax"];
		$vendor["company_notes"] = $_POST["notes"];
		
		$where["id"] = $_POST['id'];
		db_update_company($vendor,$where);
		
		
		redirect(base_url('index.php/vendors/index/view/'.$_POST['id']));
	}
	
	//ADD NEW VENDOR TO DB
	function add_vendor()
	{
		//CREATE PERSON AND USER IF THIS IS FOR STAFF
		
		//CREATE NEW VENDOR
		$vendor['company_name'] = $_POST["add_company_name"];
		$vendor['company_side_bar_name'] = $_POST["add_company_short_name"];
		$vendor['type'] = "Vendor";
		$vendor['address'] = $_POST["add_address"];
		$vendor['city'] = $_POST["add_city"];
		$vendor['state'] = $_POST["add_state"];
		$vendor['zip'] = $_POST["add_zip"];
		$vendor['contact'] = $_POST["add_contact"];
		$vendor['company_email'] = $_POST["add_email"];
		$vendor['company_phone'] = $_POST["add_phone"];
		$vendor['company_fax'] = $_POST["add_fax"];
		$vendor['company_notes'] = $_POST["add_notes"];
	
		db_insert_company($vendor);
		
		$new_vendor = db_select_company($vendor);
		
		//CREATE PAY ACCOUNT FOR STAFF
		$account = null;
		$account['company_id'] = $new_vendor["id"];
		$account['account_type'] = "Business";
		$account['category'] = "Vendor";
		$account['vendor_id'] = $new_vendor["id"];
		$account['account_status'] = "Active";
		$account['account_name'] = $_POST["add_company_name"];
		db_insert_account($account);
		
		redirect(base_url('index.php/vendors/index/view/'.$new_vendor['id']));
		
	}
		
	//ADD CREDIT CARD ACCOUNT TO VENDOR
	function add_cc()
	{
		$vendor_id = $_POST["vendor_id"];
		$cc_number = $_POST["cc_number"];
		
		//GET VENDOR
		$where = null;
		$where["id"] = $vendor_id;
		$vendor_company = db_select_company($where);
		
		//UPDATE VENDOR WITH CC NUMBER
		$update_vendor["spark_cc_number"] = $cc_number;
		
		$where = null;
		$where["id"] = $vendor_id;
		db_update_company($update_vendor,$where);
		
		//CREATE NEW TRACK EXPENSE ACCOUNT FOR VENDOR
		$cc_account["company_id"] = $vendor_id;
		$cc_account["account_type"] = "Business";
		$cc_account["category"] = "Track Expense";
		$cc_account["account_group"] = "Spark CC";
		$cc_account["account_status"] = "Active";
		$cc_account["account_name"] = "Spark CC (".$vendor_company["company_side_bar_name"].")";
		db_insert_account($cc_account);
		
		
		redirect(base_url('index.php/vendors/index/view/'.$vendor_id));
	}
}