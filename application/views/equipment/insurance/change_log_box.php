<?php
	
?>
<div style="margin-left:30px; float:left;">
		<div class="ins_box" style="padding:10px; width:520px;">
			<div style="">
				<span class="heading">Change Log</span>
			</div>
		</div>
		<div style="height:150px; margin-bottom:20px;" class="scrollable_div"?>
			<table style="margin-left:10px; margin-top:10px; font-size:12px;">
				<?php
					$i = 0;
				?>
				<?php foreach($change_logs as $change_log):?>
					<?php
						$row_background_style = "";
						if($i%2 == 0)
						{
							$row_background_style = "background-color:#F7F7F7;";
						}
						$i++;
						
						//GET USER PERSON
						$where = null;
						$where["id"] = $change_log["user_id"];
						$user = db_select_user($where);
					?>
					<tr style="<?=$row_background_style?>">
						<td style="width:70px;padding-bottom:5px; padding-top:5px;">
							<?=date("m/d/y",strtotime($change_log["change_date"]))?>
						</td>
						<td style="width:90px; padding-bottom:5px; padding-top:5px; paddint-left:10px;" title="<?=$user["person"]["full_name"]?>">
							<?=$change_log["change_reason"]?>
						</td>
						<td style="width:290px; padding-bottom:5px; padding-top:5px; padding-left:10px;">
							<?=$change_log["change_desc"]?>
						</td>
						<td style="width:40px;padding-bottom:5px; padding-top:5px; padding-left:10px; text-align:right;">
							<a href=""><?=$change_log["change_proof"]?></a>
						</td>
					</tr>
				<?php endforeach;?>
			</table>
		</div>
	</div>