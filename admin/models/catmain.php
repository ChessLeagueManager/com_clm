<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelCatMain extends JModelLegacy {

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

		// get tournament data
		$this->_getCategories();

		$this->_getPagination();

	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		$mainframe =JFactory::getApplication();
		global $option;
	
		if (!isset($this->param) OR is_null($this->param)) $this->param = array();	// seit J 4.2 nötig um notice zu vermeiden
		if (!isset($this->form) OR is_null($this->form)) $this->form = array();		// seit J 4.2 nötig um notice zu vermeiden
		// search
		$this->param['search'] = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
		$this->param['search'] = strtolower( $this->param['search'] );
	
		// parent
		$this->param['parentid'] = $mainframe->getUserStateFromRequest( "$option.filter_parentid", 'filter_parentid', 0, 'int' );
		// get Tree
		list($this->parentArray, $this->parentKeys, $this->parentChilds) = CLMCategoryTree::getTree();
		$parentlist[]	= JHTML::_('select.option',  '0', CLMText::selectOpener(JText::_( 'NO_PARENT' )), 'id', 'name' );
		foreach ($this->parentArray as $key => $value) {
			$parentlist[]	= JHTML::_('select.option',  $key, $value, 'id', 'name' );
		}
		$this->form['parent'] = JHTML::_('select.genericlist', $parentlist, 'filter_parentid', 'class="inputbox" size="1" style="max-width: 250px;"'.CLMText::stringOnchange(true), 'id', 'name', $this->param['parentid']);
	
	
		// Statusfilter
		$this->param['state'] = $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	
		// Order
		$this->param['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'c.id', 'cmd' );
		$this->param['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	
	}


	function _getCategories() {
		
		$query = 'SELECT c.*'
			. ' FROM #__clm_categories AS c'
			.$this->_sqlWhere();
		$this->catTotal = $this->_getListCount($query);
		
		$query .= $this->_sqlOrder().' LIMIT '.$this->limitstart.', '.$this->limit;
		
		$this->_db->setQuery($query);
		$this->categories = $this->_db->loadObjectList();
		
		// get Tree - für Ermittlung der Unterkategorien
		list($this->parentArray, $this->parentKeys) = CLMCategoryTree::getTree();
		
		
		// Zudem weitere Daten ermitteln
		foreach ($this->categories as $key => $value) {
			
			// für jede Kategorie Unterkategorien ermitteln
			unset ($arrayAllCatid);
			if (isset($this->parentChilds[$value->id])) {
				$arrayAllCatid = $this->parentChilds[$value->id];
				$arrayAllCatid[] = $value->id;
			} else {
				$arrayAllCatid[] = $value->id;
			}
			$addWhere = '( ( catidAlltime = '.implode( ' OR catidAlltime = ', $arrayAllCatid ).' )
								OR 
								( catidEdition = '.implode( ' OR catidEdition = ', $arrayAllCatid ).' ) )'; 
			
			// zugewiesene Turniere
			$query = 'SELECT COUNT(id)'
						. ' FROM #__clm_turniere'
						. ' WHERE '.$addWhere
						;
			$this->_db->setQuery($query);
			$this->categories[$key]->tournamentCount = $this->_db->loadResult();
		}
		
		
		
		// Kategorie-Tiefe ergänzen
		foreach ($this->categories as $key => $value) {
			$this->categories[$key]->nameTotal = $this->parentArray[$value->id];
		}
		
		
	}
	
	
	
	function _sqlWhere() {
	
		// init
		$where = array();
		
		if ($this->param['parentid'] > 0) {
			if (isset($this->parentChilds[$this->param['parentid']])) {
				$arrayAllCatid = $this->parentChilds[$this->param['parentid']];
				$arrayAllCatid[] = $this->param['parentid'];
			} else {
				$arrayAllCatid[] = $this->param['parentid'];
			}
			$where[] = '( ( id = '.implode( ' OR id = ', $arrayAllCatid ).' )
								OR 
								( parentid = '.implode( ' OR parentid = ', $arrayAllCatid ).' ) )'; 
		}
		
		if ($this->param['search']) {
			$where[] = 'LOWER(c.name) LIKE '.$this->_db->Quote( '%'.clm_escape( $this->param['search']).'%', false );
		}
	
		if ($this->param['state']) {
			if ($this->param['state'] == 'P') {
				$where[] = 'c.published = 1';
			} elseif ($this->param['state'] == 'U') {
				$where[] = 'c.published = 0';
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
			$this->param['order'] = 'c.id';
		}
			
		$orderby = ' ORDER BY '. $this->param['order'] .' '. $this->param['order_Dir'] .', c.id';
	
		return $orderby;
	
	}
	
	function _getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->pagination)) {
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->catTotal, $this->limitstart, $this->limit );
		}
	}


}

?>
