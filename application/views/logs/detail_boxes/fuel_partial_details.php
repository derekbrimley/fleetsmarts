<style>
	.event_details_table tr
	{
		height:20px;
	}
	
	.event_details_text_box
	{
		width:80px;
		font-size:12px;
		height: 18px;
		position: relative;
		bottom: 3px;
	}
	
	.edit
	{
		display:none;
	}
</style>
<script>
	
	function edit_event()
	{
		$('.edit').css({"display":"block"});
		$('.details').css({"display":"none"});
	}
	
	function save_event(log_entry_id)
	{
		set_event_filter_fields()
	
		var form_name = "edit_fuel_stop";	
		var dataString = "";
		$("#"+form_name+" select").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" input").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" textarea").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		//alert("load_log_list");
		
		//alert(dataString.substring(1));
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/save_fuel_stop")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//main_content.html(response);
					open_event_details(log_entry_id);
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
	
</script>
<div style="height:95px;">
	<div style="width:20px; float:right;">
		<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_event_details('<?=$log_entry["id"]?>')"/>
		<img id="edit_icon" class="details" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:0px;" src="/images/edit.png" title="Edit" onclick="edit_event()"/>
		<img id="save_icon" class="edit" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="this.src='/images/loading.gif'; save_event('<?=$log_entry["id"]?>');"/>
		<img id="locked_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/unlocked.png" title="Lock" onclick="this.src='/images/loading.gif'; open_event_details('<?=$log_entry["id"]?>')"/>
	</div>
	<form id="edit_fuel_stop" name="edit_fuel_stop" >
		<input type="hidden" id="fuel_stop_id" name="fuel_stop_id" value="<?= $fuel_stop_details["fuel_stop_id"] ?>" />
		<input type="hidden" id="log_entry_id" name="log_entry_id" value="<?=$log_entry_id?>" />
		<table style="font-size:12px; margin-left:80px;" class="event_details_table">
			<tr>
				<td style="width:90px;">
					Load
				</td>
				<td style="width:80px; text-align:right;">
					<?=$log_entry["load"]["customer_load_number"];?>
				</td>
				<td style="width:120px;">
				</td>
				<td style="width:110px;">
					Fill Type
				</td>
				<td class="details" style="width:80px; text-align:right;">
					<?= $fuel_stop_details["fill_type"] ?>
				</td>
				<td class="edit" style="width:80px; text-align:right;">
					<?php
						$options = array(
							"Yes" => "Fill",
							"No" => "Partial",
							"Reefer" => "Reefer"
						);
					?>
					<?php echo form_dropdown("is_fill",$options,$fuel_stop_details["is_fill"],"id='is_fill' style='font-size:12px; height:18px; width:80px; position:relative; bottom:3px;'");?>
				</td>
			</tr>
			<tr>
				<td style="">
					Main Driver
				</td>
				<td style="width:80px; text-align:right;">
					<?=substr($log_entry["main_driver"]["client_nickname"],0,strpos($log_entry["main_driver"]["client_nickname"]," ")+2)?>
				</td>
				<td style="">
				</td>
				<td style="">
					Nat'l Fuel Avg
				</td>
				<td style=" text-align:right;">
					
				</td>
			</tr>
			<tr>
				<td style="">
					Co-Driver
				</td>
				<td style="width:80px; text-align:right;">
					<?=substr($log_entry["codriver"]["client_nickname"],0,strpos($log_entry["codriver"]["client_nickname"]," ")+2)?>
				</td>
				<td style="">
				</td>
				<td style="">
					Gallons
				</td>
				<td style=" text-align:right;">
					<?= $fuel_stop_details["gallons"] ?>
				</td>
			</tr>
			<tr>
				<td style="">
					Truck
				</td>
				<td style="width:80px; text-align:right;">
					<?=$log_entry["truck"]["truck_number"];?>
				</td>
				<td style="">
				</td>
				<td style="">
					Fuel Expense
				</td>
				<td style=" text-align:right;">
					<?= $fuel_stop_details["fuel_expense"] ?>
				</td>
			</tr>
			<tr>
				<td style="">
					Trailer
				</td>
				<td style="width:80px; text-align:right;">
					<?=$log_entry["trailer"]["trailer_number"];?>
				</td>
				<td style="">
				</td>
				<td style="">
					Fuel Discount
				</td>
				<td style=" text-align:right;">
					<?= $fuel_stop_details["rebate_amount"] ?>
				</td>
			</tr>
		</table>
	</form>
	<?php if(!empty($fuel_permits)):?>
		<div style="width:950px; margin-top:20px; margin-bottom:20px;">
			<div style="width:70px; float:left;">
				<div style="font-size:12px; font-weight:bold;">
					Permits
				</div>
			</div>
			<table style="font-size:12px; margin-left:75px;" class="event_details_table">
				<?php foreach($fuel_permits as $permit):?>
					<tr>
						<td style="width:90px;">
							<?=$permit["permit_type"]?> Permit
						</td>
						<td style="width:80px; text-align:right;">
							<?=date("m/d/y",strtotime($permit["permit_datetime"]))?>
						</td>
						<td style="width:130px;">
						</td>
						<td style="width:90px;">
							<a target="_blank" href="<?=$permit["permit_link"]?>">Permit Link</a>
						</td>
						<td style="width:70px; text-align:right;">
							$<?=number_format($permit["permit_expense"],2)?>
						</td>
						<td style="width:130px;">
						</td>
						<td style="font-style:italic;">
							<?=$permit["permit_notes"]?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	<?php endif; ?>
</div>