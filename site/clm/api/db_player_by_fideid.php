<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_api_db_player_by_fideid($fideid) {
	@set_time_limit(0); // hope
	$sid = clm_core::$access->getSeason();
	// Konfigurationsparameter auslesen - get configuration parameter
	$config = clm_core::$db->config();
	$clm_key = $config->clmorg_data_key;
	If (strlen($clm_key) > 30) $s_clm_key = 1; else $s_clm_key = 0;
	$clm_domain = $config->request_domain;
	$test_button = $config->test_button;

	// Überprüfen der Eingaben - check input
	$msg = '';
	$success = false;
	$output_record = null;
	
	if ($s_clm_key == 1) { // Spielersuche auf CLM-Server
		$success = true;
		$spielerdatenurl = 'https://spielerdaten.chessleaguemanager.org/spieler.php';
		$clm_fideid = $fideid;
	
		// Webservice
		try {
			$ch = curl_init($spielerdatenurl);
			$post_data = array('clm_key' => $clm_key, 'clm_fideid' => $clm_fideid, 'clm_domain' => $clm_domain);
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
					$output_record = $player;
					$output_record["source"] = 'CLM';	
/*						if (isset($player['lastname']) AND isset($player['firstname'])) {
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
*/				}
			} else {
				$success = false;
			}
		}
	}
	
	if ($success == false) {					// Spielersuche auf DSB-Server	
		// Webservice
		try {
			$client = new clm_class_OAuth2Client();

			$result = $client->callApiWithRefresh($client->apiBaseUrl . '/dwz/dwzliste/persons?fideId='.$fideid.'/');
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
				$output_record = $playerlist;	
				$output_record["source"] = 'DSB';	
/*					$player = $playerlist;	
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
*/			} else {
				$success = false;
			}
		}
	}


	if ($success == true) {					// Spielersuche erfolgreich	
		$msg = 'FideID-Datensatz gefunden';
	} else { 								// Spielersuche nicht erfolgreich	
		$msg = 'FideID-Datensatz nicht gefunden';
	}

	return array($success, 	$output_record, $msg);
		
}
?>
