<!DOCTYPE html>
<html>
<head>
	<title>Pomoc</title>

	<!-- Connect do MySQL database -->
	<?php
		// Set credentials to login to database
		$user = 'root';
		$password = 'root';
		$database = 'pomoc';
		$host = 'localhost';
		$port = 8889;

		// Connect to database
		$db = new mysqli("$host:$port", $user, $password, $database);

		if ($db->connect_error) {
			die('Nepodařilo se připojit k MySQL serveru (' . $db->connect_errno . ') '
					. $db->connect_error);
		}

		// echo 'Připojení proběhlo úspěšně ' . $db->host_info . '<br>';
	?>

	<!-- Define functions for interactions with Map and Panorama elements -->
	<script src="http://maps.googleapis.com/maps/api/js"></script>
	<script>
		// Set inital paramaeters to Brno
		var initLat = 49.2020701; // Initial latitude [deg]
		var initLon = 16.5779606; // Initial longitude [deg]
		var initZoom = 11; // Initial zoom

		// Function for showing all locations from database
		function allLocs() {

			// Initial position of map (Brno, CZ)
			var initLat = 49.2020701;
			var initLon = 16.5779606;
			var initZoom = 11;

			// Center of map
			var myCenter = new google.maps.LatLng(initLat, initLon);

			// Map properties
			var mapProp = {
				center: myCenter,
				zoom: initZoom,
				mapTypeId: google.maps.MapTypeId.ROADMAP
				};

			// Map
			var map = new google.maps.Map(document.getElementById("map"), mapProp);

			<?php
				// Get all entries from database
				$results = $db->query("SELECT * FROM `entries`");

				// Go through all the entries and create mark on map of them
				while ($entry = $results->fetch_assoc())
				{
					// Marker
					echo 'var myCenter = new google.maps.LatLng(' . $entry['latitude'] . ', ' . $entry['longitude'] . ');'; // Center of map
					echo 'var marker = new google.maps.Marker({'; // Create marker
					echo '	position: myCenter'; // Set position of marker
					echo '	});';
					echo 'marker.setMap(map);'; // Add marker to map


				}

				// Free results from database
				$results->free_result();
			?>

			// Enlarge Map element
			document.getElementById("map").style.height = "100%";

			// Reduce Panorama element and hide it
			document.getElementById("pano").style.height = "0%";
			document.getElementById("pano").style.visibility = "collapse";
		}

		// Function for showing single chosen location
		function singleLoc(myLat, myLon, myAccuracy, myTelephoneNumber, myTime) {

			// Center of map
			var myCenter = new google.maps.LatLng(myLat, myLon); // Center of map

			// Map properties
			var mapProp = {
				center: myCenter,
				zoom: 15,
				mapTypeId: google.maps.MapTypeId.ROADMAP
				};

			// Map
			var map = new google.maps.Map(document.getElementById('map'),mapProp);

			// Panorama properties
			var panoProp = {
				position: myCenter,
				zoom: 1
				};

			// Panorama
			var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoProp);
			map.setStreetView(panorama);

			// Marker
			var marker = new google.maps.Marker({ // Create marker
				position: myCenter // Set position of marker
				});
			marker.setMap(map); // Add marker to map

			// Circle of accuracy
			var radius = new google.maps.Circle({ // Create circle of accuracy
				center: myCenter, // Set center of circle
				radius: myAccuracy, // Set radius of 
				strokeColor: "#0000FF", // Set color of stroke
				strokeOpacity: 0.8,	// Set opacity of stroke
				strokeWeight: 1, // Set width of stroke
				fillColor: "#0000FF", // Set color of fill
				fillOpacity: 0.4 // Set opacity of fill
				});
			radius.setMap(map); // Add circle to map

			// Window with information
			var infowindow = new google.maps.InfoWindow({
				content: myTelephoneNumber + '<br>' +
					myTime + '<br>' +
					myLat + ', ' + myLon + '<br>' +
					myAccuracy + ' m'
				});
			infowindow.open(map,marker);

			// Reduce Map element
			document.getElementById("map").style.height = "70%";

			// Enlarge Panorama element and make it visible
			document.getElementById("pano").style.height = "30%";
			document.getElementById("pano").style.visibility = "visible";
		}

		google.maps.event.addDomListener(window, 'load', allLocs);
	</script>
</head>
<body>

<table style="height:100%;width:100%; position: absolute; top: 0; bottom: 0; left: 0; right: 0">
	<tr style="height:100%">
		<td style="width:70%">
			<!-- Map element -->
			<div id="map" style="width: 100%; height: 70%"></div>
			<!-- Panorama element -->
			<div id="pano" style="width: 100%; height: 30%"></div>
		</td>
		<td style="width:30%" valign="top">
			<table>
				<tr>
					<td>
						<a href="javascript:void(0)" onclick="allLocs()">Všechny pozice</a>
					</td>
				</tr>
				<!-- Create table of entries -->
				<?php
					// Get all entries from database
					$results = $db->query("SELECT * FROM `entries`");

					// echo 'Z databáze jsme získali ' . $results->num_rows . ' záznamů.';

					// Go through all the entries and create table of those entries with links to Map element
					while ($entry = $results->fetch_assoc())
					{
						echo '<tr>';
						echo '<td>';
						echo '<a href="javascript:void(0)" onclick="singleLoc(' . 
							$entry['latitude'] . ', ' . 
							$entry['longitude']  . ', ' . 
							$entry['accuracy'] . ', ' . 
							"'" . $entry['telephoneNumber'] . "'" .', ' . 
							"'" . $entry['gpsTime'] . "'" .');">' . 
							$entry['gpsTime'] .  ', ' . $entry['telephoneNumber'] . '</a>';
						echo '</td>';
						echo '</tr>';
					}

					// Free results from database
					$results->free_result();

					// Close the database
					//$db->close();
				?>
			</table>
  		</td>
	</tr>
</table>

</body>
</html>