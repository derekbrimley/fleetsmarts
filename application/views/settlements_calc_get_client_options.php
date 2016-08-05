<?php
	if($this_id == 'all')
	{
		$load = null;
	}
	else
	{	
		//GET LOAD
		$load_where = null;
		$load_where["id"] = $this_id;
		$load = db_select_load($load_where);
		
		//GET PROFIT SPLIT DETAILS
		$where = null;
		$where["load_id"] = $load["id"];
		$profit_splits = db_select_settlement_profit_splits($where);
		$profit_split_1 = null;
		$profit_split_2 = null;
		$i = 1;
		foreach($profit_splits as $profit_split)
		{
			if($i == 1)
			{
				$where = null;
				$where["id"] = $profit_split["id"];
				$profit_split_1 = db_select_settlement_profit_split($where);
			}
			if($i == 2)
			{
				$where = null;
				$where["id"] = $profit_split["id"];
				$profit_split_2 = db_select_settlement_profit_split($where);
			}
			
			$i++;
		}
	}
?>

<table style="">
	<tr style="font-weight:bold;">
		<td style="width:185px; vertical-align: middle;">
			Account
		</td>
		<td style="width:160px; vertical-align: middle;">
			Percentage
		</td>
	</tr>
	<tr>
		<td style="vertical-align: middle;">
			<?php echo form_dropdown('profit_split_account_1',$client_pay_account_options,$profit_split_1["account_id"],'id="profit_split_account_1" class="left_bar_input"');?>
		</td>
		<td>
			<input type="text" id="profit_split_percentage_1" name="profit_split_percentage_1" style="width:156px; text-align:right;" onblur="calc_percentage('1')" value="<?=$profit_split_1["percentage"] ?>">
		</td>
	</tr>
	<tr>
		<td style="vertical-align: middle;">
			<?php echo form_dropdown('profit_split_account_2',$client_pay_account_options,$profit_split_2["account_id"],'id="profit_split_account_2" class="left_bar_input"');?>
		</td>
		<td>
			<input type="text" id="profit_split_percentage_2" name="profit_split_percentage_2" style="width:156px; text-align:right;" onblur="calc_percentage('2')" value="<?=$profit_split_2["percentage"] ?>">
		</td>
	</tr>
</table>
<br>
<br>
<br>