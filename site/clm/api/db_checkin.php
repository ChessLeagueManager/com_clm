<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Input: Tabellenname, Record-ID
// Output: Freigeben des Datenbanksatzes 

function clm_api_db_checkin($tabname,$id) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	$tabname = clm_core::$load->make_valid($tabname, 8, '');

	$query	= "UPDATE #__clm_".$tabname
		." SET checked_out = NULL"
		." , checked_out_time = NULL"
		." WHERE id = ".$id
		;
	
	if (clm_core::$db->query($query))
		return array(true,'m_checkin');
	else
		return array(false,'e_checkin', $query);	
}
?>
