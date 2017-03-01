<?php
// Eingang: Verband
// Ausgang: Alle Vereine in diesem
function clm_api_db_be_menu() {
	$clmAccess = clm_core::$access;
	$access = array();
	$access["BE_season_general"] = $clmAccess->access('BE_season_general');
	$access["BE_tournament_create"] = $clmAccess->access('BE_tournament_create');
	$access["BE_event_general"] = $clmAccess->access('BE_event_general');
	$access["BE_tournament_general"] = $clmAccess->access('BE_tournament_general');
	$access["BE_league_general"] = $clmAccess->access('BE_league_general');
	$access["BE_league_create"] = $clmAccess->access('BE_league_create');
	$access["BE_club_general"] = $clmAccess->access('BE_club_general');
	$access["BE_club_create"] = $clmAccess->access('BE_club_create');
	$access["BE_club_edit_member"] = $clmAccess->access('BE_club_edit_member');
	$access["BE_user_general"] = $clmAccess->access('BE_user_general');
	$access["BE_team_general"] = $clmAccess->access('BE_team_general');
	$access["BE_team_create"] = $clmAccess->access('BE_team_create');
	$access["BE_config_general"] = $clmAccess->access('BE_config_general');
	$access["BE_swt_general"] = $clmAccess->access('BE_swt_general');
	$access["BE_pgn_general"] = $clmAccess->access('BE_pgn_general');
	$access["BE_database_general"] = $clmAccess->access('BE_database_general');
	$access["BE_dewis_general"] = $clmAccess->access('BE_dewis_general');
	$access["BE_teamtournament_general"] = $clmAccess->access('BE_teamtournament_general');
	$access["BE_teamtournament_create"] = $clmAccess->access('BE_teamtournament_create');
	// aktuelle Versionsnummern auslesen !
	$status = array();
	if (ini_get('allow_url_fopen') == 0) {
		$status["fopen"] = false;
	} else {
		$status["fopen"] = true;
	}
	if (class_exists("SOAPClient")) {
		$status["soap"] = true;
	} else {
		$status["soap"] = false;
	}
	$config = clm_core::$db->config();
	$status["version"] = clm_core::$load->version();
	$status["version"] = $status["version"][0];
	if ($config->status_cache_content == "" || $config->status_cache < time() - 1800) {
		if ($status["fopen"]) {
			$ctx = stream_context_create(array('http'=>
    			array(
     			   'timeout' => 10,
   			)
			));

			$jlang = JFactory::getLanguage();
			//if (!$fp = @file_get_contents("http://www.chessleaguemanager.de/clm/updateServer/status." . clm_core::$db->config()->language, false, $ctx)) {
			if (!$fp = @file_get_contents("http://www.chessleaguemanager.de/clm/updateServer/status." . $jlang->getTag(), false, $ctx)) {
				$status["content"] = "";
			} else {
				$status["content"] = $fp;
			}
		}
		$config->status_cache = time();
		$config->status_cache_content = $status["content"];
	} else {
		$status["content"] = str_replace(array('\\r\\n', '\\n'), "\n", $config->status_cache_content);
	}
	$status["lastUpdate"] = $config->status_cache;
	return array(true, "", array($access, $status));
}
?>
