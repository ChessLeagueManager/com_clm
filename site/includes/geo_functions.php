<!--  Leaflet map -->
	
<script type="text/javascript">
     function createLeafletMap(Lat, Lon, popupText, zoom) {
        // Create Map
        var map = L.map('mapdiv1').setView([Lat, Lon], zoom);

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

<!--  OSM map -->
	
<script type="text/javascript">
     function createOSMap(Lat, Lon, markerLink, zoom) {
        var map = new ol.Map({
				layers: [new ol.layer.Tile({ source: new ol.source.OSM() })],
				target: document.getElementById('mapdiv1'),
				view: new ol.View({
					center: ol.proj.fromLonLat([Lon, Lat]),
					zoom: zoom
					})
				});

			// Adding a marker on the map
			var marker = new ol.Feature({
				geometry: new ol.geom.Point(
				ol.proj.fromLonLat([Lon,Lat])
				),  
			});
			
			marker.setStyle(new ol.style.Style({
				image: new ol.style.Icon(({
					crossOrigin: 'anonymous',
					anchor: [0.5, 1],
					anchorXUnits: 'fraction',
					anchorYUnits: 'fraction',
					src: markerLink,
				}))
			}));
			var vectorSource = new ol.source.Vector({
				features: [marker]
			});
			var markerVectorLayer = new ol.layer.Vector({
				source: vectorSource,
			});
			map.addLayer(markerVectorLayer);
     }  
</script>

<!--- Prepare Address -->
<?php
function prepareAddress($address)
{
    // Depending on User settings on parts of the address are used in the map to e.g. filter out names of buildings
    $config 			= clm_core::$db->config();
    $googlemaps_rtype   = $config->googlemaps_rtype;

    //Users is able to choose from three option
    $addressArray = explode(',', $address);

    if (isset($addressArray[2]) AND $googlemaps_rtype == 1) {  //term1, term2 and term3
        $address = implode(",", array($addressArray[0], $addressArray[1], $addressArray[2])); }
    elseif (isset($addressArray[2]) AND $googlemaps_rtype == 2) {  //only term2 and term3
        $address = implode(",", array($addressArray[1], $addressArray[2])); }
    elseif (isset($addressArray[1]) AND $googlemaps_rtype == 3) { //only term1 and term2
        $address =  implode(",", array($addressArray[0], $addressArray[1])); }
    else {
        $address = implode(",", $addressArray); //Default, use whole field
    }
  
    // Encode the address
    $address = urlencode($address);
    return ($address);
}
?>
<!--  Reverse Geo Lookup -->
<?php
function getCoordinates($address)
{
    // Prepare address (encoding for URL, separating of terms)
    $address = prepareAddress($address);

    
    // Read service provider from Config
    $config 			= clm_core::$db->config();
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
