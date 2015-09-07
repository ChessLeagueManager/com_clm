<?php
function clm_function_load_view($view, $args,$div = true) {
	clm_core::$load->load_css("reset");
	if (!function_exists("clm_view_" . $view)) {
		$path = clm_core::$path . DS . "views" . DS . $view . '.php';
		if(file_exists($path)) {
			require_once ($path);
		} else {
			clm_core::addError("view missing",$path." (".$view.")"." Backtrace: ".clm_core::getBacktrace());		
		}
	}
	ob_start();
	$out[0] = call_user_func_array("clm_view_" . $view, $args);
	if($div) {
		$out[1] = '<div class="clm_view_'.$view.'">'.ob_get_contents()."</div>";
	} else {
		$out[1] = ob_get_contents();
	}
 	ob_end_clean();
 	return $out;
}
?>
