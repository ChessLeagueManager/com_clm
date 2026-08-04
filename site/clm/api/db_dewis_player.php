<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_api_db_dewis_player($zps = - 1, $incl_pd = 0, $mgl_nr = array()) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
//clm_core::$api->test_print('zps',$zps);	
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$dewis_import_delay = $config->dewis_import_delay;

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
	// Webservice
	try {
		$client = new clm_class_OAuth2Client();

		usleep($dewis_import_delay);
			
		// Mitglieder eines Vereins
		$result = $client->callApiWithRefresh($client->apiBaseUrl . '/dwz/dwzliste/persons?vkz='.$zps);
		$playerlist = $result["body"];
		unset($client);
	}
	catch (RuntimeException $e) {
        echo json_encode(['runtime error test_php_verein' => "❌ Fehler: " . $e->getMessage()]);
        exit;
	}

	$str = '';
//clm_core::$api->test_print('playerlist-'.$zps,$playerlist);
	// Detaildaten zu Mitgliedern verarbeiten
	foreach ($playerlist['data'] as $player) {
	  
		foreach ($player["memberships"] as $membership1) {
			if ($membership1["vkz"] == $zps) {
				$member = $membership1;
				break;
			}
		}
		if ((!isset($member["licenceState"]) OR $member["licenceState"] != 'ACTIVE') && $incl_pd == 0) {
				continue;
		}
		if (isset($member["memberNo"]) AND is_numeric($member["memberNo"]) AND $member["memberNo"] > 0) $mgl_nr = $member["memberNo"];
		else $mgl_nr = '0';
		$counter++;
		if (!array_key_exists((integer)$mgl_nr, $aold)) {  
			// neuer Eintrag
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
			if (isset($player['birthYear'])) $values .= ", '" . $player['birthYear'] . "'";
			else $values .= ", '" . '0000' . "'";
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
			$sql = "INSERT INTO #__clm_dwz_spieler (" . $elements . ") VALUES (" . $values . ");";
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
			if ($old->Geburtsjahr != $player['birthyear'])
				$updates .= ", Geburtsjahr='" . $player['birthyear'] . "'";
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

			if ($updates == '') $sql = '';
			else {
				$pos = strpos($updates, ',');
				if ($pos !== false) {
					$updates = substr_replace($updates, '', $pos, 1);
				}
				$sql = "UPDATE #__clm_dwz_spieler SET " . $updates . " WHERE sid = $sid AND ZPS ='" . $zps . "' and Mgl_Nr =" . $mgl_nr . ";";
			}
		}	
		if ($sql > '') {
			$result = clm_core::$db->query($sql);	
			if ($result === false) { $str .= " ".$zps."-".$mgl_nr; }
		}
	}
	return array(true, "m_onlinePlayerSuccess".$str, $counter);
}
?>
