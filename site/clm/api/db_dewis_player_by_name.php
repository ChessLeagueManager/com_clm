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
	// SOAP Webservice
	try {
		$client = new clm_class_OAuth2Client();

		// Personenliste entspr. Namen
		$result = $client->callApiWithRefresh($client->apiBaseUrl . '/dwz/dwzliste/persons?lastname='.$name);
		$playerlist = $result["body"];
		$searchByName = array();
		$stcard = 0;	
		// Detaildaten zu Mitgliedern verarbeiten
		foreach ($playerlist['data'] as $player) {
			$stcard++;
			if ($player['birthyear'] != $year) {
				continue;
			}
			if (strpos($player['firstname'],$vorname) === false) {
				continue;
			}
			$searchByName[] = $player;
			$counter++;
		}
		unset($client);
	}
	catch(SOAPFault $f) {
		if($f->getMessage() == "that is not a valid name" || $f->getMessage() == "that name does not exists") {
			return array(true, "w_wrongName",0);
		}
		return array(false, "e_connectionError");
	}
	return array(true, "m_dewisPlayerSuccess", $counter,$searchByName);
}
?>
