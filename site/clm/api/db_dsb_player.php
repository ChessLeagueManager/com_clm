<?php
function clm_api_db_dsb_player($spieler = array(), $incl_pd = 0) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	$incl_pd = clm_core::$load->make_valid($incl_pd, 0, 0);
	if (!is_array($spieler) || count($spieler) == 0) {
		return array(true, "w_noPlayerToUpdate");
	}
	$counter = 0;
	$sql = "REPLACE INTO #__clm_dwz_spieler ( `sid`,`ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$stmt = clm_core::$db->prepare($sql);
	// Detaildaten zu Mitgliedern lesen
	for ($i = 0;$i < count($spieler);$i++) {
		//ZPS,Mgl-Nr,Status,Spielername,Geschlecht,Spielberechtigung,Geburtsjahr,Letzte-Auswertung,DWZ,Index,FIDE-Elo,FIDE-Titel,FIDE-ID,FIDE-Land
		$einSpieler = str_getcsv($spieler[$i], ",", '"');
		if (count($einSpieler) != 14 || ($einSpieler[2] == 'P' && $incl_pd == 0)) {
			continue;
		}
		$out = clm_core::$load->player_dewis_to_clm($einSpieler[3], null, $einSpieler[1], $einSpieler[4], $einSpieler[12], $einSpieler[13]);
		// return array($dsbmglnr, $clmName, $clmNameG, $dsbgeschlecht, $dsbfideland);
		$rating = ($einSpieler[8] != '0' ? $einSpieler[8] : "NULL");
		$rating_index = ($einSpieler[8] != '0' ? $einSpieler[9] : "NULL");
		$stmt->bind_param('issssssssssss', $sid, $einSpieler[0], $out[0], $out[1], $rating, $rating_index, $out[2], $out[3], $einSpieler[6], $einSpieler[10], $out[4], $einSpieler[12], $einSpieler[2]);
		$stmt->execute();
		$counter++;
	}
	$stmt->close();
	return array(true, "m_dsbClubSuccess", $counter);
}
?>
