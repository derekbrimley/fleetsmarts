<script>
	$(function(){
		$("#available_permissions_container").height($(window).height() - 216);
		$("#current_permissions_container").height($(window).height() - 216);
	})
</script>
<div id="main_content_header">
	<span style="font-weight:bold;">User - <?=$company['company_name'] ?></span>
	<div style="float:right; width:25px;">
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img onclick="load_user_report()" id="refresh_settings" name="refresh_settings" src="/images/refresh.png" title="Refresh Settings" style="cursor:pointer; float:right; height:20px; padding-top:5px;"  />
	</div>
</div>
<div style="padding-left:68px;padding-top:10px;overflow:auto;" id="permission_report_container" name="permission_report_container">
	<div id="available_permissions_box" style="float:left;width:400px;border:solid 1px #CFCFCF;">
		<div id="available_permissions_header" style="line-height:20px;padding:5px;height:20px;background-color:#CFCFCF;">
			Available Permissions
		</div>
		<div id="available_permissions_container" style="padding:5px;overflow:auto;height:435px;">
			<table>
				<?php foreach($available_permissions as $available_permission): ?>
					<tr style="height:30px;">
						<td style="padding-top:10px; width:367px;">
							<?=$available_permission['permission_name'] ?>
						</td>
						<td style="padding-top:10px;">
							<img title="Give user permission" src="<?=base_url("images/blue_arrow.png") ?>" style="cursor:pointer;float:right;height:15px; padding-left:5px;" onclick="add_permission(<?=$user["id"] ?>,<?=$available_permission['id']?>)"/>
						</td>
					</tr>
				<?php endforeach ?>
			</table>
		</div>
	</div>
	<div id="current_permissions_box" style="float:left;margin-left:68px;width:400px;border:solid 1px #CFCFCF;"">
		<div id="current_permissions_header" style="line-height:20px;padding:5px;height:20px;background-color:#CFCFCF;">
			Current Permissions
		</div>
		<div id="current_permissions_container" style="padding:5px;overflow:auto;">
			<table>
				<?php foreach($current_permissions as $current_permission): ?>
					<tr style="height:30px;">
						<form id="form_delete_<?=$current_permission['id'] ?>">
							<input type="hidden" value="<?=$current_permission['id'] ?>" />
						</form>
						<td style="padding-top:10px; width:367px;">
							<?=$current_permission['permission_name'] ?>
						</td>
						<td style="padding-top:10px;">
							<img title="Take permission from user" src="<?=base_url("images/red_arrow.png") ?>" style="cursor:pointer;float:right;height:15px;" onclick="delete_permission(<?=$user['id'] ?>,<?=$current_permission['id']?>)"/>
						</td>
					</tr>
				<?php endforeach ?>
			</table>
		</div>
	</div>
</div>
