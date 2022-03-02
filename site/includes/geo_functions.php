<!--  Leaflet map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
	
<script type="text/javascript">
     function createLeafletMap(Lat, Lon, popupText) {
        // Create Map
        var map = L.map('mapdiv1').setView([Lat, Lon], 12);

        // Set Layer
        var tileLayer = new L.TileLayer('https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png',{
        attribution: '<a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">© OpenStreetMap contributors</a>',
        noWrap: false});

        // Add Control Scale to Map
        L.control.scale({metric:true,imperial:false}).addTo(map);

        // Set Map View
        map.addLayer(tileLayer);

        // Set Marker
        var marker = L.marker([Lat, Lon]).addTo(map);

        // Add Popup with Address
        marker.bindPopup(popupText).openPopup();
     }  
</script>

<!--  Reverse Geo Lookup -->
<?php
function getCoordinates($address)
{
    $config 			= clm_core::$db->config();

    // Encode the address
    $address = urlencode($address);
    
    // Read service provider from Config
    $service = $config->maps_resolver;

    if ($service == 1){ //Google
        // Get API Key from Config
        $gAPIKey = $config->googlemaps_api;
        // Check if not null
        if (is_null($gAPIKey)==false)
        {
            $coordinates = getCoordinatesFromGoogle($address, $gAPIKey );
            return $coordinates;
        }
        // If no API-Key is provided OSM search will be executed outside if-statement
    }
    $coordinates = getCoordinatesFromOSM($address);
    return $coordinates;
}
?>

<!-- OSM/Nominatim -->
<?php
function getCoordinatesFromOSM($address)
{


    // Set User-Agent as nomantim usage policy requests it
    $options = array(
        'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" .
                    "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36\r\n"
        ));

    
    $userAgent = stream_context_create($options);     

    $url = "https://nominatim.openstreetmap.org/?format=json&addressdetails=1&q={$address}&format=json&limit=1";
        
    // Call Url
    $resp_json = file_get_contents($url, false, $userAgent);

    // Decode answer
    $resp = json_decode($resp_json, true);
    if (is_null($resp)==true){//Check if search was successful
        $coordinates = array(0,0); //Return 0,0 in case search was not successfull
    }
    else
    {
        $coordinates =  array($resp[0]['lat'], $resp[0]['lon']);
    }
    return $coordinates;
}
?>
<!-- Google Maps -->
<?php
function getCoordinatesFromGoogle($address, $gAPIKey )
{
    //Build URL
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$gAPIKey}";

    // Call Url
    $resp_json = file_get_contents($url, false);

    // Decode answer
    $resp = json_decode($resp_json, true);

    // Check if status ok
    if ($resp['status']=='OK'){
        $coordinates =  array($resp['results'][0]['geometry']['location']['lat'],
                            $resp['results'][0]['geometry']['location']['lng']);
    }
    else
    {
        $coordinates = array(0,0);
    }

    return $coordinates;
}
?>

