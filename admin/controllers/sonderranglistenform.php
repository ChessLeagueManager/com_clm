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

class CLMControllerSonderranglistenForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db	= JFactory::getDBO();
		$this->app	= JFactory::getApplication();
		
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
		$this->app->redirect( $this->adminLink->url );
	}


	function save() {
	
		$result = $this->_saveDo();   
//		$app =JFactory::getApplication();
		
		if ($result[0]) { // erfolgreich?
			
			if ($this->neu) { // neues Turnier?
				$this->app->enqueueMessage( JText::_('SP_RANKING_CREATED') );
			} else {
				$this->app->enqueueMessage( JText::_('SP_RANKING_EDITED') );
			}
		} else {
			$this->app->enqueueMessage( $result[2],$result[1] );					
		}
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die('Restricted access'); 
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'sonderranglistenform', 'TableCLM' );
		
		$post = $_POST; 
		if (!$row->bind($post)) {
			return array(false,'error',$row->getError());
		}

		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
			return array(false,'warning',JText::_('TOURNAMENT_NO_ACCESS'));
		}
		
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
			return array(false,'error',$row->getError());
		}
		$row->checkin();

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion.": ".$row->name;
		$clmLog->params = array('id' => $row->id, 'tid' => $row->turnier); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "sonderranglistenmain"; // WL in Liste
		}
	
		return array(true);
	
	}


	function cancel() {
		
		$this->adminLink->view = "sonderranglistenmain";
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
		
	}

}
