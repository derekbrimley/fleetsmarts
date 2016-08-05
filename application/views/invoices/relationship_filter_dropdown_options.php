<span style="font-weight:bold;"><?=$cust_vend?></span>
<hr/>
<?php
	$options = array(
		"All"	=> 	"All",
	);
?>
<?php echo form_dropdown('relationship_id',$relationship_options,'All','id="relationship_id" onChange="relationship_filter_selected()" class="left_bar_input"');?>
<br>
<br>