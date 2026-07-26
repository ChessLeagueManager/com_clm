<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_api_db_clmorg_player($zps = - 1, $incl_pd = 0, $mgl_nr = array()) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
//clm_core::$api->test_print('db_clmorg_player-zps',$zps);	
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$dewis_import_delay = $config->dewis_import_delay;
	$clm_key = $config->clmorg_data_key;
	$clm_domain = $config->request_domain;

	$zps = clm_core::$load->make_valid($zps, 8, "");
	$incl_pd = clm_core::$load->make_valid($incl_pd, 0, 0);
	$counter = 0;
	if (strlen($zps) != 5) {
		return array(true, "e_wrongZPSFormat",$counter);
	}
	
	$sql = "SELECT *, 0 as inUpdate FROM #__clm_dwz_spieler"
			." WHERE sid = ".$sid
			." AND ZPS = '".$zps."'";
	if ($incl_pd == 0) 
		$sql .= " AND Status = 'A'";
	$mgl = clm_core::$db->loadObjectList($sql);
	$aold = array();
	if (!is_null($mgl)) {
		foreach ($mgl as $m) {
			$aold[$m->Mgl_Nr] = $m;
		}
	}
	if (!is_null($mgl)) $anz = count($mgl); 
	else $anz = 0;		// bisher keine Spieler zum Verein -> Erstlauf zur Saison
//clm_core::$api->test_print('count-'.$zps,$anz);	

//	$clm_key = 'Q2QK3uu0e84BtWYqz19t0IrBbvnw8r1s71czKKREPhNWUHyZi0TAhKfpF6im';
	$clm_zps = $zps;
	$clm_mgl = '';

	$spielerdatenurl = 'https://spielerdaten.chessleaguemanager.org:/spieler.php?clm_zps=' . $clm_zps . '&clm_mgl=' . $clm_mgl ;


	// Webservice
	try {
		$ch = curl_init($spielerdatenurl);
		$post_data = array('clm_key' => $clm_key, 'clm_zps' => $clm_zps, 'clm_mgl' => $clm_mgl, 'clm_domain' => $clm_domain);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		usleep($dewis_import_delay);
			
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpCode !== 200) {
			$error = json_encode(['error php_spieler' => 'Token ist ung&uuml;ltig oder abgelaufen (httpCode=' . $httpCode . "/" . json_encode($response) . ")."]);
//clm_core::$api->test_print('Error1',$error);
			return array(false, "http_code_not200", $error);
		}
		
		// Mitglieder eines Vereins
		$playerlist = json_decode($response, true);
	}
	catch (RuntimeException $e) {
		$error = json_encode(['runtime error test_php_verein' => "❌ Fehler: " . $e->getMessage()]);
//clm_core::$api->test_print('Error2',$error);
		return array(false, "Exception geflogen", $error);
	}

	$str = '';
//die();
//clm_core::$api->test_print('playerlist-'.$zps,$playerlist);
	// Detaildaten zu Mitgliedern verarbeiten
	if (isset($playerlist['data'])) {				// notwendig, wenn Verein keine Mitglieder hat
	  foreach ($playerlist['data'] as $player) {

		foreach ($player["memberships"] as $membership1) {
			if ($membership1["vkz"] == $zps) {
				$member = $membership1;
				break;
			}
		}
		if ($member["licenceState"] != 'ACTIVE' && $incl_pd == 0) {
				continue;
		}
		$mgl_nr = $member["memberNo"];
		$counter++;
		if (!array_key_exists((integer)$mgl_nr, $aold)) {  
			// neuer Eintrag
			// am Saisonstart werden Spieler mit Austrittsdatum überlesen
			if (isset($member["leavingdate"]) AND $anz == 0 ) continue; 

			$elements = "sid";
			$values = $sid;
			$elements .= ", PKZ";
			$values .= ", '" . $player['nuLigaPersonId'] . "'";
			$spielername = str_replace("'", "´", $player["lastname"].",".$player["firstname"]);
			$elements .= ", Spielername";
			$values .= ", '" . $spielername . "'";
			$spielername_G = mb_strtoupper($spielername);
			$elements .= ", Spielername_G";
			$values .= ", '" . $spielername_G . "'";
			$elements .= ", Geburtsjahr";
			$values .= ", '" . $player['birthYear'] . "'";
			if (!empty($player['gender'])) {
				if ($player["gender"] == 'MALE') $geschlecht = 'M';
				elseif ($player["gender"] == 'FEMALE') $geschlecht = 'W';
				else $geschlecht = 'M';
				$elements .= ", Geschlecht";
				$values .= ", '" . $geschlecht . "'";
			}
			if (isset($player["rating"]) AND is_numeric($player["rating"]) AND  $player["rating"] > 0 ) {
				$elements .= ", DWZ";
				$values .= ", " . $player['rating'];
				if (isset($player["index"]) AND is_numeric($player["index"]) AND  $player["index"] > 0 ) {
					$elements .= ", DWZ_Index";
					$values .= ", " . $player['index'];
				}
			}	
			if (isset($player["auswdate"]) AND clm_core::$load->is_date($player["auswdate"],'Y-m-d') ) {
				$elements .= ", Letzte_Auswertung";
				$values .= ", '" . $player['auswdate'] . "'";
			}	
			$elements .= ", ZPS";
			$values .= ", '" . $zps . "'";
			$elements .= ", Mgl_Nr";
			$values .= ", " . $mgl_nr ;
			if (isset($member["licenceState"])) {
				if ($member["licenceState"] == 'ACTIVE') $state = 'A';
				elseif ($member["licenceState"] == 'PASSIVE') $state = 'P';
				else $state = '';
				$elements .= ", Status";
				$values .= ", '" . $state . "'";
			}
			if (isset($player["fide_id"]) AND is_numeric($player["fide_id"]) AND  $player["fide_id"] > 0 ) {
				$elements .= ", FIDE_ID";
				$values .= ", " . $player['fide_id'];
			}	
			if (isset($player["fide_rating"]) AND is_numeric($player["fide_rating"]) AND  $player["fide_rating"] > 0 ) {
				$elements .= ", FIDE_Elo";
				$values .= ", " . $player['fide_rating'];
			}	
			if (isset($player["fide_federation"]) AND strlen($player["fide_federation"]) == 3 ) {
				$elements .= ", FIDE_Land";
				$values .= ", '" . $player['fide_federation']. "'";
			}	
			if (isset($player["fide_title"]) AND strlen($player["fide_title"]) <= 3 ) {
				$elements .= ", FIDE_Titel";
				$values .= ", '" . $player['fide_title']. "'";
			}	
			if (isset($member["joiningdate"]) AND clm_core::$load->is_date($member["joiningdate"],'Y-m-d') AND ($member["joiningdate"] > '1970-01-01') ) {
				$elements .= ", joiningdate";
				$values .= ", '" . $member['joiningdate']. "'";
			}	
			if (isset($member["leavingdate"]) AND clm_core::$load->is_date($member["leavingdate"],'Y-m-d') AND ($member["leavingdate"] > '1970-01-01') ) {
				$elements .= ", leavingdate";
				$values .= ", '" . $member['leavingdate']. "'";
			}	
			if (isset($member["gesperrt"]) AND (($member["gesperrt"] == '1') OR ($member["gesperrt"] == '0')) ) {
				$elements .= ", gesperrt";
				$values .= ", '" . $member['gesperrt']. "'";
			}	
			$sql = "INSERT INTO #__clm_dwz_spieler (" . $elements . ") VALUES (" . $values . ");";
//clm_core::$api->test_print('playerlist-'.$zps,$playerlist);
		} else {
			// Update eines bereits vorhandenen Vereinsmitglieds
			$aold[$mgl_nr]->inUpdate = 1;
			$old = $aold[$mgl_nr];
			$updates = '';
			if (! empty($player['nuLigaPersonId']) && ($old->PKZ != $player['nuLigaPersonId'])) {
				$updates .= ", PKZ='" . $player['nuLigaPersonId'] . "'";
			}
			$spielername = str_replace("'", "´", $player["lastname"].",".$player["firstname"]);
			if ($old->Spielername != $spielername) {
				$updates .= ", Spielername='" . $spielername . "'";
				$spielername_G = mb_strtoupper($spielername);
				$updates .= ", Spielername_G='" . $spielername_G . "'";
			}
			if ($old->Geburtsjahr != $player['birthYear'])
				$updates .= ", Geburtsjahr='" . $player['birthYear'] . "'";
			if (!empty($player['gender'])) {
				if ($player["gender"] == 'MALE') $geschlecht = 'M';
				elseif ($player["gender"] == 'FEMALE') $geschlecht = 'W';
				else $geschlecht = 'M';
				if ($old->Geschlecht != $geschlecht ) 
					$updates .= ", Geschlecht='" . $geschlecht . "'";
			}
			if (isset($player["rating"]) AND is_numeric($player["rating"]) AND  $player["rating"] > 0 ) {
				if ($old->DWZ != $player["rating"] ) {
					$updates .= ", DWZ='" . $player['rating'] . "'";
					if (isset($player["index"]) AND is_numeric($player["index"]) AND  $player["index"] > 0 ) {
						if ($old->DWZ_Index != $player["index"] ) {
							$updates .= ", DWZ_Index='" . $player['index'] . "'";
						}
					}
				}
			}	
			if (isset($player["auswdate"]) AND clm_core::$load->is_date($player["auswdate"],'Y-m-d') ) {
				if ($old->Letzte_Auswertung != $player["auswdate"] ) {
					$updates .= ", Letzte_Auswertung='" . $player['auswdate'] . "'";
				}
			}	
			if (isset($member["licenceState"])) {
				if ($member["licenceState"] == 'ACTIVE') $state = 'A';
				elseif ($member["licenceState"] == 'PASSIVE') $state = 'P';
				else $state = '';
				if ($state > '' AND $old->Status != $state ) 
					$updates .= ", Status='" . $state . "'";
			}
			if (isset($player["fide_id"]) AND is_numeric($player["fide_id"]) AND  $player["fide_id"] > 0 ) {
				if ($old->FIDE_ID != $player["fide_id"] ) 
					$updates .= ", FIDE_ID='" . $player["fide_id"] . "'";
			}	
			if (isset($player["fide_rating"]) AND is_numeric($player["fide_rating"]) AND  $player["fide_rating"] > 0 ) {
				if ($old->FIDE_Elo != $player["fide_rating"] ) 
					$updates .= ", FIDE_Elo='" . $player["fide_rating"] . "'";
			}	
			if (isset($player["fide_federation"]) AND strlen($player["fide_federation"]) == 3 ) {
				if ($old->FIDE_Land != $player["fide_federation"] ) 
					$updates .= ", FIDE_Land='" . $player["fide_federation"] . "'";
			}	
			if (isset($player["fide_title"]) AND strlen($player["fide_title"]) <= 3 ) {
				if ($old->FIDE_Titel != $player["fide_title"] ) 
					$updates .= ", FIDE_Titel='" . $player["fide_title"] . "'";
			}	
			if (isset($member["joiningdate"]) AND clm_core::$load->is_date($member["joiningdate"],'Y-m-d') AND ($member["joiningdate"] > '1970-01-01') ) {
				if ( is_null($old->joiningdate) OR ($old->joiningdate <= '1970-01-01') ) 
					$updates .= ", joiningdate='" . $member["joiningdate"] . "'";
			}	
			if (isset($member["leavingdate"]) AND clm_core::$load->is_date($member["leavingdate"],'Y-m-d') AND ($member["leavingdate"] > '1970-01-01') ) {
				if ( is_null($old->leavingdate) OR ($old->leavingdate <= '1970-01-01') )
					$updates .= ", leavingdate='" . $member["leavingdate"] . "'";
			}	
			if (isset($member["gesperrt"]) AND (($member["gesperrt"] == '1') OR ($member["gesperrt"] == '0')) ) {
				if ( $old->gesperrt != $member["gesperrt"] )
					$updates .= ", gesperrt='" . $member["gesperrt"] . "'";
			}	

			if ($updates == '') $sql = '';
			else {
				$pos = strpos($updates, ',');
				if ($pos !== false) {
					$updates = substr_replace($updates, '', $pos, 1);
				}
				$sql = "UPDATE #__clm_dwz_spieler SET " . $updates . " WHERE sid = $sid AND ZPS ='" . $zps . "' and Mgl_Nr =" . $mgl_nr . ";";
//if ($mgl_nr == 58) clm_core::$api->test_print('sql58',$sql);	
			}
		}	
		if ($sql > '') {
			$result = clm_core::$db->query($sql);	
			if ($result === false) { 
clm_core::$api->test_print('sqlfalse',$sql);
				$str .= " ".$zps."-".$mgl_nr; 
			}
		}
	  }
	}
	return array(true, "m_clmorgPlayerSuccess".$str, $counter);
}
?>
