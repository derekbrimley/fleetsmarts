<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
	.editable_cell:hover
	{
		/*background:#EFEFEF;*/
		background:#F7F7F7;
		cursor:default;
	}
</style>

<div id="main_content_header">
	<span style="font-weight:bold;">E Sign</span>
	<div style="float:right; width:25px;">
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_esign_docs_report()" />
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:30px; padding-left:5px;" VALIGN="top"></td>
		<td style="width:135px; padding-left:5px;" VALIGN="top">Recipient Signer</td>
		<td style="width:200px; padding-left:5px;" VALIGN="top">Category</td>
		<td style="width:400px; padding-left:5px;" VALIGN="top">Document</td>
		<td style="width:100px; padding-left:5px;" VALIGN="top">Upload Date</td>
		<td style="width:100px;" VALIGN="top">Signed Date</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php if(!empty($esign_docs)):?>
		<?php
			$i = 0;
		?>
		<?php foreach($esign_docs as $esign_doc):?>
			<?php
				$row = $esign_doc["id"];

				$background_color = "";
				if($i%2 == 0)
				{
					$background_color = "background-color:#F2F2F2;";
				}
				$i++;
			?>
			<div id="tr_<?=$row?>" name="tr_<?=$row?>" style=" margin-left:5px; margin-right:5px; <?=$background_color?>" >
				<?php include("esign_row.php"); ?>
			</div>
		<?php endforeach;?>
	<?php else: ?>
		<table  style="table-layout:fixed; margin:5px; font-size:12px;">
			<tr>
				<td style="font-weight:bold; padding-left:40px;">
					There are no results for this filter set
				</td>
			</tr>
		</table>
	<?php endif; ?>
</div>
<script>
</script>
