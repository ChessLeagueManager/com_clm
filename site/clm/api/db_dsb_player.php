<?php

/**
 * @ Chess League Manager (CLM) Modul
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_dsb_player($spieler = array(), $incl_pd = 0, $file_v = 0)
{
    @set_time_limit(0); // hope
    $sid = clm_core::$access->getSeason();
    $incl_pd = clm_core::$load->make_valid($incl_pd, 0, 0);
    if (!is_array($spieler) || count($spieler) == 0) {
        return array(true, "w_noPlayerToUpdate");
    }
    $counter = 0;
    if ($file_v == 1) {
        $sql = "REPLACE INTO #__clm_dwz_spieler ( `sid`,`ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`, `FIDE_Titel`, `gesperrt`, `joiningdate`, `leavingdate`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    } elseif ($file_v == 2) {
        $sql = "REPLACE INTO #__clm_dwz_spieler ( `sid`,`PKZ`,`ZPS`, `Mgl_Nr`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`, `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`, `FIDE_Titel`, `gesperrt`, `joiningdate`, `leavingdate`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    } else {
        return array(true, "w_noPlayerFileType");
    }
    $stmt = clm_core::$db->prepare($sql);

    $sql01 = "SELECT ZPS, Mgl_Nr, gesperrt, joiningdate, leavingdate FROM #__clm_dwz_spieler"
            ." WHERE sid = ".$sid
            ." AND (gesperrt = 1 OR joiningdate > '1970-01-01' OR leavingdate > '1970-01-01') ";
    $m_gesperrt = clm_core::$db->loadObjectList($sql01);
    $a_gesperrt = array();
    $a_joiningdate = array();
    $a_leavingdate = array();
    if (!is_null($m_gesperrt)) {
        foreach ($m_gesperrt as $msp) {
            //			$a_gesperrt[$msp->ZPS.$msp->Mgl_Nr] = 1;
            if ($msp->gesperrt == 1) {
                $a_gesperrt[$msp->ZPS.$msp->Mgl_Nr] = $msp->gesperrt;
            }
            if ($msp->joiningdate > '1970-01-01') {
                $a_joiningdate[$msp->ZPS.$msp->Mgl_Nr] = $msp->joiningdate;
            }
            if ($msp->leavingdate > '1970-01-01') {
                $a_leavingdate[$msp->ZPS.$msp->Mgl_Nr] = $msp->leavingdate;
            }
        }
    }
    // Detaildaten zu Mitgliedern lesen
    for ($i = 0;$i < count($spieler);$i++) {
        if ($file_v == 1) {
            //ZPS,Mgl-Nr,Status,Spielername,Geschlecht,Spielberechtigung,Geburtsjahr,Letzte-Auswertung,DWZ,Index,FIDE-Elo,FIDE-Titel,FIDE-ID,FIDE-Land
            $einSpieler = str_getcsv($spieler[$i], ",", '"');
            if (count($einSpieler) != 14 || ($einSpieler[2] == 'P' && $incl_pd == 0)) {
                continue;
            }
            $out = clm_core::$load->player_dewis_to_clm($einSpieler[3], null, $einSpieler[1], $einSpieler[4], $einSpieler[12], $einSpieler[13]);

            //			if (array_key_exists($einSpieler[0].(integer)$out[0], $a_gesperrt)) $gesperrt = 1; else $gesperrt = 0;
            if (array_key_exists($einSpieler[0].(int)$out[0], $a_gesperrt)) {
                $gesperrt = $a_gesperrt[$einSpieler[0].(int)$out[0]];
            } else {
                $gesperrt = 0;
            }
            if (array_key_exists($einSpieler[0].(int)$out[0], $a_joiningdate)) {
                $joiningdate = $a_joiningdate[$einSpieler[0].(int)$out[0]];
            } else {
                $joiningdate = '1970-01-01';
            }
            if (array_key_exists($einSpieler[0].(int)$out[0], $a_leavingdate)) {
                $leavingdate = $a_leavingdate[$einSpieler[0].(int)$out[0]];
            } else {
                $leavingdate = '1970-01-01';
            }
            $rating = ($einSpieler[8] != '0' ? $einSpieler[8] : "NULL");
            $rating_index = ($einSpieler[8] != '0' ? $einSpieler[9] : "NULL");
            $stmt->bind_param('isssssssssssssiss', $sid, $einSpieler[0], $out[0], $out[1], $rating, $rating_index, $out[2], $out[3], $einSpieler[6], $einSpieler[10], $out[4], $einSpieler[12], $einSpieler[2], $einSpieler[11], $gesperrt, $joiningdate, $leavingdate);
        } else {
            //ID,VKZ,Mgl-Nr,Status,Spielername,Geschlecht,Spielberechtigung,Geburtsjahr,Letzte-Auswertung,DWZ,Index,FIDE-Elo,FIDE-Titel,FIDE-ID,FIDE-Land
            $einSpieler = str_getcsv($spieler[$i], ",", '"');
            if (count($einSpieler) != 15 || ($einSpieler[3] == 'P' && $incl_pd == 0)) {
                continue;
            }
            $out = clm_core::$load->player_dewis_to_clm($einSpieler[4], null, $einSpieler[2], $einSpieler[5], $einSpieler[13], $einSpieler[14]);

            //			if (array_key_exists($einSpieler[1].(integer)$out[0], $a_gesperrt)) $gesperrt = 1; else $gesperrt = 0;
            if (array_key_exists($einSpieler[1].(int)$out[0], $a_gesperrt)) {
                $gesperrt = $a_gesperrt[$einSpieler[1].(int)$out[0]];
            } else {
                $gesperrt = 0;
            }
            if (array_key_exists($einSpieler[1].(int)$out[0], $a_joiningdate)) {
                $joiningdate = $a_joiningdate[$einSpieler[1].(int)$out[0]];
            } else {
                $joiningdate = '1970-01-01';
            }
            if (array_key_exists($einSpieler[1].(int)$out[0], $a_leavingdate)) {
                $leavingdate = $a_leavingdate[$einSpieler[1].(int)$out[0]];
            } else {
                $leavingdate = '1970-01-01';
            }
            $rating = ($einSpieler[9] != '0' ? $einSpieler[9] : "NULL");
            $rating_index = ($einSpieler[9] != '0' ? $einSpieler[10] : "NULL");
            $stmt->bind_param('issssssssssssssiss', $sid, $einSpieler[0], $einSpieler[1], $out[0], $out[1], $rating, $rating_index, $out[2], $out[3], $einSpieler[7], $einSpieler[11], $out[4], $einSpieler[13], $einSpieler[3], $einSpieler[12], $gesperrt, $joiningdate, $leavingdate);
        }
        $stmt->execute();
        $counter++;
    }
    $stmt->close();
    return array(true, "m_dsbClubSuccess", $counter);
}
