<table style="width:300px; margin: 0 auto; margin-top:25px;">
	<tr class="heading">
		<td style="width:200px;">
			Driver
		</td>
		<td style="width:80px; text-align:right;">
			Adjustment
		</td>
	</tr>
	<?php if(!empty($adjustments)): ?>
		<?php foreach($adjustments as $adjustment): ?>
			<tr>
				<td>
					<?=$adjustment["driver"]?>
				</td>
				<td style="text-align:right;">
					<?=number_format($adjustment["amount"],2)?>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	<tr style="font-weight:bold;">
		<td>
			TOTAL
		</td>
		<td style="text-align:right;">
			<?=number_format($total_adjustments,2)?>
		</td>
	</tr>
</table>