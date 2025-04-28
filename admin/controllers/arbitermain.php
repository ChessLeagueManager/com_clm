<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerArbiterMain extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'unpublish','publish' );
	
	}

	// Weiterleitung!
	function add() {
		
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$returnview = clm_core::$load->request_string('returnview');

		$adminLink = new AdminLink();
		$adminLink->view = "arbiterform";
		$adminLink->more = array('lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}


	// Weiterleitung!
	function edit() {
		
		$cid	= clm_core::$load->request_array_int('cid');
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$returnview = clm_core::$load->request_string('returnview');
		
		$adminLink = new AdminLink();
		$adminLink->view = "arbiterform";
		$adminLink->more = array('task' => 'edit', 'id' => $cid[0], 'lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}


	function publish() {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		$lang = clm_core::$lang->arbiter;

		// TODO? evtl global inconstruct anlegen
		$user 		=JFactory::getUser();
		
		$cid		= clm_core::$load->request_array_int('cid');
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$returnview = clm_core::$load->request_string('returnview');
		
		$task		= clm_core::$load->request_string( 'task' );
		$publish	= ($task == 'publish'); // zu vergebender Wert 0/1
		
		// Inhalte übergeben?
		if (empty( $cid )) { 
			$this->app->enqueueMessage( JText::_( 'NO_ITEM_SELECTED' ),'warning' );
		
		} else { // ja, Inhalte vorhanden
			
			// erst jetzt alle Einträge durchgehen
			foreach ($cid as $key => $value) {
		
				// load the row from the db table
				$row =JTable::getInstance( 'arbiters', 'TableCLM' );
				$row->load( $value ); // Daten zu dieser ID laden
		
					// Berechtigung vorhanden
					
					// Änderung nötig?
					if ($row->published != $publish) {
						// Log
						$clmLog = new CLMLog();
						$clmLog->aktion = $lang->arbiter." ".$row->fideid.": ".$task;
						$clmLog->params = array('arbiterid' => $value, 'fideid' => $row->fideid);
						$clmLog->write();
						// Log geschrieben - Änderungen später
					} else {
						unset($cid[$key]);
					}
		
			} 
			// alle Einträge geprüft
		
			// immer noch Einträge vorhanden?
			if ( !empty($cid) ) { 
		
				$row =JTable::getInstance( 'arbiters', 'TableCLM' );
				$row->publish( $cid, $publish );
			
				// Meldung erstellen
				if ($publish) {
					$this->app->enqueueMessage( CLMText::sgpl(count($cid), $lang->arbiter, $lang->arbiter)." ".JText::_('CLM_PUBLISHED') );
				} else {
					$this->app->enqueueMessage( CLMText::sgpl(count($cid), $lang->arbiter, $lang->arbiter)." ".JText::_('CLM_UNPUBLISHED') );
				}
			
			} else {
			
				$this->app->enqueueMessage(JText::_('NO_CHANGES'));
			
			}
	
		}
	
		$adminLink = new AdminLink();
		$adminLink->view = "arbitermain";
		$adminLink->more = array('lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	
	
	/**
	* Container für Löschung
	*
	*/
	function delete() {

		$this->_deleteDo();

		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$returnview = clm_core::$load->request_string('returnview');

		$adminLink = new AdminLink();
		$adminLink->view = "arbitermain";
		$adminLink->more = array('lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );

	}
	
	
	/* eigentliche Lösch-Funktion
	*
	*/
	function _deleteDo() {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );

		$lang = clm_core::$lang->arbiter;

		$task		= clm_core::$load->request_string( 'task' );
		$cid = clm_core::$load->request_array_int('cid');
		// vorerst nur eine markiertes  übernehmen // später über foreach mehrere?
		$arbiterid = $cid[0];
		
		
		// Daten laden
		$row =JTable::getInstance( 'arbiters', 'TableCLM' );
		$row->load( $arbiterid );
		
		// falls Cat existent?
		if ( !$row->load( $arbiterid ) ) {
			$this->app->enqueueMessage( CLMText::errorText('ARBITER', 'NOTEXISTING'),'warning' );
			return false;
		
		}
		
	
		// Arbiter löschen
		$query = " DELETE FROM #__clm_arbiter "
			." WHERE id = ".$arbiterid
			;
		if (!clm_core::$db->query($query)) { 
			$this->app->enqueueMessage( $this->_db->getErrorMsg(),'error' );
			return false;
		}	
		
		// Zuordnungen löschen
		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE fideid = ".$row->fideid
			;
		if (!clm_core::$db->query($query)) { 
			$this->app->enqueueMessage( $this->_db->getErrorMsg(),'error' );
			return false;
		}
		$anz = clm_core::$db->affected_rows();
		
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $lang->arbiter." ".$row->fideid.": ".$task;
		$clmLog->params = array('arbiterid' => $arbiterid, 'fideid' => $row->fideid, 'anz' => $anz);
		$clmLog->write();
		
		
		// Message
		$this->app->enqueueMessage( $lang->arbiter_deleted." - ".$lang->fideid.": ".$row->fideid );
		
		return true;
		
	}
	
	
	// Moves the record up one position
	function orderdown() {
		
		$this->_order(1);
		
		$adminLink = new AdminLink();
		$adminLink->view = "arbitermain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	// Moves the record down one position
	function orderup() {
		
		$this->_order(-1);
		
		$adminLink = new AdminLink();
		$adminLink->view = "arbitermain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	// Moves the order of a record
	// @param integer The direction to reorder, +1 down, -1 up
	function _order($inc) {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		$cid = clm_core::$load->request_array_int('cid');
		$arbiterid = $cid[0];
	
		$row =JTable::getInstance( 'arbiters', 'TableCLM' );
		if ( !$row->load( (int)$arbiterid ) ) {
			$this->app->enqueueMessage( CLMText::errorText('ARBITER', 'NOTEXISTING'),'warning' );
			return false;
		}
//		$row->move( $inc, '' );
		$row->move($inc, '');
		$row->reorder();
	
		$this->app->enqueueMessage( $row->name.": ".JText::_('ORDERING_CHANGED') );
		
		return true;
		
	}

	// Saves user reordering entry
	function saveOrder() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
	
		$cid		= clm_core::$load->request_array_int('cid');
	
		$total		= count( $cid );
		$order		= clm_core::$load->request_array_int('order');
	
		$row =JTable::getInstance( 'arbiteres', 'TableCLM' );
		$groupings = array();
	
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->sid;
	
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
		$adminLink->view = "arbitermain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	// zurük
	function back() {
		
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$id = clm_core::$load->request_int('id');
		$returnview = clm_core::$load->request_string('returnview');
		
		$adminLink = new AdminLink();
		$adminLink->view = 'arbiterassign';
		$adminLink->more = array('lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}

}