<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
/*******************************************************/
/********Implementierung der DWZ Berechnung nach********/
/*******************DSB-Wertungsordnung*****************/
/*****************Stand: Stand: 30. Mai 2014************/
/*Quelle: http://www.schachbund.de/wertungsordnung.html*/
/***Keine Garantie auf Korrektheit der Implementierung**/
/*******************************************************/
class clm_class_dwz_rechner {
	private $tournament;
	private $link;
	private $result;
	function __construct() {
		$this->tournament = array();
		$this->tournament[0] = array(); // DWZ Spieler
		$this->tournament[1] = array(); // Spiele von DWZ Spielern gegen DWZ lose Spieler
		$this->tournament[2] = array(); // DWZ lose Spieler
		$this->link = array();
	}
	public function addPlayer($id, $A, $R_o, $Index,$fide=0) {
		// ID muss einmalig und keine Nummer sein
		if (is_numeric($id) || isset($this->link[$id])) {
			return false;
		}
		// $A, $R_o und $Index müssen Ganzzahlig und größer oder gleich 0 sein.
		if(!clm_class_dwz_rechner::is_whole_number($A) || $A < 0) {
			$A=100; // Wahrscheinlich Alt (über 25)
		}
		if(!clm_class_dwz_rechner::is_whole_number($R_o) || $R_o < 0 || !clm_class_dwz_rechner::is_whole_number($Index) || $Index < 0) {
			$R_o=0;
			$Index=0;
		}

		// 3.2.6 DWZ-Index
		// [...]
		// Dieser beträgt "1" nach der ersten Auswertung und wird nach jeder weiteren Auswertung um "1" erhöht. 
		// Spielstärkewerte, die dem FIDE-Rating entstammen, werden als Ausgangszahl mit dem Index "6", 
		// solche aus einem vergleichbaren nationalen Wertungssystem mit dem Index "0" versehen
		if($Index == 0 && clm_class_dwz_rechner::is_whole_number($fide) && $fide>0) {
			$R_o=$fide;
			$Index=6;
		}

		if ($Index == 0) { // Spieler hat keine DWZ
			$i = count($this->tournament[2]);
			$this->tournament[2][$i] = array(0, array(), array());
			$this->link[$id] = array(false, $i);
		} else { // Spieler hat DWZ
			$i = count($this->tournament[0]);
			$this->tournament[0][$i] = array($A, $Index, $R_o, 0, array());
			$this->link[$id] = array(true, $i);
		}
		$this->result = null; // Bisheriges Ergebnis ist nun ungültig
		return true;
	}
	public function addMatch($id1, $id2, $result, $gresult) {
		if (!isset($this->link[$id1]) || !isset($this->link[$id2]) || !in_array($result, array(0, 0.5, 1)) || !in_array($gresult, array(0, 0.5, 1))) {
			return false;
		}
		if ($this->link[$id1][0] && $this->link[$id2][0]) { // Beide haben DWZ
			$this->tournament[0][$this->link[$id1][1]][3]+= $result;
			$this->tournament[0][$this->link[$id1][1]][4][] = $this->tournament[0][$this->link[$id2][1]][2];
			//$this->tournament[0][$this->link[$id2][1]][3]+= 1 - $result;
			$this->tournament[0][$this->link[$id2][1]][3]+= $gresult;
			$this->tournament[0][$this->link[$id2][1]][4][] = $this->tournament[0][$this->link[$id1][1]][2];
		} else if (!$this->link[$id1][0] && $this->link[$id2][0]) { // Nur $id2 hat DWZ
			//$this->tournament[1][] = array($this->link[$id2][1], $this->link[$id1][1], 1 - $result);
			$this->tournament[1][] = array($this->link[$id2][1], $this->link[$id1][1], $gresult);
			$this->tournament[2][$this->link[$id1][1]][0]+= $result;
			$this->tournament[2][$this->link[$id1][1]][1][] = $this->tournament[0][$this->link[$id2][1]][2];
		} else if ($this->link[$id1][0] && !$this->link[$id2][0]) { // Nur $id1 hat DWZ
			$this->tournament[1][] = array($this->link[$id1][1], $this->link[$id2][1], $result);
			//$this->tournament[2][$this->link[$id2][1]][0]+= 1 - $result;
			$this->tournament[2][$this->link[$id2][1]][0]+= $gresult;
			$this->tournament[2][$this->link[$id2][1]][1][] = $this->tournament[0][$this->link[$id1][1]][2];
		} else if (!$this->link[$id1][0] && !$this->link[$id2][0]) { // Keiner hat DWZ
			$this->tournament[2][$this->link[$id1][1]][2][] = array($this->link[$id2][1], $result);
			//$this->tournament[2][$this->link[$id2][1]][2][] = array($this->link[$id1][1], 1 - $result);
			$this->tournament[2][$this->link[$id2][1]][2][] = array($this->link[$id1][1], $gresult);
		}
		$this->result = null; // Bisheriges Ergebnis ist nun ungültig
		return true;
	}
	public function addRest($id, $R_o, $result, $count=1) {
		if (!isset($this->link[$id]) || !in_array($result, array(0, 0.5, 1)) || !clm_class_dwz_rechner::is_whole_number($R_o) || $R_o < 0) {
			return false;
		}
		$this->tournament[2][$this->link[$id][1]][0]+= $result;
		for($i=0;$i<$count;$i++) {
			$this->tournament[2][$this->link[$id][1]][1][] = $R_o;
		}
	}
	private function getPlayerPosition($id) {
		if (!isset($this->link[$id])) {
			return false;
		}
		if ($this->result == null) {
			$this->result = clm_class_dwz_rechner::tournament($this->tournament);
		}
		if ($this->link[$id][0]) {
			return $this->link[$id][1];
		} else {
			return $this->link[$id][1] + count($this->tournament[0]);
		}
	}
	// Ausgabe als Objekt, siehe Variablenbezeichnung innerhalb der Funktion
	public function getPlayerObject($id) {
		$i = $this->getPlayerPosition($id);
		if (is_bool($i)) {
			return false;
		}
		$object = new StdClass;
		$object->R_o = $this->result[$i][0];
		$object->R_oI = $this->result[$i][1];
		$object->W = $this->result[$i][2];
		$object->n = $this->result[$i][3];
		$object->W_e = $this->result[$i][4];
		$object->E = $this->result[$i][5];
		$object->R_p = $this->result[$i][6];
		$object->R_c = $this->result[$i][7];
		$object->R_n = $this->result[$i][8];
		$object->R_nI = $this->result[$i][9];
		$object->Rest = $this->result[$i][10];
		return $object;
	}
	public function getAllPlayerObject() {
		$output = array();
		foreach ($this->link as $id => $value) {
			$output[$id] = $this->getPlayerObject($id);
		}
		return $output;
	}
	// Ausgabe als Array (eventuell Assoziatives), Reihenfolge oder Benennung siehe Funktion
	public function getPlayerArray($id, $key = false) {
		$i = $this->getPlayerPosition($id);
		if (is_bool($i)) {
			return false;
		}
		$output = array();
		if ($key) {
			$output["R_o"] = $this->result[$i][0];	// alte Wertzahl  
			$output["R_oI"] = $this->result[$i][1];	// Index alte Wertzahl 
			$output["W"] = $this->result[$i][2];	// erzielte Punkte
			$output["n"] = $this->result[$i][3];	// Anzahl wertbarer Partien
			$output["W_e"] = $this->result[$i][4];	// Erwartung in Punkten
			$output["E"] = $this->result[$i][5];	// Entwichlungskoeffizient
			$output["R_p"] = $this->result[$i][6];	// Leistung
			$output["R_c"] = $this->result[$i][7];	// Wertzahldurchschnitt der Gegner = Niveau
			$output["R_n"] = $this->result[$i][8];	// neue Wertzahl
			$output["R_nI"] = $this->result[$i][9];	// Index neue Wertzahl
			$output["Rest"] = $this->result[$i][10];
		} else {
			$output[] = $this->result[$i][0];
			$output[] = $this->result[$i][1];
			$output[] = $this->result[$i][2];
			$output[] = $this->result[$i][3];
			$output[] = $this->result[$i][4];
			$output[] = $this->result[$i][5];
			$output[] = $this->result[$i][6];
			$output[] = $this->result[$i][7];
			$output[] = $this->result[$i][8];
			$output[] = $this->result[$i][9];
			$output[] = $this->result[$i][10];
		}
		return $output;
	}
	public function getAllPlayerArray($key = false) {
		$output = array();
		foreach ($this->link as $id => $value) {
			$output[$id] = $this->getPlayerArray($id, $key);
		}
		return $output;
	}
	public static function is_whole_number($var) {
		return (is_numeric($var) && (intval($var) == floatval($var)));
	}
	/***********************************************/
	/***********************************************/
	/* Statische Funktionen für die DWZ Berechnung */
	/***********************************************/
	/***********************************************/
	// Die unten aufgeführte Wahrscheinlichkeitstabelle für Gewinnerwartungen P(D) beruht auf der
	// sog. Normalverteilung. Sie ist mit der von der FIDE verwendeten Tabelle identisch.
	public static $PTab = array(0, 4, 11, 18, 26, 33, 40, 47, 54, 62, 69, 77, 84, 92, 99, 107, 114, 122, 130, 138, 146, 154, 163, 171, 180, 189, 198, 207, 216, 226, 236, 246, 257, 268, 279, 291, 303, 316, 329, 345, 358, 375, 392, 412, 433, 457, 485, 518, 560, 620, 735);
	public static $PTabReverse = array(0, 7, 14, 21, 29, 36, 43, 50, 57, 65, 72, 80, 87, 95, 102, 110, 117, 125, 133, 141, 149, 158, 166, 175, 184, 192, 202, 211, 220, 230, 240, 251, 262, 273, 284, 296, 309, 322, 336, 351, 366, 383, 401, 422, 444, 470, 501, 538, 589, 677);
	// input: array(array($A, $Index, $R_o, $W,GAMES),array($A, $Index, $R_o, $W,GAMES)),array($W,array(mit DWZ),array(ohne DWZ)),array(mit DWZ, ohne DWZ (bisher), Ergebnis))
	// output: 	array(DWZ alt,Index alt,W,n,We,E,Lstg.,Niveau,DWZ neu, Index neu)
	public static function tournament($tournament) {
		// Die neue DWZ der DWZlosen bestimmen
		$neueSpieler = clm_class_dwz_rechner::newDWZ($tournament[2]);
		// Die Spieler der 1. Zusatzstufe beisetzen
		for ($i = 0;$i < count($tournament[1]);$i++) {
			if ($neueSpieler[$tournament[1][$i][1]][3] == 1) {
				$tournament[0][$tournament[1][$i][0]][3]+= $tournament[1][$i][2];
				$tournament[0][$tournament[1][$i][0]][4][] = $neueSpieler[$tournament[1][$i][1]][0];
			}
		}
		// Die neue DWZ der Spieler bestimmen und zur Ausgabe vorbereiten
		$output = array();
		for ($i = 0;$i < count($tournament[0]);$i++) {
			$aktuell = clm_class_dwz_rechner::player($tournament[0][$i][0], $tournament[0][$i][1], $tournament[0][$i][2], $tournament[0][$i][3], $tournament[0][$i][4]);
			// return array(clm_class_dwz_rechner::R_n($R_o, $W, $W_e, $J, $Index, $n, $E),$Index,$E,$W_e);
			$output[$i][0] = $tournament[0][$i][2];
			$output[$i][1] = $tournament[0][$i][1];
			$output[$i][2] = $tournament[0][$i][3];
			$output[$i][3] = count($tournament[0][$i][4]);
			// Keine gewertete Partie gespielt (nur gegen Spieler der 2. Zusatzstufe oder ohne DWZ bleibenden) aber trotzdem in der Teilnehmerliste
			if (count($tournament[0][$i][4]) != 0) {
				$output[$i][4] = $aktuell[3];
				$output[$i][5] = $aktuell[2];
				$output[$i][7] = clm_class_dwz_rechner::R_c($tournament[0][$i][4]);
				$output[$i][6] = intval(round(clm_class_dwz_rechner::R_p($tournament[0][$i][3], $output[$i][7], count($tournament[0][$i][4]))));
				$output[$i][7] = intval(round($output[$i][7]));
				$output[$i][8] = $aktuell[0];
				$output[$i][9] = $aktuell[1];
			} else {
				$output[$i][4] = 0;
				$output[$i][5] = 0;
				$output[$i][6] = 0;
				$output[$i][7] = 0;
				$output[$i][8] = $tournament[0][$i][2];
				$output[$i][9] = $tournament[0][$i][1];
			}
			// Restpartien gibt es bei DWZ Spielern nicht
			$output[$i][10] = 0;
		} // array(Neue DWZ oder Restpartien (Array der gegnerischen DWZ)), Anzahl gewerteter Partien, bewertete Punkte, Status, Niveau)
		for ($i = 0;$i < count($tournament[2]);$i++) {
			// DWZ alt 	Erg. 	We 	E 	Lstg. 	Niveau 	DWZ neu 	+/-
			$output[$i + count($tournament[0]) ][0] = 0;
			$output[$i + count($tournament[0]) ][1] = 0;
			// array(Neue DWZ oder Restpartien (Array der gegnerischen DWZ)), Anzahl gewerteter Partien, bewertete Punkte, Status, Niveau)
			$output[$i + count($tournament[0]) ][2] = $neueSpieler[$i][2];
			$output[$i + count($tournament[0]) ][3] = $neueSpieler[$i][1];
			$output[$i + count($tournament[0]) ][4] = 0;
			$output[$i + count($tournament[0]) ][5] = 0;
			if ($neueSpieler[$i][3] > 0) {
				$output[$i + count($tournament[0]) ][6] = intval(round(clm_class_dwz_rechner::R_p($neueSpieler[$i][2], $neueSpieler[$i][4], $neueSpieler[$i][1])));
			} else {
				$output[$i + count($tournament[0]) ][6] = 0;
			}
/* bis 3.9.0
			$output[$i + count($tournament[0]) ][7] = intval(round($neueSpieler[$i][4]));
			$output[$i + count($tournament[0]) ][8] = $neueSpieler[$i][0];
			if ($neueSpieler[$i][3] > 0) {
				$output[$i + count($tournament[0]) ][9] = 1;
			} else {
				$output[$i + count($tournament[0]) ][9] = 0;
			}
			$output[$i + count($tournament[0]) ][10] = $neueSpieler[$i][5];
*/
// ab 3.9.1
			$output[$i + count($tournament[0]) ][7] = intval(round($neueSpieler[$i][4]));
			if ($output[$i + count($tournament[0]) ][7] == 0) $output[$i + count($tournament[0]) ][7] = intval(round($neueSpieler[$i][5]));
			$output[$i + count($tournament[0]) ][8] = $neueSpieler[$i][0];
			if ($neueSpieler[$i][3] > 0) {
				$output[$i + count($tournament[0]) ][9] = 1;
			} else {
				$output[$i + count($tournament[0]) ][9] = 0;
			}
			if ($output[$i + count($tournament[0]) ][8] == 0) {
				$output[$i + count($tournament[0]) ][10] = 1;
			} else {
				$output[$i + count($tournament[0]) ][10] = 0;
			}
		}
		return $output;
	}
	// Eingabe:
	// $players array(player,player,...)
	// $player --> Punkte, DWZgegner, NichtDWZGegner array(index auf $player, punkt)
	// Ausgabe:
	// array(Neue DWZ), Anzahl gewerteter Partien, bewertete Punkte, Status, Niveau, ev. Restpartien)
	public static function newDWZ($players) {
		// Wer bekommt eine DWZ?
		for ($i = 0;$i < count($players);$i++) {
			// Spieler hat 5 Spiele gespielt und nicht alle gewonnen oder verloren.
			// Diese Bedingung ist auf der Seite des DSB nicht zu finde, wird aber meines Wissens so angewendet
			if (count($players[$i][1]) >= 5 && $players[$i][0] > 0 && $players[$i][0] < count($players[$i][1])) {
				// 4.7.3.1 Erste DWZ der 1. Zusatzstufe
				// In der ersten Stufe bekommen nur diejenigen Spieler ohne Wertung eine DWZ,
				// die in diesem Turnier zuzüglich eventuell gespeicherter Restpartien mindestens 5 Gegner
				// mit etablierten DWZ aufweisen können. Diese DWZ (bzw. deren durch Korrekturen verbesserten Werte)
				// fließen bei allen Gegnern unmittelbar in die Berechnungen ein oder werden als Restpartien gespeichert.
				$players[$i][3] = 1;
			}
		}
		for ($i = 0;$i < count($players);$i++) {
			if(isset($players[$i][3]) && $players[$i][3]==1) {
				continue;
			}
			$relevantPoints = 0;
			for ($p = 0;$p < count($players[$i][2]);$p++) {
				if(isset($players[$players[$i][2][$p][0]][3]) && $players[$players[$i][2][$p][0]][3]==1) {
					$relevantPoints+=$players[$i][2][$p][1];
				}
			}
			if (count($players[$i][1]) + $relevantPoints >= 5) {
				// Darf nicht alle Spiele gewonnen oder verloren haben
				$allowNewDWZ = false;
				if($players[$i][0]==0) {
					foreach($players[$i][2] as $value) {
						if($value[1]>0) {
							$allowNewDWZ = true;
							break;
						}
					}
//				} else if($players[$i][0]==count($players[$i][0])) {
				} else if($players[$i][0]==count($players[$i][1])) {
					foreach($players[$i][2] as $value) {
						if($value[1]<1) {
							$allowNewDWZ = true;
							break;
						}
					}
				} else {
					$allowNewDWZ = true;
				}
				// 4.7.3.2 Erste DWZ der 2. Zusatzstufe
				// In der zweiten Stufe werden auch die in der ersten Stufe erhaltenen Erst-DWZ
				// (die noch keiner Iterationsbehandlung unterzogen sein müssen) zur Auffüllung auf mindestens 5 DWZ-Gegner eingesetzt,
				// um so weitere, zusätzliche Erst-DWZ zu erhalten. Mit deren dabei verwendeten Gegnern erfolgt wiederum eine Rückkopplung.
				// Alle anderen Gegner der Erst-DWZ-Spieler der 2. Stufe bleiben von der Anwendung dieser DWZ ausgeschlossen;
				// es gibt also auch keine zusätzlichen Restpartien.
				if($allowNewDWZ) {				
					$players[$i][3] = 2;
				} else {
					$players[$i][3] = 0;
				}
			} else {
				// Für die übrigen Spieler, die bei einer Auswertung nicht einbezogen werden können,
				// werden die verwendbaren Spielergebnisse entsprechend 4.4 als Restpartien gespeichert.
				$players[$i][3] = 0;
			}
		}




		$R_pS = array();
		// 4.7.2.1.1 Ermittlung eines Ausgangswertes
		// Zu der bestimmten durchschnittlichen Gegnerspielstärke (Rc = ∑Ro / n) wird anhand des erzielten Ergebnisses
		// der zugehörige Wertungsunterschied D nach Tabelle Anhang 2.2 oder Tabelle Anhang 2.3 in Anrechnung gebracht
		// und ein erster DWZ-Näherungswert erhalten (Rp = Rc + D).
		// Interpretation:
		// Die Ausgangswerte der Spieler 1. Zusatzstufe basieren nur auf den Spielen gegen DWZ Spieler
		// Die Ausgangswerte der Spieler 2. Zusatzstufe basieren auf den Spielen gegen DWZ Spieler und den Ausgangswerten der ersten Zusatzstufe
		for ($i = 0;$i < count($players);$i++) {
			if ($players[$i][3] == 1) {
				$R_c = clm_class_dwz_rechner::R_c($players[$i][1]);
				$R_pS[$i] = clm_class_dwz_rechner::R_p($players[$i][0], $R_c, count($players[$i][1]));
			}
			if ($players[$i][3] == 0) { // Zur Ausgabe der Ergebnisse
				$R_pS[$i] = 0;
			}
		}
		for ($i = 0;$i < count($players);$i++) {
			if ($players[$i][3] == 2) {
				$DWZ_others = $players[$i][1];
				$Wadd = 0;
				for ($p = 0;$p < count($players[$i][2]);$p++) {
					// Der Index der Spieler von $R_pS stimmt mit den von $players überein
					// Die Ausgangswerte der Spieler 1. Zusatzstufe werden für die der 2. Zusatzstufe verwendet
					if ($players[$players[$i][2][$p][0]][2] == 1) {
						$DWZ_others[] = $R_pS[$players[$i][2][$p][0]];
						$Wadd+= $players[$i][2][$p][1];
					}
				}
				$R_c = clm_class_dwz_rechner::R_c($DWZ_others);
				$R_pS[$i] = clm_class_dwz_rechner::R_p($players[$i][0] + $Wadd, $R_c, count($DWZ_others));
			}
		}
		//4.7.2.2 Gegner erwerben im Turnier ebenfalls erste DWZ
		// Wenn mehrere erste DWZ ermittelt worden sind,
		// so beeinflussen sie sich bei weiteren Rechnungen und Iterationen (siehe 4.7.2.1.3) gegenseitig,
		// Das wird letztlich durch eine Gesamtiteration (siehe 4.7.4) ausgeglichen.
		// Interpretation:
		// Alle Spieler mit einer neuen ersten DWZ beeinflussen sich gegenseitig, erst bei stabilen Werten folgt der abbruch.
		do {
			$perfect = 0; // Konstanz der Einerstellen (W und W_e)
			for ($i = 0;$i < count($players);$i++) {
				if ($players[$i][3] != 0) {
					$DWZ_others = $players[$i][1];
					$Wadd = 0;
					for ($p = 0;$p < count($players[$i][2]);$p++) {
						if ($players[$players[$i][2][$p][0]][3] != 0) {
							$DWZ_others[] = $R_pS[$players[$i][2][$p][0]];
							$Wadd+= $players[$i][2][$p][1];
						}
					}
					$W_e = clm_class_dwz_rechner::W_e($R_pS[$i], $DWZ_others);
					$W = $players[$i][0] + $Wadd;
					// Für alle bisher ungewerteten Spieler, die nach 4.7.3.1 und 4.7.3.2 in der ersten bzw.
					// zweiten Zusatzstufe eine erste DWZ erhalten können wird zum Abschluss nach dem Einbeziehen
					// aller verwendbaren Partieresultate eine Gesamtiteration bis zur Konstanz der Einerstellen durchgeführt.
					// --> Interpretation: abs(W-W_e)>0.01 ||
					if (abs($W - $W_e) > 0.01) {
						// P(D) - Durchschnitt = (W - We) / n + 0,500
						// Kann negativ werden !!!!
						$P = ($W - $W_e) / count($DWZ_others) + 0.5;
						if ($P < 0) {
							$P = 0;
						}
						$D = clm_class_dwz_rechner::D($P);
						$R_pS[$i]+= $D;
						// Ist das Ergebnis bereits zu genau für die Tabelle,
						// aber ungenauer als 0.01 so ist dies ebenfalls ausreichend
						if ($D == 0) {
							$perfect++; // Tabelle zu ungenau
						}
					} else {
						$perfect++; // Abweichungsbestimmung
						
					}
				} else {
					$perfect++; // Spieler die keine DWZ haben sind immer korrekt
				}
			}
		} while ($perfect != count($players));
		// Der abschließende Iterationswert Ri ist in der Regel die erste DWZ eines Spielers.
		// Nur wenn Ri den Wert 800 unterschreitet, wird die erste DWZ nach der Formel
		// Rn = 700 + (Ri / 8)
		// korrigiert.
		for ($i = 0;$i < count($players);$i++) {
			if ($players[$i][3] != 0) { // Zur Ausgabe der Ergebnisse
				if ($R_pS[$i] < 800) {
					$R_pS[$i] = 700 + ($R_pS[$i] / 8);
				}
			}
		}
		// Bestimme die gewerteten Punkte / durschnittliche DWZ bei den Restpartien
		$output = array();
		for ($i = 0;$i < count($players);$i++) {
			if ($players[$i][3] == 0) {
				$output[$i][0] = 0;
				$output[$i][5] = array_sum($players[$i][1]);
			} else {
				$output[$i][0] = intval(round($R_pS[$i])); // DWZ-neu
				$output[$i][5] = 0;
			}
			$output[$i][1] = count($players[$i][1]);
			$output[$i][2] = $players[$i][0];
			$output[$i][4] = $players[$i][1]; // Niveau
			for ($p = 0;$p < count($players[$i][2]);$p++) {
				if ($players[$players[$i][2][$p][0]][3] == 1) {
					// Anstatt der DWZ gibt es die relevanten Informationen zu den Restpartien
					if ($players[$i][3] == 0) {
						$output[$i][5] += $R_pS[$players[$i][2][$p][0]];
					}
					$output[$i][1]++;
					$output[$i][2]+= $players[$i][2][$p][1];
					$output[$i][4][] = $R_pS[$players[$i][2][$p][0]]; // Niveau
					
				}
			}
			$output[$i][3] = $players[$i][3];
			if ($players[$i][3] > 0) {
				$output[$i][4] = clm_class_dwz_rechner::R_c($output[$i][4]); // Niveau
			} else {
				$output[$i][4] = 0;
			}
			if ($players[$i][3] == 0 && $output[$i][1]>0) {
				$output[$i][5] = $output[$i][5] / $output[$i][1];
			}
		}
		return $output;
	}
	// von Ro abhängige Erfolgszahl als Zwischenwert
	// 4.7.2.1.1 Ermittlung eines Ausgangswertes
	// Zu der bestimmten durchschnittlichen Gegnerspielstärke (Rc = ∑Ro / n) wird anhand des erzielten Ergebnisses
	// der zugehörige Wertungsunterschied D nach Tabelle Anhang 2.2 oder Tabelle Anhang 2.3 in Anrechnung gebracht
	// und ein erster DWZ-Näherungswert erhalten (Rp = Rc + D).
	public static function R_p($W, $R_c, $n) {
		if ($W == 0) {
			return $R_c - 677;
		} else if ($W == $n) {
			return $R_c + 677;
		}
		return $R_c + clm_class_dwz_rechner::D($W / $n);
	}
	// Ein Spieler mit DWZ gegen andere Spieler mit DWZ
	public static function player($A, $Index, $R_o, $W, $DWZ_others) {
		$W_e = clm_class_dwz_rechner::W_e($R_o, $DWZ_others);
		$n = count($DWZ_others);
		$J = clm_class_dwz_rechner::J($A);
		$E = clm_class_dwz_rechner::E($R_o, $W, $W_e, $J, $Index);
		return array(clm_class_dwz_rechner::R_n($R_o, $W, $W_e, $J, $Index, $n, $E), $Index + 1, $E, $W_e);
	}
	// Bei Spielern, die bereits eine DWZ besessen haben oder auch ein FIDE-Rating bzw. eine anerkannte,
	// vergleichbare nationale Wertung, wird die alte Wertungszahl Ro mit Hilfe der errechneten Punkterwartung We
	// und einem Entwicklungskoeffizienten E in die neue DWZ = Rn umgerechnet:
	// Rn = Ro + 800 x (W - We) / (E + n)
	public static function R_n($R_o, $W, $W_e, $J, $Index, $n, $E) {
		return intval(round($R_o + 800 * ($W - $W_e) / ($E + $n)));
	}
	//  	Durchschnitt der Gegner-DWZ
	// (Rc = ∑Ro / n)
	public static function R_c($DWZ_others) {
		$R_c = 0;
		foreach ($DWZ_others as $DWZ_other) {
			$R_c+= $DWZ_other;
		}
		return $R_c / count($DWZ_others);
	}
	// Der Entwicklungskoeffizient E
	// E = Grundwert E0 x Beschleunigungsfaktor fB + Bremszuschlag SBr
	// Für E gelten folgende Begrenzungen:
	// Der Wert von E ist stets ganzzahlig gerundet anzusetzen.
	// Er ist abhängig vom Index und muss mindestens 5 betragen.
	// Sein Maximalwert ist 30 bzw. 5 x Index bei SBr = 0,
	// er darf für SBr ≥ 0 den Wert von 150 nicht überschreiten.
	// Für Spieler ohne Wertungszahl wird bei der Ermittlung von E der Index 1 verwendet.
	// Es gilt somit bei SBr = 0:
		// E ≥ 5 und E ≤ 30 (Index > 5)
		// E ≥ 5 und E ≤ 5 x Index (Index < 6)
	// Es gilt somit bei SBr > 0:
		// E ≥ 5 und E ≤ 150
	
	public static function E($R_o, $W, $W_e, $J, $Index) {
		if ($Index == 0) {
			$Index = 1;
		}
		$S_Br = clm_class_dwz_rechner::S_Br($R_o, $W, $W_e);
		$f_B = clm_class_dwz_rechner::f_B($R_o, $W, $W_e, $J);
		$E_0 = clm_class_dwz_rechner::E_0($R_o, $J);
		$E = round($E_0 * $f_B + $S_Br);
		if ($E < 5) {
			return 5;
		}
		if ($S_Br == 0) {
			if ($Index < 6 && $E > 5 * $Index) {
				return 5 * $Index;
			} else if ($E > 30) { 
				return 30;
			} else {
				return $E;
			}
		} else { // $S_Br stets größer als 0 (Eigenschaften von e^x, x>0)
			if ($E > 150) {
				return 150;
			} else {
				return $E;
			}
		}
	}
	// Die Punkterwartung eines Spielers in einem Turnier errechnet sich
	// als die Summe aller einzelnen Gewinnerwartungen P(D)
	public static function W_e($R_o, $DWZ_others) {
		$W_e = 0;
		for ($i = 0;$i < count($DWZ_others);$i++) {
			$W_e+= clm_class_dwz_rechner::P($R_o, $DWZ_others[$i]);
		}
		return $W_e;
	}
	// Für die Gewinnerwartung p von Spieler1 gegen Spieler2 wird gemäß 2.3.1 eine Normalverteilung angesetzt,
	// die nur von der ganzzahligen Differenz D = DWZ(Spieler1) - DWZ(Spieler2) [ggf. negativ] abhängt
	// Eingang: DWZ der Spieler vor dem Turnier
	// Ausgabe: Siegwarscheinlichkeit nach Fide Tablle für Spieler a ($DWZ_a)
	public static function P($DWZ, $DWZ_other) {
		$D = $DWZ - $DWZ_other;
		if ($D > 0) {
			$Vorzeichen = 1;
		} else {
			$Vorzeichen = - 1;
			$D = $D * $Vorzeichen;
		}
		for ($i = count(clm_class_dwz_rechner::$PTab) - 1;$i >= 0;$i--) {
			if ($D >= clm_class_dwz_rechner::$PTab[$i]) {
				break;
			}
		}
		return 0.50 + ($i * $Vorzeichen) * 0.01;
	}
	// Umkehrung von P(D), Erwarteter Differenzunterschied bei einem bestimmten Ergebnis
	public static function D($P) {
		if ($P <= 0.50) {
			$Vorzeichen = - 1;
		} else {
			$P = 1 - $P;
			$Vorzeichen = 1;
		}
		$i = 50 - round($P * 100);
		// 100% Erwarteter Sieg sind durch die Iterationsschritte manchmal vorhanden
		if ($i == 50) {
			$i = 49;
		}
		return clm_class_dwz_rechner::$PTabReverse[$i] * $Vorzeichen;
	}
	// Grundwert
	// E0 = (Ro / 1000 )^4 + J
	public static function E_0($R_o, $J) {
		return pow($R_o / 1000, 4) + $J;
	}
	// Beschleunigungsfaktor für Jugendliche
	//fB = Ro / 2000 mit 0,5 ≤ fB ≤ 1,0
	//nur für Jugendliche ($J==5) bis 20 Jahre bei W ≥ We, sonst fB = 1.
	public static function f_B($R_o, $W, $W_e, $J) {
		if ($J == 5 && $W >= $W_e) {
			$f_B = $R_o / 2000;
			if ($f_B < 0.5) {
				return 0.5;
			}
			if ($f_B > 1) {
				return 1;
			}
			return $f_B;
		} else {
			return 1;
		}
	}
	// Bremszuschlag für DWZ Schwache
	// SBr = e ^((1300-Ro)/150) - 1
	// nur für R0 < 1300 und W ≤ We, sonst SBr = 0.
	public static function S_Br($R_o, $W, $W_e) {
		if ($R_o < 1300 && $W <= $W_e) {
			return exp((1300 - $R_o) / 150) - 1;
		} else {
			return 0;
		}
	}
	// Hierin ist J abhängig vom Alter:
	// Jugendliche bis 20 Jahre J = 5
	// Junioren von 21 bis 25 Jahre J = 10
	// alle Spieler über 25 Jahre J = 15
	public static function J($A) {
		if ($A <= 20) {
			return 5;
		} else if ($A <= 25) {
			return 10;
		} else {
			return 15;
		}
	}
	public static function test() {
		// http://www.schachbund.de/turnier.html?code=B431-636-C4F
		$test = new clm_class_dwz_rechner();
		$test->addPlayer("Barthel", 20, 0, 0);
		$test->addRest("Barthel", 1177, 0);
		$test->addPlayer("Afflerbach", 20, 894, 9);
		$test->addPlayer("Johannes", 20, 1087, 5);
		$test->addPlayer("Wolf", 20, 905, 6);
		$test->addPlayer("Weiß", 20, 1255, 1);
		$test->addMatch("Afflerbach", "Barthel", 0); // 1
		$test->addMatch("Barthel", "Johannes", 0); // 0
		$test->addMatch("Barthel", "Wolf", 1); // 1
		$test->addMatch("Weiß", "Barthel", 1); // 0
		echo print_r($test->getAllPlayerObject());
		// --> Ergebnis: neue DWZ: 985
		//	tastsächliche neue DWZ: 983
		// Begründung: Tabelle ungenauer als die vom DSB verwendete
		// Abweichung Niveau kommt vom Einbezug der Restpartie, DSB nimmt diese aus
		
	}
}
?>
