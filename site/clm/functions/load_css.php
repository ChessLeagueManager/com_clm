<?php
function clm_function_load_css($css) {
		$path = clm_core::$path . DS . "css" . DS . $css . '.php';
		if(file_exists($path)) {
			require_once ($path);
		} else {
			clm_core::addWarning("css missing",$path." (".$css.")"." Backtrace: ".clm_core::getBacktrace());		
		}
}
?>
