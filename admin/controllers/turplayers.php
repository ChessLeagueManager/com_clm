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
// no direct access
defined('_JEXEC') or die('Restricted access');

class CLMControllerTurPlayers extends JControllerLegacy
{
    // Konstruktor
    public function __construct($config = array())
    {

        parent::__construct($config);

        $this->app	= JFactory::getApplication();

        // Register Extra tasks
        $this->registerTask('unactive', 'active');

    }

    public function turform()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turform";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();

        $this->app->redirect($adminLink->url);

    }


    // Weiterleitung!
    public function add()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayerform";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();

        $this->app->redirect($adminLink->url);

    }
    // Nachzügler aufnehmen =  Anzahl erhöhen + Weiterleitung!
    public function add_nz()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        $tournament = new CLMTournament($id, true);
        $tournament->makePlusTln(); // Message werden dort erstellt

        $adminLink = new AdminLink();
        $adminLink->view = "turplayerform";
        $adminLink->more = array('id' => $id, 'add_nz' => 1 );
        $adminLink->makeURL();

        $this->app->redirect($adminLink->url);
    }

    public function del_player()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        // ausgewählte Einträge
        $cid = clm_core::$load->request_array_int('cid');

        $output = clm_core::$api->db_tournament_player_del($id, $cid);
        $error = clm_core::$load->load_view("notification", array($output[1],false));

        // Message
        $this->app->enqueueMessage($error[0][0]);

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }

    public function plusTln()
    {

        $this->_plusTlnDo();

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }


    public function _plusTlnDo()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        $tournament = new CLMTournament($id, true);
        $tournament->makePlusTln(); // Message werden dort erstellt

        return true;

    }



    public function remove()
    {

        $this->_removeDo();

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }


    public function _removeDo()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        // turnierid
        $id = clm_core::$load->request_int('id');

        // Turnierdaten holen
        $turnier = JTable::getInstance('turniere', 'TableCLM');
        $turnier->load($id); // Daten zu dieser ID laden

        // Turnier existent?
        if (!$turnier->id) {
            $this->app->enqueueMessage(CLMText::errorText('TOURNAMENT', 'NOTEXISTING'), 'warning');
            return false;
        }

        $clmAccess = clm_core::$access;
        if (($turnier->tl != clm_core::$access->getJid() and $clmAccess->access('BE_tournament_edit_detail') !== true) or $clmAccess->access('BE_tournament_edit_detail') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return false;
        }

        // Wenn Ergebnisse gemeldet keine nachträgliche Löschung erlauben
        $tournament = new CLMTournament($id);
        $tournament->checkTournamentStarted();
        if ($tournament->started) {
            $this->app->enqueueMessage(JText::_('DELETION_NOT_POSSIBLE').": ".JText::_('RESULTS_ENTERED'), 'warning');
            return false;
        }

        // ausgewählte Einträge
        $cid = clm_core::$load->request_array_int('cid');

        if (count($cid) < 1) {
            $this->app->enqueueMessage(JText::_('NO_ITEM_SELECTED'), 'warning');
            return false;
        }
        // alle Checks erledigt


        $cids = implode(',', $cid);
        for ($i = 0; $i < count($cid); $i++) {
            $query = 'SELECT * FROM #__clm_turniere_tlnr'
                . ' WHERE id = '. $cid[$i];
            $tlnr	= clm_core::$db->loadObjectList($query);
            $query = 'DELETE FROM #__clm_turniere_tlnr'
                .' WHERE turnier = '.$turnier->id.' AND id = '. $cid[$i];
            clm_core::$db->query($query);
            $query = 'UPDATE #__clm_turniere_tlnr  SET snr = (snr - 1) '
                .' WHERE turnier = '. $turnier->id.' AND snr > '.$tlnr[0]->snr;
            clm_core::$db->query($query);
        }
        //		$this->_db->setQuery($query);
        //		if (!$this->_db->query()) {
        if (!clm_core::$db->query($query)) {
            $this->app->enqueueMessage(JText::_('DB_ERROR'), 'warning');
            return false;
        }

        $text = CLMText::sgpl(count($cid), JText::_('PLAYER'), JText::_('PLAYERS'))." ".JText::_('DELETED');

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $text;
        $clmLog->params = array('sid' => $turnier->sid, 'tid' => $turnier->id, 'cids' => count($cid));
        $clmLog->write();


        // Message
        $this->app->enqueueMessage($text);

        return true;

    }

    // Moves the record up one position
    public function orderdown()
    {

        $this->_order(1);

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }

    // Moves the record down one position
    public function orderup()
    {

        $this->_order(-1);

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }

    // Moves the order of a record
    // @param integer The direction to reorder, +1 down, -1 up
    public function _order($inc)
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_tournament_edit_detail') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return false;
        }

        $cid = clm_core::$load->request_array_int('cid');
        $tlnid = $cid[0];

        $row = JTable::getInstance('turnier_teilnehmer', 'TableCLM');
        if (!$row->load((int)$tlnid)) {
            $this->app->enqueueMessage(CLMText::errorText('PLAYER', 'NOTEXISTING'), 'warning');
            return false;
        }
        //$row->move($inc, '');
        $row->move($inc, 'turnier = '.$row->turnier);
        $row->reorder('turnier = '.$row->turnier);

        $this->app->enqueueMessage(JText::_('ORDERING_CHANGED'));

        return true;

    }


    // Saves user reordering entry
    public function saveOrder()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_tournament_edit_detail') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return false;
        }

        // alle enthaltenen IDs
        $cid		= clm_core::$load->request_array_int('cid');
        $total		= count($cid);

        // alle Order-Einträge
        $order		= clm_core::$load->request_array_int('order');

        $row = JTable::getInstance('turnier_teilnehmer', 'TableCLM');

        $groupings = array();

        // update ordering values
        for ($i = 0; $i < $total; $i++) {
            $row->load((int) $cid[$i]);
            // track categories
            $groupings[] = $row->turnier;

            if ($row->ordering != $order[$i]) {
                $row->ordering = $order[$i];
                if (!$row->store()) {
                    $this->app->enqueueMessage($db->getErrorMsg(), 'error');
                }
            }
        }
        // execute update Order for each parent group
        $groupings = array_unique($groupings);
        foreach ($groupings as $group) {
            $row->reorder('turnier = '.(int) $group);
        }

        $this->app->enqueueMessage(JText::_('NEW_ORDERING_SAVED'));

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }


    public function sortByTWZ()
    {
        $this->_sortBy('twz');

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }

    public function sortByRandom()
    {
        $this->_sortBy('random');

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }

    public function sortByOrdering()
    {
        $this->_sortBy('ordering');

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }

    public function _sortBy($by)
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        $db	= JFactory::getDBO();

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_tournament_edit_detail') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return false;
        }

        // turnierid
        $id = clm_core::$load->request_int('id');

        $tournament = new CLMTournament($id);
        $tournament->checkTournamentStarted();
        if ($tournament->started) {
            $this->app->enqueueMessage(JText::_('SORTING_NOT_POSSIBLE').": ".JText::_('RESULTS_ENTERED'), 'warning');
            return false;
        }

        // Anzahl gemeldeter Spiele -> maximale Snr
        $query = "SELECT COUNT(id) FROM `#__clm_turniere_tlnr`"
            ." WHERE turnier =".$id
        ;
        $db->setQuery($query);
        $maximum = $db->loadResult();

        // alle Spieler in Reihenfolge laden
        if ($by == 'ordering') {
            $queryOrderBy = 'SELECT id FROM `#__clm_turniere_tlnr`'
                                .' WHERE turnier = '.$id
                                .' ORDER BY ordering ASC'
            ;
            $stringMessage = JText::_('ORDERED_BY_ORDERING');
        } elseif ($by == 'twz') {
            $queryOrderBy = 'SELECT id FROM `#__clm_turniere_tlnr`'
                                .' WHERE turnier = '.$id
                                .' ORDER BY twz DESC'
            ;
            $stringMessage = JText::_('ORDERED_BY_TWZ');
        } elseif ($by == 'random') {
            $queryOrderBy = 'SELECT id FROM `#__clm_turniere_tlnr`'
                                .' WHERE turnier = '.$id
                                .' ORDER BY RAND()'
            ;
            $stringMessage = JText::_('ORDERED_BY_RANDOM');
        }
        $db->setQuery($queryOrderBy);
        $players = $db->loadObjectList();

        $table	= JTable::getInstance('turnier_teilnehmer', 'TableCLM');
        // Snr umsortieren
        $snr = 0;
        // alle Spieler durchgehen
        foreach ($players as $value) {
            $snr++;
            $table->load($value->id);
            $table->snr = $snr;
            $table->store();
        }

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $stringMessage;
        $clmLog->params = array('sid' => $turnier->sid, 'tid' => $id, 'sort' => $by );
        $clmLog->write();

        $this->app->enqueueMessage($stringMessage);

    }


    public function setRanking()
    {
        $this->_setRankingDo();

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }


    public function _setRankingDo()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_tournament_edit_detail') === false) {
            $this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'), 'warning');
            return false;
        }

        // turnierid
        $id = clm_core::$load->request_int('id');

        $tournament = new CLMTournament($id, true);
        $tournament->checkTournamentStarted();
        if (!$tournament->started) {
            $this->app->enqueueMessage(JText::_('RANKING_NOT_POSSIBLE').": ".JText::_('NO_RESULTS_ENTERED'), 'warning');
            return false;
        } elseif ($tournament->data->typ == 3) {
            $this->app->enqueueMessage(JText::_('RANKING_NOT_POSSIBLE').": ".JText::_('MODUS_TYP_3'), 'warning');
            return false;
        }

        $tournament->calculateRanking();
        $tournament->setRankingPositions();

        $stringMessage = JText::_('SET_RANKING_DONE');

        // Log schreiben
        $clmLog = new CLMLog();
        $clmLog->aktion = $stringMessage;
        $clmLog->params = array('sid' => $tournament->data->sid, 'tid' => $id);
        $clmLog->write();

        $this->app->enqueueMessage($stringMessage);

        return true;

    }



    public function cancel()
    {

        $adminLink = new AdminLink();
        $adminLink->view = "turmain";
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }
    public function active()
    {

        $this->_activeDo();

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);

    }

    public function _activeDo()
    {

        // Check for request forgeries
        defined('_JEXEC') or die('Invalid Token');

        // turnierid
        $id = clm_core::$load->request_int('id');

        $cid = clm_core::$load->request_array_int('cid');
        $tlnrID = $cid[0];

        // Teilnehmerdaten holen
        $tlnr = JTable::getInstance('turnier_teilnehmer', 'TableCLM');
        $tlnr->load($tlnrID); // Daten zu dieser ID laden
        // Teilnehmer existent?
        if (!$tlnr->id) {
            $this->app->enqueueMessage(CLMText::errorText('PLAYER', 'NOTEXISTING'), 'warning');
            return false;

            // Teilnehmer gehört zu Turnier?
        } elseif ($tlnr->turnier != $id) {
            $this->app->enqueueMessage(CLMText::errorText('PLAYER', 'NOACCESS'), 'warning');
            return false;
        }

        $task		= clm_core::$load->request_string('task');
        $active	= ($task == 'active'); // zu vergebender Wert 0/1
        // jetzt schreiben
        if ($active) {
            $tlnr->tlnrStatus = 1;
        } else {
            $tlnr->tlnrStatus = 0;
        }
        if (!$tlnr->store()) {
            $this->app->enqueueMessage('Fehler beim Speichern', 'error');
            return false;
        }

        if ($active) {
            $this->app->enqueueMessage($tlnr->name.": "." ".JText::_('PLAYER_ACTIVE'));
        } else {
            $this->app->enqueueMessage($tlnr->name.": "." ".JText::_('PLAYER_DEACTIVE'));
        }

        // Log
        $clmLog = new CLMLog();
        $clmLog->aktion = JText::_('PLAYER')." ".$tlnr->name." (ID: ".$tlnrID."): ".$task;
        $clmLog->params = array('tid' => $id); // TurnierID wird als LigaID gespeichert
        $clmLog->write();

        return true;

    }

    // TWZ aus Parameter des Turniers, NWZ und ELO ermitteln
    /*	function _getTWZ ($param = 0, $natrating = 0, $fideelo = 0) {
            $twz = 0;
            if ($param == 0) {
                $twz = max(array($natrating, $fideelo));
            } elseif ($param == 1) {
                $twz = $natrating;
                if ($twz == 0) {
                    $twz = $fideelo;
                }
            } else {
                $twz = $fideelo;
                if ($twz == 0) {
                    $twz = $natrating;
                }
            }
            return $twz;
        }
    */
    // Weiterleitung!
    public function goto_rounds()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turrounds";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }

    // Weiterleitung!
    public function onlineRegList()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turregistrations";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }

    // Weiterleitung!
    public function mail_to_all()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayersmail";
        $adminLink->more = array('turnierid' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }

    // Weiterleitung!
    public function edit_teams()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turteams";
        $adminLink->more = array('turnierid' => $id);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }

    // Weiterleitung!
    public function player_decode()
    {

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turdecode";
        $adminLink->more = array('turnierid' => $id, 'init_numberlines' => 1);
        $adminLink->makeURL();
        $this->app->redirect($adminLink->url);
    }

    // Copy der Nicknamen aus Vorsaison
    public function player_decode_copy()
    {

        $db	= JFactory::getDBO();

        // id aktuelle Saison bestimmen
        $sql	= " SELECT id FROM #__clm_saison "
            ." WHERE archiv = 0 AND published = 1"
            ." ORDER BY id ASC LIMIT 1"
        ;
        $db->setQuery($sql);
        $sid	= $db->loadResult();
        // id Vorsaison
        if (is_numeric($sid)) {
            $vsid = $sid - 1;
        } else {
            $vsid = 0;
        }

        // schon vorhandenen Nicknamen in aktueller Saison bestimmen und in Array ablegen
        $query = 'SELECT concat(source,oname) as oname FROM `#__clm_player_decode`'
                .' WHERE sid = '.$sid
        ;
        $db->setQuery($query);
        $sid_nicknames = $db->loadObjectList();
        $arr_nicknames = array();
        foreach ($sid_nicknames as $nickname) {
            $arr_nicknames[] = "'".$nickname->oname."'";
        }
        $str_nicknames = implode(',', $arr_nicknames);

        // Alle Nicknames aus Vorsaison laden, die noch nicht vorhanden sind
        $sql = " SELECT id FROM #__clm_player_decode "
        ." WHERE sid = ".$vsid;
        if (count($arr_nicknames) > 0) {
            $sql = $sql.' AND concat(source,oname) NOT IN ('.$str_nicknames.') ';
        }
        $sql = $sql." ORDER BY id ASC "
        ;
        $db->setQuery($sql);
        $vsid_nicknames	= $db->loadObjectList();

        // turnierid
        $id = clm_core::$load->request_int('id');

        $adminLink = new AdminLink();
        $adminLink->view = "turplayers";
        $adminLink->more = array('id' => $id);
        $adminLink->makeURL();

        // Nicknamen laden und mit neuer Saison speichern
        $row = JTable::getInstance('decode', 'TableCLM');
        $i = 0;
        for ($x = 0; $x < count($vsid_nicknames); $x++) {
            $row->load($vsid_nicknames[$x]->id);
            $row->id	= "0";
            $row->sid	= $sid;
            if (!$row->store()) {
                $this->app->enqueueMessage($row->getError(), 'error');
                $this->app->redirect($adminLink->url);
            }
            $i++;
        }

        $this->app->enqueueMessage($i.' '.JText::_('DECODE_SEASON_COPIED'));
        $this->app->redirect($adminLink->url);
    }

}
