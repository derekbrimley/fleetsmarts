<script>
	//ADD ALLOCATION ROW
	var last_row = 1;
	function add_allocation_row()
	{
		last_row = last_row + 1;
		//alert(last_row);
		$("#allocation_row_"+last_row).show();
	}
	
	function add_allocations()
	{
		var invoice_amount = <?=$invoice["invoice_amount"]?>;
		var total_allocations = 0;
		for(i = 1;i<=20;i++)
		{
			var this_amount = $("#allocation_amount_"+i).val();
			total_allocations = Math.round((total_allocations + Number(this_amount))*100)/100;
		}
		
		alert(total_allocations);
		alert(invoice_amount);
		if(total_allocations != invoice_amount)
		{
			$("#total_allocations").css('color','red');
		}
		else if(total_allocations == invoice_amount)
		{
			$("#total_allocations").css('color','green');
		}
		
		$("#these_allocations").val(total_allocations);
		$("#total_allocations").html("$"+total_allocations);
	}
	
</script>


<div style="padding:20px;">
	<table style="">
		<tr style="">
			<td style="width:175px">
				<?=$invoice["vendor"]["company_side_bar_name"]?>
			</td>
			<td style="width:165px; text-align:center;">
				<?=$invoice["bill_type"]?>
			</td>
			<td style="width:175px; text-align:right;">
				Unit <?=$invoice["unit_number"]?>
			</td>
		</tr>
		<tr style="">
			<td style="width:130px">
				Invoice <?=$invoice["invoice_number"]?>
			</td>
			<td style="width:150px; text-align:center;">
				<?=$date_range ?>
			</td>
			<td style="font-size:16px; font-weight:bold; width:130px; text-align:right;">
				$<?=number_format($invoice["invoice_amount"],2)?>
			</td>
		</tr>
	</table>
	<br>
	<br>
	<?php $attributes = array('name'=>'allocate_invoice_form','id'=>'allocate_invoice_form', )?>
	<?=form_open('invoices/allocate_invoice',$attributes);?>
		<?php echo form_hidden('filter',$filter); ?>
		<?php echo form_hidden('view',$view); ?>
		<?php echo form_hidden('account_id',$account_id); ?>
		<?php echo form_hidden('vendor_id',$vendor_id); ?>
		<?php echo form_hidden('this_id',$this_id); ?>
		<input id="these_allocations" type="hidden" value="0">
		<input id="invoice_amount" type="hidden" value="<?=$invoice["invoice_amount"]?>">
		<input id="invoice_id" name="invoice_id" type="hidden" value="<?=$invoice["id"]?>">
		<input id="invoice_number" name="invoice_number" type="hidden" value="<?=$invoice["invoice_number"]?>">
		<input id="bill_owner_account_id" name="bill_owner_account_id" type="hidden" value="<?=$invoice["account_id"]?>">
		<table style="">
			<tr style="line-height:20px;">
				<td  style="width:175px; vertical-align:bottom;">
					Invoice Link
				</td>
				<td style="vertical-align:bottom">
					<input type="text" id="allocated_invoice_link" name="allocated_invoice_link" style="width:320px;">
				</td>
				<td style="color:red; font-weight:bold;  vertical-align:bottom; padding-left:5px;">
					*
				</td>
			</tr>
		</table>
		<br>
		<br>
		<span class="heading">Allocations</span>
		<span style="float:right"><a href="#" onclick="add_allocation_row()">+ Add</a></span>
		<hr>
		<table style="">
			<tr style="line-height:20px; font-weight:bold;">
				<td style="vertical-align:bottom">
					Owner
				</td>
				<td style="vertical-align:bottom">
					Expense Type
				</td>
				
				<td style="vertical-align:bottom">
					Notes
				</td>
				<td style="vertical-align:bottom; text-align:right;">
					Amount
				</td>
			</tr>
			<?php for($i = 1; $i <= 20; $i++): ?>
				<?php
					$display = "display:none";
					if($i == 1)
					{
						$display = "";
					}
				?>
				<tr id="allocation_row_<?=$i?>" name="allocation_row_<?=$i?>" style="<?=$display?>">
					<td style="min-width:110px">
						<?php echo form_dropdown('owner_dropdown_'.$i,$bill_owner_sidebar_options,"Select",'id="owner_dropdown_'.$i.'" onChange="" style="width:95px; position:relative; right:2px;"');?>
					</td>
					<td id="client_account_row_<?=$i?>" name="client_account_row_<?=$i?>" style="min-width:110px;">
						<?php 
							$options = array(
									'Select' => 'Select',
									'Non-Standard' => 'Non-Standard',
									'Standard' => 'Standard',
							); 
						?>
						<?php echo form_dropdown('expense_type_'.$i,$options,"Select",'id="expense_type_'.$i.'" onChange="" style="width:95px; position:relative; right:2px;"');?>
					</td>
					<td style="min-width:220px;">
						<input type="text" id="allocation_notes_<?=$i?>" name="allocation_notes_<?=$i?>" style="width:210px;">
					</td>
					<td style="min-width:75px; text-align:right;">
						<input type="text" id="allocation_amount_<?=$i?>" name="allocation_amount_<?=$i?>" style="text-align:right; width:75px;" onblur="add_allocations()">
					</td>
				</tr>
			<?php endfor; ?>
			<tr style="line-height:50px;">
				<td>
				</td>
				<td>
				</td>
				
				<td>
				</td>
				<td id="total_allocations" style="color:red; font-weight:bold; font-size:16px; text-align:right;">
					$00.00
				</td>
			</tr>
		</table>
	</form>
</div>