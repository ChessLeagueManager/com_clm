<?php
/**
 * @ Chess League Manager (CLM) Modul
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_dsb_player($spieler = array(), $incl_pd = 0, $file_v = 0) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	$incl_pd = clm_core::$load->make_valid($incl_pd, 0, 0);
	if (!is_array($spieler) || count($spieler) == 0) {
		return array(true, "w_noPlayerToUpdate");
	}
	$counter = 0;
	if ($file_v == 1) 
		$sql = "REPLACE INTO #__clm_dwz_spieler ( `sid`,`ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`, `FIDE_Titel`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	elseif ($file_v == 2)
		$sql = "REPLACE INTO #__clm_dwz_spieler ( `sid`,`PKZ`,`ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`, `FIDE_Titel`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	else
		return array(true, "w_noPlayerFileType");		
	$stmt = clm_core::$db->prepare($sql);
	// Detaildaten zu Mitgliedern lesen
	for ($i = 0;$i < count($spieler);$i++) {
		if ($file_v == 1) {
			//ZPS,Mgl-Nr,Status,Spielername,Geschlecht,Spielberechtigung,Geburtsjahr,Letzte-Auswertung,DWZ,Index,FIDE-Elo,FIDE-Titel,FIDE-ID,FIDE-Land
			$einSpieler = str_getcsv($spieler[$i], ",", '"');
			if (count($einSpieler) != 14 || ($einSpieler[2] == 'P' && $incl_pd == 0)) {
				continue;
			}
			$out = clm_core::$load->player_dewis_to_clm($einSpieler[3], null, $einSpieler[1], $einSpieler[4], $einSpieler[12], $einSpieler[13]);
			// return array($dsbmglnr, $clmName, $clmNameG, $dsbgeschlecht, $dsbfideland);
			$rating = ($einSpieler[8] != '0' ? $einSpieler[8] : "NULL");
			$rating_index = ($einSpieler[8] != '0' ? $einSpieler[9] : "NULL");
			$stmt->bind_param('isssssssssssss', $sid, $einSpieler[0], $out[0], $out[1], $rating, $rating_index, $out[2], $out[3], $einSpieler[6], $einSpieler[10], $out[4], $einSpieler[12], $einSpieler[2], $einSpieler[11]);
		} else {
			//ID,VKZ,Mgl-Nr,Status,Spielername,Geschlecht,Spielberechtigung,Geburtsjahr,Letzte-Auswertung,DWZ,Index,FIDE-Elo,FIDE-Titel,FIDE-ID,FIDE-Land
			$einSpieler = str_getcsv($spieler[$i], ",", '"');
			if (count($einSpieler) != 15 || ($einSpieler[2] == 'P' && $incl_pd == 0)) {
				continue;
			}
			$out = clm_core::$load->player_dewis_to_clm($einSpieler[4], null, $einSpieler[2], $einSpieler[5], $einSpieler[13], $einSpieler[14]);
			// return array($dsbmglnr, $clmName, $clmNameG, $dsbgeschlecht, $dsbfideland);
			$rating = ($einSpieler[9] != '0' ? $einSpieler[9] : "NULL");
			$rating_index = ($einSpieler[9] != '0' ? $einSpieler[10] : "NULL");
			$stmt->bind_param('issssssssssssss', $sid, $einSpieler[0], $einSpieler[1], $out[0], $out[1], $rating, $rating_index, $out[2], $out[3], $einSpieler[7], $einSpieler[11], $out[4], $einSpieler[13], $einSpieler[3], $einSpieler[12]);
		}	
		$stmt->execute();
		$counter++;
	}
	$stmt->close();
	return array(true, "m_dsbClubSuccess", $counter);
}
?>
