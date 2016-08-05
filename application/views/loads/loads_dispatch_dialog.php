<?php
	//MAKE EACH PICK OR DROP A LINK AND ADD A TITLE THAT SHOWS CITY,STATE
	$i = 1;
	$pick_text = "";
	$these_picks = $load['load_picks'];
	sort($these_picks);
	foreach($these_picks as $pick)
	{
		if($i == 1)
		{
			$pick_text = $pick['stop']["city"].", ".$pick['stop']["state"];
		}
	}
	
	$drop_text = "";
	$these_drops = $load['load_drops'];
	sort($these_drops);
	foreach($these_drops as $drop)
	{
		$drop_text = $drop['stop']["city"].", ".$drop['stop']["state"];
	}
	
	//GET TARGET MPG FROM SYSTEMS SETTINGS
	$where = null;
	$where["setting_name"] = "Contractor Base Rate";
	$contractor_base_rate_setting = db_select_setting($where);

	$base_rate = $contractor_base_rate_setting["setting_value"];
	
	//GET TARGET MPG FROM SYSTEMS SETTINGS
	$where = null;
	$where["setting_name"] = "Loaded Target MPG";
	$loaded_target_mpg_setting = db_select_setting($where);
	$loaded_target_mpg = $loaded_target_mpg_setting["setting_value"];
	
	$where = null;
	$where["setting_name"] = "Dead Head Target MPG";
	$loaded_target_mpg_setting = db_select_setting($where);
	$dh_target_mpg = $loaded_target_mpg_setting["setting_value"];
	
	$target_mpg = .75 * $loaded_target_mpg + .25 * $dh_target_mpg;
	
	$revenue_rate = round($load["natl_fuel_avg"]/$target_mpg + $base_rate,2);
?>
<div style="margin-top:15px;">
	This is a LOAD OFFER from Arrowhead Dispatch Services for a load <?=$load["customer_load_number"]?> that picks in <?=$pick_text?> on <?=date("m/d/y", strtotime($load['first_pick_datetime']))?> and delivers in <?=$drop_text?> on <?=date("m/d/y", strtotime($load['final_drop_datetime']))?>. The load has an estimated <?=number_format($load["expected_miles"])?> dispatched miles. Please reply ACCEPT or DENY.
</div>
<?php if(!empty($load["load_offer_guid"])):?>
	<div style="margin-top:25px; margin-bottom:15px;">
		<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$load["load_offer_guid"]?>" onclick="">Load Offer <?=$load["customer_load_number"]?></a>
	</div>
<?php endif;?>
<div>
	<?php $attributes = array('id' => 'load_offer_upload_file_form', 'name'=>'load_offer_upload_file_form', 'target'=>'_blank'); ?>
	<?=form_open_multipart('loads/load_offer_upload',$attributes)?>
		<input type="hidden" id="lo_load_id" name="lo_load_id" value="<?=$load["id"]?>"/>
		<div style="margin-top:25px; font-weight:bold;" >
			Load Offer Screen Shot Upload
		</div>
		<div style="margin-top:20px;">
			<input type="file" id="lo_file" name="lo_file" style="width:170px;" />
		</div>
	</form>
	<button style="margin:auto; margin-top:25px; width:200px; height:40px;" class="jq_button" onclick="upload_load_offer('<?=$load["id"]?>')" >Upload File</button>
</div>