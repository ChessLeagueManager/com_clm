<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
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

		$this->app	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply','save' );
	
	}
	
	function display($cachable = false, $urlparams = array()) { 

		parent::display(); 
		
	} 
	
	function add() { 

		$adminLink = new AdminLink();
		$adminLink->view = "accessgroupsform";
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	}
	
	function edit() { 
		$cid	= clm_core::$load->request_array_int('cid');
		
		$adminLink = new AdminLink();
		$adminLink->view = "accessgroupsform";
		$adminLink->more = array('task' => 'edit', 'id' => $cid[0]);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	}
	function copy() { 
		$model = $this->getModel('accessgroupsform'); 
		if ($model->copy()) { 
			$msg = 'Kopieren war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Kopieren'; 
		} 
		$this->app->enqueueMessage( $msg );
		$this->setRedirect('index.php?option=com_clm&view=accessgroupsmain'); 
	} 
	
	function save() { 
		$model = $this->getModel('accessgroupsform'); 
		if ($model->store()) { 
			$msg = 'Speichern war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Speichern'; 
		} 
		$this->app->enqueueMessage( $msg );
		$this->setRedirect('index.php?option=com_clm&view=accessgroupsmain'); 
	} 
	
	function remove() { 
		$model = $this->getModel('accessgroupsform'); 
		if($model->delete()) { 
			$msg = 'Löschen war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Löschen'; 
		} 
		$this->app->enqueueMessage( $msg );
		$this->setRedirect('index.php?option=com_clm&view=accessgroupsmain'); 
	}

	function publish() {
		$model = $this->getModel('accessgroupsform');
		if($model->publish()) {
			$msg = 'Freigeben war erfolgreich';
		} else {
			$msg = 'Fehler beim Freigeben';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect( 'index.php?option=com_clm&view=accessgroupsmain' );
		
	}
	
	function unpublish() {
		$model = $this->getModel('accessgroupsform');
		if($model->unpublish()) {
			$msg = 'Sperren war erfolgreich';
		} else {
			$msg = 'Fehler beim Sperren';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect( 'index.php?option=com_clm&view=accessgroupsmain' );
		
	}
	
	function saveOrder() {
		$model = $this->getModel('accessgroupsform');
		if($model->saveOrder()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect( 'index.php?option=com_clm&view=accessgroupsmain' );
	}
	
	function orderUp() {
		$model = $this->getModel('accessgroupsform');
		if($model->orderUp()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect( 'index.php?option=com_clm&view=accessgroupsmain' );
	}
	
	function orderDown() {
		$model = $this->getModel('accessgroupsform');
		if($model->orderDown()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect( 'index.php?option=com_clm&view=accessgroupsmain' );
	}
		
	function cancel() { 
		$msg = 'Aktion abgebrochen'; 
		$this->app->enqueueMessage( $msg );
		$this->app->redirect( 'index.php?option=com_clm&view=accessgroupsmain' );
	} 
} 