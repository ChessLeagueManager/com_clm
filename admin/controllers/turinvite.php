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

class CLMControllerTurInvite extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
		
		// turnierid
		$this->id = JRequest::getInt('id');
		$clmAccess = clm_core::$access;      
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load($this->id);
		echo "<br>invrow: "; var_dump($row);
		//$tournament = new CLMTournament($this->id, true);
		//die('    tinvite');
		//if (!$tournament->checkAccess(0,0,$row->tl)) {
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) AND ($clmAccess->access('BE_tournament_edit_detail') !== true)) {
			JError::raiseWarning( 500, JText::_('TOURNAMENT_NO_ACCESS') );
			$this->adminLink = new AdminLink();
			$this->adminLink->view = "turmain";
			$this->adminLink->makeURL();
			$this->setRedirect( $this->adminLink->url );
		}

		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turinvite";
		$this->adminLink->more = array('id' => $this->id);
	
	}


	function save() {
	
		$this->app =JFactory::getApplication();
		
		if ($this->_saveDo()) {
	
	
			if (JRequest::getVar('task') == 'save') {
				$this->adminLink->more = array('id' => $this->id);
				$this->adminLink->view = "turmain";
			}
		
		}
		
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $this->id ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {
		//if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
		}
	
		$invitationText = JRequest::getVar('invitationText', '', 'post', 'string', JREQUEST_ALLOWRAW);
	

		$query = "UPDATE #__clm_turniere"
					. " SET invitationText = ".$this->_db->Quote($invitationText)
					. " WHERE id = ".$this->id
					;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			JError::raiseWarning(500, JText::_('DB_ERROR') );
			return false;
		}
		$this->app->enqueueMessage( JText::_('INVITATION_EDITED') );

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('INVITATION_EDITED');
		$clmLog->params = array('tid' => $this->id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
		return true;
	
	}


	function cancel() {
		
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->view = "turmain";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
		
	}

}
