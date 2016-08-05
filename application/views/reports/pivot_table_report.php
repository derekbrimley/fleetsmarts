
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Income Statement" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_income_statement()" />
		</div>
		<div style="float:left; font-weight:bold;">Pivot Table Report <?= date('m/d/y',strtotime($after_date)) ?> - <?= date('m/d/y',strtotime($before_date)) ?></div>
	</div>
</div>
<?php $attributes = array('id' => 'download_file_form', 'name'=>'download_file_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('tickets/download_leg_csv',$attributes)?>
	<div style="font-weight:bold; width: 320px; margin-top:15px; padding-left:5px;">
		Leg Report
	</div>
	<table style="width: 370px; margin:auto; margin-top:15px;">
		<tr>
			<td style="vertical-align:middle; color:red; width:5px;">
				*
			</td>
			<td style="vertical-align:middle; width:120px;">
				Start Date
			</td>
			<td style="width:200px; padding-top:5px;">
				<input type="text" id="attachment_name" name="attachment_name" class="" />
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle; color:red;">
				*
			</td>
			<td style="vertical-align:middle;">
				End Date
			</td>
			<td style=" padding-top:5px;">
				<input type="file" id="attachment_file" name="attachment_file" class="" />
			</td>
		</tr>
	</table>
</form>