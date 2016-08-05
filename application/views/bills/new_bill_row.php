<?php
	$row = $bill_holder["id"];
	
	$bill_date_text = date("m/d/y", strtotime($bill_holder["bill_datetime"]));
	//$created_date_text = date("m/d/y", strtotime($bill_holder["bill_datetime"]));
	
	//GET FROM COMPANY
	$where = null;
	$where["id"] = $bill_holder["company_id"];
	$payer_company = db_select_company($where);
	
	//GET FROM COMPANY
	$where = null;
	$where["id"] = $bill_holder["from_company_id"];
	$from_company = db_select_company($where);
	
	//GET RELATIONSHIP
	$where = null;
	$where["relationship"] = "Vendor";
	$where["business_id"] = $payer_company["id"];
	$where["related_business_id"] = $from_company["id"];
	$relationship = db_select_business_relationship($where);
	
	
?>
<table  style="table-layout:fixed; font-size:12px;">
	<tr class="" style="line-height:30px;">
		<td style="min-width:30px; max-width:30px; padding-left:5px;" VALIGN="top">
			<?php 
				/**
				THIS USED TO BE A CHECK BOX
				<input id="bill_holder_cb_<?=$row?>" name="bill_holder_cb_<?=$row?>" type="checkbox" class="" style="position:relative; top:5px;" onclick="event.stopImmediatePropagation();new_bill_cb_clicked('<?=$row?>','<?=$bill_holder["company_id"]?>')">
				**/
			?>
			
			<img src="/images/accept_circle.png" id="bill_holder_cb_<?=$row?>" name="bill_holder_cb_<?=$row?>" title="Accept Bill" class="" style="position:relative; top:7px; height:15px;" onclick="event.stopImmediatePropagation();new_bill_cb_clicked('<?=$row?>','<?=$bill_holder["company_id"]?>')">
		</td>
		<td style="min-width:80px; max-width:80px;" VALIGN="top">
			<?=$bill_date_text?>
		</td>
		<td style="min-width:100px; max-width:100px;" VALIGN="top">
			<?=$payer_company["company_side_bar_name"]?>
		</td>
		<td style="min-width:100px; max-width:100px;" VALIGN="top">
			<?=$from_company["company_side_bar_name"]?>
		</td>
		<td style="min-width:100px; max-width:100px;" VALIGN="top">
			<?php if(!empty($bill_holder["file_guid"])):?>
				<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$bill_holder["file_guid"]?>" onclick="event.stopImmediatePropagation();"><?=$invoice["invoice_number"]?></a>
			<?php else:?>
				<?=$invoice["invoice_number"]?>
			<?php endif;?>
		</td>
		<td style="min-width:450px; max-width:450px; padding-left:15px;" title="<?=$bill_holder["description"]?>" VALIGN="top" class="ellipsis">
			<?=$bill_holder["description"]?>
		</td>
		<td style="min-width:60px; max-width:60px; text-align:right; padding-left:15px;" VALIGN="top">
			<?=number_format($bill_holder["amount"],2)?>
		</td>
	</tr>
</table>