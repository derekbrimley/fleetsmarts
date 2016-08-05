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
		<link type="text/css" href="<?=base_url("css/generate_documents.css");?>" rel="stylesheet"/>
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
		

	</head>
	<body id="body">

		<?php include("main_menu.php"); ?>
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" class="scrollable_div" style="width:175px">
				<button class='left_bar_button jq_button' id="new_client" onclick="openDialog()">Generate Document</button>
				<br>
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
		<div id="new_doc_dialog" title="Generate New Document">
			<?php
				$attributes = array('id'=>'genDocForm','target'=>'_blank');
				echo form_open('documents/generate_document', $attributes); 
			?>
				<table style="width:100%">
					<tr>
						<td>Report</td>
						<td>
							<select name="documentSelect" id="documentSelect">
								<option value="0">Select</option>
								<option value="1">Master Packet</option>
							</select>
						</td>
					</tr>
					<tr id="driver_dropdown" class="hidden">
						<td>Driver</td>
						<td>
							<select name="driverSelect" id="driverSelect">
								<option value="0">Select</option>
								<?php foreach($driver_options as $key => $driver): ?>
									<option value="<?=$key?>"><?=$driver?></option>
								<?php endforeach ?>
							</select>
						</td>
					</tr>
					<tr id="company_dropdown" class="hidden">
						<td>Company</td>
						<td>
							<select name="companySelect" id="companySelect">
								<option value="0">Unknown</option>
								<?php foreach($companies as $company): ?>
									<option value="<?=$company['id']?>"><?=$company['company_name']?></option>
								<?php endforeach ?>
								<option value="1">Master Packet</option>
							</select>
						</td>
					</tr>
				</table>
				<div id="generateDocBtn" class="hidden" style="text-align:center">
					<?php echo form_submit('genDoc', 'Generate Document','class="genBtn"'); ?>
				</div>
			</form>
		</div>
		<script src="<?=base_url('js/generate_documents.js')?>"></script>
	</body>
</html>