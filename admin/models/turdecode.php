<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelTurDecode extends JModelLegacy {

	var $_pagination = null;
	var $_total = null;

	// benötigt für Pagination
	function __construct() {
		
		parent::__construct();


		// user
		$this->user =JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get turnier
		$this->_getTurnierData();

		// get players
		$this->_getPlayersData();
		
		// Pagination
		$this->_getPagination();
		

	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		$mainframe =JFactory::getApplication();
		global $option;
		$db=JFactory::getDBO();
//echo "<br>POST: "; var_dump($_POST);	
		// clm_spielername
		$this->param['clm_spielername'] = $mainframe->getUserStateFromRequest( "$option.clm_spielername", 'clm_spielername', '', 'string' );
		$this->param['clm_spielername'] = strtolower( $this->param['clm_spielername'] );
//echo "<br>clm_spielername:".$this->param['clm_spielername']." - ";

		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;

		// clm_org
//echo "<br>lv:".$config->lv;
//echo "<br>0filter_verband:".$filter_verband;
//		$filter_verband		= $mainframe->getUserStateFromRequest( "$option.filter_verband",'filter_verband',$config->lv,'string' );
		$filter_verband		= $mainframe->getUserStateFromRequest( "filter_verband",'filter_verband',$config->lv );
//echo "<br>1filter_verband:".$filter_verband;
//		$this->param['clm_org'] = $mainframe->getUserStateFromRequest( "$option.clm_org", 'clm_org', '', 'string' );
//		$this->param['clm_org'] = strtolower( $this->param['clm_org'] );
		// Verbandsfilter
		$sql = 'SELECT Verband, Verbandname FROM #__clm_dwz_verbaende ';
		$sql .= " WHERE SUBSTR(Verband,2,2) = '00' AND Verband <> '000' ";
		$db->setQuery($sql);
		$verbandlist[]	= JHTML::_('select.option',  '', JText::_( 'DECODE_VERBAND_WAEHLEN' ), 'Verband', 'Verbandname' );
		$verbandlist	= array_merge( $verbandlist, $db->loadObjectList() );
		$this->lists['verband']	= JHTML::_('select.genericlist', $verbandlist, 'filter_verband', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','Verband', 'Verbandname', $filter_verband );
		if ($countryversion =="de") 
			$this->param['verband'] = $filter_verband;
		else
			$this->param['verband'] = '';		
//echo "<br>verband:".$this->param['verband'];

		// turnier_id
		$this->param['turnierid'] = clm_core::$load->request_int('turnierid');
	
		// limit
		$this->limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $this->limit);
		$this->setState('limitstart', $this->limitstart);

	}

	
	function _getTurnierData() {
	
		$query = 'SELECT * '
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->param['turnierid']
			;
		$this->_db->setQuery($query);
		$this->turnier = $this->_db->loadObject();

		$query = 'SELECT * '
			. ' FROM #__clm_player_decode'
			. ' WHERE sid = '.$this->turnier->sid
			;
		$this->_db->setQuery($query);
		$this->playersNames = $this->_db->loadObjectList();
		$this->a_names = array();
		foreach ($this->playersNames as $player) {
			$this->a_names[$player->oname] = new Stdclass; 
			$this->a_names[$player->oname]->oname = $player->oname; 
			$this->a_names[$player->oname]->nname = $player->nname; 
			$this->a_names[$player->oname]->verein = $player->verein; 
		}			

		$query = 'SELECT Spielername, Vereinname as verein '
			. ' FROM #__clm_dwz_spieler as a'
			. ' LEFT JOIN #__clm_dwz_vereine as v ON v.sid = a.sid AND v.ZPS = a.ZPS '
			. " WHERE a.sid = ".$this->turnier->sid
			. " AND a.Spielername != '' AND v.Vereinname != '' "
			. " AND LOWER(a.Spielername) > '".$this->param['clm_spielername']."'";
		if ($this->param['verband'] != '' AND $this->param['verband'] != '000') {
			If (substr($this->param['verband'], 1, 2) == '00')
				$query .= " AND SUBSTR(Verband, 1, 1) = '".substr($this->param['verband'], 0, 1)."'";
			else 
				$query .= " AND Verband = '".$this->param['verband']."'";
		}
		$query .= ' ORDER BY Spielername '
			. ' LIMIT 20000 '
			;
//echo "<br>query-limit:".$query;
		$this->_db->setQuery($query);
		$this->dwzData = $this->_db->loadObjectList();
		$this->dwzTotal = $this->_getListCount($query);
//echo "<br>dwzTotal:".$this->dwzTotal;
	}

	function _getPlayersData() {
	
		$query = 'SELECT * '
			. ' FROM #__clm_turniere_tlnr'
			. ' WHERE turnier = '.$this->param['turnierid']
			;
		$this->_db->setQuery($query);
		$this->playersTotal = $this->_getListCount($query);
//echo "<br>playersTotal:".$this->playersTotal;
		if ($this->limit > 0) {
			
//echo "<br>m_memory_limit: ".ini_get('memory_limit'); //var_dump(ini_get('memory_limit'));
//echo "<br>m_dwzTotal:".$this->dwzTotal;
			$memory_limit = (integer) ini_get('memory_limit');
//echo "<br>m_memory_limit: ".$memory_limit; //var_dump(ini_get('memory_limit'));
			if ($memory_limit < 128) $this->limit = 10;
			if ($memory_limit >= 128)
				if ($this->dwzTotal > 15000) $this->limit = 15;
				else $this->limit = 20;
//echo "<br>m_limit: ".$this->limit; 
			if ($this->limitstart > $this->playersTotal) {
				$this->limitstart = $this->playersTotal - $this->limit;
				if ($this->limitstart < 0) $this->limitstart = 0;
			}
			$query .= ' LIMIT '.$this->limitstart.', '.$this->limit;
		}
		
		$this->_db->setQuery($query);
		$this->turPlayers = $this->_db->loadObjectList();

	}


	function _getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->pagination)) {
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->playersTotal, $this->limitstart, $this->limit );
		}
	}


}

?>
