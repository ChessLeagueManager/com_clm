<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
/**
* erstellt eine ics-Datei mit einen oder mehreren Terminen als Kalendereinträge
* diese Datei kann dann in andere CLM-Installationen oder andere Kalenderprogramme eingelesen werden
*/
function clm_api_db_ics_export($file_name,$itermine) {
	$lang = clm_core::$lang->ics_export;

	if (!is_string($file_name)) {
		return array(false, 'e01'); 	//_ISCDataError1);
	}
	if (!is_array($itermine) OR count($itermine) < 1) {
		return array(false, 'e02');	//_ISCDataError2);
	}
	
	$uid2 = clm_core::$load->clm_path();	

	$termine = array();
	foreach($itermine as $termin) {
		if (!isset($termin['DSTART'])) continue;
		if (!is_numeric($termin['DSTART'])) continue;
		if (!isset($termin['SUMMARY'])) continue;
		$termine[] = $termin;
	}

	if (count($termine) < 1) {
		return array(false, 'e03');	//_ISCNoData);
	}

	$begin = array();
	$begin[0] = 'BEGIN:VCALENDAR';
	$begin[1] = 'VERSION:2.0';
	$begin[2] = 'CALSCALE:GREGORIAN';
	$begin[3] = 'METHOD:PUBLISH';
	$begin[4] = 'PRODID:-//chessleaguemanager.de//DE';
	$begin[5] = 'BEGIN:VTIMEZONE';
	$begin[6] = 'TZID:Europe/Berlin';
	$begin[7] = 'BEGIN:STANDARD';
	$begin[8] = 'DTSTART:16011028T030000';
	$begin[9] = 'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10';
	$begin[10] = 'TZOFFSETFROM:+0200';
	$begin[11] = 'TZOFFSETTO:+0100';
	$begin[12] = 'END:STANDARD';
	$begin[13] = 'BEGIN:DAYLIGHT';
	$begin[14] = 'DTSTART:16010325T020000';
	$begin[15] = 'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3';
	$begin[16] = 'TZOFFSETFROM:+0100';
	$begin[17] = 'TZOFFSETTO:+0200';
	$begin[18] = 'END:DAYLIGHT';
	$begin[19] = 'END:VTIMEZONE';



	$nl = "\r\n";
	$file_name .= '.ics'; 
	$file_name = clm_core::$load->make_valid($file_name, 20, 'outputfile');
	if (!file_exists('components'.DS.'com_clm'.DS.'ics'.DS)) mkdir('components'.DS.'com_clm'.DS.'ics'.DS);
	$file = 'components'.DS.'com_clm'.DS.'ics'.DS.$file_name;
	$pdatei = fopen($file,"wb");
	
	foreach($begin as $begin1) {
//		fputs($pdatei, clm_core::$load->utf8encode($begin1).$nl);
		fputs($pdatei, $begin1.$nl);
	}
		
	foreach($termine as $termin) {
		fputs($pdatei, 'BEGIN:VEVENT'.$nl);
//		fputs($pdatei, 'UID:'.$termin['UID'].'@'.$uid2.$nl);
		fputs($pdatei, 'UID:'.$termin['UID'].uniqid($uid2).$nl);
		fputs($pdatei, 'DTSTAMP:'.date("Ymd\THis\Z").$nl);
		if (isset($termin['CREATED']))
			fputs($pdatei, 'CREATED:'.$termin['CREATED'].$nl);
		else
			fputs($pdatei, 'CREATED:'.date("Ymd\THis\Z").$nl);
		fputs($pdatei, 'LAST-MODIFIED:'.date("Ymd\THis\Z").$nl);
		if (isset($termin['LOCATION']))
//			fputs($pdatei, 'LOCATION:'.clm_core::$load->utf8encode($termin['LOCATION']).$nl);
			fputs($pdatei, 'LOCATION:'.$termin['LOCATION'].$nl);
		if (isset($termin['ATTACH']))
			fputs($pdatei, 'ATTACH:'.$termin['ATTACH'].$nl);
		if (isset($termin['CLM-HOST']))
			fputs($pdatei, 'CLM-HOST:'.$termin['CLM-HOST'].$nl);
//		fputs($pdatei, 'SUMMARY;LANGUAGE=de:'.clm_core::$load->utf8encode($termin['SUMMARY']).$nl);
		fputs($pdatei, 'SUMMARY;LANGUAGE=de:'.$termin['SUMMARY'].$nl);
		if (isset($termin['DESCRIPTION'])) {
			$d0 = hex2bin("0d0a");
			$termin['DESCRIPTION'] = str_replace(' '.$d0,$d0,$termin['DESCRIPTION']);
			$termin['DESCRIPTION'] = str_replace($d0,$nl.'    ',$termin['DESCRIPTION']);
//			fputs($pdatei, 'DESCRIPTION:'.clm_core::$load->utf8encode($termin['DESCRIPTION']).'\\n'.$nl);
			fputs($pdatei, 'DESCRIPTION:'.$termin['DESCRIPTION'].'\\n'.$nl);
//			fputs($pdatei, ' '.'\\n'.$nl);
		}
		fputs($pdatei, 'DTSTART;TZID=Europe/Berlin:'.$termin['DSTART'].'T'.$termin['TSTART'].$nl);
		fputs($pdatei, 'DTEND;TZID=Europe/Berlin:'.$termin['DEND'].'T'.$termin['TEND'].$nl);

		fputs($pdatei, 'BEGIN:VALARM'.$nl);
		fputs($pdatei, 'ACTION:DISPLAY'.$nl);
		fputs($pdatei, 'TRIGGER:-PT1440M'.$nl);
		fputs($pdatei, 'DESCRIPTION:'.$termin['SUMMARY'].$nl);
		fputs($pdatei, 'END:VALARM'.$nl);
		fputs($pdatei, 'END:VEVENT'.$nl);
//		break;
	}
	fputs($pdatei, 'END:VCALENDAR'.$nl);
	fclose($pdatei);

    if (file_exists($file)) {
		header('Content-Disposition: attachment; filename="'.$file_name.'"');
		header('Content-Type: text/calendar; charset=utf-8');
		header('Cache-Control: must-understand, no-store');
		header('Content-Length: '. filesize($file));
		flush();
		readfile($file);
		flush();
		exit;
	}
	
	return array(true, 'm00');	//_ICSExportSuccess); 
}
?>