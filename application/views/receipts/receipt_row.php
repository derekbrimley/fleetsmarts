<?php
	$client_expense_id = $client_expense["id"];
	
	//DETERMINE ACTION ICON
	
	$action_icon = "/images/receipt_pending.png";
	$action_title = "Upload Receipt";
	$action = "open_upload_receipt('$client_expense_id')";

	//FORMAT EXPENSE DATETIME TEXT
	$expense_date_text = "";
	if(!empty($client_expense["expense_datetime"]))
	{
		$expense_date_text = date("m/d/y",strtotime($client_expense["expense_datetime"]));
	}
	
	//GET EXPENSE OWNER
	$where = null;
	$where["id"] = $client_expense["owner_id"];
	$owner = db_select_company($where);
	
	//GET EXPENSE CLIENT
	$where = null;
	$where["id"] = $client_expense["client_id"];
	$client = db_select_client($where);
	
	//FORMAT RECEIPT RECEIVED DATETIME TEXT
	$receipt_date_text = "";
	if(!empty($client_expense["receipt_datetime"]))
	{
		$receipt_date_text = date("m/d/y",strtotime($client_expense["receipt_datetime"]));
		$action_icon = "/images/receipt_received.png";
		$action_title = "Pending Settlement";
		$action = "";
	}
	
	//FORMAT EXPENSE DATETIME TEXT
	$settled_date_text = "";
	if(!empty($client_expense["paid_datetime"]))
	{
		$settled_date_text = date("m/d/y",strtotime($client_expense["paid_datetime"]));
		$action_icon = "/images/receipt_received.png";
		$action_title = "Settled";
		$action = "";
	}
	
	//DETERMINE NOTES IMAGE
	if(empty($client_expense["receipt_notes"]))
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
		<input type="hidden" id="row" name="row" value="<?=$client_expense["id"]?>">
		<td style="min-width:30px; max-width:30px; padding-left:5px;" >
			<img id="action_icon_<?=$client_expense["id"]?>" name="action_icon_<?=$client_expense["id"]?>" title="<?=$action_title?>" onclick="<?=$action?>" style="cursor:pointer; position:relative; left:2px; top:6px; height:16px;" src="<?=$action_icon?>" />
		</td>
		<td style="min-width:60px; max-width:60px;" VALIGN="top">
			<?=$expense_date_text?>
		</td>
		<td style="min-width:100px; max-width:100px;" class="ellipsis" title="<?=$owner["company_side_bar_name"]?>" VALIGN="top">
			<?=$owner["company_side_bar_name"]?>
		</td>
		<td style="min-width:100px; max-width:100px; padding-left:5px;" class="ellipsis" title="<?=$client["company"]["company_side_bar_name"]?>" VALIGN="top">
			<?=$client["company"]["company_side_bar_name"]?>
		</td>
		<td style="min-width:120px; max-width:120px; padding-left:5px;" class="ellipsis" title="<?=$client_expense["category"]?>" VALIGN="top">
			<?=$client_expense["category"]?>
		</td>
		<td style="min-width:290; max-width:290; padding-left:5px;" class="ellipsis" VALIGN="top" title="<?=$client_expense["description"]?>">
			<?=$client_expense["description"]?>
		</td>
		<td style="min-width:50px; max-width:50px; padding-left:5px;" VALIGN="top">
			<?=$receipt_date_text?>
		</td>
		<td style="min-width:60px; max-width:60px; text-align:right;" VALIGN="top">
			<?php if(!empty($client_expense["receipt_amount"])): ?>
				<span class="link" onclick="refresh_row('<?=$client_expense["id"]?>')">$<?=number_format($client_expense["receipt_amount"],2)?></span>
			<?php endif; ?>
		</td>
		<td style="min-width:60px; max-width:60px; text-align:right;" VALIGN="top">
			<?=number_format($client_expense["expense_amount"],2)?>
		</td>
		<td style="min-width:40px; max-width:40px; padding-left:10px;" VALIGN="top">
			<?php if(empty($client_expense["file_guid"])):?>
			<img id="paperclip_<?=$client_expense["id"]?>" src="/images/paper_clip2.png" style="height:20px; margin-left:10px; position:relative; left:6px; top:4px; cursor:pointer;" onclick="$('#row_receipt_file_<?=$client_expense["id"]?>').click()"/>
			<img id="loading_receipt_<?=$client_expense["id"]?>" src="/images/loading.gif" style="height:15px; margin-left:10px; position:relative; left:3px; top:4px; display:none;"/>
			<form id="row_receipt_upload_form_<?=$client_expense["id"]?>" enctype="multipart/form-data">
				<input type="hidden" id="client_expense_id" name="client_expense_id" value="<?=$client_expense["id"]?>">
				<input type="file" id="row_receipt_file_<?=$client_expense["id"]?>" name="row_receipt_file" style="display:none;" onchange="submit_receipt_file('<?=$client_expense["id"]?>')" />
			</form>
			<?php else:?>
				<a href="<?=base_url("/index.php/documents/download_file")."/".$client_expense["file_guid"]?>" target="_blank">Receipt</a>
			<?php endif;?>
		</td>
		<td style="overflow:hidden; min-width:30px;  max-width:30px; cursor:default; padding-left:10px;" VALIGN="top" >
			<img id="expense_notes_<?=$client_expense["id"]?>" name="expense_notes_<?=$client_expense["id"]?>" title="<?=$client_expense['receipt_notes']?>" onclick="open_notes('<?=$client_expense["id"]?>')" style="cursor:pointer; position:relative; left:2px; top:6px; height:16px; width:16px" src="<?=$notes_img?>" />
		</td>
	</tr>
</table>