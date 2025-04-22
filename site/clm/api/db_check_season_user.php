<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2018 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_check_season_user($sid = - 1)
{
    $asid = clm_core::$access->getSeason(); // aktuelle Saison
    $auser = clm_core::$access->getId(); // aktueller User

    // Konfigurationsparameter auslesen
    $config = clm_core::$db->config();
    $conf_view_archive	= $config->view_archive;

    // Check aktiv? Nein, alle Besucher sehen alle Saisons
    if ($conf_view_archive == 0) {
        return true;
    }
    // Check aktiv? Ja, nur angemeldete Benutzer sehen die Saisons im Archiv
    if ($conf_view_archive == 1) {
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
    // Check aktiv? Ja, nur angemeldete Benutzer sehen die Saisons im Archiv
    // aber die neueste Saison im Archiv ist sichtbar für alle
    if ($conf_view_archive == 2) {
        // Check aktuelle Saison?
        if (intval($sid) == intval($asid)) {
            return true;
        }
        // Bestimmung neueste Saison im Archiv
        $query = ' SELECT id,name FROM #__clm_saison
					WHERE published = 1 AND archiv = 1
					ORDER BY id DESC LIMIT 1;' ;
        $season	= clm_core::$db->loadObject($query);
        // Check neueste Saison des Archiv?
        if (intval($sid) == intval($season->id)) {
            return true;
        }
        // Check User angemeldet?
        if (intval($auser) > 0) {
            return true;
        }
        return false;
    }

    return false;
}
