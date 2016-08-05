<script>
	//DIALOG: ADD NEW TRUCK
	$( "#add_listed_driver_dialog" ).dialog(
	{
			autoOpen: false,
			height: 300,
			width: 420,
			modal: true,
			buttons: 
				[
					{
						text: "Add",
						click: function() 
						{
							//VALIDATE ADD QUOTE
							validate_add_listed_driver();
						},//end add load
					},
					{
						text: "Cancel",
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
					$("#ld_client_id").val('');
					$("#ld_client_id_input").val('Select');
					
				},//end open function
			close: function() 
				{
					$("#ld_client_id").val('');
					$("#ld_client_id_input").val('Select');
				}
	});//end dialog form
</script>
<?php
	//GET LISTED DRIVERS FOR THIS PROFILE
	$where = null;
	$where["ins_profile_id"] = $ins_profile["id"];
	
	$listed_drivers = db_select_ins_listed_drivers($where);
	
	//GET ADDITIONAL INSURER OPTIONS
	$where = null;
	$where = "1 = 1";
	$listed_driver_clients = db_select_clients($where,"client_nickname");
	
	$ld_clients_options = null;
	$ld_clients_options["Select"] = "Select";
	foreach($listed_driver_clients as $client)
	{
		$ld_clients_options[$client["id"]] = $client["client_nickname"];
	}
?>
<form id="add_listed_driver_form">
	<input type="hidden" id="ins_profile_id" name="ins_profile_id" value="<?=$ins_profile["id"]?>"/>
	<input type="hidden" id="ld_client_id" name="ld_client_id" value=""/>
	<div style="margin-top:10px; padding:10px; width:920px; margin:0 auto; clear:both;">
		<div style="width:920px;">
			<span class="heading" style="">Listed Drivers</span>
			<?php if(empty($ins_policy["policy_cancelled_date"])):?>
				<image id="edit_profile_icon" name="edit_profile_icon" class="ld_details" src="/images/edit.png" style="height:17px; cursor:pointer; float:right;" onclick="edit_listed_drivers()">
			<?php endif;?>
		</div>
	</div>
	<div class="ins_box ld_details" style="width:920px; padding:10px; text-align:center; position:relative; margin:0 auto; z-index:-1; bottom:34px;">
		<?php if(!empty($ins_profile["profile_current_since"])):?>
			<?=date('m/d/y H:i',strtotime($ins_profile["profile_current_since"]))?> to <?=$profile_current_till_text?>
		<?php else:?>	
			No Date Info
		<?php endif;?>
	</div>
	<div class="ins_box ld_edit" style="display:none; width:920px; padding-left:10px; padding-right:10px; padding-top:10px; position:relative; margin:0 auto; z-index:1; bottom:34px; height:24px;">
		<span class="heading" style="float:left;">Listed Drivers</span>
		<img id="save_profile_icon" class="ld_edit" style="display:none; height:17px; cursor:pointer; float:right; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="save_profile('<?=$ins_profile["id"]?>');"/>
		<img id="ld_back_icon" class="ld_edit" style="display:none; height:17px; margin-right:10px; cursor:pointer; float:right; position:relative; top:0px;" src="/images/back.png" title="Cancel" onclick="ld_back();"/>
		<span style="float:right; position:relative; right:300px;">
			Current as of 
			<input class="datepicker" type="text" id="ld_current_since_date" name="current_since_date" style="text-align:center; width:70px; height:20px; font-size:12px;" placeholder="<?=date('m/d/y',strtotime($ins_profile["profile_current_since"]))?>"/>
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
			<?php echo form_dropdown("current_since_minute",$options,"00","id='current_since_minute' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
		</span>
	</div>
	<div>
		<?php
			$i = 1;
		?>
		<?php if(!empty($listed_drivers)):?>
			<?php foreach($listed_drivers as $listed_driver):?>
				<div style="float:left; margin-left:40px; margin-bottom:20px;">
					<?=$i?>) <?=$listed_driver["client"]["client_nickname"]?>
				</div>
				<?php
					$i++;
				?>
			<?php endforeach;?>
		<?php endif;?>
		<div style="float:left; margin-left:40px; margin-bottom:20px; display:none;" class="ld_edit">
			<?=$i?>) <span class="link" onclick="open_listed_driver_dialog()">Add</span>
		</div>
	</div>
</form>

<div id="add_listed_driver_dialog" title="Add Listed Driver" style="">
	<div style="height:30px; width:100%;"></div>
	<table style="margin:auto;">
		<tr>
			<td style="width:150px;">
				Driver
			</td>
			<td style="width:150px;">
				<?php echo form_dropdown("ld_client_id_input",$ld_clients_options,"Select","id='ld_client_id_input' class='' style='width:130px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
			</td>
		</tr>
	</table>
</div>