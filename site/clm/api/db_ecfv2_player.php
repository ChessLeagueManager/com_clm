<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_ecfv2_player($zps = - 1, $mgl_nr = array()) {
	@set_time_limit(0); // hope
//	$source = "https://www.ecfrating.org.uk/v2/new/api.php?v2/players_ratings"; // until June 2021
	$source = "https://www.ecfrating.org.uk/v2/new/api.php?v2/rating_list";
	$sid = clm_core::$access->getSeason();
	$zps = clm_core::$load->make_valid($zps, 8, "");
	if (strlen($zps) != 4) {
		return array(true, "e_wrongZPSFormat",$zps);
	}
	$out = file_get_contents($source);
	$a_out = json_decode($out);
	
	foreach ($a_out->column_names as $key => $value) {
		if ($value == 'ECF_code') $i_ECF_code = $key;
		if ($value == 'FIDE_no') $i_FIDE_no = $key;
		if ($value == 'full_name') $i_full_name = $key;
		if ($value == 'ECF_junior') $i_ECF_junior = $key;
		if ($value == 'gender') $i_gender = $key;
		if ($value == 'nation') $i_nation = $key;
//		if ($value == 'standard_original_rating') $i_standard_original_rating = $key;
		if ($value == 'original_standard') $i_original_standard = $key;
		if ($value == 'club_code') $i_club_code = $key;
	}

	$str = '';
	$sql = "REPLACE INTO #__clm_dwz_spieler ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `Geschlecht`, `Geburtsjahr`, `Junior`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`, FIDE_Titel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$stmt = clm_core::$db->prepare($sql);
	$mgl_nr = 0;
	$geburtsjahr = '0000';
	$dwz_index = 0;
	$fide_elo = 0;
	$status = 'A';
	$fide_titel = NULL;
	$counter = 0;
	$i = 0;
	foreach ($a_out->players as $player) {
		$i++;
		if ($zps != $player[$i_club_code]) continue;
		if ('' == $player[$i_ECF_code]) continue;
		// Detaildaten zu Mitgliedern lesen
		$ECF_code = substr($player[$i_ECF_code],0,-1);	

//		$source = "https://www.ecfrating.org.uk/v2/new/api.php?v2/players/code/";
//		$source .= $ECF_code;
//		$out = file_get_contents($source);
//		$d_out = json_decode($out);

		$spl_name = strtoupper($player[$i_full_name]);
		$fide_no = (integer) $player[$i_FIDE_no];
		if (isset($i_ECF_junior)) {
			if ($player[$i_ECF_junior] == true ) $junior = 1; else $junior = 0;
		} else $junior = 0;
		if ($player[$i_gender] == 'F') $gender = 'W'; else $gender = $player[$i_gender];
//		$stmt->bind_param('isissiisssiisiss', $sid, $zps, $mgl_nr, $player[$i_ECF_code], $player[$i_full_name], $player[$i_standard_original_rating], $dwz_index, $spl_name, $gender, $geburtsjahr, $junior, $fide_elo, $player[$i_nation], $fide_no, $status, $fide_titel);
		$stmt->bind_param('isissiisssiisiss', $sid, $zps, $mgl_nr, $player[$i_ECF_code], $player[$i_full_name], $player[$i_original_standard], $dwz_index, $spl_name, $gender, $geburtsjahr, $junior, $fide_elo, $player[$i_nation], $fide_no, $status, $fide_titel);
		$result = $stmt->execute();
		if ($result === false) { $str .= " ".$zps."-".$player[$i_ECF_code]; }
		$counter++;
		unset($d_out);
		unset($out);
	}
	$stmt->close();
	
	return array(true, "m_ecfv2PlayerSuccess".$str, $counter);
}
?>
