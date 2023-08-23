<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Input: Liga, Runde, Durchgang
// Output: NWZ-Durchschnitt
function clm_api_db_nwz_average($lid,$runde=0,$dg=1) {
	$lid = clm_core::$load->make_valid($lid, 0, -1);
	$runde = clm_core::$load->make_valid($runde, 0, -1);
	$dg = clm_core::$load->make_valid($dg, 0, -1);

	$query = "SELECT l.* "
			." FROM  #__clm_liga as l "
			." WHERE l.id = ".$lid
			;
	$l = clm_core::$db->loadObjectList($query);
	if (is_null($l) OR count($l) != 1)	return array(false,0,'e_noLeague');
	
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	
	// Startwerte setzen
	$a_sum_dwz = array();
	$a_count_player = array();
	$a_count_nnplayer = array();
	$a_average_dwz = array();
	$a_average_dwz_p = array();
	for ($s=0; $s <= $l[0]->teil; $s++) { // alle Startnummern durchgehen
		$a_sum_dwz[$s] = 0;
		$a_count_player[$s] = 0;
		$a_count_nnplayer[$s] = 0;
		$a_average_dwz[$s] = 0;
		$a_average_dwz_p[$s] = '-';
	}

	if ($l[0]->anzeige_ma == 1) {
		return array(true, $a_average_dwz, $a_average_dwz_p);	
	}
	
	// Ligaparameter bereitstellen
 	$params = new clm_class_params($l[0]->params);
	$params_dwz_date = $params->get('dwz_date','1970-01-01');
	$params_pseudo_dwz = $params->get('pseudo_dwz',0);
	
	// DWZ-Schnitt laut Aufstellung 
	if ($runde == 0) {
		$query = "SELECT ml.*, d.dwz as dwz, m.tln_nr "
			." FROM #__clm_meldeliste_spieler AS ml "
			." LEFT JOIN #__clm_mannschaften AS m ON (m.liga=ml.lid AND (m.zps=ml.zps OR FIND_IN_SET(ml.zps,m.sg_zps) != 0 OR (m.zps = '0' AND ml.zps = '-1')) AND m.man_nr = ml.mnr AND m.man_nr !=0 AND m.liste !=0) ";
		if ($countryversion == 'de')
			$query .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = ml.mgl_nr AND d.ZPS = ml.zps AND d.sid = ml.sid )";
		else
			$query .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.PKZ = ml.PKZ AND d.ZPS = ml.zps AND d.sid = ml.sid )";
		$query 	.= " WHERE ml.lid = ".$lid
			." AND ml.snr < ".($l[0]->stamm + 1);
			
		$mliste = clm_core::$db->loadObjectList($query);
		
		foreach ($mliste as $mliste1) {
			if (is_null($mliste1->tln_nr)) continue;
			if ($params_dwz_date == '0000-00-00' OR $params_dwz_date == '1970-01-01') {
				if (isset($mliste1->dwz) AND is_numeric($mliste1->dwz) AND $mliste1->dwz > 0) {
					$a_sum_dwz[$mliste1->tln_nr] += $mliste1->dwz; 
					$a_count_player[$mliste1->tln_nr]++;
				} elseif ($params_pseudo_dwz > 0) {
					$a_sum_dwz[$mliste1->tln_nr] += $params_pseudo_dwz; 
					$a_count_player[$mliste1->tln_nr]++;
				}
			} else {
				if (isset($mliste1->start_dwz) AND is_numeric($mliste1->start_dwz) AND $mliste1->start_dwz > 0) {
					$a_sum_dwz[$mliste1->tln_nr] += $mliste1->start_dwz; 
					$a_count_player[$mliste1->tln_nr]++;
				} elseif ($params_pseudo_dwz > 0) {
					$a_sum_dwz[$mliste1->tln_nr] += $params_pseudo_dwz; 
					$a_count_player[$mliste1->tln_nr]++;
				}
			}
		}
		if ($params_pseudo_dwz > 0) {
			for ($s=0; $s <= $l[0]->teil; $s++) { // alle Startnummern durchgehen
				if ($a_count_player[$s] > 0 AND $a_count_player[$s] < $l[0]->stamm ) {
					$a_sum_dwz[$s] += ($l[0]->stamm - $a_count_player[$s]) * $params_pseudo_dwz;
					$a_count_player[$s] = $l[0]->stamm;
				}
			} 
		}
	}	

	// DWZ-Schnitt gespielt ermitteln
	if ($runde > 0) {
		$query = "SELECT s.*, d.dwz as dwz, ml.start_dwz as start_dwz "
			." FROM #__clm_rnd_spl AS s ";
		if ($countryversion == 'de')
			$query .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.Mgl_Nr = s.spieler AND d.ZPS = s.zps AND d.sid = s.sid )";
		else
			$query .= " LEFT JOIN #__clm_dwz_spieler AS d ON (d.PKZ = s.PKZ AND d.ZPS = s.zps AND d.sid = s.sid )";
		$query .= " LEFT JOIN #__clm_mannschaften AS m ON (m.liga= s.lid AND m.tln_nr = s.tln_nr) ";
		if ($countryversion == 'de')
			$query .= " LEFT JOIN #__clm_meldeliste_spieler AS ml ON (ml.lid = s.lid AND ml.mnr = m.man_nr AND ml.zps=s.zps AND ml.mgl_nr = s.spieler) ";
		else
			$query .= " LEFT JOIN #__clm_meldeliste_spieler AS ml ON (ml.lid = s.lid AND ml.mnr = m.man_nr AND ml.zps=s.zps AND ml.PKZ = s.PKZ) ";
		$query .= " WHERE s.lid = ".$lid
			 ." AND s.runde = ".$runde." AND s.dg = ".$dg;
			
		$mliste = clm_core::$db->loadObjectList($query);

		foreach ($mliste as $mliste1) {
			if ($params_dwz_date == '0000-00-00' OR $params_dwz_date == '1970-01-01') {
				if (isset($mliste1->dwz) AND is_numeric($mliste1->dwz) AND $mliste1->dwz > 0) {
					$a_sum_dwz[$mliste1->tln_nr] += $mliste1->dwz; 
					$a_count_player[$mliste1->tln_nr]++;
				} elseif ($params_pseudo_dwz > 0) {
					if (($countryversion == 'de' AND $mliste1->spieler != '0') OR ($countryversion == 'en' AND $mliste1->PKZ != '')) {
						$a_sum_dwz[$mliste1->tln_nr] += $params_pseudo_dwz; 
						$a_count_player[$mliste1->tln_nr]++;
					}
				}
			} else {
				if (isset($mliste1->start_dwz) AND is_numeric($mliste1->start_dwz) AND $mliste1->start_dwz > 0) {
					$a_sum_dwz[$mliste1->tln_nr] += $mliste1->start_dwz; 
					$a_count_player[$mliste1->tln_nr]++;
				} elseif ($params_pseudo_dwz > 0) {
					if (($countryversion == 'de' AND $mliste1->spieler != '0') OR ($countryversion == 'en' AND $mliste1->PKZ != '')) {
						$a_sum_dwz[$mliste1->tln_nr] += $params_pseudo_dwz; 
						$a_count_player[$mliste1->tln_nr]++;
					}
				}
			}
			if (($mliste1->spieler == '99999' OR $mliste1->PKZ == '99999') AND $mliste1->zps == 'ZZZZZ') $a_count_nnplayer[$mliste1->tln_nr]++;
		}
	}

	for ($s=0; $s <= $l[0]->teil; $s++) { // alle Startnummern durchgehen
		if ($l[0]->stamm == $a_count_nnplayer[$s]) {
			$a_sum_dwz[$s] = 0;
		} 
	}
	for ($s=0; $s <= $l[0]->teil; $s++) { // alle Startnummern durchgehen
		if ($a_count_player[$s] > 0) {
			$a_average_dwz[$s] = round($a_sum_dwz[$s]/$a_count_player[$s]);
			$a_average_dwz_p[$s] = (string) $a_average_dwz[$s];
		} 
	}

	return array(true, $a_average_dwz, $a_average_dwz_p);	
}
?>
