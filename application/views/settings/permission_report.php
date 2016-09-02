<script>
	$(function(){
		$("#available_users_container").height($(window).height() - 216);
		$("#current_users_container").height($(window).height() - 216);
	})
</script>
<div id="main_content_header">
	<span style="font-weight:bold;">Permission - <?=$permission['permission_name'] ?></span>
	<div style="float:right; width:25px;">
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img onclick="load_permission_report()" id="refresh_settings" name="refresh_settings" src="/images/refresh.png" title="Refresh Settings" style="cursor:pointer; float:right; height:20px; padding-top:5px;"  />
	</div>
</div>
<div style="padding-left:68px;padding-top:10px;overflow:auto;" id="permission_report_container" name="permission_report_container">
	<div id="available_permissions_box" style="float:left;width:400px;border:solid 1px #CFCFCF;">
		<div id="available_users_header" style="line-height:20px;padding:5px;height:20px;background-color:#CFCFCF;">
			Available Users
		</div>
		<div id="available_users_container" style="padding:5px;overflow:auto;height:435px;">
			<?php foreach($available_persons as $available_person): ?>
				<div style="width:100%;height:30px;">
					<span><?=$available_person["company_name"] ?><span>
					<span>
						<img title="Give user permission" src="/images/blue_arrow.png" style="cursor:pointer;float:right;height:15px;" onclick="add_user(<?=$available_person["id"] ?>,<?=$permission['id']?>)"/>
					</span>
				</div>
			<?php endforeach ?>
		</div>
	</div>
	<div id="current_permissions_box" style="float:left;margin-left:68px;width:400px;border:solid 1px #CFCFCF;">
		<div id="current_users_header" style="line-height:20px;padding:5px;height:20px;background-color:#CFCFCF;">
			Current Users
		</div>
		<div id="current_users_container" style="padding:5px;overflow:auto;">
			<?php foreach($current_persons as $current_person): ?>
				<form id="form_delete_<?=$current_person['id'] ?>">
					<input type="hidden" value="<?=$current_person['id'] ?>" />
				</form>
				<div style="width:100%;height:30px;">
					<span><?=$current_person['company_name'] ?></span>
					<span>
						<img title="Take permission from user" src="/images/red_arrow.png" style="cursor:pointer;float:right;height:15px;" onclick="delete_user(<?=$current_person['id'] ?>,<?=$permission['id']?>)"/>
					</span>
				</div>
			<?php endforeach ?>
		</div>
	</div>
</div>
