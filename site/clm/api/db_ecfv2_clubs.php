<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Eingang: Verband
// Ausgang: Alle Vereine in diesem
function clm_api_db_ecfv2_clubs($verband) {
	$verband = clm_core::$load->make_valid($verband, 8, "0000");
	$config = clm_core::$db->config();
	// Vereine aus der CLM Vereinstabelle holen
	$sql = " SELECT * FROM #__clm_dwz_vereine " . " WHERE Verband = '".$verband."' AND sid = " . clm_core::$access->getSeason(). ' ORDER BY ';
	if ($config->verein_sort =="1") { 
		$sql = $sql."ZPS ASC";
	} else {
		$sql = $sql."Vereinname ASC";
	}
	$vereine = clm_core::$db->loadObjectList($sql);
	return array(true, (count($vereine) == 0 ? 'w_noAssociationFound' : ''), $vereine);
}
?>
