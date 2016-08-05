<script>
	$("#scrollable_content").height($("#body").height() - 195);
	
	$("#edit_cell_form").submit(function (e) {
        e.preventDefault(); //prevent default form submit
		save_edit_cell();
    });
	
	function load_print_view()
	{
		//GET FILTER VARIABLES
		populate_hidden_checkbox_fields();
		$("#print_broker_dropdown").val($("#broker_dropdown").val());
		$("#print_fleet_managers_dropdown").val($("#fleet_managers_dropdown").val());
		$("#print_funding_status_dropdown").val($("#funding_status_dropdown").val());
		$("#print_drop_start_date_filter").val($("#drop_start_date_filter").val());
		$("#print_drop_end_date_filter").val($("#drop_end_date_filter").val());
		$("#print_billing_start_date_filter").val($("#billing_start_date_filter").val());
		$("#print_billing_end_date_filter").val($("#billing_end_date_filter").val());
		$("#print_funding_start_date_filter").val($("#funding_start_date_filter").val());
		$("#print_funding_end_date_filter").val($("#funding_end_date_filter").val());
		$("#print_get_factors").val($("#get_factors").val());
		$("#print_get_direct_bills").val($("#get_direct_bills").val());
		
		$("#funding_report_print").submit();
		
		//window.open($("filter_form").submit);
		
		//window.open("reports/load_funding_report/"+fleet_manager_dropdown+"/"+billing_start_date_filter+"/"+billing_end_date_filter+"/"+funding_start_date_filter+"/"+funding_end_date_filter+"/"+get_factors+"/"+get_direct_bills+"/");
	}
</script>
<style>
	.header_stats
	{
		font-size:12px;
	}
</style>
<form id="funding_report_print" action="<?=base_url('index.php/reports/load_funding_report_printable')?>" target="_blank" method="post" accept-charset="utf-8">
	<input type="hidden" id="print_broker_dropdown" name="print_broker_dropdown"/>
	<input type="hidden" id="print_fleet_managers_dropdown" name="print_fleet_managers_dropdown"/>
	<input type="hidden" id="print_funding_status_dropdown" name="print_funding_status_dropdown"/>
	<input type="hidden" id="print_drop_start_date_filter" name="print_drop_start_date_filter"/>
	<input type="hidden" id="print_drop_end_date_filter" name="print_drop_end_date_filter"/>
	<input type="hidden" id="print_billing_start_date_filter" name="print_billing_start_date_filter"/>
	<input type="hidden" id="print_billing_end_date_filter" name="print_billing_end_date_filter"/>
	<input type="hidden" id="print_funding_start_date_filter" name="print_funding_start_date_filter"/>
	<input type="hidden" id="print_funding_end_date_filter" name="print_funding_end_date_filter"/>
	<input type="hidden" id="print_get_factors" name="print_get_factors"/>
	<input type="hidden" id="print_get_direct_bills" name="print_get_direct_bills"/>
</form>

<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_funding_report()" />
		</div>
		<div id="" class="header_stats" style="width:35px; float:right; font-weight:normal; font-size:12px; text-align:right; margin-right:15px; position:relative; top:8px;"><img style="cursor:pointer; height:15px;" src="/images/printer.png" onclick="load_print_view()"></div>
		<div id="percentage" class="header_stats"  style="width:60px; float:right; font-weight:bold; text-align:right;" title="Loads Funded vs Loads Dropped">%</div>
		<div id="funded_total" class="header_stats"  style="width:130px; float:right; font-weight:bold; text-align:right;">Funded </div>
		<div id="short_total" class="header_stats"  style="width:100px; float:right; font-weight:bold; text-align:right;">Short Paid </div>
		<div id="billed_total" class="header_stats"  style="width:120px; float:right; font-weight:bold; text-align:right;">Billed </div>
		<div id="expected_total" class="header_stats"  style="width:140px; float:right; font-weight:bold; text-align:right;">Expected </div>
		<div id="unfunded_count" class="header_stats"  style="width:100px; float:right; font-weight:bold; text-align:right;">Missing HC </div>
		<div id="count_total" class="header_stats"  style="float:right; font-weight:bold;">Count </div>
		<div style="float:left; font-weight:bold;">Funding Report</div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:30px; padding-left:5px;" VALIGN="top">FM</td>
		<td style="width:110px;" VALIGN="top">Load</td>
		<td style="width:100px;" VALIGN="top">Broker</td>
		<td style="width:55px; padding-left:10px;" VALIGN="top">Method</td>
		<td style="width:85px; text-align:right;" VALIGN="top">Expected</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Drop Date</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Bill Date</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Billed</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Funded Date</td>
		<td style="width:95px; text-align:right;" VALIGN="top">Short Paid</td>
		<td style="width:90px; text-align:right; padding-right:5px;" VALIGN="top">Funded</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<table  style="table-layout:fixed; margin:5px; font-size:12px;">
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
				$i++;
				
				$fm_initials = substr($load["fleet_manager"]["f_name"],0,1).substr($load["fleet_manager"]["l_name"],0,1);
				
				$billing_date = "";
				if(!empty($load["billing_datetime"]))
				{
					$billing_date = date("n/d/y",strtotime($load["billing_datetime"]));
				}
				
				$drop_date = "";
				if(!empty($load["final_drop_datetime"]))
				{
					$drop_date = date("n/d/y",strtotime($load["final_drop_datetime"]));
				}
				
				$funding_date = "";
				if(!empty($load["funded_datetime"]))
				{
					$funding_date = date("n/d/y",strtotime($load["funded_datetime"]));
					$numerator++;
				}
				
				if(empty($load["hc_processed_datetime"]))
				{
					$missing_hc++;
				}
				
				$background_color = "";
				if($i%2 == 1)
				{
					$background_color = "background-color:#F2F2F2;";
				}
				
				$billed_text = "";
				if(!empty($load["amount_billed"]))
				{
					$billed_text =number_format($load["amount_billed"],2);
					
					//DENOMINATOR
					//$denominator = $denominator + $load["amount_billed"];
				}
				else
				{
					//DENOMINATOR
					//$denominator = $denominator + $load["expected_revenue"];
				}
				
				$funded_amount = 0;
				$funded_text = "";
				if(!empty($load["amount_funded"]))
				{
					$funded_amount = round($load["amount_funded"]+$load["financing_cost"],2);
					$funded_total = $funded_total + $funded_amount;
					$funded_text = number_format($funded_amount,2);
					//echo $funded_amount." -- ";
					
					
				}

				$short_amount = 0;
				$short_text = "";
				$short_color = "";
				if(!empty($load["amount_short_paid"]))
				{
					$short_amount = $load["amount_short_paid"];
					
					if($load["amount_short_paid"] > .015 || $load["amount_short_paid"] < -.015)
					{
						$short_text = number_format($load["amount_short_paid"],2);
						
						if($load["amount_short_paid"] > 0)
						{
							$short_color = " color:red; ";
						}
						else
						{
							$short_color = " color:green; ";
						}
					}
				}
				
				//NUMERATOR
				//$numerator = $numerator + $funded_amount + $short_amount;
				
				
				
				//echo $funded_total."<br>";
				$billed_total = $billed_total + $load["amount_billed"];
				
				//SHORT PAID TOTAL
				$short_paid = $short_paid + $load["amount_short_paid"];

				//EXPECTED TOTAL
				$expected = $expected + $load["expected_revenue"];
				
			?>
			<tr class="" style="line-height:30px; <?=$background_color?>">
				<td style="width:30px; padding-left:5px;" VALIGN="top"><?=$fm_initials ?></td>
				<td style="width:110px;" VALIGN="top"><?=$load["customer_load_number"]?></td>
				<td style="min-width:100px; max-width:100px;" VALIGN="top" class="ellipsis" title="<?=$load["broker"]["customer_name"]?>"><?=$load["broker"]["customer_name"]?></td>
				<td style="width:55px; padding-left:10px;" VALIGN="top"><?=$load["billing_method"]?></td>
				<td style="width:85px; text-align:right;" VALIGN="top"><?=number_format($load["expected_revenue"],2)?></td>
				<td style="width:100px; text-align:right;" VALIGN="top"><?=$drop_date?></td>
				<td style="width:100px; text-align:right;" VALIGN="top"><?=$billing_date?></td>
				<td style="width:100px; text-align:right;" VALIGN="top"><?=$billed_text?></td>
				<td style="width:100px; text-align:right;" VALIGN="top"><?=$funding_date?></td>
				<td style="width:95px; text-align:right; font-weight:bold; <?= $short_color ?>" VALIGN="top"><?=$short_text?></td>
				<td style="width:90px; text-align:right; padding-right:5px;" VALIGN="top"><?=$funded_text?></td>
			</tr>
		<?php endforeach;?>
	</table>
</div>
<script>
	$("#expected_total").html("Expected <?=number_format($expected,2)?>");
	$("#short_total").html("Short <?=number_format($short_paid,2)?>");
	$("#funded_total").html("Funded <?=number_format($funded_total,2)?>");
	$("#billed_total").html("Billed <?=number_format($billed_total,2)?>");
	$("#percentage").html("<?=number_format($numerator/$i*100,2)?>%");
	$("#unfunded_count").html("Missing HC <?=$missing_hc?>");
	$("#count_total").html("Count <?=$i?>");
	
	
</script>
