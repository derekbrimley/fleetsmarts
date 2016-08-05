<table style="margin-top:15px; margin-bottom:15px; margin-left:35px; margin-right:35px; background:#DDD;">	
	<?php if(empty($unpaid_bills)): ?>
		<tr>
			<td style="text-align:center; width:320px; vertical-align: middle;">
				No unpaid bills for this vendor in the system
			</td>
		</tr>
	<?php else:?>
		<?php foreach($unpaid_bills as $bill): ?>
			<input type="hidden" name="bill_amount_<?=$bill["id"]?>" id="bill_amount_<?=$bill["id"]?>" value="<?=$bill["entry_amount"]?>" />
			<?php
				//GET CORRESPONDING ACCOUNT
				$where = null;
				$where["company_id"] = $bill["company"]["id"];
				$where["vendor_id"] = $vendor_id;
				$corresponding_account = db_select_account($where);
			
				//BILL URL
				$bill_link = "";
				if(!empty($bill["entry_link"]))
				{
					$bill_link = '<a target="_blank" href="'.$bill["entry_link"].'">Bill</a>';
				}
				
				//CHANGE COLOR BASED ON APPROVAL
				$bill_row_style = "";
				if($bill["is_approved"] == "Yes")
				{
					$bill_row_style = "font-weight:bold";
				}
				else if($bill["is_approved"] == "No")
				{
					$bill_row_style = "color:grey;";
				}
			
			?>
			<tr id="" style="<?=$bill_row_style?>">
				<td  style="width:40px; vertical-align: middle;">
					<input type="checkbox" onclick="unpaid_bill_clicked('<?=$bill["id"]?>')" id="checkbox_<?=$bill["id"]?>" name="checkbox_<?=$bill["id"]?>" value="<?=$bill["id"]?>" />
				</td>
				<td  style="width:50px; vertical-align: middle;">
					<?=date("m/d",strtotime($bill["entry_datetime"])) ?>
				</td>
				<td  style="width:80px; vertical-align: middle;">
					<?=$bill["company"]["company_side_bar_name"] ?>
				</td>
				<td  style="width: 60px; vertical-align: middle;">
					<?=$bill_link?>
				</td>
				<td  style="width:60px; vertical-align: middle;">
					<?=$corresponding_account["account_name"] ?>
				</td>
				<td  style="width: 70px; vertical-align: middle; text-align:right; padding-right:5px;">
					$<?=number_format($bill["entry_amount"],2) ?>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
</table>
<table style="margin-left:30px;">
	<tr id="pay_account_row" name="pay_account_row">
		<td  style="width:185px; vertical-align: middle;">
			Payment Amount
		</td>
		<td  style="vertical-align: middle;">
			<input type="text" readonly id="payment_amount" name="payment_amount" class="left_bar_input">
		</td>
	</tr>
</table>