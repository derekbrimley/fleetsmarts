<style>
	hr
	{
		width:156px;
		margin:0px;
		margin-top:7px;
		margin-bottom:7px;
	}
	
	.blue_border
	{
		box-shadow: 0 0 0 3px #6295FC inset;
	}
	
	.upload_button
	{
		width:300px;
		height:50px;
		margin: 0 auto;
		margin-top:20px;
	}
</style>


<html>
	<head>
		<title><?php echo $title;?></title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<link href='http://fonts.googleapis.com/css?family=Homemade+Apple' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
		
		<?php include("expenses/expenses_script.php"); ?>
		<?php include("expenses/expense_allocation_script.php"); ?>
		
		<style>
			.upload_button
			{
				width:300px;
				height:50px;
				margin: 0 auto;
				margin-top:20px;
			}
		</style>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
				<div id="left_bar" style="width:175px">
					<button class='left_bar_button jq_button' id="log_entry" onclick="open_new_transaction_dialog()">New Transaction</button>
					<br>
					<br>
					<?php $attributes = array('name'=>'filter_form','id'=>'filter_form')?>
					<?=form_open('expenses/load_report',$attributes);?>
						<div style="margin-top:15px;" id="scrollable_filter_div"  class="scrollable_div">
							<div id="filter_div">
								<!-- AJAX GOES HERE !-->
							</div>
						</div>
					</form>
				</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<div id="plain_header" style="font-size:16px;">
						<div style="float:right; width:25px;">
							<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
							<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_report()" />
						</div>
						<div id="expense_total" class="header_stats"  style="float:right; width:150px; margin-right:20px; font-weight:bold;"></div>
						<div id="count_total" class="header_stats"  style="float:right; width:120px; font-weight:bold;"></div>
						<div style="float:left; font-weight:bold;">Transactions</div>
					</div>
				</div>
				<!-- REPORT GOES HERE !-->
			</div>
			
		</div>
	</body>
		
	<div id="add_notes" title="Add Note" style="padding:10px; display:none;">
		<div>
			<span id="notes_header" style="font-weight:bold;">Expense Notes</span>
			<br>
			<br>
			<div id="notes_ajax_div" style="height:215px; overflow:auto">
				<!-- AJAX WILL POPULATE THIS !-->
			</div>
		</div>
		<div style="position:absolute; bottom:0">
			<?php $attributes = array('name'=>'add_note_form','id'=>'add_note_form', )?>
			<?=form_open('expenses/add_note/',$attributes);?>
				Add Note:
				<input type="hidden" id="expense_id" name="expense_id">
				<textarea style="width:400px;" rows="3" id="new_note" name="new_note"></textarea>
			</form>
		</div>
	</div>
	
	<div id="split_expense_dialog" title="Split Expense" style="display:none;">
		<?php $attributes = array('name'=>'split_expense_form','id'=>'split_expense_form', )?>
		<?=form_open('expenses/split_expense/',$attributes);?>
			<div style="width:430px; height:205px; margin:auto;">
				<input type="hidden" id="split_expense_id" name="split_expense_id"/>
				<div>
					<span class="heading">Expense Splits</span>
					<span style="float:right"><a href="" name="add_allocation" id="add_allocation" onclick="add_allocation_row();return false;">+ Add</a></span>
					<hr style="width:430px;"/>
				</div>
				<table>
					<tr class="heading">
						<td>
						</td>
						<td style="vertical-align:bottom;">
							Amount
						</td>
						<td style="vertical-align:bottom;">
							Notes
						</td>
					</tr>
					<?php for($i=1;$i<=5;$i++):?>
					<?php 
						$isVisible = "";
						if($i>2)
						{
							$isVisible = "display:none";
						}
					?>
						<tr id="allocation_row_<?=$i?>" name="allocation_row_<?=$i?>" style="<?=$isVisible?>">
							<td style="vertical-align:middle; width:120px;">
								Split <?=$i?>
							</td>
							<td style="vertical-align:middle; width:180px;">
								<input  style="width:120px;text-align:right;" type="text" id="allocation_amount_<?=$i?>" name="allocation_amount_<?=$i?>"  onblur="add_allocations()">
							</td>
							<td style="vertical-align:middle; width:250px;">
								<input  style="width:250px;" type="text" id="allocation_notes_<?=$i?>" name="allocation_notes_<?=$i?>">
							</td>
						</tr>
					<?php endfor;?>
				</table>
			</div>
			<div id="total_allocations" style="width:430px; margin:auto; margin-top:10px; color:red; font-weight:bold; font-size:16px; text-align:right;">
				$00.00
			</div>
		</form>
	</div>
	
	<div id="new_transaction_dialog" title="New Transaction" style="display:none;">
		<?php $attributes = array('name'=>'add_new_entry_form','id'=>'add_new_entry_form', )?>
		<?=form_open('accounts/add_new_entry',$attributes);?>
			<table style="margin-left:30px; margin-top:20px;">
				<tr id="business_entry_type_row" style="" >
					<td style="width:185px;">Entry Type</td>
					<td style="width:155px;">
						<?php 
							$options = array(
									'Select' => 'Select',
									'Transaction Upload'    => 'Transaction Upload',
							); 
						?>
						<?php echo form_dropdown('new_transaction_type_dropdown',$options,"Select",'id="new_transaction_type_dropdown" onChange="new_transaction_type_selected()" class="left_bar_input"');?>
					</td>
					<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
				<tr id="report_type_row" style="display:none;" >
					<td style="">Report Type</td>
					<td>
						<?php 
							$options = array(
									'Select' => 'Select',
									'Comdata Transaction'    => 'Comdata Transaction',
									'Money Code Use' => 'Money Code Issued',
									'SmartPay Cash Load' => 'SmartPay Cash Load',
									'Sparks CC'  => 'Spark CC',
									'TAB Bank'  => 'TAB Bank',
									'Venure (Main) CC'  => 'Venure (Main) CC',
							); 
						?>
						<?php echo form_dropdown('report_type_dropdown',$options,"Select",'id="report_type_dropdown" onChange="report_type_selected()" class="left_bar_input"');?>
					</td>
					<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
			</table>
		</form>
		
		<div id="comdata_report_div" name="comdata_report_div" style="display:none;">
			<?php $attributes = array('name'=>'comdata_upload_form','id'=>'comdata_upload_form', )?>
			<?php echo form_open_multipart('expenses/do_upload/comdata',$attributes);?>
				<table style="margin-left:30px;">
					<tr id="smartpay_account_dropdown_row" style="">
						<td style="width:185px; vertical-align: middle;">Comdata Account</td>
						<td>
							<?php echo form_dropdown('comdata_account_dropdown',$cash_account_options,"Select",'id="comdata_account_dropdown" onChange="" class="left_bar_input"');?>
						</td>
						<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
							*
						</td>
					</tr>
				</table>
				<div style="margin-left:25px; margin-top:25px;">
					<input type="file" name="userfile" class="" />
				</div>
			</form>
			<div style="margin-left:25px;">
				<button onclick="submit_comdata_upload()" style="" class="jq_button upload_button">Upload</button>
			</div>
		</div>
		
		<div id="sp_cash_load_report_div" name="sp_cash_load_report_div" style="display:none;">
			<?php $attributes = array('name'=>'smartpay_upload_form','id'=>'smartpay_upload_form', )?>
			<?php echo form_open_multipart('expenses/do_upload/sp_cash_load',$attributes);?>
				<table style="margin-left:30px;">
					<tr id="smartpay_account_dropdown_row" style="">
						<td style="width:185px; vertical-align: middle;">SmartPay Account</td>
						<td>
							<?php echo form_dropdown('smartpay_account_dropdown',$cash_account_options,"Select",'id="smartpay_account_dropdown" onChange="" class="left_bar_input"');?>
						</td>
						<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
							*
						</td>
					</tr>
				</table>
				<div style="margin-left:25px; margin-top:25px;">
					<input type="file" name="userfile" class="" />
				</div>
			</form>
			<div style="margin-left:25px;">
				<button onclick="submit_smartpay_upload()" style="" class="jq_button upload_button">Upload</button>
			</div>
		</div>
		
		<div id="money_code_report_div" name="money_code_report_div" style="display:none;">
			<?php $attributes = array('name'=>'mc_upload_form','id'=>'mc_upload_form', )?>
			<?php echo form_open_multipart('expenses/do_upload/money_code',$attributes);?>
				<table style="margin-left:30px;">
					<tr id="smartpay_account_dropdown_row" style="">
						<td style="width:185px;">SmartPay Account</td>
						<td>
							<?php echo form_dropdown('mc_smartpay_account_dropdown',$cash_account_options,"Select",'id="mc_smartpay_account_dropdown" onChange="" class="left_bar_input"');?>
						</td>
						<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
							*
						</td>
					</tr>
				</table>
				<div style="margin-left:25px; margin-top:25px;">
					<input type="file" name="userfile" class="" />
				</div>
			</form>
			<div style="margin-left:25px;">
				<button onclick="submit_money_code_upload()" style="" class="jq_button upload_button">Upload</button>
			</div>
		</div>
		
		<div id="sparks_cc_report_div" name="sparks_cc_report_div" style="display:none;">
			<?php $attributes = array('name'=>'smartpay_upload_form','id'=>'smartpay_upload_form', )?>
			<?php echo form_open_multipart('expenses/do_upload/spark_cc',$attributes);?>
				<table style="margin-left:30px;">
					<tr id="smartpay_account_dropdown_row" style="">
						<td style="width:185px; vertical-align: middle;">Spark Account</td>
						<td>
							<?php echo form_dropdown('account_dropdown',$cash_account_options,"Select",'id="account_dropdown" onChange="" class="left_bar_input"');?>
						</td>
						<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
							*
						</td>
					</tr>
				</table>
				<div style="margin-left:25px; margin-top:25px;">
					<input type="file" name="userfile" class="" />
				</div>
				<div style="margin-left:25px;">
					<button onclick="" style="" class="jq_button upload_button">Upload</button>
				</div>
			</form>
		</div>
		
		<div id="tab_bank_div" name="tab_bank_div" style="display:none;">
			<?php $attributes = array('name'=>'tab_upload_form','id'=>'tab_upload_form', )?>
			<?php echo form_open_multipart('expenses/do_upload/tab',$attributes);?>
				<table style="margin-left:30px;">
					<tr id="smartpay_account_dropdown_row" style="">
						<td style="width:185px; vertical-align: middle;">TAB Account</td>
						<td>
							<?php echo form_dropdown('tab_account_dropdown',$cash_account_options,"Select",'id="tab_account_dropdown" onChange="" class="left_bar_input"');?>
						</td>
						<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
							*
						</td>
					</tr>
				</table>
				<div style="margin-left:25px; margin-top:25px;">
					<input type="file" name="userfile" class="" />
				</div>
			</form>
			<div style="margin-left:25px;">
				<button onclick="submit_tab_upload()" style="" class="jq_button upload_button">Upload</button>
			</div>
		</div>
		
		<div id="venture_cc_report_div" name="venture_cc_report_div" style="display:none;">
			<?php $attributes = array('name'=>'venture_cc_upload_form','id'=>'venture_cc_upload_form', )?>
			<?php echo form_open_multipart('expenses/do_upload/venture_cc',$attributes);?>
				<table style="margin-left:30px;">
					<tr id="smartpay_account_dropdown_row" style="">
						<td style="width:185px; vertical-align: middle;">Venture Account</td>
						<td>
							<?php echo form_dropdown('account_dropdown',$cash_account_options,"Select",'id="account_dropdown" onChange="" class="left_bar_input"');?>
						</td>
						<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
							*
						</td>
					</tr>
				</table>
				<div style="margin-left:25px; margin-top:25px;">
					<input type="file" name="userfile" class="" />
				</div>
				<div style="margin-left:25px;">
					<button onclick="" style="" class="jq_button upload_button">Upload</button>
				</div>
			</form>
		</div>
	</div>
	
	<div id="add_new_entry" title="Record Transaction" style="display:none;">
		<!-- AJAX GOES HERE!-->
	</div>
	
	<div id="lock_expense" title="Lock Expense" style="display:none;">
		<!-- AJAX GOES HERE!-->
	</div>
</html>