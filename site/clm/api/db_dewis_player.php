<?php
function clm_api_db_dewis_player($zps = - 1, $incl_pd = 0, $mgl_nr = array()) {
	@set_time_limit(0); // hope
	$source = "https://dwz.svw.info/services/files/dewis.wsdl";
	$sid = clm_core::$access->getSeason();
	$zps = clm_core::$load->make_valid($zps, 8, "");
	$incl_pd = clm_core::$load->make_valid($incl_pd, 0, 0);
	if (strlen($zps) != 5) {
		return array(false, "e_wrongZPSFormat");
	}
	$counter = 0;
	// SOAP Webservice
	try {
		$client = clm_core::$load->soap_wrapper($source);

		// VKZ des Vereins --> Vereinsliste
		$unionRatingList = $client->unionRatingList($zps);
		$sql = "REPLACE INTO #__clm_dwz_spieler ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`, FIDE_Titel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = clm_core::$db->prepare($sql);
		// Detaildaten zu Mitgliedern lesen
		foreach ($unionRatingList->members as $m) {
			if ($m->state == 'P' && $incl_pd == 0) {
				continue;
			}
			if (count($mgl_nr)!=0)
			{
				$update=false;
				foreach($mgl_nr as $mgl_nr1) {
					if(intval($mgl_nr1)==intval($m->membership)) {
						$update=true;
						break;
					}
				}
				if(!$update) {
					continue;
				}
			}
			$tcard = $client->tournamentCardForId($m->pid);
			$out = clm_core::$load->player_dewis_to_clm($m->surname, $m->firstname, $m->membership, $m->gender, $m->idfide, $tcard->member->fideNation);
			// return array($dsbmglnr, $clmName, $clmNameG, $dsbgeschlecht, $dsbfideland);
			$rating = ($m->rating != '0' ? $m->rating : "NULL");
			$rating_index = ($m->rating != '0' ? $m->ratingIndex : "NULL");
			$state = ($m->state != '' ? $m->state : "A");
			$stmt->bind_param('issssssssssssss', $sid, $zps, $out[0], $m->pid, $out[1], $rating, $rating_index, $out[2], $out[3], $m->yearOfBirth, $m->elo, $out[4], $m->idfide, $state, $m->fideTitle);
			$stmt->execute();
			$counter++;
			unset($tcard);
		}
		$stmt->close();
		unset($unionRatingList);
		unset($client);
	}
	catch(SOAPFault $f) {
		if($f->getMessage() == "that is not a valid union id" || $f->getMessage() == "that union does not exists") {
			return array(true, "w_wrongZPS",0);
		}
		return array(false, "e_connectionError");
	}
	return array(true, "m_dewisPlayerSuccess", $counter);
}
?>
