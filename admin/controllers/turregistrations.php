<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerTurRegistrations extends JControllerLegacy {	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app 	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'unactive','active' );

	}

	function turform() {
		
		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turform";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}


	function del_registrations() {

		// turnierid
		$id = clm_core::$load->request_int('id');

		// ausgewählte Einträge
		$cid = clm_core::$load->request_array_int('cid');
		$output = clm_core::$api->db_tournament_registration_del($id,$cid);
		$error = clm_core::$load->load_view("notification", array($output[1],false));	

		// Message
		$this->app->enqueueMessage($error[0][0]);

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	}

	function edit_registration() {

		// turnierid
		$id = clm_core::$load->request_int('id');

		// ausgewählte Einträge
		$cid = clm_core::$load->request_array_int('cid');
		if (is_null($cid)) $count = 0;
		else $count = count($cid);

		$adminLink = new AdminLink();
		if ($count < 1) {
			$this->app->enqueueMessage( JText::_('NO_ITEM_SELECTED'), 'warning' );
			$adminLink->view = "turregistrations";
			$adminLink->more = array('id' => $id);
		} else {
			$adminLink->view = "turregistrationedit";
			$adminLink->more = array('registrationid' => $cid[0]);
		}
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	}


	function remove() {
	
		$this->_removeDo();

		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}


	function _removeDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// turnierid
		$id = clm_core::$load->request_int('id');
		// Turnierdaten holen
		$turnier =JTable::getInstance( 'turniere', 'TableCLM' );
		$turnier->load( $id ); // Daten zu dieser ID laden

		// Turnier existent?
		if (!$turnier->id) {
			$this->app->enqueueMessage( CLMText::errorText('TOURNAMENT', 'NOTEXISTING'), 'warning' );
			return false;
		}
	
		$clmAccess = clm_core::$access;      
		if (($turnier->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'), 'warning' );
			return false;
		}
	
		// Wenn Ergebnisse gemeldet keine nachträgliche Löschung erlauben
		$tournament = new CLMTournament($id);
		$tournament->checkTournamentStarted();
		if ($tournament->started) {
			$this->app->enqueueMessage( JText::_( 'DELETION_NOT_POSSIBLE' ).": ".JText::_('RESULTS_ENTERED'), 'warning' );
			return false;
		}
	
		// ausgewählte Einträge
		$cid = clm_core::$load->request_array_int('cid');
		if (is_null($cid)) $cid = array();
	
		if (count($cid) < 1) {
			$this->app->enqueueMessage( JText::_('NO_ITEM_SELECTED'), 'warning' );
			return false;
		}
		// alle Checks erledigt
	
	
		$cids = implode(',', $cid );
		$query = 'DELETE FROM #__clm_turniere_tlnr'
				.' WHERE turnier = '.$turnier->id.' AND id IN ( '. $cids .' )'
			;
//		$this->_db->setQuery($query);
//		if (!$this->_db->query()) { 
		if (!clm_core::$db->query($query)) { 
			$this->app->enqueueMessage( JText::_('DB_ERROR'), 'warning' );
			return false;
		}
	
		$text = CLMText::sgpl(count($cid), JText::_('PLAYER'), JText::_('PLAYERS'))." ".JText::_('DELETED');
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $text;
		$clmLog->params = array('sid' => $turnier->sid, 'tid' => $turnier->id, 'cids' => count($cid));
		$clmLog->write();
	
	
		// Message
		$this->app->enqueueMessage( $text );
	
		return true;
		
	}

	// Moves the record up one position
	function orderdown() {
		
		$this->_order(1);
		
		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}

	// Moves the record down one position
	function orderup() {
		
		$this->_order(-1);
		
		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}

	// Moves the order of a record
	// @param integer The direction to reorder, +1 down, -1 up
	function _order($inc) {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'), 'warning' );
			return false;
		}
	
		$cid = clm_core::$load->request_array_int('cid');
		$tlnid = $cid[0];
	
		$row =JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		if ( !$row->load($tlnid) ) {
			$this->app->enqueueMessage( CLMText::errorText('PLAYER', 'NOTEXISTING'), 'warning' );
			return false;
		}
		$row->move($inc, '');
									   
		$this->app->enqueueMessage( JText::_('ORDERING_CHANGED') );
		
		return true;
		
	}


	// Saves user reordering entry
	function saveOrder() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'), 'warning' );
			return false;
		}
	
		// alle enthaltenen IDs
		$cid		= clm_core::$load->request_array_int('cid');
		$total		= count( $cid );
	
		// alle Order-Einträge
		$order		= clm_core::$load->request_array_int('order');
	
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
					$this->app->enqueueMessage( $db->getErrorMsg(), 'error' );
				}
			}
		}
		// execute update Order for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('turnier = '.(int) $group);
		}
										   
		$this->app->enqueueMessage( JText::_('NEW_ORDERING_SAVED') );
	
		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}


	function sortByTWZ() {
		$this->_sortBy('twz');

		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	}
	
	function sortByRandom() {
		$this->_sortBy('random');

		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	}
	
	function sortByOrdering() {
		$this->_sortBy('ordering');

		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	}
	
	function _sortBy($by) {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'), 'warning' );
			return false;
		}
	
		$tournament = new CLMTournament($this->id);
		$tournament->checkTournamentStarted();
		if ($tournament->started) {
			$this->app->enqueueMessage( JText::_( 'SORTING_NOT_POSSIBLE' ).": ".JText::_('RESULTS_ENTERED'), 'warning' );
			return false;
		}
	
		// Anzahl gemeldeter Spiele -> maximale Snr
		$db	= JFactory::getDBO();
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
		$clmLog->params = array('sid' => $turnier->sid, 'tid' => $id, 'cids' => count($cid));
		$clmLog->write();
		
		$this->app->enqueueMessage( $stringMessage );
	
	}


	function setRanking() {
		$this->_setRankingDo();
		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	}


	function _setRankingDo() {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'), 'warning' );
			return false;
		}
	
		// turnierid
		$id = clm_core::$load->request_int('id');

		$tournament = new CLMTournament($id, true);
		$tournament->checkTournamentStarted();
		if (!$tournament->started) {
			$this->app->enqueueMessage( JText::_( 'RANKING_NOT_POSSIBLE' ).": ".JText::_('NO_RESULTS_ENTERED'), 'warning' );
			return false;
		} elseif ($tournament->data->typ == 3) {
			$this->app->enqueueMessage( JText::_( 'RANKING_NOT_POSSIBLE' ).": ".JText::_('MODUS_TYP_3'), 'warning' );
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
		
		$this->app->enqueueMessage( $stringMessage );
	
		return true;
	
	}



	function cancel() {
		
		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turplayers";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
		
	}
	function active() {

		$this->_activeDo();

		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turplayers";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
		
	}

	function _activeDo() {

		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );

		// turnierid
		$id = clm_core::$load->request_int('id');
		$cid = clm_core::$load->request_array_int('cid');
		$tlnrID = $cid[0];
	
		// Teilnehmerdaten holen
		$tlnr =JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		$tlnr->load( $tlnrID ); // Daten zu dieser ID laden
		// Teilnehmer existent?
		if (!$tlnr->id) {
			$this->app->enqueueMessage( CLMText::errorText('PLAYER', 'NOTEXISTING'), 'warning' );
			return false;
		
		// Teilnehmer gehört zu Turnier?
		} elseif ($tlnr->turnier != $this->id) {
			$this->app->enqueueMessage( CLMText::errorText('PLAYER', 'NOACCESS'), 'warning' );
			return false;
	}

		$task		= clm_core::$load->request_string('task');
		$active	= ($task == 'active'); // zu vergebender Wert 0/1
		// jetzt schreiben
		$tlnr->tlnrStatus = $active;
		if (!$tlnr->store()) {
			$this->app->enqueueMessage( $row->getError(), 'error' );
			return false;
		}
									   
		if ($active) {
			$this->app->enqueueMessage( $tlnr->name.": "." ".JText::_('PLAYER_ACTIVE') );
		} else {
			$this->app->enqueueMessage( $tlnr->name.": "." ".JText::_('PLAYER_DEACTIVE') );
		}
	
		// Log
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('PLAYER')." ".$tlnr->name." (ID: ".$tlnrID."): ".$task;
		$clmLog->params = array('tid' => $id); // TurnierID wird als LigaID gespeichert
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

	// Weiterleitung!
	function onlineRegList() {
		
		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turregistrations";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}

}
