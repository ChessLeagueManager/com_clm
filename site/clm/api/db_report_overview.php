<?php
function clm_api_db_report_overview() {

	$jid = clm_core::$access->getJid();

	// Konfigurationsparameter auslesen
	$config		= clm_core::$db->config();
	$meldung_verein	= $config->meldung_verein;
	$meldung_heim	= $config->meldung_heim;

	$query = "SELECT l.rang,t.meldung,l.name as lname,i.gid,p.sid,p.lid,p.runde,p.paar,p.dg,p.tln_nr,p.gegner,a.zps,  "
		." l.durchgang as durchgang, " 
		." t.deadlineday, t.deadlinetime, " //klkl
		." m.id,m.sid,m.name,m.liga,m.man_nr,m.published,p.gemeldet "
		." , m.liste "
		." FROM #__clm_user as a"
		." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or FIND_IN_SET(a.zps,m.sg_zps) != 0 ) AND m.sid = a.sid "
		." LEFT JOIN #__clm_saison as s ON s.id = m.sid "
		." LEFT JOIN #__clm_rnd_man as p ON ( m.tln_nr = p.tln_nr AND p.lid = m.liga AND p.sid = a.sid)  "
		." LEFT JOIN #__clm_liga as l ON ( l.id = m.liga AND l.sid = m.sid) "
		." LEFT JOIN #__clm_rangliste_id as i ON i.zps = a.zps AND i.gid = l.rang "
		." LEFT JOIN #__clm_runden_termine as t ON t.nr = (p.runde + (l.runden * (p.dg - 1))) AND t.liga = m.liga AND t.sid = a.sid " //klkl
		." WHERE jid = ".$jid;
	if ($meldung_verein == 0) { $query = $query." AND mf = ".$jid;}
	if ($meldung_heim == 0) { $query = $query." AND p.heim = 1";}
		$query = $query
		." AND s.published = 1 AND s.archiv = 0 AND  l.rnd = 1 AND l.published = 1 "
		." ORDER BY l.rang, m.man_nr ASC, p.dg ASC, p.runde ASC "
		;
	$liga = clm_core::$db->loadObjectList($query);
	return array(true, "", $liga);
}
?>
