<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Bestimmung aktuelle Runde auf Basis Liga-Ingex
*/
// Eingang: Liga-Index
// Ausgang: aktuelle Runde einschl. Durchgang

function clm_api_db_xml_round($lid) {
	$lid = clm_core::$load->make_valid($lid, 0, -1);
	$out["input"]["lid"] = $lid;
		
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$datum_sl = $config->fe_runde_aktuell;

	// Aktuelle Runde aus SL OK (= 0) oder Datum (= 1) errechnen
	if($datum_sl == 0) {
		$query = "SELECT * FROM #__clm_runden_termine WHERE liga = $lid AND sl_ok = 1 ORDER BY nr DESC LIMIT 1";
		$data	= clm_core::$db->loadObjectList($query);
		// Es existiert noch keine SL Freigabe
		if(count($data) < 1) {
			$runde	= 1;
			$dg	= 1;
		} else {
			$nr	= $data[0]->nr;
			// Es gibt mindestens eine SL Freigabe
			$query = "SELECT runden, durchgang FROM #__clm_liga WHERE id = $lid";
			$liga_db	= clm_core::$db->loadObjectList($query);
			$rnd	= $liga_db[0]->runden;
			$dg	= $liga_db[0]->durchgang;
	
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
				$query = "SELECT * FROM #__clm_runden_termine WHERE liga = $lid AND nr = ".($nr+1);
				$data	= clm_core::$db->loadObjectList($query);
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
		}
			}
	} else {

		$query	= "SELECT runden, durchgang FROM #__clm_liga WHERE id = $lid";	
		$liga_db	= clm_core::$db->loadObjectList($query);
		$rnd	= $liga_db[0]->runden;

		$query	= "SELECT nr, datum FROM #__clm_runden_termine WHERE liga = $lid ORDER BY datum";
		$data	= clm_core::$db->loadObjectList($query);

		$now	= strtotime ( 'now' );
		$nr	= 0;
		foreach($data as $aktuell) {
			if (strtotime ( $aktuell->datum.' 00:00:00') > $now) {
				break;
			}
			if ($aktuell->datum != '0000-00-00' AND $aktuell->datum != '1970-01-01') {
				$nr ++;
			}
		}
		if ($nr > $rnd){
			$runde	= $nr - $rnd;
			$dg	= 2;
		} else {
			$runde	= $nr;
			$dg	= 1;
		}
		if ($nr =="0") {
			$runde = 1;
			$dg =1;
		}
	}

	$out["lid"]=$lid;
	$out["runde"]=$runde;
	$out["dg"]=$dg;	

	return array(true, "m_xmlDataSuccess", $out);
}
?>
