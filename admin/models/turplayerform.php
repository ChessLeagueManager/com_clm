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

class CLMModelTurPlayerForm extends JModelLegacy {

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
		$this->_getTurnier();

		// get players
		$this->_getPlayerList();

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
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	
		if (!isset($this->param) OR is_null($this->param)) $this->param = array();	// seit J 4.2 nötig um notice zu vermeiden
		// turnierid
		$this->param['id'] = clm_core::$load->request_int('id');
		$this->param['add_nz'] = clm_core::$load->request_int('add_nz');
	
		// search
		$this->param['search'] = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
		$this->param['search'] = strtolower( $this->param['search'] );
	
		// DWZ
		$this->param['dwz'] = $mainframe->getUserStateFromRequest( "$option.filter_dwz", 'filter_dwz', 0, 'int' );
		
		// verband
		$config = clm_core::$db->config();
		$this->param['verband'] = $mainframe->getUserStateFromRequest( "$option.filter_verband", 'filter_verband', $config->lv);
		
		// verein
		$this->param['vid'] = $mainframe->getUserStateFromRequest( "$option.filter_vid", 'filter_vid', 0, 'string' );
		// prüfen, ob Verband gewählt und gewählter Verein zu Verband gehört!
		if ($countryversion =="de") {		// so wie hier nur für deutsche Anwendung sinnvoll 
			if ($this->param['verband'] != '000') {
				$verband = $this->param['verband'];
				WHILE (substr($verband, -1) == '0') {
					$verband = substr_replace($verband, "", -1);
				}
				if (!strstr($this->param['vid'], $verband)) {
					$this->param['vid'] = 0;
				}
			}
		}

		// Order
		$this->param['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'DWZ', 'cmd' );
		$this->param['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir', '', 'word' );
	
		// limit
		$this->limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $this->limit);
		$this->setState('limitstart', $this->limitstart);
	
	}


	function _getTurnier() {
	
		$query = 'SELECT * '    
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->param['id']
			;
		$this->_db->setQuery($query);
		$this->turnier = $this->_db->loadObject();
	
	}

	function _getPlayerList() {
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;
		
		if ($this->param['vid'] != '0') {
		
			$sqlZPS = " AND ds.ZPS = '".$this->param['vid']."'";
		
		} elseif ($this->param['verband'] != '000' AND $this->param['verband'] != '0') {
		
			// alle Nuller hinten abschneiden...
			$verband = $this->param['verband'];
			WHILE (substr($verband, -1) == '0') {
				$verband = substr_replace($verband, "", -1);
			}
		
			$sqlZPS = " AND ds.ZPS LIKE '".$verband."%'";
		
		} else {
			$sqlZPS = "";
		}

		if ($this->param['search'] != '') {
			$sqlName = ' AND LOWER(ds.Spielername) LIKE '.$this->_db->Quote( '%'.clm_escape($this->param['search']).'%', false );
		} else {
			$sqlName = '';
		}
		
		if ($this->param['dwz'] == 56) { // > 2600
			$sqlDWZ = ' AND ds.DWZ >= 2600';
		} elseif ($this->param['dwz'] != 0) {
			$sqlDWZ = ' AND ( ds.DWZ < '.($this->param['dwz']*50).' OR ds.DWZ IS NULL )';
		} else {
			$sqlDWZ = '';
		}
		
		// SELECT 
		$query = 'SELECT ds.*, dv.Vereinname, tt.snr '
			. ' FROM #__clm_dwz_spieler AS ds'
			. ' LEFT JOIN #__clm_dwz_vereine AS dv ON dv.ZPS = ds.ZPS AND dv.sid = ds.sid'
			. ' LEFT JOIN #__clm_turniere_tlnr AS tt ON tt.zps = ds.ZPS AND tt.sid = ds.sid AND tt.turnier ='.$this->param['id'];
		if ($countryversion == 'de') 
			$query .= ' AND tt.mgl_nr = ds.Mgl_Nr ';
		else   // en 
			$query .= ' AND tt.PKZ = ds.PKZ ';
		$query .= ' WHERE ds.sid = '.$this->turnier->sid
			. $sqlZPS
			. $sqlName
			. $sqlDWZ
			. " AND ds.sid = ".clm_core::$access->getSeason()
			. $this->_sqlOrder()
			;

		$this->_db->setQuery($query);
		$this->PlayersCount = $this->_getListCount($query);
		$this->PlayersList = $this->_getList($query, $this->limitstart, $this->limit);
		
		
	}
	
	
	
	
	function _sqlOrder() {
		
		// array erlaubter order-Felder:
		$arrayOrderAllowed = array('Spielername', 'DWZ', 'Mgl_nr', 'FIDE_Elo', 'Vereinname', 'snr', 'FIDE_Titel');
		if (!in_array($this->param['order'], $arrayOrderAllowed)) {
			$this->param['order'] = 'DWZ';
		}
		$orderby = ' ORDER BY '. $this->param['order'] .' '. $this->param['order_Dir'];
	
		return $orderby;
	
	}
	
	function _getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->pagination)) {
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->PlayersCount, $this->limitstart, $this->limit );
		}
	}

}

?>
