<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	/**
	* errechnet/aktualisiert Rangliste/Punktesummen eines Mannschaftsturnier
	*/
	function clm_api_db_tournament_ranking_round($id,$group=true,$p_runde=0,$p_dg=1) {
		$dgrunde = ($p_dg * 100) + $p_runde + 1;
		$id = clm_core::$load->make_valid($id, 0, -1);
		if($group) {
	
		// Parameter auslesen, für FIDE-Ranglistenkorrektur und ...
		$query = 'SELECT * '
			. ' FROM #__clm_liga'
			. ' WHERE id = '.$id
			;
		$liga	= clm_core::$db->loadObjectList($query);
		//Liga-Parameter aufbereiten
		$paramsStringArray = explode("\n", $liga[0]->params);
		$params = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$key = substr($value,0,$ipos);
				$params[$key] = substr($value,$ipos+1);
			}
		}	
		if (!isset($params['optionTiebreakersFideCorrect']))  {   //Standardbelegung
			$params['optionTiebreakersFideCorrect'] = 0; }
		$dg = $liga[0]->durchgang;
		$runden = $liga[0]->runden;
		$teil = $liga[0]->teil;
		$liga_mt = $liga[0]->liga_mt;
		$b_wertung = $liga[0]->b_wertung;
		$order = $liga[0]->order;
		if ($liga[0]->runden_modus != 3) $params['optionTiebreakersFideCorrect'] = 0;
					
		// Wertpunkte berechnen
		$stamm = clm_core::$db->liga->get($id)->stamm;
	
		// Mannschaftsdaten sammeln
		$query = "SELECT a.sid, a.lid, a.runde, a.dg, a.paar, a.heim, a.dwz_editor, a.brettpunkte "
			." FROM #__clm_rnd_man as a "
			." WHERE a.lid = ".$id;
		if ($p_runde != 0)
			$query .= " AND ((a.dg * 100) + a.runde) < ".$dgrunde; 
		$mdata	= clm_core::$db->loadObjectList($query);
	
		foreach ($mdata as $mdata) {
			// nur Paarung neu berechnen, wenn nicht korrigiert durch 'Turnierwertung ändern'
			if (is_null($mdata->dwz_editor) OR $mdata->dwz_editor == 0) {
				// Wertpunkte Heim berechnen
				if ($mdata->brettpunkte == 0) {
					$wpunkte = 0;
				} else {
					$query	= "SELECT punkte, brett "
						." FROM #__clm_rnd_spl "
						." WHERE sid = ".$mdata->sid
						." AND lid = ".$mdata->lid
						." AND runde = ".$mdata->runde
						." AND paar = ".$mdata->paar
						." AND dg = ".$mdata->dg
						." AND heim = ".$mdata->heim;
					$sdata	= clm_core::$db->loadObjectList($query);
					$wpunkte=0;
					foreach ($sdata as $sdata) {
						$wpunkte = $wpunkte + (($stamm + 1 - $sdata->brett) * $sdata->punkte);
					}
				}
				// Mannschaftstabelle updaten
				$query	= "UPDATE #__clm_rnd_man"
					." SET wertpunkte = ".$wpunkte
					." WHERE sid = ".$mdata->sid
					." AND lid = ".$mdata->lid
					." AND runde = ".$mdata->runde
					." AND paar = ".$mdata->paar
					." AND dg = ".$mdata->dg
					." AND heim = ".$mdata->heim;
				clm_core::$db->query($query);
			}
		}
		// Wertpunkte berechnen (ENDE)

		$query = " SELECT l.sid as sid, a.tln_nr,a.zps as zps, a.sg_zps as sgzps, a.man_nr as man_nr, a.name, a.ordering, "
				." l.teil, l.stamm, l.liga_mt, l.runden_modus, l.man_sieg, l.man_remis, l.sieg, l.remis, l.tiebr1, l.tiebr2, l.tiebr3 "
			." FROM #__clm_mannschaften as a "
			." LEFT JOIN #__clm_liga as l ON l.id =".$id
			." WHERE a.liga = ".$id
			." ORDER BY a.tln_nr "
			;
		$team = clm_core::$db->loadObjectList($query);
		
		
		if(count($team)==0) {
			return array(false, "e_ligaCalculateRankingDataError");
		}
		
		$runden_modus	= $team[0]->runden_modus;
		//if ($runden_modus == 1) return;
		$man_sieg		= $team[0]->man_sieg;
		$man_remis		= $team[0]->man_remis;
		$brett_sieg		= $team[0]->sieg;
		$brett_remis	= $team[0]->remis;
		$mbrett_sieg	= $team[0]->sieg * $team[0]->stamm;
		$mbrett_remis	= $team[0]->remis * $team[0]->stamm;
		$id_stamm 	= $team[0]->stamm;
		$sid = $team[0]->sid;
		
		// "spielfrei(e)" Mannschaft suchen
		$query = " SELECT COUNT(id) FROM #__clm_mannschaften as a "
			." WHERE a.liga = ".$id
			." AND a.name = 'spielfrei'"
//			." ORDER BY a.tln_nr "
			;
		$spielfreiNumber = clm_core::$db->count($query);
		
		$query = " SELECT a.tln_nr FROM #__clm_mannschaften as a "
			." WHERE a.liga = ".$id
			." AND a.name = 'spielfrei'"
			." ORDER BY a.tln_nr "
			;
		$spielfreiList	= clm_core::$db->loadObjectList($query);
		
		if (($spielfreiNumber >= 1) AND ($runden_modus > 2)) {
			// Datum und Uhrzeit für Meldung
			$now = date('Y-m-d H:i:s') ;
			if ($p_runde != 0) {
				// als letzte gemeldete Runde ist vorgegebene Runde
				$dg_max	= $p_dg;
				$runde_max	= $p_runde;
			} else {
				// letzte gemeldete Runde suchen
				$query = "SELECT tln_nr, gegner, brettpunkte, manpunkte, dg, runde FROM `#__clm_rnd_man`"
					. " WHERE lid = ".$id." AND brettpunkte IS NOT NULL"
					;
				$maxData = clm_core::$db->loadObjectList($query);
				$dg_max	= 0;
				$runde_max	= 0;
				foreach ($maxData as $key => $value) {
					if (($dg_max < $value->dg) OR (($dg_max == $value->dg) AND ($runde_max < $value->runde))) {
						$dg_max	= $value->dg;
						$runde_max	= $value->runde;
					}
				}
			}
			foreach ($spielfreiList as $key => $spielfrei) {
				// Paarungen mit "spielfrei" Mannschaft suchen
				$query = "SELECT a.*, m.zps as zps, n.zps as gzps FROM `#__clm_rnd_man` as a"
					." LEFT JOIN #__clm_mannschaften as m ON m.liga = a.lid AND m.sid = a.sid AND m.tln_nr = a.tln_nr"
					." LEFT JOIN #__clm_mannschaften as n ON n.liga = a.lid AND n.sid = a.sid AND n.tln_nr = a.gegner"
					. " WHERE a.lid = ".$id
					. " AND a.tln_nr = ".$spielfrei->tln_nr   //.") OR (a.gegner =".$spielfrei."))"
					;
				if (($runden_modus == 4) OR ($runden_modus == 5))
					$query .= " AND a.dg = ".$dg_max." AND a.runde = ".$runde_max;
				if ($runden_modus == 3)	
					$query .= " AND ((a.dg < ".$dg_max.") OR ( a.dg = ".$dg_max." AND a.runde <= ".$runde_max." ))";
				$spielfreiData = clm_core::$db->loadObjectList($query);
				// Loop über Paarungen mit "spielfrei" Mannschaft
				foreach ($spielfreiData as $key => $value) {
					// Paarungen mit "spielfrei" Mannschaft updaten in clm_rnd_man
					$query = "UPDATE `#__clm_rnd_man`"
						. " SET ergebnis = 4, kampflos = 1, manpunkte = 0, brettpunkte = 0, gemeldet = 62, zeit = '$now'";
					if (($runden_modus == 4) OR ($runden_modus == 5)) 
						$query .= " , ko_decision = 1";	
					$query .= " WHERE lid = ".$id
						. " AND dg = ".$value->dg." AND runde = ".$value->runde
						. " AND tln_nr = ".$value->tln_nr." AND paar = ".$value->paar
						;
				   clm_core::$db->query($query);
					
					$query = "UPDATE `#__clm_rnd_man`"
						. " SET ergebnis = 5, kampflos = 1, manpunkte = ".$man_sieg.", brettpunkte = ".$id_stamm.", gemeldet = 62, zeit = '$now'";
					if (($runden_modus == 4) OR ($runden_modus == 5)) 
						$query .= " , ko_decision = 1";	
					$query .= " WHERE lid = ".$id
						. " AND dg = ".$value->dg." AND runde = ".$value->runde
						. " AND gegner = ".$value->tln_nr." AND paar = ".$value->paar
						;
				   clm_core::$db->query($query);
					// KO Turnier: Sieger ist für nächste Runde qualifiziert
					if (($runden_modus == 4) OR ($runden_modus == 5)) {
					$query = "UPDATE `#__clm_mannschaften`"
						. " SET rankingpos = ".$value->runde
						. " WHERE liga = ".$id
						. " AND tln_nr = ".$value->gegner
						;
				   clm_core::$db->query($query);
					}
					// Paarungen mit "spielfrei" Mannschaften updaten in clm_rnd_spl
					if ($value->heim == 0) {$heim = 0; $gast = 1;}     // Setzen Heim/Gast 
					else {$heim = 1; $gast = 0;}
				  for ($y=1; $y< ($id_stamm +1) ; $y++){
					if ($y%2 != 0) {$weiss = 0; $schwarz = 1;}		// ungerade Zahl für Weiss/Schwarz 
					else { $weiss = 1; $schwarz = 0;}
					// 1.Satz - zuerst testen, ob satz schon existiert 
					$query = "SELECT COUNT(id) as anzahl FROM `#__clm_rnd_spl`"
						. " WHERE lid = '$id'"
						. " AND dg = '$value->dg' AND runde = '$value->runde'"
						. " AND tln_nr = '$value->tln_nr' AND paar = '$value->paar' AND brett = '$y'"
						;
					$testData = clm_core::$db->loadObjectList($query);
					if ($testData[0]->anzahl == 0) {
						$query	= "INSERT INTO #__clm_rnd_spl "
							." ( `sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler` "
							." , `zps`, `gegner`, `gzps`, `ergebnis` , `kampflos`, `punkte`, `gemeldet`) "
							." VALUES ('$sid','$id','$value->runde','$value->paar','$value->dg','$value->tln_nr','$y','$heim','$weiss',0,'$value->zps',"
							." 0,'$value->gzps',8, 1,0,62) "
						;
				   	clm_core::$db->query($query);
					} else {
						$query	= "UPDATE #__clm_rnd_spl "
							. " SET heim = '$heim', weiss = '$weiss', spieler = 0, zps = '$value->zps', gegner = 0, gzps = '$value->gzps',"
							. " ergebnis = 8, kampflos = 1, punkte = 1, gemeldet = 62"
							. " WHERE lid = ".$id
							. " AND dg = ".$value->dg." AND runde = ".$value->runde
							. " AND gegner = ".$value->tln_nr." AND paar = ".$value->paar
						;
				 	  clm_core::$db->query($query);
					} 
					// 2.Satz - zuerst testen, ob satz schon existiert 
					$query = "SELECT COUNT(id) as anzahl FROM `#__clm_rnd_spl`"
						. " WHERE lid = '$id'"
						. " AND dg = '$value->dg' AND runde = '$value->runde'"
						. " AND tln_nr = '$value->gegner' AND paar = '$value->paar' AND brett = '$y'"
						;
					$testData = clm_core::$db->loadObjectList($query);
					//echo "<br>testData: ".$testData->anzahl; var_dump($testData);
					if ($testData[0]->anzahl == 0) {
						$query	= "INSERT INTO #__clm_rnd_spl "
							." ( `sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler` "
							." , `zps`, `gegner`, `gzps`, `ergebnis` , `kampflos`, `punkte`, `gemeldet`) "
							." VALUES ('$sid','$id','$value->runde','$value->paar','$value->dg','$value->gegner','$y','$gast','$schwarz',0,'$value->gzps',"
							." 0,'$value->zps',8, 1,1,62) "
						;
						clm_core::$db->query($query);
					} else {
						$query	= "UPDATE #__clm_rnd_spl "
							. " SET heim = '$gast', weiss = '$schwarz', spieler = 0, zps = '$value->gzps', gegner = 0, gzps = '$value->zps',"
							. " ergebnis = 8, kampflos = 1, punkte = 0, gemeldet = 62"
							. " WHERE lid = ".$id
							. " AND dg = ".$value->dg." AND runde = ".$value->runde
							. " AND gegner = ".$value->tln_nr." AND paar = ".$value->paar
						;
				   	clm_core::$db->query($query);
					} 
				}
			}
		
		} 
	}	

		if (($runden_modus == 4) OR ($runden_modus == 5)) 	return array(true, "e_ligaCalculateRankingSuccess");
		
		// alle FW in Array schreiben
		$arrayFW = array();
		$arrayFW[1] = $team[0]->tiebr1;
		$arrayFW[2] = $team[0]->tiebr2;
		$arrayFW[3] = $team[0]->tiebr3;
		// für alle Spieler Datensätze mit Summenwert 0 anlegen
		// TODO: da gab es einen eigenen PHP-Befehl für?!
		$array_PlayerMPunkte = array();
		$array_PlayerMPunkteTB = array();
		$array_PlayerBPunkte = array();
		$array_PlayerBPunkteTB = array();
		$array_PlayerBerlWertung = array();
		$array_PlayerBuch = array();
		$array_PlayerBuch1St = array();
		$array_PlayerBuchOpp = array();
		$array_PlayerBuchBP = array();
		$array_PlayerBuch1StBP = array();
		$array_PlayerBuchOppBP = array();
		$array_PlayeraSoBe = array();
		$array_PlayerSoBe = array();
		$array_PlayerBuSum = array();
		$array_PlayerBuSum1St = array();
		$array_PlayerBuSumMin = array();
		$array_PlayerBuSumBP = array();
		$array_PlayerBuSum1StBP = array();
		$array_PlayerBuSumMinBP = array();
		$array_PlayerWins = array();
		for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
			$array_PlayerSpiele[$s] = 0;
			$array_PlayerMPunkte[$s] = 0;
			$array_PlayerMPunkteTB[$s] = 0;
			$array_PlayerBPunkte[$s] = 0;
			$array_PlayerBPunkteTB[$s] = 0;
			$array_PlayerBerlWertung[$s] = 0;
			$array_PlayerBuch[$s] = 0;
			$array_PlayerBuch1St[$s] = 0;
			$array_PlayerBuchBP[$s] = 0;
			$array_PlayerBuch1StBP[$s] = 0;
			$array_PlayeraSoBe[$s] = 0;
			$array_PlayerSoBe[$s] = 0;
			$array_PlayerBuSum[$s] = 0;
			$array_PlayerBuSum1St[$s] = 0;
			$array_PlayerBuSumMin[$s] = 9999;
			$array_PlayerBuSumBP[$s] = 0;
			$array_PlayerBuSum1StBP[$s] = 0;
			$array_PlayerBuSumMinBP[$s] = 9999;
			$array_PlayerWins[$s] = 0;
		}
		
		// alle Matches in DatenArray schreiben
		$query = "SELECT dg, runde, tln_nr, gegner, ergebnis, brettpunkte, manpunkte, wertpunkte FROM `#__clm_rnd_man`"
				. " WHERE lid = ".$id." AND brettpunkte IS NOT NULL";
		if ($p_runde != 0)
			$query .= " AND ((dg * 100) + runde) < ".$dgrunde; 
		$matchData = clm_core::$db->loadObjectList($query);
		$z = count($matchData);
		// alle Matches in DatenArray schreiben
		$query = "SELECT tln_nr, brett, punkte FROM `#__clm_rnd_spl`"
				. " WHERE lid = ".$id." AND punkte IS NOT NULL";
		if ($p_runde != 0)
			$query .= " AND ((dg * 100) + runde) < ".$dgrunde; 
		$einzelData = clm_core::$db->loadObjectList($query);
		
		// Finden der letzten gespielten Runde 
		// und Anlegen einer Matrix der gesetzten Matches (Mannschaft)
		$maxround = 0;
		$matrix = array();
		if ($p_runde != 0) {
			$maxround = ((($p_dg - 1) * $runden) + $p_runde);
		} else {
			foreach ($matchData as $key => $value) {
			if ($value->ergebnis < 3 AND ((($value->dg - 1) * $runden) + $value->runde) > $maxround) $maxround = (($value->dg - 1) * $runden) + $value->runde;
			$matrix[$value->tln_nr][$value->dg][$value->runde] = 1;
			}
		}
			
		// für Teams, die nicht gesetzt wurden, werden spielfreie Pseudo-Paarungen angelegt (für FIDE-Ranglistenkorrektur)
		if ($params['optionTiebreakersFideCorrect'] == 1) {
		  for ($s=1; $s<= $teil; $s++) { 		// alle Startnummern durchgehen
			for ($d=1; $d<= $dg; $d++) { 		// alle Durchgänge durchgehen
				for ($r=1; $r<= $runden; $r++) { 	// alle Runden durchgehen
					if ($maxround < ((($d - 1) * $runden) + $r)) break;  		// nur bis zur aktuellen Runde
					if (!isset($matrix[$s][$d][$r])) {
						$matchData[$z] = new stdClass();
						$matchData[$z]->dg = $d;
						$matchData[$z]->runde = $r;
						$matchData[$z]->tln_nr = $s;
						$matchData[$z]->gegner = 0;
						$matchData[$z]->ergebnis = 8;		// spielfrei
						$matchData[$z]->brettpunkte = 0;	
						$matchData[$z]->manpunkte = 0;		
						$matchData[$z]->wertpunkte = 0;	
						$z++;
					}
				}
			}
		  }
		}
		
		// Punkte/Siege
		// alle Matches durchgehen -> Spieler erhalten Punkte und Wins
		foreach ($matchData as $key => $value) {
			if ($maxround < ((($value->dg - 1) * $runden) + $value->runde)) continue;  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
			if ($value->tln_nr == 0) continue;    //techn. Teilnehmer bei ungerader Teilnehmerzahl
			if ($value->manpunkte == $man_sieg) { // Mannschaftssieg
				$array_PlayerWins[$value->tln_nr] += 1;
			}
			if ($value->ergebnis != 8) $array_PlayerSpiele[$value->tln_nr] += 1;
			$array_PlayerMPunkte[$value->tln_nr] += $value->manpunkte;
			$array_PlayerBPunkte[$value->tln_nr] += $value->brettpunkte;
			if ($value->ergebnis < 3) { 	// gespielter Vergleich
				$array_PlayerMPunkteTB[$value->tln_nr] += $value->manpunkte;
				$array_PlayerBPunkteTB[$value->tln_nr] += $value->brettpunkte;
			} elseif ($value->ergebnis > 2 AND $params['optionTiebreakersFideCorrect'] == 0) { // kampflos und ohne FIDE-Korrektur eingestellt
				$array_PlayerMPunkteTB[$value->tln_nr] += $value->manpunkte;
				$array_PlayerBPunkteTB[$value->tln_nr] += $value->brettpunkte;
			} elseif ($value->ergebnis > 2 AND $params['optionTiebreakersFideCorrect'] == 1) { // kampflos und mit FIDE-Korrektur eingestellt
				$array_PlayerMPunkteTB[$value->tln_nr] += $man_remis;
				$array_PlayerBPunkteTB[$value->tln_nr] += $mbrett_remis;
			}
	}
		
		// Berliner Wertung
		// alle Einzels durchgehen -> Mannschaften erhalten Wertpunkte
//		foreach ($einzelData as $key => $valuee) {
//			$array_PlayerBerlWertung[$valuee->tln_nr] += $valuee->punkte * ($id_stamm + 1 - $valuee->brett);
//		}
		foreach ($matchData as $key => $valuee) {
			if ($valuee->tln_nr > 0)
			 $array_PlayerBerlWertung[$valuee->tln_nr] += $valuee->wertpunkte;
		}
	
		// Buchholz & Sonneborn-Berger
		// erneut alle Matches durchgehen -> Teams erhalten Feinwertungen
		foreach ($matchData as $key => $value) {
			if ($maxround < ((($value->dg - 1) * $runden) + $value->runde)) continue;  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
			// Buchholz auf Basis Mannschaftspunkte
			if (in_array(1, $arrayFW) OR in_array(2, $arrayFW) OR in_array(11, $arrayFW)) { // beliebige Buchholz als TieBreaker gewünscht?
				if ($value->ergebnis < 3 OR $params['optionTiebreakersFideCorrect'] == 0) {
					$array_PlayerBuchOpp[$value->tln_nr][] = $array_PlayerMPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
				} else { //Ranglistenkorrektur nach FIDE (Teil 2) nur für CH-Turniere
					$query = "SELECT tln_nr, gegner, dg, runde, ergebnis FROM `#__clm_rnd_man`"
					. " WHERE lid = ".$id
					. " AND tln_nr = ".$value->tln_nr
					. " AND ergebnis IS NOT NULL"
					. " ORDER BY dg ASC, runde ASC"
					;
					$matchDataSnr = clm_core::$db->loadObjectList($query);
					$PlayerPunkteKOR = 0;
					foreach ($matchDataSnr as $key => $valuesnr) {
						if ($maxround < ((($valuesnr->dg - 1) * $runden) + $valuesnr->runde)) continue;  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
						if (($valuesnr->dg < $value->dg) OR ($valuesnr->dg == $value->dg AND $valuesnr->runde < $value->runde)) {
							if ($valuesnr->ergebnis == 1) $PlayerPunkteKOR += $man_sieg; // Sieg
							elseif ($valuesnr->ergebnis == 2) $PlayerPunkteKOR += $man_remis; // remis
							elseif ($valuesnr->ergebnis == 5) $PlayerPunkteKOR += $man_sieg; // Sieg kampflos
						}
					}	
					if (($value->ergebnis == 4) OR ($value->ergebnis == 8)) { $PlayerPunkteKOR += $man_sieg; }// Gegner gewinnt kampflos oder spielfrei
	  				if (($value->ergebnis == 3) OR ($value->ergebnis == 6)) { $PlayerPunkteKOR += $man_sieg; }// Gegner verliert auch kampflos, ist aber egal
					$PlayerPunkteKOR += ($man_remis * (($maxround) - (($value->dg - 1) * $runden) - $value->runde));
					$array_PlayerBuchOpp[$value->tln_nr][] = $PlayerPunkteKOR; // Array mit Gegnerwerten - für Streichresultat
				}
			}
			
			// Buchholz auf Basis Brettpunkte 
			if (in_array(7, $arrayFW) OR in_array(8, $arrayFW) OR in_array(17, $arrayFW) OR in_array(18, $arrayFW)) { // beliebige Buchholz als TieBreaker gewünscht?
				if ($value->ergebnis < 3 OR $params['optionTiebreakersFideCorrect'] == 0) {
					$array_PlayerBuchOppBP[$value->tln_nr][] = $array_PlayerBPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
				} else { //Ranglistenkorrektur nach FIDE (Teil 2) nur für CH-Turniere
					$query = "SELECT tln_nr, gegner, dg, runde, ergebnis FROM `#__clm_rnd_man`"
					. " WHERE lid = ".$id
					. " AND tln_nr = ".$value->tln_nr
					. " AND ergebnis IS NOT NULL"
					. " ORDER BY dg ASC, runde ASC"
					;
					$matchDataSnr = clm_core::$db->loadObjectList($query);
					$PlayerPunkteKOR = 0;
					foreach ($matchDataSnr as $key => $valuesnr) {
						if ($maxround < ((($valuesnr->dg - 1) * $runden) + $valuesnr->runde)) continue;  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
						if (($valuesnr->dg < $value->dg) OR ($valuesnr->dg == $value->dg AND $valuesnr->runde < $value->runde)) {
							if ($valuesnr->ergebnis == 1) $PlayerPunkteKOR += $mbrett_sieg; // Sieg
							elseif ($valuesnr->ergebnis == 2) $PlayerPunkteKOR += $mbrett_remis; // remis
							elseif ($valuesnr->ergebnis == 5) $PlayerPunkteKOR += $mbrett_sieg; // Sieg kampflos
						}
					}	
					if (($value->ergebnis == 4) OR ($value->ergebnis == 8)) { $PlayerPunkteKOR += $mbrett_sieg; }// Gegner gewinnt kampflos oder spielfrei
	  				if (($value->ergebnis == 3) OR ($value->ergebnis == 6)) { $PlayerPunkteKOR += $mbrett_sieg; }// Gegner verliert auch kampflos, ist aber egal
					$PlayerPunkteKOR += ($mbrett_remis * (($maxround) - (($value->dg - 1) * $runden) - $value->runde));
					$array_PlayerBuchOppBP[$value->tln_nr][] = $PlayerPunkteKOR; // Array mit Gegnerwerten - für Streichresultat
				}
			}
						
			// Sonneborn-Berger alt
			if (in_array(3, $arrayFW)) { // SoBe(alt) als ein TieBreaker gewünscht?
				if ($value->manpunkte == $man_remis) { // remis
					$array_PlayeraSoBe[$value->tln_nr] += ($array_PlayerBPunkte[$value->gegner]/2);
				} elseif ($value->manpunkte == $man_sieg) { // Sieger
					$array_PlayeraSoBe[$value->tln_nr] += $array_PlayerBPunkte[$value->gegner];
				}
			}
			
			// Sonneborn-Berger neu
			if (in_array(23, $arrayFW)) { // SoBe(neu) als ein TieBreaker gewünscht?
				if ($value->brettpunkte > 0) { 
					$array_PlayerSoBe[$value->tln_nr] += $value->brettpunkte * $array_PlayerMPunkte[$value->gegner];
				}
			}
		}
	
		// Buchholz auf Basis Mannschaftspunkte
		if ((in_array(1, $arrayFW)) OR (in_array(2, $arrayFW)) OR (in_array(12, $arrayFW))) { // normale Buchholz als TieBreaker gewünscht?
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				//$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]);
				if (!isset($array_PlayerBuchOpp[$s])) $array_PlayerBuch[$s] = 0;
				elseif (count($array_PlayerBuchOpp[$s]) == 1) $array_PlayerBuch[$s] = $array_PlayerBuchOpp[$s][0];
				else $array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]);
			}
		} 
		if (in_array(11, $arrayFW)) { // Buchholz mit Streichresultat
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				//$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]) - min($array_PlayerBuchOpp[$s]);
				if (!isset($array_PlayerBuchOpp[$s])) 
					$array_PlayerBuch1St[$s] = 0;
				elseif (count($array_PlayerBuchOpp[$s]) == 0) 
					$array_PlayerBuch1St[$s] = 0;
				elseif (count($array_PlayerBuchOpp[$s]) == 1) 
					$array_PlayerBuch1St[$s] = $array_PlayerBuchOpp[$s][0];
				elseif (count($array_PlayerBuchOpp[$s]) > 2) //== ($dg * $runden)) 
					$array_PlayerBuch1St[$s] = array_sum($array_PlayerBuchOpp[$s]) - min($array_PlayerBuchOpp[$s]);
				else $array_PlayerBuch1St[$s] = array_sum($array_PlayerBuchOpp[$s]);
			}
		}
		// Buchholz auf Basis Brettpunkte
		if ((in_array(7, $arrayFW)) OR (in_array(8, $arrayFW)) OR (in_array(18, $arrayFW))) { // normale Buchholz als TieBreaker gewünscht?
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				//$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]);
				if (!isset($array_PlayerBuchOppBP[$s])) $array_PlayerBuchBP[$s] = 0;
				elseif (count($array_PlayerBuchOppBP[$s]) == 1) $array_PlayerBuchBP[$s] = $array_PlayerBuchOppBP[$s][0];
				else $array_PlayerBuchBP[$s] = array_sum($array_PlayerBuchOppBP[$s]);
			}
		} 
		if (in_array(17, $arrayFW)) { // Buchholz mit Streichresultat
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				//$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]) - min($array_PlayerBuchOpp[$s]);
				if (!isset($array_PlayerBuchOppBP[$s])) 
					$array_PlayerBuch1St[$s] = 0;
				elseif (count($array_PlayerBuchOppBP[$s]) == 0) 
					$array_PlayerBuch1St[$s] = 0;
				elseif (count($array_PlayerBuchOpp[$s]) == 1) 
					$array_PlayerBuch1StBP[$s] = $array_PlayerBuchOppBP[$s][0];
				elseif (count($array_PlayerBuchOpp[$s]) > 2) //== ($dg * $runden)) 
					$array_PlayerBuch1StBP[$s] = array_sum($array_PlayerBuchOppBP[$s]) - min($array_PlayerBuchOppBP[$s]);
				else $array_PlayerBuch1StBP[$s] = array_sum($array_PlayerBuchOppBP[$s]);
			}
		}
	
		// BuchholzSumme  auf Basis Mannschaftspunkte
		if ((in_array(2, $arrayFW)) OR (in_array(12, $arrayFW))) { // Buchholz-Summe als TieBreaker gewünscht?
			// erneut alle Matches durchgehen -> Spieler erhalten Buchholzsummen
			foreach ($matchData as $key => $value) {
				$array_PlayerBuSum[$value->tln_nr] += $array_PlayerBuch[$value->gegner];
				// und Min-Buchholz setzen
				if ($array_PlayerBuSumMin[$value->tln_nr] > $array_PlayerBuch[$value->gegner]) {
					$array_PlayerBuSumMin[$value->tln_nr] = $array_PlayerBuch[$value->gegner];
				}
			}
		}
		if (in_array(12, $arrayFW)) { // Buchholz-Summe mit Streichresultat als TieBreaker gewünscht?
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				if ($array_PlayerBuSumMin[$s] == 9999) {
					$array_PlayerBuSumMin[$s] = 0;
				}
				$array_PlayerBuSum1St[$s] = $array_PlayerBuSum[$s] - $array_PlayerBuSumMin[$s];
			}
		}
		// BuchholzSumme auf Basis Brettpunkte
		if ((in_array(2, $arrayFW)) OR (in_array(12, $arrayFW))) { // Buchholz-Summe als TieBreaker gewünscht?
			// erneut alle Matches durchgehen -> Spieler erhalten Buchholzsummen
			foreach ($matchData as $key => $value) {
				$array_PlayerBuSumBP[$value->tln_nr] += $array_PlayerBuchBP[$value->gegner];
				// und Min-Buchholz setzen
				if ($array_PlayerBuSumMinBP[$value->tln_nr] > $array_PlayerBuchBP[$value->gegner]) {
					$array_PlayerBuSumMinBP[$value->tln_nr] = $array_PlayerBuchBP[$value->gegner];
				}
			}
		}
		if (in_array(12, $arrayFW)) { // Buchholz-Summe mit Streichresultat als TieBreaker gewünscht?
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				if ($array_PlayerBuSumMinBP[$s] == 9999) {
					$array_PlayerBuSumMinBP[$s] = 0;
				}
				$array_PlayerBuSum1StBP[$s] = $array_PlayerBuSumBP[$s] - $array_PlayerBuSumMinBP[$s];
			}
		}

		// alle Spieler durchgehen und updaten (kein vorheriges Löschen notwendig)
		for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
			// Korrektur Mannschaftspunkte
			$query = "SELECT liga, tln_nr, name, abzug, bpabzug FROM `#__clm_mannschaften`"
				. " WHERE liga = ".$id
				. " AND tln_nr = ".$s;
			$abzug = clm_core::$db->loadObjectList($query);
			if (isset($abzug[0])) {
				$array_PlayerMPunkte[$s] = $array_PlayerMPunkte[$s] - $abzug[0]->abzug;
				$array_PlayerBPunkte[$s] = $array_PlayerBPunkte[$s] - $abzug[0]->bpabzug;
			}
			if ($liga_mt == 0) {	// Liga hat keine Feinwertungen, FW1 wird genutzt, um Berliner Wertung abzuspeichern!
				$arrayFW[1] = 10;
			}
			// den TiebrSummen ihre Werte zuordnen
			$rankingData = array();
			for ($tb=1; $tb<=3; $tb++) {
				$sumTiebr[$tb] = 0;
				switch ($arrayFW[$tb]) {
					case 1: // buchholz
						$sumTiebr[$tb] = $array_PlayerBuch[$s];
						break;
					case 2: // bhhlz.-summe
						$sumTiebr[$tb] = $array_PlayerBuSum[$s];
						break;
					case 3: // sobe (alt)
						$sumTiebr[$tb] = $array_PlayeraSoBe[$s];
						break;
					case 4: // wins
						$sumTiebr[$tb] = $array_PlayerWins[$s];
						break;
					case 5: // brettpunkte
						$sumTiebr[$tb] = $array_PlayerBPunkte[$s];
						break;
					case 7: // buchholz BP
						$sumTiebr[$tb] = $array_PlayerBuchBP[$s];
						break;
					case 8: // bhhlz.-summe BP
						$sumTiebr[$tb] = $array_PlayerBuSumBP[$s];
						break;
					case 9: // mannschaftspunkte
						$sumTiebr[$tb] = $array_PlayerMPunkte[$s];
						break;
					case 10: // berliner wertung
						$sumTiebr[$tb] = $array_PlayerBerlWertung[$s];
						break;
					case 11: // bhhlz mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuch1St[$s];
						break;
					case 12: // bhhlz-sum mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuSum1St[$s];
						break;
					case 17: // bhhlz mit 1 streichresultat BP
						$sumTiebr[$tb] = $array_PlayerBuch1St[$s];
						break;
					case 18: // bhhlz-sum mit 1 streichresultat BP
						$sumTiebr[$tb] = $array_PlayerBuSum1St[$s];
						break;
					case 23: // sobe 
						$sumTiebr[$tb] = $array_PlayerSoBe[$s];
						break;
					case 51: // ordering
						$sumTiebr[$tb] = 1000 - $team[$s-1]->ordering;
						break;
					default:
						$sumTiebr[$tb] = 0;
				}
			}
			if ($p_runde != 0) {
				$query = "UPDATE #__clm_mannschaften"
					. " SET z_summanpunkte = ".$array_PlayerMPunkte[$s].", z_sumbrettpunkte = ".$array_PlayerBPunkte[$s].", z_sumwins = ".$array_PlayerWins[$s].", "
					. " z_sumtiebr1 = ".$sumTiebr[1].", z_sumtiebr2 = ".$sumTiebr[2].", z_sumtiebr3 = ".$sumTiebr[3]
					. " WHERE liga = ".$id
					. " AND tln_nr = ".$s
					;
				clm_core::$db->query($query);
			} else {				
				$query = "UPDATE #__clm_mannschaften"
					. " SET summanpunkte = ".$array_PlayerMPunkte[$s].", sumbrettpunkte = ".$array_PlayerBPunkte[$s].", sumwins = ".$array_PlayerWins[$s].", "
					. " sumtiebr1 = ".$sumTiebr[1].", sumtiebr2 = ".$sumTiebr[2].", sumtiebr3 = ".$sumTiebr[3]
					. " WHERE liga = ".$id
					. " AND tln_nr = ".$s
					;
				clm_core::$db->query($query);
			}
			
		}
	
		if ($team[0]->tiebr1 == 25 OR $team[0]->tiebr2 == 25 OR $team[0]->tiebr3 == 25) {
			$query = "SELECT * "
				." FROM `#__clm_mannschaften`"
				." WHERE liga = ".$id;
			if ($p_runde != 0) 
				$query .= " ORDER BY z_summanpunkte DESC, z_sumtiebr1 DESC, z_sumtiebr2 DESC, z_sumtiebr3 DESC, tln_nr ASC";
			else 
				$query .= " ORDER BY summanpunkte DESC, sumtiebr1 DESC, sumtiebr2 DESC, sumtiebr3 DESC, tln_nr ASC";
				;
			$players = clm_core::$db->loadObjectList($query);
			// alle Mannschaften durchgehen
			foreach ($players as $xvalue) {
				$sum_erg = 0; $id_dv = 0;
				// alle Spieler durchgehen
				foreach ($players as $yvalue) {
					if ($xvalue->tln_nr == $yvalue->tln_nr) continue;	
					// sind x und y wertungsgleich ?
					if (($p_runde == 0 AND 
						(($team[0]->tiebr1 == 25 AND $xvalue->summanpunkte == $yvalue->summanpunkte) OR			
						($team[0]->tiebr2 == 25 AND $xvalue->summanpunkte == $yvalue->summanpunkte AND $xvalue->sumtiebr1 == $yvalue->sumtiebr1)	OR		
						($team[0]->tiebr3 == 25 AND $xvalue->summanpunkte == $yvalue->summanpunkte AND $xvalue->sumtiebr1 == $yvalue->sumtiebr1 AND $xvalue->sumtiebr2 == $yvalue->sumtiebr2)))
						OR
						($p_runde != 0 AND 
						(($team[0]->tiebr1 == 25 AND $xvalue->z_summanpunkte == $yvalue->z_summanpunkte) OR			
						($team[0]->tiebr2 == 25 AND $xvalue->z_summanpunkte == $yvalue->z_summanpunkte AND $xvalue->z_sumtiebr1 == $yvalue->z_sumtiebr1)	OR		
						($team[0]->tiebr3 == 25 AND $xvalue->z_summanpunkte == $yvalue->z_summanpunkte AND $xvalue->z_sumtiebr1 == $yvalue->z_sumtiebr1 AND $xvalue->z_sumtiebr2 == $yvalue->z_sumtiebr2))))	{			
						$id_dv = 1;
						// alle Matches in DatenArray schreiben
						$query = "SELECT * FROM `#__clm_rnd_man`"
							. " WHERE lid = ".$id." AND brettpunkte IS NOT NULL"
							. " AND tln_nr = ".$xvalue->tln_nr." AND gegner = ".$yvalue->tln_nr
							;
						if ($p_runde != 0) $query .= " AND runde < ".($p_runde + 1);	
						$matchesdirect = clm_core::$db->loadObjectList($query);		
						$zdirect = count($matchesdirect);
						foreach ($matchesdirect as $mdvalue) {
							if ($mdvalue->manpunkte == $team[0]->man_remis) $sum_erg += 1;
							elseif ($mdvalue->manpunkte == $team[0]->man_sieg) $sum_erg += 2;
						}
					}
				}
				if ($id_dv == 1) {
					$query = "UPDATE #__clm_mannschaften";
					if ($p_runde == 0) {
						if ($team[0]->tiebr1 == 25) $query .= " SET sumtiebr1 = ".$sum_erg;
						elseif ($team[0]->tiebr2 == 25) $query .= " SET sumtiebr2 = ".$sum_erg;
						else $query .= " SET sumtiebr3 = ".$sum_erg;
					} else {
						if ($team[0]->tiebr1 == 25) $query .= " SET z_sumtiebr1 = ".$sum_erg;
						elseif ($team[0]->tiebr2 == 25) $query .= " SET z_sumtiebr2 = ".$sum_erg;
						else $query .= " SET z_sumtiebr3 = ".$sum_erg;
					}
					$query .= " WHERE liga = ".$id
						. " AND tln_nr = ".$xvalue->tln_nr;
					clm_core::$db->query($query);
				} else {
					if ($p_runde == 0) {
						$query = "UPDATE #__clm_mannschaften";
						if ($team[0]->tiebr1 == 25) $query .= " SET sumtiebr1 = NULL";
						elseif ($team[0]->tiebr2 == 25) $query .= " SET sumtiebr2 = NULL";
						else $query .= " SET sumtiebr3 = NULL";
					} else {
						$query = "UPDATE #__clm_mannschaften";
						if ($team[0]->tiebr1 == 25) $query .= " SET z_sumtiebr1 = NULL";
						elseif ($team[0]->tiebr2 == 25) $query .= " SET z_sumtiebr2 = NULL";
						else $query .= " SET z_sumtiebr3 = NULL";
					}
					$query .= " WHERE liga = ".$id
						. " AND tln_nr = ".$xvalue->tln_nr
						;
					clm_core::$db->query($query);
				}
			}
		}
	
		$query = "SELECT id, name, tln_nr"
			." FROM `#__clm_mannschaften`"
			." WHERE liga = ".$id;
		if ($liga_mt == 0) {
			if ($p_runde == 0) {
				$query .= " ORDER BY summanpunkte DESC, sumbrettpunkte DESC ";
				if ($b_wertung == 0 AND $order == 1) $query .= ", ordering ASC";
				if ($b_wertung == 3 AND $order == 1) $query .= ", sumtiebr1 DESC, ordering ASC";
				if ($b_wertung == 3 AND $order == 0) $query .= ", sumtiebr1 DESC";
				if ($b_wertung == 4 AND $order == 1) $query .= ", ordering ASC, sumtiebr1 DESC";
				if ($b_wertung == 4 AND $order == 0) $query .= ", sumtiebr1 DESC";
			} else {
				$query .= " ORDER BY z_summanpunkte DESC, z_sumbrettpunkte DESC ";
				if ($b_wertung == 0 AND $order == 1) $query .= ", ordering ASC";
				if ($b_wertung == 3 AND $order == 1) $query .= ", z_sumtiebr1 DESC, ordering ASC";
				if ($b_wertung == 3 AND $order == 0) $query .= ", z_sumtiebr1 DESC";
				if ($b_wertung == 4 AND $order == 1) $query .= ", ordering ASC, z_sumtiebr1 DESC";
				if ($b_wertung == 4 AND $order == 0) $query .= ", z_sumtiebr1 DESC";
			}
			$query .= ", tln_nr ASC";
		} else {
			if ($p_runde == 0) {
				$query .= " ORDER BY summanpunkte DESC, sumtiebr1 DESC, sumtiebr2 DESC, sumtiebr3 DESC, tln_nr ASC";
			} else {
				$query .= " ORDER BY z_summanpunkte DESC, z_sumtiebr1 DESC, z_sumtiebr2 DESC, z_sumtiebr3 DESC, tln_nr ASC";
			}
		}
		$players = clm_core::$db->loadObjectList($query); 
		// rankingPos umsortieren
		$rankingPos = 0;
		// alle Spieler durchgehen
		foreach ($players as $value) {
			if ($value->name != "spielfrei") {
				$rankingPos++;
				$out = $rankingPos;
				} else { $out = 0; }
				$query = "UPDATE #__clm_mannschaften";
				if ($p_runde == 0) 
					$query .= " SET rankingpos = " . $out;
				else 
					$query .= " SET z_rankingpos = " . $out;
				$query .= " WHERE liga = ".$id
					. " AND tln_nr = ".$value->tln_nr
					;
				clm_core::$db->query($query);
		}
	} else {
		// Für Turniere noch nicht umgestellt
	}
	return array(true, "m_ligaCalculateRankingSuccess"); 
}
?>
