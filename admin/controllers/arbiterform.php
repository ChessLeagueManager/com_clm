<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerArbiterForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app =JFactory::getApplication();
					
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
	}


	function save() {
	
		$lang = clm_core::$lang->arbiter;
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$returnview = clm_core::$load->request_string('returnview');

		$result = $this->_saveDo();

		if ($result[0]) { // erfolgreich?
			
			if ($result[1]) { // neue Kategorie?
				$this->app->enqueueMessage( $lang->arbiter_created );
			} else {
				$this->app->enqueueMessage( $lang->arbiter_edited );
			}		
		}
		// sonst Fehlermeldung schon geschrieben

		$task = clm_core::$load->request_string('task');

		$adminLink = new AdminLink();
		// wenn 'apply', weiterleiten in form
		if ($task == 'save' OR !$result[0]) {
			// Weiterleitung in Liste
			$adminLink->view = "arbitermain"; // WL in Liste
			$adminLink->more = array('lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		} else {
			// Weiterleitung bleibt im Formular
			$adminLink->view = "arbiterform"; // WL in Liste
			$adminLink->more = array('task' => 'edit', 'id' => $result[2], 'lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		}
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			$this->app->enqueueMessage( JText::_('SECTION_NO_ACCESS'),'warning' );
			return array(false);
		}
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'arbiters', 'TableCLM' );
		
		$post = $_POST; 
		if (!$row->bind($post)) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return array(false);
		}
		
		if (!$row->checkData()) {
			// pre-save checks
			$this->app->enqueueMessage( $row->getError(),'warning' );
			// Weiterleitung bleibt im Formular !!
//			$this->adminLink->more = array('task' => $task, 'id' => $row->id);
			return array(false,false,$row->id);
		
		}
		
		// if new item, order last in appropriate group
		if (!$row->id) {
			$neu = true; // Flag für neue Kategorie
			$stringAktion = $lang->arbiter_created;
			// $where = "sid = " . (int) $row->sid; warum nur in Saison?
			$row->ordering = $row->getNextOrder(); // ( $where );
		} else {
			$neu = false;
			$stringAktion = $lang->arbiter_edited;
		}
		
		// save the changes
		if (!$row->store()) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return array(false,$neu,$row->id);
		}
		

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion.": ".$row->name.','.$row->vorname;
		$clmLog->params = array('arbiterid' => $row->id); 
		$clmLog->write();
		
		return array(true,$neu,$row->id);
	
	}


	function cancel() {
		
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$returnview = clm_core::$load->request_string('returnview');

		$adminLink = new AdminLink();
		$adminLink->view = "arbitermain";
		$adminLink->more = array('lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
		
	}

}