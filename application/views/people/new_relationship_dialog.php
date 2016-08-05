<div style="margin:0 auto; margin-top:20px;">
	<?php $attributes = array('id' => 'add_customer_vendor', 'name'=>'add_customer_vendor'); ?>
	<?=form_open('people/add_customer_vendor',$attributes)?>
		<input type="hidden" id="business_id" name="business_id" value="<?=$company_id?>"/>
		<table id="" style="font-size: 14px;">
			<tr>
				<td style="width:180px;">
					Relationship
				</td>
				<td>
					<?php $options = array(
											'Select'  	=> 'Select' ,
											'Customer'  	=> 'Customer',
											'Vendor'  	=> 'Vendor',
											'Staff'  	=> 'Staff',
											'Member'  	=> 'Member',
											'Fleet Manager' => 'Fleet Manager'
											); ?>
					<?php echo form_dropdown('relationship',$options,"Select",'id="relationship" class="main_content_dropdown" style="" onchange="relationship_type_selected()"');?>
				</td>
			</tr>
			<tr id="customer_vendor_row" style="display:none;">
				<td>
					Customer/Vendor
				</td>
				<td>
					<?php echo form_dropdown('customer_vendor',$customer_vendor_options,"Select",'id="customer_vendor" style="" class="main_content_dropdown"');?>
				</td>
			</tr>
			<tr id="staff_row" style="display:none;">
				<td>
					Staff
				</td>
				<td>
					<?php echo form_dropdown('staff',$staff_options,"Select",'id="staff" style="" class="main_content_dropdown"');?>
				</td>
			</tr>
			<tr id="member_row" style="display:none;">
				<td>
					Member
				</td>
				<td>
					<?php echo form_dropdown('member',$member_options,"Select",'id="member" style="" class="main_content_dropdown"');?>
				</td>
			</tr>
			<tr id="fleet_manager_row" style="display:none;">
				<td>
					Fleet Manager
				</td>
				<td>
					<?php echo form_dropdown('fleet_manager',$fleet_manager_options,"Select",'id="fleet_manager" style="" class="main_content_dropdown"');?>
				</td>
			</tr>
		</table>
	</form>
</div>