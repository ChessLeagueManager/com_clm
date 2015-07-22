<?php
function clm_function_execute_view() {
	$out = clm_core::$api->callStandalone($_GET["view"]);
	if($out[0]) {
		$body = $out[2];	
	} else {
		$body = clm_core::$load->load_view("notification",array($out[1]));
		$body = '<div class="clm">'.$body[1].'</div>';
	}
	// View stellt Datei zum Download bereit?
	if($out[0] && isset($out[3])) {
		for($i=0;$i<count($out[3]);$i++) {
			header($out[3][$i]);
		}
		$fix[1]=$body;
	} else {
		$title = clm_core::$cms->getTitle();
		$head = clm_core::$cms->getStyleScriptHead();
		$fix = clm_core::$load->load_view("html",array($title,$head,$body),false);
	}
	return $fix[1];
}
?>
