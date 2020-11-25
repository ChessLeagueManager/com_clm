<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Eingang: Liga-Index
function clm_api_db_xml_data($lid,$dg,$runde,$paar,$view) {
	$liga = clm_core::$load->make_valid($lid, 0, -1);
	$out["input"]["lid"] = $lid;
	$out["input"]["dg"] = $dg;
	$out["input"]["runde"] = $runde;
	$out["input"]["paar"] = $paar;
	$out["input"]["view"] = $view;
	
	if ($view == 4) {
		$teilnehmer = $runde;
		$runde = 0;
	}
	else $teilnehmer = 0;
	
	if ($view == 14) {
		$club = $runde; $runde = 0;
		$season = $liga; $liga = 0;
	} else {
		$club = 0;
		$season = 0;
	}

	$config		= clm_core::$db->config();
	$countryversion = $config->countryversion;
	$conf_view_archive	= $config->view_archive;
	
	$aconfig = array();
	$aconfig['template']			= $config->template;
	$aconfig['clm_lesehilfe']		= $config->lesehilfe;
	$aconfig['clm_zeile1']			= "#".$config->zeile1;
	$aconfig['clm_zeile2']			= "#".$config->zeile2;
	$aconfig['clm_re_col']			= "#".$config->re_col;
	$aconfig['clm_tableth']			= "#".$config->tableth;
	$aconfig['clm_subth']			= "#".$config->subth;
	$aconfig['clm_tableth_s1']		= "#".$config->tableth_s1;
	$aconfig['clm_tableth_s2']		= "#".$config->tableth_s2;
	$aconfig['clm_cellin_top']		= $config->cellin_top;
	$aconfig['clm_cellin_left']		= $config->cellin_left;
	$aconfig['clm_cellin_right']	= $config->cellin_right;
	$aconfig['clm_cellin_bottom']	= $config->cellin_bottom;
	$aconfig['clm_border']			= $config->border_length." ".$config->border_style." #".$config->border_color;
	$aconfig['clm_wrong1'] 			= "#".$config->wrong1;
	$aconfig['clm_wrong2']			= $config->wrong2_length." ".$config->wrong2_style." #".$config->wrong2_color;
	$aconfig['clm_rang_auf']		= "#".$config->rang_auf;
	$aconfig['clm_rang_auf_evtl']	= "#".$config->rang_auf_evtl;
	$aconfig['clm_rang_ab']			= "#".$config->rang_ab;
	$aconfig['clm_rang_ab_evtl']	= "#".$config->rang_ab_evtl;
	$aconfig['clm_msch_nr']			= $config->msch_nr;
	$aconfig['clm_msch_dwz']		= $config->msch_dwz;
	$aconfig['clm_msch_rnd']		= $config->msch_rnd;
	$aconfig['clm_msch_punkte']		= $config->msch_punkte;
	$aconfig['clm_msch_spiele']		= $config->msch_spiele;
	$aconfig['clm_msch_prozent']	= $config->msch_prozent;
	$aconfig['fe_pgn_show']			= $config->fe_pgn_show;
	
	$out["aconfig"] = $aconfig;
 
  	$ligaModel = " SELECT a.*, u.name as sl, u.email, s.id as sid, s.name as sname, s.published as spublished, s.archiv as sarchiv"
		." FROM #__clm_liga as a"
		." LEFT JOIN #__clm_user as u ON a.sl = u.jid AND u.sid = a.sid"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid ";
	if ($view != 14) {	
		$ligaModel .= " WHERE a.id = ".$lid
					." AND s.published = 1";
	} else {
		$ligaModel .= " WHERE a.sid = ".$season
					." AND a.published = 1"
					." AND s.published = 1";
	}		

	$out["ligaModel"] = $ligaModel;
	$out["liga"] = clm_core::$db->loadObjectList($ligaModel);
	
	if ($view == 14) {
		$ligen_array = array();
		foreach ($out["liga"] as $liga) {
			$ligen_array[] = (string) $liga->id;
		}
		$out["ligen_array"] = $ligen_array;
		$ligen_list = implode (',', $ligen_array);
		$out["ligen_list"] = $ligen_list;
	}
	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["liga"][0])) {
		return array(false, "e_noLeagueDataError");
	}	

	if (count($out["liga"]) == 0) {
		return array(false, "PLG_CLM_SHOW_ERR_NO_TOURNAMENT");
	}
	if ($out["liga"][0]->published == 0 OR $out["liga"][0]->spublished == 0) {
		return array(false, "PLG_CLM_SHOW_ERR_NOT_PUBLISHED");
	}
	$archive_check = clm_core::$api->db_check_season_user($out["liga"][0]->sid);
	if (!$archive_check) {
		return array(false, "PLG_CLM_SHOW_ERR_NO_ARCHIVE");
	}
	if ($dg > $out["liga"][0]->durchgang) {
		return array(false, "PLG_CLM_SHOW_ERR_MAX_DG");
	}
	if ($runde > $out["liga"][0]->runden) {
		return array(false, "PLG_CLM_SHOW_ERR_MAX_RUNDE");
	}
	if ($paar > ($out["liga"][0]->teil * 0.5)) {
		return array(false, "PLG_CLM_SHOW_ERR_MAX_PAAR");
	}

	if ($view == 14) {
		$clubModel = " SELECT * FROM #__clm_vereine"
			." WHERE sid = ".$season." AND zps = '".$club."'";
		$out["clubModel"] = $clubModel;
	
		$out["club"] = clm_core::$db->loadObjectList($clubModel);
		// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
		if (!isset($out["club"][0])) {
			return array(false, "e_noClubDataError");
		}	
	}

	// Ranking starten
	if ($view == 0 OR $view ==1) {
		if ($runde != 0)
			clm_core::$api->db_tournament_ranking_round($lid,true,$runde,$dg); 
		else
			clm_core::$api->db_tournament_ranking($lid,true); 
	}
	
	// Termin für Paarung auslesen
	if ($view == 3) {
		$tnr = ($dg - 1) * $out["liga"][0]->runden + $runde;
		$terminModel = "SELECT * FROM #__clm_runden_termine "
			." WHERE liga = ".$lid
			." AND nr = ".$tnr
			." ORDER BY nr "
			;
		$out["termin"] = clm_core::$db->loadObjectList($terminModel);
	}
		
	if ($view != 14) {
		$dgrunde = ($dg * 100) + $runde + 1;
		$mannschaftModel = " SELECT a.tln_nr, m.name as name, m.published, "; 
		if ($runde == 0)
			$mannschaftModel .= " m.summanpunkte as summanpunkte, m.sumbrettpunkte as sumbrettpunkte, m.rankingpos as rankingpos, "
						." m.sumtiebr1 as sumtiebr1, m.sumtiebr2 as sumtiebr2, m.sumtiebr3 as sumtiebr3, "; 		
		else
			$mannschaftModel .= " m.z_summanpunkte as summanpunkte, m.z_sumbrettpunkte as sumbrettpunkte, m.z_rankingpos as rankingpos, " 		
							." m.z_sumtiebr1 as sumtiebr1, m.z_sumtiebr2 as sumtiebr2, m.z_sumtiebr3 as sumtiebr3, "; 		
		$mannschaftModel .= " (SUM(a.manpunkte) - m.abzug) as mp, m.abzug as abzug, "
			." (SUM(a.brettpunkte) - m.bpabzug) as bp, m.bpabzug, SUM(a.wertpunkte) as wp, m.published, m.man_nr, COUNT(DISTINCT a.runde, a.dg) as spiele, "
			." SUM(case when a.manpunkte IS NULL then 0 else 1 end) as count_G, "
			." SUM(case when a.manpunkte = ".($out["liga"][0]->man_sieg+$out["liga"][0]->man_antritt)." then 1 else 0 end) as count_S, "
			." SUM(case when a.manpunkte = ".($out["liga"][0]->man_remis+$out["liga"][0]->man_antritt)." then 1 else 0 end) as count_R, "
			." SUM(case when a.manpunkte = ".($out["liga"][0]->man_nieder+$out["liga"][0]->man_antritt)." then 1 else 0 end) as count_V "
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_mannschaften as m ON m.liga = $liga AND m.tln_nr = a.tln_nr "
			." WHERE a.lid = ".$liga
			." AND m.man_nr <> 0 ";
		if ($runde > 0 AND $paar == 0)		// View Kreuztabelle/Tabelle
			$mannschaftModel .= " AND ((a.dg * 100) + a.runde) < ".$dgrunde;
		$mannschaftModel .= " GROUP BY a.tln_nr ";
		if ($runde == 0)
			$mannschaftModel .= " ORDER BY m.rankingpos ASC, a.tln_nr ASC"; 		
		else
			$mannschaftModel .= " ORDER BY m.z_rankingpos ASC, a.tln_nr ASC"; 		
		$out["mannschaft"] = clm_core::$db->loadObjectList($mannschaftModel);
	}

	$dgrunde = ($dg * 100) + $runde + 1;				 
  	$paarModel = " SELECT a.*, b.brettpunkte as gbrettpunkte, "
		." g.id as gid, g.name as gname, g.tln_nr as gtln, g.published as gpublished, ";
	if ($runde == 0)
		$paarModel .= " g.rankingpos as grank, h.rankingpos as hrank, "; 		
	else
		$paarModel .= " g.z_rankingpos as grank, h.z_rankingpos as hrank, "; 		
	$paarModel .= " g.man_nr as gmnr, h.id as hid, h.name as hname, h.tln_nr as htln, b.wertpunkte as gwertpunkte, "
		." h.published as hpublished, h.man_nr as hmnr, t.name as rname, t.datum as rdatum, t.startzeit ";
	if ($view == 4 OR $view == 14) {
		$paarModel .= ", ( CASE WHEN a.pdate > '1970-01-01' THEN a.pdate ";
		$paarModel .= " 		WHEN t.datum > '1970-01-01' THEN t.datum ";
		$paarModel .= "         ELSE 0 ";
		$paarModel .= "    END ) as gdate ";
		$paarModel .= ", ( CASE WHEN a.pdate > '1970-01-01' THEN a.ptime ";
		$paarModel .= " 		WHEN t.datum > '1970-01-01' THEN t.startzeit ";
		$paarModel .= "         ELSE 0 ";
		$paarModel .= "    END ) as gtime ";
	}
	$paarModel .= " FROM #__clm_rnd_man as a"
		." LEFT JOIN #__clm_liga AS l ON l.id = a.lid "
		." LEFT JOIN #__clm_mannschaften AS g ON g.liga = a.lid AND g.tln_nr = a.gegner"
		." LEFT JOIN #__clm_mannschaften AS h ON h.liga = a.lid AND h.tln_nr = a.tln_nr"
		." LEFT JOIN #__clm_rnd_man AS b ON b.lid = a.lid AND b.runde = a.runde AND b.dg = a.dg AND b.paar = a.paar AND b.heim = 0 "
		." LEFT JOIN #__clm_runden_termine AS t ON t.liga = a.lid AND t.nr = (a.runde + ((a.dg - 1) * l.runden))  ";
	if ($view == 14) {
		$paarModel .= " WHERE (FIND_IN_SET(a.lid,'".$ligen_list."') != 0) "
					." AND a.heim = 1 AND (g.zps = '".$club."' OR h.zps = '".$club."')";
	} else {
		$paarModel .= " WHERE g.liga = ".$lid
					." AND h.liga = ".$lid
					." AND a.lid = ".$lid
					." AND (g.man_nr > 0 OR h.man_nr > 0) ";
	}
	if (($view == 0 OR $view == 1) AND $runde > 0)		// View Kreuztabelle
		$paarModel .= " AND ((a.dg * 100) + a.runde) < ".$dgrunde;
	elseif ($view == 2) 								// View Paarungsliste
		$paarModel .= " AND a.heim = 1";									
	elseif ($view == 3) 								// View Paarung
		$paarModel .= " AND a.runde = ".$runde." AND a.dg = ".$dg." AND a.paar = ".$paar." AND a.heim = 1";
	elseif ($view == 4) 								// View Spielplan
		$paarModel .= " AND ((a.heim = 1 AND a.tln_nr = ".$teilnehmer.") OR (a.heim = 1 AND a.gegner = ".$teilnehmer."))";									// Sortierung für Paarungsliste
	if ($view == 0 OR $view == 1 OR $view == 3) 				// Sortierung für Kreuztabelle/einf.Tabelle vollrundig
		$paarModel .= " ORDER BY hrank ASC, a.dg ASC, grank ASC";			
	elseif ($view == 2) 										// Sortierung für Paarungsliste
		$paarModel .= " ORDER BY a.dg ASC, a.runde ASC, a.paar ASC";		
	elseif ($view == 4 OR $view == 14)											// Sortierung für Spielplan
		$paarModel .= " ORDER BY gdate ASC, gtime ASC, a.lid ASC, a.dg ASC, a.runde ASC, a.paar ASC";		
	
	$out["paarModel"] = $paarModel; 
	$out["paar"] = clm_core::$db->loadObjectList($paarModel);
	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["paar"][0])) {
		return array(false, "e_noPairingDataError");
	}	
	
	if ($view != 14)	{
		$DWZSchnittModel = " SELECT e.tln_nr as tlnr,AVG(d.DWZ) as dwz,AVG(a.start_dwz) as start_dwz"
			." FROM #__clm_meldeliste_spieler as a";
		if ($countryversion =="de") 
			$DWZSchnittModel .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = a.mgl_nr AND d.ZPS = a.zps AND d.sid = a.sid AND d.DWZ !=0)";
		else
			$DWZSchnittModel .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.PKZ = a.PKZ AND d.ZPS = a.zps AND d.sid = a.sid AND d.DWZ !=0)";
		$DWZSchnittModel .= " LEFT JOIN #__clm_mannschaften AS e ON (e.sid=a.sid AND e.liga= a.lid AND (e.zps=a.zps OR e.sg_zps=a.zps) AND e.man_nr = a.mnr AND e.man_nr !=0 AND e.liste !=0) "
			." WHERE a.lid = ".$lid
			." AND e.tln_nr IS NOT NULL "
			." AND a.snr < ".($out["liga"][0]->stamm+1)
			." GROUP BY e.tln_nr"
			;
		$out["DWZSchnitt"] = clm_core::$db->loadObjectList($DWZSchnittModel);

		// DWZ Durchschnitte - Aufstellung 
		$result = clm_core::$api->db_nwz_average($lid);
		$a_average_dwz_lineup = $result[2];
		$out["DWZSchnitt2"] = $a_average_dwz_lineup;

		$DWZgespieltModel = " SELECT a.sid,a.lid,a.runde,a.paar,a.dg, AVG(d.DWZ) as dwz,AVG(g.DWZ) as gdwz, AVG(dm.start_dwz) as start_dwz,AVG(gm.start_dwz) as gstart_dwz "
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_rnd_spl AS r ON (r.sid=a.sid AND r.lid= a.lid AND r.runde=a.runde AND r.paar = a.paar AND r.dg = a.dg) ";
		if ($countryversion == "de") 
			$DWZgespieltModel .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.ZPS = r.zps AND d.Mgl_Nr = r.spieler AND d.sid = r.sid) "
			." LEFT JOIN #__clm_dwz_spieler AS g ON (g.ZPS = r.gzps AND g.Mgl_Nr = r.gegner AND g.sid = r.sid) "
			." LEFT JOIN #__clm_meldeliste_spieler AS dm ON ( dm.lid = a.lid AND dm.zps = r.zps AND dm.mgl_nr = r.spieler )"
			." LEFT JOIN #__clm_meldeliste_spieler AS gm ON ( gm.lid = a.lid AND gm.zps = r.gzps AND gm.mgl_nr = r.gegner )";
		else 
			$DWZgespieltModel .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.ZPS = r.zps AND d.PKZ = r.PKZ AND d.sid = r.sid) "
			." LEFT JOIN #__clm_dwz_spieler AS g ON (g.ZPS = r.gzps AND g.PKZ = r.gPKZ AND g.sid = r.sid) "
			." LEFT JOIN #__clm_meldeliste_spieler AS dm ON ( dm.lid = a.lid AND dm.zps = r.zps AND dm.PKZ = r.PKZ )"
			." LEFT JOIN #__clm_meldeliste_spieler AS gm ON ( gm.lid = a.lid AND gm.zps = r.gzps AND gm.PKZ = r.gPKZ )";
		$DWZgespieltModel .= " WHERE a.lid = $lid AND a.heim = 1 AND r.heim = 1 ";
		if ($view == 3)
			$DWZgespieltModel .= " AND a.dg = ".$dg." AND a.runde = ".$runde." AND a.paar = ".$paar;
		$DWZgespieltModel .= " GROUP BY a.dg ASC, a.runde ASC, a.paar ASC"
			;
		$out["DWZgespielt"] = clm_core::$db->loadObjectList($DWZgespieltModel);

		// DWZ Durchschnitte - gespielt in Runde 
		$aa_average_dwz_round = array();
		for ($xd=1; $xd<= ($out["liga"][0]->durchgang); $xd++){
			if ($view == 3 AND $xd != $dg) continue;
			for ($xr=1; $xr<= ($out["liga"][0]->runden); $xr++){
				if ($view == 3 AND $xr != $runde) continue;
				$result = clm_core::$api->db_nwz_average($lid,$xr,$xd);
				$aa_average_dwz_round[$xd][$xr] = $result[2];
			}
		}
		$out["DWZgespielt2"] = $aa_average_dwz_round;
	
		$summeModel = " SELECT a.dg,a.paar as paarung,a.runde as runde,a.brettpunkte as sum "
			." FROM #__clm_rnd_man as a "
			." WHERE a.lid = ".$lid
			." ORDER BY a.dg ASC ,a.runde ASC, a.paar ASC, a.heim DESC "
			;
		$out["summe"] = clm_core::$db->loadObjectList($summeModel);
	}
	if ($view == 3) {
		if ($out["liga"][0]->rang == 0) {
			$EinzelModel = "  SELECT a.zps, a.gzps, a.paar,a.brett,a.spieler,a.PKZ,a.gegner,a.gPKZ,a.ergebnis,a.kampflos, a.dwz_edit, a.dwz_editor, a.weiss,"
				." a.pgnnr, pg.text,"
				." m.name, n.name as mgname, m.sname, n.sname as smgname, d.Spielername as hname, d.DWZ as hdwz, d.FIDE_Elo as helo,"
				." p.erg_text as erg_text, e.Spielername as gname, e.DWZ as gdwz, e.FIDE_Elo as gelo, q.erg_text as dwz_text,"
				." k.snr as hsnr, l.snr as gsnr, k.start_dwz as hstart_dwz, l.start_dwz as gstart_dwz, k.attr as hattr, l.attr as gattr"
				." FROM #__clm_rnd_spl as a "
				." LEFT JOIN #__clm_rnd_man as r ON ( r.lid = a.lid AND r.runde = a.runde AND r.tln_nr = a.tln_nr AND  r.dg = a.dg) "
				." LEFT JOIN #__clm_mannschaften AS m ON ( m.tln_nr = r.tln_nr AND m.liga = a.lid) "
				." LEFT JOIN #__clm_mannschaften AS n ON ( n.tln_nr = r.gegner AND n.liga = a.lid) "
				." LEFT JOIN #__clm_pgn AS pg ON ( pg.id = a.pgnnr) ";
			if ($countryversion =="de") {
				$EinzelModel .= " LEFT JOIN #__clm_dwz_spieler AS d ON ( d.Mgl_Nr = a.spieler AND d.ZPS = a.zps AND d.sid = a.sid) "
					." LEFT JOIN #__clm_dwz_spieler AS e ON ( e.Mgl_Nr = a.gegner AND e.ZPS = a.gzps AND e.sid = a.sid) "
					." LEFT JOIN #__clm_meldeliste_spieler as k ON k.sid = a.sid AND k.lid = a.lid AND k.mgl_nr = a.spieler AND k.zps = a.zps AND k.mnr=m.man_nr "  
					." LEFT JOIN #__clm_meldeliste_spieler as l ON l.sid = a.sid AND l.lid = a.lid AND l.mgl_nr = a.gegner AND l.zps = a.gzps AND l.mnr=n.man_nr ";  
			} else {
				$EinzelModel .= " LEFT JOIN #__clm_dwz_spieler AS d ON ( d.PKZ = a.PKZ AND d.ZPS = a.zps AND d.sid = a.sid) "
					." LEFT JOIN #__clm_dwz_spieler AS e ON ( e.PKZ = a.gPKZ AND e.ZPS = a.gzps AND e.sid = a.sid) "
					." LEFT JOIN #__clm_meldeliste_spieler as k ON k.sid = a.sid AND k.lid = a.lid AND k.PKZ = a.PKZ AND k.zps = a.zps AND k.mnr=m.man_nr "  
					." LEFT JOIN #__clm_meldeliste_spieler as l ON l.sid = a.sid AND l.lid = a.lid AND l.PKZ = a.gPKZ AND l.zps = a.gzps AND l.mnr=n.man_nr ";  
			}
			$EinzelModel .= " LEFT JOIN #__clm_ergebnis AS p ON p.eid = a.ergebnis "
				." LEFT JOIN #__clm_ergebnis AS q ON q.eid = a.dwz_edit "
				." WHERE a.lid =  ".$lid
				." AND a.dg = ".$dg
				." AND a.runde = ".$runde
				." AND a.paar = ".$paar			// für einzelne Paarung
				." AND a.heim = 1"
				." AND m.man_nr > 0 AND n.man_nr > 0 "
				." ORDER BY a.paar ASC, a.brett ASC"
				;
		} else 
			$EinzelModel = "  SELECT a.zps, a.gzps, a.paar,a.brett,a.spieler,a.PKZ,a.gegner,a.gPKZ,a.ergebnis,a.kampflos, a.dwz_edit, a.dwz_editor, a.weiss,"
				." a.pgnnr, pg.text,"
				." m.name, n.name as mgname, m.sname, n.sname as smgname, d.Spielername as hname, d.DWZ as hdwz, d.FIDE_Elo as helo,"
				." p.erg_text as erg_text, e.Spielername as gname, e.DWZ as gdwz, e.FIDE_Elo as gelo, q.erg_text as dwz_text,"
				." k.snr as hsnr, l.snr as gsnr, k.start_dwz as hstart_dwz, l.start_dwz as gstart_dwz,"
				." t.man_nr as tmnr, t.Rang as trang, s.man_nr as smnr, s.Rang as srang"
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
				." LEFT JOIN #__clm_rangliste_spieler as t on t.ZPS = a.zps AND t.Mgl_Nr = a.spieler AND t.sid = a.sid AND t.Gruppe = ".$rang
				." LEFT JOIN #__clm_rangliste_spieler as s on s.ZPS = a.gzps AND s.Mgl_Nr = a.gegner AND s.sid = a.sid AND s.Gruppe = ".$rang
				." LEFT JOIN #__clm_pgn AS pg ON ( pg.id = a.pgnnr) "
				." WHERE a.lid =  ".$lid
				." AND a.dg = ".$dg
				." AND a.runde = ".$runde
				." AND a.paar = ".$paar			// für einzelne Paarung
				." AND a.heim = 1"
				." ORDER BY a.paar ASC, a.brett ASC"
				;
		$out["einzel"] = clm_core::$db->loadObjectList($EinzelModel);
	}
 
	return array(true, "m_xmlDataSuccess", $out);
}
?>
