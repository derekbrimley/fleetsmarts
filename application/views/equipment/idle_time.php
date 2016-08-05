<!DOCTYPE html>
<html>
<head>
	<title><?=$title?></title>
	<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
	<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
	<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
	<script>
		$(function(){
			$('#fromDate').datepicker();
			$('#toDate').datepicker();
		});
		
		function submit_idle_form()
		{
			var dataString = $('#filter_form').serialize();
			console.log(dataString);
			console.log($("#fromDatetime").val());
			$.ajax({
				url: "<?=base_url("index.php/equipment/idle_report") ?>",
				type: "POST",
				data: dataString,
				cache: false,
				statusCode: 
				{
					200: function(response)
					{
						$('#container').html(response);
					},
					404: function()
					{
						alert('Page not found');
					},
					500: function(response)
					{
						alert("500 error! "+response);
					}
				}
			});
		}
	</script>
	<style>
		.container{
			display:flex;
			flex-wrap:wrap;
		}
		.asset_container{
			flex:1 1 200px;
			padding: 10px;
			margin: 10px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
		}
	</style>
</head>
<body>
	<div>
		<form id="filter_form">
			From 
			<input type="text" id="fromDatetime" name='fromDatetime'> 
			to 
			<input type="text" id='toDatetime' name='toDatetime' value="" placeholder="End Date"/>
		</form>
		<button type="button" id="idle_report_submit" onClick="submit_idle_form()">Submit</button>
	</div>
	<div class="container" id="container">
		<?php foreach($assetDatas as $assetData): ?>
			<div class="asset_container">
				<div>
					Truck <?=$assetData['asset']?>
				</div>
				<div>
					Idle Time: <?=gmdate("H:i:s", $assetData['idle_time'])?>
				</div>
				<div>
					Idle Fuel: <?=$assetData['idle_fuel_total']?> gallons
				</div>
				<div>
					Total Miles: <?=$assetData['mile_total']?>
				</div>
				<div>
					Total Fuel: <?=$assetData['fuel_total']?>
				</div>
				<div>
					MPG: <?=$assetData['mpg']?>
				</div>
			</div>
		<?php endforeach?>
	</div>
</body>
</html>