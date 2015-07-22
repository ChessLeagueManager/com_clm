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

class CLMControllerTurPlayerEdit extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		// turnierid
		$this->playerid = JRequest::getInt('playerid');
		$this->turnierid = JRequest::getInt('turnierid');
		
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
		$this->setRedirect( $this->adminLink->url );
	
	}
	
	
	function _saveDo() {
	
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->turnierid ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		// Task
		$task = JRequest::getVar('task');
		
		// Instanz der Tabelle
		$row = & JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		$row->load( $this->playerid ); // Daten zu dieser ID laden

		// Spieler existent?
		if (!$row->id) {
			JError::raiseWarning( 500, CLMText::errorText('PLAYER', 'NOTEXISTING') );
			return false;
		
		// Runde gehört zu Turnier?
		} elseif ($row->turnier != $this->turnierid) {
			JError::raiseWarning( 500, CLMText::errorText('PLAYER', 'NOACCESS') );
			return false;
		}
		
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		if (!$row->check(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
	
		
	 	clm_core::$api->direct("db_tournament_delDWZ",array($this->turnierid,false));

		$text = JText::_('PARTICIPANT_EDITED').": ".$row->name;

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $text;
		$clmLog->params = array('sid' => $row->sid, 'tid' => $this->turnierid); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		$app =JFactory::getApplication();
		$app->enqueueMessage( $text );

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
		$this->setRedirect( $this->adminLink->url );
		
	}

}
