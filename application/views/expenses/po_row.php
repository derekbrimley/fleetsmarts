<?php
	//MAKE PO DATE TEXT
	$po_date_text = "";
	if(!empty($po["expense_datetime"]))
	{
		$po_date_text = date("m/d/y", strtotime($po["expense_datetime"]));
	}
	
	//MAKE APPROVED DATE TEXT
	$approved_date_text = "";
	if(!empty($po["approved_datetime"]))
	{
		$approved_date_text = date("m/d/y", strtotime($po["approved_datetime"]));
	}
	
	$row = $po["id"];
?>
<table  style="table-layout:fixed; font-size:12px;">
	<tr style="line-height:30px;">
		<?php if(empty($po["approved_datetime"])): ?>
			<td onclick="load_po_view('<?=$row?>')" style="width:30px; padding-left:5px;" VALIGN="top"><img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:8px;"></td>
		<?php else:?>
			<td onclick="load_po_view('<?=$row?>')" style="width:30px; padding-left:5px;" VALIGN="top"><img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:8px;"></td>
		<?php endif; ?>
		
		<td onclick="load_po_view('<?=$row?>')" style="width:70px;" VALIGN="top"><?=$po_date_text?></td>
		<td onclick="load_po_view('<?=$row?>')" style="width:40px;" VALIGN="top"><?=$po["id"]?></td>
		<td onclick="load_po_view('<?=$row?>')" style="width:50px;" VALIGN="top"><?=$po["issuer"]["f_name"]?></td>
		<td onclick="load_po_view('<?=$row?>')" style="width:70px;" VALIGN="top"><?=$po["approved_by"]["f_name"]?></td>
		<td onclick="load_po_view('<?=$row?>')" style="width:90px;" VALIGN="top"><?=$po["owner"]["company_side_bar_name"]?></td>
		<td onclick="load_po_view('<?=$row?>')" style="width:125px;" VALIGN="top"><?=$po["category"]?></td>
		<td onclick="load_po_view('<?=$row?>')" style="min-width:225px; max-width:225px; padding-left:5px;" VALIGN="top" class="ellipsis" title="<?=$po["po_notes"]?>"><?=$po["po_notes"]?></td>
		<td onclick="load_po_view('<?=$row?>')" style="width:75px;" VALIGN="top"><?=$approved_date_text?></td>
		<td onclick="load_po_view('<?=$row?>')" style="min-width:105px; max-width:105px;" VALIGN="top" title="<?=$po["account"]["account_name"]?>"  class="ellipsis"><?=$po["account"]["account_name"]?></td>
		<td onclick="load_po_view('<?=$row?>')" style="width:50px; text-align:right;" VALIGN="top"><?=number_format($po["expense_amount"],2)?></td>
		<?php if(empty($po["approved_datetime"])): ?>
			<?php if($po_is_complete): ?>
				<td style="height:30px;" VALIGN="top"><img src="/images/approve_commission.png" style="height:15px; position:relative; top:6px; left:13px;" onclick="approve_po_from_list('<?=$po["id"]?>')"></td>
			<?php else: ?>
				<td style="height:30px;" VALIGN="top"><img src="/images/approve_commission.png" style="height:15px; position:relative; top:6px; left:13px;" onclick="alert('PO Incomplete!')"></td>
			<?php endif; ?>
		<?php endif; ?>
	</tr>
</table>
