<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerTurPlayers extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		// turnierid
		$this->id = JRequest::getInt('id');
		
		$this->_db		= JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'unactive','active' );

		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turplayers";
		$this->adminLink->more = array('id' => $this->id);
	
	}

	function turform() {
		
		$this->adminLink->view = "turform";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}


	// Weiterleitung!
	function add() {
		
		$this->adminLink->view = "turplayerform";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}
	// Nachzügler aufnehmen =  Anzahl erhöhen + Weiterleitung!
	function add_nz() {
		$tournament = new CLMTournament($this->id, true);
		$tournament->makePlusTln(); // Message werden dort erstellt
		
		$this->adminLink->view = "turplayerform";
		$this->adminLink->more = array('id' => $this->id, 'add_nz' => 1 );
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	}

	function del_player() {
		// ausgewählte Einträge
		$cid = JRequest::getVar('cid', array(), '', 'array');

		$output = clm_core::$api->db_tournament_player_del($this->id,$cid);
		$error = clm_core::$load->load_view("notification", array($output[1],false));	

		// Message
		$app =JFactory::getApplication();
		$app->enqueueMessage($error[0][0]);

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}

	function plusTln() {
	
		$this->_plusTlnDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _plusTlnDo() {
	
		$tournament = new CLMTournament($this->id, true);
		$tournament->makePlusTln(); // Message werden dort erstellt
		
		return true;
	
	}



	function remove() {
	
		$this->_removeDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _removeDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// Turnierdaten holen
		$turnier =JTable::getInstance( 'turniere', 'TableCLM' );
		$turnier->load( $this->id ); // Daten zu dieser ID laden

		// Turnier existent?
		if (!$turnier->id) {
			JError::raiseWarning( 500, CLMText::errorText('TOURNAMENT', 'NOTEXISTING') );
			return false;
		}
	
		$clmAccess = clm_core::$access;      
		if (($turnier->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		// Wenn Ergebnisse gemeldet keine nachträgliche Löschung erlauben
		$tournament = new CLMTournament($this->id);
		$tournament->checkTournamentStarted();
		if ($tournament->started) {
			JError::raiseWarning( 500, JText::_( 'DELETION_NOT_POSSIBLE' ).": ".JText::_('RESULTS_ENTERED') );
			return false;
		}
	
		// ausgewählte Einträge
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
	
		if (count($cid) < 1) {
			JError::raiseWarning(500, JText::_( 'NO_ITEM_SELECTED', true ) );
			return false;
		}
		// alle Checks erledigt
	
	
		$cids = implode(',', $cid );
		$query = 'DELETE FROM #__clm_turniere_tlnr'
				.' WHERE turnier = '.$turnier->id.' AND id IN ( '. $cids .' )'
			;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			JError::raiseWarning(500, JText::_( 'DB_ERROR', true ) );
			return false;
		}
	
		$text = CLMText::sgpl(count($cid), JText::_('PLAYER'), JText::_('PLAYERS'))." ".JText::_('DELETED');
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $text;
		$clmLog->params = array('sid' => $turnier->sid, 'tid' => $turnier->id, 'cids' => count($cid));
		$clmLog->write();
	
	
		// Message
		$app =JFactory::getApplication();
		$app->enqueueMessage( $text );
	
		return true;
		
	}

	// Moves the record up one position
	function orderdown() {
		
		$this->_order(1);
		
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	// Moves the record down one position
	function orderup() {
		
		$this->_order(-1);
		
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	// Moves the order of a record
	// @param integer The direction to reorder, +1 down, -1 up
	function _order($inc) {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$tlnid = $cid[0];
	
		$row =JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		if ( !$row->load($tlnid) ) {
			JError::raiseWarning( 500, CLMText::errorText('PLAYER', 'NOTEXISTING') );
			return false;
		}
		$row->move($inc, '');
	
		$app =JFactory::getApplication();
		$app->enqueueMessage( JText::_('ORDERING_CHANGED') );
		
		return true;
		
	}


	// Saves user reordering entry
	function saveOrder() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		// alle enthaltenen IDs
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		$total		= count( $cid );
	
		// alle Order-Einträge
		$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));
	
		$row =JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		
		$groupings = array();
	
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->turnier;
	
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
			}
		}
		// execute update Order for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('turnier = '.(int) $group);
		}
		
		
		$app =JFactory::getApplication();
		$app->enqueueMessage( JText::_('NEW_ORDERING_SAVED') );
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function sortByTWZ() {
		$this->_sortBy('twz');
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}
	
	function sortByRandom() {
		$this->_sortBy('random');
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}
	
	function sortByOrdering() {
		$this->_sortBy('ordering');
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}
	
	function _sortBy($by) {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$tournament = new CLMTournament($this->id);
		$tournament->checkTournamentStarted();
		if ($tournament->started) {
			JError::raiseWarning( 500, JText::_( 'SORTING_NOT_POSSIBLE' ).": ".JText::_('RESULTS_ENTERED') );
			return false;
		}
	
		// Anzahl gemeldeter Spiele -> maximale Snr
		$query = "SELECT COUNT(id) FROM `#__clm_turniere_tlnr`"
			." WHERE turnier =".$this->id
			;
		$this->_db->setQuery($query);
		$maximum = $this->_db->loadResult();
	
		// alle Spieler in Reihenfolge laden
		if ($by == 'ordering') {
			$queryOrderBy = 'SELECT id FROM `#__clm_turniere_tlnr`'
								.' WHERE turnier = '.$this->id
								.' ORDER BY ordering ASC'
								;
			$stringMessage = JText::_('ORDERED_BY_ORDERING');
		} elseif ($by == 'twz') {
			$queryOrderBy = 'SELECT id FROM `#__clm_turniere_tlnr`'
								.' WHERE turnier = '.$this->id
								.' ORDER BY twz DESC'
								;
			$stringMessage = JText::_('ORDERED_BY_TWZ');
		} elseif ($by == 'random') {
			$queryOrderBy = 'SELECT id FROM `#__clm_turniere_tlnr`'
								.' WHERE turnier = '.$this->id
								.' ORDER BY RAND()'
								;
			$stringMessage = JText::_('ORDERED_BY_RANDOM');
		}
		$this->_db->setQuery($queryOrderBy);
		$players = $this->_db->loadObjectList();
	
		$table	=JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
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
		$clmLog->params = array('sid' => $turnier->sid, 'tid' => $this->id, 'cids' => count($cid));
		$clmLog->write();
		
		$app =JFactory::getApplication();
		$app->enqueueMessage( $stringMessage );
	
	}


	function setRanking() {
		$this->_setRankingDo();
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}


	function _setRankingDo() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$tournament = new CLMTournament($this->id, true);
		$tournament->checkTournamentStarted();
		if (!$tournament->started) {
			JError::raiseWarning( 500, JText::_( 'RANKING_NOT_POSSIBLE' ).": ".JText::_('NO_RESULTS_ENTERED') );
			return false;
		} elseif ($tournament->data->typ == 3) {
			JError::raiseWarning( 500, JText::_( 'RANKING_NOT_POSSIBLE' ).": ".JText::_('MODUS_TYP_3') );
			return false;
		}
	
		$tournament->calculateRanking();
		$tournament->setRankingPositions();
	
		$stringMessage = JText::_('SET_RANKING_DONE');
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringMessage;
		$clmLog->params = array('sid' => $tournament->data->sid, 'tid' => $this->id);
		$clmLog->write();
		
		$app =JFactory::getApplication();
		$app->enqueueMessage( $stringMessage );
	
		return true;
	
	}



	function cancel() {
		
		$this->adminLink->view = "turmain";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
		
	}
	function active() {

		$this->_activeDo();

		$this->adminLink->view = "turplayers";
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

	function _activeDo() {

		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$tlnrID = $cid[0];
	
		// Teilnehmerdaten holen
		$tlnr =JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		$tlnr->load( $tlnrID ); // Daten zu dieser ID laden
		// Teilnehmer existent?
		if (!$tlnr->id) {
			JError::raiseWarning( 500, CLMText::errorText('PLAYER', 'NOTEXISTING') );
			return false;
		
		// Teilnehmer gehört zu Turnier?
		} elseif ($tlnr->turnier != $this->id) {
			JError::raiseWarning( 500, CLMText::errorText('PLAYER', 'NOACCESS') );
			return false;
	}

		$task		= JRequest::getCmd('task');
		$active	= ($task == 'active'); // zu vergebender Wert 0/1
		// jetzt schreiben
		$tlnr->tlnrStatus = $active;
		if (!$tlnr->store()) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
	
		$app =JFactory::getApplication();
		if ($active) {
			$app->enqueueMessage( $tlnr->name.": "." ".JText::_('PLAYER_ACTIVE') );
		} else {
			$app->enqueueMessage( $tlnr->name.": "." ".JText::_('PLAYER_DEACTIVE') );
		}
	
		// Log
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('PLAYER')." ".$tlnr->name." (ID: ".$tlnrID."): ".$task;
		$clmLog->params = array('tid' => $this->id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
	
		return true;
	
	}

	// TWZ aus Parameter des Turniers, NWZ und ELO ermitteln
	function _getTWZ ($param = 0, $natrating = 0, $fideelo = 0) {	
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

}
