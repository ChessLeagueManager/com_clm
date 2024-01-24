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

class CLMControllerTurInvite extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app 	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
		
		// turnierid
		$id = clm_core::$load->request_int('id');
		$clmAccess = clm_core::$access;      
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load($id);
		//$tournament = new CLMTournament($this->id, true);
		//die('    tinvite');
		//if (!$tournament->checkAccess(0,0,$row->tl)) {
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) AND ($clmAccess->access('BE_tournament_edit_detail') !== true)) {
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'), 'warning' );
			$adminLink = new AdminLink();
			$adminLink->view = "turmain";
			$adminLink->makeURL();
			$this->app->redirect( $this->adminLink->url );
		}

		$adminLink = new AdminLink();
		$adminLink->view = "turinvite";
		$adminLink->more = array('id' => $id);
	
	}


	function save() {
			
		// turnierid
		$id = clm_core::$load->request_int('id');
		$adminLink = new AdminLink();
		$adminLink->view = "turinvite";
		$adminLink->more = array('id' => $id);

		if ($this->_saveDo()) {		
			if (clm_core::$load->request_string('task') == 'save') {
				$adminLink->more = array('id' => $id);
				$adminLink->view = "turmain";
			}
		}
		
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// turnierid
		$id = clm_core::$load->request_int('id');
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $id ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;      
		if (($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === false) {																						   
			$this->app->enqueueMessage( JText::_('TOURNAMENT_NO_ACCESS'), 'warning' );
			return false;
		}

		$db			= JFactory::getDBO();
	
//		$invitationText = clm_core::$load->request_string('invitationText');	
		$invitationText = $_POST["invitationText"];	
		$query = "UPDATE #__clm_turniere"
					. " SET invitationText = ".$db->Quote($invitationText)
					. " WHERE id = ".$id
					;
								 
								
		if (!clm_core::$db->query($query)) { 
			$this->app->enqueueMessage( JText::_('DB_ERROR'), 'warning' );
			return false;
		}
		$this->app->enqueueMessage( JText::_('INVITATION_EDITED'), 'message' );

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('INVITATION_EDITED');
		$clmLog->params = array('tid' => $id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
	
		return true;
	
	}


	function cancel() {
		
		$adminLink = new AdminLink();
		$adminLink->more = array('id' => $id);
		$adminLink->view = "turmain";
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
		
	}

}
