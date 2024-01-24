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

class CLMControllerTurRoundForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
	}


	function save() {
	
		// turnierid
		$turnierid = clm_core::$load->request_int('turnierid');		
		// roundid
		$roundid = clm_core::$load->request_int('roundid');
		// Task
		$task = clm_core::$load->request_string('task');

		$result = $this->_saveDo();
	
		$adminLink = new AdminLink();
		if ($task == 'apply' AND $result[0]) {
			$adminLink->view = "turroundform";
			$adminLink->more = array('turnierid' => $turnierid, 'roundid' => $roundid);
		} else {
			$adminLink->view = "turrounds";
			$adminLink->more = array('id' => $turnierid);
		}
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// turnierid
		$turnierid = clm_core::$load->request_int('turnierid');		
		// roundid
		$roundid = clm_core::$load->request_int('roundid');

		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load($turnierid);

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true) OR $clmAccess->access('BE_tournament_edit_round') === false) {																						   
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'),'warning' );
			return array(false);
		}
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turnier_runden', 'TableCLM' );
		$row->load($roundid);
	
		// bind
		$post = $_POST; 
		if (!$row->bind($post)) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return array(false);
		}
		// check
		if (!$row->checkData()) {
			$this->app->enqueueMessage( $row->getError(),'warning' );
			return array(false);
		}
			// save the changes
		if (!$row->store()) {
			$this->app->enqueueMessage( $row->getError(),'error' );
			return array(false);
		}
		
	
		if ($row->startzeit != '00:00') {
			$startzeit 	= $row->startzeit.':00';
			$query = " UPDATE #__clm_turniere_rnd_termine "
				." SET startzeit = '".$startzeit."' "
				." WHERE turnier = ".$row->turnier
				." AND sid = ".$row->sid
				." AND startzeit = '00:00:00' "
			;
			clm_core::$db->query($query);
		}
		$stringAktion = JText::_('ROUND_EDITED');
		
		$this->app->enqueueMessage($stringAktion);
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion;
		$clmLog->params = array('tid' => $turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
			
		return array(true);
	
	}


	function cancel() {
		
		// turnierid
		$turnierid = clm_core::$load->request_int('turnierid');		

		$adminLink = new AdminLink();
		$adminLink->view = "turrounds";
		$adminLink->more = array('id' => $turnierid);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
		
	}

}
