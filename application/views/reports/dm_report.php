<script>
	$("#scrollable_content").height($("#body").height() - 155);
	
	$("#edit_cell_form").submit(function (e) {
        e.preventDefault(); //prevent default form submit
		save_edit_cell();
    });
</script>
<style>
	.indent
	{
		
	}
</style>
<?php
	date_default_timezone_set('America/Denver');
	$now_datetime = date("n/d/y H:i");
?>
<div id="main_content_header">
	<div id="plain_header">
		<div style="float:left; font-weight:bold;">Driver Manager Report</div>
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_dm_report()" />
		<div id="print_icon" style="width:35px; float:right; font-weight:normal; font-size:12px; text-align:right; margin-right:15px; position:relative; top:8px;"><a target="blank" href="<?=base_url("index.php/reports/print_dm_report/".$fm_company["person_id"])?>"><img style="cursor:pointer; height:15px;" title="Print" src="/images/printer.png" onclick=""></a></div>
	</div>
</div>
<div id="scrollable_content" class="scrollable_div">
	<?php include("dm_report_contents.php"); ?>
</div>