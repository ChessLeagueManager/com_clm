<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerArbiterAssign extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->app =JFactory::getApplication();
					
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
				$this->app->enqueueMessage( $lang->arbiter_set );
			} else {
				$this->app->enqueueMessage( $lang->arbiter_set );
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
			$adminLink->view = "arbiterassign"; // WL in Liste
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
			$this->app->enqueueMessage( JText::_('SECTION_NO_ACCESS'),'warning' );
			return array(false);
		}
	
		// Task
		$task = clm_core::$load->request_string('task');
		
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		
		$aca = clm_core::$load->request_int('aca');
		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'A' AND role = 'CA' "; 
		clm_core::$db->query($query);	
		if ($aca > 0) {
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($aca, $lid, $tid, 'A', 'CA', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}
		
		$adca = clm_core::$load->request_int('adca');
		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'A' AND role = 'DCA' "; 
		clm_core::$db->query($query);	
		for ($i = 0; $i <= 10; $i++) {
			$adca = clm_core::$load->request_int('adca'.$i);
			if ($adca < 1) continue;
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($adca, $lid, $tid, 'A', 'DCA', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}
		
		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'A' AND role = 'PO' "; 
		clm_core::$db->query($query);	
		for ($i = 0; $i <= 10; $i++) {
			$apo = clm_core::$load->request_int('apo'.$i);
			if ($apo < 1) continue;
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($apo, $lid, $tid, 'A', 'PO', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}

		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'A' AND role = 'SA' "; 
		clm_core::$db->query($query);	
		for ($i = 0; $i <= 10; $i++) {
			$asa = clm_core::$load->request_int('asa'.$i);
			if ($asa < 1) continue;
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($asa, $lid, $tid, 'A', 'SA', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}
		
		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'A' AND role = 'ASA' "; 
		clm_core::$db->query($query);	
		for ($i = 0; $i <= 10; $i++) {
			$aasa = clm_core::$load->request_int('aasa'.$i);
			if ($aasa < 1) continue;
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($aasa, $lid, $tid, 'A', 'ASA', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}
		
		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'A' AND role = 'ACA' "; 
		clm_core::$db->query($query);	
		for ($i = 0; $i <= 10; $i++) {
			$aaca = clm_core::$load->request_int('aaca'.$i);
			if ($aaca < 1) continue;
			$query = " INSERT INTO #__clm_arbiter_turnier "
				." (fideid, liga, turnier, trole, role, dg, runde, paar ) "
				." VALUES ($aaca, $lid, $tid, 'A', 'ACA', 0, 0, 0)"
			;
			clm_core::$db->query($query);	
		}
		
		$sf = clm_core::$load->request_array_int('sf');
		$dg = clm_core::$load->request_array_int('dg');
		$runde = clm_core::$load->request_array_int('runde');
		$paar = clm_core::$load->request_array_int('paar');
		$query = " DELETE FROM #__clm_arbiter_turnier "
			." WHERE liga = $lid AND turnier = $tid AND trole = 'A' AND role = 'A00' "; 
		clm_core::$db->query($query);	
		if (!is_null($sf)) {
			for ($i = 0; $i < count($sf); $i++) {
				if ($sf[$i] < 1) continue;
				$query = " INSERT INTO #__clm_arbiter_turnier "
					." (fideid, liga, turnier, dg, runde, paar, trole, role ) "
					." VALUES ($sf[$i], $lid, $tid, $dg[$i], $runde[$i], $paar[$i], 'A', 'A00')"
				;
				clm_core::$db->query($query);
			}				
		}

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $lang->arbiter_set.": ".$lid.','.$tid;
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