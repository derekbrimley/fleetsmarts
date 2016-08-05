<script>
	$("#scrollable_content").height($("#body").height() - 195);
	
	$("#edit_cell_form").submit(function (e) {
        e.preventDefault(); //prevent default form submit
		save_edit_cell();
    });
</script>
<style>
	table#log_table td
	{
		vertical-align:top;
		line-height:15px;
	}
	
	.city
	{
		max-width:55px;
		display:inline-block;
	}
	
	
	.state
	{
		display:inline-block;
	}
	
	.city_state:hover
	{
		text-decoration:underline;
	}
	
	.editable_cell:hover
	{
		background:#EFEFEF;
		cursor:default;
	}
</style>
<div id="main_content_header">
	<div id="plain_header">
		<span style="font-weight:bold;">Fuel Report</span>
		<span style="float:right; margin-left:40px; font-size:16px;">Expense: $<?=number_format($total_expense,2)?> </span>
		<span style="float:right; margin-left:40px; font-size:16px;">Discount: $<?=number_format($total_rebate,2)?></span>
		<span style="float:right; margin-left:40px; font-size:16px;">Gallons: <?=number_format($total_gallons,2)?></span>
		<span style="float:right; margin-left:40px; font-size:16px;">Count: <?=number_format($total_count)?></span>
	</div>
	<div id="edit_cell_header" style="display:none;">
		<button class="jq_button" style="display:inline; width:80px;" onclick="save_edit_cell()">Save</button>
		<?php $attributes = array('name'=>'edit_cell_form','id'=>'edit_cell_form', 'style'=>'display:inline;' )?>
		<?=form_open('loads/edit_cell',$attributes);?>
			<input type="hidden" id="log_entry_id" name="log_entry_id" value="">
			<input type="hidden" id="field_name" name="field_name" value="">
			<input type="text" id="cell_value" name="cell_value" style="margin-left:10px;width:855px;" value="">
			<div style="cursor:pointer; display:inline; color:gray; margin-left:5px;" onclick="cancel_edit_cell()"> X </div>
		</form>
	</div>
	<div id="edit_cell_header_dropdown" style="display:none;">
		<!-- AJAX DROPDOWN FORM GOES HERE !-->
	</div>
</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:20px;"></td>
		<td style="width:70px;" VALIGN="top">Driver</td>
		<td style="width:70px;" VALIGN="top">Truck</td>
		<td style="width:100px;" VALIGN="top">Datetime</td>
		<td style="width:490px;" VALIGN="top">City, State</td>
		<td style="width:75px; text-align:right;" VALIGN="top">Gallons</td>
		<td style="width:75px; text-align:right;" VALIGN="top">Discount</td>
		<td style="width:75px; text-align:right;" VALIGN="top">Expense</td>
		<td></td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php if(!empty($fuel_lines)): ?>
		<?php foreach($fuel_lines as $fuel_line): ?>
			<?php
				//SET ALLOCATED IMAGE
				if(!empty($fuel_line["allocated_entry_id"]))
				{
					$allocation_title = "Allocated ".$fuel_line["locked_datetime"];
					$img = "/images/fuel_allocated.png";
				}
				else
				{
					$allocation_title = "Unallocated";
					$img = "/images/fuel_unallocated.png";
				}
			
			?>
			<div style="height:20px; overflow:hidden; padding-top:5px;padding-bottom:3px;">
				<table id="log_table" style="margin-left:5px; font-size:12px;">
					<tr style="height:15px;">
						<td style="width:20px;"><img title="<?=$allocation_title?>" style="height:15px; position:relative; bottom:0px" src="<?=$img?>"/></td>
						<td id=""  style="overflow:hidden; min-width:70px;  max-width:70px;" VALIGN="top" title="<?=$fuel_line["driver"]?>"><?=$fuel_line["driver"]?></td>
						<td id=""  style="overflow:hidden; min-width:70px;  max-width:70px;" VALIGN="top" title="<?=$fuel_line["truck"]?>"><?=$fuel_line["truck"]?></td>
						<td id=""  style="overflow:hidden; min-width:100px;  max-width:100px;" VALIGN="top" title=""><?=$fuel_line["datetime"]?></td>
						<td class="ellipsis"  style="overflow:hidden; min-width:490px;  max-width:490px;" VALIGN="top" title="<?=$fuel_line["city_state"]?>"  ><?=$fuel_line["city_state"]?></td>
						<td id=""  style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="top" title="<?=$fuel_line["gallons"]?>"  ><?=number_format($fuel_line["gallons"],2)?></td>
						<td id=""  style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="top" title="<?=$fuel_line["rebate_amount"]?>"  >$<?=number_format($fuel_line["rebate_amount"],2)?></td>
						<td id=""  style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="top" title="<?=$fuel_line["fuel_expense"]?>"  >$<?=number_format($fuel_line["fuel_expense"],2)?></td>
					</tr>
				</table>
			</div>
		<?php endforeach; ?>	
	<?php else: ?>
		<div style="margin:0 auto; margin-top:100px; width:230px;">There are no results for this search</div>
	<?php endif; ?>
</div>
