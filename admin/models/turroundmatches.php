<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelTurRoundMatches extends JModelLegacy {

	// benötigt für Pagination
	function __construct() {
		
		parent::__construct();


		// user
		$this->user =JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get turnier
		$this->_getTurnier();

		// get round
		$this->_getTurRound();

		$this->_getMatches();


	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		if (!isset($this->param) OR is_null($this->param)) $this->param = array();	// seit J 4.2 nötig um notice zu vermeiden
		// turnierid
		$this->param['turnierid'] = clm_core::$load->request_int('turnierid');
	
		// roundid
		$this->param['roundid'] = clm_core::$load->request_int('roundid');
	
	}


	function _getTurnier() {
	
		//$query = 'SELECT name, dg, typ, params'
		$query = 'SELECT a.*, r.name as rname '
			. ' FROM #__clm_turniere as a '
			. ' LEFT JOIN #__clm_turniere_rnd_termine as r ON r.turnier = a.id AND r.nr = 1 '
			. ' WHERE a.id = '.$this->param['turnierid']
			;
		$this->_db->setQuery($query);
		$this->turnier = $this->_db->loadObject();
	
	}

	function _getTurRound() {
		
		$query = "SELECT a.dg, a.nr, a.name, a.gemeldet, a.editor, a.zeit, a.edit_zeit, a.tl_ok, p.name as ename, u.name as gname"
			." FROM `#__clm_turniere_rnd_termine` as a "
			." LEFT JOIN #__clm_user as u ON u.jid = a.gemeldet "
			." LEFT JOIN #__clm_user as p ON p.jid = a.editor "
			." WHERE a.turnier = ".$this->param['turnierid']
			." AND a.id = ".$this->param['roundid']
			;
		$this->_db->setQuery($query);
		$this->round = $this->_db->loadObject();
		
	}
	
	
	function _getMatches() {
	
		$query = 'SELECT a.*, q.teil, q.name as tname'
				. ' FROM #__clm_turniere_rnd_spl as a '
				. ' LEFT JOIN #__clm_turniere AS q ON q.id = a.turnier '
				. ' WHERE a.heim = 1' //  AND q.published = 1
				. ' AND a.turnier ='.$this->param['turnierid']
				// . ' AND a.dg = '.$filter_dg
				. ' AND a.runde = '.$this->round->nr // nicht ID, sondern RundenNr
				. ' AND a.dg = '.$this->round->dg 
				. ' ORDER BY a.dg ASC, a.runde ASC, a.brett ASC '
				;
		$this->_db->setQuery($query);
		$this->matches = $this->_db->loadObjectList();
	
		// Ergebnisliste laden
		$query = "SELECT a.eid, a.erg_text "
			." FROM #__clm_ergebnis as a "
			;
		$this->_db->setQuery( $query );
		$this->ergebnisse	= $this->_db->loadObjectList();
	
		// Spielerliste laden
		$query = "SELECT snr, name"
			." FROM `#__clm_turniere_tlnr` " 
			." WHERE turnier = ".$this->param['turnierid']
			."   AND tlnrStatus = 1 ";
		if ($this->turnier->rname != JText::_('ROUND_KO_1') AND ($this->turnier->typ == '3' or $this->turnier->typ == '5')) {
			if (($this->turnier->typ == '3' AND $this->round->nr < $this->turnier->runden) OR ($this->turnier->typ == '5' AND $this->round->nr < $this->turnier->runden-1)) {
				$query .= " AND ( koStatus = '1' OR  koRound >= ".$this->round->nr.")"; }
			if ($this->turnier->typ == '3' AND $this->round->nr == $this->turnier->runden) { // KO-Modus Finale
				$query .= " AND koRound >= ".($this->round->nr - 1)." "; }
			if ($this->turnier->typ == '5' AND $this->round->nr == ($this->turnier->runden-1)) { // KO-Modus Finale bei kl.Finale
				$query .= " AND koRound >= ".($this->round->nr - 1)." "; }
			if ($this->turnier->typ == '5' AND $this->round->nr == $this->turnier->runden) { // KO-Modus kleines Finale
				$query .= " AND koRound >= ".($this->round->nr - 2)." "; }
		}
		$query .= " ORDER BY snr ASC "
			;
		$this->_db->setQuery( $query );
		$this->players	= $this->_db->loadAssocList();
	
	
	}
	
	


}

?>