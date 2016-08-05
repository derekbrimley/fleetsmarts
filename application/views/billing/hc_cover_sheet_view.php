<head>
	<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
	<title><?php echo $load["customer_load_number"]." ".$title;?></title>
</head>
<?php
	//MAKE TEXT FOR ORIGIN AND DESTINATION
	$pick_text = "";
	$pick_title = "";
	$these_picks = $load['load_picks'];
	sort($these_picks);
	foreach($these_picks as $pick)
	{
		$pick_text = $pick['stop']["city"].", ".$pick['stop']["state"];
		break;
	}
	
	$drop_text = "";
	$drop_title = "";
	$these_drops = $load['load_drops'];
	sort($these_drops);
	foreach($these_drops as $drop)
	{
		$drop_text = $drop['stop']["city"].", ".$drop['stop']["state"];
		break;
	}
?>
<div style="width:600px;">
	<span style="font-size:20px">
		<span><?= $load["billed_under_carrier"]["company_name"]; ?></span>
		<span style="float:right; "><?= date("m/d/y",strtotime($load["final_drop_datetime"])); ?></span>
		<br><br>
		<span><?= $load["broker"]["customer_name"]; ?></span>
		<span style="float:right;"><?= $load["customer_load_number"]; ?></span>
	</span>
	<table style="margin-top:80px;">
		<tr>
			<td style="width:200px;">
				Fleet Manager
			</td>
			<td style="width:200px;">
				<?= $load["fleet_manager"]["full_name"]; ?>
			</td>
		</tr>
		<tr>
			<td style="width:200px;">
				Broker
			</td>
			<td style="width:200px;">
				<?= $load["broker"]["customer_name"]; ?>
			</td>
		</tr>
		<tr>
			<td style="width:200px;">
				Client
			</td>
			<td style="width:200px;">
				<?= $load["client"]["company"]["company_side_bar_name"]; ?>
			</td>
		</tr>
		<tr>
			<td style="width:200px;">
				Billed Under
			</td>
			<td style="width:200px;">
				<?= $load["billed_under_carrier"]["company_name"]; ?>
			</td>
		</tr>
		<tr>
			<td style="width:200px;">
				Load Number
			</td>
			<td style="width:200px;">
				<?= $load["customer_load_number"]; ?>
			</td>
		</tr>
		<tr>
			<td style="width:200px;">
				Pick
			</td>
			<td style="width:200px;">
				<?= $pick_text?> <?= date("m/d/y",strtotime($load["first_pick_datetime"])); ?>
			</td>
		</tr>
		<tr>
			<td style="width:200px;">
				Drop
			</td>
			<td style="width:200px;">
				<?= $drop_text?> <?= date("m/d/y",strtotime($load["final_drop_datetime"])); ?>
			</td>
		</tr>
	</table>
</div>