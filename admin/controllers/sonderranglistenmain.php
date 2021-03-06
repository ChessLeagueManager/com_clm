<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSonderranglistenMain extends JControllerLegacy {

	function __construct() {
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'sonderranglistenmain';
		parent::display(); 
	} 
	
	function add() { 
		$_REQUEST['view'] = 'sonderranglistenform' ; 
		$_REQUEST['hidemainmenu'] = 1;
		parent::display(); 
	}
	
	function edit() { 
		$_REQUEST['view'] = 'sonderranglistenform' ; 
		$_REQUEST['hidemainmenu'] = 1;
		parent::display(); 
	}
	
	function save() { 
		$model = $this->getModel('sonderranglistenform'); 
		if ($model->store()) { 
			$msg = 'Speichern war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Speichern'; 
		} 
		$this->setRedirect('index.php?option=com_clm&view=sonderranglistenmain',$msg); 
	} 
	
	function remove() { 
		$model = $this->getModel('sonderranglistenform'); 
		if($model->delete()) { 
			$msg = 'Löschen war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Löschen'; 
		} 
		$this->setRedirect('index.php?option=com_clm&view=sonderranglistenmain',$msg); 
	}

	function publish() {
		$model = $this->getModel('sonderranglistenform');
		if($model->publish()) {
			$msg = 'Freigeben war erfolgreich';
		} else {
			$msg = 'Fehler beim freigeben';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=sonderranglistenmain', $msg ); 
		
	}
	
	function unpublish() {
		$model = $this->getModel('sonderranglistenform');
		if($model->unpublish()) {
			$msg = 'Sperren war erfolgreich';
		} else {
			$msg = 'Fehler beim sperren';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=sonderranglistenmain', $msg ); 
		
	}
	
	function saveOrder() {
		$model = $this->getModel('sonderranglistenform');
		if($model->saveOrder()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=sonderranglistenmain', $msg ); 
	}
	
	function orderUp() {
		$model = $this->getModel('sonderranglistenform');
		if($model->orderUp()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=sonderranglistenmain', $msg ); 
	}
	
	function orderDown() {
		$model = $this->getModel('sonderranglistenform');
		if($model->orderDown()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->setRedirect( 'index.php?option=com_clm&view=sonderranglistenmain', $msg ); 
	}
		
	function cancel() { 
		$msg = 'Aktion abgebrochen'; 
		$this->setRedirect( 'index.php?option=com_clm&view=sonderranglistenmain', $msg ); 
	} 
	
	function copy_set() { 
		$_REQUEST['view'] = 'sonderranglistencopy' ; 
		$_REQUEST['hidemainmenu'] = 1; 
		parent::display(); 
	}

} 