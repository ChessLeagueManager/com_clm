<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_view_terminliste_xls($cid = array()) {

	$out = clm_core::$api->db_terminliste($cid);

	if (!$out[0]) {
		if($out[1]=="e_terminlisteError") {
			$out = clm_core::$api->db_report_overview();
			$fix = clm_core::$load->load_view("report_overview", array($out[2]));
			return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
		} else if(count($out)==3) {
			return array(false, $out[1], $out[2]);
		} else {
			return array(false, $out[1]);
		}
	}
	$fix = clm_core::$load->load_view("terminliste_xls", array($out[2]));
	return;
}
?>
