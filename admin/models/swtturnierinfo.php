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

class CLMModelSWTTurnierInfo extends JModelLegacy
{
    public $_turnier;

    public function __construct()
    {
        parent::__construct();
    }

    public function getTurnier()
    {

        /*		function check_date($date,$format,$sep) {

                    $pos1    = strpos($format, 'd');
                    $pos2    = strpos($format, 'm');
                    $pos3    = strpos($format, 'Y');

                    $check    = explode($sep,$date);

                    return checkdate($check[$pos2],$check[$pos1],$check[$pos3]);

                }
        */
        function check_date($date, $format, $sep)
        {

            $pos1    = strpos($format, 'd');  		// 0
            $pos2    = strpos($format, 'm');		// 1
            $pos3    = strpos($format, 'Y'); 		// 2

            $check    = explode($sep, $date);
            if (count($check) != 3) {
                return false;
            }
            if (!is_numeric($check[0]) or !is_numeric($check[1]) or !is_numeric($check[2])) {
                return false;
            }
            $check[$pos1] = str_pad($check[$pos1], 2, "0", STR_PAD_LEFT);
            $check[$pos2] = str_pad($check[$pos2], 2, "0", STR_PAD_LEFT);
            $check[$pos3] = str_pad($check[$pos3], 4, "20", STR_PAD_LEFT);

            if (!checkdate($check[$pos2], $check[$pos1], $check[$pos3])) {
                return false;
            }
            return $check[$pos3]."-".$check[$pos2]."-".$check[$pos1];

        }

        if (empty($this->_turnier)) {

            //Name und Verzeichnis der SWT-Datei
            $filename 	= clm_core::$load->request_string('swt_file', '');
            $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
            $swt 		= $path.$filename;

            //JObject wird erzeugt und mit Turnierdaten erweitert
            $this->_turnier = new JObject();

            //Datei-Version
            $file_version			= CLMSWT::readInt($swt, 609, 2);

            //Turnierdaten, die nicht aus der SWT-Datei importiert werden

            //Standartwerte
            $this->_turnier->set('tid', 0);
            //$this->_turnier->set('sid'				, $this->_getAktuelleSaison());
            $this->_turnier->set('sid', clm_core::$access->getSeason());
            $this->_turnier->set('dateStart', null);
            $this->_turnier->set('dateEnd', null);
            $this->_turnier->set('catidAlltime', 0);
            $this->_turnier->set('catidEdition', 0);
            $this->_turnier->set('tl', null);
            $this->_turnier->set('bezirk', null);
            $this->_turnier->set('bezirkTur', 1);
            $this->_turnier->set('vereinZPS', 0);
            $this->_turnier->set('published', 1);
            $this->_turnier->set('bem_int', 'SWT-Importfile:'.$filename.'; Swiss-Chess-Version:'.$file_version.';');
            $this->_turnier->set('bemerkungen', '');
            $this->_turnier->set('started', 0);
            $this->_turnier->set('finished', 0);
            $this->_turnier->set('invitationText', '');
            $this->_turnier->set('ordering', 0);
            $this->_turnier->set('params', '');
            $this->_turnier->set('sieg', '1');
            $this->_turnier->set('siegs', '1');
            $this->_turnier->set('remis', '0.5');
            $this->_turnier->set('remiss', '0.5');
            $this->_turnier->set('nieder', '0');
            $this->_turnier->set('niederk', '0');

            //Name
            if (($name = CLMSWT::readName($swt, 245, 60)) != '') {
                $this->_turnier->set('name', $name);
            } else {
                $this->_turnier->set('name', $filename);
            }

            //Mit Daten aus Datenbank �berschreiben, falls ein Turnier geupdated wird
            if (clm_core::$load->request_int('update') == 1) {
                $turnierFromDatabase = $this->_getTurnierFromDatabase();
            }


            //
            // Daten aus der SWT-Datei
            //

            //Modus
            $this->_turnier->set('modus', $this->_calculateCLMModus(CLMSWT::readInt($swt, 596, 1)));


            //rnd
            /*			if(CLMSWT::readInt($swt,3,2) == 0) {
                            $this->_turnier->set('rnd'			, 0);
                        } else {
                            $this->_turnier->set('rnd'			, 1);
                        }
            */
            $this->_turnier->set('rnd', 1);		// immer!


            //Anz. Runden und Anz. Durchg�nge
            $this->_turnier->set('runden', CLMSWT::readInt($swt, 1, 2));
            $this->_turnier->set('dg', CLMSWT::readInt($swt, 599, 1));

            //Anz. der Teilnehmer
            //$this->_turnier->set('teil'			, CLMSWT::readInt($swt,7,2));
            $this->_turnier->set('teil', $this->_getAnzTeilnehmer());

            //Turnierdatum
            if (($tdatum = CLMSWT::readName($swt, 1055, 20)) != '') {
                //				$hdatum = substr($tdatum,6,4).'-'.substr($tdatum,3,2).'-'.substr($tdatum,0,2);
                //				if(check_date($hdatum,"Ymd","-"))
                //					$this->_turnier->set('dateStart', $hdatum);
                if (check_date($tdatum, "dmY", ".") != false) {
                    $this->_turnier->set('dateStart', check_date($tdatum, "dmY", "."));
                } else {
                    $this->_turnier->set('dateStart', "");
                }
            }
            if (($tdatum = CLMSWT::readName($swt, 1076, 20)) != '') {
                //				$hdatum = substr($tdatum,6,4).'-'.substr($tdatum,3,2).'-'.substr($tdatum,0,2);
                //				if(check_date($hdatum,"Ymd","-"))
                //					$this->_turnier->set('dateEnd', $hdatum);
                if (check_date($tdatum, "dmY", ".") != false) {
                    $this->_turnier->set('dateEnd', check_date($tdatum, "dmY", "."));
                } else {
                    $this->_turnier->set('dateEnd', "");
                }
            }

            //Feinwertung
            $anzStreichwertungen = CLMSWT::readInt($swt, 9, 1);
            if ($this->_turnier->modus == 2) { //Vollrundig
                $anzStreichwertungen = 0;
            }
            if ($this->_turnier->modus == 1) {
                $feinwertung1 = CLMSWT::readInt($swt, 621, 1);
                $feinwertung2 = CLMSWT::readInt($swt, 572, 1);
            } elseif ($this->_turnier->modus == 2) {
                $feinwertung1 = CLMSWT::readInt($swt, 622, 1);
                $feinwertung2 = CLMSWT::readInt($swt, 573, 1);
            } else {
                $this->_turnier->set('tiebr1', 0);
                $this->_turnier->set('tiebr2', 0);
                $this->_turnier->set('tiebr3', 0);
            }

            if ($this->_turnier->modus == 1 or $this->_turnier->modus == 2) {
                if ($feinwertung1 == 3 and $anzStreichwertungen != 1) {
                    //Buchholz
                    $this->_turnier->set('tiebr1', 1);
                } elseif ($feinwertung1 == 3 and $anzStreichwertungen == 1) {
                    //Buchholz mit 1 Streichergebnis
                    $this->_turnier->set('tiebr1', 11);
                } elseif ($feinwertung1 == 7 and $anzStreichwertungen != 1) {
                    //Buchholzsumme
                    $this->_turnier->set('tiebr1', 2);
                } elseif ($feinwertung1 == 7 and $anzStreichwertungen == 1) {
                    //Buchholzsumme mit 1 Streichergebnis
                    $this->_turnier->set('tiebr1', 12);
                } elseif ($feinwertung1 == 4) {
                    //Summenwertung
                    $this->_turnier->set('tiebr1', 7);
                } elseif ($feinwertung1 == 5 and $anzStreichwertungen != 1) {
                    //Sonneborn-Berger
                    $this->_turnier->set('tiebr1', 3);
                } elseif ($feinwertung1 == 5 and $anzStreichwertungen == 1) {
                    //Sonneborn-Berger mit 1 Streichergebnis
                    $this->_turnier->set('tiebr1', 13);
                } elseif ($feinwertung1 == 6 and $anzStreichwertungen == 0) {
                    //mittlere Buchholz
                    $this->_turnier->set('tiebr1', 5);
                } elseif ($feinwertung1 == 8) {
                    //Elo-Schnitt
                    $this->_turnier->set('tiebr1', 6);
                } elseif ($feinwertung1 == 9) {
                    //Turnierleistung
                    $this->_turnier->set('tiebr1', 30);
                } elseif ($feinwertung1 == 14) {
                    //Anz. der Siege
                    $this->_turnier->set('tiebr1', 4);
                } elseif ($feinwertung1 == 15) {
                    //Direkter Vergleich
                    $this->_turnier->set('tiebr1', 25);
                } else {
                    $this->_turnier->set('tiebr1', 0);
                }

                if ($feinwertung2 == 3 and $anzStreichwertungen != 1) {
                    //Buchholz
                    $this->_turnier->set('tiebr2', 1);
                } elseif ($feinwertung2 == 3 and $anzStreichwertungen == 1) {
                    //Buchholz mit 1 Streichergebnis
                    $this->_turnier->set('tiebr2', 11);
                } elseif ($feinwertung2 == 7 and $anzStreichwertungen != 1) {
                    //Buchholzsumme
                    $this->_turnier->set('tiebr2', 2);
                } elseif ($feinwertung2 == 7 and $anzStreichwertungen == 1) {
                    //Buchholzsumme mit 1 Streichergebnis
                    $this->_turnier->set('tiebr2', 12);
                } elseif ($feinwertung2 == 4) {
                    //Summenwertung
                    $this->_turnier->set('tiebr2', 7);
                } elseif ($feinwertung2 == 5 and $anzStreichwertungen != 1) {
                    //Sonneborn-Berger
                    $this->_turnier->set('tiebr2', 3);
                } elseif ($feinwertung2 == 5 and $anzStreichwertungen == 1) {
                    //Sonneborn-Berger mit 1 Streichergebnis
                    $this->_turnier->set('tiebr2', 13);
                } elseif ($feinwertung2 == 6 and $anzStreichwertungen == 0) {
                    //mittlere Buchholz
                    $this->_turnier->set('tiebr2', 5);
                } elseif ($feinwertung2 == 8) {
                    //Elo-Schnitt
                    $this->_turnier->set('tiebr2', 6);
                } elseif ($feinwertung2 == 9) {
                    //Turnierleistung
                    $this->_turnier->set('tiebr2', 30);
                } elseif ($feinwertung2 == 14) {
                    //Anz. der Siege
                    $this->_turnier->set('tiebr2', 4);
                } elseif ($feinwertung2 == 15) {
                    //Direkter Vergleich
                    $this->_turnier->set('tiebr2', 25);
                } else {
                    $this->_turnier->set('tiebr2', 0);
                }

                if (CLMSWT::readBool($swt, 593) and $this->_turnier->modus != 2) {
                    $this->_turnier->set('tiebr3', 4);
                } else {
                    $this->_turnier->set('tiebr3', 0);
                }
            }

            //Ranglistenkorrektur FIDE
            $this->_turnier->set('optionTiebreakersFideCorrect', CLMSWT::readBool($swt, 675));
            //50%-Regel FIDE
            if (CLMSWT::readBool($swt, 605) == 1) {
                $this->_turnier->set('option50PercentRule', 0);
            } else {
                $this->_turnier->set('option50PercentRule', 1);
            }

            //Partiewertung
            $pwertung = CLMSWT::readInt($swt, 1336, 1);
            if ($pwertung > 3) {
                $pwertung = 0;
            }
            if ($pwertung == 0) {
                $this->_turnier->set('sieg', 1);
                $this->_turnier->set('siegs', 1);
                $this->_turnier->set('remis', .5);
                $this->_turnier->set('remiss', .5);
                $this->_turnier->set('nieder', 0);
                $this->_turnier->set('niederk', 0);
            } elseif ($pwertung == 1) {
                $this->_turnier->set('sieg', 3);
                $this->_turnier->set('siegs', 3);
                $this->_turnier->set('remis', 1);
                $this->_turnier->set('remiss', 1);
                $this->_turnier->set('nieder', 0);
                $this->_turnier->set('niederk', 0);
            } elseif ($pwertung == 2) {
                $this->_turnier->set('sieg', 3);
                $this->_turnier->set('siegs', 3);
                $this->_turnier->set('remis', 1);
                $this->_turnier->set('remiss', 1.5);
                $this->_turnier->set('nieder', 0);
                $this->_turnier->set('niederk', 0);
            } else {
                $this->_turnier->set('sieg', CLMSWT::readInt($swt, 5494, 1));
                $this->_turnier->set('siegs', CLMSWT::readInt($swt, 5496, 1));
                $this->_turnier->set('remis', CLMSWT::readInt($swt, 5498, 1));
                $this->_turnier->set('remiss', CLMSWT::readInt($swt, 5500, 1));
                $this->_turnier->set('nieder', CLMSWT::readInt($swt, 5502, 1));
                $this->_turnier->set('niederk', CLMSWT::readInt($swt, 5506, 1));
            }

            //TWZ-Einstellung
            $twz = CLMSWT::readInt($swt, 582, 1);
            if ($twz == 0) {
                $this->_turnier->set('useAsTWZ', 2);
            } elseif ($twz == 1) {
                $this->_turnier->set('useAsTWZ', 1);
            } elseif ($twz == 2) {
                $this->_turnier->set('useAsTWZ', 0);
            } elseif ($twz == 3) {
                $this->_turnier->set('useAsTWZ', 3);
            } elseif ($twz == 4) {
                $this->_turnier->set('useAsTWZ', 4);
            } else {
                $this->_turnier->set('useAsTWZ', 0);
            }

        }
        return $this->_turnier;
    }

    public function store()
    {
        $db		= JFactory::getDBO();

        //Namen aller Formularfelder als Array
        $spalten = array( "tid", "name", "sid", "dateStart", "dateEnd", "catidAlltime", "catidEdition",
                           "typ", "tiebr1", "tiebr2", "tiebr3",
                           "rnd", "teil", "runden", "dg", "tl", "bezirk", "bezirkTur", "vereinZPS",
                           "published", "started", "finished", "invitationText", "bemerkungen", "bem_int",
                           "ordering", "sieg", "siegs", "remis", "remiss", "nieder", "niederk" );

        //Strings für Felder und Werte erstellen
        $fields = '';
        $values = '';
        foreach ($spalten as $spalte) {
            $fields .= "`".$spalte."`,";
            $values .= " '".clm_escape(clm_core::$load->request_string($spalte, ''))."',";
        }

        // Parameter
        $paramsStringArray = array();
        foreach (clm_core::$load->request_array_string('params', '') as $key => $value) {
            $paramsStringArray[] = $key.'='.intval($value);
        }
        $fields .= " `params`";
        $values .= " '".implode("\n", $paramsStringArray)." '";


        //		$insert_query = "INSERT IGNORE INTO
        $insert_query = "REPLACE INTO  
								#__clm_swt_turniere" . " 
								( " . $fields . " ) "
                      . " 	VALUES 
								( " . $values . " ); ";

        //$db->setQuery($insert_query);

        if (clm_core::$db->query($insert_query)) {
            $_GET['swt_tid'] = clm_core::$db->insert_id();
            return true;
        } else {
            print $db->getErrorMsg();
            return false;
        }

    }

    public function _getTurnierFromDatabase()
    {
        if ($id = clm_core::$load->request_int('turnier')) {

            $db		= JFactory::getDBO();
            $select_query = ' 	SELECT 
							* 
						FROM 
							#__clm_turniere
						WHERE 
							id = '.$id.'; ';

            $db->setQuery($select_query);

            $turnierFromDatabase = $db->loadObject();

            //Stadartwerte werden �berschrieben
            $this->_turnier->set('name', $turnierFromDatabase->name);
            $this->_turnier->set('sid', $turnierFromDatabase->sid);
            $this->_turnier->set('dateStart', $turnierFromDatabase->dateStart);
            $this->_turnier->set('dateEnd', $turnierFromDatabase->dateEnd);
            $this->_turnier->set('catidAlltime', $turnierFromDatabase->catidAlltime);
            $this->_turnier->set('catidEdition', $turnierFromDatabase->catidEdition);
            $this->_turnier->set('tl', $turnierFromDatabase->tl);
            $this->_turnier->set('bezirk', $turnierFromDatabase->bezirk);
            $this->_turnier->set('bezirkTur', $turnierFromDatabase->bezirkTur);
            $this->_turnier->set('vereinZPS', $turnierFromDatabase->vereinZPS);
            $this->_turnier->set('published', $turnierFromDatabase->published);
            $this->_turnier->set('bem_int', $turnierFromDatabase->bem_int);
            $this->_turnier->set('bemerkungen', $turnierFromDatabase->bemerkungen);
            $this->_turnier->set('started', $turnierFromDatabase->started);
            $this->_turnier->set('finished', $turnierFromDatabase->finished);
            $this->_turnier->set('invitationText', $turnierFromDatabase->invitationText);
            $this->_turnier->set('ordering', $turnierFromDatabase->ordering);
            $this->_turnier->set('params', $turnierFromDatabase->params);
            $this->_turnier->set('sieg', $turnierFromDatabase->sieg);
            $this->_turnier->set('siegs', $turnierFromDatabase->siegs);
            $this->_turnier->set('remis', $turnierFromDatabase->remis);
            $this->_turnier->set('remiss', $turnierFromDatabase->remiss);
            $this->_turnier->set('nieder', $turnierFromDatabase->nieder);
            $this->_turnier->set('niederk', $turnierFromDatabase->niederk);
        }
    }

    public function _getAktuelleSaison()
    {
        if (empty($this->_aktuelleSaison)) {

            $query =  ' SELECT 
							id,
							name,
							published
						FROM 
							#__clm_saison 
						WHERE
							published = 1';
            $var = $this->_getList($query);
        }
        return $var[0]->id;
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

    public function _getAnzTeilnehmer()
    {
        jimport('joomla.filesystem.file');

        //Name und Verzeichnis der SWT-Datei
        $filename 	= clm_core::$load->request_string('swt_file', '');
        $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
        $swt 		= $path.$filename;

        //Datei-Version
        $file_version			= CLMSWT::readInt($swt, 609, 2);

        //Array f�r JObjects erzeugen;
        $this->_teilnehmer = array();

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

        //offset f�r Teilnehmerdaten berechnen
        if ($aktuelle_runde != 0) { //Turnier ist bereits angefangen
            if ($modus == 2) { //Vollrundig
                $offset = 13384 + $anz_teilnehmer * $anz_runden * $anz_durchgaenge * 19;
            } else {
                $offset = 13384 + $anz_teilnehmer * $anz_runden * 19;
            }
        } else { //Turnier ist noch nicht angefangen
            $offset = 13384;
        }

        //Spielerdaten werden aus SWT-Datei gelesen und in einem Array von JObjects gespeichert
        $i = 1;
        $anz_teilnehmer_clm = 0;
        while ($i <= $anz_teilnehmer) {
            if (CLMSWT::readInt($swt, $offset + 189, 1) == 102) {
                //techn. Teilnehmer -spielfrei- in SWT
            } else {
                $anz_teilnehmer_clm++;
            }
            //Offset und index f�r n�chsten Teilnehmer erh�hen
            $offset += 655;
            $i++;
        }

        return $anz_teilnehmer_clm;
    }


}
