<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerTurRoundForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db	= JFactory::getDBO();
		$this->app	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		if (!isset($this->param) OR is_null($this->param)) $this->param = array();	// seit J 4.2 nötig um notice zu vermeiden
		// turnierid
		$this->param['turnierid'] = clm_core::$load->request_int('turnierid');
		
		// roundid
		$this->param['roundid'] = clm_core::$load->request_int('roundid');
	
		// task
		$this->task = clm_core::$load->request_string('task');
	
		// Weiterleitung
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turroundform";
		$this->adminLink->more = array('turnierid' => $this->param['turnierid'], 'roundid' => $this->param['roundid']);
		// Default-Ziel zB bei Eingabefehler
	
	}


	function save() {
	
		$this->_saveDo();
	
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load($this->param['turnierid']);

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {																						   
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return false;
		}
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turnier_runden', 'TableCLM' );
		$row->load($this->param['roundid']);
	
		// bind
		$post = $_POST; 
		if (!$row->bind($post)) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return false;
		}
		// check
		if (!$row->checkData()) {
			$this->app->enqueueMessage( $row->getError(),'warning' );
			return false;
		}
			// save the changes
		if (!$row->store()) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return false;
		}
		
	
		if ($row->startzeit != '00:00') {
			$db			=JFactory::getDBO();
			$startzeit 	= $row->startzeit.':00';
			$query = " UPDATE #__clm_turniere_rnd_termine "
				." SET startzeit = '".$startzeit."' "
				." WHERE turnier = ".$row->turnier
				." AND sid = ".$row->sid
				." AND startzeit = '00:00:00' "
			;
//			$db->setQuery($query);
//			$db->query();
			clm_core::$db->query($query);
		}
		if ($this->task == 'apply') {
			$this->adminLink->view = "turroundform";
			$this->adminLink->more = array('turnierid' => $this->param['turnierid'], 'roundid' => $this->param['roundid']);
		} else {
			$this->adminLink->view = "turrounds";
			$this->adminLink->more = array('id' => $this->param['turnierid']);
		}
		
		$stringAktion = JText::_('ROUND_EDITED');
		
		$this->app->enqueueMessage($stringAktion);
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion;
		$clmLog->params = array('tid' => $this->param['turnierid']); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
			
		return true;
	
	}


	function cancel() {
		
		$this->adminLink->view = "turrounds";
		$this->adminLink->more = array('id' => $this->param['turnierid']);
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
		
	}

}
