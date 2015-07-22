<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerAccessgroupsMain extends JControllerLegacy {

	function __construct() {
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		JRequest::setVar('view','accessgroupsmain');
		parent::display(); 
	} 
	
	function add() { 
		JRequest::setVar( 'view', 'accessgroupsform' ); 
		JRequest::setVar( 'hidemainmenu', 1 ); 
		parent::display(); 
	}
	
	function edit() { 
		JRequest::setVar( 'view', 'accessgroupsform' ); 
		JRequest::setVar( 'hidemainmenu', 1 ); 
		parent::display(); 
	}
	function copy() { 
		$model = $this->getModel('accessgroupsform'); 
		if ($model->copy()) { 
			$msg = 'Kopieren war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Kopieren'; 
		} 
		$this->setRedirect('index.php?option=com_clm&view=accessgroupsmain',$msg); 
	} 
	
	function save() { 
		$model = $this->getModel('accessgroupsform'); 
		if ($model->store()) { 
			$msg = 'Speichern war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Speichern'; 
		} 
		$this->setRedirect('index.php?option=com_clm&view=accessgroupsmain',$msg); 
	} 
	
	function remove() { 
		$model = $this->getModel('accessgroupsform'); 
		if($model->delete()) { 
			$msg = 'Löschen war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Löschen'; 
		} 
		$this->setRedirect('index.php?option=com_clm&view=accessgroupsmain',$msg); 
	}

	function publish() {
		$model = $this->getModel('accessgroupsform');
		if($model->publish()) {
			$msg = 'Freigeben war erfolgreich';
		} else {
			$msg = 'Fehler beim freigeben';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=accessgroupsmain', $msg ); 
		
	}
	
	function unpublish() {
		$model = $this->getModel('accessgroupsform');
		if($model->unpublish()) {
			$msg = 'Sperren war erfolgreich';
		} else {
			$msg = 'Fehler beim sperren';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=accessgroupsmain', $msg ); 
		
	}
	
	function saveOrder() {
		$model = $this->getModel('accessgroupsform');
		if($model->saveOrder()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=accessgroupsmain', $msg ); 
	}
	
	function orderUp() {
		$model = $this->getModel('accessgroupsform');
		if($model->orderUp()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=accessgroupsmain', $msg ); 
	}
	
	function orderDown() {
		$model = $this->getModel('accessgroupsform');
		if($model->orderDown()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=accessgroupsmain', $msg ); 
	}
		
	function cancel() { 
		$msg = 'Aktion abgebrochen'; 
		$this->setRedirect( 'index.php?option=com_clm&view=accessgroupsmain', $msg ); 
	} 
} 