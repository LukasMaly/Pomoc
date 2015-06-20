<!DOCTYPE html>
<html>
<head>
	<title>Pomoc</title>
	<script src="http://maps.googleapis.com/maps/api/js"></script>
	<script>
		var myLat = 49.1815367; // Latitude [deg]
		var myLon = 16.5612094; // Longitude [deg]
		var myAccuracy = 100; // Accuracy of GPS [m]
		var myTelephoneNumber = "+420 721 438 669"; // Caller's number
		var myTime = "26. června 2015, 17:46:00"; // Time of call/fix

		function initialize() {
			var myCenter = new google.maps.LatLng(myLat, myLon); // Center of map

			// Properties of the map
			var mapProp = {
				center: myCenter,
				zoom: 15,
				mapTypeId: google.maps.MapTypeId.ROADMAP
				};

			// Map
			var map = new google.maps.Map(document.getElementById("map"),mapProp);
		}

		function update(myLat, myLon, myAccuracy, myTelephoneNumber, myTime) {
			var myCenter = new google.maps.LatLng(myLat, myLon); // Center of map

			// Properties of the map
			var mapProp = {
				center: myCenter,
				zoom: 15,
				mapTypeId: google.maps.MapTypeId.ROADMAP
				};

			// Map
			var map = new google.maps.Map(document.getElementById("map"),mapProp);

			// Marker
			var marker = new google.maps.Marker({
				position: myCenter,
				});

			// Radius of accuracy
			var radius = new google.maps.Circle({
				center: myCenter,
				radius: myAccuracy,
				strokeColor: "#0000FF",
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: "#0000FF",
				fillOpacity: 0.4
				});

			// Window with information
			var infowindow = new google.maps.InfoWindow({
				content: myTelephoneNumber + '<br>' +
					myTime + '<br>' +
					myLat + ', ' + myLon + '<br>' +
					myAccuracy + ' m'
				});

			marker.setMap(map);
			radius.setMap(map);
			infowindow.open(map,marker);
		}

		google.maps.event.addDomListener(window, 'load', initialize); // LM: co toto dela?
	</script>
</head>
<body>

<h1>Pomoc</h1>

<table>
	<tr>
		<td>
			<table>
				<tr>
					<td>
						<div id="map" style="width:500px;height:380px;"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div id="streetview" style="width:500px;height:380px;"></div>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table>
				<?php
					$user = 'root';
					$password = 'root';
					$database = 'pomoc';
					$host = 'localhost';
					$port = 8889;

					$db = new mysqli("$host:$port", $user, $password, $database);

					if ($db->connect_error) {
						die('Nepodařilo se připojit k MySQL serveru (' . $db->connect_errno . ') '
								. $db->connect_error);
					}

					// echo 'Připojení proběhlo úspěšně ' . $db->host_info . '<br>';

					$results = $db->query("SELECT * FROM `entries`");

					// echo 'Z databáze jsme získali ' . $results->num_rows . ' záznamů.';

					while ($entry = $results->fetch_assoc())
					{			
						// <tr>
						// 	<td>
						// 		<a href="javascript:void(0)" onclick="update(49.1910483, 16.5939297, 100, '+420 721 438 669', '26. června 2015, 17:46:00');">49.1910483, 16.5939297</a>
						// 	</td>
						// </tr>
						echo '<tr>';
						echo '<td>';
						echo '<a href="javascript:void(0)" onclick="update(' . 
							$entry['latitude'] . ', ' . 
							$entry['longitude']  . ', ' . 
							$entry['accuracy'] . ', ' . 
							"'" . $entry['telephoneNumber'] . "'" .', ' . 
							"'" . $entry['gpsTime'] . "'" .');">' . 
							$entry['gpsTime'] .  ', ' . $entry['telephoneNumber'] . '</a>';
						echo '</td>';
						echo '</tr>';
					}

					$results->free_result();

					$db->close();
				?>
			</table>
  		</td>
	</tr>
</table>

</body>
</html>