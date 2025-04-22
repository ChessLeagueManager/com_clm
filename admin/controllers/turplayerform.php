<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class CLMControllerTurPlayerForm extends JControllerLegacy
{
    // Konstruktor
    public function __construct($config = array())
    {

        parent::__construct($config);

        $this->app = JFactory::getApplication();

        // Register Extra tasks
        $this->registerTask('apply', 'save');

    }


    // checkt, ob Turnier noch Teilnehmerplätze frei hat
    public function _checkTournamentOpen($playersIn, $teil)
    {

        if ($playersIn >= $teil) {
            $this->app->enqueueMessage(CLMText::errorText('PLAYERLIST', 'FULL'), 'warning');
            return false;
        }

        return true;

    }

    public function save()
    {

        $result = $this->_saveDo();

        // turnierid
        $turnierid = clm_core::$load->request_int('id');
        $task		= clm_core::$load->request_string('task');

        // abschließend offene Restteilnehmerzahl

        $adminLink = new AdminLink();
        $adminLink->more = array('id' => $turnierid);
        if ($task == 'apply' or $result === false) {
            $adminLink->view = "turplayerform";
        } else {
            $adminLink->view = "turplayers";
        }
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }


    public function _saveDo()
    {

        defined('_JEXEC') or die('Invalid Token');
        //CLM parameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;

        // turnierid
        $turnierid = clm_core::$load->request_int('id');

        $db		= JFactory::getDBO();

        // Instanz der Tabelle
        $row = JTable::getInstance('turniere', 'TableCLM');
        $row->load($turnierid); // Daten zu dieser ID laden

        $clmAccess = clm_core::$access;
        if (($row->tl != clm_core::$access->getJid() and $clmAccess->access('BE_tournament_edit_detail') !== true) or $clmAccess->access('BE_tournament_edit_detail') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return false;
        }

        $task		= clm_core::$load->request_string('task');
        $cid 		= clm_core::$load->request_array_string('cid');

        // weiteren Daten aus TlnTabelle
        $query = "SELECT MAX(mgl_nr), MAX(snr) FROM `#__clm_turniere_tlnr`"
            ." WHERE turnier = ".$turnierid
        ;
        $db->setQuery($query);
        list($maxFzps, $maxSnr) = $db->loadRow();
        $maxFzps++; // fiktive ZPS für manuell eingegeben Spieler
        $maxSnr++; // maximale snr für alle Spieler


        // Turnierdaten
        $tournament = new CLMTournament($turnierid, true);
        $playersIn = $tournament->getPlayersIn();
        $turParams = new clm_class_params($tournament->data->params);
        $param_useastwz = $turParams->get('useAsTWZ', 0);

        // Turnier schon vorher voll?
        if (!$this->_checkTournamentOpen($playersIn, $tournament->data->teil)) {
            return false;
        }

        $name	= trim(clm_core::$load->request_string('name'));
        // Spieler aus manueller Nachmeldung speichern
        if ($name != "") {

            // weitere Angaben holen
            $verein = clm_core::$load->request_string('verein');
            // $dwz = clm_core::$load->request_int('dwz');
            $natrating = clm_core::$load->request_int('natrating');
            $fideelo = clm_core::$load->request_int('fideelo');
            $titel = clm_core::$load->request_string('titel');
            $geschlecht = clm_core::$load->request_string('geschlecht', 'NULL');
            $birthYear = clm_core::$load->request_string('birthYear', '0000');
            if (is_null($birthYear) or !is_numeric($birthYear)) {
                $birthYear = '0000';
            }

            $twz = clm_core::$load->gen_twz($param_useastwz, $natrating, $fideelo);
            if (is_null($twz) or $twz == '') {
                $twz = 0;
            }
            if (is_null($natrating) or $natrating == '') {
                $natrating = 0;
            }
            if (is_null($fideelo) or $fideelo == '') {
                $fideelo = 0;
            }
            if (is_null($birthYear) or !is_numeric($birthYear)) {
                $birthYear = '0000';
            }

            $mgl_nr = clm_core::$load->request_int('mgl_nr');
            $zps = clm_core::$load->request_string('zps');
            if (strlen($zps) != 5) {
                $zps = '99999';
                $mgl_nr = $maxFzps;
            }

            $query = " INSERT INTO #__clm_turniere_tlnr"
                ." (`sid`, `turnier`, `snr`, `name`, `birthYear`, `geschlecht`, `verein`, `twz`, `start_dwz`, `FIDEelo`, `titel`, `mgl_nr` ,`zps`)"
                ." VALUES"
//				." ('".$tournament->data->sid."', '".$turnierid."', '".$maxSnr++."', '$name', '$birthYear', '$geschlecht', '$verein', '$twz', '$natrating', '$fideelo', '$titel', '".$maxFzps."', '99999')";
                ." ('".$tournament->data->sid."', '".$turnierid."', '".$maxSnr++."', '$name', '$birthYear', '$geschlecht', '$verein', '$twz', '$natrating', '$fideelo', '$titel', '$mgl_nr', '".$zps."')";
            //		$this->_db->setQuery($query);
            //		if ($this->_db->query()) {
            if (clm_core::$db->query($query)) {
                $this->app->enqueueMessage(JText::_('PLAYER')." ".$name." ".JText::_('ADDED'));
                $playersIn++; // den angemeldeten Spielern zufügen
                return true;
            } else {
                $this->app->enqueueMessage(JText::_('DB_ERROR'));
                return false;
            }

        }
        // wenn hier ein Spieler eingetragen wurde, geht es nicht mehr durch die Liste...


        foreach ($cid as $id) {

            // noch Platz im Turnier?
            if ($this->_checkTournamentOpen($playersIn, $tournament->data->teil)) {

                // ausgelesene Daten
                if ($countryversion == "de") {
                    $PKZ = '';
                    $mgl	= substr($id, 5);
                    if (!is_numeric($mgl)) {
                        $mgl = -1;
                    }
                    $zps	= substr($id, 0, 5);
                } else {  // engl. Anwendung
                    $mgl = 0;
                    $PKZ	= substr($id, 4);
                    //if(!is_numeric($PKZ)) { $PKZ = -1; }
                    $zps	= substr($id, 0, 4);
                }
                // weitere Daten des Spielers ermitteln
                // in CLM DB suchen
                $query = "SELECT a.Spielername, a.Geburtsjahr, a.Geschlecht, a.FIDE_Titel, a.FIDE_Elo, a.FIDE_ID, FIDE_Land, a.DWZ, v.Vereinname, a.PKZ"
                        . " FROM `#__clm_dwz_spieler` as a"
                        . " LEFT JOIN #__clm_dwz_vereine as v ON v.ZPS = a.ZPS AND v.sid = a.sid "
                        . " LEFT JOIN #__clm_saison as s ON s.id = a.sid "
                        . " WHERE a.ZPS = '$zps'"
                        . " AND s.archiv = 0 "
                        . " AND a.sid = ".clm_core::$access->getSeason();
                if ($countryversion == "de") {
                    $query .= " AND a.Mgl_Nr = ".$mgl;
                } else {
                    $query .= " AND a.PKZ = '".$PKZ."'";
                }

                $db->setQuery($query);
                $data	= $db->loadObject();
                if (isset($data->Spielername)) {
                    if ($PKZ == '') {
                        $PKZ = $data->PKZ;
                    }
                    // checken ob Spieler schon eingetragen, um Doppelungen zu vermeiden
                    $query = "SELECT COUNT(*) FROM #__clm_turniere_tlnr"
                            . " WHERE `turnier` = '".$turnierid."' AND `zps` = '$zps'";
                    if ($countryversion == "de") {
                        $query .= " AND mgl_nr = ".$mgl;
                    } else {
                        $query .= " AND PKZ = '".$PKZ."'";
                    }
                    $db->setQuery($query);
                    if ($db->loadResult() > 0) {
                        $this->app->enqueueMessage(JText::_('PLAYER')." ".$data->Spielername." ".JText::_('ALREADYIN'), 'warning');
                    } else {

                        $twz = clm_core::$load->gen_twz($param_useastwz, $data->DWZ, $data->FIDE_Elo);
                        if (is_null($twz) or $twz == '') {
                            $twz = 0;
                        }
                        if (is_null($data->DWZ) or $data->DWZ == '') {
                            $data->DWZ = 0;
                        }
                        if (is_null($data->FIDE_Elo) or $data->FIDE_Elo == '') {
                            $data->FIDE_Elo = 0;
                        }
                        if (is_null($data->FIDE_ID) or $data->FIDE_ID == '') {
                            $data->FIDE_ID = 0;
                        }

                        $query = " INSERT INTO #__clm_turniere_tlnr"
                                . " (`sid`, `turnier`, `snr`, `name`, `birthYear`, `geschlecht`, `verein`, `twz`, `start_dwz`, `FIDEelo`, `FIDEid`, `FIDEcco`, `titel`,`mgl_nr` ,`PKZ` ,`zps`) "
                                . " VALUES"
                                . " ('".$tournament->data->sid."','".$turnierid."', '".$maxSnr++."', '".clm_escape($data->Spielername)."', '".$data->Geburtsjahr."', '".$data->Geschlecht."','".clm_escape($data->Vereinname)."', '".$twz."', '".$data->DWZ."', '".$data->FIDE_Elo."', '".$data->FIDE_ID."', '".$data->FIDE_Land."', '".$data->FIDE_Titel."', '$mgl', '$PKZ', '$zps')";
                        //						$this->_db->setQuery($query);
                        //						if ($this->_db->query()) {
                        if (clm_core::$db->query($query)) {
                            $playersIn++;
                            $this->app->enqueueMessage(JText::_('PLAYER')." ".$data->Spielername." ".JText::_('ADDED'));
                        } else {
                            $this->app->enqueueMessage(JText::_('DB_ERROR'), 'warning');
                        }
                    }

                } else {
                    $this->app->enqueueMessage(CLMText::errorText('PLAYER', 'UNKNOWN'), 'warning');
                }

            } // sonst war Turnier voll

        }


        // je nach Task: Message und Weiterleitung
        switch ($task) {
            case 'apply':
                $stringAktion = JText::_('PLAYERS_ADDED');
                break;
            case 'save':
            default:
                $stringAktion = JText::_('PLAYERS_SAVED');
                break;
        }

        // Plätze frei?
        $openSpots = ($tournament->data->teil - $playersIn);
        if ($openSpots > 0) {
            $this->app->enqueueMessage(JText::_('PARTICIPANTS_WANTED').": ".$openSpots, 'notice');
        } else {
            $this->app->enqueueMessage(CLMText::errorText('PARTICIPANTLIST', 'FULL'), 'notice');
            $_POST['task'] = 'save';
        }


        // Message
        // $this->app->enqueueMessage($stringAktion);

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $stringAktion;
        $clmLog->params = array('sid' => $tournament->data->sid, 'tid' => $turnierid); // TurnierID wird als LigaID gespeichert
        $clmLog->write();

        return true;

    }

    public function cancel()
    {

        // turnierid
        $turnierid = clm_core::$load->request_int('id');

        $add_nz = clm_core::$load->request_int('add_nz');
        if ($add_nz == '1') {
            $tournament = new CLMTournament($turnierid, true);
            $tournament->makeMinusTln(); // Message werden dort erstellt
        }

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $turnierid);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }

}
