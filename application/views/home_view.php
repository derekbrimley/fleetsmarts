<html>

<head>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
		<script src=<?= base_url("js/main.js") ?>></script>
		<link rel="manifest" href="<?= base_url("js/manifest.json") ?>">
		<script src="<?= base_url("socket.io/socket.io.js") ?>"></script>
		<script>
		  var socket = io('http://localhost');
		  socket.on('news', function (data) {
			console.log(data);
			socket.emit('my other event', { my: 'data' });
		  });
		</script>
<script type="text/javascript">
$(document).ready(function(){
  
});//end document ready

// request permission on page load
document.addEventListener('DOMContentLoaded', function () {
  if (Notification.permission !== "granted")
    Notification.requestPermission();
});

function notifyMe() 
{
  //alert('hi');
  if (!Notification) 
	{
		alert('Desktop notifications not available in your browser. Try Chromium.'); 
		return;
	}
	//alert('yo');
	if (Notification.permission !== "granted")
	{
		//alert('sup');
		Notification.requestPermission();
    }
	else 
	{
		var notification = new Notification('Login Verification', 
		{
			icon: 'http://fleetsmarts.net/images/time_keep_logo.png',
			body: "Please click here to verify that you are still working.",
		});

		notification.onclick = function () 
		{
			window.open("http://fleetsmarts.net/index.php/time_clock");      
		};
    
	}
}
</script>

<title><?php echo $title;?></title>
</head>

<body>


<?php include("main_menu.php"); ?>
<br><br><br>
<div style="width:500px; text-align:center;" id="main_div">
	<p>Welcome to the home page of Fleetsmarts!</p>
	<p>Version: 2.0 (<i>Azkaban</i>)</p>
	<p>Upgraded: 07/05/15</p>
</div>

<div style="display:none;" class="link clickable_div" onclick="notifyMe()">
	Notify!
</div>

</body>
</html>