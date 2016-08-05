<style>
	hr
	{
		width:156px;
		margin:0px;
		margin-top:7px;
		margin-bottom:7px;
	}
	
	.blue_border
	{
		box-shadow: 0 0 0 3px #6295FC inset;
	}
</style>


<html>
	<head>
		<title><?php echo $title;?></title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<link href='http://fonts.googleapis.com/css?family=Homemade+Apple' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
		
		<?php include("todo/todo_script.php"); ?>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
				<div id="left_bar" style="width:175px">
					<!--
					<button class='left_bar_button jq_button' id="log_entry" onclick="create_new_todo()">New ToDo</button>
					<br>
					<br>
					!-->
					<?php $attributes = array('name'=>'filter_form','id'=>'filter_form')?>
					<?=form_open('todo/load_report',$attributes);?>
						<div style="margin-top:15px;" id="scrollable_filter_div"  class="scrollable_div">
							<div id="filter_div">
								<!-- AJAX GOES HERE !-->
							</div>
						</div>
					</form>
				</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">ToDo</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_report()" />
					</div>
				</div>
				<!-- REPORT GOES HERE !-->
			</div>
			
		</div>
	</body>
		
	
	<div id="add_new_entry" title="Allocate Expense" style="display:none;">
		<!-- AJAX GOES HERE!-->
	</div>
	
	<div id="add_notes" title="Add Note" style="padding:10px; display:none;">
		<div>
			<span id="notes_header" style="font-weight:bold;">ToDo Notes</span>
			<br>
			<br>
			<div id="notes_ajax_div" style="height:215px; overflow:auto">
				<!-- AJAX WILL POPULATE THIS !-->
			</div>
		</div>
		<div style="position:absolute; bottom:0">
			<?php $attributes = array('name'=>'add_note_form','id'=>'add_note_form', )?>
			<?=form_open('expenses/add_note/',$attributes);?>
				Add Note:
				<input type="hidden" id="row_id" name="row_id">
				<textarea style="width:400px;" rows="3" id="new_note" name="new_note"></textarea>
			</form>
		</div>
	</div>
	
</html>