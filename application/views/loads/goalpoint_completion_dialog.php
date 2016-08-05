<?php
	//GET TRUCK
	$where = null;
	$where["id"] = $goalpoint["truck_id"];
	$truck = db_select_truck($where);
	
	//GET TRAILER
	$where = null;
	$where["id"] = $goalpoint["trailer_id"];
	$trailer = db_select_trailer($where);
	
	//GET DRIVER
	$where = null;
	$where["id"] = $goalpoint["client_id"];
	$client = db_select_client($where);
	
	$geocode = reverse_geocode($goalpoint["gps"]);
	
	//CREATE DEADLINE TEXT
	$deadline_text = "None";
	if(!empty($goalpoint["deadline"]))
	{
		$deadline_text = date("m/d/y H:i",strtotime($goalpoint["deadline"]));
	}
?>
<style>
	html, body 
	{
		height: 100%;
		margin: 0;
		padding: 0;
	}
	#map 
	{
		height: 100%;
	}
	.labels
	{
		background: #fff;
		color: #212121;
		font-family: 'Titillium Web', sans-serif;
		font-size: 14px;
		border:1px solid #646464;
		border-radius: 4px;
		font-weight: bold;
	}
</style>
<script>
	var myLatLng = {lat: <?=$lat?>, lng: <?=$lng?>};
	var map = new google.maps.Map(document.getElementById('map'), 
		{
		  zoom: 15,
		  center: myLatLng
		});
	//var marker = new google.maps.Marker;
	
	var marker = new google.maps.Marker(
			{
			  position: myLatLng,
			  map: map,
			  scale: 5,
			  title: '<?=$goalpoint["gp_type"]?> '
			});
	
	var positions = [];
	<?php if(!empty($geopoints)):?>
		var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		var labelIndex = 0;
		<?php foreach($geopoints as $gp):?>
			positions[<?=$gp["id"]?>] = {lat: <?=$gp["latitude"]?>, lng: <?=$gp["longitude"]?>};
			
			var heading = <?=$gp["heading"]?>;
			
			<?php if($gp["speed"] == 0):?>
				fillColor = '#FF0000';//RED
				marker = new google.maps.Marker(
				{
				  position: positions[<?=$gp["id"]?>],
				  map: map,
				  label: labels[labelIndex++ % labels.length],
				  fillColor: fillColor
				});
				
				//path = google.maps.SymbolPath.CIRCLE;
				//fillColor = '#F44336';
			<?php else:?>
				path = google.maps.SymbolPath.FORWARD_CLOSED_ARROW;
				fillColor = '#4CAF50';//GREEN
				
				marker = new google.maps.Marker(
				{
				  position: positions[<?=$gp["id"]?>],
				  map: map,
				  icon: {
					scale: 5,
					rotation:Number(heading),
					fillOpacity: 1,
					strokeWeight: 1,
					path: path,
					fillColor: fillColor
				  },
				});
			<?php endif;?>
			
			
			
			var infowindow = new google.maps.InfoWindow({
				content: '<div style="text-align:center; width:100px; height:25px;"><?=date("m/d/y H:i",strtotime($gp["datetime"]))?></div>'
			});
			
			marker.addListener('click',function(){
				infowindow.open(map, this);
				$("#geopoint_dropdown").val(<?=$gp["id"]?>);
				get_geopoint_data();
			});
			
		<?php endforeach;?>
	<?php endif;?>
	

	
	function draw_new_marker(myLatLng)
	{
		//REMOVE MARKERS FROM MAP
		marker.setMap(null);
		
		//myLatLng = positions[gp_id];
		
		marker = new google.maps.Marker(
			{
			  position: myLatLng,
			  map: map,
			  title: 'Hello World!'
			});
		
		//CENTER MAP ON NEW MARKER
		map.setCenter(myLatLng);
	}
	
	function draw_custom_marker()
	{
		var cell_value = $("#gp_complete_gps").val();
		var stripped_address = cell_value.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
		//alert(stripped_address);
		if(stripped_address && !isNaN(stripped_address))
		{
			var latlng_array = cell_value.split(",");
			var new_lat = latlng_array[0];
			var new_lng =  latlng_array[1];
			//alert(new_lat);
			//alert(new_lng);
			var latlng = new google.maps.LatLng(new_lat, new_lng);
			//latlng = {lat: new_lat, lng: new_lng};
			draw_new_marker(latlng);
			
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode( { 'location': latlng}, function(results, status)
			{
				//alert(status);
				if (status == google.maps.GeocoderStatus.OK) 
				{
					var location_type = results[0].geometry.location_type;
					
					//alert("Google Approves =)");
					// var geo_city = results[0]['address_components'][2]['long_name'];
					// var geo_state = results[0]['address_components'][4]['short_name'];
					
					//alert(geo_city);
					//alert(geo_state);
					
					//$("#gps_city").val(results[0]['address_components'][2]['long_name']);
					//$("#gps_state").val(results[0]['address_components'][4]['short_name']);
					
					var street_number = extractFromAdress(results[0].address_components, "street_number");
					var street = extractFromAdress(results[0].address_components, "route");
					var geo_city = extractFromAdress(results[0].address_components, "locality");//CITY
					var geo_state = extractFromAdress(results[0].address_components, "administrative_area_level_1");//STATE
					
					//alert(street_number);
					//alert(geo_state);
					
					$("#complete_gp_address_text").html(street_number+", "+street);
					$("#complete_gp_city_text").html(geo_city);
					$("#complete_gp_state_text").html(geo_state);
					
					$("#gps_isvalid").val("yes");
				} 
				else 
				{
					alert('Uh oh!Google returned the following: ' + status);
					$("#gps_isvalid").val("no");
				}
			});
		}
		else
		{
			alert('These are not valid GPS coordinates');
			$("#gps_isvalid").val("no");
		}
	}
	
	var get_geopoint_data_ajax_call;
	function get_geopoint_data()
	{
		
		var geopoint_id = $("#geopoint_dropdown").val();
		//draw_new_marker(positions[geopoint_id])
		//map.setCenter(positions[geopoint_id]);
		geopoint_details_ajax(geopoint_id,<?=$goalpoint["id"]?>);
	}
	
	function geopoint_details_ajax(geopoint_id,goalpoint_id)
	{
		//POST DATA TO PASS BACK TO CONTROLLER
		var data = "&geopoint_id=" + geopoint_id + "&goalpoint_id=" + goalpoint_id; //use & to separate values
		
		var this_div = $("#geopoint_details");
		// AJAX!
		if(!(get_geopoint_data_ajax_call===undefined))
		{
			//alert('abort');
			get_geopoint_data_ajax_call.abort();
		}
		get_geopoint_data_ajax_call =$.ajax({
			url: "<?= base_url('index.php/loads/load_geopoint_details_for_mark_goalpoint_complete_dialog')?>", // in the quotation marks
			type: "POST",
			data: data,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					$("#upper_table").show();
					show_late_div();
					
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error!")
				}
			}
		});//END AJAX
		
		return false; 
	}
	
	function show_late_div()
	{
		//alert('in');
		var geopoint_datetime = $("#gp_complete_date").val()+" "+$("#gp_complete_time").val();
		var deadline = $("#goalpoint_deadline").val();
		
		$(".late_div").hide();
		
		//alert(deadline);
		if(deadline != "01/01/70 01:00" && (new Date(geopoint_datetime).getTime() > new Date(deadline).getTime()))
		{
			$(".late_div").show();
			$("#load_isLate").val("yes");
			//alert('is late');
		}
		else
		{
			$("#load_isLate").val("no");
			//alert('not late');
		}
	}
	
	
	function lumper_selected()
	{
		if($("#is_lumper").val() == "Yes")
		{
			$("#lumper_amount_row").show();
		}
		else
		{
			$("#lumper_amount_row").hide();
		}
	}
	
	
	
	
	geopoint_details_ajax(0,<?=$goalpoint["id"]?>);
</script>
<div style="width:860px; text-align:center; color:black; background:#dfdfdf; height:30px; padding-top:10px; margin:auto; margin-top:10px;">
	<?=$goalpoint["gp_type"]?>  <?=$goalpoint["arrival_departure"]?> | <?=$goalpoint["location_name"]?> | <?=$geocode["formatted_address"]?> | Deadline: <?=$deadline_text?>
	<input type="hidden" class="" id="goalpoint_deadline" value="<?=date("m/d/y H:i",strtotime($goalpoint["deadline"]))?>"/>
</div>
<div style="margin:10px;">
	<div style="width:200px; float:left; margin-right:50px;">
		<table>
			<tr>
				<td style="min-width:70px;">
					Geopoint
				</td>
				<td style="min-width:150px; text-align:right;">
					<?php echo form_dropdown("geopoint_dropdown",$geopoint_options,"Not Found","id='geopoint_dropdown' class='' style='width:148px; height:18px; font-size:12px;' onchange='get_geopoint_data()'");?>
				</td>
			</tr>
		</table>
		<table  id="upper_table" style="display:none;">
			<tr>
				<td style="min-width:70px;">
					Truck
				</td>
				<td style="text-align:right;">
					<?=$truck["truck_number"]?>
				</td>
			</tr>
			<tr>
				<td>
					Trailer
				</td>
				<td style="text-align:right;">
					<?=$trailer["trailer_number"]?>
				</td>
			</tr>
			<tr>
				<td>
					Driver
				</td>
				<td style="min-width:150px; text-align:right;">
					<?=$client["client_nickname"]?>
				</td>
			</tr>
			<tr>
				<td style="">
					Codriver
				</td>
				<td style="text-align:right;">
					<?php echo form_dropdown("temp_codriver_id",$clients_dropdown_options,"Select","id='temp_codriver_id'  class=''  style='width:148px; height:18px; font-size:12px;' onchange=''");?>
				</td>
			</tr>
		</table>
		<div id="geopoint_details">
			<!--AJAX GOES HERE !-->
		</div>
	</div>
	<div id="map" style="height:520px; width:600px; float:left;">
		<!--MAP GOES HERE !-->
	</div>
	<div style="clear:both;"></div>
</div>
