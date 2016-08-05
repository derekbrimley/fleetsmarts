<script>
	//SET HEIGHT OF MAIN CONTENT AND SCROLLABLE CONTENT
	$("#main_content").height($(window).height() - 115);
	$("#scrollable_content").height($("#main_content").height() - 90);
</script>
<div id="main_content_header">
	<span style="font-weight:bold;">Policies | <?=count($active_profiles)?></span>
	<img id="loading_img" src="/images/loading.gif" style="display:none;float:right;margin-top:4px;height:20px;"/>
</div>

<table style="margin-top:20px;">
	<tr class="heading" style="font-size:12px;">
		<td style="width:30px;">
			
		</td>
		<td style="width:90px;">
			Policy
		</td>
		<td style="width:70px; padding-right:10px;">
			Insurer
		</td>
		<td style="width:90px; padding-right:10px;">
			Agent
		</td>
		<td style="width:90px;">
			Insured
		</td>
		<td style="width:40px; text-align:right;">
			Term
		</td>
		<td style="width:55px; text-align:right;">
			Cost/<br>Month
		</td>
		<td style="width:55px; text-align:right;">
			Trucks
		</td>
		<td style="width:55px; text-align:right;">
			Phys<br>Dam
		</td>
		<td style="width:55px; text-align:right;">
			Auto<br>Liab
		</td>
		<td style="width:55px; text-align:right;">
			DT<br>Rental
		</td>
		<td style="width:55px; text-align:right;">
			Cargo
		</td>
		<td style="width:55px; text-align:right;">
			Reefer<br>BD
		</td>
		<td style="width:55px; text-align:right;">
			Trailers
		</td>
		<td style="width:55px; text-align:right;">
			Days Till<br>Cancel
		</td>
		<td style="width:55px; text-align:right;">
			Cancel<br>Date
		</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php if(!empty($active_profiles)):?>
		<?php
			$i = 0;
		?>
		<?php foreach($active_profiles as $pr):?>
			<?php
				$row_background_style = "";
				if($i%2 == 0)
				{
					$row_background_style = "background-color:#F7F7F7;";
				}
				$i++;
				//echo $pr["id"]
			?>
			<?php include("application/views/equipment/insurance/ins_by_policy_row.php"); ?>
		<?php endforeach;?>
	<?php else:?>
		No Active Policies for this Snapshot Date
	<?php endif;?>
</div>