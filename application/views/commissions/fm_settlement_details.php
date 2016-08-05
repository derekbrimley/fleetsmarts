<?php $attributes = array('name'=>'fm_settlement_form','ID'=>'fm_settlement_form', )?>
<?=form_open('settlements/perform_fm_settlement',$attributes);?>
	<input type="hidden" id="load_id" name="load_id" value="<?=$load["id"]?>" />
	<input type="hidden" id="driver_percentage" name="driver_percentage"  value="<?=$driver_percentage?>" />
	<input type="hidden" id="fm_percentage" name="fm_percentage"  value="<?=$fm_percentage?>" />
	<input type="hidden" id="msioo_percentage" name="msioo_percentage"  value="<?=$msioo_percentage?>" />
	<input type="hidden" id="driver_split_total" name="driver_split_total"  value="<?=$driver_split_total?>" />
	<input type="hidden" id="fm_split_total" name="fm_split_total"  value="<?=$fm_split_total?>" />
	<input type="hidden" id="msioo_split_total" name="msioo_split_total"  value="<?=$msioo_split_total?>" />
	<input type="hidden" id="amount_funded" name="amount_funded"  value="<?=$amount_funded?>" />
</form>
<div class="heading" style="margin-left:40px; margin-top:10px;">Load <?=$load["customer_load_number"]?></div>
<table style="margin-left:40px; margin-top:20px;">
	<tr>
		<td style="width:105px;">
			<?=htmlentities($load["fleet_manager"]["f_name"])?>
		</td>
		<td style="width:55px; text-align:right;">
			<?=number_format(($fm_percentage+$driver_percentage)*100,2)?>%
		</td>
		<td style="width:85px; text-align:right;">
			$<?=number_format($fm_split_total+$driver_split_total,2)?>
		</td>
	</tr>
	<tr>
		<td>
			MSIOO
		</td>
		<td style="text-align:right;">
			<?=number_format($msioo_percentage*100,2)?>%
		</td>
		<td style="text-align:right;">
			$<?=number_format($msioo_split_total,2)?>
		</td>
	</tr>
	<tr style="font-weight:bold;">
		<td>
			TOTAL
		</td>
		<td style="text-align:right;">
			<?=number_format(($msioo_percentage+$fm_percentage+$driver_percentage)*100,2)?>%
		</td>
		<td style="text-align:right;">
			$<?=number_format($msioo_split_total+$fm_split_total+$driver_split_total,2)?>
		</td>
	</tr>
</table>