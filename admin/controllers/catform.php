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

class CLMControllerCatForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= JFactory::getDBO();
		$this->app =JFactory::getApplication();
					
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "catform";
	
	}


	function save() {
	
		if ($this->_saveDo()) { // erfolgreich?
			
			if ($this->neu) { // neue Kategorie?
				$this->app->enqueueMessage( JText::_('CATEGORY_CREATED') );
			} else {
				$this->app->enqueueMessage( JText::_('CATEGORY_EDITED') );
			}
		
		}
		// sonst Fehlermeldung schon geschrieben

		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			$this->app->enqueueMessage( JText::_('SECTION_NO_ACCESS'),'warning' );
			return false;
		}
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'categories', 'TableCLM' );
		
		$post = $_POST; 
		if (!$row->bind($post)) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return false;
		}
		if ($row->dateStart == '') $row->dateStart = '1970-01-01';
		if ($row->dateEnd == '') $row->dateEnd = '1970-01-01';		
		// Parameter
		$paramsStringArray = array();
		foreach ($row->params as $key => $value) {
			$paramsStringArray[] = $key.'='.intval($value);
		}
		$row->params = implode("\n", $paramsStringArray);
		
		
		if (!$row->checkData()) {
			// pre-save checks
			$this->app->enqueueMessage( $row->getError(),'warning' );
			// Weiterleitung bleibt im Formular !!
			$this->adminLink->more = array('task' => $task, 'id' => $row->id);
			return false;
		
		}
		
		// if new item, order last in appropriate group
		if (!$row->id) {
			$this->neu = true; // Flag für neue Kategorie
			$stringAktion = JText::_('CATEGORY_CREATED');
			// $where = "sid = " . (int) $row->sid; warum nur in Saison?
			$row->ordering = $row->getNextOrder(); // ( $where );
		} else {
			$this->neu = false;
			$stringAktion = JText::_('CATEGORY_EDITED');
		}
		
		// save the changes
		if (!$row->store()) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return false;
		}
		

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion.": ".$row->name;
		$clmLog->params = array('catid' => $row->id); 
		$clmLog->write();
		

		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "catmain"; // WL in Liste
		}
	
		return true;
	
	}


	function cancel() {
		
		$this->adminLink->view = "catmain";
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
		
	}

}