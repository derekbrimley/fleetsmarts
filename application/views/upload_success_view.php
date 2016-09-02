<html>
	<!-- 	TODO: 								!-->
	<!-- 	SEARCH FUNCTION 					!-->
	<!-- 	ACTIVE/INACTIVE FILTER BOX			!-->
	<!-- 	FLEET MANAGER FILTER BOX			!-->
	<head>
		<title><?php echo $title;?></title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
	</head>
	<style>
		.box_shadow
		{
			-webkit-box-shadow: 7px 7px 5px 0px rgba(50, 50, 50, 0.75);
			-moz-box-shadow:    7px 7px 5px 0px rgba(50, 50, 50, 0.75);
			box-shadow:         7px 7px 5px 0px rgba(50, 50, 50, 0.75);
		}
	</style>
	<body id="body" class="box_shadow">
		<div style="font-family: arial; color:white; width:500px; height:200px; text-align:center; background: #CFCFCF; margin:auto; margin-top:240px; overflow:auto;" class="box_shadow">
			<div style="height:25px; font-size:35px; margin-top:70px;">
				Upload Success!
			</div>
			<div style="margin-top:20px;">
				This window will close...
			</div>
		</div>
	</body>
	
</html>

<script>
	setInterval(function(){close()},1000);
</script>