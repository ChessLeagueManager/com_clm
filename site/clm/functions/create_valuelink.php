<?php
// Aufbau eines CLM Links
function clm_function_create_valuelink($view, $params = array()) {
	
		$html = '?view='.$view;
		
		// Params?
		if (count($params) > 0) {
			foreach ($params as $key => $value) {
				$html .= '&amp;'.$key.'='.$value;
			}
		}
	
		return $html;
	
	}
?>
