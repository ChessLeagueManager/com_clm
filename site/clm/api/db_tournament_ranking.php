<?php
	/**
	* errechnet/aktualisiert Rangliste/Punktesummen eines Mannschaftsturnier
	*/
	function clm_api_db_tournament_ranking($id,$group=true) {
		$id = clm_core::$load->make_valid($id, 0, -1);
		if($group) {
	
		// Wertpunkte berechnen
		$stamm = clm_core::$db->liga->get($id)->stamm;
	
		// Mannschaftsdaten sammeln
		$query = "SELECT a.sid, a.lid, a.runde, a.dg, a.paar, a.heim "
			." FROM #__clm_rnd_man as a "
			." WHERE a.lid = ".$id;
		$mdata	= clm_core::$db->loadObjectList($query);
	
		foreach ($mdata as $mdata) {
			// Wertpunkte Heim berechnen
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
		$id_stamm 	= $team[0]->stamm;
		$sid = $team[0]->sid;
		
		// "spielfrei(e)" Mannschaft suchen
		$query = " SELECT COUNT(id) FROM #__clm_mannschaften as a "
			." WHERE a.liga = ".$id
			." AND a.name = 'spielfrei'"
			." ORDER BY a.tln_nr "
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
						. " SET manpunkte = 0, brettpunkte = 0, gemeldet = 62, zeit = '$now'";
					if (($runden_modus == 4) OR ($runden_modus == 5)) 
						$query .= " , ko_decision = 1";	
					$query .= " WHERE lid = ".$id
						. " AND dg = ".$value->dg." AND runde = ".$value->runde
						. " AND tln_nr = ".$value->tln_nr." AND paar = ".$value->paar
						;
				   clm_core::$db->query($query);
					
					$query = "UPDATE `#__clm_rnd_man`"
						. " SET manpunkte = ".$man_sieg.", brettpunkte = ".$id_stamm.", gemeldet = 62, zeit = '$now'";
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
		$array_PlayerBPunkte = array();
		$array_PlayerBerlWertung = array();
		$array_PlayerBuch = array();
		$array_PlayerBuchOpp = array();
		$array_PlayeraSoBe = array();
		$array_PlayerSoBe = array();
		$array_PlayerBuSum = array();
		$array_PlayerWins = array();
		for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
			$array_PlayerMPunkte[$s] = 0;
			$array_PlayerBPunkte[$s] = 0;
			$array_PlayerBerlWertung[$s] = 0;
			$array_PlayerBuch[$s] = 0;
			$array_PlayeraSoBe[$s] = 0;
			$array_PlayerSoBe[$s] = 0;
			$array_PlayerBuSum[$s] = 0;
			$array_PlayerWins[$s] = 0;
		}
		
		// alle Matches in DatenArray schreiben
		$query = "SELECT tln_nr, gegner, brettpunkte, manpunkte FROM `#__clm_rnd_man`"
				. " WHERE lid = ".$id." AND brettpunkte IS NOT NULL"
				;
		$matchData = clm_core::$db->loadObjectList($query);
		// alle Matches in DatenArray schreiben
		$query = "SELECT tln_nr, brett, punkte FROM `#__clm_rnd_spl`"
				. " WHERE lid = ".$id." AND punkte IS NOT NULL"
				;
		$einzelData = clm_core::$db->loadObjectList($query);
		
		// Punkte/Siege
		// alle Matches durchgehen -> Spieler erhalten Punkte und Wins
		foreach ($matchData as $key => $value) {
			if ($value->manpunkte == $man_sieg) { // Mannschaftssieg
				$array_PlayerWins[$value->tln_nr] += 1;
			}
			$array_PlayerMPunkte[$value->tln_nr] += $value->manpunkte;
			$array_PlayerBPunkte[$value->tln_nr] += $value->brettpunkte;
		}
		
		// Berliner Wertung
		// alle Einzels durchgehen -> Mannschaften erhalten Wertpunkte
		foreach ($einzelData as $key => $valuee) {
			$array_PlayerBerlWertung[$valuee->tln_nr] += $valuee->punkte * ($id_stamm + 1 - $valuee->brett);
		}
	
		// Buchholz & Sonneborn-Berger
		// erneut alle Matches durchgehen -> Spieler erhalten Feinwertungen
		foreach ($matchData as $key => $value) {
			// Buchholz
			if (in_array(1, $arrayFW) OR in_array(2, $arrayFW) OR in_array(11, $arrayFW)) { // beliebige Buchholz als TieBreaker gewünscht?
				$array_PlayerBuchOpp[$value->tln_nr][] = $array_PlayerBPunkte[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
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
	
		// Buchholz
		if (in_array(1, $arrayFW)) { // normale Buchholz als TieBreaker gewünscht?
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]);
			}
		} elseif (in_array(11, $arrayFW)) { // Buchholz mit Streichresultat
			for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
				$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]) - min($array_PlayerBuchOpp[$s]);
			}
		}
	
		// BuchholzSumme
		if (in_array(2, $arrayFW)) { // Buchholz-Summe als TieBreaker gewünscht?
			// erneut alle Matches durchgehen -> Spieler erhalten Buchholzsummen
			foreach ($matchData as $key => $value) {
				//echo "<br>matchdata: "; var_dump($value);
				//echo "<br>BuSum: "; var_dump($array_PlayerBuSum);
				//echo "<br>Buch: "; var_dump($array_PlayerBuch);
				$array_PlayerBuSum[$value->tln_nr] += $array_PlayerBuch[$value->gegner];
			}
		}
		
		// alle Spieler durchgehen und updaten (kein vorheriges Löschen notwendig)
		for ($s=1; $s<= $team[0]->teil; $s++) { // alle Startnummern durchgehen
			// den TiebrSummen ihre Werte zuordnen
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
					case 6: // berliner wertung
						$sumTiebr[$tb] = $array_PlayerBerlWertung[$s];
						break;
					case 11: // bhhlz mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuch[$s];
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
			$query = "UPDATE #__clm_mannschaften"
					. " SET summanpunkte = ".$array_PlayerMPunkte[$s].", sumbrettpunkte = ".$array_PlayerBPunkte[$s].", sumwins = ".$array_PlayerWins[$s].", "
					. " sumTiebr1 = ".$sumTiebr[1].", sumTiebr2 = ".$sumTiebr[2].", sumTiebr3 = ".$sumTiebr[3]
					. " WHERE liga = ".$id
					. " AND tln_nr = ".$s
					;
			clm_core::$db->query($query);
			
		}
	
		if ($team[0]->tiebr1 == 25 OR $team[0]->tiebr2 == 25 OR $team[0]->tiebr3 == 25) {
			$query = "SELECT * "
				." FROM `#__clm_mannschaften`"
				." WHERE liga = ".$id
				." ORDER BY summanpunkte DESC, sumtiebr1 DESC, sumtiebr2 DESC, sumtiebr3 DESC, tln_nr ASC"
				;
			$players = clm_core::$db->loadObjectList($query);
			// alle Mannschaften durchgehen
			foreach ($players as $xvalue) {
				$sum_erg = 0; $id_dv = 0;
				// alle Spieler durchgehen
				foreach ($players as $yvalue) {
					if ($xvalue->tln_nr == $yvalue->tln_nr) continue;	
					// sind x und y wertungsgleich ?
					if (($team[0]->tiebr1 == 25 AND $xvalue->summanpunkte == $yvalue->summanpunkte) OR			
						($team[0]->tiebr2 == 25 AND $xvalue->summanpunkte == $yvalue->summanpunkte AND $xvalue->sumtiebr1 == $yvalue->sumtiebr1)	OR		
						($team[0]->tiebr3 == 25 AND $xvalue->summanpunkte == $yvalue->summanpunkte AND $xvalue->sumtiebr1 == $yvalue->sumtiebr1 AND $xvalue->sumtiebr2 == $yvalue->sumtiebr2)) {			
						$id_dv = 1;
						// alle Matches in DatenArray schreiben
						$query = "SELECT * FROM `#__clm_rnd_man`"
							. " WHERE lid = ".$id." AND brettpunkte IS NOT NULL"
							. " AND tln_nr = ".$xvalue->tln_nr." AND gegner = ".$yvalue->tln_nr
							;
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
					if ($team[0]->tiebr1 == 25) $query .= " SET sumTiebr1 = ".$sum_erg;
					elseif ($team[0]->tiebr2 == 25) $query .= " SET sumTiebr2 = ".$sum_erg;
					else $query .= " SET sumTiebr3 = ".$sum_erg;
					$query .= " WHERE liga = ".$id
						. " AND tln_nr = ".$xvalue->tln_nr
						;
					clm_core::$db->query($query);
				} else {
					$query = "UPDATE #__clm_mannschaften";
					if ($team[0]->tiebr1 == 25) $query .= " SET sumTiebr1 = NULL";
					elseif ($team[0]->tiebr2 == 25) $query .= " SET sumTiebr2 = NULL";
					else $query .= " SET sumTiebr3 = NULL";
					$query .= " WHERE liga = ".$id
						. " AND tln_nr = ".$xvalue->tln_nr
						;
					clm_core::$db->query($query);
				}
			}
		}
	
		$query = "SELECT id, name, tln_nr"
			." FROM `#__clm_mannschaften`"
			." WHERE liga = ".$id
			." ORDER BY summanpunkte DESC, sumtiebr1 DESC, sumtiebr2 DESC, sumtiebr3 DESC, tln_nr ASC"
			;
		$players = clm_core::$db->loadObjectList($query); 
		// rankingPos umsortieren
		$rankingPos = 0;
		// alle Spieler durchgehen
		foreach ($players as $value) {
			if ($value->name != "spielfrei") {
				$rankingPos++;
				$out = $rankingPos;
				} else { $out = 0; }
						$query = "UPDATE #__clm_mannschaften"
					. " SET rankingpos = " . $out
					. " WHERE liga = ".$id
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
