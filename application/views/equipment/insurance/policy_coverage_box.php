<script>
	$('#pc_current_since_date').datepicker({ showAnim: 'blind' });
	$('#expected_cancellation_date').datepicker({ showAnim: 'blind' });
</script>
<?php

?>
<form id="pc_edit_form" name="pc_edit_form" enctype="multipart/form-data" method="post">
	<input type="hidden" id="ins_profile_id" name="ins_profile_id" value="<?=$ins_profile["id"]?>"/>
	<div style="margin-top:10px; padding:10px; width:920px; margin:0 auto; clear:both;">
		<div style="width:920px;">
			<span class="heading" style="">Policy Coverage</span>
			<?php if(empty($ins_policy["policy_cancelled_date"])):?>
				<image id="edit_profile_icon" name="edit_profile_icon" class="pc_details" src="/images/edit.png" style="height:17px; cursor:pointer; float:right;" onclick="edit_policy_coverage('<?=$ins_profile["id"]?>')">
			<?php endif;?>
		</div>
	</div>
	<div class="ins_box pc_details" style="width:920px; padding:10px; text-align:center; position:relative; margin:0 auto; z-index:-1; bottom:34px;">
		<?php if(!empty($ins_profile["profile_current_since"])):?>
			<?=date('m/d/y H:i',strtotime($ins_profile["profile_current_since"]))?> to <?=$profile_current_till_text?>
		<?php else:?>	
			No Date Info
		<?php endif;?>
	</div>
	<div class="ins_box pc_edit" style="display:none; width:920px; padding-left:10px; padding-right:10px; padding-top:10px; position:relative; margin:0 auto; z-index:1; bottom:34px; height:24px;">
		<span class="heading" style="float:left;">Policy Coverage</span>
		<img id="save_profile_icon" class="pc_edit" style="display:none; height:17px; cursor:pointer; float:right; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="save_policy_coverage('<?=$ins_profile["id"]?>');"/>
		<img id="pc_back_icon" class="pc_edit" style="display:none; height:17px; margin-right:10px; cursor:pointer; float:right; position:relative; top:0px;" src="/images/back.png" title="Cancel" onclick="pc_back();"/>
		<span style="float:right; position:relative; right:300px;">
			Current as of 
			<input class="" type="text" id="pc_current_since_date" name="current_since_date" style="text-align:center; width:70px; height:20px; font-size:12px;" placeholder="<?=date('m/d/y',strtotime($ins_profile["profile_current_since"]))?>"/>
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
	<table style="margin-left:40px; float:left;">
		<tr class="heading">
			<td>
				Term
			</td>
		</tr>
		<tr>
			<td style="width: 100px;">
				Duration
			</td>
			<td style="width: 70px;">
				<span class="pc_details"><?=$ins_profile["term"]?> months</span>
				<input type="text" id="term" name="term" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["term"]?>" placeholder="Months"/>
			</td>
		</tr>
		<tr>
			<td>
				Exp Cancel
			</td>
			<td>
				<?php if(!empty($ins_profile["expected_cancellation_date"])):?>
					<span class="pc_details"><?=date("m/d/y",strtotime($ins_profile["expected_cancellation_date"]));?></span>
					<input type="text" id="expected_cancellation_date" name="expected_cancellation_date" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=date("m/d/y",strtotime($ins_profile["expected_cancellation_date"]));?>" placeholder="Date"/>
				<?php else:?>
					<input type="text" id="expected_cancellation_date" name="expected_cancellation_date" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="" placeholder="Date"/>
				<?php endif;?>
			</td>
		</tr>
	</table>
	<table style="margin-left:70px; float:left;">
		<tr class="heading">
			<td>
				Cargo
			</td>
		</tr>
		<tr>
			<td style="width:70px;">
				Limit
			</td>
			<td style="width:70px; text-align:right;">
				<span class="pc_details">$<?=number_format($ins_profile["cargo_limit"])?></span>
				<input type="text" id="cargo_limit" name="cargo_limit" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["cargo_limit"]?>" placeholder="Limit"/>
			</td>
		</tr>
		<tr>
			<td>
				Deductible
			</td>
			<td style="text-align:right;">
				<span class="pc_details">$<?=number_format($ins_profile["cargo_ded"])?></span>
				<input type="text" id="cargo_ded" name="cargo_ded" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["cargo_ded"]?>" placeholder="Deductible"/>
			</td>
		</tr>
		<tr>
			<td>
				Premium
			</td>
			<td style="text-align:right;">
				<span class="pc_details">$<?=number_format($ins_profile["cargo_prem"])?></span>
				<input type="text" id="cargo_prem" name="cargo_prem" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["cargo_prem"]?>" placeholder="Premium"/>
			</td>
		</tr>
	</table>
	<table style="margin-left:70px; float:left; margin-bottom:30px;">
		<tr class="heading">
			<td>
				Reefer BD
			</td>
		</tr>
		<tr>
			<td style="width: 70px;">
				Limit
			</td>
			<td style="width:70px; text-align:right;">
				<span class="pc_details">$<?=number_format($ins_profile["rbd_limit"])?></span>
				<input type="text" id="rbd_limit" name="rbd_limit" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["rbd_limit"]?>" placeholder="Limit"/>
			</td>
		</tr>
		<tr>
			<td>
				Deductible
			</td>
			<td style="text-align:right;">
				<span class="pc_details">$<?=number_format($ins_profile["rbd_ded"])?></span>
				<input type="text" id="rbd_ded" name="rbd_ded" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["rbd_ded"]?>" placeholder="Deductible"/>
			</td>
		</tr>
		<tr>
			<td>
				Premium
			</td>
			<td style="text-align:right;">
				<span class="pc_details">$<?=number_format($ins_profile["rbd_prem"])?></span>
				<input type="text" id="rbd_prem" name="rbd_prem" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["rbd_prem"]?>" placeholder="Premium"/>
			</td>
		</tr>
	</table>
	<table style="margin-left:70px; float:left;">
		<tr class="heading">
			<td style="70px">
				Fees
			</td>
		</tr>
		<tr>
			<td style="width:70px;">
				<span class="pc_details">$<?=number_format($ins_profile["fees"])?></span>
				<input type="text" id="fees" name="fees" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["fees"]?>" placeholder="Fees"/>
			</td>
			<td style="width:70px;">
			</td>
		</tr>
	</table>
	<table style="margin-left:45px; float:left;">
		<tr class="heading">
			<td style="text-align:right; width:70px;">
				Total Cost
			</td>
		</tr>
		<tr>
			<td style="font-size:14px; width: 70px; text-align:right;">
				<span class="pc_details">$<?=number_format($ins_profile["total_cost"])?></span>
				<input type="text" id="total_cost" name="total_cost" class="pc_edit" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["total_cost"]?>" placeholder="Total Cost"/>
			</td>
		</tr>
	</table>
</form>