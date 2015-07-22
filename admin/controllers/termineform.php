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

class CLMControllerTermineForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "termineform";
	
	}


	function save() {
	
		if ($this->_saveDo()) { // erfolgreich?
			
			$app =JFactory::getApplication();
			
			if ($this->neu) { // neues termine?
				$app->enqueueMessage( JText::_('TERMINE_TASK_CREATED') );
			} else {
				$app->enqueueMessage( JText::_('TERMINE_TASK_EDITED') );
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
		$row = JTable::getInstance( 'termine', 'TableCLM' );
		
	
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
			return false;
		
		} elseif (!$row->checkData()) {
			// pre-save checks
			JError::raiseWarning(500, $row->getError() );
			// Weiterleitung bleibt im Formular !!
			$this->adminLink->more = array('task' => $task, 'id' => $row->id);
			return false;
		
		}
		
		// if new item, order last in appropriate group
		if (!$row->id) {
			$this->neu = true; // Flag für neues termine
			$stringAktion = JText::_('TERMINE_TASK_CREATED');
			$row->ordering = $row->getNextOrder(); // ( $where );
		} else {
			$this->neu = false;
			$stringAktion = JText::_('TERMINE_TASK_EDITED');
		}
		
		// handling checkboxes
		if ($row->allday === 0) $row->allday = 0; else $row->allday = 1;
		if ($row->noendtime === 0) $row->noendtime = 0; else $row->noendtime = 1;
		
		// handling dates
		if ($row->startdate != '0000-00-00' AND $row->enddate == '0000-00-00') $row->enddate = $row->startdate;
		
		// save the changes
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
	
	
		


		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "terminemain"; // WL in Liste
		}
	
		return true;
	
	}


	function cancel() {
		
		$this->adminLink->view = "terminemain";
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

}