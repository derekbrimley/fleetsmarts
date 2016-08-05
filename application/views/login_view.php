<html>
	<title><?php echo $title;?></title>
	<head>
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/login.css"); ?>" rel="stylesheet" />		
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
	
		<script type="text/javascript">
		$(document).ready(function(){
		});//end document ready
		</script>
	</head>
<body>
	<div id="login_box">
		<div id="inner_box">
			<?php $attributes = array('name'=>'sign_in_form','ID'=>'sign_in_form' )?>
			<?=form_open('login/authenticate', $attributes);?>
				<?=form_hidden('ref_uri',$ref_uri); ?>
				Username:<br>
				<input type='text' name='username' style="margin-top:5px; width:200px; height: 32px;">
				<br>
				<br>
				Password:<br>
				<input type='password' name='password' style="margin-top:5px; width:200px; height: 32px; font-size: 15px;">
				<br>
				<br>
				<input type="submit" value="Sign In" id="sign_in_button" class="sign_in_button"/>
			</form>
		</div>
	</div>
</body>
</html>