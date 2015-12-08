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

defined('_JEXEC') or die();
jimport('joomla.application.component.model');


class CLMModelVerein extends JModelLegacy
{
	function _getCLMVereinstats( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
	$query = " SELECT a.ZPS, a.sid, a.Geschlecht, a.DWZ, a.FIDE_Elo, a.FIDE_ID,"
		." COUNT(Geschlecht) as Mgl,"
		." COUNT(case Geschlecht when 'M' then 1 else NULL end) as Mgl_m," // Männliche Mitglieder
		." COUNT(case Geschlecht when 'W' then 1 when 'F' then 1 else NULL end) as Mgl_w," // Weibliche Miglieder
		." avg(case DWZ when 0 then NULL else DWZ end) as DWZ," // DWZ Durchschnitt
		." avg(case FIDE_Elo when 0 then NULL else FIDE_Elo end) as FIDE_Elo," // ELO Durchschnitt
		." COUNT(case DWZ when 0 then NULL else DWZ end) as DWZ_SUM," // ELO Spieler
		." COUNT(case FIDE_ID when 0 then NULL else FIDE_ID end) as ELO_SUM" // DWZ Spieler
		." FROM #__clm_dwz_spieler as a "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE a.ZPS = '$zps'"
		." AND a.sid = ".$sid
		." GROUP BY a.ZPS"
		;
	return $query;
	}
	
	function getCLMVereinstats( $options=array() )
	{
	$query	= $this->_getCLMVereinstats( $options );
	$result = $this->_getList( $query );
	return @$result;
	}

	function _getCLMVerein( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
	$query = " SELECT a.* "
		." FROM #__clm_vereine as a "
		." WHERE a.zps = '$zps'"
		." AND a.sid = ".$sid
		." AND a.published = 1"
		;
	return $query;
	}

	function getCLMVerein( $options=array() )
	{
	$query	= $this->_getCLMVerein( $options );
	$result = $this->_getList( $query );
	return @$result;
	}

	function _getCLMMannschaft( &$options )
	{
	$sid	= JRequest::getInt('saison');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
	$query = "SELECT a.*, l.name as liga_name "
		." FROM #__clm_mannschaften as a "
		." LEFT JOIN #__clm_liga as l on l.id = a.liga AND l.sid = a.sid "
		." WHERE a.zps = '$zps'"
		." AND a.sid = ".$sid
		." AND a.published = 1 "
		." ORDER BY a.man_nr ASC "
		;
	return $query;
	}

	function getCLMMannschaft( $options=array() )
	{
	$query	= $this->_getCLMMannschaft( $options );
	$result = $this->_getList( $query );
	return @$result;
	}

	function _getCLMVereinsliste( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query  = 'SELECT DISTINCT a.zps, a.name, a.published FROM #__clm_vereine as a'
		.' WHERE published = 1'
		.' ORDER BY a.name ASC '
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
	$zps	= JRequest::getVar('zps');
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
	
	function _getCLMTurniere( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$query  = ' SELECT a.name, a.id, a.sid, a.vereinZPS FROM #__clm_turniere AS a'
		." WHERE a.vereinZPS = '$zps'"
		." AND a.sid = ".$sid
		." ORDER BY a.name DESC "
		;	 
		 
	return $query;
	}

	function getCLMTurniere( $options=array() )
	{
	$query	= $this->_getCLMTurniere( $options );
	$result = $this->_getList( $query );
	return @$result;
	}
	

	function _getCLMData ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$zps = clm_escape(JRequest::getVar('zps'));

		$db			= JFactory::getDBO();
		$id			= @$options['id'];

	$query	= "SELECT * "
		." FROM #__clm_vereine "
		." WHERE zps = '$zps' "
		." AND sid = ". $sid 
		;

		return $query;
	}

	function getCLMData ( $options=array() )
	{
		$query	= $this->_getCLMData( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMName ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$zps = clm_escape(JRequest::getVar('zps'));

		$db			= JFactory::getDBO();
		$id			= @$options['id'];

	$query	= "SELECT Vereinname "
		." FROM #__clm_dwz_vereine "
		." WHERE zps = '$zps' "
		." AND sid = ". $sid
		;

		return $query;
	}

	function getCLMName ( $options=array() )
	{
		$query	= $this->_getCLMName( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

////// Prüfen ob User berechtigt ist Daten zu ändern ///////////////////////////////////
	function _getCLMClmuser ( &$options )
	{
	$user =JFactory::getUser();
	$jid = $user->get('id');
	$sid = JRequest::getInt('saison','1');

		$db			= JFactory::getDBO();
		$id			= @$options['id'];

	$query	= "SELECT * "
		." FROM #__clm_user "
		." WHERE jid = $jid "
		." AND sid = " .$sid
		;

		return $query;
	}

	function getCLMClmuser ( $options=array() )
	{
		$query	= $this->_getCLMClmuser( $options );
		$result = $this->_getList( $query );
		return @$result;
	}


	
}
?>
