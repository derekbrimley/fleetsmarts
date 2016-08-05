<style>
	hr
	{
		width:156px;
		margin:0px;
		margin-top:7px;
		margin-bottom:7px;
	}
	
	.input_style
	{
		width:161px; 
		margin-left:2px;
	}
	
	table#updoad_doc_table tr
	{
		height:40px;
	}
	
	
</style>
<html>
	<!-- 	TODO: 								!-->
	<!-- 	SEARCH FUNCTION 					!-->
	<!-- 	ACTIVE/INACTIVE FILTER BOX			!-->
	<!-- 	FLEET MANAGER FILTER BOX			!-->
	<head>
		<title><?php echo $title;?></title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
		
		<?php include("documents/documents_script.php"); ?>

	</head>
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" class="scrollable_div" style="width:175px">
				<button class='left_bar_button jq_button' id="new_client" onclick="$( '#new_doc_dialog' ).dialog('open');;">New E Sign</button>
				<br>
				<br>
				<div id="scrollable_left_bar" class="scrollable_div" style="width:165px;overflow-x:hidden;">
					<?php $attributes = array('name'=>'filter_form','id'=>'filter_form','onkeypress'=>'return event.keyCode != 13;')?>
					<?=form_open('documents/load_esign_docs_report',$attributes);?>
						<br>
						<span style="font-weight:bold;">Search</span>
						<hr/>
						<input placeholder="Search by Document" type="text" id="document_search_input" name="document_search_input" class="left_bar_input" onkeypress="return load_documents_enter(event)" onChange="load_esign_docs_report()"/>
						<br>
						<br>
						<span class="heading">Filters</span>
						<hr/>
						<div style="margin-top:15px;" id="scrollable_filter_div"  class="">
							<br>
							<br>
							<span style="font-weight:bold;">Recipient Signer</span>
							<hr/>
							<?php echo form_dropdown('signer_input',$signer_options,'Select','onChange="load_esign_docs_report()" id="signer_input" class="left_bar_input"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Category</span>
							<hr/>
							<?php echo form_dropdown('category_input',$category_options,'Select','onChange="load_esign_docs_report()" id="category_input" class="left_bar_input"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Document</span>
							<hr/>
							<?php echo form_dropdown('document_input',$document_options,'Select','onChange="load_esign_docs_report()" id="document_input" class="left_bar_input"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Upload Date</span>
							<hr/>
								<input onChange="load_esign_docs_report()" placeholder="After" id="after_upload_date_filter" name="after_upload_date_filter" class="left_bar_input dp"/>
								<input onChange="load_esign_docs_report()" placeholder="Before" id="before_upload_date_filter" name="before_upload_date_filter" class="left_bar_input dp"/>
							<br>
							<br>
							<span style="font-weight:bold;">Signed Date</span>
							<hr/>
								<input onChange="load_esign_docs_report()" placeholder="After" id="after_signed_date_filter" name="after_signed_date_filter" class="left_bar_input dp"/>
								<input onChange="load_esign_docs_report()" placeholder="Before" id="before_signed_date_filter" name="before_signed_date_filter" class="left_bar_input dp"/>
						</div>
					</form>
				</div>
				<div id="report_left_bar">
					<!-- AJAX GOES HERE !-->
				</div>
				
			</div>
			
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">E Sign</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px; display:none;" onclick="load_esign_docs_report()" />
					</div>
				</div>
				<!-- REPORT GOES HERE !-->
			</div>
			
		</div>
	</body>
	
	<div id="new_doc_dialog" title="Upload New Document" style="display:none;">
		<div style="width:410px;  margin:auto; margin-top:25px;">
			<div id="sparks_cc_report_div" name="sparks_cc_report_div" style="">
				<?php $attributes = array('name'=>'add_new_doc_form','id'=>'add_new_doc_form', )?>
				<?php echo form_open_multipart('documents/add_new_doc',$attributes);?>
				<table id="updoad_doc_table">
					<tr>
						<td style="vertical-align: middle;color:red; padding-right:5px; font-weight:bold;">
							*
						</td>
						<td style="vertical-align:middle" >
							Recipient Signer
						</td>
						<td style="vertical-align:middle;">
							<?php echo form_dropdown('recipient_dropdown',$signer_options,"Select",'id="recipient_dropdown" style="" class="input_style" onchange=""');?>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: middle;color:red; padding-right:5px; font-weight:bold;">
							*
						</td>
						<td style="width:200px; vertical-align:middle" >
							Document Type
						</td>
						<td style="vertical-align:middle;">
							<?php
								$options = array(
										"Select" => "Select",
										"Registration Doc" => "Registration Doc",
										"Service Agreement" => "Service Agreement",
										"Statement" => "Statement",
										"Other Doc" => "Other Doc",
										);
								?>
							<?php echo form_dropdown('doc_type_dropdown',$options,"Select Report",'id="doc_type_dropdown" style="" class="input_style" onchange=""');?>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: middle;color:red; padding-right:5px; font-weight:bold;">
							*
						</td>
						<td style="vertical-align:middle" >
							Document Title
						</td>
						<td style="vertical-align:middle;">
							<input type="text" id="doc_title" name="doc_title" class="input_style" style=""/>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td style="vertical-align:middle" >
							Explanation Link
						</td>
						<td style="vertical-align:middle;">
							<input type="text" id="explanation_link" name="explanation_link" class="input_style" style=""/>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: middle;color:red; padding-right:5px; font-weight:bold;">
							*
						</td>
						<td style="vertical-align:middle" >
							Unsigned Document
						</td>
						<td style="vertical-align:middle; text-align:right;">
							<input type="file" id="unsigned_doc" name="unsigned_doc" class="" />
						</td>
					</tr>
					<tr>
						<td style="vertical-align: middle;color:red; padding-right:5px; font-weight:bold;">
							*
						</td>
						<td style="vertical-align:middle" >
							Signed Document
						</td>
						<td style="vertical-align:middle; text-align:right;">
							<input type="file" id="signed_doc" name="signed_doc" class="" />
						</td>
					</tr>
				</table>
			</form>
			</div>
		</div>
	</div>
	
</html>