<head>
	<title><?php echo $title;?></title>
	<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
	<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
	<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
	<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
	<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
	
	<style>
		.number_key
		{
			min-width:150px;
			height:120px;
			line-height:120px;
			text-align:center;
			font-size:100px;
			
		}
		
		.number_key:active
		{
			background-color:#CFCFCF;
		}
	</style>
	
	<script>
		$(document).ready(function(){
			
			//ON KEYUP FOR PIN LISTENER
			$("input").keyup(function(){
				number_pressed();
			});
			
			
		});
		
		var digit = 0;
		var pin = "";
		function number_pressed(x)
		{
			pin = pin+x;
			digit++;
			
			//UPDATE DOTS
			$("#dot_"+digit).attr("src","/images/filled_pin_dot.png");
			
			//alert(digit);
			
			if(digit == 4)
			{
				//alert(pin);
				$("#pin").val(pin);
				submit_pin();
			}
		}
		
		function reset_pin()
		{
			digit = 0;
			pin = "";
			
			//RESET DOTS
			$("#dot_1").attr("src","/images/empty_pin_dot.png");
			$("#dot_2").attr("src","/images/empty_pin_dot.png");
			$("#dot_3").attr("src","/images/empty_pin_dot.png");
			$("#dot_4").attr("src","/images/empty_pin_dot.png");
		}
		
		//SUBMIT PIN
		function submit_pin()
		{
			var this_div = $('#main_window');
		
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#pin_form").serialize();
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/time_clock/validate_pin")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						//$("#refresh_logs").show();
						//alert(response);
						open_dispatcher_dialog();
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
		
		function open_dispatcher_dialog()
		{
			var is_dispatcher = "<?= $is_dispatcher ?>";
			if(is_dispatcher == "true")
			{
				$("#dispatcher_dialog").dialog(
				{
					autoOpen: true,
					height: 300,
					width: 500,
					modal: true,
					buttons: 
					[
						{
							text: "Submit",
							click: function() 
							{
								var fleet_manager_id = $("#fleet_manager_dropdown").val();
								console.log(fleet_manager_id);
								$.ajax({
									url: "<?= base_url("index.php/time_clock/update_fleet_manager")?>", // in the quotation marks
									type: "POST",
									data: {fleet_manager_id:fleet_manager_id},
									cache: false,
									statusCode: {
										200: function(response){
											// Success!
											$("#dispatcher_dialog").dialog( "close" );
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
								});
							}
						}
					]
				});
			}
		}
	</script>
</head>

<body style="font-family:arial;">
	<div id="main_window" name="main_window" style="padding:10px;">
		<div style="width:180px; text-align:center; margin:auto;">
			<img style="height:180px;" src="/images/time_keep_logo.png"/>
		</div>
		<?php $attributes = array('name'=>'pin_form','id'=>'pin_form', )?>
		<?=form_open('',$attributes);?>
				<input type="hidden" id="pin" name="pin"/>
		</form>
		<div style="width:155px; margin:auto; margin-top:20px;">
			<img id="dot_1" src="/images/empty_pin_dot.png" style="height:20px; margin-right:20px;"/>
			<img id="dot_2" src="/images/empty_pin_dot.png" style="height:20px; margin-right:20px;"/>
			<img id="dot_3" src="/images/empty_pin_dot.png" style="height:20px; margin-right:20px;"/>
			<img id="dot_4" src="/images/empty_pin_dot.png" style="height:20px; margin-right:0px;"/>
		</div>
		<div style="width:450px; margin:auto; margin-top:30px;">
			<table>
				<tr>
					<td class="number_key" onclick="number_pressed('1')">
						1
					</td>
					<td class="number_key" onclick="number_pressed('2')">
						2
					</td>
					<td class="number_key" onclick="number_pressed('3')">
						3
					</td>
				</tr>
				<tr>
					<td class="number_key" onclick="number_pressed('4')">
						4
					</td>
					<td class="number_key" onclick="number_pressed('5')">
						5
					</td>
					<td class="number_key" onclick="number_pressed('6')">
						6
					</td>
				</tr>
				<tr>
					<td class="number_key" onclick="number_pressed('7')">
						7
					</td>
					<td class="number_key" onclick="number_pressed('8')">
						8
					</td>
					<td class="number_key" onclick="number_pressed('9')">
						9
					</td>
				</tr>
				<tr>
					<td class="number_key" style="height:0px;">
						
					</td>
					<td class="number_key" style="height:0px;" onclick="number_pressed('0')">
						0
					</td>
					<td class="number_key" style="height:0px;" onclick="reset_pin()">
						<img src="/images/delete_pin.png" style="height:40px; position:relative; bottom:20px;"/>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>

<div id="dispatcher_dialog" title="Select Fleet Manager" style="display:none;">
	<div style="height:150px;overflow:auto;text-align:center;font-size:16pt;">
		<?php if(!empty($fleet_manager_and_dispatchers)): ?>
			<?php foreach($fleet_manager_and_dispatchers as $fleet_manager => $dispatcher): ?>
				<div>
					<?= $dispatcher ?> is assigned to <?= $fleet_manager ?>.
				</div>
			<?php endforeach ?>
		<?php endif ?>
	</div>
	<div style="margin-top:10px;text-align:center;font-size:16pt;">
		Select Fleet Manager
		<?php echo form_dropdown('fleet_manager_dropdown',$fleet_managers_dropdown_options, 'All FMs','id="fleet_manager_dropdown"') ?>
	</div>
	</form>
</div>