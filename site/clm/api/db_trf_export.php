<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
/**
* trf-Export eines Turniers
 */

function clm_api_db_trf_export($turnierid, $group = false, $test = false, $clmextensions = false, $ratingexport = false)
{
    $lang = clm_core::$lang->draw;
    $test = false;
    if ($test) {
        $debug = 1;
    } else {
        $debug = 0;
    }
    $new_ID = 0;
    if ($debug > 0) {
        echo "<br><br>-- allgemeine Daten --";
    }
    if ($debug > 0) {
        echo "<br><br>Turnier: ".$turnierid;
    } 		//echo "<br>end"; //die();

    //----------------- functions --------------------------------
    //
    function ausgeschr($line)
    {
        $pat = array();
        $pat[] = "/\&auml;/";
        $pat[] = "/\&ouml;/";
        $pat[] = "/\&uuml;/";
        $pat[] = "/\&szlig;/";
        $pat[] = "/\&Auml;/";
        $pat[] = "/\&Ouml;/";
        $pat[] = "/\&Uuml;/";
        $pat[] = "/\&aacute;/";
        $pat[] = "/\&eacute;/";
        $pat[] = "/ä/";
        $pat[] = "/ö/";
        $pat[] = "/ü/";
        $pat[] = "/ß/";
        $pat[] = "/Ä/";
        $pat[] = "/Ö/";
        $pat[] = "/Ü/";
        $pat[] = "/á/";
        $pat[] = "/é/";
        $rep = array();
        $rep[] = "ae";
        $rep[] = "oe";
        $rep[] = "ue";
        $rep[] = "ss";
        $rep[] = "Ae";
        $rep[] = "Oe";
        $rep[] = "Ue";
        $rep[] = "a";
        $rep[] = "e";
        $rep[] = "ae";
        $rep[] = "oe";
        $rep[] = "ue";
        $rep[] = "ss";
        $rep[] = "Ae";
        $rep[] = "Oe";
        $rep[] = "Ue";
        $rep[] = "a";
        $rep[] = "e";
        return preg_replace($pat, $rep, $line);
    }

    // Testfunktionen (Anzeigen)
    function p_players($players, $snr, $parm, $text = 'Teilnehmer')
    {
        if ($parm == 'K' or $parm == 'G'  or $parm == 'F') {
            if ($debug > 0) {
                echo "<br><br>$text: ";
            }
            foreach ($players as $player) {
                if ($debug > 0) {
                    echo "<br>- Snr: ".$player['snr']." Rang: ".$player['rankingPos']." ".$player['name']." Gruppe: ".$player['grupp']." Gegner: ".$player['gegner'];
                }
                if ($parm == 'G') {
                    if ($debug > 0) {
                        echo " Farbdiff: ".$player['farbdiff']." Absolute Farbe: ".$player['sollfabsolut']." Starke Farbe: ".
                            $player['sollfstark']." Schwache Farbe: ".$player['sollfschwach']." Farbfolge: ".$player['color']." Sollfarbe: ".$player['sollfarbe'];
                    }
                }
                if ($parm == 'F') {
                    if ($debug > 0) {
                        echo " GPkt: ".$player['sum_punkte'];
                    }
                    for ($i = 1; $i < 50; $i++) {
                        if (!isset($player['p_round'][$i])) {
                            break;
                        }
                        if ($debug > 0) {
                            echo "  nach Runde $i  Pkt:".$player['p_round'][$i]." Fl.:".$player['floater'][$i];
                        }
                    }
                }
            }
        } elseif ($parm == 'E') {
            if ($debug > 0) {
                echo "<br><br>$text (".$snr."): ";
            }
            if ($debug > 0) {
                echo "<br>- Spieler: ".$players[$snr]['snr']." ".$players[$snr]['name']." Gruppe: ".$players[$snr]['grupp']." Gegner: ".$players[$snr]['gegner'];
            }
        } else { // parm = 'A'
            if ($debug > 0) {
                echo "<br><br>$text : ";
            }
            foreach ($snr as $snr1) {
                if ($debug > 0) {
                    echo "<br>- Spieler: ".$players[$snr1]['snr']." ".$players[$snr1]['name']." Gruppe: ".$players[$snr1]['grupp']." Gegner: ".$players[$snr1]['gegner'];
                }
            }
        }
    }

    function p_ranglist($ranglist, $parm, $text = 'Rangliste')
    {
        //if ($parm != 'E') {
        if ($debug > 0) {
            echo "<br><br>$text: ";
        }
        foreach ($ranglist as $player) {
            if ($debug > 0) {
                echo "<br>- Rang: ".$player['rankingPos']." Snr: ".$player['snr']." ".$player['name']." Pkt: ".$player['sum_punkte']." TWZ: ".
                    $player['twz']." Gruppe: ".$player['grupp']." Gegner: ".$player['gegner'];
            }
            if ($parm == 'G') {
                if ($debug > 0) {
                    echo " Farbdiff: ".$player['farbdiff']." Absolutee Farbe: ".$player['sollfabsolut']." Starke Farbe: ".$player['sollfstark'].
                        " Schwache Farbe: ".$player['sollfschwach']." Farbfolge: ".$player['color'];
                }
                if ($debug > 0) {
                    echo " Verein: ".$player['verein']." ZPS: ".$player['zps'];
                }
            }
        }
    }

    function p_pairing($pairings, $players, $parm, $p_nr = '', $text = 'Paarung(en)')
    {
        if ($p_nr == 0) {
            $p_nr = count($pairings);
        }
        // echo "<br><br>Parm: ".$parm."  Anz:".$p_nr."  "; var_dump($pairings);

        if ($parm != 'E') {
            if (count($pairings) == 0) {
                if ($debug > 0) {
                    echo "<br><br>$text: ";
                }
                if ($debug > 0) {
                    echo "<br>keine Paarung! ";
                }
            } else {
                if ($debug > 0) {
                    echo "<br><br>$text (Anz: $p_nr): ";
                }
                foreach ($pairings as $pairing) {
                    if ($debug > 0) {
                        echo "<br>Paarung: ".$pairing['brett']."  Spieler: ".$pairing['wsnr']." ".$pairing['wname'].
                            "(".$players[$pairing['wsnr']]['rankingPos'].$players[$pairing['wsnr']]['sollfarbe'].")".
                            "  -  Gegner: ".$pairing['bsnr']." ".$pairing['bname'];
                    }
                    if ($pairing['bsnr'] > 0) {
                        if ($debug > 0) {
                            echo "(".$players[$pairing['bsnr']]['rankingPos'].$players[$pairing['bsnr']]['sollfarbe'].")";
                        }
                    } else {
                        if ($debug > 0) {
                            echo "(0)";
                        }
                    }
                }
            }
        } else {
            if ($debug > 0) {
                echo "<br><br>$text ".$p_nr.": ";
            }
            if ($debug > 0) {
                echo "<br>Paarung: ".$pairings[$p_nr]['brett']." Spieler: ".$pairings[$p_nr]['wsnr']." ".$pairings[$p_nr]['wname'].
                    "  -  Gegner: ".$pairings[$p_nr]['bsnr']." ".$pairings[$p_nr]['bname'];
            }
        }
    }

    function p_perm($perm, $p_nr, $parm)
    {
        if ($parm != 'E') {
            if ($debug > 0) {
                echo "<br><br>Permutationen (Anz:".$p_nr."): ";
            }
            foreach ($perm as $perm1) {
                //if ($perm1['erg'] === true) $d_erg = " Ja "; elseif ($perm1['erg'] === false) $d_erg = "Nein"; else $d_erg = "?";
                if ($debug > 0) {
                    echo "<br>Index: ".$perm1['relkrit']." / ".$perm1['austausch']." / ".$perm1['nr']."  Anz Paarungen (h): ".
                        $perm1['counth']."  Anz Paarungen: ".$perm1['count']."  Pktdiff: ".$perm1['pktdiff']."  Farbdiff: ".$perm1['farbdiff'];
                }
            }
        } else {
            if ($debug > 0) {
                echo "<br><br>Permutation ".$p_nr.": ";
            }
            //if ($perm1['erg'] === true) $d_erg = " Ja "; elseif ($perm1[erg] === false) $d_erg = "Nein"; else $d_erg = "?";
            if ($debug > 0) {
                echo "<br>Index: ".$perm1['relkrit']." / ".$perm1['austausch']." / ".$perm1['nr']."  Anz Paarungen (h): ".
                    $perm1['counth']."  Anz Paarungen: ".$perm1['count']."  Pktdiff: ".$perm1['pktdiff']."  Farbdiff: ".$perm1['farbdiff'];
            }
        }
    }

    // Aufbau der allgemeinen Turnierzeilen als Array
    function common_lines($turnier, $players, $rundentermine)
    {
        $config = clm_core::$db->config();
        $query = "SELECT s.spielername,s.fide_id FROM #__clm_dwz_spieler s,#__clm_user u,#__clm_turniere t WHERE" .
               " s.zps=u.zps AND s.mgl_nr=u.mglnr AND t.tl=u.jid AND t.sid=s.sid AND u.sid=t.sid AND t.id=" . $turnier->id . ";";
        $turnierleiterliste = clm_core::$db->loadObjectList($query);
        $turnierleiter = "";
        if ($turnierleiterliste[0]->spielername != "") {
            $turnierleiter = $turnierleiterliste[0]->spielername . " (" . $turnierleiterliste[0]->fide_id . ")";
        }

        $lines = array();
        $lines[] 	= '012 '.utf8_decode($turnier->name);
        $lines[]	= '022 '.utf8_decode($config->tourn_town);
        $lines[]	= '032 '.utf8_decode($config->tourn_country);
        $lines[] 	= '042 '.substr($turnier->dateStart, 8, 2).'/'.substr($turnier->dateStart, 5, 2).'/'.substr($turnier->dateStart, 0, 4);
        $lines[] 	= '052 '.substr($turnier->dateEnd, 8, 2).'/'.substr($turnier->dateEnd, 5, 2).'/'.substr($turnier->dateEnd, 0, 4);
        $lines[] 	= '062 '.count($players);
        $itwz = 0;
        for ($i = 0; $i <  count($players); $i++) {
            if ($players[$i]->twz > 0) {
                $itwz++;
            }
        }
        $lines[] 	= '072 '.$itwz;
        $lines[] 	= '082 '.'0';
        if ($config->tourn_accel == 0) {
            $lines[] 	= '092 '.'Individual Swiss Dutch';
        } else {
            $lines[] 	= '092 '.'Individual Swiss Dutch (' . $config->tourn_accel_rounds . " accelerated rounds with " . $config->tourn_accel_groups . " groups)";
        }
        if ($turnierleiterliste[0]->spielername != "") {
            $lines[] 	= '102 '.ausgeschr($turnierleiter);
        }
        // Wie lange dauern 60 Züge ?
        $totalmoves = 60;
        $totaltime = 0;
        if ($totalmoves > 0) {
            // in Sekunden !
            $totaltime = 60 * $config->tourn_tc_ph_1 + min($totalmoves, $config->tourn_tc_ph_2) * $config->tourn_tc_ph_3;
            $totalmoves = $totalmoves - min($totalmoves, $config->tourn_tc_ph_2);
        }
        if ($totalmoves > 0) {
            // in Sekunden !
            $totaltime = $totaltime + (60 * $config->tourn_tc_ph_4 + min($totalmoves, $config->tourn_tc_ph_5) * $config->tourn_tc_ph_6);
            $totalmoves = $totalmoves - min($totalmoves, $config->tourn_tc_ph_5);
        }
        if ($totalmoves > 0) {
            // in Sekunden !
            $totaltime = $totaltime + (60 * $config->tourn_tc_ph_7 + min($totalmoves, $config->tourn_tc_ph_8) * $config->tourn_tc_ph_9);
            $totalmoves = $totalmoves - min($totalmoves, $config->tourn_tc_ph_8);
        }
        if ($totaltime >= 7200) {
            $tctype = "Standard:";
        } elseif ($totaltime >= 600) {
            $tctype = "Rapid:";
        } elseif ($totaltime >= 180) {
            $tctype = "Blitz:";
        } else {
            $tctype = "Lightning:";
        }
        $line = '122 ' . $tctype . ' ';
        $line = $line . $config->tourn_tc_ph_1 . " min";
        if ($config->tourn_tc_mode == 0) {
            if ($config->tourn_tc_ph_2 != 99) {
                $line = $line . "/" . $config->tourn_tc_ph_2 . " mv" . ", " . $config->tourn_tc_ph_4 . " min";
                if ($config->tourn_tc_ph_5 != 99) {
                    $line = $line . "/" . $config->tourn_tc_ph_5 . " mv" . ", " . $config->tourn_tc_ph_7 . " min";
                } else {
                    $line = $line . " for rest of game";
                }
            } else {
                $line = $line . " per game";
            }
        } elseif ($config->tourn_tc_mode == 1) {
            if ($config->tourn_tc_ph_2 != 99) {
                $line = $line . "/" . $config->tourn_tc_ph_2 . " mv + " . $config->tourn_tc_ph_3 . " sec/mv increment, " . $config->tourn_tc_ph_4 . " min";
                if ($config->tourn_tc_ph_5 != 99) {
                    $line = $line . "/" . $config->tourn_tc_ph_5 . " mv + " . $config->tourn_tc_ph_6 . " sec/mv increment, " . $config->tourn_tc_ph_7 . " min";
                    if ($config->tourn_tc_ph_8 != 99) {
                        $line = $line . "/" . $config->tourn_tc_ph_8 . " mv + " . $config->tourn_tc_ph_9 . " sec/mv increment, ...";
                    } else {
                        $line = $line . " + " . $config->tourn_tc_ph_9 . " sec/mv increment for rest of game";
                    }
                } else {
                    $line = $line . " + " . $config->tourn_tc_ph_6 . " sec/mv increment for rest of game";
                }
            } else {
                $line = $line . " + " . $config->tourn_tc_ph_3 . " sec/mv increment per game";
            }
        } elseif ($config->tourn_tc_mode == 2) {
            if ($config->tourn_tc_ph_2 != 99) {
                $line = $line . "/" . $config->tourn_tc_ph_2 . " mv + " . $config->tourn_tc_ph_3 . " sec/mv delay, " . $config->tourn_tc_ph_4 . " min";
                if ($config->tourn_tc_ph_5 != 99) {
                    $line = $line . "/" . $config->tourn_tc_ph_5 . " mv + " . $config->tourn_tc_ph_6 . " sec/mv delay, " . $config->tourn_tc_ph_7 . " min";
                    if ($config->tourn_tc_ph_8 != 99) {
                        $line = $line . "/" . $config->tourn_tc_ph_8 . " mv + " . $config->tourn_tc_ph_9 . " sec/mv delay, ...";
                    } else {
                        $line = $line . " + " . $config->tourn_tc_ph_9 . " sec/mv delay for rest of game";
                    }
                } else {
                    $line = $line . " + " . $config->tourn_tc_ph_6 . " sec/mv delay for rest of game";
                }
            } else {
                $line = $line . " + " . $config->tourn_tc_ph_3 . " sec/mv delay per game";
            }
        }
        $lines[] 	= $line;
        // $line = "XCA 3";
        // $lines[] 	= $line;
        // $line = "XCG 3";
        // $lines[] 	= $line;
        if ($ratingexport === false) {
            $lines[] 	= 'XXR '.$turnier->runden;
        }
        $line = "132                                                                                        ";
        $runde = 0;
        while ($runde < $turnier->runden) {
            $line = $line . substr($rundentermine[$runde]->datum, 2, 2) . "/" .
                substr($rundentermine[$runde]->datum, 5, 2) . "/" .
                substr($rundentermine[$runde]->datum, 8, 2) . "  ";
            $runde++;
        }
        $lines[] 	= $line;
        if ($ratingexport === false) {
            $line = "XCT                                                                                        ";
            $runde = 0;
            while ($runde < $turnier->runden) {
                $line = $line . $rundentermine[$runde]->startzeit . "  ";
                $runde++;
            }
            $lines[] 	= $line;
        } else {
            $line = "DDD SSSS ATTT NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN RRRR FFF IIIIIIIIIII BBBB/BB/BB PPPP RRRR  ";
            $runde = 0;
            while ($runde < $turnier->runden) {
                $runde++;
                $x = $runde % 10;
                $line .= sprintf("%d%d%d%d %d %d  ", $x, $x, $x, $x, $x, $x);
            }
            $lines[] 	= $line;
        }
        return $lines;
    }

    // Aufbau der Spielerzeilen als Array
    function player_lines($turnier, $players, $erg_array, $round, $clmextensions, $ratingexport)
    {
        $config = clm_core::$db->config();
        $lines = array();
        $taillines = array();
        if ($round == 1) {
            $line =	'XXC white1';
            $taillines[] = $line;
        }
        $groups = $config->tourn_accel_groups;
        if ($groups == 0) {
            $groups = 1;
        }
        $accel = floor((count($players) + 1) / 2 / $groups) * 2;
        for ($i = 0; $i <  count($players); $i++) {
            $line 	= '001 '.sprintf('%4s', $players[$i]->snr);
            $line	.= ' '.sprintf('%1s', strtolower($players[$i]->geschlecht));
            if ($ratingexport == false) {
                $line	.= sprintf('%3s', strtolower($players[$i]->titel));
            } else {
                $line	.= sprintf('%3s', strtoupper($players[$i]->titel));
            }
            $line	.= ' '.sprintf('%-33s', clm_core::$load->sub_umlaute($players[$i]->name));
            if ($ratingexport == false) {
                $line	.= ' '.sprintf('%4s', $players[$i]->twz);
            } else {
                $line	.= ' '.sprintf('%4s', $players[$i]->FIDEelo);
            }
            $line	.= ' '.sprintf('%3s', $players[$i]->FIDEcco);
            $line	.= ' '.sprintf('%11s', $players[$i]->FIDEid);
            $line	.= ' '.sprintf('%-4s/  /  ', $players[$i]->birthYear);
            $line	.= ' '.sprintf('%4s', $players[$i]->sum_punkte);
            $line	.= ' '.sprintf('%4s', $players[$i]->rankingPos);
            for ($ir = 1; $ir <=  $turnier->runden; $ir++) {
                $key = ($ir * 1000) + $players[$i]->snr;
                if (!isset($erg_array[$key])) {
                    if ($ir < $round) {
                        $line .= sprintf('%10s', '  0000 - Z');
                    } elseif ($players[$i]->tlnrStatus == 0) {
                        $line .= sprintf('%10s', '  0000 - Z');
                    } else {
                        $line .= sprintf('%10s', ' ');
                    }
                } else {
                    if ($erg_array[$key]->gegner == '0000') {
                        if ($erg_array[$key]->ergebnis == "-") {
                            $erg_array[$key]->ergebnis = "Z";
                        }
                        if ($erg_array[$key]->ergebnis == "=") {
                            $erg_array[$key]->ergebnis = "H";
                        }
                        if ($erg_array[$key]->ergebnis == "+") {
                            $erg_array[$key]->ergebnis = "F";
                        }
                    }
                    $line .= '  '.sprintf('%4s', $erg_array[$key]->gegner);
                    $line .= ' '.sprintf('%1s', $erg_array[$key]->color);
                    $line .= ' '.sprintf('%1s', $erg_array[$key]->ergebnis);
                }
            }
            $lines[] 	= $line;
            $cfgtouaccround = intval($config->tourn_accel_rounds);
            if ($config->tourn_accel == 1) {
                if ($config->tourn_accel_groups == 2) {
                    if ($i < $accel) {
                        $line = sprintf('XXA %4s', $players[$i]->snr);
                        $ii = 0;
                        while (($ii < ($cfgtouaccround - 1)) && ($ii < $round)) {
                            $line = $line . "  1.0";
                            $ii++;
                        }
                        if (($ii < $cfgtouaccround) && ($ii < $round)) {
                            $line = $line . "  0.5";
                        }
                        $taillines[] = $line;
                    }
                } elseif ($config->tourn_accel_groups == 3) {
                    if ($i < $accel) {
                        $line = sprintf('XXA %4s', $players[$i]->snr);
                        $ii = 0;
                        while (($ii < ($cfgtouaccround - 2)) && ($ii < $round)) {
                            $line = $line . "  2.0";
                            $ii++;
                        }
                        if (($ii < ($cfgtouaccround - 1)) && ($ii < $round)) {
                            $line = $line . "  1.0";
                            $ii++;
                        }
                        if (($ii < $cfgtouaccround) && ($ii < $round)) {
                            $line = $line . "  0.5";
                        }
                        $taillines[] = $line;
                    } elseif ($i < 2 * $accel) {
                        $line = sprintf('XXA %4s', $players[$i]->snr);
                        $ii = 0;
                        while (($ii < ($cfgtouaccround - 2)) && ($ii < $round)) {
                            $line = $line . "  1.0";
                            $ii++;
                        }
                        if (($ii < ($cfgtouaccround - 1)) && ($ii < $round)) {
                            $line = $line . "  0.5";
                            $ii++;
                        }
                        if (($ii < $cfgtouaccround) && ($ii < $round)) {
                            $line = $line . "  0.0";
                        }
                        $taillines[] = $line;
                    }
                } elseif ($config->tourn_accel_groups == 4) {
                    if ($i < $accel) {
                        $line = sprintf('XXA %4s', $players[$i]->snr);
                        $ii = 0;
                        while (($ii < ($cfgtouaccround - 2)) && ($ii < $round)) {
                            $line = $line . "  3.0";
                            $ii++;
                        }
                        if (($ii < ($cfgtouaccround - 1)) && ($ii < $round)) {
                            $line = $line . "  2.0";
                            $ii++;
                        }
                        if (($ii < $cfgtouaccround) && ($ii < $round)) {
                            $line = $line . "  1.0";
                        }
                        $taillines[] = $line;
                    } elseif ($i < 2 * $accel) {
                        $line = sprintf('XXA %4s', $players[$i]->snr);
                        $ii = 0;
                        while (($ii < ($cfgtouaccround - 2)) && ($ii < $round)) {
                            $line = $line . "  2.0";
                            $ii++;
                        }
                        if (($ii < ($cfgtouaccround - 1)) && ($ii < $round)) {
                            $line = $line . "  1.0";
                            $ii++;
                        }
                        if (($ii < $cfgtouaccround) && ($ii < $round)) {
                            $line = $line . "  0.5";
                        }
                        $taillines[] = $line;
                    } elseif ($i < 3 * $accel) {
                        $line = sprintf('XXA %4s', $players[$i]->snr);
                        $ii = 0;
                        while (($ii < ($cfgtouaccround - 2)) && ($ii < $round)) {
                            $line = $line . "  1.0";
                            $ii++;
                        }
                        if (($ii < ($cfgtouaccround - 1)) && ($ii < $round)) {
                            $line = $line . "  0.5";
                            $ii++;
                        }
                        if (($ii < $cfgtouaccround) && ($ii < $round)) {
                            $line = $line . "  0.0";
                        }
                        $taillines[] = $line;
                    }
                }
            }
            if ($clmextensions) {
                if ($players[$i]->verein != "") {
                    $taillines[] = "XCC " . sprintf('%4s', $players[$i]->snr) . " " . ausgeschr($players[$i]->verein);
                }
                if ($players[$i]->email != "") {
                    $taillines[] = "XCE " . sprintf('%4s', $players[$i]->snr) . " " . $players[$i]->email;
                }
                if ($players[$i]->tel_no != "") {
                    $taillines[] = "XCP " . sprintf('%4s', $players[$i]->snr) . " " . $players[$i]->tel_no;
                }
                $taillines[] = "XCR " . sprintf('%4s', $players[$i]->snr) . " " . sprintf('%4d', $players[$i]->start_dwz) . " " .
                    sprintf('%4s', $players[$i]->FIDEelo) . " " . sprintf('%4s', $players[$i]->twz);
                if (($players[$i]->zps != "") && ($players[$i]->mgl_nr != "")) {
                    $taillines[] = "XCZ " . sprintf('%4s', $players[$i]->snr) . " " . $players[$i]->zps . "-" . $players[$i]->mgl_nr;
                }
            }
        }

        // vereinsgleiche Spieler werden möglichst nicht gegeneinander gepaart
        if ($config->tourn_prev_teaming == 1) {
            for ($j = 1; $j < count($players); $j++) {
                for ($i = 0; $i < $j; $i++) {
                    if (($players[$i]->zps != "") && ($players[$i]->zps == $players[$j]->zps)) {
                        $tmcount = 0;
                        for ($tmi = 1; $tmi <  count($players); $tmi++) {
                            if ($players[$i]->zps == $players[$tmi]->zps) {
                                $tmcount++;
                            }
                        }
                        // 5 Runden CH-Sys: 2^5 = 32 funktioniert also bis 64 Teilnehmer eindeutig.
                        // Wenn mehr als 16 Teilnehmer aus einem Club kommen, dann müssen diese immer gegeneinander spielen dürfen
                        // (bis 24 sollte genug Reserve bieten, wir gehen aber auf Nummer sicher)
                        // 2 << 5 = 64; 64 >> 2 = 16. Wenn also mehr als 16 Spieler aus einem Club, dann keine Einschränkung
                        // Wenn mehr als 25% der gesamten Teilnehmer aus einem Club kommen, dann keine Einschränkung
                        if (((2 << ($turnier->runden - 2)) > $tmcount) && (count($players) > 4 * $tmcount)) {
                            // Team-Pairing verhindern ?
                            // Nur, wenn der absolute Abstand der Spieler untereinander größer als 5 ist
                            if (($j - $i) > 5) {
                                // XXP: prevent pairing
                                $line = sprintf('XXP %4s %4s', $players[$i]->snr, $players[$j]->snr);
                                if ($clmextensions) {
                                    $taillines[] 	= $line;
                                }
                            }
                        }
                    }
                }
            }
        }
        //die('PLines');
        foreach ($taillines as $line) {
            $lines[] = $line;
        }
        return $lines;
    }

    //---------------- main routine --------------------
    $message = '';

    // Turnier auslesen
    $query = 'SELECT * FROM #__clm_turniere WHERE id = '.$turnierid;
    $turnier = clm_core::$db->loadObject($query);
    $round = $turnier->dg * $turnier->runden;

    // Termine auslesen
    $query = "SELECT * FROM #__clm_turniere_rnd_termine WHERE turnier = " . $turnierid . " ORDER BY dg,nr;";
    $rundentermine = clm_core::$db->loadObjectList($query);

    // Spielerliste laden - alle Spieler
    $query = "SELECT * FROM #__clm_turniere_tlnr WHERE turnier = " . $turnierid . " ORDER BY snr;";
    $players = clm_core::$db->loadObjectList($query);

    $aplayers = array();
    foreach ($players as $player1) {
        $aplayers[$player1->snr]['name'] = $player1->name;
    }

    $lines_common = common_lines($turnier, $players, $rundentermine);

    // Paarungen der Runden laden
    $query = "SELECT * FROM #__clm_turniere_rnd_spl WHERE turnier = " . $turnierid . " AND tln_nr IS NOT NULL AND runde <= " . $round . " ORDER by tln_nr,runde;";
    $erg = clm_core::$db->loadObjectList($query);

    $erg_array = array();
    foreach ($erg as $erg1) {
        $key = ($erg1->runde * 1000) + $erg1->spieler;
        $erg_array[$key] = new stdClass();
        if ($erg1->gegner == 0) {
            $erg_array[$key]->gegner = '0000';
        } else {
            $erg_array[$key]->gegner = $erg1->gegner;
        }
        if ($erg1->gegner == 0) {
            $erg_array[$key]->color = '-';
        } elseif ($erg1->heim == '1') {
            $erg_array[$key]->color = 'w';
        } else {
            $erg_array[$key]->color = 'b';
        }
        if ($erg1->ergebnis == '0') {
            $erg_array[$key]->ergebnis = '0';
        } elseif ($erg1->ergebnis == '1') {
            $erg_array[$key]->ergebnis = '1';
        } elseif ($erg1->ergebnis == '2') {
            $erg_array[$key]->ergebnis = '=';
        } elseif ($erg1->ergebnis == '3') {
            $erg_array[$key]->ergebnis = '0';
        } elseif ($erg1->ergebnis == '4') {
            $erg_array[$key]->ergebnis = '-';
        } elseif ($erg1->ergebnis == '5') {
            $erg_array[$key]->ergebnis = '+';
        } elseif ($erg1->ergebnis == '6') {
            $erg_array[$key]->ergebnis = '-';
        } elseif ($erg1->ergebnis == '7') {
            $erg_array[$key]->ergebnis = '-';
        } elseif ($erg1->ergebnis == '8') {
            $erg_array[$key]->ergebnis = 'U';
        } elseif ($erg1->ergebnis == '9') {
            $erg_array[$key]->ergebnis = '0';
        } elseif ($erg1->ergebnis == '10') {
            $erg_array[$key]->ergebnis = '=';
        } elseif ($erg1->ergebnis == '11') {
            $erg_array[$key]->ergebnis = 'F';
        } elseif ($erg1->ergebnis == '12') {
            $erg_array[$key]->ergebnis = 'H';
        } elseif ($erg1->ergebnis == '13') {
            $erg_array[$key]->ergebnis = 'Z';
        } else {
            $erg_array[$key]->ergebnis = '0';
        }
        if ($debug > 0) {
            echo "<br>Erg $key: ";
        } if ($debug > 0) {
            var_dump($erg_array[$key]);
        }
    }

    $lines_player = player_lines($turnier, $players, $erg_array, $round, $clmextensions, $ratingexport);
    $nl = "\n";

    $ret = "";
    foreach ($lines_common as $line) {
        $ret = $ret . utf8_decode($line).$nl;
    }
    foreach ($lines_player as $line) {
        $ret = $ret . utf8_decode($line).$nl;
    }
    return $ret;
}
