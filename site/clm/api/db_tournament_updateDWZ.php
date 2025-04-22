<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Aktualisieren der NWZ in der Meldeliste eines Turnieres
function clm_api_db_tournament_updateDWZ($id, $group = true)
{
    $id = clm_core::$load->make_valid($id, 0, -1);
    if ($group) {
        $table_main = "#__clm_liga";
        $table_list = "#__clm_meldeliste_spieler";
        $table_list_id = "lid";
        $elo  = ", FIDEelo=?";
    } else {
        $table_main = "#__clm_turniere";
        $table_list = "#__clm_turniere_tlnr";
        $table_list_id = "turnier";
        $elo = ", FIDEcco=?, FIDEelo=?, FIDEid=?, twz=?";
    }
    //CLM parameter auslesen
    $config = clm_core::$db->config();
    $countryversion = $config->countryversion;
    // Datum der zu übernehmenden DWZ Daten anhand der Saison festsetzen, eventuell useAsTWZ auslesen
    $lastDWZUpdate = clm_core::$db->saison->get(clm_core::$access->getSeason())->datum;
    if ($group) {
        $table = clm_core::$db->liga->get($id);
    } else {
        $table = clm_core::$db->turniere->get($id);
    }
    $params = new clm_class_params($table->params);
    $params->set("dwz_date", $lastDWZUpdate);
    $table->params = $params->params();

    if (!$group) {
        $useAsTWZ = $params->get("useAsTWZ", "0");
    }
    // Spieler DWZ Aktualisieren
    $query = 'SELECT a.zps as zps, a.mgl_nr as mgl_nr, a.PKZ as PKZ, b.DWZ as dwz, b.FIDE_Elo as FIDEelo, b.FIDE_ID as FIDEid, b.FIDE_Land as FIDEcco, b.DWZ_Index as dwz_index FROM '.$table_list.' as a'
              .' INNER JOIN #__clm_dwz_spieler as b';
    if ($countryversion == "de") {
        $query .= ' ON a.sid=b.sid AND a.zps=b.zps AND a.mgl_nr = b.Mgl_Nr ';
    } else {
        $query .= ' ON a.sid=b.sid AND a.zps=b.zps AND a.PKZ = b.PKZ ';
    }
    $query .= ' WHERE a.'.$table_list_id.' = '.$id;

    $players = clm_core::$db->loadObjectList($query);

    if ($countryversion == "de") {
        $sql = "UPDATE ".$table_list." SET start_dwz=?, start_I0=?".$elo." WHERE ".$table_list_id."=? AND zps=? AND mgl_nr=?";
    } else {
        $sql = "UPDATE ".$table_list." SET start_dwz=?, start_I0=?".$elo." WHERE ".$table_list_id."=? AND zps=? AND PKZ=?";
    }
    $stmt = clm_core::$db->prepare($sql);
    // Ergebnis Schreiben
    foreach ($players as $value) {
        if ($group) {
            if ($countryversion == "de") {
                $stmt->bind_param('iiiisi', $value->dwz, $value->dwz_index, $value->FIDEelo, $id, $value->zps, $value->mgl_nr);
            } else {
                $stmt->bind_param('iiiiss', $value->dwz, $value->dwz_index, $value->FIDEelo, $id, $value->zps, $value->PKZ);
            }
        } else {
            // TWZ Aktualisieren
            if ($useAsTWZ != 8) {
                $twz = clm_core::$load->gen_twz($useAsTWZ, $value->dwz, $value->FIDEelo);
                if ($countryversion == "de") {
                    $stmt->bind_param('iisiiiisi', $value->dwz, $value->dwz_index, $value->FIDEcco, $value->FIDEelo, $value->FIDEid, $twz, $id, $value->zps, $value->mgl_nr);
                } else {
                    $stmt->bind_param('iisiiiiss', $value->dwz, $value->dwz_index, $value->FIDEcco, $value->FIDEelo, $value->FIDEid, $twz, $id, $value->zps, $value->PKZ);
                }
            } else {
                if ($countryversion == "de") {
                    $stmt->bind_param('iisiiisi', $value->dwz, $value->dwz_index, $value->FIDEcco, $value->FIDEelo, $value->FIDEid, $id, $value->zps, $value->mgl_nr);
                } else {
                    $stmt->bind_param('iisiiiss', $value->dwz, $value->dwz_index, $value->FIDEcco, $value->FIDEelo, $value->FIDEid, $id, $value->zps, $value->PKZ);
                }
            }
        }
        $stmt->execute();
    }
    $stmt->close();

    // Berechne oder Lösche die inoff. DWZ nach dieser Änderung
    if ($group) {
        $params = clm_core::$db->liga->get($id)->params;
    } else {
        $params = clm_core::$db->turniere->get($id)->params;
    }
    $turParams = new clm_class_params($params);
    $autoDWZ = $turParams->get("autoDWZ", 0);
    if ($autoDWZ == 0) {
        clm_core::$api->direct("db_tournament_genDWZ", array($id,$group));
    } elseif ($autoDWZ == 1) {
        clm_core::$api->direct("db_tournament_delDWZ", array($id,$group));
    }
    return array(true, "m_updateDWZSuccess");
}
