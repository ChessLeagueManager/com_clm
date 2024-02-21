<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Eingang: Termin-Id
function clm_api_db_termine($id) {
	$id = clm_core::$load->make_valid($id, 0, -1);

	$out["input"]["id"]=$id;

	$config		= clm_core::$db->config();
	$countryversion = $config->countryversion;

 
  	$terminModel = " SELECT a.* "
		." FROM #__clm_termine as a "
		." WHERE a.id = ".$id 
		;
	$out["termine"] = clm_core::$db->loadObjectList($terminModel);


	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["termine"][0])) {
		return array(false, "e_termineError");
	}	
 
	return array(true, "m_termineSuccess", $out);
}
?>
