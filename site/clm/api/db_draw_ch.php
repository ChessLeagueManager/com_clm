<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
use Joomla\CMS\Language\Text;
/**
* Auslosungsroutine für Einzelturnier im CH-Modus
*/
function clm_api_db_draw_ch($turnierid,$dg,$round,$group=false,$test=false) {
    $lang = clm_core::$lang->draw;
	if ($test) $debug = 1; else $debug = 0;
	$debug = 1;
	$test = false;
	if ($test)	echo "<br><br>Test - keine Übernahme der Daten ins CLM!"; 
	$new_ID = 0;
if ($debug > 0) { echo "<br><br>-- allgemeine Daten --";	}
if ($debug > 0) echo "<br><br>Turnier: ".$turnierid; 		//echo "<br>end"; //die();
if ($debug > 0) echo "<br>Durchgang: ".$dg; 	//echo "<br>end"; //die();
if ($debug > 0) echo "<br>Aktuelle Runde: ".$round; 

//----------------- functions --------------------------------

	// Testfunktionen (Anzeigen)
	function p_players($players,$snr,$parm,$text='Teilnehmer') {
		if ($parm == 'K' or $parm == 'G'  or $parm == 'F') {
			echo "<br><br>$text: "; 
			foreach ($players as $player) {
				echo "<br>- Snr: ".$player['snr']." Rang: ".$player['rankingPos']." ".$player['name']." Gruppe: ".$player['grupp']." Gegner: ".$player['gegner'];
				if ($parm == 'G')
					echo " Farbdiff: ".$player['farbdiff']." Absolute Farbe: ".$player['sollfabsolut']." Starke Farbe: ".$player['sollfstark']." Schwache Farbe: ".$player['sollfschwach']." Farbfolge: ".$player['color']." Sollfarbe: ".$player['sollfarbe']; 
				if ($parm == 'F') {
					echo " GPkt: ".$player['sum_punkte'];
					for( $i=1; $i<50; $i++) {
						if (!isset($player['p_round'][$i])) break;
						echo "  nach Runde $i  Pkt:".$player['p_round'][$i]." Fl.:".$player['floater'][$i]; 
					}
				}
			}
		} elseif ($parm == 'E') {
			echo "<br><br>$text (".$snr."): "; 
			echo "<br>- Spieler: ".$players[$snr]['snr']." ".$players[$snr]['name']." Gruppe: ".$players[$snr]['grupp']." Gegner: ".$players[$snr]['gegner'];
		} else { // parm = 'A'
			echo "<br><br>$text : "; 
			foreach ($snr as $snr1) {
				echo "<br>- Spieler: ".$players[$snr1]['snr']." ".$players[$snr1]['name']." Gruppe: ".$players[$snr1]['grupp']." Gegner: ".$players[$snr1]['gegner'];
			}
		}
	}
	function p_ranglist($ranglist,$parm,$text='Rangliste') {
		//if ($parm != 'E') {
		echo "<br><br>$text: "; 
		foreach ($ranglist as $player) {
			echo "<br>- Rang: ".$player['rankingPos']." Snr: ".$player['snr']." ".$player['name']." Pkt: ".$player['sum_punkte']." TWZ: ".$player['twz']." Gruppe: ".$player['grupp']." Gegner: ".$player['gegner'];
			if ($parm == 'G') {
			echo " Farbdiff: ".$player['farbdiff']." Absolutee Farbe: ".$player['sollfabsolut']." Starke Farbe: ".$player['sollfstark']." Schwache Farbe: ".$player['sollfschwach']." Farbfolge: ".$player['color']; 
			echo " Verein: ".$player['verein']." ZPS: ".$player['zps']; 
		}	}
	}
	function p_pairing($pairings,$players,$parm,$p_nr='',$text='Paarung(en)') {
		if ($p_nr == 0) $p_nr = count($pairings);
//echo "<br><br>Parm: ".$parm."  Anz:".$p_nr."  "; var_dump($pairings);

		if ($parm != 'E') {
		  if (count($pairings) == 0) {
			echo "<br><br>$text: "; 
			echo "<br>keine Paarung! ";
		  } else {
			echo "<br><br>$text (Anz: $p_nr): "; 
			foreach ($pairings as $pairing) {
				echo "<br>Paarung: ".$pairing['brett']."  Spieler: ".$pairing['wsnr']." ".$pairing['wname']."(".$players[$pairing['wsnr']]['rankingPos'].$players[$pairing['wsnr']]['sollfarbe'].")"."  -  Gegner: ".$pairing['bsnr']." ".$pairing['bname'];
				if ($pairing['bsnr'] > 0) echo "(".$players[$pairing['bsnr']]['rankingPos'].$players[$pairing['bsnr']]['sollfarbe'].")"; 
				else echo "(0)";
			}
		  }
		} else {
			echo "<br><br>$text ".$p_nr.": "; 
			echo "<br>Paarung: ".$pairings[$p_nr]['brett']." Spieler: ".$pairings[$p_nr]['wsnr']." ".$pairings[$p_nr]['wname']."  -  Gegner: ".$pairings[$p_nr]['bsnr']." ".$pairings[$p_nr]['bname'];
		}
	}
	function p_perm($perm,$p_nr,$parm) {
		if ($parm != 'E') {
			echo "<br><br>Permutationen (Anz:".$p_nr."): "; 
			foreach ($perm as $perm1) {
				//if ($perm1['erg'] === true) $d_erg = " Ja "; elseif ($perm1['erg'] === false) $d_erg = "Nein"; else $d_erg = "?";
				echo "<br>Index: ".$perm1['relkrit']." / ".$perm1['austausch']." / ".$perm1['nr']."  Anz Paarungen (h): ".$perm1['counth']."  Anz Paarungen: ".$perm1['count']."  Pktdiff: ".$perm1['pktdiff']."  Farbdiff: ".$perm1['farbdiff'];
			}
		} else {
			echo "<br><br>Permutation ".$p_nr.": "; 
			//if ($perm1['erg'] === true) $d_erg = " Ja "; elseif ($perm1[erg] === false) $d_erg = "Nein"; else $d_erg = "?";
			echo "<br>Index: ".$perm1['relkrit']." / ".$perm1['austausch']." / ".$perm1['nr']."  Anz Paarungen (h): ".$perm1['counth']."  Anz Paarungen: ".$perm1['count']."  Pktdiff: ".$perm1['pktdiff']."  Farbdiff: ".$perm1['farbdiff'];
		}
	}
	// Aufbau der allgemeinen Turnierzeilen als Array
	function common_lines($turnier,$players) {
		$config = clm_core::$db->config();
		$turparams = new clm_class_params($turnier->params);
//		$drawclubavoid = $turparams->get("drawclubavoid","0");	
		$tourn_accel_rounds = $turparams->get("accel_rounds","0");	
		$tourn_accel_groups = $turparams->get("accel_groups","0");
		$tourn_prev_teaming = $turparams->get("prev_teaming","0");	

		$lines = array();
		$line = "### Tournament Section  ";
		$lines[] 	= $line;
		$lines[] 	= '012 '.clm_core::$load->utf8decode($turnier->name);
		$lines[]	= '022 '.clm_core::$load->utf8decode($turnier->city);
		$lines[]	= '032 '.clm_core::$load->utf8decode($turnier->FIDEcco);
		$lines[] 	= '042 '.substr($turnier->dateStart,8,2).'/'.substr($turnier->dateStart,5,2).'/'.substr($turnier->dateStart,0,4);
		$lines[] 	= '052 '.substr($turnier->dateEnd,8,2).'/'.substr($turnier->dateEnd,5,2).'/'.substr($turnier->dateEnd,0,4);
		$lines[] 	= '062 '.count($players);
		$itwz = 0;
		for ($i = 0; $i <  count($players); $i++) { 
			if ($players[$i]->twz > 0) $itwz++;
		}
		$lines[] 	= '072 '.$itwz;
		$lines[] 	= '082 '.'0';
		$lines[] 	= '092 '.'Individual: Swiss-System';
			if ($tourn_accel_rounds == 0) {
				$lines[] 	= '092 '.'Individual Swiss Dutch';
			} else {
				$lines[] 	= '092 '.'Individual Swiss Dutch (' . $tourn_accel_rounds . " accelerated rounds with " . $tourn_accel_groups . " groups)";
			}
		if (isset($turnier->arbiter_CA) AND !is_null($turnier->arbiter_CA) AND count($turnier->arbiter_CA) > 0) { 
			$lines[] 	= '102 '.$turnier->arbiter_CA[0]->name.','.$turnier->arbiter_CA[0]->vorname.' ('.$turnier->arbiter_CA[0]->fideid.')';
		}
		if ($turparams->get("time_control","") >= '0') {
			$time_control = clm_core::$api->db_time_control($turparams->get("time_control",""));
			$lines[] 	= '122 '. $time_control;	
		}	
		$lines[] 	= 'XXR '.$turnier->runden;

		return $lines;
	}

	// Aufbau der Spielerzeilen als Array
	function player_lines($turnier,$players,$erg_array,$round) {	
		$config = clm_core::$db->config();
		$turparams = new clm_class_params($turnier->params);
//		$drawclubavoid = $turparams->get("drawclubavoid","0");	
		$tourn_accel_rounds = $turparams->get("accel_rounds","0");	
		$tourn_accel_groups = $turparams->get("accel_groups","0");	
		$tourn_prev_teaming = $turparams->get("prev_teaming","0");	
		if ($tourn_accel_groups == 0) $tourn_accel_groups = 1;
		$accel = floor((count($players) + 1) / 2 / $tourn_accel_groups) * 2;
		$lines = array();
		for ($i = 0; $i <  count($players); $i++) { 
			$line 	= '001 '.sprintf('%4s',$players[$i]->snr);
			$line	.= ' '.sprintf('%1s',strtolower($players[$i]->geschlecht));
			$line	.= sprintf('%3s',$players[$i]->titel);
			$line	.= ' '.sprintf('%-33s',clm_core::$load->sub_umlaute($players[$i]->name));
			$line	.= ' '.sprintf('%4s',$players[$i]->twz);
			$line	.= ' '.sprintf('%3s',$players[$i]->FIDEcco);
			$line	.= ' '.sprintf('%11s',$players[$i]->FIDEid);
			$line	.= ' '.sprintf('%-10s',$players[$i]->birthYear);
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
						if ($erg_array[$key]->ergebnis == "0") {
							$erg_array[$key]->ergebnis = " ";
							$erg_array[$key]->color = " ";
							$erg_array[$key]->gegner = "    ";
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
						$lines[] = $line;
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
						$lines[] = $line;
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
						$lines[] = $line;
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
						$lines[] = $line;
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
						$lines[] = $line;
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
						$lines[] = $line;
					}
				}
			}
			if ($players[$i]->verein != "") {
				$lines[] = "XCC " . sprintf('%4s',$players[$i]->snr) . " " . $players[$i]->verein;
			}
			if ($players[$i]->email != "") {
				$lines[] = "XCE " . sprintf('%4s',$players[$i]->snr) . " " . $players[$i]->email;
			}
			if ($players[$i]->tel_no != "") {
				$lines[] = "XCP " . sprintf('%4s',$players[$i]->snr) . " " . $players[$i]->tel_no;
			}
			$lines[] = "XCR " . sprintf('%4s', $players[$i]->snr) . " " . sprintf('%4d', $players[$i]->start_dwz) . " " .
				sprintf('%4s',$players[$i]->FIDEelo) . " " . sprintf('%4s',$players[$i]->twz);
			if (($players[$i]->zps != "") && ($players[$i]->mgl_nr != "")) {
				$lines[] = "XCZ " . sprintf('%4s',$players[$i]->snr) . " " . $players[$i]->zps . "-" . $players[$i]->mgl_nr;
			}
		}
		if ($round == 1) {
			$line =	'XXC white1';
			$lines[] 	= $line;
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
								$lines[] 	= $line;
							}
						}
					}
				}
			}
		}
//die('PLines');			
		return $lines;
	}
		
		
//---------------- main routine --------------------		
		$message = '';
		// Bestätigung durch TL ermitteln  (tl_ok)
		$query = 'SELECT *'
				. ' FROM #__clm_turniere_rnd_termine'
				. ' WHERE turnier = '.$turnierid
				. ' AND dg = '.$dg
				. ' AND nr = '.$round
				;
		$rnd_termin	= clm_core::$db->loadObjectList($query);
		$tl_ok = $rnd_termin[0]->tl_ok;
	
		// wenn Runde schon bestätigt, auslosen und zurücksetzen nicht erlauben
		if ($tl_ok == 1) {
			return array(false,'Aktion nicht möglich!<br>Runde ist bereits bestätigt.');
		}

		// Anzahl der inaktiven Spieler ohne Paarung
		$query1 = "SELECT * FROM #__clm_turniere_tlnr where turnier=".$turnierid." AND tlnrStatus = 0 AND " .
			  "(snr not in (select spieler FROM #__clm_turniere_rnd_spl where turnier=".$turnierid." AND runde=".$round." AND dg=".$dg." AND spieler is not null))";
		$teilnehmerliste = clm_core::$db->loadObjectList($query1);
		foreach ($teilnehmerliste as $teilnehmer) {
			$snr = $teilnehmer->snr;
			echo "<pre>bearbeite Spielernummer " . $snr . "</pre><br/>\n";
			$query2 = "SELECT * FROM #__clm_turniere_rnd_spl where turnier=".$turnierid." AND runde=".$round." AND dg=".$dg." AND tln_nr is null AND spieler is null order by brett asc,heim desc";
			$rundenpaarungen = clm_core::$db->loadObjectList($query2);
			foreach ($rundenpaarungen as $rundenpaarung) {
				if ($rundenpaarung->spieler == null) {
					$brett = $rundenpaarung->brett;
					echo "<pre>Brett " . $brett . " wurde belegt.</pre><br/>\n";
					$rundenpaarung->spieler = $snr;
					$update = "UPDATE #__clm_turniere_rnd_spl set tln_nr=".$snr.",spieler=".$snr.",gegner=0,ergebnis=13,pgn=NULL WHERE turnier=".$turnierid." AND runde=".$round." AND dg=".$dg." AND brett=".$brett." and heim=1;";
					$result = clm_core::$db->query($update);
					echo "<pre>" . $update . "</pre><br/>\n";
					$update = "UPDATE #__clm_turniere_rnd_spl set tln_nr=0,spieler=0,gegner=".$snr.",ergebnis=4,pgn=NULL WHERE turnier=".$turnierid." AND runde=".$round." AND dg=".$dg." AND brett=".$brett." and heim=0;";
					$result = clm_core::$db->query($update);
					echo "<pre>" . $update . "</pre><br/>\n";
					break;
				}
			}
		}
		
		// sind schon Paarungen eingetragen?
		$query = "SELECT COUNT(spieler) FROM #__clm_turniere_rnd_spl"
				. " WHERE turnier = ".$turnierid." AND runde = ".$round." AND dg = ".$dg." AND spieler IS NOT NULL"
				;
		$pairingCount = clm_core::$db->count($query);
		if ($pairingCount != 0) {
			$message .= "<br>".CLMText::sgpl(($pairingCount)/2, Text::_('PAIRING'), Text::_('PAIRINGS') )." ".Text::_('EXISTING');
		}
//die('npairingcount');	
	
		// sind schon Ergebnisse eingetragen?
		$query = "SELECT COUNT(*) FROM #__clm_turniere_rnd_spl"
				. " WHERE turnier = ".$turnierid." AND runde = ".$round." AND dg = ".$dg." AND ergebnis IS NOT NULL"
				;
		$resultCount = clm_core::$db->count($query);
		if ($resultCount != 0) {
			$message .= "<br>".CLMText::sgpl(($resultCount)/2, Text::_('RESULT'), Text::_('RESULTS') )." ".Text::_('EXISTING');
		}
	
//die('nresultcount');	

		// Rangliste neu berechnen!
		$tournament = new CLMTournament($turnierid, TRUE);
		$tournament->calculateRanking();
		$tournament->setRankingPositions();

//echo "<br>TurnierData:"; var_dump($tournament->data);
//die('turnier');		
 		$turparams = new clm_class_params($tournament->data->params);
//		$drawclubavoid = $turparams->get("prev_teaming","0");	
//		if ($debug > 0) echo "<br>Vereinstest bis Runde: ". $drawclubavoid;
//		if ($drawclubavoid > 0) {
		$tourn_prev_teaming = $turparams->get("prev_teaming","0");	
		if ($debug > 0) echo "<br>Vereinstest bis Runde: ". $$tourn_prev_teaming;
		if ($tourn_prev_teaming > 0) {
			// Sinn Vereinstest prüfen
			$query = "SELECT tlnrStatus, COUNT(tlnrStatus) as total"
				." FROM `#__clm_turniere_tlnr` " 
				." WHERE turnier = ".$turnierid
				." AND tlnrStatus = 1 "
				." GROUP BY tlnrStatus "
				;
			$total_count = clm_core::$db->loadAssocList($query);
//echo "<br>total:";	var_dump($total_count);
			$query = "SELECT verein, COUNT(verein) AS anzahl, COUNT(tlnrStatus) as gesamt"
				." FROM `#__clm_turniere_tlnr` " 
				." WHERE turnier = ".$turnierid
				." AND tlnrStatus = 1 "
				." GROUP BY verein "
				." ORDER BY anzahl DESC "
				;
			$club_count = clm_core::$db->loadAssocList($query);
			if ($club_count[0]["anzahl"]/$total_count[0]["total"] > 0.5) {
				return array(false, CLMText::errorText('PARAMETER', 'CLUB_TEST_NOT_USEFUL').$message);
			}
		}
//die('nclubavoid');

		// Turnier auslesen
		$query = 'SELECT * FROM #__clm_turniere'
			.' WHERE id = '.$turnierid;
		$turnier	= clm_core::$db->loadObject($query);
echo "<br>Turnier:"; var_dump($turnier);
echo "<br>sid:"; var_dump($turnier->sid);
//die('turnier');		
				
		// Spielerliste laden - alle Spieler
		$query = "SELECT * FROM `#__clm_turniere_tlnr` "
			." WHERE turnier = ".$turnierid
			." ORDER BY snr "
			;
		$players = clm_core::$db->loadObjectList($query);
		$aplayers = array();
		foreach ($players as $player1) {
			$aplayers[$player1->snr]['name'] = $player1->name;
		}
echo "<br><br>APlayers:"; var_dump($aplayers);

//die('players');		
		$lines = array();
		$lines_common = common_lines($turnier,$players);
echo "<br><br>Clines:"; var_dump($lines_common);
//die('clines');		
		

		// ev. Paarungen der aktuellen Runde suchen
		$query = "SELECT * "
			." FROM `#__clm_turniere_rnd_spl` " 
			." WHERE turnier = ".$turnierid
			." AND tln_nr > 0"
			." AND runde = ".$round
			." ORDER by brett ASC, heim ASC "
			;
		$erg_arunde = clm_core::$db->loadAssocList($query);
//echo "<br>erg_arunde:";	var_dump($erg_arunde);

		$p_nr = 0;             //Paarungsnummer bzw. Brettnummer über alle Gruppen
		$pairings = array();   //Paarungen der Runde über alle Gruppen
		$paired_count = 0;
		foreach ($erg_arunde as $erg1) 	{
//echo "<br>erg1:";	var_dump($erg1);
			//Übernahme der Paarungen in Arbeitstabelle
			if ($erg1['heim'] == 0) {
				$pairings[$p_nr]['grupp'] = 0; 
				$pairings[$p_nr]['ugrupp'] = 1; 
				$p_nr++; // echo "<br>Paarung-Nr: ".$p_nr;
				$pairings[$p_nr]['brett'] = $p_nr; 
				$pairings[$p_nr]['bsnr'] = $erg1['spieler'];
				$pairings[$p_nr]['bname'] = $aplayers[$erg1['spieler']]['name']; //a
echo "<br>erg-heim0:".$erg1['spieler']."  ".$pairings[$p_nr]['bname'];
				$pairings[$p_nr]['berg'] = $erg1['ergebnis'];
			} else {
				$pairings[$p_nr]['wsnr'] = $erg1['spieler']; 
				$pairings[$p_nr]['wname'] = $aplayers[$erg1['spieler']]['name']; //a
echo "<br>erg-heim1:".$erg1['spieler']."  ".$pairings[$p_nr]['wname'];
				$pairings[$p_nr]['werg'] = $erg1['ergebnis'];
				//Löschen der betroffenen Spieler aus Teilnehmer- und Rangliste
//				if (isset($ranglist[$players[$erg1['spieler']-1]])) unset($ranglist[$players[$erg1['spieler']-1]]);
//				if (isset($players[$erg1['spieler']-1])) unset($players[$erg1['spieler']-1]);
//				if (isset($ranglist[$players[$erg1['gegner']]])) unset($ranglist[$players[$erg1['gegner']]]);
//				if (isset($players[$erg1['gegner']-1])) unset($players[$erg1['gegner']-1]);
				$paired_count++;
				if ($erg1['gegner'] != '0000') {
					$paired_count++;
				}
			}
		}
//		p_pairing($pairings,$aplayers,'K',0,'Bereits im Vorfeld gesetzte Paarungen');
//		p_players($players,0,'G','Teilnehmer nach Bereinigung');
//		p_ranglist($ranglist,'G','Rangliste nach Bereinigung');
		
		if ($paired_count >= count($players)) {
			return array(true, 'Aktion nicht möglich!<br>Alle Spieler sind bereits gepaart.');
		}
//die('nbereinigung');

		// Paarungen der Vorrunden laden
		$query = "SELECT * FROM `#__clm_turniere_rnd_spl` " 
			." WHERE turnier = ".$turnierid
			." AND tln_nr IS NOT NULL"
			." AND runde <= ".$round
			." ORDER by tln_nr, runde "
			;
		$erg = clm_core::$db->loadObjectList($query);
echo "<br>Bisherige Ergebnisse: "; var_dump($erg); 
		
		$erg_array = array();
		foreach ($erg as $erg1) {
			$key = ($erg1->runde * 1000) + $erg1->spieler;
			$erg_array[$key] = new stdClass();
			if ($erg1->gegner == 0) $erg_array[$key]->gegner = '0000';
			else $erg_array[$key]->gegner = $erg1->gegner;
			if ($erg1->gegner == 0) $erg_array[$key]->color = '-';
			elseif ($erg1->heim == '1') $erg_array[$key]->color = 'w';
			else $erg_array[$key]->color = 'b';
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
echo "<br>Erg $key: "; var_dump($erg_array[$key]); 			
		}
// die('matrix');		
		$lines_player = player_lines($turnier,$players,$erg_array,$round);
echo "<br>plines:"; var_dump($lines_player);
//die('plines');		
	// Erstellen der Datei für bbpPairing
	$nl = "\n";
	$file_name = 'TRFXInput';
	$file_name .= '.txt'; 
//	if (!file_exists('components'.DS.'com_clm_pairing'.DS.'draw'.DS)) mkdir('components'.DS.'com_clm'.DS.'draw'.DS);
//	$pdatei = fopen('components'.DS.'com_clm_pairing'.DS.'draw'.DS.$file_name,"wt");
	$str_url = '..'.DS.'plugins'.DS.'xxx'.DS.'clm_pairing_files'.DS.'draw'.DS;
	if (!file_exists($str_url)) mkdir($str_url);
	$pdatei = fopen($str_url.$file_name,"wt");
	foreach($lines_common as $line) {
		fputs($pdatei, clm_core::$load->utf8decode($line).$nl);
	}
	foreach($lines_player as $line) {
		fputs($pdatei, clm_core::$load->utf8decode($line).$nl);
	}
	fclose($pdatei);

	$output = null;
	$retval = null;

// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$tourn_ch_system = $config->tourn_ch_system;
// Auslosung lokal auf eigenen Server per JaVaFo
if($tourn_ch_system ==1){
	$cmd = 'java -Xmx256m -jar '.$str_url.'javafo.jar '.$str_url.'TRFXInput.txt -p '.$str_url.'TRFXOutfile.txt 2>&1';
	$result = exec ( $cmd, $output, $retval );

	if ($retval !==  0)
		return array(false, "Ansetzungen nicht möglich<br>RC: ".$retval."<br>Fehler:".$result);
}	
// Auslosung lokal auf eigenen Server per bbpPairing
elseif($tourn_ch_system ==2){
	$cmd = $str_url.'bbpPairings.exe --dutch '.$str_url.'TRFXInput.txt -p '.$str_url.'TRFXOutfile.txt -l '.$str_url.'ListFile.txt';
	$result = exec ( $cmd, $output, $retval );

	if ($retval !==  0)
		return array(false, "Ansetzungen nicht möglich<br>RC: ".$retval."<br>Fehler:".$result); 
}
// Auslosung auf externem Server
else{
	// Zugangsdaten für den Auswertungsserver
	$url = $config->tourn_ch_server;
echo "<br><br>url"; var_dump($url);
	$data = array(
		"clm_url" => $config->tourn_ch_url,
		"clm_key" => $config->tourn_ch_key,
		"clm_ip" => $config->tourn_ch_ip,
		"clm_info" => $config->tourn_ch_mail
	);
echo "<br><br>data"; var_dump($data);
	// Textdatei zur Auswertung einlesen und anhängen
	$data["Datei"] = file_get_contents($str_url.'TRFXInput.txt', true);
	$data['Datei'] = clm_core::$load->utf8encode($data['Datei'], 'UTF-8', 'UTF-8');
clm_core::$api->test_print('Datei2',$data);
echo "<br><br>Datei"; var_dump($data["Datei"]);
//die();
	// Daten JSon encodieren für den Versand
	$encodedData = json_encode($data);
echo "<br><br>encodedData"; var_dump($encodedData);
echo "<br><br>encodedError".json_last_error();
echo "<br><br>encodedEMsg".json_last_error_msg();

	// CURL zum Transport der Daten initiieren
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedData);

//echo "<br><br>!!!"; var_dump($curl); die();
	// Antwort des Servers
	// !!! hier muss dringend geprüft werden ob sinnvolle Ergebnisse zurückkehren !!!
	$result = curl_exec($curl);
	curl_close($curl);
echo "<br><br>result"; var_dump($result);
clm_core::$api->test_print('result',$result);
//die();
    // Test, ob Routine Fehlermeldung geliefert hat
	if (!is_numeric(substr($result,0,1))) {
		return array(false, $result); 
	}

	// Ergebnis Datei schreiben
	file_put_contents($str_url.'TRFXOutfile.txt', $result);
}


	$file_name = 'TRFXInput';
	$file_name .= '.txt'; 
	echo "<br><br>File:"; var_dump($file_name);
//	$idatei = fopen('components'.DS.'com_clm_pairing'.DS.'draw'.DS.$file_name,"r");
	$idatei = fopen($str_url.$file_name,"r");

	$lines = array();
	while (($line = fgets($idatei, 4096)) !== false) {
		echo '<br>'.$line;
		$lines[] = $line;
	}
	if (!feof($idatei)) {
		echo "Error: unexpected fgets() fail\n";
		die();
	}
	fclose($idatei);
	
/*	$file_name = 'ListFile';
	$file_name .= '.txt'; 
	echo "<br><br>File:"; var_dump($file_name);
	$pfile_name = 'components'.DS.'com_clm_pairing'.DS.'draw'.DS.$file_name; 

	if (!file_exists($pfile_name))	
		return array(false, $lang->e_DrawCHFailed);  // Ausgabedatei wurde nicht erstellt --> shell_exec war nicht erfolfreich
	
	$ldatei = fopen($pfile_name,"r");

	echo "<br>Istdatum".time();
	$testdate = date(' d.m.y.H.i.s.', time());
	echo $testdate;
	
	$idate = filemtime($pfile_name);
	echo "<br>dateidatum:".$idate;
	$testdate = date(' d.m.y.H.i.s.', $idate);
	echo $testdate;
//die();	
	if ($idate < (time() - 2))	
		return array(false, $lang->e_DrawCHFailed);  // Ausgabedatei wurde nicht aktualisiert in den letzten 2 sec --> shell_exec war nicht erfolfreich
	
	$lines = array();
	while (($line = fgets($ldatei, 4096)) !== false) {
		echo '<br>'.$line;
		$lines[] = $line;
	}
	if (!feof($ldatei)) {
		echo "Error: unexpected fgets() fail\n";
		die();
	}
	fclose($ldatei);
*/
	$file_name = 'TRFXOutfile';
	$file_name .= '.txt'; 
	echo "<br><br>File:"; var_dump($file_name);
//	$odatei = fopen('components'.DS.'com_clm_pairing'.DS.'draw'.DS.$file_name,"r");
	$odatei = fopen($str_url.$file_name,"r");
	$lines = array();
	while (($line = fgets($odatei, 4096)) !== false) {
		echo '<br>'.$line;
		$lines[] = $line;
	}
	if (!feof($odatei)) {
		echo "Error: unexpected fgets() fail\n";
		die();
	}
	
	
	//Übernahme der Paarungen in Arbeitstabelle
	$p_nr = -1;             //Paarungsnummer bzw. Brettnummer über alle Gruppen
	$pairings = array();   
	foreach ($lines as $line) {
		$p_nr++;
		if ($p_nr == 0) continue;
//		$line = substr($line,0,(strlen($line)-2));
		$line = trim($line);
		$player_array = explode(' ',$line);
echo "<br>player_array:";	var_dump($player_array);
		$pairings[$p_nr]['brett'] = $p_nr;
		$pairings[$p_nr]['wsnr'] = $player_array[0];
		$pairings[$p_nr]['wname'] = $players[$player_array[0]-1]->name;
		$pairings[$p_nr]['bsnr'] = $player_array[1];
		$pairings[$p_nr]['bname'] = $players[$player_array[1]-1]->name;
	}
echo "<br>pairings:";	var_dump($pairings);

//die('<br>file');


//die('vtest');		
	if (!$test) { 
		$result = clm_core::$api->db_draw_ch_save($turnierid,$dg,$round,$pairings,$group);
		if ($debug > 1) { echo "<br>result:"; var_dump($result); }
	} else {
		echo "<br><br>Test - Ende des Protokolls!<br><br>";
	}
//die('vtest');		
	return array(true, $lines[0].' Paarungen wurden angesetzt'); 

}

?>
