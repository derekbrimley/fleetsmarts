<?php
	date_default_timezone_set('America/Denver');
?>
<script>
	$('#qd_policy_current_since').datepicker({ showAnim: 'blind' });
</script>
<div style="margin:20px;">
	<form id="new_quote_form" name="new_quote_form">
		<table>
			<tr style="height:30px;">
				<td style="width:180px;">
					Quote or Policy?
				</td>
				<td>
					<?php $options = array(
						'Select'  	=> 'Select' ,
						'Quote'  	=> 'Quote',
						'Policy'  	=> 'Policy',
						); ?>
					<?php echo form_dropdown('quote_or_policy',$options,"Select",'id="quote_or_policy" style="width:161px; height:21px;" onclick="quote_or_policy_selected()"');?>
				</td>
			</tr>
			<tr id="policy_number_row" style="display:none; height:30px;">
				<td style="width:180px;">
					Policy Number
				</td>
				<td>
					<input type="text" id="qd_policy_number" name="qd_policy_number" style="width:161px; height:21px;"/>
				</td>
			</tr>
			<tr id="quote_id_row" style="display:none; height:30px;">
				<td style="width:180px;">
					Quote ID
				</td>
				<td>
					<?php
						$quote_id = "Q".date("ymdHis");
					?>
					<?=$quote_id?>
					<input type="hidden" id="quote_id" name="quote_id" value="<?=$quote_id?>"/>
				</td>
			</tr>
			<tr id="active_since_row" style="display:none; height:30px;">
				<td style="width:180px;">
					Active Since
				</td>
				<td>
					<input type="text" id="qd_policy_current_since" name="qd_policy_current_since" style="width:161px; height:21px;"/>
				</td>
			</tr>
		</table>
	</form>
</div>