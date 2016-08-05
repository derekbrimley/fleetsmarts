<script>
	$('#after_date_filter').datepicker({ showAnim: 'blind' });
	$('#before_date_filter').datepicker({ showAnim: 'blind' });
</script>
<span style="font-weight:bold;">Owner</span>
<hr/>
<?php echo form_dropdown('owner_sidebar_dropdown',$owner_sidebar_options,'All','onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Driver</span>
<hr/>
<?php echo form_dropdown('client_sidebar_dropdown',$client_sidebar_options,'All','onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Category</span>
<hr/>
<?php echo form_dropdown('category_dropdown',$category_sidebar_options,"All",'id="category_dropdown" onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Dates</span>
<hr/>
<input type="text" id="after_date_filter" name="after_date_filter" onchange="load_report()" class="left_bar_input" placeholder="After"/>
<br>
<input type="text" id="before_date_filter" name="before_date_filter" onchange="load_report()" class="left_bar_input" placeholder="Before"/>
<br>
<br>
<br>
<span style="font-weight:bold;">Status</span>
<hr/>
<a style="margin-right:9px;" href="javascript:clear_check_boxes();">Clear All</a>|<a href="javascript:select_all_check_boxes();">Select All</a>
<br>
<br>
<table>
	<tr style="height:25px;">
		<td style="width:20px; vertical-align:middle;">
			<input id="outstanding_cb" name="outstanding_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_report()">
			<input type="hidden" id="get_outstanding" name="get_outstanding" value="">
		</td>
		<td style="vertical-align:middle; max-width:145px;" class="ellipsis" title="Outstanding">
			Outstanding
		</td>
	</tr>
	<tr style="height:25px;">
		<td style="width:20px; vertical-align:middle;">
			<input id="settled_cb" name="settled_cb" type="checkbox" style="position:relative; bottom:1px;" onclick="load_report()">
			<input type="hidden" id="get_settled" name="get_settled" value="">
		</td>
		<td style="vertical-align:middle; max-width:145px;" class="ellipsis" title="Settled">
			Settled
		</td>
	</tr>
</table>


