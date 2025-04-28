<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelArbiterAssign extends JModelLegacy {

	// benötigt für Pagination
	function __construct()
	{
		parent::__construct();

		// user
		$this->user =JFactory::getUser();
		
		$this->_getData();

		$this->_getForms();

	}


	function _getData() {
		
		$lid = clm_core::$load->request_int('lid');
		$tid = clm_core::$load->request_int('tid');
		if ($lid > 0) { 		// Teamwettbewerb
			$query = "SELECT l.*, s.name as sname FROM #__clm_liga as l "
						." LEFT JOIN #__clm_saison as s ON l.sid = s.id "
						.' WHERE l.id = '.$lid;
		} elseif ($tid > 0) { 		// Einzel(Single)wettbewerb
			$query = "SELECT t.*, s.name as sname FROM #__clm_turniere as t"
						." LEFT JOIN #__clm_saison as s ON t.sid = s.id "
						.' WHERE t.id = '.$tid;
		}
		$this->turnier	= clm_core::$db->loadObjectList($query);
		$sid = $this->turnier[0]->sid;

		// Schiedsrichter in der Installation
		$query = "SELECT a.*, CONCAT(a.name,',',a.vorname,'(',a.fideid,')') as fname, u.username FROM #__clm_arbiter as a "
				." LEFT JOIN #__clm_user as u ON a.fideid = u.fideid AND u.sid = ".$sid
				." WHERE a.fideid > 0 AND a.published = 1";
		$this->arbiters	= clm_core::$db->loadObjectList($query);
		
		// Hauptschiedsrichter
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a "
				." LEFT JOIN #__clm_user as u ON a.fideid = u.fideid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'A' AND a.role = 'CA' ";
		$this->arbiter_CA	= clm_core::$db->loadObjectList($query);
		// stellv. Hauptschiedsrichter
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a "
				." LEFT JOIN #__clm_user as u ON a.fideid = u.fideid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'A' AND a.role = 'DCA' "
				." ORDER BY a.id ";
		$this->arbiter_DCA	= clm_core::$db->loadObjectList($query);
		// weitere Schiedsrichter
		// Pairing Officer
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a"
				." LEFT JOIN #__clm_user as u ON a.fideid = u.fideid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'A' AND a.role = 'PO' "
				." ORDER BY a.id ";
		$this->arbiter_PO	= clm_core::$db->loadObjectList($query);
		// Sectorial Officer
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a"
				." LEFT JOIN #__clm_user as u ON a.fideid = u.fideid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'A' AND a.role = 'SA' "
				." ORDER BY a.id ";
		$this->arbiter_SA	= clm_core::$db->loadObjectList($query);
		// Schiedsrichter Assistent
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a"
				." LEFT JOIN #__clm_user as u ON a.fideid = u.fideid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'A' AND a.role = 'ASA' "
				." ORDER BY a.id ";
		$this->arbiter_ASA	= clm_core::$db->loadObjectList($query);
		// Anti-Cheating Verantwortlicher
		$query = "SELECT a.*, u.username FROM #__clm_arbiter_turnier as a"
				." LEFT JOIN #__clm_user as u ON a.fideid = u.fideid AND u.sid = ".$sid
				." WHERE a.liga = $lid AND a.turnier = $tid "
				." AND a.trole = 'A' AND a.role = 'ACA' "
				." ORDER BY a.id ";
		$this->arbiter_ACA	= clm_core::$db->loadObjectList($query);
		// alle Schiedsrichter im Turnier
		$query = "SELECT at.*, CONCAT(a.name,',',a.vorname,'(',a.fideid,')') as fname, u.username FROM #__clm_arbiter_turnier as at "
				." LEFT JOIN #__clm_arbiter as a ON a.fideid = at.fideid "
				." LEFT JOIN #__clm_user as u ON a.fideid = u.fideid AND u.sid = ".$sid
				." WHERE at.liga = $lid AND at.turnier = $tid "
				." AND (at.trole = 'A' AND at.role <> 'A00') "
				." ORDER BY at.id ";
		$this->arbiter_All	= clm_core::$db->loadObjectList($query);
		$this->array_All = array();
		for ($p = 0; $p < count($this->arbiter_All); $p++) {
			if ($this->arbiter_All[$p]->username > '') {
				$this->array_All[$this->arbiter_All[$p]->fideid] = $this->arbiter_All[$p]->username;
			}
		}

		// alle eingesetzten Schiedsrichter
		$query = "SELECT * FROM #__clm_arbiter_turnier as at "
				." WHERE at.liga = $lid AND at.turnier = $tid "
				." AND (at.trole = 'A' AND at.role = 'A00') "
				." ORDER BY at.dg, at.runde, at.paar ";
		$A00	= clm_core::$db->loadObjectList($query);
		$this->array_A00 = array();
		$this->array_A00U = array();
		if (is_null($A00)) $anz = 0; else $anz = count($A00);
		for ($p = 0; $p < $anz; $p++) {
			if ($A00[$p]->fideid > 1) {
				$this->array_A00[$A00[$p]->dg][$A00[$p]->runde][$A00[$p]->paar] = $A00[$p]->fideid;
				if (isset($this->array_All[$A00[$p]->fideid])) $this->array_A00U[$A00[$p]->dg][$A00[$p]->runde][$A00[$p]->paar] = 1; 
														else $this->array_A00U[$A00[$p]->dg][$A00[$p]->runde][$A00[$p]->paar] = 0;	
			}
		}
		
		// Paarungen zum Wettbewerb
		$query = "SELECT a.*, m.name as hname, m.tln_nr as htln, n.name as gname, n.tln_nr as gtln, rt.name as rname, rt.datum, rt.startzeit, rt.enddatum "
			." FROM #__clm_rnd_man as a"
			." LEFT JOIN #__clm_mannschaften as m ON m.tln_nr = a.tln_nr AND m.liga = a.lid AND m.sid = a.sid"
			." LEFT JOIN #__clm_mannschaften as n ON n.tln_nr = a.gegner AND n.liga = a.lid AND n.sid = a.sid"
			." LEFT JOIN #__clm_liga as l ON a.lid = l.id "
			." LEFT JOIN #__clm_runden_termine as rt ON rt.liga = a.lid AND rt.nr = (a.runde + (a.dg-1) * l.runden) "
			." WHERE a.lid = ".$lid
			." AND a.heim = 1"
			." ORDER BY a.dg ASC, a.runde ASC, a.paar ASC"
			;
		$this->paarung	= clm_core::$db->loadObjectList($query);

	}


	// alle vorhandenen Filter
	function _getForms() {
	
		//CLM parameter auslesen
		$clm_config = clm_core::$db->config();
		if ($clm_config->field_search == 1) $field_search = "js-example-basic-single";
		else $field_search = "inputbox";
	
		if (!isset($this->form) OR is_null($this->form)) $this->form = array();
		
		
	}

}

?>
