<style>
	.heading
	{
		color:#DD4B39;
		font-family:arial;
		//font-weight:bold;
	}
</style>
<body style="font-family:arial;">
	<?php if($hold_report["hold_status"] == "No Hold"):?>
		<div style="background-color:green; color:white; margin-top:20px; width:100%; text-align:center; font-size:40px; font-weight:bold;">
			NO HOLD
		</div>
	<?php else:?>
		<div style="background-color:red; color:white; margin-top:20px; width:100%; text-align:center; font-size:40px; font-weight:bold;">
			HOLD
		</div>
	<?php endif;?>
	<div style="margin:20px;">
		<table style="float:left; margin-bottom:10px;">
			<tr class="heading">
				<td style="width:200px; color:#DD4B39;font-family:arial;">
					Missing BOL Pics (<?=count($hold_report["loads_missing_dc"])?>)
				</td>
			</tr>
			<?php if(!empty($hold_report["loads_missing_dc"])):?>
				<?php foreach($hold_report["loads_missing_dc"] as $load):?>
					<tr>
						<td>
							<span title="<?=get_final_drop_goalpoint($load["id"])["location"]?>"><?=$load["customer_load_number"]?> <?=get_final_drop_goalpoint($load["id"])["location"]?></span>
						<td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
		</table>
		<table style="float:left; margin-bottom:10px;">
			<tr class="heading">
				<td style="width:200px; color:#DD4B39;font-family:arial;">
					Missing BOL Hard Copy (<?=count($hold_report["loads_missing_hc"])?>)
				</td>
			</tr>
			<?php if(!empty($hold_report["loads_missing_hc"])):?>
				<?php foreach($hold_report["loads_missing_hc"] as $load):?>
					<tr>
						<td>
							<span title="<?=get_final_drop_goalpoint($load["id"])["location"]?>"><?=$load["customer_load_number"]?></span>
						<td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
		</table>
		<table style="float:left; margin-bottom:10px;">
			<tr class="heading">
				<td style="width:200px; color:#DD4B39;font-family:arial;">
					Missing Receipts (<?=count($hold_report["client_expenses"])?>)
				</td>
			</tr>
			<?php if(!empty($hold_report["client_expenses"])):?>
				<?php foreach($hold_report["client_expenses"] as $ce):?>
					<tr>
						<td>
							<span title="<?=date("m/d/y",strtotime($ce["expense_datetime"]))?> <?=$ce["description"]?>">$<?=number_format($ce["expense_amount"],2)?> <?=date("m/d/y",strtotime($ce["expense_datetime"]))?> </span>
						<td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
		</table>
		<div style="clear:both;"></div>
	</div>
</body>