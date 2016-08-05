<script>
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('id' => 'download_file_form', 'name'=>'download_file_form', 'target'=>'_blank')?>
	<?=form_open('reports/download_transactions_csv',$attributes);?>
		<br>
		<span style="font-weight:bold;">Dates</span>
		<hr/>
		<input class="left_bar_input" type="text" id="start_date_filter" name="start_date_filter" placeholder="After"/>
		<br>
		<input class="left_bar_input" type="text" id="end_date_filter" name="end_date_filter" placeholder="Before"/>
		<br><br>
		<input class="jq_button left_bar_input" onclick="download_transactions_csv" type="submit"/>
	</form>
</div>