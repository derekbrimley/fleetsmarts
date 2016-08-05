<form id="cancel_policy_form">
	<input type="hidden" id="cancel_policy_id" name="cancel_policy_id" value="<?=$policy_id?>"/>
		<div style="margin:20px;">
			Make sure you want to mark this policy cancelled. There is no going back after this!
		</div>
	<table style="margin:20px; margin-top:40px;">
		<tr>
			<td>
				Cancel Reason
			</td>
			<td style="padding-bottom:10px;">
				<textarea id="cancel_reason" name="cancel_reason" style="width:156px;"></textarea>
			</td>
		</tr>
		<tr>
			<td style="width:100px; padding-top:5px;">
				Cancelled as of
			</td>
			<td>
				<span class="" style="">
					<input class="" type="text" id="cancel_date" name="cancel_date" style="text-align:center; width:70px; height:20px;"/>
					<?php
						$options = array();
						for($i=0; $i<=12; $i++)
						{
							if($i<10)
							{
								$minute = "0".$i;
							}
							else
							{
								$minute = $i;
							}
							$options[$minute] = $minute;
						}
					?>
					<?php echo form_dropdown("cancel_hour",$options,"00","id='cancel_hour' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
					<span style="margin-left:-3px; margin-right:-3;">:</span>
					<?php
						$options = array();
						for($i=0; $i<=60; $i++)
						{
							if($i<10)
							{
								$second = "0".$i;
							}
							else
							{
								$second = $i;
							}
							$options[$second] = $second;
						}
					?>
					<?php echo form_dropdown("cancel_minute",$options,"00","id='cancel_minute' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
				</span>
			</td>
		</tr>
	</table>
</form>