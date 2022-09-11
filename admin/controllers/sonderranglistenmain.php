<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
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

		$this->app	= JFactory::getApplication();
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "sonderranglistenmain";
	}
	
	function display($cachable = false, $urlparams = array()) { 
		$_REQUEST['view'] = 'sonderranglistenmain';
		parent::display(); 
	} 
	
	function add() { 
		$this->adminLink->view = "sonderranglistenform";
		$_REQUEST['hidemainmenu'] = 1;
		$this->adminLink->makeURL();		
		$this->app->redirect( $this->adminLink->url );
	}
	
	function edit() { 
		$cids = clm_core::$load->request_array_int('cid'); 
		$this->adminLink->view = "sonderranglistenform";
		$this->adminLink->more = array('id' => $cids[0]);
		$this->adminLink->makeURL();		
		$this->app->redirect( $this->adminLink->url );
	}
	
	function save() { 
		$model = $this->getModel('sonderranglistenform'); 
		if ($model->store()) { 
			$msg = 'Speichern war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Speichern'; 
		} 
		$this->app->enqueueMessage( $msg );
		$this->app->redirect('index.php?option=com_clm&view=sonderranglistenmain'); 
	} 
	
	function remove() { 
		$model = $this->getModel('sonderranglistenform'); 
		if($model->delete()) { 
			$msg = 'Löschen war erfolgreich'; 
		} else { 
			$msg = 'Fehler beim Löschen'; 
		} 
		$this->app->enqueueMessage( $msg );
		$this->app->redirect('index.php?option=com_clm&view=sonderranglistenmain'); 
	}

	function publish() {
		$model = $this->getModel('sonderranglistenform');
		if($model->publish()) {
			$msg = 'Freigeben war erfolgreich';
		} else {
			$msg = 'Fehler beim freigeben';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect('index.php?option=com_clm&view=sonderranglistenmain'); 
		
	}
	
	function unpublish() {
		$model = $this->getModel('sonderranglistenform');
		if($model->unpublish()) {
			$msg = 'Sperren war erfolgreich';
		} else {
			$msg = 'Fehler beim sperren';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect('index.php?option=com_clm&view=sonderranglistenmain'); 
		
	}
	
	function saveOrder() {
		$model = $this->getModel('sonderranglistenform');
		if($model->saveOrder()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect('index.php?option=com_clm&view=sonderranglistenmain'); 
	}
	
	function orderUp() {
		$model = $this->getModel('sonderranglistenform');
		if($model->orderUp()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect('index.php?option=com_clm&view=sonderranglistenmain'); 
	}
	
	function orderDown() {
		$model = $this->getModel('sonderranglistenform');
		if($model->orderDown()) {
			$msg = 'Reihenfolge wurde erfolgreich gespeichert';
		} else {
			$msg = 'Fehler beim speichern der Reihenfolge';
		}
		$this->app->enqueueMessage( $msg );
		$this->app->redirect('index.php?option=com_clm&view=sonderranglistenmain'); 
	}
		
	function cancel() { 
		$msg = 'Aktion abgebrochen'; 
		$this->app->enqueueMessage( $msg );
		$this->app->redirect('index.php?option=com_clm&view=sonderranglistenmain'); 
	} 
	
	function copy_set() { 
		$_REQUEST['hidemainmenu'] = 1; 
		$this->app->redirect('index.php?option=com_clm&view=sonderranglistencopy'); 
	}

} 