<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2018 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Aufbau eines xls Links im CLM
function clm_function_create_link_xls($view, $title, $params = array()) {
	
		// open div
		$string = '<div class="pdf">';
		
		// imageTag zusammensetzen
		$imageTag = '<img src="'.clm_core::$load->gen_image_url("table/doc_excel_csv").'" width="16" height="19" title="'.$title.'" alt="XLS" class="CLMTooltip" />';
 
		// Format ergänzen
		$params['format'] = 'xls';
		
		$string .= clm_core::$load->create_link($imageTag, $view, $params);
		
		// close div
		$string .= '</div>';
	
		return $string;
	
	}
?>
