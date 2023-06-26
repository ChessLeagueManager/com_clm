<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelTurRegistrations extends JModelLegacy {

	var $_pagination = null;
	var $_total = null;


	// benötigt für Pagination
	function __construct() {
		
		parent::__construct();


		// user
		$this->user =JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get all data
		$this->_getData();

		// Pagination
		$this->_getPagination();

	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		global $mainframe, $option;
		//Joomla 1.6 compatibility
		if (empty($mainframe)) {
			$mainframe = JFactory::getApplication();
			$option = $mainframe->scope;
		}
	
		if (!isset($this->param) OR is_null($this->param)) $this->param = array();	// seit J 4.2 nötig um notice zu vermeiden
		// turnierid
		$this->param['id'] = clm_core::$load->request_int('id');
	
		// search
		$this->param['search'] = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
		$this->param['search'] = strtolower( $this->param['search'] );
	
		// club
		$this->param['vid'] = $mainframe->getUserStateFromRequest( "$option.filter_vid", 'filter_vid', '0', 'string' );
		
	
		// Order
		$this->param['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'snr', 'cmd' );;
		$this->param['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir', '', 'word' );
	
		// limit
		$this->limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $this->limit);
		$this->setState('limitstart', $this->limitstart);
	
	}


	function _getData() {
	
		// turnier
		$query = 'SELECT id, name, teil, typ, tiebr1, tiebr2, tiebr3, tl, params'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->param['id']
			;
		$this->_db->setQuery($query);
		$this->turnier = $this->_db->loadObject();
	
		// players
		$query = 'SELECT a.*, rt.name as koRoundName '
				. ' FROM #__clm_turniere_tlnr as a'
				. ' LEFT JOIN #__clm_turniere_rnd_termine AS rt ON rt.turnier = a.turnier AND rt.nr = a.koRound '
				.$this->_sqlWhere();
		$this->_db->setQuery($query);
		$this->turPlayers = $this->_db->loadObjectList();
		
		// online registrations
		$query = 'SELECT a.* '
				. ' FROM #__clm_online_registration as a'
				. ' WHERE a.tid = '.$this->turnier->id;
		$this->registrationsTotal = $this->_getListCount($query);
		
		if ($this->limit > 0) {
			if ($this->limitstart > $this->registrationsTotal) {
				$this->limitstart = $this->registrationsTotal - $this->limit;
				if ($this->limitstart < 0) $this->limitstart = 0;
			}
			$query .= $this->_sqlOrder().' LIMIT '.$this->limitstart.', '.$this->limit;
		}
		
		$this->_db->setQuery($query);
		$this->turRegistrations = $this->_db->loadObjectList();
		
		// Flag, ob gestartet
		$tournament = new CLMTournament($this->param['id'], true);
		$tournament->checkTournamentStarted();
		$this->turnier->started = $tournament->started;
				
	}
	
	
	
	function _sqlWhere() {
	
		// init
		$where = array();
		
		$where[] = 'a.turnier = '.$this->param['id'];
		
		// Saison - nur Filter, wenn eingestellt
		if ($this->param['vid'] != '0') {
			$where[] = 'a.zps = '.$this->_db->Quote($this->param['vid']); 
		}
		if ($this->param['search'] != '') {
			$where[] = 'LOWER(a.name) LIKE '.$this->_db->Quote( '%'.clm_escape( $this->param['search'] ).'%', false );
		}
	
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		return $where;
		
	}
	
	function _sqlOrder() {
		
		// array erlaubter order-Felder:
		$arrayOrderAllowed = array('name', 'titel', 'club', 'FIDEcco', 'dwz', 'elo', 'status', 'timestamp', 'ordering');
		if (!in_array($this->param['order'], $arrayOrderAllowed)) {
			$this->param['order'] = 'id';
		}
		
		// normale Sortierung
		if ($this->param['order'] != 'sum_punkte') {
			$orderby = ' ORDER BY '. $this->param['order'] .' '. $this->param['order_Dir'] .', id';
		
		// Sortierung nach Punkten
		} else {
			$orderby = ' ORDER BY timestamp '. $this->param['order_Dir'];
		}
	
		return $orderby;
	
	}
	
	function _getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->pagination)) {
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->registrationsTotal, $this->limitstart, $this->limit );
		}
	}


}

?>
