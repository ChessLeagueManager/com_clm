<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2018 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Ausgang: Alle Verbände
function clm_api_view_units_form($name,$selected=null) {
	$out = clm_core::$api->db_units();
	$out = $out[2]; // array dereferencing fix php 5.3
	$value = array();
	$text = array ();
	for($i=0;$i<count($out);$i++) {
		$value[$i] = $out[$i]->Verband;
		$text[$i] = $out[$i]->Verbandname;
	}	
	$out_fs = clm_core::$load->load_view("form_select",array($name,$value,$text,$selected));
	$out_fs = $out_fs[1]; // array dereferencing fix php 5.3
	return array(true, (count($out) == 0 ? 'w_noAssociationList' : ''), $out_fs);
}
?>
