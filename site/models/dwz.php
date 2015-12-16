<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class CLMModelDWZ extends JModelLegacy
{

	function _getCLMzps ()
	{
	
	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd( 'option' );

	$zps	= clm_escape(JRequest::getVar('zps'));
	$sid	= JRequest::getInt('saison','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
		
	$query = " SELECT a.* FROM #__clm_dwz_spieler as a"
		." WHERE a.ZPS = '$zps'"
		." AND a.sid = ".$sid
		;	
		
	$filter_order     = $mainframe->getUserStateFromRequest( $option.'filter_order_dwz', 'filter_order', 'DWZ', 'cmd' );
	$filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir_dwz', 'filter_order_Dir', 'DESC', 'word' );
		
		$this->param['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'DWZ', 'cmd' ); // JRequest::getString('filter_order', 'a.id');
		$this->param['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','desc','word' );
	
	if(!empty($filter_order) && !empty($filter_order_Dir) ){
		$query .= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
	}
		
	return $query;
	}

	function getCLMzps( $options=array() )
	{
		$query	= $this->_getCLMzps( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	function _getCLMLiga( &$options )
	{
	$zps	= clm_escape(JRequest::getVar('zps'));
	$sid	= JRequest::getInt('saison','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
	$query = "SELECT Vereinname FROM #__clm_dwz_vereine as a "
		." LEFT JOIN #__clm_saison as s ON s.id = ".$sid
		." WHERE a.ZPS = '$zps'"
		." AND a.sid = ".$sid
		." AND s.published = 1"
		;
	return $query;
	}
	function getCLMLiga( $options=array() )
	{
		$query	= $this->_getCLMLiga( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	
	function _getCLMVereinsliste( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query  = 'SELECT DISTINCT a.zps, a.name FROM #__clm_vereine as a'
		." WHERE a.published = 1"
		." ORDER BY a.name ASC "
		;
		  
	return $query;
	}

	function getCLMVereinsliste( $options=array() )
	{
	$query	= $this->_getCLMVereinsliste( $options );
	$result = $this->_getList( $query );
	return @$result;
	}
	function _getCLMSaisons( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query  = ' SELECT a.name, a.id, a.archiv FROM #__clm_saison AS a'
		." ORDER BY a.name DESC "
		;
		  
	return $query;
	}

	function getCLMSaisons( $options=array() )
	{
	$query	= $this->_getCLMSaisons( $options );
	$result = $this->_getList( $query );
	return @$result;
	}
	
}
?>
