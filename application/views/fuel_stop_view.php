<!DOCTYPE html>
<html lang="en">
<head>
<?php $ua = strtolower($_SERVER['HTTP_USER_AGENT']); ?>
<?php if(stripos($ua,'android') !== false):?>
	<meta name='viewport' content='user-scalable=0'>
<?php endif; ?>
	
	
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/mobile_template.css"); ?>" rel="stylesheet" />		
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>

<script type="text/javascript">
	$(document).ready(function(){
		var myDate = new Date();
		var date = (myDate.getMonth()+1) + '/' + (myDate.getDate()) + '/' + myDate.getFullYear();
		
		var hours = ""+myDate.getHours();
		var minutes = ""+myDate.getMinutes();
		var seconds = ""+myDate.getSeconds();
		
		if (myDate.getHours() < 10)
		{
			hours = "0".concat(myDate.getHours());
		}
		if (myDate.getMinutes() < 10)
		{
			minutes = "0".concat(myDate.getMinutes());
		}
		if (myDate.getSeconds() < 10)
		{
			seconds = "0".concat(myDate.getSeconds());
		}
		
		var time = hours+":"+minutes+":"+seconds;
		$("#date").val(date);
		$("#time").val(time);
	});//end document ready
	
	//ON LOCATION FOUND
	function success(position) 
	{
		//alert("Latitude: "+position.coords.latitude);
		//alert("Longitude: "+position.coords.longitude);
		$("#latitude").val(position.coords.latitude);
		$("#longitude").val(position.coords.longitude);
	}

	function error(msg) 
	{
	  alert(msg);
	}

	if (navigator.geolocation) 
	{
	  navigator.geolocation.getCurrentPosition(success, error);
	} 
	else 
	{
	  error('not supported');
	}
	
	function validate()
	{
		var isvalid = true;
		
		if (!$("#location_name").val())
		{
			alert("Location Name must be entered!");
			isvalid = false;
		}
		
		if (!$("#city").val())
		{
			alert("City must be entered!");
			isvalid = false;
		}
		
		if (!$("#state").val())
		{
			alert("State must be entered!");
			isvalid = false;
		}
		
		if (!$("#address").val())
		{
			alert("Address must be entered!");
			isvalid = false;
		}
		
		if (!$("#odometer").val())
		{
			alert("Odometer must be entered!");
			isvalid = false;
		}
		
		if ($("#fill_or_partial").val()=="Select")
		{
			alert("Fill or Partial must be selected!");
			isvalid = false;
		}
		
		if (!$("#gallons").val())
		{
			alert("Gallons must be entered!");
			isvalid = false;
		}
		
		if (!$("#invoice_amount").val())
		{
			alert("Receipt Total must be entered!");
			isvalid = false;
		}
			
		if (isvalid)
		{
			$("#form").submit();
		}
		
	}
</script>

<title><?php echo $title;?></title>
</head>

<body>
	<div id="main_div">
		<div id="main_title_div">
			<a href="<?= base_url('index.php/home');?>" style='text-decoration:none; color: #0073ea;'>Home</a> > 
			<a href="<?= base_url('index.php/stops_menu');?>" style='text-decoration:none; color: #0073ea;'>Record Stops</a> > Fuel Stop
			<hr>
		</div>
		<div id="main_content" >
			<div id="main_content_header" >
					<span style="">Fuel Stop</span>
			</div>
			<br>
			<div style="width:200px; margin:0 auto;">
				<?php $attributes = array('id' => 'form'); ?>
				<?=form_open('fuel_stop/submit',$attributes)?>
					<input type="hidden" name="company_id" id="company_id" value="<?=$company_id?>" />
					Location Name:<br>
					<input type='text' id='location_name' name='location_name' class="mobile_input">
					<br>
					<br>
					City:<br>
					<input type='text' id='city' name='city' class="mobile_input">
					<br>
					<br>
					State:<br>
					<input type='text' id='state' name='state' class="mobile_input">
					<br>
					<br>
					Address:<br>
					<input type='text' id='address' name='address' class="mobile_input">
					<br>
					<br>
					Odometer:<br>
					<input type='number' id='odometer' name='odometer' class="mobile_input">
					<br>
					<br>
					Fill or Partial:<br>
					<?php 
						$options = array
						(
							'Select' => 'Select',
							'Fill'  => 'Fill',
							'Partial'    => 'Partial',
						); 
					?>
					<?php echo form_dropdown('fill_or_partial',$options,"Select",'id="fill_or_partial" style="width:204px; height:38px;"');?>
					<br>
					<br>
					Gallons:<br>
					<input type='number' id='gallons' name='gallons' class="mobile_input">
					<br>
					<br>
					Receipt Total:<br>
					<input type='number' id='invoice_amount' name='invoice_amount' class="mobile_input">
					<br>
					<br>
					Date:<br>
					<input type='text' id="date" name='date' readonly="readonly" class="mobile_input">
					<br>
					<br>
					Time:<br>
					<input type='text' id="time" name='time' readonly="readonly"  class="mobile_input">
					<br>
					<br>
					Latitude:<br>
					<input type='text' id='latitude' name='latitude' readonly="readonly" placeholder="Finding location..." class="mobile_input">
					<br>
					<br>
					Longitude:<br>
					<input type='text' id='longitude' name='longitude' readonly="readonly"  placeholder="Finding location..." class="mobile_input">
				<br>
				<br>
				</form>
				<button onclick="validate()" style="margin:0 auto; height:50px; width:200px" class="left_bar_button jq_button" id="change_password_button">Record Fuel Stop</button><br>
				
			</div>
		</div>
	</div>
	
</body>
</html>