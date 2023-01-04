<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Eingang: Liga-Index
function clm_api_db_paarungsliste($liga) {
	$liga = clm_core::$load->make_valid($liga, 0, -1);
	$out["input"]["liga"] = $liga;

	$config		= clm_core::$db->config();
	$countryversion = $config->countryversion;

 
  	$ligaModel = " SELECT a.*, u.name as sl, u.email, s.name as sname FROM #__clm_liga as a"
		." LEFT JOIN #__clm_user as u ON a.sl = u.jid AND u.sid = a.sid"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE a.id = ".$liga
		." AND s.published = 1"
		;
	$out["liga"] = clm_core::$db->loadObjectList($ligaModel);

	
  	$terminModel = "SELECT * FROM #__clm_runden_termine "
		." WHERE liga = ".$liga
		." ORDER BY nr "
		;
	$out["termin"] = clm_core::$db->loadObjectList($terminModel);

	
  	$mannschaftModel = " SELECT * FROM #__clm_mannschaften "
		." WHERE liga = ".$liga
		." AND published = 1 "
		." ORDER BY tln_nr "
		;
	$out["mannschaft"] = clm_core::$db->loadObjectList($mannschaftModel);

	
  	$paarModel = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln, g.published as gpublished, g.rankingpos as grank, "
		." g.man_nr as gmnr, h.id as hid, h.name as hname, h.tln_nr as htln, h.rankingpos as hrank, b.wertpunkte as gwertpunkte, "
		." h.published as hpublished, h.man_nr as hmnr, t.name as rname "
		." FROM #__clm_rnd_man as a"
		." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner"
		." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr"
		." LEFT JOIN #__clm_rnd_man AS b ON b.lid = ".$liga." AND b.runde = a.runde AND b.dg = a.dg AND b.paar = a.paar AND b.heim = 0 "
		." LEFT JOIN #__clm_runden_termine AS t ON t.liga = ".$liga." AND t.nr = (a.runde + ((a.dg - 1) * ".$out["liga"][0]->runden."))  "
		." WHERE g.liga = ".$liga
		." AND h.liga = ".$liga
		." AND a.lid = ".$liga
		." AND a.heim = 1 "
		." AND (g.man_nr > 0 OR h.man_nr > 0) "
		." ORDER BY a.dg ASC,a.runde ASC, a.paar ASC"
		;
	$out["paar"] = clm_core::$db->loadObjectList($paarModel);

	
	$DWZSchnittModel = " SELECT e.tln_nr as tlnr,AVG(d.DWZ) as dwz,AVG(a.start_dwz) as start_dwz"
		." FROM #__clm_meldeliste_spieler as a";
	if ($countryversion =="de") 
		$DWZSchnittModel .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = a.mgl_nr AND d.ZPS = a.zps AND d.sid = a.sid AND d.DWZ !=0)";
	else
		$DWZSchnittModel .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.PKZ = a.PKZ AND d.ZPS = a.zps AND d.sid = a.sid AND d.DWZ !=0)";
	$DWZSchnittModel .= " LEFT JOIN #__clm_mannschaften AS e ON (e.sid=a.sid AND e.liga= a.lid AND (e.zps=a.zps OR e.sg_zps=a.zps) AND e.man_nr = a.mnr AND e.man_nr !=0 AND e.liste !=0) "
		." WHERE a.lid = ".$liga
		." AND e.tln_nr IS NOT NULL "
		." AND a.snr < ".($out["liga"][0]->stamm+1)
		." GROUP BY e.tln_nr"
		;
	$out["DWZSchnitt"] = clm_core::$db->loadObjectList($DWZSchnittModel);

	
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
	$DWZgespieltModel .= " WHERE a.lid = $liga AND a.heim = 1 AND r.heim = 1 "
//		." GROUP BY a.dg ASC, a.runde ASC, a.paar ASC"
		." GROUP BY a.dg, a.runde, a.paar"
		;
	$out["DWZgespielt"] = clm_core::$db->loadObjectList($DWZgespieltModel);

	
  	$summeModel = " SELECT a.dg,a.paar as paarung,a.runde as runde,a.brettpunkte as sum "
		." FROM #__clm_rnd_man as a "
		." WHERE a.lid = ".$liga
		." ORDER BY a.dg ASC ,a.runde ASC, a.paar ASC, a.heim DESC "
		;
	$out["summe"] = clm_core::$db->loadObjectList($summeModel);

	
	
	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["paar"][0])) {
		return array(false, "e_paarungslisteError");
	}	
 
	return array(true, "m_paarungslisteSuccess", $out);
}
?>
