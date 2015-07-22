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

class CLMControllerTurRounds extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= JFactory::getDBO();
		
		// turnierid
		$this->id = JRequest::getInt('id');
		
		// Register Extra tasks
		$this->registerTask( 'unpublish','publish' );
		$this->registerTask( 'unapprove','approve' );
		$this->registerTask( 'disbale','enable' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turrounds";
		$this->adminLink->more = array('id' => $this->id);
	
	}


	function turform() {
		
		$this->adminLink->view = "turform";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}


	function assignMatches() {
	
		$this->_assignMatchesDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}
	
	
	function _assignMatchesDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		// Turnierdaten!
		$tournament = new CLMTournament($this->id, true);
		// $tournament->data->typ

		if (($tournament->data->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
		
		if ($tournament->data->typ == 2) {
			// Volturnier nur via Rundenerstellung!
			JError::raiseWarning(500, CLMText::errortext(JText::_('MATCHES_ASSIGN'), 'IMPOSSIBLE' ));
			return false;
		
		} elseif ($tournament->data->typ == 3) { // KO
			// maximal bestätige Runde holen - ist hier MIN(nr)
			$query = 'SELECT MIN(nr) FROM #__clm_turniere_rnd_termine'
				. ' WHERE turnier = '.$this->id.' AND tl_ok = 1';
			$this->_db->setQuery( $query );
			if ($tlokMin = $this->_db->loadResult()) {
	 			$roundToDraw = $tlokMin-1;
	 		} else {
	 			$roundToDraw = $tournament->data->runden;
			}
			// nächste zu vervollständigende Runde ermittelt
			if ($roundToDraw == 0) { // dann gibt es nichts mehr zu tun
				JError::raiseWarning(500, JText::_('NO_ROUND_LEFT') );
				return false;
			}
			
			// Frage: sind in dieser Runde schon Partien angesetzt?
			$query = 'SELECT COUNT(*)'
					. ' FROM #__clm_turniere_rnd_spl'
					. ' WHERE turnier = '.$this->id.' AND runde = '.$roundToDraw.' AND ((spieler >= 1 AND gegner >= 1) OR ergebnis = 8)';
			$this->_db->setQuery($query);
			$matchesAssigned = $this->_db->loadResult();
			
			if ($matchesAssigned > 0) { // bereits Matches angelegt
				JError::raiseWarning(500, JText::_('MATCHES_ASSIGNED_ALREADY') );
				return false;
			}
			
			// OKay, jetzt kann angesetzt werden
			// alle Spieler, die 'in' sind holen
			$query = "SELECT snr "
					. " FROM #__clm_turniere_tlnr"
					. " WHERE turnier = ".$this->id." AND koStatus = '1'";
			$this->_db->setQuery($query);
			$playersIn = $this->_db->loadAssocList('snr');
			
			// wieviele Matches werden benötigt? 
			// Spielerzahl -  (maximale Matches der Runde / 2)
			// maximale Matches der Runde: 2^Runde
			$neededMatches = (count($playersIn) - pow(2, $roundToDraw)/2);
			
			// TODO: Sicherheitscheck, ob diese Boards wirklich vorhanden!
			
			// jetzt setzen wir an jedes Board eine Zufallspaarung
			
			// Matches zusammenstellen
			$sid = $tournament->data->sid;
			for ($m=1; $m<=$neededMatches; $m++) {
				// Spieler 1
				$player1 = array_rand($playersIn);
				unset($playersIn[$player1]);
				// Spieler 2
				$player2 = array_rand($playersIn);
				unset($playersIn[$player2]);
				// SQL
				$query = "UPDATE #__clm_turniere_rnd_spl"
						. " SET tln_nr = ".$player1.", spieler = ".$player1.", gegner = ".$player2
						. " WHERE turnier = ".$this->id." AND  runde = ".$roundToDraw." AND brett = ".$m." AND heim = '1'";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) { 
					JError::raiseError(500, JText::_('MATCH: ').$m.": ".$this->_db->getErrorMsg() ); 
				}
				$query = "UPDATE #__clm_turniere_rnd_spl"
						. " SET tln_nr = ".$player2.", spieler = ".$player2.", gegner = ".$player1
						. " WHERE turnier = ".$this->id." AND  runde = ".$roundToDraw." AND brett = ".$m." AND heim = '0'";
				$this->_db->setQuery($query);
				if (!$this->_db->query()) { 
					JError::raiseError(500, JText::_('MATCH: ').$m.": ".$this->_db->getErrorMsg() ); 
				}
			}
	
			$app =JFactory::getApplication();
			$app->enqueueMessage( JText::_('ROUND_KO_'.$roundToDraw).": ".JText::_('TOURNAMENT_MATCHES_ASSIGNED') );
	
			// Log
			$clmLog = new CLMLog();
			$clmLog->aktion = JText::_('ROUND_KO_'.$roundToDraw).": ".JText::_('TOURNAMENT_MATCHES_ASSIGNED');
			$clmLog->params = array('sid' => $tournament->data->sid, 'tid' => $this->id, 'rnd' => $roundToDraw); // TurnierID wird als LigaID gespeichert
			$clmLog->write();
	
	
		} elseif ($tournament->data->typ == 1) { // CH
			JError::raiseWarning(500, CLMText::errortext(JText::_('MATCHES_ASSIGN'), 'NOTIMPLEMENTED' ));
			return false;
		
		}
	
	}
	

	function enable() {
	
		$this->_enableDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}
	
	function _enableDo() {

		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$roundID = $cid[0];
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->id ); // Daten zu dieser ID laden

	        $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}

		// Rundendaten holen
		$round =JTable::getInstance( 'turnier_runden', 'TableCLM' );
		$round->load( $roundID ); // Daten zu dieser ID laden

		// Runde existent?
		if (!$round->id) {
			JError::raiseWarning( 500, CLMText::errorText('ROUND', 'NOTEXISTING') );
			return false;
		
		// Runde gehört zu Turnier?
		} elseif ($round->turnier != $this->id) {
			JError::raiseWarning( 500, CLMText::errorText('ROUND', 'NOACCESS') );
			return false;
		}
		
		$task		= JRequest::getCmd('task');
		$enable	= ($task == 'enable'); // zu vergebender Wert 0/1
	
		// jetzt schreiben
		$round->abgeschlossen = $enable;
		if (!$round->store()) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
	
		$app =JFactory::getApplication();
		if ($enable) {
			$app->enqueueMessage( $round->name.": "." ".JText::_('ENTRY_ENABLED') );
		} else {
			$app->enqueueMessage( $round->name.": "." ".JText::_('ENTRY_DISABLED') );
		}
	
		// Log
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('ROUND')." ".$round->name." (ID: ".$roundID."): ".$task;
		$clmLog->params = array('tid' => $this->id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
	
		return true;
	
	}
	
	
	function approve() {
	
		$this->_approveDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}
	

	function _approveDo() {

		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->id ); // Daten zu dieser ID laden

	        $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}

		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$roundID = $cid[0];
	
		// Rundendaten holen
		$round =JTable::getInstance( 'turnier_runden', 'TableCLM' );
		$round->load( $roundID ); // Daten zu dieser ID laden

		// Runde existent?
		if (!$round->id) {
			JError::raiseWarning( 500, CLMText::errorText('ROUND', 'NOTEXISTING') );
			return false;
		
		// Runde gehört zu Turnier?
		} elseif ($round->turnier != $this->id) {
			JError::raiseWarning( 500, CLMText::errorText('ROUND', 'NOACCESS') );
			return false;
		}
		
		$task		= JRequest::getCmd('task');
		$approve	= ($task == 'approve'); // zu vergebender Wert 0/1
	
		// weiterer Check: Ergebnisse vollständig?
		if ($approve == 1) {
			$tournamentRound = new CLMTournamentRound($this->id, $cid[0]);
			if (!$tournamentRound->checkResultsComplete()) {
				JError::raiseWarning( 500, CLMText::errorText('RESULTS', 'INCOMPLETE') );
				return false;
			}
		}
	
		// jetzt schreiben
		$round->tl_ok = $approve;
		if (!$round->store()) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
	
		$app =JFactory::getApplication();
		if ($approve) {
			$app->enqueueMessage( $round->name." ".JText::_('CLM_APPROVED') );
		} else {
			$app->enqueueMessage( $round->name." ".JText::_('CLM_UNAPPROVED') );
		}
	
		// Log
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('ROUND')." ".$round->name." (ID: ".$roundID."): ".$task;
		$clmLog->params = array('tid' => $this->id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
	
		return true;
	
	}
	
	
	

	function publish() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->id ); // Daten zu dieser ID laden

	        $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		// TODO? evtl global inconstruct anlegen
		$user 		=JFactory::getUser();
		
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish'); // zu vergebender Wert 0/1
		
		// Inhalte übergeben?
		if (empty( $cid )) { 
			
			JError::raiseWarning( 500, 'NO_ITEM_SELECTED' );
		
		} else { // ja, Inhalte vorhanden
			
			$cids = implode( ',', $cid );
			$query = 'UPDATE #__clm_turniere_rnd_termine'
				. ' SET published = '.(int) $publish
				. ' WHERE id IN ( '. $cids .' )'
				. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				JError::raiseWarning(500, JText::_( 'DB_ERROR', true ) );
			}
			
			$app =JFactory::getApplication();
			if ($publish) {
				$app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('ROUND'), JText::_('ROUNDS'))." ".JText::_('CLM_PUBLISHED') );
			} else {
				$app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('ROUND'), JText::_('ROUNDS'))." ".JText::_('CLM_UNPUBLISHED') );
			}
			
			// Log
			$clmLog = new CLMLog();
			$clmLog->aktion = JText::_('ROUND')." ".$row->name.": ".$task;
			$clmLog->params = array('tid' => $this->id); // TurnierID wird als LigaID gespeichert
			$clmLog->write();
	
		}
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
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
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->id ); // Daten zu dieser ID laden

	        $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$rundenid = $cid[0];
	
		$row =JTable::getInstance( 'turnier_runden', 'TableCLM' );
		if ( !$row->load( $rundenid ) ) {
			JError::raiseWarning( 500, CLMText::errorText('ROUND', 'NOTEXISTING') );
			return false;
		}
		$row->move( $inc, '' );
	
		$app =JFactory::getApplication();
		$app->enqueueMessage( $row->name.": ".JText::_('ORDERING_CHANGED') );
		
		return true;
		
	}

	// Saves user reordering entry
	function saveOrder() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->id ); // Daten zu dieser ID laden

	        $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
	
		$total		= count( $cid );
		$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));
	
		$row =JTable::getInstance( 'turnier_runden', 'TableCLM' );
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
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('sid = '.(int) $group);
		}
		
		$app =JFactory::getApplication();
		$app->enqueueMessage( JText::_('NEW_ORDERING_SAVED') );
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	function cancel() {
		
		$this->adminLink->more = array();
		$this->adminLink->view = "turmain";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
		
	}

}
