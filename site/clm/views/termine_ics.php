<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_termine_ics($out) {
	$lang = clm_core::$lang->schedule;

	// Variablen initialisieren
	$db_termine 		= $out["termine"];

	// Kein Ergebnis -> Daten Inkonsistent oder falsche Eingabe
	if (is_null($db_termine) OR count($db_termine) < 1) {
		return array(false, "e_terminlisteError");
	}	
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	$termine = array();
	
	$now = time();
	$first = true;

// Terminschleife 	
	foreach ($db_termine as $termin1) { 
		if ($first) {
			$first = false;
			$event = array();	
		}
		if ($termin1->startdate == "1970-01-01" OR $termin1->startdate == "0000-00-00") continue;
 		$startdate = substr($termin1->startdate,0,4).substr($termin1->startdate,5,2).substr($termin1->startdate,8,2);
		if (!is_numeric($startdate)) continue;
		if ($termin1->starttime == "00:00:00" OR $termin1->starttime == "24:00:00") $starttime .= '0000'; 
		else $starttime = substr($termin1->starttime,0,2).substr($termin1->starttime,3,2);
		if (!is_numeric($starttime)) continue;
//echo "<br>dtdt  $startdate  $starttime ";

		if ($termin1->enddate == "1970-01-01" OR $termin1->enddate == "0000-00-00") $termin1->enddate = '0000000000';
 		$enddate = substr($termin1->enddate,0,4).substr($termin1->enddate,5,2).substr($termin1->enddate,8,2);
		if (!is_numeric($enddate)) $enddate = '00000000';
		if ($termin1->endtime == "00:00:00" OR $termin1->endtime == "24:00:00") $endtime = '0000'; 
		else $endtime = substr($termin1->endtime,0,2).substr($termin1->endtime,3,2);
		if (!is_numeric($endtime)) $endtime = '0000';
//echo "<br>dtdt  $startdate  $starttime  $enddate  $endtime "; //die();

		$event = array();	
		$event['DSTART'] = $startdate;
		$event['TSTART'] = $starttime.'00';
		$event['DEND'] = $enddate;
		$event['TEND'] = $endtime.'00';
		if ($termin1->address > '') $event['LOCATION'] = $termin1->address;
		$event['SUMMARY'] = $termin1->name;
		$event['DESCRIPTION'] = $termin1->beschreibung;
/*		$event['DESCRIPTION2'] = 'Datum: '.clm_core::$cms->showDate($termin1->startdate, "d. M Y");
		if ($termin1->starttime > '0000') $event['DESCRIPTION3'] = 'Uhrzeit: '.substr($termin1->starttime,0,5);
		if ($termin1->starttime == '0000' AND  $term->allday == '1'	) $event['DESCRIPTION3'] = 	'ganztägig'; 
		if ($termin1->enddate > '00000000')	{
			$event['DESCRIPTION4'] = 'Endedatum: '.clm_core::$cms->showDate($termin1->enddate, "d. M Y");
			if ($termin1->endtime > '0000') $event['DESCRIPTION5'] = 'Endezeit: '.substr($termin1->endtime,0,5);
		}
*/		if ($termin1->host > '0') $event['CLM-HOST'] = $termin1->host; //$termin1->host;
		if (substr($termin1->event_link,0,4) == 'http') $event['ATTACH'] = $termin1->event_link;
		$event['UID'] = $termin1->id;
		$event['UID'] = 'CLM-A'.$termin1->id;  // A - Appointment
		$termine[] = $event;
    } 

	$filename = 'Termine'.'_'.$_SERVER['HTTP_HOST'];   
	$filename = clm_core::$load->file_name($filename);

	$result = clm_core::$api->db_ics_export($filename,$termine);

	$location = $_SERVER['HTTP_HOST'].str_replace('&format=ics', '', $_SERVER['REQUEST_URI']);
	header('Location: http://'.$location."&fnr=".$result[1]);
	exit;

	return array(true, "m_TermineICSExportSuccess"); 
}
?>