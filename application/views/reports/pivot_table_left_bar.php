<script>
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_pivot_table_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Dates</span>
		<hr/>
		<input class="left_bar_input" type="text" id="start_date_filter" name="start_date_filter" onchange="load_pivot_table_report()" placeholder="After"/>
		<br>
		<input class="left_bar_input" type="text" id="end_date_filter" name="end_date_filter" onchange="load_pivot_table_report()" placeholder="Before"/>
		<br>
	</form>
</div>