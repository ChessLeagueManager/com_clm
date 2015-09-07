<?php
// Generiert einen Link zur gleichen Datei mit veränderten GET Variablen, 
// dies ermöglicht Links innerhalb eines Views bei Verwendung unter verschiedenen Views
function clm_function_gen_url($args=array(),$delete=array()) {
	$out = $_GET;
	foreach ($args as $key => $val) {
		$out[$key] = $val;
	}
	$url = "index.php?";
	foreach ($out as $key => $val) {
		$deleteIt = false;
		for($i=0;$i<count($delete);$i++) {
			if($delete[$i]==$key) {
				$deleteIt = true;
				break;			
			}
		}
		if($deleteIt) {
			continue;
		}
		$url.= urlencode($key) . "=" . urlencode($val) . "&";
	}
	return substr($url, 0, -1);
}
?>
