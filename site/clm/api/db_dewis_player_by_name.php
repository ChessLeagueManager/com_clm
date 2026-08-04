<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_api_db_dewis_player_by_name($name = '', $vorname = '', $year = '') {
	@set_time_limit(0); // hope
	if ($name == '' OR $vorname == '' ) {
		return array(false, "e_wrongDataFormat");
	}
	$counter = 0;
	// Webservice
	try {
		$client = new clm_class_OAuth2Client();

		// Personenliste entspr. Namen
		setlocale(LC_CTYPE, 'de_DE.UTF-8' );
		// Text von UTF-8 in ASCII umwandeln und ähnliche Zeichen annähern (TRANSLIT)
		$zname = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
		$result = $client->callApiWithRefresh($client->apiBaseUrl . '/dwz/dwzliste/persons?lastname='.$zname);
		$playerlist = $result["body"];
		$searchByName = array();
		$stcard = 0;
		if (!isset($playerlist['data'])) {
			return array(true, "m_dewisPlayerSuccess", 0,$searchByName);
		}
		// Detaildaten zu Mitgliedern verarbeiten
		foreach ($playerlist['data'] as $player) {
			$stcard++;
			if ($player['birthyear'] != $year) {
				continue;
			}
			if (strpos(iconv('UTF-8', 'ASCII//TRANSLIT', $player['firstname']),iconv('UTF-8', 'ASCII//TRANSLIT', $vorname)) === false) {
				continue;
			}
			$searchByName[] = $player;
			$counter++;
		}
		unset($client);
	}
	catch(RuntimeException $e) {
		$error = json_encode(['runtime error test_php_verein' => "❌ Fehler: " . $e->getMessage()]);
		clm_core::$api->test_print('RuntimeException',$error);
	}
	return array(true, "m_dewisPlayerSuccess", $counter,$searchByName);
}
?>
