<tr id="member_or_business_row" style="display:none;">
	<td style="width:180px;">Business or Member</td>
	<td>
		<?php
			$options = array(
						"Select" => "Select",
						"Business" => "Business",
						"Member" => "Member",
						);
		?>
		<?php echo form_dropdown('member_or_business',$options,'Select','id="member_or_business" onChange="member_or_business_selected()" class="left_bar_input"');?>
	</td>
</tr>
<tr id="member_row" style="display:none;">
	<td style="width:180px;">Member</td>
	<td>
		<?php
			$options = array(
						"Select" => "Select",
						);
		?>
		<?php echo form_dropdown('member_id',$member_options,'Select','id="member_id" onChange="member_selected()" class="left_bar_input"');?>
	</td>
</tr>
<script>
	<?=$script?>
</script>
