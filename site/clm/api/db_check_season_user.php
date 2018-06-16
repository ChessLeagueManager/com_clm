<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2018 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_check_season_user($sid = - 1) {
	$asid = clm_core::$access->getSeason(); // aktuelle Saison
	$auser = clm_core::$access->getId(); // aktueller User

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$conf_view_archive	= $config->view_archive;
 
	// Check aktiv?
	if ($conf_view_archive == 0) {
		return true;
	}
	// Check aktuelle Saison?
	if (intval($sid) == intval($asid)) {
		return true;
	}
	// Check User angemeldet?
	if (intval($auser) > 0) {
		return true;
	}
	return false;
}
?>
