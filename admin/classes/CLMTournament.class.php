<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/


/**
 * Turnier
*/
	
class CLMTournament {

	function __construct($turnierid, $getData = FALSE) {
		// $turnierid übergibt id des Turniers
		// $getData, ob die Turneirdaten aus clm_turniere sofort ausgelesen werden sollen

		// DB
		$this->_db				= & JFactory::getDBO();
		
		// turnierid
		$this->turnierid = $turnierid;	
	
		// get data?
		if ($getData) {
			$this->_getData();
		}
	
	}


	function _getData() {
	
		$this->data = & JTable::getInstance( 'turniere', 'TableCLM' );
		$this->data->load($this->turnierid);
	
	}


	/**
	* check, ob User Zugriff hat
	* drei Zugangsmöichgkeiten - aller per Default auf TRUE
	*/
	//function checkAccess($usertype_admin = TRUE, $usertype_tl = TRUE, $id_tl = TRUE) {
	function checkAccess($usertype_admin = TRUE, $usertype_tl = TRUE, $id_tl = '') {

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		
		// admin?
		//if ($usertype_admin AND CLM_usertype == 'admin') {
		if ($clmAccess->access() === true) {
			return TRUE;
		}
		// tl?
		//if ($usertype_tl AND CLM_usertype == 'tl') {
		if ($id_tl == CLM_ID AND $clmAccess->access() !== false) {
			return TRUE;
		}
		// tournament->tl
		//if ($id_tl AND CLM_ID == $this->data->tl) {
		//	return TRUE;
		//}
		// nichts hat zugetroffen
		return FALSE;
	
	}

	function getPlayersIn() {
	
		// Anzahl gemeldeter Spieler
		$query = "SELECT COUNT(*) FROM `#__clm_turniere_tlnr`"
				. " WHERE turnier = ".$this->turnierid
				;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	
	}
	
	
	/**
	* check, ob ein Turnier schon gestartet wurde
	* indem die Gesamtzahl von Spielern errungener Punkte ermittelt wird
	* TODO: später durch ein Flag in der DB ersetzen
	*/
	function checkTournamentStarted() {
	
		// Ergebnisse gemeldet
		$query = "SELECT COUNT(*) FROM `#__clm_turniere_rnd_spl`"
			." WHERE turnier = ".$this->turnierid
			." AND ergebnis IS NOT NULL"
			;
		$this->_db->setQuery($query);
		if ($this->_db->loadResult() > 0) {
			$this->started = TRUE;
		} else {
			$this->started = FALSE;
		}
	
	}
	
	
	/**
	* check, ob die Startnummern des Teilnehmerfeldes korrekt vergeben sind
	* liest folgende Werte aus:
	* - maxSnr:			maximale Startnummer
	* - minSnr:			minimale Startnummer
	* - distinctSnr:	Anzahl unterschiedliche Startnummern
	* - countSnr:		Anzahl Startnummern gesamt
	* folgende Checks:
	* - erste Startnummer > 1
	* - letzte Startnummer > Teilnehmerzahl
	* - gibt es doppelte Startnummern
	*/
	function checkCorrectSnr() {
	
		$query = 'SELECT MAX(snr) AS maxSnr, MIN(snr) AS minSnr, COUNT(DISTINCT(snr)) AS distinctSnr, COUNT(snr) AS countSnr'
			. ' FROM #__clm_turniere_tlnr'
			. ' WHERE turnier = '.$this->turnierid
			;
		$this->_db->setQuery($query);
		$this->checkSnr = $this->_db->loadObject();
		if ($this->checkSnr->minSnr > 1 OR $this->checkSnr->maxSnr > $this->data->teil OR $this->checkSnr->distinctSnr != $this->checkSnr->countSnr) {
			return FALSE;
		} 
	
		return TRUE;
	
	}
	
	
	/**
	* errechnet/aktualisiert Rangliste/Punktesummen eines Turniers
	*/
	function calculateRanking() {
	
		// Parameter auslesen, für FIDE-Ranglistenkorrektur und TWZ
		$query = 'SELECT `params`'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->turnierid
			;
		$this->_db->setQuery($query);
		$turParams = new JParameter($this->_db->loadResult());
		$paramTBFideCorrect = $turParams->get('optionTiebreakersFideCorrect', 0);
		$paramuseAsTWZ = $turParams->get('useAsTWZ', 0);

		$query = 'SELECT dg, runden, teil, typ'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->turnierid
			;
		$this->_db->setQuery($query);
		$dg = $this->data->dg;
		$runden = $this->data->runden;
		$teil = $this->data->teil;
		if ($this->data->typ != 1) $paramTBFideCorrect = 0;
	
		//Turnierteilnehmer
		$query = " SELECT a.* "
			." FROM #__clm_turniere_tlnr as a "
			." WHERE turnier = ".$this->turnierid
			." ORDER BY a.snr "
			;
		$this->_db->setQuery($query);
		$player	=$this->_db->loadObjectList();

		// TWZ ggf. korrigieren
		foreach($player as $player1) {
			if($paramuseAsTWZ == 0) { 
				if ($player1->FIDEelo >= $player1->NATrating) { $player1->twz = $player1->FIDEelo; }
				else { $player1->twz = $player1->NATrating; } 
			} elseif ($paramuseAsTWZ == 1) {
				if ($player1->NATrating > 0) { $player1->twz = $player1->NATrating; }
				else { $player1->twz = $player1->FIDEelo; }
			} elseif ($paramuseAsTWZ == 2) {
				if ($player1->FIDEelo > 0) { $player1->twz = $player1->FIDEelo; }
				else { $player1->twz = $player1->NATrating; }
			}	
		}		

		//bisherige Rankingdaten löschen
		$query = "UPDATE #__clm_turniere_tlnr"
				. " SET sum_punkte = 0, sum_wins = 0, "
				. " anz_spiele = 0, "
				. " sumTiebr1 = 0, sumTiebr2 = 0, sumTiebr3 = 0 "
				. " WHERE turnier = ".$this->turnierid 
				;
		$this->_db->setQuery($query);
		$this->_db->query();
 
		// alle FW in Array schreiben
		$arrayFW = array();
		for ($tb=1; $tb<=3; $tb++) {
			$fieldname = 'tiebr'.$tb;
			$arrayFW[$tb] = $this->data->$fieldname;
		}
	
		// für alle Spieler Datensätze mit Summenwert 0 anlegen
		// TODO: da gab es einen eigenen PHP-Befehl für?!
		$array_PlayerSpiele = array();
		$array_PlayerPunkte = array();
		$array_PlayerPunkteTB = array(); // Punkte, die für Feinwertungen herangezogen werden
		$array_PlayerBuch = array();
		$array_PlayerBuchOpp = array();
		$array_PlayerBuch1St = array();
		$array_PlayerBuchm11 = array();
		$array_PlayerBuchm22 = array();
		$array_PlayerSoBe = array();
		$array_PlayerSoBeOpp = array();
		$array_PlayerBuSum = array();
		$array_PlayerBuSum1St = array();
		$array_PlayerWins = array();
		$array_PlayerElo = array();
		$array_PlayerEloOpp = array();
		$array_PlayerElo1St = array();
		$array_PlayerSumWert = array();
		$array_PlayerBuSumMin = array();
		$array_PlayerBuSum1StMin = array();
		$array_PlayerDWZ = array();
		$array_PlayerDWZOpp = array();
		$array_PlayerDWZ1St = array();
		$array_PlayerTWZ = array();
		$array_PlayerTWZOpp = array();
		$array_PlayerTWZ1St = array();
		for ($s=0; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
			$array_PlayerSpiele[$s] = 0;
			$array_PlayerPunkte[$s] = 0;
			$array_PlayerPunkteTB[$s] = 0;
			$array_PlayerBuch[$s] = 0;
			$array_PlayerBuch1ST[$s] = 0;
			$array_PlayerBuchm11[$s] = 0;
			$array_PlayerBuchm22[$s] = 0;
			$array_PlayerSoBe[$s] = 0;
			$array_PlayerSoBeMin[$s] = 999;
			$array_PlayerBuSum[$s] = 0;
			$array_PlayerBuSum1St[$s] = 0;
			$array_PlayerBuSumMin[$s] = 999;
			$array_PlayerBuSum1StMin[$s] = 999;
			$array_PlayerWins[$s] = 0;
			$array_PlayerElo[$s] = 0;
			$array_PlayerElo1St[$s] = 0;
			$array_PlayerSumWert[$s] = 0;
			$array_PlayerDWZ[$s] = 0;
			$array_PlayerDWZ1St[$s] = 0;
			$array_PlayerTWZ[$s] = 0;
			$array_PlayerTWZ1St[$s] = 0;
		}
	
		// alle Matches in DatenArray schreiben
		$query = "SELECT m.tln_nr, m.gegner, m.dg, m.runde, m.ergebnis, tl.FIDEelo, tl.NATrating, tl.twz FROM `#__clm_turniere_rnd_spl` as m"
				. " LEFT JOIN #__clm_turniere_tlnr as tl ON tl.turnier = m.turnier AND tl.snr = m.gegner "
				. " WHERE m.turnier = ".$this->turnierid." AND m.ergebnis IS NOT NULL"
				;
		$this->_db->setQuery( $query );
		$matchData = $this->_db->loadObjectList();
		$z = count($matchData);
 
		// Finden der letzten gespielten Runde 
		// und Anlegen einer Matrix der gesetzten Matches
		$maxround = 0;
		$matrix = array();
		foreach ($matchData as $key => $value) {
			if ($value->ergebnis < 3 AND ((($value->dg - 1) * $runden) + $value->runde) > $maxround) $maxround = (($value->dg - 1) * $runden) + $value->runde;
			$matrix[$value->tln_nr][$value->dg][$value->runde] = 1;
		}
			
		// für Spieler, die nicht gesetzt wurden, werden spielfreie Pseudo-Paarungen angelegt (für FIDE-Ranglistenkorrektur)
		for ($s=1; $s<= $teil; $s++) { 		// alle Startnummern durchgehen
			for ($d=1; $d<= $dg; $d++) { 		// alle Durchgänge durchgehen
				for ($r=1; $r<= $runden; $r++) { 	// alle Runden durchgehen
					if ($maxround < ((($d - 1) * $runden) + $r)) break;  		// nur bis zur aktuellen Runde
					if (!isset($matrix[$s][$d][$r])) {
						$matchData[$z]->tln_nr = $s;
						$matchData[$z]->gegner = 0;
						$matchData[$z]->dg = $d;
						$matchData[$z]->runde = $r;
						$matchData[$z]->ergebnis = 8;		// spielfrei
						$z++;
					}
				}
			}
		}
		
		// Punkte/Siege
		// alle Matches durchgehen -> Spieler erhalten Punkte und Wins
		foreach ($matchData as $key => $value) {
			if ($maxround < ((($value->dg - 1) * $runden) + $value->runde)) continue;  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
			if ($value->tln_nr == 0) continue;    //techn. Teilnehmer bei ungerader Teilnehmerzahl
			//if ($value->ergebnis == 8) continue;  //spielfrei
			if ($value->ergebnis != 8) $array_PlayerSpiele[$value->tln_nr] += 1;
			if ($value->ergebnis == 2) { // remis
				$array_PlayerPunkte[$value->tln_nr] += .5;
				$array_PlayerPunkteTB[$value->tln_nr] += .5;
				$array_PlayerSumWert[$value->tln_nr] += (.5 * ($maxround - $value->runde +1));
			} elseif ($value->ergebnis == 1 OR $value->ergebnis == 5) { // Sieger
				$array_PlayerPunkte[$value->tln_nr] += 1;
				$array_PlayerWins[$value->tln_nr] += 1;
				$array_PlayerSumWert[$value->tln_nr] += ($maxround - $value->runde +1);
				if ($value->ergebnis == 5 AND $paramTBFideCorrect == 1) { // kampflos gewonnen und FIDE-Korrektur eingestellt?
					$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
				} else {
					$array_PlayerPunkteTB[$value->tln_nr] += 1;
				}
			} elseif ($value->ergebnis == 4 AND $paramTBFideCorrect == 1) { // kampflos verloren und FIDE-Korrektur eingestellt?
				$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
			} elseif ($value->ergebnis == 8 AND $paramTBFideCorrect == 1) { // spielfrei und FIDE-Korrektur eingestellt?
				$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
			} elseif ($value->ergebnis == 3 AND $paramTBFideCorrect == 1) { // Ergebnis 0-0 und FIDE-Korrektur eingestellt?
				$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
			} elseif ($value->ergebnis == 6 AND $paramTBFideCorrect == 1) { // kampflos beide verloren -:- und FIDE-Korrektur eingestellt?
				$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
			}
		}
	
		// Buchholz & Sonneborn-Berger
		// erneut alle Matches durchgehen -> Spieler erhalten Feinwertungen
		foreach ($matchData as $key => $value) {
			if ($maxround < ((($value->dg - 1) * $runden) + $value->runde)) continue;  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
			//if ($value->tln_nr == 0) continue;  // Ignorieren von techn. Spielern
			// Buchholz
			if (in_array(1, $arrayFW) OR in_array(2, $arrayFW) OR in_array(11, $arrayFW) OR in_array(12, $arrayFW) OR in_array(5, $arrayFW) OR in_array(15, $arrayFW)) { // beliebige Buchholz als TieBreaker gewünscht?
				if ($value->ergebnis < 3 OR $paramTBFideCorrect == 0) {
					$array_PlayerBuchOpp[$value->tln_nr][] = $array_PlayerPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
				} else { //Ranglistenkorrektur nach FIDE (Teil 2) nur für CH-Turniere
					$query = "SELECT tln_nr, gegner, dg, runde, ergebnis FROM `#__clm_turniere_rnd_spl`"
					. " WHERE turnier = ".$this->turnierid
					. " AND tln_nr = ".$value->tln_nr
					. " AND ergebnis IS NOT NULL"
					. " ORDER BY dg ASC, runde ASC"
					;
					$this->_db->setQuery( $query );
					$matchDataSnr = $this->_db->loadObjectList();
					$PlayerPunkteKOR = 0;
					foreach ($matchDataSnr as $key => $valuesnr) {
						if ($maxround < ((($valuesnr->dg - 1) * $runden) + $valuesnr->runde)) continue;  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
						if (($valuesnr->dg < $value->dg) OR ($valuesnr->dg == $value->dg AND $valuesnr->runde < $value->runde)) {
							if ($valuesnr->ergebnis == 1) $PlayerPunkteKOR += 1; // Sieg
							elseif ($valuesnr->ergebnis == 2) $PlayerPunkteKOR += .5; // remis
							elseif ($valuesnr->ergebnis == 5) $PlayerPunkteKOR += 1; // Sieg kampflos
						}
					}	
					if (($value->ergebnis == 4) OR ($value->ergebnis == 8)) { $PlayerPunkteKOR += 1; }// Gegner gewinnt kampflos oder spielfrei
	  				if (($value->ergebnis == 3) OR ($value->ergebnis == 6)) { $PlayerPunkteKOR += 1; }// Gegner verliert auch kampflos, ist aber egal
					//$PlayerPunkteKOR += 0.5 * (($runden * $dg) - (($value->dg - 1) * $runden) - $value->runde);
					$PlayerPunkteKOR += (.5 * (($maxround) - (($value->dg - 1) * $runden) - $value->runde));
					$array_PlayerBuchOpp[$value->tln_nr][] = $PlayerPunkteKOR; // Array mit Gegnerwerten - für Streichresultat
				}
			}
			
			// Sonneborn-Berger
			if (in_array(3, $arrayFW) OR in_array(13, $arrayFW)) { // SoBe als ein TieBreaker gewünscht?
				if ($value->ergebnis == 0 ) {
					$array_PlayerSoBeOpp[$value->tln_nr][] = 0; 	// Array mit Gegnerwerten - für Streichresultat
				} elseif ($value->ergebnis == 1) {
					$array_PlayerSoBeOpp[$value->tln_nr][] = $array_PlayerPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
				} elseif ($value->ergebnis == 2) {
					$array_PlayerSoBeOpp[$value->tln_nr][] = (.5 * $array_PlayerPunkteTB[$value->gegner]); // Array mit Gegnerwerten - für Streichresultat
				} elseif ($value->ergebnis == 5 AND $paramTBFideCorrect == 0) {
					$array_PlayerSoBeOpp[$value->tln_nr][] = $array_PlayerPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
				} elseif ($paramTBFideCorrect == 0) {
					$array_PlayerSoBeOpp[$value->tln_nr][] = 0; 		// Array mit Gegnerwerten - für Streichresultat
				} else { //Ranglistenkorrektur nach FIDE (Teil 2)
					$query = "SELECT tln_nr, gegner, dg, runde, ergebnis FROM `#__clm_turniere_rnd_spl`"
					. " WHERE turnier = ".$this->turnierid
					. " AND tln_nr = ".$value->tln_nr
					. " AND ergebnis IS NOT NULL"
					. " ORDER BY dg ASC, runde ASC"
					;
					$this->_db->setQuery( $query );
					$matchDataSnr = $this->_db->loadObjectList();
					$PlayerPunkteKOR = 0;
					foreach ($matchDataSnr as $key => $valuesnr) {
						if ($maxround < ((($valuesnr->dg - 1) * $runden) + $valuesnr->runde)) continue;  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
						if (($valuesnr->dg < $value->dg) OR ($valuesnr->dg == $value->dg AND $valuesnr->runde < $value->runde)) {
							if ($valuesnr->ergebnis == 1) $PlayerPunkteKOR += 1; // Sieg
							elseif ($valuesnr->ergebnis == 2) $PlayerPunkteKOR += .5; // remis
							elseif ($valuesnr->ergebnis == 5) $PlayerPunkteKOR += 1; // Sieg kampflos
						}
					}
					if (($value->ergebnis == 5)) { $PlayerFaktorKOR = 1; }	// Spieler gewinnt kampflos 
					else { $PlayerFaktorKOR = 0; }
					$PlayerPunkteKOR += (.5 * (($maxround) - (($value->dg - 1) * $runden) - $value->runde));
					//echo "<br>p: $value->tln_nr  PlayerPunkteKOR: "; var_dump($PlayerPunkteKOR); 
					$array_PlayerSoBeOpp[$value->tln_nr][] = ($PlayerFaktorKOR * $PlayerPunkteKOR); // Array mit Gegnerwerten - für Streichresultat
				}
				//echo "<br>p: $value->tln_nr  array_PlayerSoBeOpp: "; var_dump($array_PlayerSoBeOpp[$value->tln_nr]); 				
			}
			
			// Elo-Schnitt
			if (in_array(6, $arrayFW) OR in_array(16, $arrayFW)) { // SoBe als ein TieBreaker gewünscht?
				if ($value->gegner == 0 ) {
					$array_PlayerEloOpp[$value->tln_nr][] = 0; 	// Array mit Gegnerwerten - für Streichresultat
				} else {
					if ($value->FIDEelo > 0) $array_PlayerEloOpp[$value->tln_nr][] = $value->FIDEelo; 
					else $array_PlayerEloOpp[$value->tln_nr][] = $value->NATrating; } // Array mit Gegnerwerten - für Streichresultat
			} 
			// DWZ-Schnitt
			if (in_array(8, $arrayFW) OR in_array(18, $arrayFW)) { // DWZ-Schnitt als ein TieBreaker gewünscht?
				if ($value->gegner == 0 ) {
					$array_PlayerDWZOpp[$value->tln_nr][] = 0; 	// Array mit Gegnerwerten - für Streichresultat
				} else {
					if ($value->NATrating > 0) $array_PlayerDWZOpp[$value->tln_nr][] = $value->NATrating; 
					else $array_PlayerDWZOpp[$value->tln_nr][] = $value->FIDEelo; } // Array mit Gegnerwerten - für Streichresultat
			} 
			// TWZ-Schnitt
			if (in_array(9, $arrayFW) OR in_array(19, $arrayFW)) { // TWZ-Schnitt als ein TieBreaker gewünscht?
				if ($value->gegner == 0 ) {
					$array_PlayerTWZOpp[$value->tln_nr][] = 0; 	// Array mit Gegnerwerten - für Streichresultat
				} else {
					if ($value->twz > 0) $array_PlayerTWZOpp[$value->tln_nr][] = $value->twz; 
					else $array_PlayerTWZOpp[$value->tln_nr][] = $value->NATrating; } // Array mit Gegnerwerten - für Streichresultat
			} 
		}
		// Sonneborn-Berger
		if (in_array(3, $arrayFW)) { // normale Sonneborn-Berger als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerSoBeOpp[$s])) $array_PlayerSoBe[$s] = 0;
				elseif (count($array_PlayerSoBeOpp[$s]) == 1) $array_PlayerSoBe[$s] = $array_PlayerSoBeOpp[$s][0];
				else $array_PlayerSoBe[$s] = array_sum($array_PlayerSoBeOpp[$s]);
			}
		} elseif (in_array(13, $arrayFW)) { // Sonneborn-Berger mit Streichresultat
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerSoBeOpp[$s])) 
					$array_PlayerSoBe[$s] = 0;
				elseif (count($array_PlayerSoBeOpp[$s]) == 0) 
					$array_PlayerSoBe[$s] = 0;
				elseif (count($array_PlayerSoBeOpp[$s]) == 1) 
					$array_PlayerSoBe[$s] = $array_PlayerSoBeOpp[$s][0];
				elseif (count($array_PlayerSoBeOpp[$s]) > 2) //== ($dg * $runden)) 
					$array_PlayerSoBe[$s] = array_sum($array_PlayerSoBeOpp[$s]) - min($array_PlayerSoBeOpp[$s]);
				else $array_PlayerSoBe[$s] = array_sum($array_PlayerSoBeOpp[$s]);
			}
		}
	
		// Buchholz
		if ((in_array(1, $arrayFW)) OR (in_array(2, $arrayFW)) OR (in_array(11, $arrayFW)) OR (in_array(12, $arrayFW))) { // normale Buchholz als TieBreaker gewünscht?
			for ($s=0; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerBuchOpp[$s])) $array_PlayerBuch[$s] = 0;
				elseif (count($array_PlayerBuchOpp[$s]) == 1) $array_PlayerBuch[$s] = $array_PlayerBuchOpp[$s][0];
				else $array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]);
			}
		} 
		// Buchholz 1 Streichresultat
		if ((in_array(11, $arrayFW)) OR (in_array(12, $arrayFW))) { // Buchholz mit Streichresultat
			for ($s=0; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
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
		// mittlere Buchholz 2 Streichresultate (höchstes und niedrigstes)
		if (in_array(5, $arrayFW))  { // Buchholz mit Streichresultat
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerBuchOpp[$s])) 
					$array_PlayerBuchm11[$s] = 0;
				elseif (count($array_PlayerBuchOpp[$s]) == 0) 
					$array_PlayerBuchm11[$s] = 0;
				elseif (count($array_PlayerBuchOpp[$s]) == 1) 
					$array_PlayerBuchm11[$s] = $array_PlayerBuchOpp[$s][0];
				elseif (count($array_PlayerBuchOpp[$s]) == 2) 
					$array_PlayerBuchm11[$s] = array_sum($array_PlayerBuchOpp[$s]);
				elseif (count($array_PlayerBuchOpp[$s]) > 2) //== ($dg * $runden)) 
					$array_PlayerBuchm11[$s] = array_sum($array_PlayerBuchOpp[$s]) - min($array_PlayerBuchOpp[$s]) - max($array_PlayerBuchOpp[$s]);
				else $array_PlayerBuchm11[$s] = array_sum($array_PlayerBuchOpp[$s]);
			}
		}
			
		// BuchholzSumme
		if ((in_array(2, $arrayFW)) OR (in_array(12, $arrayFW))) { // Buchholz-Summe als TieBreaker gewünscht?
			// erneut alle Matches durchgehen -> Spieler erhalten Buchholzsummen
			foreach ($matchData as $key => $value) {
				//if ($value->gegner >= 1) {
					$array_PlayerBuSum[$value->tln_nr] += $array_PlayerBuch[$value->gegner];
					if ($array_PlayerBuSumMin[$value->tln_nr] > $array_PlayerBuch[$value->gegner]) 
							$array_PlayerBuSumMin[$value->tln_nr] = $array_PlayerBuch[$value->gegner];
				//} else $array_PlayerBuSumMin[$value->tln_nr] = 0;
			}
		}
		// BuchholzSumme mit Streichresultat
		if (in_array(12, $arrayFW)) { // als TieBreaker gewünscht?
			foreach ($matchData as $key => $value) {
					$array_PlayerBuSum1St[$value->tln_nr] += $array_PlayerBuch1St[$value->gegner];
					if ($array_PlayerBuSum1StMin[$value->tln_nr] > $array_PlayerBuch1St[$value->gegner]) 
							$array_PlayerBuSum1StMin[$value->tln_nr] = $array_PlayerBuch1St[$value->gegner];
			}
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				$array_PlayerBuSum1St[$s] = $array_PlayerBuSum1St[$s] - $array_PlayerBuSum1StMin[$s];
			}
		}
	
		// Elo-Schnitt
		if (in_array(6, $arrayFW)) { // Elo-Schnitt als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerEloOpp[$s])) $array_PlayerElo[$s] = 0;
				elseif (count($array_PlayerEloOpp[$s]) == 1) $array_PlayerElo[$s] = $array_PlayerEloOpp[$s][0];
				else { $c_EloOpp = 0;
				  foreach($array_PlayerEloOpp[$s] as $EloOpp) { 
					if ($EloOpp > 0) $c_EloOpp++; }				
				  if ($c_EloOpp == 0)
				    $array_PlayerElo[$s] = 0; 
				  else
				    $array_PlayerElo[$s] = array_sum($array_PlayerEloOpp[$s]) / $c_EloOpp; 
				}
			}
		}
		// DWZ-Schnitt
		if (in_array(8, $arrayFW)) { // DWZ-Schnitt als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerDWZOpp[$s])) $array_PlayerDWZ[$s] = 0;
				elseif (count($array_PlayerDWZOpp[$s]) == 1) $array_PlayerDWZ[$s] = $array_PlayerDWZOpp[$s][0];
				else { $c_DWZOpp = 0;
				  foreach($array_PlayerDWZOpp[$s] as $DWZOpp) { 
					if ($DWZOpp > 0) $c_DWZOpp++; }				
				  if ($c_DWZOpp == 0)
				    $array_PlayerDWZ[$s] = 0; 
				  else
				    $array_PlayerDWZ[$s] = array_sum($array_PlayerDWZOpp[$s]) / $c_DWZOpp; 
				}
			}
		}
		// TWZ-Schnitt
		if (in_array(9, $arrayFW)) { // TWZ-Schnitt als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerTWZOpp[$s])) $array_PlayerTWZ[$s] = 0;
				elseif (count($array_PlayerTWZOpp[$s]) == 1) $array_PlayerTWZ[$s] = $array_PlayerTWZOpp[$s][0];
				else { $c_TWZOpp = 0;
				  foreach($array_PlayerTWZOpp[$s] as $TWZOpp) { 
					if ($TWZOpp > 0) $c_TWZOpp++; }				
				  if ($c_TWZOpp == 0)
				    $array_PlayerTWZ[$s] = 0; 
				  else
				    $array_PlayerTWZ[$s] = array_sum($array_PlayerTWZOpp[$s]) / $c_TWZOpp; 
				}
			}
		}
 
		// Elo-Schnitt mit Streichresultat
		if (in_array(16, $arrayFW)) { // Elo-Schnitt als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerEloOpp[$s])) $array_PlayerElo1St[$s] = 0;
				elseif (count($array_PlayerEloOpp[$s]) == 1) $array_PlayerElo1St[$s] = $array_PlayerEloOpp[$s][0];
				else { $c_EloOpp = 0;
				  foreach($array_PlayerEloOpp[$s] as $EloOpp) { 
					if ($EloOpp > 0) $c_EloOpp++; }				
				  if ($c_EloOpp == 0)
				    $array_PlayerElo1St[$s] = 0; 
				  else
				    if (min($array_PlayerEloOpp[$s] == 0))
				      $array_PlayerElo1St[$s] = array_sum($array_PlayerEloOpp[$s]) / $c_EloOpp;
				    elseif ($c_EloOpp == 1)
				      $array_PlayerElo1St[$s] = array_sum($array_PlayerEloOpp[$s]);
					else
				      $array_PlayerElo1St[$s] = (array_sum($array_PlayerEloOpp[$s]) - min($array_PlayerEloOpp[$s])) / ($c_EloOpp - 1);
				}
			}
		}
		// DWZ-Schnitt mit Streichresultat
		if (in_array(18, $arrayFW)) { // DWZ-Schnitt als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerDWZOpp[$s])) $array_PlayerDWZ1St[$s] = 0;
				elseif (count($array_PlayerDWZOpp[$s]) == 1) $array_PlayerDWZ1St[$s] = $array_PlayerDWZOpp[$s][0];
				else { $c_DWZOpp = 0;
				  foreach($array_PlayerDWZOpp[$s] as $DWZOpp) { 
					if ($DWZOpp > 0) $c_DWZOpp++; }				
				  if ($c_DWZOpp == 0)
				    $array_PlayerDWZ1St[$s] = 0; 
				  else
				    if (min($array_PlayerDWZOpp[$s] == 0))
				      $array_PlayerDWZ1St[$s] = array_sum($array_PlayerDWZOpp[$s]) / $c_DWZOpp;
				    elseif ($c_DWZOpp == 1)
				      $array_PlayerDWZ1St[$s] = array_sum($array_PlayerDWZOpp[$s]);
					else
				      $array_PlayerDWZ1St[$s] = (array_sum($array_PlayerDWZOpp[$s]) - min($array_PlayerDWZOpp[$s])) / ($c_DWZOpp - 1);
				}
			}
		}
		// TWZ-Schnitt mit Streichresultat
		if (in_array(19, $arrayFW)) { // TWZ-Schnitt als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				if (!isset($array_PlayerTWZOpp[$s])) $array_PlayerTWZ1St[$s] = 0;
				elseif (count($array_PlayerTWZOpp[$s]) == 1) $array_PlayerTWZ1St[$s] = $array_PlayerTWZOpp[$s][0];
				else { $c_TWZOpp = 0;
				  foreach($array_PlayerTWZOpp[$s] as $TWZOpp) { 
					if ($TWZOpp > 0) $c_TWZOpp++; }				
				  if ($c_TWZOpp == 0)
				    $array_PlayerTWZ1St[$s] = 0; 
				  else
				    if (min($array_PlayerTWZOpp[$s] == 0))
				      $array_PlayerTWZ1St[$s] = array_sum($array_PlayerTWZOpp[$s]) / $c_TWZOpp;
				    elseif ($c_TWZOpp == 1)
				      $array_PlayerTWZ1St[$s] = array_sum($array_PlayerTWZOpp[$s]);
					else
				      $array_PlayerTWZ1St[$s] = (array_sum($array_PlayerTWZOpp[$s]) - min($array_PlayerTWZOpp[$s])) / ($c_TWZOpp - 1);
				}
			}
		}

		
		// alle Spieler durchgehen und updaten (kein vorheriges Löschen notwendig)
		for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
			// den TiebrSummen ihre Werte zuordnen
			for ($tb=1; $tb<=3; $tb++) {
				$fieldname = 'tiebr'.$tb;
				switch ($this->data->$fieldname) {
					case 1: // buchholz
						$sumTiebr[$tb] = $array_PlayerBuch[$s];
						break;
					case 2: // bhhlz.-summe
						$sumTiebr[$tb] = $array_PlayerBuSum[$s];
						break;
					case 3: // sobe
						$sumTiebr[$tb] = $array_PlayerSoBe[$s];
						break;
					case 4: // wins
						$sumTiebr[$tb] = $array_PlayerWins[$s];
						break;
					case 5: // mittl. bhhlz mit 2 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuchm11[$s];
						break;
					case 6: // elo-schnitt
						$sumTiebr[$tb] = $array_PlayerElo[$s];
						break;
					case 7: // summenwertung
						$sumTiebr[$tb] = $array_PlayerSumWert[$s];
						break;
					case 8: // DWZ-schnitt
						$sumTiebr[$tb] = $array_PlayerDWZ[$s];
						break;
					case 9: // TWZ-schnitt
						$sumTiebr[$tb] = $array_PlayerTWZ[$s];
						break;
					case 11: // bhhlz mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuch1St[$s];
						break;
					case 12: // bhhlz.-summe mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuSum1St[$s];
						break;
					case 13: // sobe mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerSoBe[$s];
						break;
					case 15: // mittl. bhhlz mit 4 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuchm22[$s];
						break;
					case 16: // elo-schnitt mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerElo1St[$s];
						break;
					case 18: // DWZ-schnitt mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerDWZ1St[$s];
						break;
					case 19: // TWZ-schnitt mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerTWZ1St[$s];
						break;
					case 29: // Prozentpunkte
						if ($array_PlayerSpiele[$s] == 0) $sumTiebr[$tb] = 0;
						else $sumTiebr[$tb] = ($array_PlayerPunkte[$s] * 100) / $array_PlayerSpiele[$s];
						break;
					case 51: // ordering
						$sumTiebr[$tb] = 1000 - $player[$s-1]->ordering;
						break;
					default:
						$sumTiebr[$tb] = 0;
				}
			}
			
			$query = "UPDATE #__clm_turniere_tlnr"
					. " SET sum_punkte = ".$array_PlayerPunkte[$s].", sum_wins = ".$array_PlayerWins[$s].", "
					. " anz_spiele = ".$array_PlayerSpiele[$s].", "
					. " sumTiebr1 = ".$sumTiebr[1].", sumTiebr2 = ".$sumTiebr[2].", sumTiebr3 = ".$sumTiebr[3].","
					. " twz = ".$player[$s-1]->twz
					. " WHERE turnier = ".$this->turnierid
					. " AND snr = ".$s
					;
			$this->_db->setQuery($query);
			$this->_db->query();
		}
 
		if ($this->data->tiebr1 == 25 OR $this->data->tiebr2 == 25 OR $this->data->tiebr3 == 25) {
			$query = "SELECT * "
				." FROM `#__clm_turniere_tlnr`"
				." WHERE turnier = ".$this->turnierid
				." ORDER BY sum_punkte DESC, sumTiebr1 DESC, sumTiebr2 DESC, sumTiebr3 DESC, snr ASC"
				;
			$this->_db->setQuery( $query );
			$players = $this->_db->loadObjectList();
			// alle Spieler durchgehen
			foreach ($players as $xvalue) {
				$sum_erg = 0; $id_dv = 0;
				// alle Spieler durchgehen
				foreach ($players as $yvalue) {
					if ($xvalue->snr == $yvalue->snr) continue;	
					// sind x und y wertungsgleich ?
					if (($this->data->tiebr1 == 25 AND $xvalue->sum_punkte == $yvalue->sum_punkte) OR			
						($this->data->tiebr2 == 25 AND $xvalue->sum_punkte == $yvalue->sum_punkte AND $xvalue->sumTiebr1 == $yvalue->sumTiebr1)	OR		
						($this->data->tiebr3 == 25 AND $xvalue->sum_punkte == $yvalue->sum_punkte AND $xvalue->sumTiebr1 == $yvalue->sumTiebr1 AND $xvalue->sumTiebr2 == $yvalue->sumTiebr2)) {			
						$id_dv = 1;
						// alle Matches in DatenArray schreiben
						$query = "SELECT * FROM `#__clm_turniere_rnd_spl` as m"
							. " WHERE turnier = ".$this->turnierid." AND ergebnis IS NOT NULL"
							. " AND tln_nr = ".$xvalue->snr." AND gegner = ".$yvalue->snr
							;
						$this->_db->setQuery( $query );
						$matchesdirect = $this->_db->loadObjectList();
						$zdirect = count($matchesdirect);
						foreach ($matchesdirect as $mdvalue) {
							if ($mdvalue->ergebnis == 2) $sum_erg += 1;
							elseif ($mdvalue->ergebnis == 1 OR $mdvalue->ergebnis == 5) $sum_erg += 2;
						}
					}
				}
				if ($id_dv == 1) {
					$query = "UPDATE #__clm_turniere_tlnr";
					if ($this->data->tiebr1 == 25) $query .= " SET sumTiebr1 = ".$sum_erg;
					elseif ($this->data->tiebr2 == 25) $query .= " SET sumTiebr2 = ".$sum_erg;
					else $query .= " SET sumTiebr3 = ".$sum_erg;
					$query .= " WHERE turnier = ".$this->turnierid
						. " AND snr = ".$xvalue->snr
						;
					$this->_db->setQuery($query);
					$this->_db->query();
				} else {
					$query = "UPDATE #__clm_turniere_tlnr";
					if ($this->data->tiebr1 == 25) $query .= " SET sumTiebr1 = NULL";
					elseif ($this->data->tiebr2 == 25) $query .= " SET sumTiebr2 = NULL";
					else $query .= " SET sumTiebr3 = NULL";
					$query .= " WHERE turnier = ".$this->turnierid
						. " AND snr = ".$xvalue->snr
						;
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}
	}

	
	function setRankingPositions() {
	
		$query = "SELECT * "
			." FROM `#__clm_turniere_tlnr`"
			." WHERE turnier = ".$this->turnierid
			." ORDER BY sum_punkte DESC, sumTiebr1 DESC, sumTiebr2 DESC, sumTiebr3 DESC, snr ASC"
			;
		
		$this->_db->setQuery( $query );
		$players = $this->_db->loadObjectList();
	
		$table	=& JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		// rankingPos umsortieren
		$rankingPos = 0; $rankingPosZ = 0;
		$sum_punkte = 0; $sumTiebr1 = 0; $sumTiebr2 = 0; $sumTiebr3 = 0;
		// alle Spieler durchgehen
		foreach ($players as $value) {
			$rankingPos++;
			$table->load($value->id);
			if ($sum_punkte == $value->sum_punkte AND $sumTiebr1 == $value->sumTiebr1 
				AND $sumTiebr2 == $value->sumTiebr2 AND $sumTiebr3 == $value->sumTiebr3)
				{ $table->rankingPos = $rankingPosZ; }
			else { $table->rankingPos = $rankingPos;
				$sum_punkte = $value->sum_punkte;
				$sumTiebr1 = $value->sumTiebr1; 
				$sumTiebr2 = $value->sumTiebr2;
				$sumTiebr3 = $value->sumTiebr3;
				$rankingPosZ = $rankingPos; }
			$table->store();
		}
		$this->determineNATrating();
	}
	
	
	function makePlusTln() {
	
		if ($this->data->typ != 1) {
			JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'WRONGMODUS') );
			return FALSE;
			
		} elseif ($this->checkTournamentStarted()) {
			JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'ALREADYSTARTED') );
			return FALSE;
		}
	
		$query = "UPDATE #__clm_turniere"
				. " SET teil = teil + 1"
				. " WHERE id = ".$this->turnierid
				;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			JError::raiseNotice(500, JText::_('DB_ERROR') );
			return FALSE;
		}
		
		$app = JFactory::getApplication();
		$app->enqueueMessage( JText::_('PARTICIPANT_COUNT_RAISED_TO').": ".($this->data->teil+1) );
		
		return TRUE;
	
	}
	
	function makeMinusTln() {
	
		if ($this->data->typ != 1) {
			JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'WRONGMODUS') );
			return FALSE;
			
		} elseif ($this->checkTournamentStarted()) {
			JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'ALREADYSTARTED') );
			return FALSE;
		}
	
		$query = "UPDATE #__clm_turniere"
				. " SET teil = teil - 1"
				. " WHERE id = ".$this->turnierid
				;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			JError::raiseNotice(500, JText::_('DB_ERROR') );
			return FALSE;
		}
		
		$app = JFactory::getApplication();
		$app->enqueueMessage( JText::_('PARTICIPANT_COUNT_LESSENED_TO').": ".($this->data->teil-1) );
		
		return TRUE;
	
	}
		
	function determineNATrating() {
		
		// aktuelles Jahr ermitteln 
		$Jahr=getdate();
		$Jahr=$Jahr['year'];
		
		//bisherige Ratingdaten löschen
		$query = "UPDATE #__clm_turniere_tlnr"
				. " SET DWZ = 0, I0 = 0, "
				. " Punkte = 0, Partien = 0, "
				. " We = 0, Leistung = 0, EFaktor = 0, Niveau = 0"
				. " WHERE turnier = ".$this->turnierid 
				;
		$this->_db->setQuery($query);
		$this->_db->query();
 
	
		// alle Matches lesen und Gegner-Array bestimmen sowie DWZ-Berechnung in Routine
		$query = "SELECT m.tln_nr, m.gegner, m.dg, m.runde, m.ergebnis, tl.FIDEelo, tl.NATrating, "
//				. " g.NATrating as gNATrating "
				. " g.NATrating as gNATrating, tl.birthYear "
				. " FROM `#__clm_turniere_rnd_spl` as m"
				. " LEFT JOIN #__clm_turniere_tlnr as tl ON tl.turnier = m.turnier AND tl.snr = m.tln_nr "
				. " LEFT JOIN #__clm_turniere_tlnr as g ON g.turnier = m.turnier AND g.snr = m.gegner "
				. " WHERE m.turnier = ".$this->turnierid." AND m.ergebnis BETWEEN 0 AND 2 "
				. " ORDER BY m.tln_nr "
				;
		$this->_db->setQuery( $query );
		$matchData = $this->_db->loadObjectList();
		//echo "<br>error: ".mysql_errno()." ".mysql_error();
		//echo "<br>md: "; var_dump($matchData);
		//die();
	  if (count($matchData) > 0) {	
		// DWZ-Aufbereitung pro Teilnehmer
		$spieler = array();
		$tln_nr = 0;
		$Gegner = array();
		$dwz = array();
		foreach ($matchData as $key => $value) {
			//echo "<br>value: "; var_dump($value);
			if ($value->tln_nr != $tln_nr) { //AND isset($spieler->tln_nr)) OR !isset($spieler->tln_nr)) {
				if ($tln_nr > 0) {
					$alter=$Jahr - $spieler->birthYear;
					if ($alter<0) $alter=0;
					ElseIf($alter<21) $alter=1;
					ElseIf($alter<26) $alter=2;
					Else $alter=3;
					$dwz = $this->DWZRechner($spieler->NATrating,22,$alter,$Gegner);
							//echo "<br>dwz: "; var_dump($dwz);
							//die();
					$query = "UPDATE #__clm_turniere_tlnr"
						. " SET DWZ = ".$dwz[0].", I0 = ".$dwz[1].", "
						. " Punkte = ".round($dwz[2]/1000,1).", Partien = ".$dwz[3].", "
						. " We = ".round($dwz[4]/1000,3).", Leistung = ".$dwz[5].", EFaktor = ".$dwz[6].", Niveau = ".$dwz[7]
						. " WHERE turnier = ".$this->turnierid
						. " AND snr = ".$spieler->tln_nr;
					;
					$this->_db->setQuery($query);
					$this->_db->query();
		//echo "<br>error: ".mysql_errno()." ".mysql_error();
		//die();
				}
				$spieler = $value;
				$tln_nr = $value->tln_nr;
			//echo "<br>spieler: "; var_dump($spieler);
				$Gegner = array();
				$dwz = array();
			}
			if ($value->ergebnis == 2) $ergebnis = 5;
			else $ergebnis = $value->ergebnis;
			$Gegner[] = array($value->gNATrating,$ergebnis);
			//echo "<br>gegner: "; var_dump($Gegner);
		}
		//die();
					$alter=$Jahr - $spieler->birthYear;
					if ($alter<0) $alter=0;
					ElseIf($alter<21) $alter=1;
					ElseIf($alter<26) $alter=2;
					Else $alter=3;
					$dwz = $this->DWZRechner($spieler->NATrating,22,$alter,$Gegner);
							//echo "<br>dwz: "; var_dump($dwz);
							//die();
					$query = "UPDATE #__clm_turniere_tlnr"
						. " SET DWZ = ".$dwz[0].", I0 = ".$dwz[1].", "
						. " Punkte = ".round($dwz[2]/1000,1).", Partien = ".$dwz[3].", "
						. " We = ".round($dwz[4]/1000,3).", Leistung = ".$dwz[5].", EFaktor = ".$dwz[6].", Niveau = ".$dwz[7]
						. " WHERE turnier = ".$this->turnierid
						. " AND snr = ".$spieler->tln_nr;
					;
					$this->_db->setQuery($query);
					$this->_db->query();
		//echo "<br>error: ".mysql_errno()." ".mysql_error();
		//die();
	  }	
	}

	
function DWZRechner($R0, $Index, $Alter, $Gegner)
   {
    $PTab = array (500,501,503,504,506,507,508,510,511,513,514,516,517,518,520,521,523,524,525,527,
                   528,530,531,532,534,535,537,538,539,541,542,544,545,546,548,549,551,552,553,555,
                   556,558,559,560,562,563,565,566,567,569,570,572,573,574,576,577,578,580,581,583,
                   584,585,587,588,590,591,592,594,595,596,598,599,600,602,603,605,606,607,609,610,
                   611,613,614,615,617,618,619,621,622,623,625,626,628,629,630,632,633,634,636,637,
                   638,639,641,642,643,645,646,647,649,650,651,653,654,655,657,658,659,660,662,663,
                   664,666,667,668,669,671,672,673,675,676,677,678,680,681,682,683,685,686,687,688,
                   690,691,692,693,695,696,697,698,700,701,702,703,705,706,707,708,709,711,712,713,
                   714,715,717,718,719,720,721,723,724,725,726,727,728,730,731,732,733,734,735,737,
                   738,739,740,741,742,743,745,746,747,748,749,750,751,752,754,755,756,757,758,759,
                   760,761,762,764,765,766,767,768,769,770,771,772,773,774,775,776,777,779,780,781,
                   782,783,784,785,786,787,788,789,790,791,792,793,794,795,796,797,798,799,800,801,
                   802,803,804,805,806,807,808,809,810,811,812,813,814,814,815,816,817,818,819,820,
                   821,822,823,824,825,826,827,827,828,829,830,831,832,833,834,835,835,836,837,838,
                   839,840,841,841,842,843,844,845,846,847,847,848,849,850,851,852,852,853,854,855,
                   856,856,857,858,859,860,860,861,862,863,863,864,865,866,867,867,868,869,870,870,
                   871,872,873,873,874,875,875,876,877,878,878,879,880,880,881,882,883,883,884,885,
                   885,886,887,887,888,889,889,890,891,891,892,893,893,894,895,895,896,897,897,898,
                   898,899,900,900,901,902,902,903,903,904,905,905,906,906,907,908,908,909,909,910,
                   910,911,912,912,913,913,914,914,915,915,916,917,917,918,918,919,919,920,920,921,
                   921,922,922,923,923,924,924,925,925,926,926,927,927,928,928,929,929,930,930,931,
                   931,932,932,933,933,934,934,934,935,935,936,936,937,937,938,938,938,939,939,940,
                   940,941,941,941,942,942,943,943,943,944,944,945,945,945,946,946,947,947,947,948,
                   948,948,949,949,950,950,950,951,951,951,952,952,952,953,953,953,954,954,954,955,
                   955,955,956,956,956,957,957,957,958,958,958,959,959,959,960,960,960,961,961,961,
                   961,962,962,962,963,963,963,963,964,964,964,965,965,965,965,966,966,966,966,967,
                   967,967,968,968,968,968,969,969,969,969,970,970,970,970,970,971,971,971,971,972,
                   972,972,972,973,973,973,973,973,974,974,974,974,975,975,975,975,975,976,976,976,
                   976,976,977,977,977,977,977,977,978,978,978,978,978,979,979,979,979,979,980,980,
                   980,980,980,980,981,981,981,981,981,981,982,982,982,982,982,982,982,983,983,983,
                   983,983,983,983,984,984,984,984,984,984,984,985,985,985,985,985,985,985,986,986,
                   986,986,986,986,986,986,987,987,987,987,987,987,987,987,988,988,988,988,988,988,
                   988,988,988,988,989,989,989,989,989,989,989,989,989,990,990,990,990,990,990,990,
                   990,990,990,990,991,991,991,991,991,991,991,991,991,991,991,991,992,992,992,992,
                   992,992,992,992,992,992,992,992,993,993,993,993,993,993,993,993,993,993,993,993,
                   993,993,993,994,994,994,994,994,994,994,994,994,994,994,994,994,994,994,994,994,
                   995,995,995,995,995,995,995,995,995,995,995,995,995,995,995,1000);

    $LTab=array(
          0.5=>array(1=>0,-191,-274,-325,-362,-391,-414,-434,-451,-465,-478,-490,-500,-510,-519,-527,-534,-542,-548,-554),
          1  =>array(2=>     0,-122,-191,-238,-274,-302,-325,-345,-362,-378,-391,-403,-414,-425,-434,-443,-451,-458,-465),
          1.5=>array(2=>   191,   0, -90,-148,-191,-224,-251,-274,-293,-310,-325,-339,-351,-362,-373,-382,-391,-399,-407),
          2  =>array(3=>        122,   0, -72,-122,-160,-191,-216,-238,-257,-274,-289,-302,-314,-325,-336,-345,-354,-362),
          2.5=>array(3=>        274,  90,   0, -60,-104,-138,-167,-191,-212,-230,-246,-260,-274,-286,-297,-307,-316,-325),
          3  =>array(4=>             191,  72,   0, -51, -90,-122,-148,-171,-191,-208,-224,-238,-251,-263,-274,-284,-293),
          3.5=>array(4=>             325, 148,  60,   0, -44, -80,-109,-134,-155,-174,-191,-206,-220,-232,-244,-254,-264),
          4  =>array(5=>                  238, 122,  51,   0, -40, -72, -99,-122,-142,-160,-176,-191,-204,-215,-228,-238),
          4.5=>array(5=>                  362, 191, 104,  44,   0, -36, -65, -90,-112,-131,-148,-164,-178,-191,-203,-214),
          5  =>array(6=>                       274, 160,  90,  40,   0, -32, -60, -83,-104,-122,-138,-153,-167,-179,-191),
          5.5=>array(6=>                       391, 224, 138,  80,  36,   0, -30, -55, -77, -96,-114,-130,-144,-157,-169),
          6  =>array(7=>                            302, 191, 122,  72,  32,   0, -27, -51, -72, -90,-107,-122,-136,-148),
          6.5=>array(7=>                            414, 251, 164, 106,  65,  30,   0, -25, -47, -67, -85,-101,-115,-128),
          7  =>array(8=>                                 325, 216, 148,  99,  60,  27,   0, -24, -44, -63, -80, -95,-109),
          7.5=>array(8=>                                 434, 274, 191, 134,  90,  55,  25,   0, -22, -42, -60, -76, -90),
          8 =>array(9=>                                      345, 238, 171, 122,  83,  51,  24,   0, -21, -40, -56, -72),
          8.5=>array(9=>                                      451, 293, 212, 155, 112,  77,  47,  22,   0, -20, -37, -53),
          9  =>array(10=>                                          362, 257, 191, 142, 104,  72,  44,  21,   0, -19, -36),
          9.5=>array(10=>                                          465, 310, 230, 174, 131,  90,  67,  42,  20,   0, -18),
          10 =>array(11=>                                               378, 274, 208, 160, 122,  90,  63,  40,  19,   0),
          10.5=>array(11=>                                              478, 325, 246, 191, 148, 114,  85,  60,  37,  18),
          11 =>array(12=>                                                    391, 289, 224, 176, 138, 108,  80,  56,  36),
          12.5=>array(12=>                                                   490, 339, 260, 206, 164, 130, 101,  76,  53),
          12 =>array(13=>                                                         403, 302, 238, 191, 153, 122,  95,  72),
          12.5=>array(13=>                                                        500, 351, 274, 220, 178, 144, 115,  90),
          13 =>array(14=>                                                              414, 314, 251, 204, 167, 136, 109),
          13.5=>array(14=>                                                             510, 362, 286, 232, 191, 157, 128),
          14 =>array(15=>                                                                   425, 325, 263, 216, 179, 148),
          14.5=>array(15=>                                                                  519, 373, 297, 244, 203, 169),
          15 =>array(16=>                                                                        434, 336, 274, 228, 191),
          15.5=>array(16=>                                                                       527, 382, 307, 254, 214),
          16 =>array(17=>                                                                             443, 345, 284, 238),
          16.5=>array(17=>                                                                            535, 391, 316, 264),
          17 =>array(18=>                                                                                  451, 354, 293),
          17.5=>array(18=>                                                                                 542, 399, 325),
          18 =>array(19=>                                                                                       458, 362),
          18.5=>array(19=>                                                                                      548, 407),
          19 =>array(20=>                                                                                            465),
          19.5=>array(20=>                                                                                           554));
    $n=0;
    $W=0;
    $We=0;
    $Leistung=0;
	$Rn=0;
	$In=0;
	$E=0;
	$niveau=0;
	if (count($Gegner) > 0) {
    foreach ($Gegner as $G)
     {
      if ($G[0]==0) continue;
      if (($G[1]!="1") and ($G[1]!="0") and ($G[1]!="5")) continue;
      $n++;
      if ($R0>0)
       {
        $D=$R0-$G[0];
        if ($D>sizeof($PTab))
			{ $P=1000; }
        Else if ($D>=0)
				{ $P=$PTab[$D]; }
			 Else if ($D<-sizeof($PTab))
					{ $P=0; }
				 Else
					{ $P=1000-$PTab[-$D]; }
        $We+=$P;
       }
      if ($G[1]=="1") $W+=1000;
      Else If ($G[1]=="5") $W+=500;
      $Leistung+=$G[0];
     } 
	}
    if ($n>0) $niveau=round($Leistung/$n);
    else $niveau=0;
    if ($n>4)
     {
      if ($W==0) { if ($R0==0) $Leistung = 0;
				   else $Leistung=$niveau-677; }
      Else If ($W==$n) $Leistung=$niveau+677;
      Else
       {
        $P=$W/1000;
		if (isset($LTab[$P][$n])) $Leistung=$niveau+$LTab[$P][$n];
		else $Leistung=$niveau;
       }
     }
    Else $Leistung=0;
    If ($R0>0)
     { 
      $E=$this->BerechneEFaktor($R0, $Index, $Alter);
      $Rn=(integer) round($R0+0.8*($W-$We)/($E+$n));
      $In=$Index+1;
     }
    Else if (($Leistung>0) and ($n>4))
     {
      if ($Leistung>=800) $Rn=$Leistung;
      Else $Rn=(integer) round($Leistung/8+700);
      $In=1;
      $E=0;
     }
    Else 
     {
      $Leistung=0;
      if ($n>0)
       {
        $Rn="Restp.";
        $In=0;
        $E=0;
       }
      Else
       {
        $Rn=$R0;
        $In=$Index;
        if (!isset($E)) $E=0;
       }
     }
  $dwz=array($Rn,$In,$W,$n,$We,$Leistung,$E,$niveau);
  //if (Return0)
   //{
    if ($Rn==0) $Rn=Null;
    if ($Rn==0)$In=Null;
    if ($R0==0)$We=Null;
    if ($n==0)$Leistung=Null;
    if ($R0==0)$E=Null;
    if ($n==0)$Niveau=Null;
   //}
  return $dwz;
 }

 
function BerechneEFaktor($R0,$Index,$Alter=3,$W=0,$We=0)
 {
/*Establishment*/
/*"E"-Faktortabelle*/
/*Benutzung:
1. Wenn DWZ nicht vorhanden E=0
2. Wen Index=1, dann E=5
2. �ber das Alter die Spalte 0,1 oder 2 festlegen
3. Von Unten nach Oben erste Zeile suchen, in der die DWZ kleiner ist.
4. In dieser Zeile sucht man sich die Spalte (min(Index,5)+3. Dort steht der E-Faktor */
  $fB=1;
  $SBr=0;
  If ($Index==0) $E=0;
  Else If ($Alter==1)
   {
    $fB = $R0 / 2000;
    If ($fB<0.5) $fB=0.5;
    If ($fB>1) $fB=1; 
    If ($R0<1300)
     {
      if (($W-$We)<=0) $SBr = exp((1300-$R0)/150)-1;
      Else $SBr=0;
     }
    Else $SBr=0;
    $E = (pow(($R0 / 1000),4)) * $fB + $SBr+5;
    If ($E<5) $E=5;
    If ($SBr>=0)
     {
      if ($E>150) $E=150;
     }
    Else If ($E>(5*$Index)) $E=5*$Index;
   }
  Else If ($Alter==2)
   {
    $E = pow($R0 / 1000,4)+10;
    if ($E<5)
     {
      $E=5;
     }
    else if ($E>30) $E=30;
    if ($E>(5*$Index)) $E=5*$Index;
   }
  else if (($Alter==3) or ($Alter==0))
   {
    $E = pow($R0 / 1000,4)+15;
    if ($E<5) $E=5;
    else if ($E>30) $E=30;
    if ($E>(5*$Index)) $E=5*$Index;
   }
  $E = round($E);
  return $E;
 }

		
}
?>