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
			Total Miles: <?=$assetData['mile_total']?> miles
		</div>
		<div>
			Total Fuel: <?=$assetData['fuel_total']?> gallons
		</div>
		<div>
			MPG: <?=$assetData['mpg']?>
		</div>
	</div>
<?php endforeach?>