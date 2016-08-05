<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
<title>SmartPay Report</title>
<div style="width:900px; margin:auto; margin-top:25px; font-family:arial;">
	<?php $attributes = array('name'=>'add_smartpay_transactions_form','id'=>'add_smartpay_transactions_form')?>
	<?=form_open('accounts/add_smartpay_transactions',$attributes);?>
		<div style="width:900px; margin-bottom:25px; text-align:center; font-weight:bold;">		
			<?=$report_name?> | <?=$account["account_name"]?>
		</div>
		<div style="width:400px; margin:auto; font-size:14px; font-weight:bold;">
			<button id="upload_transactions" name="upload_transactions" class="jq_button" style="width:200px; height:50px; margin:auto;">Upload to Database</button>
		</div>
		<table style="margin-top:20px; font-size:14px;">
			<?php 
				$i = 0;
			?>
			<?php foreach($entries as $entry): ?>
				<?php
					$datetime = $entry["entry_datetime"];
					$amount = $entry["entry_amount"];
					$description = str_replace("&amp;"," hello ",$entry["entry_description"]);
					$where = " entry_datetime = '$datetime' AND entry_amount = $amount AND entry_description LIKE '%$description%' ";
					
					//echo $description."<br>";
					//echo db_select_account_entry($where)["id"]."<br><br>";
					
					$row_style = "";
					$link = "";
					$exists = false;
					if(entry_exists($entry))
					{
						$exists = true;
						$row_style = "color:red;";
					}
					else if(empty($entry["account_id"]))
					{
						$exists = true;
						$row_style = "color:orange; font-size:20px;";
						echo "<div style='margin-top:15px; width:900px; font-weight:bold; color:red; text-align:center;'><span style='font-size:30px'>STOP!!!</span><br>FLEETSMARTS CANNOT FIND AN ACCOUNT TO ASSIGN THIS ENTRY<br><span style='font-weight:normal; font-size:14px; color:black;'>".$entry["entry_description"]."</span><br> CHECK THE CARD NUMBER... DO NOT UPLOAD!</span><br><br>";
					}
					else
					{
						$i++;
						$link = base_url("/uploads/$file_name");
					}
					
					
					//GET THIS ACCOUNT
					$where = null;
					@$where["id"] = $entry["account_id"];
					$allocation_account = db_select_account($where);
					
					//IF THIS IS A SPARK UPLOAD
					if($allocation_account["account_group"] == "Spark CC")
					{
					
						//GET THIS FM'S PAY ACCOUNT
						$where = null;
						$where["company_id"] = $allocation_account["company_id"];
						$where["category"] = "Pay";
						$fm_pay_account = db_select_account($where);
						
						//GET COMPANY
						$where = null;
						$where["id"] = $allocation_account["company_id"];
						$company = db_select_company($where);
						
						//MAKE SURE THERE IS A FM ACCOUNT
						if(empty($fm_pay_account) && $company["type"]=="Fleet Manager")
						{
							echo "<span style='font-weight:bold; color:red;'>STOP!!! FLEETSMARTS CANNOT FIND A FLEET MANAGER PAY ACCOUNT TO ASSIGN CARD NUMBER<br>".$entry["entry_description"]." TO... DO NOT UPLOAD!</span><br><br>";
						}
					
					}
					
				?>
				<tr style="<?=$row_style?>">
					<?php if(!$exists): ?>
						<input type="hidden" id="expense_type_<?=$i?>" name="expense_type_<?=$i?>" value="<?=$entry["expense_type"]?>">
						<input type="hidden" id="account_id_<?=$i?>" name="account_id_<?=$i?>" value="<?=$entry["account_id"]?>">
						<input type="hidden" id="entry_datetime_<?=$i?>" name="entry_datetime_<?=$i?>" value="<?=$entry["entry_datetime"]?>">
						<input type="hidden" id="entry_type_<?=$i?>" name="entry_type_<?=$i?>" value="<?=$entry_type?>">
						<input type="hidden" id="entry_description_<?=$i?>" name="entry_description_<?=$i?>" value="<?=$entry["entry_description"]?>">
						<input type="hidden" id="entry_link_<?=$i?>" name="entry_link_<?=$i?>" value="<?=$link?>">
						<input type="hidden" id="entry_amount_<?=$i?>" name="entry_amount_<?=$i?>" value="<?=$entry["entry_amount"]?>">
						<input type="hidden" id="entry_debit_credit_<?=$i?>" name="entry_debit_credit_<?=$i?>" value="<?=$entry["debit_credit"]?>">
					<?php endif; ?>
					<td style="width:200px;">
						<?=$entry["entry_datetime"]?>
					</td>
					<td style="width:600px;">
						<?=$entry["entry_description"]?>
					</td>
					<td style="width:100px; text-align:right;">
						<?php if($entry["debit_credit"] == "Debit"): ?>
							(<?=number_format($entry["entry_amount"],2)?>)
						<?php else: ?>
							<?=number_format($entry["entry_amount"],2)?>
						<?php endif;?>
					</td>
				</tr>
			<?php endforeach; ?>
			<input type="hidden" id="number_of_trans" name="number_of_trans" value="<?=$i?>">
			
		</table>
	</form>
	
</div>