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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerTurRounds extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app	= JFactory::getApplication();
		
		// turnierid
		$id = clm_core::$load->request_int('id');
		
		// Register Extra tasks
		$this->registerTask( 'unpublish','publish' );
		$this->registerTask( 'unapprove','approve' );
		$this->registerTask( 'disbale','enable' );
	
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


	function assignMatches() {
	
		$result = $this->_assignMatchesDo();

		$adminLink = new AdminLink();
		$adminLink->view = "turrounds";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}
	
	
	function _assignMatchesDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );

		$db	= JFactory::getDBO();

		// Turnierdaten!
		$id = clm_core::$load->request_int('id');
		$tournament = new CLMTournament($id, true);

		if (($tournament->data->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return array(false);
		}
		
		if ($tournament->data->typ == 2) {
			// Vollturnier nur via Rundenerstellung!
			$this->app->enqueueMessage( CLMText::errortext(JText::_('MATCHES_ASSIGN'), 'IMPOSSIBLE' ),'warning' );
			return array(false);
		
		} elseif ($tournament->data->typ == 3) { // KO
			// maximal bestätige Runde holen - ist hier MIN(nr)
			$query = 'SELECT MIN(nr) FROM #__clm_turniere_rnd_termine'
				. ' WHERE turnier = '.$this->id.' AND tl_ok = 1';
			$this->_db->setQuery( $query );
			if ($tlokMin = $db->loadResult()) {
	 			$roundToDraw = $tlokMin-1;
	 		} else {
	 			$roundToDraw = $tournament->data->runden;
			}
			// nächste zu vervollständigende Runde ermittelt
			if ($roundToDraw == 0) { // dann gibt es nichts mehr zu tun
				$this->app->enqueueMessage( JText::_('NO_ROUND_LEFT'),'warning' );
				return array(false);
			}
			
			// Frage: sind in dieser Runde schon Partien angesetzt?
			$query = 'SELECT COUNT(*)'
					. ' FROM #__clm_turniere_rnd_spl'
					. ' WHERE turnier = '.$this->id.' AND runde = '.$roundToDraw.' AND ((spieler >= 1 AND gegner >= 1) OR ergebnis = 8)';
			$this->_db->setQuery($query);
			$matchesAssigned = $this->_db->loadResult();
			
			if ($matchesAssigned > 0) { // bereits Matches angelegt
				$this->app->enqueueMessage( JText::_('MATCHES_ASSIGNED_ALREADY'),'warning' );
				return array(false);
			}
			
			// OKay, jetzt kann angesetzt werden
			// alle Spieler, die 'in' sind holen
			$query = "SELECT snr "
					. " FROM #__clm_turniere_tlnr"
					. " WHERE turnier = ".$this->id." AND koStatus = '1'";
			$this->_db->setQuery($query);
			$playersIn = $db->loadAssocList('snr');
			
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
				if (!clm_core::$db->query($query)) { 
					$this->app->enqueueMessage( JText::_('MATCH: ').$m.": ".$db->getErrorMsg(),'error' );
				}

				$query = "UPDATE #__clm_turniere_rnd_spl"
						. " SET tln_nr = ".$player2.", spieler = ".$player2.", gegner = ".$player1
						. " WHERE turnier = ".$this->id." AND  runde = ".$roundToDraw." AND brett = ".$m." AND heim = '0'";
				if (!clm_core::$db->query($query)) { 
					$this->app->enqueueMessage( JText::_('MATCH: ').$m.": ".$db->getErrorMsg(),'error' );
				}

			}
										
			$this->app->enqueueMessage( JText::_('ROUND_KO_'.$roundToDraw).": ".JText::_('TOURNAMENT_MATCHES_ASSIGNED') );
	
			// Log
			$clmLog = new CLMLog();
			$clmLog->aktion = JText::_('ROUND_KO_'.$roundToDraw).": ".JText::_('TOURNAMENT_MATCHES_ASSIGNED');
			$clmLog->params = array('sid' => $tournament->data->sid, 'tid' => $this->id, 'rnd' => $roundToDraw); // TurnierID wird als LigaID gespeichert
			$clmLog->write();
			return array(true);
	
	
		} elseif ($tournament->data->typ == 1) { // CH
			$this->app->enqueueMessage( CLMText::errortext(JText::_('MATCHES_ASSIGN'), 'NOTIMPLEMENTED' ),'warning' );
			return array(false);
		
		}
	
	}
	

	function enable() {
	
		$this->_enableDo();

		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turrounds";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}
	
	function _enableDo() {

		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );

		$cid = clm_core::$load->request_array_int('cid');
		$roundID = $cid[0];

		// Instanz der Turniertabelle
		$id = clm_core::$load->request_int('id');
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $id ); // Daten zu dieser ID laden

	    $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}

		// Rundendaten holen
		$round =JTable::getInstance( 'turnier_runden', 'TableCLM' );
		$round->load( $roundID ); // Daten zu dieser ID laden

		// Runde existent?
		if (!$round->id) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'NOTEXISTING'),'warning' );
			return false;
		
		// Runde gehört zu Turnier?
		} elseif ($round->turnier != $id) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'NOACCESS'),'warning' );
			return false;
		}
		
		$task		= clm_core::$load->request_string('task');
		if ($task == 'enable') $enable = 1; else $enable = 0; // zu vergebender Wert 0/1
	
		// jetzt schreiben
		$round->abgeschlossen = $enable;
		if (!$round->store()) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return false;
		}
									   
		if ($enable) {
			$this->app->enqueueMessage( $round->name.": "." ".JText::_('ENTRY_ENABLED') );
		} else {
			$this->app->enqueueMessage( $round->name.": "." ".JText::_('ENTRY_DISABLED') );
		}
	
		// Log
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('ROUND')." ".$round->name." (ID: ".$roundID."): ".$task;
		$clmLog->params = array('tid' => $id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		return true;
	
	}
	
	
	function approve() {
	
		$this->_approveDo();

		// turnierid
		$id = clm_core::$load->request_int('id');

		$adminLink = new AdminLink();
		$adminLink->view = "turrounds";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}
	

	function _approveDo() {

		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );

		// Instanz der Turniertabelle
		$id = clm_core::$load->request_int('id');
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $id ); // Daten zu dieser ID laden

	    $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}

		$cid = clm_core::$load->request_array_int('cid');						
		$roundID = $cid[0];
	
		// Rundendaten holen
		$round =JTable::getInstance( 'turnier_runden', 'TableCLM' );
		$round->load( $roundID ); // Daten zu dieser ID laden

		// Runde existent?
		if (!$round->id) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'NOTEXISTING'),'warning' );
			return false;
		
		// Runde gehört zu Turnier?
		} elseif ($round->turnier != $id) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'NOACCESS'),'warning' );
			return false;
		}
		
		$task		= clm_core::$load->request_string('task');
		if ($task == 'approve') $approve = 1; else $approve = 0; // zu vergebender Wert 0/1
	
		// weiterer Check: Ergebnisse vollständig?
		if ($approve == 1) {
			$tournamentRound = new CLMTournamentRound($id, $cid[0]);
			if (!$tournamentRound->checkResultsComplete()) {
				$this->app->enqueueMessage( CLMText::errorText('RESULTS', 'INCOMPLETE'),'warning' );
				return false;
			}
		}
	
		// jetzt schreiben
		$round->tl_ok = $approve;
		if (!$round->store()) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return false;
		}
									   
		if ($approve) {
			$this->app->enqueueMessage( $round->name." ".JText::_('CLM_APPROVED') );
		} else {
			$this->app->enqueueMessage( $round->name." ".JText::_('CLM_UNAPPROVED') );
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
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Instanz der Turniertabelle
		$id = clm_core::$load->request_int('id');
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $id ); // Daten zu dieser ID laden

	     $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}
	
		// TODO? evtl global inconstruct anlegen
		$user 		=JFactory::getUser();
		
		$cid = clm_core::$load->request_array_int('cid');
		
		$task		= clm_core::$load->request_string( 'task' );
		$publish	= ($task == 'publish'); // zu vergebender Wert 0/1
		
		// Inhalte übergeben?
		if (empty( $cid )) { 
			
			$this->app->enqueueMessage( JText::_( 'NO_ITEM_SELECTED' ),'warning' );
		
		} else { // ja, Inhalte vorhanden
			
			$cids = implode( ',', $cid );
			$query = 'UPDATE #__clm_turniere_rnd_termine'
				. ' SET published = '.(int) $publish
				. ' WHERE id IN ( '. $cids .' )'
				. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
			if (!clm_core::$db->query($query)) { 
				$this->app->enqueueMessage( JText::_( 'DB_ERROR', true ),'warning' );
				return;
			}
												
			if ($publish) {
				$this->app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('ROUND'), JText::_('ROUNDS'))." ".JText::_('CLM_PUBLISHED') );
			} else {
				$this->app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('ROUND'), JText::_('ROUNDS'))." ".JText::_('CLM_UNPUBLISHED') );
			}
			
			// Log
			$clmLog = new CLMLog();
			$clmLog->aktion = JText::_('ROUND')." ".$row->name.": ".$task;
			$clmLog->params = array('tid' => $id); // TurnierID wird als LigaID gespeichert
			$clmLog->write();
	
		}
	
		$adminLink = new AdminLink();
		$adminLink->view = "turrounds";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	
	
	// Moves the record up one position
	function orderdown() {
		
		$this->_order(1);
		
		$adminLink = new AdminLink();
		$adminLink->view = "turrounds";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	// Moves the record down one position
	function orderup() {
		
		$this->_order(-1);
		
		$adminLink = new AdminLink();
		$adminLink->view = "turrounds";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	// Moves the order of a record
	// @param integer The direction to reorder, +1 down, -1 up
	function _order($inc) {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->id ); // Daten zu dieser ID laden

	    $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}
	
		$cid = clm_core::$load->request_array_int('cid');
		$rundenid = $cid[0];
	
		$row =JTable::getInstance( 'turnier_runden', 'TableCLM' );
		if ( !$row->load( $rundenid ) ) {
			$this->app->enqueueMessage( CLMText::errorText('ROUND', 'NOTEXISTING'),'warning' );
			return false;
		}
		$row->move( $inc, '' );
									   
		$this->app->enqueueMessage( $row->name.": ".JText::_('ORDERING_CHANGED') );
		
		return true;
		
	}

	// Saves user reordering entry
	function saveOrder() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->id ); // Daten zu dieser ID laden

	        $clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}
	
		$cid		= clm_core::$load->request_array_int('cid');
	
		$total		= count( $cid );
		$order		= clm_core::$load->request_array_int('order');
	
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
					$this->app->enqueueMessage( $db->getErrorMsg(),'error' );
				}
			}
		}
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('sid = '.(int) $group);
		}
									   
		$this->app->enqueueMessage( JText::_('NEW_ORDERING_SAVED') );
	
		$adminLink = new AdminLink();
		$adminLink->view = "turrounds";
		$adminLink->more = array('id' => $id);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	function cancel() {
		
		$adminLink = new AdminLink();
		$adminLink->more = array();
		$adminLink->view = "turmain";
		$adminLink->makeURL();		
		$this->app->redirect( $adminLink->url );
		
	}

}
