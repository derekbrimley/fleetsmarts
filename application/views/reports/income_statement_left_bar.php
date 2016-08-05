<script>
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_income_statement',$attributes);?>
		<br>
		<span style="font-weight:bold;">Business User</span>
		<hr/>
		<?php echo form_dropdown('business_user_dropdown',$business_users_options,"Select",'id="business_user_dropdown" style="" class="left_bar_input" onchange="load_income_statement()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Date</span>
		<hr/>
		<input class="left_bar_input" type="text" id="start_date_filter" name="start_date_filter" onchange="load_income_statement()" placeholder="After"/>
		<br>
		<input class="left_bar_input" type="text" id="end_date_filter" name="end_date_filter" onchange="load_income_statement()" placeholder="Before"/>
		<br>
	</form>
</div>