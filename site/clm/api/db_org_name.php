<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Input: Länge des Schlüssels und Schlüssel(ZPS) selbst
// Output: 	1 - Landesverband aus dwz_verbaende
//			2 - Schachverband aus dwz_verbaende
//			3 - Schachbezirk aus dwz_verbaende

function clm_api_db_org_name($type,$zps) {

	$type = clm_core::$load->make_valid($type, 0, -1);
	if ($type != 1 AND $type != 2 AND $type != 3 ) {
		return '';
	}
	$zps = clm_core::$load->make_valid($zps, 8, "");
	if ($zps == '') {
		return '';
	}
	
	if ($type == 1) $key = substr($zps,0,1).'00';
	elseif ($type == 2) $key = substr($zps,0,2).'0';
	elseif ($type == 3) $key = substr($zps,0,3);
	$query = "SELECT * FROM #__clm_dwz_verbaende "
			." WHERE Verband = '".$key."'";
	$name = clm_core::$db->loadObject($query);

	if (isset($name->Verbandname) AND !is_null($name->Verbandname)) return $name->Verbandname;
	return '';
	
}
?>
