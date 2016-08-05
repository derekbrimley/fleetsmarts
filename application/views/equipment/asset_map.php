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
		#map {
		  height: 100%;
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
	<div id="map"></div>
	<script>
		var json_geopoints=<?php echo $json_geopoints ?>;
		
		var image = '<?=base_url("images/navigation.png")?>';
		var center = {lat: 37.9173487, lng: -98.9634981};
		var map;
		var markers = [];
		function initMap() {
			map = new google.maps.Map(document.getElementById('map'), {
				center: center,
				zoom: 5,
			});
			
			drawMarkers();
			window.setInterval(function(){
				drawMarkers();
			}, 30000);
		}
		
		function drawMarkers(){
			
			$.ajax({
				url: "<?=base_url("index.php/equipment/get_asset_data") ?>",
				method: "POST",
				cache: false,
				statusCode: {
					200: function(json){
						
						removeMarkers();
						
						var parsed_json = $.parseJSON(json);
//						console.log(parsed_json);
						for(i=0; i<parsed_json.length; i++) {
							var obj = parsed_json[i];
							var latLng = {lat: Number(obj.lat), lng: Number(obj.long)};
							var heading = obj.heading;
							var power = obj.power;
							var speed = obj.speed;
							var odometer = Math.round(obj.odom);
							var truck_number = obj.truck_number;
							addMarker(latLng,heading,power,truck_number);
						}
					},
					404: function(){
						alert('Page not found');
					},
					500: function(response){
						alert("500 error! "+response);
					}
				}
			});//END AJAX
			
			function addMarker(location,heading,power,truck_number) {
				var path;
				var fillColor;
				
				if(power=="on"){
					path = google.maps.SymbolPath.FORWARD_CLOSED_ARROW;
					fillColor = '#4CAF50';
				}else if(power=="off"){
					path = google.maps.SymbolPath.CIRCLE;
					fillColor = '#F44336';
				}
				
				var marker = new MarkerWithLabel({
					position: location,
					map: map,
					icon: {
						scale: 5,
						rotation:Number(heading),
						fillOpacity: 1,
						strokeWeight: 1,
						path: path,
						fillColor: fillColor,
					},
					labelContent: '<div>'+truck_number+'</div>',
					labelAnchor: new google.maps.Point(-5, 25),
					labelClass: "labels", // the CSS class for the label
				});
				
				animateMarker(marker);
				
				markers.push(marker);
			}
			
			function removeMarkers(){
//				console.log("markers removed");
				setMapOnAll(null);
			}
			
			function setMapOnAll(map) {
			  for (var i = 0; i < markers.length; i++) {
				 markers[i].setMap(map);
			  }
			}
		}
		
		function animateMarker(marker) {
			window.setInterval(function() {
				count = (count + 1) % 200;

				var icons = marker.get('icons');
				icons[0].offset = (count / 2) + '%';
				marker.set('icons', icons);
			}, 2000);
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMJAxjDMr91aIbzuqhdeJ8tnmnx6MqTb4&callback=initMap"></script>
	<script type="text/javascript" src="<?= base_url("js/marker_with_label.js") ?>"></script>
</body>
</html>