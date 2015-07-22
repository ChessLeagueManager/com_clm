<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class CLMModelLogMain extends JModel {

	var $_pagination = null;
	var $_total = null;


	// benötigt für Pagination
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
		//Joomla 1.6 compatibility
		if (empty($mainframe)) {
			$mainframe = &JFactory::getApplication();
			$option = $mainframe->scope;
		}

		$this->limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $this->limit);
		$this->setState('limitstart', $this->limitstart);

		$this->_getParameters();

		$this->_getLog();
		
		$this->_getForms();

		$this->_getPagination();

	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		$mainframe =& JFactory::getApplication();
		global $option;
	
		// search
		$this->param['search'] = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
		$this->param['search'] = JString::strtolower( $this->param['search'] );
	
		// turnier
		$this->param['lid'] = $mainframe->getUserStateFromRequest( "$option.lid", 'lid', 0, 'int' );
		$this->param['lid'] = JString::strtolower( $this->param['lid'] );
		
		// turnier
		$this->param['tid'] = $mainframe->getUserStateFromRequest( "$option.tid", 'tid', 0, 'int' );
		$this->param['tid'] = JString::strtolower( $this->param['tid'] );
		
		// Order
		$this->param['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'l.id', 'cmd' );
		$this->param['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	
	
	}


	function _getLog() {
		
		// get the total number of records
		$query = 'SELECT COUNT(*) '
				. ' FROM #__clm_log AS l'
				.$this->_sqlWhere();
				;
		$this->_db->setQuery( $query );
		$this->logTotal = $this->_db->loadResult();
		
		$query = ' SELECT l.*, u.username, sa.name as saisonname, tu.name as turniername, li.name as liganame, 0 as checked_out'
			.' FROM #__clm_log AS l'
			.' LEFT JOIN #__clm_user AS u ON u.jid = l.jid_aktion AND u.sid = l.sid'
			.' LEFT JOIN #__clm_saison AS sa ON sa.id = l.sid'
			.' LEFT JOIN #__clm_turniere AS tu ON tu.id = l.tid'
			.' LEFT JOIN #__clm_liga AS li ON li.id = l.lid'
				.$this->_sqlWhere()
				.$this->_sqlOrder().' LIMIT '.$this->limitstart.', '.$this->limit;
				;
		$this->_db->setQuery($query);
		$this->log = $this->_db->loadObjectList();
		
	}
	
	
	
	function _sqlWhere() {
	
		// init
		$where = array();
		
		if ($this->param['search']) {
			$where[] = 'LOWER(l.aktion) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $this->param['search'], true ).'%', false );
		}
		if ($this->param['lid'] > 0) {
			$where[] = 'l.lid = '.$this->param['lid'];
		}
		if ($this->param['tid'] > 0) {
			$where[] = 'l.tid = '.$this->param['tid'];
		}
	
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		return $where;
		
	}
	
	function _sqlOrder() {
		
		// array erlaubter order-Felder:
		$arrayOrderAllowed = array('l.aktion', 'u.username', 'l.sid', 'l.lid', 'l.tid', 'l.rnd', 'l.dg', 'l.zps', 'l.man', 'l.mgl_nr', 'l.jid', 'l.cids', 'l.datum', 'l.id');
		// passt?
		if (!in_array($this->param['order'], $arrayOrderAllowed)) {
			$this->param['order'] = 'l.id';
		}
		$orderby = ' ORDER BY '. $this->param['order'] .' '. $this->param['order_Dir'] .', l.id';
	
		return $orderby;
	
	}
	
	
	function _getForms () {
	
		$ligalist[] = JHtml::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_LEAGUE' )), 'id', 'name' );
		$query = 'SELECT DISTINCT(li.id), li.name'
			.' FROM #__clm_log AS l'
			.' LEFT JOIN #__clm_liga AS li on li.id = l.lid'
			.' WHERE li.id > 0'
				;
		$this->_db->setQuery($query);
		$ligalist = array_merge( $ligalist, $this->_db->loadObjectList() );
		$this->forms['lid'] = JHtml::_('select.genericlist', $ligalist, 'lid', 'class="inputbox" size="1"'.CLMText::stringOnchange(TRUE), 'id', 'name', intval($this->param['lid']) );
		
	
		$turnierlist[] = JHtml::_('select.option',  '0', CLMText::selectOpener(JText::_( 'SELECT_TOURNAMENT' )), 'id', 'name' );
		$query = 'SELECT DISTINCT(tu.id), tu.name'
			.' FROM #__clm_log AS l'
			.' LEFT JOIN #__clm_turniere AS tu on tu.id = l.tid'
			.' WHERE tu.id > 0'
				;
		$this->_db->setQuery($query);
		$turnierlist = array_merge( $turnierlist, $this->_db->loadObjectList() );
		$this->forms['tid'] = JHtml::_('select.genericlist', $turnierlist, 'tid', 'class="inputbox" size="1"'.CLMText::stringOnchange(TRUE), 'id', 'name', intval($this->param['tid']) );
	
	}
	
	
	function _getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->pagination)) {
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->logTotal, $this->limitstart, $this->limit );
		}
	}


}

?>