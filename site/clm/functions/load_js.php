<?php
function clm_function_load_js($js) {
		$path = clm_core::$path . DS . "js" . DS . $js . '.php';
		if(file_exists($path)) {
			require_once ($path);
		} else {
			clm_core::addWarning("js missing",$path." (".$js.")"." Backtrace: ".clm_core::getBacktrace());		
		}
}
?>
