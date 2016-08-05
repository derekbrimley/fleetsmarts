<?php
	$i = 0;
	$is_first_incomplete_gp = true;
?>
<?php if(!empty($goalpoints)):?>
	<?php foreach($goalpoints as $goalpoint):?>
		<?php include("goalpoint_row.php"); ?>
	<?php endforeach;?>
<?php endif;?>