<script>
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_fuel_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Expense Owner</span>
		<hr/>
		<?php echo form_dropdown('owner_dropdown',$bill_owner_sidebar_options,"Select",'id="owner_dropdown" style="" class="left_bar_input" onchange="load_expense_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Date</span>
		<hr/>
		<input type="text" id="start_date_filter" name="start_date_filter" onchange="load_expense_report()" class="left_bar_input" placeholder="After"/>
		<br>
		<input type="text" id="end_date_filter" name="end_date_filter" onchange="load_expense_report()" class="left_bar_input" placeholder="Before"/>
		<br>
	</form>
</div>