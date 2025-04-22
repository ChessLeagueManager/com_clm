<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
if (!function_exists('mb_str_pad')) {
    function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
    {
        $diff = strlen($input) - mb_strlen($input);
        return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
    }
}
// Eingang: Verband
// Ausgang: Alle Vereine in diesem
function clm_api_db_report_save_sl($liga, $runde, $dg, $paar, $comment, $ko_decision, $homes, $guests, $results, $icomment, $apaar)
{
    //CLM parameter auslesen
    $config = clm_core::$db->config();
    $countryversion = $config->countryversion;
    // Zu den Angaben gibt es ein Mannschaftsturnier/Liga
    $out = clm_core::$api->db_report_sl($liga, $runde, $dg, $paar, $apaar);
    if (!$out[0]) {
        return array(false, $out[1]);
    }
    $out = $out[2];
    // Die Anzahl der Elemente der Arrays stimmen
    if (!is_array($homes) || !is_array($guests) || !is_array($results) || count($results) != count($guests) || count($results) != count($homes) || count($results) != $out["liga"][0]->stamm) {
        return array(false, "e_reportSaveInconsistent");
    }
    /* Start: Kontrolle der Daten */
    $rang = $out["liga"][0]->rang;
    $gast = array();
    for ($i = 0;$i < count($out["gast"]);$i++) {
        if ($countryversion == "de") {
            $gast[] = $out["gast"][$i]->mgl_nr . ':' . $out["gast"][$i]->zps;
        } else {
            $gast[] = $out["gast"][$i]->PKZ . ':' . $out["gast"][$i]->zps;
        }
    }
    $heim = array();
    for ($i = 0;$i < count($out["heim"]);$i++) {
        if ($countryversion == "de") {
            $heim[] = $out["heim"][$i]->mgl_nr . ':' . $out["heim"][$i]->zps;
        } else {
            $heim[] = $out["heim"][$i]->PKZ . ':' . $out["heim"][$i]->zps;
        }
    }
    for ($i = 0;$i < count($results);$i++) {
        if (!is_numeric($results[$i]) || $results[$i] < 0 || $results[$i] > 10) {
            return array(false, "e_reportSaveInconsistent");
        }
        if ($guests[$i] != "-1") {
            $p = array_search($guests[$i], $gast);
            if (is_bool($p)) {
                return array(false, "e_reportSaveInconsistent");
            }
            unset($gast[$p]);
        }
        if ($homes[$i] != "-1") {
            $p = array_search($homes[$i], $heim);
            if (is_bool($p)) {
                return array(false, "e_reportSaveInconsistent");
            }
            unset($heim[$p]);
        }
    }
    /* Ende: Kontrolle der Daten*/
    $comment = clm_core::$load->make_valid($comment, 18, "");
    $icomment = clm_core::$load->make_valid($icomment, 18, "");
    $ko_decision = intval(clm_core::$load->make_valid($ko_decision, 9, 1, array(1, 2, 3, 4, 5)));
    $id = $out["access"];
    $lid = $out["input"]["liga"];
    $rnd = $out["input"]["runde"];
    $paarung = $out["input"]["paar"];
    $dg = $out["input"]["dg"];
    $jid = clm_core::$access->getJid();
    // Alte Meldung falls vorhanden löschen
    if (isset($id[0]->gemeldet)) {
        $query = "DELETE FROM #__clm_rnd_spl" . " WHERE lid = " . $lid . " AND runde = " . $rnd . " AND paar = " . $paarung . " AND dg = " . $dg;
        clm_core::$db->query($query);
    }
    //$out["access"] = clm_core::$db->loadObjectList($Access);
    //Liga-Parameter aufbereiten
    $paramsStringArray = explode("\n", $out["liga"][0]->params);
    $params = array();
    foreach ($paramsStringArray as $value) {
        $ipos = strpos($value, '=');
        if ($ipos !== false) {
            $key = substr($value, 0, $ipos);
            if (substr($key, 0, 2) == "\'") {
                $key = substr($key, 2, strlen($key) - 4);
            }
            if (substr($key, 0, 1) == "'") {
                $key = substr($key, 1, strlen($key) - 2);
            }
            $params[$key] = substr($value, $ipos + 1);
        }
    }
    if (!isset($params['color_order'])) { //Standardbelegung
        $params['color_order'] = '1';
    }
    switch ($params['color_order']) {
        case '1':
            $colorstr = '01';
            break;
        case '2':
            $colorstr = '10';
            break;
        case '3':
            $colorstr = '0110';
            break;
        case '4':
            $colorstr = '1001';
            break;
        case '5':
            $colorstr = '00';
            break;
        case '6':
            $colorstr = '11';
            break;
        default:
            $colorstr = '01';
    }
    // Datensätze in Spielertabelle schreiben
    $y1 = 0;
    $hmpunkte = 0;
    $gmpunkte = 0;
    $hwpunkte = 0;
    $gwpunkte = 0;
    $hkl = 0;
    $gkl = 0;
    for ($y = 0;$y < count($homes);$y++) {
        $heim = $homes[$y];
        $gast = $guests[$y];
        $ergebnis = $results[$y];

        // Heim/Gast Spieler ist bei --kein Spieler-- auf "-1" (String) gesetzt
        $teil_heim = $heim != "-1" ? explode(":", $heim) : [null,null];
        $teil_gast = $gast != "-1" ? explode(":", $gast) : [null,null];
        if ($countryversion == "de") {
            $hmgl = $teil_heim[0];
            $gmgl = $teil_gast[0];
            $hPKZ = '';
            $gPKZ = '';
        } else {
            $hmgl = 0;
            $gmgl = 0;
            $hPKZ = $teil_heim[0];
            $gPKZ = $teil_gast[0];
        }
        $hzps = $teil_heim[1];
        $gzps = $teil_gast[1];
        if ($ergebnis > 2 and $ergebnis < 9) {
            $kampflos = 1;
        } else {
            $kampflos = 0;
        }
        if ($ergebnis == 0) {
            $erg_h = $out["liga"][0]->nieder + $out["liga"][0]->antritt;
            $erg_g = $out["liga"][0]->sieg + $out["liga"][0]->antritt;
        }
        if ($ergebnis == 1) {
            $erg_h = $out["liga"][0]->sieg + $out["liga"][0]->antritt;
            $erg_g = $out["liga"][0]->nieder + $out["liga"][0]->antritt;
        }
        if ($ergebnis == 2) {
            $erg_h = $out["liga"][0]->remis + $out["liga"][0]->antritt;
            $erg_g = $out["liga"][0]->remis + $out["liga"][0]->antritt;
        }
        if ($ergebnis == 3) {
            $erg_h = $out["liga"][0]->antritt;
            $erg_g = $out["liga"][0]->antritt;
        }
        if ($ergebnis == 4) {
            $erg_h = 0;
            $erg_g = $out["liga"][0]->sieg + $out["liga"][0]->antritt;
        }
        if ($ergebnis == 5) {
            $erg_h = $out["liga"][0]->sieg + $out["liga"][0]->antritt;
            $erg_g = 0;
        }
        if ($ergebnis == 6) {
            $erg_h = 0;
            $erg_g = 0;
        }
        if ($ergebnis == 7) {
            $erg_h = 0;
            $erg_g = 0;
        }
        if ($ergebnis == 8) {
            $erg_h = 0;
            $erg_g = 0;
        }
        if ($ergebnis == 9) {
            $erg_h = $out["liga"][0]->nieder + $out["liga"][0]->antritt;
            $erg_g = $out["liga"][0]->remis + $out["liga"][0]->antritt;
        }
        if ($ergebnis == 10) {
            $erg_h = $out["liga"][0]->remis + $out["liga"][0]->antritt;
            $erg_g = $out["liga"][0]->nieder + $out["liga"][0]->antritt;
        }
        $weiss = substr($colorstr, $y1, 1);
        if ($weiss == 1) {
            $schwarz = 0;
        } else {
            $schwarz = 1;
        }
        $y1++;
        if ($y1 >= strlen($colorstr)) {
            $y1 = 0;
        }
        // $sid macht hier keinen Sinn, die ID der Liga ist bereits eindeutig!
        $sid = $out["liga"][0]->sid;
        if (is_null($hmgl) or $hmgl == '') {
            $hmgl = '0';
        }
        if (is_null($gmgl) or $gmgl == '') {
            $gmgl = '0';
        }
        $query = "INSERT INTO #__clm_rnd_spl " . " ( `sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler`, `PKZ` " . " , `zps`, `gegner`, `gPKZ`, `gzps`, `ergebnis` , `kampflos`, `punkte`, `gemeldet`) "
                ." VALUES ('$sid','$lid','$rnd','$paarung','$dg','" . $out["paar"][0]->htln . "','" . ($y + 1) . "',1,'$weiss','$hmgl','$hPKZ','$hzps'," . " '$gmgl','$gPKZ','$gzps','$ergebnis', '$kampflos','$erg_h','$jid') "
                .	  " , ('$sid','$lid','$rnd','$paarung','$dg','" . $out["paar"][0]->gtln . "','" . ($y + 1) . "','0','$schwarz','$gmgl','$gPKZ','$gzps'," . " '$hmgl','$hPKZ','$hzps','$ergebnis', '$kampflos','$erg_g','$jid') ";
        clm_core::$db->query($query);
        $hmpunkte += $erg_h;
        $gmpunkte += $erg_g;
        $hwpunkte += (($out["liga"][0]->stamm + 1 - ($y + 1)) * $erg_h);
        $gwpunkte += (($out["liga"][0]->stamm + 1 - ($y + 1)) * $erg_g);
        if ($erg_h == 0 && $kampflos == 1) {
            $hkl++;
        }
        if ($erg_g == 0 && $kampflos == 1) {
            $gkl++;
        }
    }

    $hkampflos = 0;
    $gkampflos = 0;
    // Mannschaftspunkte Heim / Gast verteilen
    // Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
    if ($out["liga"][0]->sieg_bed == 1) {
        if ($hmpunkte > $gmpunkte) {
            $hman_punkte = $out["liga"][0]->man_sieg;
            $gman_punkte = $out["liga"][0]->man_nieder;
            $hergebnis = 1;
            $gergebnis = 0;
        }
        if ($hmpunkte == $gmpunkte) {
            $hman_punkte = $out["liga"][0]->man_remis;
            $gman_punkte = $out["liga"][0]->man_remis;
            $hergebnis = 2;
            $gergebnis = 2;
        }
        if ($hmpunkte < $gmpunkte) {
            $hman_punkte = $out["liga"][0]->man_nieder;
            $gman_punkte = $out["liga"][0]->man_sieg;
            $hergebnis = 0;
            $gergebnis = 1;
        }
    }
    // erweiterter Standard : mehr als die Hälfte der BP -> Sieg, Hälfte der BP -> halbe MP Zahl
    if ($out["liga"][0]->sieg_bed == 2) {
        if ($hmpunkte > (($out["liga"][0]->stamm * ($out["liga"][0]->sieg + $out["liga"][0]->antritt)) / 2)) {
            $hman_punkte = $out["liga"][0]->man_sieg;
            $hergebnis = 1;
        }
        if ($hmpunkte == (($out["liga"][0]->stamm * ($out["liga"][0]->sieg + $out["liga"][0]->antritt)) / 2)) {
            $hman_punkte = $out["liga"][0]->man_remis;
            $hergebnis = 2;
        }
        if ($hmpunkte < (($out["liga"][0]->stamm * ($out["liga"][0]->sieg + $out["liga"][0]->antritt)) / 2)) {
            $hman_punkte = $out["liga"][0]->man_nieder;
            $hergebnis = 0;
        }
        if ($gmpunkte > (($out["liga"][0]->stamm * ($out["liga"][0]->sieg + $out["liga"][0]->antritt)) / 2)) {
            $gman_punkte = $out["liga"][0]->man_sieg;
            $gergebnis = 1;
        }
        if ($gmpunkte == (($out["liga"][0]->stamm * ($out["liga"][0]->sieg + $out["liga"][0]->antritt)) / 2)) {
            $gman_punkte = $out["liga"][0]->man_remis;
            $gergebnis = 2;
        }
        if ($gmpunkte < (($out["liga"][0]->stamm * ($out["liga"][0]->sieg + $out["liga"][0]->antritt)) / 2)) {
            $gman_punkte = $out["liga"][0]->man_nieder;
            $gergebnis = 0;
        }
    }
    // Antrittspunkte addieren falls angetreten
    if ($out["liga"][0]->stamm > $hkl) {
        $hman_punkte = $hman_punkte + $out["liga"][0]->man_antritt;
    } else {
        $hkampflos = 1;
        $gkampflos = 1;
    }
    if ($out["liga"][0]->stamm > $gkl) {
        $gman_punkte = $gman_punkte + $out["liga"][0]->man_antritt;
    } else {
        $hkampflos = 1;
        $gkampflos = 1;
    }
    if ($hkampflos == 1) {
        if ($hergebnis == 0) {
            $hergebnis = 4;
        }
        if ($hergebnis == 1) {
            $hergebnis = 5;
        }
        if ($hergebnis == 2) {
            $hergebnis = 6;
        }
    }
    if ($gkampflos == 1) {
        if ($gergebnis == 0) {
            $gergebnis = 4;
        }
        if ($gergebnis == 1) {
            $gergebnis = 5;
        }
        if ($gergebnis == 2) {
            $gergebnis = 6;
        }
    }

    // Datum und Uhrzeit für Meldung
    //$now = date('Y-m-d H:i:s');
    $now = clm_core::$cms->getNowDate();
    // Für Heimmannschaft updaten
    $query = "UPDATE #__clm_rnd_man" . " SET gemeldet = " . $jid . " , zeit = '$now'" . " , ergebnis = " . $hergebnis . " , kampflos = " . $hkampflos
        . " , brettpunkte = " . $hmpunkte . " , manpunkte = " . $hman_punkte . " , wertpunkte = " . $hwpunkte . " , comment = '" . $comment . "', icomment = '" . $icomment . "'"
        . " WHERE lid = " . $lid . " AND runde = " . $rnd . " AND paar = " . $paarung . " AND dg = " . $dg . " AND heim = 1 ";
    clm_core::$db->query($query);
    // Für Gastmannschaft updaten
    $query = "UPDATE #__clm_rnd_man" . " SET gemeldet = " . $jid . " , zeit = '$now'" . " , ergebnis = " . $gergebnis . " , kampflos = " . $gkampflos
        . " , brettpunkte = " . $gmpunkte . " , manpunkte = " . $gman_punkte . " , wertpunkte = " . $gwpunkte . " , comment = '" . $comment . "', icomment = '" . $icomment . "'"
        . " WHERE lid = " . $lid . " AND runde = " . $rnd . " AND paar = " . $paarung . " AND dg = " . $dg . " AND heim = 0 ";
    clm_core::$db->query($query);
    //mtmt start
    if ($out["liga"][0]->runden_modus == 4 or $out["liga"][0]->runden_modus == 5) { // KO Turnier
        if (($out["liga"][0]->runden_modus == 4) or ($out["liga"][0]->runden_modus == 5 and $rnd < $out["liga"][0]->runden)) { // KO Turnierif ($ko_decision == 1) {
            if ($ko_decision == 1) {
                if ($hmpunkte > $gmpunkte) {
                    $ko_par = 2;
                } // Sieger Heim nach Brettpunkte
                elseif ($hmpunkte < $gmpunkte) {
                    $ko_par = 3;
                } // Sieger Gast nach Brettpunkte
                elseif ($hwpunkte > $gwpunkte) {
                    $ko_par = 2;
                } // Sieger Heim nach Wertpunkte
                elseif ($hwpunkte < $gwpunkte) {
                    $ko_par = 3;
                } // Sieger Gast nach Wertpunkte
                else {
                    $ko_par = 3; // Sieger Gast nach Computer --> Nacharbeit durch TL
                }
            } elseif ($ko_decision == 2) {
                $ko_par = 2;
            } // Sieger Heim nach Blitz-Entscheid
            elseif ($ko_decision == 4) {
                $ko_par = 2;
            } // Sieger Heim nach Los-Entscheid
            else {
                $ko_par = 3;
            } // Sieger Gast nach Blitz-,Los-Entscheid
            if ($ko_par == 2) {
                $ko_heim = $rnd;
                $ko_gast = $rnd - 1;
            } else {
                $ko_heim = $rnd - 1;
                $ko_gast = $rnd;
            }
            // Für Heimmannschaft updaten
            $query = "UPDATE #__clm_mannschaften" . " SET rankingpos = " . $ko_heim . " WHERE liga = " . $lid . " AND tln_nr = " . $out["paar"][0]->htln;
            clm_core::$db->query($query);
            // Für Gastmannschaft updaten
            $query = "UPDATE #__clm_mannschaften" . " SET rankingpos = " . $ko_gast . " WHERE liga = " . $lid . " AND tln_nr = " . $out["paar"][0]->gtln;
            clm_core::$db->query($query);
        }
        // Für Heimmannschaft updaten
        $query = "UPDATE #__clm_rnd_man" . " SET ko_decision = " . $ko_decision . " , comment = '" . $comment . "', icomment = '" . $icomment . "'" . " WHERE lid = " . $lid . " AND runde = " . $rnd . " AND paar = " . $paarung . " AND dg = " . $dg . " AND heim = 1 ";
        clm_core::$db->query($query);
        ;
        // Für Gastmannschaft updaten
        $query = "UPDATE #__clm_rnd_man" . " SET ko_decision = " . $ko_decision . " , comment = '" . $comment . "', icomment = '" . $icomment . "'" . " WHERE lid = " . $lid . " AND runde = " . $rnd . " AND paar = " . $paarung . " AND dg = " . $dg . " AND heim = 0 ";
        clm_core::$db->query($query);
    }
    /*********************************************************/
    /******** Umgebungsdaten falls nötig korrigieren *********/
    /*********************************************************/
    // errechnte/aktualisiere Rangliste & inoff. DWZ falls eingestellt (autoDWZ, autoRANKING)
    clm_core::$api->db_tournament_auto($liga, true, true);

    /*********************************************************/
    /* Mails werden nicht versandt */
    /*********************************************************/

    return array(true, "m_reportSaveSuccess");
}
