<?php echo form_dropdown('account_dropdown_'.$i,$account_dropdown_options,"Select",'id="account_dropdown_'.$i.'" onChange="account_selected(\''.$i.'\')" style="width:90px;  position:relative; right:2px;"');?>
<script>
	damage_account[<?=$i?>] = <?=$damage_account_id?>;
</script>
