<style type="text/css">
	tr
	{
		height:50px;
		vertical-align:bottom;
	}
	
	div
	{
		font-family: arial;
	}
</style>
<head>
	<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
	<title><?=$invoice["invoice_number"]?></title>
</head>	
	<?php
	//CREATE DATE RANGE TEXT
	$date_range = date("n/d",strtotime($invoice["start_datetime"]))." - ".date("n/d",strtotime($invoice["end_datetime"]));
	if($invoice["start_datetime"] == $invoice["end_datetime"])
	{
		$date_range = date("n/d/y",strtotime($invoice["end_datetime"]));
	}
?>
<div style="margin-left:15px;">
	<table style="margin-bottom:40px;">
		<tr>
			<td colspan="2" style="font-size:16px; font-weight:bold;">
				INVOICE COVERSHEET
			</td>
			<td style="150">
			</td>
			<td style="width:70px">
				Date
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
				<?=$date_range?>
			</td>
		</tr>
		<tr>
			<td style="width:70px">
				Vendor
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
				<?=$invoice["vendor"]["company_side_bar_name"]?>
			</td>
			<td style="width:50px;">
			</td>
			<td style="width:70px">
				Invoice
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
				<?=$invoice["invoice_number"]?>
			</td>
		</tr>
		<tr>
			<td style="width:70px">
				Bill Type
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
				<?=$invoice["bill_type"]?>
			</td>
			<td style="">
			</td>
			<td style="width:70px">
				Unit
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
				<?=$invoice["unit_number"]?>
			</td>
		</tr>
		<tr>
			<td style="width:70px">
				Owner
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
				<?=$invoice["account"]["company_side_bar_name"]?>
			</td>
			<td style="">
			</td>
			<td style="width:70px">
				Miles
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
				<?=number_format($invoice["miles"])?>
			</td>
		</tr>
	</table>
	<span style="font-weight:bold;">Notes</span>
	
	<div style="height:180px; width:690px; border: solid 1pt; padding:10px; font-size:12px;">
		<?=str_replace("\n","<br>",$invoice["invoice_notes"])?>
	</div>
	
	<div style="margin-top:30px; text-align:center; width:710px;">
		Invoice Allocations
	</div>
	
	<table style="margin-bottom:60px;">
		<tr>
			<td style="width:70px">
				Payer
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
			<td style="width:50px;">
			</td>
			<td style="width:70px">
				Amount
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
		</tr>
		<tr>
			<td style="width:70px">
				Payer
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
			<td style="width:50px;">
			</td>
			<td style="width:70px">
				Amount
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
		</tr>
		<tr>
			<td style="width:70px">
				Payer
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
			<td style="width:50px;">
			</td>
			<td style="width:70px">
				Amount
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
		</tr>
		<tr>
			<td style="width:70px">
				Payer
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
			<td style="width:50px;">
			</td>
			<td style="width:70px">
				Amount
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
		</tr>
		<tr>
			<td style="width:70px">
				Payer
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
			<td style="width:50px;">
			</td>
			<td style="width:70px">
				Amount
			</td>
			<td style="text-align:center; width:250px; border-bottom: solid 1pt;">
			</td>
		</tr>
		<tr style="height:60px;">
			<td style="width:70px">
			</td>
			<td style="text-align:center; width:250px;">
			</td>
			<td style="width:50px;">
			</td>
			<td style="font-weight:bold; width:70px">
				TOTAL
			</td>
			<td style="font-size:20px; text-align:center; width:250px; border-bottom: solid 2pt;">
				<?=number_format($invoice["invoice_amount"],2)?>
			</td>
		</tr>
	</table>
	
	<table>
		<tr style="height:18px;">
			<td style="width:535px;">
				Payment Date __________________
			</td>
			<td style="width:140px;">
				Allocated to Payers
			</td>
			<td style="border: solid 1pt; width:18px;">
			</td>
		</tr>
	</table>
	
</div>