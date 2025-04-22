<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelSWTTurnierErg extends JModelLegacy
{
    public $_SWTmatchesWhite;
    public $_SWTmatchesBlack;
    public $_matches;

    public $_runden;

    public $_teilnehmerNamen;
    public $_ergebnisTexte;

    public $_tid;

    public function __construct()
    {
        parent::__construct();
    }

    public function getMatches()
    {
        ///Paarungen in CLM-Format konvertieren
        if (empty($this->_SWTmatchesWhite)) {
            $this->_convertMatchesToCLM();
        }
        if (!empty($this->_SWTmatchesWhite)) {
            $this->_sortMatches();
        }
        return $this->_matches;
    }

    public function getRunden()
    {
        if (empty($this->runden)) {
            jimport('joomla.filesystem.file');

            //Import-Modus auslesen
            $update = clm_core::$load->request_string('update', '');
            $tid 	= clm_escape(clm_core::$load->request_string('tid', ''));

            //Name und Verzeichnis der SWT-Datei
            $filename 	= clm_core::$load->request_string('swt_file', '');
            $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
            $swt 		= $path.$filename;

            //Datei-Version
            $file_version			= CLMSWT::readInt($swt, 609, 2);

            //Einstellungen auslesen
            $anz_runden		 		= CLMSWT::readInt($swt, 1, 2);
            $anz_durchgaenge 		= CLMSWT::readInt($swt, 599, 1);
            if ($file_version == 724) {
                $aktuelle_runde			= $anz_runden;
            } else {
                $aktuelle_runde			= CLMSWT::readInt($swt, 3, 2);
            }
            $aktueller_durchgang	= CLMSWT::readInt($swt, 598, 1);
            $ausgeloste_runden		= CLMSWT::readInt($swt, 5, 2);
            $modus = $this->_calculateCLMModus(CLMSWT::readInt($swt, 596, 1));

            //Anzahl der in der SWT-Datei gespeicherten Runden berechnen
            //			if($aktuelle_runde != 0) { //Turnier ist bereits angefangen
            if ($modus == 2) { //Vollrundig
                $swt_runden = $anz_runden * $anz_durchgaenge;
            } else {
                //					$swt_runden = $ausgeloste_runden;
                $swt_runden = $anz_runden;
            }
            //			} else {
            //				$swt_runden = 0;
            //			}

            $rnd = 1;
            while ($rnd <= $swt_runden) {
                $runde = new JObject();

                $runde->dg = $this->_calculateDurchgang($rnd);
                $runde->runde = $this->_calculateRunde($rnd);
                $runde->nr = $rnd;
                $runde->name = JText::_('ROUND')." ".$runde->runde;
                if ($anz_durchgaenge == 2 and $runde->dg == 1) {
                    $runde->name .= " (".JText::_('TOURNAMENT_STAGE_1').")";
                } elseif ($anz_durchgaenge == 2 and $runde->dg == 2) {
                    $runde->name .= " (".JText::_('TOURNAMENT_STAGE_2').")";
                } elseif ($anz_durchgaenge > 2) {
                    $runde->name .= " (".JText::_('DG')." ".$runde->dg.")";
                }
                $runde->published = 1;

                $runde->abgeschlossen 	= 0;
                $runde->tl_ok 			= 0;
                $runde->bemerkungen 	= '';
                $runde->bem_int 		= '';
                $runde->ordering		= 0;
                $runde->datum = '';
                $runde->startzeit = '';

                if ($update == 1) {
                    $this->_setRundenDetailsByDatabase($tid, $runde);
                }
                //				if (!isset($runde->datum)) $runde->datum = '';
                if ($runde->datum == '0000-00-00' or $runde->datum == '1970-01-01') {
                    $runde->datum = '';
                }
                if ($runde->datum == '') {
                    if ($file_version == 724) {
                        $runde->datum = '';
                        $runde->startzeit = '';
                    } else {
                        $test = 'datum'.$rnd;
                        $d1 = CLMSWT::readInt($swt, 11457 + (($rnd - 1) * 4), 1);
                        $d2 = CLMSWT::readInt($swt, 11457 + (($rnd - 1) * 4) + 1, 1);
                        $hh = CLMSWT::readInt($swt, 11457 + (($rnd - 1) * 4) + 2, 1);
                        $mm = CLMSWT::readInt($swt, 11457 + (($rnd - 1) * 4) + 3, 1);
                        $lt = $d1 + ($d2 * 256);
                        if ($lt > 0) {
                            $rdate = date_create('1899-12-30');
                            $ltstring = $lt." days";
                            //date_add($rdate, date_interval_create_from_date_string($ltstring));  	// for >= php 5.3.0
                            date_modify($rdate, '+'.$lt.' days');									// for >= php 5.2.0 too
                            $runde->datum = date_format($rdate, 'Y-m-d');
                            $runde->startzeit = sprintf('%02d', $hh).':'.sprintf('%02d', $mm).':00';
                        }
                    }
                }
                //				if (!isset($runde->startzeit)) $runde->startzeit = '';

                $this->_runden[$rnd] = $runde;
                $rnd += 1;
            }
        }
        return $this->_runden;

    }

    public function getTeilnehmerNamen()
    {
        if (empty($this->_teilnehmerNamen)) {
            $db		= JFactory::getDBO();

            //Turnier-ID auslesen
            $swt_tid = clm_escape(clm_core::$load->request_string('swt_tid'));

            $select_query = " 	SELECT 
									`snr`,`name`
								FROM 
									#__clm_swt_turniere_tlnr
								WHERE 
									swt_tid = ".$swt_tid."
								ORDER BY
									snr;";
            $db->setQuery($select_query);
            $this->_teilnehmerNamen = $db->loadObjectList();
        }
        return $this->_teilnehmerNamen;
    }

    public function getErgebnisTexte()
    {
        $db		= JFactory::getDBO();

        $select_query = " 	SELECT 
								`eid`,`erg_text`
							FROM 
								#__clm_ergebnis
							ORDER BY
								eid;";
        $db->setQuery($select_query);
        $this->_ergebnisTexte = $db->loadObjectList();

        return $this->_ergebnisTexte;
    }



    //
    // Hilfsfunktionen f�r das Auslesen aus der SWT-Datei
    //



    public function _sortMatches()
    {
        foreach ($this->_matches as $rnd => $roundMatches) {
            ksort($roundMatches);
            $sortetMatches[$rnd] = $roundMatches;
        }
        ksort($sortetMatches);
        $this->_matches = $sortetMatches;
    }

    public function _convertMatchesToCLM()
    {
        //Paarungen aus SWT-Datei einlesen
        if (empty($this->_SWTmatchesWhite) or empty($this->_SWTmatchesBlack)) {
            $this->_loadSWTMatches();
        }

        //Falls Paarungsdaten vorhanden
        if (!empty($this->_SWTmatchesWhite) and !empty($this->_SWTmatchesBlack)) {

            foreach ($this->_SWTmatchesWhite as $rnd => $SWTRoundMatchesWhite) {
                foreach ($SWTRoundMatchesWhite as $brett => $SWTMatchWhite) {

                    //SWT-Paarungsdaten des Schwarzspielers
                    $SWTMatchBlack = $this->_SWTmatchesBlack[$rnd][$brett];

                    //JObject f�r die Paarung wird erstellt (Wei�spieler)
                    $match = new JObject();

                    $match->set('brett', $brett);
                    $match->set('runde', $this->_calculateRunde($rnd));
                    $match->set('dg', $this->_calculateDurchgang($rnd));
                    $match->set('spieler', $SWTMatchWhite->teil_nr);
                    $match->set('gegner', $SWTMatchBlack->teil_nr);
                    $match->set('ergebnisWhite', $this->_calculateCLMErgebnisWhite($SWTMatchWhite, $SWTMatchBlack));
                    $match->set('ergebnisBlack', $this->_calculateCLMErgebnisBlack($SWTMatchWhite, $SWTMatchBlack));

                    $this->_matches[$rnd][$brett] = $match;
                }
            }
        }
    }

    public function _loadSWTMatches()
    {
        jimport('joomla.filesystem.file');

        //Name und Verzeichnis der SWT-Datei
        $filename 	= clm_core::$load->request_string('swt_file', '');
        $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
        $swt 		= $path.$filename;

        //Datei-Version
        $file_version			= CLMSWT::readInt($swt, 609, 2);

        //Einstellungen zur Berechnung des offset auslesen
        $anz_teilnehmer 		= CLMSWT::readInt($swt, 7, 2);
        $anz_runden		 		= CLMSWT::readInt($swt, 1, 2);
        $anz_durchgaenge 		= CLMSWT::readInt($swt, 599, 1);
        if ($file_version == 724) {
            $aktuelle_runde			= $anz_runden;
        } else {
            $aktuelle_runde			= CLMSWT::readInt($swt, 3, 2);
        }
        $aktueller_durchgang	= CLMSWT::readInt($swt, 598, 1);
        $ausgeloste_runden		= CLMSWT::readInt($swt, 5, 2);
        $modus = $this->_calculateCLMModus(CLMSWT::readInt($swt, 596, 1));

        //Anzahl der in der SWT-Datei gespeicherten Runden berechnen
        if ($aktuelle_runde != 0) { //Turnier ist bereits angefangen
            if ($modus == 2) { //Vollrundig
                $swt_runden = $anz_runden * $anz_durchgaenge;
            } else {
                $swt_runden = $anz_runden;
            }
        } else {
            $swt_runden = 0;
        }


        //offset f�r Spielerpaarungen setzen
        //		$offset = 13384;
        if ($file_version == 724) {
            $offset = 3894;
        } else {
            $offset = 13384;
        }

        //Paarungen auslesen
        $sp = 1;
        $bye_brett = (int) (round($anz_teilnehmer / 2) + 1);
        $bye_count = 0;
        while ($sp <= $anz_teilnehmer) {
            $rnd = 1;
            while ($rnd <= $swt_runden) {

                if (CLMSWT::readInt($swt, $offset	+ 13, 1) > 0 or CLMSWT::readInt($swt, $offset	+ 11, 1) > 0) {//Deaktivierte Spieler abfangen
                    $match = new JObject();

                    $match->set('teil_nr', $sp);
                    $match->set('SWTheim', CLMSWT::readInt($swt, $offset	+ 8, 1));
                    $match->set('brett', CLMSWT::readInt($swt, $offset	+ 13, 1));
                    $match->set('gegner', CLMSWT::readInt($swt, $offset	+ 9, 1));

                    $match->set('SWTergebnis', CLMSWT::readInt($swt, $offset	+ 11, 1));
                    $match->set('SWTattribute', CLMSWT::readInt($swt, $offset	+ 15, 1));

                    if ($match->SWTheim == 1 or $match->SWTheim == 2) {
                        $this->_SWTmatchesWhite[$rnd][$match->brett] = $match;
                    } elseif ($match->SWTheim == 3 or $match->SWTheim == 4) {
                        $this->_SWTmatchesBlack[$rnd][$match->brett] = $match;
                    }
                    //Bye Ergebnis = (kampflos 1/2 Punkt)
                    if ($match->SWTheim == 0 and $match->SWTergebnis == 2 and $match->SWTattribute == 2) {
                        $match->set('brett', ($bye_brett + $bye_count));
                        $this->_SWTmatchesWhite[$rnd][$match->brett] = $match;
                        $match = new JObject();
                        $match->set('teil_nr', 0);
                        $match->set('SWTheim', CLMSWT::readInt($swt, $offset	+ 8, 1));
                        $match->set('brett', ($bye_brett + $bye_count));
                        $bye_count++;
                        $match->set('gegner', $sp);
                        $match->set('SWTergebnis', CLMSWT::readInt($swt, $offset	+ 11, 1));
                        $match->set('SWTattribute', CLMSWT::readInt($swt, $offset	+ 15, 1));
                        $this->_SWTmatchesBlack[$rnd][$match->brett] = $match;
                    }
                }

                //Offset und index f�r n�chsten Teilnehmer erh�hen
                $offset += 19;
                $rnd++;
            }
            $sp++;
        }
    }

    public function _calculateRunde($rnd)
    {
        jimport('joomla.filesystem.file');

        //Name und Verzeichnis der SWT-Datei
        $filename 	= clm_core::$load->request_string('swt_file', '');
        $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
        $swt 		= $path.$filename;

        //Einstellungen zur Berechnung auslesen
        $anz_runden		 		= CLMSWT::readInt($swt, 1, 2);

        while ($rnd > $anz_runden) {
            $rnd -= $anz_runden;
        }

        return $rnd;
    }

    public function _calculateDurchgang($rnd)
    {
        jimport('joomla.filesystem.file');

        //Name und Verzeichnis der SWT-Datei
        $filename 	= clm_core::$load->request_string('swt_file', '');
        $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
        $swt 		= $path.$filename;

        //Einstellungen zur Berechnung auslesen
        $anz_runden		 		= CLMSWT::readInt($swt, 1, 2);

        $dg = 1;
        while ($rnd > $anz_runden) {
            $rnd -= $anz_runden;
            $dg += 1;
        }

        return $dg;
    }

    public function _calculateCLMErgebnisWhite($SWTmatchWhite, $SWTmatchBlack)
    {
        if ($SWTmatchWhite->SWTergebnis == 241) {
            $SWTmatchWhite->SWTergebnis = 1;
        }
        if ($SWTmatchWhite->SWTergebnis == 242) {
            $SWTmatchWhite->SWTergebnis = 2;
        }
        if ($SWTmatchWhite->SWTergebnis == 243) {
            $SWTmatchWhite->SWTergebnis = 3;
        }
        if ($SWTmatchBlack->SWTergebnis == 241) {
            $SWTmatchBlack->SWTergebnis = 1;
        }
        if ($SWTmatchBlack->SWTergebnis == 242) {
            $SWTmatchBlack->SWTergebnis = 2;
        }
        if ($SWTmatchBlack->SWTergebnis == 243) {
            $SWTmatchBlack->SWTergebnis = 3;
        }
        if ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 3 and $SWTmatchBlack->SWTattribute != 2) {
            return 0; // 0-1
        } elseif ($SWTmatchWhite->SWTergebnis == 3 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute != 2) {
            return 1; // 1-0
        } elseif ($SWTmatchWhite->SWTergebnis == 2 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 2 and $SWTmatchBlack->SWTattribute != 2) {
            return 2; // 0,5-0,5
        } elseif ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute != 2) {
            return 3; // 0-0
        } elseif ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute == 2 and $SWTmatchBlack->SWTergebnis == 3 and $SWTmatchBlack->SWTattribute == 2) {
            return 4; // -/+
        } elseif ($SWTmatchWhite->SWTergebnis == 3 and $SWTmatchWhite->SWTattribute == 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute == 2) {
            return 5; // +/-
        } elseif ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute == 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute == 2) {
            return 6; // -/-
        } elseif ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 2 and $SWTmatchBlack->SWTattribute != 2) {
            return 9; // 0,5-0
        } elseif ($SWTmatchWhite->SWTergebnis == 2 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute != 2) {
            return 10; // 0-0,5
        } elseif ($SWTmatchWhite->SWTergebnis == 2 and $SWTmatchWhite->SWTattribute == 2 and $SWTmatchBlack->SWTergebnis == 2 and $SWTmatchBlack->SWTattribute == 2) {
            return 12; // 0,5---
        } else {
            return 7; // noch nicht gespielt
        }
    }

    public function _calculateCLMErgebnisBlack($SWTmatchWhite, $SWTmatchBlack)
    {
        if ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 3 and $SWTmatchBlack->SWTattribute != 2) {
            return 1; // 1-0
        } elseif ($SWTmatchWhite->SWTergebnis == 3 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute != 2) {
            return 0; // 0-1
        } elseif ($SWTmatchWhite->SWTergebnis == 2 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 2 and $SWTmatchBlack->SWTattribute != 2) {
            return 2; // 0,5-0,5
        } elseif ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute != 2) {
            return 3; // 0-0
        } elseif ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute == 2 and $SWTmatchBlack->SWTergebnis == 3 and $SWTmatchBlack->SWTattribute == 2) {
            return 5; // +/-
        } elseif ($SWTmatchWhite->SWTergebnis == 3 and $SWTmatchWhite->SWTattribute == 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute == 2) {
            return 4; // -/+
        } elseif ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute == 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute == 2) {
            return 6; // -/-
        } elseif ($SWTmatchWhite->SWTergebnis == 1 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 2 and $SWTmatchBlack->SWTattribute != 2) {
            return 10; // 0-0,5
        } elseif ($SWTmatchWhite->SWTergebnis == 2 and $SWTmatchWhite->SWTattribute != 2 and $SWTmatchBlack->SWTergebnis == 1 and $SWTmatchBlack->SWTattribute != 2) {
            return 9; // 0,5-0
        } elseif ($SWTmatchWhite->SWTergebnis == 2 and $SWTmatchWhite->SWTattribute == 2 and $SWTmatchBlack->SWTergebnis == 2 and $SWTmatchBlack->SWTattribute == 2) {
            return 12; // 0,5---
        } else {
            return 7; // noch nicht gespielt
        }
    }

    public function _calculateCLMModus($modus = 0)
    {
        if ($modus == 0) {
            //CH-System
            return 1;
        } elseif ($modus == 1) {
            //Vollrundig
            return 2;
        } elseif ($modus == 2) {
            //KO-System (1. Rnd CH-System)
            return 3;
        } elseif ($modus == 3) {
            //KO-System (1. Rnd kein CH-System)
            return 3;
        } else {
            return 0;
        }
    }

    public function _setRundenDetailsByDatabase($tid, $runde)
    {
        $db		= JFactory::getDBO();

        $select_query = " 	SELECT 
								*
							FROM
								#__clm_turniere_rnd_termine
							WHERE 
								`turnier` = ".$tid." AND 
								`nr` = ".$runde->nr.";";

        $db->setQuery($select_query);
        if ($rundeDatabase = $db->loadObject()) {
            $runde->datum 			= $rundeDatabase->datum;
            $runde->startzeit 		= $rundeDatabase->startzeit;
            $runde->abgeschlossen 	= $rundeDatabase->abgeschlossen;
            $runde->tl_ok 			= $rundeDatabase->tl_ok;
            $runde->published 		= $rundeDatabase->published;
            $runde->bemerkungen 	= $rundeDatabase->bemerkungen;
            $runde->bem_int 		= $rundeDatabase->bem_int;
            $runde->gemeldet		= $rundeDatabase->gemeldet;
            $runde->editor			= $rundeDatabase->editor;
            $runde->zeit			= $rundeDatabase->zeit;
            $runde->edit_zeit		= $rundeDatabase->edit_zeit;
            $runde->ordering		= $rundeDatabase->ordering;
        }
    }


    //
    // Funktion zum Speichern in der Datenbank
    //


    public function store()
    {
        $rcount = clm_core::$load->request_int('rcount', 0);
        $rlast  = clm_core::$load->request_int('rlast', 0);

        // Rundeninformationen in Datenbank schreiben
        if (!$this->_storeRundenInfos()) {
            return false;
        }

        // Paarungen in Datenbank schreiben
        if (!$this->_storePaarungen()) {
            return false;
        }
        if ($rlast < $rcount) {
            return true;
        }

        //Spielfreidummys l�schen
        if (!$this->_deleteSpielfreiDummys()) {
            return false;
        }

        //Kopieren in CLM-Tabellen
        if (!$this->_copyToCLMTables()) {
            return false;
        }

        $turnier = new CLMTournament($this->_tid, true);

        //Punkte und Feinwertungen neu berechnen
        $turnier->calculateRanking();

        //Rangliste neu berechnen
        $turnier->setRankingPositions();

        //inoff. DWZ-Berechnung
        clm_core::$api->direct("db_tournament_genDWZ", array($turnier->turnierid,false));

        //Import war erfolgreich
        return true;
    }


    //
    // Hilfsfunktionen zum Speichern in der Datenbank
    //


    public function _storeRundenInfos()
    {
        $db		= JFactory::getDBO();
        $this->getRunden();
        $rfirst = clm_core::$load->request_int('rfirst', 0);
        $rlast  = clm_core::$load->request_int('rlast', 0);

        if (!empty($this->_runden)) {
            $insert_query = " 	INSERT IGNORE INTO 
									#__clm_swt_turniere_rnd_termine" . " 
									( `sid`, `name`, `turnier`, `swt_tid`, `dg`, `nr`, `datum`, `startzeit`, `abgeschlossen`, `tl_ok`, `published`, `bemerkungen`, `bem_int`) "
                          . " 	VALUES";
            foreach ($this->_runden as $rnd => $runde) {
                $i = $runde->nr;
                if ($i >= $rfirst and $i <= $rlast) {
                    $insert_query .= 	" ( 
										".CLMSWT::getFormValue('sid', null, 'int').", 
										'".CLMSWT::getFormValue('name', '', 'string', $rnd)."', 										
										".CLMSWT::getFormValue('tid', null, 'int').", 
										".CLMSWT::getFormValue('swt_tid', null, 'int').", 
										".CLMSWT::getFormValue('dg', null, 'int', $rnd).", 
										".CLMSWT::getFormValue('runde', null, 'int', $rnd).",
										'".CLMSWT::getFormValue('datum', '1970-01-01', 'string', $rnd)."', 
										'".CLMSWT::getFormValue('startzeit', '00:00:00', 'string', $rnd)."', 
										".CLMSWT::getFormValue('abgeschlossen', 0, 'int', $rnd).", 
										".CLMSWT::getFormValue('tl_ok', 0, 'int', $rnd).", 
										".CLMSWT::getFormValue('published', 0, 'int', $rnd).", 
										'".CLMSWT::getFormValue('bemerkungen', '', 'string', $rnd)."', 
										'".CLMSWT::getFormValue('bem_int', '', 'string', $rnd)."' 
									),";
                }
            }
            $insert_query = substr($insert_query, 0, -1);
            $insert_query .= ";";

            //$db->setQuery($insert_query);

            if (clm_core::$db->query($insert_query)) {
                //Daten wurden erfolgreich in die Datenbank geschrieben
                return true;
            } else {
                if ($db->getErrorNum() == 1062) {
                    //Seite wurde aktualisiert (F5) und Daten stehen schon in der Datenbank
                    JFactory::getApplication()->enqueueMessage(JText::_('SWT_STORE_WARNING_ROUNDS_ALLREADY_EXISTS'), 'notice');
                    return true;
                } else {
                    //Ein Fehler ist aufgetreten
                    JFactory::getApplication()->enqueueMessage(JText::_('SWT_STORE_ERROR_ROUNDS'), 'error');
                    return false;
                }
            }
        } else {
            //Keine Rundendaten zum speichern da
            return true;
        }
    }

    public function _storePaarungen()
    {
        $db		= JFactory::getDBO();
        $this->getRunden();
        $rfirst = clm_core::$load->request_int('rfirst', 0);
        $rlast  = clm_core::$load->request_int('rlast', 0);

        //Name und Verzeichnis der SWT-Datei
        $filename 	= clm_core::$load->request_string('swt_file', '');
        $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
        $swt 		= $path.$filename;
        //Anzahl der Teilnehmer aus der Turnierdatei
        $anz_teilnehmer 		= CLMSWT::readInt($swt, 7, 2);

        if (!empty($this->_runden)) {
            $ispl = 0;
            $insert_query = "INSERT IGNORE INTO 
									#__clm_swt_turniere_rnd_spl" . " 
									( `sid`, `turnier`, `swt_tid`, `runde`, `brett`, `dg`, `tln_nr`, `heim`, `spieler`, `gegner`, `ergebnis`) "
                          . " 	VALUES";

            foreach ($this->_runden as $rnd => $runde) {
                $i = $runde->nr;
                if ($i >= $rfirst and $i <= $rlast) {
                    if (!isset($_POST["brett"])) {
                        $bretter = array();
                    } else {
                        $bretter = CLMSWT::getFormValue('brett', array(), 'array', $rnd);
                    }
                    if (!is_null($bretter) and count($bretter) > 0) {
                        foreach ($bretter as $brett) {
                            if (CLMSWT::getFormValue('ergebnisWhite', null, 'int', array( $rnd, $brett)) == 7) {
                                $ergWhite = "NULL";
                            } else {
                                $ergWhite = CLMSWT::getFormValue('ergebnisWhite', null, 'int', array( $rnd, $brett));
                            }
                            if (CLMSWT::getFormValue('ergebnisBlack', null, 'int', array( $rnd, $brett)) == 7) {
                                $ergBlack = "NULL";
                            } else {
                                $ergBlack = CLMSWT::getFormValue('ergebnisBlack', null, 'int', array( $rnd, $brett));
                            }

                            //Paarungsdaten f�r Weiß
                            $insert_query .= 	" ( 
											".CLMSWT::getFormValue('sid', null, 'int').", 										
											".CLMSWT::getFormValue('tid', null, 'int').", 
											".CLMSWT::getFormValue('swt_tid', null, 'int').", 
											".CLMSWT::getFormValue('runde', null, 'int', $rnd).", 
											".CLMSWT::getFormValue('brett', null, 'int', array( $rnd, $brett)).",
											".CLMSWT::getFormValue('dg', null, 'int', $rnd).", 
											".CLMSWT::getFormValue('spieler', null, 'int', array( $rnd, $brett)).", 
											1, 
											".CLMSWT::getFormValue('spieler', null, 'int', array( $rnd, $brett)).",  
											".CLMSWT::getFormValue('gegner', null, 'int', array( $rnd, $brett)).",  
											".$ergWhite."
										),";
                            //Paarungsdaten f�r Schwarz
                            $insert_query .= 	" ( 
											".CLMSWT::getFormValue('sid', null, 'int').", 										
											".CLMSWT::getFormValue('tid', null, 'int').", 
											".CLMSWT::getFormValue('swt_tid', null, 'int').", 
											".CLMSWT::getFormValue('runde', null, 'int', $rnd).",
											".CLMSWT::getFormValue('brett', null, 'int', array( $rnd, $brett)).",
											".CLMSWT::getFormValue('dg', null, 'int', $rnd).",
											".CLMSWT::getFormValue('gegner', null, 'int', array( $rnd, $brett)).", 
											0, 
											".CLMSWT::getFormValue('gegner', null, 'int', array( $rnd, $brett)).",  
											".CLMSWT::getFormValue('spieler', null, 'int', array( $rnd, $brett)).",  
											".$ergBlack."
										),";
                            $ispl++;
                        }
                    } else {
                        //				  for ($ii = 1; $ii <= 13; $ii++) {
                        for ($ii = 1; $ii <= intdiv(($anz_teilnehmer + 1), 2); $ii++) {
                            //Paarungsdaten f�r Wei�
                            $insert_query .= 	" ( 
											".CLMSWT::getFormValue('sid', null, 'int').", 										
											".CLMSWT::getFormValue('tid', null, 'int').", 
											".CLMSWT::getFormValue('swt_tid', null, 'int').", 
											".CLMSWT::getFormValue('runde', null, 'int', $rnd).", 
											".$ii.",
											".CLMSWT::getFormValue('dg', null, 'int', $rnd).", 
											NULL, 
											1, 
											NULL,  
											NULL,  
											NULL
										),";
                            //Paarungsdaten f�r Schwarz
                            $insert_query .= 	" ( 
											".CLMSWT::getFormValue('sid', null, 'int').", 										
											".CLMSWT::getFormValue('tid', null, 'int').", 
											".CLMSWT::getFormValue('swt_tid', null, 'int').", 
											".CLMSWT::getFormValue('runde', null, 'int', $rnd).",
											".$ii.",
											".CLMSWT::getFormValue('dg', null, 'int', $rnd).",
											NULL, 
											0, 
											NULL,  
											NULL,  
											NULL
										),";
                            $ispl++;
                        }
                    }
                }
            }
            $insert_query = substr($insert_query, 0, -1);
            $insert_query .= ";";

            if ($ispl == 0) {
                return true;
            }
            if (clm_core::$db->query($insert_query)) {
                //Daten wurden erfolgreich in die Datenbank geschrieben
                return true;
            } else {
                if ($db->getErrorNum() == 1062) {
                    //Seite wurde aktualisiert (F5) und Daten stehen schon in der Datenbank
                    JFactory::getApplication()->enqueueMessage(JText::_('SWT_STORE_WARNING_MATCHES_ALLREADY_EXISTS'), 'notice');
                    return true;
                } else {
                    //Ein Fehler ist aufgetreten
                    JFactory::getApplication()->enqueueMessage(JText::_('SWT_STORE_ERROR_MATCHES'), 'error');
                    return false;
                }
            }
        } else {
            //Keine Paarungsdaten zum speichern da
            return true;
        }
    }

    public function _deleteSpielfreiDummys()
    {
        $swt_tid	= clm_escape(clm_core::$load->request_string('swt_tid'));
        $db		= JFactory::getDBO();

        //Anzahl der Spielfrei-Dummys feststellen

        $select_query = " 	SELECT 
								snr
							FROM 
									#__clm_swt_turniere_tlnr
							WHERE 
								swt_tid = ".$swt_tid." AND
								name = 'spielfrei' ;";
        $db->setQuery($select_query);
        $dummys = $db->loadObjectList();
        if (empty($dummys)) {
            $anz_dummys = 0;
        } else {
            $anz_dummys = count($dummys);
        }

        //Teilnehmeranzahl anpassen

        $select_query = " 	SELECT 
								*
							FROM 
								#__clm_swt_turniere
							WHERE 
								swt_tid = ".$swt_tid.";";
        $db->setQuery($select_query);
        $turnier = $db->loadObject();
        $turnier->teil = $turnier->teil - $anz_dummys;

        if (!$db->updateObject('#__clm_swt_turniere', $turnier, 'swt_tid')) {
            return false;
        }

        //Spielfrei-Dummys l�schen

        $delete_query = " 	DELETE FROM 
									#__clm_swt_turniere_tlnr
							WHERE 
								swt_tid = ".$swt_tid." AND
								name = 'spielfrei' ;";
        //$db->setQuery($delete_query);
        if (clm_core::$db->query($delete_query)) {
            return true;
        } else {
            //Ein Fehler ist aufgetreten
            JFactory::getApplication()->enqueueMessage(JText::_('SWT_STORE_ERROR_COULD_NOT_DELETE_DUMMYS'), 'error');
            return false;
        }
    }

    public function _copyToCLMTables()
    {
        $swt_tid	= clm_escape(clm_core::$load->request_string('swt_tid'));
        $update 	= clm_core::$load->request_int('update');
        $tid		= clm_core::$load->request_int('tid');


        // Turnier kopieren
        // Nur kopieren, wenn das Turnier noch nicht kopiert wurde (d.h. die tid in der #__swt_turniere noch nicht geupdated wurde bzw. == 0 ist)
        //		if($this->_getTid($swt_tid) == 0) {
        if (!$this->_copyTurnier($swt_tid, $update, $tid)) {
            JFactory::getApplication()->enqueueMessage(JText::_('SWT_STORE_ERROR_COPY_TOURNAMENT'), 'error');
            return false;
        }
        //		}

        // Nachdem das Turnier kopiert wurde existiert auf jeden Fall eine Turnier-ID != 0
        // Diese soll nun f�r die weiteren Aufgaben benutzt werden
        $tid = $this->_getTid($swt_tid);
        $_POST["tid"] = $tid;
        // Teilnehmer kopieren
        if (!$this->_copyTeilnehmer($swt_tid, $update, $tid)) {
            JFactory::getApplication()->enqueueMessage(JText::_('SWT_STORE_ERROR_COPY_PLAYERS'), 'error');
            return false;
        }

        // RundenInfos kopieren
        if (!$this->_copyRundenInfos($swt_tid, $update, $tid)) {
            JFactory::getApplication()->enqueueMessage(JText::_('SWT_STORE_ERROR_COPY_ROUNDS'), 'error');
            return false;
        }

        // Paarungen kopieren
        if (!$this->_copyPaarungen($swt_tid, $update, $tid)) {
            JFactory::getApplication()->enqueueMessage(JText::_('SWT_STORE_ERROR_COPY_MATCHES'), 'error');
            return false;
        }
        return true;
    }

    public function _copyTurnier($swt_tid, $update, $tid)
    {
        $db		= JFactory::getDBO();

        $select_query = "	SELECT *
							FROM #__clm_swt_turniere
							WHERE swt_tid = ".$swt_tid.";";
        $db->setQuery($select_query);
        $turnier = $db->loadObject();
        unset($turnier->tid);
        unset($turnier->swt_tid);

        if ($update == 1 and $tid != 0) {
            //$turnier->id = $tid;
            $select_query = "	SELECT *
								FROM #__clm_turniere
								WHERE id = ".$tid.";";
            $db->setQuery($select_query);
            $turnier_orig = $db->loadObject();
            if ($turnier_orig->teil != $turnier->teil or $turnier_orig->rnd != $turnier->rnd or
                $turnier_orig->dateStart != $turnier->dateStart or $turnier_orig->dateEnd != $turnier->dateEnd or
                $turnier_orig->name != $turnier->name or $turnier_orig->tiebr1 != $turnier->tiebr1 or
                $turnier_orig->tiebr2 != $turnier->tiebr2 or $turnier_orig->tiebr3 != $turnier->tiebr3) {
                $turnier_orig->teil = $turnier->teil;
                $turnier_orig->rnd  = $turnier->rnd;
                $turnier_orig->dateStart = $turnier->dateStart;
                $turnier_orig->dateEnd  = $turnier->dateEnd;
                $turnier_orig->name = $turnier->name;
                $turnier_orig->tiebr1  = $turnier->tiebr1;
                $turnier_orig->tiebr2  = $turnier->tiebr2;
                $turnier_orig->tiebr3  = $turnier->tiebr3;
                if ($db->updateObject('#__clm_turniere', $turnier_orig, 'id')) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            $turnier->dateRegistration = '1970-01-01';
            if ($db->insertObject('#__clm_turniere', $turnier, 'id')) {
                //Turnier-ID in #__clm_swt_turniere updaten, damit die neue turnier-id �ber die swt-id gefunden werden kann
                //für den Fall, dass mit (F5) die Daten erneut gesendet werden und das Turnier bereits in die CLM-Datenbank kopiert wurde
                $turnier->swt_tid = $swt_tid;
                $turnier->tid = $db->insertid();
                unset($turnier->id);
                unset($turnier->dateRegistration);

                if ($db->updateObject('#__clm_swt_turniere', $turnier, 'swt_tid')) {
                    return true;
                }
            } else {
                return false;
            }
        }


    }

    public function _copyTeilnehmer($swt_tid, $update, $tid)
    {
        $db		= JFactory::getDBO();

        $delete_query = "	DELETE FROM
								#__clm_turniere_tlnr
							WHERE
								turnier = ".$tid.";";
        //$db->setQuery($delete_query);
        if (!clm_core::$db->query($delete_query)) {
            return false;
        }

        $select_query = "	SELECT 
								*
							FROM
								#__clm_swt_turniere_tlnr
							WHERE
								swt_tid = ".$swt_tid.";";
        $db->setQuery($select_query);
        //$teilnehmer = $db->loadObjectList('','JObject');		ab Joomla 1.6
        $teilnehmer = $db->loadObjectList();
        foreach ($teilnehmer as $teil) {
            unset($teil->id);
            unset($teil->swt_tid);
            //$teil->set('turnier',$tid);						ab Joomla 1.6
            $teil->turnier = $tid;

            if (!$db->insertObject('#__clm_turniere_tlnr', $teil, 'id')) {
                return false;
            }
        }
        return true;
    }

    public function _copyRundenInfos($swt_tid, $update, $tid)
    {
        $db		= JFactory::getDBO();

        $delete_query = "	DELETE FROM
								#__clm_turniere_rnd_termine
							WHERE
								turnier = ".$tid.";";
        //$db->setQuery($delete_query);
        if (!clm_core::$db->query($delete_query)) {
            return false;
        }


        $select_query = "	SELECT 
								*
							FROM
								#__clm_swt_turniere_rnd_termine
							WHERE
								swt_tid = ".$swt_tid.";";
        $db->setQuery($select_query);
        //$teilnehmer = $db->loadObjectList('','JObject');		ab Joomla 1.6
        $runden = $db->loadObjectList();
        foreach ($runden as $runde) {
            unset($runde->id);
            unset($runde->swt_tid);
            //$runde->set('turnier',$tid);						ab Joomla 1.6
            $runde->turnier = $tid;

            if (!$db->insertObject('#__clm_turniere_rnd_termine', $runde, 'id')) {
                return false;
            }
        }
        return true;
    }

    public function _copyPaarungen($swt_tid, $update, $tid)
    {
        $db		= JFactory::getDBO();

        $select_query = " SELECT * FROM #__clm_turniere_rnd_spl "
                        ." WHERE turnier = ".$tid
                        ." AND pgn != '' ;";
        $db->setQuery($select_query);
        $pgn_daten = $db->loadObjectList();

        $pgn_array = array();
        foreach ($pgn_daten as $pgn_dat) {
            $pgn_key = ($pgn_dat->spieler * 10000) + ($pgn_dat->gegner * 10) + $pgn_dat->heim;
            $pgn_array[$pgn_key] = new stdClass();
            $pgn_array[$pgn_key]->spieler = $pgn_dat->spieler;
            $pgn_array[$pgn_key]->gegner = $pgn_dat->gegner;
            $pgn_array[$pgn_key]->heim = $pgn_dat->heim;
            $pgn_array[$pgn_key]->pgn = $pgn_dat->pgn;
        }

        $delete_query = "	DELETE FROM
								#__clm_turniere_rnd_spl
							WHERE
								turnier = ".$tid.";";
        //$db->setQuery($delete_query);
        if (!clm_core::$db->query($delete_query)) {
            return false;
        }


        $select_query = "	SELECT 
								*
							FROM
								#__clm_swt_turniere_rnd_spl
							WHERE
								swt_tid = ".$swt_tid.";";
        $db->setQuery($select_query);
        //$teilnehmer = $db->loadObjectList('','JObject');		ab Joomla 1.6
        $paarungen = $db->loadObjectList();
        if (!is_null($paarungen)) {
            foreach ($paarungen as $paarung) {
                unset($paarung->id);
                unset($paarung->swt_tid);
                //$paarung->set('turnier',$tid);					ab Joomla 1.6
                $paarung->turnier = $tid;

                $pgn_key = ($paarung->spieler * 10000) + ($paarung->gegner * 10) + $paarung->heim;
                if (isset($pgn_array[$pgn_key])) {
                    $paarung->pgn = $pgn_array[$pgn_key]->pgn;
                }

                if (!$db->insertObject('#__clm_turniere_rnd_spl',$paarung,'id')) {
                    return false;
                }
            }
        }
        return true;
    }

    public function _getTid($swt_tid)
    {
        $db		= JFactory::getDBO();

        $select_query = "	SELECT 
								tid
							FROM
								#__clm_swt_turniere
							WHERE
								swt_tid = ".$swt_tid.";";
        $db->setQuery($select_query);
        $this->_tid = $db->loadObject()->tid;
        return $this->_tid;
    }


}
