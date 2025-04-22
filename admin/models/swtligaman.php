<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelSWTLigaman extends JModelLegacy
{
    public function __construct()
    {

        parent::__construct();

        // Mannschafts-Nummer fuer update
        // ...
        // $db_man_nr  = ...

    }

    public function getVereinsliste()
    {

        // Konfigurationsparameter auslesen
        $config = clm_core::$db->config();
        $vs	= $config->verein_sort;

        $sid	= clm_core::$load->request_int('sid', 0);

        $sql = " SELECT ZPS as zps, Vereinname as name FROM #__clm_dwz_vereine as a "
//				." LEFT JOIN #__clm_saison as s ON s.id= a.sid "
//				." WHERE s.archiv = 0 AND s.published = 1 ORDER BY ";
//				." WHERE sid = ".clm_core::$access->getSeason()." ORDER BY ";
                ." WHERE sid = ".$sid." ORDER BY ";

        if ($vs == "1") {
            $sql = $sql." a.ZPS ASC";
        } else {
            $sql = $sql." a.Vereinname ASC";
        }

        return $this->_getList($sql);

    }

    public function getSpielerliste()
    {

        if (!empty($this->_spielerliste)) {
            return $this->_spielerliste;
        }

        $filter_zps = clm_core::$load->request_string('filter_zps', '');
        $filter_sg_zps = clm_core::$load->request_string('filter_sg_zps', '');
        $sid	= clm_core::$load->request_int('sid', 0);

        if ($filter_zps != '') {
            $sql = " SELECT id, Spielername as name, ZPS as zps, Mgl_Nr as mgl_nr FROM #__clm_dwz_spieler"
                ." WHERE sid = ".$sid;
            //				." WHERE sid = ".clm_core::$access->getSeason();
            if ($filter_zps != '') {
                if ($filter_sg_zps != '' and $filter_sg_zps != '') {
                    $sql .= " AND ( zps = '".$filter_zps."' OR FIND_IN_SET(zps,'".$filter_sg_zps."') )";
                } else {
                    $sql .= " AND zps = '".$filter_zps."'";
                }
            }
            $this->_spielerliste = $this->_getList($sql);
        } else {
            $this->_spielerliste = array();
        }
        return $this->_spielerliste;

    }

    public function findPlayerID($spieler)
    {
        $splist = $this->getSpielerliste();

        if ($splist == array()) {
            return -1;
        }

        if (!empty($spieler['zps']) && !empty($spieler['mgl_nr'])) {
            foreach ($splist as $sp) {
                if ($sp->zps == $spieler['zps'] && $sp->mgl_nr == $spieler['mgl_nr']) {
                    return $sp->id;
                }
            }
        }

        if (empty($spieler['name'])) {
            return -1;
        }

        foreach ($splist as $sp) {
            if ($sp->name == $spieler['name']) {
                return $sp->id;
            }
        }

        // Name ist nicht leer, aber der Spieler kann nicht gefunden werden!
        return -1;
    }

    public function findMglNr($id)
    {

        $splist = $this->getSpielerliste();

        foreach ($splist as $spieler) {
            if ($spieler->id == $id) {
                return $spieler->mgl_nr;
            }
        }
        return 0;

    }

    public function findZPS($id)
    {

        $splist = $this->getSpielerliste();

        foreach ($splist as $spieler) {
            if ($spieler->id == $id) {
                return $spieler->zps;
            }
        }
        return 0;

    }

    public function store()
    {

        // DB-Zugriff
        $db = JFactory::getDBO();

        // allgemeine Formulardaten sammeln
        //if ($sg_zps == 0) $sg_zps = '';
        $swt_id	= clm_core::$load->request_int('swt_id', 0);
        $sid	= clm_core::$load->request_int('sid', 0);
        $lid	= clm_core::$load->request_int('lid', 0);
        $zps    = clm_core::$load->request_string('filter_zps', '0');
        $name    = clm_core::$load->request_string('name', '0');
        $noOrgReference = clm_core::$load->request_string('noOrgReference', '0');
        if ($noOrgReference == '1' and $name != 'spielfrei') {
            $zps = '-1';
        }
        $noBoardResults = clm_core::$load->request_string('noBoardResults', '0');
        $dwz_handling   = clm_core::$load->request_string('dwz_handling', '0');

        $swt_data		= $this->getDataSWT();
        $swt_db_data	= $this->getDataSWTdb();
        $sg_zps			= $swt_data['sg_zps'];

        // Spieler
        $anz_bretter = $swt_db_data['anz_bretter'];
        $anz_spieler = $swt_db_data['anz_spieler']; // pro Mannschaft!

        // Defaults setzen
        $_POST['lokal'] = '';
        $_POST['mf'] = null;
        $tln_nr  = clm_core::$load->request_string('tln_nr');
        if (strlen($tln_nr) == 1) {
            $man_nr = $lid.'0'.$tln_nr;
        } else {
            $man_nr = $lid.$tln_nr;
        }
        $_POST['man_nr'] = $man_nr;
        //Mit Daten aus DB-Tab clm_mannschaften überschreiben, falls eine Liga geupdated wird
        if (clm_core::$load->request_int('update') == 1 and  clm_core::$load->request_int('lid') > 0) {
            $db		= JFactory::getDBO();
            $select_query = '  SELECT * FROM #__clm_mannschaften '
                            .' WHERE liga = '.$lid.' AND tln_nr = '.$tln_nr;
            $db->setQuery($select_query);
            $teamFromDatabase = $db->loadObject();
            //Standardwerte werden überschrieben
            $_POST['lokal'] = $teamFromDatabase->lokal;
            $_POST['mf'] = $teamFromDatabase->mf;
            $_POST['man_nr'] = $teamFromDatabase->man_nr;
        }
        //Mit Daten aus DB-Tab clm_dwz_vereine überschreiben, falls eine Liga angelegt wird
        if (clm_core::$load->request_int('update') == 0 and  clm_core::$load->request_int('lid') == 0) {
            $db		= JFactory::getDBO();
            $select_query = '  SELECT * FROM #__clm_vereine '
                            ." WHERE sid = ".$sid." AND zps = '".$zps."'";
            $db->setQuery($select_query);
            $clubFromDatabase = $db->loadObject();
            //Standardwerte werden überschrieben
            if (isset($clubFromDatabase)) {
                $_POST['lokal'] = $clubFromDatabase->lokal;
            }
        }

        // Allgemeine Mannschaftsdaten
        $man_spalten = array( 'name', 'sid', 'swt_id', 'tln_nr', 'bem_int', 'published', 'lokal', 'mf', 'man_nr' );
        $_POST['bem_int'] = 'Import durch SWT-Datei.';
        $fields = '';
        $values = '';

        foreach ($man_spalten as $spalte) {
            $fields .= "`" . $spalte . "`,";
            //			$values .= " '" . clm_core::$load->request_string($spalte) . "',";
            $values .= " '" . addslashes(htmlspecialchars_decode(clm_core::$load->request_string($spalte))) . "',";
        }


        $fields .= "`zps`,`sg_zps`";
        $values .= " '" . $zps . "','" . $sg_zps . "' ";
        $sql = ' INSERT IGNORE INTO #__clm_swt_mannschaften'
                . ' ( ' . $fields . ' ) '
                . ' VALUES ( ' . $values . ' ); ';
        //$db->setQuery ($sql);

        if (clm_core::$db->query($sql)) {
            $man_id = clm_core::$db->insert_id();
        } else {
            print $db->getErrorMsg();
            return false;
        }

        //		echo "swt_anz_spieler: " . $swt_data['anz_spieler']; //DBG
        $newPlayerFields = 'sid, ZPS, Mgl_Nr, Spielername, DWZ, FIDE_Elo';
        $fields = 'spielerid, sid, swt_id, man_id, snr, mgl_nr, zps, start_dwz, FIDEelo';
        $values = '';
        $newPlayerValues = '';

        $sql = ' SELECT spielerid FROM #__clm_swt_meldeliste_spieler'
                . ' WHERE swt_id = '.$swt_id
                . ' ORDER BY spielerid DESC';
        $objs = $this->_getList($sql);
        if (isset($objs[0])) {
            $groesste_id = max($objs[0]->spielerid, $swt_data['anz_spieler']);
        } // anz_spieler gesamt!
        else {
            $groesste_id = $swt_data['anz_spieler'];
        }
        /*		echo "objs: "; //DBG
                print_r ($objs); //DBG
                echo "objs[0]: "; //DBG
                print_r ($objs[0]); //DBG
                $obj = $objs[0]; echo "spid: " . $obj->spielerid; //DBG
                echo "groesste_id: $groesste_id"; //DBG*/

        // Freie Mitgliedsnummer für den imaginären Verein finden
        $query = "SELECT MAX(Mgl_Nr) as MglNr FROM #__clm_dwz_spieler WHERE ZPS = -1";
        $MglNr = clm_core::$db->loadObjectList($query);
        $MglNr = intval($MglNr[0]->MglNr);
        $MglNr++;

        $neu = 1;
        for ($i = 1; $i <= $anz_spieler; $i++) {
            $dwzid		= clm_core::$load->request_string('dwzid_' . $i);
            $spielerid	= clm_core::$load->request_string('spielerid_' . $i);
            $name	= clm_core::$load->request_string('name_' . $i);
            if ($name == '' and $dwzid == '') {
                continue;
            }
            if ($spielerid > 0 and $dwz_handling == '1') {
                $elo = $swt_data['spieler_'.$i]['elo'];
                if ($elo < '1') {
                    $elo = '0';
                } //die();
                $dwz = $swt_data['spieler_'.$i]['dwz'];
                if ($dwz < '1') {
                    $dwz = '0';
                } //die();
            } else {
                $elo = '0';
                $dwz = '0';
            }
            if ($noOrgReference == 1) {
                $dwzid = -1;
            }
            if (empty($spielerid)) { // neuer Spieler, der nicht in der SWT-Datei aufgeführt ist
                //				echo "groesste_id: $groesste_id"; //DBG
                $spielerid = $groesste_id + $neu;
                $neu += 1;
            }

            if ($dwzid > 0 && $this->findMglNr($dwzid) > 0) {
                $values .= "( " . $spielerid . ", " . $sid . ", " . $swt_id . ", " . $man_id . ", " . $i . ", " . $this->findMglNr($dwzid) . ", '". $this->findZPS($dwzid) ."', ".$dwz.", ".$elo." ), ";
            } elseif ($dwzid == -1) {

                if ($MglNr < 10) {
                    $MglNrString = "00".$MglNr;
                } elseif ($MglNr < 100) {
                    $MglNrString = "0".$MglNr;
                } else {
                    $MglNrString = strval($MglNr);
                }
                $MglNr++;

                $newPlayerValues .= "( " . $sid . ", '-1', '".$MglNrString."', '".clm_core::$db->escape($name)."', ".$dwz.", ".$elo." ), ";

                $values .= "( " . $spielerid . ", " . $sid . ", " . $swt_id . ", " . $man_id . ", " . $i . ", '".$MglNrString."', '-1', ".$dwz.", ".$elo." ), ";
            }
        }
        $values = substr($values, 0, -2); // letztes ", " streichen
        $newPlayerValues = substr($newPlayerValues, 0, -2); // letztes ", " streichen

        if (empty($values)) { // spielfrei
            $_POST['ungerade'] = '1';
        } else {
            $sql = ' INSERT IGNORE INTO #__clm_swt_meldeliste_spieler'
                    . ' ( ' . $fields . ' ) '
                    . ' VALUES ' . $values . '; ';

            //$db->setQuery ($sql);

            if (!clm_core::$db->query($sql)) {
                print $db->getErrorMsg();
                return false;
            }
        }
        // fehlende Spieler Anlegen
        if (!empty($newPlayerValues)) {
            $sql = ' INSERT IGNORE INTO #__clm_dwz_spieler'
                . ' ( ' . $newPlayerFields . ' ) '
                . ' VALUES ' . $newPlayerValues . '; ';
            //$db->setQuery ($sql);

            if (!clm_core::$db->query($sql)) {
                print $db->getErrorMsg();
                return false;
            }
        }
        return true;

    }

    public function fixSpielerID() // für Spieler, die während des Imports entfernt wurden, Datensatz mit "alter" Spielerid kopieren
    {// DB-Zugriff
        $db = JFactory::getDBO();

        $sid		= clm_core::$load->request_int('sid');
        $swt_id		= clm_core::$load->request_int('swt_id');
        $swt_data	= $this->getDataSWT();

        $anz_spieler = $swt_data['anz_spieler']; // anz_spieler gesamt!

        $sql = ' SELECT `spielerid` FROM #__clm_swt_meldeliste_spieler'
                . ' WHERE `swt_id` = "'.$swt_id.'"'
                . ' ORDER BY `spielerid` DESC';
        $objs = $this->_getList($sql);
        $groesste_id = max($objs[0]->spielerid, $anz_spieler); // anz_spieler gesamt!

        $exists = array();
        for ($i = 1; $i <= $groesste_id; $i++) {
            $exists[] = false;
        }

        foreach ($objs as $obj) {
            $exists[$obj->spielerid] = true;
        }

        $j = 0;
        for ($i = 1; $i <= $groesste_id; $i++) {
            if (!isset($exists[$i])) {
                $j += 1;
                $spielerid_alt = $anz_spieler + $j;
                $update_query = 'UPDATE #__clm_swt_meldeliste_spieler SET spielerid=' . $i . ''
                                . ' WHERE spielerid=' . $spielerid_alt . ''
                                . ' AND swt_id=' . $swt_id . '; ';
                //$db->setQuery ($update_query);
                if (!clm_core::$db->query($update_query)) {
                    print $db->getErrorMsg();
                    return false;
                }
            }
        }

        return true;
    }


    public function getDataSWT()
    {

        if (!empty($this->_swt_data)) {
            return $this->_swt_data;
        }

        jimport('joomla.filesystem.file');

        // Namen und Verzeichnis der SWT-Datei auslesen
        $filename = clm_core::$load->request_string('swt_file', '');
        $path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'swt' . DIRECTORY_SEPARATOR;

        $swt = $path.$filename;

        // Aktuell zu bearbeitende Mannschaft
        $man = clm_core::$load->request_int('man', 0);
        $name_land = clm_core::$load->request_string('name_land', '0');

        // ...
        //$swt_data['liga_name'] = $this->_SWTReadName ($swt, 245, 60);

        $gesp_runden		= CLMSWT::readInt($swt, 3);
        $ausgeloste_runden	= CLMSWT::readInt($swt, 5);

        // schon gespeicherte SWT-Daten aus der DB holen
        $swt_db_data = $this->getDataSWTdb();

        $anz_mannschaften	= $swt_db_data['anz_mannschaften'];
        $anz_bretter		= $swt_db_data['anz_bretter'];
        $anz_durchgaenge	= $swt_db_data['anz_durchgaenge'];
        $anz_runden			= $swt_db_data['anz_runden'];
        $anz_spieler		= CLMSWT::readInt($swt, 7, 2);

        // Offset berechnen
        $offset_rundaten = $anz_spieler * $anz_durchgaenge * $anz_runden * 19;
        $offset_mandaten = $anz_mannschaften * $anz_durchgaenge * $anz_runden * 19;
        $offset_spldaten = $anz_spieler * 655;
        if ($ausgeloste_runden == 0) {
            $offset_rundaten = 0;
            $offset_mandaten = 0;
        }
        // Mannschaft auslesen
        $offset = 13384 + $offset_rundaten + $offset_mandaten + $offset_spldaten;
        $swt_data['man_name'] = '';
        for ($m = 1; $m <= $anz_mannschaften; $m++) {
            $man_nr = CLMSWT::readInt($swt, $offset + 201);
            if ($man_nr == $man + 1) { // nur aktuelle Mannschaft betrachten
                $swt_data['man_name']	= CLMSWT::readString($swt, $offset, 32);
                $swt_data['man_land']	= CLMSWT::readString($swt, $offset + 105, 3);
                if ($name_land == '1' and $swt_data['man_land'] != '') {
                    $swt_data['man_name'] .= ' ('.$swt_data['man_land'].')';
                }
                break;
            }
            $offset += 655;
        }

        // Spieler auslesen
        $offset = 13384 + $offset_rundaten + $offset_mandaten;
        $i = 1;
        $swt_data['zps'] = '';
        $swt_data['sg_zps'] = '';
        $_GET['filter_zps'] = $swt_data['zps'];
        $_GET['filter_sg_zps'] = $swt_data['sg_zps'];
        for ($s = 1; $s <= $anz_spieler; $s++) {

            $man_nr = CLMSWT::readInt($swt, $offset + 201);
            if ($man_nr == $man + 1) { // nur Spieler der aktuellen Mannschaft betrachten

                if (!isset($swt_data['zps']) or $swt_data['zps'] == '') { // einmalig die Mannschaftsdaten setzen
                    $swt_data['zps']		= CLMSWT::readString($swt, $offset + 153, 5);
                    //$swt_data['man_name']	= CLMSWT::readString ($swt, $offset + 33, 32);
                    if (!empty($swt_data['zps']) && (clm_core::$load->request_string('filter_zps', '') == '')) {
                        $_GET['filter_zps'] = $swt_data['zps'];
                    }
                }
                $zps_act		= CLMSWT::readString($swt, $offset + 153, 5);
                //if (($swt_data['zps'] != $zps_act) AND (strpos($swt_data['sg_zps'],$zps_act) === false)) {
                if ((isset($zps_act)) and ($zps_act != '') and
                    ($swt_data['zps'] != $zps_act) and (strpos($swt_data['sg_zps'], $zps_act) === false)) {
                    if ($swt_data['sg_zps'] == '') {
                        $swt_data['sg_zps'] = $zps_act;
                    } else {
                        $swt_data['sg_zps'] .= ','.$zps_act;
                    }
                    $_GET['filter_sg_zps'] = $swt_data['sg_zps'];
                    //echo "<br>sg_zps: "; var_dump($swt_data['sg_zps']);
                    $this->_spielerliste = array();
                    //}
                }

                $i	= CLMSWT::readInt($swt, $offset + 203);

                $swt_data['spieler_'.$i]['name']	= CLMSWT::readString($swt, $offset, 32);
                $swt_data['spieler_'.$i]['elo']		= CLMSWT::readString($swt, $offset + 70, 4);
                $swt_data['spieler_'.$i]['dwz']		= CLMSWT::readString($swt, $offset + 75, 4);
                $swt_data['spieler_'.$i]['zps']		= CLMSWT::readString($swt, $offset + 153, 5);
                $swt_data['spieler_'.$i]['mgl_nr']	= CLMSWT::readString($swt, $offset + 159, 4);
                $dwzid					= $this->findPlayerID($swt_data['spieler_'.$i]);
                $swt_data['spieler_'.$i]['dwzid']	= $dwzid;
                $swt_data['spieler_'.$i]['brett']	= CLMSWT::readInt($swt, $offset + 203);

                $spielerid[$swt_data['spieler_'.$i]['brett']] = $s;

                //$i += 1;

            }
            $offset += 655;

        }
        if (isset($spielerid)) {
            $_POST['spielerid'] = $spielerid;
        }

        $swt_data['anz_spieler'] = $anz_spieler;

        $this->_swt_data = $swt_data;
        return $this->_swt_data;

    }

    public function getDataSWTdb()
    {

        if (!empty($this->_swt_db_data)) {
            return $this->_swt_db_data;
        }

        $swt_id = clm_core::$load->request_int('swt_id', '');
        $sql = ' SELECT id, sid, teil as anz_mannschaften, stamm as anz_bretter, ersatz as anz_ersatzspieler, durchgang as anz_durchgaenge, runden as anz_runden, params'
                . ' FROM #__clm_swt_liga'
                . ' WHERE id = '.$swt_id;

        $objs = $this->_getList($sql);
        $obj = $objs[0];

        $spalten = array('sid', 'anz_mannschaften', 'anz_bretter', 'anz_ersatzspieler', 'anz_durchgaenge', 'anz_runden', 'params');
        foreach ($spalten as $spalte) {
            $swt_db_data[$spalte] = $obj->$spalte;
        }
        $swt_db_data['anz_spieler'] = $swt_db_data['anz_bretter'] + $swt_db_data['anz_ersatzspieler'];

        //Liga-Parameter aufbereiten
        $lid_params = array();
        if (isset($swt_db_data['params'])) {
            $paramsStringArray = explode("\n", $swt_db_data['params']);
            foreach ($paramsStringArray as $value) {
                $ipos = strpos($value, '=');
                if ($ipos !== false) {
                    $lid_params[substr($value, 0, $ipos)] = substr($value, $ipos + 1);
                }
            }
        }
        if (isset($lid_params['anz_sgp'])) {
            $swt_db_data['anz_sgp'] = $lid_params['anz_sgp'];
        }   //anz_sg Parameterübernahme
        else {
            $swt_db_data['anz_sgp'] = 1;
        }

        $this->_swt_db_data = $swt_db_data;
        return $swt_db_data;

    }

}
