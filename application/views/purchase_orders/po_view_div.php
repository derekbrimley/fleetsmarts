<script>
	$("#scrollable_content").height($("#body").height() - 165);
	
	$('#po_date').datepicker({ showAnim: 'blind' });
	
	//DIALOG: UPLOAD SIGNATURE DIALOG
	$( "#file_upload_dialog" ).dialog(
	{
		autoOpen: false,
		height: 200,
		width: 400,
		modal: true,
		buttons: 
		[
			{
				text: "Upload",
				click: function() 
				{
					//SUBMIT FORM
					$("#upload_file_form").submit();
					$( this ).dialog( "close" );
					setTimeout(function()
					{
						save_po()
					},900);
				},//end add load
			},
			{
				text: "Cancel",
				click: function() 
				{
					//RESIZE DIALOG BOX
					$( this ).dialog( "close" );
				}
			}
		],//end of buttons
		open: function()
		{
		},//end open function
		close: function() 
		{
		}
	});//end dialog form
	
	function category_clicked(cat_id,owner_id,category)
	{
		var isValid = true;
		
		//alert(cat_id);
		//HIDE ALL CIRCLES
		//$(".circle_category").hide();
		
		//SHOW SELECTED CIRCLE
		//$("#circle_"+cat_id).show();
		
		
		if(category == "ME - Lumper")
		{
			//VALIDATE ALL INFO IS FILLED OUT
			if($("#expense_amount").val() == 0)
			{
				isValid = false;
				alert('You must enter an Expense Amount before selecting ME - Lumper!');
			}
			
			if(!$("#po_date").val())
			{
				isValid = false;
				alert('You must enter a date before selecting ME - Lumper!');
			}
			
			if($("#po_client_id").val() == 'Select')
			{
				isValid = false;
				alert('You must select a Driver before selecting ME - Lumper!');
			}
			
			if($("#po_truck_id").val() == 'Select')
			{
				isValid = false;
				alert('You must select a Truck before selecting ME - Lumper!');
			}
			
			if(!$("#po_load_number").val())
			{
				isValid = false;
				alert('You must enter a Load Number before selecting ME - Lumper!');
			}
			
			if(!$("#po_gps").val())
			{
				isValid = false;
				alert('You must enter a Location before selecting ME - Lumper!');
			}
		}
		else if(category == "ME - Fuel")
		{
			//VALIDATE ALL INFO IS FILLED OUT
			if($("#expense_amount").val() == 0)
			{
				isValid = false;
				alert('You must enter an Expense Amount before selecting ME - Fuel!');
			}
			
			if(!$("#po_date").val())
			{
				isValid = false;
				alert('You must enter a date before selecting ME - Fuel!');
			}
			
			if($("#po_client_id").val() == 'Select')
			{
				isValid = false;
				alert('You must select a Driver before selecting ME - Fuel!');
			}
			else
			{
				if($("#hold_status").val() != 'No Hold')
				{
					isValid = false;
					alert('This Driver is on HOLD!! Check the Driver Hold Report in the Reports tab for details.');
				}
			}
			
			if($("#po_truck_id").val() == 'Select')
			{
				isValid = false;
				alert('You must select a Truck before selecting ME - Fuel!');
			}
			
			if(!$("#po_load_number").val())
			{
				isValid = false;
				alert('You must enter a Load Number before selecting ME - Fuel! This fuel stop should be on the load plan found in the Loads tab. If it is not, you should add it.');
			}
			
			if(!$("#po_gps").val())
			{
				isValid = false;
				alert('You must enter a Location before selecting ME - Fuel!');
			}
			
			alert('If there are TWO DRIVERS in the truck, you must verify that BOTH driverS have no holds. Change the driver on the PO to the codriver and reselect the ME - Fuel category to document in the PO notes that both drivers are not on hold.');
		}
		else if(category == "PA")
		{
			if($("#po_client_id").val() == 'Select')
			{
				isValid = false;
				alert('You must select a Driver before selecting PA!');
			}
			else
			{
				if($("#hold_status").val() != 'No Hold')
				{
					isValid = false;
					alert('This Driver is on HOLD!! Check the Driver Hold Report in the Reports tab for details.');
				}
			}
		}
		
		if(isValid)
		{
			$("#po_category").val(category);
			$("#owner_id").val(owner_id);
			save_po();
		}
	}
	
	function open_lumper_dialog()
	{
		var id = $("#po_id").val();
		var this_div = $('#lumper_dialog');
	
		//SHOW LOADING ICON
		this_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:20px; margin-top:30px;" /></div>');
		this_div.dialog('open');
		
		//alert('save po');
		//LOAD PO FILTER DIV
		//var dataString = $("#po_form").serialize();
		var dataString = "&po_id="+id;
		
		$.ajax({
			url: "<?= base_url("index.php/purchase_orders/open_lumper_dialog")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					//alert(response);
					
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	function validate_lumper_load_number()
	{
		var isValid = true;
		
		if($("#lumper_client_id").val() == 'Select')
		{
			isValid = false;
			alert("Driver must be selected!");
		}
		
		if($("#lumper_truck_id").val() == 'Select')
		{
			isValid = false;
			alert("Truck must be selected!");
		}
		
		if(!$("#lumper_load_number").val())
		{
			isValid = false;
			alert("Load must be entered!");
		}
		
		
		if(isValid)
		{
			var id = $("#po_id").val();
			var this_div = $('#lumper_ajax_response_div');
			
			//alert('save po');
			//LOAD PO FILTER DIV
			//var dataString = $("#po_form").serialize();
			var dataString = "&load_number="+$("#lumper_load_number").val()+"&po_id="+id;
			
			$.ajax({
				url: "<?= base_url("index.php/purchase_orders/validate_lumper_load_number")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						//alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function open_pa_dialog()//NOT USING ANYMORE
	{
		var id = $("#po_id").val();
		var this_div = $('#pa_dialog');
	
		//SHOW LOADING ICON
		this_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:20px; margin-top:30px;" /></div>');
		this_div.dialog('open');
		
		//alert('save po');
		//LOAD PO FILTER DIV
		//var dataString = $("#po_form").serialize();
		var dataString = "&po_id="+id;
		
		$.ajax({
			url: "<?= base_url("index.php/purchase_orders/open_lumper_dialog")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					//alert(response);
					
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	function validate_pa_dialog()//NOT USING ANYMORE
	{
		var isValid = true;
		
		if($("#lumper_client_id").val() == 'Select')
		{
			isValid = false;
			alert("Driver must be selected!");
		}
		
		if($("#lumper_truck_id").val() == 'Select')
		{
			isValid = false;
			alert("Truck must be selected!");
		}
		
		if(!$("#lumper_load_number").val())
		{
			isValid = false;
			alert("Load must be entered!");
		}
		
		
		if(isValid)
		{
			var id = $("#po_id").val();
			var this_div = $('#lumper_ajax_response_div');
			
			//alert('save po');
			//LOAD PO FILTER DIV
			//var dataString = $("#po_form").serialize();
			var dataString = "&load_number="+$("#lumper_load_number").val()+"&po_id="+id;
			
			$.ajax({
				url: "<?= base_url("index.php/purchase_orders/validate_pa_dialog")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						//alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function save_po()
	{
		if(!(load_po_report_ajax_call===undefined))
		{
			//alert('abort');
			load_po_report_ajax_call.abort();
		}
		
		var id = $("#po_id").val();
	
		//SHOW LOADING ICON
		$("#save_icon").hide();
		$("#filter_loading_icon").show();
		
		//alert('save po');
		//LOAD PO FILTER DIV
		var dataString = $("#po_form").serialize();
		
		var this_div = $('#main_content');
		
		$.ajax({
			url: "<?= base_url("index.php/purchase_orders/save_po")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//HIDE LOADING ICON SHOW SAVE ICON
					$("#filter_loading_icon").hide();
					$("#save_icon").show();
					
					//this_div.html(response);
					
					load_po_view(id);//THIS RELOADS THE PO DETAILS VIEW AFTER EACH SAVE
					
					//load_po_report()
					//alert(response);
					
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	function delete_po()
	{
		if(confirm('Are you sure you want to delete this PO?'))
		{
			var id = $("#po_id").val();
		
			//SHOW LOADING ICON
			$("#save_icon").hide();
			$("#filter_loading_icon").show();
			
			//alert('save po');
			//LOAD PO FILTER DIV
			var dataString = $("#po_form").serialize();
			
			var this_div = $('#main_content');
			
			$.ajax({
				url: "<?= base_url("index.php/purchase_orders/delete_po/")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						
						//SHOW LOADING ICON
						//$("#filter_loading_icon").hide();
						//$("#refresh_logs").show();
						
						//this_div.html(response);
						load_po_report();
						//alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function approve_po()
	{
		if(confirm('Are you sure you want to sign?'))
		{
			var id = $("#po_id").val();
		
			//SHOW LOADING ICON
			$("#save_icon").hide();
			$("#filter_loading_icon").show();
			
			//alert('save po');
			//LOAD PO FILTER DIV
			var dataString = $("#po_form").serialize();
			
			var this_div = $('#main_content');
			
			$.ajax({
				url: "<?= base_url("index.php/purchase_orders/approve_po/")?>/"+id, // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						
						//SHOW LOADING ICON
						//$("#filter_loading_icon").hide();
						//$("#refresh_logs").show();
						
						//this_div.html(response);
						load_po_view(id)
						//load_po_report()
						//alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function deny_po()
	{
		if(confirm('Are you sure you want to deny the PO Request?'))
		{
			var id = $("#po_id").val();
		
			//SHOW LOADING ICON
			$("#save_icon").hide();
			$("#filter_loading_icon").show();
			
			//alert('save po');
			//LOAD PO FILTER DIV
			var dataString = $("#po_form").serialize();
			
			var this_div = $('#main_content');
			
			$.ajax({
				url: "<?= base_url("index.php/purchase_orders/deny_po/")?>/"+id, // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						
						//SHOW LOADING ICON
						//$("#filter_loading_icon").hide();
						//$("#refresh_logs").show();
						
						//this_div.html(response);
						load_po_view(id)
						//load_po_report()
						//alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function email_po()
	{
		if(confirm('Are you sure you want to make this PO Request?'))
		{
			var id = $("#po_id").val();
		
			//SHOW LOADING ICON
			$("#save_icon").hide();
			$("#filter_loading_icon").show();
			
			//alert('save po');
			//LOAD PO FILTER DIV
			var dataString = $("#po_form").serialize();
			
			var this_div = $('#main_content');
			
			$.ajax({
				url: "<?= base_url("index.php/purchase_orders/email_po/")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						
						//SHOW LOADING ICON
						//$("#filter_loading_icon").hide();
						//$("#refresh_logs").show();
						
						//this_div.html(response);
						load_po_view(id);
						alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function open_file_upload(po_id)
	{
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#file_upload_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#file_upload_dialog').dialog('open')
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'po_id='+po_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/purchase_orders/load_po_file_upload")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: target_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					target_div.html(response);
					
					
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error!")
				}
			}
		});//END AJAX
	}
	
	function auto_fill_po_location()//FOR EDIT GP
	{
		//alert($("#edit_gp_gps_"+gp_id).val());
		fill_in_locations("po_location",$("#po_gps").val());
		//if($("#po_gps").val())
		//{
			 save_po();
			//$("#po_gps").hide()
			//$("#po_location").show()
		//}
	}
	
	function show_po_gps()
	{
		$("#po_location").hide()
		$("#po_gps").show()
		$("#po_gps").focus()
		$("#po_gps").select();
		
	}
	
	// function driver_selected()
	// {
		// var category = $("#po_category").val();
		
		// var isValid = true;
		
		// if(category == "PA")
		// {
			// if($("#po_client_id").val() == 'Select')
			// {
				// isValid = false;
				// alert('You must select a Driver before selecting PA!');
			// }
			// else
			// {
				// if($("#hold_status").val() != 'No Hold')
				// {
					// isValid = false;
					// alert('This Driver is on HOLD!! Check to the Driver Hold Report in the Reports tab for details.');
				// }
			// }
		// }
		
		// if(isValid)
		// {
			// save_po();
		// }
	// }
	
</script>
<style>
	
	.po_input
	{
		width:200px;
		height:30px;
		text-align:center;
		font-size:14px;
		border: 0px!important;
	}
	
	.underline
	{
		border-bottom: 2px inset black;
		padding:3px;
	}
	
	.category:hover
	{
		background:#EFEFEF!important;
	}
	
	.circle_category
	{
		position:absolute;
		margin-left:-20px;
		margin-top:-5px;
		height:35px;
		width:165px;
	}
</style>
<?php
	//GET LOAD
	$where = null;
	$where["id"] = $po["load_id"];
	$load = db_select_load($where);
	
	$load_text = "";
	if(!empty($load))
	{
		$load_text = $load["customer_load_number"];
	}
	
	$hold_report = null;
	if(!empty($po["client_id"]))
	{
		$hold_report = get_hold_report($po["client_id"]);
	}
?>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px; text-align:center;">
		<div title="Save" id="save_icon" onclick="save_po()" style="margin-right:25px; width:25px; float:right;">
			<img src="/images/save.png" style="float:right; cursor:pointer; height:20px; position:relative; top: 5px;" />
		</div>
		<div id="filter_loading_icon" name="filter_loading_icon" style="margin-right:25px; width:25px; float:right; display:none;">
			<img src="/images/loading.gif" style="float:right; height:20px; padding-top:5px;" />
		</div>
		<div title="Attach" id="attach_icon" onclick="open_file_upload('<?=$po["id"]?>')" style="margin-right:25px; width:25px; float:right;">
			<img src="/images/paper_clip2.png" style="float:right; cursor:pointer; height:20px; width:11px; position:relative; top: 5px;" />
		</div>
		<?php if($po_is_complete): ?>
			<div title="Send PO Request <?=$po["email_datetime"]?>" id="email_icon" name="email_icon" style="margin-right:15px; width:25px; float:right;" onclick="email_po()">
				<img src="/images/email.png" style="float:right; cursor:pointer; height:20px; padding-top:5px;" />
			</div>
		<?php else:?>
			<div title="Incomplete" id="email_sent_icon" name="email_sent_icon" style="margin-right:15px; width:25px; float:right;" onclick="alert('The PO is incomplete!')">
				<img src="/images/email_sent.png" style="float:right; cursor:pointer; height:20px; padding-top:5px;" />
			</div>
		<?php endif;?>
		<?php if($this_person_id == $po["issuer_id"] || $this_person_id == $po["approved_by_id"]): ?>
			<div title="Delete" id="delete_icon" onclick="delete_po()" style="margin-right:25px; float:right;">
				<img src="/images/trash.png" style="float:right; cursor:pointer; height:20px; position:relative; top: 5px;" />
			</div>
		<?php endif;?>
		<div title="Back" id="back_icon" name="back_icon" onclick="load_po_report()" style="margin-right:25px; width:25px; float:right;">
			<img style="float:right; cursor:pointer; height:20px; position:relative; top: 5px;" src="/images/back.png" />
		</div>
		<div style="float:left; margin-left:20px; font-weight:bold; cursor:pointer;" onclick="load_po_view('<?=$po["id"]?>')">
			Purchase Order <?=$po["id"]?>
		</div>
		<span style="width:200px; margin:auto; color:red;">
			<?=$po_status_text?>
		</div>
	</div>
</div>
<div id="scrollable_content" class="scrollable_div">
	<div style="padding:15px;">
		<?php $attributes = array('name'=>'po_form','id'=>'po_form')?>
		<?=form_open('expenses/save_po',$attributes);?>
			<input type="hidden" id="po_id" name="po_id" value="<?=$po["id"]?>" />
			<input type="hidden" id="hold_status" name="hold_status" value="<?=$hold_report["hold_status"]?>"/>
			<table style="width:250px; float:left; margin-right:75px;">
				<tr>	
					<td style="width:75px; padding-top:20px;">
						Date
					</td>
					<td class="underline">
						<input class="po_input" id="po_date" name="po_date" type="text" class="po_input" style="width:215px;" value="<?=date("n/d/y", strtotime($po["expense_datetime"]))?>" onchange="save_po()" />
					</td>
				</tr>
				<tr>
					<td style="width:75px; padding-top:20px;">
						Driver
					</td>
					<td class="underline" style="text-align:right;">
						<?php echo form_dropdown('po_client_id',$clients_dropdown_options,$po["client_id"],"id='po_client_id' class='po_input' style='' onchange='save_po()'");?>
					</td>
				</tr>
				<tr>
					<td style="width:75px; padding-top:20px;">
						Truck
					</td>
					<td class="underline" style="text-align:right;">
						<?php echo form_dropdown('po_truck_id',$truck_dropdown_options,$po["po_truck_id"],"id='po_truck_id' class='po_input' style='' onchange='save_po()'");?>
					</td>
				</tr>
			</table>
			<table style="width:300px; float:left;">
				<tr>
					<td style="width:75px; padding-top:20px;">
						Load
					</td>
					<td class="underline" style="text-align:right;">
						<input id="po_load_number" name="po_load_number" class="po_input" style="" value="<?=$load_text?>" placeholder="Load Number" onclick="this.select();" onchange='save_po()'/>
					</td>
				</tr>
				<tr>
					<td style="width:75px; padding-top:20px;">
						Location
					</td>
					<td class="underline" style="text-align:right;">
						<input type="text" class="po_input" style="font-size:14px;" id="po_gps" name="po_gps" value="<?=$po["po_gps"]?>" placeholder="Lat,Lng" onblur="auto_fill_po_location()"/>
						<div type="text" class="po_input" style="font-size:14px; line-height:35px; display:none;" id="po_location" onclick="show_po_gps()"></div>
					</td>
				</tr>
			</table>
			<table style="float:right;">
				<tr>
					<td style="padding-top:7px;" class="underline">
						$<input class="po_input" id="expense_amount" name="expense_amount" type="text" class="po_input" value="<?=round($po["expense_amount"],2)?>" onclick="this.select();" onchange="save_po()"/>
					</td>
				</tr>
				<tr>
					<td class="underline" style="text-align:right;">
						<?php echo form_dropdown('account_dropdown',$source_accounts_options,$po["account_id"],'id="account_dropdown" onChange="save_po()" class="po_input"');?>
					</td>
				</tr>
			</table>
			<div style="clear:both;"></div>
			<div style="height:25px; line-height:25px; margin-top:25px; clear:both; text-align:center; font-weight:bold; background:#F2F2F2">
				Category (circle one)
			</div>
			<input type="hidden" id="owner_id" name="owner_id" value="<?=$po["owner_id"]?>" />
			<input type="hidden" id="po_category" name="po_category" value="<?=$po["category"]?>" />
			<div id="categories_div" style="margin-top:5px; clear:both;">
				
			</div>
			<div style="margin-top:5px; clear:both;">
				<?php
					$bi = 0;
				?>
				<?php foreach($company_categories as $comp_cat):?>
					<?php if($comp_cat["show_on_po"]):?>
						<?php
							$bi++;
							$ci = 0;
							
							$show_category = false;
							if(!empty($po["account_id"]))
							{
								//GET EXPENSE ACCOUNT
								$where = null;
								$where["id"] = $po["account_id"];
								$po_account = db_select_account($where);
								
								if($po_account["company_id"] == $comp_cat["id"])
								{
									$show_category = true;
								}
							}
							
						?>
						<div style="margin-left:20px; margin-right:20px; float:left;">
							<div style="font-weight:bold; height:25px; line-height:25px;">
								<?=$comp_cat["name"]?>
							</div>
							<?php foreach($comp_cat["categories"] as $category): ?>
								<?php
									$ci++;
									
									$cat_id = $bi.$ci;
								?>
								<?php if($show_category):?>
									<div class="category" style="font-size:12px; height:25px; line-height:25px; cursor:pointer; position:relative;" onclick="category_clicked('<?=$cat_id?>','<?=$comp_cat["id"]?>','<?=$category?>')">
										<?php 
											$display_circle = "display:none;";
											if($category == $po["category"])
											{
												$display_circle = "";
											}
										?>
										<img id="circle_<?=$cat_id?>" class="circle_category" src="/images/oval.png" style="<?=$display_circle?>" />
										<?=$category?>
									</div>
								<?php else:?>
									<div class="" style="color:rgb(225,225,225); font-size:12px; height:25px; line-height:25px; cursor:pointer; position:relative;" onclick="">
										<?=$category?>
									</div>
								<?php endif;?>
							<?php endforeach?>
						</div>
					<?php endif;?>
				<?php endforeach;?>
				<div style="clear:both;"></div>
			</div>
			<div style="height:25px; line-height:25px; margin-top:25px; clear:both; text-align:center; font-weight:bold; background:#F2F2F2">
				Description and Notes
			</div>
			<div style="margin-top:5px; clear:both;">
				<div style="float:left; width:475px; height: 70px;">
					<textarea id="po_notes" name="po_notes" style="width: 475px; height:70px; font-size:14px; border: 0px" onchange="save_po()" ><?=$po["po_notes"]?></textarea>
				</div>
				<div style="float:left; width:475px; margin-left:10px;">
					<?=str_replace("\n","<br>",$po["po_log"]);?>
				</div>
			</div>
			<div style="height:25px; line-height:25px; margin-top:25px; clear:both; text-align:center; font-weight:bold; background:#F2F2F2">
				Issuer and Approval
			</div>
			<div style="height:30px; line-height:30px; margin-top:25px; clear:both;">
				<div style="width:150px; height:30px; line-height:30px; font-size: 14px; float:left;">
					Issuer Name
				</div>
				<div class="underline" style="width:300px; height:30px; float:left; font-size:16px;">
					<span style="margin-left:5px;"><?=$po["issuer"]["full_name"]?></span>
				</div>
				<div class="underline" style="width:300px; height:30px; float:right; text-align:center; font-size:16px;">
					<span style="font-family: 'Homemade Apple', cursive;"><?=$po["issuer"]["full_name"]?></span>
					<div style="font-family: 'Homemade Apple', cursive; float:right;">
						<?=date("n/d H:i", strtotime($po["issued_datetime"]))?>
					</div>
				</div>
				<div style="width:150px; height:30px; line-height:30px; font-size: 14px; float:right;">
					Issuer Signature
				</div>
			</div>
			<div style="height:30px; line-height:30px; margin-top:25px; clear:both;">
				<div style="width:150px; height:30px; line-height:30px; font-size: 14px; float:left;">
					Approved By Name
				</div>
				<div class="underline" style="width:300px; height:30px; float:left; font-size:16px;">
					<?php echo form_dropdown('approved_by_dropdown',$approved_by_options,$po["approved_by_id"],'id="approved_by_dropdown" onChange="save_po()" class="po_input" style="width:300px;"');?>
				</div>
		</form>
				<div class="underline" style="width:300px; height:30px; float:right; text-align:center; font-size:16px;">
				<?php if(!empty($po["approved_datetime"])):?>
					<span style="font-family: 'Homemade Apple', cursive;"><?=$po["approved_by"]["full_name"]?></span>
					<div style="font-family: 'Homemade Apple', cursive; float:right;">
						<?=date("n/d H:i", strtotime($po["approved_datetime"]))?>
					</div>
				<?php else:?>
					<?php if($po_is_complete): ?>
						<?php if($po["approved_by_id"] == $this_person_id):?>
							<input type="button" class='jq_button' style="width:100px; height:30px; margin:auto; display:inline; margin-right:15px;" id="approve_po_button" onclick="approve_po();" value="Sign">
							<input type="button" class='jq_button' style="width:100px; height:30px; margin:auto; display:inline;" id="deny_po_button" onclick="deny_po();" value="Deny">
					<?php endif;?>
					<?php else:?>
						<input type="button" class='jq_button' style="width:100px; height:30px; margin:auto; display:inline; margin-right:15px;" id="approve_po_button" onclick="alert('The PO is incomplete!');" value="Sign">
						<input type="button" class='jq_button' style="width:100px; height:30px; margin:auto; display:inline;" id="deny_po_button" onclick="alert('The PO is incomplete!');" value="Deny">
					<?php endif;?>
				<?php endif;?>
				</div>
			<div style="width:150px; height:30px; line-height:30px; font-size: 14px; float:right;">
				Approved By Signature
			</div>
		</div>
		<div style="height:30px; line-height:30px; margin-top:25px; clear:both;">
			<div style="height:25px; line-height:25px; margin-top:25px; clear:both; text-align:center; font-weight:bold; background:#F2F2F2">
				Attachments
			</div>
			<?php if(!empty($attachments)):?>
					<?php foreach($attachments as $attachment):?>
						<div class="attachment_box" style="float:left;margin:5px;">
							<a target="_blank" style="text-decoration:none;color:#4e77c9;" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>" onclick=""><?=$attachment["attachment_name"]?></a>&nbsp;
						</div>
					<?php endforeach;?>
			<?php endif;?>
		</div>
	</div>
</div>

<div title="PO Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
	<!-- AJAX GOES HERE !-->
</div>