<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
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
		
		$this->app	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save', 'edit' );
	
	}


	function save() {
	
		$result = $this->_saveDo();   
		
		if ($result[0]) { // erfolgreich?
			
			if ($result[1]) { // neues Turnier?
				$this->app->enqueueMessage( JText::_('SP_RANKING_CREATED') );
			} else {
				$this->app->enqueueMessage( JText::_('SP_RANKING_EDITED') );
			}
		} else {
			$this->app->enqueueMessage( $result[2],$result[1] );					
		}

		// Task
		$task = clm_core::$load->request_string('task');

		$adminLink = new AdminLink();
		// wenn 'apply', weiterleiten in form
		if ($task == 'apply' AND $result[0]) {
			// Weiterleitung bleibt im Formular
			$adminLink->view = "sonderranglistenform";
			$adminLink->more = array('task' => 'edit', 'id' => $result[2]);
		} else {
			// Weiterleitung in Liste
			$adminLink->view = "sonderranglistenmain"; // WL in Liste
		}
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
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
			$neu = true; // Flag für neues Turnier
			$stringAktion = JText::_('SP_RANKING_CREATED');
			// $where = "sid = " . (int) $row->sid; warum nur in Saison?
			$row->ordering = $row->getNextOrder(); // ( $where );
		} else {
			$neu = false;
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
		
		return array(true,$neu,$row->id);
	
	}


	function cancel() {
		
		$adminLink = new AdminLink();
		$adminLink->view = "sonderranglistenmain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
		
	}

}
