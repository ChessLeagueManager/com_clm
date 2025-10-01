<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
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
	if ($return_section == 'users') {
		$usersModel = " SELECT a.* "
		." FROM #__clm_user as a "
		." WHERE (FIND_IN_SET(a.id, '".$cids."') != 0)"
		;
		$out["users"] = clm_core::$db->loadObjectList($usersModel);
	}

	// Mail-Adressen der Mannschaftsleiter und des Staffelleiters
	if ($return_section == 'mturniere' OR $return_section == 'ligen') {
		$lid = $out["input"]["cids"];
		$out["input"]["lid"]=$cids;
		// Daten der Liga einschl. MF holen
		$ligaModel	= "SELECT l.*, u.name as slname, u.email as slmail FROM #__clm_liga as l"
			." LEFT JOIN #__clm_user as u ON u.jid = l.sl AND u.sid = l.sid"
			." WHERE l.id = ".$lid
			;
		$out["liga"] = clm_core::$db->loadObjectList($ligaModel);
		// Daten per Mannscchftsleiter holen
		$teamModel	= "SELECT m.*, u.name as mfname, u.email as mfmail FROM #__clm_mannschaften as m"
			." LEFT JOIN #__clm_user as u ON u.jid = m.mf AND u.sid = m.sid"
			." WHERE m.liga = ".$lid
			." AND m.mf > 0"
			;
		$out["teams"] = clm_core::$db->loadObjectList($teamModel);
	}

	return array(true, "m_mailSuccess", $out);
}
?>
