<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class CLMModelTurMain extends JModel {

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

		// user
		$this->user =& JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get tournament data
		$this->_getTurniere();

		$this->_getPagination();

	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		$mainframe =& JFactory::getApplication();
		global $option;
	
		// search
		$this->param['search'] = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
		$this->param['search'] = JString::strtolower( $this->param['search'] );
	
		// parent
		$this->param['parentid'] = $mainframe->getUserStateFromRequest( "$option.filter_parentid", 'filter_parentid', 0, 'int' );
		// get Tree
		list($this->parentArray, $this->parentKeys, $this->parentChilds) = CLMCategoryTree::getTree();
		$parentlist[]	= JHtml::_('select.option',  '0', CLMText::selectOpener(JText::_( 'NO_PARENT' )), 'id', 'name' );
		foreach ($this->parentArray as $key => $value) {
			$parentlist[]	= JHtml::_('select.option',  $key, $value, 'id', 'name' );
		}
		$this->form['parent'] = JHtml::_('select.genericlist', $parentlist, 'filter_parentid', 'class="inputbox" size="1" style="max-width: 250px;"'.CLMText::stringOnchange(true), 'id', 'name', $this->param['parentid']);
	
		// Modus/Typ
		$this->param['modus'] = $mainframe->getUserStateFromRequest( "$option.filter_modus", 'filter_modus', 0, 'int' );
	
		// Bezirk
		$this->param['bezirk'] = $mainframe->getUserStateFromRequest( "$option.filter_bezirk", 'filter_bezirk', 0, 'int' );
		
		// Saison
		$this->param['sid'] = $mainframe->getUserStateFromRequest( "$option.filter_sid", 'filter_sid', 0, 'int' );
	
		// Statusfilter
		$this->param['state'] = $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	
		// Order
		$this->param['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'a.id', 'cmd' ); // JRequest::getString('filter_order', 'a.id');
		$this->param['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	
	}


	function _getTurniere() {
		
		$query = 'SELECT a.*, CHAR_LENGTH(a.invitationText) AS inviteLength, c.name AS saison, c.published as saison_publish, u.name AS editor, cu.name AS director'
			. ' FROM #__clm_turniere AS a'
			. ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
			. ' LEFT JOIN #__clm_user AS cu ON cu.jid = a.tl AND cu.sid = a.sid'
			. ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
			.$this->_sqlWhere();
		$this->turTotal = $this->_getListCount($query);
		
		$query .= $this->_sqlOrder();
		
		if($this->limit > 0) { 
			$query .= ' LIMIT '.$this->limitstart.', '.$this->limit;
		}
		
		$this->_db->setQuery($query);
		$this->turniere = $this->_db->loadObjectList();
		
		// Zudem weitere Daten ermitteln
		foreach ($this->turniere as $key => $value) {
			
			if ($value->catidAlltime > 0) {
				$this->turniere[$key]->catnameAlltime = $this->parentArray[$value->catidAlltime];
			}
			if ($value->catidEdition > 0) {
				$this->turniere[$key]->catnameEdition = $this->parentArray[$value->catidEdition];
			}
			
			// Verein
			if (($value->vereinZPS != null AND $value->vereinZPS != '0') OR (strlen($value->vereinZPS) > 1)) {
				if (strlen($value->vereinZPS) == 5)
					$query = 'SELECT Vereinname as hostname'
							. ' FROM dwz_vereine'
							. ' WHERE ZPS = "'.$value->vereinZPS.'"'
							;
				elseif (strlen($value->vereinZPS) == 3)
					$query = 'SELECT Verbandname as hostname'
							. ' FROM dwz_verbaende'
							. ' WHERE Verband = "'.$value->vereinZPS.'"'
							;
				else $query = "";
				$this->_db->setQuery($query);
				$this->turniere[$key]->hostName = $this->_db->loadResult();
			}
			
			// eingetragene Teilnehmer
			$query = 'SELECT COUNT(id)'
						. ' FROM #__clm_turniere_tlnr'
						. ' WHERE turnier = '.$value->id
						;
			$this->_db->setQuery($query);
			$this->turniere[$key]->registered = $this->_db->loadResult();
			// Runden mit Bestätigung/tl_ok
			// eingetragene Teilnehmer
			$query = 'SELECT COUNT(id)'
						. ' FROM #__clm_turniere_rnd_termine'
						. ' WHERE turnier = '.$value->id.' AND tl_ok = \'1\''
						;
			$this->_db->setQuery($query);
			$this->turniere[$key]->roundsApproved = $this->_db->loadResult();
		
			if ($value->director == "") $value->director = "-";
		
		}
		
	}
	
	
	function _sqlWhere() {
	
		// init
		$where = array();
		
		$where[] = 'c.archiv = 0';
		
		if ($this->param['parentid'] > 0) {
			if (isset($this->parentChilds[$this->param['parentid']])) {
				$arrayAllCatid = $this->parentChilds[$this->param['parentid']];
				$arrayAllCatid[] = $this->param['parentid'];
			} else {
				$arrayAllCatid[] = $this->param['parentid'];
			}
			$where[] = '( ( a.catidAlltime = '.implode( ' OR a.catidAlltime = ', $arrayAllCatid ).' )
								OR 
								( a.catidEdition = '.implode( ' OR a.catidAlltime = ', $arrayAllCatid ).' ) )'; 
		}
		if ($this->param['modus'] > 0) {
			$where[] = 'a.typ = '.(int) $this->param['modus']; 
		}
		if ($this->param['sid'] > 0) {
			$where[] = 'a.sid = '.(int) $this->param['sid']; 
		}
		if ($this->param['search']) {
			$where[] = 'LOWER(a.name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $this->param['search'], true ).'%', false );
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
		$arrayOrderAllowed = array('a.name', 'a.typ', 'a.runden', 'a.dg', 'a.teil', 'a.tl', 'c.name', 'a.rnd', 'a.published', 'a.ordering', 'a.id', 'a.dateStart');
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
			$this->pagination = new JPagination($this->turTotal, $this->limitstart, $this->limit );
		}
	}


}

?>