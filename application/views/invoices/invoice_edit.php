<style>
	.edit_box
	{
		width:170px;
	}
	
	td
	{
		vertical-align:middle;
	}
</style>
<script>
	//PLACE DATE PICKERS ON ALL THE DATE BOXES ///////////////////
	$('#edit_start_date').datepicker({ showAnim: 'blind' });
	$('#edit_end_date').datepicker({ showAnim: 'blind' });
	
	
	//VALIDATE AND SAVE INVOICE EDIT
	function save_invoice_edit()
	{
		var isValid = true;
		
		//IS VENDOR SELECTED
		if($("#edit_vendor_dropdown").val() == "Select")
		{
			isValid = false;
			alert("Vendor must be selected!");
		}
		
		//IS BILL OWNER SELECTED
		if($("#edit_bill_owner_dropdown").val() == "Select")
		{
			isValid = false;
			alert("Bill Owner must be selected!");
		}
		
		//IS BILL TYPE SELECTED
		if($("#edit_bill_type_dropdown").val() == "Select")
		{
			isValid = false;
			alert("Bill Type must be selected!");
		}
		
		//IS INVOICE ENTERED
		if(!$("#edit_invoice_num").val())
		{
			isValid = false;
			alert("Invoice Number must be entered!");
		}
		
		//IS START DATE ENTERED
		if(!isDate($("#edit_start_date").val()))
		{
			isValid = false;
			alert("Start Date must be entered correctly!")
		}
		
		//IS END DATE ENTERED
		if(!isDate($("#edit_end_date").val()))
		{
			isValid = false;
			alert("End Date must be entered correctly!")
		}
		
		//IS UNIT ENTERED
		if(!$("#edit_unit").val())
		{
			isValid = false;
			alert("Unit must be entered!");
		}
		
		//IS MILES ENTERED
		if(!$("#edit_miles").val())
		{
			isValid = false;
			alert("Miles must be entered!");
		}
		else if(isNaN($("#edit_miles").val()))
		{
			isValid = false;
			alert("Miles must be a number!");
		}
		
		//IS TOTAL AMOUNT ENTERED
		if(!$("#edit_invoice_amount").val())
		{
			isValid = false;
			alert("Total Amount must be entered!");
		}
		else if(isNaN($("#edit_invoice_amount").val()))
		{
			isValid = false;
			alert("Total Amount must be a number!");
		}
		
		//IS LINK ENTERED
		if(!$("#edit_invoice_link").val())
		{
			isValid = false;
			alert("Link must be entered!");
		}
		
		//IS NOTES ENTERED
		if(!$("#edit_invoice_desc").val())
		{
			isValid = false;
			alert("Description must be entered!");
		}
		
		//IF VALID SUBMIT FORM
		if(isValid)
		{
			
			var this_form = "invoice_edit_form";	
			var dataString = "";
			
			$("#"+this_form+" select").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#"+this_form+" input").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#"+this_form+" textarea").each(function() {
				//alert(this.id);
				//alert(this.value);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var this_ajax_div = $('#none'); //WE WILL USE AJAX TO SAVE AND THEN REFRESH THE PAGE
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/invoices/save_invoice_edit/$this_id")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						location.reload();//REFRESH THE PAGE
						
						//alert(response);
					},
					404: function(){
						// Page not found
						//alert(response);
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						//alert(response);
						alert("500 error!")
					}
				}
			});//END AJAX
		}
	}
</script>
<?php
	$where = null;
	$where["id"] = $this_id;
	$invoice = db_select_invoice($where);
	
	//CREATE DATE RANGE TEXT
	$date_range = date("m/d",strtotime($invoice["start_datetime"]))." - ".date("m/d",strtotime($invoice["end_datetime"]));
	if($invoice["bill_type"] == "Service")
	{
		$date_range = date("m/d/y",strtotime($invoice["end_datetime"]));
	}
	
	//GET PAYMENT DATE
	$payment_date = "";
	if(!empty($invoice["payment_datetime"]))
	{
		//GET PAYMENT ACCOUNT ENTRY
		$where = null;
		$where["id"] = $invoice["payment_id"];
		$payment_entry = db_select_account_entry($where);
		
		
		$payment_date = "<a target='_blank' href='".$payment_entry["entry_link"]."'>".date("m/d/y",strtotime($invoice["payment_datetime"]))."</a>";
	}
	
	//GET ALLOCATION DATE
	$allocation_date = "";
	if(!empty($invoice["allocation_datetime"]))
	{
		$allocation_date = date("m/d/Y",strtotime($invoice["allocation_datetime"]));
	}
	
	//GET ALLOCATIONS
	$where = null;
	$where["invoice_id"] = $this_id;
	$allocations = db_select_invoice_allocations($where);
	
?>
<div id="main_content_header">
	<span style="font-weight:bold;">Invoice <?=$invoice["invoice_number"]?> (Edit)</span>
	<button class='jq_button' style="float:right;  width:80px; margin-right:10px;" id="edit_invoice" onclick="save_invoice_edit()">Save</button>
	<button class='jq_button' style="float:right;  width:80px; margin-right:10px;" id="cancel" onclick="window.location.reload( true );">Cancel</button>
</div>
<div id="scrollable_content" class="scrollable_div">
	<?php $attributes = array('name'=>'invoice_edit_form','id'=>'invoice_edit_form', )?>
	<?=form_open("invoices/save_invoice_edit/$this_id",$attributes);?>
		<div id="invoice_summary_details_div" style="margin:20px;">
			<span class="section_heading">Invoice Summary Details</span>
			<hr/>
			<br>
			<table id="main_content_table" style="margin-top:6px;">
				<tr>
					<td style="width:200px;">
						Description
					</td>
					<td style="width:180px;">
						<input type="text" id="edit_invoice_desc" name="edit_invoice_desc" value="<?=$invoice["invoice_desc"]?>" class="edit_box"/>
					</td>
					<td style="width:200px; padding-left:110px;">
						Invoice Link
					</td>
					<td style=" width:180px;">
						<input type="text" id="edit_invoice_link" name="edit_invoice_link" value="<?=$invoice["invoice_link"]?>" class="edit_box"/>
					</td>
				</tr>
			</table>
			<br>
			<table id="main_content_table" style="margin-top:6px;">
				<tr>
					<td style="width:200px;">
						Payee
					</td>
					<td style="width:180px;">
						<?php echo form_dropdown('edit_bill_owner_dropdown',$bill_owner_options,$invoice["account_id"],'id="edit_bill_owner_dropdown" onChange="" class="edit_box"');?>
					</td>
					
					<td style="padding-left:110px;">Invoice#</td>
					<td style="">
						<input type="text" id="edit_invoice_num" name="edit_invoice_num" value="<?=$invoice["invoice_number"]?>" class="edit_box"/>
					</td>
				</tr>
				<tr>
					<td style="">Payer</td>
					<td style="">
						<?php echo form_dropdown('edit_vendor_dropdown',$vendor_dropdown_options,$invoice["vendor_id"],'id="edit_vendor_dropdown" onChange="" class="edit_box"');?>
					</td>
					
					<td style="padding-left:110px;">Unit</td>
					<td style="">
						<input type="text" id="edit_unit" name="edit_unit" value="<?=$invoice["unit_number"]?>" class="edit_box"/>
					</td>
				</tr>
				<tr>
					<td style="">Invoice Type</td>
					<td style="">
						<?php 
							$options = array(
									'Select' => 'Select',
									'Damage'    => 'Damage',
									'Fuel'    => 'Fuel',
									'Hours'    => 'Hours',
									'Old Debt'    => 'Old Debt',
									'Insurance'    => 'Insurance',
									'Service'    => 'Service',
									'Truck Rental' => 'Truck Rental',
									'Trailer Rental'    => 'Trailer Rental',
									'Truck in Shop'    => 'Truck in Shop',
									'Other'    => 'Other',
							); 
						?>
						<?php echo form_dropdown('edit_bill_type_dropdown',$options,$invoice["bill_type"],'id="edit_bill_type_dropdown" onChange="bill_type_selected()" class="edit_box"');?>
					</td>
					
					<td style="padding-left:110px;">Miles</td>
					<td style="">
						<input type="text" id="edit_miles" name="edit_miles" value="<?=$invoice["miles"]?>" class="edit_box"/>
					</td>
				</tr>
				<tr>
					<td style="">Invoice Date</td>
					<td style="">
						<input type="text" id="edit_start_date" name="edit_start_date" value="<?=date("m/d/Y",strtotime($invoice["start_datetime"]))?>" style="width:75px;"/>
						<span> to </span>
						<input type="text" id="edit_end_date" name="edit_end_date" value="<?=date("m/d/Y",strtotime($invoice["end_datetime"]))?>" style="width:75px;"/>
					</td>
					
					<td style="padding-left:110px;">Invoice Amount</td>
					<td style="font-weight:bold; font-size:16;">
						<?php $invoice_amount = number_format($invoice['invoice_amount'], 2,'.','');?>
						<input type="text" id="edit_invoice_amount" name="edit_invoice_amount" value="<?=$invoice_amount?>" class="edit_box"/>
					</td>
				</tr>
				<tr>
					<td style="">Payment Date</td>
					<td style="">
						<?=$payment_date?>
					</td>
					<td style="width:200px; padding-left:110px;">Allocation Date</td>
					<td style="width:180px;">
						<?=$allocation_date?>
					</td>
					
				</tr>
			</table>
		</div>
		<div id="invoice_allocations_div" style="margin:20px;">
			<span class="section_heading">Invoice Allocations</span>
			<hr/>
			<br>
			<table style="margin-top:6px;">
				<tr style="font-weight:bold;">
					<td style="width:200px; font-weight:bold;">
						Client
					</td>
					<td style="width:125px;">
						Account
					</td>
					<td style="width:365px;">
						Notes
					</td>
					<td style=" width:125px;">
						Amount
					</td>
				</tr>
				<?php foreach($allocations as $allocation): ?>
					<tr>
						<td style="">
							<?=$allocation["company"]["company_side_bar_name"]?>
						</td>
						<td style="">
							<?=$allocation["account"]["account_name"]?>
						</td>
						<td style="">
							<?=$allocation["allocation_notes"]?>
						</td>
						<td style="">
							<?=$allocation["allocation_amount"]?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
		<div id="invoice_notes_div" style="margin:20px;">
			<div>
				<span class="section_heading">Invoice Notes</span>
				<span style="float:right;"><a href="javascript:open_invoice_notes('<?=$invoice["id"]?>')">Add Note+</a><span>
			</div>
			<hr/>
			<br>
			<table id="main_content_table" style="margin-top:6px;">
				<tr>
					<td id="notes_details" name="notes_details" style="">
						<?=str_replace("\n","<br>",$invoice["invoice_notes"]);?>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>