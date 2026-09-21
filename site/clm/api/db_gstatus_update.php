<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_api_db_gstatus_update($aplayer, $type = 'clm') {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	// Konfigurationsparameter auslesen - get configuration parameter
	$config = clm_core::$db->config();
	$test_button = $config->test_button;

	$msg = '';
	$cges = 0;
	$cerr = 0;
	$cupd = 0;
	$cnon = 0;
	
	if ($type == 'clm') {
		
		$clm_key = $config->clmorg_data_key;
		$clm_domain = $config->request_domain;
		$spielerdatenurl = 'https://spielerdaten.chessleaguemanager.org/spieler.php';
	}
	
	// Überprüfen der Eingaben - check input
	foreach ($aplayer as $sp01) {
if ($test_button > 0) clm_core::$api->test_print('sp01-clm',$sp01);	
		$cges++;
		if (is_null($sp01->PKZ) OR $sp01->PKZ == '' OR is_numeric($sp01->PKZ)) {
			$cerr++;
			clm_core::addInfo('Gastspieler-Update','PKZ fehlerhaft:'.$sp01->Spielername.'-'.$sp01->Status.'-'.$sp01->PKZ.'-'.$sp01->ZPS.'-'.$sp01->Mgl_Nr);
			continue;
		}
		$success = true;
		$clm_pkz = $sp01->PKZ;
		
		if ($type == 'clm') {
			// Webservice
			try {
				$ch = curl_init($spielerdatenurl);
				$post_data = array('clm_key' => $clm_key, 'clm_pkz' => $clm_pkz, 'clm_domain' => $clm_domain);
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				if ($httpCode !== 200) {
					$error = json_encode(['error php_spieler' => 'Token ist ung&uuml;ltig oder abgelaufen (httpCode=' . $httpCode . "/" . json_encode($response) . ")."]);
					clm_core::$api->test_print('httpCode-Error',$error);
					$success = false;
				} else {
					// Spieler mit bestimmter PKZ
					$playerlist = json_decode($response, true);
				}
			}
			catch (RuntimeException $e) {
				$error = json_encode(['runtime error test_php_verein' => "❌ Fehler: " . $e->getMessage()]);
				clm_core::$api->test_print('RuntimeException',$error);
				$success = false;
			}
			if ($success == true) {    // Auslesen war erfolgreich
				if (isset($playerlist['data'])) {				// notwendig, wenn kein Spielet gefunden wurde
					foreach ($playerlist['data'] as $player) {
						if (isset($player['lastname']) AND isset($player['firstname'])) {
							$spielername = str_replace("'", "´", $player["lastname"].",".$player["firstname"]);
							if ($sp01->Spielername != $spielername) {
								$spielername_G = mb_strtoupper($spielername);
							} else {
								unset($spielername);
								unset($spielername_G);
							}
						}
						if (isset($player['birthYear']) AND $sp01->Geburtsjahr != $player['birthYear'])
							$Geburtsjahr = $player['birthYear']; else unset($Geburtsjahr);
						if (isset($player['rating']) AND $sp01->DWZ != $player['rating'])
							$DWZ = $player['rating']; else unset($DWZ);
						if (isset($player['index']) AND $sp01->DWZ_Index != $player['index']) 
							$DWZ_Index = $player['index']; else unset($DWZ_Index);
						if (isset($player['fide_rating']) AND $sp01->FIDE_Elo != $player['fide_rating']) 
							$FIDE_Elo = $player['fide_rating']; else unset($FIDE_Elo);
						if (isset($player['fide_id']) AND $sp01->FIDE_ID != $player['fide_id']) 
							$FIDE_ID = $player['fide_id']; else unset($FIDE_ID);
						if (isset($player['fide_federation']) AND $sp01->FIDE_Land != $player['fide_federation']) 
							$FIDE_Land = $player['fide_federation']; else unset($FIDE_Land);
						if (isset($player['fide_title']) AND $sp01->FIDE_Titel != $player['fide_title']) 
							$FIDE_Titel = $player['fide_title']; else unset($FIDE_Titel);
							continue;	
					}
				} else {
						$success = false;
				}
			}
		}
	
		if ($type == 'dsb') {					// Spielersuche auf DSB-Server	
			// Webservice
			try {
				$client = new clm_class_OAuth2Client();

				$result = $client->callApiWithRefresh($client->apiBaseUrl . '/dwz/dwzliste/persons/'.$sp01->PKZ.'/');
				$playerlist = $result["body"];  // !!
				unset($client);
			}
			catch(RuntimeException $e) {
				$error = json_encode(['runtime error test_php_verein' => "❌ Fehler: " . $e->getMessage()]);
				clm_core::$api->test_print('RuntimeException',$error);
				$success = false;
			}
			if ($success == true) {    // Auslesen war erfolgreich
				if (isset($playerlist['lastname'])) {				// notwendig, wenn kein Spieler gefunden wurde
					$player = $playerlist;	
						if (isset($player['lastname']) AND isset($player['firstname'])) {
							$spielername = str_replace("'", "´", $player["lastname"].",".$player["firstname"]);
							if ($sp01->Spielername != $spielername) {
								$spielername_G = mb_strtoupper($spielername);
							} else {
								unset($spielername);
								unset($spielername_G);
							}
						}
						if (isset($player['birthyear']) AND $sp01->Geburtsjahr != $player['birthyear'])
							$Geburtsjahr = $player['birthyear']; else unset($Geburtsjahr);
						if (isset($player['rating']) AND $sp01->DWZ != $player['rating'])
							$DWZ = $player['rating']; else unset($DWZ);
						if (isset($player['index']) AND $sp01->DWZ_Index != $player['index']) 
							$DWZ_Index = $player['index']; else unset($DWZ_Index);
						if (isset($player['fideRatingStandard']) AND $sp01->FIDE_Elo != $player['fideRatingStandard']) 
							$FIDE_Elo = $player['fideRatingStandard']; else unset($FIDE_Elo);
						if (isset($player['fideId']) AND $sp01->FIDE_ID != $player['fideId']) 
							$FIDE_ID = $player['fideId']; else unset($FIDE_ID);
						if (isset($player['fideCountry']) AND $sp01->FIDE_Land != $player['fideCountry']) 
							$FIDE_Land = $player['fideCountry']; else unset($FIDE_Land);
						if (isset($player['fideTitle']) AND $sp01->FIDE_Titel != $player['fideTitle']) 
							$FIDE_Titel = $player['fideTitle']; else unset($FIDE_Titel);
				} else {
					$success = false;
				}
			}
		}


		if ($success == true) {					// Spielersuche erfolgreich	
			// Welche Daten haben sich geändert?
			$updates = '';
			if (isset($spielername)) 
				$updates .= ", Spielername='" . $spielername . "'";
			if (isset($spielername_G)) 
				$updates .= ", Spielername_G='" . $spielername_G . "'";			
			if (isset($Geburtsjahr)) 
				$updates .= ", Geburtsjahr='" . $Geburtsjahr . "'";
			if (isset($DWZ)) 
				$updates .= ", DWZ='" . $DWZ . "'";
			if (isset($DWZ_Index)) 
				$updates .= ", DWZ_Index='" . $DWZ_Index . "'";
			if (isset($FIDE_Elo)) 
				$updates .= ", FIDE_Elo='" . $FIDE_Elo . "'";
			if (isset($FIDE_ID)) 
				$updates .= ", FIDE_ID='" . $FIDE_ID . "'";
			if (isset($FIDE_Land)) 
				$updates .= ", FIDE_Land='" . $FIDE_Land . "'";
			if (isset($FIDE_Titel)) 
				$updates .= ", FIDE_Titel='" . $FIDE_Titel . "'";
			if ($updates == '') {
				$sql = '';
				$cnon++;
				clm_core::addInfo('Gastspieler-Update','keine Änderung nötig:'.$sp01->Spielername.'-'.$sp01->Status.'-'.$sp01->PKZ.'-'.$sp01->ZPS.'-'.$sp01->Mgl_Nr);
			} else {
				$pos = strpos($updates, ',');
				if ($pos !== false) {
					$updates = substr_replace($updates, '', $pos, 1);
				}
				$sql = "UPDATE #__clm_dwz_spieler SET " . $updates . " WHERE sid = $sid AND ZPS ='" . $sp01->ZPS . "' and Mgl_Nr =" . $sp01->Mgl_Nr . ";";
			}
			if ($sql > '') {
				$result = clm_core::$db->query($sql);
				$result = true;
				if ($result === false) {
					$cerr++;
					clm_core::addInfo('Gastspieler-Update','DB-Fehler:'.$sp01->Spielername.'-'.$sp01->Status.'-'.$sp01->PKZ.'-'.$sp01->ZPS.'-'.$sp01->Mgl_Nr);
				} else {
					$cupd++;
					clm_core::addInfo('Gastspieler-Update','erfolgreich:'.$sp01->Spielername.'-'.$sp01->Status.'-'.$sp01->PKZ.'-'.$sp01->ZPS.'-'.$sp01->Mgl_Nr);
				}
			}
		} else { 			//	if ($success == true) {					// Spielersuche nicht erfolgreich	
			$cerr++;
			clm_core::addInfo('Gastspieler-Update','PKZ nicht gefunden:'.$sp01->Spielername.'-'.$sp01->Status.'-'.$sp01->PKZ.'-'.$sp01->ZPS.'-'.$sp01->Mgl_Nr);
		}

	}

		return array(true, 	$sp01->ZPS.'<br>'
							.$cges.' Gastspieler gefunden<br>'
							.$cupd.' Spieler aktualisiert<br>'
							.$cnon.' Spieler ohne Änderung<br>'
							.$cerr.' Fehler, siehe Logging', $cges);
		
}
?>
