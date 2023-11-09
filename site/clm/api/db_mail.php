<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_mail($return_section,$return_view,$cids) {

	$out["input"]["return_section"]=$return_section;
	$out["input"]["return_view"]=$return_view;
	$out["input"]["cids"]=$cids;

	// Einstellungen holen
	$config		= clm_core::$db->config();
	$meldung_verein	= $config->meldung_verein;
	$meldung_heim	= $config->meldung_heim;
	$countryversion = $config->countryversion;

	$jid = clm_core::$access->getJid();
	$id = clm_core::$access->getId();
	
	// Ist der Benutzer im CLM eingeloggt
	if ($id==-1) {
		return array(false, "e_mailLogin");
	} 
 
	$utype = clm_core::$access->getType();

	// Ist der Benutzer 'nur' Spieler ohne Rechte
	if ($utype == 'spl') {
		return array(false, "e_mailRight");
	} 
	// Mail-Adresse des aktuellen Benutzers
	$auserModel = " SELECT a.* "
		." FROM #__clm_user as a "
		." WHERE a.id = ".$id
		;
	$out["auser"] = clm_core::$db->loadObjectList($auserModel);
 
	// Mail-Adressen der ausgewählten Benutzer
	if ($return_section = 'users') {
		$usersModel = " SELECT a.* "
		." FROM #__clm_user as a "
		." WHERE (FIND_IN_SET(a.id, '".$cids."') != 0)"
		;
		$out["users"] = clm_core::$db->loadObjectList($usersModel);
	}
 
	return array(true, "m_mailSuccess", $out);
}
?>
