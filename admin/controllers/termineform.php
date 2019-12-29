<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
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
	
		$result = $this->_saveDo();
		$app =JFactory::getApplication();
		
		if ($result[0]) { // erfolgreich?
			
			
			if ($this->neu) { // neues termine?
				$app->enqueueMessage( JText::_('TERMINE_TASK_CREATED') );
			} else {
				$app->enqueueMessage( JText::_('TERMINE_TASK_EDITED') );
			}
		
		} else {
			$app->enqueueMessage( $result[2],$result[1] );					
		}
		$this->adminLink->makeURL();
		$app->redirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Task
		$task = clm_core::$load->request_string('task', '');
		// Instanz der Tabelle
		$row = JTable::getInstance( 'termine', 'TableCLM' );
		
	
		$post = $_POST; 
		if (!$row->bind($post)) {
			return array(false,'error',$row->getError());
		} elseif (!$row->checkData()) {
			// pre-save checks
			$this->adminLink->more = array('task' => $task, 'id' => $row->id);
			return array(false,'warning',$row->getError());
		
		}
		if ($row->startdate == '') $row->startdate = '1970-01-01';
		if ($row->enddate == '') $row->enddate = '1970-01-01';
		
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
		if ($row->startdate != '0000-00-00' AND $row->startdate != '1970-01-01' AND ($row->enddate == '0000-00-00' OR $row->enddate == '1970-01-01')) $row->enddate = $row->startdate;
		
		// save the changes
		if (!$row->store()) {
			return array(false,'error',$row->getError());
		}
		


		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "terminemain"; // WL in Liste
		}
	
		return array(true);
	
	}


	function cancel() {
		
		$this->adminLink->view = "terminemain";
		$this->adminLink->makeURL();
		$app = JFactory::getApplication();
		$app->redirect( $this->adminLink->url );
		
	}

}
