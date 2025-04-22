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

class CLMControllerTurRegistrationEdit extends JControllerLegacy
{
    // Konstruktor
    public function __construct($config = array())
    {

        parent::__construct($config);

        $this->app 	= JFactory::getApplication();

        // Register Extra tasks
        $this->registerTask('apply', 'save');
        $this->registerTask('copy_to', 'save');

    }



    public function save()
    {

        $this->_saveDo();

        // turnierid
        $registrationid = clm_core::$load->request_int('registrationid');
        $turnierid = clm_core::$load->request_int('turnierid');
        // Task
        $task = clm_core::$load->request_string('task');

        $adminLink = new AdminLink();
        // wenn 'apply', weiterleiten in form
        if ($task == 'apply' or $task == 'copy_to') {
            // Weiterleitung bleibt im Formular
            $adminLink->more = array('registrationid' => $registrationid);
            $adminLink->view = "turregistrationedit";
        } else {
            // Weiterleitung in Liste
            $adminLink->more = array('id' => $turnierid);
            $adminLink->view = "turregistrations"; // WL in Liste
        }
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }


    public function _saveDo()
    {

        defined('_JEXEC') or die('Invalid Token');

        // turnierid
        $registrationid = clm_core::$load->request_int('registrationid');
        $turnierid = clm_core::$load->request_int('turnierid');
        $snrmax = clm_core::$load->request_int('snrmax');

        // Instanz der Tabelle
        $rowt = JTable::getInstance('turniere', 'TableCLM');
        $rowt->load($turnierid); // Daten zu dieser Turnier-ID laden

        $clmAccess = clm_core::$access;
        if (($rowt->tl != clm_core::$access->getJid() and $clmAccess->access('BE_tournament_edit_detail') !== true) or $clmAccess->access('BE_tournament_edit_detail') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return false;
        }

        // Task
        $task = clm_core::$load->request_string('task');

        // Instanz der Tabelle
        $row = JTable::getInstance('registrations', 'TableCLM');
        $row->load($registrationid); // Daten zu dieser ID laden

        if ($task == 'copy_to') {
            // Turnierdaten
            $tournament = new CLMTournament($rowt->id, true);
            $playersIn = $tournament->getPlayersIn();
            $text = '';
            if ($playersIn >= $rowt->teil) {
                $text = CLMText::errorText('PLAYERLIST', 'FULL');
            }
            if ($row->status == 2) {
                $text = JText::_('REGISTRATION_ALREADY_MOVED');
            }
            if ($text != '') {
                $this->app->enqueueMessage($text);
                // Weiterleitung zurück in Liste
                return false;
            }
        }
        // registration existent?
        if (!$row->id) {
            $this->app->enqueueMessage(CLMText::errorText('REGISTRATION', 'NOTEXISTING'), 'warning');
            return false;
        }
        $post = $_POST;
        if (!$row->bind($post)) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return false;
        }

        $registrationname = $row->name;
        $pos = strpos($registrationname, ',');
        $row->name = substr($registrationname, 0, $pos);
        $row->vorname = substr($registrationname, ($pos + 1));
        if ($row->dwz_I0 == '') {
            $row->dwz_I0 = 0;
        }
        if ($row->dwz == '') {
            $row->dwz = null;
        }
        if ($row->elo == '') {
            $row->elo = null;
        }
        if ($row->FIDEid == '') {
            $row->FIDEid = null;
        }
        if ($row->mgl_nr == '') {
            $row->mgl_nr = 0;
        }

        if ($task == 'apply' or $task == 'save') {
            $row->status  = 1;
        }
        if ($task == 'copy_to') {
            $row->status  = 2;
        }
        if (!$row->check($post)) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return false;
        }
        if (!$row->store()) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return false;
        }

        if ($task == 'copy_to') {
            $turParams = new clm_class_params($rowt->params);
            $param_useastwz = $turParams->get('useAsTWZ', 0);

            // Teilnehmerdaten holen
            $tlnr = JTable::getInstance('turnier_teilnehmer', 'TableCLM');
            $tlnr->sid		= $rowt->sid;
            $tlnr->turnier	= $row->tid;
            $tlnr->snr		= $snrmax + 1;  // 0
            $tlnr->name		= $registrationname;
            $tlnr->birthYear = $row->birthYear;
            $tlnr->geschlecht = $row->geschlecht;
            $tlnr->verein	= $row->club;
            $tlnr->email	= $row->email;
            if ($row->dwz == '' or $row->dwz < 1) {
                $tlnr->start_dwz = 0;
            } else {
                $tlnr->start_dwz = $row->dwz;
            }
            $tlnr->start_I0	= $row->dwz_I0;
            if ($row->elo == '' or $row->elo < 1) {
                $tlnr->FIDEelo = 0;
            } else {
                $tlnr->FIDEelo	= $row->elo;
            }
            $tlnr->twz		= 0;
            if ($param_useastwz == 0) {
                $tlnr->twz = max(array($tlnr->start_dwz, $tlnr->FIDEelo));
            } elseif ($param_useastwz == 1) {
                $tlnr->twz = $tlnr->start_dwz;
                if ($tlnr->twz == 0) {
                    $tlnr->twz = $tlnr->FIDEelo;
                }
            } else {
                $tlnr->twz = $tlnr->FIDEelo;
                if ($tlnr->twz == 0) {
                    $tlnr->twz = $tlnr->start_dwz;
                }
            }
            $tlnr->FIDEid	= $row->FIDEid;
            $tlnr->FIDEcco	= $row->FIDEcco;
            $tlnr->titel	= $row->titel;
            $tlnr->mgl_nr	= $row->mgl_nr;
            $tlnr->PKZ		= $row->PKZ;
            $tlnr->zps		= $row->zps;
            if (strlen($tlnr->zps) != 5 or $tlnr->mgl_nr < 1) {
                // weiteren Daten aus TlnTabelle
                $db		= JFactory::getDBO();
                $query = "SELECT MAX(mgl_nr), MAX(snr) FROM `#__clm_turniere_tlnr`"
                    ." WHERE turnier = ".$tlnr->turnier
                    ." AND zps = 99999 "
                ;
                $db->setQuery($query);
                list($maxFzps, $maxSnr) = $db->loadRow();
                $maxFzps++; // fiktive ZPS für manuell eingegeben Spieler
                //$maxSnr++; // maximale snr für alle Spieler
                $tlnr->zps = '99999';
                $tlnr->mgl_nr = $maxFzps;
            }
            $tlnr->tel_no	= $row->tel_no;
            $tlnr->account	= $row->account;
            $tlnr->titel	= $row->titel;
            $tlnr->tlnrStatus = 1;
            $tlnr->published = 1;
            if (!$tlnr->check($post)) {
                $this->app->enqueueMessage($tlnr->getError(), 'error');
                return false;
            }
            if (!$tlnr->store()) {
                $this->app->enqueueMessage($tlnr->getError(), 'error');
                return false;
            }
            if ($tlnr->zps == '99999') {
                $text = JText::_('REGISTRATION_MOVED9');
            } else {
                $text = JText::_('REGISTRATION_MOVED');
            }
        } else {
            $text = JText::_('REGISTRATION_EDITED');
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $text;
        $clmLog->params = array('tid' => $turnierid, 'rid' => $registrationid, 'name' => $registrationname); // TurnierID wird als LigaID gespeichert
        $clmLog->write();

        $this->app->enqueueMessage($text);

        return true;

    }

    public function cancel()
    {

        // turnierid
        $turnierid = clm_core::$load->request_int('turnierid');

        $adminLink = new AdminLink();
        $adminLink->view = "turregistrations";
        $adminLink->more = array('id' => $turnierid);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }

}
