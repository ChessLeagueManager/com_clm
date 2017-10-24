<?php
// Aufbau eines CLM Links
function clm_function_create_link($string, $view, $params = array()) {
	
		$html = '<a href="index.php?option=com_clm&amp;view='.$view;
		
		// Params?
		if (count($params) > 0) {
			foreach ($params as $key => $value) {
				$html .= '&amp;'.$key.'='.$value;
			}
		}
		$html .= '">';
		$html .= $string;
		$html .= '</a>';
	
		return $html;
	
	}
?>
