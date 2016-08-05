<style>
	.border
	{
		border: 1px solid black;
		padding:3px;
	}
</style>
<div style="width:800px; margin: auto; margin-top:20px; margin-bottom:15px; font-family:arial;">
	<span style="font-size:16pxpx; font-weight:bold; position:relative; top:12px;"><?=$list_title?></span>
	<br><br>
	<table style="">
		<tr class="heading" style="font-size:14px; height:16px;">
			<td style="">
				<div style="width:70px">Truck</div>
			</td>
			<td style="" colspan="2">
				<div style="width:150px">Lead Driver</div>
			</td>
			<td style="" colspan="2">
				<div style="width:150px">Co-Driver</div>
			</td>
			<td style="" colspan="2">
				<div style="width:70px">Trailer</div>
			</td>
			<td style="">
				<div style="width:265px;">Notes</div>
			</td>
		</tr>
		<?php foreach($trucks as $truck): ?>
			<tr style="height:15px; font-size:12px;">
				<td rowspan="2" class="border" style="vertical-align:middle; ">
					<div style="width:70px">
						<?=$truck["truck_number"]?>
						<br>
						<span style="font-size:10px; font-style:italic;">VIN <?=substr($truck["vin"],-4)?></span>
					</div>
				</td>
				<td class="border">
					<div style="width:150px">
						<?=$truck["client"]["client_nickname"]?>
						<br>
						<span style="font-size:10px; font-style:italic;"><?=$truck["client"]["license_number"]?> - <?=$truck["client"]["license_state"]?></span>
					</div>
				</td>
				<td class="border" style="">
					<div style="width:15px;">&nbsp;</div>
				</td>
				<td class="border">
					<div style="width:150px">
						<?=$truck["codriver"]["client_nickname"]?>
						<br>
						<span style="font-size:10px; font-style:italic;"><?=$truck["codriver"]["license_number"]?> - <?=$truck["codriver"]["license_state"]?></span>
					</div>
				</td>
				<td class="border" style="">
					<div style="width:15px;">&nbsp;</div>
				</td>
				<td class="border">
					<div style="width:70px">
						<?=$truck["trailer"]["trailer_number"]?>
						<br>
						<span style="font-size:10px; font-style:italic;">VIN <?=substr($truck["trailer"]["vin"],-4)?></span>
					</div>
				</td>
				<td class="border" style="">
					<div style="width:15px;">&nbsp;</div>
				</td>
				<td rowspan="2" class="border">
					<div style="265px"><?=$truck["truck_notes"]?></div>
				</td>
			</tr>
			<tr style="height:21px;">
				<td class="border" colspan="2">
				</td>
				<td class="border" colspan="2">
				</td>
				<td class="border" colspan="2">
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<br>
	<span style="font-size:12px;">Signature ___________________________________________________</span><span style="font-size:12px; float:right;">Date _________________________</span>
</div>