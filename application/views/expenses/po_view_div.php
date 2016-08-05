<script>
	$("#scrollable_content").height($("#body").height() - 165);
	
	$('#po_date').datepicker({ showAnim: 'blind' });
	
	//DIALOG: UPLOAD SIGNATURE DIALOG
	$( "#file_upload_dialog" ).dialog(
	{
		autoOpen: false,
		height: 200,
		width: 400,
		modal: true,
		buttons: 
		[
			{
				text: "Upload",
				click: function() 
				{
					//SUBMIT FORM
					$("#upload_file_form").submit();
					$( this ).dialog( "close" );
					setTimeout(function()
					{
						save_po()
					},900);
				},//end add load
			},
			{
				text: "Cancel",
				click: function() 
				{
					//RESIZE DIALOG BOX
					$( this ).dialog( "close" );
				}
			}
		],//end of buttons
		open: function()
		{
		},//end open function
		close: function() 
		{
		}
	});//end dialog form
	
	function category_clicked(cat_id,owner_id,category)
	{
		//alert(cat_id);
		//HIDE ALL CIRCLES
		$(".circle_category").hide();
		
		//SHOW SELECTED CIRCLE
		$("#circle_"+cat_id).show();
		
		$("#po_category").val(category);
		$("#owner_id").val(owner_id);
		
		save_po();
		
	}
	
	function save_po()
	{
		var id = $("#po_id").val();
	
		//SHOW LOADING ICON
		$("#save_icon").hide();
		$("#filter_loading_icon").show();
		
		//alert('save po');
		//LOAD PO FILTER DIV
		var dataString = $("#po_form").serialize();
		
		var this_div = $('#main_content');
		
		$.ajax({
			url: "<?= base_url("index.php/expenses/save_po")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//SHOW LOADING ICON
					//$("#filter_loading_icon").hide();
					//$("#refresh_logs").show();
					
					//this_div.html(response);
					load_po_view(id);
					//load_po_report()
					//alert(response);
					
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	function delete_po()
	{
		if(confirm('Are you sure you want to delete this PO?'))
		{
			var id = $("#po_id").val();
		
			//SHOW LOADING ICON
			$("#save_icon").hide();
			$("#filter_loading_icon").show();
			
			//alert('save po');
			//LOAD PO FILTER DIV
			var dataString = $("#po_form").serialize();
			
			var this_div = $('#main_content');
			
			$.ajax({
				url: "<?= base_url("index.php/expenses/delete_po/")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						
						//SHOW LOADING ICON
						//$("#filter_loading_icon").hide();
						//$("#refresh_logs").show();
						
						//this_div.html(response);
						load_po_report();
						//alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function approve_po()
	{
		if(confirm('Are you sure you want to sign?'))
		{
			var id = $("#po_id").val();
		
			//SHOW LOADING ICON
			$("#save_icon").hide();
			$("#filter_loading_icon").show();
			
			//alert('save po');
			//LOAD PO FILTER DIV
			var dataString = $("#po_form").serialize();
			
			var this_div = $('#main_content');
			
			$.ajax({
				url: "<?= base_url("index.php/expenses/approve_po/")?>/"+id, // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						
						//SHOW LOADING ICON
						//$("#filter_loading_icon").hide();
						//$("#refresh_logs").show();
						
						//this_div.html(response);
						load_po_view(id)
						//load_po_report()
						//alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function deny_po()
	{
		if(confirm('Are you sure you want to deny the PO Request?'))
		{
			var id = $("#po_id").val();
		
			//SHOW LOADING ICON
			$("#save_icon").hide();
			$("#filter_loading_icon").show();
			
			//alert('save po');
			//LOAD PO FILTER DIV
			var dataString = $("#po_form").serialize();
			
			var this_div = $('#main_content');
			
			$.ajax({
				url: "<?= base_url("index.php/expenses/deny_po/")?>/"+id, // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						
						//SHOW LOADING ICON
						//$("#filter_loading_icon").hide();
						//$("#refresh_logs").show();
						
						//this_div.html(response);
						load_po_view(id)
						//load_po_report()
						//alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	
	function email_po()
	{
		if(confirm('Are you sure you want to make this PO Request?'))
		{
			var id = $("#po_id").val();
		
			//SHOW LOADING ICON
			$("#save_icon").hide();
			$("#filter_loading_icon").show();
			
			//alert('save po');
			//LOAD PO FILTER DIV
			var dataString = $("#po_form").serialize();
			
			var this_div = $('#main_content');
			
			$.ajax({
				url: "<?= base_url("index.php/expenses/email_po/")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						
						//SHOW LOADING ICON
						//$("#filter_loading_icon").hide();
						//$("#refresh_logs").show();
						
						//this_div.html(response);
						load_po_view(id);
						//alert(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function open_file_upload(po_id)
	{
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#file_upload_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#file_upload_dialog').dialog('open')
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'po_id='+po_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/load_po_file_upload")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: target_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					target_div.html(response);
					
					
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error!")
				}
			}
		});//END AJAX
	}
	
	
</script>
<style>
	.po_input
	{
		width:200px;
		height:30px;
		text-align:center;
		font-size:16px;
		border: 0px!important;
	}
	
	.underline
	{
		border-bottom: 2px inset black;
		padding:3px;
	}
	
	.category:hover
	{
		background:#EFEFEF!important;
	}
	
	.circle_category
	{
		position:absolute;
		margin-left:-20px;
		margin-top:-5px;
		height:35px;
		width:165px;
	}
</style>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px; text-align:center;">
		<div title="Save" id="save_icon" onclick="save_po()" style="margin-right:25px; width:25px; float:right;">
			<img src="/images/save.png" style="float:right; cursor:pointer; height:20px; position:relative; top: 5px;" />
		</div>
		<div id="filter_loading_icon" name="filter_loading_icon" style="margin-right:25px; width:25px; float:right; display:none;">
			<img src="/images/loading.gif" style="float:right; height:20px; padding-top:5px;" />
		</div>
		<div title="Attach" id="attach_icon" onclick="open_file_upload('<?=$po["id"]?>')" style="margin-right:25px; width:25px; float:right;">
			<img src="/images/paper_clip2.png" style="float:right; cursor:pointer; height:20px; width:11px; position:relative; top: 5px;" />
		</div>
		<?php if(empty($po["email_datetime"])): ?>
			<?php if($po_is_complete): ?>
				<div title="Send PO Request <?=$po["email_datetime"]?>" id="email_icon" name="email_icon" style="margin-right:15px; width:25px; float:right;" onclick="email_po()">
					<img src="/images/email.png" style="float:right; cursor:pointer; height:20px; padding-top:5px;" />
				</div>
			<?php else:?>
				<div title="Incomplete" id="email_sent_icon" name="email_sent_icon" style="margin-right:15px; width:25px; float:right;" onclick="alert('The PO is incomplete!')">
					<img src="/images/email_sent.png" style="float:right; cursor:pointer; height:20px; padding-top:5px;" />
				</div>
			<?php endif;?>
		<?php else:?>
				<div title="Sent <?=date('n/d H:i:s',strtotime($po["email_datetime"])) ?>" id="email_sent_icon" name="email_sent_icon" style="margin-right:15px; width:25px; float:right;" onclick="alert('A PO request has already been sent.')">
					<img src="/images/email_sent.png" style="float:right; cursor:pointer; height:20px; padding-top:5px;" />
				</div>
		<?php endif;?>
		<?php if($this_person_id == $po["issuer_id"] || $this_person_id == $po["approved_by_id"]): ?>
			<div title="Delete" id="delete_icon" onclick="delete_po()" style="margin-right:25px; float:right;">
				<img src="/images/trash.png" style="float:right; cursor:pointer; height:20px; position:relative; top: 5px;" />
			</div>
		<?php endif;?>
		<div title="Back" id="back_icon" name="back_icon" onclick="load_po_report()" style="margin-right:25px; width:25px; float:right;">
			<img style="float:right; cursor:pointer; height:20px; position:relative; top: 5px;" src="/images/back.png" />
		</div>
		<div style="float:left; margin-left:20px; font-weight:bold; cursor:pointer;" onclick="load_po_view('<?=$po["id"]?>')">
			Purchase Order <?=$po["id"]?>
		</div>
		<span style="width:200px; margin:auto; color:red;">
			<?=$po_status_text?>
		</div>
	</div>
</div>

<div id="scrollable_content" class="scrollable_div">
	<div style="padding:15px;">
		<?php $attributes = array('name'=>'po_form','id'=>'po_form')?>
		<?=form_open('expenses/save_po',$attributes);?>
			<input type="hidden" id="po_id" name="po_id" value="<?=$po["id"]?>" />
			<div style="color:red; text-align:center;">
				This PO is not intended to replace the normal means of communication needed between the issuer, owner, and money handler. Make sure to communicate through the normal channels with the proper people in order to get the purchase approved and the money moved.
			</div>
			
			<div style="height:30px; line-height:30px; text-align:center; font-size:20px;">
				<div style="width:50px; height:30px; line-height:30px; font-size: 14px; float:left;">
					Date
				</div>
				<div class="underline" style="height:30px; float:left;">
					<input id="po_date" name="po_date" type="text" class="po_input" style="" value="<?=date("n/d/y", strtotime($po["expense_datetime"]))?>" onchange="save_po()" />
				</div>
				PURCHASE ORDER FORM
				<div class="underline" style="width:200px; font-size:20px; height:30px; float:right;">
					<input id="expense_amount" name="expense_amount" type="text" class="po_input" value="<?=round($po["expense_amount"],2)?>" onchange="save_po()"/>
				</div>
				<div class="" style="font-size:20px; height:30px; float:right;">
				$
				</div>
			</div>
			<div style="height:30px; line-height:30px; clear:both; text-align:center; font-size:20px;">
				<div class="underline" style="width:200px; font-size:20px; height:30px; float:right;">
					<?php echo form_dropdown('account_dropdown',$source_accounts_options,$po["account_id"],'id="account_dropdown" onChange="save_po()" class="po_input"');?>
				</div>
			</div>
			<div style="height:25px; line-height:25px; margin-top:25px; clear:both; text-align:center; font-weight:bold; background:#F2F2F2">
				Category (circle one)
			</div>
			<div style="margin-top:5px; clear:both;">
				<input type="hidden" id="owner_id" name="owner_id" value="<?=$po["owner_id"]?>" />
				<input type="hidden" id="po_category" name="po_category"value="<?=$po["category"]?>" />
				<?php
					$bi = 0;
				?>
				<?php foreach($company_categories as $comp_cat):?>
					<?php if($comp_cat["show_on_po"]):?>
						<?php
							$bi++;
							$ci = 0;
						?>
						<div style="margin-left:20px; margin-right:20px; float:left;">
							<div style="font-weight:bold; height:25px; line-height:25px;">
								<?=$comp_cat["name"]?>
							</div>
							<?php foreach($comp_cat["categories"] as $category): ?>
								<?php
									$ci++;
									
									$cat_id = $bi.$ci;
								?>
								<div class="category" style="font-size:12px; height:25px; line-height:25px; cursor:pointer; position:relative;" onclick="category_clicked('<?=$cat_id?>','<?=$comp_cat["id"]?>','<?=$category?>')">
									<?php 
										$display_circle = "display:none;";
										if($category == $po["category"])
										{
											$display_circle = "";
										}
									?>
									<img id="circle_<?=$cat_id?>" class="circle_category" src="/images/oval.png" style="<?=$display_circle?>" />
									<?=$category?>
								</div>
							<?php endforeach?>
						</div>
					<?php endif;?>
				<?php endforeach;?>
				<div style="clear:both;"></div>
			</div>
			<div style="height:25px; line-height:25px; margin-top:25px; clear:both; text-align:center; font-weight:bold; background:#F2F2F2">
				Description and Notes
			</div>
			<div style="margin-top:5px; clear:both;">
				<div style="float:left; width:475px; height: 70px;">
					<textarea id="po_notes" name="po_notes" style="width: 475px; height:70px; font-size:14px; border: 0px" onchange="save_po()" ><?=$po["po_notes"]?></textarea>
				</div>
				<div style="float:left; width:475px; margin-left:10px;">
					<?=str_replace("\n","<br>",$po["po_log"]);?>
				</div>
			</div>
			<div style="height:25px; line-height:25px; margin-top:25px; clear:both; text-align:center; font-weight:bold; background:#F2F2F2">
				Issuer and Approval
			</div>
			<div style="height:30px; line-height:30px; margin-top:25px; clear:both;">
				<div style="width:150px; height:30px; line-height:30px; font-size: 14px; float:left;">
					Issuer Name
				</div>
				<div class="underline" style="width:300px; height:30px; float:left; font-size:16px;">
					<span style="margin-left:5px;"><?=$po["issuer"]["full_name"]?></span>
				</div>
				<div class="underline" style="width:300px; height:30px; float:right; text-align:center; font-size:16px;">
					<span style="font-family: 'Homemade Apple', cursive;"><?=$po["issuer"]["full_name"]?></span>
					<div style="font-family: 'Homemade Apple', cursive; float:right;">
						<?=date("n/d H:i", strtotime($po["issued_datetime"]))?>
					</div>
				</div>
				<div style="width:150px; height:30px; line-height:30px; font-size: 14px; float:right;">
					Issuer Signature
				</div>
			</div>
			<div style="height:30px; line-height:30px; margin-top:25px; clear:both;">
				<div style="width:150px; height:30px; line-height:30px; font-size: 14px; float:left;">
					Approved By Name
				</div>
				<div class="underline" style="width:300px; height:30px; float:left; font-size:16px;">
					<?php echo form_dropdown('approved_by_dropdown',$approved_by_options,$po["approved_by_id"],'id="approved_by_dropdown" onChange="save_po()" class="po_input" style="width:300px;"');?>
				</div>
		</form>
				<div class="underline" style="width:300px; height:30px; float:right; text-align:center; font-size:16px;">
				<?php if(!empty($po["approved_datetime"])):?>
					<span style="font-family: 'Homemade Apple', cursive;"><?=$po["approved_by"]["full_name"]?></span>
					<div style="font-family: 'Homemade Apple', cursive; float:right;">
						<?=date("n/d H:i", strtotime($po["approved_datetime"]))?>
					</div>
				<?php else:?>
					<?php if($po_is_complete): ?>
						<?php if($po["approved_by_id"] == $this_person_id):?>
							<input type="button" class='jq_button' style="width:100px; height:30px; margin:auto; display:inline; margin-right:15px;" id="approve_po_button" onclick="approve_po();" value="Sign">
							<input type="button" class='jq_button' style="width:100px; height:30px; margin:auto; display:inline;" id="deny_po_button" onclick="deny_po();" value="Deny">
					<?php endif;?>
					<?php else:?>
						<input type="button" class='jq_button' style="width:100px; height:30px; margin:auto; display:inline; margin-right:15px;" id="approve_po_button" onclick="alert('The PO is incomplete!');" value="Sign">
						<input type="button" class='jq_button' style="width:100px; height:30px; margin:auto; display:inline;" id="deny_po_button" onclick="alert('The PO is incomplete!');" value="Deny">
					<?php endif;?>
				<?php endif;?>
				</div>
			<div style="width:150px; height:30px; line-height:30px; font-size: 14px; float:right;">
				Approved By Signature
			</div>
		</div>
		<div style="height:30px; line-height:30px; margin-top:25px; clear:both;">
			<div style="height:25px; line-height:25px; margin-top:25px; clear:both; text-align:center; font-weight:bold; background:#F2F2F2">
				Attachments
			</div>
			<?php if(!empty($attachments)):?>
				<?php foreach($attachments as $attachment):?>
					<a href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>" onclick=""><?=$attachment["attachment_name"]?></a>&nbsp;
				<?php endforeach;?>
			<?php endif;?>
		</div>
	</div>
</div>

<div title="PO Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
	<!-- AJAX GOES HERE !-->
</div>