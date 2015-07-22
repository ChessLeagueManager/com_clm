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

jimport( 'joomla.application.component.controller' );

class CLMControllerAccessgroupsForm extends JController {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= & JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "accessgroupsform";
	
	}


	function save() {

		if ($this->_saveDo()) { // erfolgreich?
			
			$app =& JFactory::getApplication();
			
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

		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		//if (CLM_usertype != 'admin') {
		$clmAccess->accesspoint = 'BE_accessgroup_general';
		if($clmAccess->access() === false) {
			JError::raiseWarning(500, JText::_('SECTION_NO_ACCESS') );
			return false;
		}
	
		// Task
		$task = JRequest::getVar('task');
		// Instanz der Tabelle
		$row = & JTable::getInstance( 'accessgroupsform', 'TableCLM' );
		
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		
		// Parameter
		$paramsStringArray = array();
		foreach ($row->be_params as $key => $value) {
			$paramsStringArray[] = $key.'='.intval($value);
		}
		$row->be_params = implode("\n", $paramsStringArray);
		
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
		$row->checkin();		

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion.": ".$row->name;
		$clmLog->params = array('sid' => CLM_SEASON, 'cids' => $row->usertype); 
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