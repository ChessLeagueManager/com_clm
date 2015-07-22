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

class CLMModelSonderranglistenMain extends JModelLegacy {

	var $_sonderranglisten;
	var $_turniere;
	var $_total = null;
	var $_pagination = null;
	var $_user;
	var $_filterSaisons;
	var $_filterTurniere;
	

	function __construct(){
		parent::__construct();
		
		$mainframe 	= JFactory::getApplication();
		$option 	= JRequest::getVar( 'option' );

		//Pagination Variabeln
		$limit 		= JRequest::getVar( 'limit' , $mainframe->getCfg('list_limit') , 'default' , 'int' );
		$limitstart	= JRequest::getVar( 'limitstart' , 0 , 'default' , 'int' );

		$this->setState( 'limit' , $limit ); 
		$this->setState( 'limitstart' , $limitstart );
		
		//Suche und Filter
		$filter_saison	= JRequest::getVar( 'filter_saison' , $this->_getAktuelleSaison() , 'default' , 'int' );
		$filter_turnier	= JRequest::getVar( 'filter_turnier' , '' , 'default' , 'int' );
		$search 		= JRequest::getVar( 'search' , '' , 'default' , 'string' );
		$search 		= JString::strtolower( $search );
		
		$this->setState( 'filter_saison' , $filter_saison ); 
		$this->setState( 'filter_turnier' , $filter_turnier ); 
		$this->setState( 'search' , $search );
		
		//Sortierung Variabeln
		$filter_order     = JRequest::getVar( 'filter_order' , 'turnier' , 'default' , 'cmd' );
		$filter_order_Dir = JRequest::getVar( 'filter_order_Dir' , 'ASC' , 'default' , 'word' );
		
		$this->setState( 'filter_order' , $filter_order );
		$this->setState( 'filter_order_Dir' , $filter_order_Dir );
		
		// User
		$this->user =JFactory::getUser();
	}
	
	function getSonderranglisten() { 
		if (empty( $this->_sonderranglisten )) { 
			$query = $this->_buildQuery();
			$this->_sonderranglisten = $this->_getList( $query , $this->getState('limitstart') , $this->getState('limit') ); 
		} 
		return $this->_sonderranglisten; 
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
	
	function getTurniere() {
		if (empty( $this->_turniere )) { 
			$query =  ' SELECT 
							id,
							name
						FROM 
							#__clm_turniere ';
			$this->_turniere = $this->_getList( $query );
		} 
		return $this->_turniere;
	}
	
	function _buildQuery(){
		$where = $this->_buildContentWhere();
		$order = $this->_buildContentOrderBy();
		$query = ' 	SELECT 
						a.*,
						a.ordering AS ordering,
						a.turnier AS turnier,
						b.name AS turniername
					FROM 
						#__clm_turniere_sonderranglisten AS a
					LEFT JOIN
						#__clm_turniere AS b
					ON
						a.turnier = b.id	
					'.$where.' 
					'.$order; 
		return $query;
	}
	
	function _buildContentWhere() {
		$mainframe	= JFactory::getApplication();
		$option 	= JRequest::getCmd( 'option' );

		$filter_turnier	= JRequest::getVar( 'filter_turnier' , '' , 'default' , 'int' );
		$search 		= JRequest::getVar( 'search' , '' , 'default' , 'string' );
		$search 		= JString::strtolower( $search );

		$where = array();
		if ($search) {
			$where[] = " LOWER(a.name) LIKE ".$this->_db->Quote('%'.$search.'%');
		}
		
		if ($filter_turnier) {
			$where[] = " a.turnier = ".$filter_turnier;
		}
		
		$where = ( count( $where ) ? " WHERE ". implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	
	function _buildContentOrderBy()	{
		$mainframe	= JFactory::getApplication();
		$option 	= JRequest::getCmd( 'option' );
 
		$orderby = '';
		$filter_order     = $this->getState('filter_order');
		$filter_order_Dir = $this->getState('filter_order_Dir');


		if(!empty($filter_order) && !empty($filter_order_Dir) ){
				$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
				if($filter_order  != 'ordering'){
					$orderby = $orderby.', ordering ASC';
				}
		}

		return $orderby;
	}
	
	function getFilterSaisons() {
		if (empty( $this->_filterSaisons )) { 
		
			$query =  ' SELECT 
							id,
							name,
							published,
							archiv
						FROM 
							#__clm_saison 
						WHERE 
							archiv = 0';
			$this->_filterSaisons = $this->_getList( $query );
		} 
		return $this->_filterSaisons;
	}
	
	function getFilterTurniere() {
		if (empty( $this->_filterTurniere )) { 
		
			$aktSaison = $this->_getAktuelleSaison();
			//$filter_saison	= JRequest::getVar( 'filter_saison' , $aktSaison , 'default' , 'int' );
			$filter_saison	= JRequest::getVar( 'filter_saison' , clm_core::$access->getSeason() , 'default' , 'int' );
			
			$query =  ' SELECT 
							id,
							sid,
							name
						FROM 
							#__clm_turniere ';
			if($filter_saison) {
				$query .= 'WHERE sid = '.$filter_saison;
			}
			$this->_filterTurniere = $this->_getList( $query );
		} 
		return $this->_filterTurniere;
	}
	
	function _getAktuelleSaison() {
		if (empty( $this->_aktuelleSaison )) { 
		
			$query =  ' SELECT 
							id,
							name,
							published
						FROM 
							#__clm_saison 
						WHERE
							published = 1';
			$var = $this->_getList( $query );
		} 
		return $var[0]->id;
	}
	
}

?>