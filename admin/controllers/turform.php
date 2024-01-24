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

class CLMControllerTurForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save', 'edit' );
	
	
	}


	function edit() {
		$adminLink = new AdminLink();
			// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$adminLink->view = "turform";
			$adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$adminLink->view = "turmain"; // WL in Liste
		}
		$adminLink->makeURL();
		$app =JFactory::getApplication();
		$app->redirect( $adminLink->url );
	}


	function save() {
	
		$result = $this->_saveDo();   
		$app =JFactory::getApplication();
		$adminLink = new AdminLink();
		
		if ($result[0]) { // erfolgreich?
		
			if ($result[1]) { // neues Turnier?
				$app->enqueueMessage( JText::_('TOURNAMENT_CREATED') );
			} else {
				$app->enqueueMessage( JText::_('TOURNAMENT_EDITED') );
			}
			$adminLink->view = $result[2];
			$adminLink->more = $result[3];
		} else {
			$app->enqueueMessage( $result[2],$result[1] );					
			$adminLink->view = "turmain"; // WL in Liste
		}
		$adminLink->makeURL();
		$app->redirect( $adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		
		$post = $_POST; 
		if (!$row->bind($post)) {
			return array(false,'error',$row->getError());
		}
		
	    $clmAccess = clm_core::$access;
		if ($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) {
			return array(false,'warning',JText::_('TOURNAMENT_NO_ACCESS'));
		}
		
		// Rundenzahl berechnen!
		if ($row->typ == 2) {
			$tempTeil = $row->teil;
			if ($tempTeil%2 != 0) { // gerade machen
				$tempTeil++;
			}
			$row->runden = $tempTeil-1;
		} elseif ($row->typ == 3) {
			$row->runden = ceil(log($row->teil)/log(2));
		} elseif ($row->typ == 5) {
			$row->runden = ceil(log($row->teil)/log(2)) + 1;
		}
		
		// Parameter
		$paramsStringArray = array();
		foreach ($row->params as $key => $value) {
			if ($key == 'import_source') {
				$paramsStringArray[] = $key.'='.$value;
			} else {
				$paramsStringArray[] = $key.'='.intval($value);
			}
		}
		$row->params = implode("\n", $paramsStringArray);

		// handling dates
		if ($row->dateStart == '') $row->dateStart = '1970-01-01';
		if ($row->dateEnd == '') $row->dateEnd = '1970-01-01'; 
		if ($row->dateRegistration == '') $row->dateRegistration = '1970-01-01'; 
		if ($row->dateStart != '0000-00-00' AND $row->dateStart != '1970-01-01' AND ($row->dateEnd == '0000-00-00' OR $row->dateEnd == '1970-01-01')) $row->dateEnd = $row->dateStart;
		
		if (!$row->checkData()) {
			// pre-save checks
			// Weiterleitung bleibt im Formular !!
			$this->adminLink->more = array('task' => $task, 'id' => $row->id);
			return array(false,'warning',$row->getError());
		}
		
		// if new item, order last in appropriate group
		if (!$row->id) {
			$neu = true; // Flag für neues Turnier
			$stringAktion = JText::_('TOURNAMENT_CREATED');
			// $where = "sid = " . (int) $row->sid; warum nur in Saison?
			$row->ordering = $row->getNextOrder(); // ( $where );
		} else {
			$neu = false;
			$stringAktion = JText::_('TOURNAMENT_EDITED');
		}
		
		// save the changes
		if (!$row->store()) {
			return array(false,'error',$row->getError());
		}
				
		// bei bereits bestehendem Turnier noch calculateRanking
		if (!$neu) {
			$tournament = new CLMTournament($row->id, true);
			$tournament->calculateRanking();
			$tournament->setRankingPositions();
		}
		
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion.": ".$row->name;
		$clmLog->params = array('sid' => $row->sid, 'tid' => $row->id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		
		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$view = "turform"; // WL in Liste
			$more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$view = "turmain"; // WL in Liste
			$more = array();
		}
	
		return array(true,$neu,$view,$more);
	
	}


	function cancel() {
		
		$adminLink = new AdminLink();
		$adminLink->view = "turmain";
		$adminLink->makeURL();
		$app =JFactory::getApplication();
		$app->redirect( $adminLink->url );
		
	}

}
