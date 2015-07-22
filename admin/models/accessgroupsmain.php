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

class CLMModelAccessgroupsMain extends JModelLegacy {

	var $_accessgroups;
	var $_total = null;
	var $_pagination = null;
	var $_user;
	var $_filterAccessGroups;
	

	function __construct(){
		parent::__construct();
		$mainframe 	= JFactory::getApplication();
		$option 	= JRequest::getVar( 'option' );

		//Pagination Variabeln
		$limit 		= JRequest::getVar( 'limit' , $mainframe->getCfg('list_limit') , 'default' , 'int' );
		$limitstart	= JRequest::getVar( 'limitstart' , 0 , 'default' , 'int' );

		$this->setState( 'limit' , $limit ); 
		$this->setState( 'limitstart' , $limitstart );
		
		//Suche 
		$search 		= JRequest::getVar( 'search' , '' , 'default' , 'string' );
		$search 		= JString::strtolower( $search );
		
		$this->setState( 'search' , $search );
		
		//Sortierung Variabeln
		$filter_order     = JRequest::getVar( 'filter_order' , 'accessgroup' , 'default' , 'cmd' );
		$filter_order_Dir = JRequest::getVar( 'filter_order_Dir' , 'ASC' , 'default' , 'word' );
		
		$this->setState( 'filter_order' , $filter_order );
		$this->setState( 'filter_order_Dir' , $filter_order_Dir );
 
		// User
		$this->user =JFactory::getUser();
	}
	
	function getAccessgroups() { 
		if (empty( $this->_accessgroups )) { 
			$query = $this->_buildQuery();
			$this->_accessgroups = $this->_getList( $query , $this->getState('limitstart') , $this->getState('limit') ); 
		} 
		return $this->_accessgroups; 
	} 
	
	function getTotal() { 
		if (empty( $this->_total )) { 
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount( $query ); 
		} 
		return $this->_total; 
	} 
	
	function getPagination()
		{
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
		
	function getUser()
		{
		if (empty($this->_user)) {
			$this->_user =JFactory::getUser();;
		}
		return $this->_user;
	}
	
	function _buildQuery(){
		$where = $this->_buildContentWhere();
		$order = $this->_buildContentOrderBy();
		$query = ' 	SELECT *, 0 as checked_out FROM #__clm_usertype 
					'.$where 
					 .$order; 
		return $query;
	}
	
	function _buildContentWhere() {
		$mainframe	= JFactory::getApplication();
		$option 	= JRequest::getCmd( 'option' );

		//$filter_accessgroup	= JRequest::getVar( 'filter_accessgroup' , '' , 'default' , 'string' );
		$search 		= JRequest::getVar( 'search' , '' , 'default' , 'string' );
		$search 		= JString::strtolower( $search );

		$where = array();
		if ($search) {
			$where[] = " LOWER(name) LIKE ".$this->_db->Quote('%'.$search.'%');
		}
		$where[] = ' name NOT IN ("CLMreserve01","CLMreserve02","CLMreserve03") ';
				
		$where = ( count( $where ) ? " WHERE ". implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	
	function _buildContentOrderBy()	{
		$mainframe	= JFactory::getApplication();
		$option 	= JRequest::getCmd( 'option' );
 
		$orderby = '';
		$filter_order     = ''; //$this->getState('filter_order');
		$filter_order_Dir = ''; //$this->getState('filter_order_Dir');


		if(!empty($filter_order) && !empty($filter_order_Dir) ){
				$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
				if($filter_order  != 'ordering'){
					$orderby = $orderby.', ordering ASC';
				}
		}
		$orderby = ' ORDER BY ordering ASC';
		return $orderby;
	}
	
		
}

?>
