<script>
	$("#fleet_manager_name").val("<?=$fm_name?>");
</script>

<table style="margin-left:30px;">	
	
	<tr id="client_pay_account_row" style="display:none;">
		<td style="width:185px; vertical-align: middle;">Client Pay Account</td>
		<td>
			<?php echo form_dropdown('client_pay_account_dropdown',$client_pay_account_options,"Select",'id="client_pay_account_dropdown" onChange="client_pay_account_dropdown_selected()" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="pay_account_row"  style="display:none;">
		<td style="width:185px; vertical-align: middle;">From Pay Account</td>
		<td>
			<?php 
				$options = array();
				$options["Select"] = "Select";
				foreach($these_accounts as $account)
				{
					if($account["category"] == "Pay")
					{
						$options[$account["id"]] = $account["account_name"];
					}
				} 
			?>
			<?php echo form_dropdown('pay_account_dropdown',$options,"Select",'id="pay_account_dropdown" onChange="" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="advance_match_row"  style="display:none;">
		<td style="width:185px; vertical-align: middle;">Advance for Expense</td>
		<td>
			<?php 
				$options = array();
				$options["Select"] = "Select";
				foreach($unfunded_advances as $advance)
				{
					$options[$advance["id"]] = "$".$advance["entry_amount"]." ".$advance["entry_description"]." ".date("m/d",strtotime($advance["entry_datetime"]));
				} 
				$options["OOP"] = "Out of Pocket Expense";
			?>
			<?php echo form_dropdown('advance_dropdown',$options,"Select",'id="advance_dropdown" onChange="advance_selected()" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="load_match_row"  style="display:none;">
		<td style="width:185px; vertical-align: middle;">Load for Expense</td>
		<td>
			<?php 
				$options = array();
				$options["Select"] = "Select";
				foreach($loads_to_match as $load)
				{
					$options[$load["id"]] = $load["customer_load_number"]." ".$load["broker"]["customer_name"];
				} 
				$options["not_load_exp"] = "Not A Load Expense";
				$options["billed"] = "Already Been Billed";
			?>
			<?php echo form_dropdown('load_match_dropdown',$options,"Select",'id="load_match_dropdown" onChange="load_selected()" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="unsettled_load_match_row"  style="display:none;">
		<td style="width:185px; vertical-align: middle;">Load for Expense</td>
		<td>
			<?php 
				$options = array();
				$options["Select"] = "Select";
				foreach($loads_to_match as $load)
				{
					$options[$load["id"]] = substr($load["broker"]["customer_name"],0,10)." ".$load["customer_load_number"];
				} 
			?>
			<?php echo form_dropdown('unsettled_load_match_dropdown',$options,"Select",'id="unsettled_load_match_dropdown" onChange="" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="new_bill_type_row"  style="display:none;">
		<td style="width:185px; vertical-align: middle;">Bill Type</td>
		<td>
			<?php 
				$options = array();
				$options["Select"] = "Select";
				foreach($these_accounts as $account)
				{
					if($account["category"] == "Bill")
					{
						$label = $account["account_name"]." - ".$account["vendor"]["company_name"];
						$options[$account["id"]] = $label;
					}
				} 
			?>
			<?php echo form_dropdown('new_bill_type_dropdown',$options,"Select",'id="new_bill_type_dropdown" onChange="" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="pay_bill_type_row"  style="display:none;">
		<td style="width:185px; vertical-align: middle;">Bill Type</td>
		<td>
			<?php 
				$options = array();
				$options["Select"] = "Select";
				foreach($these_accounts as $account)
				{
					if($account["category"] == "Bill")
					{
						$options[$account["id"]] = $account["account_name"];
					}
				} 
			?>
			<?php echo form_dropdown('pay_bill_type_dropdown',$options,"Select",'id="pay_bill_type_dropdown" onChange="" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="transfer_from_row"  style="display:none;">
		<td style="width:185px; vertical-align: middle;">Transfer FROM Account</td>
		<td>
			<?php 
				$options = array();
				$options["Select"] = "Select";
				foreach($these_accounts as $account)
				{
					$options[$account["id"]] = $account["account_name"];
				} 
			?>
			<?php echo form_dropdown('transfer_from_dropdown',$options,"Select",'id="transfer_from_dropdown" onChange="" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="transfer_to_row"  style="display:none;">
		<td style="width:185px; vertical-align: middle;">Transfer TO Account</td>
		<td>
			<?php 
				$options = array();
				$options["Select"] = "Select";
				foreach($these_accounts as $account)
				{
					$options[$account["id"]] = $account["account_name"];
				} 
			?>
			<?php echo form_dropdown('transfer_to_dropdown',$options,"Select",'id="transfer_to_dropdown" onChange="" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	
	<tr id="estimate_match_row"  style="display:none;">
		<td style="width:185px; vertical-align: middle;">Estimate for Damage</td>
		<td>
			<?php echo form_dropdown('estimate_dropdown',$estimate_options,"Select",'id="estimate_dropdown" onChange="" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	
</table>