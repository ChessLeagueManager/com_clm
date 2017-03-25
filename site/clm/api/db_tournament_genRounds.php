<?php
function clm_api_db_tournament_genRounds($id, $group = true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	// ************************************* //
	// ******* Mannschaftsturniere ********* //
	// ************************************* //
	if ($group) {
		$sid = clm_core::$db->liga->get($id)->sid;
		$teil = clm_core::$db->liga->get($id)->teil;
		$dg = clm_core::$db->liga->get($id)->durchgang;
		$heimrecht = clm_core::$db->liga->get($id)->heim;
		$runden = clm_core::$db->liga->get($id)->runden;
		$rnd_mode = clm_core::$db->liga->get($id)->runden_modus;

		// Modus implementiert?
		if ($rnd_mode < 1 || $rnd_mode > 5) {
			return array(false, "e_teamtournamentModusNotSupported");
		// Runden bereits erstellt?
		} elseif (clm_core::$db->liga->get($id)->rnd == 1) {
			return array(false, "w_teamtournamentRoundsAlreadyCreated");
		}

		$lang = clm_core::$lang->tournament_group;
		
		// Runden (Termine) anlegen
		for ($y = 1;$y < 1 + $dg;$y++) {
			for ($x = 1;$x < 1 + $runden;$x++) {
				$nr = $x + ($y - 1) * $runden;
				$name = $lang->LIGEN_STD_ROUND . " " . $x;
				if ($dg == 2) {
					if ($y == 1) $name.= " (" . $lang->LIGEN_STD_HIN . ")";
					if ($y == 2) $name.= " (" . $lang->LIGEN_STD_RUECK. ")";
				}
				if ($dg > 2) {
					$name .= " (".$lang->LIGEN_STD_DG." ".$y.")";
				}
				if ($rnd_mode == 4) { // KO System
					$langString = 'ROUND_KO_' . ($runden - $x + 1);
					$name = $lang->$langString;
				}
				if (($rnd_mode == 5) AND $x < $runden) { // KO System
					$langString = 'ROUND_KO_' . ($runden - $x);
					$name = $lang->$langString;
				}
				if (($rnd_mode == 5) AND $x == $runden) { // KO System
					$name = $lang->ROUND_KO_S3;
				}
				$query = " INSERT INTO #__clm_runden_termine (`sid`,`name`,`liga`,`nr`,`meldung`,`sl_ok` "
					. " ,`datum`,`enddatum`,`deadlineday`,`zeit`,`edit_zeit`,`checked_out_time`,`published` ) " 
					. " VALUES ('".$sid."','"
					. clm_core::$db->escape($name)."','".$id."','".$nr."','0','0'"
					. " ,'1970-01-01','1970-01-01','1970-01-01','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','".clm_core::$db->liga->get($id)->published."') ";
				clm_core::$db->query($query);
			}
		}

		// Runden der Mannschaften setzen (#__clm_rnd_man)
		$n = $teil; // Anzahl Spieler
		if ($n % 2 != 0) {
			$n++;
		} // Anzahl gerade machen
		for ($dg_dg = 1;$dg_dg < 1 + $dg;$dg_dg++) {
			$y = 1;
			if ($dg_dg % 2 != 0) {
				$dgh = 1;
				$dgg = 0;
			} else {
				$dgh = 0;
				$dgg = 1;
			}
			switch ($rnd_mode) {
				case 1:
				case 2:
					// Modus festlegen 1 = Normal; 2 = zentrale Endrunde
					if ($rnd_mode == 1) {
						$rnd_one = 1;
					}
					if ($rnd_mode == 2) {
						$rnd_one = $runden;
					}
					// Runde 1
					for ($f = 1;$f < 1 + $n / 2;$f++) {
						if ($heimrecht == 0) {
							$heim = $f;
							$gast = $n - $f + 1;
						} else {
							$heim = $n - $f + 1;
							$gast = $f;
						}
						$query = "INSERT INTO #__clm_rnd_man " 
							. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner`"
							. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
							. " VALUES ('$sid','$id','$rnd_one','$f','$dg_dg','$dgh','$heim','$gast'"
							. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
							. " ('$sid','$id','$rnd_one','$f','$dg_dg','$dgg','$gast','$heim'"
							. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
						clm_core::$db->query($query);
					}
					// Ende Runde 1
					for ($p = 2;$p < $n;$p++) {
						// Modus festlegen 1 = Normal; 2 = zentrale Enderunde
						if ($rnd_mode == "1") {
							$rnd_cnt = $p;
						}
						if ($rnd_mode == "2") {
							$rnd_cnt = $p - 1;
						}
						// Paarungsschleife
						if ($p % 2 != 0) {
							$gerade = 0;
							$y++;
						} else {
							$gerade = 1;
						}
						///////////////
						// 1.Element //
						///////////////
						if ($gerade == 0) {
							if ($heimrecht == 0) {
								$heim = $y;
								$gast = $n;
							} else {
								$heim = $n;
								$gast = $y;
							}
						} else {
							if ($heimrecht == 0) {
								$heim = $n;
								$gast = ($n / 2) + $y;
							} else {
								$heim = ($n / 2) + $y;
								$gast = $n;
							}
						}
						$query = "INSERT INTO #__clm_rnd_man " 
							. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner`" 
							. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
							. " VALUES ('$sid','$id','$rnd_cnt','1','$dg_dg','$dgh','$heim','$gast' " 
							. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
							. " ('$sid','$id','$rnd_cnt','1','$dg_dg','$dgg','$gast','$heim' "
							. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
						clm_core::$db->query($query);
						///////////////////
						// ab 2. Element //
						///////////////////
						// ungerade Runde
						if ($gerade == 0) {
							for ($z = 2;$z < ($y + 1);$z++) {
								if ($heimrecht == 0) {
									$heim = $z + $y - 1;
									$gast = $p - $z - $y + 2;
								} else {
									$heim = $p - $z - $y + 2;
									$gast = $z + $y - 1;
								}
								$query = "INSERT INTO #__clm_rnd_man " 
									. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner`" 
									. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
									. " VALUES ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgh','$heim','$gast' " 
									. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
									. " ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgg','$gast','$heim' "
									. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
								clm_core::$db->query($query);
							}
							for ($z = ($y + 1);$z < (($n / 2) + 1);$z++) {
								if ($heimrecht == 0) {
									$heim = $z + $y - 1;
									$gast = $n + $p - $z - $y + 1;
								} else {
									$heim = $n + $p - $z - $y + 1;
									$gast = $z + $y - 1;
								}
								$query = "INSERT INTO #__clm_rnd_man " 
									. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner`" 
									. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
									. " VALUES ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgh','$heim','$gast' " 
									. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
									. " ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgg','$gast','$heim' "
									. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
								clm_core::$db->query($query);
							}
						}
						// gerade Runde //
						else {
							for ($z = 2;$z < (($n / 2) - $y + 1);$z++) {
								if ($heimrecht == 0) {
									$heim = ($n / 2) + $y + $z - 1;
									$gast = ($n / 2) + $y - $z + 1;
								} else {
									$heim = ($n / 2) + $y - $z + 1;
									$gast = ($n / 2) + $y + $z - 1;
								}
								$query = "INSERT INTO #__clm_rnd_man " 
									. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` " 
									. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
									. " VALUES ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgh','$heim','$gast' " 
									. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
									. " ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgg','$gast','$heim' "
									. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
								clm_core::$db->query($query);
							}
							for ($z = (($n / 2) - $y + 1);$z < ($n / 2) + 1;$z++) {
								if ($heimrecht == 0) {
									$heim = $p - ($n / 2) - $y + $z;
									$gast = ($n / 2) + $y - $z + 1;
								} else {
									$heim = ($n / 2) + $y - $z + 1;
									$gast = $p - ($n / 2) - $y + $z;
								}
								$query = "INSERT INTO #__clm_rnd_man " 
									. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` " 
									. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
									. " VALUES ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgh','$heim','$gast' " 
									. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
									. " ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgg','$gast','$heim' "
									. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
								clm_core::$db->query($query);
							}
						}
					}
					//Anlegen leerer Runden, 
					//wenn die Rundenanzahl im Ligastammsatz die nötige Rundenzahl entspr. Teilnehmerzahl überschreitet
					//Das ermöglicht das manuelle Nachpflegen, was auch zwingend in einen solchen Fall notwendig ist
					if (($dg == 1) AND ($rnd_mode == "1")) {     //nur für Ligen mit einem Durchgängen zugelassen
																 //nur für Standardmodus nach FIDE-Tabelle zugelassen	 
						while ($rnd_cnt < $runden) {
							$rnd_cnt++;
							for ($z = 1; $z < ($n/2)+1; $z++) {
								$query	= "INSERT INTO #__clm_rnd_man "
									." ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` "
									. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
									." VALUES ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgh',0,0 "
									. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
									." ('$sid','$id','$rnd_cnt','$z','$dg_dg','$dgg',0,0 "
									." ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
								clm_core::$db->query($query);
							}
						}	
					}
				break;
				case 3: //Schweizer System mtmt
					$rnd_cnt = 1;
					// Runde 1
					for ($f = 1;$f < 1 + $n / 2;$f++) {
						if ($f % 2 != 0) {
							$heim = $f;
							$gast = $n / 2 + $f;
						} else {
							$gast = $f;
							$heim = $n / 2 + $f;
						}
						$query = "INSERT INTO #__clm_rnd_man " 
							. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` " 
							. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
							. " VALUES ('$sid','$id','$rnd_cnt','$f','$dg_dg','$dgh','$heim','$gast' " 
							. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
							. " ('$sid','$id','$rnd_cnt','$f','$dg_dg','$dgg','$gast','$heim' "
							." ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
						clm_core::$db->query($query);
					}
					// Ende Runde 1
					// weitere Runden - leere Sätze
					for ($rnd_cnt = 2;$rnd_cnt < 1 + $runden;$rnd_cnt++) {
						for ($f = 1;$f < 1 + $n / 2;$f++) {
							$heim = 0;
							$gast = 0;
							$query = "INSERT INTO #__clm_rnd_man " 
							. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` " 
							. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
							. " VALUES ('$sid','$id','$rnd_cnt','$f','$dg_dg','$dgh','$heim','$gast' " 
							. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
							. " ('$sid','$id','$rnd_cnt','$f','$dg_dg','$dgg','$gast','$heim' "
							." ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
							clm_core::$db->query($query);
						}
					}
				break;
				case 4: //KO System ohne kleines Finale
					
				case 5: //KO System mit kleinem Finale
					$rnd_nn = 0; // notwendige KO Runden für nn Teilnehmer
					while ($n > (pow(2, $rnd_nn))) {
						$rnd_nn++;
					}
					$nn = pow(2, $rnd_nn); // Anzahl auf Potenz von 2 setzen
					$rnd_cnt = 0;
					while ($rnd_nn > $rnd_cnt) {
						$rnd_cnt++;
						for ($f = 1;$f < 1 + pow(2, ($rnd_nn - $rnd_cnt));$f++) {
							if ($rnd_cnt == 1) {
								$heim = $f;
								$gast = pow(2, $rnd_nn + 1 - $rnd_cnt) + 1 - $f;
							} else {
								$heim = 0;
								$gast = 0;
							}
							$query = "INSERT INTO #__clm_rnd_man " 
								. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` " 
								. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
								. " VALUES ('$sid','$id','$rnd_cnt','$f','$dg_dg','$dgh','$heim','$gast' " 
								. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
								. " ('$sid','$id','$rnd_cnt','$f','$dg_dg','$dgg','$gast','$heim' "
								." ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
							clm_core::$db->query($query);
						}
					}
					if ($rnd_mode == 5) { //KO System mit kleinem Finale
						$rnd_cnt++; // zusätzliche Runde
						$f = 1; // kleines Finale ist einzige Paarung in zus. Runde
						$query = "INSERT INTO #__clm_rnd_man " 
							. " ( `sid`, `lid`, `runde`, `paar`, `dg`, `heim`, `tln_nr`, `gegner` " 
							. ", `zeit`, `edit_zeit`, `dwz_zeit`, `checked_out_time`, `comment`, `pdate`) " 
							. " VALUES ('$sid','$id','$rnd_cnt','$f','$dg_dg','$dgh','$heim','$gast' " 
							. " ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01'), " 
							. " ('$sid','$id','$rnd_cnt','$f','$dg_dg','$dgg','$gast','$heim' "
							." ,'1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','1970-01-01 00:00:00','','1970-01-01')";
							clm_core::$db->query($query);
					}
					// Ende Runde 1
					
				break;
			}
		}

		// Rundenbyte setzen
		clm_core::$db->liga->get($id)->rnd = 1;

	// ************************************* //
	// ************* Turniere ************** //
	// ************************************* //
	} else {
		$sql = "SELECT * FROM #__clm_turniere WHERE id=" . $id;
		$row = clm_core::$db->loadObjectList($sql);
		// vorerst nur eine ID bearbeiten!
		$turnierid = $id;
		$row = $row[0];
		// Modus implementiert?
		if ($row->typ <= 0 OR $row->typ == 4 OR $row->typ > 6) {
			return array(false, "e_tournamentModusNotSupported");
			// Runden bereits erstellt?
			
		} elseif ($row->rnd == 1) {
			return array(false, "w_tournamentRoundsAlreadyCreated");
		}
		clm_core::$api->direct("db_tournament_delDWZ", array($id, false));
		// INIT der Turnierdaten
		$sid = $row->sid;
		$dg = $row->dg;
		if ($row->typ == 3) {
			// Rundenanzahl errechnen
			$runden = ceil(log($row->teil) / log(2));
		} elseif ($row->typ == 5) {
			// Rundenanzahl errechnen
			$runden = ceil(log($row->teil) / log(2)) + 1;
		} else {
			$runden = $row->runden;
		}
		$publish = $row->published; // für Runden-Termine
		// Startdatum gegeben UND (kein ODER gleiches Enddatum) - also Eintagesturnier!
		if ($row->dateStart != '0000-00-00' AND $row->dateStart != '1970-01-01' AND ($row->dateEnd == '0000-00-00' OR $row->dateEnd == '1970-01-01' OR $row->dateEnd == $row->dateStart)) {
			$datum = $row->dateStart;
		} else {
			$datum = '1970-01-01';
		}
		// Anzahl Spieler
		$n = $row->teil;
		// Anzahl gerade machen
		if ($n % 2 != 0) {
			$n++;
		}
		// Anzahl Partien pro Runde
		$gameCount = $n / 2;
		///////////////////////
		// KO-System         //
		///////////////////////
		if ($row->typ == 3 or $row->typ == 5) {
			// array für alle DB-Einträge
			$sqlValuesStrings = array();
			// Rundenzähler
			$roundCount = 0;
			if ($row->typ == 5) {
				$runden--;
			}
			// alle Runden durchgehen - werden umgekehrt benannt -> 3 = VF, 2 = HF, 1 = Finale  ?warum? Änderung mit 1.4.3
			//for ($r=$runden; $r>=1; $r--) {
			for ($r = 1;$r <= $runden;$r++) {
				// Anzahl Matches
				//if ($r == $runden) { // Auftaktunde
				if ($r == 1) { // Auftaktunde
					$matchCount = ($row->teil - (pow(2, $runden) / 2));
				} else {
					//$matchCount = (pow(2, $r)/2);
					$matchCount = (pow(2, ($runden - $r + 1)) / 2);
				}
				// alle Matches durchgehen
				for ($m = 1;$m <= $matchCount;$m++) {
					$sqlValuesStrings[] = "('$sid','$turnierid','$r','$m','1','1')";
					$sqlValuesStrings[] = "('$sid','$turnierid','$r','$m','1','0')";
				}
			}
			// kleines Finale einbauen
			if ($row->typ == 5) {
				$runden++;
				$sqlValuesStrings[] = "('$sid','$turnierid','$runden','1','1','1')";
				$sqlValuesStrings[] = "('$sid','$turnierid','$runden','1','1','0')";
			}
			// alle Partien eintragen
			$query = "INSERT INTO #__clm_turniere_rnd_spl" . " (`sid`, `turnier`, `runde`, `brett`, `dg`, `heim`)" . " VALUES " . implode(", ", $sqlValuesStrings);
			clm_core::$db->query($query);
			///////////////////////
			// Vollrunden System //
			///////////////////////
			
		} elseif ($row->typ == 2) {
			// array für alle DB-Einträge
			$sqlValuesStrings = array();
			// Rundenzähler
			$roundCount = 0;
			// Schleife über Durchgänge
			for ($i = 1;$i < 1 + $dg;$i++) {
				// INIT
				$y = 1; // ?
				// Heimrecht
				// (in Einzelturnieren canceln? - nein, wird zur Unterscheidung der benötigten (gedoppelten) datensätze genutzt)
				if ($i % 2 != 0) {
					$dgh = 1;
					$dgg = 0;
				} else {
					$dgh = 0;
					$dgg = 1;
				}
				// Runde 1
				// Rundenzähler aktuell
				$roundCount++;
				$round = $roundCount;
				while ($round > $runden) {
					$round-= $runden;
				}
				for ($f = 1;$f <= $gameCount;$f++) {
					$heim = $f;
					$gast = $n - $f + 1;
					$sqlValuesStrings[] = "('$sid','$turnierid','$round','$f','$i','$dgh','$heim','$heim','$gast')";
					$sqlValuesStrings[] = "('$sid','$turnierid','$round','$f','$i','$dgg','$gast','$gast','$heim' )";
				}
				// Ende Runde 1
				// RUnden 2 bis x
				for ($p = 2;$p < $n;$p++) {
					// Rundenzähler aktuell
					$roundCount++;
					$round = $roundCount;
					while ($round > $runden) {
						$round-= $runden;
					}
					// Paarungsschleife
					if ($p % 2 != 0) {
						$gerade = 0;
						$y++;
					} else {
						$gerade = 1;
					}
					///////////////
					// 1.Element //
					///////////////
					if ($gerade == 0) {
						$heim = $y;
						$gast = $n;
					} else {
						$heim = $n;
						$gast = ($n / 2) + $y;
					}
					$sqlValuesStrings[] = "('$sid','$turnierid','$round','1','$i','$dgh','$heim','$heim','$gast')";
					$sqlValuesStrings[] = "('$sid','$turnierid','$round','1','$i','$dgg','$gast','$gast','$heim')";
					///////////////////
					// ab 2. Element //
					///////////////////
					// ungerade Runde
					if ($gerade == 0) {
						for ($z = 2;$z < ($y + 1);$z++) {
							$heim = $z + $y - 1;
							$gast = $p - $z - $y + 2;
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgh','$heim','$heim','$gast')";
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgg','$gast','$gast','$heim')";
						}
						for ($z = ($y + 1);$z < (($n / 2) + 1);$z++) {
							$heim = $z + $y - 1;
							$gast = $n + $p - $z - $y + 1;
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgh','$heim','$heim','$gast')";
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgg','$gast','$gast','$heim')";
						}
					} else { // gerade Runde //
						for ($z = 2;$z < (($n / 2) - $y + 1);$z++) {
							$heim = ($n / 2) + $y + $z - 1;
							$gast = ($n / 2) + $y - $z + 1;
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgh','$heim','$heim','$gast')";
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgg','$gast','$gast','$heim')";
						}
						for ($z = (($n / 2) - $y + 1);$z < ($n / 2) + 1;$z++) {
							$heim = $p - ($n / 2) - $y + $z;
							$gast = ($n / 2) + $y - $z + 1;
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgh','$heim','$heim','$gast')";
							$sqlValuesStrings[] = "('$sid','$turnierid','$round','$z','$i','$dgg','$gast','$gast','$heim')";
						}
					}
					// Ende gerade/ungerade Runde
					
				}
				// Ende Runden 2 bis x
				
			}
			// Ende Durchgänge
			// alle Partien eintragen
			$query = "INSERT INTO #__clm_turniere_rnd_spl" . " (`sid`, `turnier`, `runde`, `brett`, `dg`, `heim`, `tln_nr`, `spieler`, `gegner`)" . " VALUES " . implode(", ", $sqlValuesStrings);
			clm_core::$db->query($query);
			///////////////
			// CH System //
			///////////////
			
		} elseif ($row->typ == 1) {
			for ($dg_dg = 1;$dg_dg < 1 + $dg;$dg_dg++) {
				$y = 1;
				if ($dg_dg % 2 != 0) {
					$dgh = 1;
					$dgg = 0;
				} else {
					$dgh = 0;
					$dgg = 1;
				}
				// Runde 1
				if ($row->teil % 2 == 0) { // ohne 'spielfrei'
					for ($f = 1;$f <= $gameCount;$f++) {
						if ($f % 2 == 0) {
							$gast = $f;
							$heim = $n / 2 + $f; // $n-$f+1;
							
						} else {
							$heim = $f;
							$gast = $n / 2 + $f; // $n-$f+1;
							
						}
						$query = "INSERT INTO #__clm_turniere_rnd_spl " . " ( `sid`, `turnier`, `runde`, `brett`, `dg`, `heim`, `tln_nr`, `spieler`, `gegner` ) " . " VALUES ('$sid','$turnierid','1','$f','$dg_dg','$dgh','$heim','$heim','$gast') " . " ,('$sid','$turnierid','1','$f','$dg_dg','$dgg','$gast','$gast','$heim' )";
						clm_core::$db->query($query);
					}
				} else { // mit 'spielfrei' - letzter Spieler bekommt das Freilos
					// ein Match weniger in der Schleife!
					for ($f = 1;$f <= $gameCount - 1;$f++) {
						if ($f % 2 == 0) {
							$gast = $f;
							$heim = ($n - 2) / 2 + $f; // $n-$f+1;
							
						} else {
							$heim = $f;
							$gast = ($n - 2) / 2 + $f; // $n-$f+1;
							
						}
						$query = "INSERT INTO #__clm_turniere_rnd_spl " . " ( `sid`, `turnier`, `runde`, `brett`, `dg`, `heim`, `tln_nr`, `spieler`, `gegner` ) " . " VALUES ('$sid','$turnierid','1','$f','$dg_dg','$dgh','$heim','$heim','$gast') " . " ,('$sid','$turnierid','1','$f','$dg_dg','$dgg','$gast','$gast','$heim' )";
						clm_core::$db->query($query);
					}
					// letztes Match: Freilos
					$gast = $n;
					$heim = ($n - 1);
					$query = "INSERT INTO #__clm_turniere_rnd_spl " . " ( `sid`, `turnier`, `runde`, `brett`, `dg`, `heim`, `tln_nr`, `spieler`, `gegner` ,`ergebnis`) " . " VALUES ('$sid','$turnierid','1','$f','$dg_dg','$dgh','$heim','$heim','$gast', '5') " . " ,('$sid','$turnierid','1','$f','$dg_dg','$dgg','$gast','$gast','$heim', '4')";
					clm_core::$db->query($query);
				}
				// Ende Runde 1
				// Ab 2. Runde
				for ($p = 2;$p <= $runden;$p++) { // Bugfix: <= statt <
					$sqlValuesStrings = array();
					for ($f = 1;$f <= $gameCount;$f++) {
						$sqlValuesStrings[] = "('$sid','$turnierid','$p','$f','$dg_dg','$dgh')";
						$sqlValuesStrings[] = "('$sid','$turnierid','$p','$f','$dg_dg','$dgg')";
					}
					// alle Runden abspeichern
					$query = "INSERT INTO #__clm_turniere_rnd_spl" . " ( `sid`, `turnier`, `runde`, `brett`,`dg`, `heim`)" . " VALUES " . implode(", ", $sqlValuesStrings);
					clm_core::$db->query($query);
				}
			}
			///////////////////
			// freies System //
			///////////////////
			
		} elseif ($row->typ == 6) {
			// Anzahl Spieler
			$n = $row->teil;
			// Anzahl gerade machen
			if ($n % 2 != 0) {
				$n++;
			}
			// angesetzte Partien pro Runde
			$gameCount = $n / 2;
			// alle Runden
			for ($p = 1;$p <= $runden;$p++) {
				$sqlValuesStrings = array();
				for ($f = 1;$f <= $gameCount;$f++) {
					$sqlValuesStrings[] = "('$sid','$turnierid','$p','$f','1','1')";
					$sqlValuesStrings[] = "('$sid','$turnierid','$p','$f','1','0')";
				}
				// alle Runden abspeichern
				$query = "INSERT INTO #__clm_turniere_rnd_spl" . " ( `sid`, `turnier`, `runde`, `brett`,`dg`, `heim`)" . " VALUES " . implode(", ", $sqlValuesStrings);
				clm_core::$db->query($query);
			}
		}
		// Ende Typ/mpdus
		// Runden-Termine anlegen
		$sqlValuesStrings = array();
		$lang = clm_core::$lang->tournament;
		for ($y = 1;$y < 1 + $dg;$y++) { // dg
			for ($x = 1;$x < 1 + $runden;$x++) {
				$nr = $x; // + ($y - 1) * $runden;
				if ($row->typ != 3 AND $row->typ != 5) {
					$name = $lang->round . " " . $x;
					if ($dg == 2 AND $y == 1) $name.= " (" . $lang->TOURNAMENT_STAGE_1 . ")";
					elseif ($dg == 2 AND $y == 2) $name.= " (" . $lang->TOURNAMENT_STAGE_2 . ")";
					elseif ($dg > 2) $name.= " (DG " . $y . ")";
				} elseif ($row->typ == 3) {
					$t = 'ROUND_KO_' . ($runden - $nr + 1);
					$name = $lang->$t;
				} elseif ($row->typ == 5) {
					$t = 'ROUND_KO_' . ($runden - $nr);
					$name = $lang->$t;
				}
				$sqlValuesStrings[] = "('$sid', '$name', '$datum', '$turnierid', '$y', '$nr', '0', '0', '$publish', '1970-01-01 00:00:00', '1970-01-01 00:00:00', '1970-01-01 00:00:00')";
			}
		}
		// alle abspeichern
		$query = "INSERT INTO #__clm_turniere_rnd_termine" . " (`sid`, `name`, `datum`, `turnier`, `dg`, `nr`, `abgeschlossen`, `tl_ok`, `published`, `zeit`, `edit_zeit`, `checked_out_time`)" . " VALUES " . implode(", ", $sqlValuesStrings);
		clm_core::$db->query($query);
		// Rundenbyte setzen
		clm_core::$db->turniere->get($id)->rnd = 1;
	}
	return array(true, "");
}
?>
