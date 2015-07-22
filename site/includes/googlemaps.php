<?php
if(!isset($google_address)) {
	$google_address = "";
} else {
	$google_address = preg_replace('~[\r\n]+~', '', $google_address);
}
?>
<script type="text/javascript">
   
   var address ="<?php echo $google_address; ?>";
   var map = null;
   var geocoder = null;

     function load() {
       geocoder = new GClientGeocoder();
       if (GBrowserIsCompatible()) {
         map = new GMap2(document.getElementById("map"));
         if (geocoder) {
         geocoder.getLatLng(
           address,
           function(point) {
             if (!point) {
               alert(address + " not found");
             } else {
               map.setCenter(point, 14);
               map.addControl(new GLargeMapControl());
               var marker = new GMarker(point);
               map.addOverlay(marker);
               marker.openInfoWindowHtml(address);
             }
           }
         );
       }
       
       }
     }
  
</script>
