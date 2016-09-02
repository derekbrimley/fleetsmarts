<!DOCTYPE html>
<html>
<head>
	<link href='https://fonts.googleapis.com/css?family=Titillium+Web' rel='stylesheet' type='text/css'>
	<style>
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}
		.map {
		}
		.labels{
			background: #fff;
			color: #212121;
			font-family: 'Titillium Web', sans-serif;
			font-size: 14px;
			border:1px solid #646464;
			border-radius: 4px;
			font-weight: bold;
		}
	</style>
	<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
	
	
</head>
<body>
	<div style="height:60%;" id="map_<?=$log_entry_id?>"></div>
	<script>
		var map;
		var markers = [];
		var log_entry_id = <?=$log_entry_id?>;
		function initMap() 
		{
			var directionsDisplay = new google.maps.DirectionsRenderer;;
			var directionsService = new google.maps.DirectionsService();
			map = new google.maps.Map(document.getElementById('map_'+<?=$log_entry_id?>), {
				zoom: 5,
				scrollwheel: false
			});
			directionsDisplay.setMap(map);
			
			calcRoute(directionsService,directionsDisplay,log_entry_id);
			
			drawMapMarkers(log_entry_id);
			
		}
		
		function drawMapMarkers(log_entry_id)
		{
			var dataString = {log_entry_id: log_entry_id};
			console.log("Datastring: " + dataString.log_entry_id);
			$.ajax({
				url: "<?=base_url('index.php/logs/get_end_leg_data') ?>",
				method: "POST",
				data: dataString,
				cache: false,
				statusCode: {
					200: function(json){
						console.log("json: " + json);
						
						var parsed_json = $.parseJSON(json);
						for(i=0; i<parsed_json.length; i++) {
							var obj = parsed_json[i];
						  	console.log("gps: " + obj.gps_coordinates);
						  	if(obj.gps_coordinates != null)
						  	{
							  	var gps = obj.gps_coordinates;
							  	var type = obj.entry_type;
								var gps_array = gps.split(",");
								var latLng = {lat: Number(gps_array[0]), lng: Number(gps_array[1])};

								addMarker(latLng,type);
						 	}
						}
						calcRoute(directionsService,directionsDisplay,json);
					},
					404: function(){
						alert('Page not found');
					},
					500: function(response){
						alert("500 error! "+response);
					}
				}
			});//END AJAX
			
			function addMarker(location,type) {
				
				if(type=='End Leg')
				{
					var image = new google.maps.MarkerImage(
						"<?=base_url('images/log_end_leg.png')?>",
						null, /* size is determined at runtime */
						null, /* origin is 0,0 */
						null, /* anchor is bottom center of the scaled image */
						new google.maps.Size(18, 28)
					);
				}
				else if(type=='Drop')
				{
					var image = new google.maps.MarkerImage(
						"<?=base_url('images/pick_trailer.png')?>",
						null, /* size is determined at runtime */
						null, /* origin is 0,0 */
						null, /* anchor is bottom center of the scaled image */
						new google.maps.Size(32, 28)
					);
				}
				
				var marker = new MarkerWithLabel({
					position: location,
					map: map,
					icon: image
				});
				
				markers.push(marker);
				
				var currCenter = map.getCenter();
				
				google.maps.event.trigger(map, 'resize');
				map.setCenter(location);
			}
			
			
		
//			function removeMarkers(){
////				console.log("markers removed");
//				setMapOnAll(null);
//			}
//			
//			function setMapOnAll(map) {
//			  for (var i = 0; i < markers.length; i++) {
//				 markers[i].setMap(map);
//			  }
//			}
		}
		
		function calcRoute(directionsService,directionsDisplay,log_entry_id)
		{
			var dataString = {log_entry_id: log_entry_id};
			$.ajax({
				url: "<?=base_url('index.php/logs/get_start_end') ?>",
				method: "POST",
				data: dataString,
				cache: false,
				statusCode: {
					200: function(route){
						console.log("Route: " + route);
						var start = route.start;
						console.log("Start: " + start);
						//var end = new google.maps.LatLng(38.334818, -181.884886);
						var end = route.end;
						console.log("end: " + end);
						directionsService.route({
							origin: start,
							destination: end,
							travelMode: 'DRIVING'
						}, function(response, status){
							if(status === 'OK'){
								directionsDisplay.setDirections(response);
							} else {
								window.alert('Directions request failed due to ' + status);
							}
						});
					},
					404: function(){
						alert('Page not found');
					},
					500: function(response){
						alert("500 error! "+response);
					}
				}
			});//END AJAX
//			console.log("json: " + json);
//			var parsed_json = $.parseJSON(json);
//			console.log("parsed json: " + parsed_json);
//			var coordinates_array = [];
//			for(i=0; i<parsed_json.length; i++) {
//				var obj = parsed_json[i];
//				console.log("type: " + obj.entry_type);
//				console.log("coord: " + obj.gps_coordinates);
//				if(obj.entry_type == 'End Leg')
//				{
//					var gps = obj.gps_coordinates;
//					var gps_array = gps.split(",");
//					console.log("lat: " + gps_array[1]);
//					var latLng = new google.maps.LatLng(Number(gps_array[0]),Number(gps_array[1]));
//					console.log("latlng: " + latLng);
//					coordinates_array.push(latLng);
//					console.log("Coordinates array: " + coordinates_array);
//				}
//			}
//			console.log("Coordinates array: " + coordinates_array);
			
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMJAxjDMr91aIbzuqhdeJ8tnmnx6MqTb4&callback=initMap" async defer></script>
	<script type="text/javascript" src="<?= base_url("js/marker_with_label.js") ?>"></script>
</body>
</html>