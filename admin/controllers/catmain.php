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

class CLMControllerCatMain extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app	= JFactory::getApplication();
		
		// Register Extra tasks
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'unpublish','publish' );
	
	}

	// Weiterleitung!
	function add() {
		
		$adminLink = new AdminLink();
		$adminLink->view = "catform";
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}

	
	/**
	* Container für kopieren
	*
	*/
	function copy() {

		$this->_copyDo();

		$adminLink = new AdminLink();
		$adminLink->view = "catmain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );

	}

	/**
	* eigentliche copy-Funktion
	*
	*/
	function _copyDo() {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
		
		
		// zu bearbeitende IDs auslesen
		$cid		= clm_core::$load->request_array_int('cid');
		// vorerst nur eine ID bearbeiten!
		$catid = $cid[0];
		
		// access?
		$category = new CLMCategory($catid, true);
		if (!$category->checkAccess()) {
			$this->app->enqueueMessage( CLMText::errorText('JTOOLBAR_DUPLICATE', 'NOACCESS'),'warning' );
			return false;
		}
		
		// Daten holen
		$row =JTable::getInstance( 'categories', 'TableCLM' );

		if ( !$row->load($catid) ) {
			$this->app->enqueueMessage( CLMText::errorText('JCATEGORY', 'NOTEXISTING'),'warning' );
			return false;
		}
		
		// alten Namen zwischenspeichern für Message und Log
		$nameOld = $row->name;
		
		// Daten für Kopie anpassen
		$row->id				= 0; // neue id wird von DB vergeben
		$row->name			= JText::_('COPY_OF').' '.$row->name;
		$row->published	= 0;


		if (!$row->store()) {	
			$this->app->enqueueMessage( $row->getError(),'warning' );
			return false; 
		}
		
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('CATEGORY_COPIED').": ".$nameOld;
		$clmLog->params = array('catid' => $catid);
		$clmLog->write();
		
		// Message
		$this->app->enqueueMessage( $nameOld.": ".JText::_('CATEGORY_COPIED') );

		// Ende Runden erstellt
		return true;
	
	}


	// Weiterleitung!
	function edit() {
		
		$cid	= clm_core::$load->request_array_int('cid');
		
		$adminLink = new AdminLink();
		$adminLink->view = "catform";
		$adminLink->more = array('task' => 'edit', 'id' => $cid[0]);
		$adminLink->makeURL();
		
		$this->app->redirect( $adminLink->url );
	
	}


	function publish() {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		// TODO? evtl global inconstruct anlegen
		$user 		=JFactory::getUser();
		
		$cid		= clm_core::$load->request_array_int('cid');
		
		$task		= clm_core::$load->request_string( 'task' );
		$publish	= ($task == 'publish'); // zu vergebender Wert 0/1
		
		// Inhalte übergeben?
		if (empty( $cid )) { 
			$this->app->enqueueMessage( JText::_( 'NO_ITEM_SELECTED' ),'warning' );
		
		} else { // ja, Inhalte vorhanden
			
			// erst jetzt alle Einträge durchgehen
			foreach ($cid as $key => $value) {
		
				// load the row from the db table
				$row =JTable::getInstance( 'categories', 'TableCLM' );
				$row->load( $value ); // Daten zu dieser ID laden
		
				// Prüfen ob User Berechtigung für diese category hat
				if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
					$this->app->enqueueMessage( $row->name.": ".JText::_( 'CATEGORY_NO_ACCESS' ),'warning' );
					
					// daher diesen Eintrag aus dem cid-Array löschen
					unset($cid[$key]);
					
				} else {
					// Berechtigung vorhanden
					
					// Änderung nötig?
					if ($row->published != $publish) {
						// Log
						$clmLog = new CLMLog();
						$clmLog->aktion = JText::_('JCATEGORY')." ".$row->name.": ".$task;
						$clmLog->params = array('catid' => $value);
						$clmLog->write();
						// Log geschrieben - Änderungen später
					} else {
						unset($cid[$key]);
					}
					
				
				}
		
			} 
			// alle Einträge geprüft
		
			// immer noch Einträge vorhanden?
			if ( !empty($cid) ) { 
		
				$row =JTable::getInstance( 'categories', 'TableCLM' );
				$row->publish( $cid, $publish );
			
				// Meldung erstellen
				if ($publish) {
					$this->app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('JCATEGORY'), JText::_('JCATEGORIES'))." ".JText::_('CLM_PUBLISHED') );
				} else {
					$this->app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('JCATEGORY'), JText::_('JCATEGORIES'))." ".JText::_('CLM_UNPUBLISHED') );
				}
			
			} else {
			
				$this->app->enqueueMessage(JText::_('NO_CHANGES'));
			
			}
	
		}
	
		$adminLink = new AdminLink();
		$adminLink->view = "catmain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	
	
	/**
	* Container für Löschung
	*
	*/
	function delete() {

		$this->_deleteDo();

		$adminLink = new AdminLink();
		$adminLink->view = "catmain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );

	}
	
	
	/**CATEGORY_DELETED
	* eigentliche Lösch-Funktion
	*
	*/
	function _deleteDo() {
		
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		$cid = clm_core::$load->request_array_int('cid');
		// vorerst nur eine markiertes  übernehmen // später über foreach mehrere?
		$catid = $cid[0];
		
		
		// access?
		$category = new CLMCategory($catid, TRUE);
		if (!$category->checkAccess(true, false, false)) {
			$this->app->enqueueMessage( JText::_('CATEGORY_NO_ACCESS'),'warning' );
			return false;
		}
		
		if (!$category->checkDelete()) {
			$this->app->enqueueMessage( JText::_('CATEGORY_NO_DELETE'),'warning' );
			return false;
		}
		
		// Daten laden
		$row =JTable::getInstance( 'categories', 'TableCLM' );
		$row->load( $catid );
		
		// falls Cat existent?
		if ( !$row->load( $catid ) ) {
			$this->app->enqueueMessage( CLMText::errorText('CATEGORY', 'NOTEXISTING'),'warning' );
			return false;
		
		}
		
	
		// Category löschen
		$query = " DELETE FROM #__clm_categories "
			." WHERE id = ".$catid
			;
//		$this->_db->setQuery($query);
//		if (!$this->_db->query()) { 
		if (!clm_core::$db->query($query)) { 
			$this->app->enqueueMessage( $this->_db->getErrorMsg(),'error' );
			return false;
		}	
		
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('CATEGORY_DELETED');
		$clmLog->params = array('catid' => $catid);
		$clmLog->write();
		
		
		// Message
		$this->app->enqueueMessage( $row->name.": ".JText::_('CATEGORY_DELETED') );
		
		return true;
		
	}
	
	
	// Moves the record up one position
	function orderdown() {
		
		$this->_order(1);
		
		$adminLink = new AdminLink();
		$adminLink->view = "catmain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	// Moves the record down one position
	function orderup() {
		
		$this->_order(-1);
		
		$adminLink = new AdminLink();
		$adminLink->view = "catmain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

	// Moves the order of a record
	// @param integer The direction to reorder, +1 down, -1 up
	function _order($inc) {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		$cid = clm_core::$load->request_array_int('cid');
		$catid = $cid[0];
	
		// access?
		$category = new CLMCategory($catid, true);
		if (!$category->checkAccess()) {
			$this->app->enqueueMessage( JText::_('CATEGORY_NO_ACCESS'),'warning' );
			return false;
		}
	
		$row =JTable::getInstance( 'categories', 'TableCLM' );
		if ( !$row->load( (int)$catid ) ) {
			$this->app->enqueueMessage( CLMText::errorText('CATEGORY', 'NOTEXISTING'),'warning' );
			return false;
		}
//		$row->move( $inc, '' );
		$row->move($inc, 'sid = '.$row->sid);
		$row->reorder('sid = '.$row->sid);
	
		$this->app->enqueueMessage( $row->name.": ".JText::_('ORDERING_CHANGED') );
		
		return true;
		
	}

	// Saves user reordering entry
	function saveOrder() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
	
		if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			$this->app->enqueueMessage( JText::_('SECTION_NO_ACCESS'),'warning' );
			return false;
		}
	
	
		$cid		= clm_core::$load->request_array_int('cid');
	
		$total		= count( $cid );
		$order		= clm_core::$load->request_array_int('order');
	
		$row =JTable::getInstance( 'categories', 'TableCLM' );
		$groupings = array();
	
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->sid;
	
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->app->enqueueMessage( $db->getErrorMsg(),'error' );
				}
			}
		}
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('sid = '.(int) $group);
		}
		
		$this->app->enqueueMessage( JText::_('NEW_ORDERING_SAVED') );
	
		$adminLink = new AdminLink();
		$adminLink->view = "catmain";
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}

}