<html style="font-family:arial; font-size:12px;">
	<body>
		<div>
			<table>
				<tr>
					<td style="font-weight:bold; width:200px;">
						Load Number
					</td>
					<td>
						<?= $load_id ?>
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;">
						Truck Number
					</td>
					<td>
						<?=$truck_number?>
					</td>
				</tr>
				<?php foreach($file_guids as $name => $file_guid): ?>
					<tr>
						<td style="font-weight:bold;">	
							Link to document
						</td>
						<td style="max-width:150px;">
							<a href="<?= base_url('/index.php/documents/download_file/' . $file_guid) ?>"><?= $name ?></a>
						</td>
					</tr>
				<?php endforeach ?>
			</table>
		</div>
	</body>
</html>