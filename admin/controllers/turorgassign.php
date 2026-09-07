<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class CLMControllerTurorgAssign extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app =Factory::getApplication();
					
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
	}


	function save() {

		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$returnview = clm_core::$load->request_string('returnview');

		$result = $this->_saveDo();
		$lang = clm_core::$lang->arbiter;

		if ($result[0]) { // erfolgreich?
			
			if ($result[1]) { // neue Kategorie?
				$this->app->enqueueMessage( $lang->organiser_set );
			} else {
				$this->app->enqueueMessage( $lang->organiser_set );
			}		
		}
		// sonst Fehlermeldung schon geschrieben

		$task = clm_core::$load->request_string('task');

		$adminLink = new AdminLink();
		// wenn 'apply', weiterleiten in form
		if ($task == 'save' OR !$result[0]) {
			// Weiterleitung zurück
			if ($returnview == 'mturniere') 
				$adminLink->more = array('section' => $returnview, 'liga' => '0', 'id' => $lid, 'task' => 'edit');
			elseif ($returnview == 'ligen') 
				$adminLink->more = array('section' => $returnview, 'liga' => '1', 'id' => $lid, 'task' => 'edit');
			elseif ($returnview == 'turform') 
				$adminLink->more = array('view' => $returnview, 'id' => $tid, 'task' => 'edit');
			else  // runden 
				$adminLink->more = array('section' => 'runden', 'liga' => $result[2]);
		} else {
			// Weiterleitung bleibt im Formular
			$adminLink->view = "turorgassign"; // WL in Liste
			$adminLink->more = array('task' => 'edit', 'lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		}
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
		$lang = clm_core::$lang->arbiter;
	
		if (clm_core::$access->getType() != 'admin' AND clm_core::$access->getType() != 'tl') {
			$this->app->enqueueMessage( Text::_('SECTION_NO_ACCESS'),'warning' );
			return array(false);
		}
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		
		$ttl = clm_core::$load->request_int('ttl');
		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'T' AND role = 'TL' "; 
		clm_core::$db->query($query);	
		if ($ttl > 0) {
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($ttl, $lid, $tid, 'T', 'TL', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}
		
/*		$adca = clm_core::$load->request_int('adca');
*/		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'T' AND role = 'TLW' "; 
		clm_core::$db->query($query);	
		for ($i = 0; $i <= 10; $i++) {
			$ttlw = clm_core::$load->request_int('ttlw'.$i);
			if ($ttlw < 1) continue;
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($ttlw, $lid, $tid, 'T', 'TLW', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}
		
		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'T' AND role = 'TO' "; 
		clm_core::$db->query($query);	
		for ($i = 0; $i <= 10; $i++) {
			$tto = clm_core::$load->request_int('tto'.$i);
			if ($tto < 1) continue;
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($tto, $lid, $tid, 'T', 'TO', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}

		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'T' AND role = 'KA' "; 
		clm_core::$db->query($query);	
		for ($i = 0; $i <= 10; $i++) {
			$tka = clm_core::$load->request_int('tka'.$i);
			if ($tka < 1) continue;
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($tka, $lid, $tid, 'T', 'KA', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}
		
		

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $lang->organiser_set.": ".$lid.','.$tid;
		$clmLog->params = array('lid' => $lid, 'tid' => $tid); 
		$clmLog->write();
		
		return array(true,$lid,$tid);
	}

	function arbitermain() {

		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$returnview = clm_core::$load->request_string('returnview');

		$adminLink = new AdminLink();
		$adminLink->more = array('lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
		$adminLink->view = "arbitermain";
		$adminLink->makeURL();
		$this->app->redirect($adminLink->url); 		
	}
	
	function cancel() {	

		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		$returnview = clm_core::$load->request_string('returnview');

		$adminLink = new AdminLink();
		if ($returnview == 'turform') {
			$adminLink->more = array('view' => $returnview, 'id' => $tid, 'task' => 'edit');
		} elseif ($returnview == 'mturniere') {
			$adminLink->more = array('section' => $returnview, 'liga' => '0', 'task' => 'edit', 'id' => $lid);
		} elseif ($returnview == 'ligen') {
			$adminLink->more = array('section' => $returnview, 'liga' => '1', 'task' => 'edit', 'id' => $lid);
		} else {
			$adminLink->view = "arbitermain";
		}
		$adminLink->makeURL();
		$this->app->redirect( $adminLink->url );	
	}

}