<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Input: Saison, Benutzer 
// Output: Liste der Vorkommen
function clm_api_db_user_check($sid,$user) {
	$sid = clm_core::$load->make_valid($sid, 0, -1);
	$user = clm_core::$load->make_valid($user, 0, -1);

	// bei falschen Parameter bzw. engl. Anwendung kein Check!!
	if ($sid == -1 OR $user == -1) return array(false, 'Kein Check möglich');
	
	$query = "SELECT * FROM  #__clm_user "
			." WHERE sid = ".$sid
			." AND id = ".$user
			;
	$result = clm_core::$db->loadObjectList($query);
	if (is_null($result) OR !isset($result[0]->jid))	return array(false, 'Kein Check möglich');
	$jid = $result[0]->jid;
	$fideid = $result[0]->fideid;

	$query = "SELECT * FROM  #__clm_liga "
			." WHERE sid = ".$sid
			." AND sl = ".$jid
			;
	$result = clm_core::$db->loadObjectList($query);
	if (!is_null($result) AND count($result) > 0)	return array(false, 'Benutzer ist Staffel-/Turnierleiter');
	
	$query = "SELECT * FROM  #__clm_mannschaften "
			." WHERE sid = ".$sid
			." AND mf = ".$jid
			;
	$result = clm_core::$db->loadObjectList($query);
	if (!is_null($result) AND count($result) > 0)	return array(false, 'Benutzer ist Mannschaftsleiter');

	$query = "SELECT * FROM  #__clm_turniere "
			." WHERE sid = ".$sid
			." AND tl = ".$jid
			;
	$result = clm_core::$db->loadObjectList($query);
	if (!is_null($result) AND count($result) > 0)	return array(false, 'Benutzer ist Turnierleiter');
	
	$query = "SELECT * FROM  #__clm_turniere "
			." WHERE sid = ".$sid
			." AND torg = ".$jid
			;
	$result = clm_core::$db->loadObjectList($query);
	if (!is_null($result) AND count($result) > 0)	return array(false, 'Benutzer ist Turnierorganisator');

/*	if ($fideid > 0) {
		$query = "SELECT a.*, l.id as lid, l.name as lname, l.sid FROM #__clm_arbiter_turnier as a "
				." LEFT JOIN #__clm_liga as l ON a.liga = l.id AND a.fideid = ".$fideid
				." WHERE a.fideid = ".$fideid
				." AND l.sid = ".$sid
				;
		$result = clm_core::$db->loadObjectList($query);
		if (!is_null($result) AND count($result) > 0)	return array(false, 'Benutzer ist Schiedsrichter im Teamwettbewerb');

		$query = "SELECT * FROM  #__clm_turniere as t "
				." LEFT JOIN #__clm_arbiter_turnier as a ON a.turnier = t.id AND a.fideid = ".$fideid
				." WHERE t.sid = ".$sid
				;
		$result = clm_core::$db->loadObjectList($query);
		if (!is_null($result) AND count($result) > 0)	return array(false, 'Benutzer ist Schiedsrichter in Einzelturnier');
	}
*/
	return array(true, 'Benutzer kann gelöscht werden');	
}
?>
