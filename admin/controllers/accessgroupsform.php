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

class CLMControllerAccessgroupsForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "accessgroupsform";
	
	}


	function save() {

		if ($this->_saveDo()) { // erfolgreich?
			
			$app =JFactory::getApplication();
			
			if ($this->neu) { // new access group?
				$app->enqueueMessage( JText::_('ACCESSGROUP_CREATED') );
			} else {
				$app->enqueueMessage( JText::_('ACCESSGROUP_EDITED') );
			}
		
		}
		// sonst Fehlermeldung schon geschrieben

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$clmAccess = clm_core::$access;      

		if($clmAccess->access('BE_accessgroup_general') === false) {
			JError::raiseWarning(500, JText::_('SECTION_NO_ACCESS') );
			return false;
		}
	
		// Task
		$task = JRequest::getVar('task');
		// Instanz der Tabelle
		$row = JTable::getInstance( 'accessgroupsform', 'TableCLM' );
		
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		
		// Parameter
		$paramsStringArray = array();
		foreach ($row->params as $key => $value) {
			if(intval($value)>0){
			$paramsStringArray[] = $key.'='.intval($value);
	   	}
		}
		$row->params = implode("\n", $paramsStringArray);
		
		if (!$row->checkData()) {
			// pre-save checks
			JError::raiseWarning(500, $row->getError() );
			// Weiterleitung bleibt im Formular !!
			$this->adminLink->more = array('task' => $task, 'id' => $row->id, 'row' => $row);
			return false;
		}
		
		// if new item, order last in appropriate group
		if (!$row->id) {
			$this->neu = true; // Flag für neue accessgruppe
			$stringAktion = JText::_('ACCESSGROUP_CREATED');
			// $where = "sid = " . (int) $row->sid; warum nur in Saison?
			$row->ordering = $row->getNextOrder(); // ( $where );
		} else {
			$this->neu = false;
			$stringAktion = JText::_('ACCESSGROUP_EDITED');
		}
		
		// save the changes
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
				

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion.": ".$row->name;
		$clmLog->params = array('sid' => clm_core::$access->getSeason(), 'cids' => $row->usertype); 
		$clmLog->write();
		
		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "accessgroupsmain"; // WL in Liste
		}
	
		return true;
	
	}


	function cancel() {
		
		$this->adminLink->view = "accessgroupsmain";
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

}
