<html>
	<head>
		<title><?php echo $title;?></title>
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
	</head>
</html>
<body style="font-family:arial;">
	<div style="width:920px; margin:auto; text-align:center;">
		<div style="margin-top:25px; margin-bottom:25px; font-size;20px; font-weight:bold;">
			Transaction <?=$transaction["id"]?> | <?=$transaction["category"]?>
		</div>
		<table style="font-size:12px; width:920px; margin-bottom:20px;">
			<tr class="heading">
				<td style="width:50px; padding-left:10px;">
					Entry Date
				</td>
				<td style="width:50px; padding-left:10px;">
					Recorded Date
				</td>
				<td style="width:100px; padding-left:10px;">
					Owner
				</td>
				<td style="width:250px; padding-left:10px;">
					Account
				</td>
				<td style="width:400px; padding-left:10px;">
					Description
				</td>
				<td style="width:50px; padding-left:10px;text-align:right;">
					Debit
				</td>
				<td style="width:50px; padding-left:10px;text-align:right;">
					Credit
				</td>
				<td style="width:20px; padding-left:10px;text-align:right;">
					ID
				</td>
			</tr>
			<?php foreach($account_entries as $account_entry):?>
				<?php
					//GET ACCOUNT
					$where = null;
					$where["id"] = $account_entry["account_id"];
					$account = db_select_account($where);
					
					//GET ACCOUNT OWNER
					$where = null;
					$where["id"] = $account["company_id"];
					$owner_company = db_select_company($where);
					
					$debit_text = "";
					$credit_text = "";
					
					if($account_entry["debit_credit"] == "Debit")
					{
						$debit_text = number_format($account_entry["entry_amount"],2);
					}
					else if($account_entry["debit_credit"] == "Credit")
					{
						$credit_text = number_format($account_entry["entry_amount"],2);
					}
				?>
				<tr style="">
					<td style="width:50px; padding-left:10px; padding-top:10px;">
						<?=date("m/d/y",strtotime($account_entry["entry_datetime"]))?>
					</td>
					<td style="width:50px; padding-left:10px; padding-top:10px;">
						<?=date("m/d/y",strtotime($account_entry["recorded_datetime"]))?>
					</td>
					<td style="width:100px; padding-left:10px; padding-top:10px;">
						<?=$owner_company["company_side_bar_name"]?>
					</td>
					<td style="width:250px; padding-left:10px; padding-top:10px;">
						<?=$account["account_name"]?>
					</td>
					<td style="width:350px; padding-left:10px; padding-top:10px;">
						<?=$account_entry["entry_description"]?>
					</td>
					<td style="width:75px; padding-left:10px; padding-top:10px;text-align:right;">
						<?=$debit_text?>
					</td>
					<td style="width:75px; padding-left:10px; padding-top:10px;text-align:right;">
						<?=$credit_text?>
					</td>
					<td style="width:20px; padding-left:10px; padding-top:10px;text-align:right;">
						<?=$account_entry["id"]?>
					</td>
				</td>
				</tr>
			<?php endforeach;?>
		</table>
		<?php $attributes = array('name'=>'reverse_transaction_form','id'=>'reverse_transaction_form')?>
		<?=form_open('accounts/reverse_transaction',$attributes);?>
			<input type="hidden" name="transaction_id" id="transaction_id" value="<?=$transaction["id"]?>" />
			<button class='left_bar_button jq_button' style="width:200px; margin:auto;" id="reverse_transaction_button" onclick="$('#reverse_transaction_form').submit();">Reverse Transaction</button>
		</form>
	</div>
</body>