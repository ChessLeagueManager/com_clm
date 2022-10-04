<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Aufbau eines CLM Links
function clm_function_create_link($string, $view, $params = array()) {
	
		$html = '<a href="index.php?option=com_clm&amp;view='.$view;
		$itemid 	= clm_core::$load->request_int('Itemid',0);
		
		// Params?
		if (count($params) > 0) {
			foreach ($params as $key => $value) {
				$html .= '&amp;'.$key.'='.$value;
			}
		}
		if ($itemid > 0) {
			$html .= '&amp;Itemid='.$itemid;
		}
		$html .= '">';
		$html .= $string;
		$html .= '</a>';
	
		return $html;
	
	}
?>
