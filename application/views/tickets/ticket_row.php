<?php
	$row = $ticket["id"];
	
	if($ticket["truck_or_trailer"] == "Truck")
	{
		$unit_number = $ticket['truck_number'];
	}
	else
	{
		$unit_number = $ticket['trailer_number'];
	}
?>
<table  style="table-layout:fixed; font-size:12px;">
	<tr style="line-height:30px;">
		<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:50px;max-width:50px;padding-left:5px;"><?=$ticket["id"]?></td>
		<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:57px;max-width:57px;" VALIGN="top"><?=$unit_number?></td>
		<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:70px;max-width:70px;" VALIGN="top"><?=$ticket["category"]?></td>
		<td onclick="open_sub_ticket('<?=$row?>')" style="max-width:192px; min-width:192px; padding-right:5px;" class="ellipsis" title="<?=$ticket["description"]?>" VALIGN="top"><?=$ticket["description"]?></td>
		<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:114px;max-width:114px;" VALIGN="top"><?=$ticket["responsible_party"]?></td>
		
		<?php if(!is_null($ticket["incident_date"])):?>
			<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;" VALIGN="top"><?=date('m/d/y',strtotime($ticket["incident_date"]))?></td>
		<?php else: ?>
			<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;" VALIGN="top"></td>
		<?php endif ?>
		
		<?php if(!is_null($ticket["action_item_due_date"])):?>
			<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;" VALIGN="top"><?=date('m/d/y',strtotime($ticket["action_item_due_date"]))?></td>
		<?php else: ?>
			<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;" VALIGN="top"></td>
		<?php endif ?>
		
		<?php if(!is_null($ticket["estimated_completion_date"])):?>
			<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;" VALIGN="top"><?=date('m/d/y',strtotime($ticket["estimated_completion_date"]))?></td>
		<?php else: ?>
			<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;" VALIGN="top"></td>
		<?php endif ?>
		
		<?php if(!is_null($ticket["completion_date"])):?>
			<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;" VALIGN="top"><?=date('m/d/y',strtotime($ticket["completion_date"]))?></td>
		<?php else: ?>
			<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;" VALIGN="top"></td>
		<?php endif ?>
		<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;padding-right:5px;text-align:right" ><?=number_format($ticket['amount'],2)?></td>
		<td onclick="open_sub_ticket('<?=$row?>')" style="min-width:75px;max-width:75px;padding-right:5px;text-align:right" ><?=number_format(get_ticket_balance($ticket),2)?></td>
		<td onclick="open_ticket_note('<?=$row?>')" style="min-width:29px;max-width:29px;text-align:right;"
			<?php if(empty($ticket['notes'])):?>
				>
				<img style="position:relative; right:5px; height:16px;padding-left:10px;padding-top:5px;" src="/images/add_notes_empty.png"/>
			<?php else: ?>
				title="<?=$ticket['notes']?>">
				<img style="position:relative; right:5px; height:16px;padding-left:10px;padding-top:5px;" src="/images/add_notes.png"/>
			<?php endif ?>
		</td>
	</tr>
</table>

