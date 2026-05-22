<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Input: Liga, Runde, Durchgang
// Output: 	true,1 - alle Bretter besetzt
//         	true,0 - mindestens ein Brett nicht besetzt
//			false,... - etwas falsch -> Hinweis

function clm_api_db_team_complete($lid,$dg,$runde,$paar,$heim) {
	$lid = clm_core::$load->make_valid($lid, 0, -1);
	$dg = clm_core::$load->make_valid($dg, 0, -1);
	$runde = clm_core::$load->make_valid($runde, 0, -1);
	$paar = clm_core::$load->make_valid($paar, 0, -1);
	$heim = clm_core::$load->make_valid($heim, 0, -1);
//$tstr = $lid."-".$dg."-".$runde."-".$paar."-".$heim;
//clm_core::$api->test_print('imput',$tstr);
	if (($lid < 1) OR ($dg < 1) OR($runde < 1) OR ($paar < 1) OR ($paar < 1))
		return array(false,0,'e_Parameter');

	$query = "SELECT l.* "
			." FROM  #__clm_liga as l "
			." WHERE l.id = ".$lid
			;
	$liga = clm_core::$db->loadObjectList($query);
	if (is_null($liga) OR count($liga) != 1)
		return array(false,0,'e_noLeague');

	// Ergebnisse, die für einen Antritt der Mannschaft bzw. ein besetztes Brett stehen
	$bb_Hstring = "0,1,2,5";
	$bb_Gstring = "0,1,2,4";

/*	// Mannschaftsergebnisprüfen
	$query	= "SELECT ergebnis "
		." FROM #__clm_rnd_man "
		." WHERE lid = ".$lid
		." AND dg = ".$dg
		." AND runde = ".$runde
		." AND paar = ".$paar
		." AND heim = ".$heim 
		;
	$result = clm_core::$db->loadObjectList($query);
	$mergebnis=$result[0]->ergebnis;
clm_core::$api->test_print('mergebnis',$mergebnis);
	
	if ($heim == 1 AND $mergebnis == 5) return array(true,1,'m_SiegKampflos');
	if ($heim == 1 AND $mergebnis == 4) return array(true,0,'m_keinAntritt');
	if ($heim == 0 AND $mergebnis == 4) return array(true,1,'m_SiegKampflos');
	if ($heim == 0 AND $mergebnis == 5) return array(true,0,'m_keinAntritt');
*/		
	// Anzahl besetzte Bretter zählen
	$query	= "SELECT COUNT(heim) as bb "
		." FROM #__clm_rnd_spl "
		." WHERE lid = ".$lid
		." AND dg = ".$dg
		." AND runde = ".$runde
		." AND paar = ".$paar
		." AND heim = ".$heim;
	if ($heim == 1)
		$query .= " AND ( FIND_IN_SET(CAST(ergebnis AS CHAR),'".$bb_Hstring."') != 0 )";
	else
		$query .= " AND ( FIND_IN_SET(CAST(ergebnis AS CHAR),'".$bb_Gstring."') != 0 )";
	$result = clm_core::$db->loadObjectList($query);
	$bb_count=$result[0]->bb;
//clm_core::$api->test_print('bb_count',$bb_count);
	if ($bb_count == $liga[0]->stamm)
		return array(true,1,'m_complete');
	else
		return array(true,0,'m_incomplete');
}