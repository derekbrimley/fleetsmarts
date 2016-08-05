
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_statement" name="refresh_statement" src="/images/refresh.png" title="Refresh Income Statement" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_carrier_driver_report()" />
		</div>
		<div style="float:left; font-weight:bold;"><?=$carrier['company_name']?> Report</div>
	</div>
</div>
<div id="driver_container" style="margin-top:10px;margin:10px 150px;border:1px solid #CFCFCF;">
	<div id="driver_header" style="font-weight:bold;height:25px;background-color:#CFCFCF">
		<span style="margin-left:5px;position:relative;top:4px;font-size:16px;">Drivers</span>
	</div>
	<div id="drivers_list" style="margin-top:10px;margin-left:10px;overflow:auto;">
		<?php foreach($companies as $driver): ?>
			<div style="height:20px;">
				<?= $driver ?>
			</div>
		<?php endforeach ?>
	</div>
</div>
<script>
	$("#driver_container").height($(window).height() - 213);
	$("#drivers_list").height($(window).height() - 249);
</script>