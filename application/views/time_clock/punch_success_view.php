<style>
</style>

<script>
	$(document).ready(function(){
		
		setInterval(function(){location.reload();},2000);
		
	});
	
	
</script>
<?php
?>

<div id="main_box" name="main_box" style="float:left; width:55%; text-align:center;">
	<div style="margin-top:200px;">
		<span style="font-size:60px;">Punch Successful!</span>
	</div>
	<img src="/images/tko.gif" style="height:300px; margin-top:100px;"/>
</div>
<div id="right_bar" style=" border:solid 1px #CFCFCF; width:520px; height:95%; float:right;">
	<div style="height:40px; line-height:40px; font-size:20px; font-weight:bold; background-color:#CFCFCF; text-align:center;">
		Punch Log
	</div>
	<div style="width:500px; margin:auto;">
		<table style="width:500px; margin:auto; margin-top:15px;  font-size:16px;">
		<?php
			$i = 0;
		?>
		<?php foreach($punches as $punch):?>
			<?php
				$i++;
			?>
			<tr style="height:30px;">
				<td style="width:40px;">
					<?php if($i == 1):?>
						<?php if($punch["in_out"] == "In"):?>
							<img src="/images/green_dot.png" style="height:20px; position:relative; top:3px;"/>
						<?php else:?>
							<img src="/images/red_dot.png" style="height:20px; position:relative; top:3px;"/>
						<?php endif;?>
					<?php endif;?>
				</td>
				<td style="width:60px;">
					<?=$punch["in_out"]?>
				</td>
				<td style="width:200px;">
					<?=$user["person"]["full_name"]?>
				</td>
				<td style="width:200px;">
					<?=date('m/d/y H:i:s',strtotime($punch["datetime"]))?>
				</td>
			</tr>
		<?php endforeach;?>
		</table>
	</div>
</div>