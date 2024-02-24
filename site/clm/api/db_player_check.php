<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Input: Saison, Vereinscode(ZPS), Mitgliedsnummer 
// Output: Liste der Vorkommen
function clm_api_db_player_check($sid,$zps,$mglnr) {
	$sid = clm_core::$load->make_valid($sid, 0, -1);
	$zps = clm_core::$load->make_valid($zps, 8, "");
	$mglnr = clm_core::$load->make_valid($mglnr, 0, -1);

	// bei falschen Parameter bzw. engl. Anwendung kein Check!!
	if ($sid == -1 OR strlen($zps) != 5 OR $mglnr == -1) return array(false, 'Kein Check möglich');
	
	$query = "SELECT * FROM  #__clm_rnd_spl "
			." WHERE sid = ".$sid
			." AND zps = ".$zps
			." AND spieler = ".$mglnr
			;
	$result = clm_core::$db->loadObjectList($query);
	if (!is_null($result) AND count($result) > 0)	return array(false, 'Ergebnisse in Teamwettbewerben liegen vor');
	
	$query = "SELECT * FROM  #__clm_rangliste_spieler "
			." WHERE sid = ".$sid
			." AND (ZPS = ".$zps." OR ZPSmgl = ".$zps.")"
			." AND Mgl_Nr = ".$mglnr
			;
	$result = clm_core::$db->loadObjectList($query);
	if (!is_null($result) AND count($result) > 0)	return array(false, 'Spieler wird in Rangfolge geführt');
	
	$query = "SELECT * FROM  #__clm_meldeliste_spieler "
			." WHERE sid = ".$sid
			." AND zps = ".$zps
			." AND mgl_nr = ".$mglnr
			;
	$result = clm_core::$db->loadObjectList($query);
	if (!is_null($result) AND count($result) > 0)	return array(false, 'Spieler wird in Meldeliste geführt');

	$query = "SELECT * FROM  #__clm_user "
			." WHERE sid = ".$sid
			." AND zps = ".$zps
			." AND mglnr = ".$mglnr
			;
	$result = clm_core::$db->loadObjectList($query);
	if (!is_null($result) AND count($result) > 0)	return array(false, 'Benutzer ist mit Mitgliedsnummer angelegt');
	
	$query = "SELECT * FROM  #__clm_turniere_tlnr "
			." WHERE sid = ".$sid
			." AND zps = ".$zps
			." AND mgl_nr = ".$mglnr
			;
	$result = clm_core::$db->loadObjectList($query);
	if (!is_null($result) AND count($result) > 0)	return array(false, 'Spieler ist Teilnehmer in Einzelturnier');
	

	return array(true, 'Spieler kann gelöscht werden');	
}
?>
