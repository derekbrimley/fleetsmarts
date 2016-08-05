<?php		


	
class Customers extends MY_Controller 
{

	function index($customer_status,$mode,$customer_id)
	{	
	
		//NO DRIVERS ALLOWED
		if ($this->session->userdata('role') == 'Driver')
		{
			redirect("http://clients.fleetsmarts.net");
		}
		
		$customer_status = urldecode($customer_status);
		
		//GET CUSTOMERS ACCORDING TO STATUS
		if ($customer_status == "All")
		{
			$customers_where["1"] = true;
		}
		else
		{
			$customers_where["status"] = $customer_status;
		}
		$customers = db_select_customers($customers_where,"customer_name");
		
		$this_customer_where['ID'] = $customer_id;
		$this_customer = db_select_customer($this_customer_where);
		
		//GET ALL CUSTOMERS FOR VALIDATION LIST
		$all_customers_where[1] = true;
		$all_customers = db_select_customers($all_customers_where,"customer_name");
		
		@$data['this_customer'] = $this_customer;
		$data['all_customers'] = $all_customers;
		$data['customers'] = $customers;
		$data['customer_status'] = $customer_status;
		$data['mode'] = $mode;
		$data['tab'] = 'Customers';
		$data['title'] = "Customers";
		$this->load->view('customers_view',$data);
	
	}//END INDEX

	//CUSTOMER STATUS DROPDOWN WAS CHANGED
	function select_customer_status()
	{
		$customer_status = $_POST["customer_status_dropdown"];
		$mode = $_POST["mode"];
		redirect(base_url("index.php/customers/index/$customer_status/none/none/0"));
	}
	
	//SAVE CUSTOMER EDIT
	function save_customer()
	{
		//UPDATE CUSTOMER
		$customer["customer_name"] = $_POST["customer_name"];
		$customer["form_of_payment"] = $_POST["form_of_payment"];
		$customer["address"] = $_POST["address"];
		$customer["city"] = $_POST["city"];
		$customer["state"] = $_POST["state"];
		$customer["status"] = $_POST["status"];
		$customer["zip"] = $_POST["zip"];
		$customer["contact"] = $_POST["contact"];
		$customer["phone"] = $_POST["phone"];
		$customer["fax"] = $_POST["fax"];
		$customer["email"] = $_POST["email"];
		$customer["mc_number"] = $_POST["mc_number"];
		$customer["notes"] = $_POST["notes"];
		
		$where["ID"] = $_POST['id'];
		db_update_customer($customer,$where);
		
		
		redirect(base_url('index.php/customers/index/'.$customer["status"].'/view/'.$_POST['id']));
	}
	
	//ADD NEW CUSTOMER TO DB
	function add_customer()
	{
		//CREATE NEW CUSTOMER
		$customer['customer_name'] = $_POST["add_customer_name"];
		$customer['address'] = $_POST["add_address"];
		$customer['city'] = $_POST["add_city"];
		$customer['state'] = $_POST["add_state"];
		$customer['zip'] = $_POST["add_zip"];
		$customer['contact'] = $_POST["add_contact"];
		$customer['phone'] = $_POST["add_phone"];
		$customer['fax'] = $_POST["add_fax"];
		$customer['email'] = $_POST["add_email"];
		$customer['mc_number'] = $_POST["add_mc_number"];
		$customer['form_of_payment'] = $_POST["add_form_of_payment"];
		$customer['status'] = $_POST["add_status"];
		$customer['notes'] = $_POST["add_notes"];
	
		db_insert_customer($customer);
		
		$customer = null;
		$customer['customer_name'] = $_POST["add_customer_name"];
		$customer['address'] = $_POST["add_address"];
		$customer['city'] = $_POST["add_city"];
		$customer['state'] = $_POST["add_state"];
		$customer['zip'] = $_POST["add_zip"];
		$customer['contact'] = $_POST["add_contact"];
		$customer['phone'] = $_POST["add_phone"];
		$customer['email'] = $_POST["add_email"];
		$customer['form_of_payment'] = $_POST["add_form_of_payment"];
		$customer['status'] = $_POST["add_status"];
		
		$new_customer = db_select_customer($customer);
		
		redirect(base_url('index.php/customers/index/'.$new_customer["status"].'/view/'.$new_customer['id']));
		
	}
		
}