<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerConfig extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "config";
	
	}


	function save() {
	
		if ($this->_saveDo()) { // erfolgreich?
			
			$app =JFactory::getApplication();
			$app->enqueueMessage( JText::_('CONFIG_SAVED') );
		
		}
		// sonst Fehlermeldung schon geschrieben

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
	        $clmAccess = clm_core::$access;
		if($clmAccess->access('BE_config_general') === false) {
			JError::raiseWarning(500, JText::_('SECTION_NO_ACCESS') );
			return false;
		}
	
		// Task
		$task = JRequest::getVar('task');
		
		
		// Joomla-Version ermitteln
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if (substr_count($joomlaVersion, '1.5')) {
			$table = JTable::getInstance('component');
			if (!$table->loadByOption('com_clm')) {
				JError::raiseWarning(500, 'Not a valid component');
				return false;
			}
		} else { // in 1.6 ist loadByOption abgeschafft!
			$table = JTable::getInstance('Asset');
			if (!$table->loadByName('com_clm')) {
				JError::raiseWarning(500, 'Not a valid component');
				return false;
			}
		}
		
		$post = JRequest::get('post');
		$table->bind($post);

		//-- Pre-save checks
		if (!$table->check()) {
			JError::raiseWarning(500, $table->getError());
			return false;
		}
		
		//-- Save the changes
		if (!$table->store()){
			JError::raiseWarning(500, $table->getError());
			return false;
		}		

		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->view = "config";
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "";
			$this->adminLink->more = array("section" => "info");
		}
	
		return true;
	
	}

	
	function cancel() {
		
		$this->adminLink->view = "";
		$this->adminLink->more = array("section" => "info");
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

}
