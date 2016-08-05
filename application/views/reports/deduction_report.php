<script>
	$("#scrollable_content").height($("#body").height() - 195);
	
	//ADD TO ENTRY DESCTIPTION DIALOG
	$( "#add_to_desc_dialog").dialog(
	{
			autoOpen: false,
			height: 210,
			width: 400,
			modal: true,
			buttons: 
				[
					{
						text: "Save",
						click: function() 
						{
							
							if(!$("#new_desc").val())
							{
								alert("You must enter a description!");
							}
							else
							{
								//-------------- AJAX TO LOAD PEOPLE LIST ---------
								// GET THE DIV IN DIALOG BOX
								var entry_desc = $("#entry_desc_"+ajax_entry_id);
								
								//POST DATA TO PASS BACK TO CONTROLLER
								var dataString = "&new_desc="+$("#new_desc").val()+"&entry_id="+ajax_entry_id;
								//var dataString = "&entry_id=880&new_desc=hello";
								//alert(dataString);
								// AJAX!
								$.ajax({
									url: "<?= base_url("index.php/accounts/add_to_entry")?>", // in the quotation marks
									type: "POST",
									data: dataString,
									cache: false,
									context: entry_desc, // use a jquery object to select the result div in the view
									statusCode: {
										200: function(response){
											// Success!
											//alert(response);
											
											entry_desc.html(response);
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
								
								$( this ).dialog( "close" );
							}
							
						},//end save
					},
					{
						text: "Cancel",
						click: function() 
						{
							//clear_load_info();
							
							$( this ).dialog( "close" );
						}//end cancel
					}
				],//end of buttons
			
			open: function()
				{
				},//end open function
			close: function() 
				{
					$("#new_desc").val("");
					//clear_load_info();
				}
	});//end add to desc
	
	//ADD TO ENTRY DESCRIPTION
	function add_to_desc(entry_id)
	{
		ajax_entry_id  = entry_id;
		//-------------- AJAX TO LOAD PEOPLE LIST ---------
		// GET THE DIV IN DIALOG BOX
		var existing_desc = $("#existing_desc");
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&entry_id="+entry_id;
		//alert(dataString);
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/accounts/get_entry_desc")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: existing_desc, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					
					existing_desc.html(response);
					$( "#add_to_desc_dialog").dialog('open');
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
	.entry_rows td
	{
		padding-top:10px;
		padding-bottom:10px;
	}
</style>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:left; font-weight:bold;">Deduction Report</div>
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_deduction_report()" />
		</div>
		<div id="total_deductions" class="header_stats"  style="width:150px; margin-right:20px; float:right; font-weight:bold; text-align:right;">Total </div>
		<div id="count" class="header_stats"  style="width:150px; margin-right:20px; float:right; font-weight:bold; text-align:right;">Count </div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:35px;" VALIGN="top">FM</td>
		<td style="width:55px;" VALIGN="top">Date</td>
		<td style="width:60px;" VALIGN="top">Time</td>
		<td style="width:55px;" VALIGN="top">Link</td>
		<td style="width:640px;" VALIGN="top">Description</td>
		<td style="width:65px;text-align:right;" VALIGN="top">Debit</td>
		<td style="width:65px;text-align:right;" VALIGN="top">Credit</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<table  style="table-layout:fixed; margin:5px; font-size:12px;">
		<?php 
			$i = 0;
			$total = 0;
		?>
		<?php foreach ($account_entries as $entry): ?>
			<?php
				$i++;
				
				$background_color = "";
				if($i%2 == 1)
				{
					$background_color = "background-color:#F2F2F2;";
				}
				
				//GET ACCOUNT
				$where = null;
				$where["id"] = $entry["account_id"];
				$account = db_select_account($where);
				
				//GET FM COMPANY
				$where["id"] = $account["company_id"];
				$fm_company = db_select_company($where);
				
				//GET INITIALS
				$fm_initials = substr($fm_company["person"]["f_name"],0,1).substr($fm_company["person"]["l_name"],0,1);
				
				//SET DEBIT AND CREDIT TEXT
				$debit_text = "";
				$credit_text = "";
				if($entry["debit_credit"] == "Debit")
				{
					if($entry["entry_amount"] > 0)
					{
						$debit_text = number_format($entry["entry_amount"],2);
					}
					else
					{
						$credit_text = number_format(-$entry["entry_amount"],2);
					}
				}
				else if($entry["debit_credit"] == "Credit")
				{
					if($entry["entry_amount"] > 0)
					{
						$credit_text = number_format($entry["entry_amount"],2);
					}
					else
					{
						$debit_text = number_format(-$entry["entry_amount"],2);
					}
				}
				
				$entry_description = $entry["entry_description"];
				
				//MAKE LINK TEXT
				$link = "";
				if(!empty($entry["entry_link"]))
				{
					$link = '<a oncontextmenu="edit_entry_link(\''.$entry["id"].'\');return false;" href="'.$entry["entry_link"].'" target="_blank">Link</a>';
				}
				
				//TOTAL DEDUCTIONS
				if($entry["debit_credit"] == "Credit")
				{
					$total = $total+$entry["entry_amount"];
				}
				else if($entry["debit_credit"] == "Debit")
				{
					$total = $total-$entry["entry_amount"];
				}
				
			?>
			<tr class="entry_rows" style="<?=$background_color?> min-height:30px;">
				<td style="width:35px;"><?=$fm_initials?></td>
				<td style="width:55px;"><?=date("m/d/y",strtotime($entry["entry_datetime"])) ?></td>
				<td style="width:60px;"><?=date("H:i",strtotime($entry["entry_datetime"])) ?></td>
				<td style="width:55px;"><?=$link ?></td>
				<td style="width:640px;"><span  id="entry_desc_<?=$entry["id"]?>" name="entry_desc_<?=$entry["id"]?>"><?=$entry_description?></span> <a onclick="add_to_desc('<?=$entry["id"]?>');return false;" href="#">edit</a></td>
				<td style="width:65px; text-align:right;"><?=$debit_text ?></td>
				<td style="width:65px; text-align:right;"><?=$credit_text ?></td>
			</tr>
		<?php endforeach;?>
	</table>
</div>
<div id="add_to_desc_dialog" title="Add Description" style="display:none;">
		<div id="existing_desc" name="existing_desc" style="height:65px;">
			Existing desctiption goes here...
		</div>
		Add to Description:<br>
		<textarea id="new_desc" name="new_desc" style="width:370px"></textarea>
	</div>
<script>
	$("#total_deductions").html("Total <?=number_format($total,2)?>");
	$("#count").html("Count <?=$i?>");
</script>