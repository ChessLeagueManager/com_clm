<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelStatistik extends JModelLegacy
{

	function _getCLMLiga( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$lid	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
	$query = " SELECT a.* FROM #__clm_liga as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE a.id = $lid AND a.sid = $sid "
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

	function getCLMSchwarz( $options=array() )
	{
		$query	= $this->_getCLMSchwarz( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMRemis( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT COUNT(id) as remis FROM #__clm_rnd_spl"
			." WHERE weiss = 1 AND (ergebnis = 2 OR ergebnis > 8) AND sid = $sid AND lid = $lid "
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
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT COUNT(id) as kampflos FROM #__clm_rnd_spl"
			." WHERE weiss = 1 AND ergebnis > 2 AND ergebnis < 9 AND sid = $sid AND lid = $lid "
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
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,SUM(punkte) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1 AND lid = $lid "
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
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,SUM(punkte) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 0 AND lid = $lid "
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
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT COUNT(id) as gesamt FROM #__clm_rnd_spl"
			." WHERE weiss = 1 AND sid = $sid AND lid = $lid "
			;

		return $query;
	}

	function getCLMGesamt( $options=array() )
	{
		$query	= $this->_getCLMGesamt( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMBestenliste( &$options )
	{
	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd( 'option' );

	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');	
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
	$query	= " SET SQL_BIG_SELECTS=1";
	$db->setQuery($query);
	$db->query();

	$query = " SELECT a.rang, a.params FROM #__clm_liga as a"
		." WHERE a.id = $lid "
		." AND a.published = 1"
	;
	$db->setQuery( $query);
	$result=$db->loadObjectList();
	//Parameter aufbereiten
	$rang = $result[0]->rang;
	$paramsStringArray = explode("\n", $result[0]->params);
	$params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	$sort_string = "";
	if (!isset($params['btiebr1']) OR $params['btiebr1'] == 0) $sort_string = " g.gpunkte DESC, g.gpartien ASC, a.Leistung DESC, a.Niveau DESC ";
	else {
		for ($x=1; $x<5; $x++) {
			if ($x != 1 AND $params['btiebr'.$x] > 0) $sort_string .= ",";
			//if ($params['btiebr'.$x] == 1) $sort_string .= " g.gpunkte DESC ";
			if ($params['btiebr'.$x] == 1) $sort_string .= " a.Punkte DESC ";
			//elseif ($params['btiebr'.$x] == 2) $sort_string .= " g.gpartien ASC ";
			elseif ($params['btiebr'.$x] == 2) $sort_string .= " a.Partien ASC ";
			elseif ($params['btiebr'.$x] == 3) $sort_string .= " a.Niveau DESC ";
			elseif ($params['btiebr'.$x] == 4) $sort_string .= " a.Leistung DESC ";
			//elseif ($params['btiebr'.$x] == 5) $sort_string .= " gprozent DESC ";
			elseif ($params['btiebr'.$x] == 5) $sort_string .= " Prozent DESC ";
			elseif ($params['btiebr'.$x] == 6) $sort_string .= " e.epunkte DESC ";
			elseif ($params['btiebr'.$x] == 7) $sort_string .= " e.epartien ASC ";
			elseif ($params['btiebr'.$x] == 8) $sort_string .= " eprozent ASC ";
			elseif ($params['btiebr'.$x] == 9) $sort_string .= " ebrett ASC ";
		}
	} 
		$query = " SELECT a.lid, a.mnr, a.snr, a.mgl_nr, a.PKZ, a.zps, a.Punkte, a.Partien, a.Niveau, a.Leistung, d.ZPS, d.DWZ "
			.", d.Spielername, v.Vereinname, m.tln_nr, a.Punkte*100/a.Partien as Prozent, 0 as leistung100 "
			.", e.epunkte, e.epartien, e.epunkte*100/e.epartien as eprozent, e.ebrett "
			.", g.gpunkte, g.gpartien, g.gpunkte*100/g.gpartien as gprozent, g.gbrett "
			." FROM #__clm_meldeliste_spieler as a";
		if ($countryversion =="de") {
			$query .= " LEFT JOIN #__clm_dwz_spieler as d ON d.ZPS = a.zps AND d.Mgl_Nr = a.mgl_nr AND d.sid = a.sid";
		} else {
			$query .= " LEFT JOIN #__clm_dwz_spieler as d ON d.ZPS = a.zps AND d.PKZ = a.PKZ AND d.sid = a.sid";
		}
		$query .= " LEFT JOIN #__clm_dwz_vereine as v ON v.ZPS = a.zps  AND v.sid = a.sid"
			." LEFT JOIN #__clm_mannschaften as m ON m.liga = a.lid AND m.sid = a.sid AND (m.zps = a.zps OR FIND_IN_SET(a.zps,m.sg_zps) != 0) AND m.man_nr = a.mnr"			
			." LEFT JOIN ( SELECT *, SUM(punkte) as gpunkte, COUNT(punkte) as gpartien, SUM(brett) as gbrett FROM #__clm_rnd_spl "
			."  WHERE (ergebnis < 3 OR ergebnis > 8) ";
		if ($countryversion =="de") {
			$query .= "  GROUP BY lid, tln_nr, zps, spieler) g "
					."  ON g.lid = a.lid AND g.sid = a.sid AND g.zps = a.zps AND g.spieler = a.mgl_nr AND g.tln_nr = m.tln_nr";
		} else {
			$query .= "  GROUP BY lid, tln_nr, zps, PKZ) g "
					."  ON g.lid = a.lid AND g.sid = a.sid AND g.zps = a.zps AND g.PKZ = a.PKZ AND g.tln_nr = m.tln_nr";
		}	
		$query .= " LEFT JOIN ( SELECT *, SUM(punkte) as epunkte, COUNT(punkte) as epartien, SUM(brett) as ebrett FROM #__clm_rnd_spl "
			."  WHERE (ergebnis < 7 OR ergebnis > 8) ";
		if ($countryversion =="de") {
			$query .= "  GROUP BY lid, tln_nr, zps, spieler) e "
					."  ON e.lid = a.lid AND e.sid = a.sid AND e.zps = a.zps AND e.spieler = a.mgl_nr AND e.tln_nr = m.tln_nr";
		} else {
			$query .= "  GROUP BY lid, tln_nr, zps, PKZ) e "
					."  ON e.lid = a.lid AND e.sid = a.sid AND e.zps = a.zps AND e.PKZ = a.PKZ AND e.tln_nr = m.tln_nr";
		}
		$query .= " WHERE a.lid = $lid AND epartien > 0"
			." AND a.sid = ".$sid
			." AND a.status = ".$rang;
		if ($countryversion =="de") {
			$query .= " GROUP BY a.zps, a.mgl_nr, a.snr ";
		} else {
			$query .= " GROUP BY a.zps, a.PKZ, a.snr ";
		}

		$filter_order     = $mainframe->getUserStateFromRequest( $option.'filter_order_bl', 'filter_order', 'Punkte', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir_bl', 'filter_order_Dir', '', 'word' );
			
		if(!empty($filter_order) && !empty($filter_order_Dir) ){
 			$query .= ' ORDER BY '.$filter_order.' '.$filter_order_Dir .', '.$sort_string;
		} else {
			$query .= ' ORDER BY '.$sort_string;
		}
		
		return $query;
	}

	function getCLMBestenliste( $options=array() )
	{
		$query	= $this->_getCLMBestenliste( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	public static function checkSpieler( $punkte )
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT COUNT(lid) as id FROM #__clm_meldeliste_spieler "
			." WHERE Punkte = $punkte AND lid = $lid "
			;
		$db->setQuery( $query);
		$count	= $db->loadObjectList();
		$anzahl = $count[0]->id;

		return $anzahl;
	}

	function _getCLMMannschaft( &$options )
	{
	$lid	= JRequest::getInt('liga','1');
	$sid	= JRequest::getInt('saison','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
		$query = " SELECT a.tln_nr,l.name as liga,l.stamm,m.name, COUNT(a.id) as count, SUM(manpunkte) as mp, SUM(brettpunkte) as bp"
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_liga as l ON  l.id = a.lid AND l.sid = a.sid "
			." LEFT JOIN #__clm_mannschaften as m ON  m.liga = a.lid AND m.tln_nr = a.tln_nr AND m.sid = a.sid "
			//." WHERE a.heim = 1 "
			." WHERE a.manpunkte > -1 AND lid = $lid "
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

	public static function Bretter()
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT stamm FROM #__clm_liga "
			." WHERE sid =$sid AND id = $lid "
			." ORDER BY stamm DESC LIMIT 1"
			;
		$db->setQuery( $query);
		$count	= $db->loadObjectList();
		$anzahl = $count[0]->stamm;

		return $anzahl;
	}

	function _getCLMBrett( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,SUM(punkte) as sum, COUNT(id) as count FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1 AND lid = $lid AND `gemeldet` != 0"
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

	function _getCLMGBrett( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,SUM(punkte) as sum, COUNT(id) as count FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 0 AND lid = $lid AND `gemeldet` != 0"

			." GROUP BY brett "
			." ORDER BY brett ASC  "
			;

		return $query;
	}

	function getCLMGBrett( $options=array() )
	{
		$query	= $this->_getCLMGBrett( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	public static function CLMBrett_all( $bretter )
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');
		$db			= JFactory::getDBO();

// wg
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1 AND lid = $lid "
			//." AND weiss = 0 AND ergebnis = 1"
			." AND weiss = 1 AND ergebnis = 1"		//klkl
			." GROUP BY brett "
			." ORDER BY brett, dg, runde, paar "
			;
	$db->setQuery( $query);
	$wg= $db->loadObjectList();
// wu
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1 AND lid = $lid "
			//." AND weiss = 1 AND ergebnis = 0"
			." AND weiss = 0 AND ergebnis = 0"		//klkl
			." GROUP BY brett "
			." ORDER BY brett, dg, runde, paar "
			;
	$db->setQuery( $query);
	$wu= $db->loadObjectList();
// sg
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1 AND lid = $lid "
			//." AND weiss = 0 AND ergebnis = 0"
			." AND weiss = 1 AND ergebnis = 0"		//klkl
			." GROUP BY brett "
			." ORDER BY brett, dg, runde, paar "
			;
	$db->setQuery( $query);
	$sg= $db->loadObjectList();
// su
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1 AND lid = $lid "
			//." AND weiss = 1 AND ergebnis = 1"
			." AND weiss = 0 AND ergebnis = 1"		//klkl
			." GROUP BY brett "
			." ORDER BY brett, dg, runde, paar "
			;
	$db->setQuery( $query);
	$su= $db->loadObjectList();

	$wgc = 0;
	$wuc = 0;
	$sgc = 0;
	$suc = 0;


	for ($x=1; $x < (1+$bretter); $x++) {

	  $sum_weiss	= 0;
	  $sum_schwarz	= 0;

	// ungerades Brett
	  //if  ($x%2 == 0) {		
	  if  ($x%2 !== 0) {		//klkl
	    if(isset($wu[$wuc]) AND $wu[$wuc]->brett == $x) { $sum_weiss = $wu[$wuc]->sum; $wuc++;}
	    if(isset($su[$suc]) AND $su[$suc]->brett == $x) { $sum_schwarz = $su[$suc]->sum; $suc++;}

	// gerades Brett
	    } else {
	    if(isset($wg[$wgc]) AND $wg[$wgc]->brett == $x) { $sum_weiss = $wg[$wgc]->sum; $wgc++;}
	    if(isset($sg[$sgc]) AND $sg[$sgc]->brett == $x) { $sum_schwarz = $sg[$sgc]->sum; $sgc++;}
		}

		$all[] = array('w' => "$sum_weiss", 's' => "$sum_schwarz");
		}
		return $all;
	}


	function _getCLMRBrett( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND heim = 1 AND lid = $lid "
			." AND (ergebnis = 2 OR ergebnis > 8)"
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
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT brett,COUNT(id) as sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND weiss = 1 AND lid = $lid "
			//." WHERE sid = $sid AND heim = 1 AND lid = $lid "
			." AND (ergebnis > 2 AND ergebnis < 9)"
			//." AND `kampflos` != 0"
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

	function _getCLMkvMannschaft( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT tln_nr, COUNT(id) as kv_sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND lid = $lid "
			." AND ((`ergebnis` = 4 AND `heim` = 1) OR (`ergebnis` = 5 AND `heim` = 0) OR (`ergebnis` = 6))"
			." GROUP BY tln_nr "
			." ORDER BY tln_nr DESC  "
			;

		return $query;
	}

	function getCLMkvMannschaft( $options=array() )
	{
		$query	= $this->_getCLMkvMannschaft( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMkgMannschaft( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT tln_nr, COUNT(id) as kg_sum FROM #__clm_rnd_spl "
			." WHERE sid = $sid AND lid = $lid "
			." AND ((`ergebnis` = 4 AND `heim` = 0) OR (`ergebnis` = 5 AND `heim` = 1))"
			." GROUP BY tln_nr "
			." ORDER BY tln_nr DESC  "
			;

		return $query;
	}

	function getCLMkgMannschaft( $options=array() )
	{
		$query	= $this->_getCLMkgMannschaft( $options );
		$result = $this->_getList( $query );
		return @$result;
	}


}
?>
