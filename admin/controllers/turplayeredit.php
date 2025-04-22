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

class CLMControllerTurPlayerEdit extends JControllerLegacy
{
    // Konstruktor
    public function __construct($config = array())
    {

        parent::__construct($config);

        $this->app = JFactory::getApplication();

        // Register Extra tasks
        $this->registerTask('apply', 'save');

    }



    public function save()
    {

        $this->_saveDo();

        // playerid
        $playerid = clm_core::$load->request_int('playerid');
        $turnierid = clm_core::$load->request_int('turnierid');
        // Task
        $task = clm_core::$load->request_string('task');

        $adminLink = new AdminLink();
        if ($task == 'apply') {
            // Weiterleitung bleibt im Formular
            $adminLink->more = array('playerid' => $playerid);
            $adminLink->view = "turplayeredit";
        } else {
            // Weiterleitung in Liste
            $adminLink->more = array('id' => $turnierid);
            $adminLink->view = "turplayers"; // WL in Liste
        }
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }


    public function _saveDo()
    {

        defined('_JEXEC') or die('Invalid Token');

        // turnierid
        $playerid = clm_core::$load->request_int('playerid');
        $turnierid = clm_core::$load->request_int('turnierid');

        // Instanz der Tabelle
        $row = JTable::getInstance('turniere', 'TableCLM');
        $row->load($turnierid); // Daten zu dieser ID laden

        $clmAccess = clm_core::$access;
        if (($row->tl != clm_core::$access->getJid() and $clmAccess->access('BE_tournament_edit_detail') !== true) or $clmAccess->access('BE_tournament_edit_detail') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return false;
        }

        // Task
        $task = clm_core::$load->request_string('task');

        // Instanz der Tabelle
        $row = JTable::getInstance('turnier_teilnehmer', 'TableCLM');
        $row->load($playerid); // Daten zu dieser ID laden

        // Spieler existent?
        if (!$row->id) {
            $this->app->enqueueMessage(CLMText::errorText('PLAYER', 'NOTEXISTING'), 'warning');
            return false;

            // Runde gehört zu Turnier?
        } elseif ($row->turnier != $turnierid) {
            $this->app->enqueueMessage(CLMText::errorText('PLAYER', 'NOACCESS'), 'warning');
            return false;
        }

        $post = $_POST;
        if (!$row->bind($post)) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return false;
        }
        if (is_null($row->mgl_nr) or !is_numeric($row->mgl_nr)) {
            $row->mgl_nr = 999;
        }
        if ($row->start_dwz == '') {
            $row->start_dwz = 0;
        }
        if (is_null($row->start_I0) or !is_numeric($row->start_I0)) {
            $row->start_I0 = 0;
        }
        if ($row->sum_punkte == '') {
            $row->sum_punkte = 0;
        }
        if ($row->sumTiebr1 == '') {
            $row->sumTiebr1 = 0;
        }
        if ($row->sumTiebr2 == '') {
            $row->sumTiebr2 = 0;
        }
        if ($row->sumTiebr3 == '') {
            $row->sumTiebr3 = 0;
        }
        if (is_null($row->FIDEid) or !is_numeric($row->FIDEid)) {
            $row->FIDEid = 0;
        }
        if (is_null($row->birthYear) or !is_numeric($row->birthYear)) {
            $row->birthYear = '0000';
        }
        if (is_null($row->s_punkte) or !is_numeric($row->s_punkte)) {
            $row->s_punkte = 0.0;
        }
        if (!$row->check($post)) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return false;
        }
        if (!$row->store()) {
            $this->app->enqueueMessage($row->getError(), 'error');
            return false;
        }


        clm_core::$api->direct("db_tournament_delDWZ", array($turnierid,false));

        $text = JText::_('PARTICIPANT_EDITED').": ".$row->name;

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $text;
        $clmLog->params = array('sid' => $row->sid, 'tid' => $turnierid); // TurnierID wird als LigaID gespeichert
        $clmLog->write();

        $this->app->enqueueMessage($text);

        return true;

    }

    public function cancel()
    {

        // turnierid
        $turnierid = clm_core::$load->request_int('turnierid');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $turnierid);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }

}
