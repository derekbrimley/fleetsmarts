<script>
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_financial_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Dates</span>
		<hr/>
		<input type="text" id="start_date_filter" name="start_date_filter" onchange="load_financial_report()" placeholder="Start Date"/>
		<br>
		<input type="text" id="end_date_filter" name="end_date_filter" onchange="load_financial_report()" placeholder="End Date"/>
		<br>
		<br>
	</form>
</div>