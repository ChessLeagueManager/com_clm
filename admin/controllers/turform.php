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

class CLMControllerTurForm extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save', 'edit' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turform";
	
	}


	function edit() {
			// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "turmain"; // WL in Liste
		}
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}


	function save() {
	
		if ($this->_saveDo()) { // erfolgreich?
			
			$app =JFactory::getApplication();
			
			if ($this->neu) { // neues Turnier?
				$app->enqueueMessage( JText::_('TOURNAMENT_CREATED') );
			} else {
				$app->enqueueMessage( JText::_('TOURNAMENT_EDITED') );
			}
		
		}
		// sonst Fehlermeldung schon geschrieben

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// Task
		$task = JRequest::getVar('task');
		
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		
	        $clmAccess = clm_core::$access;
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		if ($row->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return false;
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
			$paramsStringArray[] = $key.'='.intval($value);
		}
		$row->params = implode("\n", $paramsStringArray);
		
		
		if (!$row->checkData()) {
			// pre-save checks
			JError::raiseWarning(500, $row->getError() );
			// Weiterleitung bleibt im Formular !!
			$this->adminLink->more = array('task' => $task, 'id' => $row->id);
			return false;
		
		}
		
		// if new item, order last in appropriate group
		if (!$row->id) {
			$this->neu = true; // Flag für neues Turnier
			$stringAktion = JText::_('TOURNAMENT_CREATED');
			// $where = "sid = " . (int) $row->sid; warum nur in Saison?
			$row->ordering = $row->getNextOrder(); // ( $where );
		} else {
			$this->neu = false;
			$stringAktion = JText::_('TOURNAMENT_EDITED');
		}
		
		// save the changes
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		

		
		// bei bereits bestehendem Turnier noch calculateRanking
		if (!$this->neu) {
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
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "turmain"; // WL in Liste
		}
	
		return true;
	
	}


	function cancel() {
		
		$this->adminLink->view = "turmain";
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

}
