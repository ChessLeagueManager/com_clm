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

defined('_JEXEC') or die();
jimport('joomla.application.component.model');


class CLMModelInfo extends JModel
{

	function CLMSid()
	{
	$db			= JFactory::getDBO();
	$sid = JRequest::getInt('saison','0');     	//klkl
	If ($sid == 0) {							//klkl
		$query = " SELECT id,name FROM #__clm_saison"
			." WHERE published = 1 "
			." AND archiv = 0 "
			." ORDER BY id DESC LIMIT 1 "
			;
 
		$db->setQuery( $query);
		$saison	= $db->loadObjectList();
		$sid	= $saison[0]->id;
	}											//klkl 
		return $sid;
	}

	function _getCLMSaison( &$options )
	{
	$sid = CLMModelInfo::CLMSid();				
		$db			= JFactory::getDBO();
		$id			= @$options['id'];

		$query = " SELECT id,name,bemerkungen,datum as dsb_datum FROM #__clm_saison"
			." WHERE published = 1 "
			//." AND archiv = 0 "				
			." AND id =".$sid					
			." ORDER BY id DESC LIMIT 1 "
			;

		return $query;
	}

	function getCLMSaison( $options=array() )
	{
		$query	= $this->_getCLMSaison( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMRemis( &$options )
	{
	$sid = CLMModelInfo::CLMSid();
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT COUNT(id) as remis FROM #__clm_rnd_spl"
			." WHERE weiss = 1 AND ergebnis = 2 AND sid = ".$sid
			;
		return $query;
	}

	function getCLMRemis( $options=array() )
	{
		$query	= $this->_getCLMRemis( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMKampflos( &$options )
	{
	$sid = CLMModelInfo::CLMSid();
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT COUNT(id) as kampflos FROM #__clm_rnd_spl"
			." WHERE weiss = 1 AND ergebnis > 2 AND sid = ".$sid
			;
		return $query;
	}

	function getCLMKampflos( $options=array() )
	{
		$query	= $this->_getCLMKampflos( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMHeim( &$options )
	{
	$sid = CLMModelInfo::CLMSid();
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,SUM(punkte) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1"
			." GROUP BY sid "
			;
		return $query;
	}

	function getCLMHeim( $options=array() )
	{
		$query	= $this->_getCLMHeim( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMGast( &$options )
	{
	$sid = CLMModelInfo::CLMSid();
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,SUM(punkte) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 0"
			." GROUP BY sid "
			;
		return $query;
	}

	function getCLMGast( $options=array() )
	{
		$query	= $this->_getCLMGast( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMGesamt( &$options )
	{
	$sid = CLMModelInfo::CLMSid();
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT COUNT(id) as gesamt FROM #__clm_rnd_spl"
			." WHERE weiss = 1 AND sid = ".$sid
			;
		return $query;
	}

	function getCLMGesamt( $options=array() )
	{
		$query	= $this->_getCLMGesamt( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMSpieler( &$options )
	{
	$sid = CLMModelInfo::CLMSid();
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT a.mgl_nr,a.zps,a.Punkte,a.Niveau,a.Leistung,a.ZPS,a.DWZ,a.Spielername,v.Vereinname "
			." FROM #__clm_dwz_spieler as a"
			." LEFT JOIN #__clm_dwz_vereine as v ON v.ZPS = a.zps AND v.sid = a.sid"
			." WHERE a.sid = ".$sid
			." AND a.Partien > 0"
			." ORDER BY a.Punkte DESC, a.Niveau DESC LIMIT 10"
			;
		return $query;
	}

	function getCLMSpieler( $options=array() )
	{
		$query	= $this->_getCLMSpieler( $options );
		$result = $this->_getList( $query );

		return @$result;
	}

	function checkSpieler( $punkte )
	{
	$sid = CLMModelInfo::CLMSid();
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT COUNT(lid) as id FROM #__clm_meldeliste_spieler "
			." WHERE Punkte =".$punkte
			." AND a.sid = ".$sid
			;
		$db->setQuery( $query);
		$count	= $db->loadObjectList();
		if (isset($count[0])) $anzahl = $count[0]->id; else $anzahl = 0;

		return $anzahl;
	}

	function _getCLMMannschaft( &$options )
	{
	$sid = CLMModelInfo::CLMSid();

		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT a.tln_nr,a.lid,l.name as liga,l.stamm,l.man_sieg,l.man_antritt, "
			." m.name, COUNT(a.id) as count, SUM(manpunkte) as mp, SUM(brettpunkte) as bp"
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_liga as l ON  l.id = a.lid AND l.sid = a.sid "
			." LEFT JOIN #__clm_mannschaften as m ON  m.liga = a.lid AND m.tln_nr = a.tln_nr AND m.sid = a.sid "
			//." WHERE a.heim = 1 "
			." WHERE a.manpunkte > -1 "
			." AND a.sid = ".$sid
			." GROUP BY a.lid,a.tln_nr "
			." ORDER BY mp DESC, bp DESC "
			." LIMIT 20 "
			;
		return $query;
	}

	function getCLMMannschaft( $options=array() )
	{
		$query	= $this->_getCLMMannschaft( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function Bretter()
	{
	$sid = CLMModelInfo::CLMSid();
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT stamm FROM #__clm_liga "
			." WHERE sid =".$sid
			." ORDER BY stamm DESC LIMIT 1"
			;
		$db->setQuery( $query);
		$count	= $db->loadObjectList();
		$anzahl = $count[0]->stamm;

		return $anzahl;
	}

	function _getCLMBrett( &$options )
	{
	$sid = CLMModelInfo::CLMSid();

		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,SUM(punkte) as sum, COUNT(id) as count FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1"
			." GROUP BY brett "
			." ORDER BY brett ASC  "
			;
		return $query;
	}

	function getCLMBrett( $options=array() )
	{
		$query	= $this->_getCLMBrett( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMWBrett( &$options )
	{
	$sid = CLMModelInfo::CLMSid();

		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1"
			." AND ergebnis = 0"
			." GROUP BY brett "
			." ORDER BY brett ASC  "
			;
		return $query;
	}

	function getCLMWBrett( $options=array() )
	{
		$query	= $this->_getCLMWBrett( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMSBrett( &$options )
	{
	$sid = CLMModelInfo::CLMSid();

		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1"
			." AND ergebnis = 1"
			." GROUP BY brett "
			." ORDER BY brett ASC  "
			;
		return $query;
	}

	function getCLMSBrett( $options=array() )
	{
		$query	= $this->_getCLMSBrett( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMRBrett( &$options )
	{
	$sid = CLMModelInfo::CLMSid();

		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1"
			." AND ergebnis = 2"
			." GROUP BY brett "
			." ORDER BY brett ASC  "
			;
		return $query;
	}

	function getCLMRBrett( $options=array() )
	{
		$query	= $this->_getCLMRBrett( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMKBrett( &$options )
	{
	$sid = CLMModelInfo::CLMSid();

		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1"
			." AND ergebnis > 2"
			." GROUP BY brett "
			." ORDER BY brett ASC  "
			;
		return $query;
	}

	function getCLMKBrett( $options=array() )
	{
		$query	= $this->_getCLMKBrett( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
}
?>
