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
		
		<script>
			$(document).ready(function()
			{
				$("#main_content").height($(window).height() - 115);
			});
			
			function search_file()
			{
				//SHOW LOADING ICON
				$("#refresh_list").hide();
				$("#filter_loading_icon").show();
				
				// GET THE DIV IN DIALOG BOX
				var this_div = $('#main_content');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				var dataString = $("#filter_form").serialize();
				// var dataString;
				console.log(dataString);
				
				
				// AJAX!
				if(!(report_ajax_call===undefined))
				{
					//alert('abort');
					report_ajax_call.abort();
				}
				report_ajax_call = $.ajax({
					url: "<?= base_url("index.php/documents/load_esign_docs_report")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							this_div.html(response);
							
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
		</script>
	</head>
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" class="scrollable_div" style="width:175px">
				<div id="scrollable_left_bar" class="scrollable_div" style="width:165px;overflow-x:hidden;">
					<?php $attributes = array('name'=>'filter_form','id'=>'filter_form','onkeypress'=>'return event.keyCode != 13;')?>
					<?=form_open('documents/load_esign_docs_report',$attributes);?>
						<br>
						<span style="font-weight:bold;">Search</span>
						<hr/>
						<input type="text" id="file_search" name="file_search" class="left_bar_input" onchange="search_file()" onkeydown="Javascript: if (event.keyCode==13) search_file();" placeholder="File ID">
						<br>
						<br>
					</form>
				</div>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">E Sign</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh List" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="search_file()" />
					</div>
				</div>
				<div id="results_div">
					<!-- REPORT GOES HERE !-->
				</div>
			</div>
		</div>
	</body>
</html>