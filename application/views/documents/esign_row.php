<?php
	$upload_date_text = "";
	$status = '';
	if(!empty($esign_doc["upload_datetime"]))
	{
		$upload_date_text = date("m/d/y H:i",strtotime($esign_doc["upload_datetime"]));
	}
	
	$signed_date_text = "";
	if(!empty($esign_doc["signed_datetime"]))
	{
		$status = "SIGNED";
		$signed_date_text = date("m/d/y H:i",strtotime($esign_doc["signed_datetime"]));
	}
	else
	{
		$status = "UNSIGNED";
	}
?>
	<table  style="table-layout:fixed; font-size:12px;">
		<tr class="" style="line-height:30px;">
			<?php if(empty($esign_doc["signed_datetime"])):?>
				<td style="width:30px; padding-left:5px;" VALIGN="top">
					<image src="/images/load_status_1_icon.png" style="height:20px; position:relative; top:5px;" title="<?=$status?>"/>
				</td>
				<td style="width:135px; padding-left:5px;" VALIGN="top"><?=$esign_doc["person"]["f_name"]." ".$esign_doc["person"]["l_name"]?></td>
				<td style="width:200px; padding-left:5px; vertical-align:middle; line-height:15px;" VALIGN="top"><?=$esign_doc["unsigned_doc"]["category"]?></td>
				<td style="width:400px; padding-left:5px;" VALIGN="top"><a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$esign_doc["unsigned_doc_guid"]?>" onclick="">[<?=$status?>] <?=$esign_doc["unsigned_doc"]["title"]?></a></td>
			<?php else:?>
				<td style="width:30px; padding-left:5px;" VALIGN="top">
					<image src="/images/load_status_7_icon.png" style="height:20px; position:relative; top:5px;" title="<?=$status?>"/>
				</td>
				<td style="width:135px; padding-left:5px;" VALIGN="top"><?=$esign_doc["person"]["f_name"]." ".$esign_doc["person"]["l_name"]?></td>
				<td style="width:200px; padding-left:5px; vertical-align:middle; line-height:15px;" VALIGN="top"><?=$esign_doc["signed_doc"]["category"]?></td>
				<td style="width:400px; padding-left:5px;" VALIGN="top"><a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$esign_doc["signed_doc_guid"]?>" onclick="">[<?=$status?>] <?=$esign_doc["signed_doc"]["title"]?></a></td>
			<?php endif;?>
			<td style="width:100px; padding-left:5px;" VALIGN="top"><?=$upload_date_text?></td>
			<td style="width:100px;" VALIGN="top"><?=$signed_date_text?></td>
		</tr>
	</table>
