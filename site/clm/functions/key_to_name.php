<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_function_key_to_name($detail,$key,$group=true) {
		if($detail != 'tiebreak' AND $detail != 'color_order') {
			return "";		
		}		
		if (!clm_core::$load->is_whole_number($key)) {
			return "";
		}
		$key = intval($key);
		
		if($group) {
			$lang = "tournament_group";		
		} else {
			$lang = "tournament";		
		}
		
		$lang = clm_core::$lang->$lang;
		$text_key = $detail.'_'.$key;
		return $lang->$text_key;
}
?>