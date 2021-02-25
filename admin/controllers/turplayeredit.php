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

class CLMControllerTurPlayerEdit extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		// turnierid
		$this->playerid = clm_core::$load->request_int('playerid');
		$this->turnierid = clm_core::$load->request_int('turnierid');
		
		$this->_db		= JFactory::getDBO();
		
		$this->app =JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->more = array('playerid' => $this->playerid);
		$this->adminLink->view = "turplayeredit";
	
	}

	
	
	function save() {
	
		$this->_saveDo();

		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
	
	}
	
	
	function _saveDo() {
	
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->turnierid ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->app->enqueueMessage(JText::_('TOURNAMENT_NO_ACCESS'),'warning');
			return false;
		}
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		$row->load( $this->playerid ); // Daten zu dieser ID laden

		// Spieler existent?
		if (!$row->id) {
			$this->app->enqueueMessage(CLMText::errorText('PLAYER', 'NOTEXISTING'),'warning');
			return false;
		
		// Runde gehört zu Turnier?
		} elseif ($row->turnier != $this->turnierid) {
			$this->app->enqueueMessage(CLMText::errorText('PLAYER', 'NOACCESS'),'warning');
			return false;
		}
		
		$post = $_POST; 
		if (!$row->bind($post)) {
			$this->app->enqueueMessage($row->getError(),'error');
			return false;
		}
		if ($row->start_dwz == '') $row->start_dwz = 0;
		if ($row->start_I0 == '') $row->start_I0 = 0;
		if ($row->sum_punkte == '') $row->sum_punkte = 0;
		if ($row->sumTiebr1 == '') $row->sumTiebr1 = 0;
		if (!$row->check($post)) {
			$this->app->enqueueMessage($row->getError(),'error');
			return false;
		}
		if (!$row->store()) {
			$this->app->enqueueMessage($row->getError(),'error');
			return false;
		}
	
		
	 	clm_core::$api->direct("db_tournament_delDWZ",array($this->turnierid,false));

		$text = JText::_('PARTICIPANT_EDITED').": ".$row->name;

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $text;
		$clmLog->params = array('sid' => $row->sid, 'tid' => $this->turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		$this->app->enqueueMessage( $text );

		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('playerid' => $this->playerid);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->more = array('id' => $this->turnierid);
			$this->adminLink->view = "turplayers"; // WL in Liste
		}
		return true;
	
	}

	function cancel() {
		
		$this->adminLink->view = "turplayers";
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
		
	}

}
