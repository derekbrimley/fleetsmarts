<?php
	$show_row = true;
	if($expense["company_id"] == 42)//ABC FACTORING
	{
		$show_row = false;
		if(user_has_permission('View ABC Factoring Transactions'))
		{
			$show_row = true;
		}
	}
?>

<?php if($show_row):?>
	<?php
		$row = $expense["id"];
		
		//echo $i;
		if(isset($i))
		{
			$i++;
			
			$expense_amount = $expense["expense_amount"];
			if($expense["debit_credit"] == "Debit")
			{
				$expense_amount = $expense_amount * -1;
			}
			
			$expense_total = $expense_total + $expense_amount;
		}
		
		//SET CATEGORY OPTIONS TO BLANK
		$category_options = array(
						'' => '',
				);
		
		//echo $expense["id"]." ".$expense["expense_type"];
		
		//DETERMINE EXPENSE AMOUNT
		if($expense["expense_type"] == 'Expense')
		{
			if($expense["debit_credit"] == 'Debit')
			{
				$expense_amount = "(".number_format($expense["expense_amount"],2).")";
			}
			else
			{
				$expense_amount = number_format($expense["expense_amount"],2);
			}
		}
		else if($expense["expense_type"] == 'Transfer')
		{
			if($expense["debit_credit"] == 'Credit')
			{
				$expense_amount = "(".number_format($expense["expense_amount"],2).")";
			}
			else
			{
				$expense_amount = number_format($expense["expense_amount"],2);
			}
		}
		else if($expense["expense_type"] == 'Revenue')
		{
			if($expense["debit_credit"] == 'Credit')
			{
				$expense_amount = "(".number_format($expense["expense_amount"],2).")";
			}
			else
			{
				$expense_amount = number_format($expense["expense_amount"],2);
			}
		}
		
		
		//DETERMINE LOCKED OR UNLOCKED
		if(empty($expense["locked_datetime"]))
		{
			$is_locked = 'no';
			$locked_icon_src = "/images/unlocked4.png";
			$locked_icon_style = "right:2px;";
			$locked_icon_title = "Lock";
			$locked_icon_onclick = "open_lock_expense_dialog('$row')";
			$cb_disabled = "disabled";
		}
		else
		{
			$is_locked = 'yes';
			$locked_icon_src = "/images/locked.png";
			$locked_icon_style = "right:0px;";
			$locked_icon_title = "Locked ".date('m/d/y',strtotime($expense["locked_datetime"]));
			$cb_disabled = "";
			
			//CAN ONLY UNLOCK IF EXPENSE HAS NOT YET BEEN RECORDED
			if(empty($expense["recorded_datetime"]))
			{
				//DETERMINE IF USER HAS ABILITY TO UNLOCK EXPENSE
				if(user_has_permission("lock non-owned expenses"))
				{
					$locked_icon_onclick = "unlock_expense('$row')";
				}
				else
				{
					$locked_icon_onclick = "alert('The Gods have not given you permission to unlock expenses!')";
				}
			}
			else
			{
				$locked_icon_onclick = "alert('This expense has already been recorded! You cant unlock it anymore.')";
			}
		}
		
		$expense_date_text = "";
		if(!empty($expense["expense_datetime"]))
		{
			$expense_date_text = date("n/d/y",strtotime($expense["expense_datetime"]));
		}
		
		$owner_text = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if(!empty($expense["company_id"]))
		{
			//GET COMPANY
			$where = null;
			$where["id"] = $expense["company_id"];
			$company = db_select_company($where);
			
			$owner_text = $company["company_side_bar_name"];
			
			//GET COPMANY CATEGORIES
			$expense_categories = get_expense_categories($company["id"]);
			
			$category_options[""] = "";
			
			$category_options["Cash to Cash"] = "Cash to Cash";
			$category_options["Bill Paid"] = "Bill Paid";
			$category_options["Payment Received"] = "Payment Received";
			
			if(!empty($expense_categories))
			{
				foreach($expense_categories as $cat)
				{
					$category_options[$cat] = $cat;
				}
			}
		}
		
		
		$issuer_text = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if(!empty($expense["issuer_id"]))
		{
			//GET COMPANY
			$where = null;
			$where["id"] = $expense["issuer_id"];
			$company = db_select_company($where);
			
			$issuer_text = $company["company_side_bar_name"];
		}
		
		$category_text = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if(!empty($expense["category"]))
		{
			$category_text = $expense["category"];
		}
		
		//STYLE CHECKBOX
		$check_box_style = "";
		if(!empty($expense["recorded_datetime"]))
		{
			$check_box_style = "checked";
			$cb_disabled = "disabled";
		}
		
		
		

		//DETERMINE NOTES IMAGE
		if(empty($expense["expense_notes"]))
		{
			$notes_img = "/images/add_notes_empty.png";
		}
		else
		{
			$notes_img = "/images/add_notes.png";
		}
		

		
	?>
	<?php $attributes = array("name"=>"expense_form_$row",'id'=>"expense_form_$row")?>
	<?=form_open('expenses/save_expense_edit',$attributes);?>
		<table  style="table-layout:fixed; font-size:12px;">
			<tr class="" style="line-height:30px;">
				<input type="hidden" id="row" name="row" value="<?=$row?>">
				<input type="hidden" id="recorded_<?=$row?>" name="recorded_<?=$row?>">
				<input type="hidden" id="locked_<?=$row?>" name="locked_<?=$row?>" value="<?=$is_locked?>">
				<input type="hidden" id="lock_notes_<?=$row?>" name="lock_notes_<?=$row?>">
				
				
				<td style="min-width:30px; max-width:30px; padding-left:5px;" VALIGN="top">
					<input <?=$check_box_style?> <?=$cb_disabled?> style="position:relative; top:5px;"  type="checkbox" name="recorded_cb_<?=$row?>" id="recorded_cb_<?=$row?>" onclick="cb_changed('<?=$row?>')">
				</td>
				<td style="min-width:20px; max-width:20px;" >
					<img id="lock_icon_<?=$row?>" name="lock_icon_<?=$row?>" style="height:12px; cursor:pointer; position:relative; top:8px; <?=$locked_icon_style?>" onclick="<?=$locked_icon_onclick?>" src="<?=$locked_icon_src?>" title="<?=$locked_icon_title?>" />
				</td>
				<td style="min-width:70px; max-width:70px;" VALIGN="top"><?=$expense_date_text?></td>
				<td style="min-width:115px; max-width:115px;" VALIGN="top">
					<div class="editable_cell" style="padding-left:5px;" id="issuer_text_<?=$row?>" style="cursor:default; height:100%;" oncontextmenu="$('#issuer_text_<?=$row?>').hide(); $('#issuer_dd_<?=$row?>').show(); return false;" ><?=$issuer_text?></div>
					<div class="editable_cell" id="issuer_dd_<?=$row?>" style="cursor:default; height:30px; display:none;" oncontextmenu="$('#issuer_text_<?=$row?>').show(); $('#issuer_dd_<?=$row?>').hide(); return false;"><?php echo form_dropdown("issuer_dropdown_$row",$issuer_sidebar_options,@$expense["issuer_id"],'id="issuer_dropdown_'.$row.'" style="font-size:12px; height:22px; width:110px; position:relative; top:3px;" onchange="save_row(\''.$row.'\')" ');?></div>
				</td>
				<td style="min-width:115px; max-width:115px;" VALIGN="top">
					<div style="padding-left:5px;" id="owner_text_<?=$row?>" style="cursor:default; height:100%;" 
					<?php if(user_has_permission("allow user to change owner on transaction")): ?>
						class="editable_cell" oncontextmenu="$('#owner_text_<?=$row?>').hide(); $('#owner_dd_<?=$row?>').show(); return false;" 
					<?php endif ?>
					>
						<?=$owner_text?>
					</div>
					<div class="editable_cell" id="owner_dd_<?=$row?>" style="cursor:default; height:30px; display:none;" oncontextmenu="$('#owner_text_<?=$row?>').show(); $('#owner_dd_<?=$row?>').hide(); return false;">
						<?php echo form_dropdown("owner_dropdown_$row",$bill_owner_sidebar_options,@$expense["company_id"],'id="owner_dropdown_'.$row.'" style="font-size:12px; height:22px; width:110px; position:relative; top:3px;" onchange="save_row(\''.$row.'\')" ');?>
					</div>
				</td>
				<td style="min-width:130px; max-width:130px;" VALIGN="top">
					<div class="editable_cell ellipsis" style="padding-left:5px;" id="category_text_<?=$row?>" style="cursor:default; height:100%;" oncontextmenu="$('#category_text_<?=$row?>').hide(); $('#category_dd_<?=$row?>').show(); return false;" ><?=$category_text?></div>
					<div class="editable_cell" id="category_dd_<?=$row?>" style="cursor:default; height:30px; display:none;" oncontextmenu="$('#category_text_<?=$row?>').show(); $('#category_dd_<?=$row?>').hide(); return false;"><?php echo form_dropdown("category_dropdown_$row",$category_options,@$expense["category"],'id="category_dropdown_'.$row.'" style="font-size:12px; height:22px; width:110px; position:relative; top:3px;" onchange="save_row(\''.$row.'\')" ');?></div>
				</td>
				<td style="min-width:360px; max-width:360px;" class="ellipsis" VALIGN="top" title="<?=$expense["description"]?>"><?=$expense["description"]?></td>
				<td style="min-width:60px;  max-width:60px;text-align:right; padding-right:5px;" VALIGN="top"><?=$expense_amount?></td>
				<?php if(empty($expense["locked_datetime"])): ?>
					<td style="overflow:hidden; min-width:30px;  max-width:30px;  cursor:default; padding-left:10px;" VALIGN="top" ><img id="split_expense_<?=$expense["id"]?>" name="split_expense_<?=$expense["id"]?>" title="Split Expense" onclick="split_expense('<?=$expense["id"]?>','<?=$expense["expense_amount"]?>')" style="cursor:pointer; position:relative; left:2px; top:6px; height:16px; width:16px" src="/images/split_expense.png" /></td>
				<?php else: ?>
					<td style="overflow:hidden; min-width:30px;  max-width:30px;  cursor:default; padding-left:10px;" VALIGN="top" ><img id="split_expense_<?=$expense["id"]?>" name="split_expense_<?=$expense["id"]?>" title="Locked" onclick="alert('This expense is locked!')" style="cursor:pointer; position:relative; left:2px; top:6px; height:16px; width:16px" src="/images/split_expense_disabled.png" /></td>
				<?php endif; ?>
				<td style="overflow:hidden; min-width:30px;  max-width:30px;  cursor:default;" VALIGN="top" >
					<img id="expense_notes_<?=$expense["id"]?>" name="expense_notes_<?=$expense["id"]?>" title="<?=$expense['expense_notes']?>" onclick="open_notes('<?=$expense["id"]?>')" style="cursor:pointer; position:relative; left:2px; top:6px; height:16px; width:16px" src="<?=$notes_img?>" />
				</td>
			</tr>
		</table>
	</form>
<?php endif;?>