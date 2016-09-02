<?php //<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> ?>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOka6AH6q31NseA0_TtBFfYd-PCtwH1y0">
</script>
<script>
	$(document).ready(function(){
		// setInterval(function() 
		// {
			// check_for_new_notifications();
		// }, 5*1000);//1000 = 1 second
  
	});//end document ready
	
	// request permission on page load
	document.addEventListener('DOMContentLoaded', function () {
		 if (Notification.permission !== "granted")
		 {
			Notification.requestPermission();
		 }
	});

	
	function check_for_new_notifications()
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $("#notification_script_div");
			
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&user_id="+<?=$this->session->userdata('user_id')?>;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/home/check_for_new_notifications")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//this_div.html(response);
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					//alert(response);
					alert("500 error!");
					//$("#main_content").html(response);
				}
			}
		});//END AJAX
	}
	
	function display_notification(title,text,img,action_url) 
	{
	  //alert('hi');
	  if (!Notification) 
		{
			//alert('Desktop notifications not available in your browser. Try Chromium.'); 
			alert(title);
			return;
		}
		//alert('yo');
		if (Notification.permission !== "granted")
		{
			//alert('sup');
			Notification.requestPermission();
		}
		else 
		{
			var notification = new Notification(title, 
			{
				icon: img,
				body: text,
			});

			notification.onclick = function () 
			{
				window.open(action_url);      
			};
		
		}
	}
	
	function notifyMe() 
	{
	  //alert('hi');
	  if (!Notification) 
		{
			alert('Desktop notifications not available in your browser. Try Chromium.'); 
			return;
		}
		//alert('yo');
		if (Notification.permission !== "granted")
		{
			//alert('sup');
			Notification.requestPermission();
		}
		else 
		{
			var notification = new Notification('Login Verification', 
			{
				icon: 'http://fleetsmarts.net/images/time_keep_logo.png',
				body: "Please click here to verify that you are still working.",
			});

			notification.onclick = function () 
			{
				window.open("http://fleetsmarts.net/index.php/time_clock");      
			};
		
		}
	}
</script>
<?php
	//GET CLOCKED IN STATUS
	
	//GET LAST PUNCH
	$where = null;
	$where["user_id"] = $this->session->userdata('user_id');
	$last_punch = db_select_time_punch($where,"datetime");
	
	//GET USER
	$where = null;
	$where["id"] = $this->session->userdata('user_id');
	$user = db_select_user($where);
	
?>
<div id="main_menu_box" style="height: 70px; margin: 0 auto;  position:relative; background: #DCDCDC;">
	<div id="top_bar" style="height:29px; background:#2D2D2D;">
		<!-- JRR Tolkien - I don't know half of you half as well as I should like; and I like less than half of you half as well as you deserve !-->
		<!-- King of Gondor - If by my life or death I can protect you, I will. You have my sword... !-->
		<!-- Legolas - ... And you have my bow!-->
		<div id="notification_script_div">
			<!-- AJAX GOES HERE !-->
		</div>
		<?php if(user_has_permission('can clock in remotely')):?>
			<a href="<?php echo base_url("index.php/time_clock")?>" style="float:left;" title="TimeKeep <?=$user["pin"]?>"><img style="height:20px; position:relative; left:10px; top:4px;" src="/images/time_keep_logo.png"/></a>
		<?php endif;?>
		<a href ="<?php echo base_url("index.php/home")?>" style="text-decoration:none;" id="title_version" title="Happiness can be found, even in the darkest of times, if one only remembers to turn on the light.">
			<?php if($last_punch["in_out"] == "In"):?>
				<img src="/images/green_dot.png" style="height:14px; position:relative; top:2px;" title="Clocked In - <?=date("m/d H:i:s",strtotime($last_punch["datetime"]))?>"/>
			<?php else:?>
				<img src="/images/blinking_red_dot.gif" style="height:14px; position:relative; top:2px;" title="Clocked Out - <?=date("m/d H:i:s",strtotime($last_punch["datetime"]))?>"/>
			<?php endif;?>
			Fleetsmarts v2.0
		</a>
		<div id="username_div">
			<span id="welcome_label">Welcome </span>
			<span id="logout" title="<?=$this->session->userdata('user_id')?>"><?=$this->session->userdata('f_name') ?></span>
			<span id="divider"> | </span>
			<a id="logout_linkbutton" class="logout" href="<?=base_url("index.php/home/logout")?>">Log Out</a>
		</div>
	</div>
	<?php
		$this_fm = $this->session->userdata('person_id');
		if($this->session->userdata('role') != "Fleet Manager")
		{
			$this_fm = "all";
		}
		if($this->session->userdata('person_id') == 26)
		{
			$this_fm = "19";
		}
	?>
	<div style="width:1300px; margin:auto;">
		<div id="outer_main_menu" style=" padding-left:30px; color:White; font-family:arial; font-size:15px; font-weight:bold; position:absolute; bottom:0;" >
			<div id="main_menu" style="min-width:1130px; max-height:30px;">
				<!--<a href ="<?php //echo base_url("index.php/home")?>" style="<?php if($tab == 'Home'){echo "background-color:white; color:#DD4B39;";} ?>" >Home</a> !-->
				<a href ="<?=base_url("index.php/people")?>" style="<?php if($tab == 'Contacts'){echo "background-color:white; color:#DD4B39;";} ?>">Contacts</a>
				<a href ="<?=base_url("index.php/equipment")?>" style="<?php if($tab == 'Equipment'){echo "background-color:white; color:#DD4B39;";} ?>" >Equipment</a>
				<!--<a href ="<?php //echo base_url("index.php/vendors/index/Good/none/0")?>" style="<?php //if($tab == 'Vendors'){echo "background-color:white; color:#DD4B39;";} ?>">Vendors</a> !-->
				<!--<a href ="<?php //echo base_url("index.php/customers/index/Good/none/0")?>" style="<?php //if($tab == 'Customers'){echo "background-color:white; color:#DD4B39;";} ?>">Customers</a> !-->
				<?php if(user_has_permission("view business accounts")): ?>
					<a href ="<?=base_url("index.php/accounts")?>" style="<?php if($tab == 'Accounts'){echo "background-color:white; color:#DD4B39;";} ?>">Accounts</a>
				<?php endif ?>
				<a href ="<?=base_url("index.php/purchase_orders")?>" style="min-width:100px; <?php if($tab == 'POs'){echo "background-color:white; color:#DD4B39;";} ?>">&nbsp;&nbsp;POs&nbsp;&nbsp;</a>
				<?php if(user_has_permission("view transactions tab")): ?>
					<a href ="<?=base_url("index.php/expenses")?>" style="<?php if($tab == 'Transactions'){echo "background-color:white; color:#DD4B39;";} ?>">Transactions</a>
				<?php endif ?>
				<a href ="<?=base_url("index.php/receipts")?>" style="<?php if($tab == 'Receipts'){echo "background-color:white; color:#DD4B39;";} ?>">Receipts</a>
				<a href ="<?=base_url("index.php/tickets")?>" style="<?php if($tab == 'Tickets'){echo "background-color:white; color:#DD4B39;";} ?>">Tickets</a>
				<?php if(user_has_permission("view invoice tab")): ?>
					<a href ="<?=base_url("index.php/invoices")?>" style="<?php if($tab == 'Invoices'){echo "background-color:white; color:#DD4B39;";} ?>">Invoices</a>
				<?php endif ?>
				<a href ="<?=base_url("index.php/bills")?>" style="<?php if($tab == 'Bills'){echo "background-color:white; color:#DD4B39;";} ?>">Bills</a>
				<a href ="<?=base_url("index.php/loads")?>" style="<?php if($tab == 'Loads'){echo "background-color:white; color:#DD4B39;";} ?>">Loads</a>
				<a href ="<?=base_url("index.php/billing")?>" style="<?php if($tab == 'Billing'){echo "background-color:white; color:#DD4B39;";} ?>">Billing</a>
				<!--<a href ="<?php //echo base_url("index.php/prelogs")?>" style="<?php //if($tab == 'Pre-Logs'){echo "background-color:white; color:#DD4B39;";} ?>">Pre-Logs</a> !-->
				<a href ="<?=base_url("index.php/logs")?>" style="<?php if($tab == 'Logs'){echo "background-color:white; color:#DD4B39;";} ?>">Logs</a>
				<a href ="<?=base_url("index.php/settlements")?>" style="<?php if($tab == 'Statements'){echo "background-color:white; color:#DD4B39;";} ?>">Statements</a>
				<a href ="<?=base_url("index.php/performance")?>" style="<?php if($tab == 'Performance'){echo "background-color:white; color:#DD4B39;";} ?>">Performance</a>
				<!--<a href ="<?php //echo base_url("index.php/commissions")?>" style="<?php //if($tab == 'Commissions'){echo "background-color:white; color:#DD4B39;";} ?>">Commissions</a> !-->
				<a href ="<?=base_url("index.php/todo")?>" style="<?php if($tab == 'ToDo'){echo "background-color:white; color:#DD4B39;";} ?>">ToDo</a>
				<!-- <a href ="<?php //echo base_url("index.php/documents")?>" style="<?php //if($tab == 'Documents'){echo "background-color:white; color:#DD4B39;";} ?>">Documents</a> !-->
				<a href ="<?=base_url("index.php/trippak")?>" style="<?php if($tab == 'Trippak'){echo "background-color:white; color:#DD4B39;";} ?>">Trippak</a>
				<a href ="<?=base_url("index.php/reports")?>" style="<?php if($tab == 'Reports'){echo "background-color:white; color:#DD4B39;";} ?>">Reports</a>
				<?php if(user_has_permission("view settings tab")): ?>
					<a href ="<?=base_url("index.php/settings")?>" style="<?php if($tab == 'Settings'){echo "background-color:white; color:#DD4B39;";} ?>">Settings</a>
				<?php endif ?>
				<!--<a href ="<?php //echo base_url("index.php/driver_logs/index/none")?>" style="<?php //if($tab == 'Driver Logs'){echo "background-color:white; color:#DD4B39;";} ?>">Driver Logs</a> !-->
				<!-- <a href ="<? //echo base_url("index.php/schedule/index/Driver/0/0")?>" style="<?php //if($tab == 'Schedule'){echo "background-color:white; color:#DD4B39;";} ?>">Schedule</a> !-->
				<!-- <a href ="">Expenses</a> !-->
				<!-- <a href ="<? //echo base_url("index.php/payroll/index/Drivers/none/0/0")?>" style="<?php //if($tab == 'Payroll'){echo "background-color:white; color:#DD4B39;";} ?>">Payroll</a> !-->
				<!-- <a href ="">Daily Log</a> !-->
				<!-- <a href ="">PreTrips</a> !-->
				<!-- <a href ="<? //echo base_url("index.php/equipment/index/truck/Active/none/0")?>" style="<?php //if($tab == 'Equipment'){echo "background-color:white; color:#DD4B39;";} ?>">Equipment</a> !-->
				<!-- <a href ="<? //echo base_url("index.php/staff/index/All/none/0")?>" style="<?php //if($tab == 'Staff'){echo "background-color:white; color:#DD4B39;";} ?>">Staff</a> !-->
				<!-- <a href ="">Vendors</a> !-->
				<!-- <a href ="<? //echo base_url("index.php/reports/index/none_selected/no_data")?>" style="<?php //if($tab == 'Reports'){echo "background-color:white; color:#DD4B39;";} ?>">Reports</a> !-->
				<!-- <a href ="<? //echo base_url("index.php/maps/index/live_view/0")?>" style="<?php //if($tab == 'Maps'){echo "background-color:white; color:#DD4B39;";} ?>">Maps</a> !-->
			</div>
		</div>
	</div>
</div>