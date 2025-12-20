<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Input: Bedenkzeit Index oder Textstring
// Output: Textstring
function clm_api_db_time_control($time_control) {

	if (!clm_core::$load->is_whole_number($time_control)) {
		return $time_control;
	}
	
	$query = 'SELECT name FROM #__clm_zeitmodus WHERE published = 1 AND id = '.$time_control;
	$tc = clm_core::$db->loadObject($query);

	if (isset($tc->name) AND !is_null($tc->name)) return $tc->name;
	return '';
}
?>
