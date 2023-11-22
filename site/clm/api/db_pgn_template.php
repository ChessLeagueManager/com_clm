<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * erstellt pgn-Template einer Runde eines Mannschafts- oder Einzelturniers
*/
function clm_api_db_pgn_template($id,$dg,$round,$type,$group=true) {
 	$lang = clm_core::$lang->pgn;
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
	
	if($group) {
		// Liga auslesen
		$query = 'SELECT * FROM #__clm_liga'
			. ' WHERE id = '.$id;
		$turnier	= clm_core::$db->loadObject($query);
 		$params = new clm_class_params($turnier->params);
		
		// Rundentermin auslesen
		$nr = (($dg - 1) * $turnier->runden) + $round;
		$query = " SELECT * FROM #__clm_runden_termine "
			." WHERE liga = ".$id
			." AND nr = ".$nr;
		$runde = clm_core::$db->loadObject($query);
		// Ergebnisse auslesen
		if ($turnier->rang == 0) 
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
			." LEFT JOIN #__clm_rangliste_spieler as t on t.ZPS = a.zps AND t.Mgl_Nr = a.spieler AND t.sid = a.sid AND t.Gruppe = ".$turnier->rang
			." LEFT JOIN #__clm_rangliste_spieler as s on s.ZPS = a.gzps AND s.Mgl_Nr = a.gegner AND s.sid = a.sid AND s.Gruppe = ".$turnier->rang
			." WHERE a.lid = ".$id
			." AND a.runde = ".$round
			." AND a.dg = ".$dg
			." AND a.heim = 1"
			." ORDER BY a.paar ASC, a.brett ASC"
			;
		$matches = clm_core::$db->loadObjectList($query);
		
	} else {
		// Turnier auslesen
		$query = 'SELECT * FROM #__clm_turniere'
			.' WHERE id = '.$id;
		$turnier	= clm_core::$db->loadObject($query);
	
		// Rundentermin auslesen
		$query = "SELECT * FROM #__clm_turniere_rnd_termine"
			.' WHERE turnier = '.$id." AND dg = ".$dg." AND nr = ".$round;
		$runde = clm_core::$db->loadObject($query);
		// Ergebnisse auslesen
		$query = " SELECT a.*, "
			." t.name as wname, t.twz as wtwz, t.verein as wverein, t.start_dwz as wdwz, t.FIDEelo as welo, "
			." u.name as sname, u.twz as stwz, u.verein as sverein, u.start_dwz as sdwz, u.FIDEelo as selo, "
			." pg.text "
			." FROM #__clm_turniere_rnd_spl as a"
			." LEFT JOIN #__clm_turniere_tlnr as t ON t.snr = a.spieler AND t.turnier = a.turnier "
			." LEFT JOIN #__clm_turniere_tlnr as u ON u.snr = a.gegner AND u.turnier = a.turnier "
			." LEFT JOIN #__clm_pgn as pg ON a.pgn = pg.id "
			." WHERE a.turnier = ".$id
			." AND a.runde = ".$round." AND a.dg = ".$dg
			." AND a.heim = 1 "
			." ORDER BY a.brett ASC ";
		$matches = clm_core::$db->loadObjectList($query);
	
	}
 		
	if(count($matches)==0) {
		return array(false, "e_PgnNoDataError");
	}
 	
	$nl = "\n";
	$file_name = clm_core::$load->utf8decode($turnier->name).'_'.clm_core::$load->utf8decode($runde->name);
	if ($type == 1) $file_name .= '_'.clm_core::$load->utf8decode($user_zps);
	$file_name = strtr($file_name,' ./','___');
	$file_name .= '.pgn'; 
	if (!file_exists('components'.DS.'com_clm'.DS.'pgn'.DS)) mkdir('components'.DS.'com_clm'.DS.'pgn'.DS);
	$pdatei = fopen('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name,"wt");
	if($group) {
	 foreach ($matches as $einz) {
	  if (($einz->zps == $user_zps) OR ($einz->gzps == $user_zps) OR ($type == 2)) {
		  $gtmarker = "*";
		  $resulthint = "";
		if ($name_subuml == 1) {
			$einz->hname = clm_core::$load->sub_umlaute($einz->hname);
			$einz->gname = clm_core::$load->sub_umlaute($einz->gname);
			$einz->name = clm_core::$load->sub_umlaute($einz->name);
			$einz->mgname = clm_core::$load->sub_umlaute($einz->mgname);
			$einz->sname = clm_core::$load->sub_umlaute($einz->sname);
			$einz->smgname = clm_core::$load->sub_umlaute($einz->smgname);
		}
		switch ($params->get("pgntype","0")) {
		  case 1:
			fputs($pdatei, '[Event "'.clm_core::$load->utf8decode($turnier->name).'"]'.$nl);
			break;
		  case 2:
			fputs($pdatei, '[Event "'.clm_core::$load->utf8decode($params->get("pgnlname","")).'"]'.$nl);
			break;
		  case 3:
			fputs($pdatei, '[Event "'.clm_core::$load->utf8decode($einz->name).' - '.clm_core::$load->utf8decode($einz->mgname).'"]'.$nl);
			break;
		  case 4:
			fputs($pdatei, '[Event "'.clm_core::$load->utf8decode($einz->sname).' - '.clm_core::$load->utf8decode($einz->smgname).'"]'.$nl);
			break;
		  case 5:
			fputs($pdatei, '[Event "'.clm_core::$load->utf8decode($params->get("pgnlname","")).': '.clm_core::$load->utf8decode($einz->sname).' - '.clm_core::$load->utf8decode($einz->smgname).'"]'.$nl);
			break;
		  default:
		 	fputs($pdatei, '[Event "'.'"]'.$nl);
		}
		fputs($pdatei, '[Site "?"]'.$nl);
//		fputs($pdatei, '[Date "'.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('Y.m.d')).'"]'.$nl);
		fputs($pdatei, '[Date "'.clm_core::$cms->showDate($runde->datum, 'Y.m.d').'"]'.$nl);
		fputs($pdatei, '[Round "'.$round.'.'.$einz->paar.'"]'.$nl);
		fputs($pdatei, '[Board "'.$einz->brett.'"]'.$nl);
		if ($einz->weiss == "0") {
			fputs($pdatei, '[White "'.clm_core::$load->utf8decode($einz->gname).'"]'.$nl);
			fputs($pdatei, '[Black "'.clm_core::$load->utf8decode($einz->hname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.clm_core::$load->utf8decode($einz->mgname).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.clm_core::$load->utf8decode($einz->name).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$einz->gelo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$einz->helo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$einz->gdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$einz->hdwz.'"]'.$nl);
			if ($einz->ergebnis == 2) { fputs($pdatei, '[Result "1/2-1/2"]'.$nl); $gtmarker = "1/2-1/2"; }
			elseif ($einz->ergebnis == 1) { fputs($pdatei, '[Result "0-1"]'.$nl); $gtmarker = "0-1"; }
			elseif ($einz->ergebnis == 0) { fputs($pdatei, '[Result "1-0"]'.$nl); $gtmarker = "1-0"; }
			elseif ($einz->ergebnis == 4) { fputs($pdatei, '[Result "1-0"]'.$nl); $resulthint = "{".clm_core::$load->utf8decode($lang->white)."}"; $gtmarker = "1-0"; }
			elseif ($einz->ergebnis == 5) { fputs($pdatei, '[Result "0-1"]'.$nl); $resulthint = "{".clm_core::$load->utf8decode($lang->black)."}"; $gtmarker = "0-1"; }
			elseif ($einz->ergebnis == 6) { fputs($pdatei, '[Result "*"]'.$nl); $resulthint = "{".clm_core::$load->utf8decode($lang->nobody)."}"; $gtmarker = "*"; }
			else fputs($pdatei, '[Result "'.$einz->erg_text.'"]'.$nl);		
		} else {
			fputs($pdatei, '[White "'.clm_core::$load->utf8decode($einz->hname).'"]'.$nl);
			fputs($pdatei, '[Black "'.clm_core::$load->utf8decode($einz->gname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.clm_core::$load->utf8decode($einz->name).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.clm_core::$load->utf8decode($einz->mgname).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$einz->helo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$einz->gelo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$einz->hdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$einz->gdwz.'"]'.$nl);
			if ($einz->ergebnis == 2) { fputs($pdatei, '[Result "1/2-1/2"]'.$nl); $gtmarker = "1/2-1/2"; }
			elseif ($einz->ergebnis == 1) { fputs($pdatei, '[Result "1-0"]'.$nl); $gtmarker = "1-0"; }
			elseif ($einz->ergebnis == 0) { fputs($pdatei, '[Result "0-1"]'.$nl); $gtmarker = "0-1"; }
			elseif ($einz->ergebnis == 4) { fputs($pdatei, '[Result "0-1"]'.$nl); $resulthint = "{".clm_core::$load->utf8decode($lang->black)."}"; $gtmarker = "0-1"; }
			elseif ($einz->ergebnis == 5) { fputs($pdatei, '[Result "1-0"]'.$nl); $resulthint = "{".clm_core::$load->utf8decode($lang->white)."}"; $gtmarker = "1-0"; }
			elseif ($einz->ergebnis == 6) { fputs($pdatei, '[Result "*"]'.$nl); $resulthint = "{".clm_core::$load->utf8decode($lang->nobody)."}"; $gtmarker = "*"; }
			else fputs($pdatei, '[Result "'.$einz->erg_text.'"]'.$nl);		
		}
		fputs($pdatei, '[PlyCount "0"]'.$nl);
		fputs($pdatei, '[EventDate "'.clm_core::$cms->showDate($runde->datum, 'Y.m.d').'"]'.$nl);
		fputs($pdatei, '[SourceDate "'.clm_core::$cms->showDate($runde->datum, 'Y.m.d').'"]'.$nl);
		fputs($pdatei, ' '.$nl);
		fputs($pdatei, $resulthint.' '.$gtmarker.$nl);
		fputs($pdatei, ' '.$nl);
	  }
	}
	} else {
	 foreach ($matches as $value) {
		if ( ($value->spieler != 0 AND $value->gegner != 0) OR !is_null($value->ergebnis)) {
			$gtmarker = "*";
			$resulthint = "";
			fputs($pdatei, '[Event "'.clm_core::$load->utf8decode($turnier->name).'"]'.$nl);
			fputs($pdatei, '[Site "?"]'.$nl);
			//fputs($pdatei, '[Date "'.JHTML::_('date',  $runde->datum, JText::_('Y.m.d')).'"]'.$nl);
			fputs($pdatei, '[Date "'.clm_core::$cms->showDate($runde->datum, 'Y.m.d').'"]'.$nl);
			fputs($pdatei, '[Round "'.$runde->nr.'"]'.$nl);
			fputs($pdatei, '[Board "'.$value->brett.'"]'.$nl);
			if ($name_subuml == 1) {
				$value->wname = clm_core::$load->sub_umlaute($value->wname);
				$value->sname = clm_core::$load->sub_umlaute($value->sname);
				$value->wverein = clm_core::$load->sub_umlaute($value->wverein);
				$value->sverein = clm_core::$load->sub_umlaute($value->sverein);
			}
			fputs($pdatei, '[White "'.clm_core::$load->utf8decode($value->wname).'"]'.$nl);
			fputs($pdatei, '[Black "'.clm_core::$load->utf8decode($value->sname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.clm_core::$load->utf8decode($value->wverein).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.clm_core::$load->utf8decode($value->sverein).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$value->welo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$value->selo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$value->wdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$value->sdwz.'"]'.$nl);
			if ($value->ergebnis == "2") { fputs($pdatei, '[Result "1/2-1/2"]'.$nl); $gtmarker = "1/2-1/2"; }
			elseif ($value->ergebnis == "0") { fputs($pdatei, '[Result "0-1"]'.$nl); $gtmarker = "0-1"; }
			elseif ($value->ergebnis == "1") { fputs($pdatei, '[Result "1-0"]'.$nl); $gtmarker = "1-0"; }
			elseif ($value->ergebnis == "5") { fputs($pdatei, '[Result "1-0"]'.$nl); $resulthint = "{".clm_core::$load->utf8decode($lang->white)."}"; $gtmarker = "1-0"; }
			elseif ($value->ergebnis == "4") { fputs($pdatei, '[Result "0-1"]'.$nl); $resulthint = "{".clm_core::$load->utf8decode($lang->black)."}"; $gtmarker = "0-1"; }
			elseif ($value->ergebnis == "6") { fputs($pdatei, '[Result "*"]'.$nl); $resulthint = "{".clm_core::$load->utf8decode($lang->nobody)."}"; $gtmarker = "*"; }
			else fputs($pdatei, '[Result "'.$value->ergebnis.'"]'.$nl);		
			fputs($pdatei, '[PlyCount "0"]'.$nl);
			//fputs($pdatei, '[EventDate "'.JHTML::_('date',  $turnier->dateStart, JText::_('Y.m.d')).'"]'.$nl);
			//fputs($pdatei, '[SourceDate "'.JHTML::_('date',  $runde->datum, JText::_('Y.m.d')).'"]'.$nl);
			fputs($pdatei, '[EventDate "'.clm_core::$cms->showDate($turnier->dateStart, 'Y.m.d').'"]'.$nl);
			fputs($pdatei, '[SourceDate "'.clm_core::$cms->showDate($runde->datum, 'Y.m.d').'"]'.$nl);
			fputs($pdatei, ' '.$nl);
			fputs($pdatei, $resulthint.' '.$gtmarker.$nl);
			fputs($pdatei, ' '.$nl);
		}
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
