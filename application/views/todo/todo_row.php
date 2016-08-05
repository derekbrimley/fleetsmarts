<?php
	//MAKE DUE DATE TEXT
	$due_date_text = "";
	if(!empty($action_item["due_date"]))
	{
		$due_date_text = date("m/d/y", strtotime($action_item["due_date"]));
	}
	
	//MAKE COMPLETION DATE TEXT
	$complete_date_text = "";
	if(!empty($action_item["completion_date"]))
	{
		$complete_date_text = date("m/d/y", strtotime($action_item["completion_date"]));
	}

	//GET OWNER USER
	$where = null;
	$where["id"] = $action_item["owner_id"];
	$owner_user = db_select_user($where);
	
	//GET OWNER PERSON
	$where = null;
	$where["id"] = $owner_user["person_id"];
	$owner_person = db_select_person($where);
	
	//GET MANAGER USER
	$where = null;
	$where["id"] = $action_item["manager_id"];
	$manager_user = db_select_user($where);
	
	//GET MANAGER PERSON
	$where = null;
	$where["id"] = $manager_user["person_id"];
	$manager_person = db_select_person($where);
	
	$row = $action_item["id"];
?>
<table  style="table-layout:fixed; font-size:12px;">
	<tr style="line-height:30px;">
		<?php if(empty($action_item["completion_date"])): ?>
			<td style="width:30px;" VALIGN="top"><img src="/images/empty_red_box.png" style="height:15px; position:relative; top:8px; left:5px;" onclick="alert('ToDo Incomplete!')"></td>
		<?php else:?>
			<td style="width:30px;" VALIGN="top"><img src="/images/green_checkmark.png" style="height:15px; position:relative; top:8px; left:4px;" onclick="alert('Comlete!')"></td>
		<?php endif; ?>
		<td onclick="" style="width:70px;" VALIGN="top" title="<?=$owner_person["full_name"]?>"><?=$owner_person["f_name"]?></td>
		<td onclick="" style="width:70px; padding-left:5px;" VALIGN="top" title="<?=$manager_person["full_name"]?>"><?=$manager_person["f_name"]?></td>
		<td onclick="" style="width:50px; padding-left:5px;" VALIGN="top"><?=$action_item["object_type"]?></td>
		<td onclick="" style="min-width:570px; max-width:570px; padding-left:5px;" VALIGN="top" class="ellipsis" title="<?=$action_item["description"]?>"><?=$action_item["description"]?></td>
		<td onclick="" style="width:75px; padding-left:5px;" VALIGN="top"><?=$due_date_text?></td>
		<td onclick="" style="width:75px;" VALIGN="top"><?=$complete_date_text?></td>
		<td onclick="" style="width:30px;" VALIGN="top">
			<?php if(empty($action_item["notes"])): ?>
				<img id="action_item_notes_<?=$row?>" name="action_item_notes_<?=$row?>" title="<?=$action_item['notes']?>" onclick="open_notes('<?=$row?>')" style="cursor:pointer; position:relative; left:2px; top:6px; height:16px; width:16px" src="/images/add_notes_empty.png" />
			<?php else: ?>
				<img id="action_item_notes_<?=$row?>" name="action_item_notes_<?=$row?>" title="<?=$action_item['notes']?>" onclick="open_notes('<?=$row?>')" style="cursor:pointer; position:relative; left:2px; top:6px; height:16px; width:16px" src="/images/add_notes.png" />
			<?php endif; ?>
		</td>
	</tr>
</table>
