<style>
	.punch_in_button
	{
		background-color: green;
		width: 300px;
		height: 120px;
		font-size: 60px;
		font-weight: bold;
		color: white;
		line-height: 120px;
		border-radius: 10px 10px 10px 10px;
		box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
	}
	
	.punch_in_button:active
	{
		position:relative;
		top:10px;
		left:10px;
		box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
	}
	
	.punch_out_button
	{
		background-color: red;
		width: 300px;
		height: 120px;
		font-size: 60px;
		font-weight: bold;
		color: white;
		line-height: 120px;
		border-radius: 10px 10px 10px 10px;
		box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
	}
	
	.punch_out_button:active
	{
		position:relative;
		top:10px;
		left:10px;
		box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
	}
</style>

<script>
	$(document).ready(function(){
		
		
		
	});
	
	
	//SUBMIT PUNCH
	function submit_punch(in_out)
	{
		$("#in_out").val(in_out);
		
		var this_div = $('#main_window');
	
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#punch_form").serialize();
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/time_clock/submit_punch")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					//$("#refresh_logs").show();
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
</script>
<?php
	date_default_timezone_set('America/Denver');
?>

<?php $attributes = array('name'=>'punch_form','id'=>'punch_form', )?>
<?=form_open('',$attributes);?>
	<input type="hidden" id="user_id" name="user_id" value="<?=$user["id"]?>"/>
	<input type="hidden" id="datetime" name="datetime" value="<?=date("Y-m-d H:i:s",time())?>"/>
	<input type="hidden" id="in_out" name="in_out" value=""/>
	<input type="hidden" id="location" name="location" value="Time Clock"/>
</form>

<div id="main_box" name="main_box" style="float:left; width:55%; text-align:center;">
	<div style="width:300px; text-align:left;">
		<img src="/images/back_fat.png" style="width:80" onclick="location.reload();"/>
	</div>
	<div style="margin-top:50px;">
		<span style="font-size:60px;"><?=$user["person"]["full_name"]?></span>
		<br><br>
		<span style="font-size:50px;"><?=date('m/d/y H:i',time())?></span>
	</div>
	<div class="punch_in_button" style="margin:auto; margin-top:60px;" onclick="submit_punch('In')">
		IN
	</div>
	<div class="punch_out_button" style="margin:auto; margin-top:60px;" onclick="submit_punch('Out')">
		OUT
	</div>
	<div style="margin-top:50px; text-align:center; display:none;">
		<span style="font-size:20px;">You are currently logged </span><span style="font-size:30px;"><?=$last_punch["in_out"]?></span>
	</div>
</div>
<div id="right_bar" style=" border:solid 1px #CFCFCF; width:520px; height:95%; float:right;">
	<div style="height:40px; line-height:40px; font-size:20px; font-weight:bold; background-color:#CFCFCF; text-align:center;">
		Punch Log
	</div>
	<div style="width:500px; margin:auto;">
		<table style="width:500px; margin:auto; margin-top:15px;  font-size:16px;">
		<?php
			$i = 0;
		?>
		<?php foreach($punches as $punch):?>
			<?php
				$i++;
			?>
			<tr style="height:30px;">
				<td style="width:40px;">
					<?php if($i == 1):?>
						<?php if($punch["in_out"] == "In"):?>
							<img src="/images/green_dot.png" style="height:20px; position:relative; top:3px;"/>
						<?php else:?>
							<img src="/images/red_dot.png" style="height:20px; position:relative; top:3px;"/>
						<?php endif;?>
					<?php endif;?>
				</td>
				<td style="width:60px;">
					<?=$punch["in_out"]?>
				</td>
				<td style="width:200px;">
					<?=$user["person"]["full_name"]?>
				</td>
				<td style="width:200px;">
					<?=date('m/d/y H:i:s',strtotime($punch["datetime"]))?>
				</td>
			</tr>
		<?php endforeach;?>
		</table>
	</div>
</div>

