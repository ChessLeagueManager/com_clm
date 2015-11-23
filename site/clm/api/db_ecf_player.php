<?php
function clm_api_db_ecf_player($spieler = array(), $incl_pd = 0) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	$incl_pd = clm_core::$load->make_valid($incl_pd, 0, 0);
	if (!is_array($spieler) || count($spieler) == 0) {
		return array(true, "w_noPlayerToUpdate");
	}
	
	$config		= clm_core::$db->config();
	$lv	= $config->lv;
	$sql = " SELECT ZPS, Vereinname FROM #__clm_dwz_vereine WHERE sid = ".$sid." AND Verband = '".$lv."'";
	$vereine = clm_core::$db->loadObjectList($sql);	
	$c_vereine = count($vereine);
	if (!is_array($vereine) || count($vereine) == 0) {
		return array(true, "w_noPlayerToUpdate");
	}
	
	$counter = 0;
	$sql = "REPLACE INTO #__clm_dwz_spieler ( `sid`, `PKZ`, `ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$stmt = clm_core::$db->prepare($sql);
	// Standardwerte für alle Spieler
	$Mgl_Nr = "0";
	$status = "";
	$rating_index = "NULL";
	$fide_elo = "0";
	
	// Detaildaten zu Mitgliedern lesen
	for ($i = 0;$i < count($spieler);$i++) {
		//"Ref","Name","Sex","JrAge","Cat","Grade","Grade1","Games","RCat","RGrade","RGrade1","RGames","ClubNam1","ClubNam2","ClubNam3","ClubNam4","ClubNam5","ClubNam6","FIDECode","Nation"
		$einSpieler = str_getcsv($spieler[$i], ",", '"');
		if (count($einSpieler) != 20) {
			continue;
		}
		for ($j = 12;$j < 18;$j++) {
			if ($einSpieler[$j] == '') break;
			for ($vj = 0;$vj < $c_vereine;$vj++) {
				if ($einSpieler[$j] != $vereine[$vj]->Vereinname) continue;
				$PKZ = $einSpieler[0];
				$ZPS = $vereine[$vj]->ZPS;
				$Spielername = $einSpieler[1];
				$rating = ($einSpieler[5] != '0' ? $einSpieler[5] : "NULL");
				$Spielername_G = strtoupper($einSpieler[1]);
				$sex = $einSpieler[2];
				$birth_year = ($einSpieler[3] != '' ? (2015 - $einSpieler[3]) : '0000');
				$fide_land = $einSpieler[19];
				$fide_ID = $einSpieler[18];
				$stmt->bind_param('isssssssssssss', $sid, $PKZ, $ZPS, $Mgl_Nr, $Spielername, $rating, $rating_index, $Spielername_G, $sex, $birth_year, $fide_elo, $fide_land, $fide_ID, $status);
				$stmt->execute();
				$counter++;
			}
		}
	}
	$stmt->close();
	return array(true, "m_ecfPlayerSuccess", $counter);
}
?>
