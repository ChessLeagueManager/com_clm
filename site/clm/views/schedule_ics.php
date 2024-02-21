<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_schedule_ics($out) {
	$lang = clm_core::$lang->schedule;

	// Variablen initialisieren
	$paar 		= $out["paar"];
	$club 		= $out["club"];

	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	$termine = array();
	
	$now = time();
	$first = true;

// Terminschleife 	
	foreach ($paar as $paar1) { 
		if ($first) {
			$first = false;
			$event = array();	
		}
		if ($paar1->rdate > "1970-01-01") { $prdate = clm_core::$cms->showDate($paar1->rdate, "d M Y"); 
								if ($paar1->rtime != "00:00:00" AND $paar1->rtime != "24:00:00") $prdate .= ' '.substr($paar1->rtime,0,5); }
		else continue;
		$prdate = substr($paar1->rdate,0,4).substr($paar1->rdate,5,2).substr($paar1->rdate,8,2);
//		$prdate = '2024'.substr($paar1->rdate,5,2).substr($paar1->rdate,8,2);
		if (!is_numeric($prdate)) continue;
		$prtime = substr($paar1->rtime,0,2).substr($paar1->rtime,3,2);
		if (!is_numeric($prtime)) continue;
		$event = array();	
		$event['DSTART'] = $prdate;
		$event['TSTART'] = $prtime.'00';
		$event['DEND'] = $prdate;
		$event['TEND'] = sprintf("%06d", $event['TSTART'] + 50000);
//		$event['SUMMARY'] = 'Punktspiel '.clm_core::$load->utf8decode($paar1->lname).' : '.clm_core::$load->utf8decode($paar1->hname).' - '.clm_core::$load->utf8decode($paar1->gname);
		$event['SUMMARY'] = 'Punktspiel '.$paar1->lname.' : '.$paar1->hname.' - '.$paar1->gname;
//		$event['DESCRIPTION'] = 'Punktspiel '.clm_core::$load->utf8decode($paar1->lname).' : '.clm_core::$load->utf8decode($paar1->hname).' - '.clm_core::$load->utf8decode($paar1->gname);
		$event['UID'] = 'CLM-C'.$paar1->lid.$paar1->dg.$paar1->runde.$paar1->paar;  // C - Club Schedule
		$termine[] = $event;
//		break;
    } 

	$filename = 'Schedule'.'_'.clm_core::$load->utf8decode($club[0]->name."_".$club[0]->season_name);   
	$filename = clm_core::$load->file_name($filename);
	
	$result = clm_core::$api->db_ics_export($filename,$termine);

	$location = $_SERVER['HTTP_HOST'].str_replace('&format=ics', '', $_SERVER['REQUEST_URI']);
	header('Location: http://'.$location."&fnr=".$result[1]);
	exit;

	return array(true, "m_ScheduleICSExportSuccess"); 
}
?>