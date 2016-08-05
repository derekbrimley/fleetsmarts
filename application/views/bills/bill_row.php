<?php
	$row = $invoice["id"];
	
	//STYLE CHECKBOX
	$check_box_style = "";
	$cb_disabled = "";
	if($relationship_id == 'All')
	{
		$cb_disabled = "disabled";
	}
	
	//echo $relationship_id;
	
	$date_text = date("m/d/y", strtotime($invoice["invoice_datetime"]));
	
	//GET RELATIONSHIP
	$where = null;
	$where["id"] = $invoice["relationship_id"];
	$relationship = db_select_business_relationship($where);
	
	//GET CUSTOMER/VENDOR
	$where = null;
	$where["id"] = $relationship["related_business_id"];
	$customer_vendor = db_select_company($where);
	
	//GET PAYEE
	$where = null;
	$where["id"] = $relationship["business_id"];
	$business_user = db_select_company($where);
	
	//DETERMINE NOTES IMAGE
	if(empty($invoice["invoice_notes"]))
	{
		$notes_img = "/images/add_notes_empty.png";
	}
	else
	{
		$notes_img = "/images/add_notes.png";
	}
	
?>
<table  style="table-layout:fixed; font-size:12px;">
	<tr class="" style="line-height:30px;">
		<td style="min-width:30px; max-width:30px; padding-left:5px;" VALIGN="top"  onclick="event.stopImmediatePropagation();">
			<input type="checkbox" <?=$cb_disabled?> class="" style="position:relative; top:5px;" name="payment_approval_cb_<?=$row?>" id="payment_approval_cb_<?=$row?>" onclick="event.stopImmediatePropagation();cb_changed('<?=$row?>')">
		</td>
		<td style="min-width:100px; max-width:80px;" VALIGN="top">
			<?=$date_text?>
		</td>
		<td style="min-width:80px; max-width:80px;" class="ellipsis" title="<?=$business_user["company_side_bar_name"]?>" VALIGN="top">
			<?=$business_user["company_side_bar_name"]?>
		</td>
		<td style="min-width:80px; max-width:80px; padding-left:5px;" class="ellipsis" title="<?=$customer_vendor["company_side_bar_name"]?>" VALIGN="top">
			<?=$customer_vendor["company_side_bar_name"]?>
		</td>
		<td style="min-width:100px; max-width:100px;" VALIGN="top" onclick="event.stopImmediatePropagation();">
			<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$invoice["file_guid"]?>" onclick=""><?=$invoice["invoice_number"]?></a>
		</td>
		<td style="min-width:120px; max-width:110px; padding-left:10px;" title="<?=$invoice["invoice_category"]?>" VALIGN="top" class="ellipsis">
			<?=$invoice["invoice_category"]?>
		</td>
		<td style="min-width:270px; max-width:270px; padding-left:15px;" title="<?=$invoice["invoice_description"]?>" VALIGN="top" class="ellipsis">
			<?=$invoice["invoice_description"]?>
		</td>
		<td style="min-width:60px; max-width:60px; text-align:right; padding-left:15px;" VALIGN="top">
			<?=number_format($invoice["invoice_amount"],2)?>
		</td>
		<td style="min-width:85px; max-width:100px; text-align:right;" VALIGN="top">
			<?=number_format(get_invoice_balance($invoice),2)?>
		</td>
		<td style="overflow:hidden; min-width:30px;  max-width:30px;  cursor:default; padding-left:10px;" VALIGN="top" >
			<img title="<?=$invoice['invoice_notes']?>" onclick="event.stopImmediatePropagation();open_invoice_notes('<?=$invoice["id"]?>')" style="cursor:pointer; position:relative; left:2px; top:6px; height:16px; width:16px" src="<?=$notes_img?>" />
		</td>
	</tr>
</table>