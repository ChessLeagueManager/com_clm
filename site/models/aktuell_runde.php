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

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelAktuell_Runde extends JModel
{
	
	function Runden ()
	{
	$db	= JFactory::getDBO();
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	
	// Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$datum_sl = $config->get('runde_aktuell',1);

	// Aktuelle Runde aus SL OK (= 0) oder Datum (= 1) errechnen
	if($datum_sl == 0) {
	$query = " SELECT * FROM #__clm_runden_termine WHERE liga = $liga AND sl_ok = 1  ORDER BY nr DESC LIMIT 1 "
		;
		$db->setQuery( $query);
		$data	= $db->loadObjectList();
		$nr	= $data[0]->nr;
	// Es existiert noch keine SL Freigabe
	if(count($data) < 1){
		$runde	= 1;
		$dg	= 1;
	} else {
	// Es gibt mindestens eine SL Freigabe
	$query = " SELECT runden, durchgang FROM #__clm_liga WHERE id = $liga "
		;
		$db->setQuery( $query);
		$liga	= $db->loadObjectList();
		$rnd	= $liga[0]->runden;
		$dg	= $liga[0]->durchgang;
	
	// Wenn letzte Runde
	if ($nr == ($rnd*$dg) ) {
		// Wenn Nr größer als Rundenzahl dann DG = 2 
		if ($nr > $rnd){
			$runde	= $nr - $rnd;
			$dg	= 2;
		} else {
			$runde	= $nr;
			$dg	= 1;
			}
	}
	// wenn nicht letzte Runde
	else {
		$query = " SELECT * FROM #__clm_runden_termine WHERE liga = $liga AND nr = ".($nr+1)
			;	
		$db->setQuery( $query);
		$data	= $db->loadObjectList();
		$nr_next= $data[0]->nr;
		$datum	= $data[0]->datum;	
		// Wenn Datum gesetzt dann vergleichen
		if ($datum !="") {
			// positiv -> Zukunft; negativ -> Vergangenheit
			$date_db = strtotime($datum) - time();
			// nächster Termin liegt in der Zukunft
			if($date_db > 0) {
				// Wenn Nr größer als Rundenzahl dann DG = 2 
				if ($nr > $rnd){
					$runde	= $nr - $rnd;
					$dg	= 2;
	
				} else {
					$runde	= $nr;
					$dg	= 1;
				}
			// nächster Termin liegt nicht in der Zukunft
			} else {
				// Wenn Nr_next größer als Rundenzahl dann DG = 2 
				if ($nr_next > $rnd){
					$runde	= $nr_next - $rnd;
					$dg	= 2;
	
				} else {
					$runde	= $nr_next;
					$dg	= 1;
				}
			}
		}
		// Wenn nicht dann ist vorherige Runde die aktuelle
		else {
			// Wenn Nr größer als Rundenzahl dann DG = 2 
			if ($nr > $rnd){
				$runde	= $nr - $rnd;
				$dg	= 2;

			} else {
				$runde	= $nr;
				$dg	= 1;
			}
		}
	}}
	} else {

	$query	= " SELECT runden, durchgang FROM #__clm_liga WHERE id = $liga ";	
	$db->setQuery( $query);
	$lid	= $db->loadObjectList();
	$rnd	= $lid[0]->runden;
	
	$query	= " SELECT nr, datum FROM #__clm_runden_termine WHERE liga = $liga ORDER BY datum";
	$db->setQuery( $query);
	$data	= $db->loadObjectList();

	//$runde	= $rnd;
	$now	= strtotime ( 'now' );
	$nr	= 0;
		foreach($data as $aktuell) { if (strtotime ( $aktuell->datum.' 00:00:00') > $now) { break;} if ($aktuell->datum != '0000-00-00') {$nr ++;}}
			if ($nr > $rnd){
				$runde	= $nr - $rnd;
				$dg	= 2;
			} else {
				$runde	= $nr;
				$dg	= 1;
			}
	if($nr =="0"){ $runde = 1; $dg =1;}
	}

	$x[]=$runde;
	$x[]=$dg;	
	return $x;
	}

	
	function _getCLMLiga( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
		$query = " SELECT a.*,r.datum,r.bemerkungen as comment, r.published as pub,"
			."u.name as mf_name,u.email as email FROM #__clm_liga as a"      //klkl		
			." LEFT JOIN #__clm_runden_termine as r ON r.liga = a.id AND r.sid = a.sid "
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." LEFT JOIN #__clm_user as u ON u.jid = a.sl and u.sid = a.sid"  //klkl
			." WHERE a.id = ".$liga
				." AND a.sid = ".$sid
				." AND s.published = 1"
				." ORDER BY nr "
			;
		return $query;
	}
	function getCLMLiga( $options=array() )
	{
		$query	= $this->_getCLMLiga( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMMannschaft( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');
		// TODO: Cache on the fingerprint of the arguments
		$db			= JFactory::getDBO();
		$id			= @$options['id'];
 
		$query = " SELECT * FROM #__clm_mannschaften "
			." WHERE liga = ".$liga
			." AND sid = ".$sid
			." ORDER BY tln_nr "
			;

		return $query;
	}
	function getCLMMannschaft( $options=array() )
	{
		$query	= $this->_getCLMMannschaft( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMPaar ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');

	$rnd_dg = CLMModelAktuell_Runde::Runden();
	$runde	= $rnd_dg[0];
	$dg	= $rnd_dg[1];

		$db			= JFactory::getDBO();
		$id			= @$options['id'];

	$query = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln,g.published as gpublished, "
		." h.id as hid, h.name as hname, h.tln_nr as htln, h.published as hpublished "
		." FROM #__clm_rnd_man as a"
		." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner"
		." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr"
			." WHERE g.liga = ".$liga
			." AND g.sid = ".$sid
			." AND h.liga = ".$liga
			." AND h.sid = ".$sid
			." AND a.sid = ".$sid
			." AND a.lid = ".$liga
			." AND a.runde = ".$runde
			." AND a.dg = ".$dg
			." AND a.heim = 1 "
			." ORDER BY a.paar ASC"
			;

		return $query;
	}

	function getCLMPaar ( $options=array() )
	{
		$query	= $this->_getCLMPaar( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMDWZSchnitt ( &$options )
	{
	$liga = JRequest::getInt('liga','1');
	$sid = JRequest::getInt('saison','1');


		$db			= JFactory::getDBO();
		$id			= @$options['id'];

		$query = " SELECT stamm,ersatz FROM #__clm_liga "
			." WHERE id = ".$liga
			;
		$db->setQuery( $query);
		$row_tln=$db->loadObjectList();
		//$tln	= ($row_tln[0]->stamm)+($row_tln[0]->ersatz);
		$tln	= $row_tln[0]->stamm;
		
	$query = " SELECT e.tln_nr as tlnr,AVG(d.DWZ) as dwz"
		." FROM #__clm_meldeliste_spieler as a"
		." LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = a.mgl_nr AND d.ZPS = a.zps AND d.sid = a.sid AND d.DWZ !=0)"
		." LEFT JOIN #__clm_mannschaften AS e ON (e.sid=a.sid AND e.liga= a.lid AND (e.zps=a.zps OR e.sg_zps=a.zps) AND e.man_nr = a.mnr AND e.man_nr !=0 AND e.liste !=0) "
			." WHERE a.lid = ".$liga
			." AND a.sid = ".$sid
			." AND e.tln_nr IS NOT NULL "
			." AND a.snr < ".($tln+1)
			." GROUP BY e.tln_nr"
			;
		return $query;
	}

	function getCLMDWZSchnitt ( $options=array() )
	{
		$query	= $this->_getCLMDWZSchnitt( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMDWZgespielt ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');

	$rnd_dg = CLMModelAktuell_Runde::Runden();
	$runde	= $rnd_dg[0];
	$dg	= $rnd_dg[1];
	
	$db	= JFactory::getDBO();

	$query = " SELECT a.sid,a.lid,a.runde,a.paar,a.dg, AVG(d.DWZ) as dwz,AVG(g.DWZ) as gdwz "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_rnd_spl AS r ON (r.sid=a.sid AND r.lid= a.lid AND r.runde=a.runde AND r.paar = a.paar AND r.dg = a.dg) "
		." LEFT JOIN #__clm_dwz_spieler AS d ON (d.ZPS = r.zps AND d.Mgl_Nr = r.spieler AND d.sid = r.sid) "
		." LEFT JOIN #__clm_dwz_spieler AS g ON (g.ZPS = r.gzps AND g.Mgl_Nr = r.gegner AND g.sid = r.sid) "
			." WHERE a.lid = $liga  AND a.sid = $sid AND a.heim = 1 AND r.heim = 1 "
			." AND a.runde = $runde"
			." AND a.dg = $dg"
			." AND a.sid = ".$sid
			." GROUP BY a.paar ASC"
			;

		return $query;
	}
	function getCLMDWZgespielt ( $options=array() )
	{
		$query	= $this->_getCLMDWZgespielt( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMEinzel ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');

	$rnd_dg = CLMModelAktuell_Runde::Runden();
	$runde	= $rnd_dg[0];
	$dg	= $rnd_dg[1];
	
	$db		= JFactory::getDBO();
	$query	= " SET SQL_BIG_SELECTS=1";			
	$db->setQuery( $query);
	$db->query();

	$query = " SELECT a.zps, a.gzps, a.paar,a.brett,a.spieler,a.gegner,a.ergebnis,a.kampflos, a.dwz_edit, a.dwz_editor, m.name,"
		." n.name, d.Spielername as hname, d.DWZ as hdwz, "
		." p.erg_text as erg_text, e.Spielername as gname, e.DWZ as gdwz, q.erg_text as dwz_text, "
		." k.snr as hsnr, l.snr as gsnr"                                                                                     //klkl		
		." FROM #__clm_rnd_spl as a "
		." LEFT JOIN #__clm_rnd_man as r ON ( r.lid = a.lid AND r.runde = a.runde AND r.tln_nr = a.tln_nr AND  r.dg = a.dg) "
		." LEFT JOIN #__clm_mannschaften AS m ON ( m.tln_nr = r.tln_nr AND m.liga = a.lid) "
		." LEFT JOIN #__clm_mannschaften AS n ON ( n.tln_nr = r.gegner AND n.liga = a.lid) "
        	." LEFT JOIN #__clm_dwz_spieler AS d ON ( d.Mgl_Nr = a.spieler AND d.ZPS = a.zps AND d.sid = a.sid) "
        	." LEFT JOIN #__clm_dwz_spieler AS e ON ( e.Mgl_Nr = a.gegner AND e.ZPS = a.gzps AND e.sid = a.sid) "
		." LEFT JOIN #__clm_ergebnis AS p ON p.eid = a.ergebnis "
		." LEFT JOIN #__clm_ergebnis AS q ON q.eid = a.dwz_edit "
		." LEFT JOIN #__clm_meldeliste_spieler as k ON k.sid = a.sid AND k.lid = a.lid AND k.mgl_nr = a.spieler AND k.zps = a.zps AND k.mnr=m.man_nr "  //klkl2
		." LEFT JOIN #__clm_meldeliste_spieler as l ON l.sid = a.sid AND l.lid = a.lid AND l.mgl_nr = a.gegner AND l.zps = a.gzps AND l.mnr=n.man_nr "  //klkl2				
			." WHERE a.sid =  ".$sid
			." AND a.lid = ".$liga
			." AND a.runde = ".$runde
			." AND a.dg = ".$dg
			." AND a.heim = 1"
			." ORDER BY a.paar ASC, a.brett ASC"
			;
		return $query;
	}
	function getCLMEinzel ( $options=array() )
	{
		$query	= $this->_getCLMEinzel( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMSumme ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$liga = JRequest::getInt('liga','1');

	$rnd_dg = CLMModelAktuell_Runde::Runden();
	$runde	= $rnd_dg[0];
	$dg	= $rnd_dg[1];
	
		$db			= JFactory::getDBO();
		$id			= @$options['id'];

	$query = " SELECT u.name,a.paar as paarung,a.runde as runde,a.brettpunkte as sum, dwz_editor "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_user as u ON (u.jid = a.gemeldet AND a.heim = 1 AND u.sid = $sid)"
			." WHERE a.lid = ".$liga
			." AND a.sid = ".$sid
			." AND a.runde = ".$runde
			." AND a.dg = ".$dg
			." ORDER BY a.paar ASC, a.heim DESC"
			;
			return $query;
	}
	function getCLMSumme ( $options=array() )
	{
		$query	= $this->_getCLMSumme( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMOK ( &$options )
	{
		$db			= JFactory::getDBO();
		$id			= @$options['id'];

	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');
	
	$rnd_dg = CLMModelAktuell_Runde::Runden();
	$runde	= $rnd_dg[0];
	$dg	= $rnd_dg[1];
	// Anz.Runden und Durchgänge aus #__clm_liga holen
	$query = " SELECT a.runden, a.durchgang "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
	
	if ($dg > 1) $runde = $runde + $liga[0]->runden;
		
	$query = " SELECT a.sl_ok as sl_ok" 
		." FROM #__clm_runden_termine as a"
			." WHERE a.liga = ".$lid
			." AND a.sid = ".$sid
			." AND a.nr = ".$runde
			." AND a.published = 1"
			;
		return $query;
	}
	function getCLMOK ( $options=array() )
	{
		$query	= $this->_getCLMOK( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
///////////////
////////////////
/////////////
	function _getCLMSpielfrei( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$liga	= JRequest::getInt('liga','1');
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
 
		$query = "SELECT COUNT(tln_nr) AS count FROM #__clm_mannschaften "
			." WHERE liga = ".$liga
			." AND sid = ".$sid
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
	$db	= JFactory::getDBO();
	
	$rnd_dg = CLMModelAktuell_Runde::Runden();
	$runde	= $rnd_dg[0];
	$dg	= $rnd_dg[1];
	
	$id	= @$options['id'];
	// ordering für Rangliste -> Ersatz für direkten Vergleich
		$query = "SELECT a.order, a.runden, a.durchgang, a.b_wertung FROM #__clm_liga as a"  //klkl
			." WHERE id = ".$liga
			." AND sid = ".$sid
			;
		$db->setQuery($query);
		$order = $db->loadObjectList();
 			if ($order[0]->order == 1) { $ordering = " , m.ordering ASC";}
			else { $ordering =', a.tln_nr ASC ';} 
		$query = " SELECT a.tln_nr as tln_nr,m.name as name, SUM(a.manpunkte) as mp, "
			." SUM(a.brettpunkte) as bp, SUM(a.wertpunkte) as wp, m.published, m.man_nr, COUNT(DISTINCT a.runde) as spiele"  //klkl
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_mannschaften as m ON m.liga = $liga AND m.tln_nr = a.tln_nr "
			." WHERE a.lid = ".$liga
			." AND a.sid = ".$sid
			." AND m.man_nr <> 0 "
			." AND gemeldet > 0 ";
			if (($runde != "")&&($dg == 1)) { $query = $query." AND runde < ".($runde +1)." AND dg = 1";}
			if (($runde != "")&&($dg > 1)) { $query = $query." AND ( runde < ".($runde +1)." OR dg = 1)";}
			
		$query = $query	
			." GROUP BY a.tln_nr ";
		if ($order[0]->b_wertung == 0) {   
			$query = $query
			." ORDER BY mp DESC, bp DESC".$ordering; }
		if ($order[0]->b_wertung == 3) { 
			$query = $query
			." ORDER BY mp DESC, bp DESC, wp DESC".$ordering; }
		if ($order[0]->b_wertung == 4) { 
			$query = $query
			." ORDER BY mp DESC, bp DESC, ".$ordering.", wp DESC"; }		
		return $query;
	}
	function getCLMPunkte( $options=array() )
	{
		$query	= $this->_getCLMPunkte( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMDWZSchnitt_rang ( &$options )
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
		$tln	= ($row_tln[0]->stamm)+($row_tln[0]->ersatz);

	$query = " SELECT e.tln_nr as tlnr,AVG(d.DWZ) as dwz"
			." FROM #__clm_meldeliste_spieler as a"
			." LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = a.mgl_nr AND d.ZPS = a.zps AND d.sid = a.sid AND d.DWZ !=0)"
			." LEFT JOIN #__clm_mannschaften AS e ON (e.sid=a.sid AND e.liga= a.lid AND (e.zps=a.zps OR e.sg_zps=a.zps) AND e.man_nr = a.mnr AND e.man_nr !=0 AND e.liste !=0) "
			." WHERE a.lid = ".$liga
			." AND a.sid = ".$sid
			//." AND e.man_nr <> 0 "
			//." AND e.liste <> 0 "
			//." AND d.DWZ > 0 "
			//." AND d.DWZ <> ''"
			// Verursachen massive Perfomance Probleme : Abfrage dauert 15-20 Sekunden !!!
			// stattdessen AND d.DWZ !=0 im JOIN
			." GROUP BY e.tln_nr"
			." LIMIT 0, ".$tln
			;
		return $query;
	}
	function getCLMDWZSchnitt_rang ( $options=array() )
	{
		$query	= $this->_getCLMDWZSchnitt_rang( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	function punkte_tlnr ( $sid, $lid, $tlnr, $dg )
	{
	//defined('_JEXEC') or die('Restricted access'); 
	$db	= JFactory::getDBO();
	$query = " SELECT a.runde,a.tln_nr,a.gegner,a.runde, a.brettpunkte"
		." FROM #__clm_rnd_man as a "
		." WHERE a.lid = ".$lid
		." AND a.sid = ".$sid
		." AND a.tln_nr = ".$tlnr
		." AND a.dg = $dg "
		." ORDER BY a.gegner "
		;
	$db 	=& JFactory::getDBO();
	$db->setQuery( $query );
	$runden	=$db->loadObjectList();
	
	return $runden;
	}

	function punkte_text ($lid)
	{
	defined('_JEXEC') or die('Restricted access'); 
	// Ergebnisliste laden
	$sql = "SELECT a.id, a.erg_text "
		." FROM #__clm_ergebnis as a "
		;
	$db 		=& JFactory::getDBO();
	$db->setQuery( $sql );
	$ergebnis	= $db->loadObjectList();

	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$sieg 		= $liga[0]->sieg;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;

	// Ergebnistexte nach Modus setzen
	$ergebnis[0]->erg_text = ($nieder+$antritt)." - ".($sieg+$antritt);
	$ergebnis[1]->erg_text = ($sieg+$antritt)." - ".($nieder+$antritt);
	$ergebnis[2]->erg_text = ($remis+$antritt)." - ".($remis+$antritt);
	$ergebnis[3]->erg_text = ($nieder+$antritt)." - ".($nieder+$antritt);
	if ($antritt > 0) {
		$ergebnis[4]->erg_text = "0 - ".round($antritt+$sieg)." (kl)";
		$ergebnis[5]->erg_text = round($antritt+$sieg)." - 0 (kl)";
		$ergebnis[6]->erg_text = "0 - 0 (kampflos)";
		}
		
	return $ergebnis;
	}
	
	// Paarungen Folgerunde   klkl
	function _getCLMPaar1 ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$lid = JRequest::getInt('liga','1');
	$dg = JRequest::getInt('dg');
	$runde = JRequest::getInt('runde');
	
	$db			= JFactory::getDBO();
	$id			= @$options['id'];

	// Anz.Runden und Durchgänge aus #__clm_liga holen
	$query = " SELECT a.runden, a.durchgang "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
	 
	if (($liga[0]->durchgang > "1")&&($dg == 1)&&($liga[0]->runden == $runde)) {
		$dg++;
		$runde = 1;
	} else $runde++;

	$db			= JFactory::getDBO();
	$id			= @$options['id'];

	$query = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln,g.published as gpublished, "
		." h.id as hid, h.name as hname, h.tln_nr as htln, h.published as hpublished "
		." FROM #__clm_rnd_man as a"
		." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner"
		." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr"
			." WHERE g.liga = ".$lid
			." AND g.sid = ".$sid
			." AND h.liga = ".$lid
			." AND h.sid = ".$sid
			." AND a.sid = ".$sid
			." AND a.lid = ".$lid
			." AND a.runde = ".$runde
			." AND a.dg = ".$dg
			." AND a.heim = 1 "
			." ORDER BY a.paar ASC"
			;

		return $query;
	}

	function getCLMPaar1 ( $options=array() )
	{
		$query	= $this->_getCLMPaar1( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	function _getCLMlog( &$options )   //klkl
	{
	$sid	= JRequest::getInt('saison','1');
	$lid	= JRequest::getInt('liga','1');
	$dg 	= JRequest::getInt('dg');
	$runde 	= JRequest::getInt('runde');
	$db		= JFactory::getDBO();
	$id		= @$options['id'];
	
	// Anz.Runden und Durchgänge aus #__clm_liga holen
	$query = " SELECT a.runden, a.durchgang "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
	 
	if ($dg > 1) $runde = $runde + $liga[0]->runden;
	//letztes Freigabe-Update finden 
	$query = " SELECT a.datum, a.nr_aktion "
		." FROM #__clm_log as a "
		." WHERE a.lid = ".$lid
		." AND a.sid = ".$sid
		." AND a.rnd = ".$runde
		//." AND a.dg = ".$dg
		." AND (a.nr_aktion = 201 OR a.nr_aktion = 202)" 	// 201 Runde freigegeben; 202 Freigabe zurückgenommen
		." ORDER BY a.datum DESC LIMIT 1 ";
		return $query;
	}
	
	function getCLMlog( $options=array() )
	{
		$query	= $this->_getCLMlog( $options );
		$result = $this->_getList( $query );
		return @$result;
	}	
}
?>
