<?php
	
	function random_color()
	{
    		mt_srand((double)microtime()*1000000);
    		$c = '';
    		
		while(strlen($c) < 6) $c .= sprintf("%02X", mt_rand(0, 255));
    		
		return $c;
	}
    	
	$trackColor = "#000000";
	$randomColors = false;

	if(isset($_GET["color"]))
	{
		switch($_GET["color"])
		{
			case 'black': 	$trackColor = "#000000"; break;
			case 'red': 	$trackColor = "#FF0000"; break;
			case 'yellow': 	$trackColor = "#FFFF00"; break;
			case 'green': 	$trackColor = "#00FF00"; break;
			case 'random':  
				$trackColor = "random";	 
				$randomColors = true;
			break;
		}
	}					

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	
<html>

<head>
  <title>Bike Ride Tracking</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="distribution" content="global">
	<meta name="robots" content="none">

	<meta name="language" content="en">

	<meta name="description" content="">
    
    <link rel="stylesheet" href="css/main.css" type="text/css" media="screen">
	
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=YOUR_GOOGLE_MAPS_API_KEY" type="text/javascript"></script>
	<script type="text/javascript">
    //<![CDATA[

	function load() 
	{
    	if (GBrowserIsCompatible()) 
    	{        	
        	var map = new GMap2(document.getElementById("map"));
        	map.addControl(new GLargeMapControl());
			map.addControl(new GMapTypeControl ());
			map.setCenter(new GLatLng(43.648795,-79.403687), 14); // Defaults to downtown Toronto Ontario.
			map.setMapType(G_HYBRID_MAP);
			map.enableRotation();
			
			<?php
			
			$myDirectory = opendir("./gpx");
			while($entryName = readdir($myDirectory)) $dirArray[] = $entryName;
			closedir($myDirectory);
			$indexCount	= count($dirArray);
			
			for($index = 0; $index < $indexCount; $index++) 
			{
				if($randomColors) $trackColor = "#" . random_color();	

				$filename = $dirArray[$index];
				
				if($filename != ".." && $filename != ".") 
				{
					echo '
						GDownloadUrl("gpx/' . $filename . '", function(data, responseCode) 
						{
							var xml = GXml.parse(data);
  							var markers = xml.documentElement.getElementsByTagName("trkpt");
  							var data = new Array();
  							
  							for (var i = 0; i < markers.length; i++) 
  							{
  								var lat = parseFloat(markers[i].getAttribute("lat"));
  								var lon = parseFloat(markers[i].getAttribute("lon"))
  								
  								if (lat != null && lon != null) 
  								{
    								var point = new GLatLng(lat, lon);
    								data[i] = point;
    							}
  							}
  							
							var polyline = new GPolyline(data, "' . $trackColor . '", 5);
							map.addOverlay(polyline);
  						});
					';
				}     
			}
			
			?>
  			
    	}
    	
    	$("#toolbar").draggable();
	}
	
    //]]>
    </script>
</head>

<body onload="load()" onunload="GUnload()">

	<div id="toolbar">
		<h3>Bike Ride Tracking</h3>
		<p>Bike Trips Tracked: <?php echo $indexCount - 2; ?></p>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td>
					Track Colour:&nbsp;&nbsp;
				</td>
				<td>
					<form name="colorForm" action="index.php" method="GET">
						<div align="center">
							<select name="color" onchange="this.form.submit();">
								<option value="">Select a Colour</option>
								<option value="black">Black</option>
								<option value="red">Red</option>
								<option value="yellow">Yellow</option>
								<option value="green">Green</option>
								<option value="random">Random</option>
							</select>
						</div>
					</form>
				</td>
			</tr>
		</table>
	</div>
	
	<div id="map" style="width:100%; height:100%"></div>

</body>

</html>