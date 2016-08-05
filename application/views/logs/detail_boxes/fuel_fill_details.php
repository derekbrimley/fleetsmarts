<!-- USE THIS DETAIL BOX FOR ALL FUEL TYPES !-->
<style>
	.event_details_table tr
	{
		height:20px;
	}
	
	.edit_<?=$log_entry_id?>
	{
		display:none;
	}
</style>
<script>
	
	//DIALOG: ADD NEW EVENT
	$( "#export_fuel_allocation_<?=$this_fuel_stop["id"]?>" ).dialog(
	{
			autoOpen: false,
			height: 200,
			width: 500,
			modal: true,
			buttons: 
				[
					{
						text: "Close",
						click: function() 
						{
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
	
	function edit_event(log_entry_id)
	{
		$('.edit_'+log_entry_id).css({"display":"block"});
		$('.details_'+log_entry_id).css({"display":"none"});
	}
	
	function save_fuel_stop(log_entry_id)
	{
	
		var form_name = "edit_fuel_stop_"+log_entry_id;	
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
					//main_content.html(response);
					//alert(response);
					//alert("500 error!")
				}
			}
		});//END AJAX
	}
	
	function export_fuel_allocation(fuel_stop_id)
	{
		$( "#export_fuel_allocation_"+fuel_stop_id ).dialog('open');
	}
	
	
</script>
	<?php //echo $fuel_stop_details["f2f_expense"] ?>
<div style="font-size:12px;">
	<?php if(empty($log_entry["locked_datetime"])): ?>
		<div style="font-weight:bold; float:left;">Fuel <?=$this_fuel_stop["id"]?></div>
		<div style="width:20px; float:right;">
			<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_event_details('<?=$log_entry["id"]?>')"/>
			<img id="edit_icon" class="details_<?=$log_entry_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:0px;" src="/images/edit.png" title="Edit" onclick="edit_event('<?=$log_entry_id?>')"/>
			<img id="save_icon" class="edit_<?=$log_entry_id?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="this.src='/images/loading.gif'; save_fuel_stop('<?=$log_entry["id"]?>');"/>
			<img id="unlocked_icon" style="display:block; <?=$fill_partial_style?> margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:1px;" src="/images/unlocked.png" title="Lock" onclick="this.src='/images/loading.gif'; lock_event('<?=$log_entry["id"]?>')"/>
			<?php if($this_fuel_stop["source"] == "Estimate"): ?>
				<img id="delete_icon" style="display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/trash.png" title="Delete" onclick="delete_event('<?=$log_entry_id?>')"/>
			<?php endif; ?>
			<img id="new_checkpoint" style="margin-bottom:13px; margin-right:15px; cursor:pointer; height:16px; position:relative; left:1px;" src="/images/new_checkpoint.png" title="New Checkpoint" onclick="this.src='/images/loading.gif'; create_new_checkpoint('<?=$log_entry["id"]?>');"/>
		</div>
	<?php else: ?>
		<div style="width:70px; float:left;">
			<div style="font-size:12px; font-weight:bold;">
				Fuel <?=$this_fuel_stop["id"]?>
			</div>
		</div>
		<div style="width:20px; float:right;">
			<img id="locked_icon" style="display:block; margin-bottom:12px; margin-right:15px; height:15px; position:relative; left:2px;" src="/images/locked.png" title="Locked <?=date("n/j H:i",strtotime($log_entry["locked_datetime"]))?>"/>
		</div>
	<?php endif; ?>
	<form id="edit_fuel_stop_<?=$log_entry_id?>" name="edit_fuel_stop_<?=$log_entry_id?>" >
		<input type="hidden" id="fuel_stop_id" name="fuel_stop_id" value="<?= $this_fuel_stop["id"] ?>" />
		<input type="hidden" id="log_entry_id" name="log_entry_id" value="<?=$log_entry_id?>" />
		<table style="font-size:12px; margin-left:75px;" class="event_details_table">
			<tr>
				<td style="width:90px;">
					Load
				</td>
				<td style="width:70px; text-align:right;">
					<?=$log_entry["load"]["customer_load_number"];?>
				</td>
				<td style="width:130px;">
				</td>
				<td style="width:90px;">
					Fill Type
				</td>
				<td class="details_<?=$log_entry_id?>" style="width:70px; text-align:right;">
					<?= $fuel_stop_details["fill_type"] ?>
				</td>
				<td class="edit_<?=$log_entry_id?>" style="width:70px; text-align:right;">
					<?php
						$options = array(
							"Yes" => "Fill",
							"No" => "Partial",
							"Reefer" => "Reefer"
						);
					?>
					<?php echo form_dropdown("is_fill",$options,$this_fuel_stop["is_fill"],"id='is_fill' style='font-size:12px; height:18px; width:65px; position:relative; bottom:3px;'");?>
				</td>
				<td style="width:130px;">
				</td>
				<td style="width:100px; <?=$fill_partial_style?>">
					Odometer Miles
				</td>
				<td style="width:70px; text-align:right; <?=$fill_partial_style?>">
					<?= $this_fuel_stop["odom_miles"] ?>
					<input type="hidden" id="odom_miles" name="odom_miles" value="<?= $this_fuel_stop["odom_miles"] ?>" />
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
					Fuel Price
				</td>
				<td style=" text-align:right;">
					<?php if($fuel_stop_details["price_compare"] > 0):?>
						<?php if($fuel_stop_details["price_compare"] > .30):?>
							<span style="font-weight:bold; color:green" title="+<?=$fuel_stop_details["price_compare"]?>"><?=number_format($this_fuel_stop["fuel_price"],2)?></span>
						<?php elseif($fuel_stop_details["price_compare"] > .10 && $fuel_stop_details["price_compare"] <= .30):?>
							<span style="font-weight:bold; color:orange" title="+<?=$fuel_stop_details["price_compare"]?>"><?=number_format($this_fuel_stop["fuel_price"],2)?></span>
						<?php elseif($fuel_stop_details["price_compare"] <= .10):?>
							<span style="font-weight:bold; color:red" title="+<?=$fuel_stop_details["price_compare"]?>"><?=number_format($this_fuel_stop["fuel_price"],2)?></span>
						<?php endif; ?>
					<?php else: ?>
						<span style="font-weight:bold; color:red" title="<?=$fuel_stop_details["price_compare"]?>"><?=number_format($this_fuel_stop["fuel_price"],2)?></span>
					<?php endif; ?>
				</td>
				<td style="">
				</td>
				<td style=" <?=$fill_partial_style?>">
					Map Miles
				</td>
				<td style="text-align:right; <?=$fill_partial_style?>">
					<?= $this_fuel_stop["map_miles"] ?>
					<input type="hidden" id="f2f_miles" name="f2f_miles" value="<?= $this_fuel_stop["map_miles"] ?>" />
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
					<?= $this_fuel_stop["gallons"] ?>
				</td>
				<td style="">
				</td>
				<td style=" <?=$fill_partial_style?>">
					Fill to Fill Gallons
				</td>
				<td style="text-align:right; <?=$fill_partial_style?>">
					<?= number_format($this_fuel_stop["fill_to_fill_gallons"],2) ?>
					<input type="hidden" id="f2f_gallons" name="f2f_gallons" value="<?= $this_fuel_stop["fill_to_fill_gallons"] ?>" />
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
					<?= $this_fuel_stop["fuel_expense"] ?>
				</td>
				<td style="">
				</td>
				<td style=" <?=$fill_partial_style?>">
					Fill to Fill Expense
				</td>
				<td style="text-align:right; <?=$fill_partial_style?>">
					$<?= number_format($this_fuel_stop["fill_to_fill_expense"],2) ?>
					<input type="hidden" id="f2f_expense" name="f2f_expense" value="<?= $this_fuel_stop["fill_to_fill_expense"] ?>" />
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
					<?= $this_fuel_stop["rebate_amount"] ?>
				</td>
				<td style="">
				</td>
				<td style=" <?=$fill_partial_style?>">
					Fill to Fill Discount
				</td>
				<td style="text-align:right; <?=$fill_partial_style?>">
					$<?= number_format($this_fuel_stop["fill_to_fill_rebate"],2) ?>
					<input type="hidden" id="fill_to_fill_rebate" name="fill_to_fill_rebate" value="<?= $this_fuel_stop["fill_to_fill_rebate"] ?>" />
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
						<td style="width:70px;">
							<?=$permit["permit_type"]?> Permit
						</td>
						<td style="width:100px; text-align:right;">
							<?=date("m/d/y H:i",strtotime($permit["permit_datetime"]))?>
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
	<?php if($log_entry["entry_type"] == "Fuel Fill"): ?>
		<a href="javascript:void;" onclick="open_fuel_allocations(<?=$log_entry_id?>)" style="font-">Fuel Allocations</a>
		<div id="fuel_allocations_<?=$log_entry_id?>" style="font-size:12px;">
			<!-- AJAX GOES HERE !-->
		</div>
	<?php endif; ?>
</div>
