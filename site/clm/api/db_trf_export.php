<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
/**
 * trf-Export eines Turniers / einer Liga
 */

function clm_api_db_trf_export($turnierid,$group=false,$test=false,$clmextensions=false,$ratingexport=false) {
	$lang = clm_core::$lang->draw;
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$test_button = $config->test_button;
	if ($test_button == 1) $test = true; else $test = false;
	if ($test) $debug = 1; else $debug = 0;
	$new_ID = 0;
	if ($debug > 0) { echo "<br><br>-- allgemeine Daten --";	}
	if ($debug > 0) echo "<br><br>Turnier: ".$turnierid; 		//echo "<br>end"; //die();

	//----------------- functions --------------------------------
	//
	function ausgeschr($line) {
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
		$pat[] = "/\&oacute;/";
		$pat[] = "/ä/";
		$pat[] = "/ö/";
		$pat[] = "/ü/";
		$pat[] = "/ß/";
		$pat[] = "/Ä/";
		$pat[] = "/Ö/";
		$pat[] = "/Ü/";
		$pat[] = "/á/";
		$pat[] = "/é/";
		$pat[] = "/ó/";
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
		$rep[] = "o";
		$rep[] = "ae";
		$rep[] = "oe";
		$rep[] = "ue";
		$rep[] = "ss";
		$rep[] = "Ae";
		$rep[] = "Oe";
		$rep[] = "Ue";
		$rep[] = "a";
		$rep[] = "e";
		$rep[] = "o";
		return preg_replace($pat, $rep, $line);
	}

	// Testfunktionen (Anzeigen)
	function p_players($players,$snr,$parm,$text='Teilnehmer') {
		if ($parm == 'K' or $parm == 'G'  or $parm == 'F') {
			if ($debug > 0) echo "<br><br>$text: "; 
			foreach ($players as $player) {
				if ($debug > 0) echo "<br>- Snr: ".$player['snr']." Rang: ".$player['rankingPos']." ".$player['name']." Gruppe: ".$player['grupp']." Gegner: ".$player['gegner'];
				if ($parm == 'G') {
					if ($debug > 0) echo " Farbdiff: ".$player['farbdiff']." Absolute Farbe: ".$player['sollfabsolut']." Starke Farbe: ".
						$player['sollfstark']." Schwache Farbe: ".$player['sollfschwach']." Farbfolge: ".$player['color']." Sollfarbe: ".$player['sollfarbe'];
				}
				if ($parm == 'F') {
					if ($debug > 0) echo " GPkt: ".$player['sum_punkte'];
					for( $i=1; $i<50; $i++) {
						if (!isset($player['p_round'][$i])) break;
						if ($debug > 0) echo "  nach Runde $i  Pkt:".$player['p_round'][$i]." Fl.:".$player['floater'][$i]; 
					}
				}
			}
		} elseif ($parm == 'E') {
			if ($debug > 0) echo "<br><br>$text (".$snr."): "; 
			if ($debug > 0) echo "<br>- Spieler: ".$players[$snr]['snr']." ".$players[$snr]['name']." Gruppe: ".$players[$snr]['grupp']." Gegner: ".$players[$snr]['gegner'];
		} else { // parm = 'A'
			if ($debug > 0) echo "<br><br>$text : "; 
			foreach ($snr as $snr1) {
				if ($debug > 0) echo "<br>- Spieler: ".$players[$snr1]['snr']." ".$players[$snr1]['name']." Gruppe: ".$players[$snr1]['grupp']." Gegner: ".$players[$snr1]['gegner'];
			}
		}
	}

	function p_ranglist($ranglist,$parm,$text='Rangliste') {
		//if ($parm != 'E') {
		if ($debug > 0) echo "<br><br>$text: "; 
		foreach ($ranglist as $player) {
			if ($debug > 0) echo "<br>- Rang: ".$player['rankingPos']." Snr: ".$player['snr']." ".$player['name']." Pkt: ".$player['sum_punkte']." TWZ: ".
				$player['twz']." Gruppe: ".$player['grupp']." Gegner: ".$player['gegner'];
			if ($parm == 'G') {
				if ($debug > 0) echo " Farbdiff: ".$player['farbdiff']." Absolutee Farbe: ".$player['sollfabsolut']." Starke Farbe: ".$player['sollfstark'].
					" Schwache Farbe: ".$player['sollfschwach']." Farbfolge: ".$player['color']; 
				if ($debug > 0) echo " Verein: ".$player['verein']." ZPS: ".$player['zps']; 
			}
		}
	}

	function p_pairing($pairings,$players,$parm,$p_nr='',$text='Paarung(en)') {
		if ($p_nr == 0) $p_nr = count($pairings);
		// echo "<br><br>Parm: ".$parm."  Anz:".$p_nr."  "; var_dump($pairings);

		if ($parm != 'E') {
			if (count($pairings) == 0) {
				if ($debug > 0) echo "<br><br>$text: "; 
				if ($debug > 0) echo "<br>keine Paarung! ";
			} else {
				if ($debug > 0) echo "<br><br>$text (Anz: $p_nr): "; 
				foreach ($pairings as $pairing) {
					if ($debug > 0) echo "<br>Paarung: ".$pairing['brett']."  Spieler: ".$pairing['wsnr']." ".$pairing['wname'].
						"(".$players[$pairing['wsnr']]['rankingPos'].$players[$pairing['wsnr']]['sollfarbe'].")".
						"  -  Gegner: ".$pairing['bsnr']." ".$pairing['bname'];
					if ($pairing['bsnr'] > 0) {
						if ($debug > 0) echo "(".$players[$pairing['bsnr']]['rankingPos'].$players[$pairing['bsnr']]['sollfarbe'].")"; 
					} else {
						if ($debug > 0) echo "(0)";
					}
				}
			}
		} else {
			if ($debug > 0) echo "<br><br>$text ".$p_nr.": "; 
			if ($debug > 0) echo "<br>Paarung: ".$pairings[$p_nr]['brett']." Spieler: ".$pairings[$p_nr]['wsnr']." ".$pairings[$p_nr]['wname'].
				"  -  Gegner: ".$pairings[$p_nr]['bsnr']." ".$pairings[$p_nr]['bname'];
		}
	}

	function p_perm($perm,$p_nr,$parm) {
		if ($parm != 'E') {
			if ($debug > 0) echo "<br><br>Permutationen (Anz:".$p_nr."): "; 
			foreach ($perm as $perm1) {
				//if ($perm1['erg'] === true) $d_erg = " Ja "; elseif ($perm1['erg'] === false) $d_erg = "Nein"; else $d_erg = "?";
				if ($debug > 0) echo "<br>Index: ".$perm1['relkrit']." / ".$perm1['austausch']." / ".$perm1['nr']."  Anz Paarungen (h): ".
					$perm1['counth']."  Anz Paarungen: ".$perm1['count']."  Pktdiff: ".$perm1['pktdiff']."  Farbdiff: ".$perm1['farbdiff'];
			}
		} else {
			if ($debug > 0) echo "<br><br>Permutation ".$p_nr.": "; 
			//if ($perm1['erg'] === true) $d_erg = " Ja "; elseif ($perm1[erg] === false) $d_erg = "Nein"; else $d_erg = "?";
			if ($debug > 0) echo "<br>Index: ".$perm1['relkrit']." / ".$perm1['austausch']." / ".$perm1['nr']."  Anz Paarungen (h): ".
				$perm1['counth']."  Anz Paarungen: ".$perm1['count']."  Pktdiff: ".$perm1['pktdiff']."  Farbdiff: ".$perm1['farbdiff'];
		}
	}

	// Aufbau der allgemeinen Turnierzeilen als Array
	function common_lines($group,$turnier,$players,$teams,$rundentermine,$clmextensions,$ratingexport) {
		$config = clm_core::$db->config();
 		$turparams = new clm_class_params($turnier->params);
		$drawclubavoid = $turparams->get("drawclubavoid","0");	
		$tourn_accel_rounds = $turparams->get("accel_rounds","0");	
		$tourn_accel_groups = $turparams->get("accel_groups","0");	

		$lines = array();
		$line = "### Tournament Section  ";
		$lines[] 	= $line;
		$lines[] 	= '012 '.clm_core::$load->utf8decode($turnier->name);
		$lines[]	= '022 '.clm_core::$load->utf8decode($turnier->city);
		if ($turnier->city <= '')
			$lines[] = 'FFF Es ist kein Spielort bzw. -region eingetragen';
		$lines[]	= '032 '.clm_core::$load->utf8decode($turnier->FIDEcco);
		if ($turnier->FIDEcco <= '')
			$lines[] = 'FFF Es ist keine Veranstalterföderation  eingetragen';
		$lines[] 	= '042 '.substr($turnier->dateStart,0,4).'/'.substr($turnier->dateStart,5,2).'/'.substr($turnier->dateStart,8,2);
		if ($turnier->dateStart <= '1970-01-01')
			$lines[] = 'FFF Es ist kein Turnierstartdatum  eingetragen';
		$lines[] 	= '052 '.substr($turnier->dateEnd,0,4).'/'.substr($turnier->dateEnd,5,2).'/'.substr($turnier->dateEnd,8,2);
		if ($turnier->dateEnd <= '1970-01-01')
			$lines[] = 'FFF Es ist kein Turnierendedatum  eingetragen';
		$lines[] 	= '062 '.count($players);
		$ielo = 0;
		for ($i = 0; $i <  count($players); $i++) { 
			if ($players[$i]->FIDEelo > 0) $ielo++;
		}

		$lines[] 	= '072 '.$ielo;
		if ($group) {
			if (is_array($teams) AND count($teams) > 0)
				$lines[] 	= '082 '.count($teams);
			else
				$lines[] 	= '082 '.'0';
			$lines[] 	= '092 '.$turnier->ttyp;			
		} else {
			$lines[] 	= '082 '.'0';
			if ($turnier->typ == '1') { 
				if ($tourn_accel_rounds == 0) {
					$lines[] 	= '092 '.'Individual Swiss Dutch';
				} else {
					$lines[] 	= '092 '.'Individual Swiss Dutch (' . $tourn_accel_rounds . " accelerated rounds with " . $tourn_accel_groups . " groups)";
				}
			} else {
				$lines[] 	= '092 '.$turnier_typ;	
			}
		}
//		if ($turnier->leiter != "") {
//			$lines[] 	= '102 '.ausgeschr($turnier->leiter);
//		}
		if (isset($turnier->arbiter_CA) AND !is_null($turnier->arbiter_CA) AND count($turnier->arbiter_CA) > 0) 
			$lines[] 	= '102 '.$turnier->arbiter_CA[0]->name.','.$turnier->arbiter_CA[0]->vorname.' ('.$turnier->arbiter_CA[0]->fideid.')';
		else
			$lines[] = 'FFF Es ist kein Hauptschiedsrichter  eingetragen';
		if (isset($turnier->arbiter_more) AND !is_null($turnier->arbiter_more) AND count($turnier->arbiter_more) > 0) 
			for ($i = 0; $i <  count($turnier->arbiter_more); $i++) { 
				$lines[] 	= '112 '.$turnier->arbiter_more[$i]->name.','.$turnier->arbiter_more[$i]->vorname.' ('.$turnier->arbiter_more[$i]->fideid.')';
			}
		
		if ($turparams->get("time_control","") >= '0') {
			$time_control = clm_core::$api->db_time_control($turparams->get("time_control",""));
			$lines[] 	= '122 '. $time_control;	
		}	

		// $line = "XCA 3";
		// $lines[] 	= $line;
		// $line = "XCG 3";
		// $lines[] 	= $line;
		if ($ratingexport === false) {
			$lines[] 	= 'XXR '.$turnier->runden;
		}
		if ($ratingexport === false) {
			$line = "XCT                                                                                        ";
			$runde = 0;
			while ($runde < $turnier->runden) {
				$line = $line . $rundentermine[$runde]->startzeit . "  ";
				$runde++;
			}
			$lines[] 	= $line;
		}
		$lines[] 	= '142 '.$turnier->runden;
		
		$time_control_code = clm_core::$api->db_time_control($turparams->get("time_control",""),true);
		if ($time_control_code > '') {
			$lines[] = '222 '. $time_control_code;	
		} else {
			$lines[] = '222 '. '';	
			$lines[] = 'FFF Der Code zur Bedenkzeitregel ist nicht gegeben';			
		}	

		if ($clmextensions) {
			if ($turnier->lokal != "") {
				$lines[] 	= 'XCL '.$turnier->lokal;
			}
			if ($turnier->params != "") {
				$lines[] 	= 'XCP '. str_replace("\n",";;",$turnier->params);
			}
			$lines[] 	= 'XCF '.sprintf('%2s',$turnier->tiebr1) . " " .sprintf('%2s',$turnier->tiebr2) . " " .sprintf('%2s',$turnier->tiebr3);
		}

		$line = "132                                                                                        ";
		$runde = 0;
		$check_runden = 0;
		while ($runde < $turnier->runden) {
			$line = $line . substr($rundentermine[$runde]->datum, 2, 2) . "/" .
				substr($rundentermine[$runde]->datum, 5, 2) . "/" .
				substr($rundentermine[$runde]->datum, 8, 2) . "  ";
			if ($rundentermine[$runde]->datum > '1970-01-01') $check_runden++; 
			$runde++;
		}
		$lines[] 	= $line;
		if ($check_runden == 0)
			$lines[] = 'FFF Die Datumsangaben zu den Runden liegen nicht vor';
		elseif ($check_runden < $turnier->runden)
			$lines[] = 'FFF Die Datumsangaben zu den Runden sind unvollständig';			
		
		return $lines;
	}

	// Aufbau der Spielerzeilen als Array
	function player_lines($group,$turnier,$players,$erg_array,$round,$clmextensions,$ratingexport) {	
		$config = clm_core::$db->config();
 		$turparams = new clm_class_params($turnier->params);
		$drawclubavoid = $turparams->get("drawclubavoid","0");	
		$tourn_accel_rounds = $turparams->get("accel_rounds","0");	
		$tourn_accel_groups = $turparams->get("accel_groups","0");	
		$tourn_prev_teaming = $turparams->get("prev_teaming","0");
		
		$lines = array();
		$line = "### Player Section  ";
		$lines[] 	= $line;
		if ($ratingexport) {
			$line = "### SSSS ATTT NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN RRRR FFF IIIIIIIIIII BBBB/BB/BB PPPP RRRR  ";
			$runde = 0;
			while ($runde < $turnier->runden) {
				$runde++;
				$x = $runde % 10;
				$line .= sprintf("%d%d%d%d %d %d  ", $x, $x, $x, $x, $x, $x);
			}
			$lines[] 	= $line;
		}
		
		$taillines = array();
		if ($round == 1) {
			$line =	'XXC white1';
			$taillines[] = $line;
		}
		$groups = $tourn_accel_groups;
		if ($groups == 0) {
			$groups = 1;
		}
		$accel = floor((count($players) + 1) / 2 / $groups) * 2;
		for ($i = 0; $i <  count($players); $i++) { 

			// Feldprüfungen$players
			if ($players[$i]->FIDEid < 1) {
				$lines[] = 'FFF Spieler '.$players[$i]->snr.' '.clm_core::$load->sub_umlaute($players[$i]->name)." ist ohne FIDE-ID";
			}	
			$line 	= '001 '.sprintf('%4s',$players[$i]->snr);
			$line	.= ' '.sprintf('%1s',strtolower($players[$i]->geschlecht));
			if (is_null($players[$i]->titel)) $players[$i]->titel = '';
			if ($ratingexport == false) {
				$line	.= sprintf('%3s',strtolower($players[$i]->titel));
			} else {
				$line	.= sprintf('%3s',strtoupper($players[$i]->titel));
			}
			$line	.= ' '.sprintf('%-33s',clm_core::$load->sub_umlaute($players[$i]->name));
			if ($ratingexport == false) {
				$line	.= ' '.sprintf('%4s',$players[$i]->twz);
			} else {
				if ($players[$i]->FIDEelo <= 0) $players[$i]->FIDEelo = NULL;
				$line	.= ' '.sprintf('%4s',$players[$i]->FIDEelo);
			}
			$line	.= ' '.sprintf('%3s',$players[$i]->FIDEcco);
			$line	.= ' '.sprintf('%11s',$players[$i]->FIDEid);
			$line	.= ' '.sprintf('%-4s/  /  ',$players[$i]->birthYear);

			$line	.= ' '.sprintf('%4s',$players[$i]->sum_punkte);
			$line	.= ' '.sprintf('%4s',$players[$i]->rankingPos);
			for ($ir = 1; $ir <=  $turnier->runden; $ir++) { 
				$key = ($ir * 1000) + $players[$i]->snr;
				if (!isset($erg_array[$key])) 
					if ($ir < $round) $line .= sprintf('%10s','  0000 - Z');
					elseif ($players[$i]->tlnrStatus == 0) $line .= sprintf('%10s','  0000 - Z');
					else $line .= sprintf('%10s',' ');
				else {
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
					$line .= '  '.sprintf('%4s',$erg_array[$key]->gegner);
					$line .= ' '.sprintf('%1s',$erg_array[$key]->color);
					$line .= ' '.sprintf('%1s',$erg_array[$key]->ergebnis);
				}
			}
			$lines[] 	= $line;
			$cfgtouaccround = intval($tourn_accel_rounds);
			if ($tourn_accel_rounds > 0) {
				if ($tourn_accel_groups == 2) {
					if ($i < $accel) {
						$line = sprintf ('XXA %4s', $players[$i]->snr);
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
				} else if ($tourn_accel_groups == 3) {
					if ($i < $accel) {
						$line = sprintf ('XXA %4s', $players[$i]->snr);
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
					} else if ($i < 2 * $accel) {
						$line = sprintf ('XXA %4s', $players[$i]->snr);
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
				} else if ($tourn_accel_groups == 4) {
					if ($i < $accel) {
						$line = sprintf ('XXA %4s', $players[$i]->snr);
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
					} else if ($i < 2 * $accel) {
						$line = sprintf ('XXA %4s', $players[$i]->snr);
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
					} else if ($i < 3 * $accel) {
						$line = sprintf ('XXA %4s', $players[$i]->snr);
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
					$taillines[] = "XCC " . sprintf('%4s',$players[$i]->snr) . " " . ausgeschr($players[$i]->verein);
				}
				if ($players[$i]->email != "") {
					$taillines[] = "XCE " . sprintf('%4s',$players[$i]->snr) . " " . $players[$i]->email;
				}
				if ($players[$i]->tel_no != "") {
					$taillines[] = "XCP " . sprintf('%4s',$players[$i]->snr) . " " . $players[$i]->tel_no;
				}
				$taillines[] = "XCR " . sprintf('%4s', $players[$i]->snr) . " " . sprintf('%4d', $players[$i]->start_dwz) . " " .
					sprintf('%4s',$players[$i]->FIDEelo) . " " . sprintf('%4s',$players[$i]->twz);
				if (($players[$i]->zps != "") && ($players[$i]->mgl_nr != "")) {
					$taillines[] = "XCZ " . sprintf('%4s',$players[$i]->snr) . " " . $players[$i]->zps . "-" . $players[$i]->mgl_nr;
				}
			}
		}

		// vereinsgleiche Spieler werden möglichst nicht gegeneinander gepaart
		if ($tourn_prev_teaming == 1) {
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
						if (((2 << ($turnier->runden -2)) > $tmcount) && (count($players) > 4 * $tmcount)) {
							// Team-Pairing verhindern ?
							// Nur, wenn der absolute Abstand der Spieler untereinander größer als 5 ist
							if (($j-$i) > 5) {
								// XXP: prevent pairing
								$line = sprintf ('XXP %4s %4s', $players[$i]->snr, $players[$j]->snr);
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
		foreach($taillines as $line) {
			$lines[] = $line;
		}
		return $lines;
	}

	// Aufbau der Mannschaftszeilen als Array
	function team_lines($group,$turnier,$players,$erg_array,$round,$teams,$clmextensions,$ratingexport) {	
		$config = clm_core::$db->config();
 		$turparams = new clm_class_params($turnier->params);
		$tourn_accel_rounds = $turparams->get("accel_rounds","0");	
		$tourn_accel_groups = $turparams->get("accel_groups","0");	
		$tourn_prev_teaming = $turparams->get("prev_teaming","0");
		
		$lines = array();
		$line = "### Team Section  ";
		$lines[] 	= $line;
		if ($ratingexport) {
			$line =	"### SSS NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN FFFFF EEEEEE MMMM GGGGGG RRR  PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP PPPP ";
			$lines[] 	= $line;
		}
		
		$teamplayers = array();
		for ($i = 1; $i <  (count($teams) + 1); $i++) {
			$teamplayers[$i] = '';
		}
		for ($i = 0; $i <  count($players); $i++) { 
			if ($players[$i]->tln_nr < 1) continue;
			$teamplayers[$players[$i]->tln_nr] .= sprintf('%5s',$players[$i]->snr);
		}
		// Durchschnittswerte Elo
		$elo_average = clm_core::$api->db_elo_average($turnier->id);
			echo "<br>ea : "; var_dump($elo_average);
//die();		
		
		for ($i = 0; $i <  count($teams); $i++) { 
			$line 	= '310 '.sprintf('%3s',$teams[$i]->tln_nr);
			$line	.= ' '.sprintf('%-34s',clm_core::$load->sub_umlaute($teams[$i]->name));
			$line	.= ' '.sprintf('%-5s',''); // Nickname
			$line   .= ' '.sprintf('%-5s',$elo_average[2][$i+1]); // ELO-Schnitt der Stammbretter
			$line 	.= ' '.sprintf('%3s',intval($teams[$i]->summanpunkte));
			$line 	.= ' '.sprintf('%6s',$teams[$i]->sumbrettpunkte);
			$line 	.= ' '.sprintf('%3s',$teams[$i]->rankingpos);
			$line 	.= ' '.$teamplayers[$teams[$i]->tln_nr];
			$lines[] 	= $line;
		}
		
		// Vollständigkeit prüfen für Ligen und Mannschaftsturniere
		// 1.	Erwartete Anzahl von Einzelergebnissen auf Basis Anzahl spielfreie Mannschaften
		$query = "SELECT COUNT(tln_nr) AS count FROM #__clm_mannschaften "
			." WHERE liga = ".$turnier->id
			." AND man_nr = 0"
			;
		$spielfrei = clm_core::$db->loadObject($query);
		if (isset($spielfrei) AND !is_null($spielfrei)) $count = $spielfrei->count;
		else $count = 0;
		if ($turnier->runden_modus == 1 OR $turnier->runden_modus == 2) {
			$counter= intval(($turnier->teil - $count)/2)*$turnier->stamm;
		} elseif ($turnier->runden_modus == 3) {
			$counter= intval(($turnier->teil - $count)/2)*$turnier->stamm;
		} else {
			$counter= 0;
		}

		// 2.	Einzelergebnisse pro Durchgang/Runde
		$all_count = 0;
		for ($dg = 1; $dg <= $turnier->durchgang; $dg++) { 
		  for ($rnd = 1; $rnd <= $turnier->runden; $rnd++) { 
			$query = " SELECT ee.runde, ee.dg, COUNT(*) AS cnt_runde FROM `#__clm_rnd_spl` as ee"
				." LEFT JOIN #__clm_rnd_man as me ON me.lid = ee.lid AND me.runde = ee.runde AND me.dg = ee.dg AND me.tln_nr = ee.tln_nr"		
				." LEFT JOIN #__clm_mannschaften as m ON m.liga = me.lid AND m.tln_nr = me.gegner"		
				." WHERE ee.lid = ".$turnier->id
				." AND ee.dg = ".$dg
				." AND ee.runde = ".$rnd
				." AND ee.heim = 1 "
				." AND m.man_nr > 0 "
				." GROUP BY ee.dg, ee.runde "
				." ORDER BY ee.dg ASC, ee.runde ASC "
				;
			$rnd_proof = clm_core::$db->loadObjectList($query);
			if (isset($rnd_proof[0])) $rnd_count = intval($rnd_proof[0]->cnt_runde);
			else $rnd_count = 0;
			$all_count += $rnd_count;

			// kampflose Paarungen vermindern die Anzahl der zu erwarteten Einzelergebnisse
			$query = " SELECT * FROM `#__clm_rnd_man` as me"
				." WHERE me.lid = ".$turnier->id
				." AND me.dg = ".$dg
				." AND me.runde = ".$rnd
				." AND me.ergebnis = 5 "
				;
			$rnd_kampflos = clm_core::$db->loadObjectList($query);
			if (is_null($rnd_kampflos)) $count_kampflos = 0;
			else $count_kampflos = count($rnd_kampflos) * $turnier->stamm;

			$fehler	= 0;
			if($rnd_count < ($counter - $count_kampflos) AND $rnd_count == 0){
				$lines[] 	= 'FFF '.Text::_( 'DB_WTEXT0' ).Text::_( 'DB_ROUND' ).$rnd.Text::_( 'DB_DG' ).$dg;
				$fehler = 1;
			} elseif($rnd_count < ($counter - $count_kampflos)){
				$lines[] 	= 'FFF '.Text::_( 'DB_WTEXT1' ).Text::_( 'DB_ROUND' ).$rnd.Text::_( 'DB_DG' ).$dg;
				$fehler = 1;
			}
		  }
		}
		
		return $lines;

	}

	
//---------------- main routine --------------------		
	$message = '';
	
	if ($group) {
		// Teamwettbewerburnier auslesen
		$query = 'SELECT * FROM #__clm_liga WHERE id = '.$turnierid;
		$turnier = clm_core::$db->loadObject($query);
		// Anzahl Runden
		$round = $turnier->durchgang * $turnier->runden;
		// Setzen Turniertyp
		$typ	= $turnier->runden_modus;
		$turnier->ttyp = '';
		if($typ =="1"){ $turnier->ttyp = 'Team Round Robin'; } // TR: Mannschaftsturnier; jeder gegen jeden
		if($typ =="2"){ $turnier->ttyp = 'Team Round Robin'; }
		if($typ =="3"){ $turnier->ttyp = 'Team Swiss Dutch'; } // TW: Mannschaftsturnier: Schweizer System
		if($typ =="4"){ $turnier->ttyp = 'Team Knockout'; }
		if($typ =="5"){ $turnier->ttyp = 'Team Knockout'; } // TC: Mannschaftsturnier: K.O.-System (Pokal)
		// Turnierleiter mit Name und FIDE-ID
		$query = "SELECT s.spielername,s.fide_id FROM #__clm_dwz_spieler s,#__clm_user u,#__clm_liga t WHERE" .
		       " s.zps=u.zps AND s.mgl_nr=u.mglnr AND t.sl=u.jid AND t.sid=s.sid AND u.sid=t.sid AND t.id=" . $turnier->id . ";";
		$turnierleiterliste = clm_core::$db->loadObjectList($query);
		$turnier->leiter = "";
		if (isset($turnierleiterliste[0]) AND $turnierleiterliste[0]->spielername != "") {
			$turnier->leiter = $turnierleiterliste[0]->spielername . " (" . $turnierleiterliste[0]->fide_id . ")";
		}
										  

		// Hauptschiedsrichter auslesen
		$query = "SELECT at.*, a.name, a.vorname, a.fidefed FROM #__clm_arbiter_turnier as at "
				." LEFT JOIN #__clm_arbiter as a ON at.fideid = a.fideid "
				." WHERE at.liga = $turnierid "
				." AND at.trole = 'A' AND at.role = 'CA' ";
		$turnier->arbiter_CA	= clm_core::$db->loadObjectList($query);

		// Hauptschiedsrichter auslesen
		$query = "SELECT at.*, a.name, a.vorname, a.fidefed FROM #__clm_arbiter_turnier as at "
				." LEFT JOIN #__clm_arbiter as a ON at.fideid = a.fideid "
				." WHERE at.liga = $turnierid "
				." AND at.trole = 'A' AND at.role != 'CA' ";
		$turnier->arbiter_more	= clm_core::$db->loadObjectList($query);

		// Termine auslesen
		$query = "SELECT * FROM #__clm_runden_termine WHERE liga = " . $turnierid . " ORDER BY nr";
		$rundentermine = clm_core::$db->loadObjectList($query);
				
		// Spielerliste laden - alle Spieler
		$query = "SELECT ml.*, ml.Punkte as sum_punkte, d.DWZ as dwz, d.FIDE_Elo as FIDEelo, d.FIDE_ID as FIDEid, d.FIDE_Land as FIDEcco,"
			." d.FIDE_Titel as titel, d.spielername as name, d.Geburtsjahr as birthYear, d.Geschlecht as geschlecht,"
			." 0 as rankingPos, 0 as tlnrStatus, m.tln_nr "
			." FROM #__clm_meldeliste_spieler AS ml "
			." LEFT JOIN #__clm_mannschaften AS m ON (m.liga=ml.lid AND (m.zps=ml.zps OR FIND_IN_SET(ml.zps,m.sg_zps) != 0 OR (m.zps = '0' AND ml.zps = '-1'))"
				." AND m.man_nr = ml.mnr AND m.man_nr !=0 AND m.liste !=0) "
			." LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = ml.mgl_nr AND d.ZPS = ml.zps AND d.sid = ml.sid )"
			." WHERE ml.lid = ".$turnierid
			." ORDER BY m.tln_nr, ml.snr"
			;
		$players = clm_core::$db->loadObjectList($query);

		$aplayers = array();
		$lsnr = 0;
		foreach ($players as $player1) {
			if (isset($aplayers[$player1->zps][$player1->mgl_nr])) {
				$player1->snr_ml = $player1->snr;
				$player1->snr = 0;
				$player1->lsnr = 0;
				continue;
			}
			$lsnr++;
			$aplayers[$player1->zps][$player1->mgl_nr]['name'] = $player1->name;
			$aplayers[$player1->zps][$player1->mgl_nr]['lsnr'] = $lsnr;
			$player1->snr_ml = $player1->snr;
			$player1->snr = $lsnr;
			$player1->lsnr = $lsnr;
		}
	
		// Paarungen der Runden laden
		$query = "SELECT sp.*, ml.snr FROM #__clm_rnd_spl as sp " 
				." LEFT JOIN #__clm_mannschaften AS m ON ((m.liga=sp.lid) AND (m.tln_nr=sp.tln_nr)) "
				." LEFT JOIN #__clm_meldeliste_spieler AS ml ON ((sp.lid=ml.lid) AND (m.man_nr=ml.mnr) AND (sp.zps=ml.zps) AND (sp.spieler=ml.mgl_nr)) "
				." WHERE sp.lid = " . $turnierid . " AND sp.spieler > 0 AND sp.runde <= " . $round . " ORDER by sp.tln_nr, ml.snr, sp.runde;";
		$erg = clm_core::$db->loadObjectList($query);
		$i = 0;
		foreach ($erg as $erg1) {
			$i++;
			if (isset($aplayers[$erg1->zps][$erg1->spieler])) {
				$erg1->lsnr = $aplayers[$erg1->zps][$erg1->spieler]['lsnr'];
			} else {
				echo "<br>$i SPIELER : "; var_dump($erg1);
			}
			if (isset($aplayers[$erg1->gzps][$erg1->gegner])) {
				$erg1->lgsnr = $aplayers[$erg1->gzps][$erg1->gegner]['lsnr'];
			} else {
				$erg1->lgsnr = 0;
//				echo "<br>$i GEGNER : "; var_dump($erg1);
			}
		}

		// Mannschaften auslesen
		$query = "SELECT * FROM #__clm_mannschaften WHERE liga = " . $turnierid . " ORDER BY tln_nr";
		$teams = clm_core::$db->loadObjectList($query);
		
	} else {
		// Einzelturnierturnier auslesen
		$query = 'SELECT * FROM #__clm_turniere WHERE id = '.$turnierid;
		$turnier = clm_core::$db->loadObject($query);
		// Setzen Anzahl runden
		$round = $turnier->dg * $turnier->runden;
		// Setzen Turniertyp
		$typ	= $turnier->typ;
		$turnier->ttyp = '';
		if($typ =="1"){ $turnier->ttyp = 'Individual Swiss Dutch'; } // SW: Einzelturnier; Schweizer System
		if($typ =="2"){ $turnier->ttyp = 'Individual Round Robin'; } // SR: Einzelturnier; jeder gegen jeden
		if($typ =="3"){ $turnier->ttyp = 'Individual Knockout'; } // SC: Einzelturnier; K.O. System (Pokal)
		if($typ =="4"){ $turnier->ttyp = 'Individual Knockout'; } 
		if($typ =="5"){ $turnier->ttyp = 'Individual Knockout'; } // SC: Einzelturnier; K.O. System (Pokal)
		if($typ =="6"){ $turnier->ttyp = 'Individual Round Robin'; } // SR: Einzelturnier; jeder gegen jeden
		// Turnierleiter mit Name und FIDE-ID
		$query = "SELECT s.spielername,s.fide_id FROM #__clm_dwz_spieler s,#__clm_user u,#__clm_turniere t WHERE" .
		       " s.zps=u.zps AND s.mgl_nr=u.mglnr AND t.tl=u.jid AND t.sid=s.sid AND u.sid=t.sid AND t.id=" . $turnier->id . ";";
		$turnierleiterliste = clm_core::$db->loadObjectList($query);
		$turnier->leiter = "";
		if (isset($turnierleiterliste[0]) AND $turnierleiterliste[0]->spielername != "") {
			$turnier->leiter = $turnierleiterliste[0]->spielername . " (" . $turnierleiterliste[0]->fide_id . ")";
		}
		
		// Hauptschiedsrichter auslesen
		$query = "SELECT at.*, a.name, a.vorname, a.fidefed FROM #__clm_arbiter_turnier as at "
				." LEFT JOIN #__clm_arbiter as a ON at.fideid = a.fideid "
				." WHERE at.turnier = $turnierid "
				." AND at.trole = 'A' AND at.role = 'CA' ";
		$turnier->arbiter_CA	= clm_core::$db->loadObjectList($query);

		// Hauptschiedsrichter auslesen
		$query = "SELECT at.*, a.name, a.vorname, a.fidefed FROM #__clm_arbiter_turnier as at "
				." LEFT JOIN #__clm_arbiter as a ON at.fideid = a.fideid "
				." WHERE at.turnier = $turnierid "
				." AND at.trole = 'A' AND at.role != 'CA' ";
		$turnier->arbiter_more	= clm_core::$db->loadObjectList($query);

		// Termine auslesen
		$query = "SELECT * FROM #__clm_turniere_rnd_termine WHERE turnier = " . $turnierid . " ORDER BY dg,nr;";
		$rundentermine = clm_core::$db->loadObjectList($query);
				
		// Spielerliste laden - alle Spieler
		$query = "SELECT * FROM #__clm_turniere_tlnr WHERE turnier = " . $turnierid . " ORDER BY snr;";
		$players = clm_core::$db->loadObjectList($query);

		$aplayers = array();
		foreach ($players as $player1) {
			$aplayers[$player1->snr]['name'] = $player1->name;
			
		// Paarungen der Runden laden
		$query = "SELECT * FROM #__clm_turniere_rnd_spl WHERE turnier = " . $turnierid . " AND tln_nr IS NOT NULL AND runde <= " . $round . " ORDER by tln_nr,runde;";
		$erg = clm_core::$db->loadObjectList($query);
			
		}
	}
	if ($debug > 0) { 
		echo "<br><br>-- Ergebnisse --";	
		$i = 0;
		foreach ($erg as $erg1) {
			$i++;
			echo "<br>$i : "; var_dump($erg1);
		}
		
		echo "<br><br>-- Turnier --";	
			echo "<br>0 : "; var_dump($turnier);
			
		echo "<br><br>-- Rundentermine --";	
		$i = 0;
		foreach ($rundentermine as $termin1) {
			$i++;
			echo "<br>$i : "; var_dump($termin1);
		}
		
		echo "<br><br>-- Spieler --";	
		$i = 0;
		foreach ($players as $player1) {
			$i++;
			echo "<br>$i : "; var_dump($player1);
		}
		
		if ($group) {
		echo "<br><br>-- Mannschaften --";	
		$i = 0;
		foreach ($teams as $team1) {
			$i++;
			echo "<br>$i : "; var_dump($team1);
		}
		}
	}

	$lines_common = common_lines($group,$turnier,$players,$teams,$rundentermine,$clmextensions,$ratingexport);
		echo "<br><br>-- General --";	
		$i = 0;
		foreach ($lines_common as $lines_common1) {
			$i++;
			echo "<br>$i : "; var_dump($lines_common1);
		}

	$erg_array = array();
	foreach ($erg as $erg1) {
		if ($group) {
			if ($erg1->lsnr < 1) ontinue;
			$key = ($erg1->runde * 1000) + $erg1->lsnr;
			$erg_array[$key] = new stdClass();			
			if ($erg1->lgsnr == 0) $erg_array[$key]->gegner = '0000';
			else $erg_array[$key]->gegner = $erg1->lgsnr;
			if ($erg1->lgsnr == 0) $erg_array[$key]->color = '-';
			elseif ($erg1->weiss == '1') $erg_array[$key]->color = 'w';
			else $erg_array[$key]->color = 'b';
		} else {
			$key = ($erg1->runde * 1000) + $erg1->spieler;
			$erg_array[$key] = new stdClass();
			if ($erg1->gegner == 0) $erg_array[$key]->gegner = '0000';
			else $erg_array[$key]->gegner = $erg1->gegner;
			if ($erg1->gegner == 0) $erg_array[$key]->color = '-';
			elseif ($erg1->heim == '1') $erg_array[$key]->color = 'w';
			else $erg_array[$key]->color = 'b';
		}
		if ($erg1->ergebnis == '0') $erg_array[$key]->ergebnis = '0';
		elseif ($erg1->ergebnis == '1') $erg_array[$key]->ergebnis = '1';
		elseif ($erg1->ergebnis == '2') $erg_array[$key]->ergebnis = '=';
		elseif ($erg1->ergebnis == '3') $erg_array[$key]->ergebnis = '0';
		elseif ($erg1->ergebnis == '4') $erg_array[$key]->ergebnis = '-';
		elseif ($erg1->ergebnis == '5') $erg_array[$key]->ergebnis = '+';
		elseif ($erg1->ergebnis == '6') $erg_array[$key]->ergebnis = '-';
		elseif ($erg1->ergebnis == '7') $erg_array[$key]->ergebnis = '-';
		elseif ($erg1->ergebnis == '8') $erg_array[$key]->ergebnis = 'U';
		elseif ($erg1->ergebnis == '9') $erg_array[$key]->ergebnis = '0';
		elseif ($erg1->ergebnis == '10') $erg_array[$key]->ergebnis = '=';
		elseif ($erg1->ergebnis == '11') $erg_array[$key]->ergebnis = 'F';
		elseif ($erg1->ergebnis == '12') $erg_array[$key]->ergebnis = 'H';
		elseif ($erg1->ergebnis == '13') $erg_array[$key]->ergebnis = 'Z';
		else $erg_array[$key]->ergebnis = '0';						
		if ($debug > 0) echo "<br>Erg $key: "; if ($debug > 0) var_dump($erg_array[$key]); 			
	}

	$lines_player = player_lines($group,$turnier,$players,$erg_array,$round,$clmextensions,$ratingexport);
	$nl = "\n";
		echo "<br><br>-- Player data --";	
		$i = 0;
		foreach ($lines_player as $lines_player1) {
			$i++;
			echo "<br>$i : "; var_dump($lines_player1);
		}
	if ($group) {
		$lines_team = team_lines($group,$turnier,$players,$erg_array,$round,$teams,$clmextensions,$ratingexport);
		$nl = "\n";
		echo "<br><br>-- Player data --";	
		$i = 0;
		foreach ($lines_team as $lines_team1) {
			$i++;
			echo "<br>$i : "; var_dump($lines_team1);
		}
	}

	$ret = "";
	$elines = array();
	foreach($lines_common as $line) {
			if (substr($line,0,3) == 'FFF') {
				$elines[] = $line;
			} else {
				$ret .= clm_core::$load->utf8decode($line).$nl;
			}
	}
	foreach($lines_player as $line) {
			if (substr($line,0,3) == 'FFF') {
				$elines[] = $line;
			} else {
				$ret .= clm_core::$load->utf8decode($line).$nl;
			}
	}
	if ($group) {
		foreach($lines_team as $line) {
			if (substr($line,0,3) == 'FFF') {
				$elines[] = $line;
			} else {
				$ret = $ret . clm_core::$load->utf8decode($line).$nl;
			}
		}
	}
	
	if (count($elines) > 0) {
		$ret = $ret . "### Error/Info Section  ".$nl;
		foreach($elines as $line) {
			$line_3 = '###'.substr($line,3);
			$ret = $ret . clm_core::$load->utf8decode($line_3).$nl;
		}
	}

	return array($ret,$elines);
}

?>
