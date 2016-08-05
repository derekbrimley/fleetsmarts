<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
	.clickable_row:hover
	{
		background:#D5D5D5!important;
	}
</style>
<div id="main_content_header">
	<span style="font-weight:bold;" onclick="load_invoice_details(<?=$invoice["id"]?>)">Invoice <?=$invoice["invoice_number"]?></span>
	<div style="float:right; width:25px;">
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img id="back" name="back" src="/images/back.png" title="Back" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_bills_view('Old Bills')" />
	</div>
</div>
<div id="scrollable_content" class="scrollable_div">
	<?php 
		//GET CUSTOMER/VENDOR
		$invoice_type = $invoice["invoice_type"];
		
		//GET CUSTOMER/VENDOR ACCOUNT
		$where = null;
		if($invoice_type == "Revenue Generated" || $invoice_type == "Deposit Receivable")
		{
			$where["id"] = $invoice["debit_account_id"];
		}
		else if($invoice_type == "Expense Incurred" || $invoice_type == "Deposit Payable")
		{
			$where["id"] = $invoice["credit_account_id"];
		}
		$customer_vendor_account = db_select_account($where);
		
		//GET RELATIONSHIP
		$where = null;
		$where["id"] = $customer_vendor_account["relationship_id"];
		$relationship = db_select_business_relationship($where);
		
		//GET CUSTOMER/VENDOR
		$where = null;
		$where["id"] = $relationship["related_business_id"];
		$customer_vendor = db_select_company($where);
		
		//GET INVOICE STATUS TEXT
		if(empty($invoice["closed_datetime"]))
		{
				$status_text = "Open";
		}
		else
		{
			$status_text = "Closed ".date("m/d/y",strtotime($invoice["closed_datetime"]));
		}
		
		
		
		
	?>
	<div style="padding:10px; margin-top:15px;">
		<span class="heading">Bill Summary</span>
		<hr style="width:auto;">
		<br>
		<table style="width:470px; float:left;">
			<tr>
				<td style="width:190px; font-weight:bold;">
				Invoice Number
				</td>
				<td style="width:190px;">
					<?=$invoice["invoice_number"];?>
				</td>
			</tr>
			<tr>
				<td style=" font-weight:bold;">
				Date
				</td>
				<td style="">
					<?=date('m/d/y',strtotime($invoice["invoice_datetime"]));?>
				</td>
			</tr>
			<tr>
				<td style=" font-weight:bold;">
				Vendor
				</td>
				<td style="">
					<?=$customer_vendor["company_side_bar_name"]?>
				</td>
			</tr>
			<tr>
				<td style=" font-weight:bold;">
				Description
				</td>
				<td style="">
					<?=$invoice["invoice_description"];?>
				</td>
			</tr>
		</table>
		<table style="margin-left:50px; width:450px; float:left;">
			<tr>
				<td style="width:150px; font-weight:bold;">
				Bill Type
				</td>
				<td style="width:230px;">
					<?=$invoice["invoice_type"];?>
				</td>
			</tr>
			<tr>
				<td style=" font-weight:bold;">
				Bill Amount
				</td>
				<td style="">
					$<?=number_format($invoice["invoice_amount"],2);?>
				</td>
			</tr>
			<tr>
				<td style=" font-weight:bold;">
				Category
				</td>
				<td style="">
					<?=$invoice["invoice_category"];?>
				</td>
			</tr>
			<tr>
				<td style=" font-weight:bold;">
				Status
				</td>
				<td style="">
					<?=$status_text;?>
				</td>
			</tr>
		</table>
	</div>
	<div style="clear:both; padding:10px; padding-top:50px;">
		<span class="heading">Notes</span>
		<hr style="width:auto;">
		<br>
		<?=str_replace("\n","<br>",$invoice["invoice_notes"]);?>
	</div>
	<div style="padding:10px; margin-top:30px;">
		<span class="heading">Payment History</span>
		<span style="float:right; font-weight:bold; font-size:16px;">Balance $<?=number_format(get_invoice_balance($invoice),2)?></span>
		<hr style="width:auto;">
		<br>
		<?php if(!empty($invoice_payments)):?>
			<table>
				<?php foreach($invoice_payments as $payment):?>
						<tr>
							<td style="width:100px;">
								<?=date('m/d/y',strtotime($payment["account_entry"]["entry_datetime"]))?>
							</td>
							<td style="width:780px;">
								<?=$payment["account_entry"]["entry_description"]?>
							</td>
							<td style="width:100px; text-align:right;">
								$<?=number_format($payment["account_entry"]["entry_amount"],2)?>
							</td>
						</tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
	</div>
</div>
<script>
</script>

