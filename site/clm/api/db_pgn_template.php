<?php
/**
* erstellt pgn-Template einer Runde eines Mannschafts- oder Einzelturniers
*/
function clm_api_db_pgn_template($id,$dg,$round,$type,$group=true) {
//echo "<br>id:"; var_dump($id); //die();	
	$id = clm_core::$load->make_valid($id, 0, -1);
	$dg = clm_core::$load->make_valid($dg, 0, -1);
	$round = clm_core::$load->make_valid($round, 0, -1);
	$type = clm_core::$load->make_valid($type, 0, -1);
	// CLM-Parameter auslesen	
	$config		= clm_core::$db->config();
	$name_subuml	= $config->fe_runde_subuml;
	$countryversion	= $config->countryversion;
	// Verein des aktiven Benutzers
	$user_zps = clm_core::$access->getUserZPS();
//echo "<br>user_zps:"; var_dump($user_zps); //die();	
	
	if($group) {
		// Liga auslesen
		$query = 'SELECT * FROM #__clm_liga'
			. ' WHERE id = '.$id;
		$turnier	= clm_core::$db->loadObjectList($query);
//echo "<br>turnier:"; var_dump($turnier); //die();
 		$params = new clm_class_params($turnier[0]->params);
//echo "<br>params:"; var_dump($params); //die();
//echo "<br>pgntype:"; var_dump($params->get("pgntype","0")); 
//echo "<br>pgnlname:"; var_dump($params->get("pgnlname","")); 
		
		// Rundentermin auslesen
		$nr = (($dg - 1) * $turnier[0]->runden) + $round;
		$query = " SELECT * FROM #__clm_runden_termine "
			." WHERE liga = ".$id
			." AND nr = ".$nr;
		$runde = clm_core::$db->loadObjectList($query);
//echo "<br>runde:"; var_dump($runde);	
		// Ergebnisse auslesen
		if ($turnier[0]->rang == 0) 
		{	$query = "  SELECT a.zps, a.gzps, a.paar,a.brett,a.spieler,a.PKZ,a.gegner,a.gPKZ,a.ergebnis,a.kampflos, a.dwz_edit, a.dwz_editor, a.weiss,"
				." m.name, n.name as mgname, m.sname, n.sname as smgname, d.Spielername as hname, d.DWZ as hdwz, d.FIDE_Elo as helo,"
				." p.erg_text as erg_text, e.Spielername as gname, e.DWZ as gdwz, e.FIDE_Elo as gelo, q.erg_text as dwz_text,"
				." k.snr as hsnr, l.snr as gsnr, k.start_dwz as hstart_dwz, l.start_dwz as gstart_dwz, k.attr as hattr, l.attr as gattr"
				." FROM #__clm_rnd_spl as a "
				." LEFT JOIN #__clm_rnd_man as r ON ( r.lid = a.lid AND r.runde = a.runde AND r.tln_nr = a.tln_nr AND  r.dg = a.dg) "
				." LEFT JOIN #__clm_mannschaften AS m ON ( m.tln_nr = r.tln_nr AND m.liga = a.lid) "
				." LEFT JOIN #__clm_mannschaften AS n ON ( n.tln_nr = r.gegner AND n.liga = a.lid) ";
			if ($countryversion =="de") {
				$query .= " LEFT JOIN #__clm_dwz_spieler AS d ON ( d.Mgl_Nr = a.spieler AND d.ZPS = a.zps AND d.sid = a.sid) "
					." LEFT JOIN #__clm_dwz_spieler AS e ON ( e.Mgl_Nr = a.gegner AND e.ZPS = a.gzps AND e.sid = a.sid) "
					." LEFT JOIN #__clm_meldeliste_spieler as k ON k.sid = a.sid AND k.lid = a.lid AND k.mgl_nr = a.spieler AND k.zps = a.zps AND k.mnr=m.man_nr "  
					." LEFT JOIN #__clm_meldeliste_spieler as l ON l.sid = a.sid AND l.lid = a.lid AND l.mgl_nr = a.gegner AND l.zps = a.gzps AND l.mnr=n.man_nr ";  
			} else {
				$query .= " LEFT JOIN #__clm_dwz_spieler AS d ON ( d.PKZ = a.PKZ AND d.ZPS = a.zps AND d.sid = a.sid) "
					." LEFT JOIN #__clm_dwz_spieler AS e ON ( e.PKZ = a.gPKZ AND e.ZPS = a.gzps AND e.sid = a.sid) "
					." LEFT JOIN #__clm_meldeliste_spieler as k ON k.sid = a.sid AND k.lid = a.lid AND k.PKZ = a.PKZ AND k.zps = a.zps AND k.mnr=m.man_nr "  
					." LEFT JOIN #__clm_meldeliste_spieler as l ON l.sid = a.sid AND l.lid = a.lid AND l.PKZ = a.gPKZ AND l.zps = a.gzps AND l.mnr=n.man_nr ";  
			}
			$query .= " LEFT JOIN #__clm_ergebnis AS p ON p.eid = a.ergebnis "
				." LEFT JOIN #__clm_ergebnis AS q ON q.eid = a.dwz_edit "
				." WHERE a.lid = ".$id
				." AND a.runde = ".$round
				." AND a.dg = ".$dg
				." AND a.heim = 1"
				." AND m.man_nr > 0 AND n.man_nr > 0 "
				." ORDER BY a.paar ASC, a.brett ASC"
				;
		} else 
			$query = "  SELECT a.zps, a.gzps, a.paar,a.brett,a.spieler,a.gegner,a.ergebnis,a.kampflos, a.dwz_edit, a.dwz_editor, a.weiss,"
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
			." WHERE a.lid = ".$id
			." AND a.runde = ".$round
			." AND a.dg = ".$dg
			." AND a.heim = 1"
			." ORDER BY a.paar ASC, a.brett ASC"
			;
		$einzel = clm_core::$db->loadObjectList($query);
//echo "<br>einzel:"; var_dump($einzel);	//die();
		
	} else {
		// Turnier auslesen
		$query = 'SELECT * FROM #__clm_turniere'
			.' WHERE id = '.$id;
		$turnier	= clm_core::$db->loadObjectList($query);
	
		// pgn-Notationen auslesen
		$query = " SELECT * FROM #__clm_pgn as a "
			." WHERE a.tkz = 's' "
			." AND a.tid = ".$id
			." AND a.dg > 0 "
			." ORDER BY a.dg, a.runde, a.paar, a.brett ";
		$pgn = clm_core::$db->loadObjectList($query);
		
	}
 		
	if(count($einzel)==0) {
		return array(false, "e_PgnNoDataError");
	}
//die();		
	$nl = "\n";
	$file_name = utf8_decode($turnier[0]->name).'_'.utf8_decode($runde[0]->name);
	if ($type == 1) $file_name .= '_'.utf8_decode($user_zps);
	$file_name .= '.pgn'; 
	$file_name = strtr($file_name,' ','_');
	if (!file_exists('components'.DS.'com_clm'.DS.'pgn'.DS)) mkdir('components'.DS.'com_clm'.DS.'pgn'.DS);
	$pdatei = fopen('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name,"wt");
	foreach ($einzel as $einz) {
	  if (($einz->zps == $user_zps) OR ($einz->gzps == $user_zps) OR ($type == 2)) {
		  $gtmarker = "*";
		  $resulthint = "";
		switch ($params->get("pgntype","0")) {
		  case 1:
			fputs($pdatei, '[Event "'.utf8_decode($turnier[0]->name).'"]'.$nl);
			break;
		  case 2:
			fputs($pdatei, '[Event "'.utf8_decode($params->get("pgnlname","")).'"]'.$nl);
			break;
		  case 3:
			fputs($pdatei, '[Event "'.utf8_decode($einz->name).' - '.utf8_decode($einz->mgname).'"]'.$nl);
			break;
		  case 4:
			fputs($pdatei, '[Event "'.utf8_decode($einz->sname).' - '.utf8_decode($einz->smgname).'"]'.$nl);
			break;
		  case 5:
			fputs($pdatei, '[Event "'.utf8_decode($params->get("pgnlname","")).': '.utf8_decode($einz->sname).' - '.utf8_decode($einz->smgname).'"]'.$nl);
			break;
		  default:
		 	fputs($pdatei, '[Event "'.'"]'.$nl);
		}
		fputs($pdatei, '[Site "?"]'.$nl);
//		fputs($pdatei, '[Date "'.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('Y.m.d')).'"]'.$nl);
		fputs($pdatei, '[Date "'.clm_core::$cms->showDate($runde[0]->datum, 'Y.m.d').'"]'.$nl);
		fputs($pdatei, '[Round "'.$round.'.'.$einz->paar.'"]'.$nl);
		fputs($pdatei, '[Board "'.$einz->brett.'"]'.$nl);
		if ($name_subuml == 1) {
			$einz->hname = clm_core::$load->sub_umlaute($einz->hname);
			$einz->gname = clm_core::$load->sub_umlaute($einz->gname);
			$einz->name = clm_core::$load->sub_umlaute($einz->name);
			$einz->mgname = clm_core::$load->sub_umlaute($einz->mgname);
		}
		if ($einz->weiss == "0") {
			fputs($pdatei, '[White "'.utf8_decode($einz->gname).'"]'.$nl);
			fputs($pdatei, '[Black "'.utf8_decode($einz->hname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.utf8_decode($einz->mgname).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.utf8_decode($einz->name).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$einz->gelo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$einz->helo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$einz->gdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$einz->hdwz.'"]'.$nl);
			if ($einz->ergebnis == 2) { fputs($pdatei, '[Result "1/2-1/2"]'.$nl); $gtmarker = "1/2-1/2"; }
			elseif ($einz->ergebnis == 1) { fputs($pdatei, '[Result "0-1"]'.$nl); $gtmarker = "0-1"; }
			elseif ($einz->ergebnis == 0) { fputs($pdatei, '[Result "1-0"]'.$nl); $gtmarker = "1-0"; }
			elseif ($einz->ergebnis == 4) { fputs($pdatei, '[Result "1-0"]'.$nl); $resulthint = "{".utf8_decode('Weiß gewinnt kampflos')."}"; $gtmarker = "1-0"; }
			elseif ($einz->ergebnis == 5) { fputs($pdatei, '[Result "0-1"]'.$nl); $resulthint = "{Schwarz gewinnt kampflos}"; $gtmarker = "0-1"; }
			elseif ($einz->ergebnis == 6) { fputs($pdatei, '[Result "*"]'.$nl); $resulthint = "{beide verlieren kampflos}"; $gtmarker = "*"; }
			else fputs($pdatei, '[Result "'.$einz->erg_text.'"]'.$nl);		
		} else {
			fputs($pdatei, '[White "'.utf8_decode($einz->hname).'"]'.$nl);
			fputs($pdatei, '[Black "'.utf8_decode($einz->gname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.utf8_decode($einz->name).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.utf8_decode($einz->mgname).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$einz->helo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$einz->gelo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$einz->hdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$einz->gdwz.'"]'.$nl);
			if ($einz->ergebnis == 2) { fputs($pdatei, '[Result "1/2-1/2"]'.$nl); $gtmarker = "1/2-1/2"; }
			elseif ($einz->ergebnis == 1) { fputs($pdatei, '[Result "1-0"]'.$nl); $gtmarker = "1-0"; }
			elseif ($einz->ergebnis == 0) { fputs($pdatei, '[Result "0-1"]'.$nl); $gtmarker = "0-1"; }
			elseif ($einz->ergebnis == 4) { fputs($pdatei, '[Result "0-1"]'.$nl); $resulthint = "{Schwarz gewinnt kampflos}"; $gtmarker = "0-1"; }
			elseif ($einz->ergebnis == 5) { fputs($pdatei, '[Result "1-0"]'.$nl); $resulthint = "{".utf8_decode('Weiß gewinnt kampflos')."}"; $gtmarker = "1-0"; }
			elseif ($einz->ergebnis == 6) { fputs($pdatei, '[Result "*"]'.$nl); $resulthint = "{beide verlieren kampflos}"; $gtmarker = "*"; }
			else fputs($pdatei, '[Result "'.$einz->erg_text.'"]'.$nl);		
		}
		fputs($pdatei, '[PlyCount "0"]'.$nl);
		fputs($pdatei, '[EventDate "'.clm_core::$cms->showDate($runde[0]->datum, 'Y.m.d').'"]'.$nl);
		fputs($pdatei, '[SourceDate "'.clm_core::$cms->showDate($runde[0]->datum, 'Y.m.d').'"]'.$nl);
		fputs($pdatei, ' '.$nl);
		fputs($pdatei, $resulthint.' '.$gtmarker.$nl);
		fputs($pdatei, ' '.$nl);
	  }
	}
	fclose($pdatei);
    if (file_exists('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name)) {
		header('Content-Disposition: attachment; filename="'.$file_name.'"');
		header('Content-Type: text/html');
		header('Cache-Control:');
		header('Pragma:');
		flush();
		readfile('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name);
		flush();
		exit;
	}
	
	return array(true, "m_PgnExportSuccess"); 
}
?>
