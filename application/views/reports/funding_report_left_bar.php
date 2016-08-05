<script>
	$("#filter_list").height($(window).height() - 225);
	
	$('#drop_start_date_filter').datepicker({ showAnim: 'blind' });
	$('#drop_end_date_filter').datepicker({ showAnim: 'blind' });
	$('#billing_start_date_filter').datepicker({ showAnim: 'blind' });
	$('#billing_end_date_filter').datepicker({ showAnim: 'blind' });
	$('#funding_start_date_filter').datepicker({ showAnim: 'blind' });
	$('#funding_end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', 'target'=>'_blank' )?>
	<?=form_open('reports/load_funding_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Broker</span>
		<hr/>
		<?php echo form_dropdown('broker_dropdown',$broker_dropdown_options,"All",'id="broker_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Fleet Manager</span>
		<hr/>
		<?php echo form_dropdown('fleet_managers_dropdown',$fleet_managers_dropdown_options,"All",'id="fleet_managers_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Funding Status</span>
		<hr/>
		<?php $options = array(
			'All' => 'All',
			'Funded'    => 'Funded',
			'Unfunded'  => 'Unfunded',
			); 
		?>
		<?php echo form_dropdown('funding_status_dropdown',$options,"All",'id="funding_status_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
		<br>
		<?php $options = array(
			'All' => 'All',
			'Open'    => 'Open',
			'Closed'  => 'Closed',
			); 
		?>
		<?php echo form_dropdown('closed_status_dropdown',$options,"All",'id="closed_status_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Drop Date</span>
		<hr/>
		<input type="text" id="drop_start_date_filter" name="drop_start_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="After"/>
		<br>
		<input type="text" id="drop_end_date_filter" name="drop_end_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="Before"/>
		<br>
		<br>
		<span style="font-weight:bold;">Billing Date</span>
		<hr/>
		<input type="text" id="billing_start_date_filter" name="billing_start_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="After"/>
		<br>
		<input type="text" id="billing_end_date_filter" name="billing_end_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="Before"/>
		<br>
		<br>
		<span style="font-weight:bold;">Funding Date</span>
		<hr/>
		<input type="text" id="funding_start_date_filter" name="funding_start_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="After"/>
		<br>
		<input type="text" id="funding_end_date_filter" name="funding_end_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="Before"/>
		<br>
		<br>
		<span style="font-weight:bold;">Billing Type</span>
		<hr/>
		<table>
			<tr style="height:25px;">
				<td style="width:20px; vertical-align:middle;">
					<input id="factor_cb" name="factor_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_funding_report()">
					<input type="hidden" id="get_factors" name="get_factors" value="">
				</td>
				<td style="vertical-align:middle;">
					Factor
				</td>
			</tr>
			<tr style="height:25px;">
				<td style="vertical-align:middle;">
					<input id="direct_bill_cb" name="direct_bill_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_funding_report()">
					<input type="hidden" id="get_direct_bills" name="get_direct_bills" value="">
				</td>
				<td style="vertical-align:middle;">
					Direct Bill
				</td>
			</tr>
		</table>
		
	</form>
</div>