<?php
function clm_function_soap_wrapper($url) {
	// Deaktiviere Zertifikatskontrolle für SOAP falls gewünscht
	if(clm_core::$db->config()->soap_safe) {
		return new SoapClient($url);
	} else {
		$context = stream_context_create(array("ssl"=>array('verify_peer'=>false,'verify_peer_name'  => false)));
		return new SoapClient($url,array('trace' => 1,"stream_context" => $context));
	}
}
?>
