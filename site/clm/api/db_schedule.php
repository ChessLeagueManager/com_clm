<?php
// Eingang: Saison, Club
function clm_api_db_schedule($season, $club) {
	$season = clm_core::$load->make_valid($season, 0, -1);

	$out["input"]["season"]=$season;
	$out["input"]["club"]=$club;

	$config		= clm_core::$db->config();
	$meldung_verein	= $config->meldung_verein;
	$meldung_heim	= $config->meldung_heim;
	$countryversion = $config->countryversion;

 
  	$clubModel = " SELECT a.*, s.name as season_name "
		." FROM #__clm_vereine as a "
		." LEFT JOIN #__clm_saison AS s ON s.id = a.sid "
		." WHERE a.sid = ".$season 
		." AND ( a.zps = '".$club."' ) "
		;
	$out["club"] = clm_core::$db->loadObjectList($clubModel);

  	$paarModel = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln,"
		." b.brettpunkte as gbrettpunkte, "
		." h.id as hid, h.name as hname, h.tln_nr as htln, "
		." g.zps as gzps, g.sg_zps as gsgzps, g.mf as gast_mf, "
		." h.zps as hzps, h.sg_zps as hsgzps, h.mf as heim_mf, "
		." l.params, l.name as lname, "
		." r.datum as rdatum, r.startzeit, "
		." IF(a.pdate > '1970-01-01', a.pdate, r.datum) as rdate, "
		." IF(a.pdate > '1970-01-01', a.ptime, r.startzeit) as rtime "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_rnd_man AS b ON a.lid = b.lid AND b.tln_nr = a.gegner "
			." AND a.dg = b.dg AND a.runde = b.runde AND a.paar = b.paar "
		." LEFT JOIN #__clm_mannschaften AS g ON a.lid = g.liga AND g.tln_nr = a.gegner "
		." LEFT JOIN #__clm_mannschaften AS h ON a.lid = h.liga AND h.tln_nr = a.tln_nr "
		." LEFT JOIN #__clm_liga AS l ON a.lid = l.id "
		." LEFT JOIN #__clm_runden_termine AS r ON a.lid = r.liga AND (((a.dg - 1) * l.runden) + a.runde) = r.nr "
		." WHERE a.sid = ".$season 
		." AND a.heim = 1 "
		." AND ( g.zps = '".$club."' OR h.zps = '".$club."' ) "
		." ORDER BY rdate, rtime, a.lid, a.dg, a.runde, a.paar "
		//." ORDER BY a.lid, a.dg, a.runde, a.paar "
		;
	$out["paar"] = clm_core::$db->loadObjectList($paarModel);

	  	$club_listModel = " SELECT a.* "
		." FROM #__clm_vereine as a "
		." WHERE a.sid = ".$season 
		." ORDER BY a.name "
		;
	$out["club_list"] = clm_core::$db->loadObjectList($club_listModel);

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["club"][0])) {
		return array(false, "e_scheduleError");
	}	
 
	return array(true, "m_reportSuccess", $out);
}
?>
