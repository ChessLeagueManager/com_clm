<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelTabelle extends JModelLegacy
{
	
	function _getCLMLiga( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
		$query = " SELECT a.*, u.name as sl, u.email FROM #__clm_liga as a"
			." LEFT JOIN #__clm_user as u ON a.sl = u.jid AND u.sid = a.sid"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." WHERE a.id = ".$liga
			//." AND a.sid = ".$sid         //da die liga-nummer saisonübergreifend vergeben wird, kann auf die test bzgl. sid verzichtet werden
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
	function _getCLMSpielfrei( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
		$query = "SELECT COUNT(tln_nr) AS count FROM #__clm_mannschaften "
			." WHERE liga = ".$liga
			//." AND sid = ".$sid
			." AND man_nr = 0"
			;
		return $query;
	}
	function getCLMSpielfrei( $options=array() )
	{
		$query	= $this->_getCLMSpielfrei( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMPunkte( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$runde	= JRequest::getInt('runde');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
	// ordering für Rangliste -> Ersatz für direkten Vergleich
		$query = "SELECT a.* FROM #__clm_liga as a"
			." WHERE id = ".$liga
			//." AND sid = ".$sid
			;
		$db->setQuery($query);
 		$order = $db->loadObjectList();
 			if ($order[0]->order == 1) { $ordering = " , m.ordering ASC";}
			//else { $ordering =', a.tln_nr ASC ';} 
			else { $ordering =' ';} 
		$query = " SELECT a.tln_nr as tln_nr,m.name as name, "
			." (SUM(a.manpunkte) - m.abzug) as mp, m.abzug as abzug, "
			." (SUM(a.brettpunkte) - m.bpabzug) as bp, m.bpabzug, SUM(a.wertpunkte) as wp, m.published, m.man_nr, COUNT(DISTINCT a.runde, a.dg) as spiele, "
			." SUM(case when a.manpunkte IS NULL then 0 else 1 end) as count_G, "
			." SUM(case when a.manpunkte = ".($order[0]->man_sieg+$order[0]->man_antritt)." then 1 else 0 end) as count_S, "
			." SUM(case when a.manpunkte = ".($order[0]->man_remis+$order[0]->man_antritt)." then 1 else 0 end) as count_R, "
			." SUM(case when a.manpunkte = ".($order[0]->man_nieder+$order[0]->man_antritt)." then 1 else 0 end) as count_V, "
			." m.sumtiebr1, m.sumtiebr2, m.sumtiebr3 "
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_mannschaften as m ON m.liga = $liga AND m.tln_nr = a.tln_nr "
			." WHERE a.lid = ".$liga
			." AND m.man_nr <> 0 ";
			//if ($runde != "") { $query = $query." AND runde < ".($runde +1);}
		$query = $query	
			." GROUP BY a.tln_nr ";
		if ($order[0]->b_wertung == 0 AND $order[0]->liga_mt == 0) {   
			$query = $query
			." ORDER BY mp DESC, bp DESC".$ordering; }
		if ($order[0]->b_wertung == 3 AND $order[0]->liga_mt == 0) { 
			$query = $query
			." ORDER BY mp DESC, bp DESC, wp DESC".$ordering; }
		if ($order[0]->b_wertung == 4 AND $order[0]->liga_mt == 0) { 
			$query = $query
			." ORDER BY mp DESC, bp DESC".$ordering.", wp DESC"; }
		if ($order[0]->liga_mt == 1) {                       //mtmt
			$query = $query
			." ORDER BY rankingpos ASC"; }
		$query = $query
			.", a.tln_nr ASC"; 		
		return $query;
	}
	function getCLMPunkte( $options=array() )
	{
		$query	= $this->_getCLMPunkte( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMDWZSchnitt ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];
		$query = " SELECT stamm,ersatz FROM #__clm_liga "
			." WHERE id = ".$liga
			;
		$db->setQuery( $query);
		$row_tln=$db->loadObjectList();
		$tln	= $row_tln[0]->stamm;

		$query = " SELECT l.rang,a.zps as zps, a.sg_zps as sgzps, a.man_nr as man_nr"
			." FROM #__clm_mannschaften as a "
			." LEFT JOIN #__clm_liga as l ON l.id =".$liga
			." WHERE a.liga = ".$liga
			//." AND a.sid = ".$sid
			//." AND a.tln_nr = ".$tln
			;
		$db->setQuery($query);
		$man	=$db->loadObjectList();
		$rang	=$man[0]->rang;
	
	if ($rang > 0) {
	$query = " SELECT e.tln_nr as tlnr,AVG(d.DWZ) as dwz,AVG(a.start_dwz) as start_dwz"
			." FROM #__clm_meldeliste_spieler as a"
			." LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = a.mgl_nr AND d.ZPS = a.zps AND d.sid = a.sid AND d.DWZ !=0)"
//			." LEFT JOIN #__clm_mannschaften AS e ON (e.sid=a.sid AND e.liga= a.lid AND (e.zps=a.zps OR e.sg_zps=a.zps) AND e.man_nr = a.mnr AND e.man_nr !=0 AND e.liste !=0) "
			." LEFT JOIN #__clm_mannschaften AS e ON (e.sid=a.sid AND e.liga= a.lid AND (e.zps=a.zps OR FIND_IN_SET(a.zps,e.sg_zps) != 0) AND e.man_nr = a.mnr AND e.man_nr !=0 AND e.liste !=0) "
			." LEFT JOIN #__clm_rangliste_spieler as r on r.ZPS = a.zps AND r.Mgl_Nr = a.mgl_nr AND r.sid = a.sid "
			." LEFT JOIN #__clm_rangliste_id as i on i.ZPS = a.zps AND i.gid = r.Gruppe AND i.sid = a.sid "
			." WHERE a.lid = ".$liga
			//." AND a.sid = ".$sid
			." AND e.tln_nr IS NOT NULL "
			." AND a.snr < ".($tln+1)
			//." AND e.man_nr <> 0 "
			//." AND e.liste <> 0 "
			//." AND d.DWZ > 0 "
			//." AND d.DWZ <> ''"
			// Verursachen massive Perfomance Probleme : Abfrage dauert 15-20 Sekunden !!!
			// stattdessen AND d.DWZ !=0 im JOIN
			." GROUP BY e.tln_nr"
			//." LIMIT 0, ".$tln
			;
	} else {	
	$query = " SELECT e.tln_nr as tlnr,AVG(d.DWZ) as dwz,AVG(a.start_dwz) as start_dwz"
			." FROM #__clm_meldeliste_spieler as a"
			." LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = a.mgl_nr AND d.ZPS = a.zps AND d.sid = a.sid AND d.DWZ !=0)"
//			." LEFT JOIN #__clm_mannschaften AS e ON (e.sid=a.sid AND e.liga= a.lid AND (e.zps=a.zps OR e.sg_zps=a.zps) AND e.man_nr = a.mnr AND e.man_nr !=0 AND e.liste !=0) "
			." LEFT JOIN #__clm_mannschaften AS e ON (e.sid=a.sid AND e.liga= a.lid AND (e.zps=a.zps OR FIND_IN_SET(a.zps,e.sg_zps) != 0) AND e.man_nr = a.mnr AND e.man_nr !=0 AND e.liste !=0) "
			." WHERE a.lid = ".$liga
			//." AND a.sid = ".$sid
			." AND e.tln_nr IS NOT NULL "
			." AND a.snr < ".($tln+1)
			//." AND e.man_nr <> 0 "
			//." AND e.liste <> 0 "
			//." AND d.DWZ > 0 "
			//." AND d.DWZ <> ''"
			// Verursachen massive Perfomance Probleme : Abfrage dauert 15-20 Sekunden !!!
			// stattdessen AND d.DWZ !=0 im JOIN
			." GROUP BY e.tln_nr"
			//." LIMIT 0, ".$tln
			;
	}		
		return $query;
	}
	function getCLMDWZSchnitt ( $options=array() )
	{
		$query	= $this->_getCLMDWZSchnitt( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	public static function punkte_tlnr ( $sid, $lid, $tlnr, $dg, $runden_modus )
	{
	$db	= JFactory::getDBO();
	$query = " SELECT a.runde,a.tln_nr,a.gegner,a.runde, a.brettpunkte, m.rankingpos, m.name "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_mannschaften AS m ON m.liga = a.lid AND m.tln_nr = a.gegner "
		." WHERE a.lid = ".$lid
		//." AND a.sid = ".$sid
		." AND a.tln_nr = ".$tlnr
		." AND a.dg = $dg "
		//." ORDER BY a.gegner "
		;
	if ($runden_modus == 3) $query .= " ORDER BY a.runde";	
	else $query .= " ORDER BY a.gegner ";
	$db 	=JFactory::getDBO();
	$db->setQuery( $query );
	$runden	=$db->loadObjectList();
	
	return $runden;
	}
	
	//neu: Mannschaften der entspr. Liga (klkl)
	function _getCLMMannschaft( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = "SELECT a.zps,a.sg_zps,l.durchgang as dg, l.rang as lrang,"
			." l.name as liga_name, l.runden as runden, l.published as lpublished, a.* "
			." FROM #__clm_mannschaften as a "
			." LEFT JOIN #__clm_liga AS l ON l.id = a.liga"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." WHERE a.liga = ".$liga
			//." AND a.sid = ".$sid
			." AND s.published = 1"
			." ORDER BY a.tln_nr"
			;	
		return $query;
	}

	function getCLMMannschaft( $options=array() )
	{
		$query	= $this->_getCLMMannschaft( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	//neu: Mannschaftsleiter der entspr. Liga (klkl)
	function _getCLMMLeiter( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = "SELECT a.zps,u.name as mf_name,u.email as email, "
			." u.tel_mobil,u.tel_fest, a.* "
			." FROM #__clm_mannschaften as a "
			." LEFT JOIN #__clm_user AS u ON u.jid = a.mf AND u.sid = a.sid"
			." LEFT JOIN #__clm_liga AS l ON l.id = a.liga"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." WHERE a.liga = ".$liga
			//." AND a.sid = ".$sid
			." AND s.published = 1"
			." ORDER BY a.tln_nr"
			;	
		return $query;
	}

	function getCLMMLeiter( $options=array() )
	{
		$query	= $this->_getCLMMLeiter( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	//neu: Meldelisten der entspr. Mannschaften (klkl)
	function _getCLMCount ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');

		$db			= JFactory::getDBO();
		$id			= @$options['id'];
		
		$query = " SELECT l.rang,a.zps as zps, a.sg_zps as sgzps, a.man_nr as man_nr, l.ersatz_regel as ersatz_regel"
			." FROM #__clm_mannschaften as a "
			." LEFT JOIN #__clm_liga as l ON l.id =".$liga
			." WHERE a.liga = ".$liga
			//." AND a.sid = ".$sid
			;
		$db->setQuery($query);
		$man	=$db->loadObjectList();
		$zps	=$man[0]->zps;
		$sgzps	=$man[0]->sgzps;
		$mnr	=$man[0]->man_nr;
		$rang	=$man[0]->rang;
		$ersatz_regel	=$man[0]->ersatz_regel;
		
	if ($rang > 0) {
		$query = " SELECT m.tln_nr as tln_nr,a.snr,a.dwz,a.mgl_nr,a.zps, d.Spielername as name,d.DWZ as dwz,a.start_dwz "
			.",r.man_nr as rmnr, r.Rang as rrang "
			." FROM #__clm_meldeliste_spieler as a "
			." LEFT JOIN #__clm_rangliste_spieler as r on r.ZPS = a.zps AND r.Mgl_Nr = a.mgl_nr AND r.sid = a.sid "
			." LEFT JOIN #__clm_rangliste_id as i on i.ZPS = a.zps AND i.gid = r.Gruppe AND i.sid = a.sid "
			." LEFT JOIN #__clm_dwz_spieler as d on d.zps = a.zps AND d.mgl_nr = a.mgl_nr AND d.sid = a.sid"
//			." LEFT JOIN #__clm_mannschaften as m on m.liga = a.lid AND (m.zps = a.zps OR m.sg_zps = a.zps) AND m.man_nr = a.mnr AND m.sid = a.sid"
			." LEFT JOIN #__clm_mannschaften as m on m.liga = a.lid AND (m.zps = a.zps OR FIND_IN_SET(a.zps,m.sg_zps) != 0) AND m.man_nr = a.mnr AND m.sid = a.sid"
			." WHERE a.lid = ".$liga
			//." AND a.sid = ".$sid
			." AND r.Gruppe = $rang ";
		if ($ersatz_regel == 0) 
			$query .= " AND r.man_nr NOT IN ( SELECT aa.man_nr FROM #__clm_mannschaften as aa "
					." WHERE aa.liga = ".$liga
					//." AND aa.sid = ".$sid
					." AND ( aa.zps = r.ZPS )"
					." AND aa.man_nr <> a.mnr )";
		$query .= " ORDER BY tln_nr ASC, rmnr ASC, rrang ASC ";
	} else {
		$query = " SELECT m.tln_nr as tln_nr,a.snr,a.dwz,a.mgl_nr,a.zps, d.Spielername as name,d.DWZ as dwz,a.start_dwz "
			." FROM #__clm_meldeliste_spieler as a "
			." LEFT JOIN #__clm_dwz_spieler as d on d.zps = a.zps AND d.mgl_nr = a.mgl_nr AND d.sid = a.sid"
			." LEFT JOIN #__clm_mannschaften as m on m.liga = a.lid AND (m.zps = a.zps OR FIND_IN_SET(a.zps,m.sg_zps) != 0) AND m.man_nr = a.mnr AND m.sid = a.sid"
			." WHERE a.lid = ".$liga
			//." AND a.sid = ".$sid
//			." AND (m.zps = a.zps OR m.sg_zps = a.zps) "
			." AND (m.zps = a.zps OR FIND_IN_SET(a.zps,m.sg_zps) != 0) "
			." AND a.zps > '0' "
			." ORDER BY tln_nr ASC, a.snr ASC ";
		}	
		return $query;
	}

	function getCLMCount ( $options=array() )
	{
		$query	= $this->_getCLMCount( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	//neu: Saison (klkl)
	function _getCLMSaison ( &$options )
	{
 
		$sid	= JRequest::getInt('saison','1');
		$liga	= JRequest::getInt('liga','1');
		$db			= JFactory::getDBO();
		$id			= @$options['id'];

		$query = " SELECT s.name FROM #__clm_liga as a"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." WHERE a.id = ".$liga
			//." AND a.sid = ".$sid
			." AND s.published = 1"
			;
		return $query;
	}

	function getCLMSaison ( $options=array() )
	{
		$query	= $this->_getCLMSaison( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	function _getCLMBP ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];

		$query = " SELECT a.tln_nr,a.brettpunkte,a.runde,a.dg,a.paar,a.heim "
			." FROM #__clm_rnd_man as a "
			." WHERE a.lid = ".$liga
			//." AND a.sid = ".$sid
			." ORDER BY a.tln_nr ASC,a.dg ASC ,a.runde ASC "
			;
		return $query;
	}

	function getCLMBP ( $options=array() )
	{
		$query	= $this->_getCLMBP( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMSumBP ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];

		$query = " SELECT tln_nr, SUM(brettpunkte) as summe "
			." FROM #__clm_rnd_man"
			." WHERE lid = ".$liga
			." GROUP BY tln_nr ORDER BY tln_nr ASC"
			;
		return $query;
	}

	function getCLMSumBP ( $options=array() )
	{
		$query	= $this->_getCLMSumBP( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMSumPlan ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');
	
		$query = " SELECT a.dg,a.lid,a.sid,a.runde,a.paar,a.tln_nr,a.gegner "
			//." ,t.name as dat_name, t.datum as datum "
			." ,m.name as hname, n.name as gname, m.published as hpublished, "
			." n.published as gpublished "
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_mannschaften as m ON m.tln_nr = a.tln_nr AND m.sid = a.sid AND m.liga = a.lid "
			." LEFT JOIN #__clm_mannschaften as n ON n.tln_nr = a.gegner AND n.sid = a.sid AND n.liga = a.lid"
			//." LEFT JOIN #__clm_runden_termine as t ON t.nr = a.runde AND t.liga = $liga AND t.sid = a.sid "
			." WHERE a.lid =".$liga
			//." AND a.sid =".$sid
			." AND a.heim = 1"
			." ORDER BY a.dg ASC, a.runde ASC, a.paar ASC "
			;
		return $query;
	}

	function getCLMSumPlan ( $options=array() )
	{
		$query	= $this->_getCLMSumPlan( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMTermin( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = "SELECT nr, datum FROM #__clm_runden_termine "
			." WHERE liga = ".$liga
			//." AND sid = ".$sid
			." ORDER BY nr "
			;
		return $query;
	}
	function getCLMTermin( $options=array() )
	{
		$query	= $this->_getCLMTermin( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	//neu: Einzelergebnisse (klkl)
	function _getCLMEinzel ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];

		$query = " SELECT l.rang,a.zps as zps, a.sg_zps as sgzps, a.man_nr as man_nr"
			." FROM #__clm_mannschaften as a "
			." LEFT JOIN #__clm_liga as l ON l.id =".$liga
			." WHERE a.liga = ".$liga
			//." AND a.sid = ".$sid
			;
		$db->setQuery($query);
		$man	=$db->loadObjectList();
		$rang	=$man[0]->rang;
		
	if ($rang > 0) {
		$query = " SELECT a.tln_nr,a.dg,a.lid,a.sid,a.runde,a.brett,a.spieler,a.punkte,a.kampflos,a.zps, "
			." m.snr as snr ,r.man_nr as rmnr, r.Rang as rrang "
			." FROM #__clm_rnd_spl as a "
			." LEFT JOIN #__clm_mannschaften as m1 ON m1.sid = a.sid AND m1.liga = a.lid AND m1.tln_nr = a.tln_nr"
			." LEFT JOIN #__clm_meldeliste_spieler as m ON m.sid = a.sid AND m.lid = a.lid AND m.mgl_nr = a.spieler AND m.zps = a.zps AND m.mnr = m1.man_nr"
			//." LEFT JOIN #__clm_rangliste_spieler as r on r.ZPS = a.zps AND r.Mgl_Nr = a.spieler AND r.sid = a.sid "
			." LEFT JOIN #__clm_rangliste_spieler as r on r.ZPS = a.zps AND r.Mgl_Nr = a.spieler AND r.sid = a.sid AND r.Gruppe = ".$rang
			//." LEFT JOIN #__clm_rangliste_id as i on i.ZPS = a.zps AND i.gid = r.Gruppe AND i.sid = a.sid "
			." WHERE m.lid = ".$liga
			//." AND m.sid =".$sid
			." AND a.lid =".$liga
			//." AND a.sid =".$sid
			." ORDER BY a.tln_nr ASC, rmnr ASC, rrang ASC, a.dg ASC, a.runde ASC "
			;
	} else {
		$query = " SELECT a.tln_nr,a.dg,a.lid,a.sid,a.runde,a.brett,a.spieler,a.punkte,a.kampflos,a.zps, "
			." m.snr as snr "
			." FROM #__clm_rnd_spl as a "
			." LEFT JOIN #__clm_mannschaften as m1 ON m1.sid = a.sid AND m1.liga = a.lid AND m1.tln_nr = a.tln_nr"
			." LEFT JOIN #__clm_meldeliste_spieler as m ON m.sid = a.sid AND m.lid = a.lid AND m.mgl_nr = a.spieler AND m.zps = a.zps AND m.mnr = m1.man_nr"
			." WHERE m.lid = ".$liga
			//." AND m.sid =".$sid
			." AND a.lid =".$liga
			//." AND a.sid =".$sid
			." ORDER BY a.tln_nr ASC, snr ASC,a.dg ASC,a.runde ASC "
			;
	}
		return $query;
	}

	function getCLMEinzel ( $options=array() )
	{
		$query	= $this->_getCLMEinzel( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
		
}
?>