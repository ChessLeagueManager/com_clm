<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport( 'joomla.html.parameter' );

class CLMModelTurnier_Paarungsliste extends JModelLegacy {
	
	
	function __construct() {
		
		parent::__construct();

		$this->turnierid = JRequest::getInt('turnier', 0);

		$this->_getTurnierData();

		$this->_getTurnierPlayers();

		$this->_getTurnierRounds();
		
		$this->_getTurnierMatches();
		$this->_getTurnierPoints();

	}
	
	
	
	function _getTurnierData() {
	
		$query = "SELECT *"
			." FROM #__clm_turniere"
			." WHERE id = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->turnier = $this->_db->loadObject();

		// TO-DO: auslagern
		// zudem PGN-Parameter auswerten
		$turParams = new clm_class_params($this->turnier->params);
		$pgnInput = $turParams->get('pgnInput', 1);
		$pgnPublic = $turParams->get('pgnPublic', 1);
		
		// User ermitteln
		$user =JFactory::getUser();
		
		// Flag für View und Template setzen: pgnShow
		// FALSE - PGN nicht verlinken/anzeigen
		// TRUE - PGN-Links setzen und anzeigen 
		// 'pgnInput möglich' UND ('pgn öffentlich' ORDER 'User eingeloggt')
		if ($pgnInput == 1 AND ($pgnPublic == 1 OR $user->id > 0) ) {
			$this->pgnShow = TRUE;
		} else {
			$this->pgnShow = FALSE;
		}

		$this->displayTlOK = $turParams->get('displayTlOK', 0);

		// turniernamen anpassen?
		$addCatToName = $turParams->get('addCatToName', 0);
		if ($addCatToName != 0 AND ($this->turnier->catidAlltime > 0 OR $this->turnier->catidEdition > 0)) {
			$this->turnier->name = CLMText::addCatToName($addCatToName, $this->turnier->name, $this->turnier->catidAlltime, $this->turnier->catidEdition);
		}


	}
	
	
	function _getTurnierPlayers() {
	
		$query = "SELECT snr, name, twz"
			." FROM `#__clm_turniere_tlnr`"
			." WHERE turnier = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->players = $this->_db->loadObjectList('snr');
	

		$this->players[0] = new stdClass();
		$this->players[0]->name = "";
		$this->players[0]->twz = "";
	
	}
	
	function _getTurnierRounds() {
	
		//if ($this->turnier->typ != 3) {
			$sortDir = 'ASC';
		//} else {
		//	$sortDir = 'DESC'; // KO-System mit Finale untern anzeigen
		//}
	
		$query = "SELECT *"
			." FROM #__clm_turniere_rnd_termine"
			." WHERE turnier = ".$this->turnierid
			." ORDER BY dg $sortDir, nr $sortDir"
			;
		$this->_db->setQuery( $query );
		$this->rounds = $this->_db->loadObjectList();
	
	
	}
	
	function _getTurnierMatches() {
	
		// alle ermittelten Runden durchgehen
		foreach ($this->rounds as $value) {
			$query = "SELECT a.*, "
				." t.name as wname, t.twz as wtwz, t.verein as wverein, t.start_dwz as wdwz, t.FIDEelo as welo, "
				." u.name as sname, u.twz as stwz, u.verein as sverein, u.start_dwz as sdwz, u.FIDEelo as selo, "
				." pg.text "
				." FROM #__clm_turniere_rnd_spl as a"
				." LEFT JOIN #__clm_turniere_tlnr as t ON t.snr = a.spieler AND t.turnier = a.turnier "
				." LEFT JOIN #__clm_turniere_tlnr as u ON u.snr = a.gegner AND u.turnier = a.turnier "
				." LEFT JOIN #__clm_pgn as pg ON a.pgn = pg.id "
				." WHERE a.turnier = ".$this->turnierid." AND a.dg = ".$value->dg." AND a.runde = ".$value->nr." AND a.heim = '1'"
				." ORDER BY a.dg ASC, a.runde ASC, a.brett ASC"
				;
			$this->_db->setQuery( $query );
			$this->matches[$value->nr + (($value->dg - 1) * $this->turnier->runden)] = $this->_db->loadObjectList();
		
		}

	}
	
	function _getTurnierPoints() {
	
		$this->points = array();
		// Übernehmen der Sonderpunkte als Startpunkt
		$query = "SELECT snr, s_punkte "
			." FROM #__clm_turniere_tlnr"
			." WHERE turnier = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->s_points = $this->_db->loadObjectList();
		if (isset($this->s_points)) {
		  foreach ($this->s_points as $pvalue) {
			foreach ($this->rounds as $value) {
				$irunde=($value->nr + (($value->dg -1) * $this->turnier->runden)+ 1);
				$this->points[$irunde][$pvalue->snr] = $pvalue->s_punkte;
			}
		  }
		}
		
		// Matchpunkte hinzufügen, alle ermittelten Runden durchgehen
		foreach ($this->rounds as $value) {
			$query = "SELECT spieler, ergebnis "
				." FROM #__clm_turniere_rnd_spl"
				." WHERE turnier = ".$this->turnierid." AND dg = ".$value->dg." AND runde = ".$value->nr
				." ORDER BY dg ASC, runde ASC, brett ASC"
				;
			$this->_db->setQuery( $query );
			$this->round_points = $this->_db->loadObjectList();
			foreach ($this->round_points as $pvalue) {
			  for ($irunde=($value->nr + (($value->dg -1) * $this->turnier->runden)+ 1); $irunde < (($this->turnier->dg * $this->turnier->runden) + 1); $irunde++) {  
				if ($pvalue->ergebnis == 1 OR $pvalue->ergebnis == 5 OR $pvalue->ergebnis == 11) $point = 1;
				elseif ($pvalue->ergebnis == 2 OR $pvalue->ergebnis == 10 OR $pvalue->ergebnis == 12) $point = .5;
				else $point = 0;
				if (isset($this->points[$irunde][$pvalue->spieler]))  $this->points[$irunde][$pvalue->spieler] += $point;
				else $this->points[$irunde][$pvalue->spieler] = $point;
			  }
			}
		}

	}

}
?>
