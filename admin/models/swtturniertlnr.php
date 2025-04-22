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

class CLMModelSWTTurnierTlnr extends JModelLegacy
{
    public $_teilnehmer;

    public function __construct()
    {
        parent::__construct();
    }

    public function getTeilnehmer()
    {
        jimport('joomla.filesystem.file');

        //Name und Verzeichnis der SWT-Datei
        $filename 	= clm_core::$load->request_string('swt_file', '');
        $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
        $swt 		= $path.$filename;

        //Array f�r JObjects erzeugen;
        $this->_teilnehmer = array();

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

        //offset f�r Teilnehmerdaten berechnen
        if ($file_version == 724) {
            $offset_eerg = 3894;
        } else {
            $offset_eerg = 13384;
        }
        if ($aktuelle_runde != 0) { //Turnier ist bereits angefangen
            if ($modus == 2) { //Vollrundig
                $offset = $offset_eerg + $anz_teilnehmer * $anz_runden * $anz_durchgaenge * 19;
            } else {
                $offset = $offset_eerg + $anz_teilnehmer * $anz_runden * 19;
            }
        } else { //Turnier ist noch nicht angefangen
            $offset = $offset_eerg;
        }

        //TWZ-Bestimmen Parameter lesen
        //		$useAsTWZ = CLMSWT::getFormValue('params',0,'int','useAsTWZ');
        $useAsTWZ = clm_core::$load->request_string('useAsTWZ', '0');

        //Spielerdaten werden aus SWT-Datei gelesen und in einem Array von JObjects gespeichert
        $i = 1;
        while ($i <= $anz_teilnehmer) {
            $teilnehmer = new JObject();


            if (CLMSWT::readInt($swt, $offset + 189, 1) == 102) {
                $teilnehmer->set('name', 'spielfrei');

            } else {

                $teilnehmer->set('name', CLMSWT::readName($swt, $offset, 32));
                $teilnehmer->set('verein', CLMSWT::readName($swt, $offset + 33, 32));
                $teilnehmer->set('title', CLMSWT::readName($swt, $offset + 66, 3));
                $teilnehmer->set('FIDEelo', CLMSWT::readName($swt, $offset + 70, 4));
                $teilnehmer->set('start_dwz', CLMSWT::readName($swt, $offset + 75, 4));
                $teilnehmer->set('FIDEcco', CLMSWT::readName($swt, $offset + 105, 3));
                $teilnehmer->set('NATcco', CLMSWT::readName($swt, $offset + 109, 3));
                $teilnehmer->set('birthYear', CLMSWT::readName($swt, $offset + 128, 4));
                $teilnehmer->set('zps', CLMSWT::readName($swt, $offset + 153, 5));
                $teilnehmer->set('mgl_nr', CLMSWT::readName($swt, $offset + 159, 4));
                $teilnehmer->set('geschlecht', CLMSWT::readName($swt, $offset + 184, 1));
                if ($teilnehmer->geschlecht == 'F' or $teilnehmer->geschlecht == 'f' or $teilnehmer->geschlecht == 'w') {
                    $teilnehmer->set('geschlecht', 'W');
                }
                if ($teilnehmer->geschlecht == 'm') {
                    $teilnehmer->set('geschlecht', 'M');
                }
                $teilnehmer->set('tlnrStatus', (CLMSWT::readName($swt, $offset + 184, 1) == "*" ? "0" : "1"));
                if ($modus == 3 or $modus == 5) {
                    $teilnehmer->set('tlnrStatus', 1);
                }
                if ($file_version == 724) {
                    $teilnehmer->set('FIDEid', 0);
                } else {
                    $teilnehmer->set('FIDEid', CLMSWT::readName($swt, $offset + 324, 12));
                }

                $s_points = CLMSWT::readInt($swt, $offset + 273, 1);
                $s_sign = CLMSWT::readInt($swt, $offset + 274, 1);
                if ($s_sign == 255) {
                    $s_points = ($s_points - 256);
                }
                $s_punkte = strval($s_points / 2);
                $teilnehmer->set('s_punkte', $s_punkte);

                //TWZ-Bestimmen
                $z_twz = clm_core::$load->gen_twz($useAsTWZ, $teilnehmer->start_dwz, $teilnehmer->FIDEelo);
                $teilnehmer->set('twz', $z_twz);
                /*				if($useAsTWZ == 0) {
                                    if ($teilnehmer->FIDEelo >= $teilnehmer->start_dwz) { $teilnehmer->set('twz'	, $teilnehmer->FIDEelo); }
                                    else { $teilnehmer->set('twz'	, $teilnehmer->start_dwz); }
                                } elseif ($useAsTWZ ==1) {
                                    if ($teilnehmer->start_dwz > 0) { $teilnehmer->set('twz'	, $teilnehmer->start_dwz); }
                                    else { $teilnehmer->set('twz'	, $teilnehmer->FIDEelo); }
                                } elseif ($useAsTWZ ==2) {
                                    if ($teilnehmer->FIDEelo > 0) { $teilnehmer->set('twz'	, $teilnehmer->FIDEelo); }
                                    else { $teilnehmer->set('twz'	, $teilnehmer->start_dwz); }
                                }
                */
                // Geschlecht korrigieren
                // Keine Angabe = Männlich
                if ($teilnehmer->geschlecht == " ") {
                    $teilnehmer->set('geschlecht', "M", 1);
                }
            }

            $this->_teilnehmer[$i] = $teilnehmer;

            //Offset und index f�r n�chsten Teilnehmer erh�hen
            if ($file_version == 724) {
                $offset += 292;
            } else {
                $offset += 655;
            }
            $i++;
        }

        return $this->_teilnehmer;
    }

    public function store()
    {
        $db		= JFactory::getDBO();

        //Name und Verzeichnis der SWT-Datei
        $filename 	= clm_core::$load->request_string('swt_file', '');
        $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
        $swt 		= $path.$filename;

        //Teilnehmerzahl auslesen
        $anz_teilnehmer 		= CLMSWT::readInt($swt, 7, 2);

        if ($anz_teilnehmer > 0) {
            $insert_query = "INSERT IGNORE INTO 
									#__clm_swt_turniere_tlnr" . " 
									( `sid`, `turnier`, `swt_tid`, `snr`, `name`, `birthYear`, `geschlecht`, `tlnrStatus`, `verein`, `twz`, `start_dwz`, `FIDEelo`, `titel`, `FIDEcco`, `FIDEid`, `mgl_nr`, `zps`, `status`, `s_punkte`) "
                          . " 	VALUES";

            print clm_core::$load->request_string('snr[1]');
            $pfirst = clm_core::$load->request_int('pfirst', 0);
            $plast  = clm_core::$load->request_int('plast', 0);
            $i = 1;
            $name = clm_core::$load->request_array_string('name');
            while ($i <= $anz_teilnehmer) {

                if ($i >= $pfirst and $i <= $plast) {
                    if (isset($name[$i])) {
                        $zpscode = CLMSWT::getFormValue('zps', '', 'string', $i);
                        if ($zpscode == '' or $zpscode == 0) {
                            $zpscode = CLMSWT::getFormValue('zps_z', '', 'string', $i);
                        }

                        $insert_query .= 	" ( 
										".CLMSWT::getFormValue('sid', null, 'int').", 
										".CLMSWT::getFormValue('tid', null, 'int').", 
										".CLMSWT::getFormValue('swt_tid', null, 'int').", 
										".CLMSWT::getFormValue('snr', null, 'int', $i).", 
										'".addslashes(htmlspecialchars_decode(CLMSWT::getFormValue('name', '', 'string', $i)))."',
										".CLMSWT::getFormValue('birthYear', 0, 'int', $i).", 
										'".CLMSWT::getFormValue('geschlecht', '', 'string', $i)."', 
										".CLMSWT::getFormValue('tlnrStatus', 0, 'int', $i).",
										'".addslashes(htmlspecialchars_decode(CLMSWT::getFormValue('verein', '', 'string', $i)))."',
										".CLMSWT::getFormValue('twz', 0, 'int', $i).", 
										".CLMSWT::getFormValue('start_dwz', 0, 'int', $i).", 
										".CLMSWT::getFormValue('FIDEelo', 0, 'int', $i).", 
										'".CLMSWT::getFormValue('title', '', 'string', $i)."', 
										'".CLMSWT::getFormValue('FIDEcco', '', 'string', $i)."',
										".CLMSWT::getFormValue('FIDEid', 0, 'int', $i).", 
										".CLMSWT::getFormValue('mgl_nr', 0, 'int', $i).", 
										'".$zpscode."', 
										"."0".", 
										".CLMSWT::getFormValue('s_punkte', '', 'string', $i)." 
									),";
                    }
                }
                $i++;
            }
            $insert_query = substr($insert_query, 0, -1);
            $insert_query .= ";";
            //print $insert_query;

            //$db->setQuery($insert_query);

            if (clm_core::$db->query($insert_query)) {
                //Daten wurden erfolgreich in die Datenbank geschrieben
                return true;
            } else {
                if ($db->getErrorNum() == 1062) {
                    //Seite wurde aktualisiert (F5) und Daten stehen schon in der Datenbank
                    return true;
                } else {
                    //Ein Fehler ist aufgetreten
                    return false;
                    print $db->getErrorMsg();
                }
            }
        } else {
            //Keine Spieler vorhanden
            return true;
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

}
