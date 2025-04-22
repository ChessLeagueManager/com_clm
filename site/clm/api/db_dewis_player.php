<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_dewis_player($zps = - 1, $incl_pd = 0, $mgl_nr = array())
{
    @set_time_limit(0); // hope
    $source = "https://dwz.svw.info/services/files/dewis.wsdl";
    $sid = clm_core::$access->getSeason();
    //CLM parameter auslesen
    $config = clm_core::$db->config();
    $dewis_import_delay = $config->dewis_import_delay;

    $zps = clm_core::$load->make_valid($zps, 8, "");
    $incl_pd = clm_core::$load->make_valid($incl_pd, 0, 0);
    $counter = 0;
    if (strlen($zps) != 5) {
        return array(true, "e_wrongZPSFormat",$counter);
    }

    $sql01 = "SELECT Mgl_Nr, gesperrt, joiningdate, leavingdate FROM #__clm_dwz_spieler"
            ." WHERE sid = ".$sid
            ." AND ZPS = '".$zps."'"
            ." AND (gesperrt = 1 OR joiningdate > '1970-01-01' OR leavingdate > '1970-01-01') ";
    $m_gesperrt = clm_core::$db->loadObjectList($sql01);
    $a_gesperrt = array();
    $a_joiningdate = array();
    $a_leavingdate = array();
    if (!is_null($m_gesperrt)) {
        foreach ($m_gesperrt as $msp) {
            if ($msp->gesperrt == 1) {
                $a_gesperrt[$msp->Mgl_Nr] = $msp->gesperrt;
            }
            if ($msp->joiningdate > '1970-01-01') {
                $a_joiningdate[$msp->Mgl_Nr] = $msp->joiningdate;
            }
            if ($msp->leavingdate > '1970-01-01') {
                $a_leavingdate[$msp->Mgl_Nr] = $msp->leavingdate;
            }
        }
    }

    // SOAP Webservice
    try {
        $client = clm_core::$load->soap_wrapper($source);

        // VKZ des Vereins --> Vereinsliste

        usleep($dewis_import_delay);

        $unionRatingList = $client->unionRatingList($zps);
        $str = '';
        $sql = "REPLACE INTO #__clm_dwz_spieler ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`, `FIDE_Titel`, `gesperrt`, `joiningdate`, `leavingdate`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = clm_core::$db->prepare($sql);
        // Detaildaten zu Mitgliedern lesen
        foreach ($unionRatingList->members as $m) {
            if ($m->state == 'P' && $incl_pd == 0) {
                continue;
            }
            if (count($mgl_nr) != 0) {
                $update = false;
                foreach ($mgl_nr as $mgl_nr1) {
                    if (intval($mgl_nr1) == intval($m->membership)) {
                        $update = true;
                        break;
                    }
                }
                if (!$update) {
                    continue;
                }
            }

            usleep($dewis_import_delay);

            $tcard = $client->tournamentCardForId($m->pid);
            $out = clm_core::$load->player_dewis_to_clm($m->surname, $m->firstname, $m->membership, $m->gender, $m->idfide, $tcard->member->fideNation);

            if (array_key_exists((int)$out[0], $a_gesperrt)) {
                $gesperrt = $a_gesperrt[(int)$out[0]];
            } else {
                $gesperrt = 0;
            }
            if (array_key_exists((int)$out[0], $a_joiningdate)) {
                $joiningdate = $a_joiningdate[(int)$out[0]];
            } else {
                $joiningdate = '1970-01-01';
            }
            if (array_key_exists((int)$out[0], $a_leavingdate)) {
                $leavingdate = $a_leavingdate[(int)$out[0]];
            } else {
                $leavingdate = '1970-01-01';
            }
            $rating = ($m->rating != '0' ? $m->rating : "NULL");
            $rating_index = ($m->rating != '0' ? $m->ratingIndex : "NULL");
            $state = ($m->state != '' ? $m->state : "A");
            $stmt->bind_param('issssssssssssssiss', $sid, $zps, $out[0], $m->pid, $out[1], $rating, $rating_index, $out[2], $out[3], $m->yearOfBirth, $m->elo, $out[4], $m->idfide, $state, $m->fideTitle, $gesperrt, $joiningdate, $leavingdate);
            $result = $stmt->execute();
            if ($result === false) {
                $str .= " ".$zps."-".$out[0];
            }
            $counter++;
            unset($tcard);
        }
        $stmt->close();
        unset($unionRatingList);
        unset($client);
    } catch (SOAPFault $f) {
        if ($f->getMessage() == "that is not a valid union id" || $f->getMessage() == "that union does not exists") {
            return array(true, "w_wrongZPS",0);
        }
        return array(false, "e_connectionError");
    }
    return array(true, "m_dewisPlayerSuccess".$str, $counter);
}
