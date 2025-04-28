<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelArbiterMain extends JModelLegacy {

	var $_pagination = null;
	var $_total = null;


	// benötigt für Pagination
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
		//Joomla 1.6 compatibility
		if (empty($mainframe)) {
			$mainframe = JFactory::getApplication();
			$option = $mainframe->scope;
		}

		$this->limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $this->limit);
		$this->setState('limitstart', $this->limitstart);

		// user
		$this->user =JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get arbiters data
		$this->_getArbiters();

		$this->_getPagination();

	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		//CLM parameter auslesen
		$clm_config = clm_core::$db->config();
		if ($clm_config->field_search == 1) $field_search = "js-example-basic-single";
		else $field_search = "inputbox";
	
		$mainframe =JFactory::getApplication();
		global $option;
	
		if (!isset($this->param) OR is_null($this->param)) $this->param = array();	// seit J 4.2 nötig um notice zu vermeiden
		if (!isset($this->form) OR is_null($this->form)) $this->form = array();		// seit J 4.2 nötig um notice zu vermeiden
		// search
		$this->param['search'] = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
		$this->param['search'] = strtolower( $this->param['search'] );
	
		// Statusfilter
		$this->param['state'] = $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	
		// Order
		$this->param['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'c.id', 'cmd' );
		$this->param['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	
	}


	function _getArbiters() {
		
		$query = 'SELECT a.*'
			. ' FROM #__clm_arbiter AS a'
			.$this->_sqlWhere();
		$this->arbiterTotal = $this->_getListCount($query);
		
		$query .= $this->_sqlOrder().' LIMIT '.$this->limitstart.', '.$this->limit;
		
		$this->_db->setQuery($query);
		$this->arbiters = $this->_db->loadObjectList();
						
	}
	
	
	
	function _sqlWhere() {
	
		// init
		$where = array();
				
		if ($this->param['search']) {
			if (is_numeric($this->param['search']))
				$where[] = 'CONVERT(a.fideid,char) LIKE '.$this->_db->Quote( '%'.clm_escape( $this->param['search']).'%', false );
			else
				$where[] = 'LOWER(CONCAT(a.name, " ",a.vorname)) LIKE '.$this->_db->Quote( '%'.clm_escape( $this->param['search']).'%', false );
		}
	
		if ($this->param['state']) {
			if ($this->param['state'] == 'P') {
				$where[] = 'a.published = 1';
			} elseif ($this->param['state'] == 'U') {
				$where[] = 'a.published = 0';
			}
		}
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		return $where;
		
	}
	
	function _sqlOrder() {
		
		// array erlaubter order-Felder:
		$arrayOrderAllowed = array('name', 'published', 'ordering', 'id');
		// passt?
		if (!in_array($this->param['order'], $arrayOrderAllowed)) {
			$this->param['order'] = 'a.id';
		}
			
		$orderby = ' ORDER BY '. $this->param['order'] .' '. $this->param['order_Dir'] .', a.id';
	
		return $orderby;
	
	}
	
	function _getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->pagination)) {
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->arbiterTotal, $this->limitstart, $this->limit );
		}
	}


}

?>
