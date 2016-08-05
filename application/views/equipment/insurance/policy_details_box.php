<script>
	$('#policy_active_date').datepicker({ showAnim: 'blind' });
	$('#policy_cancelled_date').datepicker({ showAnim: 'blind' });
	//$('#cancel_date').datepicker({ showAnim: 'blind' });
	//DIALOG: ADD NEW TRUCK
	$("#cancel_policy_dialog" ).dialog(
	{
			autoOpen: false,
			height: 300,
			width: 420,
			modal: true,
			buttons: 
				[
					{
						text: "Proceed",
						click: function() 
						{
							//VALIDATE ADD QUOTE
							var isValid = true;
							
							if(!$("#cancel_reason").val())
							{
								isValid = false;
								alert('Cancel Reason must be entered!');
							}
							
							if(!$("#cancel_date").val())
							{
								isValid = false;
								alert('Cancel Date must be entered!');
							}
							
							if(isValid)
							{
								cancel_policy();
							}
							
							
							
						},//end add load
					},
					{
						text: "Go Back",
						click: function() 
						{
							//RESIZE DIALOG BOX
							$( this ).dialog( "close" );
							
							//RESET ALL FIELDS IN DIALOG FORM
						}
					}
				],//end of buttons
			
			open: function()
				{
					$('#cancel_date').blur();
					$('#cancel_date').datepicker({ showAnim: 'blind' });

					//RESET FIELDS
					$("#cancel_reason").val('')
					$('#cancel_date').val('');
					$('#cancel_minute').val('00');
					$('#cancel_hour').val('00');
					
				},//end open function
			close: function() 
				{
				}
	});//end dialog form
</script>
<?php
	//GET USER WHO REQUESTED QUOTE
	$where = null;
	$where["id"] = $ins_policy["quoted_by_id"];
	$quote_user = db_select_user($where);
?>
<form id="policy_edit_form" name="policy_edit_form" enctype="multipart/form-data" method="post">
	<input type="hidden" id="ins_policy_id" name="ins_policy_id" value="<?=$ins_policy["id"]?>"/>
	<div style="margin-left:30px; float:left;">
	<div class="ins_box" style="padding:10px; width:350px;">
		<div style="">
			<span class="heading" title="<?=$ins_policy["id"]?>">Policy</span>
			<?php if(empty($ins_policy["policy_cancelled_date"])):?>
				<img id="edit_profile_icon" class="policy_details" src="/images/edit.png" style="height:17px; cursor:pointer; float:right;" onclick="edit_policy()">
				<img id="save_profile_icon" class="policy_edit" style="display:none; height:17px; cursor:pointer; float:right; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="save_policy();"/>
				<img id="cancel_profile_icon" class="policy_edit" style="display:none; height:20px; cursor:pointer; float:right; position:relative; bottom:1px; margin-right:10px;" src="/images/grey_cancel_icon.png" title="Cancel Policy" onclick="open_cancel_dialog('<?=$ins_policy["id"]?>')"/>
				<img id="po_back_icon" class="policy_edit" style="display:none; height:17px; margin-right:10px; cursor:pointer; float:right; position:relative; top:0px;" src="/images/back.png" title="Cancel" onclick="po_back();"/>
			<?php endif;?>
		</div>
	</div>
	<table style="margin-left:10px; margin-top:20px; margin-bottom:20px;">
		<tr>
			<td>
				Policy Number
			</td>
			<td>
				<span class="policy_details"><?=$ins_policy["policy_number"]?></span>
				<input type="text" id="policy_number" name="policy_number" class="policy_edit" style="display:none; width:160px; height:20px; font-size:12px; float:right;" value="<?=$ins_policy["policy_number"]?>" placeholder="Policy Number"/>
			</td>
		</tr>
		<tr>
			<td>
				Active Date
			</td>
			<td>
				<span class="policy_details"><?=date("m/d/y",strtotime($ins_policy["policy_active_date"]))?></span>
				<input type="text" id="policy_active_date" name="policy_active_date" class="policy_edit" style="display:none; width:160px; height:20px; font-size:12px; float:right;" value="<?=date("m/d/y",strtotime($ins_policy["policy_active_date"]))?>" placeholder="Active Date"/>
			</td>
		</tr>
		<tr>
			<td>
				Cancelled Date
			</td>
			<td>
				<?php if(!empty($ins_policy["policy_cancelled_date"])):?>
					<span class="policy_details"><?=date("m/d/y",strtotime($ins_policy["policy_cancelled_date"]))?></span>
				<?php endif;?>
			</td>
		</tr>
		<tr>
			<td style="width:150px;">
				Quote or Policy
			</td>
			<td style="">
				<span class="policy_details"><?=$ins_policy["quote_status"]?></span>
				<?php
					$options = array(
						'Quote'		=>	'Quote',
						'Policy'	=>	'Policy',
					);
				?>
				<?php echo form_dropdown("quote_status",$options,$ins_policy["quote_status"],"id='quote_status' class='policy_edit' style='width:160px; height:20px; font-size:12px; position:relative; display:none;'");?>
			</td>
		</tr>
		<tr>
			<td>
				Quote ID
			</td>
			<td>
				<?=$ins_policy["quote_code"]?>
			</td>
		</tr>
		<tr>
			<td>
				Quote Request
			</td>
			<td>
				<?=$quote_user["person"]["full_name"]?>
				
			</td>
		</tr>
		<tr>
			<td>
				<span class="policy_edit" style="display:none;">Current as of</span>
			</td>
			<td>
				<span class="policy_edit" style="display:none;">
					<input class="" type="text" id="current_since_date" name="current_since_date" style="text-align:center; width:70px; height:20px;"/>
					<?php
						$options = array();
						for($i=0; $i<=12; $i++)
						{
							if($i<10)
							{
								$minute = "0".$i;
							}
							else
							{
								$minute = $i;
							}
							$options[$minute] = $minute;
						}
					?>
					<?php echo form_dropdown("current_since_hour",$options,"00","id='current_since_hour' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
					<span style="margin-left:-3px; margin-right:-3;">:</span>
					<?php
						$options = array();
						for($i=0; $i<=60; $i++)
						{
							if($i<10)
							{
								$second = "0".$i;
							}
							else
							{
								$second = $i;
							}
							$options[$second] = $second;
						}
					?>
					<?php echo form_dropdown("current_since_minute",$options,"00","id='policy_current_since_minute' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
				</span>
			</td>
		</tr>
	</table>
	</div>
</form>

<div id="cancel_policy_dialog" title="Mark Policy Cancelled" style="display:none;">
	
</div>