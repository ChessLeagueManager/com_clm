<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');


class CLMModelDWZ_Liga extends JModelLegacy
{
	
	function _getCLMLiga( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$lid	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];

	$row	= JTable::getInstance( 'ligen', 'TableCLM' );
	$row->load( $lid );

	if ($row->rang > 0) {
		$query = "SELECT  l.rang, l.name as lname, m.tln_nr, m.name, d.Spielername,d.DWZ as dsbDWZ,d.DWZ_Index, a.*, l.anzeige_ma"
			.",r.man_nr as rmnr, r.Rang as rrang, l.params "
			." FROM #__clm_meldeliste_spieler as a "
			." LEFT JOIN #__clm_rangliste_spieler as r on r.Gruppe = $row->rang AND r.ZPS = a.zps AND r.Mgl_Nr = a.mgl_nr AND r.sid = a.sid "
			." LEFT JOIN #__clm_rangliste_id as i on i.ZPS = a.zps AND i.gid = r.Gruppe AND i.sid = a.sid "
			." LEFT JOIN #__clm_dwz_spieler as d on d.ZPS = a.zps AND d.Mgl_Nr = a.mgl_nr AND d.sid = a.sid"
			." LEFT JOIN #__clm_mannschaften as m on m.zps = a.zps AND m.man_nr = a.mnr AND m.sid = a.sid AND m.liga = a.lid"
			." LEFT JOIN #__clm_liga AS l ON l.id = m.liga  AND l.sid = m.sid "
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$lid
			." AND a.status = ".$row->rang
			." AND a.Partien > 0 "
			." ORDER BY m.tln_nr ASC , rmnr ASC, rrang ASC "
			;
	} else {
		//der bisherige Ansatz versagt bei Spielgemeinschaften
		$query = "SELECT l.rang, l.name as lname, m.tln_nr, m.name, d.Spielername,d.DWZ as dsbDWZ,d.DWZ_Index, a.* "
			." FROM #__clm_meldeliste_spieler as a "
			." LEFT JOIN #__clm_dwz_spieler as d on d.zps = a.zps AND d.mgl_nr = a.mgl_nr AND d.sid = a.sid"
			." LEFT JOIN #__clm_mannschaften as m on m.zps = a.zps AND m.man_nr = a.mnr AND m.sid = a.sid AND m.liga = a.lid"
			." LEFT JOIN #__clm_liga AS l ON l.id = m.liga  AND l.sid = m.sid "
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$lid
			." AND a.status = ".$row->rang
			." AND a.Partien > 0 "
			." ORDER BY m.tln_nr ASC, a.mnr ASC, a.snr ASC ";
		//ein neuer Versuch auch für Spielgemeinschaften
		$query = "SELECT l.rang, l.name as lname, m.tln_nr, m.name, d.Spielername,d.DWZ as dsbDWZ,d.DWZ_Index, a.*, l.anzeige_ma, l.params"
			." FROM #__clm_mannschaften as m "
			." LEFT JOIN #__clm_liga AS l ON l.id = m.liga  AND l.sid = m.sid "
			//." LEFT JOIN #__clm_meldeliste_spieler as a on (a.zps = m.zps OR a.zps = m.sg_zps) AND a.mnr = m.man_nr AND a.sid = m.sid AND a.lid = m.liga"
			." LEFT JOIN #__clm_meldeliste_spieler as a on (a.zps = m.zps OR FIND_IN_SET(a.zps,m.sg_zps) != 0) AND a.mnr = m.man_nr AND a.sid = m.sid AND a.lid = m.liga"
			." LEFT JOIN #__clm_dwz_spieler as d on d.zps = a.zps AND d.mgl_nr = a.mgl_nr AND d.sid = a.sid"
			." WHERE m.sid = ".$sid
			." AND m.liga = ".$lid
			." AND a.Partien > 0 "
			." ORDER BY m.tln_nr ASC, a.mnr ASC, a.snr ASC ";
		}
	
		return $query;
	}

	function getCLMLiga( $options=array() )
	{
		$query	= $this->_getCLMLiga( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMSpieler( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$lid	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
	$query = " SELECT a.tln_nr, COUNT(a.zps) as count, SUM(m.DWZ) as dwz, SUM(m.Punkte) as punkte, SUM(m.I0) as i0, SUM(m.Partien) as partien, SUM(m.We) as we, SUM(m.Leistung) as leistung,"
		." SUM(m.EFaktor) as efaktor, SUM(m.Niveau) as niveau, SUM(d.DWZ) as dsbDWZ, SUM(m.start_dwz) as start_dwz"
		." FROM #__clm_mannschaften as a "
		." LEFT JOIN #__clm_meldeliste_spieler AS m ON m.lid = a.liga AND ( m.zps = a.zps OR FIND_IN_SET(m.zps,a.sg_zps) != 0) AND m.mnr = a.man_nr AND m.sid = a.sid "
		." LEFT JOIN #__clm_dwz_spieler AS d ON d.ZPS = m.zps  AND d.Mgl_Nr = m.mgl_nr AND d.sid = m.sid"
		." WHERE a.liga = ".$lid
		." AND a.sid = ".$sid
		." AND m.mgl_nr > 0 "
		." AND m.Partien > 0 "
		." GROUP BY a.tln_nr "
		;
		return $query;
	}

	function getCLMSpieler( $options=array() )
	{
		$query	= $this->_getCLMSpieler( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMdwz( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$lid	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
	$query = " SELECT a.name, a.published, s.datum as dsb_datum "
		." FROM #__clm_liga as a "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE a.id = ".$lid
		." AND a.sid = ".$sid
		." AND s.published = 1"
		;
		return $query;
	}

	function getCLMdwz( $options=array() )
	{
		$query	= $this->_getCLMdwz( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
}
?>
