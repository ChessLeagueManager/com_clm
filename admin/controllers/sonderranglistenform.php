<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSonderranglistenForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save', 'edit' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "sonderranglistenform";
	
	}


	function edit() {
			// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "sonderranglistenmain"; // WL in Liste
		}
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}


	function save() {
	
		if ($this->_saveDo()) { // erfolgreich?
			
			$app =JFactory::getApplication();
			
			if ($this->neu) { // neues Turnier?
				$app->enqueueMessage( JText::_('SP_RANKING_CREATED') );
			} else {
				$app->enqueueMessage( JText::_('SP_RANKING_EDITED') );
			}
		
		}
		// sonst Fehlermeldung schon geschrieben

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// Task
		$task = JRequest::getVar('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'sonderranglistenform', 'TableCLM' );
		
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
			//$section = 'info';
			JError::raiseWarning( 500, JText::_( 'TOURNAMENT_NO_ACCESS' ) );
			//$link = 'index.php?option='.$option.'&section='.$section;
			//$mainframe->redirect( $link);
			return false;
		}
//		if ( $row->tl !== clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true ) {
//			JError::raiseWarning(500, JText::_('SECTION_NO_ACCESS') );
//			return false;
//		}
		
		
/*		if (!$row->checkData()) {
			// pre-save checks
			JError::raiseWarning(500, $row->getError() );
			// Weiterleitung bleibt im Formular !!
			$this->adminLink->more = array('task' => $task, 'id' => $row->id);
			return false;
		
		}
*/		
		// if new item, order last in appropriate group
		if (!$row->id) {
			$this->neu = true; // Flag für neues Turnier
			$stringAktion = JText::_('SP_RANKING_CREATED');
			// $where = "sid = " . (int) $row->sid; warum nur in Saison?
			$row->ordering = $row->getNextOrder(); // ( $where );
		} else {
			$this->neu = false;
			$stringAktion = JText::_('SP_RANKING_EDITED');
		}
		
		// save the changes
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion.": ".$row->name;
		$clmLog->params = array('sid' => $row->sid, 'tid' => $row->id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		

		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "sonderranglistenmain"; // WL in Liste
		}
	
		return true;
	
	}


	function cancel() {
		
		$this->adminLink->view = "sonderranglistenmain";
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

}
