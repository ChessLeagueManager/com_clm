<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerTurTeams extends JControllerLegacy 
{
	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app 	= JFactory::getApplication();
			
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );

	}

	
	function save() {
	
		$result = $this->_saveDo();   
		$app =JFactory::getApplication();
		
		if ($result[0]) { // erfolgreich?		
			$app->enqueueMessage( JText::_('TOURNAMENT_TEAMS_EDITED') );
		} else {
			$app->enqueueMessage( $result[2],$result[1] );					
		}

		// Task
		$task = clm_core::$load->request_string('task');
		$tid = clm_core::$load->request_string('tid');

		$adminLink = new AdminLink();
		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$adminLink->view = "turteams"; // WL in Liste
			$adminLink->more = array('turnierid' => $tid);
		} else {
			// Weiterleitung in Liste
			$adminLink->view = "turplayers"; // WL in Liste
			$adminLink->more = array('id' => $tid);
		}
		$adminLink->makeURL();
		$app->redirect( $adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
		// Task
		$task = clm_core::$load->request_string('task');
		$tid = clm_core::$load->request_string('tid');
		$sid = clm_core::$load->request_string('sid');
		
		
		$query	= "DELETE FROM #__clm_turniere_teams"
			. " WHERE tid = ".$tid;
		clm_core::$db->query($query);	
		
		for ($y=1; $y< 100; $y++){
			$tln_nr	= clm_core::$load->request_int( 'tln_nr'.$y, 100);
			$name	= clm_core::$load->request_string( 'name'.$y);
			if ($tln_nr == 100) break;
			if ($tln_nr == 0) continue;
			$query	= "REPLACE INTO #__clm_turniere_teams"
				." ( `tid`, `name`, `sid`, `tln_nr`, `zps`, `man_nr`, `published`) "
				. " VALUES (".$tid.",'".$name."',".$sid.",".$tln_nr.", NULL, NULL, 1 )";
			clm_core::$db->query($query);	
		}
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('TOURNAMENT_TEAMS_EDITED');
		$clmLog->params = array('sid' => $sid, 'tid' => $tid); 
		$clmLog->write();
		
		return array(true);
	
	}
	
	

	function cancel() {
		
		$tid = clm_core::$load->request_string('tid');

		$adminLink = new AdminLink();
		$adminLink->view = "turplayers";
		$adminLink->more = array('id' => $tid);
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
		
	}

}
