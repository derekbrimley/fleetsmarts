<?php $attributes = array('name'=>'upload_receipt_form','id'=>'upload_receipt_form', 'target'=>'_blank' )?>
<?=form_open_multipart('receipts/upload_receipt',$attributes);?>
	<input type="hidden" id="client_expense_id" name="client_expense_id" value="<?=$client_expense_id?>">
	<input type="hidden" id="file_guid" name="file_guid" value="<?=$client_expense["file_guid"]?>">
	<div id="upload_receipt_form_div" style="width:342px; margin:auto; margin-top:20px;">
		<table style="width:350px">
			<tr>
				<td style="width:150px;">
					Who Pays?
				</td>
				<td style="width:200px;">
					<?php 
						$options = array(
								'Select' => 'Select',
								'Business User' => 'Business User',
								'Broker'  => 'Broker',
								'Driver'  => 'Driver',
								'FleetProtect'  => 'FleetProtect',
								'Lost Receipt'  => 'Lost Receipt',
						); 
					?>
					<?php echo form_dropdown('who_pays',$options,"Select",'id="who_pays"  onChange="who_pays_selected()" class="left_bar_input"');?>
				</td>
			</tr>
			<tr id="business_user_row" style="display:none;">
				<td style="">
					Business User
				</td>
				<td>
					<?php echo form_dropdown('business_user_id',$business_users_options,"Select",'id="business_user_id"  onChange="business_user_selected()" class="left_bar_input"');?>
				</td>
			</tr>
			<tr id="expense_account_row" style="display:none;">
				<td style="">
					Expense Account
				</td>
				<td>
					<input type="hidden" id="expense_account_id" name="expense_account_id" value="Select"/>
				</td>
			</tr>
			<tr id="load_row" style="display:none;">
				<td style="">
					Load
				</td>
				<td>
					<?php echo form_dropdown('load_id',$load_options,"Select",'id="load_id"  onChange="show_common_fields()" class="left_bar_input"');?>
				</td>
			</tr>
			<tr id="fleetprotect_row" style="display:none;">
				<td style="">
					FleetProtect Account
				</td>
				<td>
					<?php echo form_dropdown('fp_account_id',$fp_accounts_options,"Select",'id="fp_account_id"  onChange="show_common_fields()" class="left_bar_input"');?>
				</td>
			</tr>
			<tr id="revenue_acc_row" style="display:none;">
				<td style="">
					PA Fee Rev Account
				</td>
				<td>
					<?php echo form_dropdown('rev_account_id',$rev_options,"Select",'id="rev_account_id"  onChange="rev_account_selected()" class="left_bar_input"');?>
				</td>
			</tr>
			<tr id="receipt_amount_row" name="receipt_amount_row" style="display:none;">
				<td style="">
					Receipt Amount
				</td>
				<td>
					<input type="text" id="receipt_amount" name="receipt_amount" class="left_bar_input" />
				</td>
			</tr>
			<tr id="document_row" name="document_row" style="display:none;">
				<td style="vertical-align:bottom;">
					Document Upload
				</td>
				<td style="vertical-align:bottom;">
					<input type="file" id="receipt_file" name="receipt_file" style="width:190px;" />
				</td>
			</tr>
			<tr id="more_receipts_row" name="more_receipts_row" style="display:none;">
				<td style="vertical-align: middle;">
					There are more receipts
				</td>
				<td>
					<input type="hidden" id="more_receipts" name="more_receipts" value="false">
					<input id="more_receipts_cb" name="more_receipts_cb" type="checkbox" style="position:relative; top:5px; right:3px;" onclick="">
				</td>
			</tr>
		</table>
	</div>
	<div id="lost_receipt_div" style="color:black; text-align:center; margin-top:20px; display:none;" >
		PA Fees will be charged to the driver.
	</div>
	<div id="driver_expense_div" style="color:black; text-align:center; margin-top:20px; display:none;" >
		The driver will be charged the FULL expense amount.
	</div>
	<div id="uploading_text" style="display:none; color:black; text-align:center; margin-top:20px;">
		Uploading receipt...
	</div>
</form>