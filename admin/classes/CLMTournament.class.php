<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team  All rights reserved
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

class CLMTournament extends stdClass
{
    public function __construct($turnierid, $getData = false)
    {
        // $turnierid übergibt id des Turniers
        // $getData, ob die Turneirdaten aus clm_turniere sofort ausgelesen werden sollen

        // DB
        $this->_db				= JFactory::getDBO();

        // turnierid
        $this->turnierid = $turnierid;

        // get data?
        if ($getData) {
            $this->_getData();
        }

    }


    public function _getData()
    {

        $this->data = JTable::getInstance('turniere', 'TableCLM');
        $this->data->load($this->turnierid);

    }


    /**
    * check, ob User Zugriff hat
    * drei Zugangsmöichgkeiten - aller per Default auf TRUE
    */
    public function checkAccess($usertype_admin = true, $usertype_tl = true, $id_tl = '')
    {

        $clmAccess = clm_core::$access;
        $clmAccess->accesspoint = 'BE_tournament_edit_detail';

        if ($clmAccess->access('BE_tournament_edit_detail') === true) {
            return true;
        }
        // tl?
        if ($id_tl == clm_core::$access->getJid() and $clmAccess->access() !== false) {
            return true;
        }
        return false;

    }

    public function getPlayersIn()
    {

        // Anzahl gemeldeter Spieler
        if ($this->turnierid != "") {
            $query = "SELECT COUNT(*) FROM `#__clm_turniere_tlnr`"
                    . " WHERE turnier = ".$this->turnierid
            ;
            $this->_db->setQuery($query);
            return $this->_db->loadResult();
        } else {
            return null;
        }

    }


    /**
    * check, ob ein Turnier schon gestartet wurde
    * indem die Gesamtzahl von Spielern errungener Punkte ermittelt wird
    * TODO: später durch ein Flag in der DB ersetzen
    */
    public function checkTournamentStarted()
    {

        // Ergebnisse gemeldet
        $query = "SELECT COUNT(*) FROM `#__clm_turniere_rnd_spl`"
            ." WHERE turnier = ".$this->turnierid
            ." AND ergebnis IS NOT NULL"
        ;
        $this->_db->setQuery($query);
        if ($this->_db->loadResult() > 0) {
            $this->started = true;
        } else {
            $this->started = false;
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
    public function checkCorrectSnr()
    {

        $query = 'SELECT MAX(snr) AS maxSnr, MIN(snr) AS minSnr, COUNT(DISTINCT(snr)) AS distinctSnr, COUNT(snr) AS countSnr'
            . ' FROM #__clm_turniere_tlnr'
            . ' WHERE turnier = '.$this->turnierid
        ;
        $this->_db->setQuery($query);
        $this->checkSnr = $this->_db->loadObject();
        if ($this->checkSnr->minSnr > 1 or $this->checkSnr->maxSnr > $this->data->teil or $this->checkSnr->distinctSnr != $this->checkSnr->countSnr) {
            return false;
        }

        return true;

    }


    /**
    * errechnet/aktualisiert Rangliste/Punktesummen eines Turniers
    */
    public function calculateRanking()
    {

        // 'virtueller Gegner' gültig bis laut FIDE-Beschluß
        $enddate_vg = '2024-07-31';

        // Parameter auslesen, für FIDE-Ranglistenkorrektur und TWZ
        $query = 'SELECT `params`'
            . ' FROM #__clm_turniere'
            . ' WHERE id = '.$this->turnierid
        ;
        $this->_db->setQuery($query);

        $turParams = new clm_class_params($this->_db->loadResult());
        $paramTBFideCorrect = $turParams->get('optionTiebreakersFideCorrect', 0);
        $param50PercentRule = $turParams->get('option50PercentRule', 0);
        $paramuseAsTWZ = $turParams->get('useAsTWZ', 0);
        $query = ' SELECT *'
            . ' FROM #__clm_turniere'
            . ' WHERE id = '.$this->turnierid
        ;
        $this->_db->setQuery($query);
        $this->data	= clm_core::$db->loadObject($query);
        $dg = $this->data->dg;
        $runden = $this->data->runden;
        $teil = $this->data->teil;
        if ($this->data->typ != 1) {
            $paramTBFideCorrect = 0;
        }
        $sieg = $this->data->sieg;
        $siegs = $this->data->siegs;
        $remis = $this->data->remis;
        $remiss = $this->data->remiss;
        $nieder = $this->data->nieder;
        $niederk = $this->data->niederk;
        $dateEnd = $this->data->dateEnd;

        //Turnierteilnehmer
        $query = " SELECT a.* "
            ." FROM #__clm_turniere_tlnr as a "
            ." WHERE turnier = ".$this->turnierid
            ." ORDER BY a.snr "
        ;
        $player	= clm_core::$db->loadObjectList($query);

        if (count($player) < 1) {
            return;
        }

        // TWZ ggf. korrigieren
        foreach ($player as $player1) {
            /*			if($paramuseAsTWZ == 0) {
                            if ($player1->FIDEelo >= $player1->start_dwz) { $player1->twz = $player1->FIDEelo; }
                            else { $player1->twz = $player1->start_dwz; }
                        } elseif ($paramuseAsTWZ == 1) {
                            if ($player1->start_dwz > 0) { $player1->twz = $player1->start_dwz; }
                            else { $player1->twz = $player1->FIDEelo; }
                        } elseif ($paramuseAsTWZ == 2) {
                            if ($player1->FIDEelo > 0) { $player1->twz = $player1->FIDEelo; }
                            else { $player1->twz = $player1->start_dwz; }
                        }
            */
            $player1->twz = clm_core::$load->gen_twz($paramuseAsTWZ, $player1->start_dwz, $player1->FIDEelo);
        }

        //bisherige Rankingdaten löschen
        $query = "UPDATE #__clm_turniere_tlnr"
                . " SET sum_punkte = 0, sum_wins = 0, "
                . " anz_spiele = 0, "
                . " sumTiebr1 = 0, sumTiebr2 = 0, sumTiebr3 = 0 "
                . " WHERE turnier = ".$this->turnierid
        ;
        clm_core::$db->query($query);

        // alle FW in Array schreiben
        $arrayFW = array();
        for ($tb = 1; $tb <= 3; $tb++) {
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
        for ($s = 0; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
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

        // Startpunkt für Punktesumme sind Sonderpunkte
        foreach ($player as $player1) {
            $array_PlayerPunkte[$player1->snr] = $player1->s_punkte;
        }

        // alle Matches in DatenArray schreiben
        $query = "SELECT m.tln_nr, m.heim, m.gegner, m.dg, m.runde, m.ergebnis, tl.FIDEelo, tl.start_dwz, tl.twz FROM `#__clm_turniere_rnd_spl` as m"
                . " LEFT JOIN #__clm_turniere_tlnr as tl ON tl.turnier = m.turnier AND tl.snr = m.gegner "
                . " WHERE m.turnier = ".$this->turnierid." AND m.ergebnis IS NOT NULL"
        ;
        $this->_db->setQuery($query);
        $matchData = $this->_db->loadObjectList();
        $z = count($matchData);

        // Finden der letzten gespielten Runde
        // und Anlegen einer Matrix der gesetzten Matches
        $maxround = 0;
        $matrix = array();
        foreach ($matchData as $key => $value) {
            if (($value->ergebnis < 3 or $value->ergebnis == 9 or $value->ergebnis == 10) and ((($value->dg - 1) * $runden) + $value->runde) > $maxround) {
                $maxround = (($value->dg - 1) * $runden) + $value->runde;
            }
            $matrix[$value->tln_nr][$value->dg][$value->runde] = 1;
        }

        // für Spieler, die nicht gesetzt wurden, werden spielfreie Pseudo-Paarungen angelegt (für FIDE-Ranglistenkorrektur)
        for ($s = 1; $s <= $teil; $s++) { 		// alle Startnummern durchgehen
            for ($d = 1; $d <= $dg; $d++) { 		// alle Durchgänge durchgehen
                for ($r = 1; $r <= $runden; $r++) { 	// alle Runden durchgehen
                    if ($maxround < ((($d - 1) * $runden) + $r)) {
                        break;
                    }  		// nur bis zur aktuellen Runde
                    if (!isset($matrix[$s][$d][$r])) {
                        $matchData[$z] = new stdClass();
                        $matchData[$z]->tln_nr = $s;
                        $matchData[$z]->heim = 1;
                        $matchData[$z]->gegner = 0;
                        $matchData[$z]->dg = $d;
                        $matchData[$z]->runde = $r;
                        $matchData[$z]->ergebnis = 8;		// spielfrei
                        $z++;
                    }
                }
            }
        }

        // prüfen, ob mindestens 50% der Spiele gespielt wurden ab $maxround = 5 und Vollturnier
        $gamesCount = array();
        for ($s = 0; $s <= $teil; $s++) { 		// alle Startnummern durchgehen, auch der Spieler 0
            $gamesCount[$s] = new stdClass();
            $gamesCount[$s]->tab = 1;
            $gamesCount[$s]->count = 0;
        }
        if ($maxround > 4 and $this->data->typ == 2 and $param50PercentRule == 1) {		//nur Vollturniere und Prüfung nicht ausgeschaltet
            $anz_paarungen = 0;
            $anz_gespielt = 0;
            foreach ($matchData as $key => $value) {
                if ($maxround < ((($value->dg - 1) * $runden) + $value->runde)) {
                    continue;
                }  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
                $anz_paarungen++;
                if ($value->ergebnis != 8 and !is_null($value->ergebnis)) {
                    $anz_gespielt++;
                }
            }
            if (($anz_gespielt / $anz_paarungen) > 0.7) {
                foreach ($matchData as $key => $value) {
                    if ($maxround < ((($value->dg - 1) * $runden) + $value->runde)) {
                        continue;
                    }  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
                    if ($value->ergebnis != 4 and $value->ergebnis != 6 and $value->ergebnis != 7 and $value->ergebnis != 8 and $value->ergebnis != 11 and $value->ergebnis != 12 and $value->ergebnis != 13 and !is_null($value->ergebnis)) {
                        $gamesCount[$value->tln_nr]->count++;
                    }
                }
                for ($s = 1; $s <= $teil; $s++) { 		// alle Startnummern durchgehen
                    if ($gamesCount[$s]->count < $maxround / 2) {
                        $gamesCount[$s]->tab = 0;
                    }
                }
            }
        }

        // Punkte/Siege
        // alle Matches durchgehen -> Spieler erhalten Punkte und Wins
        foreach ($matchData as $key => $value) {
            if ($gamesCount[$value->tln_nr]->tab == 0) {
                continue;
            }    //teilnehmer hat weniger als 50% der Partien gespielt
            if ($gamesCount[$value->gegner]->tab == 0) {
                continue;
            }    //gegner hat weniger als 50% der Partien gespielt
            if ($maxround < ((($value->dg - 1) * $runden) + $value->runde)) {
                continue;
            }  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
            if ($value->tln_nr == 0) {
                continue;
            }    //techn. Teilnehmer bei ungerader Teilnehmerzahl
            if ($value->heim == 1) {
                $vsieg = $sieg;
            } else {
                $vsieg = $siegs;
            }
            if ($value->heim == 1) {
                $vremis = $remis;
            } else {
                $vremis = $remiss;
            }
            //if ($value->ergebnis == 8) continue;  //spielfrei
            if ($value->ergebnis != 8) {
                $array_PlayerSpiele[$value->tln_nr] += 1;
            }
            if ($value->ergebnis == 2 or $value->ergebnis == 10 or $value->ergebnis == 12) { // remis
                $array_PlayerPunkte[$value->tln_nr] += $vremis;
                $array_PlayerPunkteTB[$value->tln_nr] += $vremis;
                $array_PlayerSumWert[$value->tln_nr] += ($vremis * ($maxround - $value->runde + 1));
            } elseif ($value->ergebnis == 1 or $value->ergebnis == 5 or $value->ergebnis == 11) { // Sieger
                $array_PlayerPunkte[$value->tln_nr] += $vsieg;
                $array_PlayerWins[$value->tln_nr] += 1;
                $array_PlayerSumWert[$value->tln_nr] += ($maxround - $value->runde + 1);
                if (($value->ergebnis == 5 or $value->ergebnis == 11) and $paramTBFideCorrect == 1) { // kampflos gewonnen und FIDE-Korrektur eingestellt?
                    if ($dateEnd > $enddate_vg) {
                        //						if ($value->runde == $runden)
                        //							$array_PlayerPunkteTB[$value->tln_nr] += $vremis; // FW-Korrektur Teil 1
                        //						else
                        $array_PlayerPunkteTB[$value->tln_nr] += $vsieg;
                    } // FW-Korrektur Teil 1
                    else {
                        $array_PlayerPunkteTB[$value->tln_nr] += $vremis;
                    } // FW-Korrektur Teil 1
                } else {
                    $array_PlayerPunkteTB[$value->tln_nr] += $vsieg;
                }
            } elseif ($value->ergebnis == 0) { 	// verloren
                $array_PlayerPunkte[$value->tln_nr] += $nieder;
                $array_PlayerPunkteTB[$value->tln_nr] += $nieder;
            } elseif ($value->ergebnis == 3) { 							// Ergebnis 0-0
                $array_PlayerPunkte[$value->tln_nr] += $niederk;
                if ($paramTBFideCorrect == 1) {  							// FIDE-Korrektur eingestellt? -> FW-Korrektur Teil 1
                    $array_PlayerPunkteTB[$value->tln_nr] += $vremis;
                }
            } elseif ($value->ergebnis == 4) { 							// kampflos verloren
                $array_PlayerPunkte[$value->tln_nr] += $niederk;
                if ($paramTBFideCorrect == 1) {  							// FIDE-Korrektur eingestellt? -> FW-Korrektur Teil 1
                    if ($dateEnd > $enddate_vg) {
                        $array_PlayerPunkteTB[$value->tln_nr] += $niederk;
                    } // FW-Korrektur Teil 1
                    else {
                        $array_PlayerPunkteTB[$value->tln_nr] += $vremis;
                    }
                }
            } elseif ($value->ergebnis == 6) { 							// kampflos beide verloren -:- und FIDE-Korrektur eingestellt?
                $array_PlayerPunkte[$value->tln_nr] += $niederk;
                if ($paramTBFideCorrect == 1) {  							// FIDE-Korrektur eingestellt? -> FW-Korrektur Teil 1
                    $array_PlayerPunkteTB[$value->tln_nr] += $vremis;
                }
            } elseif ($value->ergebnis == 7) { 							// nicht gespielt --- und FIDE-Korrektur eingestellt?
                $array_PlayerPunkte[$value->tln_nr] += $niederk;
                if ($paramTBFideCorrect == 1) {  							// FIDE-Korrektur eingestellt? -> FW-Korrektur Teil 1
                    $array_PlayerPunkteTB[$value->tln_nr] += $vremis;
                }
            } elseif ($value->ergebnis == 8) { 							// spielfrei und FIDE-Korrektur eingestellt?
                $array_PlayerPunkte[$value->tln_nr] += $niederk;
                if ($paramTBFideCorrect == 1) {  							// FIDE-Korrektur eingestellt? -> FW-Korrektur Teil 1
                    if ($dateEnd > $enddate_vg) {
                        if ($value->runde == $runden) {
                            $array_PlayerPunkteTB[$value->tln_nr] += $vremis;
                        } // FW-Korrektur Teil 1
                        else {
                            $array_PlayerPunkteTB[$value->tln_nr] += $niederk;
                        }
                    } // FW-Korrektur Teil 1
                    else {
                        $array_PlayerPunkteTB[$value->tln_nr] += $vremis;
                    }
                }
            } elseif ($value->ergebnis == 13) { 						// kampflos beide verloren 0:- und FIDE-Korrektur eingestellt?
                $array_PlayerPunkte[$value->tln_nr] += $niederk;
                if ($paramTBFideCorrect == 1) {  							// FIDE-Korrektur eingestellt? -> FW-Korrektur Teil 1
                    $array_PlayerPunkteTB[$value->tln_nr] += $vremis;
                }
            }
        }

        // Buchholz & Sonneborn-Berger
        // erneut alle Matches durchgehen -> Spieler erhalten Feinwertungen
        foreach ($matchData as $key => $value) {
            if ($gamesCount[$value->tln_nr]->tab == 0) {
                continue;
            }    //teilnehmer hat weniger als 50% der Partien gespielt
            if ($gamesCount[$value->gegner]->tab == 0) {
                continue;
            }    //gegner hat weniger als 50% der Partien gespielt
            if ($maxround < ((($value->dg - 1) * $runden) + $value->runde)) {
                continue;
            }  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
            //if ($value->tln_nr == 0) continue;  // Ignorieren von techn. Spielern
            // Buchholz
            if (in_array(1, $arrayFW) or in_array(2, $arrayFW) or in_array(11, $arrayFW) or in_array(12, $arrayFW) or in_array(5, $arrayFW) or in_array(15, $arrayFW)) { // beliebige Buchholz als TieBreaker gewünscht?
                if ($value->ergebnis < 3 or $value->ergebnis == 9 or $value->ergebnis == 10 or $paramTBFideCorrect == 0) {
                    $array_PlayerBuchOpp[$value->tln_nr][$value->runde] = $array_PlayerPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
                } else { //Ranglistenkorrektur nach FIDE (Teil 2) nur für CH-Turniere
                    if ($dateEnd > $enddate_vg) {
                        //						if ($value->gegner == 0)
                        $array_PlayerBuchOpp[$value->tln_nr][$value->runde] = $array_PlayerPunkte[$value->tln_nr];
                        //						else
                        //							$array_PlayerBuchOpp[$value->tln_nr][$value->runde] = $array_PlayerPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
                    } else {
                        $query = "SELECT tln_nr, heim, gegner, dg, runde, ergebnis FROM `#__clm_turniere_rnd_spl`"
                            . " WHERE turnier = ".$this->turnierid
                            . " AND tln_nr = ".$value->tln_nr
                            . " AND ergebnis IS NOT NULL"
                            . " ORDER BY dg ASC, runde ASC"
                        ;
                        $this->_db->setQuery($query);
                        $matchDataSnr = $this->_db->loadObjectList();
                        $PlayerPunkteKOR = 0;
                        foreach ($matchDataSnr as $key => $valuesnr) {
                            if ($maxround < ((($valuesnr->dg - 1) * $runden) + $valuesnr->runde)) {
                                continue;
                            }  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
                            if (($valuesnr->dg < $value->dg) or ($valuesnr->dg == $value->dg and $valuesnr->runde < $value->runde)) {
                                if ($valuesnr->heim == 1) {
                                    $vsieg = $sieg;
                                } else {
                                    $vsieg = $siegs;
                                }
                                if ($valuesnr->heim == 1) {
                                    $vremis = $remis;
                                } else {
                                    $vremis = $remiss;
                                }
                                if ($valuesnr->ergebnis == 1) {
                                    $PlayerPunkteKOR += $vsieg;
                                } // Sieg
                                elseif ($valuesnr->ergebnis == 2 or $valuesnr->ergebnis == 10 or $valuesnr->ergebnis == 12) {
                                    $PlayerPunkteKOR += $vremis;
                                } // remis
                                elseif ($valuesnr->ergebnis == 5 or $valuesnr->ergebnis == 11) {
                                    $PlayerPunkteKOR += $vsieg;
                                } // Sieg kampflos
                            }
                        }
                        if ($value->heim == 1) {
                            $vsieg = $sieg;
                        } else {
                            $vsieg = $siegs;
                        }
                        if ($value->heim == 1) {
                            $vremis = $remis;
                        } else {
                            $vremis = $remiss;
                        }
                        if ($value->ergebnis == 4 or $value->ergebnis == 8) {
                            $PlayerPunkteKOR += $vsieg;
                        }// Gegner gewinnt kampflos oder spielfrei
                        if ($value->ergebnis == 12) {
                            $PlayerPunkteKOR += $vremis;
                        }// Gegner spielt kampflos remis                                                                                 neu
                        if ($value->ergebnis == 3 or $value->ergebnis == 6 or $value->ergebnis == 13) {
                            $PlayerPunkteKOR += $vsieg;
                        }// Gegner verliert auch kampflos, ist aber egal
                        //$PlayerPunkteKOR += 0.5 * (($runden * $dg) - (($value->dg - 1) * $runden) - $value->runde);
                        $PlayerPunkteKOR += ($vremis * (($maxround) - (($value->dg - 1) * $runden) - $value->runde));
                        $array_PlayerBuchOpp[$value->tln_nr][] = $PlayerPunkteKOR; // Array mit Gegnerwerten - für Streichresultat
                    }
                }
            }

            // Sonneborn-Berger
            if (in_array(3, $arrayFW) or in_array(13, $arrayFW)) { // SoBe als ein TieBreaker gewünscht?
                if ($value->ergebnis == 0 or $value->ergebnis == 9) {
                    $array_PlayerSoBeOpp[$value->tln_nr][$value->runde] = 0; 	// Array mit Gegnerwerten - für Streichresultat
                } elseif ($value->ergebnis == 1) {
                    $array_PlayerSoBeOpp[$value->tln_nr][$value->runde] = $array_PlayerPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
                } elseif ($value->ergebnis == 2 or $value->ergebnis == 10) {
                    $array_PlayerSoBeOpp[$value->tln_nr][$value->runde] = (.5 * $array_PlayerPunkteTB[$value->gegner]); // Array mit Gegnerwerten - für Streichresultat
                } elseif ($value->ergebnis == 12 and $paramTBFideCorrect == 0) {
                    $array_PlayerSoBeOpp[$value->tln_nr][$value->runde] = (.5 * $array_PlayerPunkteTB[$value->gegner]); // Array mit Gegnerwerten - für Streichresultat
                } elseif ($value->ergebnis == 5 and $paramTBFideCorrect == 0) {
                    $array_PlayerSoBeOpp[$value->tln_nr][$value->runde] = $array_PlayerPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
                } elseif ($value->ergebnis == 11 and $paramTBFideCorrect == 0) {
                    $array_PlayerSoBeOpp[$value->tln_nr][$value->runde] = $array_PlayerPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
                } elseif ($paramTBFideCorrect == 0) {
                    $array_PlayerSoBeOpp[$value->tln_nr][$value->runde] = 0; 		// Array mit Gegnerwerten - für Streichresultat
                } else { //Ranglistenkorrektur nach FIDE (Teil 2)
                    if ($dateEnd > $enddate_vg) {
                        if ($value->ergebnis == 5) {
                            if ($value->gegner == 0) {
                                $array_PlayerSoBeOpp[$value->tln_nr][$value->runde] = $array_PlayerPunkte[$value->tln_nr];
                            } else {
                                $array_PlayerSoBeOpp[$value->tln_nr][$value->runde] = $array_PlayerPunkteTB[$value->gegner];
                            } // Array mit Gegnerwerten - für Streichresultat
                        }
                    } else {
                        $query = "SELECT tln_nr, heim, gegner, dg, runde, ergebnis FROM `#__clm_turniere_rnd_spl`"
                            . " WHERE turnier = ".$this->turnierid
                            . " AND tln_nr = ".$value->tln_nr
                            . " AND ergebnis IS NOT NULL"
                            . " ORDER BY dg ASC, runde ASC"
                        ;
                        $this->_db->setQuery($query);
                        $matchDataSnr = $this->_db->loadObjectList();
                        $PlayerPunkteKOR = 0;
                        foreach ($matchDataSnr as $key => $valuesnr) {
                            if ($maxround < ((($valuesnr->dg - 1) * $runden) + $valuesnr->runde)) {
                                continue;
                            }  // Ignorieren von bereits gesetzten kampflos oder spielfrei in Folgerunden
                            if ($valuesnr->heim == 1) {
                                $vsieg = $sieg;
                            } else {
                                $vsieg = $siegs;
                            }
                            if ($valuesnr->heim == 1) {
                                $vremis = $remis;
                            } else {
                                $vremis = $remiss;
                            }
                            if (($valuesnr->dg < $value->dg) or ($valuesnr->dg == $value->dg and $valuesnr->runde < $value->runde)) {
                                if ($valuesnr->ergebnis == 1) {
                                    $PlayerPunkteKOR += $vsieg;
                                } // Sieg
                                elseif ($valuesnr->ergebnis == 2 or $valuesnr->ergebnis == 12) {
                                    $PlayerPunkteKOR += $vremis;
                                } // remis
                                elseif ($valuesnr->ergebnis == 5 or $valuesnr->ergebnis == 11) {
                                    $PlayerPunkteKOR += $vsieg;
                                } // Sieg kampflos
                            }
                        }
                        if ($value->heim == 1) {
                            $vsieg = $sieg;
                        } else {
                            $vsieg = $siegs;
                        }
                        if ($value->heim == 1) {
                            $vremis = $remis;
                        } else {
                            $vremis = $remiss;
                        }
                        if (($value->ergebnis == 5 or $value->ergebnis == 11)) {
                            $PlayerFaktorKOR = $vsieg;
                        }	// Spieler gewinnt kampflos
                        elseif (($value->ergebnis == 12)) {
                            $PlayerPunkteKOR += $vremis;
                            $PlayerFaktorKOR = $vremis;
                        }	// Spieler remis kampflos (bye)
                        else {
                            $PlayerFaktorKOR = 0;
                        }
                        $PlayerPunkteKOR += ($vremis * (($maxround) - (($value->dg - 1) * $runden) - $value->runde));
                        //echo "<br>p: $value->tln_nr  PlayerPunkteKOR: "; var_dump($PlayerPunkteKOR);
                        $array_PlayerSoBeOpp[$value->tln_nr][] = ($PlayerFaktorKOR * $PlayerPunkteKOR); // Array mit Gegnerwerten - für Streichresultat
                    }
                }
                //echo "<br>p: $value->tln_nr  array_PlayerSoBeOpp: "; var_dump($array_PlayerSoBeOpp[$value->tln_nr]);
            }

            // Elo-Schnitt
            if (in_array(6, $arrayFW) or in_array(16, $arrayFW)) { // Elo-Schnitt als ein TieBreaker gewünscht?
                if ($value->gegner == 0) {
                    //$array_PlayerEloOpp[$value->tln_nr][] = 0; 	// Array mit Gegnerwerten - für Streichresultat
                } else {
                    if ($value->FIDEelo > 0) {
                        $array_PlayerEloOpp[$value->tln_nr][] = $value->FIDEelo;
                    } else {
                        $array_PlayerEloOpp[$value->tln_nr][] = $value->start_dwz;
                    }
                } // Array mit Gegnerwerten - für Streichresultat
            }
            // DWZ-Schnitt
            if (in_array(8, $arrayFW) or in_array(18, $arrayFW)) { // DWZ-Schnitt als ein TieBreaker gewünscht?
                if ($value->gegner == 0) {
                    //$array_PlayerDWZOpp[$value->tln_nr][] = 0; 	// Array mit Gegnerwerten - für Streichresultat
                } else {
                    if ($value->start_dwz > 0) {
                        $array_PlayerDWZOpp[$value->tln_nr][] = $value->start_dwz;
                    } else {
                        $array_PlayerDWZOpp[$value->tln_nr][] = $value->FIDEelo;
                    }
                } // Array mit Gegnerwerten - für Streichresultat
            }
            // TWZ-Schnitt
            if (in_array(9, $arrayFW) or in_array(19, $arrayFW)) { // TWZ-Schnitt als ein TieBreaker gewünscht?
                if ($value->gegner == 0) {
                    //$array_PlayerTWZOpp[$value->tln_nr][] = 0; 	// Array mit Gegnerwerten - für Streichresultat
                } else {
                    if ($value->twz > 0) {
                        $array_PlayerTWZOpp[$value->tln_nr][] = $value->twz;
                    } else {
                        $array_PlayerTWZOpp[$value->tln_nr][] = $value->start_dwz;
                    }
                } // Array mit Gegnerwerten - für Streichresultat
            }
        }
        // Sonneborn-Berger
        if (in_array(3, $arrayFW)) { // normale Sonneborn-Berger als TieBreaker gewünscht?
            for ($s = 1; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerSoBeOpp[$s])) {
                    $array_PlayerSoBe[$s] = 0;
                } elseif (count($array_PlayerSoBeOpp[$s]) == 1) {
                    $array_PlayerSoBe[$s] = array_sum($array_PlayerSoBeOpp[$s]);
                } else {
                    $array_PlayerSoBe[$s] = array_sum($array_PlayerSoBeOpp[$s]);
                }
            }
        } elseif (in_array(13, $arrayFW)) { // Sonneborn-Berger mit Streichresultat
            for ($s = 1; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerSoBeOpp[$s])) {
                    $array_PlayerSoBe[$s] = 0;
                } elseif (count($array_PlayerSoBeOpp[$s]) == 0) {
                    $array_PlayerSoBe[$s] = 0;
                } elseif (count($array_PlayerSoBeOpp[$s]) == 1) {
                    $array_PlayerSoBe[$s] = $array_PlayerSoBeOpp[$s][0];
                } elseif (count($array_PlayerSoBeOpp[$s]) > 2) { //== ($dg * $runden))
                    $array_PlayerSoBe[$s] = array_sum($array_PlayerSoBeOpp[$s]) - min($array_PlayerSoBeOpp[$s]);
                } else {
                    $array_PlayerSoBe[$s] = array_sum($array_PlayerSoBeOpp[$s]);
                }
            }
        }

        // Buchholz
        if ((in_array(1, $arrayFW)) or (in_array(2, $arrayFW)) or (in_array(11, $arrayFW)) or (in_array(12, $arrayFW))) { // normale Buchholz als TieBreaker gewünscht?
            for ($s = 0; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerBuchOpp[$s])) {
                    $array_PlayerBuch[$s] = 0;
                } elseif (count($array_PlayerBuchOpp[$s]) == 1) {
                    $array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]);
                } else {
                    $array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]);
                }
            }
        }
        // Buchholz 1 Streichresultat
        if ((in_array(11, $arrayFW)) or (in_array(12, $arrayFW))) { // Buchholz mit Streichresultat
            for ($s = 0; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerBuchOpp[$s])) {
                    $array_PlayerBuch1St[$s] = 0;
                } elseif (count($array_PlayerBuchOpp[$s]) == 0) {
                    $array_PlayerBuch1St[$s] = 0;
                } elseif (count($array_PlayerBuchOpp[$s]) == 1) {
                    //					$array_PlayerBuch1St[$s] = $array_PlayerBuchOpp[$s][0];
                    $array_PlayerBuch1St[$s] = array_sum($array_PlayerBuchOpp[$s]);
                } elseif (count($array_PlayerBuchOpp[$s]) > 2) { //== ($dg * $runden))
                    $array_PlayerBuch1St[$s] = array_sum($array_PlayerBuchOpp[$s]) - min($array_PlayerBuchOpp[$s]);
                } else {
                    $array_PlayerBuch1St[$s] = array_sum($array_PlayerBuchOpp[$s]);
                }
            }
        }
        // mittlere Buchholz 2 Streichresultate (höchstes und niedrigstes)
        if (in_array(5, $arrayFW)) { // Buchholz mit Streichresultat
            for ($s = 1; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerBuchOpp[$s])) {
                    $array_PlayerBuchm11[$s] = 0;
                } elseif (count($array_PlayerBuchOpp[$s]) == 0) {
                    $array_PlayerBuchm11[$s] = 0;
                } elseif (count($array_PlayerBuchOpp[$s]) == 1) {
                    $array_PlayerBuchm11[$s] = $array_PlayerBuchOpp[$s][0];
                } elseif (count($array_PlayerBuchOpp[$s]) == 2) {
                    $array_PlayerBuchm11[$s] = array_sum($array_PlayerBuchOpp[$s]);
                } elseif (count($array_PlayerBuchOpp[$s]) > 2) { //== ($dg * $runden))
                    $array_PlayerBuchm11[$s] = array_sum($array_PlayerBuchOpp[$s]) - min($array_PlayerBuchOpp[$s]) - max($array_PlayerBuchOpp[$s]);
                } else {
                    $array_PlayerBuchm11[$s] = array_sum($array_PlayerBuchOpp[$s]);
                }
            }
        }

        // BuchholzSumme
        if ((in_array(2, $arrayFW)) or (in_array(12, $arrayFW))) { // Buchholz-Summe als TieBreaker gewünscht?
            // erneut alle Matches durchgehen -> Spieler erhalten Buchholzsummen
            foreach ($matchData as $key => $value) {
                if ($value->gegner >= 1) {
                    $array_PlayerBuSum[$value->tln_nr] += $array_PlayerBuch[$value->gegner];
                    if ($array_PlayerBuSumMin[$value->tln_nr] > $array_PlayerBuch[$value->gegner]) {
                        $array_PlayerBuSumMin[$value->tln_nr] = $array_PlayerBuch[$value->gegner];
                    }
                } else {
                    $array_PlayerBuSumMin[$value->tln_nr] = 0;
                }
            }
        }
        // BuchholzSumme mit 1 Streichwertung
        if ((in_array(12, $arrayFW)) or (in_array(12, $arrayFW))) { // Buchholz-Summe - 1 als TieBreaker gewünscht?
            // erneut alle Matches durchgehen -> Spieler erhalten Buchholzsummen - 1
            foreach ($matchData as $key => $value) {
                if ($value->gegner >= 1) {
                    $array_PlayerBuSum1St[$value->tln_nr] += $array_PlayerBuch1St[$value->gegner];
                }
            }
        }
        /*		// BuchholzSumme mit Streichresultat - alt
                if (in_array(12, $arrayFW)) { // als TieBreaker gewünscht?
                    $array_s12 = array();
                    foreach ($matchData as $key => $value) {
                            if ($value->ergebnis == 8) { // Spielfrei bzw. Pausieren
                                // Zielstellung: die spielfreie Runde wird als Streichresultat verwendet, weitere spielfreie Runden werden ignoriert
                                if (isset($array_s12[$value->tln_nr])) continue;
                                $array_s12[$value->tln_nr] = 1;
                            }
                            $array_PlayerBuSum1St[$value->tln_nr] += $array_PlayerBuch1St[$value->gegner];
                            if ($array_PlayerBuSum1StMin[$value->tln_nr] > $array_PlayerBuch1St[$value->gegner])
                                    $array_PlayerBuSum1StMin[$value->tln_nr] = $array_PlayerBuch1St[$value->gegner];
                    }
                    for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
                        if ($array_PlayerBuSum1StMin[$s] < 999)
                            $array_PlayerBuSum1St[$s] = $array_PlayerBuSum1St[$s] - $array_PlayerBuSum1StMin[$s];
                    }
                }
        */
        // Elo-Schnitt
        if (in_array(6, $arrayFW)) { // Elo-Schnitt als TieBreaker gewünscht?
            for ($s = 1; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerEloOpp[$s])) {
                    $array_PlayerElo[$s] = 0;
                } elseif (count($array_PlayerEloOpp[$s]) == 1) {
                    $array_PlayerElo[$s] = $array_PlayerEloOpp[$s][0];
                } else {
                    $c_EloOpp = 0;
                    foreach ($array_PlayerEloOpp[$s] as $EloOpp) {
                        if ($EloOpp > 0) {
                            $c_EloOpp++;
                        }
                    }
                    if ($c_EloOpp == 0) {
                        $array_PlayerElo[$s] = 0;
                    } else {
                        $array_PlayerElo[$s] = array_sum($array_PlayerEloOpp[$s]) / $c_EloOpp;
                    }
                }
            }
        }
        // DWZ-Schnitt
        if (in_array(8, $arrayFW)) { // DWZ-Schnitt als TieBreaker gewünscht?
            for ($s = 1; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerDWZOpp[$s])) {
                    $array_PlayerDWZ[$s] = 0;
                } elseif (count($array_PlayerDWZOpp[$s]) == 1) {
                    $array_PlayerDWZ[$s] = $array_PlayerDWZOpp[$s][0];
                } else {
                    $c_DWZOpp = 0;
                    foreach ($array_PlayerDWZOpp[$s] as $DWZOpp) {
                        if ($DWZOpp > 0) {
                            $c_DWZOpp++;
                        }
                    }
                    if ($c_DWZOpp == 0) {
                        $array_PlayerDWZ[$s] = 0;
                    } else {
                        $array_PlayerDWZ[$s] = array_sum($array_PlayerDWZOpp[$s]) / $c_DWZOpp;
                    }
                }
            }
        }
        // TWZ-Schnitt
        if (in_array(9, $arrayFW)) { // TWZ-Schnitt als TieBreaker gewünscht?
            for ($s = 1; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerTWZOpp[$s])) {
                    $array_PlayerTWZ[$s] = 0;
                } elseif (count($array_PlayerTWZOpp[$s]) == 1) {
                    $array_PlayerTWZ[$s] = $array_PlayerTWZOpp[$s][0];
                } else {
                    $c_TWZOpp = 0;
                    foreach ($array_PlayerTWZOpp[$s] as $TWZOpp) {
                        if ($TWZOpp > 0) {
                            $c_TWZOpp++;
                        }
                    }
                    if ($c_TWZOpp == 0) {
                        $array_PlayerTWZ[$s] = 0;
                    } else {
                        $array_PlayerTWZ[$s] = array_sum($array_PlayerTWZOpp[$s]) / $c_TWZOpp;
                    }
                }
            }
        }

        // Elo-Schnitt mit Streichresultat
        if (in_array(16, $arrayFW)) { // Elo-Schnitt als TieBreaker gewünscht?
            for ($s = 1; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerEloOpp[$s])) {
                    $array_PlayerElo1St[$s] = 0;
                } elseif (count($array_PlayerEloOpp[$s]) == 1) {
                    $array_PlayerElo1St[$s] = 0;
                } //$array_PlayerEloOpp[$s][0];
                else {
                    $c_EloOpp = 0;
                    foreach ($array_PlayerEloOpp[$s] as $EloOpp) {
                        if ($EloOpp > 0) {
                            $c_EloOpp++;
                        }
                    }
                    if ($c_EloOpp == 0) {
                        $array_PlayerElo1St[$s] = 0;
                    } elseif (min($array_PlayerEloOpp[$s]) == 0) {
                        $array_PlayerElo1St[$s] = array_sum($array_PlayerEloOpp[$s]) / $c_EloOpp;
                    } elseif ($c_EloOpp == 1) {
                        $array_PlayerElo1St[$s] = array_sum($array_PlayerEloOpp[$s]);
                    } else {
                        $array_PlayerElo1St[$s] = (array_sum($array_PlayerEloOpp[$s]) - min($array_PlayerEloOpp[$s])) / ($c_EloOpp - 1);
                    }
                }
            }
        }
        // DWZ-Schnitt mit Streichresultat
        if (in_array(18, $arrayFW)) { // DWZ-Schnitt als TieBreaker gewünscht?
            for ($s = 1; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerDWZOpp[$s])) {
                    $array_PlayerDWZ1St[$s] = 0;
                } elseif (count($array_PlayerDWZOpp[$s]) == 1) {
                    $array_PlayerDWZ1St[$s] = 0;
                } //$array_PlayerDWZOpp[$s][0];
                else {
                    $c_DWZOpp = 0;
                    foreach ($array_PlayerDWZOpp[$s] as $DWZOpp) {
                        if ($DWZOpp > 0) {
                            $c_DWZOpp++;
                        }
                    }
                    if ($c_DWZOpp == 0) {
                        $array_PlayerDWZ1St[$s] = 0;
                    } elseif (min($array_PlayerDWZOpp[$s]) == 0) {
                        $array_PlayerDWZ1St[$s] = array_sum($array_PlayerDWZOpp[$s]) / $c_DWZOpp;
                    } elseif ($c_DWZOpp == 1) {
                        $array_PlayerDWZ1St[$s] = array_sum($array_PlayerDWZOpp[$s]);
                    } else {
                        $array_PlayerDWZ1St[$s] = (array_sum($array_PlayerDWZOpp[$s]) - min($array_PlayerDWZOpp[$s])) / ($c_DWZOpp - 1);
                    }
                }
            }
        }
        // TWZ-Schnitt mit Streichresultat
        if (in_array(19, $arrayFW)) { // TWZ-Schnitt als TieBreaker gewünscht?
            for ($s = 1; $s <= $this->data->teil; $s++) { // alle Startnummern durchgehen
                if (!isset($array_PlayerTWZOpp[$s])) {
                    $array_PlayerTWZ1St[$s] = 0;
                } elseif (count($array_PlayerTWZOpp[$s]) == 1) {
                    $array_PlayerTWZ1St[$s] = 0;
                } //$array_PlayerTWZOpp[$s][0];
                else {
                    $c_TWZOpp = 0;
                    foreach ($array_PlayerTWZOpp[$s] as $TWZOpp) {
                        if ($TWZOpp > 0) {
                            $c_TWZOpp++;
                        }
                    }
                    if ($c_TWZOpp == 0) {
                        $array_PlayerTWZ1St[$s] = 0;
                    } elseif (min($array_PlayerTWZOpp[$s]) == 0) {
                        $array_PlayerTWZ1St[$s] = array_sum($array_PlayerTWZOpp[$s]) / $c_TWZOpp;
                    } elseif ($c_TWZOpp == 1) {
                        $array_PlayerTWZ1St[$s] = array_sum($array_PlayerTWZOpp[$s]);
                    } else {
                        $array_PlayerTWZ1St[$s] = (array_sum($array_PlayerTWZOpp[$s]) - min($array_PlayerTWZOpp[$s])) / ($c_TWZOpp - 1);
                    }
                }
            }
        }


        // alle Spieler durchgehen und updaten (kein vorheriges Löschen notwendig)
        //		for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
        for ($s = 1; $s <= count($player); $s++) { // alle Startnummern durchgehen
            // den TiebrSummen ihre Werte zuordnen
            if (!isset($player[$s - 1])) {
                break;
            }
            for ($tb = 1; $tb <= 3; $tb++) {
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
                        if ($array_PlayerSpiele[$s] == 0) {
                            $sumTiebr[$tb] = 0;
                        } else {
                            $sumTiebr[$tb] = ($array_PlayerPunkte[$s] * 100) / $array_PlayerSpiele[$s];
                        }
                        break;
                    case 30: // Turnierleistung
                        $sumTiebr[$tb] = $player[$s - 1]->Leistung;
                        break;
                    case 51: // ordering
                        $sumTiebr[$tb] = 1000 - $player[$s - 1]->ordering;
                        break;
                    default:
                        $sumTiebr[$tb] = 0;
                }
            }

            if ($player[$s - 1]->twz == "") {
                $player[$s - 1]->twz = "0";
            }
            $query = "UPDATE #__clm_turniere_tlnr"
                    . " SET sum_punkte = ".$array_PlayerPunkte[$s].", sum_wins = ".$array_PlayerWins[$s].", "
                    . " anz_spiele = ".$array_PlayerSpiele[$s].", "
                    . " sumTiebr1 = ".$sumTiebr[1].", sumTiebr2 = ".$sumTiebr[2].", sumTiebr3 = ".$sumTiebr[3].","
                    . " twz = '".$player[$s - 1]->twz."'"
                    . " WHERE turnier = ".$this->turnierid
                    . " AND snr = ".$s
            ;



            clm_core::$db->query($query);
            //			$this->_db->setQuery($query);
            //			$this->_db->query();
            // nur zur Verzögerung, damit UPDATE fertig ist bevor unten SELECT startet
            for ($xx = 0; $xx <= 1000; $xx++) {
            }
        }

        // direkter Vergleich
        if ($this->data->tiebr1 == 25 or $this->data->tiebr2 == 25 or $this->data->tiebr3 == 25) {
            $query = "SELECT * "
                ." FROM `#__clm_turniere_tlnr`"
                ." WHERE turnier = ".$this->turnierid
                ." AND sum_punkte > 0"					// check nur für Spieler mit Punkten > 0
                ." ORDER BY sum_punkte DESC, sumTiebr1 DESC, sumTiebr2 DESC, sumTiebr3 DESC, snr ASC"
            ;
            $this->_db->setQuery($query);
            $players = $this->_db->loadObjectList();
            // alle Spieler durchgehen
            foreach ($players as $xvalue) {
                $sum_erg = 0;
                $id_dv = 0;
                // alle Spieler durchgehen
                foreach ($players as $yvalue) {
                    if ($xvalue->snr == $yvalue->snr) {
                        continue;
                    }
                    // sind x und y wertungsgleich ?
                    if (($this->data->tiebr1 == 25 and $xvalue->sum_punkte == $yvalue->sum_punkte) or
                        ($this->data->tiebr2 == 25 and $xvalue->sum_punkte == $yvalue->sum_punkte and $xvalue->sumTiebr1 == $yvalue->sumTiebr1)	or
                        ($this->data->tiebr3 == 25 and $xvalue->sum_punkte == $yvalue->sum_punkte and $xvalue->sumTiebr1 == $yvalue->sumTiebr1 and $xvalue->sumTiebr2 == $yvalue->sumTiebr2)) {
                        $id_dv = 1;
                        // alle Matches in DatenArray schreiben
                        $query = "SELECT * FROM `#__clm_turniere_rnd_spl` as m"
                            . " WHERE turnier = ".$this->turnierid." AND ergebnis IS NOT NULL"
                            . " AND tln_nr = ".$xvalue->snr." AND gegner = ".$yvalue->snr
                        ;
                        $this->_db->setQuery($query);
                        $matchesdirect = $this->_db->loadObjectList();
                        $zdirect = count($matchesdirect);
                        foreach ($matchesdirect as $mdvalue) {
                            if ($mdvalue->ergebnis == 2 or $mdvalue->ergebnis == 10) {
                                $sum_erg += 0.5;
                            } elseif ($mdvalue->ergebnis == 1 or $mdvalue->ergebnis == 5) {
                                $sum_erg += 1;
                            }
                        }
                    }
                }
                if ($id_dv == 1) {
                    $query = "UPDATE #__clm_turniere_tlnr";
                    if ($this->data->tiebr1 == 25) {
                        $query .= " SET sumTiebr1 = ".$sum_erg;
                    } elseif ($this->data->tiebr2 == 25) {
                        $query .= " SET sumTiebr2 = ".$sum_erg;
                    } else {
                        $query .= " SET sumTiebr3 = ".$sum_erg;
                    }
                    $query .= " WHERE turnier = ".$this->turnierid
                        . " AND snr = ".$xvalue->snr
                    ;
                    clm_core::$db->query($query);
                    //					$this->_db->setQuery($query);
                    //					$this->_db->query();
                } else {
                    $query = "UPDATE #__clm_turniere_tlnr";
                    if ($this->data->tiebr1 == 25) {
                        $query .= " SET sumTiebr1 = NULL";
                    } elseif ($this->data->tiebr2 == 25) {
                        $query .= " SET sumTiebr2 = NULL";
                    } else {
                        $query .= " SET sumTiebr3 = NULL";
                    }
                    $query .= " WHERE turnier = ".$this->turnierid
                        . " AND snr = ".$xvalue->snr
                    ;
                    clm_core::$db->query($query);
                    //					$this->_db->setQuery($query);
                    //					$this->_db->query();
                }
            }
        }
    }


    public function setRankingPositions()
    {

        if ($this->turnierid == "") {
            return;
        }
        $query = "SELECT * "
            ." FROM `#__clm_turniere_tlnr`"
            ." WHERE turnier = ".$this->turnierid
            ." ORDER BY sum_punkte DESC, sumTiebr1 DESC, sumTiebr2 DESC, sumTiebr3 DESC, snr ASC"
        ;

        $this->_db->setQuery($query);
        $players = $this->_db->loadObjectList();

        $table	= JTable::getInstance('turnier_teilnehmer', 'TableCLM');
        // rankingPos umsortieren
        $rankingPos = 0;
        $rankingPosZ = 0;
        $sum_punkte = 0;
        $sumTiebr1 = 0;
        $sumTiebr2 = 0;
        $sumTiebr3 = 0;
        // alle Spieler durchgehen
        foreach ($players as $value) {
            $rankingPos++;
            $table->load($value->id);
            if ($sum_punkte == $value->sum_punkte and $sumTiebr1 == $value->sumTiebr1
                and $sumTiebr2 == $value->sumTiebr2 and $sumTiebr3 == $value->sumTiebr3) {
                $table->rankingPos = $rankingPosZ;
            } else {
                $table->rankingPos = $rankingPos;
                $sum_punkte = $value->sum_punkte;
                $sumTiebr1 = $value->sumTiebr1;
                $sumTiebr2 = $value->sumTiebr2;
                $sumTiebr3 = $value->sumTiebr3;
                $rankingPosZ = $rankingPos;
            }
            $table->store();
        }
    }


    public function makePlusTln()
    {

        if ($this->data->typ != 1) {
            JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'WRONGMODUS'));
            return false;

        } elseif ($this->checkTournamentStarted()) {
            JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'ALREADYSTARTED'));
            return false;
        }

        $query = "UPDATE #__clm_turniere"
                . " SET teil = teil + 1"
                . " WHERE id = ".$this->turnierid
        ;
        //		$this->_db->setQuery($query);
        //		if (!$this->_db->query()) {
        if (!clm_core::$db->query($query)) {
            JError::raiseNotice(500, JText::_('DB_ERROR'));
            return false;
        }

        $app = JFactory::getApplication();
        $app->enqueueMessage(JText::_('PARTICIPANT_COUNT_RAISED_TO').": ".($this->data->teil + 1));

        return true;

    }

    public function makeMinusTln()
    {

        if ($this->data->typ != 1) {
            JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'WRONGMODUS'));
            return false;

        } elseif ($this->checkTournamentStarted()) {
            JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'ALREADYSTARTED'));
            return false;
        }

        $query = "UPDATE #__clm_turniere"
                . " SET teil = teil - 1"
                . " WHERE id = ".$this->turnierid
        ;
        //		$this->_db->setQuery($query);
        //		if (!$this->_db->query()) {
        if (!clm_core::$db->query($query)) {
            JError::raiseNotice(500, JText::_('DB_ERROR'));
            return false;
        }

        $app = JFactory::getApplication();
        $app->enqueueMessage(JText::_('PARTICIPANT_COUNT_LESSENED_TO').": ".($this->data->teil - 1));

        return true;

    }
}
