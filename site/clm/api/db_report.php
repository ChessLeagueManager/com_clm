<?php
// Eingang: Verband
// Ausgang: Alle Vereine in diesem
function clm_api_db_report($liga, $runde, $dg, $paar) {

	$liga = clm_core::$load->make_valid($liga, 0, -1);
	$runde = clm_core::$load->make_valid($runde, 0, -1);
	$dg = clm_core::$load->make_valid($dg, 0, -1);
	$paar = clm_core::$load->make_valid($paar, 0, -1);

	$out["input"]["liga"]=$liga;
	$out["input"]["runde"]=$runde;
	$out["input"]["dg"]=$dg;
	$out["input"]["paar"]=$paar;

	$config		= clm_core::$db->config();
	$meldung_verein	= $config->meldung_verein;
	$meldung_heim	= $config->meldung_heim;
	$countryversion = $config->countryversion;

	// Ist die Ergebniss Eingabe im Frontend erlaubt?
	if ($config->conf_ergebnisse != 1) {
		return array(false, "e_reportDisabled");
	}

	$jid = clm_core::$access->getJid();
	$id = clm_core::$access->getId();

	// Ist der Benutzer im CLM eingeloggt
	if ($id==-1) {
		return array(false, "e_reportLogin");
	} 
 
  	$paarModel = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln,"
		." h.id as hid, h.name as hname, h.tln_nr as htln, "
		." g.zps as gzps, g.sg_zps as gsgzps, g.mf as gast_mf, "
		." h.zps as hzps, h.sg_zps as hsgzps, h.mf as heim_mf "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner "
		." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr "
		." WHERE a.runde = ".$runde 
		." AND a.lid = ".$liga
		." AND a.heim = 1 "
		." AND a.dg = ".$dg
		." AND a.paar = ".$paar
		." AND g.liga = ".$liga
		." AND h.liga = ".$liga
		;
	$out["paar"] = clm_core::$db->loadObjectList($paarModel);

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["paar"][0])) {
		return array(false, "e_reportError");
	}	
 
	// Namen und Email der Mannschaftsleiter
	if (isset($out["paar"][0]->heim_mf) AND $out["paar"][0]->heim_mf > 0) {
		$hmfModel = " SELECT name, email "
			." FROM #__users "
			." WHERE id = ".$out["paar"][0]->heim_mf 
			;
		$out["hmf"] = clm_core::$db->loadObjectList($hmfModel);
		// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
		if (!isset($out["hmf"][0])) {
			return array(false, "e_reportError");
		}	
	}
	if (isset($out["paar"][0]->gast_mf) AND $out["paar"][0]->gast_mf > 0) {
		$gmfModel = " SELECT name, email "
			." FROM #__users "
			." WHERE id = ".$out["paar"][0]->gast_mf 
			;
		$out["gmf"] = clm_core::$db->loadObjectList($gmfModel);
		// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
		if (!isset($out["gmf"][0])) {
			return array(false, "e_reportError");
		}	
	}
	
 	$zps = clm_core::$db->user->get($id)->zps;
	 
	// Ist der Benutzer auch wirklich ein Spieler der beteiligten Mannschaften?
	if ($zps != $out["paar"][0]->hzps && $zps != $out["paar"][0]->gzps && (!isset($out["paar"][0]->hsgzps) || strpos($out["paar"][0]->hsgzps,$zps) === false) && (!isset($out["paar"][0]->gsgzps) || strpos($out["paar"][0]->gsgzps,$zps) === false)) {
		return array(false, "e_reportClub");
	}
 
 	// Ist der Benutzer Mannschaftsführer der Mannschaften oder dürfen alle Spieler einer Mannschaft das Ergebnis melden
	if ($out["paar"][0]->heim_mf != $jid && $out["paar"][0]->gast_mf != $jid && $config->meldung_verein == 0) {
		return array(false, "e_reportTeamLeader");
	}
	
	$someData = "SELECT a.gemeldet,a.editor, a.id,a.sid, a.lid, a.runde, a.dg, a.tln_nr,"
		." a.gegner,a.paar, a.dwz_zeit, a.dwz_editor,  "
		." a.zeit, a.edit_zeit,  "
		." m.name as hname,m.zps as hzps,m.man_nr as hmnr,m.sg_zps as sgh_zps, "
		." n.name as gname, n.zps as gzps, n.man_nr as gmnr, n.sg_zps as sgg_zps, "
		." l.name as lname, l.stamm, l.ersatz, l.sl as sl, l.rang, l.id as lid"
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." LEFT JOIN #__clm_mannschaften AS m ON (m.liga = a.lid AND m.tln_nr = a.tln_nr)"
		." LEFT JOIN #__clm_mannschaften AS n ON (n.liga = a.lid AND n.tln_nr = a.gegner)"
			." WHERE a.lid = ".$liga
			." AND a.runde = ".$runde
			." AND a.paar = ".$paar
			." AND a.dg = ".$dg
			." AND a.heim = 1  ";		
	$someData = clm_core::$db->loadObjectList($someData);

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($someData[0])) {
		return array(false, "e_reportError");
	}	

	$Heim = "SELECT a.*, d.Spielername as name ";
		if($someData[0]->rang !="0") {$Heim = $Heim.",r.rang ,r.man_nr as rmnr";}
		$Heim = $Heim
		." FROM #__clm_meldeliste_spieler as a ";
		if ($countryversion =="de") {
			$Heim .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		} else {
			$Heim .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.PKZ= a.PKZ AND d.sid = a.sid) ";
		}
		if($someData[0]->rang !="0") {
			$Heim = $Heim
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) ";
		}
		$Heim = $Heim
		." WHERE a.sid = ".$someData[0]->sid
		." AND (a.gesperrt = 0 OR a.gesperrt IS NULL )"
		." AND (( a.zps = '".$someData[0]->hzps."' AND a.mnr = ".$someData[0]->hmnr." )"
		." OR ( FIND_IN_SET(a.zps,'".$someData[0]->sgh_zps."') != 0 AND a.mnr = ".$someData[0]->hmnr." )) ";
		if ($countryversion =="de") {
			$Heim .= " AND a.mgl_nr <> '0' ";
		} else {
			$Heim .= " AND a.PKZ <> '' ";
		}
		if($someData[0]->rang !="0") {
			$Heim = $Heim
				." AND a.status = ".$someData[0]->rang
				." AND a.lid = ".$someData[0]->lid
				." ORDER BY r.man_nr,r.Rang"; }
		else { $Heim = $Heim
				." AND a.lid = ".$someData[0]->lid
				." ORDER BY a.snr"; }
				
	$out["heim"] = clm_core::$db->loadObjectList($Heim);	
	
	// Wurden für die Heimmannschaft schon Spieler gemeldet?
	if (!isset($out["heim"][0])) {
		return array(false, "e_reportListHome");
	}
	
	// Dürfen nur Heimspieler melden?
	if ($meldung_heim == 0 && $zps != $someData[0]->hzps && (!isset($someData[0]->sgh_zps) || strpos($someData[0]->sgh_zps,$zps) === false)) {
	//if ($zps != $out["heim"][0]->zps && $meldung_heim == 0) {
		return array(false, "e_reportOnlyHome");
	}
 
	$Gast = "SELECT a.*, d.Spielername as name";
		if($someData[0]->rang !="0") {$Gast = $Gast.",r.rang,r.man_nr as rmnr ";}
		$Gast = $Gast
		." FROM #__clm_meldeliste_spieler as a ";
		if ($countryversion =="de") {
			$Gast .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		} else {
			$Gast .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.PKZ= a.PKZ AND d.sid = a.sid) ";
		}
		if($someData[0]->rang !="0") {
			$Gast = $Gast
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) ";
		}
		$Gast = $Gast
		." WHERE a.sid = ".$someData[0]->sid
		." AND (a.gesperrt = 0 OR a.gesperrt IS NULL )"
		." AND (( a.zps = '".$someData[0]->gzps."' AND a.mnr = ".$someData[0]->gmnr." ) "
		." OR ( FIND_IN_SET(a.zps,'".$someData[0]->sgg_zps."') AND a.mnr = ".$someData[0]->gmnr." )) ";
		if ($countryversion =="de") {
			$Gast .= " AND a.mgl_nr <> '0' ";
		} else {
			$Gast .= " AND a.PKZ <> '' ";
		}
		if($someData[0]->rang !="0") {
			$Gast = $Gast
				." AND a.status = ".$someData[0]->rang
				." AND a.lid = ".$someData[0]->lid
				." ORDER BY r.man_nr,r.Rang"; }
		else { $Gast = $Gast
				." AND a.lid = ".$someData[0]->lid
				." ORDER BY a.snr"; }

	$out["gast"] = clm_core::$db->loadObjectList($Gast);	
	
	// Wurden für die Gastmannschaft schon Spieler gemeldet?
	if (!isset($out["gast"][0])) {
		return array(false, "e_reportListGuest");
	}
	
	$ligaModel = "SELECT a.*,t.datum as datum FROM #__clm_liga as a"
		." LEFT JOIN #__clm_runden_termine AS t ON t.liga = a.id AND t.nr = ($runde + ($dg -1) * a.runden)"  
		." WHERE a.id = ".$liga
		;

	$out["liga"] = clm_core::$db->loadObjectList($ligaModel);	
	
	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["liga"][0])) {
		return array(false, "e_reportError");
	}		
	// Namen und Email des Staffelleiters
	if (isset($out["liga"][0]->sl) AND $out["liga"][0]->sl > 0) {
		$slModel = " SELECT name, email "
			." FROM #__users "
			." WHERE id = ".$out["liga"][0]->sl 
			;
		$out["sl"] = clm_core::$db->loadObjectList($slModel);
		// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
		if (!isset($out["sl"][0])) {
			return array(false, "e_reportError");
		}	
	}
	
	$Access	= "SELECT gemeldet "
		." FROM #__clm_rnd_man "
		." WHERE lid = $liga AND runde = $runde "
		." AND paar = $paar AND dg = $dg AND heim = 1"
		;

	$out["access"] = clm_core::$db->loadObjectList($Access);

	$finish	= "SELECT *  "
		." FROM #__clm_runden_termine "
		." WHERE liga = ".$liga
		." ORDER BY nr ASC"
		;
		
	$finish = clm_core::$db->loadObjectList($finish);	
	
	if ($finish[($runde+(($dg-1)*$out["liga"][0]->runden)-1)]->meldung == 0 ) {  //klkl
		return array(false, "e_reportUnpublished", array($liga,$out["paar"][0]->sid));
	}
		// Prüfen ob Datensatz schon vorhanden ist
		$now = date('Y-m-d H:i:s'); 
		$mdt = $finish[($runde+(($dg-1)*$out["liga"][0]->runden)-1)]->deadlineday.' ';
		if ($finish[($runde+(($dg-1)*$out["liga"][0]->runden)-1)]->deadlinetime != '00:00:00') $mdt .= $finish[($runde+(($dg-1)*$out["liga"][0]->runden)-1)]->deadlinetime; else $mdt .= '24:00:00';
		if ($out["access"][0]->gemeldet > 0 AND $mdt < $now) {
		return array(false, "e_reportAlready", array($liga,$out["paar"][0]->sid));
	}	

	$Ergebnis = " SELECT eid, erg_text "
		." FROM #__clm_ergebnis "
			;

	$out["ergebnis"] = clm_core::$db->loadObjectList($Ergebnis);

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($out["ergebnis"][0])) {
		return array(false, "e_reportError");
	}		
	

	$ergebnis = new ArrayObject($out["ergebnis"]);


	$punkteText1 = " SELECT a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$liga
		;

	$punkteText1 = clm_core::$db->loadObjectList($punkteText1);
	
	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (!isset($punkteText1[0])) {
		return array(false, "e_reportError");
	}		
	
	$sieg 		= $punkteText1[0]->sieg;
	$remis 		= $punkteText1[0]->remis;
	$nieder		= $punkteText1[0]->nieder;
	$antritt	= $punkteText1[0]->antritt;

	// Ergebnistexte nach Modus setzen
	$ergebnis[0]->erg_text = ($nieder+$antritt)." - ".($sieg+$antritt);
	$ergebnis[1]->erg_text = ($sieg+$antritt)." - ".($nieder+$antritt);
	$ergebnis[2]->erg_text = ($remis+$antritt)." - ".($remis+$antritt);
	$ergebnis[3]->erg_text = ($nieder+$antritt)." - ".($nieder+$antritt);
	if ($antritt > 0) {
		$ergebnis[4]->erg_text = "0 - ".round($antritt+$sieg)." (kl)";
		$ergebnis[5]->erg_text = round($antritt+$sieg)." - 0 (kl)";
		$ergebnis[6]->erg_text = "0 - 0 (kl)";
	}
	$ergebnis[9]->erg_text = ($nieder+$antritt)." - ".($remis+$antritt);
	$ergebnis[10]->erg_text = ($remis+$antritt)." - ".($nieder+$antritt);
	
	$out["punkteText"] = $ergebnis;

	$result	= "SELECT * "
		." FROM #__clm_rnd_spl "
		." WHERE lid = $liga AND runde = $runde "
		." AND paar = $paar AND dg = $dg AND heim = 1 "
		." ORDER BY brett "
		;
		
	$out["oldresult"] = clm_core::$db->loadObjectList($result);

	return array(true, "m_reportSuccess", $out);
}
?>
