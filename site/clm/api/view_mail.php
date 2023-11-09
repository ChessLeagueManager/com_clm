<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_view_mail($return_section,$return_view,$cids) {

	$out = clm_core::$api->db_mail($return_section,$return_view,$cids);
	if (!$out[0]) {
		if($out[1]=="e_mailError") {
			$out = clm_core::$api->db_mail_overview();
			$fix = clm_core::$load->load_view("mail_overview", array($out[2]));
			return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
		} else if(count($out)==3) {
			return array(false, $out[1], $out[2]);
		} else {
			return array(false, $out[1]);
		}
	}
	$fix = clm_core::$load->load_view("mail", array($out[2]));
	return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
}
?>
