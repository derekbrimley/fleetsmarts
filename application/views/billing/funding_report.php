<script>
	$("#scrollable_content").height($("#body").height() - 195);
	
	$("#edit_cell_form").submit(function (e) {
        e.preventDefault(); //prevent default form submit
		save_edit_cell();
    });
</script>
<style>
	.header_stats
	{
		font-size:12px;
	}
</style>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_funding_report()" />
		</div>
		<div style="float:right; width:25px; margin-right:10px;">
			<img id="print_icon" name="print_icon" src="/images/printer.png" title="Print View" style="cursor:pointer; float:right; height:18px; padding-top:7px;" onclick="printer_icon_pressed()" />
		</div>
		<div id="percentage" class="header_stats"  style="width:60px; float:right; font-weight:bold; text-align:right; margin-right:10px;" title="Loads Funded vs Loads Dropped">%</div>
		<div id="funded_total" class="header_stats"  style="width:130px; float:right; font-weight:bold; text-align:right;">Funded </div>
		<div id="short_total" class="header_stats"  style="width:100px; float:right; font-weight:bold; text-align:right;">Short Paid </div>
		<div id="billed_total" class="header_stats"  style="width:120px; float:right; font-weight:bold; text-align:right;">Billed </div>
		<div id="expected_total" class="header_stats"  style="width:140px; float:right; font-weight:bold; text-align:right;">Expected </div>
		<div id="unfunded_count" class="header_stats"  style="width:100px; float:right; font-weight:bold; text-align:right;">Missing HC </div>
		<div id="count_total" class="header_stats"  style="float:right; font-weight:bold;">Count </div>
		<div style="float:left; font-weight:bold;">Billing</div>
	</div>
</div>
<table  style="table-layout:fixed; margin-top:5px; font-size:12px;">
	<tr class="heading" style="line-height:12px;">
		<td style="width:25px; padding-left:5px;" VALIGN="top"></td>
		<td style="width:60px;" VALIGN="top" class="pending_td">Pending</td>
		<td style="width:70px; padding-right:5px;" VALIGN="top">Load</td>
		<td style="width:30px;" VALIGN="top" class="fm_td">FM</td>
		<td style="width:30px;" VALIGN="top" class="dm_td">DM</td>
		<td style="width:30px;" VALIGN="top">ARS</td>
		<td style="width:55px;" VALIGN="top" class="driver1_td">Driver 1</td>
		<td style="width:55px;" VALIGN="top" class="driver2_td">Driver 2</td>
		<td style="width:60px;" VALIGN="top">Carrier</td>
		<td style="width:60px;" VALIGN="top">Broker</td>
		<td style="width:50px; padding-right:5px; text-align:right;" VALIGN="top">Expected</td>
		<td style="width:60px; padding-right:5px;" VALIGN="top" class="drop_city_td">Drop City</td>
		<td style="width:50px; text-align:right;" VALIGN="top">Pushed</td>
		<td style="width:50px; padding-right:5px; text-align:right;" VALIGN="top">Billed</td>
		<td style="width:50px;" VALIGN="top">Method</td>
		<td style="width:50px; text-align:right;" VALIGN="top">Expect<br>Payment</td>
		<td style="width:45px; text-align:right;" VALIGN="top" class="age_td">Age</td>
		<td style="width:50px; text-align:right;" VALIGN="top" class="short_td">Short</td>
		<td style="width:50px; text-align:right;" VALIGN="top">Funded</td>
		<td style="width:85px; padding-left:10px; padding-right:5px; display:none;" VALIGN="top" class="last_update_td">Hold Reason</td>
		<td style="width:300px; display:none;" VALIGN="top" class="last_update_td">Last Update</td>
		<td style="width:25px;" VALIGN="top"></td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
		<?php 
			$i = 0;
			$funded_total = 0;
			$billed_total = 0;
			$short_paid = 0;
			$expected = 0;
			$numerator = 0;
			$denominator = 0;
			$missing_hc = 0;
		?>
		<?php foreach($loads as $load):?>
			<?php
				$background_color = "";
				if($i%2 == 0)
				{
					$background_color = "background-color:#F7F7F7;";
				}
				$i++;
				
				//NUMERATOR
				//$numerator = $numerator + $funded_amount + $short_amount;
				
				if(!empty($load["funded_datetime"]))
				{
					$numerator++;
				}
				
				if(empty($load["hc_processed_datetime"]))
				{
					$missing_hc++;
				}
				
				//echo $funded_total."<br>";
				$billed_total = $billed_total + $load["amount_billed"];
				
				//SHORT PAID TOTAL
				$short_paid = $short_paid + $load["amount_short_paid"];

				//EXPECTED TOTAL
				$expected = $expected + $load["expected_revenue"];
				
				if(!empty($load["amount_funded"]))
				{
					$funded_amount = round($load["amount_funded"]+$load["financing_cost"],2);
					$funded_total = $funded_total + $funded_amount;
					//echo $funded_amount." -- ";
					
					
				}
			?>
			<div id="row_<?=$load["id"]?>" style="height:30px; <?=$background_color?>" class="clickable_row">
				<?php include("billing_row.php"); ?>
			</div>
			<div id="details_<?=$load["id"]?>" style="display:none; font-size:12px; width:945px; margin-left:15px; min-height:30px; padding:10px; background:#EFEFEF;">
				<!-- AJAX GOES HERE !-->
				<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />
			</div>
		<?php endforeach;?>
</div>
<script>
	$("#expected_total").html("Expected <?=number_format($expected,2)?>");
	$("#short_total").html("Short <?=number_format($short_paid,2)?>");
	$("#funded_total").html("Funded <?=number_format($funded_total,2)?>");
	$("#billed_total").html("Billed <?=number_format($billed_total,2)?>");
	<?php if($i > 0):?>
		$("#percentage").html("<?=number_format($numerator/$i*100,2)?>%");
	<?php else:?>
		$("#percentage").html("0%");
	<?php endif;?>
	$("#unfunded_count").html("Missing HC <?=$missing_hc?>");
	$("#count_total").html("Count <?=$i?>");
	
	
</script>
