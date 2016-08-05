<title>Adjustments</title>
<?php $attributes = array('name'=>'adjustment_form','id'=>'adjustment_form', )?>
<?=form_open('accounts/submit_adjustment',$attributes);?>
	<div style="width:250px; margin:auto; margin-top:125px; font-family:arial;">
		<div style="font-size:26px; margin-bottom:15px;">
			Adjusting Entry
		</div>
		<table>
			<tr>
				<td style="width:100px;">
					Account ID 
				</td>
				<td>
					<input type="text" id="account_id" name="account_id"/>
				</td>
			</tr>
			<tr>
				<td style="">
					Amount 
				</td>
				<td>
					<input type="text" id="amount" name="amount"/>
				</td>
			</tr>
			<tr>
				<td style="">
					Notes
				</td>
				<td>
					<textarea style="width:153px; position:relative; left:2px;" id="notes" name="notes">Adjusting entry</textarea>
				</td>
			</tr>
		</table>	
		<button style="width:125px; height:40px; margin: auto; margin-top:25px; display:block" type="submit">Make Adjustment</button>
	</div>
</form>