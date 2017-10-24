<?php
// Aufbau eines pdf Links im CLM
function clm_function_create_link_pdf($view, $title, $params = array()) {
	
		// open div
		$string = '<div class="pdf">';
		
		// imageTag zusammensetzen
		//$imageTag = '<img src="'.CLMImage::imageURL('pdf_button.png').'" width="16" height="19" title="'.$title.'" alt="PDF" class="CLMTooltip" />';
		$imageTag = '<img src="'.clm_core::$load->gen_image_url("table/pdf_button").'" width="16" height="19" title="'.$title.'" alt="PDF" class="CLMTooltip" />';
 
		// Format ergänzen
		$params['format'] = 'pdf';
		
		$string .= clm_core::$load->create_link($imageTag, $view, $params);
		
		// close div
		$string .= '</div>';
	
		return $string;
	
	}
?>
